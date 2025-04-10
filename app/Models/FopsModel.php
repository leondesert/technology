<?php

namespace App\Models;

use CodeIgniter\Model;

class FopsModel extends Model
{
    protected $table = 'fops';
    protected $primaryKey = 'fops_id';
    protected $allowedFields = ['fops_id', 'tickets_id', 'passengers_id', 'fops_type', 'fops_org', 'fops_docser', 'fops_docnum', 'fops_auth_info_code', 'fops_auth_info_currency', 'fops_auth_info_amount', 'fops_auth_info_provider', 'fops_auth_info_rrn', 'fops_docinfo', 'fops_amount'];

}
