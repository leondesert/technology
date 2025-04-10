

(function($){

$(document).ready(function() {


    var fields = [
        // {
        //     label: 'ID:',
        //     name: 'taxes_id',
        // },
        // {
        //     label: 'tickets_id:',
        //     name: 'tickets_id',
        // },
        // {
        //     label: 'passengers_id:',
        //     name: 'passengers_id',
        // },
        {
            label: 'segno:',
            name: 'segno',
        },
        {
            label: 'tax_code:',
            name: 'tax_code',
        },
        {
            label: 'tax_amount:',
            name: 'tax_amount',
        },
        {
            label: 'tax_namount:',
            name: 'tax_namount',
        },
        {
            label: 'tax_ncurrency:',
            name: 'tax_ncurrency',
        },
        {
            label: 'tax_nrate:',
            name: 'tax_nrate',
        },
        {
            label: 'tax_oamount:',
            name: 'tax_oamount',
        },
        {
            label: 'tax_ocurrency:',
            name: 'tax_ocurrency',
        },
        {
            label: 'tax_orate:',
            name: 'tax_orate',
        },
        {
            label: 'tax_oprate:',
            name: 'tax_oprate',
        },
        {
            label: 'tax_taxes_vat_amount:',
            name: 'tax_taxes_vat_amount',
        },
        {
            label: 'tax_taxes_vat_rate:',
            name: 'tax_taxes_vat_rate',
        },
        {
            label: 'tax_tax_vat_amount:',
            name: 'tax_tax_vat_amount',
        },
        {
            label: 'tax_tax_vat_rate:',
            name: 'tax_tax_vat_rate',
        },
        {
            label: 'exchanged:',
            name: 'exchanged',
        },
    ];

    if (role === 'superadmin') {
        fields.unshift(
            {
                label: 'taxes_id:',
                name: 'taxes_id',
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
            url: '..//php/taxes.php',
            type: 'POST',
            data: function(d) {
                d.key = myVar; // Передаем значение ключа на сервер
                d.colum_name = colum_name;
            }
        },
    table: '#taxes',    
    fields: fields
      
        
    } );

  // Создаем массив с кнопками
  var buttons = [
    { extend: 'collection', text: 'Экспорт', buttons: ['copy', 'excel', 'csv', 'pdf', 'print'] },
    { extend: 'pageLength' }, 'colvis',
    
  ];

  var columns = [
            // { data: 'taxes_id' },
            // { data: 'tickets_id' },
            // { data: 'passengers_id' },
            { data: 'segno' },
            { data: 'tax_code' },
            { data: 'tax_amount' },
            { data: 'tax_namount' },
            { data: 'tax_ncurrency' },
            { data: 'tax_nrate' },
            { data: 'tax_oamount' },
            { data: 'tax_ocurrency' },
            { data: 'tax_orate' },
            { data: 'tax_oprate' },
            { data: 'tax_taxes_vat_amount' },
            { data: 'tax_taxes_vat_rate' },
            { data: 'tax_tax_vat_amount' },
            { data: 'tax_tax_vat_rate' },
            { data: 'exchanged' },
        ];

  if (role === 'superadmin') {
    buttons.unshift(
      { extend: 'create', editor: editor },
      { extend: 'edit', editor: editor },
      { extend: 'remove', editor: editor }
    );

    columns.unshift(
      { data: 'taxes_id' },
      { data: 'tickets_id' },
      { data: 'passengers_id' },
    );

  }



    var table = new DataTable('#taxes', {
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ru.json',
      },
        ajax: {
                url: '..//php/taxes.php',
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

