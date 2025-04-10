<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Controllers\LogsController;

class Passengers extends BaseController
{
    public function index()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); // Редирект на страницу входа, если не авторизован
        }

        // Log
        $action = 'Вход в страницу Пассажиры';
        $logger = new LogsController(); 
        $logger->logAction($action);


        

        // Если пользователь авторизован, выполняем код для защищенной страницы
        return view('passengers/index');
    }
}
