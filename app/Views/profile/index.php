<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>


    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Мой профиль</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">Мой профиль</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    
    <?php 
      //Аватар
      $src = $user['user_photo_url'];
      if($src === '' || $src === null){
        $src = base_url()."dist/img/user2-160x160.jpg";
      }else{
        $src = base_url().'uploads/avatars/'.$user['user_photo_url'];
      }
      
    ?>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

          <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle" src="<?= $src; ?>" alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"><?= $user['user_login']; ?></h3>
                <p class="text-muted text-center"><?= $user['role']; ?></p>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->


            
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-body">
                <div class="tab-content">
                <form class="form-horizontal" action="<?= base_url('/profile/update')?>" method="post" enctype="multipart/form-data">
                      <div class="form-group row">
                        <label for="user_login" class="col-sm-2 col-form-label">Логин</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" name="user_login" id="user_login" placeholder="Логин" value="<?= $user['user_login'] ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="fio" class="col-sm-2 col-form-label">ФИО</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" name="fio" id="fio" placeholder="ФИО" value="<?= $user['fio'] ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="user_pass" class="col-sm-2 col-form-label">Новый пароль</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" name="user_pass" id="user_pass" placeholder="Новый пароль">
                        </div>
                      </div>
                      

                      <div class="form-group row">
                        <label for="avatar" class="col-sm-2 col-form-label">Аватар</label>
                        <div class="col-sm-10">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" id="avatar" name="avatar">
                            <label class="custom-file-label" for="avatar">Выберите файл</label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group row">
                        <label for="user_mail" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                          <input type="email" class="form-control" name="user_mail" id="user_mail" placeholder="Email" value="<?= $user['user_mail'] ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="user_phone" class="col-sm-2 col-form-label">Телефон</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" name="user_phone" id="user_phone" placeholder="Телефон" value="<?= $user['user_phone'] ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="user_desc" class="col-sm-2 col-form-label">DESC</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" name="user_desc" id="user_desc" placeholder="DESC" value="<?= $user['user_desc'] ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="filter" class="col-sm-2 col-form-label">Фильтр</label>
                        <div class="col-sm-10">
                            <select name="filter" class="form-control">
                                
                                <?php foreach ($table_names as $table_name): ?>
                                    <?php
                                        if ($table_name['value'] == $user['filter']) {
                                          $isSelected = 'selected';
                                        }else{
                                          $isSelected = '';
                                        }
                                    ?>
                                    <option value="<?=$table_name['value'];?>" <?=$isSelected;?>><?=$table_name['name'];?></option>
                                <?php endforeach; ?>  
                            </select>
                          </div>
                      </div>
                      <div class="form-group row">
                        <label for="secret_key" class="col-sm-2 col-form-label">Ключ</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" name="secret_key" id="secret_key" placeholder="Ключ" value="<?= $user['secret_key'] ?>">
                        </div>
                      </div>
                     
                      
                      
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <button type="submit" class="btn btn-danger">Сохранить</button>
                        </div>
                      </div>
                  </form>


                    <!-- <pre><php print_r($table_names2);?></pre> -->


                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->






<?= $this->endSection() ?>