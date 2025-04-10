<?php
  $role = session()->get('role');
?>


<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>

    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Билеты</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/dashboard">Главная</a></li>
              <li class="breadcrumb-item active">Билеты</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    
	<div class="card">
			<div class="card-body">
				<table id="tickets" class="display" style="width:100%">
					<thead>
						<tr>            <?php if ($role == "superadmin") : ?>
                                <th>tickets_id</th>
                                <th>passengers_id</th>
                                <th>opr_id</th>
                                <th>tap_id</th>
                                <th>stamp_id</th>
                                <th>agency_id</th>
                            <?php endif; ?>

                
                            <th>Тип билета</th>
                            <!-- <th>tickets_system_id</th> -->
                            <!-- <th>tickets_system_session</th> -->
                            <!-- <th>tickets_system_bso_id</th> -->
                            <th>Валюта билета</th>
                            <th>Дата формирования</th>
                            <th>Время формирования</th>
                            <th>Тип операции</th>
                            <th>Тип транзакции</th>
                            <!--<th>tickets_MCO_TYPE</th>
                            <th>tickets_MCO_TYPE_rfic</th>
                            <th>tickets_MCO_TYPE_rfisc</th>-->
                            <th>Номер билета</th>
                            <th>Номер старшего билета</th>
                            <!--<th>tickets_GENERAL_CARRIER</th>
                            <th>tickets_RETTYPE</th>
                            <th>tickets_TOURCODE</th>
                            <th>tickets_OCURRENCY</th>
                            <th>tickets_ORATE</th>
                            <th>tickets_NCURRENCY</th>
                            <th>tickets_NRATE</th>
                            <th>tickets_OPRATE</th>-->
                            <th>Тариф</th>
                            <!--<th>tickets_FARE_type</th>
                            <th>tickets_FARE_vat_amount</th>
                            <th>tickets_FARE_vat_rate</th>
                            <th>tickets_OFARE</th>
                            <th>tickets_PENALTY</th>
                            <th>tickets_FARECALC</th>
                            <th>tickets_ENDORS_RESTR</th>
                            <th>tickets_PNR</th>-->
                            <th>PNR</th>
                            <!--<th>tickets_INV_PNR</th>
                            <th>tickets_CONJ</th>
                            <th>tickets_TO_BSONUM</th>
                            <th>tickets_TYP_NUM_ser</th>
                            <th>tickets_FCMODE</th>
                            <th>tickets_COMMISSION_type</th>
                            <th>tickets_COMMISSION_currency</th>
                            <th>tickets_COMMISSION_amount</th>
                            <th>tickets_COMMISSION_rate</th>
                            <th>tickets_BOOK_date</th>
                            <th>tickets_BOOK_disp</th>
                            <th>tickets_BOOK_time</th>
                            <th>tickets_BOOK_utc</th>-->
                            <th>Дата оформления</th>
                            <th>Индентификатор продавца</th>
                            <th>Время оформления</th>
                            <th>Время оформления UTC</th>
                            <!--<th>tickets_DEAL_ersp</th>
                            <th>tickets_DEAL_pcc</th>
                            <th>tickets_SALE_date</th>
                            <th>tickets_SALE_disp</th>
                            <th>tickets_SALE_time</th>
                            <th>tickets_SALE_utc</th>
                            <th>tickets_AGN_INFO_CLIENT_NUM</th>
                            <th>tickets_AGN_INFO_RESERV_NUM</th>
                            <th>tickets_AGN_INFO_INFO</th>-->
                            <th>Номер билета возврата</th>
							
						</tr>
					</thead>
					
				</table>
			</div>
	</div>	


    



<?= $this->endSection() ?>