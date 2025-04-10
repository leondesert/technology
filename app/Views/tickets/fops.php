<?php
  $role = session()->get('role');
?>


<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>

    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>FOPS</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">FOPS</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    
	<div class="card">
			<div class="card-body">
				<table id="fops" class="display" style="width:100%">
					<thead>
						<tr>
                            <?php if ($role == "superadmin") : ?>
                                  <th>fops_id</th>
                                  <th>tickets_id</th>
                                  <th>passengers_id</th>
                              <?php endif; ?>
                           
                            <th>Вид оплаты</th>
                            <!--<th>fops_org</th>
                            <th>fops_docser</th>-->
                            <th>Номер билета</th>
                            <!--<th>fops_auth_info_code</th>
                            <th>fops_auth_info_currency</th>
                            <th>fops_auth_info_amount</th>
                            <th>fops_auth_info_provider</th>
                            <th>fops_auth_info_rrn</th>
                            <th>fops_docinfo</th>-->
                            <th>Cумма оплаты</th>
						</tr>
					</thead>
					
				</table>
			</div>
	</div>	


    



<?= $this->endSection() ?>