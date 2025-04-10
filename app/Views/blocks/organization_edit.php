<?php
    $class_col = "col-md-6";

    if ($hidden !== true) {
        $class_col = "col-md-12";
    }
    

?>
<!-- left column -->
<div class="<?= $class_col ?>">
<!-- general form elements -->
<div class="card card-primary">
  <div class="card-header">
    <h3 class="card-title">Изменить данные</h3>
  </div>
  <!-- /.card-header -->
  <!-- form start -->
  <form role="form" method="post" action="<?= base_url('/'.$name.'/update/' . $data['id']) ?>">
    <div class="card-body">
        <div class="form-group">
            <label for="code">Код</label>
            <input type="text" class="form-control" id="code" name="code" placeholder="Введите код" value="<?= $data['code'] ?>" <?= $role !== 'superadmin' ? 'disabled' : '' ?>>
        </div>
        <div class="form-group">
            <label for="name">Название</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Введите название" value="<?= $data['name'] ?>">
        </div>
        <div class="form-group">
            <label for="address">Адрес</label>
            <input type="text" class="form-control" id="address" name="address" placeholder="Введите адрес" value="<?= $data['address'] ?>">
        </div>
        <div class="form-group">
            <label for="phone">Телефон</label>
            <input type="tel" class="form-control" id="phone" name="phone" placeholder="Введите телефон" value="<?= $data['phone'] ?>">
        </div>
        <div class="form-group">
            <label for="mail">Электронная почта</label>
            <input type="email" class="form-control" id="mail" name="mail" placeholder="Введите email" value="<?= $data['mail'] ?>">
        </div>
        

        <?php if ($role === 'superadmin') : ?>
        <div class="form-group">
            <label for="balance_tjs">Баланс TJS</label>
            <input type="number" class="form-control" id="balance_tjs" name="balance_tjs" placeholder="Введите баланс" value="<?= $data['balance_tjs'] ?>">
        </div>
        <div class="form-group">
            <label for="balance_rub">Баланс RUB</label>
            <input type="number" class="form-control" id="balance_rub" name="balance_rub" placeholder="Введите баланс" value="<?= $data['balance_rub'] ?>">
        </div>
        <?php endif; ?>

        <?php if ($hidden == true) : ?>
        <div class="form-group">
            <label for="penalty">Штраф</label>
            <input type="number" class="form-control" id="penalty" name="penalty" placeholder="Введите сумму штрафа" value="<?= $data['penalty'] ?>">
        </div>
        <div class="form-group">
            <label for="reward">Вознаграждение</label>
            <input type="number" class="form-control" id="reward" name="reward" placeholder="Введите сумму вознаграждения" value="<?= $data['reward'] ?>">
        </div>
        <?php endif; ?>


    </div>
    <!-- /.card-body -->

    <div class="card-footer">
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="<?= base_url('/'.$name ) ?>" class="btn btn-warning">Назад</a>
    </div>
  </form>


</div>
<!-- /.card -->
</div>

<?php if ($hidden === true) : ?>
<!-- right column -->
<div class="col-md-6">
<!-- Form Element sizes -->
<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">Добавить иcключение</h3>
    </div>

    <form role="form" method="post" action="<?= base_url('/agency/reg_reward') ?>">
        <div class="card-body">

            <!-- Действие -->
            <div class="form-group">
                <label>Действие</label>
                <select name="action" class="form-control">
                    <option value="create">Создать</option>
                    <option value="edit">Изменить</option>
                    <option value="delete">Удалить</option>
                </select>
            </div>

            <!-- Метод -->
            <div class="form-group">
                <label>Метод</label>
                <select name="method" class="form-control">
                    <option value="reward">Вознаграждения</option>
                    <option value="penalty">Штраф</option>
                </select>
            </div>

            <!-- Тип -->
            <div class="form-group">
                <label>Тип</label>
                <select name="type" class="form-control">
                    <option value="citycodes">Маршрут</option>
                    <option value="carrier">Перевозчик</option>
                </select>
            </div>
            <!-- Список -->
            <div class="form-group" id="list_element" style="display: none;">
                <label>Список</label>
                <select name="list" class="form-control" >
                    
                </select>
            </div>

            <div class="form-group" id="code_element" >
                <label for="code">Название</label>
                <input type="text" class="form-control" id="code_value" name="code" placeholder="Введите название" value="">
            </div>
            <div class="form-group" id="procent_element" >
                <label for="procent">Процент</label>
                <input type="number" class="form-control" id="procent" name="procent" placeholder="Введите процент" value="">
            </div>
            

            <input type="hidden" id="id" name="id" value="<?= $data['id'] ?>">
            <input type="hidden" id="name" name="name" value="<?= $name ?>">
        
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
            <button type="submit" class="btn btn-success">Выполнить</button>
        </div>
    </form> 
