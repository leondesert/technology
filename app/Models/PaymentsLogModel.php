<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentsLogModel extends Model
{
    protected $table = 'payments_log';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'datetime', 'host', 'type', 'method', 'request', 'response'];

}
