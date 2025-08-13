<?php

namespace App\Controllers;

require_once '../vendor/autoload.php';

use App\Models\TransactionsModel;
use App\Models\UserModel;
use App\Models\AgencyModel;
use App\Models\StampModel;
use App\Models\TapModel;
use App\Models\OprModel;
use App\Models\ShareModel;
use App\Models\ReshareModel;
use App\Models\AcquiringModel;

use App\Controllers\LogsController;
use App\Controllers\Dashboard;
use App\Controllers\Profile;
use App\Controllers\PaysController;
use App\Controllers\BigExportController;


use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;


class Transactions extends BaseController
{   
    public function get_id_of_code($name_table, $value_table)
    {
        // получить id по code

        $c_name1 = $name_table."_id";
        $c_name2 = $name_table."_code";

        $BigExportController = new BigExportController();
        $model = $this->getModelByName($name_table);
        $result = $model->select($c_name1)->where($c_name2, $value_table)->asArray()->first();


        if ($result) {
            return $result[$c_name1];
        } else {
            return false;
        }   
    }

    public function get_code_of_id($name_table, $value_table)
    {
        // получить id по code

        $c_name1 = $name_table."_id";
        $c_name2 = $name_table."_code";

        $BigExportController = new BigExportController();
        $model = $this->getModelByName($name_table);
        $result = $model->select($c_name2)->where($c_name1, $value_table)->asArray()->first();


        if ($result) {
            return $result[$c_name2];
        } else {
            return false;
        }   
    }

    public function get_column($name_table, $value_table, $c_name1, $c_name2)
    {
        $c_name1_col = $name_table.$c_name1;
        $c_name2_col = $name_table.$c_name2;

        $model = $this->getModelByName($name_table);
        if (!$model) {
            return false;
        }

        $result = $model->select($c_name2_col)->where($c_name1_col, $value_table)->asArray()->first();

        if ($result && isset($result[$c_name2_col]) && !empty(trim($result[$c_name2_col]))) {
            return $result[$c_name2_col];
        } else {
            return false;
        }   
    }

    public function getModelFilter($user_id, $daterange, $name_table, $value_table, $currency)
    {

        $model = new UserModel();
        $user = $model->find($user_id);
        $agency_ids = explode(',', $user['agency_id']);
        $stamp_ids = explode(',', $user['stamp_id']);
        $tap_ids = explode(',', $user['tap_id']);
        $opr_ids = explode(',', $user['opr_id']);
        $share_ids = explode(',', $user['share_id'] ?? '');
        $reshare_ids = explode(',', $user['reshare_id'] ?? '');

        $model = new TransactionsModel();

        // Фильтрация по полям name и value
        $model->groupStart()
                    ->orGroupStart()
                        ->where('name', 'agency')
                        ->whereIn('value', $agency_ids)
                    ->groupEnd()
                    ->orGroupStart()
                        ->where('name', 'stamp')
                        ->whereIn('value', $stamp_ids)
                    ->groupEnd()
                    ->orGroupStart()
                        ->where('name', 'tap')
                        ->whereIn('value', $tap_ids)
                    ->groupEnd()
                    ->orGroupStart()
                        ->where('name', 'opr')
                        ->whereIn('value', $opr_ids)
                    ->groupEnd()
                    ->orGroupStart() // Добавляем группу для share
                        ->where('name', 'share')
                        ->whereIn('value', $share_ids)
                    ->groupEnd()
                    ->orGroupStart() // Добавляем группу для reshare
                        ->where('name', 'reshare')
                        ->whereIn('value', $reshare_ids)
                    ->groupEnd()
                ->groupEnd();

        


        // #1 Фильтруем по диапазону дат
        if (!empty($daterange)) {
            
            $dates = explode(' / ', $daterange);
            
            // Получаем начальную и конечную дату из диапазона
            $start_date = date('Y-m-d', strtotime($dates[0]));
            $end_date   = date('Y-m-d', strtotime($dates[1]));

            // Добавляем условия фильтрации по дате
            $model->where('payment_date' . " >= ", $start_date);
            $model->where('payment_date' . " <= ", $end_date);

        }


        // #2 Фильтруем по Организации
        if (!empty($name_table)) {

            if ($name_table !== "all") {

                if ($value_table !== "all") {
                    //$value_table = $this->get_column($name_table, $value_table, '_code', '_id');               
                    $model->where('name', $name_table)
                          ->where('value', $value_table);
                }else{

                    $model->where('name', $name_table);
                }
                

            }


        }


        // #3 Фильтруем по валюте
        if (!empty($currency)) {
            $model->where('currency', $currency);
        }


        return $model;
    }

