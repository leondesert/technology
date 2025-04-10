<?php


namespace App\Models;

use CodeIgniter\Model;

class PaysModel extends Model
{
    protected $table      = 'transaction';
    protected $primaryKey = 'id';

    // Укажите имя соединения с базой данных
    protected $DBGroup    = 'remote';

    // Укажите, какие поля можно заполнять
    protected $allowedFields = [

        'description',       // Номер заказа в Немо
        'orderNumber',       // Номер платежа в Немо
        'amount',            // Сумма
        'currency',          // Валюта
        'transaction.id',    // Номер заказа на сайте
        'tranDateTime',      // Дата создания
        'orderStatus',       // Статус Транзакции
        'name_payment',      // Метод оплаты
        'unic_order_id',     // Номер заказа в эквайринге
        'status',            // Статус в банке
        'datetime',          // Дата платежа
        'jsonParams',        // Номер телефона
        
    ];


    public function countFiltered($searchValue, $filters)
    {
        $builder = $this->builder();
        $this->joinTables($builder);
        if ($searchValue != '') {
            $this->applySearchFilter($builder, $searchValue);
        }

        // Фильтровать
        $builder = $this->filters($builder, $filters);


        return $builder->countAllResults();
    }

    private function name_payments()
    {
        $name_payments = [
            ["name" => "Алиф (инвойс)", "value" => "alif_mobi", "bank" => "alif", "comission" => "1", "reward" => "0"],
            ["name" => "Душанбе Сити (кошелек)", "value" => "dc_wallet", "bank" => "dc", "comission" => "0", "reward" => "0"],
            ["name" => "Эсхата Онлайн", "value" => "eskhata_online", "bank" => "eskhata", "comission" => "1", "reward" => "0"],
            ["name" => "IBT (виза)", "value" => "ibt_visa", "bank" => "ibt", "comission" => "2.3", "reward" => "1.3"],
            ["name" => "IBT (корти милли)", "value" => "ibt_km", "bank" => "ibt", "comission" => "1", "reward" => "0"],
            ["name" => "Душанбе Сити (карта мир)", "value" => "dc_visa", "bank" => "dc", "comission" => "0", "reward" => "0"],
            ["name" => "Душанбе Сити (мир)", "value" => "dc_mir_km", "bank" => "dc", "comission" => "0", "reward" => "0"],
            ["name" => "Алиф (эквайринг)", "value" => "alif_km", "bank" => "alif", "comission" => "1", "reward" => "0"],
            ["name" => "Душанбе Сити (корти милли)", "value" => "dc_km", "bank" => "dc", "comission" => "0", "reward" => "0"],
        ];

        return $name_payments;
    }

    private function findComissionByValue($name_payments, $name_payment, $name_value) 
    {
        foreach ($name_payments as $payment) {
            if ($payment['value'] === $name_payment) {
                return $payment[$name_value];
            }
        }

        return 0; // Если значение не найдено
    }

    public function getFilteredData($start, $length, $searchValue, $filters, $order = [], $columns = [])
    {
        $builder = $this->builder();

        $this->joinTables($builder);

        $builder->select($this->allowedFields);
        if ($searchValue != '') {
            $this->applySearchFilter($builder, $searchValue);
        }
        
        // Фильтровать
        $builder = $this->filters($builder, $filters);


        if ($order) {
            $columnIndex = $order[0]['column'];
            $columnName = $columns[$columnIndex]['data'];
            $columnSortOrder = $order[0]['dir'];
            $builder->orderBy($columnName, $columnSortOrder);
        } else {
            $builder->orderBy('datetime', 'DESC');
        }



        if ($length < 0) {
            $results = $builder->get()->getResultArray();
        }else{
            $results = $builder->get($length, $start)->getResultArray();
        }
        



        // добавить комиссии 
        $results = $this->add_colums_new($results);
        


        return $results;
    }

    private function add_colums_new($results)
    {
        $name_payments = $this->name_payments();


        foreach ($results as &$result) {

            $comisson = $this->findComissionByValue($name_payments, $result['name_payment'], 'comission');
            $reward = $this->findComissionByValue($name_payments, $result['name_payment'], 'reward');

            // $newValue = $oldValue + ($oldValue * ($percentToAdd / 100));

            $result['summa_with_comission'] = $result['amount'] + ($result['amount'] * ($reward / 100));
            $result['summa_out_comission'] = $result['summa_with_comission'] - ($result['summa_with_comission'] * ($comisson / 100));


            $result['comission'] = $result['summa_with_comission'] - $result['summa_out_comission']; // Комиссия
            $result['comission_bank_client'] = $result['summa_with_comission'] - $result['amount'];  // Комиссия банка (клиент)
            $result['comission_bank_avs'] = $result['comission'] - $result['comission_bank_client']; // Комиссия банка (АВС)
            

        }


        return $results;
    }

