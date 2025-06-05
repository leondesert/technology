<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\AgencyModel;
use App\Models\OprModel;
use App\Models\StampModel;
use App\Models\TapModel;
use App\Models\ShareModel;
use App\Controllers\LogsController;
use App\Controllers\Profile;

class UsersController extends BaseController
{

    public function index()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }

        // Log
        $action = 'Вход в страницу Пользователи';
        $logger = new LogsController(); 
        $logger->logAction($action);

        

        // Получаем данные из базы данных
        $user_id = session()->get('user_id');
        $UserModel = new UserModel();

        

        $role = session()->get('role');
        if ($role === 'superadmin'){
            $users = $UserModel->findAll();
        }else{
            $users = $UserModel->where('parent', $user_id)->findAll();
        }
        
        return view('users/index', ['users' => $users]);
    }

    public function create_user()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); // Редирект на страницу входа, если не авторизован
        }

        // Log
        $action = 'Вход в страницу Пользователи/Создать';
        $logger = new LogsController(); 
        $logger->logAction($action);

        // Если пользователь авторизован, выполняем код для защищенной страницы
        $role = session()->get('role');

        if($role === "superadmin"){
            $model = new AgencyModel();
            $agencies = $model->findAll();
            $model = new OprModel();
            $oprs = $model->findAll();
            $model = new StampModel();
            $stamps = $model->findAll();
            $model = new TapModel();
            $taps = $model->findAll();
            $model = new ShareModel(); 
            $shares = $model->findAll();

            

        }else{
            $userId = session()->get('user_id');
            $model = new UserModel();
            $user = $model->find($userId);
            $agencies = explode(',', $user['agency_id']);
            $oprs = explode(',', $user['opr_id']);
            $stamps = explode(',', $user['stamp_id']);
            $taps = explode(',', $user['tap_id']);
            $shares_ids = explode(',', $user['share_id'] ?? '');


            $model = new AgencyModel();
            $agencies = $model->whereIn('agency_id', $agencies)->findAll();
            $model = new OprModel();
            $oprs = $model->whereIn('opr_id', $oprs)->findAll();
            $model = new StampModel();
            $stamps = $model->whereIn('stamp_id', $stamps)->findAll();
            $model = new TapModel();
            $taps = $model->whereIn('tap_id', $taps)->findAll();
            $model = new ShareModel(); 
            $shares = $model->whereIn('share_id', $shares_ids)->findAll();

            
        }

        $user_id = session()->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->where('user_id', $user_id)->first();
        $four_params = new Profile(); 
        $table_names = $four_params->four_params($user_id);


    

        $data = [
            'agencies' => $agencies, 
            'oprs' => $oprs, 
            'stamps' => $stamps, 
            'taps' => $taps,
            'shares' => $shares, 
            'filters' => $table_names,
            'user' => $user,
            
        ];

        if ($role === 'superadmin') { 
            $data['potential_parents'] = $userModel->select('user_id, user_login, fio')
                                                   ->orderBy('user_login', 'ASC')
                                                   ->findAll();
        }

        
        return view('users/create_user', $data);
    }

    public function edit($id)
    {
        // Fetch the agency data based on the ID from the database
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }

        $role = session()->get('role');

        if($role === "superadmin"){
            $model = new AgencyModel();
            $agencies = $model->findAll();
            $model = new OprModel();
            $oprs = $model->findAll();
            $model = new StampModel();
            $stamps = $model->findAll();
            $model = new TapModel();
            $taps = $model->findAll();
            $model = new ShareModel();
            $shares_all = $model->findAll(); 
            

        }else{
            $user_id = session()->get('user_id');
            $model = new UserModel();
            $user = $model->find($user_id);

            $agencies = explode(',', $user['agency_id']);
            $oprs = explode(',', $user['opr_id']);
            $stamps = explode(',', $user['stamp_id']);
            $taps = explode(',', $user['tap_id']);
            $shares_ids = explode(',', $user['share_id'] ?? ''); 


            $model = new AgencyModel();
            $agencies = $model->whereIn('agency_id', $agencies)->findAll();
            $model = new OprModel();
            $oprs = $model->whereIn('opr_id', $oprs)->findAll();
            $model = new StampModel();
            $stamps = $model->whereIn('stamp_id', $stamps)->findAll();
            $model = new TapModel();
            $taps = $model->whereIn('tap_id', $taps)->findAll();
            $model = new ShareModel(); 
            $shares_all = $model->whereIn('share_id', $shares_ids)->findAll();

        }

        
        $model = new UserModel();
        $user = $model->find($id);

        $model = new UserModel(); 
        $user = $model->find($id); 

        if (!$user) { 
            return redirect()->to('/users')->with('error', 'Пользователь не найден.');
        }

        // Log
        $action = 'Попытка изменить пользователя '.$user['user_login'];
        $logger = new LogsController(); 
        $logger->logAction($action);



        $user_id = session()->get('user_id');
        $four_params = new Profile(); 
        $table_names = $four_params->four_params($user_id);



        $roles = ['admin', 'user'];

        $data = [
            'user' => $user, 
            'agencies' => $agencies, 
            'oprs' => $oprs, 
            'stamps' => $stamps, 
            'taps' => $taps,
            'shares' => $shares_all, 
            'filters' => $table_names,
            'roles' => $roles,
            
        ];

        if ($role === 'superadmin') { 
            $data['potential_parents'] = $model->select('user_id, user_login, fio')
                                               ->where('user_id !=', $id) 
                                               ->orderBy('user_login', 'ASC')
                                               ->findAll();
        }
        
        return view('users/edit_user', $data);
    }
    
    private function isUsernameUnique($userModel, $username)
    {
        $user = $userModel->where('user_login', $username)->first();
        return $user === null;
    }

    public function register()
    {
        // Получение данных из POST-запроса
        $fio = $this->request->getPost('fio');
        $username = $this->request->getPost('login');
        $password = $this->request->getPost('password');

        // Проверка уникальности логина
        $userModel = new UserModel();
        if (!$this->isUsernameUnique($userModel, $username)) {
            // Вывод ошибки, если логин не уникален
            return redirect()->back()->with('error', 'Пользователь с таким логином уже существует.');
        }

        $agencies = $this->request->getPost('agencies');
        $agencies = is_array($agencies) ? implode(',', $agencies) : '';

        $oprs = $this->request->getPost('oprs');
        $oprs = is_array($oprs) ? implode(',', $oprs) : '';

        $taps = $this->request->getPost('taps');
        $taps = is_array($taps) ? implode(',', $taps) : '';

        $stamps = $this->request->getPost('stamps');
        $stamps = is_array($stamps) ? implode(',', $stamps) : '';

        $shares_post = $this->request->getPost('shares');
        // ОТЛАДКА:
        log_message('error', 'Raw POST shares data (register): ' . print_r($this->request->getPost('shares'), true));
        
        // Если это массив, объединяем в строку через запятую. 
        // Если не массив (например, одиночное значение или null), используем его как есть или пустую строку.
        $shares_post = is_array($shares_post) ? implode(',', $shares_post) : ($shares_post ?: '');
        log_message('error', 'Processed shares_post for DB (register): ' . $shares_post);

        
        $role = "user"; 
        if($this->request->getPost('role')){
            $role = $this->request->getPost('role'); 
        }
        $filter = $this->request->getPost('filter');
        $userId = session()->get('user_id');
        // Валидация данных, проверка на уникальность пользователя и т.д.

        $start_date = session()->get('firstDayOfMonth');
        $end_date = session()->get('lastDayOfMonth');


        // Ключ
        $secret_key = bin2hex(random_bytes(32 / 2));

        $acquiring = $this->request->getPost('acquiring') ? '1' : '0';
        $is_airline = $this->request->getPost('is_airline') ? '1' : '0';

        // Определение родителя
        $parent_value_for_db = null;
        if (session()->get('role') === 'superadmin') { 
            $posted_parent_id = $this->request->getPost('parent_id');
            if (!empty($posted_parent_id)) {
                $parent_value_for_db = $posted_parent_id;
            } 
        } else {
            $parent_value_for_db = $userId; 
        }

        // Создание нового пользователя
        $data_to_insert = [
            'user_login' => $username,
            'user_pass' => password_hash($password, PASSWORD_DEFAULT), // Хеширование пароля
            'filter' => $filter,
            'role' => $role,
            'agency_id' => $agencies,
            'opr_id' => $oprs,
            'tap_id' => $taps,
            'stamp_id' => $stamps,
            'share_id' => $shares_post,
            'parent' => $parent_value_for_db, 
            'start_date' => $start_date,
            'end_date' => $end_date,
            'secret_key' => $secret_key,
            'fio' => $fio,
            'acquiring' => $acquiring,
            'is_airline' => $is_airline,
        ];
        $userModel->insert($data_to_insert);

        // Log
        $action = 'Пользователь логином '.$username.' успешно создан!';
        $logger = new LogsController(); 
        $logger->logAction($action);


        // Редирект после успешной регистрации
        return redirect()->to('/users')->with('success', 'Пользователь логином <b>'.$username.'</b> успешно создан!');
    }

    public function update($id)
    {
        
        $username = $this->request->getPost('login');
        $password = $this->request->getPost('password');
        $fio = $this->request->getPost('fio');
        $agencies = $this->request->getPost('agencies');
        $agencies = is_array($agencies) ? implode(',', $agencies) : '';

        $oprs = $this->request->getPost('oprs');
        $oprs = is_array($oprs) ? implode(',', $oprs) : '';

        $taps = $this->request->getPost('taps');
        $taps = is_array($taps) ? implode(',', $taps) : '';

        $stamps = $this->request->getPost('stamps');
        $stamps = is_array($stamps) ? implode(',', $stamps) : '';

        $shares_post = $this->request->getPost('shares');
        // ОТЛАДКА:
        log_message('error', 'Raw POST shares data (update): ' . print_r($this->request->getPost('shares'), true));

        // Если это массив, объединяем в строку через запятую.
        // Если не массив (например, одиночное значение или null), используем его как есть или пустую строку.
        $shares_post = is_array($shares_post) ? implode(',', $shares_post) : ($shares_post ?: '');
        log_message('error', 'Processed shares_post for DB (update): ' . $shares_post);


        $role = "user"; 
        if($this->request->getPost('role')){
            $role = $this->request->getPost('role'); 
        }
        $filter = $this->request->getPost('filter');

        $acquiring = $this->request->getPost('acquiring') ? '1' : '0';
        $is_airline = $this->request->getPost('is_airline') ? '1' : '0';

        $model = new UserModel();
        $data = [
            'user_login' => $username,
            'filter' => $filter,
            'role' => $role,
            'agency_id' => $agencies,
            'opr_id' => $oprs,
            'tap_id' => $taps,
            'stamp_id' => $stamps,
            'share_id' => $shares_post,
            'fio' => $fio,
            'acquiring' => $acquiring,
            'is_airline' => $is_airline,
        ];

        if (!empty($password)) {
            $data['user_pass'] = password_hash($password, PASSWORD_DEFAULT); 
        }

        // Parent update logic
        if (session()->get('role') === 'superadmin') { 
            $parent_id_from_post = $this->request->getPost('parent_id');

            if ($parent_id_from_post !== '' && (int)$parent_id_from_post === (int)$id) {
                 return redirect()->back()->withInput()->with('error', 'Пользователь не может быть назначен сам себе в качестве родителя.');
            }

            if (!empty($parent_id_from_post)) {
                $data['parent'] = $parent_id_from_post;
            } else {
                $data['parent'] = null; 
            }
        }

        $model->update($id, $data); 

        // Log
        $action = 'Изменен пользователь ID: '.$id;
        $logger = new LogsController(); 
        $logger->logAction($action);


        // Redirect to the list of agencies after updating
        return redirect()->to('/users')->with('success', 'Пользователь <b>'.$username.'</b> успешно обновлен!');
    }

    public function delete($id)
    {
        // Handle the deletion of the agency from the database
        $model = new UserModel();
        $model->delete($id);

        // Log
        $action = 'Удален пользователь ID: '.$id;
        $logger = new LogsController(); 
        $logger->logAction($action);

        // Redirect to the list of agencies after deletion
        return redirect()->back()->with('success', 'Пользователь успешно удален!');
    }

    public function update_tables_states()
    {
        $userID = session()->get('user_id');
        $statesData = json_encode($this->request->getPost('statesData'));
        // $statesData = 'http';
        $model = new UserModel();
        $model->update($userID, [ 'tables_states' => $statesData]);

        return $this->response->setJSON(['message' => $statesData]);
    }

    public function get_tables_states()
    {
        $userID = session()->get('user_id');
        $model = new UserModel();
        $user = $model->where('user_id', $userID)->first();
        $tables_states = '{}';
        if ( ! empty($user['tables_states'])) {
          $tables_states = json_decode($user['tables_states'], true);

        }

        return $this->response->setJSON(['tables_states' => $tables_states]);
    }

    public function update_colreorder()
    {
        $userID = session()->get('user_id');
        $colReorder = json_encode($this->request->getPost('colReorder'));
        // $colReorder = 'http';
        $model = new UserModel();
        $model->update($userID, [ 'colums_position' => $colReorder]);

        return $this->response->setJSON(['colReorder' => $colReorder]);
    }

    public function get_colreorder()
    {
        $userID = session()->get('user_id');
        $model = new UserModel();
        $user = $model->where('user_id', $userID)->first();
        $colReorder = '{}';
        if ( ! empty($user['colums_position'])) {
          $colReorder = json_decode($user['colums_position'], true);

        }

        return $this->response->setJSON(['colReorder' => $colReorder]);
    }


}
