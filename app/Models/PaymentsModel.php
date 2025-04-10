<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentsModel extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'created_at', 'updated_at', 'status', 'amount', 'currency', 'info'];

}
