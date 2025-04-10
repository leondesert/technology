<?php
// Логи


?>

<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>




<!-- Content Header (Page header) -->
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Логи</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Логи</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->

</section>




</style>
<!-- Контейнер таблицы -->
<section class="content">
    <div class="container-fluid">
    
        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">

                        


                            <table id="logs" class="display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Дата</th>
                                        <th>Время</th>
                                        <th>Логин</th>
                                        <th>IP-адресс</th>
                                        <th>Действие</th>
                                        <th>Данные</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>



                    </div>
                </div>
            </div>


    <?php if ($role === 'superadmin') : ?>
            
            <div class="col-md-12">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">Просмотр логов</h3>
                    </div>
                    <div class="card-body">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="commandSelect">Выбрать лог:</label>
                                <select id="logFileSelect" class="form-control">
                                <?php foreach ($logFiles as $key => $path): ?>
                                    <option value="<?= $key ?>"><?= basename($path) ?></option>
                                <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <pre id="logContent" style="margin-top: 20px;"></pre>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="commandSelect">Выбрать команду:</label>
                                <select id="commandSelect" class="form-control">
                                    <?php foreach ($commands as $title => $command): ?>
                                        <option value="<?= $command ?>"><?= $title ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <button id="executeButton" class="btn btn-primary">Выполнить команду</button>
                        <div id="output" style="margin-top: 20px;"></div>


                    </div>
                </div>
            </div>


            <script>
                function fetchLog() {
                        const fileKey = $('#logFileSelect').val();
                        $.getJSON(`<?= site_url('logs/getLog') ?>/${fileKey}`, function(data) {
                            if (data.error) {
                                $('#logContent').text(data.error);
                            } else {
                                $('#logContent').text(data.join("\n"));
                            }
                        });
                    }
                
                document.addEventListener('DOMContentLoaded', function () {
                    $('#logFileSelect').change(fetchLog);
                        fetchLog();
                        setInterval(fetchLog, 2000); // Обновлять каждые 10 секунд


                    $('#executeButton').click(function() {
                    const selectedCommand = $('#commandSelect').val();
                    $.post('<?= site_url('logs/executeCommand') ?>', { command: selectedCommand }, function(data) {
                        $('#output').text(data.output);
                    }, 'json');
                });


                });


                
            </script>


    <?php endif; ?>


        </div>
    </div>
</section>





<!-- Модальное окно Данные -->
<div class="modal fade" id="jsonModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="filterModalLabel">Данные</h5>
        

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>

      </div>
      <div class="modal-body">
        <textarea id="jsonTextArea"></textarea>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div> 







<?= $this->endSection() ?>