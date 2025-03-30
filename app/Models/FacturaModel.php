<?php

namespace App\Models;

use CodeIgniter\Model;

class FacturaModel extends Model
{
    protected $table      = 'sellopro_facturas';
    protected $primaryKey = 'id';

    // Habilitar timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    // Campos permitidos para asignación masiva
    protected $allowedFields = [
        'cotizacion_id',
        'factura_uuid',
        'folio',
        'serie',
        'estado',
        'fecha_timbrado',
        'pdf_url',
        'xml_url',
        'monto',
        'respuesta_completa'
        // No incluir fecha_creacion ni fecha_actualizacion aquí
        // porque son manejados automáticamente
    ];

    
}