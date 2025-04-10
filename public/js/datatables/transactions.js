$(document).ready(function() {


    if (window.location.pathname === '/transactions') {

        
    var buttons = [
        {
            text: 'Создать',
            action: function (e, dt, node, config) {

                window.location.href = '/transactions/create';
            }
        },
        { extend: 'pageLength' },
        { extend: 'colvis' },
        {
            text: 'Экспорт Excel',
            action: function (e, dt, node, config) {

                var user_login = document.getElementById("user_login").value;
                var startDate = document.getElementById("startDate").value;
                var endDate = document.getElementById("endDate").value;
                var name_table = document.getElementById("name_table").value;
                var value_table = document.getElementById("value_table").value;
                var currency = document.getElementById("currency").value;

                // console.log(user_login);
                
                // Формируем строку запроса
                var queryString = '?user_login=' + encodeURIComponent(user_login) +
                                  '&startDate=' + encodeURIComponent(startDate) +
                                  '&endDate=' + encodeURIComponent(endDate) +
                                  '&name_table=' + encodeURIComponent(name_table) +
                                  '&value_table=' + encodeURIComponent(value_table) +
                                  '&currency=' + encodeURIComponent(currency);

                // Составляем полный URL с параметрами запроса
                var url = '/transactions/export' + queryString;

                // console.log(url);

                $.ajax({
                    url: url,
                    method: 'GET',
                    xhrFields: {
                        responseType: 'blob' // Для получения файла
                    },
                    success: function(response) {
                        // Код для обработки ответа и скачивания файла
                        var blob = new Blob([response]);
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = "export.xlsx";
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    },
                    error: function(xhr, status, error) {
                        console.log(error);
                        
                    },
                    
                    
                    }); 



            }
        }
        
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



    var table = $('#transactions').DataTable({
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ru.json',
        },
        columns: [
            { "data": "creation_date" },
            { "data": "payment_date" },
            { "data": "receipt_number" },
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
            { "data": "receipt_photo" },
            { "data": "name" },
            { "data": "value" },
            { "data": "action" },
        ],
        buttons: buttons,
        dom: 'QBfrtip',
        stateSave: true,
        select: false,
        serverSide: false,
        lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"] ],
        pageLength: 10,
        scrollY: true,
        scrollCollapse: true,
        scrollX: true
    });



    

    var downtable = $('#trans_downtable').DataTable({
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ru.json',
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




    


        function chart(labels, amounts){
            var ctx = document.getElementById('line-chart').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Агенство',
                            data: amounts.agency,
                            borderWidth: 3,
                            borderColor: '#23c8f1',
                            backgroundColor: 'rgba(153, 230, 249, 0.5)',
                        },
                        {
                            label: 'ППР',
                            data: amounts.stamp,
                            borderWidth: 3,
                            borderColor: '#f15854',
                            backgroundColor: 'rgba(241, 88, 84, 0.5)',
                        },
                        {
                            label: 'Пульт',
                            data: amounts.tap,
                            borderWidth: 3,
                            borderColor: '#64b5f6',
                            backgroundColor: 'rgba(100, 181, 246, 0.5)',
                        },
                        {
                            label: 'Оператор',
                            data: amounts.opr,
                            borderWidth: 3,
                            borderColor: '#66bb6a',
                            backgroundColor: 'rgba(102, 187, 106, 0.5)',
                        }
                    ]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        }

        function getTableData(){

                // загрузка
                showLoading();

                // Получаем данные формы
                var formData = $("#myForm").serialize();
                
                // console.log(formData);

                $.ajax({
                    type: 'POST',
                    url: '/transactions/get_data_trans',
                    data: formData, 
                    success: function(response) {
                        
                        // console.log(response);

                        // Обновить данные в таблице
                        table.clear().draw();
                        table.rows.add(response.transactions).draw();


                        // Обновить данные в таблице
                        downtable.clear().draw();
                        downtable.rows.add(response.downTable).draw();


                    
                        
                        $('#trans_downtable tbody tr:last-child').addClass('bold-row');



                        // Обновить график
                        chart(response.labels, response.amounts);

                        // загрузка
                        hideLoading();
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error: ' + status + error);
                    }
                });
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

        // при обновлении стр
        let intervalId;
        intervalId = setInterval(() => {

            let is_update = document.getElementById('is_update').value;
            // console.log('5000', is_update);

            if (is_update === 'yes') {
                clearInterval(intervalId);
                getTableData();
            }
        }, 500);


        // показать чек
        $('#transactions').on('click', '.showValue', function() {
            
            var value = $(this).attr('value');

            // console.log(value);

            // Construct the path to the image
            var imagePath = '/uploads/checks/' + value;

            // Set the image src attribute
            $('#modalImage').attr('src', imagePath);

            // Show the modal
            $('#imageModal').modal('show');


        });





        







    }












});
