<?php

// Отчеты

?>

<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>

<!-- Мета-тег с ID текущего пользователя для использования в JavaScript -->
<meta name="current-user-id" content="<?= $user_id ?>">


<!-- Content Header (Page header) -->
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Отчеты</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Отчеты</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->

</section>

<style>
    /*div.dataTables_processing > div:last-child {
      display: none;
    }*/
</style>



<!-- Контейнер таблицы -->
<section class="content">
    <div class="container-fluid">
    
        <div class="row">

            <!-- Фильтр -->
            <!-- <= $this->include('blocks/form_filter') ?> -->



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
                                    <option value="all">- Все -</option>
                                    <?php if($role !== "superadmin"): ?>
                                        <option value="<?= $user_id ?>">
                                            <?= $username ?>
                                        </option>
                                    <?php endif; ?>

                                    <?php foreach($users as $item): ?>
                                        
                                        <option value="<?= $item['user_id'] ?>">
                                            <?= $item['user_login'] ?>
                                        </option>
                                        
                                    <?php endforeach; ?>

                                </select>
                            </div>
                            <!-- Валюта -->
                            <div class="form-group">
                              <label for="currency">Валюта</label>
                                <select class="form-control" id="currency" name="currency">
                                    <option value="all">- Все -</option>
                                    <?php foreach ($currencies as $item): ?>
                                        <option value="<?= $item ?>">
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
                                    <option value="agency">Агенство</option>
                                    <option value="stamp">ППР</option>
                                    <option value="tap">Пульт</option>
                                    <option value="opr">Оператор</option>
                                    <option value="share">Раздача</option>
                                    <option value="pre_share">Предварительная раздача</option>
                                </select>
                            </div>

                            <!-- Статус -->
                            <div class="form-group">
                                <label>Статус</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="all">- Все -</option>
                                    <option value="0">В обработке</option>
                                    <option value="1">Подтвержден</option>
                                    <option value="2">Отклонен</option>
                                </select>
                            </div>
                            
                          </div>

                          <!-- Правая колонка -->
                          <!-- <div class="col-md-4"> -->
                            <!-- Организация -->
                            <!-- <div class="form-group">
                                <label>Организация</label>
                                <select name="name_table" id="name_table" class="form-control">
                                    <option value="all">- Все -</option>
                                </select>
                            </div> -->
                            <!-- Название -->
                            <!-- <div class="form-group">
                                <label>Название</label>
                                <select name="value_table" id="value_table" class="form-control">
                                  <option value="all">- Все -</option>
                                </select>
                            </div>
                          </div> -->




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
            
            <!-- Таблица -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">

                            <!-- reports -->
                            <table id="reports" class="display" style="width:100%">
                                <thead>
                                    <tr>

                                        <th>ID</th>
                                        <th>Пользователь</th>
                                        <th>Начало</th>
                                        <th>Конец</th> 
                                        <!--<th>Организация</th>-->
                                        <!--<th>Название</th>-->
                                        <th>Валюта</th>
                                        <!-- <th>Баланс</th> -->
                                        <th>Дата отправки</th>
                                        <th>Дата проверки</th>
                                        <th>Статус</th>
                                        <th>Действия</th>
                                        
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>

                            
                    </div>

                    <!-- <div class="overlay">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div> -->



                </div>
            </div>




        </div>
    </div>
</section>




