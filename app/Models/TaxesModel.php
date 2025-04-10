<?php

namespace App\Models;

use CodeIgniter\Model;

class TaxesModel extends Model
{
    protected $table = 'taxes';
    protected $primaryKey = 'taxes_id';
    protected $allowedFields = ['taxes_id', 'tickets_id', 'passengers_id', 'segno', 'tax_code', 'tax_amount', 'tax_namount', 'tax_ncurrency', 'tax_nrate', 'tax_oamount', 'tax_ocurrency', 'tax_orate', 'tax_oprate', 'tax_taxes_vat_amount', 'tax_taxes_vat_rate', 'tax_tax_vat_amount', 'tax_tax_vat_rate', 'exchanged'];

}
