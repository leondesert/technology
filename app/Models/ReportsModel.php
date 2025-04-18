<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\UserModel;
use App\Models\AgencyModel;
use App\Models\StampModel;
use App\Models\TapModel;
use App\Models\OprModel;

class ReportsModel extends Model
{
    protected $table = 'reports';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id', 
        'user_id', 
        'start_date', 
        'end_date', 
        'currency', 
        'report_type', 
        'name_table', 
        'value_table', 
        'balance', 
        'is_report', 
        'status',
        'send_date', 
        'check_date', 

    ];


    


    // получить
    public function getReportById($id)
    {
        return $this->where('id', $id)->first();
    }


    // изменить
    public function updateStatus($params)
    {   

        // === 1. Найти запись чтоб получить данные
        $record = $this->find($params['id']);

        if (!$record) {
            $status = false;
        }


        $params['user_login'] = $record['user_id'];
        $params['name_table'] = $record['name_table'];




        // === 2. найти баланс отчета
        $builder = $this->builder();

        // доп условие
        $builder->where([
            //'user_id' => $params['user_login'], //global
            'end_date' => $record['end_date'],
            'currency' => $record['currency'],
            'report_type' => $record['report_type'],
            'name_table' => $record['name_table'],
            'is_report' => 0,
            // 'status' => 1,
            
        ]);


        if (!strpos($record['value_table'], ',')) {
            $builder->where('value_table', $record['value_table']);
            $builder->select('balance');
            $result = $builder->get()->getRow();
            $balance_report = $result->balance;
        }else{
            $balance_report = $this->filter_for_FistBalance($builder, $params);
            
        }
        




        // === 3. сравнить

        $status = true;

        if ($params['status'] !== "2") {

            if($balance_report !== $params['balance']){
                $params['status'] = "2";
                $status = false;
            }

        }
        
        




        // === 4. обновить статус балансов и отчета
        $result = $this->updateStatusBalances($params);
        $res = $this->update($params['id'], ['balance' => $params['balance'], 'status' => $params['status']]); // для отчета



        $data = [
            'balance_report' => $balance_report,
            'params_balance' => $params['balance'],
            'status' => $status,
            'updateStatusBalances' => $result,
        ];


        return $data;
    }

    // удалить
    public function deleteReport($id)
    {


        $record = $this->getReportById($id);

        // // удалить запись
        // $this->delete($id);


        // if (!$record) {
        //     // Если запись не найдена
        //     return false;
        // }


        // Построение запроса
        $builder = $this->builder();
        
        
        //условие
        $builder = $this->filter2($record, $builder);



        // Удаление записи
        return $builder->delete();


    }


    public function filter2($record, $builder)
    {
        

        // Получаем значения 
        $valueTable = explode(',', $record['value_table']);

        
        //условие
        $builder->whereIn('value_table', $valueTable);

        //доп условие
        $builder->where([
            // 'user_id' => $record['user_id'],
            'start_date' => $record['start_date'],
            'end_date' => $record['end_date'],
            'currency' => $record['currency'],
            'report_type' => $record['report_type'],
            'name_table' => $record['name_table'],
            'status' => $record['status'],
            // 'is_report' => 0,
        ]);


        return $builder;     
    }




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
            $builder->orderBy('status', 'DESC');
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
            ->orLike('start_date', $searchValue)
            ->orLike('end_date', $searchValue)
            ->orLike('currency', $searchValue)
            ->orLike('name_table', $searchValue)
            ->orLike('value_table', $searchValue)
            ->orLike('balance', $searchValue)
            ->orLike('is_report', $searchValue)
            ->orLike('status', $searchValue)
            ->groupEnd();
    }


    private function change_colums($results)
    {
        // изменить значения status
        foreach ($results as &$result) {
            switch ($result['status']) {
                case 0:
                    $result['status'] = 'В обработке';
                    break;
                case 1:
                    $result['status'] = 'Подтвержден';
                    break;
                case 2:
                    $result['status'] = 'Отклонен';
                    break;
            }
        }




        // меняем user_id на user_login
        $results = $this->change_userid_to_userlogin($results);




        return $results;
    }

    public function change_userid_to_userlogin($results)
    {

        $usersModel = new UserModel();
        $users = $usersModel->select('user_id, user_login')->findAll();


        // Преобразуем массив $users в ассоциативный массив, где ключ - user_id, а значение - user_login
        $userMap = [];
        foreach ($users as $user) {
            $userMap[$user['user_id']] = $user['user_login'];
        }

        // Проходим по массиву $results и заменяем user_id на user_login
        foreach ($results as &$result) {
            if (isset($userMap[$result['user_id']])) {
                $result['user_id'] = $userMap[$result['user_id']]; // Заменяем user_id на user_login
            }
        }


        return $results;
    }

    private function filters($builder, $filters)
    {

        // =================== Базовый ===============//

        // если отчет
        $builder->where('is_report', 1);


        // по пользователю
        $user_id = session()->get('user_id');
        $role = session()->get('role');
        $UserModel = new UserModel();
        $users = $UserModel->select('user_id')->where('parent', $user_id)->findAll();
        $ids = array_column($users, 'user_id');
        $ids[] = $user_id;

        // $user_ids = [3, 58];

        if ($role === "admin") {
            $builder->whereIn('user_id', $ids);
        }elseif($role === "user"){
            $builder->where('user_id', $user_id);
        }
        

        //первый раз когда заходит
        if ($filters['is_refresh'] == "yes") {
            return $builder;
        }


        // =================== Форма ===============//

        // условие
        $builder->where('start_date' . ' >=', $filters['startDate']);
        $builder->where('end_date' . ' <=', $filters['endDate']);

        if ($filters['user_login'] !== "all") {
            $builder->where('user_id', $filters['user_login']);
        }

        if ($filters['currency'] !== "all") {
            $builder->where('currency', $filters['currency']);
        }
        

        if ($filters['status'] !== "all") {
            $builder->where('status', $filters['status']);
        }

        if ($filters['name_table'] !== "all") {
            $builder->where('name_table', $filters['name_table']);
        }

        

        // if ($filters['value_table'] !== "all") {
        //     $value = $filters['value_table'];
        //     $builder->where("FIND_IN_SET('$value', value_table) >", 0);
        // }


        return $builder;
    }

    
    public function updateStatusBalances($params)
    {
        // Найти запись
        $record = $this->find($params['id']);

        if (!$record) {
            // Если запись не найдена
            return false;
        }


        // Построение запроса
        $builder = $this->builder();
        
        
        //условие
        $builder = $this->filter2($record, $builder);


        // Выполнение запроса
        $result = $builder->update(['status' => $params['status']]);



        // Проверка, было ли успешным обновление
        if ($result) {
            return true;  // Возвращаем true при успешном обновлении
        } else {
            // Обработка ошибки при обновлении
            return false;  // Или можно выбросить исключение
        }
    }

    public function minus_one_day($day)
    {
        // отнять 1 день
        $dateTime = new \DateTime($day);
        $dateTime->modify('-1 day');
        $target_date = $dateTime->format('Y-m-d');


        return $target_date;
    }


    public function get_nearest_date($builder, $params)
    {
        // отнять 1 день
        $dateTime = new \DateTime($params['start_date']);
        $dateTime->modify('-1 day');
        $target_date = $dateTime->format('Y-m-d');


        // Добавляем условие на даты меньше или равные заданной
        $builder->where('end_date <=', $target_date);

        // искать ближайшему дню
        $builder->select('end_date'); // Колонка с датами
        $builder->orderBy("ABS(UNIX_TIMESTAMP(end_date) - UNIX_TIMESTAMP('{$target_date}'))", 'ASC');
        $builder->limit(1);


        //для одиночных
        if ($params['value_table'] !== "all") {
            $builder->where('value_table', $params['value_table']);
        }


        $cloneBuilder = clone $builder;


        // Выполнение запроса
        $result = $builder->get()->getRow();
        
        if ($result) {
            // Ближайшая дата 
            $nearest_date = $result->end_date;
        } else {
            $nearest_date = false;
        }


        return [
            'nearest_date' => $nearest_date,
            'builder' => $cloneBuilder,
            
        ];


    }

    public function get_date_for_balance($params)
    {
        // Построение запроса
        $builder = $this->builder();


        // доп условие
        $builder->where([
            //'user_id' => $params['user_login'], //global
            'currency' => $params['currency'],
            'report_type' => $params['type'],
            'name_table' => $params['name_table'],
            'is_report' => 0,
            'status' => 1,
            
        ]);


        $result = $this->get_nearest_date($builder, $params);
        $nearest_date = $result['nearest_date'];
        $builder = $result['builder'];



        //для всех
        $check_nearest_date = '';
        if ($params['value_table'] == "all") {


            
            


            while (true) {

                // 1. Проверить существует ли все значения по ближайщей дате
                $params['nearest_date'] = $nearest_date;
                $check_nearest_date = $this->check_nearest_date($params);


                if ($check_nearest_date) {
                    break;
                }

                // 2. Искать следующую ближайщую дату
                $params['start_date'] = $this->minus_one_day($nearest_date);
                

                $result = $this->get_nearest_date($builder, $params);
                $nearest_date = $result['nearest_date'];

                if ($nearest_date == false) {
                    break;
                }

            }


        

        }

        



        $data = [
            'check_nearest_date' => $check_nearest_date,
            'nearest_date' => $nearest_date
        ];



        return $data;

    }


    public function check_nearest_date($params)
    {   


        // === 1. получить из org name из users
        $UserModel = new UserModel();
        $colum_name = $params['name_table'].'_id';
        $colum_name2 = $params['name_table'].'_code';
        $user = $UserModel->find($params['user_login']);
        $ids = explode(',', $user[$colum_name]);


        $model = $this->getModal_for_FistBalance($params['name_table']);
        $results = $model->select($colum_name2)->whereIn($colum_name, $ids)->findAll();

        // Получаем массив значений `code`
        $valueTable = array_column($results, $colum_name2);

        
        
        // Построение запроса
        $builder = $this->builder();
        $builder->select('value_table');

        // доп условие
        $builder->where([
            //'user_id' => $params['user_login'], //global
            'currency' => $params['currency'],
            'report_type' => $params['type'],
            'name_table' => $params['name_table'],
            'is_report' => 0,
            'status' => 1,
            
        ]);



        // $valueTable = ['595'];
        // $valueTable = ['542', '598', '83', '595', '359', '195', '196', '543', '194'];



        // === 2. ищем существует ли все значения по текущему дату
        $builder->where('end_date', $params['nearest_date']);


        // Выполнение запроса
        $result = $builder->get()->getResultArray();

        // Проверяем, все ли значения присутствуют в результатах
        $foundValues = array_column($result, 'value_table');

        foreach ($valueTable as $value) {
            if (!in_array($value, $foundValues)) {
                return false;
            }
        }


        return true;

    }

    public function getFistBalance($params)
    {
        // Построение запроса
        $builder = $this->builder();

        // // отнять 1 день
        // $dateTime = new \DateTime($params['start_date']);
        // $dateTime->modify('-1 day');
        // $target_date = $dateTime->format('Y-m-d');


        // доп условие
        $builder->where([
            //'user_id' => $params['user_login'], //global
            'end_date' => $params['start_date'],
            'currency' => $params['currency'],
            'report_type' => $params['type'],
            'name_table' => $params['name_table'],
            'is_report' => 0,
            'status' => 1,
            
        ]);

        // доп условие
        if ($params['value_table'] === "all") {

            $balance = $this->filter_for_FistBalance($builder, $params);
            return $balance ? $balance : false;


        }else{
            $builder->where('value_table', $params['value_table']);
        }


        

        // получить
        $builder->select('balance');
        $result = $builder->get()->getRow();


        return $result ? $result->balance : false;
    }

    private function filter_for_FistBalance($builder, $params)
    {
        //получить из org name из users
        $UserModel = new UserModel();
        $colum_name = $params['name_table'].'_id';
        $colum_name2 = $params['name_table'].'_code';
        $user = $UserModel->find($params['user_login']);
        $ids = explode(',', $user[$colum_name]);


        $model = $this->getModal_for_FistBalance($params['name_table']);
        $results = $model->select($colum_name2)->whereIn($colum_name, $ids)->findAll();

        // Получаем массив значений `code`
        $valueTable = array_column($results, $colum_name2);


        // $valueTable = ['01ДШБ', '63ДШБ'];


        //условие
        $builder->whereIn('value_table', $valueTable);

        $cloneBuilder = clone $builder;

        // Выполнение запроса
        $result = $builder->get()->getResultArray();

        // Проверяем, все ли значения присутствуют в результатах
        $foundValues = array_column($result, 'value_table');

        foreach ($valueTable as $value) {
            if (!in_array($value, $foundValues)) {
                return false;  
            }
        }

        // условие
        $cloneBuilder->selectSum('balance', 'balance_sum');

        // Выполнение запроса
        $result = $cloneBuilder->get()->getRow();

        // Вернуть сумму balance
        return $result ? $result->balance_sum : false;
    }

    private function getModal_for_FistBalance($table_name)
    {
        switch ($table_name) {
            case 'agency':
                $model = new AgencyModel();
                return $model;
                break;
            case 'stamp':
                $model = new StampModel();
                return $model;
                break;
            case 'tap':
                $model = new TapModel();
                return $model;
                break;
            case 'opr':
                $model = new OprModel();
                return $model;
                break;
        }
    }



}


        
