

(function($){

$(document).ready(function() {


  var fields = [
        //{
        //    label: 'fops_id:',
        //    name: 'fops_id',
        //},
        //{
        //    label: 'tickets_id:',
        //    name: 'tickets_id',
        //},
        //{
        //    label: 'passengers_id:',
        //    name: 'passengers_id',
        //},
        {
            label: 'fops_type:',
            name: 'fops_type',
        },
        //{
         //   label: 'fops_org:',
        //    name: 'fops_org',
        //},
        //{
        //    label: 'fops_docser:',
        //    name: 'fops_docser',
        //},
        {
            label: 'fops_docnum:',
            name: 'fops_docnum',
        },
        //{
        //    label: 'fops_auth_info_code:',
        //    name: 'fops_auth_info_code',
        //},
        //{
         //   label: 'fops_auth_info_currency:',
         //   name: 'fops_auth_info_currency',
        //},
        //{
        //    label: 'fops_auth_info_amount:',
         //   name: 'fops_auth_info_amount',
        //},
        //{
        //    label: 'fops_auth_info_provider:',
         //   name: 'fops_auth_info_provider',
        //},
        //{
        //    label: 'fops_auth_info_rrn:',
        //    name: 'fops_auth_info_rrn',
        //},
        //{
         //   label: 'fops_docinfo:',
         //   name: 'fops_docinfo',
        //},
        {
            label: 'fops_amount:',
            name: 'fops_amount',
        }
  ];



  if (role === 'superadmin') {
    fields.unshift(
      {
         label: 'fops_id:',
         name: 'fops_id',
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
            url: '..//php/fops.php',
            type: 'POST',
            data: function(d) {
                d.key = myVar; // Передаем значение ключа на сервер
                d.colum_name = colum_name;
            }
        },
    table: '#fops',    
    fields: fields
      
        
	} );

  // Создаем массив с кнопками
  var buttons = [
    { extend: 'collection', text: 'Экспорт', buttons: ['copy', 'excel', 'csv', 'pdf', 'print'] },
    { extend: 'pageLength' }, 'colvis',
    
  ];

  var columns = [
            //{ data: 'fops_id' },
            //{ data: 'tickets_id' },
            //{ data: 'passengers_id' },
            { data: 'fops_type' },
            //{ data: 'fops_org' },
            //{ data: 'fops_docser' },
            { data: 'fops_docnum' },
            //{ data: 'fops_auth_info_code' },
            //{ data: 'fops_auth_info_currency' },
            //{ data: 'fops_auth_info_amount' },
            //{ data: 'fops_auth_info_provider' },
            //{ data: 'fops_auth_info_rrn' },
            //{ data: 'fops_docinfo' },
            { data: 'fops_amount' }
        ];

  if (role === 'superadmin') {
    buttons.unshift(
      { extend: 'create', editor: editor },
      { extend: 'edit', editor: editor },
      { extend: 'remove', editor: editor }
    );

    columns.unshift(
      { data: 'fops_id' },
      { data: 'tickets_id' },
      { data: 'passengers_id' },
    );

  }



	var table = new DataTable('#fops', {
        language: {
          url: '/js/datatables/i18n/ru.json',
      },
        ajax: {
                url: '..//php/fops.php',
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

