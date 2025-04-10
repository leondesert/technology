<?php

namespace App\Controllers;

use App\Models\TicketsModel;
use App\Models\UserModel;
use App\Models\FopsModel;
use App\Controllers\LogsController;


class Dashboard extends BaseController
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
            $users = $userModel->where('parent', $user_id)->findAll();
        }


        // для поле Валюта
        $currencies = ['TJS', 'RUB'];
        
        // для поле Дата
        $dates = $this->getDates();



        $ProfileController = new Profile();


        $data = [
        	'users' => $users,
            'currencies' => $currencies,
        	'dates' => $dates,
            'user_id' => $user_id,
            'username' => $username,
            'role' => $role,
            'filter_values' => $ProfileController->get_filter_values()
        	
        ];


        // Log
        $action = 'Вход в страницу Главная';
        $logger = new LogsController(); 
        $logger->logAction($action);
    
        return view('dashboard/index', $data);
    }

    public function getDates()
    {
        // Сегодня
        $today = date('Y-m-d');

        // Вчера
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        // Текущий месяц
        $thisMonthFirst = date('Y-m-01');
        $thisMonthLast = date('Y-m-t');

        // Прошлого месяца
        $lastMonthFirst = date('Y-m-01', strtotime('first day of last month'));
        $lastMonthLast = date('Y-m-t', strtotime('last day of last month'));

        // Текущая декада
        $daterange = $this->getThisDecade();
        $dates = explode(' / ', $daterange);
        $thisDecadeFirst = $dates[0];
        $thisDecadeLast = $dates[1];


        // Текущий месяц
        $thisMonth = date('Y-m');
        // Первая декада
        $firstDecadeStart = date('Y-m-01', strtotime($thisMonth));
        $firstDecadeEnd = date('Y-m-10', strtotime($thisMonth));
        // Вторая декада
        $secondDecadeStart = date('Y-m-11', strtotime($thisMonth));
        $secondDecadeEnd = date('Y-m-20', strtotime($thisMonth));
        // Третья декада
        $thirdDecadeStart = date('Y-m-21', strtotime($thisMonth));
        $thirdDecadeEnd = date('Y-m-t', strtotime($thisMonth));


        $dates = [
            'today' => $today,
            'yesterday' => $yesterday,
            'thisMonthFirst' => $thisMonthFirst,
            'thisMonthLast' => $thisMonthLast,
            'lastMonthFirst' => $lastMonthFirst,
            'lastMonthLast' => $lastMonthLast,
            'thisDecadeFirst' => $thisDecadeFirst,
            'thisDecadeLast' => $thisDecadeLast,
            'firstDecadeStart' => $firstDecadeStart,
            'firstDecadeEnd' => $firstDecadeEnd,
            'secondDecadeStart' => $secondDecadeStart,
            'secondDecadeEnd' => $secondDecadeEnd,
            'thirdDecadeStart' => $thirdDecadeStart,
            'thirdDecadeEnd' => $thirdDecadeEnd,
        ];


        return $dates;
    }

    public function getThisDecade()
    {
        // Текущая декада

        $todayDate = date('Y-m-d');
        $dayOfMonth = date('j', strtotime($todayDate));
        $decade = ceil($dayOfMonth / 10);
        $firstDayOfDecade = date('Y-m-d', strtotime(date('Y-m-01', strtotime($todayDate)) . " + " . (($decade - 1) * 10) . " days"));
        $tenthDayOfDecade = date('Y-m-d', strtotime($firstDayOfDecade . " + 9 days"));
        $daterange = $firstDayOfDecade." / ".$tenthDayOfDecade;

        return $daterange;
    }

}
