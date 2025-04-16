<?php

namespace App\Models;

use CodeIgniter\Model;

class CuentasModel extends Model
{
    protected $table            = 'sellopro_cuentas';
    protected $primaryKey       = 'id_cuenta';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array'; // O 'object' si prefieres objetos
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $allowedFields    = ['Banco', 'NoCta', 'Saldo'];

    // Si no necesitas eliminar registros de forma "suave" (soft delete),
    // puedes comentar o eliminar estas líneas:
    // protected $useSoftDeletes   = false;
    // protected $deletedField     = 'deleted_at';
    // protected $dateFormat       = 'datetime';

    // Si tienes alguna validación específica, puedes definirla aquí:
    // protected $validationRules    = [];
    // protected $validationMessages = [];
    // protected $skipValidation     = false;

    // Callbacks (si necesitas lógica adicional en ciertos puntos del ciclo de vida)
    // protected $beforeInsert   = [];
    // protected $afterInsert    = [];
    // protected $beforeUpdate   = [];
    // protected $afterUpdate    = [];
    // protected $beforeFind     = [];
    // protected $afterFind      = [];
    // protected $beforeDelete   = [];
    // protected $afterDelete    = [];
}