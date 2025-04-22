$(document).ready(function() {

if (window.location.pathname === '/reports') {


// Функция для показа кнопок
function showReportButtons() {
    document.getElementById('acceptReport').classList.remove('d-none');
    document.getElementById('rejectReport').classList.remove('d-none');
    document.getElementById('deleteReport').classList.remove('d-none');
}


// формат суммы
function formatMoney(number) {
    return new Intl.NumberFormat('ru-RU', { 
        style: 'decimal', 
        minimumFractionDigits: 2,
        maximumFractionDigits: 2  
    }).format(number);
}




// кнопки 
var buttons = [
    
    { extend: 'pageLength' },
    { extend: 'colvis' },
];


// таблица
var table = $('#reports').DataTable({
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ru.json',
          "processing": '',
        },
        pagingType: 'simple',
        ajax: {
                url: '/reports/get_reports',
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
            { data: 'id' },
            { data: 'user_id' },
            // { data: 'name_table' },
            // { data: 'value_table' },
            { data: 'start_date' },
            { data: 'end_date' },
            { data: 'currency' },
            // { data: 'balance' },
            { data: 'send_date' },
            { data: 'check_date' },
            { data: 'status' },
            {
                data: null, // В этой колонке нет данных из таблицы
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-info view-btn" data-id="${row.id}">Посмотреть</button>

                    `;
                }
            }


            // <button class="btn btn-success accept-btn" data-id="${row.id}">Принять</button>
            // <button class="btn btn-danger reject-btn" data-id="${row.id}">Отклонить</button>
            // <button class="btn btn-secondary delete-btn" data-id="${row.id}">Удалить</button>
        
        ],
        order: [[5, 'desc']],
        stateSave: false,
        select: false,
        serverSide: true,
        lengthMenu: [ [5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, 'Все'] ],
        pageLength: 5,
        scrollY: true,
        scrollCollapse: true,
        scrollX: true
});




// посмотреть
$('#reports').on('click', '.view-btn', function() {

    var reportId = $(this).data('id');
    console.log('Просмотр отчета с ID: ' + reportId);
    document.getElementById('report_id').value = reportId;

    $('#summaryModal #otchet').hide();
    $('#summaryModal #loadingAnimation').show();
    $('#summaryModal #modalMessage').text('Пожалуйста, подождите... Идет формирование отчета.');
    $('#summaryModal #modalMessage').show();
    $('#summaryModal').modal('show');


    $.ajax({
        url: '/reports/show_report',
        method: 'POST',
        data: {
            id: reportId
        },
        success: function(data) {

            console.log(data);

            view_chapka(data);


            let OTCHET = data.OTCHET;

            // ===== Услуги =====//

            let serviceData = OTCHET['6']['amounts'];
            let serviceRows = "";

            // Проверяем, что serviceData является массивом и что он не пустой
            if (Array.isArray(serviceData) && serviceData.length > 0) {

                // console.log(serviceData);

                // Выполняем ваш код для создания строк HTML
                serviceRows = serviceData.map((item, i) => `
                    <tr class="even"> 
                        <td>6.${i + 1}</td>
                        <td>${item.service_name}</td>
                        <td>${formatMoney(item.amount)}</td>
                    </tr>
                `).join('');
            }


            // ===== Транзакции =====//

            let transationsData = OTCHET['7']['amounts'];
            let transationsRows = "";

            // Проверяем, что transationsData является массивом и что он не пустой
            if (Array.isArray(transationsData) && transationsData.length > 0) {

                // console.log(transationsData);

                // Выполняем ваш код для создания строк HTML
                transationsRows = transationsData.map((item, i) => `
                    <tr class="even"> 
                        <td>7.${i + 1}</td>
                        <td>${item.method}</td>
                        <td>${formatMoney(item.summa)}</td>
                    </tr>
                `).join('');
            }
            

            // if airline

            let oneHtml = '';
            let twoHtml = '';
            let threeHtml = '';

            if (OTCHET['type'] == 'airline') {

                oneHtml = `<tr class="even">
                                    <td>1.4</td>
                                    <td>Сбор за бронь (YR)</td>
                                    <td>${formatMoney(OTCHET['1.4'])}</td>
                                </tr>
                                <tr class="even">
                                    <td>1.5</td>
                                    <td>Аэропортовый сбор (+ сбор за безопасность+ таксы прочие )</td>
                                    <td>${formatMoney(OTCHET['1.5'])}</td>
                                </tr>
                                <tr class="even">
                                    <td>1.6</td>
                                    <td>Сбор за бронь (YR) не перечисляемый</td>
                                    <td>${formatMoney(OTCHET['1.6'])}</td>
                                </tr>`;

                twoHtml = `<tr class="even">
                                        <td>2.3</td>
                                        <td>Сборы за бронь</td>
                                        <td>${formatMoney(OTCHET['2.3'])}</td>
                                    </tr>
                                    <tr class="even">
                                        <td>2.4</td>
                                        <td>Сбор за бронь (YR)</td>
                                        <td>${formatMoney(OTCHET['2.4'])}</td>
                                    </tr>
                                    <tr class="even">
                                        <td>2.5</td>
                                        <td>Аэропортовый сбор (+ сбор за безопасность+ таксы прочие )</td>
                                        <td>${formatMoney(OTCHET['2.5'])}</td>
                                    </tr>
                                    <tr class="even">
                                        <td>2.6</td>
                                        <td>Сбор за бронь (YR) не перечисляемый</td>
                                        <td>${formatMoney(OTCHET['2.6'])}</td>
                                    </tr>`;

                threeHtml = `<tr class="even">
                                        <td>3.5</td>
                                        <td>Сбор за бронь (YR)</td>
                                        <td>${formatMoney(OTCHET['3.5'])}</td>
                                    </tr>
                                    <tr class="even">
                                        <td>3.6</td>
                                        <td>Аэропортовый сбор (+ сбор за безопасность+ таксы прочие )</td>
                                        <td>${formatMoney(OTCHET['3.6'])}</td>
                                    </tr>
                                    <tr class="even">
                                        <td>3.7</td>
                                        <td>Сбор за бронь (YR) не перечисляемый</td>
                                        <td>${formatMoney(OTCHET['3.7'])}</td>
                                    </tr>`;
            }
            


            let summaryContent = `<table id="summary_table" class="dataTable display">
                      <tr>
                          <th>№ П/П</th>
                          <th>НАИМЕНОВАНИЕ СТАТЬИ ВЫРУЧКИ</th>
                          <th>ВСЕГО</th>
                      </tr>
                      <tr class="odd" style="background-color: lightgreen; font-weight: bold;">
                          <td></td>
                          <td>Сальдо взаиморасчетов на начало</td>
                          <td>${formatMoney(OTCHET['0'])}</td>
                      </tr>
                      <tr class="odd" style="background-color: lightblue; font-weight: bold;">
                          <td>1</td>
                          <td>Выручка по реестрам продажи авиабилетов</td>
                          <td>${formatMoney(OTCHET['1'])}</td>
                      </tr>
                      <tr class="even">
                          <td>1.1</td>
                          <td>Тариф а/б</td>
                          <td>${formatMoney(OTCHET['1.1'])}</td>
                      </tr>
                      <tr class="even">
                          <td>1.2</td>
                          <td>Сборы за бронь</td>
                          <td>${formatMoney(OTCHET['1.2'])}</td>
                      </tr>
                      <tr class="even">
                          <td>1.3</td>
                          <td>Сумма аннуляции бланков</td>
                          <td>${formatMoney(OTCHET['1.3'])}</td>
                      </tr>

                      ${oneHtml}

                      <tr class="odd" style="background-color: lightpink; font-weight: bold;">
                          <td>2</td>
                          <td>Выручка по реестрам обмена</td>
                          <td>${formatMoney(OTCHET['2'])}</td>
                      </tr>
                      <tr class="even">
                          <td>2.1</td>
                          <td>Доплата по тарифу</td>
                          <td>${formatMoney(OTCHET['2.1'])}</td>
                      </tr>
                      <tr class="even">
                          <td>2.2</td>
                          <td>Штрафы</td>
                          <td>${formatMoney(OTCHET['2.2'])}</td>
                      </tr>

                      ${twoHtml}

                      <tr class="odd" style="background-color: lightgrey; font-weight: bold;">
                          <td>3</td>
                          <td>Сумма по реестрам возврата</td>
                          <td>${formatMoney(OTCHET['3'])}</td>
                      </tr>
                      <tr class="even">
                          <td>3.1</td>
                          <td>Возврат тарифа</td>
                          <td>${formatMoney(OTCHET['3.1'])}</td>
                      </tr>
                      <tr class="even">
                          <td>3.2</td>
                          <td>Штрафы</td>
                          <td>${formatMoney(OTCHET['3.2'])}</td>
                      </tr>
                      <tr class="even">
                          <td>3.3</td>
                          <td>Сборы (аэропортовые)</td>
                          <td>${formatMoney(OTCHET['3.3'])}</td>
                      </tr>
                      <tr class="even">
                          <td>3.4</td>
                          <td>Сборы (за возврат)</td>
                          <td>${formatMoney(OTCHET['3.4'])}</td>
                      </tr>

                      ${threeHtml}

                      <tr class="odd" style="background-color: yellow; font-weight: bold;">
                          <td>4</td>
                          <td>Комиссионное вознаграждение</td>
                          <td>${formatMoney(OTCHET['4'])}</td>
                      </tr>
                      <tr class="even">
                          <td>4.1</td>
                          <td>По реестрам продажи</td>
                          <td>${formatMoney(OTCHET['4.1'])}</td>
                      </tr>
                      <tr class="even">
                          <td>4.2</td>
                          <td>По реестрам обмена</td>
                          <td>${formatMoney(OTCHET['4.2'])}</td>
                      </tr>
                      <tr class="even">
                          <td>4.3</td>
                          <td>По реестрам возврата</td>
                          <td>${formatMoney(OTCHET['4.3'])}</td>
                      </tr>
                      <tr class="odd" style="background-color: lightgrey; font-weight: bold;">
                          <td>5</td>
                          <td>Подлежит перечислению</td>
                          <td>${formatMoney(OTCHET['5'])}</td>
                      </tr>
                      <tr class="even">
                          <td>5.1</td>
                          <td>Выручка по реестрам продажи</td>
                          <td>${formatMoney(OTCHET['1'])}</td>
                      </tr>
                      <tr class="even">
                          <td>5.2</td>
                          <td>Выручка по реестрам обмена</td>
                          <td>${formatMoney(OTCHET['2'])}</td>
                      </tr>
                      <tr class="even">
                          <td>5.3</td>
                          <td>Сумма по реестрам возврата</td>
                          <td>${formatMoney(OTCHET['3'])}</td>
                      </tr>
                      <tr class="even">
                          <td>5.4</td>
                          <td>Комиссионное вознаграждение</td>
                          <td>${formatMoney(OTCHET['4'])}</td>
                      </tr>
                      <tr class="odd" style="background-color: lightgreen; font-weight: bold;">
                          <td>6</td>
                          <td>Сумма по претензиям и пультам</td>
                          <td>${formatMoney(OTCHET['6']['total'])}</td>
                      </tr>

                      ${serviceRows}

                      <tr class="odd" style="background-color: lightblue; font-weight: bold;">
                          <td>7</td>
                          <td>Перечислено всего</td>
                          <td>${formatMoney(OTCHET['7']['total'])}</td>
                      </tr>

                      ${transationsRows}

                      <tr class="odd" style="background-color: orange; font-weight: bold;">
                          <td>8</td>
                          <td>Сальдо взаиморасчетов в конец</td>
                          <td id=balance>${formatMoney(OTCHET['8'])}</td>
                      </tr>
                  </table>`;

            
            $('#summaryModal #loadingAnimation').hide();
            $('#summaryModal #modalMessage').hide();

            $('#summaryModal #otchet').html(summaryContent);
            $('#summaryModal #otchet').show();

            


            // формируем отчет
            if (data.status == 1) {
                FormReport(data);
            }
            

        },
        error: function(error) {
            console.log("error: ", error);
        }
    });



});


// принять
$('#acceptReport').on('click', function() {


    // Получаем параметры
    var report_id = document.getElementById('report_id').value;
    var balanceValue = document.getElementById("balance").textContent;
    var num = parseFloat(balanceValue.replace(/\s/g, '').replace(',', '.'));
    let balance = num.toFixed(2);


    if (confirm('Уверены что хотите принять отчет?')) {
        
        $('#summaryModal').modal('hide');

        $.ajax({
            url: '/reports/updateStatus',
            method: 'POST',
            data: {
                id: report_id,
                balance: balance,
                status: 1
            },
            success: function(response) {

                console.log(response);

                if (response.status === 'success') {
                    toastr.success(response.message);

                    //формируем отчет
                    FormReport(response);

                } else {
                    toastr.error(response.message);
                }

                // обновить таблицу
                table.ajax.reload();
            },
            error: function(error) {
                console.log("error: ", error);
            }
        });
    }


});

// отклонить
$('#rejectReport').on('click', function() {

    // Получаем параметры
    var report_id = document.getElementById('report_id').value;

    if (confirm('Уверены что хотите отклонить отчет?')) {


        $('#summaryModal').modal('hide');

        $.ajax({
            url: '/reports/updateStatus',
            method: 'POST',
            data: {
                id: report_id,
                balance: 0,
                status: 2
            },
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }

                // обновить таблицу
                table.ajax.reload();
            },
            error: function(error) {
                console.log("error: ", error);
            }
        });

    }

});

// удалить
$('#deleteReport').on('click', function() {
    // Получаем параметры
    var report_id = document.getElementById('report_id').value;

    if (confirm('Уверены что хотите удалить отчет?')) {


        $('#summaryModal').modal('hide');

        
        $.ajax({
            url: '/reports/delete',
            type: 'POST',
            data: { id: report_id },
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }

                // обновить таблицу
                table.ajax.reload();
            },
            error: function() {
                console.log("error: ", error);
            }
        });

    }
});


function view_chapka(response) {

    document.getElementById('summaryModalLabel').innerHTML = response.data.user_desc;
    document.getElementById('id_report').textContent = response.data.report.id;
    document.getElementById('iname_table').textContent = response.data.report.name_table + ':';
    document.getElementById('ivalue_table').textContent = response.data.report.value_table;
    document.getElementById('start_date').textContent = response.data.report.start_date;
    document.getElementById('end_date').textContent = response.data.report.end_date;
    document.getElementById('icurrency').textContent = response.data.report.currency;


    // показать элементы
    $('#up_part').show();

}

function FormReport(response) {

    // Генерируем QR-код
    generateQRCode(response.data.qrcode_user, 'qrcode_user'); 
    generateQRCode(response.data.qrcode_admin, 'qrcode_admin'); 


    
    document.getElementById('send_date').textContent = response.data.report.send_date;
    document.getElementById('check_date').textContent = response.data.report.check_date;

    document.getElementById('fio_user').textContent = response.data.fio_user;
    document.getElementById('fio_admin').textContent = response.data.fio_admin;
    

    // показать элементы
    $('#down_part').show();
    $('#printReport').show();
}


//скрыть элементы
function HideElements() {
    $('#up_part').hide();
    $('#down_part').hide();
    $('#printReport').hide();
}


// Функция для генерации QR-кода
function generateQRCode(data, id) {
    //очищаем
    document.getElementById(id).textContent = '';
    
    // Подключаем библиотеку QRCode.js и генерируем новый QR-код
    const qrcode = new QRCode(document.getElementById(id), {
        width: 100, // Размер QR-кода
        height: 100,
        colorDark: '#000000', // Цвет QR-кода
        colorLight: '#ffffff', // Цвет фона
    });
    
    // Генерируем QR-код
    qrcode.makeCode(data);
    
    // Добавляем логирование в консоль
    // console.log('QR-код успешно создан:', data);
}


//скрыть submitReport по вне модального
$(window).click(function(event) {
    if (event.target == $('#summaryModal')[0]) {
        HideElements(); 
    }
});

//скрыть submitReport по закрыть
$('#summaryModal .close, .btn-secondary[data-dismiss="modal"]').off('click').on('click', function() {
    HideElements(); 
});


$('#printReport').on('click', function() {
    // Создаем новый элемент <iframe>, чтобы избежать проблем с отображением
    const iframe = document.createElement("iframe");
    iframe.style.display = "none"; // Скрываем iframe
    document.body.appendChild(iframe);


    const printContents = document.getElementById("reportContent").innerHTML;
    const title = document.getElementById("summaryModalLabel").innerHTML;

    const doc = iframe.contentWindow.document;
    doc.open();
    
    doc.write('<html><head><title></title></head><body>');


    // Стиль
    doc.write('<style>');

    doc.write('body { font-family: Arial, sans-serif; line-height: 1.5; font-size: 14px;}');
    doc.write('.container { display: flex; flex-wrap: wrap; margin: 15px 0; }');
    doc.write('.column { flex: 1; padding: 10px; min-width: 300px; }');
    doc.write('.column p { margin: 5px 0; }');
    doc.write('#qrcode_user, #qrcode_admin { margin-top: 10px; width: 100px; height: 100px; background-color: #eee; }');
    doc.write(`
        
        .modal-title {
            text-align: center;
            width: 100%;
        }
    `);

    // Стиль таблицы
    doc.write('#summary_table { width: 100%; border-collapse: collapse; border: 1px solid #ccc; font-size: 14px;}');
    doc.write('#summary_table th, #summary_table td { border: 1px solid #ccc; padding: 3px; }');

    doc.write('</style>');


    // добавить шапку
    doc.write('<h4 class="modal-title">' + title + '</h4>');




    doc.write(printContents); // Содержимое для печати
    doc.write('</body></html>');
    doc.close();

    // Делаем печать
    iframe.contentWindow.focus();
    iframe.contentWindow.print();

    // Удаляем iframe после печати
    setTimeout(() => {
    document.body.removeChild(iframe);
    }, 100);
});



$('#printReport2').on('click', function() {

    const printContents = document.getElementById("reportContent").innerHTML;
    const newWindow = window.open('', '', 'height=600,width=800');
    newWindow.document.write('<html><head><title>Распечатка отчета</title>');
    newWindow.document.write('</head><body>');
    newWindow.document.write(printContents); // Содержимое для печати
    newWindow.document.write('</body></html>');
    newWindow.document.close();
    newWindow.focus();
    newWindow.print();
    newWindow.close();

});









// ==================== Кнопки формы =========================== //





        function getTableData(){

                // загрузка
                // showLoading();
            
                document.getElementById('is_refresh').value = 'no';
                
                table.ajax.reload();

                
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

        




        // //проверяем установлены все опции
        // const selectElement = document.getElementById('value_table');

        // const checkOptionsLoaded = setInterval(function() {
        //     if (selectElement.options.length > 0) {
        //         // console.log('Опции установлены.');
        //         clearInterval(checkOptionsLoaded);  // Останавливаем проверку
        //         getTableData();

        //     }
        // }, 1000);











}

});