    public function changeColumsNames($transactions, $user_id=null)
    {
        

        $model = new AgencyModel();
        $agencies = $model->findAll();
        $model = new StampModel();
        $stamps = $model->findAll();
        $model = new TapModel();
        $taps = $model->findAll();
        $model = new OprModel();
        $oprs = $model->findAll();
        $model = new ShareModel();
        $shares = $model->findAll();
        $model = new ReshareModel();
        $reshares = $model->findAll();

        foreach ($transactions as $key => $transaction) {
            switch ($transaction['name']) {
                case 'agency':
                        $match = array_search($transaction['value'], array_column($agencies, 'agency_id'));
                        $transactions[$key]['value'] = $agencies[$match]['agency_code'] ?? 'Unknown Agency';
                        $transactions[$key]['name'] = "Агенство";
                        break;
                    case 'stamp':
                        $match = array_search($transaction['value'], array_column($stamps, 'stamp_id'));
                        $transactions[$key]['value'] = $stamps[$match]['stamp_code'] ?? 'Unknown Stamp';
                        $transactions[$key]['name'] = "ППР";
                        break;
                    case 'tap':
                        $match = array_search($transaction['value'], array_column($taps, 'tap_id'));
                        $transactions[$key]['value'] = $taps[$match]['tap_code'] ?? 'Unknown Tap';
                        $transactions[$key]['name'] = "Пульт";
                        break;
                    case 'opr':
                        $match = array_search($transaction['value'], array_column($oprs, 'opr_id'));
                        $transactions[$key]['value'] = $oprs[$match]['opr_code'] ?? 'Unknown Opr';
                        $transactions[$key]['name'] = "Оператор";
                        break;
                    case 'share': 
                        $match = array_search($transaction['value'], array_column($shares, 'share_id'));
                        $transactions[$key]['value'] = $shares[$match]['share_code'] ?? 'Unknown Share';
                        $transactions[$key]['name'] = "Раздача";
                        break;
                    case 'reshare':
                        $match = array_search($transaction['value'], array_column($reshares, 'reshare_id'));
                        $transactions[$key]['value'] = $reshares[$match]['reshare_code'] ?? 'Unknown Reshare';
                        $transactions[$key]['name'] = "Пере-раздача";
                        break;

                }
            
        }


        return $transactions;
    }

    public function upTable($user_id, $daterange, $name_table, $value_table, $currency, $transform = true)
    {
        // фильтрация   
        $model = $this->getModelFilter($user_id, $daterange, $name_table, $value_table, $currency);
        $transactions = $model->findAll();

        if ($transform) {
            // Преобразование удобочитаемый формат некоторых столбцов
            $transactions = $this->changeColumsNames($transactions);
            $transactions = $this->appendValueNames($transactions, $name_table);
        }

        return $transactions;
    }

    public function appendValueNames($transactions, $name_table)
    {
        $PaysController = new PaysController();
        foreach($transactions as &$item){
            // изменить название Эквайринг
            if (isset($item['acquiring'])) {
                $item['acquiring'] = $PaysController->get_name_acq($item['acquiring']);
            }

            // ========= установить рядом (название)
            if ($name_table && $name_table !== 'all' && isset($item['value'])) {
                 $name = $this->get_column($name_table, $item['value'], '_code', '_name');

                if ($name) {
                    $item['value'] = $item['value'] . ' ('.$name.')';
                }
            }
        }
        return $transactions;
    }