<!-- Модальное окно Отчета -->
<div class="modal fade" id="summaryModal" tabindex="-1" role="dialog" aria-labelledby="summaryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
            <h5 class="modal-title" id="summaryModalLabel">Отчет</h5>
      </div>


      <div class="modal-body" id="reportContent">
          <!-- Анимация загрузки от AdminLTE -->

          <div class="text-center">
            <div id="loadingAnimation" class="spinner-border" role="status">
              <span class="sr-only">Загрузка...</span>
            </div>
          </div>
          
          <!-- Текстовое сообщение -->
          <p id="modalMessage" class="mt-2 text-center">Пожалуйста, подождите...</p>

        <!-- Верхняя часть -->
        <div id="up_part" class="report-info text-end mb-4" style="display: none; line-height: 0.5;">
          <p><strong>Отчет №:</strong> <span id="id_report">12345</span></p>
          <p><strong id="iname_table">Таблица:</strong> <span id="ivalue_table">Example Table</span></p>
          <p><strong>Дата:</strong> <span id="start_date">2024-01-01</span> - <span id="end_date">2024-12-31</span></p>
          <p><strong>Валюта:</strong> <span id="icurrency">USD</span></p>
        </div>


          <!-- Отчет -->
          <div id="otchet"></div>
          
          

       
        
         <!-- Нижняя часть -->
        <div id="down_part" style="display: none; padding-top: 15px; line-height: 1.2;">
            <div class="container">
                <div class="column">
                    <p><strong>Дата формирования:</strong> <span id="send_date">timestamp</span></p>
                    <p><strong>Отправитель:</strong> <span id="fio_user">ФИО user</span></p>
                    <p><strong>Подпись:</strong> ____________________</p>
                    <div id="qrcode_user"></div>
                </div>
                <div class="column">
                    <p><strong>Дата принятия:</strong> <span id="check_date">timestamp</span></p>
                    <p><strong>Проверяющий:</strong> <span id="fio_admin">ФИО admin</span></p>
                    <p><strong>Подпись:</strong> ____________________</p>
                    <div id="qrcode_admin"></div>
                </div>
            </div>
        </div>




          <div id="report_id"></div>
      </div>


        <div class="modal-footer d-flex justify-content-between">
            <!-- Кнопка печати слева -->
            <div class="me-auto">
                <button type="button" class="btn btn-primary" style="display: none;" id="printReport">
                    <i class="fas fa-print"></i>
                    Распечатать
                </button>
            </div>

            <!-- Кнопки справа -->
            <div>
                <?php if ($role == "admin" || $role == "superadmin"): ?>
                    <button type="button" class="btn btn-success" id="acceptReport">Принять</button>
                    <button type="button" class="btn btn-warning" id="rejectReport">Отклонить</button>
                <?php endif; ?>
                <?php if ($role == "superadmin"): ?>
                    <button type="button" class="btn btn-danger" id="deleteReport">Удалить</button>
                <?php endif; ?>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>


    </div>
  </div>
</div>

<style>
        .modal-header {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        .modal-title {
            text-align: center;
            width: 100%;
        }


        .container {
            display: flex; 
            flex-wrap: wrap; 
            margin: 15px 0;
        }
        .column {
            flex: 1; 
            padding: 10px; 
            min-width: 300px; 
        }
        .column p {
            margin: 5px 0; 
        }
        
        #qrcode_user, #qrcode_admin {
            margin-top: 10px; 
            width: 100px; 
            height: 100px; 
            background-color: #eee;
        }
</style>


<script type="text/javascript">
    function addDeleteClass() {
  $('#submitBtnToday, #submitBtnYesterday, #submitBtnOneDecade, #submitBtnTwoDecade, #submitBtnThreeDecade, #submitBtnThisMonth, #submitBtnLastMonth').removeClass('bg-gradient-warning').addClass('bg-gradient-primary');
}


</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>



<!-- Модальное окно Экспорт Excel -->
<div class="modal fade" id="exportModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Экспорт данных</h4>
                <button type="button" class="close closex" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <!-- Анимация загрузки от AdminLTE -->
                <div id="loadingAnimation" class="spinner-border" role="status">
                    <span class="sr-only">Загрузка...</span>
                </div>
                <!-- Текстовое сообщение -->
                <p id="modalMessage" class="mt-2">Пожалуйста, подождите... Идет экспорт данных.</p>
                <!-- Кнопка скачивания от AdminLTE -->
                <button id="downloadButton" class="btn btn-success mt-2" style="display: none;">Скачать файл</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary closez" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>