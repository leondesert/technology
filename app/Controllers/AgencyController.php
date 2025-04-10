<?php

namespace App\Controllers;

use App\Models\AgencyModel;
use App\Models\UserModel;
use App\Models\RewardsModel;
use App\Controllers\LogsController;

class AgencyController extends BaseController
{


    public function index()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); // Редирект на страницу входа, если не авторизован
        }

        // Log
        $action = 'Вход в страницу Агенства';
        $logger = new LogsController(); 
        $logger->logAction($action);

        $role = session()->get('role');

        $data = [
            'role' => $role,
        ];
            
        
        return view('organization/agency/index', $data);
    }



    public function create()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }

        // Log
        $action = 'Вход в страницу Агенства/Создать';
        $logger = new LogsController(); 
        $logger->logAction($action);
        
        return view('organization/agency/create');
    }

    public function register()
    {
        
        $model = new AgencyModel();

        $data = [
            'agency_code' => $this->request->getPost('agency_code'),
            'agency_name' => $this->request->getPost('agency_name'),
            'agency_address' => $this->request->getPost('agency_address'),
            'agency_phone' => $this->request->getPost('agency_phone'),
            'agency_mail' => $this->request->getPost('agency_mail'),
            'reward' => $this->request->getPost('reward'),
            'balance_tjs' => $this->request->getPost('balance_tjs'),
            'balance_rub' => $this->request->getPost('balance_rub'),
            'penalty' => $this->request->getPost('penalty'),
        ];

        $model->insert($data);


        return redirect()->to('/agency')->with('success', 'Успешно создан!');   
    }

    public function reg_reward()
    {

        $model = new RewardsModel();

        $action = $this->request->getVar('action');
        $id = $this->request->getVar('id');
        $method = $this->request->getVar('method');
        $type = $this->request->getVar('type');
        $name = $this->request->getVar('name');
        $code = $this->request->getVar('code');
        $percent = $this->request->getVar('procent');
        $reward_id = $this->request->getVar('list');


        $status = "Успешно!";


        switch ($action) {
            case 'create':
                
                $existingRecord = $model->where('code', $code)
                        ->where('name', $name)
                        ->where('value', $id)
                        ->first();


                if (is_null($existingRecord)) {
                $model->insert([
                    'method' => $method,
                    'type' => $type,
                    'procent' => $percent,
                    'code' => $code,
                    'name' => $name,
                    'value' => $id,
                ]);

                } else {
                    $status = "Запись с такими параметрами уже существует.";
                }

                break;
            case 'edit':
               
                $model->update($reward_id, [
                    'procent' => $percent,
                    'code' => $code,
                ]);
                break;
            case 'delete':
                
                $model->delete($reward_id);
                break;
        }


        return redirect()->back()->with('success', $status);
    }

    public function get_reward()
    {
        // Получаем объект запроса
        $request = service('request');
        
        // Получаем значение параметра tableName
        $method = $request->getGet('method');
        $type = $request->getGet('type');
        $name = $request->getGet('name');
        $value = $request->getGet('value');

        $model = new RewardsModel();
        $tableData = $model->where('value', $value)
                           ->where('name', $name)
                           ->where('type', $type)
                           ->where('method', $method)
                           ->findAll();


        return $this->response->setJSON($tableData);      
    }

    public function edit($id)
    {
        $role = session()->get('role');
        $model = new AgencyModel();
        $agency = $model->find($id);

        $model = new RewardsModel();
        $rewards = $model->where('value', $id)->where('name', 'agency')->findAll();

        // скрыть/показать поле вознаграждения, штраф
        $userId = session()->get('user_id');
        $model = new UserModel();
        $user = $model->find($userId);
        $agency_ids = $user['agency_id'];
        $hidden = true;

        if ($role !== "superadmin") {
            if (!empty($agency_ids)) {
                $hidden = false;
            }
        }
        


        $data = [
            'id' => $agency['agency_id'],
            'code' => $agency['agency_code'],
            'name' => $agency['agency_name'],
            'address' => $agency['agency_address'],
            'phone' => $agency['agency_phone'],
            'mail' => $agency['agency_mail'],
            'balance_tjs' => $agency['balance_tjs'],
            'balance_rub' => $agency['balance_rub'],
            'penalty' => $agency['penalty'],
            'reward' => $agency['reward'],
        ];


        $data = [
            'rewards' => $rewards,
            'hidden' => $hidden,
            'role' => $role,
            'data' => $data,
            'name' => 'agency',
        ];


        // Log
        $action = 'Изменить Агенство';
        $logger = new LogsController(); 
        $logger->logAction($action);


        return view('organization/agency/edit', $data);
    }

    public function update($id)
    {
        $role = session()->get('role');
        $model = new AgencyModel();

        $data = [
            'agency_name' => $this->request->getPost('name'),
            'agency_address' => $this->request->getPost('address'),
            'agency_phone' => $this->request->getPost('phone'),
            'agency_mail' => $this->request->getPost('mail'),
            'reward' => $this->request->getPost('reward'),
            'balance_tjs' => $this->request->getPost('balance_tjs'),
            'balance_rub' => $this->request->getPost('balance_rub'),
            'penalty' => $this->request->getPost('penalty'),
        ];

        if ($role === 'superadmin') {
            $data['agency_code'] = $this->request->getPost('code');
        }


        $datajson = json_encode($data);


        if ($model->update($id, $data)) {

            // Log
            $action = 'Успешно изменен Агенство';
            $logger = new LogsController(); 
            $logger->logAction($action, $datajson);

            return redirect()->to('/agency')->with('success', 'Успешно обновлен!');
        }else{

            // Log
            $action = 'Не успешно изменен Агенство';
            $logger = new LogsController(); 
            $logger->logAction($action, $datajson);

            return redirect()->to('/agency')->with('error', 'Ошибка!');
        }
    }

    public function delete($id)
    {

        $model = new AgencyModel();
        $model->delete($id);


        // Log
        $action = 'Удалено Агенство';
        $logger = new LogsController(); 
        $logger->logAction($action);


        return redirect()->back()->with('success', 'Успешно удален!');
    }

    public function getDataTable()
    {

        $table_name = $this->request->getPost('name_table');
        $colum_name = $table_name.'_id';
        $currencies = ['TJS', 'RUB'];

        $user_id = session()->get('user_id');
        $model = new UserModel();
        $user = $model->find($user_id);
        $ids = explode(',', $user[$colum_name]);

        $BigExportController = new BigExportController();
        $model = $BigExportController->getModal($table_name);

        $role = session()->get('role');

        if ($role === "superadmin") {
            $results = $model->findAll();
        }else{
            $results = $model->whereIn($colum_name, $ids)->findAll();

        }

        // $results = $model->whereIn($colum_name, $ids)->findAll();

        // балансы как кнопка
        foreach ($results as $key => $item) {
            $results[$key]['balance_tjs'] = '<button type="button" class="btn btn-primary btn-sm showValue" value="'.$item[$table_name.'_code'].'" currency="TJS"><i class="fas fa-eye"></i></button>';
            $results[$key]['balance_rub'] = '<button type="button" class="btn btn-primary btn-sm showValue" value="'.$item[$table_name.'_code'].'" currency="RUB"><i class="fas fa-eye"></i></button>';
        }




        // Цикл для добавления поля action к каждой транзакции
        foreach ($results as &$result) {
            $result['code'] = $result[$table_name.'_code'];
            $result['name'] = $result[$table_name.'_name'];
            $result['address'] = $result[$table_name.'_address'];
            $result['phone'] = $result[$table_name.'_phone'];
            $result['mail'] = $result[$table_name.'_mail'];
            $edit_button = '<a href="' . base_url($table_name . '/edit/' . $result[$colum_name]) . '" class="btn btn-info btn-sm"><i class="fas fa-pencil-alt"></i></a>';
            $delete_button = '';

            if ($role == 'superadmin') {
                $delete_button = ' <a href="' . base_url($table_name . '/delete/' . $result[$colum_name]) . '" class="btn btn-danger btn-sm" onclick="return confirmDelete()"><i class="fas fa-trash"></i></a>';
            }

            $result['action'] = $edit_button . $delete_button;

        }

        $data = [
            'data' => $results,
        ];


        return $this->response->setJSON($data);
    }



    public function balance_today($filter)
    {
        
        $params =  [

            "searchBuilder" => [ 
                "criteria" => [
                    [
                        "condition" => "between",
                        "data" => "Дата формирования",
                        "origData" => "tickets.tickets_dealdate",
                        "type" => "date",
                        "value" => [$filter['day'], $filter['day']]
                    ],
                    [
                        "condition" => "=",
                        "data" => "Валюта билета",
                        "origData" => "tickets.tickets_currency",
                        "type" => "string",
                        "value" => [$filter['currency']]
                    ],
                    [
                        "condition" => "=",
                        "data" => $filter['data'],
                        "origData" => $filter['origData'],
                        "type" => "string",
                        "value" => [$filter['value']]
                    ],

                ],
                "logic" => "AND",
            ],
            "user_login" => $filter['user_login'],
            "name_table" => $filter['name_table'],
            "value_table" => $filter['value'],
            "currency" => $filter['currency'],
            "start_date" => $filter['day'],
            "end_date" => $filter['day'],
            "type" => $filter['type'],
        ];

        $BigExportController = new BigExportController();
        $response = $BigExportController->for_summaryTable($params);

        // return $response;
        return $response['OTCHET']['8'];
    }


    public function getBalances()
    {
        $table_name = $this->request->getPost('name_table');
        $value = $this->request->getPost('value');
        $currency = $this->request->getPost('currency');
        $type = $this->request->getPost('type');
        
        $BigExportController = new BigExportController();

        $filter = [

            'data' => $BigExportController->getName($table_name),
            'origData' => $table_name.'.'.$table_name.'_code',
            'currency' => $currency,
            'name_colum' => 'balance_'.strtolower($currency),
            'name_table' => $table_name,
            'user_login' => session()->get('user_id'),
            'day' => date('Y-m-d'),
            'value' => $value,
            'type' => $type

        ];

        
        $value = $this->balance_today($filter);
        
        return $this->response->setJSON($value);
    }
    
    




}
