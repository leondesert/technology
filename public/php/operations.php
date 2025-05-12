<?php

/*
 * Example PHP implementation used for the index.html example
 */

// DataTables PHP library
include( "lib/DataTables.php" );

// Alias Editor classes so they are easy to use
use
	DataTables\Editor,
	DataTables\Editor\Field,
	DataTables\Editor\Format,
	DataTables\Editor\Mjoin,
	DataTables\Editor\Options,
	DataTables\Editor\Upload,
	DataTables\Editor\Validate,
	DataTables\Editor\ValidateOptions,
	DataTables\Editor\SearchBuilderOptions;




// $start_date = '2024-01-01';
// $end_date = '2024-01-05';

$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

$ids = $_POST['key'];
$colum_name = "tickets.".$_POST['colum_name'];

$searchBuilder = json_decode($_POST['sss'], true);
$table_name = str_replace("_id", "", $_POST['colum_name']);

$uniqueTaxCodes = $_POST['uniqueTaxCodes'];

// Build our Editor instance and process the data coming from _POST
Editor::inst( $db, 'tickets', 'tickets_id' )
	->fields(
		// Field::inst('tickets.tickets_type')->searchBuilderOptions(SearchBuilderOptions::inst()),
		Field::inst('tickets.tickets_type'),
		// Field::inst('tickets.tickets_currency')->searchBuilderOptions(SearchBuilderOptions::inst()),
		Field::inst('tickets.tickets_currency'),
		Field::inst('tickets.tickets_dealdate'),
		Field::inst('tickets.tickets_dealtime'),
		// Field::inst('tickets.tickets_OPTYPE')->searchBuilderOptions(SearchBuilderOptions::inst()),
		Field::inst('tickets.tickets_OPTYPE'),
		// Field::inst('tickets.tickets_TRANS_TYPE')->searchBuilderOptions(SearchBuilderOptions::inst()),
		Field::inst('tickets.tickets_TRANS_TYPE'),
		Field::inst('tickets.tickets_BSONUM'),
		Field::inst('tickets.tickets_EX_BSONUM'),
		Field::inst('tickets.tickets_TO_BSONUM'),
		Field::inst('tickets.tickets_FARE'),
		Field::inst('tickets.tickets_PNR_LAT'),
		Field::inst('tickets.tickets_DEAL_date'),
		Field::inst('tickets.tickets_DEAL_disp'),
		Field::inst('tickets.tickets_DEAL_time'),
		Field::inst('tickets.tickets_DEAL_utc'),
		Field::inst('tickets.summa_no_found'),

	)
	// Присоединяем таблицу "opr"
	->leftJoin( 'opr', 'opr.opr_id', '=', 'tickets.opr_id' )
	->fields(
		Field::inst('opr.opr_code')	
	)
	// Присоединяем таблицу "agency"
	->leftJoin( 'agency', 'agency.agency_id', '=', 'tickets.agency_id' )
	->fields(
		Field::inst('agency.agency_code'),
	)
	// Присоединяем таблицу "emd"
	->leftJoin( 'emd', 'emd.tickets_id', '=', 'tickets.tickets_id' )
	->fields(
		Field::inst('emd.emd_value')
	)
	// Присоединяем таблицу "fops"
	->leftJoin( 'fops', 'fops.tickets_id', '=', 'tickets.tickets_id' )
	->fields(
		Field::inst('fops.fops_type'),
		Field::inst('fops.fops_amount')
	)

	// Присоединяем таблицу "passengers"
	->leftJoin( 'passengers', 'passengers.passengers_id', '=', 'tickets.passengers_id' )
	->fields(
		Field::inst('passengers.fio'),
		Field::inst('passengers.pass'),
		Field::inst('passengers.pas_type'),
		Field::inst('passengers.citizenship')
	)

	// Присоединяем таблицу "segments"
	->leftJoin( 'segments', 'segments.tickets_id', '=', 'tickets.tickets_id' )
	->fields(
		Field::inst('segments.citycodes'),
		Field::inst('segments.carrier'),
		Field::inst('segments.class'),
		Field::inst('segments.reis'),
		Field::inst('segments.flydate'),
		Field::inst('segments.flytime'),
		Field::inst('segments.basicfare')
	)


	// Присоединяем таблицу "stamp"
	->leftJoin( 'stamp', 'stamp.stamp_id', '=', 'tickets.stamp_id' )
	->fields(
		Field::inst('stamp.stamp_code')
	)
	// Присоединяем таблицу "tap"
	->leftJoin( 'tap', 'tap.tap_id', '=', 'tickets.tap_id' )
	->fields(
		Field::inst('tap.tap_code')
	)
	
	// Присоединяем таблицу "taxes"
	->leftJoin( 'taxes', 'taxes.tickets_id', '=', 'tickets.tickets_id' )
	->fields(
		Field::inst('taxes.tax_code'),
		Field::inst('taxes.tax_amount'),
		Field::inst('taxes.tax_amount_main'),
	)

	//добавить кастомное поле
	->on('postGet', function ($editor, &$data, $id) use ($db, $searchBuilder, $table_name, $uniqueTaxCodes) {


		if (isset($searchBuilder['criteria'])) {
			$criteria = $searchBuilder['criteria'];
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
		}
    

	

    foreach ($data as &$record) {

    		
        $c_name = $table_name.'_code';

		$currencyValue = null;
		$rewardValue = null;
		$penaltyValue = null;
		$penalty = null;
		$reward = null;

    	$tickets_type = $record['tickets']['tickets_type'];
        $tickets_trans_type = $record['tickets']['tickets_TRANS_TYPE'];

        $dealdate = $record['tickets']['tickets_dealdate'];
        $currency = $record['tickets']['tickets_currency'];
        $tickets_FARE = $record['tickets']['tickets_FARE'];
        $value_code = $record[$table_name][$c_name];
        $citycodes = $record['segments']['citycodes'];
        $carrier = $record['segments']['carrier'];

        
        // currencyValue
        $query = $editor->db()->sql(
            "SELECT value FROM currencies WHERE date = '{$dealdate}' AND name = '{$currency}' LIMIT 1"
        )->fetchAll();

        if (!empty($query)) {
            $currencyValue = $query[0]['value'];
        }


        // Формирования колонок reward и penalty

        $c_name = $table_name.'_code';
        $c_name2 = $table_name.'_id';

        // получить таблицу по $table_name
        $results_table = $editor->db()->sql(
            "SELECT `{$c_name2}`, reward, penalty FROM `{$table_name}` WHERE `{$c_name}` = '{$value_code}' LIMIT 1"
        )->fetchAll();

        // получить таблицу rewards
        $results_rewards = $editor->db()->sql(
            "SELECT * FROM `rewards`"
        )->fetchAll();



        // 1. поиск по маршруту и перевозчику
        if (!empty($results_rewards)) {

        	// 1.1 поиск конкретного маршрута
	        foreach ($results_rewards as $row) {
	        	if ($row['method'] === 'reward' && $row['type'] === 'citycodes' && $row['code'] === $citycodes && $row['name'] === $table_name && $row['value'] === $results_table[0][$c_name2]) {

	        		// Установка вознаграждения
	        		$rewardValue = $row['procent'];
	        		break;
	        	}
	        	if ($row['method'] === 'penalty' && $row['type'] === 'citycodes' && $row['code'] === $citycodes && $row['name'] === $table_name && $row['value'] === $results_table[0][$c_name2]) {

	        		// Установка штрафа
	        		$penaltyValue = $row['procent'];
	        		break;
	        	}
	        }

	        // 1.2 поиск по частям маршрута
		    if ($citycodes !== null) {
		    	// с начала и с конца
                $prefixStartEnd = substr($citycodes, 0, 3) . '/*/' . substr($citycodes, -3);
		    	// с начала
		        $prefixStart = substr($citycodes, 0, 3) . '/*';
		        // с конца
		        $prefixEnd = '*/' . substr($citycodes, -3);

		        // между
		        $prefixMiddle = null;
		        if (preg_match('/\/([A-Z]{3})\//', $citycodes, $matches)) {
		            $prefixMiddle = '*/'. $matches[1] . '/*';
		        }


		        // по умолчанию разрешаем поиск по началу и концу
                $enableSearch = true;

                // Проверка на наличие двух слэшей в строке
                if (substr_count($citycodes, '/') >= 2) {
                    // если два или более слэша, то отключаем поиск по началу и концу
                    $enableSearch = false;
                }
                

		        foreach ($results_rewards as $row) {
		        	if ($rewardValue === null){
		        		if ($row['method'] === 'reward' 
		        			&& $row['type'] === 'citycodes' 
		        			&& ($row['code'] === $prefixStartEnd 
		        			|| ($enableSearch && ($row['code'] === $prefixStart || $row['code'] === $prefixEnd))
		        			|| ($prefixMiddle && $row['code'] === $prefixMiddle))
		                	&& $row['name'] === $table_name 
		                	&& $row['value'] === $results_table[0][$c_name2]) {

		            		// Установка вознаграждения
		                	$rewardValue = $row['procent'];
		                	break;
		            	}
		        	}
		        	if ($penaltyValue === null){
		        		if ($row['method'] === 'penalty' && $row['type'] === 'citycodes' && ($row['code'] === $prefixStart || $row['code'] === $prefixEnd || ($prefixMiddle && $row['code'] === $prefixMiddle))
		                && $row['name'] === $table_name 
		                && $row['value'] === $results_table[0][$c_name2]) {

		            		// Установка вознаграждения
		                	$penaltyValue = $row['procent'];
		                	break;
		            	}
		        	}
		            
		        }
		    }

		    // 1.3 поиск конкретного перевозчика
		    if ($carrier !== null) {

			    foreach ($results_rewards as $row) {

			    	if ($rewardValue === null){
			    		if ($row['method'] === 'reward' && $row['type'] === 'carrier' && $row['code'] === $carrier && $row['name'] === $table_name && $row['value'] === $results_table[0][$c_name2]) {

			        		// Установка вознаграждения
			        		$rewardValue = $row['procent'];
			        		break;
			        	}
			    	}
			    	if ($penaltyValue === null){
			    		if ($row['method'] === 'penalty' && $row['type'] === 'carrier' && $row['code'] === $carrier && $row['name'] === $table_name && $row['value'] === $results_table[0][$c_name2]) {

			        		// Установка вознаграждения
			        		$penaltyValue = $row['procent'];
			        		break;
			        	}
			    	}
		        	
		        }
	    	}

            

        }

        // 2. Если не найдено, установим базовую
        if (!empty($results_table)) {

        	// Установка вознаграждения
            if ($rewardValue === null) {
            	$rewardValue = $results_table[0]['reward'];
            }

            // Установка штраф
            if ($penaltyValue === null) {
            	$penaltyValue = $results_table[0]['penalty'];
            }
        }

       
        
        // Вознаграждения
        if ($tickets_type === "ETICKET" && $tickets_trans_type === "SALE" || $tickets_type === "ETICKET" && $tickets_trans_type === "EXCHANGE") {
        	$reward = $tickets_FARE * $rewardValue / 100;
        	$reward = round($reward, 2);
        	$reward = number_format($reward ?? 0, 2);
        }

        // Штраф
        if ($tickets_type === "ETICKET" && $tickets_trans_type === "CANCEL") {
        	$penalty = $currencyValue * $penaltyValue;
        	$penalty = round($penalty, 2);
        	$penalty = number_format($penalty ?? 0, 2);
        }


       

        // Добавляем кастомные данные к каждой записи
        $record['custom'] = array(
        	"penalty_summa" => number_format($penaltyValue ?? 0, 2),
        	"penalty_currency" => number_format($currencyValue ?? 0, 2),
        	"penalty" => $penalty,
        	"reward_procent" => number_format($rewardValue ?? 0, 2),
        	"reward" => $reward,
        	

        );


        // вывод уникальных значении tax
        // $uniqueTaxCodes = [
        //     'A2', 'AE', 'CN', 'CP', 'CS', 'DE', 'E3', 'F6', 'FX', 
        //     'GE', 'I6', 'IO', 'IR', 'JA', 'JN', 'M6', 'OY', 'RA', 
        //     'T2', 'TP', 'TR', 'UJ', 'UZ', 'YQ', 'YR', 'ZR', 'ZZ'
        // ];


    	if (!empty($record['taxes']['tax_code']) && !empty($record['taxes']['tax_amount_main'])) {

			$taxCodes = explode(',', $record['taxes']['tax_code']);
		    $taxAmounts = explode(',', $record['taxes']['tax_amount_main']);
		    $rowData = array_fill_keys($uniqueTaxCodes, null); // Создаем строку с пустыми значениями

		    foreach ($taxCodes as $index => $code) {
		        $rowData[$code] = $taxAmounts[$index];

		    }
		    
		    
			// Добавляем кастомные данные к каждой записи
		    $record['tax'] = $rowData;
		    

		}else{
			$rowData = array_fill_keys($uniqueTaxCodes, null);
			$record['tax'] = $rowData;
		}


    }

    // Добавляем uniqueTaxCodes в метаданные
    // $data['options']['uniqueTaxCodes'] = $uniqueTaxCodes;
    // $data['options']['formattedData'] = $formattedData;
    

})

	->where(
		function ( $q ) use ( $colum_name, $ids ){
			if (!empty($ids)) {
	            $q->where($colum_name, $ids, 'IN', false);
	        }
		}
	)


	->where(function ($q) use ($searchBuilder, $start_date, $end_date) {
		$const = 0;
        if (isset($searchBuilder['criteria'])) {
            foreach ($searchBuilder['criteria'] as $criteria) {
                if ($criteria['condition'] === '=' && $criteria['data'] === 'Дата формирования') {
                    $q->where('tickets.tickets_dealdate', $criteria['value'][0], '=');
                    $const = 1;
                } elseif ($criteria['condition'] === 'between' && $criteria['data'] === 'Дата формирования') {
                    $q->where('tickets.tickets_dealdate', $criteria['value'][0], '>=')
          			  ->where('tickets.tickets_dealdate', $criteria['value'][1], '<=');
          			$const = 1;
                }
                
            }
        }

        if($const === 0) {
	        	    $q->where('tickets.tickets_dealdate', $start_date, '>=')
	          		  ->where('tickets.tickets_dealdate', $end_date, '<=');
        }
        
    })


	->debug(false)
	->process( $_POST )
	->json();




