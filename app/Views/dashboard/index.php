<?php

    $styles = ['info', 'success', 'warning', 'danger'];
?>

<?= $this->extend('templates/admin_template') ?>


<?= $this->section('content') ?>


<style>
  .table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch; /* Для плавной прокрутки на устройствах iOS */
}


</style>

    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Общий отчет</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Общий отчет</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

<!-- Content -->
<section class="content">
      <div class="container-fluid">        
              <div class="row">

                  <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                      
                      <div class="inner" >
                        <h5>0</h5>
                        
                        <p>0 шт.</p>
                      </div>
                      <div class="icon">
                        <i class="ion ion-bag"></i>
                      </div>
                      <a href="#" class="small-box-footer">Продажа</a>

                      <div class="overlay upTable">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                      </div>

                    </div>
                  </div>
                  <!-- ./col -->
                  <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                      <div class="inner">
                        <h5>0</h5>

                        <p>0 шт.</p>
                      </div>
                      <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                      </div>
                      <a href="#" class="small-box-footer">Возврат</a>

                      <div class="overlay upTable">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                      </div>


                    </div>
                  </div>
                  <!-- ./col -->
                  <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                      <div class="inner">
                        <h5>0</h5>

                        <p>0 шт.</p>
                      </div>
                      <div class="icon">
                        <i class="ion ion-person-add"></i>
                      </div>
                      <a href="#" class="small-box-footer">Обмен</a>


                      <div class="overlay upTable">
                          <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                        </div>


                    </div>
                  </div>
                  <!-- ./col -->
                  <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                      <div class="inner">
                        <h5>0</h5>

                        <p>0 шт.</p>
                      </div>
                      <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                      </div>
                      <a href="#" class="small-box-footer">Выручка</a>

                        <div class="overlay upTable">
                          <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                        </div>


                    </div>
                  </div>
                  <!-- ./col -->
                  

                  <!-- Эквайринг -->



                  <!-- Фильтр -->
                  <?= $this->include('blocks/form_filter') ?>

                  <!-- Таблица -->
                  <div class="col-md-12">
                    <div class="card card-success">
                        <div class="card-header">    
                            <h3 class="card-title" id="titletable">Декада</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i>
                                </button>
                            </div>

                        </div>
                      <div class="card-body">
                        <div class="downtable-content table-responsive">
                            
                        </div>
                    
                      </div>
                      <div class="overlay" id="overlay2">
                          <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                      </div>



                    </div>  
                  </div> 


              </div>
              <!-- /.row -->
      </div>  
</section>        







