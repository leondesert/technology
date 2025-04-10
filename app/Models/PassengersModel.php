<?php

namespace App\Models;

use CodeIgniter\Model;

class PassengersModel extends Model
{
    protected $table = 'passengers';
    protected $primaryKey = 'passengers_id';
    protected $allowedFields = ['passengers_id', 'fio', 'surname', 'name', 'pass', 'pas_type', 'benefit_doc', 'birth_date', 'gender', 'citizenship', 'contact'];

}
