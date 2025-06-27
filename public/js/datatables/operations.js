$(document).ready(function() {



if (window.location.pathname === '/operations') {



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
            tableHTML += '<td>' + formatCount(count) + '</td>';
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


var ruLanguage = {
    "processing": '<div class="custom-loader"><div class="progress"><div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div></div></div>',
    "search": "Поиск:",
    "lengthMenu": "Показать _MENU_ записей",
    "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
    "infoEmpty": "Записи с 0 до 0 из 0 записей",
    "infoFiltered": "(отфильтровано из _MAX_ записей)",
    "loadingRecords": "Загрузка записей...",
    "zeroRecords": "Записи отсутствуют.",
    "emptyTable": "В таблице отсутствуют данные",
    "paginate": {
        "first": "Первая",
        "previous": "Предыдущая",
        "next": "Следующая",
        "last": "Последняя"
    },
    "aria": {
        "sortAscending": ": активировать для сортировки столбца по возрастанию",
        "sortDescending": ": активировать для сортировки столбца по убыванию"
    },
    "select": {
        "rows": {
            "_": "Выбрано записей: %d",
            "1": "Выбрана одна запись"
        },
        "cells": {
            "_": "Выбрано %d ячеек",
            "1": "Выбрана 1 ячейка "
        },
        "columns": {
            "1": "Выбран 1 столбец ",
            "_": "Выбрано %d столбцов "
        }
    },
    "searchBuilder": {
        "conditions": {
            "string": {
                "startsWith": "Начинается с",
                "contains": "Содержит",
                "empty": "Пусто",
                "endsWith": "Заканчивается на",
                "equals": "Равно",
                "not": "Не",
                "notEmpty": "Не пусто",
                "notContains": "Не содержит",
                "notStartsWith": "Не начинается на",
                "notEndsWith": "Не заканчивается на"
            },
            "date": {
                "after": "После",
                "before": "До",
                "between": "Между",
                "empty": "Пусто",
                "equals": "Равно",
                "not": "Не",
                "notBetween": "Не между",
                "notEmpty": "Не пусто"
            },
            "number": {
                "empty": "Пусто",
                "equals": "Равно",
                "gt": "Больше чем",
                "gte": "Больше, чем равно",
                "lt": "Меньше чем",
                "lte": "Меньше, чем равно",
                "not": "Не",
                "notEmpty": "Не пусто",
                "between": "Между",
                "notBetween": "Не между ними"
            },
            "array": {
                "equals": "Равно",
                "empty": "Пусто",
                "contains": "Содержит",
                "not": "Не равно",
                "notEmpty": "Не пусто",
                "without": "Без"
            }
        },
        "data": "Данные",
        "deleteTitle": "Удалить условие фильтрации",
        "logicAnd": "И",
        "logicOr": "Или",
        "title": {
            "0": "Конструктор поиска",
            "_": "Конструктор поиска (%d)"
        },
        "value": "Значение",
        "add": "Добавить условие",
        "button": {
            "0": "Конструктор поиска",
            "_": "Конструктор поиска (%d)"
        },
        "clearAll": "Очистить всё",
        "condition": "Условие",
        "leftTitle": "Превосходные критерии",
        "rightTitle": "Критерии отступа",
        "search": "Поиск"
    },
    "searchPanes": {
        "clearMessage": "Очистить всё",
        "collapse": {
            "0": "Панели поиска",
            "_": "Панели поиска (%d)"
        },
        "count": "{total}",
        "countFiltered": "{shown} ({total})",
        "emptyPanes": "Нет панелей поиска",
        "loadMessage": "Загрузка панелей поиска",
        "title": "Фильтры активны - %d",
        "showMessage": "Показать все",
        "collapseMessage": "Скрыть все"
    },
    "buttons": {
        "pdf": "PDF",
        "print": "Печать",
        "collection": "Коллекция <span class=\"ui-button-icon-primary ui-icon ui-icon-triangle-1-s\"><\/span>",
        "colvis": "Видимость столбцов",
        "colvisRestore": "Восстановить видимость",
        "copy": "Копировать",
        "copyTitle": "Скопировать в буфер обмена",
        "csv": "CSV",
        "excel": "Excel",
        "pageLength": {
            "-1": "Показать все строки",
            "_": "Показать %d строк",
            "1": "Показать 1 строку"
        },
        "removeState": "Удалить",
        "renameState": "Переименовать",
        "copySuccess": {
            "1": "Строка скопирована в буфер обмена",
            "_": "Скопировано %d строк в буфер обмена"
        },
        "createState": "Создать состояние",
        "removeAllStates": "Удалить все состояния",
        "savedStates": "Сохраненные состояния",
        "stateRestore": "Состояние %d",
        "updateState": "Обновить",
        "copyKeys": "Нажмите ctrl  или u2318 + C, чтобы скопировать данные таблицы в буфер обмена.  Для отмены, щелкните по сообщению или нажмите escape."
    },
    "decimal": ".",
    "infoThousands": ",",
    "autoFill": {
        "cancel": "Отменить",
        "fill": "Заполнить все ячейки <i>%d<i><\/i><\/i>",
        "fillHorizontal": "Заполнить ячейки по горизонтали",
        "fillVertical": "Заполнить ячейки по вертикали",
        "info": "Информация"
    },
    "datetime": {
        "previous": "Предыдущий",
        "next": "Следующий",
        "hours": "Часы",
        "minutes": "Минуты",
        "seconds": "Секунды",
        "unknown": "Неизвестный",
        "amPm": [
            "AM",
            "PM"
        ],
        "months": {
            "0": "Январь",
            "1": "Февраль",
            "10": "Ноябрь",
            "11": "Декабрь",
            "2": "Март",
            "3": "Апрель",
            "4": "Май",
            "5": "Июнь",
            "6": "Июль",
            "7": "Август",
            "8": "Сентябрь",
            "9": "Октябрь"
        },
        "weekdays": [
            "Вс",
            "Пн",
            "Вт",
            "Ср",
            "Чт",
            "Пт",
            "Сб"
        ]
    },
    "editor": {
        "close": "Закрыть",
        "create": {
            "button": "Новый",
            "title": "Создать новую запись",
            "submit": "Создать"
        },
        "edit": {
            "button": "Изменить",
            "title": "Изменить запись",
            "submit": "Изменить"
        },
        "remove": {
            "button": "Удалить",
            "title": "Удалить",
            "submit": "Удалить",
            "confirm": {
                "_": "Вы точно хотите удалить %d строк?",
                "1": "Вы точно хотите удалить 1 строку?"
            }
        },
        "multi": {
            "restore": "Отменить изменения",
            "title": "Несколько значений",
            "info": "Выбранные элементы содержат разные значения для этого входа. Чтобы отредактировать и установить для всех элементов этого ввода одинаковое значение, нажмите или коснитесь здесь, в противном случае они сохранят свои индивидуальные значения.",
            "noMulti": "Это поле должно редактироваться отдельно, а не как часть группы"
        },
        "error": {
            "system": "Возникла системная ошибка (<a target=\"\\\" rel=\"nofollow\" href=\"\\\">Подробнее<\/a>)."
        }
    },
    "searchPlaceholder": "Что ищете?",
    "stateRestore": {
        "creationModal": {
            "button": "Создать",
            "search": "Поиск",
            "columns": {
                "search": "Поиск по столбцам",
                "visible": "Видимость столбцов"
            },
            "name": "Имя:",
            "order": "Сортировка",
            "paging": "Страницы",
            "scroller": "Позиция прокрутки",
            "searchBuilder": "Редактор поиска",
            "select": "Выделение",
            "title": "Создать новое состояние",
            "toggleLabel": "Включает:"
        },
        "removeJoiner": "и",
        "removeSubmit": "Удалить",
        "renameButton": "Переименовать",
        "duplicateError": "Состояние с таким именем уже существует.",
        "emptyError": "Имя не может быть пустым.",
        "emptyStates": "Нет сохраненных состояний",
        "removeConfirm": "Вы уверены, что хотите удалить %s?",
        "removeError": "Не удалось удалить состояние.",
        "removeTitle": "Удалить состояние",
        "renameLabel": "Новое имя для %s:",
        "renameTitle": "Переименовать состояние"
    },
    "thousands": " "
};


function updateSavedFiltersSelect() {

  $.ajax({
              url: '/users/get_tables_states', 
              method: 'POST', 
              data: null,
              dataType: 'json',
              success: function(response) {
                  

                  
                  // console.log(JSON.parse(response.tables_states));
                  // console.log('список обновлен');
                
                  var savedFilters = JSON.parse(response.tables_states) || {};
                  var $select = $('#savedFiltersSelect');
                  $select.empty(); // Очистка списка перед добавлением новых элементов
                  
                  // Добавление опций в выпадающий список
                  Object.keys(savedFilters).forEach(function(filterName) {
                    $select.append($('<option>').val(filterName).text(filterName));
                  });


              },
              error: function(xhr, status, error) {
                  console.log('Ошибка при get_tables_states');
              }
          });
}

function savedFilters(filters, callback) {
  $.ajax({
    url: '/users/update_tables_states', 
    method: 'POST', 
    data: {
      statesData: JSON.stringify(filters)
    },
    success: function(response) {
      // console.log('сохранен!');
      if (typeof callback === "function") {
        callback(); // Вызов callback функции после успешного выполнения AJAX запроса
      }
    },
    error: function(xhr, status, error) {
      console.log('Ошибка при update_tables_states');
    }
  });
}

function updatecolReorder() {

  $.ajax({
              url: '/users/get_colreorder', 
              method: 'POST', 
              data: null,
              dataType: 'json',
              success: function(response) {
                  

                  
                  // console.log(JSON.parse(response.colReorder));
                  // console.log('список обновлен');
                
                  var savedFilters = JSON.parse(response.colReorder) || {};
                  var $select = $('#savedFiltersSelect');
                  $select.empty(); // Очистка списка перед добавлением новых элементов
                  
                  // Добавление опций в выпадающий список
                  Object.keys(savedFilters).forEach(function(filterName) {
                    $select.append($('<option>').val(filterName).text(filterName));
                  });


              },
              error: function(xhr, status, error) {
                  console.log('Ошибка при get_tables_states');
              }
          });
}

function savedcolReorder(filters, callback) {
  $.ajax({
    url: '/users/update_colreorder', 
    method: 'POST', 
    data: {
      colReorder: JSON.stringify(filters)
    },
    success: function(response) {
      // console.log('Сохранен: ' + JSON.parse(response.colReorder));

      if (typeof callback === "function") {
        callback(); // Вызов callback функции после успешного выполнения AJAX запроса
      }
    },
    error: function(xhr, status, error) {
      console.log('Ошибка при update_colReorder');
    }
  });
}

// отбрасывает лишние знаки после двух десятичных без округления (например, 1.999 станет 1.99)
function formatMoney2(number) {
    const truncated = Math.trunc(number * 100) / 100;
    return new Intl.NumberFormat('ru-RU', {
        style: 'decimal',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(truncated);
}

function formatMoney(number) {
    return number
}

function formatCount(number) {
    return new Intl.NumberFormat('ru-RU', { 
        style: 'decimal', 
        minimumFractionDigits: 0,
        maximumFractionDigits: 0  
    }).format(number);
}

function ExportExcel(dt, type, url){
    
    if (confirm('Уверены что хотите сделать экспорт?')) {

        var params = dt.ajax.params();

        params['type'] = type;
        params['start_date'] = document.getElementById('startDate').value;
        params['end_date'] = document.getElementById('endDate').value;
        params['user_login'] = document.getElementById('user_login').value;
        params['currency'] = document.getElementById('currency').value;
        params['name_table'] = document.getElementById('name_table').value;
        params['value_table'] = document.getElementById('value_table').value;


      var columnVisibility = dt.columns().visible().toArray();
      
      
      // Получаем названия всех столбцов
      var columnNames = dt.columns().header().toArray().map(function(header) {
          return $(header).text().trim(); // Убедитесь, что названия столбцов не содержат лишних пробелов
      });
      
      // Фильтруем названия, оставляя только видимые
      var visibleColumnNames = columnNames.filter(function(name, index) {
          return columnVisibility[index];
      });
      
      // Преобразуем массив названий в строку для URL
      var visibleColumnsParam = visibleColumnNames.join(','); // Используем запятую как разделитель
            

      $('#exportModal').modal({
          backdrop: 'static', // Предотвращение закрытия при клике вне модального окна
          keyboard: false     // Предотвращение закрытия с помощью клавиши Esc
      });

      $('#exportModal #downloadButton').hide();
      $('#exportModal #loadingAnimation').show();
      $('#exportModal #modalMessage').text('Пожалуйста, подождите... Идет экспорт данных.');
      $('#exportModal #downloadButton').hide();
      
      params['visibleColumns'] = visibleColumnsParam;
      
      console.log('Export excel:', params);

      $.ajax({
          url: url,
          method: 'POST',
          data: params,
          success: function(data) {

              console.log(data);
              
              if (data.status) {
                  // Скрыть анимацию и обновить сообщение
                  $('#exportModal #loadingAnimation').hide();
                  $('#exportModal #modalMessage').text('Ваш файл готов к скачиванию!');

                  // Показать кнопку скачивания
                  $('#exportModal #downloadButton').show().click(function() {
                      window.location.href = data.downloadUrl;
                  });
              }else{

                  // Скрыть анимацию и обновить сообщение
                  $('#exportModal #loadingAnimation').hide();
                  $('#exportModal #modalMessage').text('Включите следующиее столбцы: ' + data.not_requireds.join(', '));

              }
              
              

          },
          error: function(xhr, status, error) {
              console.error(xhr);
              // Скрыть анимацию загрузки
              $('#exportModal #loadingAnimation').hide();

              // Обновить сообщение модального окна для отображения ошибки
              $('#exportModal #modalMessage').text('Ошибка запроса. Пожалуйста попробуйте снова.');


              // Скрыть кнопку скачивания, так как экспорт не удался
              $('#exportModal #downloadButton').hide();
          },
          complete: function() {
              // Скрыть кнопку скачивания и кнопку закрытия при закрытии модального окна
              $('#exportModal').on('hidden.bs.modal', function () {
                  // $('#exportModal #downloadButton').hide();
                  // $('#loadingAnimation').show();
                  // $('#modalMessage').text('Пожалуйста, подождите... Идет экспорт данных.');
              });
          }
      
      });

    }


    // Закрываем основное модальное окно
    $('#exportModal .closez').off('click').on('click', function() {
        if (confirm('Уверены что хотите закрыть?')) {
            $('#exportModal').modal('hide');
        }
    });
    $('#exportModal .closex').off('click').on('click', function() {
        $('#exportModal').modal('hide');
    });


}

function ExportReport(dt, type){


    $('#summaryModal').modal({
        backdrop: 'static', // Предотвращение закрытия при клике вне модального окна
        keyboard: false     // Предотвращение закрытия с помощью клавиши Esc
    });


    var params = dt.ajax.params();

    
    //сохранить type
    document.getElementById('report_type').innerText = type;


    params['type'] = type;
    params['start_date'] = document.getElementById('startDate').value;
    params['end_date'] = document.getElementById('endDate').value;
    params['user_login'] = document.getElementById('user_login').value;
    params['currency'] = document.getElementById('currency').value;
    params['name_table'] = document.getElementById('name_table').value;
    params['value_table'] = document.getElementById('value_table').value;


    console.log('Export report:', params);

    $('#summaryModal #otchet').hide();
    $('#summaryModal #loadingAnimation').show();
    $('#summaryModal #modalMessage').text('Пожалуйста, подождите... Идет формирование отчета.');
    $('#summaryModal #modalMessage').show();
    $('#summaryModal').modal('show');



    $.ajax({
        url: '/summaryTable',
        method: 'POST',
        data: params,
        success: function(data) {
            
            console.log(data);

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
            
            //показать кнопку Отправить отчет
            $('#submitReport').fadeIn();


        },
        error: function(xhr, status, error) {
            $('#summaryModal #loadingAnimation').hide();
            $('#summaryModal #modalMessage').text('Ошибка запроса...');


            // Вывод статуса ошибки
            console.error(xhr);


        }
    });


    // Закрываем основное модальное окно
    $('#summaryModal .closez').off('click').on('click', function() {
        if (confirm('Уверены что хотите закрыть?')) {
            $('#summaryModal').modal('hide');
            $('#submitReport').hide();
        }
    });
    $('#summaryModal .closex').off('click').on('click', function() {
        $('#summaryModal').modal('hide');
        $('#submitReport').hide();
    });
    

}



var DefaultFilter = {
                "criteria": [
                    {
                        "condition": "between",
                        "data": "Дата формирования",
                        "origData": "tickets.tickets_dealdate",
                        "type": "date",
                        "value": [
                            decade_start_date,
                            decade_end_date
                        ]
                    }
                ],
                "logic": "AND"
};
var searchBuilderParams = {};


// кнопки для Сформировать отчет
var buttons_report = [];
buttons_report.push({
    text: 'по умолчанию',
    action: function (e, dt, node, config) {
        var type = "default";
        ExportReport(dt, type);
    }
});


var buttons_excel = [];
buttons_excel.push({
    text: 'по умолчанию',
    action: function (e, dt, node, config) {
        var type = "default";
        var url = '/bigexport';
        ExportExcel(dt, type, url);
    }
});


if (is_acquiring == 1) {
    buttons_report.push({
        text: 'для сайта',
        action: function (e, dt, node, config) {
            console.log('для сайта');
            var type = "site";
            ExportReport(dt, type);
        }
    });
    buttons_excel.push({
        text: 'для сайта',
        action: function (e, dt, node, config) {
            console.log('для сайта');
            var type = "site";
            ExportExcel(dt, type);
        }
    });
}

if (is_airline == 1) {
    buttons_report.push({
        text: 'для авиакомпании',
        action: function (e, dt, node, config) {
            console.log('для авиакомпании');
            var type = "airline";
            ExportReport(dt, type);
        }
    });
    buttons_excel.push({
        text: 'для авиакомпании',
        action: function (e, dt, node, config) {
            console.log('для авиакомпании');
            var type = "airline";
            ExportExcel(dt, type);
        }
    });
}





// Кнопки
var buttons = [
    
    // Показать строк
    { extend: 'pageLength' },

    // Видимость столбцов
    { extend: 'colvis', text: 'Видимость столбцов', },

    // Управления фильтрами
    {
    text: 'Управление фильтрами',
    action: function ( e, dt, node, config ) {

        updateSavedFiltersSelect();

        $("#filterModalLabel").text("Управления фильтрами");
    
        $('#saveFilterModal').modal('show');

        
        // Сохранить
        $('#save-filter').off('click').on('click', function() {
        $.ajax({
                url: '/users/get_tables_states', 
                method: 'POST', 
                data: null,
                dataType: 'json',
                success: function(response) {
                // console.log(JSON.parse(response.tables_states));

                var filterName = $('#filter-name').val().trim();
                if (!filterName) {
                    alert('Пожалуйста, введите имя фильтра.');
                    return;
                }

                var filters = JSON.parse(response.tables_states) || {};
                if (filters[filterName]) {
                    if (!confirm('Фильтр с таким именем уже существует. Перезаписать?')) {
                    return; 
                    }
                }

                
                // Получаем состояние SearchBuilder
                var state = table.searchBuilder.getDetails();


                // Сохраняем состояние фильтра под выбранным именем
                filters[filterName] = state;
                

                // console.log(state);

                
                // отправка на сервер
                savedFilters(filters, updateSavedFiltersSelect); 

                

                // Очистка поля ввода
                $('#filter-name').val('');

                }

        });         
        });

        
        // Применить
        $('#apply-filter').off('click').on('click', function() {
        
            $('#saveFilterModal').modal('hide');

            $.ajax({
                url: '/users/get_tables_states', 
                method: 'POST', 
                dataType: 'json',
                success: function(response) {
                    
                    // console.log(JSON.parse(response.tables_states));

                
                    var selectedFilterName = $('#savedFiltersSelect').val();
                    if (selectedFilterName) {
                        var filters = JSON.parse(response.tables_states);
                        var selectedFilterState = filters[selectedFilterName];
                        if (selectedFilterState) {

                            // Применение фильтра
                            table.searchBuilder.rebuild(selectedFilterState);
                            
                            // console.log("Применен фильтр:", selectedFilterState);
                            
                        }
                    } else {
                        alert('Пожалуйста, выберите фильтр для применения.');
                    }


                }

            });
        });


        // Удалить
        $('#delete-filter').off('click').on('click', function() {
            $.ajax({
            url: '/users/get_tables_states', 
            method: 'POST', 
            data: null,
            dataType: 'json',
            success: function(response) {
                var selectedFilterName = $('#savedFiltersSelect').val();
                if (selectedFilterName && confirm('Вы уверены, что хотите удалить выбранный фильтр?')) {
                var filters = JSON.parse(response.tables_states);
                if (filters && filters[selectedFilterName]) {
                    delete filters[selectedFilterName]; // Удаление выбранного фильтра
                    savedFilters(filters, updateSavedFiltersSelect); 
                } else {
                    alert('Ошибка при удалении фильтра. Возможно, фильтр уже был удален.');
                }
                }
            }
            });
        });

        // Сброс
        $('#sbros-filter').off('click').on('click', function() {
        
            $('#saveFilterModal').modal('hide');

            // Применение фильтра
            table.searchBuilder.rebuild(DefaultFilter);
            // console.log("Применен фильтр:", DefaultFilter);

        });


    }
    },

    // Управления колонками
    {
    text: 'Управление колонками',
    action: function ( e, dt, node, config ) {

        updatecolReorder();

        $("#filterModalLabel").text("Управления колонками");
        $('#sbros-filter').show();
        $('#saveFilterModal').modal('show');

        // Сохранить
        $('#save-filter').off('click').on('click', function() {
        $.ajax({
                url: '/users/get_colreorder', 
                method: 'POST',
                dataType: 'json',
                success: function(response) {

                // console.log(JSON.parse(response));

                var filterName = $('#filter-name').val().trim();
                if (!filterName) {
                    alert('Пожалуйста, введите имя настройки.');
                    return;
                }

                var filters = JSON.parse(response.colReorder) || {};
                if (filters[filterName]) {
                    if (!confirm('Настройка с таким именем уже существует. Перезаписать?')) {
                    return; 
                    }
                }

                // Инициализируем чтобы добавить ключ
                if (!filters[filterName]) {
                    filters[filterName] = {};
                }
                
                // Сохраняем расположения колонок 
                filters[filterName]['colReorder'] = table.colReorder.order();

                // Сохраняем видимость колонок 
                filters[filterName]['columnVisibility'] = table.columns().visible().toArray();
                

                // отправка на сервер
                savedcolReorder(filters, updatecolReorder); 

                
                // Очистка поля ввода
                $('#filter-name').val('');

                }

        });                               
        });

        
        // Применить
        $('#apply-filter').off('click').on('click', function() {
        
            $('#saveFilterModal').modal('hide');

            $.ajax({
                url: '/users/get_colreorder', 
                method: 'POST', 
                data: null,
                dataType: 'json',
                success: function(response) {
                    // console.log('Применить');
                    // console.log(JSON.parse(response.colReorder));

                
                    var selectedFilterName = $('#savedFiltersSelect').val();
                    if (selectedFilterName) {
                        var filters = JSON.parse(response.colReorder);
                        var selectedFilterState = filters[selectedFilterName]['colReorder'];
                        var columnVisibility = filters[selectedFilterName]['columnVisibility'];

                        if (selectedFilterState) {
                            // Применение фильтра
                            table.colReorder.order(selectedFilterState);
                            // console.log("Применен фильтр:", selectedFilterState);
                        }


                        if (columnVisibility) {
                        // Применение настроек видимости к каждому столбцу
                        columnVisibility.forEach(function(visible, index) {
                            table.column(index).visible(visible);
                        });
                        }


                    } else {
                        alert('Пожалуйста, выберите фильтр для применения.');
                    }


                }

            });
        });


        // Удалить
        $('#delete-filter').off('click').on('click', function() {
            $.ajax({
            url: '/users/get_colreorder', 
            method: 'POST', 
            data: null,
            dataType: 'json',
            success: function(response) {
                var selectedFilterName = $('#savedFiltersSelect').val();
                if (selectedFilterName && confirm('Вы уверены, что хотите удалить выбранный фильтр?')) {
                var filters = JSON.parse(response.colReorder);
                if (filters && filters[selectedFilterName]) {
                    delete filters[selectedFilterName]; // Удаление выбранного фильтра
                    savedcolReorder(filters, updatecolReorder); 
                } else {
                    alert('Ошибка при удалении фильтра. Возможно, фильтр уже был удален.');
                }
                }
            }
            });
        });

        // Сбросить
        $('#sbros-filter').off('click').on('click', function() {

            $('#saveFilterModal').modal('hide');

            table.colReorder.reset();

            // Сделать все столбцы видимыми
            table.columns().every(function() {
                this.visible(true);
            });

        });



    }
    },

    // Экспорт в Excel
    {
        text: 'Экспорт в Excel',
        className: 'all-file-excel',
        action: function(e, dt, node, config) {
            var type = "default";
            var url = '/allexport';
            ExportExcel(dt, type, url);
            
        }
    },

    // Формировать отчет
    {
        extend: 'collection',
        className: 'report-excel',
        autoClose: true, 
        text: 'Сформировать отчёт',
        buttons: buttons_report
    },

    // Экспорт в Excel
    {
        extend: 'collection',
        className: 'file-excel',
        autoClose: true, 
        text: 'Сформировать отчёт в Excel',
        buttons: buttons_excel
    },

];




var columns = [
        // Таблица "tickets"
        { data: 'tickets.tickets_type', title: 'Тип билета'}, 
        { data: 'tickets.tickets_currency', title: 'Валюта билета'}, 
        { data: 'tickets.tickets_dealdate', title: 'Дата формирования'}, 
        { data: 'tickets.tickets_dealtime', title: 'Время формирования'},
        { data: 'tickets.tickets_OPTYPE', title: 'Тип операции'}, 
        { data: 'tickets.tickets_TRANS_TYPE', title: 'Тип транзакции'}, 
        { data: 'tickets.tickets_BSONUM', title: 'Номер билета'}, 
        { data: 'tickets.tickets_EX_BSONUM', title: 'Номер старшего билета' }, 
        { data: 'tickets.tickets_TO_BSONUM', title: 'Номер основного билета' }, 
        { data: 'tickets.tickets_FARE', title: 'Тариф цена'}, 
        { data: 'tickets.tickets_PNR_LAT', title: 'PNR'},
        { data: 'tickets.tickets_DEAL_date', title: 'Дата оформления'}, 
        { data: 'tickets.tickets_DEAL_disp', title: 'Индентификатор продавца'},  
        { data: 'tickets.tickets_DEAL_time', title: 'Время оформления'}, 
        { data: 'tickets.tickets_DEAL_utc', title: 'Время оформления UTC'}, 
        { data: 'tickets.summa_no_found', title: 'Сумма обмена без EMD'},
        // Таблица "opr"
        { data: 'opr.opr_code', title: 'Код оператора'},
        // Таблица "share" (Раздача)
        { data: 'share.share_code', title: 'Код раздачи', name: 'share.share_code'},
        // Таблица "agency"
        { data: 'agency.agency_code', title: 'Код агентства'},
        // Таблица "emd"
        { data: 'emd.emd_value', title: 'Сумма EMD'},
        // Таблица "fops"
        { data: 'fops.fops_type', title: 'Вид оплаты'},
        { data: 'fops.fops_amount', title: 'Сумма оплаты'},
        // Таблица "passengers"
        { data: 'passengers.fio', title: 'ФИО'},
        { data: 'passengers.pass', title: 'Паспорт'},
        { data: 'passengers.pas_type', title: 'Тип'},
        { data: 'passengers.citizenship', title: 'Гражданство'},
        // Таблица "segments"
        { data: 'segments.citycodes', type: 'string', title: 'Маршрут'},
        { data: 'segments.carrier', title: 'Перевозчик'},
        { data: 'segments.class', title: 'Класс'},
        { data: 'segments.reis', title: 'Рейс'},
        { data: 'segments.flydate', title: 'Дата полёта'},
        { data: 'segments.flytime', title: 'Время полёта'},
        { data: 'segments.basicfare', title: 'Тариф'},
        // Таблица "stamp"
        { data: 'stamp.stamp_code', title: 'Код ППР'},
        // Таблица "tap"
        { data: 'tap.tap_code', title: 'Код пульта'},
        // Таблица "taxes"
        { data: 'taxes.tax_code', title: 'Код сбора'},
        { data: 'taxes.tax_amount', title: 'Сумма сбора'},
        { data: 'taxes.tax_amount_main', title: 'Суммы сборов'},
        { data: 'custom.reward_procent', searchable: false, orderable: false, title: 'Процент вознаграждение'},
        { data: 'custom.reward', searchable: false, orderable: false, title: 'Вознаграждение'},
        { data: 'custom.penalty_currency', searchable: false, orderable: false, title: 'Курс валюты'},
        { data: 'custom.penalty_summa', searchable: false, orderable: false, title: 'Сумма штрафа'},
        { data: 'custom.penalty', searchable: false, orderable: false, title: 'Штраф'},
        
];



// Преобразовать объект в массив значений
var uniqueTaxCodes2 = Object.keys(uniqueTaxCodes).map(function(key) {
    return uniqueTaxCodes[key];
});

// Добавляем заголовки в таблицу
var headersRow = $('#dynamic-headers');
uniqueTaxCodes2.forEach(function(code) {
// Object.keys(uniqueTaxCodes).forEach(function(code) {
    headersRow.append('<th>' + code + '</th>');
    columns.push({ data: `tax.${code}`, searchable: false, orderable: false });

});



var table = $('#operations').DataTable({
        
        language: ruLanguage,
        searchBuilder: {
          liveSearch: false,
          // preDefined: DefaultFilter,

        
        },
        columnDefs: [
            { 
                type: 'date', 
                targets: 2,
            }
        ],

        colReorder: true,
        pagingType: 'simple',
        ajax: {
                url: '/php/operations.php',
                type: 'POST',
        
                data: function(d) {

                    // показываем слой загрузки
                    showLoading();

                    d.page = 'operations';
                    d.user_login = $('#user_login').val();
                    d.name_table = $('#name_table').val();
                    d.currency = $('#currency').val();
                    d.key = myVar;
                    d.colum_name = colum_name;
                    d.start_date = start_date;
                    d.end_date = end_date;
                    d.sss = JSON.stringify(searchBuilderParams);
                    d.uniqueTaxCodes = uniqueTaxCodes;


                    // console.log("php colum_name:", colum_name);
                    // console.log("php ids:", myVar);
                    
                    // console.log("ajax", searchBuilderParams);
                    // console.log("start_date", start_date);
                    // console.log("end_date", end_date);
                    
                },
                // dataSrc: function (json) {
                //     // Получаем uniqueTaxCodes
                //     // uniqueTaxCodes = json.options.uniqueTaxCodes;
                //     // formattedData = json.options.formattedData;

                //     return json.data;
                // },

                // dataFilter: function(data){
                //   console.log(data);
                //   return data;
                // }
            },
        buttons: buttons,
        dom: 'QBfrtip',
        processing: true,
        columns: columns,
        stateSave: true,
        select: true,
        serverSide: true,
        drawCallback: function(row, data, start, end, display) {

            // Получаем текущие параметры запроса
            var params = this.api().ajax.params();

            // console.log('drawCallback', params);


            $('#summary_text').remove();


            // downTable
            $.ajax({
                url: '/calculateSummary',
                method: 'POST',
                data: params,
                success: function(data) {
                    
                    // console.log(data);

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
                    hideLoading();


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
                    


        },
        lengthMenu: [ [5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, 'Все'] ],
        // lengthMenu: [ [5, 10, 25, 50, 100], [5, 10, 25, 50, 100] ],
        pageLength: 5,
        scrollY: true,
        scrollCollapse: true,
        scrollX: true
});






table.on('search.dt', function() {
    searchBuilderParams = table.searchBuilder.getDetails();
    // console.log('search.dt', searchBuilderParams);
});





        function set_active_filter(start_date, end_date, callback) {

            // изменить активный параметр
            const currency = document.getElementById('currency').value;
            const name_table = document.getElementById('name_table').value;
            const value_table = document.getElementById('value_table').value;
            const user_id = document.getElementById('user_login').value;

            // console.log('изменить активный параметр');

            $.ajax({
                url: '/operations/get_active_params',
                method: 'POST',
                data: {
                  user_id: user_id,
                  filter: name_table,
                },
                success: function(data) {
                    
                    // console.log('получил colum_name:', data.colum_name);
                    // console.log('получил ids:', data.ids);

                    myVar = data.ids;
                    colum_name = data.colum_name;


                    if (typeof callback === "function") {
                        callback(start_date, end_date); // Вызов callback функции
                    }

                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        function getCriteriaNameTable(name_table, value_table){

            var criteria;
            // console.log('getCriteriaNameTable - name_table:', name_table, 'value_table:', value_table);

            if (value_table !== 'all') {
                if (name_table === "agency") {
                    criteria = {
                                "condition": "=",
                                "data": "Код агентства",
                                "origData": "agency.agency_code",
                                "type": "string",
                                "value": [value_table]
                            };
                }else if(name_table === "stamp"){
                    criteria = {
                                "condition": "=",
                                "data": "Код ППР",
                                "origData": "stamp.stamp_code",
                                "type": "string", // Коды обычно строки
                                "value": [value_table]
                            };
                }else if(name_table === "tap"){
                    criteria = {
                                "condition": "=",
                                "data": "Код пульта",
                                "origData": "tap.tap_code",
                                "type": "string", 
                                "value": [value_table]
                            };
                }else if(name_table === "opr"){
                    criteria = {
                                "condition": "=",
                                "data": "Код оператора",
                                "origData": "opr.opr_code",
                                "type": "string", // Коды обычно строки
                                "value": [value_table]
                            };
                } else if (name_table === "share") { 
                    criteria = {
                                "condition": "=",
                                "data": "Код раздачи",      
                                "origData": "share.share_code", 
                                "type": "string", 
                                "value": [value_table]   
                            };
                        }
            }else{
                criteria = false;
            }
            

            return criteria;
        }

        function applySearchBuilder(start_date, end_date){

            // console.log('установка значений в Конструктор');

            const currency = document.getElementById('currency').value;
            const name_table = document.getElementById('name_table').value;
            const value_table = document.getElementById('value_table').value;
            const user_id = document.getElementById('user_login').value;

            var criteria = getCriteriaNameTable(name_table, value_table);

            // Конструктор
            if (criteria) {
                var filter = {
                    "criteria": [
                        {
                            "condition": "between",
                            "data": "Дата формирования",
                            "origData": "tickets.tickets_dealdate",
                            "type": "date",
                            "value": [
                                start_date,
                                end_date
                            ]
                        },
                        {
                            "condition": "=",
                            "data": "Валюта билета",
                            "origData": "tickets.tickets_currency",
                            "type": "string",
                            "value": [currency]
                        },  criteria                      

                    ],
                    "logic": "AND"
                };
            }else{
                var filter = {
                    "criteria": [
                        {
                            "condition": "between",
                            "data": "Дата формирования",
                            "origData": "tickets.tickets_dealdate",
                            "type": "date",
                            "value": [
                                start_date,
                                end_date
                            ]
                        },
                        {
                            "condition": "=",
                            "data": "Валюта билета",
                            "origData": "tickets.tickets_currency",
                            "type": "string",
                            "value": [currency]
                        },                        

                    ],
                    "logic": "AND"
                };
            }
            

            // Применение фильтра
            table.searchBuilder.rebuild(filter);
        }


        $('#submitBtn').click(function(){
            
            addDeleteClass();

            var start_date = document.getElementById('startDate').value;
            var end_date = document.getElementById('endDate').value;

            set_active_filter(start_date, end_date, applySearchBuilder);
        });

        $('#submitBtnToday').click(function(){

            addDeleteClass();

            document.getElementById('startDate').value = today;
            document.getElementById('endDate').value = today;
          
            set_active_filter(today, today, applySearchBuilder);

            // Изменяем классы кнопок
            $(this).removeClass('btn-primary').addClass('bg-gradient-warning');
        });

        $('#submitBtnYesterday').click(function(){

            addDeleteClass();

            document.getElementById('startDate').value = yesterday;
            document.getElementById('endDate').value = yesterday;

            set_active_filter(yesterday, yesterday, applySearchBuilder);
        

            // Изменяем классы кнопок
            $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
        });

        $('#submitBtnOneDecade').click(function(){

            addDeleteClass();

            document.getElementById('startDate').value = firstDecadeStart;
            document.getElementById('endDate').value = firstDecadeEnd;

            set_active_filter(firstDecadeStart, firstDecadeEnd, applySearchBuilder);

            // Изменяем классы кнопок
            $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
        });

        $('#submitBtnTwoDecade').click(function(){

            addDeleteClass();

            document.getElementById('startDate').value = secondDecadeStart;
            document.getElementById('endDate').value = secondDecadeEnd;

            set_active_filter(secondDecadeStart, secondDecadeEnd, applySearchBuilder);

            // Изменяем классы кнопок
            $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
        });

        $('#submitBtnThreeDecade').click(function(){

            addDeleteClass();

            document.getElementById('startDate').value = thirdDecadeStart;
            document.getElementById('endDate').value = thirdDecadeEnd;

            set_active_filter(thirdDecadeStart, thirdDecadeEnd, applySearchBuilder);

            // Изменяем классы кнопок
            $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
        });

        $('#submitBtnThisMonth').click(function(){

            addDeleteClass();

            document.getElementById('startDate').value = thisMonthFirst;
            document.getElementById('endDate').value = thisMonthLast;

            set_active_filter(thisMonthFirst, thisMonthLast, applySearchBuilder);

            // Изменяем классы кнопок
            $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
        });

        $('#submitBtnLastMonth').click(function(){

            addDeleteClass();

            document.getElementById('startDate').value = lastMonthFirst;
            document.getElementById('endDate').value = lastMonthLast;

            set_active_filter(lastMonthFirst, lastMonthLast, applySearchBuilder);

            // Изменяем классы кнопок
            $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
        });


    


// отправить отчет
$('#submitReport').click(function(){


    // Делаем кнопку неактивной и добавляем анимацию
    var $submitButton = $(this);
    $submitButton.prop('disabled', true); // Отключаем кнопку
    $submitButton.html('<i class="fas fa-spinner fa-spin"></i> Отправка...'); // Добавляем иконку загрузки



    // Получаем параметры
    var form = document.getElementById('myForm');
    var formData = new FormData(form);

    // Получить report_type
    var report_type = document.getElementById('report_type').innerText;
    formData.append('report_type', report_type); 

    console.log("formData: ", formData);

    $.ajax({
        url: '/reports/sendreport',
        method: 'POST',
        data: formData,
        processData: false, // Не обрабатываем данные
        contentType: false, // Устанавливаем правильный заголовок
        success: function(response) {

            console.log(response);


            if (response.status === 'success') {
                toastr.success(response.message);
            } else {
                toastr.error(response.message);
            }


            // Возвращаем кнопку в активное состояние и восстанавливаем текст
            $submitButton.prop('disabled', false);
            $submitButton.html('Отправить отчёт');

        },
        error: function(error) {
            console.log("error: ", error);


            // Возвращаем кнопку в активное состояние и восстанавливаем текст
            $submitButton.prop('disabled', false);
            $submitButton.html('Отправить отчёт');


        }
    });

    
});       


    



        // Применение фильтра по умолчанию
        table.on('init', function() {
            
            // console.log('Таблица загружена');

            var start_date = document.getElementById('startDate').value;
            var end_date = document.getElementById('endDate').value;

            set_active_filter(start_date, end_date, applySearchBuilder);


        });










} //link
    
   
});
