<?php

namespace App\Models;

use CodeIgniter\Model;

class BalanceModel extends Model
{
    protected $table            = 'sellopro_balance';
    protected $primaryKey       = 'id_balance';
    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['monto', 'capital', 'beneficio', 'created_at'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';

}