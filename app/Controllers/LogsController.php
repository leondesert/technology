<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\LogsModel;
use App\Models\UserModel;
use App\Controllers\LogsController;



class LogsController extends Controller
{
    public function index()
    {

        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }


        // // Log
        // $action = 'Вход в страницу Логи';
        // $logger = new LogsController(); 
        // $logger->logAction($action);



        // Получить список дочерных пользователей
        // показать лог только своего аккаунта и дочериных аккаунтов
        $role = session()->get('role');
        $user_id = session()->get('user_id');
        $userModel = new UserModel();
        if($role === "superadmin"){
            $users = $userModel->findAll();
        }else{
            $users = $userModel->where('parent', $user_id)->findAll();
        }
        $userIds = array_column($users, 'user_id');
        $userIds[] = $user_id;




        
    


        $data = [
            'logFiles' => $this->logFiles,
            'commands' => $this->commands,
            'role' => $role,

        ];


        return view('logs/index', $data);
    }

    public function logAction($action, $data=null)
    {

        // Получение IP-адреса пользователя
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $user_id = session()->get('user_id');


        $model = new LogsModel();
        

        $currentDateTime = new \DateTime();
        $currentDate = $currentDateTime->format('Y-m-d');
        $currentTime = $currentDateTime->format('H:i:s');

        $data = [
            'user_id' => $user_id,
            'action' => $action,
            'data' => $data,
            'ip_address' => $ipAddress,
            'log_date' => $currentDate,
            'log_time' => $currentTime,
        ];
        $model->insert($data);
    }

    private $logFiles = [
        'log1' => ROOTPATH . 'python/file.log',
        'log2' => ROOTPATH . 'python/file_mover.log',
        
    ];


    

    public function getLog($fileKey)
    {
        if (!array_key_exists($fileKey, $this->logFiles)) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'File not found']);
        }
        
        $logFilePath = $this->logFiles[$fileKey];
        $lines = 10;
        $log = $this->tailCustom($logFilePath, $lines);
        return $this->response->setJSON($log);
    }

    private function tailCustom($filepath, $lines = 1)
    {
        $f = @fopen($filepath, "rb");
        if ($f === false) return false;
        fseek($f, -1, SEEK_END);
        if (fread($f, 1) != "\n") $lines -= 1;

        $output = '';
        $chunk = 4096;
        while (ftell($f) > 0 && $lines >= 0) {
            $seek = min(ftell($f), $chunk);
            fseek($f, -$seek, SEEK_CUR);
            $output = fread($f, $seek) . $output;
            fseek($f, -mb_strlen($output, '8bit'), SEEK_CUR);
            $lines -= substr_count($output, "\n");
        }

        while ($lines++ < 0) {
            $output = substr($output, strpos($output, "\n") + 1);
        }

        fclose($f);
        return explode("\n", trim($output));
    }
    

    // Старт Стоп

    private $commands = [
        'команда1' => 'ls -la',
        'команда2' => 'whoami',
        
    ];


    public function executeCommand()
    {
        $command = $this->request->getPost('command');
        
        if ($command && array_key_exists($command, $this->commands)) {
            $output = shell_exec($this->commands[$command]);
            return $this->response->setJSON(['output' => $output]);
        } else {
            return $this->response->setJSON(['output' => 'Invalid command']);
        }
    }

    public function getData()
    {
        $request = service('request');
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'];
        $filters = [];



        // Инициализация модели
        $model = new LogsModel();


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



    public function getById()
    {   
        $id = $this->request->getPost('id');

        // Инициализация модели
        $model = new LogsModel();

        $data = $model->getById($id);

        $jsonString = $data['data'];

        return $this->response->setJSON($jsonString);
        

    }

}
