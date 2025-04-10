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

$sql = "(SELECT passengers_id FROM tickets WHERE ".$colum_name." IN ".$ids.")";
$colum_name = 'passengers_id';


// Build our Editor instance and process the data coming from _POST
Editor::inst( $db, 'passengers', 'passengers_id' )
	->fields(
		Field::inst('passengers_id'),
		Field::inst('fio'),
		Field::inst('surname'),
		Field::inst('name'),
		Field::inst('pass'),
		Field::inst('pas_type'),
		Field::inst('benefit_doc'),
		Field::inst('birth_date'),
		Field::inst('gender'),
		Field::inst('citizenship'),
		Field::inst('contact')
	)
	->where(
		function ( $q ) use ( $colum_name, $sql, $ids ){
			if (!empty($ids)) {
				$q->where( $colum_name, $sql, 'IN', false );
			}
		} 
	)
	->debug(true)
	->process( $_POST )
	->json();

	
	
