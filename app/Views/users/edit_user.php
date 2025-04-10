<?php

$session = session();
$role = $session->get('role');

?>

<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>

    <!-- card-header -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Пользователи</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Пользователи / Изменить пользователя</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

   

<!-- card -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
        <div class="card-header">
          <h3 class="card-title">Изменить данные пользователя</h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form action="<?= base_url('/users/update/' . $user['user_id']) ?>" method="post">
          <div class="card-body">

            <div class="row">
              <!-- Левая колонка -->
              <div class="col-md-6">
                <div class="form-group">
                  <label for="fio">ФИО</label>
                  <input type="text" class="form-control" id="fio" name="fio" value="<?= $user['fio'] ?>" autocomplete="off" placeholder="Введите ФИО">
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail1">Логин</label>
                  <input type="text" class="form-control" id="login" name="login" value="<?= $user['user_login'] ?>" autocomplete="off" placeholder="Введите логин">
                </div>
                <div class="form-group">
                  <label for="exampleInputPassword1">Новый пароль</label>
                  <input type="password" class="form-control" id="password" name="password" autocomplete="off" placeholder="Введите пароль">
                </div>
                <!-- <php if ($role === 'superadmin') : ?> -->
                  <div class="form-group">
                    <label>Роль</label>
                    <select class="form-control" id="role" name="role">
                      <?php foreach ($roles as $item): ?>
                          <?php

                              if ($item == $user['role']) {
                                $isSelected = 'selected';
                              }else{
                                $isSelected = '';
                              }
                          ?>
                          <option value="<?=$item;?>" <?=$isSelected;?>><?=$item;?></option>
                      <?php endforeach; ?> 
                    </select>
                  </div>
                <!-- <php endif; ?> -->
                
                <div class="form-group">
                    <label>Активный фильтр</label>
                    <select class="form-control" id="filter" name="filter">
                      <?php foreach ($filters as $filter): ?>
                          <?php

                              if ($filter['value'] == $user['filter']) {
                                $isSelected = 'selected';
                              }else{
                                $isSelected = '';
                              }
                          ?>
                          <option value="<?=$filter['value'];?>" <?=$isSelected;?>><?=$filter['name'];?></option>
                      <?php endforeach; ?> 
                    </select>
                </div>

                

                  


                

              </div>
              <!-- Правая колонка -->
              <div class="col-md-6">
                
                  <!-- Агентства -->
                  <?php if (!empty($user['agency_id']) || $role === "superadmin"):?>
                  <div class="form-group">
                        <label>Агентства</label>
                        <select name="agencies[]" class="select2" multiple="multiple" data-placeholder="Выбрать" style="width: 100%;">
                            <?php foreach ($agencies as $agency): ?>
                                <?php
                                    $agencyIdsArray = explode(',', $user['agency_id']);
                                    $isSelected = in_array($agency['agency_id'], $agencyIdsArray);
                                    $selectedAttribute = $isSelected ? 'selected' : '';
                                ?>
                                <option value="<?=$agency['agency_id'];?>" <?=$selectedAttribute;?>><?=$agency['agency_code'];?></option>
                            <?php endforeach; ?>  
                        </select>
                  </div>
                  <?php endif; ?>

                  <!-- ППР -->
                  <?php if (!empty($user['stamp_id']) || $role === "superadmin"):?>
                  <div class="form-group">
                      <label>ППР</label>
                      <select name="stamps[]" class="select2" multiple="multiple" data-placeholder="Выбрать" style="width: 100%;">
                          <?php foreach ($stamps as $stamp): ?>
                              <?php
                                  $stampIdsArray = explode(',', $user['stamp_id']);
                                  $isSelected = in_array($stamp['stamp_id'], $stampIdsArray);
                                  $selectedAttribute = $isSelected ? 'selected' : '';
                              ?>
                              <option value="<?=$stamp['stamp_id'];?>" <?=$selectedAttribute;?>><?=$stamp['stamp_code'];?></option>
                          <?php endforeach; ?>  
                      </select>
                  </div>
                  <?php endif; ?>
                  
                  <!-- Пульт -->
                  <?php if (!empty($user['tap_id']) || $role === "superadmin"):?>
                  <div class="form-group">
                      <label>Пульт</label>
                      <select name="taps[]" class="select2" multiple="multiple" data-placeholder="Выбрать" style="width: 100%;">
                          <?php foreach ($taps as $tap): ?>
                              <?php
                                  $tapIdsArray = explode(',', $user['tap_id']);
                                  $isSelected = in_array($tap['tap_id'], $tapIdsArray);
                                  $selectedAttribute = $isSelected ? 'selected' : '';
                              ?>
                              <option value="<?=$tap['tap_id'];?>" <?=$selectedAttribute;?>><?=$tap['tap_code'];?></option>
                          <?php endforeach; ?>  
                      </select>
                  </div>
                  <?php endif; ?>

                  <!-- Оператор -->
                  <?php if (!empty($user['opr_id']) || $role === "superadmin"):?>
                  <div class="form-group">
                      <label>Оператор</label>
                      <select name="oprs[]" class="select2" multiple="multiple" data-placeholder="Выбрать" style="width: 100%;">
                          <?php foreach ($oprs as $opr): ?>
                              <?php
                                  $oprIdsArray = explode(',', $user['opr_id']);
                                  $isSelected = in_array($opr['opr_id'], $oprIdsArray);
                                  $selectedAttribute = $isSelected ? 'selected' : '';
                              ?>
                              <option value="<?=$opr['opr_id'];?>" <?=$selectedAttribute;?>><?=$opr['opr_code'];?></option>
                          <?php endforeach; ?>  
                      </select>
                  </div>
                  <?php endif; ?>




                  

                
              </div>
            </div>
            
            

            





          </div>
          <!-- /.card-body -->

          <div class="card-footer">
            <button type="submit" class="btn btn-primary">Обновить</button>
            <a href="/users" class="btn btn-warning">Назад</a>
          </div>
        </form>
      </div>
    </div>
  </section>    
<!-- /.card -->


<?= $this->endSection() ?>