    public function downTable($user_id, $daterange, $name_table, $value_table, $currency)
    {

        // фильтрация
        $model = $this->getModelFilter($user_id, $daterange, $name_table, $value_table, $currency);

        // Начало составления запроса
        $model->select('`name`, `value`');
        $model->select('COUNT(DISTINCT `transaction_id`) AS `count`, SUM(`amount`) AS `summa`', false);
        $model->groupBy('`name`, `value`');

        $transactions = $model->findAll();

        // Преобразование удобочитаемый формат некоторых столбцов
        $transactions = $this->changeColumsNames($transactions);
        

        $totalCount = 0;
        $totalSum = 0;


        foreach ($transactions as &$item){

            $totalSum += $item['summa'];
            $totalCount += $item['count'];

            // ========= установить рядом (название)
            
            $name = $this->get_column($name_table, $item['value'], '_code', '_name');

            if ($name) {
                $item['value'] = $item['value'] . ' ('.$name.')'; 
            }

        }


        $data = [
            'transactions' => $transactions,
            'totalCount' => $totalCount,
            'totalSum' => $totalSum,
        ];

        return $data;
    }

    public function export()
    {


        $user_id = $this->request->getVar('user_login');
        $startDate = $this->request->getVar('startDate');
        $endDate = $this->request->getVar('endDate');
        $name_table = $this->request->getVar('name_table');
        $value_table = $this->request->getVar('value_table');
        $currency = $this->request->getVar('currency');
        $daterange = $startDate.' / '.$endDate;
        


        // ======  Получить данные для верхней таблицы

        $uptable_data = $this->upTable($user_id, $daterange, $name_table, $value_table, $currency);


        
        // =======  Получить данные для нижней таблицы

        $data = $this->downTable($user_id, $daterange, $name_table, $value_table, $currency); 
        $downtable_data = $data['transactions'];




        // Создать лист Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('TICKETS');

        

        // Формируем Excel
        $data = $this->createExcel($sheet, $uptable_data, $downtable_data);
        $sheet = $data['sheet'];


        // Готовим файл к выгрузке
        $writer = new Xlsx($spreadsheet);
        $fileName = 'export.xlsx';

        // Очистка буфера
        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();


        // Log
        $action = 'Экспорт в excel Транзакции';
        $logger = new LogsController(); 
        $logger->logAction($action);


        // Установка заголовков для скачивания файла
        return $this->response
            ->setStatusCode(200)
            ->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', "attachment; filename=\"$fileName\"")
            ->setBody($excelOutput);
    }


    

