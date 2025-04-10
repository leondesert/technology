<?php

// Эквайринг

?>

<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>

<!-- Content Header (Page header) -->
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Эквайринг</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Эквайринг</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->

</section>


<style>
    .bold-row {
    font-weight: bold;
}

</style>


<!-- Контейнер таблицы -->
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

                          <!-- Левая колонка -->
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
                            <!-- Метод оплаты -->
                            <div class="form-group">
                              <label for="user_login">Метод оплаты</label>
                                <select class="form-control" id="name_payment" name="name_payment" >

                                    <?php foreach($name_payments as $name_payment): ?>
                                        
                                        <option value="<?= $name_payment['value'] ?>">
                                            <?= $name_payment['name'] ?>
                                        </option>
                                        
                                    <?php endforeach; ?>

                                </select>
                            </div>
                            <!-- Валюта -->
                            <div class="form-group">
                              <label for="currency">Валюта</label>
                              <select class="form-control" id="currency" name="currency" >

                                    <?php foreach($currencies as $currency): ?>
                                        
                                        <option value="<?= $currency['value'] ?>">
                                            <?= $currency['name'] ?>
                                        </option>
                                        
                                    <?php endforeach; ?>

                                </select>
                            </div>
                          </div>

                          <!-- Средняя колонка -->
                          <div class="col-md-4">
                            <!-- Валюта -->
                            <div class="form-group">
                              <label for="status">Статус</label>
                              <select class="form-control" id="status" name="status" >

                                    <option value="">Все</option>
                                    <option value="null">Пусто</option>
                                    <option value="paid">Оплачено</option>

                                </select>
                            </div>
                          </div>



                          <!-- обновить при обновлении стр -->
                          <input type="hidden" id="is_refresh" value="yes">
                          <input type="hidden" id="is_update" value="no">
                          
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

            <!-- Таблица -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">

                            <!-- pays -->
                            <table id="pays" class="display" style="width:100%">
                                <thead>
                                    <tr>

                                        <th>Номер заказа в Немо</th>
                                        <th>Номер платежа в Немо</th>
                                        <th>Сумма</th>
                                        <th>Сумма оплаты</th>
                                        <th>Сумма без комиссии</th>
                                        <th>Комиссия</th>
                                        <th>Комиссия банка (клиент)</th>
                                        <th>Комиссия банка (АВС)</th>
                                        <th>Валюта</th>
                                        <th>Номер заказа на сайте</th>
                                        <th>Дата создания</th>
                                        <th>Статус транзакции</th>
                                        <th>Телефон</th>
                                        <th>Почта</th>
                                        <th>Метод оплаты</th>
                                        <th>Номер заказа в эквайринге</th>
                                        <th>Статус в банке</th>
                                        <th>Дата платежа</th>
                                        
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>


                    </div>

                    <div class="overlay">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>



                </div>
            </div>


            <!-- Нижняя таблица -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                            <!-- downtable -->
                            <table id="pays_downtable" class="display" style="width:100%">
                                <thead>
                                    <tr>

                                        <th>Метод оплаты</th>
                                        <th>Процент</th>
                                        <th>Количество</th>
                                        <th>Сумма оплаты</th>
                                        <th>Сумма без комиссии</th>
                                        <th>Комиссия</th>
                                        
                                        
                                        <th>Комиссия (клиент)</th>
                                        <th>Комиссия банка (АВС)</th>
                                        <th>Сумма</th>

                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    

                                </tbody>

                                


                            </table>

                    </div>

                    <div class="overlay">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                    
                </div>
            </div>


            <!-- Нижняя таблица -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                            <!-- downtable -->
                            <table id="pays_downtable2" class="display" style="width:100%">
                                <thead>
                                    <tr>
                                    
                                        
                                        <th>Название</th>
                                        <th>Начальный баланс</th>
                                        <th>Платежи</th>
                                        <th>Транзакции</th>
                                        <th>Баланс</th>
                                        
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    

                                </tbody>

                                


                            </table>

                    </div>

                    <div class="overlay">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                    
                </div>
            </div>



        </div>
    </div>
</section>







<?= $this->endSection() ?>