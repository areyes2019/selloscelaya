<?php
namespace App\Helpers;

use RuntimeException;

class CertificadosHelper {
    /**
     * Convierte un archivo a Base64
     */
    public static function archivoABase64(string $rutaArchivo): string 
    {
        if (!file_exists($rutaArchivo)) {
            throw new RuntimeException("Archivo no encontrado: {$rutaArchivo}");
        }

        $contenido = file_get_contents($rutaArchivo);
        return base64_encode($contenido);
    }

    /**
     * Obtiene los datos de los certificados en un array asociativo
     */
    public static function obtenerDatosCertificados(): array 
    {
        $rutaBase = WRITEPATH . 'credenciales/';
        
        return [
            'cer' => self::archivoABase64($rutaBase . 'ivd920810gu2.cer'),
            'key' => self::archivoABase64($rutaBase . 'Claveprivada_FIEL_IVD920810GU2_20230518_055729.key'),
            'password' => '12345678a' // Reemplázala con tu contraseña real
        ];
    }
}