<?php

namespace App\Controllers;

use App\Models\PreShareModel;
use App\Models\UserModel;
use App\Models\RewardsModel;
use App\Controllers\LogsController;

class PreShareController extends BaseController
{
    public function index()
    {   
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $logger = new LogsController(); 
        $logger->logAction('Вход в страницу Предварительные раздачи');

        $role = session()->get('role');
        $data = [
            'role' => $role,
        ];
        
        return view('organization/pre_share/index', $data);
    }

    public function create()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }

        $action = 'Вход в страницу Предварительные раздачи/Создать'; 
        $logger = new LogsController(); 
        $logger->logAction($action);
        
        return view('organization/pre_share/create');
    }

    public function register()
    {
        $model = new PreShareModel();

        $data = [
            'pre_share_code' => $this->request->getPost('pre_share_code'),
            'pre_share_name' => $this->request->getPost('pre_share_name'),
            'pre_share_address' => $this->request->getPost('pre_share_address'),
            'pre_share_phone' => $this->request->getPost('pre_share_phone'),
            'pre_share_mail' => $this->request->getPost('pre_share_mail'),
            'reward' => $this->request->getPost('reward'),
            'balance_tjs' => $this->request->getPost('balance_tjs'),
            'balance_rub' => $this->request->getPost('balance_rub'),
            'penalty' => $this->request->getPost('penalty'),
        ];

        $model->insert($data);
        return redirect()->to('/pre_share')->with('success', 'Успешно создан!');
    }

    public function edit($id)
    {

        $action = 'Попытка изменить Предварительную раздачу';
        $logger = new LogsController(); 
        $logger->logAction($action); 

        $preShareModel = new PreShareModel();
        $pre_share_entity = $preShareModel->find($id); 

        $rewardsModel = new RewardsModel();
        $rewards = $rewardsModel->where('value', $id)->where('name', 'pre_share')->findAll(); 

        $userId = session()->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->find($userId);
        
        $agency_ids = $user['agency_id'] ?? '';
        $stamp_ids = $user['stamp_id'] ?? '';
        $tap_ids = $user['tap_id'] ?? '';
        $opr_ids = $user['opr_id'] ?? '';
        $pre_share_ids = $user['pre_share_id'] ?? ''; 
        $hidden = true;

        if (empty($agency_ids) && empty($stamp_ids) && empty($tap_ids) && empty($opr_ids) && !empty($pre_share_ids)) {
            $hidden = false;
        }

        $role = session()->get('role');

        $viewData = [
            'id' => $pre_share_entity['pre_share_id'],
            'code' => $pre_share_entity['pre_share_code'],
            'name' => $pre_share_entity['pre_share_name'],
            'address' => $pre_share_entity['pre_share_address'] ?? '', 
            'phone' => $pre_share_entity['pre_share_phone'],
            'mail' => $pre_share_entity['pre_share_mail'],
            'balance_tjs' => $pre_share_entity['balance_tjs'],
            'balance_rub' => $pre_share_entity['balance_rub'],
            'penalty' => $pre_share_entity['penalty'],
            'reward' => $pre_share_entity['reward'],
        ];

        $data = [
            'rewards' => $rewards,
            'hidden' => $hidden,
            'role' => $role,
            'data' => $viewData, 
            'name' => 'pre_share', 
        ];

        return view('organization/pre_share/edit', $data);
    }

    public function update($id)
    {   
        $role = session()->get('role');
        $model = new PreShareModel();

        $data = [
            'pre_share_name' => $this->request->getPost('name'),
            'pre_share_phone' => $this->request->getPost('phone'),
            'pre_share_mail' => $this->request->getPost('mail'),
            'reward' => $this->request->getPost('reward'),
            'balance_tjs' => $this->request->getPost('balance_tjs'),
            'balance_rub' => $this->request->getPost('balance_rub'),
            'penalty' => $this->request->getPost('penalty'),
        ];

        if ($role === 'superadmin') {
            $data['pre_share_code'] = $this->request->getPost('code');
        }

        $model->update($id, $data);

        $action = 'Изменен ID Предварительной раздачи: '.$id;
        $logger = new LogsController(); 
        $logger->logAction($action);
        
        return redirect()->to('/pre_share')->with('success', 'Успешно обновлен!');
    }

    public function delete($id)
    {
 
        $model = new PreShareModel();
        $model->delete($id);

        // Log
        $action = 'Удален ID Предварительной раздачи: '.$id;
        $logger = new LogsController(); 
        $logger->logAction($action);

        
        return redirect()->back()->with('success', 'Успешно удален!');
    }

    public function reg_reward()
    {

        $model = new RewardsModel();

        $action = $this->request->getVar('action');
        $id = $this->request->getVar('id');
        $method = $this->request->getVar('method');
        $type = $this->request->getVar('type');
        $name = $this->request->getVar('name');
        $code = $this->request->getVar('code');
        $percent = $this->request->getVar('procent');
        $reward_id = $this->request->getVar('list');


        $status = "Успешно!";


        switch ($action) {
            case 'create':
                
                $existingRecord = $model->where('code', $code)
                        ->where('name', $name)
                        ->where('value', $id)
                        ->first();


                if (is_null($existingRecord)) {
                $model->insert([
                    'method' => $method,
                    'type' => $type,
                    'procent' => $percent,
                    'code' => $code,
                    'name' => $name,
                    'value' => $id,
                ]);

                } else {
                    $status = "Запись с такими параметрами уже существует.";
                }

                break;
            case 'edit':
               
                $model->update($reward_id, [
                    'procent' => $percent,
                    'code' => $code,
                ]);
                break;
            case 'delete':
                
                $model->delete($reward_id);
                break;
        }


        return redirect()->back()->with('success', $status);
    }


}