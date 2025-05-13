<?php namespace App\Controllers;

require_once '../vendor/autoload.php';

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
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoSizeMode;



use App\Models\ServicesModel;
use App\Models\TransactionsModel;
use App\Models\UserModel;
use App\Models\TicketsModel;
use App\Models\AgencyModel;
use App\Models\StampModel;
use App\Models\TapModel;
use App\Models\OprModel;
use App\Models\ReportsModel;
use App\Models\PaysModel;


use App\Controllers\LogsController;
use App\Controllers\Transactions;
use App\Controllers\PaysController;


class BigExportController extends Controller
{
    public function getCurrencyId($name)
    {
        switch ($name) {
            case 'TJS':
                return "972";
                break;
            
            case 'RUB':
                return "643";
                break;

            case 'USD':
                return "840";
                break;

            case 'EUR':
                return "978";
                break;
        }
    }

    public function getColorForNumber($number)
    {
        switch ($number) {
            case '0':
                return 'FABF8F';
            case '1':
                return 'D99594';
            case '2':
                return '92D050';
            case '3':
                return '8DB3E2';
            case '4':
                return 'FFFF00'; 
            case '5':
                return 'B2A1C7';
            case '6':
                return '00FFFF';
            case '7':
                return 'c2d69b'; 
            case '8':
                return 'FABF8F';
            default:
                return null;
        }
    }

    public function Сonstructor($builder, $criteria, $logic)
    {

        if ($logic === 'OR') {
            $builder->groupStart();
        }

        foreach ($criteria as $condition) {
            switch ($condition['condition']) {
                case '=':
                    // Используем метод orWhere или where в зависимости от логики
                    $logic === 'OR' ? $builder->orWhere($condition['origData'], $condition['value'][0]) : $builder->where($condition['origData'], $condition['value'][0]);
                    break;

                case '!=':
                    $logic === 'OR' ? $builder->orWhere($condition['origData'] . ' !=', $condition['value'][0]) : $builder->where($condition['origData'] . ' !=', $condition['value'][0]);
                    break;

                case 'starts':
                    $logic === 'OR' ? $builder->orLike($condition['origData'], $condition['value'][0], 'after') : $builder->like($condition['origData'], $condition['value'][0], 'after');
                    break;

                case '!starts':
                    $logic === 'OR' ? $builder->orNotLike($condition['origData'], $condition['value'][0], 'after') : $builder->notLike($condition['origData'], $condition['value'][0], 'after');
                    break;

                case 'contains':
                    $logic === 'OR' ? $builder->orLike($condition['origData'], $condition['value'][0]) : $builder->like($condition['origData'], $condition['value'][0]);
                    break;

                case '!contains':
                    $logic === 'OR' ? $builder->orNotLike($condition['origData'], $condition['value'][0]) : $builder->notLike($condition['origData'], $condition['value'][0]);
                    break;

                case 'ends':
                    $logic === 'OR' ? $builder->orLike($condition['origData'], $condition['value'][0], 'before') : $builder->like($condition['origData'], $condition['value'][0], 'before');
                    break;

                case '!ends':
                    $logic === 'OR' ? $builder->orNotLike($condition['origData'], $condition['value'][0], 'before') : $builder->notLike($condition['origData'], $condition['value'][0], 'before');
                    break;

                case 'null':
                    $logic === 'OR' ? $builder->orWhere($condition['origData'] . ' IS NULL') : $builder->where($condition['origData'] . ' IS NULL');
                    break;

                case '!null':
                    $logic === 'OR' ? $builder->orWhere($condition['origData'] . ' IS NOT NULL') : $builder->where($condition['origData'] . ' IS NOT NULL');
                    break;

                case '<':
                    $logic === 'OR' ? $builder->orWhere($condition['origData'] . ' <', $condition['value'][0]) : $builder->where($condition['origData'] . ' <', $condition['value'][0]);
                    break;

                case '<=':
                    $logic === 'OR' ? $builder->orWhere($condition['origData'] . ' <=', $condition['value'][0]) : $builder->where($condition['origData'] . ' <=', $condition['value'][0]);
                    break;

                case '>':
                    $logic === 'OR' ? $builder->orWhere($condition['origData'] . ' >', $condition['value'][0]) : $builder->where($condition['origData'] . ' >', $condition['value'][0]);
                    break;

                case '>=':
                    $logic === 'OR' ? $builder->orWhere($condition['origData'] . ' >=', $condition['value'][0]) : $builder->where($condition['origData'] . ' >=', $condition['value'][0]);
                    break;

                case 'between':
                    $logic === 'OR' ? $builder->orGroupStart() : $builder->groupStart();
                    $builder->where($condition['origData'] . ' >=', $condition['value'][0]);
                    $builder->where($condition['origData'] . ' <=', $condition['value'][1]);
                    $builder->groupEnd();
                    break;

                case '!between':
                    $logic === 'OR' ? $builder->orGroupStart() : $builder->groupStart();
                    $builder->where($condition['origData'] . ' <', $condition['value'][0]);
                    $builder->orWhere($condition['origData'] . ' >', $condition['value'][1]);
                    $builder->groupEnd();
                    break;

                
            }
        }

        if ($logic === 'OR') {
            $builder->groupEnd();
        }



        return $builder;
    }

    public function getData($params)
    {

        $criteria = [];
        $logic = "";

        if (isset($params['searchBuilder']['criteria']) && isset($params['searchBuilder']['logic'])) {
            $criteria = $params['searchBuilder']['criteria'];
            $logic = $params['searchBuilder']['logic'];
        }

        // подключение к бд
        $db = \Config\Database::connect();
        $builder = $db->table('tickets');

        // Определение нужных полей
        $ticketsFields = ['tickets.tickets_type', 'tickets.tickets_currency', 'tickets.tickets_dealdate', 'tickets.tickets_dealtime', 'tickets.tickets_OPTYPE', 'tickets.tickets_TRANS_TYPE', 'tickets.tickets_BSONUM', 'tickets.tickets_EX_BSONUM', 'tickets.tickets_TO_BSONUM', 'tickets.tickets_FARE', 'tickets.tickets_PNR_LAT', 'tickets.tickets_DEAL_date', 'tickets.tickets_DEAL_disp', 'tickets.tickets_DEAL_time', 'tickets.tickets_DEAL_utc', 'tickets.summa_no_found', 'opr.opr_code', 'agency.agency_code', 'emd.emd_value', 'fops.fops_type', 'fops.fops_amount', 'passengers.fio', 'passengers.pass', 'passengers.pas_type', 'passengers.citizenship', 'segments.citycodes', 'segments.carrier', 'segments.class', 'segments.reis', 'segments.flydate', 'segments.flytime', 'segments.basicfare', 'stamp.stamp_code', 'tap.tap_code', 'taxes.tax_code', 'taxes.tax_amount', 'taxes.tax_amount_main', 'tickets.penalty_currency', 'tickets.penalty_summa', 'tickets.penalty', 'tickets.reward', 'tickets.reward_procent'];

        // Заголовки
        $headers = ['Тип билета', 'Валюта билета', 'Дата формирования', 'Время формирования', 'Тип операции', 'Тип транзакции', 'Номер билета', 'Номер старшего билета', 'Номер основного билета', 'Тариф цена', 'PNR', 'Дата оформления', 'Индентификатор продавца', 'Время оформления', 'Время оформления UTC', 'Сумма обмена без EMD', 'Код оператора', 'Код агентства', 'Сумма EMD', 'Вид оплаты', 'Сумма оплаты', 'ФИО', 'Паспорт', 'Тип', 'Гражданство', 'Маршрут', 'Перевозчик', 'Класс', 'Рейс', 'Дата полёта', 'Время полёта', 'Тариф', 'Код ППР', 'Код пульта', 'Код сбора', 'Сумма сбора', 'Суммы сборов', 'Курс валюты', 'Сумма штрафа', 'Штраф', 'Вознаграждение', 'Процент вознаграждение'];

        $uniqueTax = session()->get('uniqueTaxCodes');
    

        // Добавляем префикс к каждому элементу массива
        $uniqueTaxCodes = array_map(function($code) {
            return 'taxes_unics.' . $code;
        }, $uniqueTax);


        $ticketsFields = array_merge($ticketsFields, $uniqueTaxCodes);
        $headers = array_merge($headers, $uniqueTax);



        // Заголовки для видимых полей
        $filteredHeaders = explode(',', $params['visibleColumns']);


        // Обязательные
        $requireds = ['Тип билета', 'Валюта билета', 'Дата формирования', 'Тип операции', 'Тип транзакции', 'Код агентства', 'Код ППР', 'Код пульта', 'Код оператора', 'Маршрут', 'Перевозчик', 'Тариф цена', 'Суммы сборов', 'Код сбора'];

        $requireds_colums = ['tickets_type', 'tickets_currency', 'tickets_dealdate', 'tickets_OPTYPE', 'tickets_TRANS_TYPE', 'agency_code', 'stamp_code', 'tap_code', 'opr_code', 'citycodes', 'carrier', 'tickets_FARE', 'tax_amount_main', 'tax_code'];


        // Определить какое обязательное поле отсутсвует
        $not_requireds = [];
        foreach ($requireds as $item) {
            if (!in_array($item, $filteredHeaders)) {
                $not_requireds[] = $item;
            }
        }


        // добавить обязательные поля
        $filteredHeaders = array_merge($filteredHeaders, $not_requireds);

        // определить колонки обязательные
        $mapping = array_combine($requireds, $requireds_colums);
        // Формирование нового массива с нужным порядком полей
        $not_requireds_colums = [];
        foreach ($not_requireds as $header) {
            if (array_key_exists($header, $mapping)) {
                $not_requireds_colums[] = $mapping[$header];
            }
        }


        // Создание ассоциативного массива для сопоставления
        $mapping = array_combine($headers, $ticketsFields);

        // Формирование нового массива с нужным порядком полей
        $filteredFields = [];
        foreach ($filteredHeaders as $header) {
            if (array_key_exists($header, $mapping)) {
                $filteredFields[] = $mapping[$header];
            }
        }

        
        // Формирование строки запроса
        $builder->select($filteredFields);

        // Присоединение таблиц
        $builder->join('opr', 'opr.opr_id = tickets.opr_id', 'left');
        $builder->join('agency', 'agency.agency_id = tickets.agency_id', 'left');
        $builder->join('passengers', 'passengers.passengers_id = tickets.passengers_id', 'left');
        $builder->join('stamp', 'stamp.stamp_id = tickets.stamp_id', 'left');
        $builder->join('tap', 'tap.tap_id = tickets.tap_id', 'left');
        $builder->join('taxes', 'taxes.tickets_id = tickets.tickets_id', 'left');
        $builder->join('emd', 'emd.tickets_id = tickets.tickets_id', 'left');
        $builder->join('fops', 'fops.tickets_id = tickets.tickets_id', 'left');
        $builder->join('segments', 'segments.tickets_id = tickets.tickets_id', 'left');
        $builder->join('taxes_unics', 'taxes_unics.tickets_id = tickets.tickets_id', 'left');
        


        // Применяем фильтр
        $builder = $this->filter_tickets($params, $builder);


        // Конструктор
        $builder = $this->Сonstructor($builder, $criteria, $logic);
        

        $query = $builder->get();
        $results = $query->getResultArray();

        $data = [
            "data" => $results,
            "filteredHeaders" => $filteredHeaders,
            "not_requireds" => $not_requireds,
            "not_requireds_colums" => $not_requireds_colums
            
        ];

        return $data;    
    }

