<?php

// Услуги

$role = session()->get('role');
$user_id = session()->get('user_id');

?>

<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>

    <!-- card-header -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Услуги</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Услуги / Изменить услуги</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->

    </div>

   



<!-- card -->
  <section class="content">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">Изменить данные услуги</h3>
      </div>
      <!-- /.card-header -->
      <!-- form start -->
      <form role="form" method="post" action="<?= base_url('/services/update/' . $services['id']) ?>" enctype="multipart/form-data">
                  <div class="card-body">

                    <div class="row">
                          <!-- Левая колонка -->
                          <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount">Сумма</label>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="<?= $services['amount'] ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Валюта</label>
                                <select name="currency" id="currency" class="form-control" required>

                                  <?php foreach ($currencies as $currency): ?>
                                          <?php
                                              if ($currency == $services['currency']) {
                                                $isSelected = 'selected';
                                              }else{
                                                $isSelected = '';
                                              }
                                          ?>
                                      <option value="<?=$currency;?>" <?=$isSelected;?>><?=$currency;?></option>
                                  <?php endforeach; ?>  

                              
                                </select>
                            </div>

                           


                            <div class="form-group">
                                <label>Дата услуги</label>
                                <div class="input-group date" id="startDatePicker" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" data-target="#startDatePicker" name="doc_date" value="<?= $services['doc_date'] ?>" required/>
                                    <div class="input-group-append" data-target="#startDatePicker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="doc_number">Номер услуги</label>
                                <input type="text" class="form-control" id="doc_number" name="doc_number" value="<?= $services['doc_number'] ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="service_name">Название услуги</label>
                                <input type="text" class="form-control" id="service_name" name="service_name" value="<?= $services['service_name'] ?>" required>
                            </div>


                            <div class="form-group">
                              <label for="doc_scan">Скан документа</label>
                              <div class="input-group">
                                <div class="custom-file">
                                  <input type="file" class="custom-file-input" id="doc_scan" name="doc_scan">
                                  <label class="custom-file-label" for="doc_scan">Выберите файл</label>
                                </div>
                                <!-- <div class="input-group-append">
                                  <span class="input-group-text">Загрузить</span>
                                </div> -->
                              </div>
                            </div>
                          </div>
                          <!-- Правая колонка -->
                          <div class="col-md-6">

                            <div class="form-group">
                                <label>Метод оплаты</label>
                                <select name="method" id="method" class="form-control" required>

                                  <?php foreach ($methods as $method): ?>
                                          <?php
                                              if ($method == $services['method']) {
                                                $isSelected = 'selected';
                                              }else{
                                                $isSelected = '';
                                              }
                                          ?>
                                      <option value="<?=$method;?>" <?=$isSelected;?>><?=$method;?></option>
                                  <?php endforeach; ?>  

                              
                                </select>
                            </div>


                            <div class="form-group" id="bank_block">
                                <label>Банк</label>
                                <select name="bank" id="bank" class="form-control" required>

                                  <?php foreach ($banks as $item): ?>
                                          <?php
                                              if ($item == $services['bank']) {
                                                $isSelected = 'selected';
                                              }else{
                                                $isSelected = '';
                                              }
                                          ?>
                                      <option value="<?=$item;?>" <?=$isSelected;?>><?=$item;?></option>



                                  <?php endforeach; ?>  

                              
                                </select>
                            </div>


                         <?php if ($user['acquiring'] === '1') : ?>
                            
                            <div class="form-group" id="acquiring_block">
                                <label>Эквайринг</label>
                                <select name="acquiring" id="acquiring" class="form-control">
                                  <option value="not_select">- Выбрать -</option>
                                  <?php foreach ($acquirings as $acquiring): ?>
                                          <?php
                                              if ($acquiring['value'] == $services['acquiring']) {
                                                $isSelected = 'selected';
                                              }else{
                                                $isSelected = '';
                                              }
                                          ?>
                                      <option value="<?=$acquiring['value'];?>" <?=$isSelected;?>><?=$acquiring['name'];?></option>
                                  <?php endforeach; ?>  

                              
                                </select>
                            </div>

                          <?php endif; ?>



                            <div class="form-group">
                                <label for="note">Примечание</label>
                                <textarea class="form-control" id="note" name="note" rows="3" value="<?= $services['note'] ?>"></textarea>
                            </div>
                            <div class="form-group">
                                  <label>Организация</label>
                                  <select name="name_table" class="form-control" required>
                                      <option value=""></option>
                                  </select>
                              </div>


                              <div class="form-group">
                                  <label>Название</label>
                                  <select name="value_table" class="form-control" required>
                                    <option value=""></option>
                                  </select>
                              </div>

                                <select class="form-control" id="user_login" name="user_login" style="display: none;">
                                    <option value="<?= $user_id ?>">
                                        <?= $user_id ?>
                                    </option>
                                </select>
                                
                                <!-- обновить при обновлении стр -->
                                <input type="hidden" id="is_refresh" value="yes">
                                <input type="hidden" id="is_update" value="no">

                                <!-- значения по умолчанию -->
                                <input type="hidden" id="selected_name_table" value="<?= $services['name'] ?>">
                                <input type="hidden" id="selected_value_table" value="<?= $services['value'] ?>">


                          </div>
                    </div>



                      
                      

                  </div>
                  <!-- /.card-body -->

                  <div class="card-footer">
                      <button type="submit" class="btn btn-primary">Сохранить</button>
                      <a href="/services" class="btn btn-warning">Назад</a>
                  </div>
              </form>
    </div>
  </section>    
<!-- /.card -->



<script type="text/javascript" language="javascript" src="<?= base_url(); ?><?= latest('js/custom/filter_form.js'); ?>"></script>


<script type="text/javascript" language="javascript" src="<?= base_url(); ?><?= latest('js/custom/for_create_edit.js'); ?>"></script>





<?= $this->endSection() ?>