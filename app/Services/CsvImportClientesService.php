<?php

namespace App\Services;

class CsvImportClientesService
{
    private const SIMILARITY_THRESHOLD = 85.0;
    private const BATCH_SIZE           = 100;

    // Códigos SAT de régimen fiscal
    private const REGIMENES = [
        601 => 'General de Ley Personas Morales',
        603 => 'Personas Morales con Fines no Lucrativos',
        605 => 'Sueldos y Salarios e Ingresos Asimilados a Salarios',
        606 => 'Arrendamiento',
        607 => 'Régimen de Enajenación o Adquisición de Bienes',
        608 => 'Demás ingresos',
        610 => 'Residentes en el Extranjero sin Establecimiento Permanente en México',
        611 => 'Ingresos por Dividendos',
        612 => 'Personas Físicas con Actividades Empresariales y Profesionales',
        614 => 'Ingresos por intereses',
        615 => 'Régimen de los ingresos por obtención de premios',
        616 => 'Sin obligaciones fiscales',
        620 => 'Sociedades Cooperativas de Producción',
        621 => 'Incorporación Fiscal',
        622 => 'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras',
        623 => 'Opcional para Grupos de Sociedades',
        624 => 'Coordinados',
        625 => 'Actividades Empresariales con ingresos a través de Plataformas Tecnológicas',
        626 => 'Régimen Simplificado de Confianza',
        628 => 'Hidrocarburos',
        630 => 'Enajenación de acciones en bolsa de valores',
    ];

    private \CodeIgniter\Database\BaseConnection $db;

    // Índices en memoria para matching rápido
    private array $clientesById      = [];
    private array $rfcIndex          = [];   // RFC_UPPER => id_cliente
    private array $emailIndex        = [];   // email_lower => id_cliente
    private array $normalizedIndex   = [];   // nombre_normalizado => [id_cliente, ...]

    // Acumuladores batch
    private array $toInsert = [];
    private array $toUpdate = [];   // id_cliente => [campos a actualizar]

    // Resultados
    private array $log   = [];
    private array $stats = [
        'total'       => 0,
        'creados'     => 0,
        'actualizados'=> 0,
        'omitidos'    => 0,
        'conflictos'  => 0,
        'errores'     => 0,
    ];

    public function __construct()
    {
        $this->db = db_connect();
        $this->loadCache();
    }

    // ──────────────────────────────────────────────────────────
    // PUNTO DE ENTRADA PÚBLICO
    // ──────────────────────────────────────────────────────────

    public function processCsvFile(string $tmpPath): array
    {
        $content = file_get_contents($tmpPath);
        if ($content === false) {
            throw new \RuntimeException('No se pudo leer el archivo CSV.');
        }

        $content = self::fixEncoding($content);

        // Escribe a un temp para usar fgetcsv (streaming, no carga todo en RAM)
        $tmp = tempnam(sys_get_temp_dir(), 'csv_imp_');
        file_put_contents($tmp, $content);

        $handle = fopen($tmp, 'r');
        if (!$handle) {
            unlink($tmp);
            throw new \RuntimeException('No se pudo abrir el archivo temporal.');
        }

        $headers = null;
        $rowNum  = 0;

        while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            $rowNum++;

            if ($headers === null) {
                $headers = $this->parseHeaders($row);
                continue;
            }

            if (!is_array($row) || count($row) < count($headers)) {
                $this->addLog($rowNum, '', 'error', 'Columnas insuficientes (' . count($row) . ')');
                $this->stats['errores']++;
                continue;
            }

            // Si hay más columnas que headers, recortar
            $row  = array_slice($row, 0, count($headers));
            $data = array_combine($headers, $row);

            $this->processRow($rowNum, $data);
            $this->stats['total']++;
        }

        fclose($handle);
        unlink($tmp);

        $this->flushInserts();
        $this->flushUpdates();

