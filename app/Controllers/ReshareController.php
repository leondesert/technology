<?php

namespace App\Controllers;

use App\Models\ReshareModel;
use App\Models\UserModel;
use App\Models\RewardsModel;
use App\Controllers\LogsController;

class ReshareController extends BaseController
{
    public function index()
    {   
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $logger = new LogsController(); 
        $logger->logAction('Вход в страницу Пере-раздачи');

        $role = session()->get('role');
        $data = [
            'role' => $role,
        ];
        
        return view('organization/reshare/index', $data);
    }

    public function create()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }

        $action = 'Вход в страницу Пере-раздачи/Создать'; 
        $logger = new LogsController(); 
        $logger->logAction($action);
        
        return view('organization/reshare/create');
    }

    public function register()
    {
        $model = new ReshareModel();

        $data = [
            'reshare_code' => $this->request->getPost('reshare_code'),
            'reshare_name' => $this->request->getPost('reshare_name'),
            'reshare_address' => $this->request->getPost('reshare_address'),
            'reshare_phone' => $this->request->getPost('reshare_phone'),
            'reshare_mail' => $this->request->getPost('reshare_mail'),
            'reward' => $this->request->getPost('reward'),
            'balance_tjs' => $this->request->getPost('balance_tjs'),
            'balance_rub' => $this->request->getPost('balance_rub'),
            'penalty' => $this->request->getPost('penalty'),
        ];

        $model->insert($data);
        return redirect()->to('/reshare')->with('success', 'Успешно создан!');
    }

    public function edit($id)
    {

        $action = 'Попытка изменить Пере-раздачу';
        $logger = new LogsController(); 
        $logger->logAction($action); 

        $reShareModel = new ReshareModel();
        $reshare_entity = $reShareModel->find($id); 

        $rewardsModel = new RewardsModel();
        $rewards = $rewardsModel->where('value', $id)->where('name', 'reshare')->findAll(); 

        $userId = session()->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->find($userId);
        
        $agency_ids = $user['agency_id'] ?? '';
        $stamp_ids = $user['stamp_id'] ?? '';
        $tap_ids = $user['tap_id'] ?? '';
        $opr_ids = $user['opr_id'] ?? '';
        $reshare_ids = $user['reshare_id'] ?? ''; 
        $hidden = true;

        if (empty($agency_ids) && empty($stamp_ids) && empty($tap_ids) && empty($opr_ids) && !empty($reshare_ids)) {
            $hidden = false;
        }

        $role = session()->get('role');

        $viewData = [
            'id' => $reshare_entity['reshare_id'],
            'code' => $reshare_entity['reshare_code'],
            'name' => $reshare_entity['reshare_name'],
            'address' => $reshare_entity['reshare_address'] ?? '', 
            'phone' => $reshare_entity['reshare_phone'],
            'mail' => $reshare_entity['reshare_mail'],
            'balance_tjs' => $reshare_entity['balance_tjs'],
            'balance_rub' => $reshare_entity['balance_rub'],
            'penalty' => $reshare_entity['penalty'],
            'reward' => $reshare_entity['reward'],
        ];

        $data = [
            'rewards' => $rewards,
            'hidden' => $hidden,
            'role' => $role,
            'data' => $viewData, 
            'name' => 'reshare', 
        ];

        return view('organization/reshare/edit', $data);
    }

    public function update($id)
    {   
        $role = session()->get('role');
        $model = new ReshareModel();

        $data = [
            'reshare_name' => $this->request->getPost('name'),
            'reshare_phone' => $this->request->getPost('phone'),
            'reshare_mail' => $this->request->getPost('mail'),
            'reward' => $this->request->getPost('reward'),
            'balance_tjs' => $this->request->getPost('balance_tjs'),
            'balance_rub' => $this->request->getPost('balance_rub'),
            'penalty' => $this->request->getPost('penalty'),
        ];

        if ($role === 'superadmin') {
            $data['reshare_code'] = $this->request->getPost('code');
        }

        $model->update($id, $data);

        $action = 'Изменен ID Пере-раздачи: '.$id;
        $logger = new LogsController(); 
        $logger->logAction($action);
        
        return redirect()->to('/reshare')->with('success', 'Успешно обновлен!');
    }

    public function delete($id)
    {
 
        $model = new ReshareModel();
        $model->delete($id);

        // Log
        $action = 'Удален ID Пере-раздачи: '.$id;
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