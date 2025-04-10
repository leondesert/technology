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
Editor::inst( $db, 'fops', 'fops_id' )
	->fields(
		Field::inst('fops_id'),
		Field::inst('tickets_id'),
		Field::inst('passengers_id'),
		Field::inst('fops_type'),
		//Field::inst('fops_org')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('fops_docser')->searchBuilderOptions( SearchBuilderOptions::inst()),
		Field::inst('fops_docnum'),
		//Field::inst('fops_auth_info_code')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('fops_auth_info_currency')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('fops_auth_info_amount')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('fops_auth_info_provider')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('fops_auth_info_rrn')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('fops_docinfo')->searchBuilderOptions( SearchBuilderOptions::inst()),
		Field::inst('fops_amount')

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

	
	
