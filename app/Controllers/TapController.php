<?php

namespace App\Controllers;

use App\Models\TapModel;
use App\Models\UserModel;
use App\Models\RewardsModel;
use App\Controllers\LogsController;

class TapController extends BaseController
{


    public function index()
    {   
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); // Редирект на страницу входа, если не авторизован
        }

        // Log
        $action = 'Вход в страницу Пульты';
        $logger = new LogsController(); 
        $logger->logAction($action);


        $role = session()->get('role');

        $data = [
            'role' => $role,
        ];
        
        
        return view('organization/tap/index', $data);
    }

    public function create()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }

        // Log
        $action = 'Вход в страницу Пульты/Создать';
        $logger = new LogsController(); 
        $logger->logAction($action);
        
        return view('organization/tap/create');
    }

    public function register()
    {
        
        $model = new TapModel();

        $data = [
            
            'tap_code' => $this->request->getPost('tap_code'),
            'tap_name' => $this->request->getPost('tap_name'),
            'tap_address' => $this->request->getPost('tap_address'),
            'tap_phone' => $this->request->getPost('tap_phone'),
            'tap_mail' => $this->request->getPost('tap_mail'),
            'reward' => $this->request->getPost('reward'),
            'balance_tjs' => $this->request->getPost('balance_tjs'),
            'balance_rub' => $this->request->getPost('balance_rub'),
            'penalty' => $this->request->getPost('penalty'),
        ];

        $model->insert($data);

        return redirect()->to('/tap')->with('success', 'Успешно создан!');   
    }

    public function reg_reward()
    {

        $model = new RewardsModel();

        $action = $this->request->getVar('action');
        $tap_id = $this->request->getVar('id');
        $cityCodes = $this->request->getVar('citycodes');
        $percent = $this->request->getVar('procent');
        $reward_id = $this->request->getVar('reward_id');


        $status = "Успешно!";


        switch ($action) {
            case 'create':
                
                $existingRecord = $model->where('citycodes', $cityCodes)
                        ->where('name', 'tap')
                        ->where('value', $tap_id)
                        ->first();


                if (is_null($existingRecord)) {
                $model->insert([
                    'procent' => $percent,
                    'citycodes' => $cityCodes,
                    'name' => 'tap',
                    'value' => $tap_id,
                ]);

                } else {
                    $status = "Запись с такими параметрами уже существует.";
                }

                break;
            case 'edit':
               
                $model->update($reward_id, [
                    'procent' => $percent,
                    'citycodes' => $cityCodes,
                ]);
                break;
            case 'delete':
                
                $model->delete($reward_id);
                break;
        }


        return redirect()->back()->with('success', $status);        
    }

    public function edit($id)
    {

        // Log
        $action = 'Попытка изменить Пульты ID: '.$id;
        $logger = new LogsController(); 
        $logger->logAction($action);


        $model = new TapModel();
        $tap = $model->find($id);

        $model = new RewardsModel();
        $rewards = $model->where('value', $id)->where('name', 'tap')->findAll();

        // скрыть/показать поле вознаграждения, штраф
        $userId = session()->get('user_id');
        $model = new UserModel();
        $user = $model->find($userId);
        $agency_ids = $user['agency_id'];
        $stamp_ids = $user['stamp_id'];
        $tap_ids = $user['tap_id'];
        $hidden = true;

        if (empty($agency_ids) && empty($stamp_ids) && !empty($tap_ids)) {
            $hidden = false;
        }


        $role = session()->get('role');


        $data = [
            'id' => $tap['tap_id'],
            'code' => $tap['tap_code'],
            'name' => $tap['tap_name'],
            'address' => $tap['tap_address'],
            'phone' => $tap['tap_phone'],
            'mail' => $tap['tap_mail'],
            'balance_tjs' => $tap['balance_tjs'],
            'balance_rub' => $tap['balance_rub'],
            'penalty' => $tap['penalty'],
            'reward' => $tap['reward'],
        ];


        $data = [
            'rewards' => $rewards,
            'hidden' => $hidden,
            'role' => $role,
            'data' => $data,
            'name' => 'tap',
        ];

        return view('organization/tap/edit', $data);
    }

    public function update($id)
    {   
        $role = session()->get('role');
        $model = new TapModel();

        $data = [
            'tap_name' => $this->request->getPost('name'),
            'tap_address' => $this->request->getPost('address'),
            'tap_phone' => $this->request->getPost('phone'),
            'tap_mail' => $this->request->getPost('mail'),
            'reward' => $this->request->getPost('reward'),
            'balance_tjs' => $this->request->getPost('balance_tjs'),
            'balance_rub' => $this->request->getPost('balance_rub'),
            'penalty' => $this->request->getPost('penalty'),
        ];

        if ($role === 'superadmin') {
            $data['tap_code'] = $this->request->getPost('code');
        }

        $model->update($id, $data);

        // Log
        $action = 'Изменен Пульт';
        $logger = new LogsController(); 
        $logger->logAction($action, json_encode($data));


        return redirect()->to('/tap')->with('success', 'Успешно обновлен!');

    }

    public function delete($id)
    {

        $model = new TapModel();
        $model->delete($id);


        // Log
        $action = 'Удалено Пульт ID: '.$id;
        $logger = new LogsController(); 
        $logger->logAction($action);


        return redirect()->back()->with('success', 'Успешно удален!');
    }






}
