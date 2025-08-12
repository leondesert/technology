<?php

$session = session();
$role = $session->get('role');

?>
<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>

    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Предварительная раздача</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/dashboard">Главная</a></li>
              <li class="breadcrumb-item active">Предварительная раздача</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
<!-- Контейнер таблицы -->
<section class="content">
  <div class="container-fluid">
    <div class="card">
      <?php if ($role == "superadmin") : ?>
      <div class="card-header">
            <a class="btn btn-primary" href="/pre_share/create">
              <i class="fas fa-user-plus"></i> Создать
            </a>
      </div>

      <?php endif; ?>

        <div class="card-body">
          <table id="organization" class="table table-bordered table-striped" style="width:100%">
            <thead>
              <tr>
                              <th>Код</th>
                              <th>Название</th>
                              <th>Адрес</th>
                              <th>Телефон</th>
                              <th>Почта</th>
                              <th>Баланс TJS</th>
                              <th>Баланс RUB</th>

                              <?php if ($role == "superadmin" || $role == "admin") : ?>
                              <th>Операция</th>
                              <?php endif; ?>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
        <div class="overlay" id="overlay1">
            <i class="fas fa-2x fa-sync-alt fa-spin"></i>
        </div>
    </div>
  </div>
</section>
<?= $this->endSection() ?>