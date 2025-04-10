

(function($){

$(document).ready(function() {

    var fields = [
       // {
       //     label: 'segments_id:',
        //    name: 'segments_id',
       // },
       // {
       //     label: 'tickets_id:',
       //     name: 'tickets_id',
       // },
       // {
       //     label: 'passengers_id:',
       //     name: 'passengers_id',
       // },
       // {
       //     label: 'segno:',
       //     name: 'segno',
       // },
        {
            label: 'Маршрут:',
            name: 'citycodes',
        },
       // {
       //     label: 'portcodes:',
       //     name: 'portcodes',
       // },
        {
            label: 'Перевозчик:',
            name: 'carrier',
        },
        {
            label: 'Класс:',
            name: 'class',
        },
        {
            label: 'Рейс:',
            name: 'reis',
        },
        {
            label: 'Дата полёта:',
            name: 'flydate',
        },
        {
            label: 'Время полёта:',
            name: 'flytime',
        },
        {
            label: 'Код тарифа:',
            name: 'basicfare',
        }
        //{
        //    label: 'seg_bsonum:',
        //    name: 'seg_bsonum',
        //},
        //{
        //    label: 'coupon_no:',
        //    name: 'coupon_no',
        //},
        //{
         //   label: 'is_void:',
         //   name: 'is_void',
        //},
        //{
        //    label: 'stpo:',
        //    name: 'stpo',
        //},
        //{
        //    label: 'term1:',
        //    name: 'term1',
        //},
        //{
        //    label: 'term2:',
        //    name: 'term2',
        //},
        //{
        //    label: 'arrdate:',
         //   name: 'arrdate',
        //},
        //{
        //    label: 'arrtime:',
        //    name: 'arrtime',
        //},
        //{
        //    label: 'nfare:',
        //    name: 'nfare',
        //},
        //{
        //    label: 'baggage_number:',
        //    name: 'baggage_number',
        //},
        //{
        //    label: 'baggage_qualifier:',
        //    name: 'baggage_qualifier',
        //},
        //{
        //    label: 'ffp_info_number:',
        //    name: 'ffp_info_number',
        //},
        //{
        //    label: 'ffp_info_certificate:',
        //    name: 'ffp_info_certificate',
        //},
        //{
        //    label: 'exchanged:',
        //    name: 'exchanged',
        //},
    ];



    if (role === 'superadmin') {
        fields.unshift(
           {
               label: 'segments_id:',
               name: 'segments_id',
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
            url: '..//php/segments.php',
            type: 'POST',
            data: function(d) {
                d.key = myVar; // Передаем значение ключа на сервер
                d.colum_name = colum_name;
            }
        },
    table: '#segments',    
    fields: fields


    } );

  // Создаем массив с кнопками
  var buttons = [
    { extend: 'collection', text: 'Экспорт', buttons: ['copy', 'excel', 'csv', 'pdf', 'print'] },
    { extend: 'pageLength' }, 'colvis',
    
  ];

  var columns = [
            //{ data: 'segments_id' },
            //{ data: 'tickets_id' },
            //{ data: 'passengers_id' },
            //{ data: 'segno' },
            { data: 'citycodes' },
            //{ data: 'portcodes' },
            { data: 'carrier' },
            { data: 'class' },
            { data: 'reis' },
            { data: 'flydate' },
            { data: 'flytime' },
            { data: 'basicfare' }
            //{ data: 'seg_bsonum' },
            //{ data: 'coupon_no' },
            //{ data: 'is_void' },
            //{ data: 'stpo' },
            //{ data: 'term1' },
            //{ data: 'term2' },
            //{ data: 'arrdate' },
            //{ data: 'arrtime' },
            //{ data: 'nfare' },
            //{ data: 'baggage_number' },
            //{ data: 'baggage_qualifier' },
            //{ data: 'ffp_info_number' },
            //{ data: 'ffp_info_certificate' },
            //{ data: 'exchanged' },
        ];


  if (role === 'superadmin') {
    buttons.unshift(
      { extend: 'create', editor: editor },
      { extend: 'edit', editor: editor },
      { extend: 'remove', editor: editor }
    );

    columns.unshift(
        { data: 'segments_id' },
        { data: 'tickets_id' },
        { data: 'passengers_id' },
    );

  }



    var table = new DataTable('#segments', {
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ru.json',
      },
        ajax: {
                url: '..//php/segments.php',
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

