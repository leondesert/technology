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
            <h1 class="m-0">Раздача</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Раздача / Создать</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

    

<!-- card -->
  <section class="content">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">Создать раздачу</h3>
      </div>
      <!-- /.card-header -->
        <!-- form start -->
        <form method="post" action="/share/register">
            <?= csrf_field() ?>
            <div class="card-body">

                <div class="form-group">
                    <label for="share_code">Код раздачи</label>
                    <input type="text" class="form-control" id="share_code" name="share_code" placeholder="Введите код" required>
                </div>
                <div class="form-group">
                    <label for="share_name">Название раздачи</label>
                    <input type="text" class="form-control" id="share_name" name="share_name" placeholder="Введите название" required>
                </div>
                <div class="form-group">
                    <label for="share_address">Адрес раздачи</label>
                    <input type="text" class="form-control" id="share_address" name="share_address" placeholder="Введите адрес">
                </div>
                <div class="form-group">
                    <label for="share_phone">Телефон раздачи</label>
                    <input type="tel" class="form-control" id="share_phone" name="share_phone" placeholder="Введите телефон">
                </div>
                <div class="form-group">
                    <label for="share_mail">Электронная почта раздачи</label>
                    <input type="email" class="form-control" id="share_mail" name="share_mail" placeholder="Введите email">
                </div>
                <div class="form-group">
                    <label for="share_type">Тип раздачи</label>
                    <input type="text" class="form-control" id="share_type" name="share_type" placeholder="Введите тип раздачи">
                </div>
                <div class="form-group">
                    <label for="reward">Вознаграждение</label>
                    <input type="number" step="0.01" class="form-control" id="reward" name="reward" placeholder="Введите сумму вознаграждения" value="0">
                </div>
                <div class="form-group">
                    <label for="balance_tjs">Баланс TJS</label>
                    <input type="number" step="0.01" class="form-control" id="balance_tjs" name="balance_tjs" placeholder="Введите баланс" value="0">
                </div>
                <div class="form-group">
                    <label for="balance_rub">Баланс RUB</label>
                    <input type="number" step="0.01" class="form-control" id="balance_rub" name="balance_rub" placeholder="Введите баланс" value="0">
                </div>
                <div class="form-group">
                    <label for="penalty">Штраф</label>
                    <input type="number" step="0.01" class="form-control" id="penalty" name="penalty" placeholder="Введите сумму штрафа" value="0">
                </div>
            </div>
            <!-- /.card-body -->

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Отправить</button>
                <a href="/share" class="btn btn-warning">Назад</a>
            </div>
        </form>

    </div>
  </section>    
<!-- /.card -->


<?= $this->endSection() ?>