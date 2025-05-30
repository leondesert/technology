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

        $data = [
            'role' => session()->get('role'),
        ];
        
        return view('organization/share/index', $data);
    }

    public function getDataTable()
    {
        if (!session()->get('is_logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $request = service('request');
        $model = new ShareModel();

        // Параметры DataTables
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'] ?? '';
        $order = $request->getPost('order');
        $columns = $request->getPost('columns');

        // Базовый запрос
        $builder = $model;

        // Поиск
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('share_code', $searchValue)
                ->orLike('share_name', $searchValue)
                ->orLike('share_address', $searchValue)
                ->orLike('share_phone', $searchValue)
                ->orLike('share_mail', $searchValue)
                ->groupEnd();
        }

        // Общее количество записей (до фильтрации)
        $totalRecords = $model->countAllResults(false); // false чтобы не сбрасывать предыдущие условия

        // Количество отфильтрованных записей
        $recordsFiltered = $builder->countAllResults(false); // false чтобы не сбрасывать предыдущие условия

        // Сортировка
        if (!empty($order)) {
            $columnName = $columns[$order[0]['column']]['data'];
            $dir = $order[0]['dir'];
            $builder->orderBy($columnName, $dir);
        } else {
            $builder->orderBy('share_id', 'DESC'); // Сортировка по умолчанию
        }

        // Лимит и смещение
        if ($length != -1) {
            $builder->limit($length, $start);
        }

        $data = $builder->findAll();

        $output = [
            'draw' => intval($draw),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($recordsFiltered),
            'data' => $data,
        ];

        return $this->response->setJSON($output);
    }



    public function create()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }

        $logger = new LogsController(); 
        $logger->logAction('Вход в страницу Раздачи/Создать');
        
        return view('organization/share/create');
    }

    public function register()
    {
        $model = new ShareModel();

        $data = [
            'share_code' => $this->request->getPost('share_code'),
            'share_name' => $this->request->getPost('share_name'),
            'share_address' => $this->request->getPost('share_address'), // Добавлено по аналогии
            'share_phone' => $this->request->getPost('share_phone'),
            'share_mail' => $this->request->getPost('share_mail'),
            'reward' => $this->request->getPost('reward'),
            'balance_tjs' => $this->request->getPost('balance_tjs'),
            'balance_rub' => $this->request->getPost('balance_rub'),
            'penalty' => $this->request->getPost('penalty'),
        ];

        if ($model->insert($data)) {
            return redirect()->to('/share')->with('success', 'Раздача успешно создана!');
        } else {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }
    }

    public function reg_reward()
    {
        $model = new RewardsModel();

        $action = $this->request->getVar('action');
        $share_id = $this->request->getVar('id'); // Используем share_id
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
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $logger = new LogsController(); 
        $logger->logAction('Попытка изменить Раздачу ID: ' . $id);

        $shareModel = new ShareModel(); // Используем ShareModel
        $share_entity = $shareModel->find($id); // Переменная названа share_entity во избежание конфликта

        if (!$share_entity) {
            return redirect()->to('/share')->with('error', 'Раздача не найдена.');
        }

        $rewardsModel = new RewardsModel();
        $rewards = $rewardsModel->where('value', $id)->where('name', 'share')->findAll(); // Используем 'share'

        $userId = session()->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->find($userId);
        
        $agency_ids = $user['agency_id'] ?? '';
        $stamp_ids = $user['stamp_id'] ?? '';
        $tap_ids = $user['tap_id'] ?? '';
        $opr_ids = $user['opr_id'] ?? '';
        $share_ids = $user['share_id'] ?? ''; // Поле для share в таблице users
        $hidden = true;

        if (empty($agency_ids) && empty($stamp_ids) && empty($tap_ids) && empty($opr_ids) && !empty($share_ids)) {
            $hidden = false;
        }

        $role = session()->get('role');

        // Передаем данные как 'data' для совместимости с существующими edit views
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
            'data' => $viewData, // Передаем подготовленный массив
            'name' => 'share', // Для универсального шаблона редактирования
        ];

        return view('organization/share/edit', $data);
    }

    public function update($id)
    {   
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }
        $role = session()->get('role');
        $model = new ShareModel();

        $dataToUpdate = [
            'share_name' => $this->request->getPost('name'),
            'share_address' => $this->request->getPost('address'), 
            'share_phone' => $this->request->getPost('phone'),
            'share_mail' => $this->request->getPost('mail'),
            'reward' => $this->request->getPost('reward'),
            'balance_tjs' => $this->request->getPost('balance_tjs'),
            'balance_rub' => $this->request->getPost('balance_rub'),
            'penalty' => $this->request->getPost('penalty'),
        ];

        if ($role === 'superadmin') {
            $dataToUpdate['share_code'] = $this->request->getPost('code');
        }

        if ($model->update($id, $dataToUpdate)) {
            $logger = new LogsController(); 
            $logger->logAction('Изменена Раздача ID: ' . $id, json_encode($dataToUpdate));
            return redirect()->to('/share')->with('success', 'Раздача успешно обновлена!');

        } else {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }
    }

    public function delete($id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $model = new ShareModel();
        $rewardsModel = new RewardsModel();
        $rewardsModel->where('name', 'share')->where('value', $id)->delete();

        if ($model->delete($id)) {
            $logger = new LogsController(); 
            $logger->logAction('Удалена Раздача ID: ' . $id);
            return redirect()->back()->with('success', 'Раздача успешно удалена!');
        } else {
            return redirect()->back()->with('error', 'Ошибка удаления Раздачи.');
        }
    }
}