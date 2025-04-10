<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Controllers\LogsController;

class Profile extends BaseController
{
    public function index()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); // Редирект на страницу входа, если не авторизован
        }

        // Log
        $action = 'Вход в страницу Профиль';
        $logger = new LogsController(); 
        $logger->logAction($action);


        // Данные
        $user_id = session()->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->where('user_id', $user_id)->first();

        $table_names = $this->four_params($user_id);

        $data = [
            "table_names" => $table_names,
            "user" => $user,
            

        ];

        return view('profile/index', $data);
    }

    public function update()
    {
        
        $id = session()->get('user_id');
        $role = session()->get('role');


        $user_login = $this->request->getPost('user_login');
        $password = $this->request->getPost('user_pass');
        $user_mail = $this->request->getPost('user_mail');
        $user_phone = $this->request->getPost('user_phone');
        $user_desc = $this->request->getPost('user_desc');
        $filter = $this->request->getPost('filter');
        $fio = $this->request->getPost('fio');

        //Сохранить данные пользователя
        $model = new UserModel();
        $data = [
            'user_login' => $user_login,
            'user_mail' => $user_mail,
            'user_phone' => $user_phone,
            'user_desc' => $user_desc,
            'filter' => $filter,
            'fio' => $fio,
        ];

        if (!empty($password)) {
            $data['user_pass'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // аватар
        $file = $this->request->getFile('avatar');
        if ($file->isValid() && !$file->hasMoved()) {
            // Check the file extension
            $validExtensions = ['jpg', 'jpeg', 'png'];
            $extension = $file->getExtension();

            if (in_array($extension, $validExtensions)) {
                // Create the save path if it doesn't exist
                $path = FCPATH . 'uploads/avatars';
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }

                $newName = $file->getRandomName();
                $file->move($path, $newName);
                
                $data['user_photo_url'] = $newName;
            } else {
                // Handle invalid file extension
                return redirect()->back()->withInput()->with('error', 'Неверное расширение файла! Допустимы только jpg, jpeg, png.');
            }
        }

        $model->update($id, $data);



        ///////////////////////////////////////////////////////////////
        $model = new UserModel();
        $user = $model->where('user_id', $id)->first();
        

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

        $session = \Config\Services::session();
        // Устанавливаем данные в сессию
        $session->set([
            'filter' => $filter,
            'colum_name' => $colum_name,
            'ids' => $ids,
        ]);
        ////////////////////////////////////////////////////////////////
        

        // Log
        $action = 'Изменен профиль';
        $logger = new LogsController(); 
        $logger->logAction($action);


        return redirect()->back()->with('success', 'Успешно сохранен.');
    }
    
    public function four_params($user_id)
    {
        $model = new UserModel();
        $user = $model->where('user_id', $user_id)->first();
        $fourParametrs = ['agency', 'stamp', 'tap', 'opr'];
        $table_names = [];

        $i = 0;
        foreach ($fourParametrs as $key => $value) {

            if (!empty($user[$value.'_id'])) {
                $table_names[$i]['name'] = $this->NamefourParametrs($value);
                $table_names[$i]['value'] = $value;
                $i++;
            }
        }


        return $table_names;        
    }

    public function NamefourParametrs($value)
    {


        $table_name = null;
        switch ($value) {
            case 'agency':
                $table_name = "Агенство";
                break;
            case 'stamp':
                $table_name = "ППР";
                break;
            case 'tap':
                $table_name = "Пульт";
                break;
            case 'opr':
                $table_name = "Оператор";
                break;

        }

        return $table_name;
    }

    public function four_params_json()
    {
        $user_id = $this->request->getVar('user_id');
        $table_names = $this->four_params($user_id);

        // $table_names = [
        //     ['name' => 'Агенство', 'value' => 'agency'],
        //     ['name' => 'ППР', 'value' => 'stamp'],
        //     ['name' => 'Пульт', 'value' => 'tap'],
        //     ['name' => 'Оператор', 'value' => 'opr'],
        // ];


        return $this->response->setJSON($table_names);
    }

    public function avatar()
    {
        $model = new UserModel();
        
        $data = [];

        $file = $this->request->getFile('receipt_photo');
        if ($file->isValid() && !$file->hasMoved()) {
            // Check the file extension
            $validExtensions = ['jpg', 'jpeg', 'png'];
            $extension = $file->getExtension();

            if (in_array($extension, $validExtensions)) {
                // Create the save path if it doesn't exist
                $path = WRITEPATH . 'uploads/avatars';
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }

                $newName = $file->getRandomName();
                $file->move($path, $newName);
                
                $data['user_photo_url'] = $newName;
            } else {
                // Handle invalid file extension
                return redirect()->back()->withInput()->with('error', 'Неверное расширение файла! Допустимы только jpg, jpeg, png.');
            }
        }

        if ($model->insert($data)) {
            // On successful save
            return redirect()->to('/transactions')->with('success', 'Успешно создан!');
        } else {
            // Handle save errors
            return redirect()->back()->withInput()->with('error', 'Ошибка!');
        }
    }

    public function updateSession()
    {
        // Загружаем сессию
        $session = session();

        // Получаем параметр и значение из запроса
        $start_date = $this->request->getPost('startDate');
        $end_date = $this->request->getPost('endDate');
        $user_login = $this->request->getPost('user_login');
        $currency = $this->request->getPost('currency');
        $name_table = $this->request->getPost('name_table');
        $value_table = $this->request->getPost('value_table');
        


        // Обновляем сессию для переданного параметра
        $result = $session->set([
            'start_date' => $start_date,
            'end_date' => $end_date,
            'user_login' => $user_login,
            'currency' => $currency,
            'name_table' => $name_table,
            'value_table' => $value_table,
        ]);


        $start_date = $session->get('start_date');
        $value_table = $session->get('value_table');
        $user_login = $session->get('user_login');


        return $this->response->setJSON([
            'status' => 'success', 
            'start_date' => $start_date,
            'value_table' => $value_table,
            'user_login' => $user_login,
            
        ]);
    }

    public function getSessionValue2()
    {
        // Загружаем сессию
        $session = session();

        // Получаем параметр из запроса
        $param = $this->request->getPost('param');

        // // Проверяем, есть ли значение в сессии
        // if ($session->has($param)) {
        

        $response = [
            'status' => 'success',
            'value' => $session->get($param)
        ];


        // Возвращаем JSON-ответ
        return $this->response->setJSON($response);
    }

    public function get_filter_values()
    {
        // Загружаем сессию
        $session = session();

        $filter_values = [
            'start_date' => $session->get('start_date'),
            'end_date' => $session->get('end_date'),
            'user_login' => $session->get('user_login'),
            'currency' => $session->get('currency'),
            'name_table' => $session->get('name_table'),
            'value_table' => $session->get('value_table'),
        ];

        return $filter_values;

    }

}
