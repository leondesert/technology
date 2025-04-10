<?php

namespace App\Models;

use CodeIgniter\Model;

class AgencyModel extends Model
{
    protected $table = 'agency'; 
    protected $primaryKey = 'agency_id'; 
    protected $allowedFields = ['agency_code', 'agency_name', 'agency_address', 'agency_phone', 'agency_mail', 'reward', 'balance_tjs', 'balance_rub', 'penalty']; 


}
