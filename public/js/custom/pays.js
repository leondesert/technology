$(document).ready(function() {

if (window.location.pathname === '/pays') {


    function showLoading() {
      var overlays = document.getElementsByClassName('overlay');
      for (var i = 0; i < overlays.length; i++) {
          overlays[i].classList.remove('d-none');
      }
    }

    function hideLoading() {
      var overlays = document.getElementsByClassName('overlay');
      for (var i = 0; i < overlays.length; i++) {
          overlays[i].classList.add('d-none');
      }     
    }

    function addDeleteClass() {
        $('#submitBtnToday, #submitBtnYesterday, #submitBtnOneDecade, #submitBtnTwoDecade, #submitBtnThreeDecade, #submitBtnThisMonth, #submitBtnLastMonth').removeClass('bg-gradient-warning').addClass('bg-gradient-primary');
    }



    var buttons = [
        
        { extend: 'pageLength' },
        { extend: 'colvis' },
        {
            text: 'Экспорт Excel',
            action: function (e, dt, node, config) {

               
                var startDate = document.getElementById("startDate").value;
                var endDate = document.getElementById("endDate").value;
                var name_payment = document.getElementById("name_payment").value;
                var currency = document.getElementById("currency").value;
                var status = document.getElementById("status").value;
                
                // Формируем строку запроса
                var queryString = '?startDate=' + encodeURIComponent(startDate) +
                                  '&endDate=' + encodeURIComponent(endDate) +
                                  '&name_payment=' + encodeURIComponent(name_payment) +
                                  '&currency=' + encodeURIComponent(currency) + 
                                  '&status=' + encodeURIComponent(status);

                // Составляем полный URL с параметрами запроса
                var url = '/pays/export' + queryString;

                console.log(url);

                $.ajax({
                    url: url,
                    method: 'GET',
                    xhrFields: {
                        responseType: 'blob' // Для получения файла
                    },
                    success: function(response) {

                        // console.log(response);

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


    // таблица
    var table = $('#pays').DataTable({
        language: {
          url: '/js/datatables/i18n/ru.json',
          
        },
        pagingType: 'simple',
        ajax: {
            url: '/pays/fetchdata',
            type: 'POST',
            data: function (d) {

                d.startDate = $('#startDate').val();
                d.endDate = $('#endDate').val();
                d.name_payment = $('#name_payment').val();
                d.currency = $('#currency').val();
                d.status = $('#status').val();
                

            }

        },
        columns: [
            {   
                data: 'description', 
                render: function(data, type, row) {

                    try {
                        var orderNumber = data.match(/№(\d+)/)[1];

                        var link = 'https://booking.avs.tj/' + orderNumber;

                        
                        return '<a href="' + link + '" target="_blank">' + orderNumber + '</a>';
                    } catch (e) {
                        return '';
                    }
                }
            },
            {data: 'orderNumber'},
            {
                data: 'amount', 
                render: function(data, type, row) {

                    try {
                        let number = parseFloat(data);
                        let result = number / 100;
                        return formatNumber(result);
                    } catch (e) {
                        return '';
                    }
                    


                }
            },
            {
                data: 'summa_with_comission', 
                render: function(data, type, row) {

                    try {
                        let number = parseFloat(data);
                        let result = number / 100;
                        return formatNumber(result);
                    } catch (e) {
                        return '';
                    }
                    


                }
            },
            {
                data: 'summa_out_comission',
                render: function(data, type, row) {

                    try {
                        let number = parseFloat(data);
                        let result = number / 100;
                        return formatNumber(result);
                    } catch (e) {
                        return '';
                    }
                    


                }
            },
            {
                data: 'comission',
                render: function(data, type, row) {

                    try {
                        let number = parseFloat(data);
                        let result = number / 100;
                        return formatNumber(result);
                    } catch (e) {
                        return '';
                    }
                    


                }
            },
            {
                data: 'comission_bank_client',
                render: function(data, type, row) {

                    try {
                        let number = parseFloat(data);
                        let result = number / 100;
                        return formatNumber(result);
                    } catch (e) {
                        return '';
                    }
                    


                }
            },
            {
                data: 'comission_bank_avs',
                render: function(data, type, row) {

                    try {
                        let number = parseFloat(data);
                        let result = number / 100;
                        return formatNumber(result);
                    } catch (e) {
                        return '';
                    }
                    


                }
            },
            {   
                data: 'currency', 
                render: function(data, type, row) {
                    try {

                        if (data === "972") {
                            return 'TJS';
                        }else if(data === "643"){
                            return 'RUB';
                        }else if(data === "840"){
                            return 'USD';    
                        }else if(data === "978"){
                            return 'EUR';
                        }else{
                            return data;
                        }
                        

                    } catch (e) {
                        return '';
                    }
                }
            },
            {data: 'id'},
            {data: 'tranDateTime'},
            {data: 'orderStatus'},
            {   
                data: 'jsonParams', 
                render: function(data, type, row) {
                    try {

                        var jsonData = JSON.parse(data);
                        return jsonData.phone;

                    } catch (e) {
                        return '';
                    }
                }
            },
            {   
                data: 'jsonParams', 
                render: function(data, type, row) {
                    try {
                        var jsonData = JSON.parse(data);  
                        return jsonData.email;
                    } catch (e) {
                        return '';
                    }
                }
            },
            {data: 'name_payment'},
            {   
                data: 'unic_order_id', 
                render: function(data, type, row) {

                    try {

                        const isJson = data.includes("{");

                        if (isJson) {
                            var jsonData = JSON.parse(data);
                            // return data;
                            return jsonData.unic_order_id;
                        }else{
                            return data;
                        }

                    } catch (e) {
                        return '';
                    }
                    
                }
            },
            {data: 'status'},
            {data: 'datetime'}
  
        ],
        buttons: buttons,
        dom: 'Bfrtip',
        processing: true,
        serverSide: true,
        select: true,
        lengthMenu: [ [5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "Все"] ],
        pageLength: 5,
        scrollY: true,
        scrollCollapse: true,
        scrollX: true,
        stateSave: true,
        order: [[17, 'desc']],
         drawCallback: function(row, data, start, end, display) {

            // показываем слой загрузки
            // showLoading2('overlay');
            // скрываем слой загрузки
            // hideLoading2('overlay');

        }
    });



    var downtable = $('#pays_downtable').DataTable({
        language: {
          url: '/js/datatables/i18n/ru.json',
        },
        columns: [
            { "data": "name_payment" },
            { 
                "data": "percentage", 
                render: data => formatNumber(data) + ' %'
            },
            { 
                "data": "count", 
                "render": function (data) {
                    return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
                } 
            },
            { 
                "data": "total_amount", 
                render: data => formatNumber(data)
            },
            { 
                "data": "total_amount_comission", 
                render: data => formatNumber(data)
            },
            { 
                "data": "comission", 
                render: data => formatNumber(data)
            },
            { 
                "data": "comission_bank_client", 
                render: data => formatNumber(data)
            },
            { 
                "data": "comission_bank_avs", 
                render: data => formatNumber(data)
            },
            { 
                "data": "amount", 
                render: data => formatNumber(data)
            },
            
        
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
        scrollX: true,
        drawCallback: function(row, data, start, end, display) {

        }
    });



    var downtable2 = $('#pays_downtable2').DataTable({
        language: {
          url: '/js/datatables/i18n/ru.json',
        },
        columns: [
            
            { "data": "name" },
            { 
                data: "fist_balance",
                render: data => formatNumber(data)
            },
            { 
                data: "pay_amount",
                render: data => formatNumber(data)
            },
            { 
                data: "transaction_amount",
                render: data => formatNumber(data)
            },
            { 
                data: "amount",
                render: data => formatNumber(data)
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
        scrollX: true,
        drawCallback: function(row, data, start, end, display) {
            // Итого
            addSummaryRow(this.api());
            
        }
    });



    // Функция для добавления строки "ИТОГО"
    function addSummaryRow(api) {
        const totalFistBalance = calculateTotal(api, 1); // Столбец fist_balance
        const totalPayAmount = calculateTotal(api, 2);   // Столбец pay_amount
        const totalTransactionAmount = calculateTotal(api, 3); // Столбец transaction_amount
        const totalAmount = calculateTotal(api, 4);      // Столбец amount


        // Удаляем существующую строку "ИТОГО", если она уже есть
        if ($('#totalRow').length) {
            $('#totalRow').remove();
        }

        // Добавляем строку "ИТОГО" в таблицу
        $(api.table().body()).append(
            `<tr id="totalRow">
                <td>Итого:</td>
                <td>${totalFistBalance}</td>
                <td>${totalPayAmount}</td>
                <td>${totalTransactionAmount}</td>
                <td>${totalAmount}</td>
            </tr>`
        );


        $('#pays_downtable2 tbody tr:last-child').addClass('bold-row');
    }

    // Функция для расчета суммы столбца
    function calculateTotal(api, colIndex) {
        return formatNumber(
            api.column(colIndex).data().reduce((a, b) => parseFloat(a) + parseFloat(b), 0)
        );
    }

    // Функция для форматирования чисел
    function formatNumber(data) {
        return parseFloat(data).toLocaleString('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }


    function getTableData(isrefresh){

        if(!isrefresh){
            table.ajax.reload();
        }
        


        // загрузка
        showLoading();

        // Получаем данные формы
        var formData = $("#myForm").serialize();
        
        // console.log(formData);

        $.ajax({
            type: 'POST',
            url: '/pays/downtable',
            data: formData, 
            success: function(response) {
                
                console.log(response);

                // Обновить данные в таблице 
                downtable.clear().draw();
                downtable.rows.add(response.summary).draw();

                downtable.row.add({
                    'name_payment': 'Итого:',
                    'percentage': '100',
                    'count': response.overall_total_count,
                    'total_amount': response.overall_total_amount,
                    'total_amount_comission': response.overall_total_amount_comission,
                    'comission': response.overall_comission,
                    'amount': response.overall_amount,
                    'comission_bank_client': response.overall_comission_bank_client,
                    'comission_bank_avs': response.overall_comission_bank_avs,

                }).draw(false);

                $('#pays_downtable tbody tr:last-child').addClass('bold-row');



                // Обновить данные в таблице
                downtable2.clear().draw();
                downtable2.rows.add(response.acquirings).draw();



                // загрузка
                hideLoading();
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + status + error);
            }
        });
    }






    
    var isrefresh = true;
    getTableData(isrefresh);


    $('#submitBtn').click(function() {
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




        






}







});