    public function createExcel($sheet, $uptable_data, $downtable_data)
    {


        $user_id = session()->get('user_id');
        $UserModel = new UserModel();
        $user = $UserModel->find($user_id);


        //  =========================== верхняя таблица ==============================  //
    

        // Заголовки
        $headers_1 = [
            'Дата создания', 
            'Дата транзакции', 
            'Номер транзакции', 
            'Сумма', 
            'Валюта', 
            'Примечание', 
            'Организация', 
            'Название', 
            'Метод оплаты',
            'Банк',
            
        ]; 


        if ($user['acquiring'] === '1'){
            $headers_1[] = 'Эквайринг';
        }


        $letter = 'A';
        foreach ($headers_1 as $header) {
            $sheet->setCellValue("{$letter}1", $header);
            $letter++;
        }

        $rowNumber = 2; // Начиная со второй строки
        $rowNumberForSum = $rowNumber;
        

        // Добавление данных в лист
        foreach ($uptable_data as $pay) {

            $row = [
                $pay['creation_date'],            // Дата создания
                $pay['payment_date'],            // Дата транзакции
                $pay['receipt_number'],                 // Номер транзакции
                $pay['amount'],   // Сумма
                $pay['currency'],    // Валюта
                $pay['note'],              // Примечание
                $pay['name'],  // Организация
                $pay['value'],     // Название
                $pay['method'],               // Метод оплаты
                $pay['bank'],                     // Банк
                $pay['acquiring'],                     // Эквайринг
            
            ];

            if ($user['acquiring'] === '1'){
                $row[] = $pay['acquiring'];
            }


            $letter = 'A';
            foreach ($row as $cell) {
                $sheet->setCellValue("{$letter}{$rowNumber}", $cell);
                $letter++;
            }
            $rowNumber++;
        }

        // изменитт формат
        // $sheet->getStyle('M2:M'.$rowNumber)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

        // Применение автофильтра к диапазону с данными

        if ($user['acquiring'] === '1'){
            $c = 'K';
        }else{
            $c = 'J';
        }

        $sheet->setAutoFilter("A1:". $c . ($rowNumber - 1));
        


        // // Добавление строки "Итого"
        // $sheet->setCellValue("A{$rowNumber}", 'Итого:');

        // // Добавляем формулы для подсчета итогов
        // $columns = ['C', 'D', 'E', 'F', 'G', 'H'];


        // if (count($uptable_data) >= 1) {
        //     foreach ($columns as $column) {
        //         $sheet->setCellValue("{$column}{$rowNumber}", "=SUM({$column}{$rowNumberForSum}:{$column}" . ($rowNumber - 1) . ")");
        //     }
        // }else{
        //     foreach ($columns as $column) {
        //         $sheet->setCellValue("{$column}{$rowNumber}", "0");
        //     }
        // }
        


        // Сделать строку жирной
        // $sheet->getStyle("A{$rowNumber}:H{$rowNumber}")->applyFromArray(['font' => ['bold' => true,]]);


        // Выравнивание и автоширина для всех колонок с данными
        foreach (range('A', $sheet->getHighestDataColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $sheet->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        }




        //  =========================== нижняя таблица ==============================  //

    

        // Добавление заголовков 

        // Пустая строка между данными
        $rowNumberUptable = $rowNumber;

        $rowNumber++;
        $rowNumber++;
        $rowNumber++;

        $startRowDown = $rowNumber;
      

        
        $headers_2 = [
            'Организация', 
            'Название', 
            'Количество', 
            'Сумма'
        ];


        $letter = 'A';
        foreach ($headers_2 as $header) {
            $sheet->setCellValue("{$letter}{$rowNumber}", $header);
            // Устанавливаем стиль для ячейки
            $sheet->getStyle("{$letter}{$rowNumber}")->getFont()->setBold(true);
            $letter++;
        }

        
    
        // Добавление данных в лист
        $rowNumber++; // Переход к следующей строке для добавления данных
        $rowNumberForSum = $rowNumber;


        

        foreach ($downtable_data as $pay) {


            $row = [
                $pay['name'],
                $pay['value'],
                $pay['summa'],
                $pay['count'],
                
            ];


            $letter = 'A';
            foreach ($row as $cell) {
                $sheet->setCellValue("{$letter}{$rowNumber}", $cell);
                $letter++;
            }


            $rowNumber++;
        }




        
        // Добавление строки "Итого"
        $sheet->setCellValue("A{$rowNumber}", 'Итого:');

        // Добавляем формулы для подсчета итогов
        $columns = ['C', 'D'];

        if (count($downtable_data) >= 1) {
            foreach ($columns as $column) {
                $sheet->setCellValue("{$column}{$rowNumber}", "=SUM({$column}{$rowNumberForSum}:{$column}" . ($rowNumber - 1) . ")");
            }
        }else{
            foreach ($columns as $column) {
                $sheet->setCellValue("{$column}{$rowNumber}", "0");
            }
        }



        // Сделать строку жирной
        $sheet->getStyle("A{$rowNumber}:D{$rowNumber}")->applyFromArray(['font' => ['bold' => true,]]);


        $data = [
            'sheet' => $sheet,
            'rowNumber' => $rowNumber,
            'rowNumberUptable' => $rowNumberUptable 
        ];

        return $data;
    }




    public function gettable($tableName, $user_id)
    {

        $table = $tableName.'_id';
        
        $model = new UserModel();
        $user = $model->find($user_id);
        $ids = explode(',', $user[$table]);


        switch ($tableName) {
            case 'agency':
                $model = new AgencyModel();
                break;
            case 'stamp':
                $model = new StampModel();
                break;
            case 'tap':
                $model = new TapModel();
                break;
            case 'opr':
                $model = new OprModel();
                break;
            case 'share': // Добавляем ShareModel
                $model = new ShareModel();
                break;
            case 'reshare': // Добавляем ReshareModel
                $model = new ReshareModel();
                break;
        }
        
        $tableData = $model->whereIn($table, $ids)->findAll();

        return $tableData;
    }

    public function gettable_json()
    {

        // Получаем объект запроса
        $request = service('request');
        
        // Получаем значение параметра tableName
        $table_name = $request->getGet('table_name');
        $user_id = $request->getGet('user_id');

        $tableData = $this->gettable($table_name, $user_id);
        return $this->response->setJSON($tableData);
    }



    public function get_data_trans()
    {
        // Получаем параметры из POST-запроса
        $name_table = $this->request->getPost('name_table');
        $value_table = $this->request->getPost('value_table');
        $currency = $this->request->getPost('currency');
        $user_id = $this->request->getPost('user_login');
        $startDate = $this->request->getPost('startDate');
        $endDate = $this->request->getPost('endDate');
        $daterange = $startDate." / ".$endDate;
       
        // Получить данные для верхней таблицы
        $raw_transactions = $this->upTable($user_id, $daterange, $name_table, $value_table, $currency, false);
        $transactions = $this->changeColumsNames($raw_transactions);
        $transactions = $this->appendValueNames($transactions, $name_table);

        // Получить данные для нижней таблицы
        $data = $this->downTable($user_id, $daterange, $name_table, $value_table, $currency);


        $downTable = $data['transactions'];
        $totalCount = $data['totalCount'];
        $totalSum = $data['totalSum'];    



        $payment_dates = [];
        $agency_amounts = [];
        $stamp_amounts = [];
        $tap_amounts = [];
        $opr_amounts = [];
        $share_amounts = [];
        $reshare_amounts = [];
        $agency_labels = [];
        $stamp_labels = [];
        $tap_labels = [];
        $opr_labels = [];
        $share_labels = []; // Инициализируем массив для меток Раздачи
        $reshare_labels = [];

        // Build chart data from raw transactions, before names are translated
        foreach($raw_transactions as $transaction){
            $payment_dates[] = $transaction['payment_date'];

            if ($transaction['name'] === 'agency') {
                $agency_amounts[] = $transaction['amount'];
                $agency_labels[] = $transaction['payment_date'];
            }elseif ($transaction['name'] === 'stamp') {
                $stamp_amounts[] = $transaction['amount'];
                $stamp_labels[] = $transaction['payment_date'];
            }elseif ($transaction['name'] === 'tap') {
                $tap_amounts[] = $transaction['amount'];
                $tap_labels[] = $transaction['payment_date'];
            }elseif ($transaction['name'] === 'opr') {
                $opr_amounts[] = $transaction['amount'];
                $opr_labels[] = $transaction['payment_date'];
            }elseif ($transaction['name'] === 'share') {
                $share_amounts[] = $transaction['amount']; // Суммы для Раздачи
                $share_labels[] = $transaction['payment_date']; // Метки дат для Раздачи
            }elseif ($transaction['name'] === 'reshare') {
                $reshare_amounts[] = $transaction['amount'];
                $reshare_labels[] = $transaction['payment_date'];
            }

        }

        $unique_payment_dates = array_unique($payment_dates);
        $labels = $unique_payment_dates;
        sort($labels);


        // Создаем пустые массивы данных для каждой линии
        $agency_data = array_fill_keys($labels, 0);
        $stamp_data = array_fill_keys($labels, 0);
        $tap_data = array_fill_keys($labels, 0);
        $opr_data = array_fill_keys($labels, 0);
        $share_data = array_fill_keys($labels, 0);
        $reshare_data = array_fill_keys($labels, 0);


        // Заполняем массивы данными из исходных массивов
        foreach ($agency_labels as $index => $label) {
            if(isset($agency_amounts[$index]) && isset($agency_data[$label])) $agency_data[$label] += $agency_amounts[$index];
        }
        foreach ($stamp_labels as $index => $label) {
            if(isset($stamp_amounts[$index]) && isset($stamp_data[$label])) $stamp_data[$label] += $stamp_amounts[$index];
        }
        foreach ($tap_labels as $index => $label) {
            if(isset($tap_amounts[$index]) && isset($tap_data[$label])) $tap_data[$label] += $tap_amounts[$index];
        }
        foreach ($opr_labels as $index => $label) {
            if(isset($opr_amounts[$index]) && isset($opr_data[$label])) $opr_data[$label] += $opr_amounts[$index];
        }
        foreach ($share_labels as $index => $label) {
            if(isset($share_amounts[$index]) && isset($share_data[$label])) $share_data[$label] += $share_amounts[$index];
        }
        foreach ($reshare_labels as $index => $label) {
            if(isset($reshare_amounts[$index]) && isset($reshare_data[$label])) $reshare_data[$label] += $reshare_amounts[$index];
        }

        // Преобразуем массивы данных в индексированные массивы
        $agency_amounts = array_values($agency_data);
        $stamp_amounts = array_values($stamp_data);
        $tap_amounts = array_values($tap_data);
        $opr_amounts = array_values($opr_data);
        $share_amounts = array_values($share_data); // Данные для Раздачи
        $reshare_amounts = array_values($reshare_data);

        $amounts = [
            'agency' => $agency_amounts,
            'stamp' => $stamp_amounts,
            'tap' => $tap_amounts,
            'opr' => $opr_amounts,
            'share' => $share_amounts,
            'reshare' => $reshare_amounts,
        ];


        // Цикл для добавления поля action к каждой транзакции
        foreach ($transactions as &$transaction) {
            $transaction['action'] = '<a href="' . base_url('transactions/edit/' . $transaction['transaction_id']) . '" class="btn btn-info btn-sm"><i class="fas fa-pencil-alt"></i></a> <a href="' . base_url('transactions/delete/' . $transaction['transaction_id']) . '" class="btn btn-danger btn-sm" onclick="return confirmDelete()"><i class="fas fa-trash"></i></a>';
            $transaction['receipt_photo'] = '<button type="button" class="btn btn-primary btn-sm showValue" value="'.$transaction['receipt_photo'].'"><i class="fas fa-eye"></i></button>';
        }

        

        // Новый элемент для добавления в конец массива
        $newData = [
            "name" => "Итого:",
            "value" => "",
            "count" => $totalCount,
            "summa" => $totalSum
        ];
        array_push($downTable, $newData);


        $data = [
            'transactions' => $transactions,
            'downTable' => $downTable,
            'totalCount' => $totalCount,
            'totalSum' => $totalSum,
            'labels' => $labels,
            'amounts' => $amounts,
        ];


        return $this->response->setJSON($data);
    }

    public function index()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }

        

