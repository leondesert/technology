<?php
// Операции
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
  
<style>
  .table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch; /* Для плавной прокрутки на устройствах iOS */
  }
</style>

<style>
    .btn-fa-file-export::before {
        content: "\f56e"; /* Unicode для fa-file-export */
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        margin-right: 8px;
        color: #6c757d;
    }
    .btn-fa-file-excel::before {
        content: "\f1c3"; /* Unicode для fa-file-excel */
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        margin-right: 8px;
        color: #6c757d;
    }

</style>


    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Операции</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Операции</li>
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
            <?= $this->include('blocks/form_filter') ?>



            <!-- Таблица -->
            <div class="col-md-12">

              	<div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="far fa-chart-bar"></i>
                            Операции
                        </h3>

        
                        <div class="card-tools">                            
                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
              			<div class="card-body">
                      <div class="table-container">
              				    <table id="operations" class="table table-bordered table-striped" style="width:100%">
                    					<thead>
                    						<tr id="dynamic-headers">
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
                                                <!-- // Таблица "share" -->
                                                <th>Код раздачи</th>
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
                                                <th>ВалютаТакс</th>
                                                <th>Процент вознаграждение</th>
                                                <th>Вознаграждение</th>
                                                <th>Курс валюты</th>
                                                <th>Сумма штрафа</th>
                                                <th>Штраф</th>
                                                

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
                          <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
                          </button>
                      </div>

                  </div>
                <div class="card-body">
                  <div class="downtable-content table-responsive">
                      
                  </div>
              
                </div>
                <div class="overlay" id="overlay">
                    <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                </div>



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
        <button type="button" class="close closex" aria-label="Close">
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
          <div id="otchet"></div>
          <div id="report_type" style="display: none;"></div>
          
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" style="display: none;" id="submitReport">Отправить отчет</button>
        <button type="button" class="btn btn-secondary closez">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<!-- Модальное окно Экспорт Excel -->
<div class="modal fade" id="exportModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Экспорт данных</h4>
                <button type="button" class="close closex" aria-label="Close">
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
                <button id="downloadButton" class="btn btn-success mt-2">Скачать файл</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary closez">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно Управления фильтрами и Управления колонками-->
<div class="modal fade" id="saveFilterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="filterModalLabel">Управление</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <!-- <label for="filter-name">Имя фильтра:</label> -->
          <h6>Имя:</h6>
          <input type="text" class="form-control" id="filter-name">
        </div>
        <div class="form-group">
          <button type="button" class="btn btn-primary" id="save-filter">Сохранить</button>
        </div>
        <hr>
        <h6>Сохраненные:</h6>
        <div class="form-group">
          <select id="savedFiltersSelect" class="form-control">
            <!-- Сюда будут добавлены опции при загрузке -->
          </select>
        </div>
        <button type="button" class="btn btn-success" id="apply-filter">Применить</button>
        <button type="button" class="btn btn-danger" id="delete-filter">Удалить</button>
        <button type="button" class="btn btn-primary" id="sbros-filter">Сбросить</button>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>


<!-- Модальное окно Настройка -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="filterModalLabel">Настройка</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Форма -->
        <form id="filterForm">
          <!-- Выпадающий список -->
          <div class="form-group">
            <label for="filterSelect">Фильтр</label>
            <select class="form-control" id="filterSelect">
              <!-- <php foreach ($table_names as $table_name): ?>
                  <php
                      if ($table_name['value'] == $user['filter']) {
                        $isSelected = 'selected';
                      }else{
                        $isSelected = '';
                      }
                  ?>
                  <option value="<=$table_name['value'];?>" <=$isSelected;?>><=$table_name['name'];?></option>
              <php endforeach; ?> -->
            </select>
          </div>
          <!-- Поля выбора дат -->
          <div class="form-group">
            <label for="startDate">Начальная дата:</label>
            <!-- <input type="date" class="form-control" id="startDate" value="<=$user['start_date']?>"> -->
          </div>
          <div class="form-group">
            <label for="endDate">Конечная дата:</label>
            <!-- <input type="date" class="form-control" id="endDate" value="<=$user['end_date']?>"> -->
          </div>
          <!-- Кнопка сохранить -->
          <button type="submit" class="btn btn-success">Сохранить</button>
          <button type="button" class="btn btn-primary" id="sbros-filter">Сбросить</button>
        </form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

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



<script>
  
  var today = "<?php echo $dates['today']; ?>";
  var yesterday = "<?php echo $dates['yesterday']; ?>";
  var thisMonthFirst = "<?php echo $dates['thisMonthFirst']; ?>";
  var thisMonthLast = "<?php echo $dates['thisMonthLast']; ?>";
  var thisDecadeFirst = "<?php echo $dates['thisDecadeFirst']; ?>";
  var thisDecadeLast = "<?php echo $dates['thisDecadeLast']; ?>";
  
  
</script>



<?= $this->endSection() ?>