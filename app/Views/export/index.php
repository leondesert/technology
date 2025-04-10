<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>

    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Export</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Export</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->

      <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('success')): ?>
          <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
      <?php endif; ?>


    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Экспорт таблиц из базы данных в SQL</h3>
                    </div>
                    <div class="card-body">
                        <form action="/export/sql" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Экспортировать</button>
                            </div>
                        </form>


                        <?=print_r($params);?>

                        
                    </div>
                </div>
            </div>
        </div>
    </div>


<?= $this->endSection() ?>