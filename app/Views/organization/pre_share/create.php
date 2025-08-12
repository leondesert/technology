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
            <h1 class="m-0">Предварительная раздача</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Предварительная раздача / Создать</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

    

<!-- card -->
  <section class="content">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">Создать предварительную раздачу</h3>
      </div>
      <!-- /.card-header -->
        <!-- form start -->
        <form method="post" action="/pre_share/register">
            <div class="card-body">

                <div class="form-group">
                    <label for="pre_share_code">Код предварительной раздачи</label>
                    <input type="text" class="form-control" id="pre_share_code" name="pre_share_code" placeholder="Введите код">
                </div>
                <div class="form-group">
                    <label for="pre_share_name">Название предварительной раздачи</label>
                    <input type="text" class="form-control" id="pre_share_name" name="pre_share_name" placeholder="Введите название">
                </div>
                <div class="form-group">
                    <label for="pre_share_address">Адрес предварительной раздачи</label>
                    <input type="text" class="form-control" id="pre_share_address" name="pre_share_address" placeholder="Введите адрес">
                </div>
                <div class="form-group">
                    <label for="pre_share_phone">Телефон предварительной раздачи</label>
                    <input type="tel" class="form-control" id="pre_share_phone" name="pre_share_phone" placeholder="Введите телефон">
                </div>
                <div class="form-group">
                    <label for="pre_share_mail">Электронная почта предварительной раздачи</label>
                    <input type="email" class="form-control" id="pre_share_mail" name="pre_share_mail" placeholder="Введите email">
                </div>
                <!-- <div class="form-group">
                    <label for="pre_share_type">Тип предварительной раздачи</label>
                    <input type="text" class="form-control" id="pre_share_type" name="pre_share_type" placeholder="Введите тип раздачи">
                </div> -->
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
                <a href="#" onclick="history.back();" class="btn btn-warning">Назад</a>
            </div>
        </form>

    </div>
  </section>    
<!-- /.card -->


<?= $this->endSection() ?>