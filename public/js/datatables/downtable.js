$(document).ready(function() {
    var table = $('#downtable').DataTable({
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ru.json',
        },
        dom: 't',
        stateSave: false,
        ordering: false, 
        select: false,
        serverSide: false,
        lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"] ],
        pageLength: 10,
        scrollY: true,
        scrollCollapse: true,
        scrollX: true
    });
});
