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
                  <input type="text" class="form-control" id="fio" name="fio" value="<?= esc($user['fio'], 'attr') ?>" autocomplete="off" placeholder="Введите ФИО">
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail1">Логин</label>
                  <input type="text" class="form-control" id="login" name="login" value="<?= esc($user['user_login'], 'attr') ?>" autocomplete="off" placeholder="Введите логин">
                </div>
                <div class="form-group">
                  <label for="exampleInputPassword1">Новый пароль</label>
                  <input type="password" class="form-control" id="password" name="password" autocomplete="off" placeholder="Введите пароль">
                </div>
                <?php if ($role === 'superadmin') : ?>
                  <div class="form-group">
                    <label>Роль</label>
                    <select class="form-control" id="role" name="role">
                      <?php foreach ($roles as $item): ?>
                          <?php
                            $isSelected = ($item == $user['role']) ? 'selected' : '';
                          ?>
                          <option value="<?= esc($item, 'attr');?>" <?=$isSelected;?>><?= esc($item);?></option>
                      <?php endforeach; ?> 
                    </select>
                  </div>
                <?php else: ?>
                  <!-- Если не superadmin, можно просто отобразить текущую роль без возможности изменения, если это требуется -->
                  <!-- или скрыть поле, если роль не должна меняться другими -->
                  <input type="hidden" name="role" value="<?= esc($user['role'], 'attr') ?>">
                <?php endif; ?>

                
                <div class="form-group">
                    <label>Активный фильтр</label>
                    <select class="form-control" id="filter" name="filter">
                      <?php foreach ($filters as $filter_item): ?>
                          <?php
                              $isSelected = ($filter_item['value'] == $user['filter']) ? 'selected' : '';
                          ?>
                          <option value="<?= esc($filter_item['value'], 'attr');?>" <?=$isSelected;?>><?= esc($filter_item['name']);?></option>
                      <?php endforeach; ?>
                    </select>
                </div>

              <?php if ($role === 'superadmin'): ?>
                  <?php if (isset($potential_parents) && is_array($potential_parents)): ?>
                  <div class="form-group">
                      <label for="parent_id">Родительские пользователи</label>
                      <select name="parent_id[]" id="parent_id" class="select2" multiple="multiple" data-placeholder="Выбрать" style="width: 100%;">
                          <?php foreach ($potential_parents as $parent_option): ?>
                              <?php
                                  $isSelected = isset($selected_parents) && in_array($parent_option['user_id'], $selected_parents);
                                  $selectedAttribute = $isSelected ? 'selected' : '';
                              ?>
                              <option value="<?= esc($parent_option['user_id'], 'attr') ?>" <?= $selectedAttribute ?>>
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
                                <option value="<?= esc($agency['agency_id'], 'attr');?>" <?=$selectedAttribute;?>><?= esc($agency['agency_code']);?></option>
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
                              <option value="<?= esc($stamp['stamp_id'], 'attr');?>" <?=$selectedAttribute;?>><?= esc($stamp['stamp_code']);?></option>
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
                              <option value="<?= esc($tap['tap_id'], 'attr');?>" <?=$selectedAttribute;?>><?= esc($tap['tap_code']);?></option>
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
                              <option value="<?= esc($opr['opr_id'], 'attr');?>" <?=$selectedAttribute;?>><?= esc($opr['opr_code']);?></option>
                          <?php endforeach; ?>  
                      </select>
                  </div>
                  <?php endif; ?>

                  <!-- Раздача -->
                  <?php if (!empty($user['share_id']) || $role === "superadmin"):?>
                  <div class="form-group">
                        <label>Раздача</label>
                        <select name="shares[]" class="select2" multiple="multiple" data-placeholder="Выбрать" style="width: 100%;">
                            <?php foreach ($shares as $share_item): ?>
                                <?php
                                    $shareIdsArray = explode(',', $user['share_id']);
                                    $isSelected = in_array($share_item['share_id'], $shareIdsArray);
                                    $selectedAttribute = $isSelected ? 'selected' : '';
                                ?>
                                <option value="<?=$share_item['share_id'];?>" <?=$selectedAttribute;?>><?= esc($share_item['share_code']);?></option>
                            <?php endforeach; ?>
                        </select>
                  </div>
                  <?php endif; ?>

                  <!-- Предварительная раздача -->
                  <?php if (!empty($user['pre_share_id']) || $role === "superadmin"):?>
                  <div class="form-group">
                        <label>Предварительная раздача</label>
                        <select name="pre_shares[]" class="select2" multiple="multiple" data-placeholder="Выбрать" style="width: 100%;">
                            <?php foreach ($pre_shares as $pre_share_item): ?>
                                <?php
                                    $shareIdsArray = explode(',', $user['pre_share_id']);
                                    $isSelected = in_array($pre_share_item['pre_share_id'], $shareIdsArray);
                                    $selectedAttribute = $isSelected ? 'selected' : '';
                                ?>
                                <option value="<?= esc($pre_share_item['pre_share_id'], 'attr');?>" <?=$selectedAttribute;?>><?= esc($pre_share_item['pre_share_code']);?></option>
                            <?php endforeach; ?>
                        </select>
                  </div>
                  <?php endif; ?>

                  <?php if (session()->get('role') === 'superadmin'): ?>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="acquiring" id="acquiring" value="1" <?= (isset($user['acquiring']) && $user['acquiring'] == '1') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="acquiring">
                                Доступ к эквайрингу
                            </label>
                        </div>
                    </div>
                
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_airline" id="is_airline" value="1" <?= (isset($user['is_airline']) && $user['is_airline'] == '1') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_airline">
                                Пользователь авиакомпании
                            </label>
                        </div>
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