        return [
            'stats' => $this->stats,
            'log'   => $this->log,
        ];
    }

    // ──────────────────────────────────────────────────────────
    // PROCESAMIENTO POR FILA
    // ──────────────────────────────────────────────────────────

    private function processRow(int $rowNum, array $data): void
    {
        $rfc          = $this->clean($data['rfc']           ?? null);
        $razonSocial  = $this->clean($data['razonsocial']   ?? null);
        $email        = $this->clean($data['email']         ?? null);
        $calle        = $this->clean($data['calle']         ?? null);
        $noExt        = $this->clean($data['numexterior']   ?? null);
        $noInt        = $this->clean($data['numinterior']   ?? null);
        $colonia      = $this->clean($data['colonia']       ?? null);
        $cp           = $this->clean($data['cp']            ?? null);
        $ciudad       = $this->clean($data['ciudad']        ?? null);
        $municipio    = $this->clean($data['municipio']     ?? null);
        $estado       = $this->clean($data['estado']        ?? null);
        $pais         = $this->clean($data['pais']          ?? null);
        $cfdiUse      = $this->clean($data['cfdiuse']       ?? null);
        $numRegIdTrib = $this->clean($data['numregidtrib']  ?? null);
        $taxResidence = $this->clean($data['taxresidence']  ?? null);
        $regimenRaw   = $this->clean($data['regimenfiscal'] ?? null);

        // Validaciones obligatorias
        if (empty($rfc)) {
            $this->addLog($rowNum, $razonSocial ?? '', 'error', 'RFC vacío');
            $this->stats['errores']++;
            return;
        }
        if (empty($razonSocial)) {
            $this->addLog($rowNum, $rfc, 'error', 'RazonSocial vacía');
            $this->stats['errores']++;
            return;
        }

        // Sanitizar CP: solo dígitos, 5 caracteres
        if ($cp !== null && !ctype_digit($cp)) {
            $cp = preg_replace('/\D/', '', $cp) ?: null;
        }

        // Sanitizar email
        if ($email !== null && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = null;
        }

        $rfcUpper    = strtoupper($rfc);
        $nombreNorm  = self::normalizeName($razonSocial);
        $regimenCode = $this->parseRegimenFiscal($regimenRaw);

        $csvData = [
            'nombre_normalizado' => $nombreNorm,
            'tax_id'             => $rfcUpper,
            'regimen_fiscal'     => $regimenCode,
            'codigo_postal'      => $cp,
            'calle'              => $calle,
            'no_ext'             => $noExt,
            'no_int'             => $noInt,
            'colonia'            => $colonia,
            'municipio'          => $municipio,
            'ciudad'             => $ciudad,
            'estado'             => $estado,
            'pais'               => $pais,
            'uso_cfdi'           => $cfdiUse,
            'num_reg_id_trib'    => $numRegIdTrib,
            'tax_residence'      => $taxResidence,
            'correo'             => $email,
        ];

        // ── Estrategia A: buscar por RFC ──────────────────────
        if (isset($this->rfcIndex[$rfcUpper])) {
            $existId = $this->rfcIndex[$rfcUpper];

            // Clave temporal: el RFC ya apareció antes en el mismo CSV y se marcó
            // para insertar, pero aún no existe en BD → duplicado dentro del archivo
            if (!is_int($existId)) {
                $this->addLog($rowNum, $razonSocial, 'omitido', "RFC duplicado en el mismo CSV: $rfcUpper");
                $this->stats['omitidos']++;
                return;
            }

            $existing  = $this->clientesById[$existId];
            $updateData = $this->mergeOnlyMissing($existing, $csvData);

            if (!empty($updateData)) {
                $this->toUpdate[$existId] = array_merge(
                    $this->toUpdate[$existId] ?? [],
                    $updateData
                );
                $this->addLog($rowNum, $razonSocial, 'actualizado', "RFC coincide: $rfcUpper");
                $this->stats['actualizados']++;
            } else {
                $this->addLog($rowNum, $razonSocial, 'omitido', "RFC coincide, sin datos nuevos: $rfcUpper");
                $this->stats['omitidos']++;
            }
            return;
        }

        // ── Estrategia B: buscar por nombre normalizado ───────
        $matchIds = $this->findBySimilarity($nombreNorm);

        // ── Estrategia C: buscar por email ────────────────────
        if (empty($matchIds) && $email !== null) {
            $emailLower = strtolower($email);
            if (isset($this->emailIndex[$emailLower])) {
                $matchIds = [$this->emailIndex[$emailLower]];
            }
        }

        if (count($matchIds) > 1) {
            $ids = implode(', ', $matchIds);
            $this->addLog($rowNum, $razonSocial, 'conflicto', "Múltiples coincidencias posibles (IDs: $ids)");
            $this->stats['conflictos']++;
            return;
        }

        if (count($matchIds) === 1) {
            $existId    = $matchIds[0];
            $existing   = $this->clientesById[$existId];
            $updateData = $this->mergeOnlyMissing($existing, $csvData);
            $existNorm  = $existing['nombre_normalizado'] ?: self::normalizeName($existing['nombre']);
            $pct        = $this->similarityPct($nombreNorm, $existNorm);

            if (!empty($updateData)) {
                $this->toUpdate[$existId] = array_merge(
                    $this->toUpdate[$existId] ?? [],
                    $updateData
                );
                $this->addLog($rowNum, $razonSocial, 'actualizado', "Nombre similar ({$pct}%): \"{$existing['nombre']}\"");
                $this->stats['actualizados']++;
            } else {
                $this->addLog($rowNum, $razonSocial, 'omitido', "Nombre similar ({$pct}%), sin datos nuevos");
                $this->stats['omitidos']++;
            }

            // Registrar RFC en índice para evitar duplicados en filas siguientes
            $this->rfcIndex[$rfcUpper] = $existId;
            return;
        }

        // ── Sin coincidencia → crear nuevo ────────────────────
        $this->toInsert[] = array_filter(array_merge([
            'nombre' => $razonSocial,
            'tipo'   => 1,
        ], $csvData), fn($v) => $v !== null);

        // Registrar en índices para evitar duplicados con filas posteriores del mismo CSV
        $tempKey = 'new_' . count($this->toInsert);
        $this->rfcIndex[$rfcUpper]           = $tempKey;
        $this->normalizedIndex[$nombreNorm][] = $tempKey;

        $this->addLog($rowNum, $razonSocial, 'creado', 'Sin coincidencia en BD');
        $this->stats['creados']++;
    }

    // ──────────────────────────────────────────────────────────
    // FLUSH BATCH
    // ──────────────────────────────────────────────────────────

    private function flushInserts(): void
    {
        if (empty($this->toInsert)) {
            return;
        }

        // insertBatch usa las claves de la primera fila para todas; normalizar
        // para que cada fila tenga exactamente las mismas columnas (null si falta).
        $allKeys = [];
        foreach ($this->toInsert as $row) {
            foreach (array_keys($row) as $k) {
                $allKeys[$k] = null;
            }
        }

        $normalized = array_map(
            fn($row) => array_merge($allKeys, $row),
            $this->toInsert
        );

        foreach (array_chunk($normalized, self::BATCH_SIZE) as $batch) {
            $this->db->table('sellopro_clientes')->insertBatch($batch);
        }
    }

    private function flushUpdates(): void
    {
        if (empty($this->toUpdate)) {
            return;
        }
        // update_batch requiere columnas homogéneas; como cada fila puede tener
        // distintos campos, usamos actualizaciones individuales.
        foreach ($this->toUpdate as $id => $data) {
            $this->db->table('sellopro_clientes')
                ->where('id_cliente', $id)
                ->update($data);
        }
    }

    // ──────────────────────────────────────────────────────────
    // CARGA DE CACHÉ
    // ──────────────────────────────────────────────────────────

    private function loadCache(): void
    {
        $rows = $this->db->table('sellopro_clientes')
            ->select('id_cliente, nombre, nombre_normalizado, tax_id, correo,
                      regimen_fiscal, codigo_postal, calle, no_ext, no_int,
                      colonia, municipio, ciudad, estado')
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $id = (int)$row['id_cliente'];
            $this->clientesById[$id] = $row;

            if (!empty($row['tax_id'])) {
                $this->rfcIndex[strtoupper(trim($row['tax_id']))] = $id;
            }
            if (!empty($row['correo'])) {
                $this->emailIndex[strtolower(trim($row['correo']))] = $id;
            }

            $norm = !empty($row['nombre_normalizado'])
                ? $row['nombre_normalizado']
                : self::normalizeName($row['nombre']);

            $this->normalizedIndex[$norm][] = $id;
        }
    }

    // ──────────────────────────────────────────────────────────
    // HELPERS ESTÁTICOS REUTILIZABLES
    // ──────────────────────────────────────────────────────────

    /**
     * Detecta y corrige encoding del contenido del CSV.
     * Maneja UTF-8 (con/sin BOM), ISO-8859-1, Windows-1252
     * y mojibake típico como "JOSÃ‰" → "JOSÉ".
     */
    public static function fixEncoding(string $content): string
    {
        // Quitar BOM UTF-8
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }

        // Si no es UTF-8 válido, convertir desde Windows-1252
        if (!mb_check_encoding($content, 'UTF-8')) {
            $detected = mb_detect_encoding($content, ['Windows-1252', 'ISO-8859-1'], true);
            return mb_convert_encoding($content, 'UTF-8', $detected ?: 'Windows-1252');
        }

        // Detectar mojibake: bytes UTF-8 de un carácter Latin-1 mal leídos como Windows-1252
        // y luego re-codificados a UTF-8.
        // Ejemplo: "JOSÉ" (UTF-8: C3 89) → leído como Win-1252 → "Ã‰" → UTF-8: C3 83 E2 80 B0
        //
        // Patrón confiable (solo escapes hexadecimales, sin Unicode en el fuente PHP):
        //   \xC3\x83 = bytes UTF-8 de "Ã" (U+00C3)
        //   seguido de \xC2[\xA0-\xBF] o \xC3[\x80-\xBF]  (caracteres Latin-supplement en UTF-8)
        // Cubre: á é í ó ú ü ñ Á É Í Ó Ú Ü Ñ y más
        if (preg_match('/\xC3\x83(?:\xC2[\xA0-\xBF]|\xC3[\x80-\xBF])/', $content)) {
            $candidate = mb_convert_encoding($content, 'Windows-1252', 'UTF-8');
            if (mb_check_encoding($candidate, 'UTF-8')) {
                return $candidate;
            }
        }

        return $content;
    }

    /**
     * Normaliza un nombre para comparación:
     * mayúsculas, sin acentos, sin caracteres especiales, espacios simples.
     */
    public static function normalizeName(string $name): string
    {
        $name = mb_strtoupper(trim($name), 'UTF-8');

        $from = ['Á','É','Í','Ó','Ú','Ü','Ñ','À','È','Ì','Ò','Ù','Â','Ê','Î','Ô','Û','Ä','Ë','Ï','Ö','ü','á','é','í','ó','ú','ü','ñ'];
        $to   = ['A','E','I','O','U','U','N','A','E','I','O','U','A','E','I','O','U','A','E','I','O','U','A','E','I','O','U','U','N'];
        $name = str_replace($from, $to, $name);

        // Quitar todo lo que no sea A-Z, 0-9 o espacio
        $name = preg_replace('/[^A-Z0-9 ]/', '', $name);

        return trim(preg_replace('/\s+/', ' ', $name));
    }

    // ──────────────────────────────────────────────────────────
    // HELPERS PRIVADOS
    // ──────────────────────────────────────────────────────────

    private function parseHeaders(array $raw): array
    {
        return array_map(function (string $h): string {
            $h = trim($h);
            // Quitar BOM de la primera columna
            $h = ltrim($h, "\xEF\xBB\xBF");
            // Normalizar: minúsculas, sin espacios ni guiones
            return strtolower(preg_replace('/[\s\-_]/', '', $h));
        }, $raw);
    }

    private function clean(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim($value);
        return $value === '' ? null : $value;
    }

    /**
     * Extrae el código entero de régimen fiscal del campo del CSV.
     * Acepta: "626", "626 Régimen Simplificado", "Régimen Simplificado de Confianza"
     */
    private function parseRegimenFiscal(?string $value): ?int
    {
        if (empty($value)) {
            return null;
        }
        // Extraer número de 3 dígitos si existe
        if (preg_match('/\b(\d{3})\b/', $value, $m)) {
            $code = (int)$m[1];
            return isset(self::REGIMENES[$code]) ? $code : null;
        }
        // Buscar por nombre (aproximado)
        $norm = self::normalizeName($value);
        foreach (self::REGIMENES as $code => $name) {
            if (self::normalizeName($name) === $norm) {
                return $code;
            }
        }
        return null;
    }

    /**
     * Calcula porcentaje de similitud entre dos strings normalizados.
     * Combina similar_text y distancia de Levenshtein para mejor precisión.
     */
    private function similarityPct(string $a, string $b): string
    {
        similar_text($a, $b, $pct);

        // Boost con Levenshtein si similar_text está cerca del umbral
        if ($pct >= 70 && $pct < 95) {
            $maxLen = max(strlen($a), strlen($b));
            if ($maxLen > 0) {
                $lev     = levenshtein($a, $b);
                $levPct  = (1 - $lev / $maxLen) * 100;
                $pct     = ($pct + $levPct) / 2; // promedio de ambos
            }
        }

        return number_format($pct, 1);
    }

    /**
     * Busca clientes cuyo nombre normalizado supere el umbral de similitud.
     * Devuelve array de id_cliente que coinciden.
     */
    private function findBySimilarity(string $normalizedName): array
    {
        $matches = [];

        foreach ($this->normalizedIndex as $existNorm => $ids) {
            similar_text($normalizedName, $existNorm, $pct);

            if ($pct >= self::SIMILARITY_THRESHOLD) {
                foreach ($ids as $id) {
                    if (is_int($id)) { // ignorar entradas temporales 'new_X'
                        $matches[$id] = $pct;
                    }
                }
            }
        }

        return array_keys($matches);
    }

    /**
     * Devuelve solo los campos de $newData que están vacíos en $existing.
     * Los campos de metadata (nombre_normalizado, uso_cfdi, etc.) siempre
     * se actualizan si vienen en el CSV.
     */
    private function mergeOnlyMissing(array $existing, array $newData): array
    {
        $update = [];

        // Campos fiscales: actualizar solo si están vacíos en BD
        $fiscalFields = ['tax_id', 'regimen_fiscal', 'codigo_postal', 'calle', 'no_ext',
                         'no_int', 'colonia', 'municipio', 'ciudad', 'estado'];

        foreach ($fiscalFields as $field) {
            if (!empty($newData[$field]) && empty($existing[$field])) {
                $update[$field] = $newData[$field];
            }
        }

        // Email: solo si no tiene
        if (!empty($newData['correo']) && empty($existing['correo'])) {
            $update['correo'] = $newData['correo'];
        }

        // Campos nuevos (siempre vacíos en BD): actualizar si vienen del CSV
        foreach (['nombre_normalizado', 'uso_cfdi', 'num_reg_id_trib', 'tax_residence', 'pais'] as $f) {
            if (!empty($newData[$f]) && empty($existing[$f])) {
                $update[$f] = $newData[$f];
            }
        }

        return $update;
    }

    private function addLog(int $fila, string $nombre, string $accion, string $motivo): void
    {
        $this->log[] = [
            'fila'   => $fila,
            'nombre' => $nombre,
            'accion' => $accion,
            'motivo' => $motivo,
        ];
    }
}
