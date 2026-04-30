<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ClientesModel;
use CodeIgniter\API\ResponseTrait;
use Smalot\PdfParser\Parser;

class Clientes extends BaseController
{
    use ResponseTrait;

    // Mapeo nombre legible → código SAT
    private const REGIMENES = [
        'General de Ley Personas Morales'                                                   => 601,
        'Personas Morales con Fines no Lucrativos'                                           => 603,
        'Sueldos y Salarios e Ingresos Asimilados a Salarios'                               => 605,
        'Arrendamiento'                                                                      => 606,
        'Régimen de Enajenación o Adquisición de Bienes'                                    => 607,
        'Demás ingresos'                                                                     => 608,
        'Residentes en el Extranjero sin Establecimiento Permanente en México'              => 610,
        'Ingresos por Dividendos'                                                            => 611,
        'Personas Físicas con Actividades Empresariales y Profesionales'                    => 612,
        'Ingresos por intereses'                                                             => 614,
        'Régimen de los ingresos por obtención de premios'                                   => 615,
        'Sin obligaciones fiscales'                                                          => 616,
        'Sociedades Cooperativas de Producción'                                             => 620,
        'Incorporación Fiscal'                                                               => 621,
        'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras'                          => 622,
        'Opcional para Grupos de Sociedades'                                                 => 623,
        'Coordinados'                                                                        => 624,
        'Actividades Empresariales con ingresos a través de Plataformas Tecnológicas'       => 625,
        'Régimen Simplificado de Confianza'                                                  => 626,
        'Hidrocarburos'                                                                      => 628,
        'Enajenación de acciones en bolsa de valores'                                        => 630,
    ];

    // ──────────────────────────────────────────────────────────
    // LISTADO
    // ──────────────────────────────────────────────────────────

    public function index()
    {
        $model = new ClientesModel();
        $data['clientes'] = $model->findAll();
        return view('Panel/clientes', $data);
    }

    // ──────────────────────────────────────────────────────────
    // CREAR
    // ──────────────────────────────────────────────────────────

    public function nuevo()
    {
        $model = new ClientesModel();
        $model->insert([
            'nombre'   => $this->request->getPost('nombre'),
            'tipo'     => $this->request->getPost('tipo') == '2' ? 2 : 1,
            'telefono' => $this->request->getPost('telefono'),
        ]);
        return redirect()->to('/clientes');
    }

    // ──────────────────────────────────────────────────────────
    // EDITAR
    // ──────────────────────────────────────────────────────────

    public function editar($id)
    {
        $model     = new ClientesModel();
        $resultado = $model->where('id_cliente', $id)->findAll();
        $nombre    = $resultado[0]['nombre'];
        return view('Panel/editar_cliente', ['clientes' => $resultado, 'nombre' => $nombre]);
    }

    // ──────────────────────────────────────────────────────────
    // ACTUALIZAR
    // ──────────────────────────────────────────────────────────

    public function actualizar()
    {
        $modelo = new ClientesModel();
        $id     = $this->request->getPost('idcliente');

        if (!$modelo->find($id)) {
            return redirect()->to('/clientes')->with('error', 'Cliente no encontrado');
        }

        $data = [
            'nombre'         => $this->request->getPost('nombre'),
            'correo'         => $this->request->getPost('correo'),
            'telefono'       => $this->request->getPost('telefono'),
            'tipo'           => $this->request->getPost('tipo') == '2' ? 2 : 1,
            // Datos fiscales
            'tax_id'         => strtoupper(trim($this->request->getPost('tax_id') ?? '')),
            'regimen_fiscal' => (int) ($this->request->getPost('regimen_fiscal') ?? 0) ?: null,
            'codigo_postal'  => trim($this->request->getPost('codigo_postal') ?? ''),
            // Dirección
            'calle'          => $this->request->getPost('calle'),
            'no_ext'         => $this->request->getPost('no_ext'),
            'no_int'         => $this->request->getPost('no_int'),
            'colonia'        => $this->request->getPost('colonia'),
            'ciudad'         => $this->request->getPost('ciudad'),
            'municipio'      => $this->request->getPost('municipio'),
            'estado'         => $this->request->getPost('estado'),
            // Dirección formateada (usada en otros módulos)
            'direccion'      => $this->buildDireccion($this->request->getPost()),
        ];

        if ($modelo->update($id, $data)) {
            return redirect()->to('/clientes')->with('success', 'Cliente actualizado correctamente');
        }
        return redirect()->back()->withInput()->with('errors', $modelo->errors());
    }

