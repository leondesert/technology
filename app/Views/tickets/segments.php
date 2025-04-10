<?php
  $role = session()->get('role');
?>


<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>

    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Сегменты</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Сегменты</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    
  <div class="card">
      <div class="card-body">
        <table id="segments" class="display" style="width:100%">
          <thead>
            <tr>
                            <?php if ($role == "superadmin") : ?>
                                  <th>segments_id</th>
                                  <th>tickets_id</th>
                                  <th>passengers_id</th>
                            <?php endif; ?>

                            <!--
                            <th>segno</th>-->
                            <th>Маршрут</th>
                            <!--<th>port1code</th>
                            <th>port2code</th>-->
                            <th>Перевозчик</th>
                            <th>Класс</th>
                            <th>Рейс</th>
                            <th>Дата полёта</th>
                            <th>Время полёта</th>
                            <th>Код тарифа</th>
                            <!--<th>seg_bsonum</th>
                            <th>coupon_no</th>
                            <th>is_void</th>
                            <th>stpo</th>
                            <th>term1</th>
                            <th>term2</th>
                            <th>arrdate</th>
                            <th>arrtime</th>
                            <th>nfare</th>
                            <th>baggage_number</th>
                            <th>baggage_qualifier</th>
                            <th>ffp_info_number</th>
                            <th>ffp_info_certificate</th>
                            <th>exchanged</th>-->
            </tr>
          </thead>
          
        </table>
      </div>
  </div>  


    



<?= $this->endSection() ?>