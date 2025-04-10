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
            <h1 class="m-0">ППР</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">ППР / Создать</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

    

<!-- card -->
  <section class="content">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">Создать ППР</h3>
      </div>
      <!-- /.card-header -->
        <!-- form start -->
        <form method="post" action="/stamp/register">
            <div class="card-body">


                <div class="form-group">
                    <label for="stamp_code">Код ППР</label>
                    <input type="text" class="form-control" id="stamp_code" name="stamp_code" placeholder="Введите код">
                </div>
                <div class="form-group">
                    <label for="stamp_name">Название ППР</label>
                    <input type="text" class="form-control" id="stamp_name" name="stamp_name" placeholder="Введите название">
                </div>
                <div class="form-group">
                    <label for="stamp_address">Адрес ППР</label>
                    <input type="text" class="form-control" id="stamp_address" name="stamp_address" placeholder="Введите адрес">
                </div>
                <div class="form-group">
                    <label for="stamp_phone">Телефон ППР</label>
                    <input type="tel" class="form-control" id="stamp_phone" name="stamp_phone" placeholder="Введите телефон">
                </div>
                <div class="form-group">
                    <label for="stamp_mail">Электронная почта ППР</label>
                    <input type="email" class="form-control" id="stamp_mail" name="stamp_mail" placeholder="Введите email">
                </div>
                <div class="form-group">
                    <label for="reward">Вознаграждение</label>
                    <input type="number" class="form-control" id="reward" name="reward" placeholder="Введите сумму вознаграждения">
                </div>
                <div class="form-group">
                    <label for="balance">Баланс TJS</label>
                    <input type="number" class="form-control" id="balance_tjs" name="balance_tjs" placeholder="Введите баланс">
                </div>
                <div class="form-group">
                    <label for="balance">Баланс RUB</label>
                    <input type="number" class="form-control" id="balance_rub" name="balance_rub" placeholder="Введите баланс">
                </div>
                <div class="form-group">
                    <label for="penalty">Штраф</label>
                    <input type="number" class="form-control" id="penalty" name="penalty" placeholder="Введите сумму штрафа">
                </div>
            </div>
            <!-- /.card-body -->

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Отправить</button>
                <a href="#" onclick="history.back();" class="btn btn-warning">Назад</a>
            </div>
        </form>

    </div>
  </section>    
<!-- /.card -->


<?= $this->endSection() ?>