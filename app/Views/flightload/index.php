<?php

// Загрузка рейса


?>


<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>


<style>

  .down_cell:hover {
      background-color: #f0f0f0;
      cursor: pointer; 
  }

  .custom-loader {
      width: 100%;
      position: fixed;
      bottom: 0;
      left: 0;
      z-index: 9999;
  }

  .table-container {
      position: relative;
      width: 100%;

  } 

  .custom-loader .progress {
      height: 20px; /* Увеличиваем высоту контейнера прогресс-бара */
  }

  .custom-loader .progress-bar {
      height: 100%; /* Убеждаемся, что прогресс-бар занимает всю высоту контейнера */
  }



  div.dataTables_processing > div:last-child {
      display: none;
  }


</style>



    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Загрузка рейса</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Загрузка рейса</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    

<!-- Контейнер -->
<section class="content">
    <div class="container-fluid">
    
        <div class="row">

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
                        <div class="row">

                          <!-- первая колонка -->
                          <div class="col-md-3">

                            <!-- Начальная дата -->
                            <div class="form-group">
                                <label>Начало:</label>
                                <div id="startDatePicker" class="input-group date" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" data-target="#startDatePicker" name="startDate" id="startDate" value="<?=$dates['today']?>" autocomplete="off"/>
                                    <div class="input-group-append" data-target="#startDatePicker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                            <!-- Конечная дата -->
                            <div class="form-group">
                                <label>Конец:</label>
                                <div id="endDatePicker" class="input-group date" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" data-target="#endDatePicker" name="endDate" id="endDate" value="<?=$dates['today']?>" autocomplete="off"/>
                                    <div class="input-group-append" data-target="#endDatePicker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>

                          </div>

                          <!-- вторая колонка -->
                          <div class="col-md-3">

                            <!-- Дата полёта -->
                            <div class="form-group">
                                <label>Дата полёта:</label>
                                <div id="flydatePicker" class="input-group date" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" data-target="#flydatePicker" name="flydate" id="flydate" value="" autocomplete="off" placeholder="Введите дату полёта"/>
                                    <div class="input-group-append" data-target="#flydatePicker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                            <!-- Маршрут -->
                            <div class="form-group">
                              <label for="citycodes">Маршрут</label>
                              <input type="text" class="form-control" id="citycodes" name="citycodes" placeholder="Введите маршрут" autocomplete="off">
                            </div>
                            
                          </div>

                          <!--  третья колонка -->
                          <div class="col-md-3">

                            <!-- Время полёта -->
                            <div class="form-group">
                                <label>Время полёта:</label>
                                  <div id="timepicker" class="input-group date" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" data-target="#timepicker" name="flytime" id="flytime" value="" autocomplete="off" placeholder="Введите время полёта">
                                    <div class="input-group-append" data-target="#timepicker" data-toggle="datetimepicker">
                                      <div class="input-group-text"><i class="far fa-clock"></i></div>
                                    </div>
                                  </div>
                            </div>
                            <!-- Пользователь -->
                            <div class="form-group">
                              <label for="user_login">Пользователь</label>
                                <select class="form-control" id="user_login" name="user_login" >

                                    <?php if($role !== "superadmin"): ?>
                                        <option value="<?= $user_id ?>">
                                            <?= $username ?>
                                        </option>
                                    <?php endif; ?>

                                    <?php foreach($users as $user): ?>
                                        
                                        <option value="<?= $user['user_id'] ?>">
                                            <?= $user['user_login'] ?>
                                        </option>
                                        
                                    <?php endforeach; ?>

                                </select>
                            </div>

                          </div>



                          <!-- четвертая колонка -->
                          <div class="col-md-3">
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
                          <input type="hidden" id="is_refresh" value="yes">
                          <input type="hidden" id="is_update" value="no">

                          <!-- значения по умолчанию -->
                          <input type="hidden" id="selected_name_table" value="<?=$filter_values['name_table']?>">
                          <input type="hidden" id="selected_value_table" value="<?=$filter_values['value_table']?>">
                          
                        </div>
                      </div>
                      <!-- /.card-body -->
                      <div class="card-footer">
                      
                        <button type="submit" id="submitBtn" class="btn bg-gradient-success button_spacing">Применить</button>
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

            <!-- Топ 10 популярных рейсов -->
            <div class="col-md-12">
              <div class="card card-success collapsed-card">
                  <div class="card-header">    
                      <h3 class="card-title" id="titletable">Популярные рейсы</h3>
                      <div class="card-tools">
                          <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                          <button type="button" class="btn btn-tool" data-card-widget="maximize">
                            <i class="fas fa-expand"></i>
                          </button>
                      </div>

                  </div>
                <div class="card-body">
                    <div class="row">

                    
                      <div class="col-md-3">
                        <!-- Метод -->
                        <div class="form-group">
                          <label for="filterby">Метод</label>
                            <select class="form-control" id="filterby" name="filterby" >

                                <option value="citycodes">Маршрут</option>
                                <option value="tickets_dealdate">Дата</option>

                            </select>
                        </div>
                      </div>

                      <div class="col-md-3">
                          <!-- Показать -->
                          <div class="form-group">
                            <label for="show">Показать</label>
                              <select class="form-control" id="show" name="show" >

                                  <option value="5">5</option>
                                  <option value="10">10</option>
                                  <option value="25">25</option>
                                  <option value="50">50</option>
                                  <option value="100">100</option>
                                  <option value="all">Все</option>

                              </select>
                          </div>
                      </div>


                  </div>

                  

                  
              
                </div>
                <div class="overlay d-none" id="overlay2">
                    <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                </div>

                <div class="card-footer">
                    <button type="submit" id="submitBtnPopular" class="btn bg-gradient-success">Показать</button>
                </div>

                <div class="card-body">
                  <!-- Таблица -->
                  <div class="top-ten-popular table-responsive">
                      <!-- <p>Нажми на поиск</p> -->
                  </div>
                </div>


              </div>
            </div>

            <!-- Таблица -->
            <div class="col-md-12">

              	<div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="far fa-chart-bar"></i>
                            Операции
                        </h3>

        
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>                         
                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                <i class="fas fa-expand"></i>
                            </button>

                            
                        </div>
                    </div>
              			<div class="card-body">
                      <div class="table-container">
                          <table id="flightload" class="table table-bordered table-striped" style="width:100%">
                              <thead>
                                <tr>
                                                <!-- // Таблица "tickets" -->

                                                <th>Тип билета</th>
                                                <th>Валюта билета</th>
                                                <th>Дата формирования</th>
                                                <th>Время формирования</th>
                                                <th>Тип операции</th>
                                                <th>Тип транзакции</th>
                                                <th>Номер билета</th>
                                                <th>Номер старшего билета</th>
                                                <th>Номер основного билета</th>
                                                <th>Тариф цена</th>
                                                <th>PNR</th>
                                                <th>Дата оформления</th>
                                                <th>Индентификатор продавца</th>
                                                <th>Время оформления</th>
                                                <th>Время оформления UTC</th>
                                                <th>Сумма обмена без EMD</th>
                                                <!-- // Таблица "opr" -->
                                                <th>Код оператора</th>
                                                <!-- // Таблица "agency" -->
                                                <th>Код агентства</th>
                                                <!-- // Таблица "emd" -->
                                                <th>Сумма EMD</th>    
                                                <!-- // Таблица "fops" -->
                                                <th>Вид оплаты</th>
                                                <th>Сумма оплаты</th>
                                                <!-- // Таблица "passengers" -->
                                                <th>ФИО</th>
                                                <th>Паспорт</th>
                                                <th>Тип</th>
                                                <th>Гражданство</th>
                                                <!-- // Таблица "segments" -->
                                                <th>Маршрут</th>
                                                <th>Перевозчик</th>
                                                <th>Класс</th>
                                                <th>Рейс</th>
                                                <th>Дата полёта</th>
                                                <th>Время полёта</th>
                                                <th>Тариф </th>
                                                <!-- // Таблица "stamp" -->
                                                <th>Код ППР</th>
                                                <!-- // Таблица "tap" -->
                                                <th>Код пульта</th>
                                                <!-- // Таблица "taxes" -->
                                                <th>Код сбора</th>
                                                <th>Сумма сбора</th>
                                                
                                </tr>
                              </thead>
                          </table>
                      </div>
              			</div>
              	</div>
            </div>



            <!-- Отчет внизу таблицы -->
            <div class="col-md-12">
              <div class="card card-primary card-outline">
                  <div class="card-header">    
                      <h3 class="card-title" id="titletable">Отчет</h3>
                      <div class="card-tools">
                          <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                          </button>
                          <button type="button" class="btn btn-tool" data-card-widget="maximize">
                            <i class="fas fa-expand"></i>
                          </button>
                      </div>

                  </div>
                <div class="card-body">
                  <div class="downtable-content table-responsive">
                      
                  </div>
              
                </div>
                <div class="overlay" id="overlay3">
                    <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                </div>



              </div>
            </div>

            
            


        </div>
    </div>
