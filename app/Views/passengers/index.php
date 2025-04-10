<?php
  $role = session()->get('role');
?>

<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>


<style>
    

      .custom-loader {
              width: 100%;
              position: fixed;
              bottom: 0;
              left: 0;
              z-index: 9999;
          }

          .table-container {
              position: relative;
              width: 100%;

          } 

          .custom-loader .progress {
              height: 20px; /* Увеличиваем высоту контейнера прогресс-бара */
          }

          .custom-loader .progress-bar {
              height: 100%; /* Убеждаемся, что прогресс-бар занимает всю высоту контейнера */
          }


      div.dataTables_processing > div:last-child {
          display: none;
      }

</style>


    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Пассажиры</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Пассажиры</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>


<!-- Контейнер -->
<section class="content">
    <div class="container-fluid">
    
        <div class="row">

            <!-- Таблица -->
            <div class="col-md-12">

              	<div class="card card-success">
                  <div class="card-header">
                        <h3 class="card-title">
                            <i class="far fa-chart-bar"></i>
                            Пассажиры
                        </h3>

        
                        <div class="card-tools">                            
                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>

              			<div class="card-body">
              				<table id="passengers" class="table table-bordered table-striped" style="width:100%">
              					<thead>
              						<tr>          
                                    <?php if ($role === "superadmin"):?>
                                          <th>ID</th>
                                    <?php endif; ?>
                                    
                                          <th>ФИО</th>
                                          <th>Фамилия</th>
                                          <th>Имя</th>
                                          <th>Пасспорт</th>
                                          <th>Тип пассажира</th>
                                          <th>Льгота</th>
                                          <th>Дата рождения</th>
                                          <th>Пол</th>
                                          <th>Гражданство</th>
                                          <th>Контакты</th>
              						</tr>
              					</thead>
              					
              				</table>
              			</div>
              	</div>

            </div>
        </div>
    </div>
</section>
    



<?= $this->endSection() ?>