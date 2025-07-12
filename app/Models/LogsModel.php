<?php

namespace App\Models;

use CodeIgniter\Model;

use App\Models\ReportsModel;
use App\Models\UserModel;

class LogsModel extends Model
{
    protected $table = 'logs'; 
    protected $primaryKey = 'id'; 
    protected $allowedFields = ['id', 'user_id', 'action', 'data', 'ip_address', 'log_date', 'log_time']; 



    // таблица
    public function countFiltered($searchValue, $filters)
    {
        $builder = $this->builder();
        
        if ($searchValue != '') {
            $this->applySearchFilter($builder, $searchValue);
        }

        // Фильтровать
        $builder = $this->filters($builder);


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
        $builder = $this->filters($builder);


        if ($order) {
            $columnIndex = $order[0]['column'];
            $columnName = $columns[$columnIndex]['data'];
            $columnSortOrder = $order[0]['dir'];
            $builder->orderBy($columnName, $columnSortOrder);
        } else {
            $builder->orderBy('log_date', 'DESC');
        }
        $results = $builder->get($length, $start)->getResultArray();


        //изменить поля
        $results = $this->change_colums($results);

        

        return $results;
    }


    private function applySearchFilter($builder, $searchValue)
    {
        $builder->groupStart()
            ->like('user_id', $searchValue)
            ->orLike('action', $searchValue)
            ->orLike('data', $searchValue)
            ->orLike('ip_address', $searchValue)
            ->orLike('log_date', $searchValue)
            ->orLike('log_time', $searchValue)
            ->groupEnd();
    }

    private function filters($builder)
    {

        // показать лог только своего аккаунта и дочериных аккаунтов
        $role = session()->get('role');
        $user_id = session()->get('user_id');
        $userModel = new UserModel();
        if($role === "superadmin"){
            $users = $userModel->findAll();
        }else{
            $users = $userModel->where("FIND_IN_SET('$user_id', parent) >", 0)->findAll();
        }
        $userIds = array_column($users, 'user_id');
        $userIds[] = $user_id;

        // условие 1
        $builder->whereIn('user_id', $userIds);





        return $builder;
    }


    private function change_colums($results)
    {   

        $ReportsModel = new ReportsModel();

        // меняем user_id на user_login
        $results = $ReportsModel->change_userid_to_userlogin($results);

        return $results;

    }


    // получить
    public function getById($id)
    {
        return $this->where('id', $id)->first();
    }







}
