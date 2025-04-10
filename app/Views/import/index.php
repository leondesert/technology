<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>

    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Import</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Import</li>
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
                        <h3 class="card-title">Импорт XML</h3>
                    </div>
                    <div class="card-body">
                        <form action="/import/xml" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="userfile">Выберите файл</label>
                                <input style="height: calc(2.25rem + 8px);" type="file" name="userfile" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Загрузить</button>
                            </div>
                        </form>
                    </div>
                </div>
               <!--  <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Импорт базы данных</h3>
                    </div>
                    <div class="card-body">
                        <form action="/import/sql" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="userfile">Выберите файл</label>
                                <input style="height: calc(2.25rem + 8px);" type="file" name="sql_file" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Загрузить</button>
                            </div>
                        </form>
                    </div>
                </div> -->
            </div>
        </div>
    </div>


<?= $this->endSection() ?>