    // ──────────────────────────────────────────────────────────
    // ELIMINAR
    // ──────────────────────────────────────────────────────────

    public function eliminar($id)
    {
        (new ClientesModel())->delete($id);
        return redirect()->to('/clientes');
    }

    // ──────────────────────────────────────────────────────────
    // PARSEAR CSF  (POST /clientes/parsear_csf)
    // ──────────────────────────────────────────────────────────

    public function parsearCsf()
    {
        $archivo = $this->request->getFile('csf_pdf');

        if (!$archivo || !$archivo->isValid()) {
            return $this->respond(['ok' => false, 'message' => 'Archivo inválido o no recibido'], 400);
        }

        if ($archivo->getClientMimeType() !== 'application/pdf') {
            return $this->respond(['ok' => false, 'message' => 'El archivo debe ser un PDF'], 422);
        }

        try {
            $parser = new Parser();
            $pdf    = $parser->parseFile($archivo->getTempName());
            $texto  = $pdf->getText();
        } catch (\Throwable $e) {
            log_message('error', '[CSF Parser] ' . $e->getMessage());
            return $this->respond(['ok' => false, 'message' => 'No se pudo leer el PDF: ' . $e->getMessage()], 500);
        }

        $datos = $this->extraerDatosCsf($texto);

        return $this->respond(['ok' => true, 'datos' => $datos]);
    }

    // ──────────────────────────────────────────────────────────
    // HELPERS PRIVADOS
    // ──────────────────────────────────────────────────────────

