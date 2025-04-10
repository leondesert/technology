<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PaymentsModel;
use App\Models\PaymentsLogModel;

class Payments extends BaseController
{
    public function index()
    {
        if ( ! session()->get('is_logged_in'))
            return redirect()->to('/login');
        $session = \Config\Services::session();
        $userID = $session->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->where('user_id', $userID)->first();
        return view('payments/index', ['user' => $user]);
    }

    public function dcb()
    {
        // Get action
        $action = $_GET['act'] ?? '';
        // Log request
        $log = new PaymentsLogModel();
        $log->insert([
            'datetime' => date('Y-m-d H:i:s'),
            'host' => $_SERVER['REMOTE_ADDR'],
            'type' => 'fromNemo',
            'method' => $action,
            'request' => json_encode([
                'GET' => $_GET,
                'POST' => $_POST,
            ], JSON_PRETTY_PRINT),
            'response' => ''
        ]);
        $logID = $log->insertID();
        // Init
        $success = false;
        $message = [
            'errorCode'    => 1,
            'errorMessage' => 'Something goes wrong',
        ];
        // Perform action
        switch ($action) {
            case 'register.do':
                // Регистрация заказа
                $order = [
                    'userName'    => $_POST['userName'] ?? null,
                    'password'    => $_POST['password'] ?? null,
                    'orderNumber' => $_POST['orderNumber'] ?? null,
                    'amount'      => $_POST['amount'] ?? null,
                    'returnUrl'   => $_POST['returnUrl'] ?? null,
                    'currency'    => $_POST['currency'] ?? null,
                    'description' => $_POST['description'] ?? null,
                    'language'    => $_POST['language'] ?? null,
                    'jsonParams'  => $_POST['jsonParams'] ?? null,
                ];
                // TODO: 
                // $response = $dcb->createOrder($order);
                if ( ! empty($response['orderId']) and ! empty($response['formUrl']))
                    $message = [
                        'orderId' => $response['orderId'],
                        'formUrl' => $response['formUrl'],
                        'errorCode' => 0,
                        'errorMessage' => 'ok',
                    ];
                break;

            case 'getOrderStatusExtended.do':
                // Запрос состояния заказа
                $order = [
                    'userName'    => $_POST['userName'] ?? null,
                    'password'    => $_POST['password'] ?? null,
                    'orderId'     => $_POST['orderId'] ?? null,
                    'orderNumber' => $_POST['orderNumber'] ?? null,
                    'language'    => $_POST['language'] ?? null,
                ];
                // TODO: get status

                if ($success)
                    $message = [
                        'orderStatus' => 0, // 0 - Заказ зарегистрирован, но не оплачен
                        // 1 - Предавторизованная сумма захолдирована (для двухстадийных платежей)
                        // 2 - Проведена полная авторизация суммы заказа
                        // 6 - Авторизация отклонена
                        'cardAuthInfo' => [
                            'expiration'     => $status['expiration'],
                            'cardholderName' => $status['cardholderName'],
                            'approvalCode'   => $status['approvalCode'],
                            'pan'            => $status['pan'],
                        ],
                        'errorCode' => 0,
                        'errorMessage' => 'ok',
                    ];
                break;

            case 'deposit.do':
                // Завершение oплаты заказа
                $deposit = [
                    'userName' => $_POST['userName'] ?? null,
                    'password' => $_POST['password'] ?? null,
                    'orderId'  => $_POST['orderId'] ?? null,
                    'amount'   => $_POST['amount'] ?? null,
                ];
                // TODO

                if ($success)
                    $message = [
                        'errorCode' => 0,
                        'errorMessage' => 'ok',
                    ];
                break;

            case 'reverse.do':
                // Отмена оплаты
            
                if ($success)
                    $message = [
                        'errorCode' => 0,
                        'errorMessage' => 'ok',
                    ];
                break;

            case 'refund.do':
                // Возврат средств
            
                if ($success)
                    $message = [
                        'errorCode' => 0,
                        'errorMessage' => 'ok',
                    ];
                break;

            case 'registerPreAuth.do':
                // Регистрация заказа c преавторизацией
            
                break;

            default:
                $message = [
                    'errorCode' => 2,
                    'errorMessage' => 'Unknown action',
                ];
        }
        // Log response
        $log->update($logID, [
            'response' => json_encode($message, JSON_PRETTY_PRINT)
        ]);
        // Output
        echo json_encode($message);
    }

    public function dcb_success()
    {
        $message = json_encode([
            'errorCode'    => 0,
            'errorMessage' => 'ok',
        ], JSON_PRETTY_PRINT);

        $log = new PaymentsLogModel();
        $log->insert([
            'datetime' => date('Y-m-d H:i:s'),
            'host' => $_SERVER['REMOTE_ADDR'],
            'type' => 'fromDcb',
            'method' => 'success',
            'request' => json_encode([
                'GET' => $_GET,
                'POST' => $_POST,
            ], JSON_PRETTY_PRINT),
            'response' => $message
        ]);

        echo $message;
    }

    public function dcb_error()
    {
        $message = json_encode([
            'errorCode'    => 0,
            'errorMessage' => 'ok',
        ], JSON_PRETTY_PRINT);

        $log = new PaymentsLogModel();
        $log->insert([
            'datetime' => date('Y-m-d H:i:s'),
            'host' => $_SERVER['REMOTE_ADDR'],
            'type' => 'fromDcb',
            'method' => 'error',
            'request' => json_encode([
                'GET' => $_GET,
                'POST' => $_POST,
            ], JSON_PRETTY_PRINT),
            'response' => $message
        ]);
        
        echo $message;
    }
}
