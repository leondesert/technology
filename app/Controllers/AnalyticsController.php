<?php

namespace App\Controllers;

use App\Models\TransactionsModel;
use App\Models\UserModel;
use App\Models\AgencyModel;
use App\Models\StampModel;
use App\Models\TapModel;
use App\Models\OprModel;
use App\Models\ReshareModel;
use App\Controllers\LogsController;
use App\Controllers\Transactions;
use App\Controllers\Dashboard;
use App\Controllers\BigExportController;

class AnalyticsController extends BaseController
{   
    
    public function index()
    {
        // Проверяем, авторизован ли пользователь
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login'); 
        }


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

        $labels = [1,2,3];
        $amounts = [1,2,3];





        $ProfileController = new Profile();



        $data = [
            'users' => $users,
            'currencies' => $currencies,
            'dates' => $dates,
            'user_id' => $user_id,
            'username' => $username,
            'role' => $role,
            'labels' => $labels,
            'amounts' => $amounts,
            'filter_values' => $ProfileController->get_filter_values()          
        
        ];


        // Log
        $action = 'Вход в страницу Аналитика';
        $logger = new LogsController(); 
        $logger->logAction($action);


        return view('analytics/index', $data);
    }

    public function getDataTable()
    {
        // Получаем параметры из POST-запроса
        $name_table = $this->request->getPost('name_table');
        $value_table = $this->request->getPost('value_table');
        $currency = $this->request->getPost('currency');
        $user_login = $this->request->getPost('user_login');
        $startDate = $this->request->getPost('startDate');
        $endDate = $this->request->getPost('endDate');
        $daterange = $startDate." / ".$endDate;


        $getDaysArray = $this->getDaysArray($daterange);
        $dohod = [];
        $sale = [];
        $table_data = [];
        $totalAmount = 0;
        foreach($getDaysArray as $key => $day){

            $data = $this->report($day, $currency, $name_table, $value_table, $user_login);
            $dohod[] = $data['reward'];
            $sale[] = $data['1'];

            $table_data[$key]['date'] = $day;
            $table_data[$key]['dohod'] = round($data['reward'], 2);
            $table_data[$key]['sale'] = round($data['1'], 2);
            $table_data[$key]['currency'] = $currency;
            $table_data[$key]['name_table'] = $this->get_name_table($name_table);

            $totalAmount += $data['reward'];

            if ($value_table === "all") {
                $table_data[$key]['value_table'] = "Все";
            }else{
                $table_data[$key]['value_table'] = $value_table;
            }
        }


        $data = [
            'table_data' => $table_data,
            'labels' => $getDaysArray,
            'dohod' => $dohod,
            'sale' => $sale,
            'totalAmount' => round($totalAmount, 2),
            'currency' => $currency
        ];

        // Log
        $action = 'Отправка запроса Доход';
        $logger = new LogsController(); 
        $logger->logAction($action, json_encode($data));

        return $this->response->setJSON($data);
    }

    public function get_name_table($table_name)
    {
        switch ($table_name) {
            case 'agency':
                return "Агенство";
                break;
            case 'stamp':
                return "ППР";
                break;
            case 'tap':
                return "Пульт";
                break;
            case 'opr':
                return "Оператор";
                break;
            case 'reshare':
                return "Пере-раздача";
                break;
            default:
                return "Все";
        }
    }

    public function getData($searchBuilder)
    {


        // подключение к бд
        $db = \Config\Database::connect();
        $builder = $db->table('tickets');

        // Определение нужных полей
        $ticketsFields = ['tickets.tickets_type', 'tickets.tickets_currency', 'tickets.tickets_dealdate', 'tickets.tickets_dealtime', 'tickets.tickets_OPTYPE', 'tickets.tickets_TRANS_TYPE', 'tickets.tickets_BSONUM', 'tickets.tickets_EX_BSONUM', 'tickets.tickets_TO_BSONUM', 'tickets.tickets_FARE', 'tickets.tickets_PNR_LAT', 'tickets.tickets_DEAL_date', 'tickets.tickets_DEAL_disp', 'tickets.tickets_DEAL_time', 'tickets.tickets_DEAL_utc', 'tickets.summa_no_found', 'opr.opr_code', 'agency.agency_code', 'emd.emd_value', 'fops.fops_type', 'fops.fops_amount', 'passengers.fio', 'passengers.pass', 'passengers.pas_type', 'passengers.citizenship', 'segments.citycodes', 'segments.carrier', 'segments.class', 'segments.reis', 'segments.flydate', 'segments.flytime', 'segments.basicfare', 'stamp.stamp_code', 'tap.tap_code', 'taxes.tax_code', 'taxes.tax_amount', 'tickets.penalty', 'tickets.reward', '._code'];

        $ticketsFields2 = ['tickets.tickets_type', 'tickets.tickets_currency', 'tickets.tickets_dealdate', 'tickets.tickets_OPTYPE', 'tickets.tickets_TRANS_TYPE', 'tickets.tickets_FARE', 'segments.citycodes', 'opr.opr_code', 'agency.agency_code', 'stamp.stamp_code', 'tap.tap_code', 'taxes.tax_amount', 'segments.carrier', '._code'];

        
        // Формирование строки запроса
        $builder->select($ticketsFields2);

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
        $builder->join('reshare', 'reshare.reshare_id = tickets.reshare_id', 'left');
        


        // Применяем фильтр
        // $ids = session()->get('ids');
        // $c_name = session()->get('colum_name');

        $userModel = new UserModel();
        $user = $userModel->where('user_id', $searchBuilder['user_login'])->first();
        $filter = $user['filter'];
        $c_name = $filter.'_id';
        $ids = $user[$c_name];
        $ids = explode(",", $ids);
        $ids = array_map('intval', $ids);
        $colum_name = "tickets.".$c_name;

        $role = session()->get('role');
        if($role !== "superadmin"){
            $builder->whereIn($colum_name, $ids);
        }


        //Конструктор
        $builder->where('tickets.tickets_currency', $searchBuilder['currency']);
        $builder->where('tickets.tickets_dealdate', $searchBuilder['day']);

        $name_colum = $searchBuilder['name_table'].".".$searchBuilder['name_table']."_code";


        if ($searchBuilder['name_table'] !== "all") {
            if ($searchBuilder['value_table'] !== "all") {
                $builder->where($name_colum, $searchBuilder['value_table']);
            }else{
                $gettable = new Transactions(); 
                $tableDatas = $gettable->gettable($searchBuilder['name_table'], $searchBuilder['user_login']);
                $colum = $searchBuilder['name_table'].'_code';
                $values = [];
                foreach($tableDatas as $item){
                    $values[] = $item[$colum];
                }

                $builder->whereIn($name_colum, $values);
            }
        }
        

        $query = $builder->get();
        $results = $query->getResultArray();

        return $results;
    }
   
    public function report($day, $currency, $name_table, $value_table, $user_login)
    {

        
        $searchBuilder['day'] = $day;
        $searchBuilder['currency'] = $currency;
        $searchBuilder['value_table'] = $value_table;
        $searchBuilder['name_table'] = $name_table;
        $searchBuilder['user_login'] = $user_login;
        

        //получить данные
        $data = $this->getData($searchBuilder);
        $DATA['data'] = $data;

        $filter = session()->get('filter');
        $role = session()->get('role');
        $ids = session()->get('ids');

        
        

        // Группировка данных
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



        //=========================================================================================================


        

        
        // фильтр по 4 параметрам .........................................
        $userModel = new UserModel();
        $user = $userModel->where('user_id', $user_login)->first();
        $filter = $user['filter'];

        $table_name = null;
        if ($name_table !== "all") {
            $table_name = $name_table;
        }else{
            $table_name = $filter;
        }

        $BigExportController = new BigExportController();
        $parent = $BigExportController->isParent($table_name, $filter);

        if ($parent === true) {
            $table_name = $filter;
        }


        //////////////////////////////////////////////////////////////////

        // 1. Выручка по реестрам продажи авиабилетов
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

        if(isset($groupedData['SALE'])){
            foreach ($groupedData['SALE'] as $t) {
                $summa_tariff += $t['tickets_FARE'];
                $summa_sbora += $t['tax_amount'];
            }
        }


        if(isset($groupedData['CANCEL'])){
            foreach ($groupedData['CANCEL'] as $t) {

                $penaltyValue = $BigExportController->exception($t, $table_name, $results_table, $results_rewards, "penalty");


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
        
        $virochka_po_reest_pro = $summa_tariff + $summa_sbora + $summa_za_an;
        $OTCHET['1'] = round($virochka_po_reest_pro, 2);
        



        // 4. Комиссионное вознаграждение ==================================
        

        

        $po_reestr_sale = 0; // По реестрам продажи
        $po_reestr_exchange = 0; // По реестрам обмена
        $po_reestr_refund = 0; // По реестрам возврата


        if (isset($groupedData['SALE'])) {
            foreach ($groupedData['SALE'] as $t) {

                $reward = $BigExportController->exception($t, $table_name, $results_table, $results_rewards, "reward");
                $po_reestr_sale += $t['tickets_FARE'] * $reward / 100;
                
            }
        }

        if (isset($groupedData['EXCHANGE'])) {
            foreach ($groupedData['EXCHANGE'] as $t) {

                $reward = $BigExportController->exception($t, $table_name, $results_table, $results_rewards, "reward");
                $po_reestr_exchange += $t['tickets_FARE'] * $reward / 100;

            }
        }

        if (isset($groupedData['REFUND'])) {

            foreach ($groupedData['REFUND'] as $t) {

                $reward = $BigExportController->exception($t, $table_name, $results_table, $results_rewards, "reward");
                $po_reestr_refund += $t['tickets_FARE'] * $reward / 100;

            }
        }

        
        $comission_rewards = $po_reestr_sale + $po_reestr_exchange - $po_reestr_refund; // Комиссионное вознаграждение 

        $OTCHET['reward'] = round($comission_rewards, 2);

        
        
        // Отправляем ответ
        return $OTCHET;
    }

    public function getDaysArray($daterange)
    {
        // Разбиваем строку диапазона на начальную и конечную даты
        $dates = explode(' / ', $daterange);
        $start_date = strtotime($dates[0]);
        $end_date = strtotime($dates[1]);

        $days = array();

        // Определяем разницу в днях между начальной и конечной датами
        $days_diff = floor(($end_date - $start_date) / (60 * 60 * 24));

        // Определяем интервал в зависимости от разницы в днях
        if ($days_diff > 31) {
            // Если разница более 31 дня, интервал - 10 дней
            $interval = '+10 days';
        } elseif ($days_diff > 365) {
            // Если разница более года, интервал - 1 месяц
            $interval = '+1 month';
        } else {
            // Иначе интервал - 1 день
            $interval = '+1 day';
        }

        // Проходим по каждой дате в диапазоне с определенным интервалом и добавляем ее в массив $days
        $current_date = $start_date;
        while ($current_date <= $end_date) {
            $days[] = date('Y-m-d', $current_date);
            $current_date = strtotime($interval, $current_date);
        }

        return $days;
    }
    

}
