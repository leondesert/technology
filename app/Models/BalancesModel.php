<?php

namespace App\Models;

use CodeIgniter\Model;

class BalancesModel extends Model
{
    protected $table = 'balances';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'balances.id', 
        'balances.start_date', 
        'balances.end_date', 
        'balances.currency', 
        'balances.name_table', 
        'balances.value_table', 
        'balances.user_id', 
        'balances.balance', 
        'balances.status', 
        'users.user_login'
    ];


    


    // получить
    public function getBalanceById($id)
    {
        return $this->where('id', $id)->first();
    }


    // изменить
    public function updateStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }

    // удалить
    public function deleteBalance($id)
    {
        return $this->delete($id);
    }


}
