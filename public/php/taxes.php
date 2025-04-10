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


	
$ids = $_POST['key'];
$colum_name = $_POST['colum_name'];

$sql = "(SELECT tickets_id FROM tickets WHERE ".$colum_name." IN ".$ids.")";
$colum_name = 'tickets_id';


// Build our Editor instance and process the data coming from _POST
Editor::inst( $db, 'taxes', 'taxes_id' )
	->fields(
		Field::inst('taxes_id'),
		Field::inst('tickets_id'),
		Field::inst('passengers_id'),
		Field::inst('segno'),
		Field::inst('tax_code'),
		Field::inst('tax_amount'),
		Field::inst('tax_namount'),
		Field::inst('tax_ncurrency'),
		Field::inst('tax_nrate'),
		Field::inst('tax_oamount'),
		Field::inst('tax_ocurrency'),
		Field::inst('tax_orate'),
		Field::inst('tax_oprate'),
		Field::inst('tax_taxes_vat_amount'),
		Field::inst('tax_taxes_vat_rate'),
		Field::inst('tax_tax_vat_amount'),
		Field::inst('tax_tax_vat_rate'),
		Field::inst('exchanged'),


	)
	->where(
		function ( $q ) use ( $colum_name, $sql ){
			if (!empty($ids)) {
				$q->where( $colum_name, $sql, 'IN', false );
			}
		} 
	)
	->debug(true)
	->process( $_POST )
	->json();

	
	
