<?php

namespace App\Models;

use CodeIgniter\Model;

class AcquiringModel extends Model
{
    protected $table = 'acquirings'; 
    protected $primaryKey = 'id'; 
    protected $allowedFields = ['name', 'value', 'date', 'balance']; 


}
