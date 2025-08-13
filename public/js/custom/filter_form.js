document.addEventListener('DOMContentLoaded', function () {

        const tableNameSelect = document.querySelector('select[name="name_table"]');
        const valueTableSelect = document.querySelector('select[name="value_table"]');
        const userLoginSelect = document.getElementById('user_login');
        const is_refresh = document.getElementById('is_refresh').value;
        const selected_name_table = document.getElementById('selected_name_table').value;

        // Функция для обновления списка значений name_table
        function update_name_table() {
          const user_id = document.getElementById('user_login').value;
          const url = `/profile/four_params_json?user_id=${user_id}`;
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        // console.log(data);
                        tableNameSelect.innerHTML = '';
                        // tableNameSelect.innerHTML = '<option value="all">- Все -</option>';

                        // Добавление новых опций
                        data.forEach(item => {
                            const option = new Option(item['name'], item['value']);
                            tableNameSelect.add(option);

                            // Установить по умолчанию
                            if (selected_name_table) {
                                if (item['value'] === selected_name_table) {
                                    option.selected = true;
                                }
                            }
                            
                            // active_filter
                            // if (active_filter) {
                            //     if (item['value'] === active_filter) {
                            //         option.selected = true;
                            //     }
                            // }
                            

                        });


                        

                        //обновить 
                        update_value_table();

                    })
                    .catch(error => {
                        console.error('Ошибка:', error);
                        valueTableSelect.disabled = true;
                    });
        }

        
        // Функция для обновления списка значений value_table
        function update_value_table() {
            const tableName = document.querySelector('select[name="name_table"]').value;
            const userId = document.getElementById('user_login').value;
            const selected_value_table = document.getElementById('selected_value_table').value;
            
            valueTableSelect.disabled = true; // сделать неактивным
            
            // Проверяем, выбрано ли допустимое значение
            if (tableName !== "all") {
                const url = `/gettable?table_name=${encodeURIComponent(tableName)}&user_id=${encodeURIComponent(userId)}`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        valueTableSelect.innerHTML = '';

                        if (window.location.pathname !== '/transactions/create' && window.location.pathname !== '/services/create') {
                            valueTableSelect.innerHTML = '<option value="all">- Все -</option>';
                        }
                        

                        // Добавление новых опций
                        data.forEach(item => {
                            var var1 = tableName + '_code';
                            var var2 = tableName + '_id';
                            var var3 = tableName + '_name';
                            var option;
                            var option_name = '';

                            // добавить имя опцию
                            if (item[var3]) {
                                option_name = ' (' + item[var3] + ')';
                            }
                            
                            // создать опцию - для транзакций и сервисов используем ID, для остального - код
                            if (window.location.pathname === '/transactions/create' 
                                || window.location.pathname === '/transactions'
                                || window.location.pathname.startsWith('/transactions/edit/')
                                || window.location.pathname === '/services/create'
                                || window.location.pathname.startsWith('/services/edit/')
                                || window.location.pathname === '/services'){
                                option = new Option(item[var1] + option_name, item[var2]);
                            }else{
                                option = new Option(item[var1] + option_name, item[var1]);
                            }

                            // Значение по умолчанию
                            if (selected_value_table) {
                                // Для транзакций и сервисов сравниваем с ID, для остального - с кодом
                                if (window.location.pathname === '/transactions/create' 
                                    || window.location.pathname === '/transactions'
                                    || window.location.pathname.startsWith('/transactions/edit/')
                                    || window.location.pathname === '/services/create'
                                    || window.location.pathname === '/services'){
                                    if (item[var2] === selected_value_table) {
                                        option.selected = true;
                                    }
                                }else{
                                    if (item[var1] === selected_value_table) {
                                        option.selected = true;
                                    }
                                }
                            }

                            valueTableSelect.add(option);

        

                            
                    



                        });

                        valueTableSelect.disabled = false; // Сделать активным
                        document.getElementById('is_update').value = 'yes';

                        

                    })
                    .catch(error => {
                        console.error('Ошибка:', error);
                        // valueTableSelect.disabled = true; // Деактивируем в случае ошибки
                    });
            } else {
                // Сделать неактивным и очистить, если выбрано неопределённое значение
                valueTableSelect.disabled = true;
                valueTableSelect.innerHTML = '';
                valueTableSelect.innerHTML = '<option value="all">- Все -</option>';
                document.getElementById('is_update').value = 'yes';
            }
        }

        // Вызов функции при изменении значения в tableNameSelect и userLoginSelect
        userLoginSelect.addEventListener('input', update_name_table);
        tableNameSelect.addEventListener('input', update_value_table);
        

        // при загрузке страницы
        if (is_refresh === "yes") {
            update_name_table();
        }
        





        // ======== Сохранить сессии =========


        
        $('.button_spacing').click(function() {

            // console.log('Кнопка сохранить сессии');


            // Выполняем код через 1 секунду
            setTimeout(function() {

                // var startDate = document.getElementById('startDate').value;
                // console.log(startDate);

                var form = document.getElementById('myForm');
                var formData = new FormData(form);

                updateSession(formData);



            }, 500); // Задержка (1 секунда)


            
        });


       

        // обновить сессию
        function updateSession(formData) {

            // console.log(formData);

            $.ajax({
                url: '/profile/updateSession',
                method: 'POST',
                data: formData,
                processData: false, // Не обрабатываем данные
                contentType: false, // Устанавливаем правильный заголовок
                success: function(response) {
                    //console.log(response);
                    //console.log('Session updated');
                },
                error: function() {
                    console.error('Error updating session');
                }
            });



        }




        // получить сессию
        function getSessionValue(param) {
            $.ajax({
                url: '/profile/getSessionValue',
                method: 'POST',
                data: {
                    param: param
                },
                success: function(response) {
                    
                    if (response.status) {
                        console.log('Session value:', response.value);
                        // var id = '#' + param;
                        // $(id).val(response.value);

                    }
                    
                },
                error: function() {
                    console.error('Error fetching session value');
                }
            });
        }



        // активировать кнопки

        const button = document.querySelector('.button_spacing');
        if (button) {
            lampa();
        }



});




