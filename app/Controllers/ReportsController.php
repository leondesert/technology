<?php

namespace App\Controllers;

use App\Models\ReportsModel;
use App\Models\UserModel;
use App\Controllers\Dashboard;

use App\Controllers\LogsController;
use App\Controllers\BigExportController;


class ReportsController extends BaseController
{
    public function index()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }


        // для поле пользователь
        $user_id = session()->get('user_id');
        $model = new UserModel();
        $user = $model->find($user_id);
        $username = session()->get('username');
        $userModel = new UserModel();
        $role = session()->get('role');
        if($role === "superadmin"){
            $users = $userModel->findAll();
        }else{
            $users = $userModel->where('parent', $user_id)->findAll();
        }

        // для поле Дата
        $dashboard = new Dashboard(); 
        $dates = $dashboard->getDates();

        // для поле Валюта
        $currencies = ['TJS', 'RUB'];

        $ProfileController = new Profile();

        $data = [
            'role' => $role,
            'users' => $users,
            'user_id' => $user_id,
            'username' => $username,
            'dates' => $dates,
            'currencies' => $currencies,
            'filter_values' => $ProfileController->get_filter_values()
        ];



        return view('reports/index', $data);
    }


    public function get_organisation_values($name_table, $user_id)
    {
        $colum_name = $name_table.'_id';
        $model = new UserModel();
        $user = $model->find($user_id);
        $ids = explode(',', $user[$colum_name]);

        $BigExportController = new BigExportController();
        $model = $BigExportController->getModal($name_table);


        $results = $model->whereIn($colum_name, $ids)->findAll();

        return $results;
    }


    public function create_searchBuilder($filter)
    {
        $params =  [

            "searchBuilder" => [ 
                "criteria" => [
                    [
                        "condition" => "between",
                        "data" => "Дата формирования",
                        "origData" => "tickets.tickets_dealdate",
                        "type" => "date",
                        "value" => [$filter['start_date'], $filter['end_date']]
                    ],
                    [
                        "condition" => "=",
                        "data" => "Валюта билета",
                        "origData" => "tickets.tickets_currency",
                        "type" => "string",
                        "value" => [$filter['currency']]
                    ],
                    

                ],
                "logic" => "AND",
            ],
            "user_login" => $filter['user_login'],
            "name_table" => $filter['name_table'],
            "value_table" => $filter['value_table'],
            "currency" => $filter['currency'],
            "type" => $filter['report_type'],
            'start_date' => $filter['start_date'],
            'end_date' => $filter['end_date'],
        ];



        //если есть значение организации
        if ($filter['is_org']) {
            $params['searchBuilder']['criteria'][] = [
                        "condition" => "=",
                        "data" => $filter['data'],
                        "origData" => $filter['origData'],
                        "type" => "string",
                        "value" => [$filter['value_table']]
                    ];
        }


        return $params;
    }


    public function sendreport()
    {
        // 1. получить данные 
        $start_date = $this->request->getPost('startDate');
        $end_date = $this->request->getPost('endDate');
        $currency = $this->request->getPost('currency');
        $name_table = $this->request->getPost('name_table');
        $value_table = $this->request->getPost('value_table');
        $user_id = $this->request->getPost('user_login');
        $report_type = $this->request->getPost('report_type');


        $reportModel = new ReportsModel();
        $BigExportController = new BigExportController();


        $values = [];

        if ($value_table !== "all") {
            $values[] = $value_table;
        }else{
            $values = $this->get_organisation_values($name_table, $user_id);
        }

        
        

        // 3. добавить Балансы отчета
        $values_table = [];


        foreach ($values as $value) {

            
            $name_code = $name_table."_code";


            if ($value_table !== "all") {
                $vall = $value;
            }else{
                $vall = $value[$name_code];
            }


            $values_table[] = $vall;

            $filter = [

                'data' => $BigExportController->getName($name_table),
                'origData' => $name_table.'.'.$name_table.'_code',
                'currency' => $currency,
                'report_type' => $report_type,
                'name_table' => $name_table,
                'user_login' => $user_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'value_table' => $vall,
                'is_org' => true

            ];

            

            


            // Проверяем, существует ли запись с такими же данными
            $existingReport = $reportModel
            // ->where('user_id', $user_id) //global
                                          ->where('start_date', $start_date)
                                         ->where('end_date', $end_date)
                                         ->where('currency', $currency)
                                         ->where('report_type', $report_type)
                                         ->where('name_table', $name_table)
                                         ->where('value_table', $vall)
                                         ->groupStart()
                                              ->where('status', 0)
                                              ->orWhere('status', 1)
                                          ->groupEnd()
                                         ->first();

            // Если запись не найдена, добавляем новую
            if (!$existingReport) {

                //получить баланс
                $params = $this->create_searchBuilder($filter);

                // получить отчет
                $result = $BigExportController->for_summaryTable($params);
                $balance = $result['OTCHET']['8'];
                
                // // новые данные
                // $params['fist_balance'] = true; // получить баланс именно а не отчет
                // $params['balance_date'] = $BigExportController->balance_date();

                // $OTCHET = $BigExportController->getBalanceFirst($params);
                // $params['fist_balance'] = false; 
                // $OTCHET = $BigExportController->report($params, $OTCHET['8']);
                // $balance = $OTCHET['8'];


                $data = [
                    'user_id' => $user_id,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'currency' => $currency,
                    'report_type' => $report_type,
                    'name_table' => $name_table,
                    'value_table' => $vall,
                    'balance' => $balance,
                    'is_report' => 0,
                    'status' => 0
                ];


                $response = $reportModel->insert($data);
            }
        }

        


        // 4. добавить сам отчет
        $values_table = implode(',', $values_table);
        
        // Проверяем, существует ли запись с такими же данными
        $existingReport = $reportModel
        // ->where('user_id', $user_id) //global
                                     ->where('start_date', $start_date)
                                     ->where('end_date', $end_date)
                                     ->where('currency', $currency)
                                     ->where('report_type', $report_type)
                                     ->where('name_table', $name_table)
                                     ->where('value_table', $values_table)
                                     ->where('is_report', 1)
                                     ->groupStart()
                                        ->where('status', 0)
                                        ->orWhere('status', 1)
                                     ->groupEnd()
                                     ->first();

        // Если запись не найдена, добавляем новую
        if (!$existingReport) {

            $data = [
                'user_id' => $user_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'currency' => $currency,
                'report_type' => $report_type,
                'name_table' => $name_table,
                'value_table' => $values_table,
                'balance' => 0,
                'is_report' => 1,
                'status' => 0
            ];


            $response = $reportModel->insert($data);

            // ответ
            return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Отчет успешно отправлен!', 
                    'report_type' => $report_type
                ]);

        }else{

            // ответ
            return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Отчет уже отправлен!', 
                    'report_type' => $report_type
                ]);

        }



        
    }


    public function get_reports()
    {

        $TransactionsController = new Transactions();

        $request = service('request');
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'];
        
        // Получаем данные из POST-запроса
        $filters = $this->request->getPost();


        // Инициализация модели
        $model = new ReportsModel();


        $totalRecords = $model->countAll();
        $totalRecordwithFilter = $model->countFiltered($searchValue, $filters);

        $data = $model->getFilteredData($start, $length, $searchValue, $filters, $request->getPost('order'), $request->getPost('columns'));

        $response = [
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data,
            "filters" => $filters
        ];

        return $this->response->setJSON($response);
    }

    public function create_params_for_report()
    {
    }


    public function show_report()
    {

        $id = $this->request->getPost('id');
        $BigExportController = new BigExportController();
        $ReportsModel = new ReportsModel();


        // формировать параметр
        $report = $ReportsModel->getReportById($id);


        $filter = [
            'data' => $BigExportController->getName($report['name_table']),
            'origData' => $report['name_table'].'.'.$report['name_table'].'_code',
            'currency' => $report['currency'],
            'report_type' => $report['report_type'],
            'name_table' => $report['name_table'],
            'user_login' => $report['user_id'],
            'start_date' => $report['start_date'],
            'end_date' => $report['end_date'],
         
        ];



        if (!strpos($report['value_table'], ',')) {
            $filter['is_org'] = true;
            $filter['value_table'] = $report['value_table'];
        }else{
            $filter['is_org'] = false;
            $filter['value_table'] = 'all';
        }


        
        // создать searchBuilder
        $params = $this->create_searchBuilder($filter);
        
        
    
        // получить отчет        
        $result = $BigExportController->for_summaryTable($params);
     

        
        // получаем параметры для распечатки
        $data = $this->dateForPrint($id);


        return $this->response->setJSON([
            'OTCHET' => $result['OTCHET'], 
            'response' => $result['response'],
            'data' => $data,
            'status' => $report['status'],
            'params' => $params,
            
            
        ]);
    }


    public function updateStatus()
    {
        // Получаем данные POST-запроса
        $params = $this->request->getPost();
        
        

        // изменить статусы
        $ReportsModel = new ReportsModel();
        $response = $ReportsModel->updateStatus($params);
        

        
        // получаем параметры для распечатки
        $data = $this->dateForPrint($params['id']);
        $finalStatus = $response['final_report_status']; // Получаем финальный статус из ответа модели

        // Проверка результата и возврат ответа
        if ($response['operation_successful']) { // Проверяем успех операций в БД

            $message = 'Статус изменен.';
            $message_type = 'success'; // Тип уведомления по умолчанию

            switch ($finalStatus) { // Используем финальный статус для определения сообщения
                case '1':
                    $message = 'Отчет одобрен!';
                    break;
                case '2':
                    // Если изначально пытались одобрить (статус '1'), но модель отклонила (статус '2')
                    if ($params['status'] == '1' && $finalStatus == '2') {
                        $balance_report_rounded = round(floatval($response['balance_report']), 2);
                        $params_balance_rounded = round(floatval($response['params_balance']), 2);
                        $message = "Отчет отклонен: Расчетная сумма ({$params_balance_rounded}) не совпадает с суммой деталей ({$balance_report_rounded}).";
                        $message_type = 'error'; // Красное уведомление для отклонения из-за расхождения
                    } else {
                        $message = 'Отчет отклонен.';
                        $message_type = 'error'; // Красное уведомление для обычного отклонения
                    }
                    break;
                case '0':
                    $message = 'Статус отчета: В обработке.';
                    break;
                default:
                    break;
            }

            return $this->response->setJSON([
                'status' => $message_type, // Используем message_type для определения цвета в JS
                'message' => $message,
                'data' => $data,
                'report_actual_status' => $finalStatus // Передаем актуальный статус в JS
                
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Ошибка при изменения статуса',
                'response' => $response, // Для отладки
            ]);
        }
    }


    public function dateForPrint($id_report)
    {
        // получаем параметры для распечатки
        $ReportsModel = new ReportsModel();
        $BigExportController = new BigExportController();

        $report = $ReportsModel->getReportById($id_report);
        $report['name_table'] = $BigExportController->getName($report['name_table']);
        $UserModel = new UserModel();

        // пользователь отчета
        $user = $UserModel->find($report['user_id']);
        $fio_user = $user['fio'];
        $secret_key_user = $user['secret_key'];
        $shapka = $user['user_desc'];

        // пользователь текущий
        $user_id = session()->get('user_id');
        $user = $UserModel->find($user_id);
        $fio_admin = $user['fio'];
        $secret_key_admin = $user['secret_key'];

        // создаем QR-code
        $qrcode_user = $this->create_qrcode($secret_key_user, $report);
        $qrcode_admin = $this->create_qrcode($secret_key_admin, $report);


        // формируем данные
        $data = [
            'report' => $report,
            'qrcode_user' => $qrcode_user,
            'qrcode_admin' => $qrcode_admin,
            'fio_user' => $fio_user,
            'fio_admin' => $fio_admin,
            'user_desc' => $shapka,
        ];

        return $data;
    }

    public function deleteReport()
    {
        // Получаем ID отчета из POST-запроса
        $reportId = $this->request->getPost('id');

        // Инициализируем модель
        $reportModel = new ReportsModel();

        // Проверяем, существует ли запись с данным ID
        $report = $reportModel->find($reportId);

        if ($report) {
            // Удаляем запись
            $reportModel->deleteReport($reportId);

            // Возвращаем успешный ответ
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Отчет успешно удален'
            ]);
        } else {
            // Если отчета не существует
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Отчет не найден'
            ]);
        }
    }
    

    private function create_qrcode($secret_key, $array)
    {   

        $data = [
            'id' => $array['id'],
            'value_table' => $array['value_table'],
            'start_date' => $array['start_date'],
            'end_date' => $array['end_date'],
            'currency' => $array['currency'],
            'balance' => $array['balance'],
        ];

        // Преобразуем массив в JSON-строку
        $json = json_encode($data);

        // Генерируем хэш с использованием HMAC SHA-256
        $hash = hash_hmac('sha256', $json, $secret_key);


        $data = [
            'id' => $array['id'],
            'hash' => $hash,
        ];


        // Преобразуем массив в JSON-строку
        $datajson = json_encode($data);


        return $datajson;
    }

    public function check_qrcode($secret_key, $array)
    {
        
        
    }



}
//checkpoint commit