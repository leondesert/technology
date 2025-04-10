<?php

namespace App\Models;

use CodeIgniter\Model;

class FlightLoadModel extends Model
{
    protected $table = 'tickets';
    protected $primaryKey = 'tickets_id';
    protected $allowedFields = ['tickets_type', 'tickets_currency', 'tickets_dealdate', 'tickets_dealtime', 'tickets_OPTYPE', 'tickets_TRANS_TYPE', 'tickets_BSONUM', 'tickets_EX_BSONUM', 'tickets_TO_BSONUM', 'tickets_FARE', 'tickets_PNR_LAT', 'tickets_DEAL_date', 'tickets_DEAL_disp', 'tickets_DEAL_time', 'tickets_DEAL_utc', 'summa_no_found', 'opr_code', 'agency_code', 'emd_value', 'fops_type', 'fops_amount', 'fio', 'pass', 'pas_type', 'citizenship', 'citycodes', 'carrier', 'class', 'reis', 'flydate', 'flytime', 'basicfare', 'stamp_code', 'tap_code', 'tax_code', 'tax_amount'];

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
            $builder->orderBy('tickets_dealdate', 'DESC');
        }
        return $builder->get($length, $start)->getResultArray();
    }

    private function filters($builder, $filters)
    {
        if ($filters['startDate'] !== '' && $filters['endDate'] !== '') {
            $builder->where('tickets_dealdate' . ' >=', $filters['startDate']);
            $builder->where('tickets_dealdate' . ' <=', $filters['endDate']);
        }
        if ($filters['flydate'] !== '') {
            $builder->like('flydate', $filters['flydate'], 'both');
        }
        if ($filters['citycodes'] !== '') {
            $builder->like('citycodes', $filters['citycodes'], 'both');
        }
        if ($filters['flytime'] !== '') {
            $builder->like('flytime', $filters['flytime'], 'both');
        }
        if ($filters['colum_name'] !== '') {
            $builder->whereIn($filters['colum_name'], $filters['ids']);
        }
        if ($filters['value_table'] !== 'all') {
            $colum_name = $filters['name_table'].'_code';
            $builder->where($colum_name, $filters['value_table']);
        }

        return $builder;
    }

    private function joinTables($builder)
    {
        $builder->join('opr', 'opr.opr_id = tickets.opr_id', 'left');
        $builder->join('agency', 'agency.agency_id = tickets.agency_id', 'left');
        $builder->join('passengers', 'passengers.passengers_id = tickets.passengers_id', 'left');
        $builder->join('stamp', 'stamp.stamp_id = tickets.stamp_id', 'left');
        $builder->join('tap', 'tap.tap_id = tickets.tap_id', 'left');
        $builder->join('taxes', 'taxes.tickets_id = tickets.tickets_id', 'left');
        $builder->join('emd', 'emd.tickets_id = tickets.tickets_id', 'left');
        $builder->join('fops', 'fops.tickets_id = tickets.tickets_id', 'left');
        $builder->join('segments', 'segments.tickets_id = tickets.tickets_id', 'left');
    }

    private function joinTables2($builder)
    {
        $builder->join('fops', 'fops.tickets_id = tickets.tickets_id', 'left');
    }

    private function applySearchFilter($builder, $searchValue)
    {
        $builder->groupStart()
            ->like('tickets_type', $searchValue)
            ->orLike('tickets_currency', $searchValue)
            ->orLike('tickets_dealdate', $searchValue)
            ->orLike('tickets_dealtime', $searchValue)
            ->orLike('tickets_OPTYPE', $searchValue)
            ->orLike('tickets_TRANS_TYPE', $searchValue)
            ->orLike('tickets_BSONUM', $searchValue)
            ->orLike('tickets_EX_BSONUM', $searchValue)
            ->orLike('tickets_TO_BSONUM', $searchValue)
            ->orLike('tickets_FARE', $searchValue)
            ->orLike('tickets_PNR_LAT', $searchValue)
            ->orLike('tickets_DEAL_date', $searchValue)
            ->orLike('tickets_DEAL_disp', $searchValue)
            ->orLike('tickets_DEAL_time', $searchValue)
            ->orLike('tickets_DEAL_utc', $searchValue)
            ->orLike('summa_no_found', $searchValue)
            ->orLike('opr_code', $searchValue)
            ->orLike('agency_code', $searchValue)
            ->orLike('emd_value', $searchValue)
            ->orLike('fops_type', $searchValue)
            ->orLike('fops_amount', $searchValue)
            ->orLike('fio', $searchValue)
            ->orLike('pass', $searchValue)
            ->orLike('pas_type', $searchValue)
            ->orLike('citizenship', $searchValue)
            ->orLike('citycodes', $searchValue)
            ->orLike('carrier', $searchValue)
            ->orLike('class', $searchValue)
            ->orLike('reis', $searchValue)
            ->orLike('flydate', $searchValue)
            ->orLike('flytime', $searchValue)
            ->orLike('basicfare', $searchValue)
            ->orLike('stamp_code', $searchValue)
            ->orLike('tap_code', $searchValue)
            ->orLike('tax_code', $searchValue)
            ->orLike('tax_amount', $searchValue)
            ->groupEnd();
    }

    public function getPopularFlights($filters)
    {
        
        $builder = $this->builder();
        $this->joinTables($builder);
        
        
        // Выбор и группировка
        $builder->select($filters['filterby'].' as filterby, COUNT(*) as sale_count, SUM(fops_amount) as total_fops_amount');

        // Фильтровать
        $builder = $this->filters($builder, $filters);

        $builder->where('tickets_OPTYPE', 'SALE')
                ->groupBy('filterby')
                ->orderBy('sale_count', 'DESC');


        if ($filters['show'] !== 'all') {
            $builder->limit($filters['show']);
        }
                
        
        $query = $builder->get();
        return $query->getResult();
    }










}
