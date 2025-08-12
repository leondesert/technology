$(document).ready(function() {

function showLoading2(id) {
  var overlay = document.getElementById(id);
  overlay.classList.remove('d-none');
}

function hideLoading2(id) {
  var overlay = document.getElementById(id);
  overlay.classList.add('d-none');
}





function ShowBalancesAll(type)
{
    $('.showValue').each(function() {
        // Получаем значение атрибутов value и currency текущей кнопки
        var value = $(this).attr('value');
        var currency = $(this).attr('currency');
        var element = $(this).closest('[value="' + value + '"]');
        const pathname = window.location.pathname;
        const name_table = pathname.replace('/', '');

        getBalances(type, name_table, currency, value, element);


        // Выводим значения в консоль
        // console.log('Value: ' + value + ', Currency: ' + currency);
    });
}



// кнопки для Сформировать отчет
var buttons_show_balances = [];
buttons_show_balances.push({
    text: 'по умолчанию',
    action: function (e, dt, node, config) {
        console.log('по умолчанию');
        var type = "default";
        ShowBalancesAll(type);
    }
});

if (is_acquiring == 1) {
   
    buttons_show_balances.push({
        text: 'для сайта',
        // className: 'btn-fa-file-excel',
        action: function (e, dt, node, config) {
            console.log('для сайта');
            var type = "site";
            ShowBalancesAll(type);
        }
    });
}

if (is_airline == 1) {
   
    buttons_show_balances.push({
        text: 'для авиакомпании',
        // className: 'btn-fa-file-excel',
        action: function (e, dt, node, config) {
            console.log('для авиакомпании');
            var type = "airline";
            ShowBalancesAll(type);
        }
    });
}




var buttons = [
        { extend: 'pageLength' }, 
        // 'colvis',
        { 
            extend: 'collection', 
            text: 'Экспорт', 
            buttons: ['copy', 'excel', 'csv', 'pdf', 'print'] 
        },
        // Показать балансы
        {
            extend: 'collection',
            className: 'btn-fa-file-export',
            autoClose: true, 
            text: 'Показать балансы',
            buttons: buttons_show_balances
        },
       
        
    ];






var table = $('#organization').DataTable({
        language: {
          url: '/js/datatables/i18n/ru.json',
        },
        columns: [
            { "data": "code" },
            { "data": "name" },
            { "data": "address" },
            { "data": "phone" },
            { "data": "mail" },
            { "data": "balance_tjs" },
            { "data": "balance_rub" },
            { "data": "action" },
            
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



function getTableData(name_table){

    // загрузка
    showLoading2('overlay1');

    // Получаем данные формы
    // var formData = $("#myForm").serialize();
    
    // console.log(formData);

    $.ajax({
        type: 'POST',
        url: '/agency/get_data_table',
        data: {
            name_table: name_table
        }, 
        success: function(response) {
            
            // console.log(response);

            // Обновить данные в таблице
            table.clear().draw();
            table.rows.add(response.data).draw();


            // загрузка
            hideLoading2('overlay1');
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error: ' + status + error);
        }
    });
}


function getBalances(type, name_table, currency, value, element){

    // загрузка
    showLoading2('overlay1');

    $.ajax({
        type: 'POST',
        url: '/agency/getBalances',
        data: {
            type: type,
            name_table: name_table,
            value: value,
            currency: currency,
        }, 
        success: function(response) {
            
            // console.log(response);

            // загрузка
            hideLoading2('overlay1');

            var newTextElement = $('<span>').text(response);
            element.replaceWith(newTextElement);

        },
        error: function(xhr, status, error) {
            console.error(xhr);
        }
    });


}


const paths = {
    '/agency': 'agency',
    '/stamp': 'stamp',
    '/tap': 'tap',
    '/opr': 'opr',
    '/share': 'share',
    '/pre_share': 'pre_share'
};

const pathname = window.location.pathname;

if (paths[pathname]) {
    // Удаляем начальный слэш и вызываем getTableData с соответствующим значением
    const name_table = pathname.replace('/', '');
    getTableData(name_table);



    $('#organization').on('click', '.showValue', function() {
        // console.log('showValue');
        var type = "default";
        var value = $(this).attr('value');
        var currency = $(this).attr('currency');

        var element = $(this).closest('[value="' + value + '"]');
        getBalances(type, name_table, currency, value, element);

    });



    


}


    

});


