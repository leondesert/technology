<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>

    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Payments</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Payments</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    
	<div class="card">
			<div class="card-body">
				<table id="payments" class="display" style="width:100%">
					<thead>
						<tr>
              <th>id</th>
              <th>created_at</th>
              <th>updated_at</th>
              <th>status</th>
              <th>amount</th>
              <th>currency</th>
              <th>info</th>
						</tr>
					</thead>
					<tfoot>
            <tr>
              <th>id</th>
              <th>created_at</th>
              <th>updated_at</th>
              <th>status</th>
              <th>amount</th>
              <th>currency</th>
              <th>info</th>
            </tr>
					</tfoot>
				</table>
			</div>
	</div>

<?= $this->endSection() ?>