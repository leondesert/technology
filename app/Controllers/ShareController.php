<?php

namespace App\Controllers;

use App\Models\ShareModel;
use App\Models\UserModel;
use App\Models\RewardsModel;
use App\Controllers\LogsController;

class ShareController extends BaseController
{
    public function index()
    {   
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $logger = new LogsController(); 
        $logger->logAction('Вход в страницу Раздачи');

        $role = session()->get('role');
        $data = [
            'role' => $role,
        ];
        
        return view('organization/share/index', $data);
    }

    public function create()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }

        $action = 'Вход в страницу Раздачи/Создать'; 
        $logger = new LogsController(); 
        $logger->logAction($action);
        
        return view('organization/share/create');
    }

    public function register()
    {
        $model = new ShareModel();

        $data = [
            'share_code' => $this->request->getPost('share_code'),
            'share_name' => $this->request->getPost('share_name'),
            'share_address' => $this->request->getPost('share_address'),
            'share_phone' => $this->request->getPost('share_phone'),
            'share_mail' => $this->request->getPost('share_mail'),
            'reward' => $this->request->getPost('reward'),
            'balance_tjs' => $this->request->getPost('balance_tjs'),
            'balance_rub' => $this->request->getPost('balance_rub'),
            'penalty' => $this->request->getPost('penalty'),
        ];

        $model->insert($data);
        return redirect()->to('/share')->with('success', 'Успешно создан!');
    }

    public function reg_reward()
    {
        $model = new RewardsModel();

        $action = $this->request->getVar('action');
        $share_id = $this->request->getVar('id');
        $cityCodes = $this->request->getVar('citycodes');
        $percent = $this->request->getVar('procent');
        $reward_id = $this->request->getVar('reward_id');

        $status = "Успешно!";

        switch ($action) {
            case 'create':
                $existingRecord = $model->where('citycodes', $cityCodes)
                                        ->where('name', 'share') // Используем 'share'
                                        ->where('value', $share_id)
                                        ->first();

                if (is_null($existingRecord)) {
                    $model->insert([
                        'procent' => $percent,
                        'citycodes' => $cityCodes,
                        'name' => 'share', // Используем 'share'
                        'value' => $share_id,
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

        $action = 'Попытка изменить Раздачу';
        $logger = new LogsController(); 
        $logger->logAction($action); 

        $shareModel = new ShareModel();
        $share_entity = $shareModel->find($id); 

        $rewardsModel = new RewardsModel();
        $rewards = $rewardsModel->where('value', $id)->where('name', 'share')->findAll(); 

        $userId = session()->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->find($userId);
        
        $agency_ids = $user['agency_id'] ?? '';
        $stamp_ids = $user['stamp_id'] ?? '';
        $tap_ids = $user['tap_id'] ?? '';
        $opr_ids = $user['opr_id'] ?? '';
        $share_ids = $user['share_id'] ?? ''; 
        $hidden = true;

        if (empty($agency_ids) && empty($stamp_ids) && empty($tap_ids) && empty($opr_ids) && !empty($share_ids)) {
            $hidden = false;
        }

        $role = session()->get('role');

        $viewData = [
            'id' => $share_entity['share_id'],
            'code' => $share_entity['share_code'],
            'name' => $share_entity['share_name'],
            'address' => $share_entity['share_address'] ?? '', 
            'phone' => $share_entity['share_phone'],
            'mail' => $share_entity['share_mail'],
            'balance_tjs' => $share_entity['balance_tjs'],
            'balance_rub' => $share_entity['balance_rub'],
            'penalty' => $share_entity['penalty'],
            'reward' => $share_entity['reward'],
        ];

        $data = [
            'rewards' => $rewards,
            'hidden' => $hidden,
            'role' => $role,
            'data' => $viewData, 
            'name' => 'share', 
        ];

        return view('organization/share/edit', $data);
    }

    public function update($id)
    {   
        $role = session()->get('role');
        $model = new ShareModel();

        $data = [
            'share_name' => $this->request->getPost('name'),
            'share_phone' => $this->request->getPost('phone'),
            'share_mail' => $this->request->getPost('mail'),
            'reward' => $this->request->getPost('reward'),
            'balance_tjs' => $this->request->getPost('balance_tjs'),
            'balance_rub' => $this->request->getPost('balance_rub'),
            'penalty' => $this->request->getPost('penalty'),
        ];

        if ($role === 'superadmin') {
            $data['share_code'] = $this->request->getPost('code');
        }

        $model->update($id, $data);

        $action = 'Изменен ID Раздачи: '.$id;
        $logger = new LogsController(); 
        $logger->logAction($action);
        
        return redirect()->to('/share')->with('success', 'Успешно обновлен!');
    }

    public function delete($id)
    {
 
        $model = new ShareModel();
        $model->delete($id);

        // Log
        $action = 'Удален ID Раздачи: '.$id;
        $logger = new LogsController(); 
        $logger->logAction($action);

        
        return redirect()->back()->with('success', 'Успешно удален!');
    }

}