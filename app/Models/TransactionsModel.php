<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionsModel extends Model
{
    protected $table = 'transactions'; 
    protected $primaryKey = 'transaction_id'; 
    protected $allowedFields = ['amount', 'currency', 'creation_date', 'payment_date', 'receipt_number', 'receipt_photo', 'note', 'name', 'value', 'value_name', 'method', 'acquiring', 'bank']; 


    

    public function forAcquiringBalance($filters)
    {

        $builder = $this->builder();

        // Фильтровать
        if ($filters['startDate'] !== '' && $filters['endDate'] !== '') {

            $builder->where('payment_date >=', $filters['startDate']);
            $builder->where('payment_date <=', $filters['endDate']);
        }


        $currencies = [
            ["name" => "TJS", "value" => "972"],
            ["name" => "RUB", "value" => "643"],
            ["name" => "USD", "value" => "840"],
            ["name" => "EUR", "value" => "978"],
        ];

        // Если задан фильтр по валюте
        if (isset($filters['currency']) && $filters['currency'] !== '') {
            // Ищем название валюты по её значению
            $currencyName = array_reduce($currencies, function ($carry, $currency) use ($filters) {
                return $currency['value'] === $filters['currency'] ? $currency['name'] : $carry;
            }, null);

            // Применяем фильтр по найденному имени валюты
            if ($currencyName !== null) {
                $builder->where('currency', $currencyName);
            }
        }



        // Группируем по полю 'acquiring' и суммируем 'amount'
        $builder->select('acquiring, SUM(amount) as total_amount');
        $builder->groupBy('acquiring');

        // Выполняем запрос и получаем результаты
        return $builder->get()->getResultArray();
        
    }

    
    


}


