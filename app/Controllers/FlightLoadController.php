<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\FlightLoadModel;
use App\Controllers\LogsController;
use App\Controllers\BigExportController;

class FlightLoadController extends BaseController
{

    public function index()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }


        // для поле пользователь
        $user_id = session()->get('user_id');
        $username = session()->get('username');
        $userModel = new UserModel();
        $role = session()->get('role');
        if($role === "superadmin"){
            $users = $userModel->findAll();
        }else{
            $users = $userModel->where("FIND_IN_SET('$user_id', parent) >", 0)->findAll();
        }

        // Добавляем ФИО к логину для отображения в фильтре
        foreach ($users as &$user) {
            if (!empty(trim((string) $user['fio']))) {
                $user['user_login'] .= ' (' . trim($user['fio']) . ')';
            }
        }
        unset($user);

        // для поле Дата
        $dashboard = new Dashboard(); 
        $dates = $dashboard->getDates();


        $ProfileController = new Profile();


        $data = [
            'users' => $users,
            'user_id' => $user_id,
            'username' => $username,
            'role' => $role,
            'dates' => $dates,
            'filter_values' => $ProfileController->get_filter_values()
        ];



        // Log
        $action = 'Вход в страницу Загрузка рейса';
        $logger = new LogsController(); 
        $logger->logAction($action);

        return view('flightload/index', $data);
    }

    public function getData()
    {

        // Получаем параметры из POST-запроса
        // $name_table = $this->request->getPost('name_table');
        // $value_table = $this->request->getPost('value_table');

        $user_id = session()->get('user_id');

        $params = [

                    "searchBuilder" => [ 
                        "criteria" => [
                            [
                                "condition" => "between",
                                "data" => "Дата формирования",
                                "origData" => "tickets.tickets_dealdate",
                                "type" => "date",
                                "value" => ['2024-05-16', '2024-05-16']
                            ]
                            

                        ],
                        "logic" => "AND",
                    ],
                    "user_login" => $user_id,
                    "name_table" => 'agency',

                ];


        $params['visibleColumns'] = 'Тип билета,Валюта билета,Дата формирования,Время формирования,Тип операции,Тип транзакции,Номер билета,Номер старшего билета,Номер основного билета,Тариф цена,PNR,Дата оформления,Индентификатор продавца,Время оформления,Время оформления UTC,Сумма обмена без EMD,Код оператора,Код агентства,Сумма EMD,Вид оплаты,Сумма оплаты,ФИО,Паспорт,Тип,Гражданство,Маршрут,Перевозчик,Класс,Рейс,Дата полёта,Время полёта,Тариф,Код ППР,Код пульта,Код сбора,Сумма сбора';


        $getData = new BigExportController(); 
        $table_data = $getData->getData($params);

        // $table_data = [1,2,3];

        $data = [
            'table_data' => $table_data,
        ];


        return $this->response->setJSON($data);
    }

    public function fetchData()
    {
        $request = service('request');
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'];

        // Получение дополнительных параметров
        $startDate = $request->getPost('startDate');
        $endDate = $request->getPost('endDate');
        $flydate = $request->getPost('flydate');
        $citycodes = $request->getPost('citycodes');
        $flytime = $request->getPost('flytime');
        $user_login = $request->getPost('user_login');
        $name_table = $request->getPost('name_table');
        $value_table = $request->getPost('value_table');

        // для фильтра
        if ($name_table === "all") {
            $user_login = session()->get('user_id');
            $name_table = session()->get('filter');
        }
        
        $model = new UserModel();
        $user = $model->where('user_id', $user_login)->first();
        $c_name = $name_table.'_id';
        $ids = $user[$c_name];
        $ids = explode(",", $ids);
        $ids = array_map('intval', $ids);
        $colum_name = "tickets.".$c_name;
        

        $filters = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'flydate' => $flydate,
            'citycodes' => $citycodes,
            'flytime' => $flytime,
            'colum_name' => $colum_name,
            'ids' => $ids,
            'name_table' => $name_table,
            'value_table' => $value_table
        ];

        $model = new FlightLoadModel();

        $totalRecords = $model->countAll();
        $totalRecordwithFilter = $model->countFiltered($searchValue, $filters);

        $data = $model->getFilteredData($start, $length, $searchValue, $filters, $request->getPost('order'), $request->getPost('columns'));

        $response = [
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        ];

        return $this->response->setJSON($response);
    }
    
    public function popularFlights()
    {   
        $request = service('request');
        $startDate = $request->getPost('startDate');
        $endDate = $request->getPost('endDate');
        $flydate = $request->getPost('flydate');
        $citycodes = $request->getPost('citycodes');
        $flytime = $request->getPost('flytime');
        $user_login = $request->getPost('user_login');
        $name_table = $request->getPost('name_table');
        $value_table = $request->getPost('value_table');
        $filterby = $request->getPost('filterby');
        $show = $request->getPost('show');

        // для фильтра
        if ($name_table === "all") {
            $user_login = session()->get('user_id');
            $name_table = session()->get('filter');
        }
        
        $model = new UserModel();
        $user = $model->where('user_id', $user_login)->first();
        $c_name = $name_table.'_id';
        $ids = $user[$c_name];
        $ids = explode(",", $ids);
        $ids = array_map('intval', $ids);
        $colum_name = "tickets.".$c_name;
        

        $filters = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'flydate' => $flydate,
            'citycodes' => $citycodes,
            'flytime' => $flytime,
            'colum_name' => $colum_name,
            'ids' => $ids,
            'name_table' => $name_table,
            'value_table' => $value_table,
            'filterby' => $filterby,
            'show' => $show,

        ];


        $flightModel = new FlightLoadModel();
        $response = $flightModel->getPopularFlights($filters);
        
        // доход
        // foreach($response as $key => $item){
            // $report = new AnalyticsController();
            // $data = $report->report($item->tickets_dealdate, $currency, $name_table, $value_table, $user_login);
            // $response[$key]->total_reward = round($data['reward'], 2);
            // $response[$key]->total_reward = 0;
        // }


        // Загрузка представления с данными
        return $this->response->setJSON($response);
    }






}
