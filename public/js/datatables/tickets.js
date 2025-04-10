

(function($){

$(document).ready(function() {


  var fields = [
            // {
            //   label: 'tickets_id:',
            //   name: 'tickets_id',
            // },
            // {
            //   label: 'passengers_id:',
            //   name: 'passengers_id',
            // },
            // {
            //   label: 'opr_id:',
            //   name: 'opr_id',
            // },
            // {
            //   label: 'tap_id:',
            //   name: 'tap_id',
            // },
            // {
            //   label: 'stamp_id:',
            //   name: 'stamp_id',
            // },
            // {
            //   label: 'agency_id:',
            //   name: 'agency_id',
            // },
            {
              label: 'Тип билета:',
              name: 'tickets_type',
            },
            //{
            //  label: 'tickets_system_id:',
            //  name: 'tickets_system_id',
            //},
            //{
            //  label: 'tickets_system_session:',
            //  name: 'tickets_system_session',
            //},
            //{
            //  label: 'tickets_system_bso_id:',
            //  name: 'tickets_system_bso_id',
            //},
            {
              label: 'Валюта билета:',
              name: 'tickets_currency',
            },
            {
              label: 'Дата формирования:',
              name: 'tickets_dealdate',
            },
            {
              label: 'Время формирования:',
              name: 'tickets_dealtime',
            },
            {
              label: 'Тип операции:',
              name: 'tickets_OPTYPE',
            },
            {
              label: 'Тип транзакции:',
              name: 'tickets_TRANS_TYPE',
            },
            //{
            //  label: 'tickets_MCO_TYPE:',
            //  name: 'tickets_MCO_TYPE',
            //},
            //{
            //  label: 'tickets_MCO_TYPE_rfic:',
            //  name: 'tickets_MCO_TYPE_rfic',
            //},
            //{
            //  label: 'tickets_MCO_TYPE_rfisc:',
            //  name: 'tickets_MCO_TYPE_rfisc',
            //},
            {
              label: 'Номер билета:',
              name: 'tickets_BSONUM',
            },
            {
              label: 'Номер старшего билета:',
              name: 'tickets_EX_BSONUM',
            },
            //{
            //  label: 'tickets_GENERAL_CARRIER:',
            //  name: 'tickets_GENERAL_CARRIER',
            //},
            //{
            // label: 'tickets_RETTYPE:',
            //  name: 'tickets_RETTYPE',
            //},
            //{
             // label: 'tickets_TOURCODE:',
             // name: 'tickets_TOURCODE',
            //},
            //{
            //  label: 'tickets_OCURRENCY:',
             // name: 'tickets_OCURRENCY',
            //},
            //{
            //  label: 'tickets_ORATE:',
            //  name: 'tickets_ORATE',
            //},
            //{
             // label: 'tickets_NCURRENCY:',
            //  name: 'tickets_NCURRENCY',
            //},
            //{
            //  label: 'tickets_NRATE:',
            //  name: 'tickets_NRATE',
            //},
            //{
            //  label: 'tickets_OPRATE:',
            //  name: 'tickets_OPRATE',
            //},
            {
              label: 'Тариф:',
              name: 'tickets_FARE',
            },
            //{
            //  label: 'tickets_FARE_type:',
            //  name: 'tickets_FARE_type',
            //},
            //{
            //  label: 'tickets_FARE_vat_amount:',
            //  name: 'tickets_FARE_vat_amount',
            //},
            //{
            //  label: 'tickets_FARE_vat_rate:',
            //  name: 'tickets_FARE_vat_rate',
            //},
            //{
            //  label: 'tickets_OFARE:',
            //  name: 'tickets_OFARE',
            //},
            //{
            //  label: 'tickets_PENALTY:',
            //  name: 'tickets_PENALTY',
            //},
            //{
             // label: 'tickets_FARECALC:',
            //  name: 'tickets_FARECALC',
            //},
            //{
            //  label: 'tickets_ENDORS_RESTR:',
            //  name: 'tickets_ENDORS_RESTR',
            //},
            //{
             // label: 'tickets_PNR:',
            //  name: 'tickets_PNR',
            //},
            {
              label: 'PNR:',
              name: 'tickets_PNR_LAT',
            },
            //{
            //  label: 'tickets_INV_PNR:',
            //  name: 'tickets_INV_PNR',
            //},
            //{
            //  label: 'tickets_CONJ:',
            //  name: 'tickets_CONJ',
            //},
            //{
            //  label: 'tickets_TO_BSONUM:',
            //  name: 'tickets_TO_BSONUM',
            //},
            //{
            //  label: 'tickets_TYP_NUM_ser:',
            //  name: 'tickets_TYP_NUM_ser',
            //},
            //{
            //  label: 'tickets_FCMODE:',
            //  name: 'tickets_FCMODE',
            //},
            //{
            //  label: 'tickets_COMMISSION_type:',
             // name: 'tickets_COMMISSION_type',
            //},
            //{
            //  label: 'tickets_COMMISSION_currency:',
            //  name: 'tickets_COMMISSION_currency',
            //},
            //{
            //  label: 'tickets_COMMISSION_amount:',
            //  name: 'tickets_COMMISSION_amount',
            //},
            //{
            //  label: 'tickets_COMMISSION_rate:',
            //  name: 'tickets_COMMISSION_rate',
            //},
            //{
            //  label: 'tickets_BOOK_date:',
            //  name: 'tickets_BOOK_date',
            //},
            //{
            //  label: 'tickets_BOOK_disp:',
            //  name: 'tickets_BOOK_disp',
            //},
            //{
            //  label: 'tickets_BOOK_time:',
            //  name: 'tickets_BOOK_time',
            //},
            //{
            //  label: 'tickets_BOOK_utc:',
            //  name: 'tickets_BOOK_utc',
            //},
            {
              label: 'Дата оформленя:',
              name: 'tickets_DEAL_date',
            },
            {
              label: 'Индентификатор продавца:',
              name: 'tickets_DEAL_disp',
            },
            {
              label: 'Время оформления:',
              name: 'tickets_DEAL_time',
            },
            {
              label: 'Время покупки UTC:',
              name: 'tickets_DEAL_utc',
            },
            //{
            //  label: 'tickets_DEAL_ersp:',
            //  name: 'tickets_DEAL_ersp',
            //},
            //{
            //  label: 'tickets_DEAL_pcc:',
            //  name: 'tickets_DEAL_pcc',
            //},
            //{
            //  label: 'tickets_SALE_date:',
            //  name: 'tickets_SALE_date',
            //},
            //{
             // label: 'tickets_SALE_disp:',
             // name: 'tickets_SALE_disp',
            //},
            //{
            //  label: 'tickets_SALE_time:',
            //  name: 'tickets_SALE_time',
            //},
            //{
             // label: 'tickets_SALE_utc:',
            //  name: 'tickets_SALE_utc',
            //},
            //{
             // label: 'tickets_AGN_INFO_CLIENT_NUM:',
             // name: 'tickets_AGN_INFO_CLIENT_NUM',
            //},
            //{
            //  label: 'tickets_AGN_INFO_RESERV_NUM:',
            //  name: 'tickets_AGN_INFO_RESERV_NUM',
            //},
            //{
            //  label: 'tickets_AGN_INFO_INFO:',
            //  name: 'tickets_AGN_INFO_INFO',
            //},
            {
              label: 'Номер билета возврата:',
              name: 'ticket_exchanged',
            }
          ];

  if (role === 'superadmin') {
      fields.unshift(
            {
              label: 'tickets_id:',
              name: 'tickets_id',
            },
            {
              label: 'passengers_id:',
              name: 'passengers_id',
            },
            {
              label: 'opr_id:',
              name: 'opr_id',
            },
            {
              label: 'tap_id:',
              name: 'tap_id',
            },
            {
              label: 'stamp_id:',
              name: 'stamp_id',
            },
            {
              label: 'agency_id:',
              name: 'agency_id',
            },
      );
  }


	var editor = new DataTable.Editor( {
		ajax: {
            url: '..//php/tickets.php',
            type: 'POST',
            data: function(d) {
                d.key = myVar; // Передаем значение ключа на сервер
                d.colum_name = colum_name;
            }
        },
    table: '#tickets',    
    fields: fields    
        
	} );

  // Создаем массив с кнопками
  var buttons = [
    { extend: 'collection', text: 'Экспорт', buttons: ['copy', 'excel', 'csv', 'pdf', 'print'] },
    { extend: 'pageLength' }, 'colvis',
    
  ];

  var columns = [
            // { data: 'tickets_id' },
            // { data: 'passengers_id' },
            // { data: 'opr_id' },
            // { data: 'tap_id' },
            // { data: 'stamp_id' },
            // { data: 'agency_id' },
            { data: 'tickets_type' },
            //{ data: 'tickets_system_id' },
            //{ data: 'tickets_system_session' },
            //{ data: 'tickets_system_bso_id' },
            { data: 'tickets_currency' },
            { data: 'tickets_dealdate' },
            { data: 'tickets_dealtime' },
            { data: 'tickets_OPTYPE' },
            { data: 'tickets_TRANS_TYPE' },
            //{ data: 'tickets_MCO_TYPE' },
            //{ data: 'tickets_MCO_TYPE_rfic' },
            //{ data: 'tickets_MCO_TYPE_rfisc' },
            { data: 'tickets_BSONUM' },
            { data: 'tickets_EX_BSONUM' },
            //{ data: 'tickets_GENERAL_CARRIER' },
            //{ data: 'tickets_RETTYPE' },
            //{ data: 'tickets_TOURCODE' },
            //{ data: 'tickets_OCURRENCY' },
            //{ data: 'tickets_ORATE' },
            //{ data: 'tickets_NCURRENCY' },
            //{ data: 'tickets_NRATE' },
            //{ data: 'tickets_OPRATE' },
            { data: 'tickets_FARE' },
            //{ data: 'tickets_FARE_type' },
            //{ data: 'tickets_FARE_vat_amount' },
            //{ data: 'tickets_FARE_vat_rate' },
            //{ data: 'tickets_OFARE' },
            //{ data: 'tickets_PENALTY' },
            //{ data: 'tickets_FARECALC' },
            //{ data: 'tickets_ENDORS_RESTR' },
            //{ data: 'tickets_PNR' },
            { data: 'tickets_PNR_LAT' },
            //{ data: 'tickets_INV_PNR' },
            //{ data: 'tickets_CONJ' },
            //{ data: 'tickets_TO_BSONUM' },
            //{ data: 'tickets_TYP_NUM_ser' },
            //{ data: 'tickets_FCMODE' },
            //{ data: 'tickets_COMMISSION_type' },
            //{ data: 'tickets_COMMISSION_currency' },
            //{ data: 'tickets_COMMISSION_amount' },
            //{ data: 'tickets_COMMISSION_rate' },
            //{ data: 'tickets_BOOK_date' },
            //{ data: 'tickets_BOOK_disp' },
            //{ data: 'tickets_BOOK_time' },
            //{ data: 'tickets_BOOK_utc' },
            { data: 'tickets_DEAL_date' },
            { data: 'tickets_DEAL_disp' },
            { data: 'tickets_DEAL_time' },
            { data: 'tickets_DEAL_utc' },
            //{ data: 'tickets_DEAL_ersp' },
            //{ data: 'tickets_DEAL_pcc' },
            //{ data: 'tickets_SALE_date' },
            //{ data: 'tickets_SALE_disp' },
            //{ data: 'tickets_SALE_time' },
            //{ data: 'tickets_SALE_utc' },
            //{ data: 'tickets_AGN_INFO_CLIENT_NUM' },
            //{ data: 'tickets_AGN_INFO_RESERV_NUM' },
            //{ data: 'tickets_AGN_INFO_INFO' },
            { data: 'ticket_exchanged' }
          ];


  if (role === 'superadmin') {
    buttons.unshift(
      { extend: 'create', editor: editor },
      { extend: 'edit', editor: editor },
      { extend: 'remove', editor: editor }
    );

    columns.unshift(
            { data: 'tickets_id' },
            { data: 'passengers_id' },
            { data: 'opr_id' },
            { data: 'tap_id' },
            { data: 'stamp_id' },
            { data: 'agency_id' },
    );

  }



	var table = new DataTable('#tickets', {
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ru.json',
      },
        ajax: {
                url: '..//php/tickets.php',
                type: 'POST',
                data: function(d) {
                    d.key = myVar; // Передаем значение ключа на сервер
                    d.colum_name = colum_name;
                }
            },
        buttons: buttons,
        columns: columns,
        dom: 'QBfrtip',
        stateSave: true,
        select: true,
        serverSide: true,
        pagingType: 'simple',
        lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
        pageLength: 10,
        scrollY: true,
        scrollCollapse: true,
        scrollX: true
	} );



} );

}(jQuery));