// функции
function showLoading_c(c) {
    var overlays = document.getElementsByClassName(c);
    for (var i = 0; i < overlays.length; i++) {
        overlays[i].classList.remove('d-none');
    }
}

function hideLoading_c(c) {
    var overlays = document.getElementsByClassName(c);
    for (var i = 0; i < overlays.length; i++) {
        overlays[i].classList.add('d-none');
    }     
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

function showLoading2(id) {
  var overlay = document.getElementById(id);
  overlay.classList.remove('d-none');
}

function hideLoading2(id) {
  var overlay = document.getElementById(id);
  overlay.classList.add('d-none');
}

function addDeleteClass() {
  $('#submitBtnToday, #submitBtnYesterday, #submitBtnOneDecade, #submitBtnTwoDecade, #submitBtnThreeDecade, #submitBtnThisMonth, #submitBtnLastMonth').removeClass('bg-gradient-warning').addClass('bg-gradient-primary');
}





function lampa(){

    // Получаем значение из поля ввода
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    // console.log(startDate);


    // Сравниваем даты
    if (startDate == today && endDate == today) {
        $('#submitBtnToday').removeClass('btn-primary').addClass('bg-gradient-warning');
    } else if (startDate == yesterday && endDate == yesterday) {
        $('#submitBtnYesterday').removeClass('btn-primary').addClass('bg-gradient-warning');
    } else if (startDate == firstDecadeStart && endDate == firstDecadeEnd) {
        $('#submitBtnOneDecade').removeClass('btn-primary').addClass('bg-gradient-warning');
    } else if (startDate == secondDecadeStart && endDate == secondDecadeEnd) {
        $('#submitBtnTwoDecade').removeClass('btn-primary').addClass('bg-gradient-warning');
    } else if (startDate == thirdDecadeStart && endDate == thirdDecadeEnd) {
        $('#submitBtnThreeDecade').removeClass('btn-primary').addClass('bg-gradient-warning');
    } else if (startDate == thisMonthFirst && endDate == thisMonthLast) {
        $('#submitBtnThisMonth').removeClass('btn-primary').addClass('bg-gradient-warning');
    }else if (startDate == lastMonthFirst && endDate == lastMonthLast) {
        $('#submitBtnLastMonth').removeClass('btn-primary').addClass('bg-gradient-warning');
    }

}


