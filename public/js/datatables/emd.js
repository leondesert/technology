$(document).ready(function() {
  var fields = [
      //{
      //    label: 'emd_id:',
      //    name: 'emd_id',
      //},
      //{
      //    label: 'tickets_id:',
      //    name: 'tickets_id',
      //},
      //{
      //    label: 'passengers_id:',
      //    name: 'passengers_id',
      //},
      //{
      //    label: 'emd_coupon_no:',
      //    name: 'emd_coupon_no',
      //},
      {
          label: 'Суммма EMD:',
          name: 'emd_value',
      }
      //{
       //   label: 'emd_remark:',
       //   name: 'emd_remark',
      //},
      //{
      //    label: 'emd_related:',
      //    name: 'emd_related',
      //},
      //{
      //    label: 'emd_reason_rfisc:',
      //    name: 'emd_reason_rfisc',
      //},
      //{
      //    label: 'emd_reason_airline:',
       //   name: 'emd_reason_airline',
      //},
      //{
      //    label: 'emd_xbaggage_number:',
      //    name: 'emd_xbaggage_number',
      //},
      //{
      //    label: 'emd_xbaggage_qualifier:',
      //    name: 'emd_xbaggage_qualifier',
      //},
      //{
      //    label: 'emd_xbaggage_rpu:',
      //    name: 'emd_xbaggage_rpu',
      //},
      //{
      //    label: 'emd_xbaggage_currency:',
      //    name: 'emd_xbaggage_currency',
      //},
  ];

  if (role === 'superadmin') {
    fields.unshift(
      {
         label: 'emd_id:',
         name: 'emd_id',
      },
      {
         label: 'tickets_id:',
         name: 'tickets_id',
      },
      {
         label: 'passengers_id:',
         name: 'passengers_id',
      },

    );
  }

	var editor = new DataTable.Editor( {
		ajax: {
            url: '..//php/emd.php',
            type: 'POST',
            data: function(d) {
                d.key = myVar; // Передаем значение ключа на сервер
                d.colum_name = colum_name;
            }
        },
    table: '#emd',    
    fields: fields
        
	} );

  // Создаем массив с кнопками
  var buttons = [
    { extend: 'collection', text: 'Экспорт', buttons: ['copy', 'excel', 'csv', 'pdf', 'print'] },
    { extend: 'pageLength' }, 'colvis',
    
  ];


  var columns = [
          //{ data: 'emd_id' },
          //{ data: 'tickets_id' },
          //{ data: 'passengers_id' },
          //{ data: 'emd_coupon_no' },
          { data: 'emd_value' }
          //{ data: 'emd_remark' },
         // { data: 'emd_related' },
          //{ data: 'emd_reason_rfisc' },
          //{ data: 'emd_reason_airline' },
          //{ data: 'emd_xbaggage_number' },
          //{ data: 'emd_xbaggage_qualifier' },
          //{ data: 'emd_xbaggage_rpu' },
          //{ data: 'emd_xbaggage_currency' },
  ];


  if (role === 'superadmin') {
    buttons.unshift(
      { extend: 'create', editor: editor },
      { extend: 'edit', editor: editor },
      { extend: 'remove', editor: editor }
    );


    columns.unshift(
      { data: 'emd_id' },
      { data: 'tickets_id' },
      { data: 'passengers_id' },
    );
  }


	var table = $('#emd').DataTable({
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ru.json',
      },
        ajax: {
                url: '..//php/emd.php',
                type: 'POST',
                data: function(d) {
                    d.key = myVar; // Передаем значение ключа на сервер
                    d.colum_name = colum_name;
                }
            },
        buttons: buttons,
        columns: columns,
        dom: 'QBfrtip',
        stateSave: false,
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