        //////////////////////////////////////////////////////////////////////////////////

        // для поле пользователь
        $user_id = session()->get('user_id');
        $model = new UserModel();
        $user = $model->find($user_id);
        $username = session()->get('username');
        $userModel = new UserModel();
        $role = session()->get('role');
        if($role === "superadmin"){
            $users = $userModel->findAll();
        }else{
            $users = $userModel->where('parent', $user_id)->findAll();
        }

        // Добавляем ФИО к логину для отображения в фильтре
        foreach ($users as &$userfio) {
            if (!empty(trim((string) $userfio['fio']))) {
                $userfio['user_login'] .= ' (' . trim($userfio['fio']) . ')';
            }
        }
        unset($userfio);


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
            'user' => $user,
            'filter_values' => $ProfileController->get_filter_values()
        ];


        return view('transactions/index', $data);
    }

    public function create()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }

        // Log
        $action = 'Вход в страницу Транзакции/Создать';
        $logger = new LogsController(); 
        $logger->logAction($action);


        $user_id = session()->get('user_id');
        $model = new UserModel();
        $user = $model->find($user_id);
        $ids = explode(',', $user['agency_id']);

        $model = new AgencyModel();
        $agencies = $model->whereIn('agency_id', $ids)->findAll();

        $currencies = ['TJS', 'RUB'];

        $four_params = new Profile(); 
        $table_names = $four_params->four_params($user_id);

        $methods = ['Наличная оплата', 'Банковский перевод'];

        $model = new AcquiringModel();
        $acquirings = $model->findAll();


        $banks = $this->list_banks();

        $data = [
            'table_names' => $table_names,
            'agencies' => $agencies,
            'currencies' => $currencies,
            'methods' => $methods,
            'acquirings' => $acquirings,
            'user' => $user,
            'banks' => $banks,
            

        ];


        return view('transactions/create', $data);
    }

    public function register()
    {
        $model = new TransactionsModel();
        

        $name_table = $this->request->getPost('name_table');
        $value_table = $this->request->getPost('value_table');
        $method = $this->request->getPost('method');
        $bank = $this->request->getPost('bank');
        $acquiring = $this->request->getPost('acquiring');
        // Убираем лишнее преобразование, так как теперь в форме передается ID
        // $value_table = $this->get_column($name_table, $value_table, '_code', '_id');

        if ($acquiring === 'not_select') {
            $acquiring = null;
        }
        if ($method === 'Наличная оплата') {
            $bank = null;
        }


        $data = [
            'amount' => $this->request->getPost('amount'),
            'currency' => $this->request->getPost('currency'),
            'payment_date' => $this->request->getPost('payment_date'),
            'receipt_number' => $this->request->getPost('receipt_number'),
            'note' => $this->request->getPost('note'),
            'name' => $name_table,
            'value' => $value_table,
            'method' => $method,
            'acquiring' => $acquiring,
            'bank' => $bank,
            
        ];

        $file = $this->request->getFile('receipt_photo');
        if ($file->isValid() && !$file->hasMoved()) {
            // Check the file extension
            $validExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
            $extension = $file->getExtension();

            if (in_array($extension, $validExtensions)) {
                // Create the save path if it doesn't exist
                $path = FCPATH . 'uploads/checks';
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }

                $newName = $file->getRandomName();
                $file->move($path, $newName);
                
                $data['receipt_photo'] = $newName;
            } else {
                // Handle invalid file extension
                return redirect()->back()->withInput()->with('error', 'Неверное расширение файла! Допустимы только JPG, JPEG, PNG, PDF.');
            }
        }

        if ($model->insert($data)) {
            // On successful save
            return redirect()->to('/transactions')->with('success', 'Успешно создан!');
        } else {
            // Handle save errors
            return redirect()->back()->withInput()->with('error', 'Ошибка!');
        }
    }


    public function list_banks()
    {
        return ['ОриенБанк','МБТ','Амонатбанк','Душанбе Сити'];
    }

    public function edit($id)
    {
        
        $user_id = session()->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->where('user_id', $user_id)->first();

        $four_params = new Profile(); 
        $table_names = $four_params->four_params($user_id);

        $model = new TransactionsModel();
        $transaction = $model->find($id);
        // Убираем преобразование, так как теперь в форме используется ID
        // $transaction['value'] = $this->get_column($transaction['name'], $transaction['value'], '_id', '_code');

        $tableData = $this->gettable($transaction['name'], $user_id);


        $currencies = ['TJS', 'RUB'];
        $methods = ['Наличная оплата', 'Банковский перевод'];

        $model = new AcquiringModel();
        $acquirings = $model->findAll();


        $banks = $this->list_banks();

        $data = [
            'transaction' => $transaction,
            'table_names' => $table_names,
            'table_values' => $tableData,
            'currencies' => $currencies,
            'methods' => $methods,
            'acquirings' => $acquirings,
            'user' => $user,
            'banks' => $banks,
        ];


        // Log
        $action = 'Попытка изменить Транзакции';
        $logger = new LogsController(); 
        $logger->logAction($action);


        return view('transactions/edit', $data);
    }

    public function update($id)
    {   
        
        $model = new TransactionsModel();
        

        $name_table = $this->request->getPost('name_table');
        $value_table = $this->request->getPost('value_table');
        $method = $this->request->getPost('method');
        $bank = $this->request->getPost('bank');
        $acquiring = $this->request->getPost('acquiring');
        // Убираем лишнее преобразование, так как теперь в форме передается ID
        // $value_table = $this->get_column($name_table, $value_table, '_code', '_id');


        if ($acquiring === 'not_select') {
            $acquiring = null;
        }
        if ($method === 'Наличная оплата') {
            $bank = null;
        }

        $data = [
            'amount' => $this->request->getPost('amount'),
            'currency' => $this->request->getPost('currency'),
            'payment_date' => $this->request->getPost('payment_date'),
            'receipt_number' => $this->request->getPost('receipt_number'),
            'note' => $this->request->getPost('note'),
            'name' => $name_table,
            'value' => $value_table,
            'method' => $method,
            'acquiring' => $acquiring,
            'bank' => $bank,
        ];


        $file = $this->request->getFile('receipt_photo');
        if ($file->isValid() && !$file->hasMoved()) {
            // Check the file extension
            $validExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
            $extension = $file->getExtension();

            if (in_array($extension, $validExtensions)) {
                // Создайте путь сохранения, если он не существует
                // Примечание: здесь используется WRITEPATH, в register() - FCPATH. Оставляем как есть.
                $path = WRITEPATH . 'uploads/checks'; 
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }

                $newName = $file->getRandomName();
                $file->move($path, $newName);
                
                $data['receipt_photo'] = $newName;
            } else {
                // Handle invalid file extension
                return redirect()->back()->withInput()->with('error', 'Неверное расширение файла! Допустимы только JPG, JPEG, PNG, PDF.');
            }
        }

        if ($model->update($id, $data)) {
            // В случае успешного сохранения

            // Log
            $action = 'Изменен Транзакции ID: '.$id;
            $logger = new LogsController(); 
            $logger->logAction($action);


            return redirect()->to('/transactions')->with('success', 'Успешно обновлен!');
        } else {
            // Обработка ошибок сохранения
            return redirect()->back()->withInput()->with('success', 'Ошибка!');
        }
    }

    public function delete($id)
    {
        // Handle the deletion of the agency from the database
        $model = new TransactionsModel();
        $model->delete($id);


        // Log
        $action = 'Удалено Транзакции ID: '.$id;
        $logger = new LogsController(); 
        $logger->logAction($action);


        // Redirect to the list of agencies after deletion
        return redirect()->back()->with('success', 'Успешно удален!');
    }

    private function getModelByName($tableName)
    {
        switch ($tableName) {
            case 'agency':
                return new AgencyModel();
            case 'stamp':
                return new StampModel();
            case 'tap':
                return new TapModel();
            case 'opr':
                return new OprModel();
            case 'share':
                return new ShareModel();
            case 'reshare':
                return new ReshareModel();
            default:
                // Можно выбросить исключение или вернуть null, если имя таблицы неизвестно
                // throw new \InvalidArgumentException("Unknown table name: $tableName");
                return null; 
        }
    }
    
    
}