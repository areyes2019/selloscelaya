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
    protected $allowedFields    = ['banco', 'cuenta', 'saldo'];
}