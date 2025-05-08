<?php

namespace App\Controllers;

use App\Models\CurrenciesModel;
use App\Controllers\LogsController;

class CurrenciesController extends BaseController
{   

    public function index()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }

        
        $model = new CurrenciesModel();
        $currencies = $model->findAll();

        // Заменяем null значения курса валюты на пустую строку
        if (!empty($currencies)) {
            foreach ($currencies as &$currency_item) {
                if (isset($currency_item['value']) && $currency_item['value'] === null) {
                    $currency_item['value'] = "";
                }
            }
            unset($currency_item); // Разрываем ссылку на последний элемент
        }
            

            
        $data = [
            'currencies' => $currencies,
        ];


        // Log
        $action = 'Вход в страницу Курс валюты';
        $logger = new LogsController(); 
        $logger->logAction($action);


        return view('currencies/index', $data);
    }

    public function create()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }

        // Log
        $action = 'Вход в страницу Курс валюты/Создать';
        $logger = new LogsController(); 
        $logger->logAction($action);

        return view('currencies/create');
    }

    public function register()
    {
        
        $model = new CurrenciesModel();
        $name = $this->request->getPost('name');


        $data = [
            'date' => $this->request->getPost('date'),
            'name' => $name,
            'value' => $this->request->getPost('value'),
            
        ];


        if ($model->insert($data)) {

            // Log
            $action = 'Создана курс валюта: '.$name;
            $logger = new LogsController(); 
            $logger->logAction($action);

            return redirect()->to('/currencies')->with('success', 'Успешно создан!');
        } else {

            // Log
            $action = 'Ошибка при создании курс валюта: '.$name;
            $logger = new LogsController(); 
            $logger->logAction($action);

            return redirect()->back()->withInput()->with('success', 'Ошибка!');
        }
    }

    public function edit($id)
    {
        $model = new CurrenciesModel();
        $currency = $model->find($id);


        $data = [
            'currency' => $currency,

        ];

        // Log
        $action = 'Попытка изменить Курс валюты ID: '.$id;
        $logger = new LogsController(); 
        $logger->logAction($action);


        return view('currencies/edit', $data);
    }

    public function update($id)
    {   
        
        $model = new CurrenciesModel();
        
        $data = [
            'date' => $this->request->getPost('date'),
            'name' => $this->request->getPost('name'),
            'value' => $this->request->getPost('value'),
            
        ];

        if ($model->update($id, $data)) {

            // Log
            $action = 'Изменен Курс валюты ID: '.$id;
            $logger = new LogsController(); 
            $logger->logAction($action);

            return redirect()->to('/currencies')->with('success', 'Успешно обновлен!');
        } else {

            // Log
            $action = 'Ошибка при изменении Курс валюты ID: '.$id;
            $logger = new LogsController(); 
            $logger->logAction($action);

            return redirect()->back()->withInput()->with('success', 'Ошибка!');
        }
    }

    public function delete($id)
    {
        // Handle the deletion of the agency from the database
        $model = new CurrenciesModel();
        

        if ($model->delete($id)) {
            // Log
            $action = 'Удалено Курс валюты ID: '.$id;
            $logger = new LogsController(); 
            $logger->logAction($action);

            return redirect()->back()->with('success', 'Успешно удален!');
        }else{
            // Log
            $action = 'Ошибка при удалении Курс валюты ID: '.$id;
            $logger = new LogsController(); 
            $logger->logAction($action);

            return redirect()->back()->withInput()->with('success', 'Ошибка!');
        }
        
    }

    
    
}
