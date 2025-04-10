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

// Build our Editor instance and process the data coming from _POST
Editor::inst( $db, 'tickets', 'tickets_id' )
	->fields(
		Field::inst('tickets_id'),
		Field::inst('passengers_id'),
		Field::inst('opr_id'),
		Field::inst('tap_id'),
		Field::inst('stamp_id'),
		Field::inst('agency_id'),
		Field::inst('tickets_type'),
		// Field::inst('tickets_system_id')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_system_session')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_system_bso_id')->searchBuilderOptions( SearchBuilderOptions::inst()),
		Field::inst('tickets_currency'),
		Field::inst('tickets_dealdate'),
		Field::inst('tickets_dealtime'),
		Field::inst('tickets_OPTYPE'),
		Field::inst('tickets_TRANS_TYPE'),
		// Field::inst('tickets_MCO_TYPE')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_MCO_TYPE_rfic')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_MCO_TYPE_rfisc')->searchBuilderOptions( SearchBuilderOptions::inst()),
		Field::inst('tickets_BSONUM'),
		Field::inst('tickets_EX_BSONUM'),
		// Field::inst('tickets_GENERAL_CARRIER')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_RETTYPE')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_TOURCODE')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_OCURRENCY')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_ORATE')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_NCURRENCY')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_NRATE')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_OPRATE')->searchBuilderOptions( SearchBuilderOptions::inst()),
		Field::inst('tickets_FARE'),
		// Field::inst('tickets_FARE_type')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_FARE_vat_amount')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_FARE_vat_rate')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_OFARE')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_PENALTY')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_FARECALC')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_ENDORS_RESTR')->searchBuilderOptions( SearchBuilderOptions::inst()),
		//Field::inst('tickets_PNR')->searchBuilderOptions( SearchBuilderOptions::inst()),
		Field::inst('tickets_PNR_LAT'),
		// Field::inst('tickets_INV_PNR')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_CONJ')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_TO_BSONUM')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_TYP_NUM_ser')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_FCMODE')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_COMMISSION_type')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_COMMISSION_currency')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_COMMISSION_amount')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_COMMISSION_rate')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_BOOK_date')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_BOOK_disp')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_BOOK_time')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_BOOK_utc')->searchBuilderOptions( SearchBuilderOptions::inst()),
		Field::inst('tickets_DEAL_date'),
		Field::inst('tickets_DEAL_disp'),
		Field::inst('tickets_DEAL_time'),
		Field::inst('tickets_DEAL_utc'),
		// Field::inst('tickets_DEAL_ersp')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_DEAL_pcc')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_SALE_date')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_SALE_disp')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_SALE_time')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_SALE_utc')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_AGN_INFO_CLIENT_NUM')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_AGN_INFO_RESERV_NUM')->searchBuilderOptions( SearchBuilderOptions::inst()),
		// Field::inst('tickets_AGN_INFO_INFO')->searchBuilderOptions( SearchBuilderOptions::inst()),
		Field::inst('ticket_exchanged')
		
	)
	->where(
		function ( $q ) use ( $colum_name, $ids ){
			if (!empty($ids)) {
				$q->where( $colum_name, $ids, 'IN', false );
			}
		} 
	)
	->debug(true)
	->process( $_POST )
	->json();

	
	
