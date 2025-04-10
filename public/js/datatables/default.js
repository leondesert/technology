$(document).ready(function() {
    var buttons = [
        { extend: 'pageLength' }, 
        // 'colvis',
        { 
            extend: 'collection', 
            text: 'Экспорт', 
            buttons: ['copy', 'excel', 'csv', 'pdf', 'print'] 
        },
        
    ];

    var table = $('#default').DataTable({
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ru.json',
        },
        buttons: buttons,
        dom: 'Bfrtip',
        stateSave: false,
        select: false,
        serverSide: false,
        lengthMenu: [ [5, 25, 50, 100, -1], [5, 25, 50, 100, "Все"] ],
        pageLength: 5,
        scrollY: true,
        scrollCollapse: true,
        scrollX: true
    });
});
