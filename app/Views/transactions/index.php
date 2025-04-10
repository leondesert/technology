<?php


?>

<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>

<!-- Content Header (Page header) -->
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Транзакции</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Транзакции</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->

</section>

<style>
    .bold-row {
    font-weight: bold;
}

</style>

<!-- Контейнер таблицы -->
<section class="content">
    <div class="container-fluid">
    
        <div class="row">

            <!-- Фильтр -->
            <?= $this->include('blocks/form_filter') ?>

            <!-- Таблица -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="far fa-chart-bar"></i>
                            Транзакции
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">

                            <!-- transactions -->
                            <table id="transactions" class="display" style="width:100%">
                                <thead>
                                    <tr>

                                        <th>Дата создания</th>
                                        <th>Дата транзакции</th>
                                        <th>Номер транзакции</th>
                                        <th>Сумма</th>
                                        <th>Валюта</th>
                                        <th>Метод оплаты</th>
                                        <th>Банк</th>
                                        <?php if ($user['acquiring'] === '1') : ?>
                                            <th>Эквайринг</th>
                                        <?php endif; ?>

                                        <th>Примечание</th>
                                        <th>Чек</th>
                                        <th>Организация</th>
                                        <th>Название</th>
                                        <th>Операция</th>
                                        
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>

                    </div>

                    <div class="overlay">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                </div>
            </div>

            <!-- Нижняя таблица -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="far fa-chart-bar"></i>
                            Нижняя таблица
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                            <!-- downtable -->
                            <table id="trans_downtable" class="display" style="width:100%">
                                <thead>
                                    <tr>

                                        <th>Организация</th>
                                        <th>Название</th>
                                        <th>Количество</th>
                                        <th>Сумма</th>
                                        
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    

                                </tbody>
                            </table>

                    </div>

                    <div class="overlay">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                    
                </div>
            </div>

            
            <!-- График -->
            <div class="col-md-12">
                <!-- График -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="far fa-chart-bar"></i>
                            График
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="line-chart" style="height: 300px;"></canvas>
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

</script>


<script>
  var today = "<?php echo $dates['today']; ?>";
  var yesterday = "<?php echo $dates['yesterday']; ?>";
  var thisMonthFirst = "<?php echo $dates['thisMonthFirst']; ?>";
  var thisMonthLast = "<?php echo $dates['thisMonthLast']; ?>";
  var thisDecadeFirst = "<?php echo $dates['thisDecadeFirst']; ?>";
  var thisDecadeLast = "<?php echo $dates['thisDecadeLast']; ?>";
  var isAcquiring = "<?php echo $user['acquiring']; ?>";


</script>





    <!-- Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="imageModalLabel">Чек</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-center">
            <img id="modalImage" src="" class="img-fluid" alt="Receipt Image">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
          </div>
        </div>
      </div>
    </div>




<?= $this->endSection() ?>