    public function groupedData($data)
    {
        // Группировка данных по типу транзакции
        $groupedData = [];
        foreach ($data as $row) {

            // Тип билета = ETICKET and Тип операции = SALE and Тип транзакции = SALE

            if ($row['tickets_type'] == "ETICKET" && $row['tickets_OPTYPE'] == "SALE" && $row['tickets_TRANS_TYPE'] == "SALE") {
                $groupedData["SALE"][] = $row;
            }

            // Тип билета = ETICKET and Тип операции = SALE and Тип транзакции = EXCHANGE
            if ($row['tickets_type'] == "ETICKET" && $row['tickets_OPTYPE'] == "SALE" && $row['tickets_TRANS_TYPE'] == "EXCHANGE") {
                $groupedData["EXCHANGE"][] = $row;
            }


            // Тип билета = ETICKET and Тип операции = REFUND and Тип транзакции = REFUND
            if ($row['tickets_type'] == "ETICKET" && $row['tickets_OPTYPE'] == "REFUND" && $row['tickets_TRANS_TYPE'] == "REFUND") {
                $groupedData["REFUND"][] = $row;
            }



            // Тип билета = ETICKET and Тип операции = REFUND and Тип транзакции = CANCEL
            if ($row['tickets_type'] == "ETICKET" && $row['tickets_OPTYPE'] == "REFUND" && $row['tickets_TRANS_TYPE'] == "CANCEL") {
                $groupedData["CANCEL"][] = $row;
            }



            // Тип билета = EMD and Тип операции = SALE and Тип транзакции = EXCHANGE
            if ($row['tickets_type'] == "EMD" && $row['tickets_OPTYPE'] == "SALE" && $row['tickets_TRANS_TYPE'] == "EXCHANGE") {
                $groupedData["EMD_EXCHANGE"][] = $row;
            }


            // Тип билета = EMD and Тип операции = SALE and Тип транзакции = REFUND
            if ($row['tickets_type'] == "EMD" && $row['tickets_OPTYPE'] == "SALE" && $row['tickets_TRANS_TYPE'] == "REFUND") {
                $groupedData["EMD_REFUND"][] = $row;
            }
        }


        return $groupedData;
    }

    public function addNewElements($table_name, $groupedData, $filteredHeaders)
    {
        // Добавить новые элементы в массив
        $db = \Config\Database::connect();
        $builder = $db->table($table_name);
        $results_table = $builder->get()->getResult();
        $builder = $db->table('rewards');
        $results_rewards = $builder->get()->getResult();
        $builder = $db->table('currencies');
        $results_currencies = $builder->get()->getResult();
        $c_name = $table_name.'_code';
        $forRewardColnum = ['SALE', 'REFUND', 'EXCHANGE', 'CANCEL'];

        foreach ($forRewardColnum as $index => $type) {
            if (isset($groupedData[$type])) {
                foreach ($groupedData[$type] as $k => $t) {

                    $rewardValue = $this->exception($t, $table_name, $results_table, $results_rewards, "reward");
                    $penaltyValue = $this->exception($t, $table_name, $results_table, $results_rewards, "penalty");
    
                    $penaltyV = null;
                    $rewardV = null;

                    if ($type == "CANCEL") {
                        $currencyValue = 0;
                        foreach ($results_currencies as $row) {
                            if ($row->date == $t['tickets_dealdate'] && $row->name == $t['tickets_currency']) {
                                $currencyValue = $row->value;
                                break; 
                            }
                        }

                        $penaltyV = $this->roundCents($currencyValue * $penaltyValue);
                        

                        // значения для полей 'Курс валюты', 'Сумма штрафа', 'Штраф'
                        $groupedData[$type][$k]['penalty_currency'] = $this->roundCents($currencyValue);
                        $groupedData[$type][$k]['penalty_summa'] = $this->roundCents($penaltyValue);
                        $groupedData[$type][$k]['penalty'] = $this->roundCents($penaltyV);
                        
                    }

                    if ($type == "SALE" || $type == "REFUND" || $type == "EXCHANGE") {

                        $rewardV = $t['tickets_FARE'] * $rewardValue / 100;

                        // значения для полей 'Вознаграждение', 'Процент вознаграждение'
                        $groupedData[$type][$k]['reward'] = $this->roundCents($rewardV);
                        $groupedData[$type][$k]['reward_procent'] = $this->roundCents($rewardValue);

                    }
                    
                    
                    // значения для полей tax
                    // $uniqueTaxCodes = [
                    //     'A2', 'AE', 'CN', 'CP', 'CS', 'DE', 'E3', 'F6', 'FX', 
                    //     'GE', 'I6', 'IO', 'IR', 'JA', 'JN', 'M6', 'OY', 'RA', 
                    //     'T2', 'TP', 'TR', 'UJ', 'UZ', 'YQ', 'YR', 'ZR', 'ZZ'
                    // ];
                    
                    // $uniqueTaxCodes = session()->get('uniqueTaxCodes');
                    // $rowData = array_fill_keys($uniqueTaxCodes, null);


                    $taxCodes = explode(',', $t['tax_code']);
                    $taxAmounts = explode(',', $t['tax_amount_main']);
                    
                    foreach ($taxCodes as $index => $code) {
                        if (in_array($code, $filteredHeaders)) {
                            $groupedData[$type][$k][$code] = $taxAmounts[$index];
                            // $groupedData[$type][$k][$code] = 1000;
                        }

                    }




                }

            }

        }

        return $groupedData;
    }

    public function is_table_name($params)
    {
        // фильтр по 4 параметрам конструктор
        $model = new UserModel();
        $user = $model->where('user_id', $params['user_login'])->first();
        $filter = $user['filter'];

        $table_name = null;
        if (isset($params['searchBuilder']['criteria'])) {
            $table_name = $this->if_four_params($params['searchBuilder']['criteria']);

            if ($table_name === null) {
                $table_name = $filter;
            }
        }

        $parent = $this->isParent($table_name, $filter);

        if ($parent === true) {
            $table_name = $filter;
        }

        

        return $table_name;
    }

    public function getColumnOfIndex($index) 
    {
        return $index !== false ? Coordinate::stringFromColumnIndex($index + 1) : false;
    }

