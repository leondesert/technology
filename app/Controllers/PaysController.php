<?php

namespace App\Controllers;

use App\Models\PaysModel;
use App\Models\UserModel;
use App\Models\AcquiringModel;
use App\Models\TransactionsModel;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;



class PaysController extends BaseController
{ 


    public function index()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }

        // проверяем доступ
        $userID = session()->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->where('user_id', $userID)->first();
        if ($user['acquiring'] === '0') {
            return redirect()->to('/login'); 
        }


        // для поле Дата
        $dashboard = new Dashboard(); 
        $dates = $dashboard->getDates();

        $name_payments = [
            [
                "name" => "Все",
                "value" => ""
            ],
            [
                "name" => "Эсхата Онлайн",
                "value" => "eskhata_online"
            ],
            [
                "name" => "Душанбе Сити (мир)",
                "value" => "dc_mir_km"
            ],
            [
                "name" => "Алиф (инвойс)",
                "value" => "alif_mobi"
            ],
            [
                "name" => "Душанбе Сити (виза рф)",
                "value" => "dc_visa"
            ],
            [
                "name" => "Алиф (корти милли)",
                "value" => "alif_km"
            ],
            [
                "name" => "Душанбе Сити (кошелек)",
                "value" => "dc_wallet"
            ],
            [
                "name" => "Душанбе Сити (корти милли)",
                "value" => "dc_km"
            ],
            [
                "name" => "IBT (виза)",
                "value" => "ibt_visa"
            ],
            [
                "name" => "IBT (корти милли)",
                "value" => "ibt_km"
            ],
        ];

        $currencies = [
            
            [
                "name" => "TJS",
                "value" => "972"
            ],
            /*[
                "name" => "RUB",
                "value" => "643"
            ],
            [
                "name" => "USD",
                "value" => "840"
            ],
            [
                "name" => "EUR",
                "value" => "978"
            ],*/
        ];




        $ProfileController = new Profile();


        $data = [
            'name_payments' => $name_payments,
            'currencies' => $currencies,
            'dates' => $dates,
            'filter_values' => $ProfileController->get_filter_values()
        ];

        return view('pays/index', $data);
    }


    public function fetchData()
    {
        $request = service('request');
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'];
        

        // Получение дополнительных параметров
        $startDate = $request->getPost('startDate');
        $endDate = $request->getPost('endDate');
        $name_payment = $request->getPost('name_payment');
        $currency = $request->getPost('currency');
        $status = $request->getPost('status');

        
        $filters = [
            "startDate" => $startDate,
            "endDate" => $endDate,
            "name_payment" => $name_payment,
            "currency" => $currency,
            "status" => $status,

        ];


        $model = new PaysModel();

        $totalRecords = $model->countAll();
        $totalRecordwithFilter = $model->countFiltered($searchValue, $filters);

        $data = $model->getFilteredData($start, $length, $searchValue, $filters, $request->getPost('order'), $request->getPost('columns'));

        

        $Datetime = $model->DatatimeCheck($filters);


        $response = [
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data,
            "Datetime" => $Datetime
        ];

        return $this->response->setJSON($response);
    }


    public function downtable()
    {

        
        $status = $this->request->getPost('status');
        $currency = $this->request->getPost('currency');
        $name_payment = $this->request->getPost('name_payment');
        $startDate = $this->request->getPost('startDate');
        $endDate = $this->request->getPost('endDate');


        //  ============   для нижней таблицы

        $filters = [
            "startDate" => $startDate,
            "endDate" => $endDate,
            "name_payment" => $name_payment,
            "currency" => $currency,
            "status" => $status,
        ];



        $paysModel = new PaysModel();
        $paymentSummary = $paysModel->getPaymentSummary($filters);

    
        

        // ============  для самой нижней таблицы

    
        // 1. получить acquirings

        $model = new AcquiringModel();
        $acquirings = $model->findAll();
        $acquiringsTotal = $paymentSummary['acquirings'];
        


        foreach ($acquirings as &$acquiring) {

            $acquiring['bank'] = $acquiring['value'];
            $acquiring['amount'] = 0;
            $acquiring['pay_amount'] = 0;

            foreach ($acquiringsTotal as $item) {
                if ($acquiring['value'] === $item['bank']) {

                    $acquiring['amount'] = $item['amount'];
                    $acquiring['pay_amount'] = $item['amount'];
                    break;
                }
            }
            
            
        }

        

        






        // 2. получить transactions

        $model = new TransactionsModel();
        $transactions = $model->forAcquiringBalance($filters);
        $paymentSummary['transactions'] = $transactions;







        // 3. получить первый баланс

        // Вычитаем 1 день
        $dateTime = new \DateTime($startDate);
        $dateTime->modify('-1 day');
        $newDate = $dateTime->format('Y-m-d');


        $filters = [
            "startDate" => '2024-08-01',
            "endDate" => $newDate,
            "name_payment" => $name_payment,
            "currency" => $currency,
            "status" => $status,
        ];



        $paysModel = new PaysModel();
        $results = $paysModel->getPaymentSummary($filters);
        $data = $this->getFistBalance($filters, $results);

        

        $paymentSummary['fist_balances'] = $data;
        $balances = $data['acquirings'];






        // 4. result =  balance + acq - trans 

        foreach ($acquirings as &$acquiring) {

            $acquiring['transaction_amount'] = 0;


            // Вычитаем суммы из transactions
            foreach ($transactions as $transaction) {
                if ($acquiring['bank'] === $transaction['acquiring']) {
                    $acquiring['amount'] -= floatval($transaction['total_amount']);

                    $acquiring['transaction_amount'] = $transaction['total_amount'];
                    break;
                }
            }

            // Складываем суммы из additionalAmounts
            // Начальный баланс
            foreach ($balances as $balance) {

                $acquiring['fist_balance'] = 0;


                if ($acquiring['bank'] === $balance['bank']) {
                    $acquiring['amount'] += $balance['amount'];

                    $acquiring['fist_balance'] = $balance['amount'];
                    break;
                }
            }

        }



        $paymentSummary['acquirings'] = $acquirings;




        return $this->response->setJSON($paymentSummary);
    }


    public function getFistBalance($filters, $paymentSummary)
    {

        // 1. получить transactions

        $model = new TransactionsModel();
        $transactions = $model->forAcquiringBalance($filters);


        // 2. получить acquirings

        // $model = new PaysModel();
        // $results = $model->getPaymentSummary($filters);
        // $acquirings = $results['acquirings'];


        $model = new AcquiringModel();
        $acquirings = $model->findAll();
        $acquiringsTotal = $paymentSummary['acquirings'];
        


        foreach ($acquirings as &$acquiring) {

            $acquiring['bank'] = $acquiring['value'];
            $acquiring['amount'] = 0;


            foreach ($acquiringsTotal as $item) {
                if ($acquiring['value'] === $item['bank']) {

                    $acquiring['amount'] = $item['amount'];
                    break;
                }
            }
        }


        $data['filters'] = $filters;
        $data['pays'] = $acquirings;


        // 3. balance = balance.db + acq - trans

        foreach ($acquirings as &$acquiring) {
            foreach ($transactions as $transaction) {
                if ($acquiring['bank'] === $transaction['acquiring']) {
                    $acquiring['amount'] -= floatval($transaction['total_amount']);
                    break;
                }
            }


            
                
            // получить баланс и добавить
            $model = new AcquiringModel();
            $res = $model->where('value', $acquiring['bank'])->first();
            $acquiring['name'] = $res['name'];
            $acquiring['amount'] += $res['balance'];
        }

        // unset($acquiring);



        $data['transactions'] = $transactions;
        $data['acquirings'] = $acquirings;




        return $data;
    }


    public function export()
    {
        $status = $this->request->getVar('status'); 
        $startDate = $this->request->getVar('startDate');
        $endDate = $this->request->getVar('endDate');
        $name_payment = $this->request->getVar('name_payment');
        $currency = $this->request->getVar('currency');
        $searchValue = '';
        $paysModel = new PaysModel();


        $filters = [
            "startDate" => $startDate,
            "endDate" => $endDate,
            "name_payment" => $name_payment,
            "currency" => $currency,
            "status" => $status,
        ];


        
        // Получить данные для верхней таблицы
        $uptable_data = $paysModel->getData($filters, $searchValue);



        // Получить данные для нижней таблицы
        $dataArray = $paysModel->getPaymentSummary($filters, $searchValue);
        $downtable_data = $dataArray['summary'];




        // Создать лист Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Эквайринг');




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




        return $this->response->setJSON($pays);    
    }


    public function createExcel($sheet, $uptable_data, $downtable_data)
    {
        //  =========================== верхняя таблица ==============================  //
    

        // Заголовки
        $secondTableHeaders = [
            'Номер заказа в Немо', 
            'Номер платежа в Немо', 
            'Сумма',
            'Сумма оплаты',
            'Сумма без комиссии',
            'Комиссия',
            'Комиссия банка (клиент)',
            'Комиссия банка (АВС)',
            'Валюта', 
            'Номер заказа в бд', 
            'Дата создания', 
            'Статус транзакции',
            'Метод оплаты', 
            'Номер заказа в эквайринге', 
            'Статус в банке', 
            'Дата платежа', 
            'Почта',
            'Телефон',
        ];

        $letter = 'A';
        foreach ($secondTableHeaders as $header) {
            $sheet->setCellValue("{$letter}1", $header);
            $letter++;
        }

        $rowNumber = 2; // Начиная со второй строки
        $rowNumberForSum = $rowNumber;
        

        // Добавление данных в лист
        foreach ($uptable_data as $pay) {

            $row = [
                $pay['description'],            // Номер заказа в Немо
                $pay['orderNumber'],            // Номер платежа в Немо
                $pay['amount'],                 // Сумма
                $pay['summa_with_comission'],   // Сумма оплаты
                $pay['summa_out_comission'],    // Сумма без комиссии
                $pay['comission'],              // Комиссия
                $pay['comission_bank_client'],  // Комиссия банка (клиент)
                $pay['comission_bank_avs'],     // Комиссия банка (АВС)
                $pay['currency'],               // Валюта
                $pay['id'],                     // Номер заказа на сайте
                $pay['tranDateTime'],           // Дата создания
                $pay['orderStatus'],            // Статус Транзакции
                $pay['name_payment'],           // Метод оплаты
                $pay['unic_order_id'],          // Номер заказа в эквайринге
                $pay['status'],                 // Статус в банке
                $pay['datetime'],               // Дата платежа
                $pay['jsonParams'],             // Почта
                $pay['phone'],                  // Номер телефона
            ];


            $letter = 'A';
            foreach ($row as $cell) {
                $sheet->setCellValue("{$letter}{$rowNumber}", $cell);
                $letter++;
            }
            $rowNumber++;
        }

        
        
        // Применение автофильтра к диапазону с данными
        $sheet->setAutoFilter("A1:R" . ($rowNumber - 1));

    


        // Добавление строки "Итого"
        $sheet->setCellValue("A{$rowNumber}", 'Итого:');

        // Добавляем формулы для подсчета итогов
        $columns = ['C', 'D', 'E', 'F', 'G', 'H'];


        if (count($uptable_data) >= 1) {
            foreach ($columns as $column) {
                $sheet->setCellValue("{$column}{$rowNumber}", "=SUM({$column}{$rowNumberForSum}:{$column}" . ($rowNumber - 1) . ")");

                // изменить формат колон
                $sheet->getStyle($column.'2:'.$column.$rowNumber)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            }
        }else{
            foreach ($columns as $column) {
                $sheet->setCellValue("{$column}{$rowNumber}", "0");
            }
        }
        

        

        


        // Сделать строку жирной
        $sheet->getStyle("A{$rowNumber}:H{$rowNumber}")->applyFromArray(['font' => ['bold' => true,]]);


        // Выравнивание и автоширина для всех колонок с данными
        foreach (range('A', $sheet->getHighestDataColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $sheet->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        }




        //  =========================== нижняя таблица ==============================  //

    

        // Добавление заголовков 

        // Пустая строка между данными
        $rowNumber++;
        $rowNumber++;
        $rowNumber++;

        $startRowDown = $rowNumber;
      

        $headers = [
            'Название оплаты', 
            'Значение оплаты', 
            'Количество', 
            'Процент',
            'Сумма',
            'Сумма оплаты', 
            'Сумма без комиссии', 
            'Комиссия',
            'Комиссия банка (клиент)',
            'Комиссия банка (АВС)',
        ];


        $letter = 'A';
        foreach ($headers as $header) {
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
                $pay['name_payment'],                   // Название оплаты
                $pay['bank'],                           // Значение оплаты
                $pay['count'],                          // Количество
                $pay['percentage'],                     // Процент
                $pay['amount'],                         // Сумма +
                $pay['total_amount'],                   // Сумма оплаты
                $pay['total_amount_comission'],         // Сумма без комиссии
                $pay['comission'],                      // Комиссия
                $pay['comission_bank_client'],          // Комиссия банка (клиент)
                $pay['comission_bank_avs'],             // Комиссия банка (АВС)
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
        $columns = ['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];

        if (count($downtable_data) >= 1) {
            foreach ($columns as $column) {
                $sheet->setCellValue("{$column}{$rowNumber}", "=SUM({$column}{$rowNumberForSum}:{$column}" . ($rowNumber - 1) . ")");

            }
        }else{
            foreach ($columns as $column) {
                $sheet->setCellValue("{$column}{$rowNumber}", "0");
            }
        }


        
        // изменить формат колон
        $columns = ['D', 'E', 'F', 'G', 'H', 'I', 'J'];
        foreach ($columns as $column) {
            
            $sheet->getStyle($column.$rowNumberForSum.':'.$column.$rowNumber)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        }




        // Сделать строку жирной
        $sheet->getStyle("A{$rowNumber}:J{$rowNumber}")->applyFromArray(['font' => ['bold' => true,]]);


        $data = [
            'sheet' => $sheet,
            'rowNumber' => $rowNumber,
        ];

        return $data;
    }

    public function get_name_acq($value)
    {
        switch ($value) {
            case 'dc':
                return 'Душанбе Сити';
                break;
            case 'alif':
                return 'Алиф';
                break;
            case 'eskhata':
                return 'Эсхата';
                break;
            case 'ibt':
                return 'IBT';
                break;
            
            default:
                return $value;
                break;
        }
    }



}