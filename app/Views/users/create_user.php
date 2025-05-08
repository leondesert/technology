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
              <li class="breadcrumb-item active">Пользователи / Создать</li>
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
          <h3 class="card-title">Создать нового пользователя</h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form action="/users/register" method="post">
          <div class="card-body">
            <div class="row">
              <!-- Левая колонка -->
              <div class="col-md-6">
                <div class="form-group">
                  <label for="fio">ФИО</label>
                  <input type="text" class="form-control" id="fio" name="fio" placeholder="Введите ФИО" autocomplete="off">
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail1">Логин</label>
                  <input type="text" class="form-control" id="login" name="login" placeholder="Введите логин" autocomplete="off" required>
                </div>
                <div class="form-group">
                  <label for="exampleInputPassword1">Пароль</label>
                  <input type="password" class="form-control" id="password" name="password" placeholder="Введите пароль" autocomplete="off" required>
                </div>
                <?php if ($role === 'superadmin' || $role === 'admin') : ?>
                  <div class="form-group">
                    <label>Роль</label>
                    <select class="form-control" id="role" name="role">
                      <option>admin</option>
                      <option>user</option>
                    </select>
                  </div>
                <?php endif; ?>
                  
                  <!-- Активный фильтр -->
                  <div class="form-group">
                    <label>Активный фильтр</label>
                    <select class="form-control" id="filter" name="filter">
                      <?php foreach ($filters as $filter): ?>
                          <option value="<?=$filter['value'];?>"><?=$filter['name'];?></option>
                      <?php endforeach; ?> 
                    </select>
                  </div>

                  <?php if ($role === 'superadmin'): ?>
                      <?php if (isset($potential_parents) && is_array($potential_parents)): ?>
                      <div class="form-group">
                          <label for="parent_id">Родительский пользователь</label>
                          <select name="parent_id" id="parent_id" class="form-control">
                              <option value="">-- Не выбран --</option>
                              <?php foreach ($potential_parents as $parent_option): ?>
                                  <option value="<?= esc($parent_option['user_id']) ?>">
                                      <?= esc($parent_option['user_login']) ?> (<?= esc($parent_option['fio']) ?>)
                                  </option>
                              <?php endforeach; ?>
                          </select>
                      </div>
                      <?php endif; ?>
                  <?php endif; ?>

              </div>

              <!-- Правая колонка -->
              <div class="col-md-6">

                <!-- Агентства -->
                <?php if (!empty($user['agency_id']) || $user['role'] === "superadmin"):?>
                <div class="form-group">
                    <label>Агентства</label>
                    <select name="agencies[]" class="select2" multiple="multiple" data-placeholder="Выбрать" style="width: 100%;">
                      <?php foreach ($agencies as $agency): ?>
                        <option value="<?=$agency['agency_id'];?>"><?=$agency['agency_code'];?></option>
                      <?php endforeach; ?>  
                    </select>
                </div>
                <?php endif; ?>

                <!-- ППР -->
                <?php if (!empty($user['stamp_id']) || $user['role'] === "superadmin"):?>
                <div class="form-group">
                      <label>ППР</label>
                      <select name="stamps[]" class="select2" multiple="multiple" data-placeholder="Выбрать" style="width: 100%;">
                        <?php foreach ($stamps as $stamp): ?>
                          <option value="<?=$stamp['stamp_id'];?>"><?=$stamp['stamp_code'];?></option>
                        <?php endforeach; ?>  
                      </select>
                </div>
                <?php endif; ?>

                <!-- Пульт -->
                <?php if (!empty($user['tap_id']) || $user['role'] === "superadmin"):?>
                <div class="form-group">
                      <label>Пульт</label>
                      <select name="taps[]" class="select2" multiple="multiple" data-placeholder="Выбрать" style="width: 100%;">
                        <?php foreach ($taps as $tap): ?>
                          <option value="<?=$tap['tap_id'];?>"><?=$tap['tap_code'];?></option>
                        <?php endforeach; ?>  
                      </select>
                </div>
                <?php endif; ?>
                
                <!-- Оператор -->
                <?php if (!empty($user['opr_id']) || $user['role'] === "superadmin"):?>
                <div class="form-group">
                      <label>Оператор</label>
                      <select name="oprs[]" class="select2" multiple="multiple" data-placeholder="Выбрать" style="width: 100%;">
                        <?php foreach ($oprs as $opr): ?>
                          <option value="<?=$opr['opr_id'];?>"><?=$opr['opr_code'];?></option>
                        <?php endforeach; ?>  
                      </select>
                </div>
                <?php endif; ?>

                



              </div>
            </div>

            
          </div>

            
          <!-- /.card-body -->

          <div class="card-footer">
            <button type="submit" class="btn btn-primary">Создать</button>
            <a href="/users" class="btn btn-warning">Назад</a>
          </div>
        </form>
      </div>
    </div>
  </section>    
<!-- /.card -->


<?= $this->endSection() ?>