<?php
// Доход

?>

<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>


<!-- Content Header (Page header) -->
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Доход</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Доход</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->     
</section>



<!-- Контейнер таблицы -->
<section class="content">
    <div class="container-fluid">
    
        <div class="row">

            <!-- Фильтр -->
            <?= $this->include('blocks/form_filter') ?>


            <!-- Таблица 1 -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">


                            <table id="analytics" class="table table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Дата</th>
                                        <th>Доход</th>
                                        <th>Продажа</th>
                                        <th>Валюта</th>
                                        <th>Организация</th>
                                        <th>Название</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>

                    </div>

                    <div class="card-footer">
                        <h4 id="summa_dohoda">Сумма дохода: 0</h4>
                    </div>
                    <div class="overlay" id="overlay1">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                </div>
            </div>


            <!-- График -->
            <div class="col-md-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="far fa-chart-bar"></i>
                            График
                        </h3>

        
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            
                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                <i class="fas fa-expand"></i>
                            </button>
                            
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="line-chart-dohod" style="height: 300px;"></canvas>

                    </div>

                    <div class="overlay">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>


                </div>
            </div>

        </div>
    </div>




</section>


<script>
  var today = "<?php echo $dates['today']; ?>";
  var yesterday = "<?php echo $dates['yesterday']; ?>";
  var thisMonthFirst = "<?php echo $dates['thisMonthFirst']; ?>";
  var thisMonthLast = "<?php echo $dates['thisMonthLast']; ?>";
  var thisDecadeFirst = "<?php echo $dates['thisDecadeFirst']; ?>";
  var thisDecadeLast = "<?php echo $dates['thisDecadeLast']; ?>";
</script>





<?= $this->endSection() ?>