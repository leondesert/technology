<?php

namespace App\Models;

use CodeIgniter\Model;

class OprModel extends Model
{
    protected $table = 'opr'; 
    protected $primaryKey = 'opr_id'; 
    protected $allowedFields = ['opr_code', 'opr_name', 'opr_phone', 'opr_mail', 'reward', 'balance_tjs', 'balance_rub', 'penalty']; 


}