    public function exportData()
    {

        // Получение параметров запроса
        $params = $this->request->getPost();
        $getData = $this->getData($params);

        // экспорт по type
        $is_airline = false;
        $is_site = false;
        if ($params['type'] == "airline" ) {
            $is_airline = true;
        }elseif ($params['type'] == "site") {
            $is_site = true;
        }


        $data = $getData["data"];
        $filteredHeaders = $getData["filteredHeaders"];
        $not_requireds = $getData["not_requireds"];
        $not_requireds_colums = $getData["not_requireds_colums"];

        $searchBuilder = [];
        if (isset($params['searchBuilder'])) {
            $searchBuilder = $params["searchBuilder"];
        }

      

        // Определение типов транзакций для разделения данных
        $transactionTypes = ['SALE', 'REFUND', 'EXCHANGE', 'CANCEL', 'EMD_REFUND', 'EMD_EXCHANGE'];

        // Группировка данных по типу транзакции
        $groupedData = $this->groupedData($data);
        

        // фильтр по 4 параметрам конструктор
        $table_name = $this->is_table_name($params);


        // Добавить новые элементы в массив
        $groupedData = $this->addNewElements($table_name, $groupedData, $filteredHeaders);
        

        // меняем формат ячеек номеров 
        $fheaders = ['Номер билета', 'Номер старшего билета', 'Номер основного билета'];
        $fheaders = array_intersect($fheaders, $filteredHeaders); // удалить элемент по видимости
        $columnIndexes = [];
        foreach ($fheaders as $fheader) {
            $index = array_search($fheader, $filteredHeaders); 
            if ($index !== false) {
                $columnIndexes[$fheader] = Coordinate::stringFromColumnIndex($index + 1);
            }
        }

        // меняем формат ячеек сумм
        $fheaders = ['Штраф', 'Вознаграждение', 'Сумма сбора', 'Сумма оплаты', 'Тариф цена', 'Сумма EMD'];
        $fheaders = array_intersect($fheaders, $filteredHeaders); // удалить элемент по видимости
        $columnIndexesSum = [];
        foreach ($fheaders as $fheader) {
            $index = array_search($fheader, $filteredHeaders); 
            if ($index !== false) {
                $columnIndexesSum[$fheader] = Coordinate::stringFromColumnIndex($index + 1);
            }
        }

        // номер строки для листа MAIN
        $forMainNumLast = [];


        // Создание экземпляра
        $spreadsheet = new Spreadsheet();

        // Применение стиля шрифта ко всем листам
        $spreadsheet->getDefaultStyle()->getFont()->setName('Palatino Linotype');

        // Удалить обязательные заголовки
        $filteredHeaders = array_diff($filteredHeaders, $not_requireds);
        $filteredHeaders = array_values($filteredHeaders);

        // Формируем заголовки для каждого листа
        $filteredHeadersBase = $filteredHeaders;

        // Создание листов
        foreach ($transactionTypes as $index => $type) {
            // Первый лист уже создан по умолчанию
            if ($index > 0) { 
                $spreadsheet->createSheet();
            }

            // Создать лист
            $spreadsheet->setActiveSheetIndex($index);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle($type);


            // формируем заголовки для каждого листа
            if ($type !== 'CANCEL') {

                $keys1 = ['Штраф', 'Курс валюты', 'Сумма штрафа'];
                $filteredHeaders = array_diff($filteredHeaders, $keys1);
                $filteredHeaders = array_values($filteredHeaders);

            }

           if (!in_array($type, ['SALE', 'REFUND', 'EXCHANGE'])) {

                $keys2 = ['Процент вознаграждение', 'Вознаграждение'];
                $filteredHeaders = array_diff($filteredHeaders, $keys2);
                $filteredHeaders = array_values($filteredHeaders);

            }


            // Добавление заголовков на лист
            $sheet->fromArray($filteredHeaders, null, 'A1');
            



            // Добавление данных на лист
            if (isset($groupedData[$type])) {

                // Удалить колонки
                foreach ($groupedData[$type] as &$data) {

                    // Удалить обязательные колонки
                    foreach ($not_requireds_colums as $required) {
                        unset($data[$required]);
                    }


                    // Удалить колонки
                    if ($type !== 'CANCEL') {
                        unset($data['penalty_currency']);
                        unset($data['penalty_summa']);
                        unset($data['penalty']);
                    }

                    if (!in_array($type, ['SALE', 'REFUND', 'EXCHANGE'])) {
                        unset($data['reward_procent']);
                        unset($data['reward']);
                    }
                }


                // добавить данных
                $sheet->fromArray($groupedData[$type], null, 'A2');

                // Установка автофильтра для всех столбцов с данными
                $sheet->setAutoFilter($sheet->calculateWorksheetDimension());
            }


            // Определение номера последней строки с данными
            $lastRow = $sheet->getHighestRow();
            $forMainNumLast[$type] = $lastRow + 1;
            


            // Добавление формулы
            $paymentAmountIndex = array_search('Сумма оплаты', $filteredHeaders);   // Сумма оплаты
            $fareIndex = array_search('Тариф цена', $filteredHeaders);              // Тариф цена
            $summaSboraIndex = array_search('Сумма сбора', $filteredHeaders);       // Сумма сбора
            $summaEmdIndex = array_search('Сумма EMD', $filteredHeaders);           // Сумма EMD
            $penaltyIndex = array_search('Штраф', $filteredHeaders);                // Штраф
            $rewardIndex = array_search('Вознаграждение', $filteredHeaders);        // Вознаграждение
            $YRIndex = array_search('YR', $filteredHeaders);                        // YR
            


            

            // Общие колонки
            $paymentAmountColumn = $this->getColumnOfIndex($paymentAmountIndex); // Сумма оплаты
            $fareColumn = $this->getColumnOfIndex($fareIndex);                   // Тариф цена
            $summaSboraColumn = $this->getColumnOfIndex($summaSboraIndex);       // Сумма сбора
            $summaEmdColumn = $this->getColumnOfIndex($summaEmdIndex);           // Сумма EMD
            $penaltyColumn = $this->getColumnOfIndex($penaltyIndex);             // Штраф
            $rewardColumn = $this->getColumnOfIndex($rewardIndex);               // Вознаграждение
            $YRColumn = $this->getColumnOfIndex($YRIndex);                       //YR


            // Специфичные колонки по типу
            switch ($type) {
                case 'SALE':

                    $YRColumnSALE = $YRColumn;                        // YR
                    $fareColumnSALE = $fareColumn;                    // Тариф цена
                    $summaSboraColumnSALE = $summaSboraColumn;        // Сумма сбора
                    $rewardColumnSALE = $rewardColumn;                // Вознаграждение
                    break;

                case 'EXCHANGE':

                    $summaSboraColumnEXCHANGE = $summaSboraColumn;    // Сумма сбора
                    $YRColumnEXCHANGE = $YRColumn;                    // YR
                    $fareColumnEXCHANGE = $fareColumn;                // Тариф цена
                    $rewardColumnEXCHANGE = $rewardColumn;            // Вознаграждение
                    break;

                case 'REFUND':

                    $YRColumnREFUND = $YRColumn;                      // YR
                    $fareColumnREFUND = $fareColumn;                  // Тариф цена
                    $summaSboraColumnREFUND = $summaSboraColumn;      // Сумма сбора
                    $rewardColumnREFUND = $rewardColumn;              // Вознаграждение
                    break;

                case 'CANCEL':
                    
                    $penaltyColumnCANCEL = $penaltyColumn;            // Штраф
                    break;

                case 'EMD_EXCHANGE':
                    
                    $fareColumnEMD_EXCHANGE = $fareColumn;            // Тариф цена
                    $rewardColumnEMD_EXCHANGE = $rewardColumn;            // Вознаграждение
                    break;

                case 'EMD_REFUND':
                    
                    $fareColumnEMD_REFUND = $fareColumn;              // Тариф цена
                    break;
            }

            




            $sheet->setCellValue('A'.($lastRow+1), 'Итого:');
            $sheet->setCellValue('A'.($lastRow+2), 'Количество:');


            if ($lastRow == 1) {
                $paymentAmountColumn && $sheet->setCellValue($paymentAmountColumn.($lastRow+1), "0");
                $fareColumn && $sheet->setCellValue($fareColumn.($lastRow+1), "0");
                $summaSboraColumn && $sheet->setCellValue($summaSboraColumn.($lastRow+1), "0");
                $summaEmdColumn && $sheet->setCellValue($summaEmdColumn.($lastRow+1), "0");
                $paymentAmountColumn && $sheet->setCellValue($paymentAmountColumn.($lastRow+2), "0");
                $penaltyColumn && $sheet->setCellValue($penaltyColumn.($lastRow+1), "0");
                $rewardColumn && $sheet->setCellValue($rewardColumn.($lastRow+1), "0");
            }else{

                $paymentAmountColumn && $sheet->setCellValue($paymentAmountColumn.($lastRow+1), "=SUM($paymentAmountColumn".'2:'.$paymentAmountColumn.$lastRow.')');
                $fareColumn && $sheet->setCellValue($fareColumn.($lastRow+1), "=SUM($fareColumn".'2:'.$fareColumn.$lastRow.')'); 
                $summaSboraColumn && $sheet->setCellValue($summaSboraColumn.($lastRow+1), "=SUM($summaSboraColumn".'2:'.$summaSboraColumn.$lastRow.')');
                $summaEmdColumn && $sheet->setCellValue($summaEmdColumn.($lastRow+1), "=SUM($summaEmdColumn".'2:'.$summaEmdColumn.$lastRow.')');
                $paymentAmountColumn && $sheet->setCellValue($paymentAmountColumn.($lastRow+2), "=COUNTA($paymentAmountColumn".'2:'.$paymentAmountColumn.$lastRow.')');
                $penaltyColumn && $sheet->setCellValue($penaltyColumn.($lastRow+1), "=SUM($penaltyColumn".'2:'.$penaltyColumn.$lastRow.')');
                $rewardColumn && $sheet->setCellValue($rewardColumn.($lastRow+1), "=SUM($rewardColumn".'2:'.$rewardColumn.$lastRow.')');
            }
                


            // $uniqueTax = [
            //     'A2', 'AE', 'CN', 'CP', 'CS', 'DE', 'E3', 'F6', 'FX', 
            //     'GE', 'I6', 'IO', 'IR', 'JA', 'JN', 'M6', 'OY', 'RA', 
            //     'T2', 'TP', 'TR', 'UJ', 'UZ', 'YQ', 'YR', 'ZR', 'ZZ'
            // ];

            $uniqueTax = session()->get('uniqueTaxCodes');

            foreach ($uniqueTax as $tax) {
                $taxIndex = array_search($tax, $filteredHeaders);
                $taxColumn = $taxIndex !== false ? Coordinate::stringFromColumnIndex($taxIndex + 1) : false;

                if ($lastRow == 1) {
                    $taxColumn && $sheet->setCellValue($taxColumn . ($lastRow + 1), "0");
                } else {
                    $taxColumn && $sheet->setCellValue($taxColumn . ($lastRow + 1), "=SUM($taxColumn" . '2:' . $taxColumn . $lastRow . ')');
                }
            }




            // Изменение формата ячеек номеров и сумм
            if (isset($groupedData[$type])) {
                foreach ($columnIndexes as $column) {
                    $sheet->getStyle($column.'2:'.$column.$lastRow)
                          ->getNumberFormat()
                          ->setFormatCode(NumberFormat::FORMAT_NUMBER);
                }

                foreach ($columnIndexesSum as $column) {
                    $sheet->getStyle($column.'2:'.$column.$lastRow+1)
                          ->getNumberFormat()
                          ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
                }

            }


            // Автоширина
            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
            
            // Настройка автоматической ширины для всех столбцов в листе
            for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setAutoSize(true);
            }




            $filteredHeaders = $filteredHeadersBase;


        }


        
        
        

        
        

        // =======================  Создание листа TRANSACTIONS ====================== //

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(6);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('TRANSACTIONS');


        $PaysController = new PaysController();
        $TransactionsController = new Transactions();


        $user_id = $params['user_login'];
        $daterange = $params['start_date'].' / '.$params['end_date'];
        $name_table = $params['name_table'];
        $value_table = $params['value_table'];
        $currency = $params['currency'];



        // ======  Получить данные для верхней таблицы

        $uptable_data = $TransactionsController->upTable($user_id, $daterange, $name_table, $value_table, $currency);


        // =======  Получить данные для нижней таблицы

        $data = $TransactionsController->downTable($user_id, $daterange, $name_table, $value_table, $currency); 
        $downtable_data = $data['transactions'];


        

        // Формируем Excel
        $data = $TransactionsController->createExcel($sheet, $uptable_data, $downtable_data);
        $sheet = $data['sheet'];
        $rowNumberUptable = $data['rowNumberUptable'] - 1; // последняя строка верхней табл транзакции

        // 7.1 пункт
        // $transactions = $this->reportGetTransactions($params, $table_name);  // ===== GLOBAL
        $transactionsNew = $this->reportTransMethodPay($uptable_data);


        

        

        // === Самая нижняя таблица

        // Определение номера последней строки с данными
        $lastRow = $sheet->getHighestRow();
        $row = $lastRow + 2;
        
        if (isset($transactionsNew['amounts'])){
            foreach($transactionsNew['amounts'] as $item){
                $sheet->setCellValue('A'.$row, $item['method'].':');
                $sheet->setCellValue('B'.$row, '=SUMIF(I2:I'.$rowNumberUptable.', "'.$item['method'].'", D2:D'.$rowNumberUptable.')');
                $row++;
            }
        }
        

        // // Автоширина
        // $highestColumn = $sheet->getHighestColumn();
        // $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        
        // // Настройка автоматической ширины для всех столбцов в листе
        // for ($col = 1; $col <= $highestColumnIndex; ++$col) {
        //     $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setAutoSize(true);
        // }

    

        //  ============================  Создание листа ACQUIRING ====================== //

        $main_index = 7;

        if ($is_site) { // startif
            
            $main_index = $main_index + 1;
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex(7);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('ACQUIRING');
            $PaysModel = new PaysModel();
            $PaysController = new PaysController();


            // Получить данные 
            $filters = [
                "startDate" => $params['start_date'],
                "endDate" => $params['end_date'],
                "name_payment" => '',
                "currency" => $this->getCurrencyId($params['currency']),
                "status" => 'paid',
            ];
            


            // Получить данные для верхней таблицы
            $uptable_data = $PaysModel->getData($filters, '');


            // Получить данные для нижней таблицы
            $dataArray = $PaysModel->getPaymentSummary($filters, '');
            $downtable_data = $dataArray['summary'];


            // Формируем Excel
            $data = $PaysController->createExcel($sheet, $uptable_data, $downtable_data);
            $sheet = $data['sheet'];
            $rowNumber = $data['rowNumber'];


            // Комиссия итого
            $comission_bank = "=ACQUIRING!J".$rowNumber;



        } // endif









        // =============================  Создание листа MAIN ====================== //

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex($main_index);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('MAIN');

        $sheet->getColumnDimension('C')->setWidth(60);
        $sheet->getColumnDimension('D')->setWidth(20);


        // Шапка дата
        $shapka_date_1 = $params['start_date'];
        $shapka_date_2 = $params['end_date'];



        if (!empty($shapka_date_2) && $shapka_date_1 !== $shapka_date_2) {
            $shapka_date = $shapka_date_1 . ' - ' .$shapka_date_2;
        }else{
            $shapka_date = $shapka_date_1;
        }
        
        
        $sheet->setCellValue('D2', 'Дата: '.$shapka_date); // Шапка дата
        $sheet->getStyle('D2')->getAlignment()->setWrapText(true); // Включаем перенос строки

