$(document).ready(function() {

if (window.location.pathname === '/logs') {

    var buttons = [
        
        { extend: 'pageLength' },
        // { extend: 'colvis' },
        {
            extend: 'collection',
            autoClose: true,
            text: 'Экспорт',
            buttons: [
                { extend: 'copy', filename: 'Логи' },
                { extend: 'excel', filename: 'Логи' },
                { extend: 'csv', filename: 'Логи' },
                { extend: 'pdf', filename: 'Логи' },
                'print'
            ]
        }
    ];

    
    // таблица
    var table = $('#logs').DataTable({
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ru.json',
        },
        ajax: {
                url: '/logs/getData',
                type: 'POST',
                data: function(d) {

                },
                
        },
        columns: [

            // <th>Дата</th>
            // <th>Время</th>
            // <th>Логин</th>
            // <th>IP-адресс</th>
            // <th>Действие</th>
            // <th>Данные</th>

            { data: 'log_date' },
            { data: 'log_time' },
            { data: 'user_id' },
            { data: 'ip_address' },
            { data: 'action' },
            {
                data: null, // В этой колонке нет данных из таблицы
                orderable: false,
                render: function(data, type, row) {

                    return `
                        <button class="btn btn-info view-btn" data-id="${row.id}">Посмотреть</button>

                    `;
                }
            }
        ],
        processing: true,
        pagingType: 'simple',
        buttons: buttons,
        dom: 'Bfrtip',
        stateSave: false,
        select: false,
        serverSide: true,
        lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"] ],
        pageLength: 10,
        scrollY: true,
        scrollCollapse: true,
        scrollX: true,
        // order: [[0, "desc"], [1, "desc"]],
        
   

    });

    




    // CodeMirror
    var jsonEditor = CodeMirror.fromTextArea(document.getElementById('jsonTextArea'), {
      lineNumbers: false,
      mode: "htmlmixed",
      theme: "monokai",
      readOnly: true,
      // autoRefresh: true,
    });
        

    // Функция для отображения данных в модальном окне
    function showModalWithData(data) {

        console.log(data);

        var jsonString = JSON.stringify(data, null, 2);


        // Устанавливаем данные в текстовое поле
        jsonEditor.setValue(jsonString);


        // Обновляем содержимое редактора
        setTimeout(function () {
                jsonEditor.refresh();
                // console.log('1 sec');
            }, 200)


        // Открываем модальное окно
        $('#jsonModal').modal('show');

        
    }




    // "Показать"
    $('#logs').on('click', '.view-btn', function() {

    // console.log('view-btn');

    var id = $(this).data('id');


    $.ajax({
        url: '/logs/getById',
        method: 'POST',
        data: {
            id: id
        },
        success: function(data) {

            console.log(data);

            // let data = JSON.parse(data);
            // let jsonString = JSON.stringify(data);

            
            showModalWithData(data);

        },
        error: function(error) {
            console.log("error: ", error);
        }
    });
    });

      




















}

});
