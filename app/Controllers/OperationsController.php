<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Controllers\LogsController;
use App\Models\ShareModel;
use App\Models\ReshareModel;
use App\Models\TicketsModel;

class OperationsController extends BaseController
{
    public function index()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }

        
        //////////////////////////////////////////////////////////////////////////////////

        // для поле пользователь
        $user_id = session()->get('user_id');
        $username = session()->get('username');
        $userModel = new UserModel();
        $role = session()->get('role');
        if($role === "superadmin"){
            $users = $userModel->findAll();
        }else{
            $users = $userModel->where("FIND_IN_SET('$user_id', parent) >", 0)->findAll();
        }

        // Добавляем ФИО к логину для отображения в фильтре
        foreach ($users as &$user) {
            if (!empty(trim((string) $user['fio']))) {
                $user['user_login'] .= ' (' . trim($user['fio']) . ')';
            }
        }
        unset($user);


        // для поле Валюта
        $currencies = ['TJS', 'RUB'];
        
        // для поле Дата
        $dashboard = new Dashboard(); 
        $dates = $dashboard->getDates();


        $ProfileController = new Profile();
    
        $data = [
            'users' => $users,
            'currencies' => $currencies,
            'dates' => $dates,
            'user_id' => $user_id,
            'username' => $username,
            'role' => $role,
            'filter_values' => $ProfileController->get_filter_values()
            
        
        ];


        // Log
        $action = 'Вход в страницу Операции';
        $logger = new LogsController(); 
        $logger->logAction($action);


        // Если пользователь авторизован, выполняем код для защищенной страницы
        return view('operations/index', $data);
    }
    

    public function get_active_params()
    {

        // Получаем параметры из POST-запроса
        $user_id = $this->request->getPost('user_id');
        $filter = $this->request->getPost('filter');

        $model = new UserModel();
        $user = $model->where('user_id', $user_id)->first();
        
        $colum_name = $filter.'_id';
        $ids = $user[$colum_name];
        $ids = explode(",", $ids);
        $ids = array_map('intval', $ids);
        $ids = "(" . implode(",", $ids) . ")";

        $data = [
            'colum_name' => $colum_name,
            'ids' => $ids
        ];
        
        return $this->response->setJSON($data);
    }

    public function reduct_share_reshare()
    {
        if (!session()->get('is_logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }
    
        $ticket_BSONUM = $this->request->getPost('ticket_BSONUM');
        $share_code = $this->request->getPost('share_code');
        $reshare_code = $this->request->getPost('reshare_code');

        if (empty($ticket_BSONUM)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Ticket BSONUM is required.']);
        }
    
        $shareModel = new ShareModel();
        $reshareModel = new ReshareModel();
        $ticketsModel = new TicketsModel();

        // Find ticket_id from ticket_BSONUM
        $ticket = $ticketsModel->where('tickets_BSONUM', $ticket_BSONUM)->first();
        if (!$ticket || !isset($ticket['tickets_id'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid Ticket BSONUM.']);
        }
        $ticket_id = $ticket['tickets_id'];
    
        $share_id = null;
        if (!empty($share_code)) {
            $share = $shareModel->where('share_code', $share_code)->first();
            if ($share && isset($share['share_id'])) {
                $share_id = $share['share_id'];
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid Share Code.']);
            }
        }
    
        $reshare_id = null;
        if (!empty($reshare_code)) {
            $reshare = $reshareModel->where('reshare_code', $reshare_code)->first();
            if ($reshare && isset($reshare['reshare_id'])) {
                $reshare_id = $reshare['reshare_id'];
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid Reshare Code.']);
            }
        }
    
        $data = [
            'share_id' => $share_id,
            'reshare_id' => $reshare_id,
        ];

    
        $updated = $ticketsModel->update($ticket_id, $data);
    
        if ($updated) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Ticket updated successfully.']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update ticket.']);
        }
    }
}