    public function getData($filters, $searchValue)
    {
        $builder = $this->builder();

        $this->joinTables($builder);

        $builder->select($this->allowedFields);
        if ($searchValue != '') {
            $this->applySearchFilter($builder, $searchValue);
        }
        
        // Фильтровать
        $builder = $this->filters($builder, $filters);


        
        $results = $builder->get()->getResultArray();


        // добавить комиссии 
        $results = $this->add_colums_new($results);




        // изменяем колонки
        foreach ($results as &$item) {


            // amount
            $item['amount'] = $item['amount'] / 100;
            $item['summa_with_comission'] = $item['summa_with_comission'] / 100;
            $item['summa_out_comission'] = $item['summa_out_comission'] / 100;
            $item['comission'] = $item['comission'] / 100;
            $item['comission_bank_client'] = $item['comission_bank_client'] / 100;
            $item['comission_bank_avs'] = $item['comission_bank_avs'] / 100;
            
            

            // phone email
            $data = json_decode($item['jsonParams'], true);
            $item['jsonParams'] = $data['email'];
            $item['phone'] = $data['phone'];


            // description
            preg_match('/№(\d+)/', $item['description'], $matches);
            $orderNumber = $matches[1];
            $item['description'] = $orderNumber;
            

            // unic_order_id
            $isJson = strpos($item['unic_order_id'], '{') !== false;
            if ($isJson) {
                $jsonData = json_decode($item['unic_order_id'], true);
                $item['unic_order_id'] = $jsonData['unic_order_id'];
            }
        }
        

        return $results;
    }

    public function DatatimeCheck($filters)
    {
        $startDateTime = $filters['startDate'] . ' 00:00:00';
        $endDateTime = $filters['endDate'] . ' 23:59:59';


        // Создаем объект DateTime
        $date1 = new \DateTime($startDateTime);
        $date2 = new \DateTime($endDateTime);

        // прибавляем 2 часа
        $date1->modify('+0 hours');
        $date2->modify('+0 hours');

        // Получаем результат в нужном формате
        $startDateTime = $date1->format('Y-m-d H:i:s');
        $endDateTime = $date2->format('Y-m-d H:i:s');


        $data = [
            "startDateTime" => $startDateTime,
            "endDateTime" => $endDateTime,
        ];

        return $data;
    }

    private function filters($builder, $filters)
    {
        // основная фильтрация
        // $builder->where('orderStatus', '2');
        // $builder->where('status', 'paid');


        // доп фильтрация 
        if ($filters['startDate'] !== '' && $filters['endDate'] !== '') {

            $startDateTime = $filters['startDate'] . ' 00:00:00';
            $endDateTime = $filters['endDate'] . ' 23:59:59';


            // Создаем объект DateTime
            $date1 = new \DateTime($startDateTime);
            $date2 = new \DateTime($endDateTime);

            // прибавляем 2 часа
            $date1->modify('+0 hours');
            $date2->modify('+0 hours');

            // Получаем результат в нужном формате
            $startDateTime = $date1->format('Y-m-d H:i:s');
            $endDateTime = $date2->format('Y-m-d H:i:s');

           


            // Применение фильтров
            $builder->where('tranDateTime >=', $startDateTime);
            $builder->where('tranDateTime <=', $endDateTime);
        }


        if ($filters['currency'] !== '') {
            $builder->where('currency', $filters['currency']);
        }
        if ($filters['name_payment'] !== '') {
            $builder->where('name_payment', $filters['name_payment']);
        }
        if ($filters['status'] !== '') {

            if ($filters['status'] === 'null') {
                $builder->where('status', null);
            }else{
                $builder->where('status', $filters['status']);
            }
            
        }


        

        return $builder;
    }

    private function joinTables($builder)
    {
        // $builder->join('pays', 'transaction.id = pays.order_id', 'left');

        $builder->join('pays', 'transaction.id = pays.order_id AND pays.status = "paid"', 'left');
        $builder->where('transaction.orderStatus', 2);       
    }

