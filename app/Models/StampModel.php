<?php

namespace App\Models;

use CodeIgniter\Model;

class StampModel extends Model
{
    protected $table = 'stamp';
    protected $primaryKey = 'stamp_id';
    protected $allowedFields = ['stamp_code', 'stamp_name', 'stamp_address', 'stamp_phone', 'stamp_mail', 'reward', 'balance_tjs', 'balance_rub', 'penalty']; 
}
