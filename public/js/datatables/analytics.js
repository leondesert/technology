$(document).ready(function() {

    // кнопки 
    var buttons = [
        
        { extend: 'pageLength' },
        { extend: 'colvis' },
        {
            extend: 'collection',
            autoClose: true,
            text: 'Экспорт',
            buttons: [
                { extend: 'copy', filename: 'Доход' },
                { extend: 'excel', filename: 'Доход' },
                { extend: 'csv', filename: 'Доход' },
                { extend: 'pdf', filename: 'Доход' },
                'print'
            ]
        },
    ];

    // таблица
    var table = $('#analytics').DataTable({
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ru.json',
        },
        columns: [
            { "data": "date" },
            { "data": "dohod" },
            { "data": "sale" },
            { "data": "currency" },
            { "data": "name_table" },
            { "data": "value_table" },
            
            
        ],
        buttons: buttons,
        dom: 'Bfrtip',
        stateSave: true,
        select: false,
        serverSide: false,
        lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"] ],
        pageLength: 10,
        scrollY: true,
        scrollCollapse: true,
        scrollX: true
    });


    


    if (window.location.pathname === '/analytics') {


        function chart(labels, dohod, sale){
            var ctx = document.getElementById('line-chart-dohod').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Доход',
                            data: dohod,
                            borderWidth: 3,
                            borderColor: '#23c8f1',
                            backgroundColor: 'rgba(153, 230, 249, 0.5)',
                        },
                        {
                            label: 'Продажа',
                            data: sale,
                            borderWidth: 3,
                            borderColor: '#23f18b',
                            backgroundColor: 'rgba(153, 249, 203, 0.5)',
                        },
                        
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
                
                console.log(formData);

                $.ajax({
                    type: 'POST',
                    url: '/analytics/getdatatable',
                    data: formData, 
                    success: function(response) {
                        
                        console.log(response);

                        // Обновить данные в таблице
                        table.clear().draw();
                        table.rows.add(response.table_data).draw();

                        // сумма дохода
                        document.getElementById('summa_dohoda').innerHTML = 'Сумма дохода: ' + response.totalAmount + ' ' + response.currency;

                        // Обновить график
                        chart(response.labels, response.dohod, response.sale);

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



    }
    


    
    


});
