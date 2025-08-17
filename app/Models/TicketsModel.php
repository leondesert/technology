<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketsModel extends Model
{
    protected $table = 'tickets';
    protected $primaryKey = 'tickets_id';
    protected $allowedFields = [
        'agency_id', 'agency_code', 'agency_name', 'agency_address', 'agency_phone', 'agency_mail',
        'tickets_id', 'passengers_id', 'opr_id', 'tap_id', 'stamp_id', 'share_id','reshare_id', 'tickets_type',
        'tickets_system_id', 'tickets_system_session', 'tickets_system_bso_id', 'tickets_currency',
        'tickets_dealdate', 'tickets_dealtime', 'tickets_OPTYPE', 'tickets_TRANS_TYPE',
        'tickets_MCO_TYPE', 'tickets_MCO_TYPE_rfic', 'tickets_MCO_TYPE_rfisc', 'tickets_BSONUM',
        'tickets_EX_BSONUM', 'tickets_GENERAL_CARRIER', 'tickets_RETTYPE', 'tickets_TOURCODE',
        'tickets_OCURRENCY', 'tickets_ORATE', 'tickets_NCURRENCY', 'tickets_NRATE',
        'tickets_OPRATE', 'tickets_FARE', 'tickets_FARE_type', 'tickets_FARE_vat_amount',
        'tickets_FARE_vat_rate', 'tickets_OFARE', 'tickets_PENALTY', 'tickets_FARECALC',
        'tickets_ENDORS_RESTR', 'tickets_PNR', 'tickets_PNR_LAT', 'tickets_INV_PNR',
        'tickets_CONJ', 'tickets_TO_BSONUM', 'tickets_TYP_NUM_ser', 'tickets_FCMODE',
        'tickets_COMMISSION_type', 'tickets_COMMISSION_currency', 'tickets_COMMISSION_amount',
        'tickets_COMMISSION_rate', 'tickets_BOOK_date', 'tickets_BOOK_disp', 'tickets_BOOK_time',
        'tickets_BOOK_utc', 'tickets_DEAL_date', 'tickets_DEAL_disp', 'tickets_DEAL_time',
        'tickets_DEAL_utc', 'tickets_DEAL_ersp', 'tickets_DEAL_pcc', 'tickets_SALE_date',
        'tickets_SALE_disp', 'tickets_SALE_time', 'tickets_SALE_utc', 'tickets_AGN_INFO_CLIENT_NUM',
        'tickets_AGN_INFO_RESERV_NUM', 'tickets_AGN_INFO_INFO', 'ticket_exchanged',
    ];

    public function getData($start_date = null, $end_date = null, $ids = null, $colum_name = null, $searchBuilder = null)
    {
        $builder = $this->builder();

        $fields = [
            'tickets.tickets_id',
            'tickets.tickets_type',
            'tickets.tickets_currency',
            'tickets.tickets_dealdate',
            'tickets.tickets_dealtime',
            'tickets.tickets_OPTYPE',
            'tickets.tickets_TRANS_TYPE',
            'tickets.tickets_BSONUM',
            'tickets.tickets_EX_BSONUM',
            'tickets.tickets_TO_BSONUM',
            'tickets.tickets_FARE',
            'tickets.tickets_PNR_LAT',
            'tickets.tickets_DEAL_date',
            'tickets.tickets_DEAL_disp',
            'tickets.tickets_DEAL_time',
            'tickets.tickets_DEAL_utc',
            'tickets.summa_no_found',
            'opr.opr_code',
            'reshare.reshare_code',
            'agency.agency_code',
            'emd.emd_value',
            'fops.fops_type',
            'fops.fops_amount',
            'passengers.fio',
            'passengers.pass',
            'passengers.pas_type',
            'passengers.citizenship',
            'segments.citycodes',
            'segments.carrier',
            'segments.class',
            'segments.reis',
            'segments.flydate',
            'segments.flytime',
            'segments.basicfare',
            'stamp.stamp_code',
            'tap.tap_code',
            'taxes.tax_code',
            'taxes.tax_amount',
            'taxes.tax_amount_main',
        ];

        $builder->select($fields);
        $builder->join('opr', 'opr.opr_id = tickets.opr_id', 'left')
                ->join('share', 'share.share_id = tickets.share_id', 'left')
                ->join('reshare', 'reshare.reshare_id = tickets.reshare_id', 'left')
                ->join('agency', 'agency.agency_id = tickets.agency_id', 'left')
                ->join('emd', 'emd.tickets_id = tickets.tickets_id', 'left')
                ->join('fops', 'fops.tickets_id = tickets.tickets_id', 'left')
                ->join('passengers', 'passengers.passengers_id = tickets.passengers_id', 'left')
                ->join('segments', 'segments.tickets_id = tickets.tickets_id', 'left')
                ->join('stamp', 'stamp.stamp_id = tickets.stamp_id', 'left')
                ->join('tap', 'tap.tap_id = tickets.tap_id', 'left')
                ->join('taxes', 'taxes.tickets_id = tickets.tickets_id', 'left');

        if ($start_date && $end_date) {
            $builder->where('tickets.tickets_dealdate >=', $start_date)
                    ->where('tickets.tickets_dealdate <=', $end_date);
        }

        if ($ids && $colum_name) {
            $builder->whereIn($colum_name, $ids);
        }

        if ($searchBuilder && isset($searchBuilder['criteria'])) {
            $logic = $searchBuilder['logic'] ?? 'AND';
            foreach ($searchBuilder['criteria'] as $criteria) {
                $field = $this->mapDataToField($criteria['data']);
                if ($criteria['condition'] === '=') {
                    $builder->where($field, $criteria['value'][0]);
                } elseif ($criteria['condition'] === 'between' && $criteria['data'] === 'Дата формирования') {
                    $builder->where('tickets.tickets_dealdate >=', $criteria['value'][0])
                            ->where('tickets.tickets_dealdate <=', $criteria['value'][1]);
                }
            }
        }

        // Limit to first 10 rows
        $builder->limit(10);

        return $builder->get()->getResultArray();
    }

    private function mapDataToField($data)
    {
        $mapping = [
            'Тип билета' => 'tickets.tickets_type',
            'Валюта билета' => 'tickets.tickets_currency',
            'Дата формирования' => 'tickets.tickets_dealdate',
            'Время формирования' => 'tickets.tickets_dealtime',
            'Тип операции' => 'tickets.tickets_OPTYPE',
            'Тип транзакции' => 'tickets.tickets_TRANS_TYPE',
            'Номер билета' => 'tickets.tickets_BSONUM',
            'Номер старшего билета' => 'tickets.tickets_EX_BSONUM',
            'Номер основного билета' => 'tickets.tickets_TO_BSONUM',
            'Тариф цена' => 'tickets.tickets_FARE',
            'PNR' => 'tickets.tickets_PNR_LAT',
            'Дата оформления' => 'tickets.tickets_DEAL_date',
            'Индентификатор продавца' => 'tickets.tickets_DEAL_disp',
            'Время оформления' => 'tickets.tickets_DEAL_time',
            'Время оформления UTC' => 'tickets.tickets_DEAL_utc',
            'Сумма обмена без EMD' => 'tickets.summa_no_found',
            'Код оператора' => 'opr.opr_code',
            'Код пере-раздачи' => 'reshare.reshare_code',
            'Код агентства' => 'agency.agency_code',
            'Сумма EMD' => 'emd.emd_value',
            'Вид оплаты' => 'fops.fops_type',
            'Сумма оплаты' => 'fops.fops_amount',
            'ФИО' => 'passengers.fio',
            'Паспорт' => 'passengers.pass',
            'Тип' => 'passengers.pas_type',
            'Гражданство' => 'passengers.citizenship',
            'Маршрут' => 'segments.citycodes',
            'Перевозчик' => 'segments.carrier',
            'Класс' => 'segments.class',
            'Рейс' => 'segments.reis',
            'Дата полёта' => 'segments.flydate',
            'Время полёта' => 'segments.flytime',
            'Тариф' => 'segments.basicfare',
            'Код ППР' => 'stamp.stamp_code',
            'Код пульта' => 'tap.tap_code',
            'Код сбора' => 'taxes.tax_code',
            'Сумма сбора' => 'taxes.tax_amount',
            'Суммы сборов' => 'taxes.tax_amount_main',
        ];

        return $mapping[$data] ?? null;
    }
}