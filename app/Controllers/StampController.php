<?php

namespace App\Controllers;

use App\Models\StampModel;
use App\Models\UserModel;
use App\Models\RewardsModel;
use App\Controllers\LogsController;


class StampController extends BaseController
{


    public function index()
    {   
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); // Редирект на страницу входа, если не авторизован
        }
        

        // Log
        $action = 'Вход в страницу ППР';
        $logger = new LogsController(); 
        $logger->logAction($action);

        
        $role = session()->get('role');

        $data = [
            'role' => $role,
        ];

        
        
        return view('organization/stamp/index', $data);
    }

    public function create()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }
        
        // Log
        $action = 'Вход в страницу ППР/Создать';
        $logger = new LogsController(); 
        $logger->logAction($action);

        return view('organization/stamp/create');
    }

    public function register()
    {
        
        $model = new StampModel();

        $data = [
            
            'stamp_code' => $this->request->getPost('stamp_code'),
            'stamp_name' => $this->request->getPost('stamp_name'),
            'stamp_address' => $this->request->getPost('stamp_address'),
            'stamp_phone' => $this->request->getPost('stamp_phone'),
            'stamp_mail' => $this->request->getPost('stamp_mail'),
            'reward' => $this->request->getPost('reward'),
            'balance_tjs' => $this->request->getPost('balance_tjs'),
            'balance_rub' => $this->request->getPost('balance_rub'),
            'penalty' => $this->request->getPost('penalty'),
        ];

        $model->insert($data);

        return redirect()->to('/stamp')->with('success', 'Успешно создан!');   
    }

    public function reg_reward()
    {

        $model = new RewardsModel();

        $action = $this->request->getVar('action');
        $stamp_id = $this->request->getVar('id');
        $cityCodes = $this->request->getVar('citycodes');
        $percent = $this->request->getVar('procent');
        $reward_id = $this->request->getVar('reward_id');


        $status = "Успешно!";


        switch ($action) {
            case 'create':
                
                $existingRecord = $model->where('citycodes', $cityCodes)
                        ->where('name', 'stamp')
                        ->where('value', $stamp_id)
                        ->first();


                if (is_null($existingRecord)) {
                $model->insert([
                    'procent' => $percent,
                    'citycodes' => $cityCodes,
                    'name' => 'stamp',
                    'value' => $stamp_id,
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
        $action = 'Попытка изменить ППР ID: '.$id;
        $logger = new LogsController(); 
        $logger->logAction($action);


        $model = new StampModel();
        $stamp = $model->find($id);

        $model = new RewardsModel();
        $rewards = $model->where('value', $id)->where('name', 'stamp')->findAll();

        // скрыть/показать поле вознаграждения, штраф
        $userId = session()->get('user_id');
        $model = new UserModel();
        $user = $model->find($userId);
        $agency_ids = $user['agency_id'];
        $stamp_ids = $user['stamp_id'];
        $hidden = true;

        if (empty($agency_ids) && !empty($stamp_ids)) {
            $hidden = false;
        }



        $role = session()->get('role');


        $data = [
            'id' => $stamp['stamp_id'],
            'code' => $stamp['stamp_code'],
            'name' => $stamp['stamp_name'],
            'address' => $stamp['stamp_address'],
            'phone' => $stamp['stamp_phone'],
            'mail' => $stamp['stamp_mail'],
            'balance_tjs' => $stamp['balance_tjs'],
            'balance_rub' => $stamp['balance_rub'],
            'penalty' => $stamp['penalty'],
            'reward' => $stamp['reward'],
        ];


        $data = [
            'rewards' => $rewards,
            'hidden' => $hidden,
            'role' => $role,
            'data' => $data,
            'name' => 'stamp',
        ];

        return view('organization/stamp/edit', $data);
    }

    public function update($id)
    {   
        $role = session()->get('role');
        $model = new StampModel();

        $data = [
            'stamp_name' => $this->request->getPost('name'),
            'stamp_address' => $this->request->getPost('address'),
            'stamp_phone' => $this->request->getPost('phone'),
            'stamp_mail' => $this->request->getPost('mail'),
            'reward' => $this->request->getPost('reward'),
            'balance_tjs' => $this->request->getPost('balance_tjs'),
            'balance_rub' => $this->request->getPost('balance_rub'),
            'penalty' => $this->request->getPost('penalty'),
        ];

        if ($role === 'superadmin') {
            $data['stamp_code'] = $this->request->getPost('code');
        }

        $model->update($id, $data);

        // Log
        $action = 'Изменен ППР ID: '.$id;
        $logger = new LogsController(); 
        $logger->logAction($action);


        return redirect()->to('/stamp')->with('success', 'Успешно обновлен!');
    }

    public function delete($id)
    {

        $model = new StampModel();
        $model->delete($id);

        // Log
        $action = 'Удалено ППР ID: '.$id;
        $logger = new LogsController(); 
        $logger->logAction($action);

        
        return redirect()->back()->with('success', 'Успешно удален!');
    }






}
