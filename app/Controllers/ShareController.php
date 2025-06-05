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

    public function getDataTable()
    {
        if (!session()->get('is_logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $BigExportController = new BigExportController(); // Для getModal, если понадобится, или используем new ShareModel() напрямую
        $request = service('request');
        $shareModel = new ShareModel(); // Используем ShareModel

        // Параметры DataTables
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'] ?? '';
        $order = $request->getPost('order') ?? [];
        $columns = $request->getPost('columns') ?? [];

        // Получение данных о пользователе для фильтрации
        $user_id = session()->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->find($user_id);
        $role = session()->get('role');

        // Базовый запрос
        $builder = $shareModel;

        // Фильтрация по правам доступа
        if ($role !== "superadmin") {
            $share_ids_str = $user['share_id'] ?? '';
            if (!empty($share_ids_str)) {
                $share_ids = explode(',', $share_ids_str);
                $share_ids = array_map('intval', $share_ids);
                $builder->whereIn('share_id', $share_ids);
            } else {
                // Если у пользователя не назначены раздачи, он ничего не увидит (кроме superadmin)
                $builder->whereIn('share_id', [0]); // effectively no results
            }
        }

        // Поиск
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('share_code', $searchValue)
                ->orLike('share_name', $searchValue)
                // Добавьте другие поля для поиска, если необходимо
                ->groupEnd();
        }

        // Общее количество записей (до фильтрации)
        $totalRecordsBuilder = clone $builder; // Клонируем для подсчета без учета limit/offset и order
        $totalRecords = $totalRecordsBuilder->countAllResults(false);

        // Количество отфильтрованных записей
        $recordsFilteredBuilder = clone $builder;
        $recordsFiltered = $recordsFilteredBuilder->countAllResults(false);

        // Сортировка
        if (!empty($order)) {
            $columnIndex = $order[0]['column'];
            $columnName = $columns[$columnIndex]['data'] ?? 'share_id'; // Имя колонки из DataTables
            $dir = $order[0]['dir'] ?? 'asc';
            // Валидация имени колонки, чтобы избежать SQL-инъекций, если имя колонки приходит напрямую
            $allowedSortColumns = ['share_id', 'share_code', 'share_name', 'share_address', 'share_phone', 'share_mail', 'reward', 'balance_tjs', 'balance_rub', 'penalty'];
            if (in_array($columnName, $allowedSortColumns)) {
                $builder->orderBy($columnName, $dir);
            } else {
                $builder->orderBy('share_id', 'DESC');
            }
        } else {
            $builder->orderBy('share_id', 'DESC'); // Сортировка по умолчанию
        }

        // Лимит и смещение для пагинации
        if ($length != -1) {
            $builder->limit($length, $start);
        }

        $data = $builder->findAll();

        $output_data = [];
        foreach ($data as &$item) { // Используем ссылку, чтобы изменять оригинальный массив $data
            // Переименовываем поля для консистентности и добавляем новые
            $item['code'] = $item['share_code'];
            $item['name'] = $item['share_name'];
            $item['address'] = $item['share_address'] ?? ''; // Используем null coalescing operator на случай отсутствия
            $item['phone'] = $item['share_phone'];
            $item['mail'] = $item['share_mail'];

            // Формируем кнопки баланса и помещаем их в соответствующие поля
            $item['balance_tjs'] = '<button type="button" class="btn btn-primary btn-sm showValue" value="'.$item['share_code'].'" currency="TJS"><i class="fas fa-eye"></i></button>';
            $item['balance_rub'] = '<button type="button" class="btn btn-primary btn-sm showValue" value="'.$item['share_code'].'" currency="RUB"><i class="fas fa-eye"></i></button>';
            
            // Формируем кнопки действий
            $edit_button = '<a href="' . base_url('share/edit/' . $item['share_id']) . '" class="btn btn-info btn-sm"><i class="fas fa-pencil-alt"></i></a>';
            $delete_button = '';
            if ($role == 'superadmin') {
                $delete_button = ' <a href="' . base_url('share/delete/' . $item['share_id']) . '" class="btn btn-danger btn-sm" onclick="return confirmDelete()"><i class="fas fa-trash"></i></a>';
            }
            $item['action'] = $edit_button . $delete_button;
        }
        unset($item); // Разрываем ссылку на последний элемент

        $output_data = $data; // Теперь $data содержит все необходимые преобразования

        $output = [
            'draw' => intval($draw),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($recordsFiltered),
            'data' => $output_data,
        ];

        return $this->response->setJSON($output);
    }



    public function create()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }

        $action = 'Вход в страницу Раздачи/Создать'; // Сообщение для лога в переменную, как в OprController
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
        $logger->logAction('Попытка изменить Раздача'); // Сообщение в лог без ID, как в OprController

        $shareModel = new ShareModel(); // Используем ShareModel
        $share_entity = $shareModel->find($id); // Переменная названа share_entity во избежание конфликта
        // Убрана проверка if (!$share_entity) для соответствия OprController,
        // который не выполняет такую явную проверку в методе edit.
        // if (!$share_entity) {
        //     return redirect()->to('/share')->with('error', 'Раздача не найдена.');
        // }

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

        $data = [
            'share_name' => $this->request->getPost('name'),
            // 'share_address' => $this->request->getPost('address'), // Адрес не обновляем для соответствия OprController
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

        $logger = new LogsController(); 
        $logger->logAction('Изменена Раздача ID: ' . $id); // Не передаем данные в лог для соответствия
        return redirect()->to('/share')->with('success', 'Успешно обновлен!');
    }

    public function delete($id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $model = new ShareModel();
        // $rewardsModel = new RewardsModel(); // Удаление связанных вознаграждений убрано для соответствия OprController
        // $rewardsModel->where('name', 'share')->where('value', $id)->delete();

        if ($model->delete($id)) {
            $logger = new LogsController(); 
            $logger->logAction('Удалена Раздача ID: ' . $id);
            return redirect()->back()->with('success', 'Раздача успешно удалена!');
        } else {
            return redirect()->back()->with('error', 'Ошибка удаления Раздачи.');
        }
    }
}