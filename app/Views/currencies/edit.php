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
            <h1 class="m-0">Курс валюты</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Курс валюты / Изменить</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->

      <?php if (session()->get('errors')): ?>
        <div class="alert alert-danger">
            <?php foreach (session()->get('errors') as $error): ?>
                <?= esc($error) ?><br>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (session()->get('success')): ?>
        <div class="alert alert-success">
            <?= session()->get('success') ?>
        </div>
    <?php endif; ?>

    </div>

   



<!-- card -->
  <section class="content">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">Изменить данные</h3>
      </div>
      <!-- /.card-header -->
      <!-- form start -->
      <form role="form" method="post" action="<?= base_url('/currencies/update/' . $currency['id']) ?>">
            <div class="card-body">
                <div class="form-group">
                    <label for="date">Дата</label>
                    <input type="date" class="form-control" id="date" name="date" value="<?= $currency['date'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="name">Название</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Введите название" value="<?= $currency['name'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="value">Значение</label>
                    <input type="text" class="form-control" id="value" name="value" placeholder="Введите значение" value="<?= $currency['value'] ?>" required>
                </div>
            </div>
            <!-- /.card-body -->

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Отправить</button>
            </div>
        </form>


    </div>
  </section>    
<!-- /.card -->


<?= $this->endSection() ?>