    /**
     * Extrae campos fiscales del texto plano de una CSF mediante regex.
     */
    private function extraerDatosCsf(string $texto): array
    {
        $get = function (string $patron) use ($texto): string {
            if (preg_match($patron, $texto, $m)) {
                return trim($m[1]);
            }
            return '';
        };

        // ── Identidad ───────────────────────────────────────────────
        // El parser elimina espacios dentro de las etiquetas de la tabla
        // (ej. "Primer Apellido" → "PrimerApellido"), por eso usamos \s* en los patrones.
        $rfc            = $get('/RFC:\s*([A-Z&Ñ]{3,4}\d{6}[A-Z\d]{2,3})/u');
        $nombres        = $get('/Nombre\s*\(s\)\s*:\s*(.+?)(?=\n|Primer\s*Apellido)/su');
        $primerApellido = $get('/Primer\s*Apellido\s*:\s*(.+?)(?=\n|Segundo\s*Apellido)/su');
        $segundoApellido= $get('/Segundo\s*Apellido\s*:\s*(.+?)(?=\n|Fecha\s*inicio)/su');

        // El encabezado del PDF sí conserva los espacios; lo usamos para el nombre completo.
        $nombreHeader = $get('/Registro\s+Federal\s+de\s+Contribuyentes\s*\n(.+?)\nNombre/su');
        if ($nombreHeader !== '') {
            $nombreCompleto = $this->limpiar($nombreHeader);
        } else {
            $nombreCompleto = $this->limpiar(implode(' ', array_filter([
                $this->limpiar($nombres),
                $this->limpiar($primerApellido),
                $this->limpiar($segundoApellido),
            ])));
        }

        // ── Domicilio ───────────────────────────────────────────────
        // El parser elimina espacios entre palabras en las etiquetas de la tabla
        // (ej. "Nombre de Vialidad" → "NombredeVialidad"), por eso \s* en lugar de \s+.
        $stop = 'C[oó]digo\s*Postal|Tipo\s*de\s*Vialidad|Nombre\s*de\s*Vialidad'
              . '|N[uú]mero\s*Exterior|N[uú]mero\s*Interior'
              . '|Nombre\s*de\s*la\s*Colonia|Nombre\s*de\s*la\s*Localidad'
              . '|Nombre\s*del?\s*Municipio|Nombre\s*de\s*la\s*Entidad'
              . '|Entre\s*Calle|Y\s*Calle|Actividades\s*Econ|Reg[ií]menes';

        $cp      = $get('/C[oó]digo\s*Postal\s*:?\s*(\d{5})/ui');
        $calle   = $get("/Nombre\\s*de\\s*Vialidad\\s*:\\s*(.+?)(?=\\s*(?:{$stop})|$)/sui");
        $noExt   = $get("/N[uú]mero\\s*Exterior\\s*:\\s*(.+?)(?=\\s*(?:{$stop})|$)/sui");
        $noInt   = $get("/N[uú]mero\\s*Interior\\s*:?\\s*(.+?)(?=\\s*(?:{$stop})|$)/sui");
        $colonia = $get("/Nombre\\s*de\\s*la\\s*Colonia\\s*:\\s*(.+?)(?=\\s*(?:{$stop})|$)/sui");
        $ciudad  = $get("/Nombre\\s*de\\s*la\\s*Localidad\\s*:\\s*(.+?)(?=\\s*(?:{$stop})|$)/sui");
        $mpio    = $get("/Nombre\\s*del?\\s*Municipio(?:\\s*o\\s*Demarcaci[oó]n\\s*Territorial)?\\s*:\\s*(.+?)(?=\\s*(?:{$stop})|$)/sui");
        $estado  = $get("/Nombre\\s*de\\s*la\\s*Entidad\\s*Federativa\\s*:\\s*(.+?)(?=\\s*(?:{$stop})|$)/sui");

        // ── Régimen fiscal (devuelve el código SAT) ──────────────────
        $regimenNombre = '';
        $regimenCodigo = null;
        foreach (self::REGIMENES as $nombre => $codigo) {
            if (stripos($texto, $nombre) !== false) {
                $regimenNombre = $nombre;
                $regimenCodigo = $codigo;
                break;
            }
        }

        return [
            'rfc'             => strtoupper($rfc),
            'nombre_completo' => $nombreCompleto,
            'nombres'         => $this->limpiar($nombres),
            'primer_apellido' => $this->limpiar($primerApellido),
            'segundo_apellido'=> $this->limpiar($segundoApellido),
            'codigo_postal'   => $cp,
            'calle'           => $this->limpiar($calle),
            'no_ext'          => $this->limpiar($noExt),
            'no_int'          => $this->limpiar($noInt),
            'colonia'         => $this->limpiar($colonia),
            'ciudad'          => $this->limpiar($ciudad),
            'municipio'       => $this->limpiar($mpio),
            'estado'          => $this->limpiar($estado),
            'regimen_nombre'  => $regimenNombre,
            'regimen_codigo'  => $regimenCodigo,
        ];
    }

    private function limpiar(string $valor): string
    {
        return trim(preg_replace('/\s+/', ' ', $valor));
    }

    private function buildDireccion(array $post): string
    {
        $partes = array_filter([
            ($post['calle']   ?? '') . ' ' . ($post['no_ext'] ?? '') . (($post['no_int'] ?? '') ? '-' . $post['no_int'] : ''),
            'Col. ' . ($post['colonia'] ?? ''),
            ($post['ciudad'] ?? ''),
            'C.P. ' . ($post['codigo_postal'] ?? ''),
        ]);
        return implode(', ', $partes);
    }
}