<script>
  document.addEventListener('DOMContentLoaded', function () {


      function acq(){

          // Получаем данные формы
          var formData = $("#myForm").serialize();
          
          // console.log(formData);

          $.ajax({
              type: 'POST',
              url: '/pays/downtable',
              data: formData, 
              success: function(response) {
                  
                  console.log(response);
                
              },
              error: function(xhr, status, error) {
                  console.error('AJAX Error: ' + status + error);
              }
          });


      }


      function sendDataToday() {

          // показываем слой загрузки
          showLoading_c('upTable');

          // Получаем данные формы
          var startDate = today;
          var endDate = today;
          var user_login = document.getElementById('user_login').value;
          var name_table = document.getElementById('name_table').value;
          var value_table = document.getElementById('value_table').value;
          var currency = document.getElementById('currency').value;
          var DefaultFilter = {
              "criteria": [
                  {
                      "condition": "between",
                      "data": "Дата формирования",
                      "origData": "tickets.tickets_dealdate",
                      "type": "date",
                      "value": [
                          startDate,
                          endDate
                      ]
                  },
                  {
                      "condition": "=",
                      "data": "Валюта билета",
                      "origData": "tickets.tickets_currency",
                      "type": "string",
                      "value": [
                          currency
                      ]
                  }
              ],
              "logic": "AND"
          };

          var params = {
              "page": "dashboard",
              "searchBuilder": DefaultFilter,
              "user_login": user_login,
              "name_table": name_table,
              "value_table": value_table,
              "colum_name": name_table + '_id',
          };

          // console.log(params);


          // Отправляем данные на сервер с помощью AJAX запроса
          $.ajax({
              type: 'POST',
              url: '/calculateSummary', 
              data: params, 
              dataType: 'json',
              success: function(response) {
                
                  let data = response.data;

                  // console.log('response', response);

                  var headings = document.querySelectorAll('.inner h5');
                  var paragraphs = document.querySelectorAll('.inner p');
                  
                  function split(str) {
                    var elements = str.split(' / ');
                    var leftElement = elements[0].trim();
                    var rightElement = elements[1].trim();

                    return [leftElement, rightElement];
                  }

                  
                  var resultSale = split(data.ETICKET.SALE);
                  var resultRefund = split(data.ETICKET.REFUND);
                  var resultExchangeETICKET = split(data.ETICKET.EXCHANGE);
                  var resultExchangeEMD = split(data.EMD.EXCHANGE);

                  var resultExchangeSum = parseFloat(resultExchangeETICKET[1]) + parseFloat(resultExchangeEMD[1]);
                  var resultExchangeCount = parseFloat(resultExchangeETICKET[0]) + parseFloat(resultExchangeEMD[0]);

                  // console.log(resultExchangeETICKET[1]);


                  headings[0].innerHTML = resultSale[1] + ' ' + currency; // Продажа SALE
                  headings[1].innerHTML = resultRefund[1] + ' ' + currency; // Возврат REFUND
                  headings[2].innerHTML = resultExchangeSum + ' ' + currency; // Обмен EXCHANGE
                  headings[3].innerHTML = data.totalAmount + ' ' + currency; // Выручка 

                  paragraphs[0].innerHTML = resultSale[0] + ' шт.'; // Продажа 
                  paragraphs[1].innerHTML = resultRefund[0] + ' шт.'; // Возврат
                  paragraphs[2].innerHTML = resultExchangeCount + ' шт.'; // Обмен
                  paragraphs[3].innerHTML = data.totalCount + ' шт.'; // Выручка

                  
                  
                  // Скрываем слой загрузки после получения данных
                  hideLoading_c('upTable');
              },
              error: function(xhr, status, error) {
                  // Обработка ошибок
                  console.error(xhr);
                  
              }

              
          });    
      }

      function sendData() {

          // показываем слой загрузки
          showLoading2('overlay2');

          // Получаем данные формы
          var startDate = document.getElementById('startDate').value;
          var endDate = document.getElementById('endDate').value;
          var user_login = document.getElementById('user_login').value;
          var name_table = document.getElementById('name_table').value;
          var value_table = document.getElementById('value_table').value;
          var currency = document.getElementById('currency').value;

          var DefaultFilter = {
              "criteria": [
                  {
                      "condition": "between",
                      "data": "Дата формирования",
                      "origData": "tickets.tickets_dealdate",
                      "type": "date",
                      "value": [
                          startDate,
                          endDate
                      ]
                  },
                  {
                      "condition": "=",
                      "data": "Валюта билета",
                      "origData": "tickets.tickets_currency",
                      "type": "string",
                      "value": [
                          currency
                      ]
                  }
              ],
              "logic": "AND"
          };

          var params = {
              "page": "dashboard",
              "searchBuilder": DefaultFilter,
              "user_login": user_login,
              "name_table": name_table,
              "value_table": value_table,
              "colum_name": name_table + '_id',
          };

          // console.log(params);

          // $('#summary_text').remove();

          // Отправляем данные на сервер с помощью AJAX запроса
          $.ajax({
              type: 'POST',
              url: '/calculateSummary', // Укажите URL, на который вы хотите отправить запрос
              data: params, // Передаем данные формы
              dataType: 'json', // Ожидаемый тип данных от сервера
              success: function(response) {
                  // Обработка успешного ответа от сервера
                  // console.log(response);

                  let tData = response.data;

                  // Формирование и вставка кода таблицы
                  let summaryText = `<div id="summary_text">

                      <table id="downtable" class="dataTable display" style="width:100%">
                          <tr><td></td><td><b>ETICKET</b></td><td><b>EMD</b></td><td><b>Сумма</b></td></tr>
                          <tr class="odd"><td><b>Продажа (sale)</b></td><td>${tData.ETICKET.SALE}</td><td>${tData.EMD.SALE}</td><td></td></tr>
                          <tr class="even"><td><b>Обмен (exchange)</b></td><td>${tData.ETICKET.EXCHANGE}</td><td>${tData.EMD.EXCHANGE}</td><td></td></tr>
                          <tr class="odd"><td><b>Возврат (refund)</b></td><td>${tData.ETICKET.REFUND}</td><td>${tData.EMD.REFUND}</td><td></td></tr>
                          <tr class="even"><td><b>Отмена (cancel)</b></td><td>${tData.ETICKET.CANCEL}</td><td>${tData.EMD.CANCEL}</td><td></td></tr>
                          <tr class="odd"><td><b>Всего транзакций</b></td><td>${tData.ETICKET.count}</td><td>${tData.EMD.count}</td><td>${tData.totalCount}</td></tr>
                          <tr class="even"><td><b>Выручка</b></td><td>${tData.ETICKET.amount}</td><td>${tData.EMD.amount}</td><td>${tData.totalAmount}</td></tr>
                      </table>
                      </div>`;
                  
                  // $('.downtable-content').append(summaryText);

                  document.querySelector('.downtable-content').innerHTML = summaryText;


                  // Скрываем слой загрузки
                  hideLoading2('overlay2');

              },
              error: function(xhr, status, error) {
                  // Обработка ошибок
                  console.error(xhr);
              }
          });
      }


      $('#submitBtn').click(function(){
          addDeleteClass();
          sendDataToday();
          sendData();

          var startDate = document.getElementById('startDate').value;
          var endDate = document.getElementById('endDate').value;
          document.getElementById('titletable').innerHTML = 'Диапазон: ' + startDate + ' / ' + endDate;   
      });

      $('#submitBtnToday').click(function(){

          addDeleteClass();

          document.getElementById('startDate').value = today;
          document.getElementById('endDate').value = today;
          document.getElementById('titletable').innerHTML = 'Сегодня';
          
          sendDataToday();
          sendData();

          // Изменяем классы кнопок
          $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
      });

      $('#submitBtnYesterday').click(function(){

          addDeleteClass();

          document.getElementById('startDate').value = yesterday;
          document.getElementById('endDate').value = yesterday;
          document.getElementById('titletable').innerHTML = 'Вчера';

          sendDataToday();
          sendData();

          // Изменяем классы кнопок
          $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
      });

      $('#submitBtnOneDecade').click(function(){

          addDeleteClass();

          document.getElementById('startDate').value = firstDecadeStart;
          document.getElementById('endDate').value = firstDecadeEnd;
          document.getElementById('titletable').innerHTML = 'Первая декада';

          sendDataToday();
          sendData();

          // Изменяем классы кнопок
          $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
      });

      $('#submitBtnTwoDecade').click(function(){

          addDeleteClass();

          document.getElementById('startDate').value = secondDecadeStart;
          document.getElementById('endDate').value = secondDecadeEnd;
          document.getElementById('titletable').innerHTML = 'Вторая декада';

          sendDataToday();
          sendData();

          // Изменяем классы кнопок
          $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
      });

      $('#submitBtnThreeDecade').click(function(){

          addDeleteClass();

          document.getElementById('startDate').value = thirdDecadeStart;
          document.getElementById('endDate').value = thirdDecadeEnd;
          document.getElementById('titletable').innerHTML = 'Третья декада';

          sendDataToday();
          sendData();

          // Изменяем классы кнопок
          $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
      });

      $('#submitBtnThisMonth').click(function(){

          addDeleteClass();

          document.getElementById('startDate').value = thisMonthFirst;
          document.getElementById('endDate').value = thisMonthLast;
          document.getElementById('titletable').innerHTML = 'Текущий месяц';

          sendDataToday();
          sendData();

          // Изменяем классы кнопок
          $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
      });

      $('#submitBtnLastMonth').click(function(){

          addDeleteClass();

          document.getElementById('startDate').value = lastMonthFirst;
          document.getElementById('endDate').value = lastMonthLast;
          document.getElementById('titletable').innerHTML = 'Прошлый месяц';

          sendDataToday();
          sendData();

          // Изменяем классы кнопок
          $(this).removeClass('bg-gradient-primary').addClass('bg-gradient-warning');
      });


      // Вызываем функцию каждые 10 секунд
      setInterval(function() {
          sendDataToday();
      }, 10000);

      let intervalId;
      intervalId = setInterval(() => {

            let is_update = document.getElementById('is_update').value;
            // console.log('5000', is_update);

            if (is_update === 'yes') {
                clearInterval(intervalId);
                sendDataToday();
                sendData();
            }
      }, 500);




  });
</script>






<?= $this->endSection() ?>