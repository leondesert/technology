<?php

namespace App\Models;

use CodeIgniter\Model;

class ShareModel extends Model
{
    protected $table = 'share';
    protected $primaryKey = 'share_id';
    protected $allowedFields = ['share_code', 'share_name', 'share_address', 'share_phone', 'share_mail', 'reward', 'balance_tjs', 'balance_rub', 'penalty'];


}