// $data = json_decode(ob_get_clean(), true);
// $data = json_decode(ob_get_clean(), true);

// $uniqueTaxCodes = ['CP', 'YR', 'A2'];
// $formattedData = [];
// $uniqueTaxCodes = [];

// Собираем все уникальные tax_code
// foreach ($data['data'] as &$record) {

// 	if (!empty($record['taxes']['tax_code'])) {

// 		$taxCodes = explode(',', $record['taxes']['tax_code']);

// 	    foreach ($taxCodes as $code) {
// 	        if (!in_array($code, $uniqueTaxCodes)) {
// 	            $uniqueTaxCodes[] = $code;
// 	        }
// 	    }

// 	}
  
// }


// Подготавливаем данные
// foreach ($data['data'] as &$record) {

// 	if (!empty($record['taxes']['tax_code'])) {

// 		$taxCodes = explode(',', $record['taxes']['tax_code']);
// 	    $taxAmounts = explode(',', $record['taxes']['tax_ncurrency'] ?? 'пустой');
// 	    $rowData = array_fill_keys($uniqueTaxCodes, null); // Создаем строку с пустыми значениями

// 	    foreach ($taxCodes as $index => $code) {
// 	        $rowData[$code] = $taxAmounts[$index];

// 	    }
	    
// 	    $formattedData[] = $rowData;

// 		// Добавляем кастомные данные к каждой записи
// 	    $record['tax'] = $rowData;
	    

// 	}else{
// 		$rowData = array_fill_keys($uniqueTaxCodes, null);
// 		$record['tax'] = $rowData;
// 	}	




// }






// $data['options']['uniqueTaxCodes'] = $uniqueTaxCodes;
// $data['options']['formattedData'] = $formattedData;

// echo json_encode($data);
	
