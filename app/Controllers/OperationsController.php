<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Controllers\LogsController;

class OperationsController extends BaseController
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
            'filter_values' => $ProfileController->get_filter_values()
            
        
        ];


        // Log
        $action = 'Вход в страницу Операции';
        $logger = new LogsController(); 
        $logger->logAction($action);


        // Если пользователь авторизован, выполняем код для защищенной страницы
        return view('operations/index', $data);
    }
    

    public function get_active_params()
    {

        // Получаем параметры из POST-запроса
        $user_id = $this->request->getPost('user_id');
        $filter = $this->request->getPost('filter');

        $model = new UserModel();
        $user = $model->where('user_id', $user_id)->first();
        
        $colum_name = $filter.'_id';
        $ids = $user[$colum_name];
        $ids = explode(",", $ids);
        $ids = array_map('intval', $ids);
        $ids = "(" . implode(",", $ids) . ")";

        $data = [
            'colum_name' => $colum_name,
            'ids' => $ids
        ];
        
        return $this->response->setJSON($data);
    }




    
}
