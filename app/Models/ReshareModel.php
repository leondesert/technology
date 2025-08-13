<?php

namespace App\Models;

use CodeIgniter\Model;

class ReshareModel extends Model
{
    protected $table = 'reshare';
    protected $primaryKey = 'reshare_id';
    protected $allowedFields = ['reshare_code', 'reshare_name', 'reshare_address', 'reshare_phone', 'reshare_mail', 'reward', 'balance_tjs', 'balance_rub', 'penalty'];


}