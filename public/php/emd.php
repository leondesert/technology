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
Editor::inst( $db, 'emd', 'emd_id' )
	->fields(
		Field::inst('emd_id'),
		Field::inst('tickets_id'),
		Field::inst('passengers_id'),
		//Field::inst('emd_coupon_no')->searchBuilderOptions( SearchBuilderOptions::inst()),
		Field::inst('emd_value')
		//Field::inst('emd_remark')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('emd_related')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('emd_reason_rfisc')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('emd_reason_airline')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('emd_xbaggage_number')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('emd_xbaggage_qualifier')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('emd_xbaggage_rpu')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('emd_xbaggage_currency')->searchBuilderOptions( SearchBuilderOptions::inst()),
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

	
	
