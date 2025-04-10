<?php
  $role = session()->get('role');
?>


<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>

    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>EMD</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Главная</a></li>
              <li class="breadcrumb-item active">EMD</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    
	<div class="card">
			<div class="card-body">
				<table id="emd" class="display" style="width:100%">
					<thead>
						<tr>
                            <?php if ($role == "superadmin") : ?>
                                  <th>emd_id</th>
                                  <th>tickets_id</th>
                                  <th>passengers_id</th>
                              <?php endif; ?>

                            <!--<th>emd_coupon_no</th>-->
                            <th>Сумма EMD</th>
                            <!--<th>emd_remark</th>
                            <th>emd_related</th>
                            <th>emd_reason_rfisc</th>
                            <th>emd_reason_airline</th>
                            <th>emd_xbaggage_number</th>
                            <th>emd_xbaggage_qualifier</th>
                            <th>emd_xbaggage_rpu</th>
                            <th>emd_xbaggage_currency</th>-->
						</tr>
					</thead>
					
				</table>
			</div>
	</div>	


    



<?= $this->endSection() ?>