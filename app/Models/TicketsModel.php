<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketsModel extends Model
{
    protected $table = 'tickets'; // Имя таблицы базы данных, связанной с моделью
    protected $primaryKey = 'tickets_id'; // Первичный ключ таблицы
    protected $allowedFields = [
        'agency_id',
        'agency_code',
        'agency_name',
        'agency_address',
        'agency_phone',
        'agency_mail',
        'tickets_id',
        'passengers_id',
        'opr_id',
        'tap_id',
        'stamp_id',
        'tickets_type',
        'tickets_system_id',
        'tickets_system_session',
        'tickets_system_bso_id',
        'tickets_currency',
        'tickets_dealdate',
        'tickets_dealtime',
        'tickets_OPTYPE',
        'tickets_TRANS_TYPE',
        'tickets_MCO_TYPE',
        'tickets_MCO_TYPE_rfic',
        'tickets_MCO_TYPE_rfisc',
        'tickets_BSONUM',
        'tickets_EX_BSONUM',
        'tickets_GENERAL_CARRIER',
        'tickets_RETTYPE',
        'tickets_TOURCODE',
        'tickets_OCURRENCY',
        'tickets_ORATE',
        'tickets_NCURRENCY',
        'tickets_NRATE',
        'tickets_OPRATE',
        'tickets_FARE',
        'tickets_FARE_type',
        'tickets_FARE_vat_amount',
        'tickets_FARE_vat_rate',
        'tickets_OFARE',
        'tickets_PENALTY',
        'tickets_FARECALC',
        'tickets_ENDORS_RESTR',
        'tickets_PNR',
        'tickets_PNR_LAT',
        'tickets_INV_PNR',
        'tickets_CONJ',
        'tickets_TO_BSONUM',
        'tickets_TYP_NUM_ser',
        'tickets_FCMODE',
        'tickets_COMMISSION_type',
        'tickets_COMMISSION_currency',
        'tickets_COMMISSION_amount',
        'tickets_COMMISSION_rate',
        'tickets_BOOK_date',
        'tickets_BOOK_disp',
        'tickets_BOOK_time',
        'tickets_BOOK_utc',
        'tickets_DEAL_date',
        'tickets_DEAL_disp',
        'tickets_DEAL_time',
        'tickets_DEAL_utc',
        'tickets_DEAL_ersp',
        'tickets_DEAL_pcc',
        'tickets_SALE_date',
        'tickets_SALE_disp',
        'tickets_SALE_time',
        'tickets_SALE_utc',
        'tickets_AGN_INFO_CLIENT_NUM',
        'tickets_AGN_INFO_RESERV_NUM',
        'tickets_AGN_INFO_INFO',
        'ticket_exchanged',
    ];
     // Поля, доступные для работы с таблицей
  
     public function getData()
     {
        return $this->builder()
        ->select('tickets_id, tickets_type, tickets_currency')
        ->limit(10) // Ограничиваем только 10 строк
        ->get()
        ->getResultArray(); // Преобразуем результат в массив
     }
     

}
