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
            <h1 class="m-0">Пульты</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Пульты / Создать</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

    

<!-- card -->
  <section class="content">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">Создать агенства</h3>
      </div>
      <!-- /.card-header -->
        <!-- form start -->
        <form method="post" action="/tap/register">
            <div class="card-body">


               


                <div class="form-group">
                    <label for="tap_code">Код агентства</label>
                    <input type="text" class="form-control" id="tap_code" name="tap_code" placeholder="Введите код">
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