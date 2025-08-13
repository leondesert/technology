<?php

namespace App\Controllers;

require_once '../vendor/autoload.php';

use App\Models\ServicesModel;
use App\Models\UserModel;
use App\Models\AgencyModel;
use App\Models\StampModel;
use App\Models\TapModel;
use App\Models\OprModel;
use App\Models\AcquiringModel;

use App\Controllers\Transactions;
use App\Controllers\LogsController;
use App\Controllers\Dashboard;
use App\Controllers\Profile;
use App\Controllers\PaysController;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;


class ServicesController extends BaseController
{   

    public function index()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }

        

        //////////////////////////////////////////////////////////////////////////////////

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
            $users = $userModel->where("FIND_IN_SET('$user_id', parent) >", 0)->findAll();
        }

        // Добавляем ФИО к логину для отображения в фильтре
        foreach ($users as &$userfio) {
            if (!empty(trim((string) $userfio['fio']))) {
                $userfio['user_login'] .= ' (' . trim($userfio['fio']) . ')';
            }
        }
        unset($userfio);


        // для поле Валюта
        $currencies = ['TJS', 'RUB'];
        
        // для поле Дата
        $dashboard = new Dashboard(); 
        $dates = $dashboard->getDates();


        
        $ProfileController = new Profile();
        
        $data = [
            'users' => $users,
            'currencies' => $currencies,
            'dates' => $dates,
            'user_id' => $user_id,
            'username' => $username,
            'role' => $role,
            'user' => $user,
            'filter_values' => $ProfileController->get_filter_values()
        
        ];


        return view('services/index', $data);
    }

    public function get_services()
    {
        $TransactionsController = new Transactions();
        $BigExportController = new BigExportController();

        $request = service('request');
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'];


        // Получаем данные из POST-запроса
        $filters = $this->request->getPost();


        // Убираем лишнее преобразование, теперь value_table всегда ID
        

        // Инициализация модели
        $ServicesModel = new ServicesModel();


        $totalRecords = $ServicesModel->countAll();
        $totalRecordwithFilter = $ServicesModel->countFiltered($searchValue, $filters);

        $data = $ServicesModel->getFilteredData($start, $length, $searchValue, $filters, $request->getPost('order'), $request->getPost('columns'));




        
        

        // изменить колонки
        foreach ($data as &$item) {
            $original_item_name_key = $item['name'] ?? 'unknown'; // Тип организации (например, 'agency', 'share')
            $original_item_value_id = $item['value'] ?? null;    // ID организации

            // ========= установить рядом (название)
            
            // Получаем код организации (например, 'AG001')
            $code = $TransactionsController->get_column($original_item_name_key, $original_item_value_id, '_id', '_code');
            // Получаем описательное имя организации (например, 'Название Агентства Х')
            $name_suffix = $TransactionsController->get_column($original_item_name_key, $original_item_value_id, '_id', '_name');
            
            // Устанавливаем $item['name'] в человекочитаемый тип организации (например, 'Агенство', 'Раздача')
            // Если getName вернет null, используем оригинальный ключ, сделав первую букву заглавной.
            $item['name'] = (string) ($BigExportController->getName($original_item_name_key) ?? ucfirst($original_item_name_key));

            // Формируем $item['value'] как строку: "код (описательное имя)"
            // Гарантируем, что $item['value'] всегда будет строкой.
            if ($code !== false && $code !== null) {
                $item['value'] = (string)$code;
                if ($name_suffix !== false && $name_suffix !== null && trim((string)$name_suffix) !== '') {
                    $item['value'] .= ' (' . (string)$name_suffix . ')';
                }
            } else {
                // Если код не найден, отображаем ID и сообщение
                $item['value'] = 'ID: ' . (string)$original_item_value_id . ' (Код/Имя не найдены)';
            }
        }
        unset($item); // Важно разорвать ссылку на последний элемент массива


        $response = [
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        ];

        return $this->response->setJSON($response);
    }

    public function create()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }

        // Log
        $action = 'Вход в страницу Услуги/Создать';
        $logger = new LogsController(); 
        $logger->logAction($action);


        $user_id = session()->get('user_id');
        $model = new UserModel();
        $user = $model->find($user_id);
        


        $currencies = ['TJS', 'RUB'];
        $methods = ['Банковский перевод', 'Наличная оплата',];

        $model = new AcquiringModel();
        $acquirings = $model->findAll();

        $TransactionsController = new Transactions();
        $banks = $TransactionsController->list_banks();


        $data = [
            'currencies' => $currencies,
            'methods' => $methods,
            'acquirings' => $acquirings,
            'user' => $user,
            'banks' => $banks,
            

        ];


        return view('services/create', $data);
    }

    public function register()
    {
        $model = new ServicesModel();
        
        $TransactionsController = new Transactions();

        $name_table = $this->request->getPost('name_table');
        $value_table = $this->request->getPost('value_table');
        $method = $this->request->getPost('method');
        $bank = $this->request->getPost('bank');
        $acquiring = $this->request->getPost('acquiring');
        // Убираем лишнее преобразование, так как теперь в форме передается ID
        $value_table = $TransactionsController->get_column($name_table, $value_table, '_code', '_id');


        if ($acquiring === 'not_select') {
            $acquiring = null;
        }
        if ($method === 'Наличная оплата') {
            $bank = null;
        }

        $data = [
            'amount' => $this->request->getPost('amount'),
            'currency' => $this->request->getPost('currency'),
            'doc_date' => $this->request->getPost('doc_date'),
            'doc_number' => $this->request->getPost('doc_number'),
            'service_name' => $this->request->getPost('service_name'),
            'doc_scan' => $this->request->getPost('doc_scan'),
            'note' => $this->request->getPost('note'),
            'name' => $name_table,
            'value' => $value_table,
            'method' => $method,
            'acquiring' => $acquiring,
            'bank' => $bank,
            
        ];

        $file = $this->request->getFile('doc_scan');
        if ($file->isValid() && !$file->hasMoved()) {
            // Check the file extension
            $validExtensions = ['pdf', 'doc'];
            $extension = $file->getExtension();

            if (in_array($extension, $validExtensions)) {
                // Create the save path if it doesn't exist
                $path = FCPATH . 'uploads/checks';
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }

                $newName = $file->getRandomName();
                $file->move($path, $newName);
                
                $data['doc_scan'] = $newName;
            } else {
                // Handle invalid file extension
                return redirect()->back()->withInput()->with('error', 'Неверное расширение файла! Допустимы только jpg, jpeg, png.');
            }
        }

        if ($model->insert($data)) {
            // On successful save
            return redirect()->to('/services')->with('success', 'Успешно создан!');
        } else {
            // Handle save errors
            return redirect()->back()->withInput()->with('error', 'Ошибка!');
        }
    }

    public function edit($id)
    {
    
        $user_id = session()->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->where('user_id', $user_id)->first();
        $TransactionsController = new Transactions();
        

        $model = new ServicesModel();
        $services = $model->find($id);
        // Убираем преобразование, так как теперь в форме используется ID
        $services['value'] = $TransactionsController->get_column($services['name'], $services['value'], '_id', '_code');


        $currencies = ['TJS', 'RUB'];
        $methods = ['Банковский перевод', 'Наличная оплата'];

        $model = new AcquiringModel();
        $acquirings = $model->findAll();

        $TransactionsController = new Transactions();
        $banks = $TransactionsController->list_banks();


        $data = [
            'services' => $services,
            'currencies' => $currencies,
            'methods' => $methods,
            'acquirings' => $acquirings,
            'user' => $user,
            'banks' => $banks,
        ];


        // Log
        $action = 'Попытка изменить Услуги';
        $logger = new LogsController(); 
        $logger->logAction($action);


        return view('services/edit', $data);
    }

    public function update($id)
    {   
        
        $model = new ServicesModel();
        $TransactionsController = new Transactions();

        $name_table = $this->request->getPost('name_table');
        $value_table = $this->request->getPost('value_table');
        $method = $this->request->getPost('method');
        $bank = $this->request->getPost('bank');
        $acquiring = $this->request->getPost('acquiring');
        // Убираем лишнее преобразование, так как теперь в форме передается ID
        $value_table = $TransactionsController->get_column($name_table, $value_table, '_code', '_id');

        if ($acquiring === 'not_select') {
            $acquiring = null;
        }
        if ($method === 'Наличная оплата') {
            $bank = null;
        }

        $data = [
            'amount' => $this->request->getPost('amount'),
            'currency' => $this->request->getPost('currency'),
            'doc_date' => $this->request->getPost('doc_date'),
            'doc_number' => $this->request->getPost('doc_number'),
            'service_name' => $this->request->getPost('service_name'),
            'doc_scan' => $this->request->getPost('doc_scan'),
            'note' => $this->request->getPost('note'),
            'name' => $name_table,
            'value' => $value_table,
            'method' => $method,
            'acquiring' => $acquiring,
            'bank' => $bank,
        ];


        $file = $this->request->getFile('doc_scan');


        if ($file->isValid() && !$file->hasMoved()) {
            // Создайте путь сохранения, если он не существует
            $path = WRITEPATH . 'uploads/checks';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $newName = $file->getRandomName();
            $file->move($path, $newName);
            
            $data['doc_scan'] = $newName;
        }
        
        

        if ($model->update($id, $data)) {
            // В случае успешного сохранения

            // Log
            $action = 'Изменен Услуги ID: '.$id;
            $logger = new LogsController(); 
            $logger->logAction($action);


            return redirect()->to('/services')->with('success', 'Успешно обновлен!');
        } else {
            // Обработка ошибок сохранения
            return redirect()->back()->withInput()->with('success', 'Ошибка!');
        }
    }

    public function delete($id)
    {
        // Handle the deletion of the agency from the database
        $model = new ServicesModel();
        $model->delete($id);


        // Log
        $action = 'Удалено Услуги ID: '.$id;
        $logger = new LogsController(); 
        $logger->logAction($action);


        // Redirect to the list of agencies after deletion
        return redirect()->back()->with('success', 'Успешно удален!');
    }



    public function get_downtable()
    {


        // Получаем параметры из POST-запроса
        $filters = $this->request->getPost();

        $ServicesModel = new ServicesModel();
        $TransactionsController = new Transactions();
        $BigExportController = new BigExportController();
        

        // Проверяем, существует ли 'value_table' перед использованием
        // Если value_table не "all", то предполагается, что он уже является ID.
        // Преобразование из _code в _id больше не требуется, так как фронтенд передает ID.
        // if (($filters['value_table'] ?? 'all') !== "all") {
        //     $filters['value_table'] = $TransactionsController->get_column($filters['name_table'], $filters['value_table'], '_code', '_id');
        // }


        $results = $ServicesModel->getDataForDowntable($filters);


        $tableData = $results['results'];


        // изменить колонки
        foreach ($tableData as &$item) {

            // ========= установить рядом (название)
            
            $name = $TransactionsController->get_column($item['name'], $item['value'], '_id', '_name');
            $code = $TransactionsController->get_column($item['name'], $item['value'], '_id', '_code');
            

            $item['value'] = $code;
            $item['name'] = $BigExportController->getName($item['name']);

            if ($name) {
                $item['value'] = $code . ' ('.$name.')';
            }
        }



        // Новый элемент для добавления в конец массива
        $newData = [
            "name" => "Итого:",
            "value" => "",
            "count" => $results['totalCount'],
            "summa" => $results['totalSum']
        ];
        array_push($tableData, $newData);



        return $this->response->setJSON($tableData);


    }










    
}