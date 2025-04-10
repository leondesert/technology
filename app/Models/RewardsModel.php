<?php

namespace App\Models;

use CodeIgniter\Model;

class RewardsModel extends Model
{
    protected $table = 'rewards';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'method', 'type', 'code', 'name', 'value', 'procent'];


}
