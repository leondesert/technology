<?php

namespace App\Models;

use CodeIgniter\Model;

class EmdModel extends Model
{
    protected $table = 'emd';
    protected $primaryKey = 'emd_id';
    protected $allowedFields = ['emd_id', 'tickets_id', 'passengers_id', 'emd_coupon_no', 'emd_value', 'emd_remark', 'emd_related', 'emd_reason_rfisc', 'emd_reason_airline', 'emd_xbaggage_number', 'emd_xbaggage_qualifier', 'emd_xbaggage_rpu', 'emd_xbaggage_currency'];


}
