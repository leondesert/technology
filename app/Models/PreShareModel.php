<?php

namespace App\Models;

use CodeIgniter\Model;

class PreShareModel extends Model
{
    protected $table = 'pre_share';
    protected $primaryKey = 'pre_share_id';
    protected $allowedFields = ['pre_share_code', 'pre_share_name', 'pre_share_address', 'pre_share_phone', 'pre_share_mail', 'reward', 'balance_tjs', 'balance_rub', 'penalty'];


}