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
            <h1>Курс валюты</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/dashboard">Главная</a></li>
              <li class="breadcrumb-item active">Курс валюты</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    
  <div class="card">
    <div class="card-header">
        
          <a class="btn btn-primary" href="/currencies/create">
            <i class="fas fa-user-plus"></i> Создать
          </a>

  
        
    </div>
      <div class="card-body">
        <table id="default" class="display" style="width:100%">
          <thead>
            <tr>
                            <th>Дата</th>
                            <th>Название</th>
                            <th>Значение</th>
                            <th>Действия</th>
                          
            </tr>
          </thead>
          <tbody>
                <?php foreach ($currencies as $currency): ?>
                    <tr>
                        
                        <td><?= $currency['date'] ?></td>
                        <td><?= $currency['name'] ?></td>
                        <td><?= $currency['value'] ?></td>
            
                        <td>
                            <a href="<?= base_url('currencies/edit/' . $currency['id']) ?>" class="btn btn-primary">Изменить</a>
                            <a href="<?= base_url('currencies/delete/' . $currency['id']) ?>" class="btn btn-danger" onclick="return confirmDelete()">Удалить</a>
                        </td>
                       
                        
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
      </div>
  </div>  


    



<?= $this->endSection() ?>