    private function applySearchFilter($builder, $searchValue)
    {
        $builder->groupStart()
            ->like('transaction.id', $searchValue)
            ->orLike('name_payment', $searchValue)
            ->orLike('unic_order_id', $searchValue)
            ->orLike('status', $searchValue)
            ->orLike('datetime', $searchValue)
            ->orLike('description', $searchValue)
            ->orLike('orderNumber', $searchValue)
            ->orLike('amount', $searchValue)
            ->orLike('currency', $searchValue)
            ->orLike('tranDateTime', $searchValue)
            ->orLike('orderStatus', $searchValue)
            ->orLike('jsonParams', $searchValue)
            ->groupEnd();
    }

    public function getPaymentSummary($filters)
    {
        $builder = $this->builder();

        $this->joinTables($builder);

        
        // Фильтровать
        $builder = $this->filters($builder, $filters);



        $name_payments = $this->name_payments();



        $caseStatements = [];
        $caseBankStatements = [];
        $caseComissionStatements = [];
        $caseRewardStatements = [];

        foreach ($name_payments as $payment) {
            $caseStatements[] = "WHEN name_payment = '{$payment['value']}' THEN '{$payment['name']}'";
            $caseBankStatements[] = "WHEN name_payment = '{$payment['value']}' THEN '{$payment['bank']}'";
            $caseComissionStatements[] = "WHEN name_payment = '{$payment['value']}' THEN {$payment['comission']}";
            $caseRewardStatements[] = "WHEN name_payment = '{$payment['value']}' THEN {$payment['reward']}";
        }

        $caseSql = implode(' ', $caseStatements);
        $caseBankSql = implode(' ', $caseBankStatements);
        $caseComissionSql = implode(' ', $caseComissionStatements);
        $caseRewardSql = implode(' ', $caseRewardStatements);

        $builder->select("
            CASE 
                $caseSql
                ELSE name_payment 
            END as name_payment,
            CASE 
                $caseBankSql
                ELSE 'Unknown' 
            END as bank,
            COUNT(*) as count,
            SUM(amount * (1 + (CASE $caseRewardSql ELSE 0 END) / 100)) / 100 as total_amount,
            SUM(amount * (1 + (CASE $caseRewardSql ELSE 0 END) / 100) * (1 - (CASE $caseComissionSql ELSE 0 END) / 100)) / 100 as total_amount_comission,
            SUM(amount / 100) as amount
            

        ")
        ->groupBy('name_payment');

        // Получаем результаты
        $results = $builder->get()->getResultArray();


        // Итого
        $overall_total_amount = array_sum(array_column($results, 'total_amount'));

        // Добавим новые поля
        foreach ($results as &$result) {
            $result['percentage'] = ($overall_total_amount > 0) ? ($result['total_amount'] / $overall_total_amount) * 100 : 0; // Процент
            $result['comission'] = $result['total_amount'] - $result['total_amount_comission'];             // Комиссия
            $result['comission_bank_client'] = $result['total_amount'] - $result['amount'];                 // Комиссия банка (клиент)
            $result['comission_bank_avs'] = $result['comission'] - $result['comission_bank_client'];;      // Комиссия банка (АВС)

        }



        // получить сумму по банку
        $acquirings = [];

        foreach ($results as $entry) {
            $bank = $entry['bank'];
            $totalAmount = floatval($entry['total_amount_comission']);
            
            if (!isset($acquirings[$bank])) {
                $acquirings[$bank] = [
                    'bank' => $bank,
                    'amount' => 0
                ];
            }
            
            
            $acquirings[$bank]['amount'] += $totalAmount;
        }

        // Преобразование ассоциативного массива в индексированный массив
        $acquirings = array_values($acquirings);



        // Итого
        $overall_total_amount = array_sum(array_column($results, 'total_amount'));
        $overall_total_amount_comission = array_sum(array_column($results, 'total_amount_comission'));
        $overall_total_count = array_sum(array_column($results, 'count'));
        $overall_comission = array_sum(array_column($results, 'comission'));
        $overall_amount = array_sum(array_column($results, 'amount'));
        $overall_comission_bank_client = array_sum(array_column($results, 'comission_bank_client'));
        $overall_comission_bank_avs = array_sum(array_column($results, 'comission_bank_avs'));

        // Return
        return [
            'summary' => $results,
            'acquirings' => $acquirings,
            'overall_total_amount' => $overall_total_amount,
            'overall_total_amount_comission' => $overall_total_amount_comission,
            'overall_total_count' => $overall_total_count,
            'overall_comission' => $overall_comission,
            'overall_amount' => $overall_amount,
            'overall_comission_bank_client' => $overall_comission_bank_client,
            'overall_comission_bank_avs' => $overall_comission_bank_avs,
            
        ];
    }




    



}
