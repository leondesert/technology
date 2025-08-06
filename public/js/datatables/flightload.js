$(document).ready(function() {

// Функция для обработки кликов и отображения модального окна
function handleTableCellClick(id_name, data) {
    $('#summaryTextModal').modal('show');
    $('#summaryTextModal #loadingAnimation').hide();
    $('#summaryTextModal #modalMessage').hide();

    // console.log(id_name);
    
    var parts = id_name.split('_');
    var firstPart = parts[0];
    var secondPart = parts[1];

    // Получаем данные
    var ads = data.operations[firstPart][secondPart];
    var asd = data.data[firstPart][secondPart];
    var parts = asd.split(' / ');
    var totalCount = parts[0].replace(/\s/g, '');
    var totalAmount = parts[1].replace(/\s/g, '');
    
    var totalCount = parseFloat(totalCount.replace(',', '.'));
    var totalAmount = parseFloat(totalAmount.replace(',', '.'));

    // console.log(totalAmount);

    var tableHTML = table_create(tableHTML, ads, totalAmount); // Создаем таблицу

    // Вставляем таблицу в модальное окно
    $('#podrobnee').html(tableHTML);
}

function table_create(tableHTML, ads, totalAmount){

    if (typeof ads !== 'undefined' && ads !== null) {
        // Создаем HTML-код таблицы
        var tableHTML = '<table id="downtable" class="table table-striped table-bordered table-hover dt-responsive nowrap" style="width:100%">';
        

        tableHTML += '<thead>';
        tableHTML += '<tr><th>Название</th><th>Сумма</th><th>Количество</th><th>Доля</th></tr>';
        tableHTML += '</thead><tbody>';

        // console.log(ads);

        // Проходим по каждому ключу в объекте data
        Object.keys(ads).forEach(function(item) {
            var name = ads[item].name;
            var amount = ads[item].amount;
            var count = ads[item].count;

            // console.log(amount);
            // console.log(count);

            tableHTML += '<tr>';
            tableHTML += '<td>' + name + '</td>';
            tableHTML += '<td>' + formatMoney(amount) + '</td>';
            tableHTML += '<td>' + formatMoney2(count) + '</td>';
            tableHTML += '<td>' + Math.round(amount/totalAmount*100) + ' %</td>';
            // tableHTML += '<td><span class="badge bg-success">' + Math.round(amount/totalAmount*100) + ' %</span></td>';
            tableHTML += '</tr>';

        });


        tableHTML += '</tbody></table>';
    } else {
        
        tableHTML = 'Данных нет';
    }

    return tableHTML;
}

//  Топ 10 популярных рейсов
function topPopularFlights(){

    showLoading2('overlay2');

    $.ajax({
        url: '/flightload/popularflights',
        type: 'POST',
        dataType: 'json',
        data: {
            startDate: $('#startDate').val(),
            endDate: $('#endDate').val(),
            flydate: $('#flydate').val(),
            citycodes: $('#citycodes').val(),
            flytime: $('#flytime').val(),
            user_login: $('#user_login').val(),
            name_table: $('#name_table').val(),
            value_table: $('#value_table').val(),
            filterby: $('#filterby').val(),
            show: $('#show').val(),
            
        },
        success: function(data) {

            console.log(data);

            if (data.length > 0) {



                var method = $('#filterby').find('option:selected').text();

                var html = '<table class="table table-striped table-bordered table-hover dt-responsive nowrap" style="width:100%">';
                html += '<thead>';
                html += '<tr>';
                html += '<th>#</th>';
                html += '<th>'+ method +'</th>';
                html += '<th>Количество продаж</th>';
                html += '<th>Сумма продаж</th>';
                // html += '<th>Сумма дохода</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';
                
                $.each(data, function(index, flight) {
                    html += '<tr>';
                    html += '<td>' + (index + 1) + '</td>';
                    html += '<td>' + flight.filterby + '</td>';
                    html += '<td>' + flight.sale_count + '</td>';
                    html += '<td>' + flight.total_fops_amount + '</td>';
                    // html += '<td>' + flight.total_reward + '</td>';
                    html += '</tr>';
                });

                html += '</tbody>';
                html += '</table>';
                
                $('.top-ten-popular').html(html);

            } else {
                $('.top-ten-popular').html('<p>Нет данных</p>');
            }

            hideLoading2('overlay2');

        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('Ошибка: ' + textStatus + ' ' + errorThrown);
        }
    });
}


// формат суммы
function formatMoney(number) {
    return new Intl.NumberFormat('ru-RU', { 
        style: 'decimal', 
        minimumFractionDigits: 2,
        maximumFractionDigits: 2  
    }).format(number);
}

function formatMoney2(number) {
    return new Intl.NumberFormat('ru-RU', { 
        style: 'decimal', 
        minimumFractionDigits: 0,
        maximumFractionDigits: 0  
    }).format(number);
}

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


    // кнопки 
    var buttons = [
        
        { extend: 'pageLength' },
        { extend: 'colvis' },
    ];

    // таблица
    var table = $('#flightload').DataTable({
        language: {
          url: '/js/datatables/i18n/ru.json',
          "processing": '<div class="custom-loader"><div class="progress"><div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div></div></div>',
        },
        pagingType: 'simple',
        ajax: {
            url: '/flightload/fetchdata',
            type: 'POST',
            data: function (d) {
                d.startDate = $('#startDate').val();
                d.endDate = $('#endDate').val();
                d.flydate = $('#flydate').val();
                d.citycodes = $('#citycodes').val();
                d.flytime = $('#flytime').val();
                d.user_login = $('#user_login').val();
                d.name_table = $('#name_table').val();
                d.value_table = $('#value_table').val();
                d.page = 'flightload';
                // console.log(flydate);

            }

            // dataFilter: function(data){
            //       console.log(data);
            //       return data;
            // }
        },
        columns: [
            // Таблица "tickets"
          { data: 'tickets_type'}, 
          { data: 'tickets_currency'}, 
          { data: 'tickets_dealdate'}, 
          { data: 'tickets_dealtime'},
          { data: 'tickets_OPTYPE'}, 
          { data: 'tickets_TRANS_TYPE'}, 
          { data: 'tickets_BSONUM'}, 
          { data: 'tickets_EX_BSONUM'}, 
          { data: 'tickets_TO_BSONUM'}, 
          { data: 'tickets_FARE'}, 
          { data: 'tickets_PNR_LAT'},
          { data: 'tickets_DEAL_date'}, 
          { data: 'tickets_DEAL_disp'},  
          { data: 'tickets_DEAL_time'}, 
          { data: 'tickets_DEAL_utc'},
          { data: 'summa_no_found'},
          // Таблица "opr"
          { data: 'opr_code'},
          // Таблица "agency"
          { data: 'agency_code'},
          // Таблица "emd"
          { data: 'emd_value'},
          // // Таблица "fops"
          { data: 'fops_type'},
          { data: 'fops_amount'},
          // // Таблица "passengers"
          { data: 'fio'},
          { data: 'pass'},
          { data: 'pas_type'},
          { data: 'citizenship'},
          // // Таблица "segments"
          { data: 'citycodes'},
          { data: 'carrier'},
          { data: 'class'},
          { data: 'reis'},
          { data: 'flydate'},
          { data: 'flytime'},
          { data: 'basicfare'},
          // Таблица "stamp"
          { data: 'stamp_code'},
          // Таблица "tap"
          { data: 'tap_code'},
          // Таблица "taxes"
          { data: 'tax_code'},
          { data: 'tax_amount'},
        ],
        buttons: buttons,
        dom: 'Bfrtip',
        processing: true,
        serverSide: true,
        select: false,
        lengthMenu: [ [5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "Все"] ],
        pageLength: 5,
        scrollY: true,
        scrollCollapse: true,
        scrollX: true,
        stateSave: true,
        drawCallback: function(row, data, start, end, display) {

            // показываем слой загрузки
            showLoading2('overlay3');

            // Получаем текущие параметры запроса
            var params = this.api().ajax.params();

            console.log('drawCallback', params);


            $('#summary_text').remove();


            // downTable
            $.ajax({
                url: '/calculateSummary',
                method: 'POST',
                data: params,
                success: function(data) {
                    
                    console.log(data);

                    tData = data.data;

                    
                    // Формирование и вставка кода таблицы
                    let summaryText = `<div id="summary_text">

                      <table id="downtable" class="dataTable display" style="width:100%">
                          <tr class="even"><td></td><td><b>ETICKET</b></td><td><b>EMD</b></td><td><b>Сумма</b></td></tr>
                          <tr class="odd"><td><b>Продажа (sale)</b></td><td id="ETICKET_SALE" class="down_cell">${tData.ETICKET.SALE}</td><td id="EMD_SALE" class="down_cell">${tData.EMD.SALE}</td><td></td></tr>
                          <tr class="even"><td><b>Обмен (exchange)</b></td><td id="ETICKET_EXCHANGE" class="down_cell">${tData.ETICKET.EXCHANGE}</td><td id="EMD_EXCHANGE" class="down_cell">${tData.EMD.EXCHANGE}</td><td></td></tr>
                          <tr class="odd"><td><b>Возврат (refund)</b></td><td id="ETICKET_REFUND" class="down_cell">${tData.ETICKET.REFUND}</td><td id="EMD_REFUND" class="down_cell">${tData.EMD.REFUND}</td><td></td></tr>
                          <tr class="even"><td><b>Отмена (cancel)</b></td><td id="ETICKET_CANCEL" class="down_cell">${tData.ETICKET.CANCEL}</td><td id="EMD_CANCEL" class="down_cell">${tData.EMD.CANCEL}</td><td></td></tr>
                          <tr class="odd"><td><b>Всего транзакций</b></td><td>${tData.ETICKET.count}</td><td>${tData.EMD.count}</td><td>${tData.totalCount}</td></tr>
                          <tr class="even"><td><b>Выручка</b></td><td>${tData.ETICKET.amount}</td><td>${tData.EMD.amount}</td><td>${tData.totalAmount}</td></tr>
                      </table>
                      </div>`;

                    // $('#summary_text').remove();

                    document.querySelector('.downtable-content').innerHTML = summaryText;

                    // Скрываем слой загрузки
                    hideLoading2('overlay3');
                    // hideLoading();


                    // Подробнее
                    $('.down_cell').on('click', function() {
                        var id_name = $(this).attr('id');
                        handleTableCellClick(id_name, data);
                    });


                },
                error: function(error) {
                    console.log(error);
                }
            });
                    


        }
    });
    

    if (window.location.pathname === '/flightload') {


        // topPopularFlights();


        $('#submitBtnPopular').click(function() {
            topPopularFlights();
        });

        $('#submitBtn').click(function() {
            // topPopularFlights();
            table.ajax.reload();
        });

        $('#submitBtnToday').click(function(){

            addDeleteClass();

            document.getElementById('startDate').value = today;
            document.getElementById('endDate').value = today;
            
            // topPopularFlights();
            table.ajax.reload();

            // Изменяем классы кнопок
            $(this).removeClass('btn-primary').addClass('bg-gradient-warning');
        });

        $('#submitBtnYesterday').click(function(){

            addDeleteClass();

            document.getElementById('startDate').value = yesterday;
            document.getElementById('endDate').value = yesterday;

            // topPopularFlights();
            table.ajax.reload();
        

            // Изменяем классы кнопок
            $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
        });

        $('#submitBtnOneDecade').click(function(){

            addDeleteClass();

            document.getElementById('startDate').value = firstDecadeStart;
            document.getElementById('endDate').value = firstDecadeEnd;

            // topPopularFlights();
            table.ajax.reload();

            // Изменяем классы кнопок
            $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
        });

        $('#submitBtnTwoDecade').click(function(){

            addDeleteClass();

            document.getElementById('startDate').value = secondDecadeStart;
            document.getElementById('endDate').value = secondDecadeEnd;

            // topPopularFlights();
            table.ajax.reload();

            // Изменяем классы кнопок
            $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
        });

        $('#submitBtnThreeDecade').click(function(){

            addDeleteClass();

            document.getElementById('startDate').value = thirdDecadeStart;
            document.getElementById('endDate').value = thirdDecadeEnd;

            // topPopularFlights();
            table.ajax.reload();

            // Изменяем классы кнопок
            $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
        });

        $('#submitBtnThisMonth').click(function(){

            addDeleteClass();

            document.getElementById('startDate').value = thisMonthFirst;
            document.getElementById('endDate').value = thisMonthLast;

            // topPopularFlights();
            table.ajax.reload();

            // Изменяем классы кнопок
            $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
        });

        $('#submitBtnLastMonth').click(function(){

            addDeleteClass();

            document.getElementById('startDate').value = lastMonthFirst;
            document.getElementById('endDate').value = lastMonthLast;

            // topPopularFlights();
            table.ajax.reload();

            // Изменяем классы кнопок
            $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
        });




        






    }


});



