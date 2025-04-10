<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Controllers\LogsController;

class Tickets extends BaseController
{
    public function ticket()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); // Редирект на страницу входа, если не авторизован
        }

        // Log
        $action = 'Вход в страницу Билеты';
        $logger = new LogsController(); 
        $logger->logAction($action);


        // Если пользователь авторизован, выполняем код для защищенной страницы
        return view('tickets/ticket');
    }
    public function fops()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); // Редирект на страницу входа, если не авторизован
        }

        // Log
        $action = 'Вход в страницу Фопс';
        $logger = new LogsController(); 
        $logger->logAction($action);

        // Если пользователь авторизован, выполняем код для защищенной страницы
        return view('tickets/fops');
    }
    public function segments()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); // Редирект на страницу входа, если не авторизован
        }

        // Log
        $action = 'Вход в страницу Сегменты';
        $logger = new LogsController(); 
        $logger->logAction($action);

        // Если пользователь авторизован, выполняем код для защищенной страницы
        return view('tickets/segments');
    }
    public function taxes()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); // Редирект на страницу входа, если не авторизован
        }

        $taxes = $this->getUniqueColumns();
        
        // $taxes = [
        //     'A2', 'AE', 'CN', 'CP', 'CS', 'DE', 'E3', 'F6', 'FX', 
        //     'GE', 'I6', 'IO', 'IR', 'JA', 'JN', 'M6', 'OY', 'RA', 
        //     'T2', 'TP', 'TR', 'UJ', 'UZ', 'YQ', 'YR', 'ZR', 'ZZ'
        // ];


        $data = [
            'taxes' => $taxes,
        ];

        // Log
        $action = 'Вход в страницу Сборы';
        $logger = new LogsController(); 
        $logger->logAction($action);

        // Если пользователь авторизован, выполняем код для защищенной страницы
        return view('tickets/taxes', $data);
    }
    public function emd()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); // Редирект на страницу входа, если не авторизован
        }

        // Log
        $action = 'Вход в страницу EMD';
        $logger = new LogsController(); 
        $logger->logAction($action);

        // Если пользователь авторизован, выполняем код для защищенной страницы
        return view('tickets/emd');
    }



    // uniqueTaxCodes


    protected $db;

    public function __construct()
    {
        // Получение экземпляра базы данных
        $this->db = \Config\Database::connect();
    }


    public function getUniqueColumns()
    {

        $db = \Config\Database::connect();
        $sql = "SELECT DISTINCT COLUMN_NAME
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_NAME = 'taxes_unics'
                ORDER BY COLUMN_NAME";

        $query = $db->query($sql);

        $result = $query->getResultArray();

        $taxes = [];

        foreach ($result as $key => $value) {
            $taxes[] = $value['COLUMN_NAME'];
        }

        
        $taxes = array_diff($taxes, ['tickets_id', 'id']);
        
        // Устанавливаем данные в сессию
        $session = \Config\Services::session();
        $session->set([
            'uniqueTaxCodes' => $taxes,
            
        ]);


        return $taxes;
    }

    public function addColumnIfNotExists()
    {
        $tableName = 'taxes_unics';
        $columnName = $this->request->getPost('column_name');

        if ($tableName && $columnName) {
            // Проверка, существует ли колонка
            $sqlCheck = "SELECT COLUMN_NAME 
                         FROM INFORMATION_SCHEMA.COLUMNS 
                         WHERE TABLE_NAME = ? AND COLUMN_NAME = ?";
            $queryCheck = $this->db->query($sqlCheck, [$tableName, $columnName]);
            $resultCheck = $queryCheck->getResultArray();

            // Если колонка не существует, добавить её
            if (empty($resultCheck)) {
                $sqlAdd = "ALTER TABLE $tableName 
                           ADD $columnName VARCHAR(50)";
                $this->db->query($sqlAdd);
                $status = "Такс успешно добавлен.";
            } else {
                $status = "Такс уже существует.";
            }
        } else {
            $status = "Выберите Такс.";
        }


        // Загрузка представления с сообщением
        return redirect()->to('/taxes')->with('success', $status);
    }

    public function deleteColumnIfExists()
    {
        $tableName = 'taxes_unics';
        $columnName = $this->request->getPost('column_name');

        if ($tableName && $columnName) {
            // Проверка, существует ли колонка
            $sqlCheck = "SELECT COLUMN_NAME 
                         FROM INFORMATION_SCHEMA.COLUMNS 
                         WHERE TABLE_NAME = ? AND COLUMN_NAME = ?";
            $queryCheck = $this->db->query($sqlCheck, [$tableName, $columnName]);
            $resultCheck = $queryCheck->getResultArray();

            // Если колонка существует, удалить её
            if (!empty($resultCheck)) {
                $sqlDelete = "ALTER TABLE $tableName 
                              DROP COLUMN $columnName";
                $this->db->query($sqlDelete);
                $status = "Такс успешно удален.";
            } else {
                $status = "Такс не существует.";
            }
        } else {
            $status = "Выберите Такс.";
        }



        // Загрузка представления с сообщением
        return redirect()->to('/taxes')->with('success', $status);
    }











}
