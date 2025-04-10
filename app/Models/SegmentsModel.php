<?php

namespace App\Models;

use CodeIgniter\Model;

class SegmentsModel extends Model
{
    protected $table = 'segments';
    protected $primaryKey = 'segments_id';
    protected $allowedFields = ['segments_id', 'tickets_id', 'passengers_id', 'segno', 'city1code', 'city2code', 'port1code', 'port2code', 'carrier', 'class', 'reis', 'flydate', 'flytime', 'basicfare', 'seg_bsonum', 'coupon_no', 'is_void', 'stpo', 'term1', 'term2', 'arrdate', 'arrtime', 'nfare', 'baggage_number', 'baggage_qualifier', 'ffp_info_number', 'ffp_info_certificate', 'exchanged'];

}
