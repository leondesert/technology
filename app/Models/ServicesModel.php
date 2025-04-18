<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\UserModel;


class ServicesModel extends Model
{
    protected $table = 'services'; 
    protected $primaryKey = 'id'; 
    protected $allowedFields = [
        'id',
        'create_date', 
        'doc_date', 
        'doc_number',
        'service_name',
        'amount', 
        'currency', 
        'method', 
        'acquiring', 
        'note', 
        'doc_scan', 
        'name', 
        'value',
        'bank', 
        
    ]; 


    
    // таблица
    public function countFiltered($searchValue, $filters)
    {
        $builder = $this->builder();
        
        if ($searchValue != '') {
            $this->applySearchFilter($builder, $searchValue);
        }

        // Фильтровать
        $builder = $this->filters($builder, $filters);


        return $builder->countAllResults();
    }

    public function getFilteredData($start, $length, $searchValue, $filters, $order = [], $columns = [])
    {
        $builder = $this->builder();
        
        $builder->select($this->allowedFields);
        if ($searchValue != '') {
            $this->applySearchFilter($builder, $searchValue);
        }
        

        
        // Фильтровать
        $builder = $this->filters($builder, $filters);


        if ($order) {
            $columnIndex = $order[0]['column'];
            $columnName = $columns[$columnIndex]['data'];
            $columnSortOrder = $order[0]['dir'];
            $builder->orderBy($columnName, $columnSortOrder);
        } else {
            $builder->orderBy('create_date', 'DESC');
        }
        $results = $builder->get($length, $start)->getResultArray();


        
        

        return $results;
    }

    

    private function applySearchFilter($builder, $searchValue)
    {
        $builder->groupStart()
            ->like('create_date', $searchValue)
            ->orLike('doc_date', $searchValue)
            ->orLike('doc_number', $searchValue)
            ->orLike('service_name', $searchValue)
            ->orLike('amount', $searchValue)
            ->orLike('currency', $searchValue)
            ->orLike('method', $searchValue)
            ->orLike('acquiring', $searchValue)
            ->orLike('note', $searchValue)
            ->orLike('doc_scan', $searchValue)
            ->orLike('name', $searchValue)
            ->orLike('value', $searchValue)
            ->groupEnd();
    }



    private function filters($builder, $filters)
    {
        // условие
        $builder->where('doc_date' . ' >=', $filters['startDate']);
        $builder->where('doc_date' . ' <=', $filters['endDate']);
        $builder->where('currency', $filters['currency']);
        $builder->where('name', $filters['name_table']);


        if ($filters['value_table'] !== "all") {

            $builder->where('value', $filters['value_table']);


        } else {


            // если value_table = all
            $UserModel = new UserModel();
            $user_id = $filters['user_login'];
            $user = $UserModel->find($user_id);
            $colum_name = $user['filter'].'_id';
            $ids = $user[$colum_name];
            $ids = explode(',', $ids);

            $builder->whereIn('value', $ids);

        }





        return $builder;
    }



    public function getDataForReport($filters)
    {
        $builder = $this->builder();
        $builder->select($this->allowedFields);


        if ($filters['fist_balance']) {

            // отнять 1 день
            $dateTime = new \DateTime($filters['start_date']);
            $dateTime->modify('-1 day');
            $filters['end_date'] = $dateTime->format('Y-m-d');
            
            $filters['start_date'] = '2024-01-01'; // balance_date 
        }

        // Фильтровать
        $filters = [
            'startDate' => $filters['start_date'],
            'endDate' => $filters['end_date'],
            'currency' => $filters['currency'],
            'name_table' => $filters['name_table'],
            'value_table' => $filters['value_table'],
            'user_login' => $filters['user_login'],

        ];

        



        

        $builder = $this->filters($builder, $filters);


        return $builder->get()->getResultArray();

    }

    
    public function getDataForDowntable($params)
    {
        $colum_name = $params['name_table'].'_id';
        $user_id = session()->get('user_id');
        $model = new UserModel();
        $user = $model->find($user_id);
        $ids = explode(',', $user[$colum_name]);


        $builder = $this->builder();

        // // Фильтровать 
        $builder->where('doc_date' . ' >=', $params['startDate']);
        $builder->where('doc_date' . ' <=', $params['endDate']);
        $builder->where('currency', $params['currency']);
        $builder->where('name', $params['name_table']);
        $builder->whereIn('value', $ids);

        if ($params['value_table'] !== "all") {
            $builder->where('value', $params['value_table']);
        }



        // Начало составления запроса
        $builder->select('`name`, `value`');
        $builder->select('COUNT(`id`) AS `count`, SUM(`amount`) AS `summa`', false);
        $builder->groupBy('`name`, `value`');


        // получить
        $results = $builder->get()->getResultArray();




        $totalCount = array_sum(array_column($results, 'count'));
        $totalSum = array_sum(array_column($results, 'summa'));



    
        return [
            'results' => $results,
            'totalCount' => $totalCount,
            'totalSum' => $totalSum,

        ];


    }


}


