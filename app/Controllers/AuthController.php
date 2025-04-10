<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use App\Controllers\LogsController;
use App\Controllers\Tickets;
use App\Controllers\Dashboard;


class AuthController extends Controller
{

    public function showRegistrationForm()
    {
        return view('auth/registration_form');
    }

    private function isUsernameUnique($userModel, $username)
    {
        $user = $userModel->where('user_login', $username)->first();
        return $user === null; // Вернет true, если пользователя с таким логином не существует, иначе false
    }

    public function showLoginForm()
    {
        return view('auth/login_form');
    }

    public function login()
    {
        // Получение данных из POST-запроса
        $username = $this->request->getPost('login');
        $password = $this->request->getPost('password');


        // Поиск пользователя в базе данных по логину
        $userModel = new UserModel();
        $user = $userModel->where('user_login', $username)->first();

        
        // Проверка пароля
        if ($user && password_verify($password, $user['user_pass'])) {
            // Успешный вход
            
            // Устанавливаем сессию для авторизации пользователя
            $this->setUserSession($user['user_id'], $user['user_login'], $user['role'], $user['filter']);


            // Log
            $action = 'Успешная аутентификация';
            $logger = new LogsController(); 
            $logger->logAction($action, json_encode($user));

            return redirect()->to('/dashboard'); 
        } else {
            

            // Неверные учетные данные, добавляем флэш-сообщение
            $session = \Config\Services::session();
            $session->setFlashdata('error', 'Неверные учетные данные. Пожалуйста, попробуйте еще раз.');


            // Log
            $data = ['error' => 'Неверные учетные данные. Пожалуйста, попробуйте еще раз.'];
            $action = 'Неуспешная аутентификация';
            $logger = new LogsController(); 
            $logger->logAction($action, json_encode($data));


            return redirect()->to('/login'); 
        }
    }

    private function setUserSession($userId, $username, $role, $filter)
    {
        // Загружаем библиотеку сессии
        $session = \Config\Services::session();


        ///////////////////////////////////////////////////////////////
        $model = new UserModel();
        $user = $model->where('user_id', $userId)->first();
        

        if($role == "superadmin"){
            $colum_name = 'agency_id';
            $ids = "0";
        }else{
            //логика
            $colum_name = $filter.'_id';
            $ids = $user[$colum_name];
            $ids = explode(",", $ids);
            $ids = array_map('intval', $ids);

        }
        ////////////////////////////////////////////////////////////////

        // decade
        $decade = $this->getDecade();
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $firstDayOfMonth = date('Y-m-01');
        $lastDayOfMonth = date('Y-m-t');


        // uniqueTaxCodes
        // $uniqueTaxCodes = [
        //     'A2', 'AE', 'CN', 'CP', 'CS', 'DE', 'E3', 'F6', 'FX', 
        //     'GE', 'I6', 'IO', 'IR', 'JA', 'JN', 'M6', 'OY', 'RA', 
        //     'T2', 'TP', 'TR', 'UJ', 'UZ', 'YQ', 'YR', 'ZR', 'ZZ'
        // ];

        $Tickets = new Tickets(); 
        $uniqueTaxCodes = $Tickets->getUniqueColumns();



        $dashboard = new Dashboard(); 
        $dates = $dashboard->getDates();


        // Устанавливаем данные в сессию
        $session->set([
            'user_id' => $userId,
            'username' => $username,
            'role' => $role,
            'filter' => $filter,
            'colum_name' => $colum_name,
            'ids' => $ids,
            'decade' => $decade,
            'today' => $today,
            'yesterday' => $yesterday,
            'firstDayOfMonth' => $firstDayOfMonth,
            'lastDayOfMonth' => $lastDayOfMonth,
            'is_logged_in' => true,
            'uniqueTaxCodes' => $uniqueTaxCodes,
            'start_date' => $dates['today'],
            'end_date' => $dates['today'],
            'name_table' => $filter,
            
        ]);
    }


    

    public function logout()
    {

        // Log
        $action = 'Пользователь вышел из системы';
        $logger = new LogsController();
        $logger->logAction($action);

        // Загружаем библиотеку сессий
        $session = session();

        // Уничтожаем текущую сессию
        $session->destroy();

        // Перенаправляем пользователя на страницу входа или другую нужную страницу
        return redirect()->to('/login');
    }

    public function getDecade()
    {

        $todayDate = date('Y-m-d');
        $dayOfMonth = date('j', strtotime($todayDate));
        $decade = ceil($dayOfMonth / 10);
        $firstDayOfDecade = date('Y-m-d', strtotime(date('Y-m-01', strtotime($todayDate)) . " + " . (($decade - 1) * 10) . " days"));
        $tenthDayOfDecade = date('Y-m-d', strtotime($firstDayOfDecade . " + 9 days"));
        $daterange = $firstDayOfDecade." / ".$tenthDayOfDecade;

        return $daterange;
    }
}
