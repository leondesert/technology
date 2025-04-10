<?php

$session = session();
$role = $session->get('role');

?>

<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>



<!-- Content Header (Page header) -->
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Пользователи</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Пользователи</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
</section>



<!-- Контейнер таблицы -->
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                    
                  <a class="btn btn-primary" href="/users/create">
                    <i class="fas fa-user-plus"></i> Создать пользователя
                  </a>

                        
                
            </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="default" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <!-- <th>user_id</th> -->
                            <th>Логин</th>
                            <!-- <th>agency_id</th>
                            <th>orp_id</th>
                            <th>tap_id</th>
                            <th>stamp_id</th> -->
                            <th>Роль</th>
                            <th>Операция</th> <!-- Adding a new header column for the buttons -->
                            
                            <!-- Добавьте другие столбцы таблицы, если нужно -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                
                                <td><?= $user['user_login'] ?></td>
                                <td><?= $user['role'] ?></td>
                                <td>
                                    <a href="<?= base_url('users/edit/' . $user['user_id']) ?>" class="btn btn-primary">Изменить</a>
                                    <a href="<?= base_url('users/delete/' . $user['user_id']) ?>" class="btn btn-danger" onclick="return confirmDelete()">Удалить</a>
                                </td>
                               
                                <!-- Вывод других столбцов, если есть -->
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>











<?= $this->endSection() ?>