        // Включаем выравнивание по центру
        $sheet->getStyle('D2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        

        // Шапка Название отчета
        $model = new UserModel();
        $user = $model->find($params['user_login']);

        $shapka = str_replace('<br>', '', $user['user_desc']);
        $sheet->setCellValue('C2', $shapka);
        $sheet->getStyle('C2')->getAlignment()->setWrapText(true); // Включаем перенос строки

        // Включаем выравнивание по центру
        $sheet->getStyle('C2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);


        // пункт 0 баланс
        $res = $this->getFistBalanceNewMethod($params);
        $balance = $res['fistBalance'];


        // $OTCHET = $this->for_summaryTable($params);
        // $balance = $OTCHET['OTCHET']['8'];





        // Добавить 6 пункт Услуги
        $params['fist_balance'] = false;

        // $ServicesModel = new ServicesModel();
        
        // $filters_service = $params;
        // if ($params['value_table'] !== "all") {
        //     $filters_service['value_table'] = $TransactionsController->get_column($params['name_table'], $params['value_table'], '_code', '_id');
        // }

        // $serviceData2 = $ServicesModel->getDataForReport($filters_service);

        $serviceData = $this->reportServices($params);



        $aaa5 = [];
        $formula_services = "";

        $i = 1;
        $base = 27;
        $countService = 0;

        if ($is_airline) {
            $base = 37;
        }


        if (!empty($serviceData['amounts'])) {
            foreach ($serviceData['amounts'] as $item) {
                $aaa5[] = [
                    "number" => "6." . $i, 
                    "name" => $item["service_name"],
                    "formula" => $item["amount"],
                ];

                $number = $base + $i;
                if ($i > 1) {
                    $formula_services .= "+";
                }
                $formula_services .= "D" . $number;

                $i++;

                $base_base = $number;
            }


            $formula_services = '='.$formula_services;
            $countService = count($serviceData['amounts']);

        }else{
            $formula_services = "0";
        }


        



        // Добавить 7 пункт Транзакции

        $aaa = [];
        $formula_trans = "";

        $i = 1;
        $base = $base + $countService + 1;


        if (!empty($transactionsNew['amounts'])) {
            foreach ($transactionsNew['amounts'] as $item) {
                $aaa[] = [
                    "number" => "7." . $i, 
                    "name" => $item["method"],
                    "formula" => '=SUMIF(TRANSACTIONS!I2:I'.$rowNumberUptable.', "'.$item['method'].'", TRANSACTIONS!D2:D'.$rowNumberUptable.')'
                ];

                $number = $base + $i;
                if ($i > 1) {
                    $formula_trans .= "+";
                }
                $formula_trans .= "D" . $number;

                $i++;
            }


            // // Добавить Комиссия банка
            // if ($is_site) {
            //     $aaa[] = ["number" => "7." . $i, "name" => "Комиссия банка", "formula" => "$comission_bank"];
            //     $formula_trans = $formula_trans . "+D" .$number + 1;
            // }
            

            $formula_trans = '='.$formula_trans;



        }else{

            $formula_trans = 0;

            // if ($is_site) {
            //     $formula_trans = "=D".$base + 1;

            //     // Добавить Комиссия банка
            //     $aaa[] = ["number" => "7.1", "name" => "Комиссия банка", "formula" => "$comission_bank"];
            // }
            
        }



        $seven = "D".$base; // 7 пункт




        // Формулы

        $formuls = [];
        
        $formuls['1.1'] =  $fareColumnSALE !== false ? "=SALE!".$fareColumnSALE.$forMainNumLast['SALE'] : 0; 
        $formuls['1.2'] =  $summaSboraColumnSALE !== false ? "=SALE!".$summaSboraColumnSALE.$forMainNumLast['SALE'] : 0;
        $formuls['1.3'] =  $penaltyColumnCANCEL !== false ? "=CANCEL!".$penaltyColumnCANCEL.$forMainNumLast['CANCEL'] : 0;
        $formuls['2.1'] =  $fareColumnEXCHANGE !== false ? "=EXCHANGE!".$fareColumnEXCHANGE.$forMainNumLast['EXCHANGE'] : 0;
        $formuls['2.2'] =  ($fareColumnEMD_EXCHANGE !== false && $summaSboraColumnEXCHANGE !== false) ? "=EMD_EXCHANGE!".$fareColumnEMD_EXCHANGE.$forMainNumLast['EMD_EXCHANGE']. " + EXCHANGE!".$summaSboraColumnEXCHANGE.$forMainNumLast['EXCHANGE'] : 0;
        $formuls['3.1'] =  $fareColumnREFUND !== false ? "=REFUND!".$fareColumnREFUND.$forMainNumLast['REFUND'] : 0;
        $formuls['3.2'] =  $fareColumnEMD_REFUND !== false ? "=EMD_REFUND!".$fareColumnEMD_REFUND.$forMainNumLast['EMD_REFUND'] : 0;
        $formuls['3.3'] =  $summaSboraColumnREFUND !== false ? "=REFUND!".$summaSboraColumnREFUND.$forMainNumLast['REFUND'] : 0;
        $formuls['4.1'] =  $rewardColumnSALE !== false ? "=SALE!".$rewardColumnSALE.$forMainNumLast['SALE'] : 0;
        $formuls['4.2'] =  $rewardColumnEXCHANGE !== false ? "=EXCHANGE!".$rewardColumnEXCHANGE.$forMainNumLast['EXCHANGE'] : 0;
        $formuls['4.3'] =  $rewardColumnREFUND !== false ? "=REFUND!".$rewardColumnREFUND.$forMainNumLast['REFUND'] : 0;
        

        $formuls['1'] = "=D7+D8+D9";
        $formuls['2'] = "=D11+D12";
        $formuls['3'] = "=D14-D15+D16+D17";
        $formuls['4'] = "=D19+D20-D21";
        $formuls['5'] = "=D23+D24-D25-D26";
        $formuls['5.1'] = "=D6";
        $formuls['5.2'] = "=D10";
        $formuls['5.3'] = "=D13";
        $formuls['5.4'] = "=D18";
        $formuls['8'] = "=D5+D22+D27-" . $seven;



        // Для авиакомпании

        $aaa2 = [];
        $aaa3 = [];
        $aaa4 = [];


        if ($is_airline) {


            $formuls['1'] = "=D7+(D10+D11-D12)+D9";
            $formuls['2'] = "=D14+D15+(D17+D18-D19)";
            $formuls['3'] = "=D21-D22+(D25+D26-D27)+D24";
            $formuls['4'] = "=D29+D30-D31";
            $formuls['5'] = "=D33+D34-D35-D36";
            $formuls['5.1'] = "=D6";
            $formuls['5.2'] = "=D13";
            $formuls['5.3'] = "=D20";
            $formuls['5.4'] = "=D28";
            $formuls['8'] = "=D5+D32+D37-" . $seven;


            $formuls['1.4'] =  $YRColumnSALE !== false ? "=SALE!".$YRColumnSALE.$forMainNumLast['SALE'] : 0;
            $formuls['2.2'] =  ($fareColumnEMD_EXCHANGE !== false && $summaSboraColumnEXCHANGE !== false) ? "=EMD_EXCHANGE!".$fareColumnEMD_EXCHANGE.$forMainNumLast['EMD_EXCHANGE'] : 0;
            $formuls['2.3'] =  $summaSboraColumnEXCHANGE !== false ? "=EXCHANGE!".$summaSboraColumnEXCHANGE.$forMainNumLast['EXCHANGE'] : 0;
            $formuls['2.4'] =  $YRColumnEXCHANGE !== false ? "=EXCHANGE!".$YRColumnEXCHANGE.$forMainNumLast['EXCHANGE'] : 0;
            $formuls['3.5'] =  $YRColumnREFUND !== false ? "=REFUND!".$YRColumnREFUND.$forMainNumLast['REFUND'] : 0;
            

            $aaa2[] = ["number" => "1.4", "name" => "Сбор за бронь (YR)", "formula" => $formuls['1.4']];
            $aaa2[] = ["number" => "1.5", "name" => "Аэропортовый сбор (+ сбор за безопасность+ таксы прочие )", "formula" => "=D8-D10"];
            $aaa2[] = ["number" => "1.6", "name" => "Сбор за бронь (YR) не перечисляемый", "formula" => "=D10/7*3"];

            
            $aaa3[] = ["number" => "2.3", "name" => "Сборы за бронь", "formula" => $formuls['2.3']];
            $aaa3[] = ["number" => "2.4", "name" => "Сбор за бронь (YR)", "formula" => $formuls['2.4']];
            $aaa3[] = ["number" => "2.5", "name" => "Аэропортовый сбор (+ сбор за безопасность+ таксы прочие )", "formula" => "=D16-D17"];
            $aaa3[] = ["number" => "2.6", "name" => "Сбор за бронь (YR) не перечисляемый", "formula" => "=D17/7*3"];

            
            $aaa4[] = ["number" => "3.5", "name" => "Сбор за бронь (YR)", "formula" => $formuls['3.5']];
            $aaa4[] = ["number" => "3.6", "name" => "Аэропортовый сбор (+ сбор за безопасность+ таксы прочие )", "formula" => "=D23-D25"];
            $aaa4[] = ["number" => "3.7", "name" => "Сбор за бронь (YR) не перечисляемый", "formula" => "=D25/7*3"];
        }
        


        



        // MAIN

        $MainList = [
            ["number" => "№ п/п", "name" => "Наименование статьи выручки", "formula" => "Всего"],
            ["number" => "", "name" => "Сальдо взаиморасчетов на начало", "formula" => "$balance"],
            ["number" => "1", "name" => "Выручка по реестрам продажи авиабилетов", "formula" => $formuls['1']],
            ["number" => "1.1", "name" => "Тариф а/б", "formula" => $formuls['1.1']],
            ["number" => "1.2", "name" => "Сборы за бронь", "formula" => $formuls['1.2']],
            ["number" => "1.3", "name" => "Сумма аннуляции бланков", "formula" => $formuls['1.3']],
            ...$aaa2,
            ["number" => "2", "name" => "Выручка по реестрам обмена", "formula" => $formuls['2']],
            ["number" => "2.1", "name" => "Доплата по тарифу", "formula" => $formuls['2.1']],
            ["number" => "2.2", "name" => "Штрафы", "formula" => $formuls['2.2']],
            ...$aaa3,
            ["number" => "3", "name" => "Сумма по реестрам возврата", "formula" => $formuls['3']],
            ["number" => "3.1", "name" => "Возврат тарифа", "formula" => $formuls['3.1']],
            ["number" => "3.2", "name" => "Штрафы", "formula" => $formuls['3.2']],
            ["number" => "3.3", "name" => "Сборы (аэропортовые)", "formula" => $formuls['3.3']],
            ["number" => "3.4", "name" => "Сборы (за возврат)", "formula" => "0"],
            ...$aaa4,
            ["number" => "4", "name" => "Комиссионное вознаграждение", "formula" => $formuls['4']],
            ["number" => "4.1", "name" => "По реестрам продажи", "formula" => $formuls['4.1']],
            ["number" => "4.2", "name" => "По реестрам обмена", "formula" => $formuls['4.2']],
            ["number" => "4.3", "name" => "По реестрам возврата", "formula" => $formuls['4.3']],
            ["number" => "5", "name" => "Подлежит перечислению", "formula" => $formuls['5']],
            ["number" => "5.1", "name" => "Выручка по реестрам продажи", "formula" => $formuls['5.1']],
            ["number" => "5.2", "name" => "Выручка по реестрам обмена", "formula" => $formuls['5.2']],
            ["number" => "5.3", "name" => "Сумма по реестрам возврата", "formula" => $formuls['5.3']],
            ["number" => "5.4", "name" => "Комиссионное вознаграждение", "formula" => $formuls['5.4']],
            ["number" => "6", "name" => "Сумма по претензиям и пультам", "formula" => $formula_services],
            ...$aaa5,
            ["number" => "7", "name" => "Перечислено всего", "formula" => $formula_trans],
            ...$aaa,
            ["number" => "8", "name" => "Сальдо взаиморасчетов в конец", "formula" => $formuls['8']]
        ];

        // Добавление данных в лист
        $row = 4; 

        foreach ($MainList as $item) {

            $color = $this->getColorForNumber($item['number']);
            if ($color) {
                $sheet->getStyle('B'.$row.':D'.$row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);
            }

            // $sheet->setCellValue('B' . $row, $item['number']);
            $sheet->setCellValueExplicit('B' . $row, $item['number'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $row, $item['name']);
            $sheet->setCellValue('D' . $row, $item['formula']);


            // Добавление границ для текущей строки
            $sheet->getStyle('B'.$row.':D'.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('000000');


            // Применение формата ячейки для столбца "formula"
            $sheet->getStyle('D'.$row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle('D'.$row)->getAlignment()->setHorizontal('right');

            // Применение формата ячейки для столбца "number"
            $sheet->getStyle('B'.$row)->getAlignment()->setHorizontal('left');
            

            $row++;
        }


        // Установка жирного шрифта для первой и второй строки
        $sheet->getStyle('B2:D4')->getFont()->setBold(true);
        

        // Автоширина
        // $highestColumn = $sheet->getHighestColumn();
        // $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        
        // Настройка автоматической ширины для всех столбцов в листе
        // for ($col = 1; $col <= $highestColumnIndex; ++$col) {
        //     $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setAutoSize(true);
        // }
        

        // end создание листа MAIN ///////////////////////////////////////////////////////////////////


        
        


        // Сохранение Excel файла и подготовка к скачиванию
        $writer = new Xlsx($spreadsheet);
        $username = session()->get('username');
        $shapka_date = $shapka_date_1 . '_' .$shapka_date_2;
        $filename = $username.'_'.$shapka_date.'.xlsx';
        $filePath = WRITEPATH . 'exports/' . $filename;
        $writer->save($filePath);


        // Log
        $action = 'Cформировать отчет в Excel';
        $logger = new LogsController(); 
        $logger->logAction($action);
    
        
        return $this->response->setJSON([
            'status' => true, 
            'downloadUrl' => base_url('download/' . $filename), 
            'params' => $params, 
            'serviceData' => $serviceData,
            
            
        ]);
    }

    public function cancelExport()
    {
        
        $userID = session()->get('user_id');
        $cancelFlagFile = WRITEPATH . 'exports/cancel_flag_' . $userID . '.txt';
        file_put_contents($cancelFlagFile, 'cancel');
        return $this->response->setJSON(['message' => 'Флаг создан: '.$userID]);
    }

    public function download($filename)
    {
        $filePath = WRITEPATH . 'exports/' . $filename;

        // Проверяем, существует ли файл
        if (!file_exists($filePath)) {
            // Вы можете решить, как обрабатывать ситуацию, когда файл не найден
            // Например, показать сообщение об ошибке или перенаправить пользователя
            return $this->response->setStatusCode(404, 'Файл не найден.');
        }

        // Отправляем правильные заголовки, чтобы браузер начал скачивание файла
        return $this->response->download($filePath, null);
    }

    public function get_tax_value($item, $name_tax)
    {
        
        $taxCodes = explode(',', $item['tax_code']);
        $taxAmounts = explode(',', $item['tax_amount_main']);

        // Ищем индекс 
        $taxIndex = array_search($name_tax, $taxCodes);

        // Проверяем, найден ли индекс и существует ли соответствующий элемент в $taxAmounts
        if ($taxIndex !== false && isset($taxAmounts[$taxIndex])) {
            // Если индекс найден
            return floatval($taxAmounts[$taxIndex]);
        }

        // Если индекс не найден 
        return 0.0;
    }

    public function report($params, $balance)
    {

        // экспорт по type
        $is_airline = false;
        $is_site = false;

        if(isset($params['type'])){
            if ($params['type'] == "airline" ) {
                $is_airline = true;
            }elseif ($params['type'] == "site") {
                $is_site = true;
            }
        }


        



        $params['visibleColumns'] = 'Тип билета,Валюта билета,Дата формирования,Время формирования,Тип операции,Тип транзакции,Номер билета,Номер старшего билета,Номер основного билета,Тариф цена,PNR,Дата оформления,Индентификатор продавца,Время оформления,Время оформления UTC,Сумма обмена без EMD,Код оператора,Код агентства,Сумма EMD,Вид оплаты,Сумма оплаты,ФИО,Паспорт,Тип,Гражданство,Маршрут,Перевозчик,Класс,Рейс,Дата полёта,Время полёта,Тариф,Код ППР,Код пульта,Код сбора,Сумма сбора';
        

        $getData = $this->getData($params);
        $data = $getData["data"];
        $filteredHeaders = $getData["filteredHeaders"];


        $filter = session()->get('filter');
        $role = session()->get('role');
        $ids = session()->get('ids');

        

        // Группировка данных по типу транзакции
        $groupedData = $this->groupedData($data);

        

        

        // 0. Сальдо взаиморасчетов на начало

        $OTCHET = [];
        $OTCHET['0'] = $balance;

        // 1. Выручка по реестрам продажи авиабилетов 


        // фильтр по 4 параметрам конструктор

        $table_name = $this->is_table_name($params);


        $db = \Config\Database::connect();
        $builder = $db->table('rewards');
        $results_rewards = $builder->get()->getResult();

        $c_name = $table_name.'_code';
        $builder = $db->table($table_name);
        $results_table = $builder->get()->getResult();
        $builder = $db->table('currencies');
        $results_currencies = $builder->get()->getResult();
        $summa_tariff = 0; // Тариф а/б
        $summa_sbora = 0; // Сборы за бронь
        $summa_za_an = 0; // Сумма аннуляции бланков // кол.строк(штраф * курс)
        $YR_SALE = 0; // Сбор за бронь (YR) 1.4


        if(isset($groupedData['SALE'])){
            foreach ($groupedData['SALE'] as $t) {
                $summa_tariff += $t['tickets_FARE'];
                $summa_sbora += $t['tax_amount'];

                if ($is_airline) {
                    $YR_SALE += $this->get_tax_value($t, 'YR');
                }
                
            }
        }


        if(isset($groupedData['CANCEL'])){
            foreach ($groupedData['CANCEL'] as $t) {

                $penaltyValue = $this->exception($t, $table_name, $results_table, $results_rewards, "penalty");
                
                $currencyValue = 0;
                foreach ($results_currencies as $row) {
                    if ($row->date == $t['tickets_dealdate'] && $row->name == $t['tickets_currency']) {
                        $currencyValue = $row->value;
                        break; 
                    }
                }

                

                $summa_za_an += $currencyValue * $penaltyValue;
            }
        }
        
        $virochka_po_reest_pro = round($summa_tariff, 2) + round($summa_sbora, 2) + round($summa_za_an, 2);
        // $virochka_po_reest_pro = $summa_tariff + $summa_sbora + $summa_za_an;

        $OTCHET['1'] = round($virochka_po_reest_pro, 2);
        $OTCHET['1.1'] = round($summa_tariff, 2);
        $OTCHET['1.2'] = round($summa_sbora, 2);
        $OTCHET['1.3'] = round($summa_za_an, 2);

        if ($is_airline) {
            $OTCHET['1.4'] = round($YR_SALE, 2);
            $OTCHET['1.5'] = $OTCHET['1.2'] - $OTCHET['1.4']; // 1.2 - 1.4
            $OTCHET['1.6'] = $OTCHET['1.4'] / 7*3; // 1.4 / 7*3
        }



        // 2. Выручка по реестрам обмена ==================================

        $doplata_po_tarifu = 0; // Доплата по тарифу
        $tax_amount = 0; // Сумма сбора
        $penalty_v = 0; // Штрафы
        $YR_EXCHANGE = 0; // Сбор за бронь (YR)

        if (isset($groupedData['EXCHANGE'])) {

            foreach ($groupedData['EXCHANGE'] as $t) {
                $doplata_po_tarifu += $t['tickets_FARE'];
                $tax_amount += $t['tax_amount'];

                if ($is_airline) {
                    $YR_EXCHANGE += $this->get_tax_value($t, 'YR');
                }

            }
        }
        
        

        if (isset($groupedData['EMD_EXCHANGE'])) {

            foreach ($groupedData['EMD_EXCHANGE'] as $t) {  
                $penalty_v += $t['tickets_FARE'];
            }
        }


        $penalty_v_v = $penalty_v + $tax_amount;
        $virochka_po_reest_obm = $doplata_po_tarifu + $penalty_v_v; // Выручка по реестрам обмена 

        $OTCHET['2'] = round($virochka_po_reest_obm, 2);
        $OTCHET['2.1'] = round($doplata_po_tarifu, 2);
        $OTCHET['2.2'] = round($penalty_v_v, 2);

        if ($is_airline) {
            $OTCHET['2.2'] = round($penalty_v, 2);
            $OTCHET['2.3'] = round($tax_amount, 2);
            $OTCHET['2.4'] = round($YR_EXCHANGE, 2);
            $OTCHET['2.5'] = $OTCHET['2.3'] - $OTCHET['2.4']; // 2.3 - 2.4
            $OTCHET['2.6'] = $OTCHET['2.4'] / 7*3; // 2.4 / 7*3
        }


        // 3. Сумма по реестрам возврата ==================================

        $vozvrat_tariffa = 0;   // Возврат тарифа
        $penalty_s = 0;         // Штрафы
        $sbori_air = 0;         // Сборы (аэропортовые)
        $sbori_vozrat = 0;      // Сборы (за возврат)
        $YR_REFUND = 0;       // Сбор за бронь (YR)
        // $YRS = [];

        if (isset($groupedData['REFUND'])) {

            foreach ($groupedData['REFUND'] as $t) {
                $vozvrat_tariffa += $t['tickets_FARE'];
                $sbori_air += $t['tax_amount'];

                if ($is_airline) {
                    $YR_REFUND += $this->get_tax_value($t, 'YR');

                }
            }
        }
        
        if (isset($groupedData['EMD_REFUND'])) {

            foreach ($groupedData['EMD_REFUND'] as $t) {
                $penalty_s += $t['tickets_FARE'];
            }
        }

        $virochka_po_reest_voz = $vozvrat_tariffa - $penalty_s + $sbori_air + $sbori_vozrat; // Сумма по реестрам возврата

        $OTCHET['3'] = round($virochka_po_reest_voz, 2);
        $OTCHET['3.1'] = round($vozvrat_tariffa, 2);
        $OTCHET['3.2'] = round($penalty_s, 2);
        $OTCHET['3.3'] = round($sbori_air, 2);
        $OTCHET['3.4'] = round($sbori_vozrat, 2);


        if ($is_airline) {
            $OTCHET['3.5'] = round($YR_REFUND, 2);
            $OTCHET['3.6'] = $OTCHET['3.3'] - $OTCHET['3.5']; // 3.3 - 3.5
            $OTCHET['3.7'] = $OTCHET['3.5'] / 7*3; // 3.5 / 7*3

            // 1.1 + (1.4 + 1.5 - 1.6) + 1.3
            $OTCHET['1'] = $OTCHET['1.1'] + $OTCHET['1.4'] + $OTCHET['1.5'] - $OTCHET['1.6'] + $OTCHET['1.3'];
            // 2.1 + 2.2 + (2.4 + 2.5 + 2.6)
            $OTCHET['2'] = $OTCHET['2.1'] + $OTCHET['2.2'] + $OTCHET['2.4'] + $OTCHET['2.5'] +$OTCHET['2.6']; 
            // 3.1 - 3.2 + (3.5 + 3.6 - 3.7) + 3.4
            $OTCHET['3'] = $OTCHET['3.1'] - $OTCHET['3.2'] + $OTCHET['3.5'] + $OTCHET['3.6'] - $OTCHET['3.7'] + $OTCHET['3.4']; 
        }






        // $OTCHET['groupedData'] = $groupedData;



        // 4. Комиссионное вознаграждение ==================================
       
        $builder = $db->table($table_name);
        $results_table = $builder->get()->getResult();

        $po_reestr_sale = 0; // По реестрам продажи
        $po_reestr_exchange = 0; // По реестрам обмена
        $po_reestr_refund = 0; // По реестрам возврата


        if (isset($groupedData['SALE'])) {
            foreach ($groupedData['SALE'] as $t) {

                $reward = $this->exception($t, $table_name, $results_table, $results_rewards, "reward");
                $po_reestr_sale += $t['tickets_FARE'] * $reward / 100;
                
            }
        }

        if (isset($groupedData['EXCHANGE'])) {
            foreach ($groupedData['EXCHANGE'] as $t) {

                $reward = $this->exception($t, $table_name, $results_table, $results_rewards, "reward");
                $po_reestr_exchange += $t['tickets_FARE'] * $reward / 100;

            }
        }

        if (isset($groupedData['REFUND'])) {

            foreach ($groupedData['REFUND'] as $t) {

                $reward = $this->exception($t, $table_name, $results_table, $results_rewards, "reward");
                $po_reestr_refund += $t['tickets_FARE'] * $reward / 100;

            }
        }

        
        

        
        $OTCHET['4.1'] = round($po_reestr_sale, 2);
        $OTCHET['4.2'] = round($po_reestr_exchange, 2);
        $OTCHET['4.3'] = round($po_reestr_refund, 2);


        $comission_rewards = $OTCHET['4.1'] + $OTCHET['4.2'] - $OTCHET['4.3']; // Комиссионное вознаграждение 

        $OTCHET['4'] = round($comission_rewards, 2);
        $OTCHET['5'] = round($OTCHET['1'] + $OTCHET['2'] - $OTCHET['3'] - $OTCHET['4'], 2); // Подлежит перечислению


        // добавить 6 пункт Услуги
        $OTCHET['6'] = $this->reportServices($params);


        // $OTCHET['6']['total'] = '100'; //delete
        // $OTCHET['6']['amounts'] = ['service_name' => 'dsdd', 'amount' => '100']; //delete




        // 7. Перечислено всего Фильтр по payment_date и 4 параметрам
        $transactions = $this->reportGetTransactions($params, $table_name);
        

        // ================ GLOBAL замена

        // $user_id = $params['user_login'];
        // $daterange = $params['start_date'].' / '.$params['end_date'];
        // $name_table = $params['name_table'];
        // $value_table = $params['value_table'];
        // $currency = $params['currency'];

        // $TransactionsController = new Transactions();
        // $transactions = $TransactionsController->upTable($user_id, $daterange, $name_table, $value_table, $currency);

        //================ GLOBAL




        // разделение по методу оплаты
        $OTCHET['7'] = $this->reportTransMethodPay($transactions);


        // Установить Комиссия Банка
        // if ($is_site) {

            // // Получить данные 
            // $filters = [
            //     "startDate" => $params['start_date'],
            //     "endDate" => $params['end_date'],
            //     "name_payment" => '',
            //     "currency" => $this->getCurrencyId($params['currency']),
            //     "status" => 'paid',
            // ];
            

            // $PaysModel = new PaysModel();
            // $dataArray = $PaysModel->getPaymentSummary($filters, '');
            // $comission_bank = $dataArray['overall_comission_bank_avs'];



            // $OTCHET['7']['amounts'][] =  ['method' => 'Комиссия Банка', 'summa' => $comission_bank];
            // $OTCHET['7']['total'] = $OTCHET['7']['total'] + $comission_bank;

        // }



        // 8. Сальдо взаиморасчетов в конец D20+5+6-7
        $OTCHET['8'] = round($OTCHET['0'] + $OTCHET['5'] + $OTCHET['6']['total'] - $OTCHET['7']['total'], 2);
        // $OTCHET['8'] = round($OTCHET['0'] + $OTCHET['5'] + 2000 - $OTCHET['7']['total'], 2);

        
        if(isset($params['type'])){
            $OTCHET['type'] = $params['type'];
        }
        



        


        // Отправляем ответ
        return $OTCHET;
    }

    public function reportServices($params)
    {   
        $TransactionsController = new Transactions();
        $ServicesModel = new ServicesModel();


        if ($params['value_table'] !== "all") {
            $params['value_table'] = $TransactionsController->get_column($params['name_table'], $params['value_table'], '_code', '_id');
        }
        
        $serviceData = $ServicesModel->getDataForReport($params);

        // разделить транзакции по методу оплаты
        $result = [];
        $totalAmount = 0;
        $i = 0;

        foreach ($serviceData as $item) {
            $result['amounts'][$i] = ['service_name' => $item['service_name'], 'amount' => $item['amount']];
            $totalAmount += $item['amount'];
            $i++;

        }
        
        $result['total'] = $totalAmount;

        return $result;
    }

    public function exception($t, $table_name, $results_table, $results_rewards, $method)
    {
        $reward = null;
        $c_name = $table_name.'_code';
        $c_name2 = $table_name.'_id';

        // по значению _code из таблицы ищет его _id
        $key = array_search($t[$c_name], array_column($results_table, $c_name));
        $id = $results_table[$key]->$c_name2;



        
        // 1. поиск по маршруту и перевозчику
        if (!empty($results_rewards)) {

            // 1.1 поиск конкретного маршрута
            foreach ($results_rewards as $row) {
                if ($row->method === $method && $row->type === 'citycodes' && $row->code === $t['citycodes'] && $row->name === $table_name && $row->value === $id) {

                    // Установка вознаграждения
                    $reward = $row->procent;
                    break;
                }
            }

            // 1.2 поиск по частям маршрута
            if ($reward === null && $t['citycodes'] !== null) {
                // с начала и с конца
                $prefixStartEnd = substr($t['citycodes'], 0, 3) . '/*/' . substr($t['citycodes'], -3);
                // с начала
                $prefixStart = substr($t['citycodes'], 0, 3) . '/*';
                // с конца
                $prefixEnd = '*/' . substr($t['citycodes'], -3);

                // между
                $prefixMiddle = null;
                if (preg_match('/\/([A-Z]{3})\//', $t['citycodes'], $matches)) {
                    $prefixMiddle = '*/'. $matches[1] . '/*';
                }



                // по умолчанию разрешаем поиск по началу и концу
                $enableSearch = true;

                // Проверка на наличие двух слэшей в строке
                if (substr_count($t['citycodes'], '/') >= 2) {
                    // если два или более слэша, то отключаем поиск по началу и концу
                    $enableSearch = false;
                }


                foreach ($results_rewards as $row) {
                    if ($row->method === $method 
                        && $row->type === 'citycodes' 
                        && ($row->code === $prefixStartEnd
                            || ($enableSearch && ($row->code === $prefixStart || $row->code === $prefixEnd))
                            || ($prefixMiddle && $row->code === $prefixMiddle))
                        && $row->name === $table_name 
                        && $row->value === $id) {

                        // Установка вознаграждения
                        $reward = $row->procent;
                        break;
                    }
                }
            }

            // 1.3 поиск конкретного перевозчика
            if ($reward === null && $t['carrier'] !== null) {
                foreach ($results_rewards as $row) {
                    if ($row->method === $method && $row->type === 'carrier' && $row->code === $t['carrier'] && $row->name === $table_name && $row->value === $id) {

                        // Установка вознаграждения
                        $reward = $row->procent;
                        break;
                    }
                }
            }


        }



        // 2. Если не найдено, установим базовую
        if (!empty($results_table)) {

            // Установка вознаграждения
            if ($reward === null) {
                $reward = $results_table[$key]->$method;
            }
        }

        if (empty($reward)) {
            $reward = 0;
        }

        return $reward;
    }

    public function reportGetTransactions($params, $table_name)
    {
        // 7. пункт в отсчете Транзакции
        $db = \Config\Database::connect();
        $builder = $db->table('transactions');

        $model = new UserModel();
        $user_id = $params['user_login'];
        $user = $model->find($user_id);
        $colum_name = $user['filter'].'_id';
        $ids = $user[$colum_name];
        $ids = explode(',', $ids);

        

        if (isset($params['searchBuilder']['criteria'])) {
            foreach ($params['searchBuilder']['criteria'] as $criteria) {
                if (!empty($criteria['value'][0])) {
                    // фильтр по дате
                    if ($criteria['condition'] === '=' && $criteria['data'] === 'Дата формирования') {
                        $builder->where('payment_date', $criteria['value'][0]);
                    }elseif ($criteria['condition'] === 'between' && $criteria['data'] === 'Дата формирования') {
                        $builder->where('payment_date' . " >= ", $criteria['value'][0]);
                        $builder->where('payment_date' . " <= ", $criteria['value'][1]);
                    } elseif ($criteria['data'] === 'Код агентства' || $criteria['data'] === 'Код ППР' || $criteria['data'] === 'Код пульта' || $criteria['data'] === 'Код оператора') {

                        // Фильтр по 4 параметрам констуктор table_name выше известен
                        $c_name1 = $table_name."_id";
                        $c_name2 = $table_name."_code";
                        $ids = explode(',', $user[$c_name1]);
                        $model = $this->getModal($table_name);

                        if (!empty($criteria['value'][0])) {
                            $result = $model->where($c_name2, $criteria['value'][0])->first();
                            $builder->where('name', $table_name)->where('value', $result[$c_name1]);
                        }


                    } elseif ($criteria['data'] === 'Валюта билета'){

                        $builder->where('currency', $criteria['value'][0]);

                    }  
                    
                }
                
            }
        }


        $builder->select('payment_date, receipt_number, amount, currency, note, name, value, method')->where('name', $table_name)->whereIn('value', $ids);
        $transactions = $builder->get()->getResultArray();


        return $transactions;
    }
    
    public function reportTransMethodPay($transactions)
    {
        // разделить транзакции по методу оплаты
        $amounts = [];
        $totalAmount = 0;
        foreach ($transactions as $transaction) {
            $method = $transaction['method'];
            $amount = $transaction['amount'];
            if (!isset($amounts[$method])) {
                $amounts[$method] = 0;
            }
            $amounts[$method] += $amount;
            $totalAmount += $amount;
        }
        // Преобразование в желаемый формат массива
        $result = [];
        $i = 0;
        foreach ($amounts as $method => $summa) {
            $result['amounts'][$i] = ['method' => $method, 'summa' => $summa];
            $i++;
        }
        $result['total'] = $totalAmount;

        return $result;
    }

    public function getModal($table_name)
    {
        switch ($table_name) {
            case 'agency':
                $model = new AgencyModel();
                return $model;
                break;
            case 'stamp':
                $model = new StampModel();
                return $model;
                break;
            case 'tap':
                $model = new TapModel();
                return $model;
                break;
            case 'opr':
                $model = new OprModel();
                return $model;
                break;
        }
    }

    public function getName($table_name)
    {
        switch ($table_name) {
            case 'agency':
                return 'Код агентства';
                break;
            case 'stamp':
                return 'Код ППР';
                break;
            case 'tap':
                return 'Код пульта';
                break;
            case 'opr':
                return 'Код оператора';
                break;
        }
    }

    public function get_balance($table_name, $params)
    {
        $c_name = $table_name.'_id';
        $userId = $params['user_login'];
        $model = new UserModel();
        $user = $model->find($userId);
        $ids = explode(',', $user[$c_name]);

        
        $colum_name_balance = 'balance_'.strtolower($params['currency']);


        $balance = 0;

        $model = $this->getModal($table_name);
        
        if ($params['value_table2']) {
            $results = $model->where($c_name, $params['value_table2'])->first();
            $balance = $results[$colum_name_balance];
        }else{
            $results = $model->whereIn($c_name, $ids)->findAll();
            foreach ($results as $row) {
                    $balance += $row[$colum_name_balance];
            }
        }
        

        return $balance;
    }

    public function balance_date()
    {   
        //дата для начального баланса
        return '2024-01-01';
    }

    public function getBalanceFirst($params)
    {
        $TransactionsController = new Transactions();
        $table_name = $this->is_table_name($params);
    
        
        // минусуем 1 день
        $dateTime = new \DateTime($params['start_date']);
        $dateTime->modify('-1 day');
        

        $date1 = $params['balance_date'];
        $date2 = $dateTime->format('Y-m-d'); // start_date - 1 day


        $params['value_table2'] = $TransactionsController->get_column($params['name_table'], $params['value_table'], '_code', '_id');
        


        if (!empty($params['searchBuilder'])) {
            
            // Фильтруем конструктор
            $params['searchBuilder']['criteria'] = array_filter($params['searchBuilder']['criteria'], function ($criteria) {
                return $criteria['data'] === "Дата формирования" ||
                       $criteria['data'] === "Код агентства" ||
                       $criteria['data'] === "Код ППР" ||
                       $criteria['data'] === "Код пульта" ||
                       $criteria['data'] === "Код оператора" ||
                       $criteria['data'] === "Валюта билета";
            });

            // Изменяем значения
            $newValues = [];
            array_push($newValues, $date1);
            array_push($newValues, $date2);

            foreach ($params['searchBuilder']['criteria'] as $key => &$criteria) {
                if ($criteria['data'] === "Дата формирования") {
                    $criteria['value'] = $newValues;
                    $criteria['value1'] = $date1;
                    $criteria['value2'] = $date2;
                    $criteria['condition'] = 'between';
                    break;
                }
            }
        }


        if ($params['get_balance'] !== false) {
            $balance = $params['get_balance'];
            $params['start_date'] = $date1;
            $params['end_date'] = $date2;
        }else{
            $balance = $this->get_balance($table_name, $params); // получить из бд балансы огранизации
        }
        


        $OTCHET = $this->report($params, $balance);

        $OTCHET['params_fistbalance'] = $params;

        
        return $OTCHET;
    }

    public function summaryTable()
    {
        
        $params = $this->request->getPost();
        $result = $this->for_summaryTable($params);
        

        // Log
        $data = json_encode($params);
        $action = 'Cформировать отчет';
        $logger = new LogsController(); 
        $logger->logAction($action, $data);

        return $this->response->setJSON([
            'OTCHET' => $result['OTCHET'], 
            'response' => $result['response'],

        ]);
    }

    public function getFistBalanceNewMethod($params)
    {
        // 1. получить параметры
        $ReportsModel = new ReportsModel();
        $params2 = $params;
        $params['balance_date'] = $this->balance_date();
        $params['get_balance'] = false;
        $raz = true; // для установки даты для сервиса
        

        // 2. получить первый баланс из бд
        $results = $ReportsModel->get_date_for_balance($params); // 1. получить ближайшую дату 
        $date = $results['nearest_date'];
        
        if ($date) {
            $params['start_date'] = $date;
            $params['get_balance'] = $ReportsModel->getFistBalance($params); // установить в качестве баланса (get_balance)


            // плюс 1 день
            $dateTime = new \DateTime($date);
            $dateTime->modify('+1 day');
            $params['balance_date'] = $dateTime->format('Y-m-d'); // установить в качестве $date1
            $params['start_date'] = $params2['start_date'];


            $raz = false;


        }
        

        

        // 3. получить первый баланс по умолчанию
        $params['fist_balance'] = $raz; // получить баланс, а не отчет
        $OTCHET = $this->getBalanceFirst($params);
        $fistBalance = $OTCHET['8']; // 3. нужно найти баланс (8 пункт) для даты 2024-09-05 (start_date - 1 день)
        $params_fistbalance = $OTCHET['params_fistbalance'];



    


        $response = [
            'fistBalance' => $fistBalance,
            'date' => $date,
            'params_fistbalance' => $params_fistbalance,
            'raz' => $raz,
            'get_balance' => $params['get_balance'],
            'get_date_for_balance' => $results
        ];

        return $response;
    }

    public function for_summaryTable($params)
    {
        // ============= задача ================== //

        // 2024-09-06 - 2024-09-10


        // 1. получить ближайшую дату к (start_date - 1 день)   Итог: 2024-09-03
        // 2. получить баланс из бд для даты (2024-09-03)       Итог: 133620.32 
        // 3. нужно найти баланс (8 пункт) для даты 2024-09-05 (start_date - 1 день):

            // 3.1 формировать отчет на дипазон 2024-09-03 - 2024-09-05 (ближайшая дата - start_date - 1 день)
            // 3.2 для balance_date использовать "2024-09-03" и вместо get_balance "133620.32"
            // 3.3 изменить searchBuilder на 2024-09-03 - 2024-09-05 (ближайшая дата - start_date - 1 день)

        // 4. поставив баланс (8 пункт от даты 2024-09-05) формировать отчет на дипазон (2024-09-06 - 2024-09-10)




        // ============= задача ================== //

        // 1. получить начальный баланс
        $response = $this->getFistBalanceNewMethod($params);
        
        

        // 2. получить отчет
        $params['fist_balance'] = false;
        $OTCHET = $this->report($params, $response['fistBalance']);



        $result = [
            'OTCHET' => $OTCHET,
            'response' => $response,
        ];

        return $result;

    }

    public function if_four_params($criteria)
    {

        $table_name = null;

        foreach ($criteria as $condition) {
            switch ($condition['data']) {
                case 'Код агентства':
                    $table_name = "agency";
                    break;
                case 'Код ППР':
                    $table_name = "stamp";
                    break;
                case 'Код пульта':
                    $table_name = "tap";
                    break;
                case 'Код оператора':
                    $table_name = "opr";
                    break;

            }

        }

        return $table_name;
    }

    public function isParent($var1, $var2)
    {   

        $hierarchy = [
            'stamp' => 'agency',
            'tap' => 'stamp',
            'opr' => 'tap'
        ];

        // Идем по цепочке родителей для var2, пока не достигнем корня иерархии
        while (isset($hierarchy[$var2])) {
            if ($hierarchy[$var2] === $var1) {
                // Нашли var1 в цепочке родителей var2
                return true;
            }
            // Перемещаемся вверх по иерархии
            $var2 = $hierarchy[$var2];
        }
        // Если цепочка родителей закончилась, и мы не нашли var1
        return false;
    }

    public function downTable($params)
    {
                
        $db = \Config\Database::connect();
        $builder = $db->table('tickets');

        // Определение нужных полей
        $ticketsFields = ['tickets.tickets_TRANS_TYPE', 'tickets.tickets_type', 'fops.fops_amount', 'agency.agency_code', 'stamp.stamp_code', 'tap.tap_code', 'opr.opr_code', 'segments.flydate', 'segments.citycodes'];

        // $ticketsFields = ['tickets.tickets_type', 'tickets.tickets_currency', 'tickets.tickets_dealdate', 'tickets.tickets_dealtime', 'tickets.tickets_OPTYPE', 'tickets.tickets_TRANS_TYPE', 'tickets.tickets_BSONUM', 'tickets.tickets_EX_BSONUM', 'tickets.tickets_TO_BSONUM', 'tickets.tickets_FARE', 'tickets.tickets_PNR_LAT', 'tickets.tickets_DEAL_date', 'tickets.tickets_DEAL_disp', 'tickets.tickets_DEAL_time', 'tickets.tickets_DEAL_utc', 'tickets.summa_no_found', 'opr.opr_code', 'agency.agency_code', 'emd.emd_value', 'fops.fops_type', 'fops.fops_amount', 'passengers.fio', 'passengers.pass', 'passengers.pas_type', 'passengers.citizenship', 'segments.citycodes', 'segments.carrier', 'segments.class', 'segments.reis', 'segments.flydate', 'segments.flytime', 'segments.basicfare', 'stamp.stamp_code', 'tap.tap_code', 'taxes.tax_code', 'taxes.tax_amount'];


        // Формирование строки запроса
        $builder->select($ticketsFields);

        // Присоединение таблиц
        $builder->join('fops', 'fops.tickets_id = tickets.tickets_id', 'left');
        $builder->join('opr', 'opr.opr_id = tickets.opr_id', 'left');
        $builder->join('agency', 'agency.agency_id = tickets.agency_id', 'left');
        $builder->join('stamp', 'stamp.stamp_id = tickets.stamp_id', 'left');
        $builder->join('tap', 'tap.tap_id = tickets.tap_id', 'left');
        $builder->join('segments', 'segments.tickets_id = tickets.tickets_id', 'left');

        // Применяем фильтр
        $builder = $this->filter_tickets($params, $builder);


        // Конструктор
        if (isset($params['searchBuilder']['criteria'])) {
            $criteria = $params['searchBuilder']['criteria'];
            $logic = $params['searchBuilder']['logic'] ?? 'AND';
            $builder = $this->Сonstructor($builder, $criteria, $logic);
        }
        

        // для Dashboard
        if ($params['page'] !== "operations") {
            if(isset($params['name_table'])){
                $name_colum = $params['name_table'].".".$params['name_table']."_code";

                if ($params['name_table'] !== "all") {
                    if ($params['value_table'] !== "all") {
                        $builder->where($name_colum, $params['value_table']);
                    }else{
                        $gettable = new Transactions(); 
                        $tableDatas = $gettable->gettable($params['name_table'], $params['user_login']);
                        $colum = $params['name_table'].'_code';
                        $values = [];
                        foreach($tableDatas as $item){
                            $values[] = $item[$colum];
                        }

                        $builder->whereIn($name_colum, $values);
                    }
                }
            }
        }
        
        

        // для Flightload
        if ($params['page'] === "flightload") {
            // Фильтровать

            if ($params['startDate'] !== '' && $params['endDate'] !== '') {
                $builder->where('tickets_dealdate' . ' >=', $params['startDate']);
                $builder->where('tickets_dealdate' . ' <=', $params['endDate']);
            }
            if ($params['flydate'] !== '') {
                $builder->like('flydate', $params['flydate'], 'both');
            }
            if ($params['citycodes'] !== '') {
                $builder->like('citycodes', $params['citycodes'], 'both');
            }
            if ($params['flytime'] !== '') {
                $builder->like('flytime', $params['flytime'], 'both');
            }
        }


        $query = $builder->get();
        $data = $query->getResultArray();

        // Отправляем ответ
        return $data;
    }

    public function filter_tickets($params, $builder)
    {

        $user_login = $params['user_login'];
        $name_table = $params['name_table'];

        if ($name_table === "all") {
            $user_login = session()->get('user_id');
            $name_table = session()->get('filter');
        }
        
        $model = new UserModel();
        $user = $model->where('user_id', $user_login)->first();
        $c_name = $name_table.'_id';
        $ids = $user[$c_name];
        $ids = explode(",", $ids);
        $ids = array_map('intval', $ids);
        $colum_name = "tickets.".$c_name;


        $role = $user['role'];
        if($role !== "superadmin"){
            $builder->whereIn($colum_name, $ids);
        }


        return $builder;
    }

    public function settings() 
    {

        $id = session()->get('user_id');
        $filter = $this->request->getPost('filter');
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        $method = $this->request->getPost('method');

        // Сохранить данные пользователя
        $model = new UserModel();
        $data = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'filter' => $filter,
        ];

        $model->update($id, $data);



        ///////////////////////////////////////////////////////////////обновить данные сессии
        
        $user = $model->where('user_id', $id)->first();
        $filter = $user['filter'];
        $role = session()->get('role');

        if($role == "superadmin"){
            $colum_name = 'agency_id';
            $ids = "0";
        }else{
            //логика
            $colum_name = $filter.'_id';
            $ids = $user[$colum_name];
            $ids = explode(",", $ids);
            $ids = array_map('intval', $ids);

        }

        $session = \Config\Services::session();
        // Устанавливаем данные в сессию
        $session->set([
            'filter' => $filter,
            'colum_name' => $colum_name,
            'ids' => $ids,
        ]);
        ////////////////////////////////////////////////////////////////

        if ($method === "Сохранить") {
            $action = 'Сохранить настройку в Операции';
        }else{
            $action = 'Сбросить настройку в Операции';
        }

        // Log
        $logger = new LogsController(); 
        $logger->logAction($action);
        
        // Возвращаем ответ
        return $this->response->setJSON(['success' => true, 'data' => $data]);
    }
  
    public function calculateSummary()
    {   

        $params = $this->request->getPost();
        $transactions = $this->downTable($params);
        // $column_name = $params['colum_name'];
        // $column_name = str_replace('_id', '_code', $column_name);

        $name_table = $params['name_table'];
        if ($name_table === "all") {
            $name_table = session()->get('filter');
        }
        $column_name = $name_table.'_code';


        $summary = [
            'EMD' => [
                'SALE' => ['amount' => 0, 'count' => 0],
                'EXCHANGE' => ['amount' => 0, 'count' => 0],
                'REFUND' => ['amount' => 0, 'count' => 0],
                'CANCEL' => ['amount' => 0, 'count' => 0],
            ],
            'ETICKET' => [
                'SALE' => ['amount' => 0, 'count' => 0, 'data' => []],
                'EXCHANGE' => ['amount' => 0, 'count' => 0, 'data' => []],
                'REFUND' => ['amount' => 0, 'count' => 0, 'data' => []],
                'CANCEL' => ['amount' => 0, 'count' => 0, 'data' => []],
            ]
        ];

        $operations = [];
        $agencyCodes = [];

        $TRANS_TYPES = ['ETICKET', 'EMD'];
        $types = ['SALE', 'EXCHANGE', 'REFUND', 'CANCEL'];

        
        foreach ($transactions as $i => $transaction) {
            $type = $transaction['tickets_TRANS_TYPE'];
            $ticketType = $transaction['tickets_type'];
            $amount = $this->intVal($transaction['fops_amount']);

            if (isset($summary[$ticketType][$type])) {
                $summary[$ticketType][$type]['amount'] += $amount;
                $summary[$ticketType][$type]['count'] += 1;
            }


            // уникальные значения
            if (!in_array($transaction[$column_name], $agencyCodes)) {
                $agencyCodes[] = $transaction[$column_name];
            }

            
            
            foreach($TRANS_TYPES as $a){
                foreach($types as $b){
                    if ( $ticketType === $a &&  $type === $b) {
                        // по уникальным значениям
                        foreach($agencyCodes as $u){
                            $amount = 0;
                            $count = 0;


                            // Создаем массив, если его нет
                            if (!isset($operations[$a][$b][$u]['amount'])) {
                                $operations[$a][$b][$u] = ['name' => $u, 'amount' => 0, 'count' => 0]; 
                            }


                            if ($transaction[$column_name] === $u) {
                                $operations[$a][$b][$u]['amount'] += $transaction['fops_amount'];
                                $operations[$a][$b][$u]['count'] += 1;
                            }

                        }
                    }

                }
            }
        }

        $data = $this->formatSummaryData($summary);

        return $this->response->setJSON(['data' => $data, 'params' => $params, 'operations' => $operations, 'column_name' => $column_name]);
    }

    private function intVal($value)
    {
        return is_numeric($value) ? (float) preg_replace('/[^\d.]/', '', $value) : 0;
    }

    private function roundCents($value)
    {
        return round($value, 2);
    }

    private function formatMoney($value)
    {
        return number_format($value, 2, ',', ' ');
    }

    private function formatMoney2($value)
    {
        return number_format($value, 0, '.', ' ');
    }

    private function formatSummaryData($summary)
    {
        
        
        $totalAmount1 = $summary['ETICKET']['SALE']['amount'] + $summary['ETICKET']['EXCHANGE']['amount'] - $summary['ETICKET']['REFUND']['amount'] ;
        $totalAmount2 = $summary['EMD']['SALE']['amount'] + $summary['EMD']['EXCHANGE']['amount'];
        

        $totalAmount = $totalAmount1 + $totalAmount2;

        return [
            'ETICKET' => [
                'SALE' => $this->formatMoney2($summary['ETICKET']['SALE']['count']) . ' / ' . $this->formatMoney($this->roundCents($summary['ETICKET']['SALE']['amount'])),
                'EXCHANGE' => $this->formatMoney2($summary['ETICKET']['EXCHANGE']['count']) . ' / ' . $this->formatMoney($this->roundCents($summary['ETICKET']['EXCHANGE']['amount'])),
                'REFUND' => $this->formatMoney2($summary['ETICKET']['REFUND']['count']) . ' / ' . $this->formatMoney($this->roundCents($summary['ETICKET']['REFUND']['amount'])),
                'CANCEL' => $this->formatMoney2($summary['ETICKET']['CANCEL']['count']) . ' / ' . $this->formatMoney($this->roundCents($summary['ETICKET']['CANCEL']['amount'])),
                'count' => $this->formatMoney2(array_sum(array_column($summary['ETICKET'], 'count'))),
                'amount' => $this->formatMoney($totalAmount1),
                
            ],
            'EMD' => [
                'SALE' => $this->formatMoney2($summary['EMD']['SALE']['count']) . ' / ' . $this->formatMoney($this->roundCents($summary['EMD']['SALE']['amount'])),
                'EXCHANGE' => $this->formatMoney2($summary['EMD']['EXCHANGE']['count']) . ' / ' . $this->formatMoney($this->roundCents($summary['EMD']['EXCHANGE']['amount'])),
                'REFUND' => $this->formatMoney2($summary['EMD']['REFUND']['count']) . ' / ' . $this->formatMoney($this->roundCents($summary['EMD']['REFUND']['amount'])),
                'CANCEL' => $this->formatMoney2($summary['EMD']['CANCEL']['count']) . ' / ' . $this->formatMoney($this->roundCents($summary['EMD']['CANCEL']['amount'])),
                'count' => $this->formatMoney2(array_sum(array_column($summary['EMD'], 'count'))),
                'amount' => $this->formatMoney($totalAmount2),
                ],
                'totalAmount' => $this->formatMoney($totalAmount),
                'totalCount' => $this->formatMoney2(array_sum(array_column($summary['ETICKET'], 'count')) + array_sum(array_column($summary['EMD'], 'count')))
            ];
    }

    public function allExport()
    {
        // получить параметры из пост запроса
        $params = $this->request->getPost();

        $getDataResult = $this->getData($params);
        $rawData = $getDataResult['data'];
        $filteredHeaders = $getDataResult['filteredHeaders']; // Эти заголовки будут использоваться для записи в Excel

        // Определяем имя таблицы для правил вознаграждений/штрафов
        $table_name = $this->is_table_name($params);

        // Группируем сырые данные так, как это ожидает addNewElements
        $groupedRawData = $this->groupedData($rawData);

        // Обогащаем данные вычисленными полями
        $enrichedGroupedData = $this->addNewElements($table_name, $groupedRawData, $filteredHeaders);

        // "Сплющиваем" сгруппированные и обогащенные данные обратно в один массив строк
        $data = [];
        if (!empty($enrichedGroupedData)) {
            foreach ($enrichedGroupedData as $type => $rows) {
                if (is_array($rows)) {
                    foreach ($rows as $row) {
                        $data[] = $row;
                    }
                }
            }
        } else {
            $data = $rawData; // Если исходных данных не было или после обработки ничего не осталось
        }

        // Определяем ключи для номеров билетов и добавляем префикс-апостроф
        $ticketNumberKeysMap = [
            'Номер билета' => 'tickets.tickets_BSONUM',
            'Номер старшего билета' => 'tickets.tickets_EX_BSONUM', // Уточни, если ключ другой
            'Номер основного билета' => 'tickets.tickets_TO_BSONUM', // Уточни, если ключ другой
        ];

        if (!empty($data)) {
            foreach ($ticketNumberKeysMap as $headerName => $dataKey) {
                if (in_array($headerName, $filteredHeaders) && isset($data[0][$dataKey])) {
                    foreach ($data as &$row) {
                        if (isset($row[$dataKey]) && $row[$dataKey] !== null && $row[$dataKey] !== '') {
                            $row[$dataKey] = "'" . strval($row[$dataKey]);
                        }
                    }
                    unset($row); // Разрываем ссылку на последний элемент
                }
            }
        }

        // создать spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        // добавить заголовки
        $sheet->fromArray($filteredHeaders, null, 'A1');

        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:' . $sheet->getHighestDataColumn() . '1')->applyFromArray($headerStyle);

        // добавить данных
        $sheet->fromArray($data, null, 'A2');

        // получаем номер последней строки и колонки с данными
        $lastRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestDataColumn();

        // Применяем форматирование к нужным колонкам
        if ($lastRow >= 2 && !empty($filteredHeaders)) {
            $columnsToFormat = [
                'Штраф' => NumberFormat::FORMAT_NUMBER_00,
                'Сумма штрафа' => NumberFormat::FORMAT_NUMBER_00,
                'Курс валюты' => NumberFormat::FORMAT_NUMBER_00,
                'Вознаграждение' => NumberFormat::FORMAT_NUMBER_00,
                'Процент вознаграждение' => NumberFormat::FORMAT_NUMBER_00,
                'Тариф цена' => NumberFormat::FORMAT_NUMBER_00,
                'Сумма EMD' => NumberFormat::FORMAT_NUMBER_00,
                'Сумма оплаты' => NumberFormat::FORMAT_NUMBER_00,
            ];

            $uniqueTaxCodesFromSession = session()->get('uniqueTaxCodes');
            if (is_array($uniqueTaxCodesFromSession)) {
                foreach ($uniqueTaxCodesFromSession as $taxCode) {
                    $columnsToFormat[$taxCode] = NumberFormat::FORMAT_NUMBER_00;
                }
            }

            foreach ($filteredHeaders as $colIndex => $headerName) {
                if (isset($columnsToFormat[$headerName])) {
                    $columnLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
                    $sheet->getStyle($columnLetter . '2:' . $columnLetter . $lastRow)
                          ->getNumberFormat()
                          ->setFormatCode($columnsToFormat[$headerName]);
                } elseif (in_array($headerName, ['Номер билета', 'Номер старшего билета', 'Номер основного билета'])) {
                    $columnLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
                    $sheet->getStyle($columnLetter . '1:' . $columnLetter . $lastRow) // Применяем формат ко всему столбцу, включая заголовок, если нужно, или с '2' для данных
                          ->getNumberFormat()
                          ->setFormatCode('0'); // Устанавливаем формат "целое число"
                }
            }
        }

        $highestColumn = $sheet->getHighestDataColumn(); // Получаем последнюю колонку (например, 'R')
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        for ($colIndex = 1; $colIndex <= $highestColumnIndex; ++$colIndex) {
            $colString = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->getColumnDimension($colString)->setAutoSize(true);
        }

        // Установка автофильтра для всех столбцов с данными (от A1 до последней колонки и последней строки)
        // Проверяем, есть ли данные для фильтрации (хотя бы заголовки)
        if ($lastRow >= 1 && !empty($highestColumn)) {
            $sheet->setAutoFilter('A1:' . $highestColumn . $lastRow);
        }

        // генерация file
        $writer = new Xlsx($spreadsheet);
        $filename = 'export_' . date('Ymd_His') . '.xlsx';
        $filePath = WRITEPATH . 'exports/' . $filename;
        $writer->save($filePath);

        return $this->response->setJSON([
            'status' => true,
            'downloadUrl' => base_url('download/' . $filename), 
            'params' => $params,
            'data' => $data,
        ]);
    }
    
    
}
