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
Editor::inst( $db, 'segments', 'segments_id' )
	->fields(
		Field::inst('segments_id'),
		Field::inst('tickets_id'),
		Field::inst('passengers_id'),
		//Field::inst('segno')->searchBuilderOptions( SearchBuilderOptions::inst()),
		Field::inst('citycodes'),
		//Field::inst('portcodes')->searchBuilderOptions( SearchBuilderOptions::inst()),
		Field::inst('carrier'),
		Field::inst('class'),
		Field::inst('reis'),
		Field::inst('flydate'),
		Field::inst('flytime'),
		Field::inst('basicfare')
		//Field::inst('seg_bsonum')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('coupon_no')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('is_void')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('stpo')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('term1')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('term2')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('arrdate')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('arrtime')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('nfare')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('baggage_number')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('baggage_qualifier')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('ffp_info_number')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('ffp_info_certificate')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('exchanged')->searchBuilderOptions( SearchBuilderOptions::inst()),
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

	
	
