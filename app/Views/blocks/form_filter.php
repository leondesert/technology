<?php



?>


<!-- Фильтр -->
<div class="col-md-12">
  <div class="card card-warning">

      <!-- header -->
      <div class="card-header">
          <h3 class="card-title">Фильтр</h3>
      </div>

      <!-- Форма -->
      <form id="myForm" method="post" onsubmit="return false">
          <div class="card-body">
            <!-- Левая колонка -->
            <div class="row">
              <div class="col-md-4">

                <!-- Начальная дата -->
                <div class="form-group">
                    <label>Начало:</label>
                    <div class="input-group date" id="startDatePicker" data-target-input="nearest">
                        <input type="text" class="form-control datetimepicker-input" data-target="#startDatePicker" name="startDate" id="startDate" value="<?=$filter_values['start_date'];?>" autocomplete="off"/>
                        <div class="input-group-append" data-target="#startDatePicker" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>
                <!-- Конечная дата -->
                <div class="form-group">
                    <label>Конец:</label>
                    <div class="input-group date" id="endDatePicker" data-target-input="nearest">
                        <input type="text" class="form-control datetimepicker-input" data-target="#endDatePicker" name="endDate" id="endDate" value="<?=$filter_values['end_date'];?>" autocomplete="off"/>
                        <div class="input-group-append" data-target="#endDatePicker" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>

              </div>

              <!-- Средняя колонка -->
              <div class="col-md-4">
                <!-- Пользователь -->
                <div class="form-group">
                  <label for="user_login">Пользователь</label>
                    <select class="form-control" id="user_login" name="user_login" >

                        <?php if($role !== "superadmin"): ?>
                            <option value="<?= $user_id ?>" <?= $filter_values['user_login'] == $user_id ? 'selected' : '' ?>>
                                <?= $username ?>
                            </option>
                        <?php endif; ?>

                        <?php foreach($users as $item): ?>
                            
                            <option value="<?= $item['user_id'] ?>" <?= $filter_values['user_login'] == $item['user_id'] ? 'selected' : '' ?>>
                                <?= $item['user_login'] ?>
                            </option>
                            
                        <?php endforeach; ?>

                    </select>
                </div>
                <!-- Валюта -->
                <div class="form-group">
                  <label for="currency">Валюта</label>
                    <select class="form-control" id="currency" name="currency">
                        <?php foreach ($currencies as $item): ?>
                            <option value="<?= $item ?>" <?= $filter_values['currency'] == $item ? 'selected' : '' ?>>
                                <?= $item ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>



              </div>

              <!-- Правая колонка -->
              <div class="col-md-4">
                <!-- Организация -->
                <div class="form-group">
                    <label>Организация</label>
                    <select name="name_table" id="name_table" class="form-control">
                        <option value="all">- Все -</option>
                    </select>
                </div>
                <!-- Название -->
                <div class="form-group">
                    <label>Название</label>
                    <select name="value_table" id="value_table" class="form-control">
                      <option value="all">- Все -</option>
                    </select>
                </div>
              </div>
              <!-- обновить при обновлении стр -->
              <input type="hidden" id="is_refresh" name="is_refresh" value="yes">
              <input type="hidden" id="is_update" value="no">
              <!-- значения по умолчанию -->
              <input type="hidden" id="selected_name_table" value="<?=$filter_values['name_table']?>">
              <input type="hidden" id="selected_value_table" value="<?=$filter_values['value_table']?>">

            </div>
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
            
            <button type="button" id="submitBtn" class="btn bg-gradient-success button_spacing">Применить</button>
            <button type="button" id="submitBtnToday" class="btn bg-gradient-primary button_spacing">Сегодня</button>
            <button type="button" id="submitBtnYesterday" class="btn bg-gradient-primary button_spacing">Вчера</button>
            <button type="button" id="submitBtnOneDecade" class="btn bg-gradient-primary button_spacing">Первая декада</button>
            <button type="button" id="submitBtnTwoDecade" class="btn bg-gradient-primary button_spacing">Вторая декада</button>
            <button type="button" id="submitBtnThreeDecade" class="btn bg-gradient-primary button_spacing">Третья декада</button>
            <button type="button" id="submitBtnThisMonth" class="btn bg-gradient-primary button_spacing">Текущий месяц</button>
            <button type="button" id="submitBtnLastMonth" class="btn bg-gradient-primary button_spacing">Прошлый месяц</button>
          </div>
      </form>
      
  </div>
</div>


<script>

  var today = "<?php echo $dates['today']; ?>";
  var yesterday = "<?php echo $dates['yesterday']; ?>";
  var thisMonthFirst = "<?php echo $dates['thisMonthFirst']; ?>";
  var thisMonthLast = "<?php echo $dates['thisMonthLast']; ?>";
  var thisDecadeFirst = "<?php echo $dates['thisDecadeFirst']; ?>";
  var thisDecadeLast = "<?php echo $dates['thisDecadeLast']; ?>";


</script>


<script type="text/javascript" language="javascript" src="<?= base_url(); ?><?= latest('js/custom/filter_form.js'); ?>"></script>