</section>




<!-- Модальное окно Подробнее -->
<div class="modal fade" id="summaryTextModal" tabindex="-1" role="dialog" aria-labelledby="summaryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="summaryModalLabel">Подробнее</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
          <!-- Анимация загрузки от AdminLTE -->
          <div id="loadingAnimation" class="spinner-border" role="status">
              <span class="sr-only">Загрузка...</span>
          </div>
          <!-- Текстовое сообщение -->
          <p id="modalMessage" class="mt-2">Пожалуйста, подождите...</p>
          <!-- Отчет -->
          <div id="podrobnee" class="table-responsive"></div>


          
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>




<script type="text/javascript" language="javascript" src="<?= base_url(); ?><?= latest('js/custom/filter_form.js'); ?>"></script>

<script>
  
  var today = "<?php echo $dates['today']; ?>";
  var yesterday = "<?php echo $dates['yesterday']; ?>";
  var thisMonthFirst = "<?php echo $dates['thisMonthFirst']; ?>";
  var thisMonthLast = "<?php echo $dates['thisMonthLast']; ?>";
  var thisDecadeFirst = "<?php echo $dates['thisDecadeFirst']; ?>";
  var thisDecadeLast = "<?php echo $dates['thisDecadeLast']; ?>";

  
</script>



<?= $this->endSection() ?>