<?php

namespace App\Models;

use CodeIgniter\Model;

class TapModel extends Model
{
    protected $table = 'tap'; 
    protected $primaryKey = 'tap_id'; 
    protected $allowedFields = ['tap_code', 'tap_name', 'tap_address', 'tap_phone', 'tap_mail', 'reward', 'balance_tjs', 'balance_rub', 'penalty']; 


}