</div>
<?php endif; ?>


<script>
    

    

document.addEventListener('DOMContentLoaded', function () {

        const actionSelect = document.querySelector('select[name="action"]');
        const tableNameSelect = document.querySelector('select[name="type"]');
        const valueTableSelect = document.querySelector('select[name="list"]');
        const methodSelect = document.querySelector('select[name="method"]');
        

        const name = "<?= $name ?>";
        const value = "<?= $data['id'] ?>";
        
        // Функция для обновления списка значений list
        function update_value_table() {
            const method = methodSelect.value;
            const type = tableNameSelect.value;
            
            
            valueTableSelect.disabled = true; // сделать неактивным
            
            // Проверяем, выбрано ли допустимое значение
            if (type !== "all") {
                const url = `/agency/get_reward?method=${encodeURIComponent(method)}&type=${encodeURIComponent(type)}&name=${encodeURIComponent(name)}&value=${encodeURIComponent(value)}`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        valueTableSelect.innerHTML = '';                        

                        // Добавление новых опций
                        data.forEach(item => {
                            
                            var option;                        

                            option = new Option(item['code'], item['id']);
                            option.setAttribute('data-code', item['code']);
                            option.setAttribute('data-procent', item['procent']);

                            valueTableSelect.add(option);

                            
                        });

                        valueTableSelect.disabled = false; // Сделать активным
                        paste_values();
                        

                    })
                    .catch(error => {
                        console.error('Ошибка:', error);
                        // valueTableSelect.disabled = true; // Деактивируем в случае ошибки
                    });
            } else {
                // Сделать неактивным и очистить, если выбрано неопределённое значение
                valueTableSelect.disabled = true;
                valueTableSelect.innerHTML = '';
                
            }
        }

        // устанить значения в поля
        function paste_values() {
            var action_value = actionSelect.value;
            // console.log(action_value);
            
            if (action_value !== "create") {
                var selectedValue1 = valueTableSelect.options[valueTableSelect.selectedIndex].getAttribute('data-code');
                var selectedValue2 = valueTableSelect.options[valueTableSelect.selectedIndex].getAttribute('data-procent');

                document.getElementById('code_value').value = selectedValue1 || '';
                document.getElementById('procent').value = selectedValue2 || '';
            }
            
        }

        
        function change_action() {
            var action_value = actionSelect.value;
            var list_element = document.getElementById('list_element');
            var code_element = document.getElementById('code_element');
            var procent_element = document.getElementById('procent_element');

            if (action_value === "create") {
                list_element.style.display = 'none';
                document.getElementById('code_value').value = '';
                document.getElementById('procent').value = '';
            }else if(action_value === "edit"){
                list_element.style.display = '';
                code_element.style.display = '';
                procent_element.style.display = '';
                update_value_table();
            }else if(action_value === "delete"){
                list_element.style.display = '';
                code_element.style.display = 'none';
                procent_element.style.display = 'none';
            }
        }



        // Вызов функции при изменении значения
        methodSelect.addEventListener('input', update_value_table);
        tableNameSelect.addEventListener('input', update_value_table);
        valueTableSelect.addEventListener('input', paste_values);
        actionSelect.addEventListener('input', change_action);

        
        


});


</script>