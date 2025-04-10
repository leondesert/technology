<?php
  $role = session()->get('role');
?>


<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>

    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>TAXES</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">TAXES</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    

<!-- Контейнер -->
<section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-md-12">
              <div class="card">
                  <div class="card-body">
                    <table id="taxes" class="display" style="width:100%">
                      <thead>
                        <tr>
                                        <?php if ($role == "superadmin") : ?>
                                              <th>taxes_id</th>
                                              <th>tickets_id</th>
                                              <th>passengers_id</th>
                                        <?php endif; ?>
                                        
                                        <th>segno</th>
                                        <th>tax_code</th>
                                        <th>tax_amount</th>
                                        <th>tax_namount</th>
                                        <th>tax_ncurrency</th>
                                        <th>tax_nrate</th>
                                        <th>tax_oamount</th>
                                        <th>tax_ocurrency</th>
                                        <th>tax_orate</th>
                                        <th>tax_oprate</th>
                                        <th>tax_taxes_vat_amount</th>
                                        <th>tax_taxes_vat_rate</th>
                                        <th>tax_tax_vat_amount</th>
                                        <th>tax_tax_vat_rate</th>
                                        <th>exchanged</th>
                        </tr>
                      </thead>
                      
                    </table>





                  </div>
              </div>
          </div>



          

    </div>
</section>


<!-- Контейнер -->
<section class="content">
    <div class="container-fluid">
        <div class="row">

          <!-- Уникальные таксы -->
          <div class="col-md-6">
            

                  <!-- Add Tax -->
                  <div class="card card-primary">
                      <div class="card-header">
                          <h3 class="card-title">Добавить такс</h3>
                      </div>

                          <form method="post" action="/taxes/add-column">
                              <div class="card-body">
                                  <?php if (isset($add_message)): ?>
                                      <div class="alert alert-info"><?= $add_message ?></div>
                                  <?php endif; ?>
                                  
                                  <div class="form-group">
                                      <label for="column_name">Имя такса</label>
                                      <input type="text" class="form-control" id="column_name" name="column_name" required>
                                  </div>
                              </div>
                              <div class="card-footer">
                                  <button type="submit" class="btn btn-primary">Добавить такс</button>
                              </div>
                          </form>

                  </div>
          </div>

          <div class="col-md-6">
            <!-- Delete Tax -->
                  <div class="card card-danger">
                      <div class="card-header">
                          <h3 class="card-title">Удалить Такс</h3>
                      </div>
                      <form method="post" action="/taxes/delete-column">
                          <div class="card-body">
                              <?php if (isset($delete_message)): ?>
                                  <div class="alert alert-info"><?= $delete_message ?></div>
                              <?php endif; ?>
                              
                              <!-- Таксы -->
                              <div class="form-group">
                                <label for="column_name">Таксы</label>
                                <select class="form-control" id="column_name" name="column_name" >

                                      <?php foreach($taxes as $tax): ?>
                                          
                                          <option value="<?= $tax ?>">
                                              <?= $tax ?>
                                          </option>
                                          
                                      <?php endforeach; ?>

                                  </select>
                              </div>


                          </div>
                          <div class="card-footer">
                              <button type="submit" class="btn btn-danger">Удалить Такс</button>
                          </div>
                      </form>

                      <!-- <pre><php print_r($taxes); ></pre> -->


                  </div>
          </div>


        </div>   
    </div>
</section>


<?= $this->endSection() ?>