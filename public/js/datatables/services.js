$(document).ready(function() {


    if (window.location.pathname === '/services') {

        
    var buttons = [
        {
            text: 'Создать',
            action: function (e, dt, node, config) {

                window.location.href = '/services/create';
            }
        },
        { extend: 'pageLength' },
        { extend: 'colvis' },
       
        
    ];



    // для acquiring
    function getNameAcq(value) {
        switch (value) {
            case 'dc':
                return 'Душанбе Сити';
            case 'alif':
                return 'Алиф';
            case 'eskhata':
                return 'Эсхата';
            case 'ibt':
                return 'IBT';
            default:
                return value;
        }
    }



    var table = $('#services').DataTable({
        language: {
          url: '/js/datatables/i18n/ru.json',
          "processing": '',
        },
        pagingType: 'simple',
        ajax: {
                url: '/services/get_services',
                type: 'POST',
                data: function(d) {
                    var formData = $('#myForm').serializeArray();
            
                    // Преобразуем массив formData в объект
                    formData.forEach(function(item) {
                        d[item.name] = item.value;
                    });

                    return d;
                },
                
        },
        buttons: buttons,
        dom: 'Bfrtip',
        processing: true,
        columns: [
    
            { "data": "create_date" },
            { "data": "doc_date" },
            { "data": "doc_number" },
            { "data": "service_name" },
            { "data": "amount" },
            { "data": "currency" },
            { "data": "method" },
            { "data": "bank" },

            ...(isAcquiring === '1' ? [{
                data: "acquiring",
                render: function (data) {
                    return getNameAcq(data);
                }
            }] : []),

            { "data": "note" },
            { "data": "doc_scan" },
            { "data": "name" },
            { "data": "value" },
            {
                data: null, // В этой колонке нет данных из таблицы
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <a href="/services/edit/${row.id}" class="btn btn-info btn-sm">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <a href="/services/delete/${row.id}" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i>
                        </a>
                    `;
                }
            }
        ],
        stateSave: false,
        select: false,
        serverSide: true,
        lengthMenu: [ [5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, 'Все'] ],
        pageLength: 5,
        scrollY: true,
        scrollCollapse: true,
        scrollX: true,
        drawCallback: function(row, data, start, end, display) {


            // services_downtable
            // DownTable();

        }
    });




    var downtable = $('#services_downtable').DataTable({
        language: {
          url: '/js/datatables/i18n/ru.json',
        },
        columns: [
            { "data": "name" },
            { "data": "value" },
            { 
                "data": "count", 
                "render": function (data) {
                    return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
                } 
            },
            { 
                "data": "summa", 
                "render": function (data) {
                    return parseFloat(data).toLocaleString('ru-RU', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                } 
            }
        
        ],
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




        function DownTable(){

                // загрузка
                showLoading();

                // Получаем данные формы
                var formData = $("#myForm").serialize();
                
                // console.log(formData);

                $.ajax({
                    type: 'POST',
                    url: '/services/get_downtable',
                    data: formData, 
                    success: function(response) {
                        
                        // console.log(response);


                        // Обновить данные в таблице
                        downtable.clear().draw();
                        downtable.rows.add(response).draw();

                        
                        $('#services_downtable tbody tr:last-child').addClass('bold-row');


                        // загрузка
                        hideLoading();
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error: ' + status + error);
                    }
                });
        }

  

        function getTableData(){

                // загрузка
                // showLoading();

                
                table.ajax.reload();


                DownTable();
                
                // загрузка
                // hideLoading();          
        }




        $('#submitBtn').click(function(){
            addDeleteClass();
            getTableData();            
        });

        $('#submitBtnToday').click(function(){

            addDeleteClass();

            document.getElementById('startDate').value = today;
            document.getElementById('endDate').value = today;
          
            getTableData();

            // Изменяем классы кнопок
            $(this).removeClass('btn-primary').addClass('bg-gradient-warning');
        });

        $('#submitBtnYesterday').click(function(){

            addDeleteClass();

            document.getElementById('startDate').value = yesterday;
            document.getElementById('endDate').value = yesterday;

            getTableData();
        

            // Изменяем классы кнопок
            $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
        });

        $('#submitBtnOneDecade').click(function(){

            addDeleteClass();

            document.getElementById('startDate').value = firstDecadeStart;
            document.getElementById('endDate').value = firstDecadeEnd;

            getTableData();

            // Изменяем классы кнопок
            $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
        });

        $('#submitBtnTwoDecade').click(function(){

            addDeleteClass();

            document.getElementById('startDate').value = secondDecadeStart;
            document.getElementById('endDate').value = secondDecadeEnd;

            getTableData();

            // Изменяем классы кнопок
            $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
        });

        $('#submitBtnThreeDecade').click(function(){

            addDeleteClass();

            document.getElementById('startDate').value = thirdDecadeStart;
            document.getElementById('endDate').value = thirdDecadeEnd;

            getTableData();

            // Изменяем классы кнопок
            $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
        });

        $('#submitBtnThisMonth').click(function(){

            addDeleteClass();

            document.getElementById('startDate').value = thisMonthFirst;
            document.getElementById('endDate').value = thisMonthLast;

            getTableData();

            // Изменяем классы кнопок
            $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
        });

        $('#submitBtnLastMonth').click(function(){

            addDeleteClass();

            document.getElementById('startDate').value = lastMonthFirst;
            document.getElementById('endDate').value = lastMonthLast;

            getTableData();

            // Изменяем классы кнопок
            $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
        });

        




        //проверяем установлены все опции
        const selectElement = document.getElementById('value_table');

        const checkOptionsLoaded = setInterval(function() {
            if (selectElement.options.length > 0) {
                // console.log('Опции установлены.');
                clearInterval(checkOptionsLoaded);  // Останавливаем проверку
                getTableData();

            }
        }, 1000);






    }




});
