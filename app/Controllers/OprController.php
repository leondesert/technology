<?php

namespace App\Controllers;

use App\Models\OprModel;
use App\Models\UserModel;
use App\Models\RewardsModel;
use App\Controllers\LogsController;

class OprController extends BaseController
{


    public function index()
    {   
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); // Редирект на страницу входа, если не авторизован
        }

        // Log
        $action = 'Вход в страницу Операторы';
        $logger = new LogsController(); 
        $logger->logAction($action);


        $role = session()->get('role');

        $data = [
            'role' => $role,
        ];

        
        
        return view('organization/opr/index', $data);
    }

    public function create()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }

        // Log
        $action = 'Вход в страницу Операторы/Создать';
        $logger = new LogsController(); 
        $logger->logAction($action);
        
        return view('organization/opr/create');
    }

    public function register()
    {
        
        $model = new OprModel();

        $data = [
            
            'opr_code' => $this->request->getPost('opr_code'),
            'opr_name' => $this->request->getPost('opr_name'),
            'opr_phone' => $this->request->getPost('opr_phone'),
            'opr_mail' => $this->request->getPost('opr_mail'),
            'reward' => $this->request->getPost('reward'),
            'balance_tjs' => $this->request->getPost('balance_tjs'),
            'balance_rub' => $this->request->getPost('balance_rub'),
            'penalty' => $this->request->getPost('penalty'),
        ];

        $model->insert($data);

        return redirect()->to('/opr')->with('success', 'Успешно создан!');   
    }

    public function reg_reward()
    {

        $model = new RewardsModel();

        $action = $this->request->getVar('action');
        $opr_id = $this->request->getVar('id');
        $cityCodes = $this->request->getVar('citycodes');
        $percent = $this->request->getVar('procent');
        $reward_id = $this->request->getVar('reward_id');


        $status = "Успешно!";


        switch ($action) {
            case 'create':
                
                $existingRecord = $model->where('citycodes', $cityCodes)
                        ->where('name', 'opr')
                        ->where('value', $opr_id)
                        ->first();


                if (is_null($existingRecord)) {
                $model->insert([
                    'procent' => $percent,
                    'citycodes' => $cityCodes,
                    'name' => 'opr',
                    'value' => $opr_id,
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
        $action = 'Попытка изменить Оператор';
        $logger = new LogsController(); 
        $logger->logAction($action);


        $model = new OprModel();
        $opr = $model->find($id);

        $model = new RewardsModel();
        $rewards = $model->where('value', $id)->where('name', 'opr')->findAll();

        // скрыть/показать поле вознаграждения, штраф
        $userId = session()->get('user_id');
        $model = new UserModel();
        $user = $model->find($userId);
        $agency_ids = $user['agency_id'];
        $stamp_ids = $user['stamp_id'];
        $tap_ids = $user['tap_id'];
        $opr_ids = $user['opr_id'];
        $hidden = true;

        if (empty($agency_ids) && empty($stamp_ids) && empty($tap_ids) && !empty($opr_ids)) {
            $hidden = false;
        }


        $role = session()->get('role');


        $data = [
            'id' => $opr['opr_id'],
            'code' => $opr['opr_code'],
            'name' => $opr['opr_name'],
            'address' => $opr['opr_address'],
            'phone' => $opr['opr_phone'],
            'mail' => $opr['opr_mail'],
            'balance_tjs' => $opr['balance_tjs'],
            'balance_rub' => $opr['balance_rub'],
            'penalty' => $opr['penalty'],
            'reward' => $opr['reward'],
        ];

        $data = [
            'rewards' => $rewards,
            'hidden' => $hidden,
            'role' => $role,
            'data' => $data,
            'name' => 'opr',
        ];

        return view('organization/opr/edit', $data);
    }

    public function update($id)
    {   
        $role = session()->get('role');
        $model = new OprModel();

        $data = [
            'opr_name' => $this->request->getPost('name'),
            'opr_phone' => $this->request->getPost('phone'),
            'opr_mail' => $this->request->getPost('mail'),
            'reward' => $this->request->getPost('reward'),
            'balance_tjs' => $this->request->getPost('balance_tjs'),
            'balance_rub' => $this->request->getPost('balance_rub'),
            'penalty' => $this->request->getPost('penalty'),
        ];

        if ($role === 'superadmin') {
            $data['opr_code'] = $this->request->getPost('code');
        }

        $model->update($id, $data);

        // Log
        $action = 'Изменен Оператор ID: '.$id;
        $logger = new LogsController(); 
        $logger->logAction($action);


        return redirect()->to('/opr')->with('success', 'Успешно обновлен!');
    }

    public function delete($id)
    {

        $model = new OprModel();
        $model->delete($id);

        // Log
        $action = 'Удалено Оператор ID: '.$id;
        $logger = new LogsController(); 
        $logger->logAction($action);

        
        return redirect()->back()->with('success', 'Успешно удален!');
    }






}
