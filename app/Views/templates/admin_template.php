<?php
use App\Controllers\Dashboard;
use App\Models\UserModel;

// получить даты
$dashboard = new Dashboard(); 
$dates = $dashboard->getDates();

// Текущая декада
$decade_start_date = $dates['thisDecadeFirst'];
$decade_end_date = $dates['thisDecadeLast'];

// Дата для операции
// $start_date = $dates['lastMonthFirst'];
// $end_date = $dates['thisMonthLast'];
$start_date = $dates['thisDecadeFirst'];
$end_date = $dates['thisDecadeLast'];

$userID = session()->get('user_id');
$userModel = new UserModel();
$user = $userModel->where('user_id', $userID)->first();



$role = session()->get('role');
$ids = session()->get('ids');
$colum_name = session()->get('colum_name');
$filter = session()->get('filter');

if($role === "superadmin"){
  $colum_name = 'agency_id';
  $ids = null;
}else{
  $ids = "(" . implode(",", $ids) . ")";
}

//Аватар
$user_image = $user['user_photo_url'];
if($user_image === '' || $user_image === null){
  $user_image = base_url()."dist/img/user2-160x160.jpg";
}else{
  $user_image = base_url().'uploads/avatars/'.$user['user_photo_url'];
}

// шапка excel
$user_desc = $user['user_desc'];
if(empty($user_desc)){
  $user_desc = 'Tickets';
}

// состояния таблиц
$tables_states = $user['tables_states'];
if(empty($tables_states)){
  $tables_states = '{}';
}


// uniqueTaxCodes
$uniqueTaxCodes = session()->get('uniqueTaxCodes');

?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>АВС Technology</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url(); ?>plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="<?= base_url(); ?>plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?= base_url(); ?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="<?= base_url(); ?>plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url(); ?>dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?= base_url(); ?>plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?= base_url(); ?>plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="<?= base_url(); ?>plugins/summernote/summernote-bs4.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?= base_url(); ?>plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?= base_url(); ?>plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="<?= base_url(); ?>plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="<?= base_url(); ?>plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="<?= base_url(); ?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

  <!-- DATA_TABLE_EDITOR -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/datetime/1.5.1/css/dataTables.dateTime.min.css">

  <!-- Стили для уведомлений -->
  <style>
    .notification-badge-container {
        display: flex;
        position: absolute;
        top: 5px;
        right: 10px;
    }
    .notification-badge {
      min-width: 18px;
      height: 18px;
      border-radius: 50%;
      background-color: #dc3545;
      color: white;
      font-size: 11px;
      font-weight: bold;
      text-align: center;
      line-height: 18px;
      padding: 0 4px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
      z-index: 1000;
      margin-left: 2px;
    }
    
    .notification-badge.badge-success {
      background-color: #28a745;
    }
    
    .notification-badge.badge-warning {
      background-color: #ffc107;
      color: #212529;
    }
    
    .notification-badge.badge-danger {
      background-color: #dc3545;
    }
    
    .nav-item {
      position: relative;
    }
    
    .nav-link {
      position: relative;
    }
  </style>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/searchbuilder/1.6.0/css/searchBuilder.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>css/editor.dataTables.min.css"> 

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/colreorder/1.5.2/css/colReorder.dataTables.min.css"/>

    <!-- Toastr -->
  <link rel="stylesheet" href="<?= base_url(); ?>plugins/toastr/toastr.min.css">
   
  <!-- CodeMirror -->
  <link rel="stylesheet" href="<?= base_url(); ?>plugins/codemirror/codemirror.css">
  <link rel="stylesheet" href="<?= base_url(); ?>plugins/codemirror/theme/monokai.css">
  <link rel="stylesheet" href="<?= base_url(); ?>css/custom.css">

  <style>

    /* отступ между кнопками*/
    .button_spacing {
      margin: 3px; /* Adjust the value as needed */
    }


    /* Отключение выделения текста на всей странице */
    /*body {
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none; 
        -ms-user-select: none;
    }*/


    
  </style>


</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
<div class="wrapper">

  



  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <!-- <h6><=$colum_name. ' = '. $ids;?></h6> -->
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <!-- Кнопка "Выйти" (Logout) -->
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="/logout" id="logout-btn">
              <i class="fa fa-sign-out-alt"></i> Выйти
            </a>
          </li>
        </ul>
      </li>
    <li class="nav-item">
    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
    <i class="fas fa-expand-arrows-alt"></i>
    </a>
    </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- <aside class="main-sidebar main-sidebar-custom sidebar-dark-primary elevation-4"> -->
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
      <img src="<?= base_url(); ?>dist/img/avs-logo.png" alt="avs-logo" class="brand-image img-circle elevation-3">
      <span class="brand-text font-weight-light"><b>AVS</b> Technology</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
            <img src="<?= $user_image; ?>" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
            <a href="/profile" class="d-block"><?= $user['user_login'] ?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          
          
              <li class="nav-item">
                <a href="/dashboard" class="nav-link">
                  <i class="nav-icon fas fa-tachometer-alt"></i>
                  <p>
                    Главная
                  </p>
                </a>
              </li>
          
          <?php if ($user['role'] === 'admin' || $user['role'] === 'superadmin') : ?>
              <li class="nav-item">
                <a href="/users" class="nav-link">
                  <i class="nav-icon fas fa-users"></i>
                  <p>
                    Пользователи
                  </p>
                </a>
              </li>
          <?php endif; ?>
          
              

              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-building"></i>
                  <p>
                  Организация
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <?php if (!empty($user['agency_id']) || $user['role'] === "superadmin"):?>
                  <li class="nav-item">
                    <a href="/agency" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Агентства</p>
                    </a>
                  </li>
                  <?php endif; ?>
                  <?php if (!empty($user['stamp_id']) || $user['role'] === "superadmin"):?>
                  <li class="nav-item">
                    <a href="/stamp" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>ППР</p>
                    </a>
                  </li>
                  <?php endif; ?>
                  <?php if (!empty($user['tap_id']) || $user['role'] === "superadmin"):?>
                  <li class="nav-item">
                    <a href="/tap" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Пульты</p>
                    </a>
                  </li>
                  <?php endif; ?>
                  <?php if (!empty($user['opr_id']) || $user['role'] === "superadmin"):?>
                  <li class="nav-item">
                    <a href="/opr" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Операторы</p>
                    </a>
                  </li>
                  <?php endif; ?>
                  <?php if (!empty($user['share_id']) || $user['role'] === "superadmin"):?>
                  <li class="nav-item">
                    <a href="/share" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Раздача</p>
                    </a>
                  </li>
                  <?php endif; ?>
                  <?php if (!empty($user['pre_share_id']) || $user['role'] === "superadmin"):?>
                  <li class="nav-item">
                    <a href="/pre_share" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Пред. Раздача</p>
                    </a>
                  </li>
                  <?php endif; ?>
                </ul>
              </li>
              
              <li class="nav-item">
                <a href="/operations" class="nav-link">
                  <i class="nav-icon fas fa-edit"></i>
                  <p>
                    Операции
                  </p>
                </a>
              </li>
              
              <li class="nav-item">
                <a href="/transactions" class="nav-link">
                  <i class="nav-icon fas fa-exchange-alt"></i>
                  <p>
                    Транзакции
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/services" class="nav-link">
                  <i class="nav-icon fas fa-briefcase"></i>
                  <p>
                    Услуги
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/reports" class="nav-link" id="reports-menu-item">
                  <i class="nav-icon fas fa-copy"></i>
                  <p>
                    Отчеты
                    <span class="notification-badge-container">
                      <span class="notification-badge badge-success" id="reports-notification-approved" style="display: none;"></span>
                      <span class="notification-badge badge-warning" id="reports-notification-pending" style="display: none;"></span>
                      <span class="notification-badge badge-danger" id="reports-notification-rejected" style="display: none;"></span>
                    </span>
                  </p>
                </a>
              </li>
              <?php if ($user['acquiring'] === '1') : ?>
              <li class="nav-item">
                <a href="/pays" class="nav-link">
                  <i class="nav-icon fas fa-wallet"></i>
                  <p>
                    Эквайринг
                  </p>
                </a>
              </li>
              <?php endif; ?>
              <li class="nav-item">
                <a href="/analytics" class="nav-link">
                  <i class="nav-icon fas fa-chart-line"></i>
                  <p>
                    Аналитика
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/flightload" class="nav-link">
                  <i class="nav-icon fas fa-plane"></i>
                  <p>
                    Загрузка рейса
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/passengers" class="nav-link">
                  <i class="nav-icon fas fa-user-friends"></i>
                  <p>
                    Пассажиры
                  </p>
                    <!-- <span class="right badge badge-danger">New</span> -->
                </a>
              </li>
              <?php if ($user['role'] === 'superadmin') : ?>
              <li class="nav-item">
                <a href="/currencies" class="nav-link">
                  <i class="nav-icon fa fa-coins"></i>
                  <p>
                    Курс валюты
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-ticket-alt"></i>
                  <p>
                  Билеты
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="/ticket" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Билет</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="/emd" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>EMD</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="/fops" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Фопс</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="/segments" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Сегменты</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="/taxes" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Сборы</p>
                    </a>
                  </li>
                </ul>
              </li>
              <?php endif; ?>
              <?php if ($user['role'] === 'superadmin') : ?> 
              <li class="nav-item">
                <a href="/logs" class="nav-link">
                  <i class="nav-icon fas fa-file-alt"></i>
                  <p>
                    Логи
                  </p>
                </a>
              </li>
              <?php endif; ?>
              <li class="nav-item">
                <a target="_blank" href="https://avs.tj/technology.html" class="nav-link">
                  <i class="nav-icon fas fa-file-alt"></i>
                  <p>
                    Инструкция
                  </p>
                </a>
              </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>

    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
  

<!-- Модальное окно для ожидания загрузки таблицы -->
<div class="modal" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="loadingModalLabel">Загрузка</h5>
      </div>
      <div class="modal-body">
        <div class="text-center">
          <div class="spinner-border" role="status">
            <span class="sr-only">Загрузка...</span>
          </div>
          <p class="mt-2">Загрузка данных, пожалуйста, подождите...</p>
        </div>
      </div>
    </div>
  </div>
</div>

    <?=$this->renderSection('content')?>

  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>Авторское право принадлежит <a href="https://avs.tj">ОАО "АВС"</a> &copy; 2025 </strong>
    <div class="float-right d-none d-sm-inline-block">
      <a href="https://t.me/karimovnasimjon"> Developed by <b>KNN</b></a> <i>(ver.2.2) </i>
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->


<!-- Сценарий JavaScript -->
<script type="text/javascript">
  
  var colum_name = "<?php echo $colum_name; ?>";
  var myVar = "<?php echo $ids; ?>";
  var role = "<?php echo $role; ?>";
  var user_desc = "<?php echo $user_desc; ?>";

  var start_date = "<?php echo $start_date; ?>";
  var end_date = "<?php echo $end_date; ?>";
  var decade_start_date = "<?php echo $decade_start_date; ?>";
  var decade_end_date = "<?php echo $decade_end_date; ?>";
  var active_filter = "<?php echo $filter; ?>";

  var today = "<?php echo $dates['today']; ?>";
  var yesterday = "<?php echo $dates['yesterday']; ?>";
  var thisMonthFirst = "<?php echo $dates['thisMonthFirst']; ?>";
  var thisMonthLast = "<?php echo $dates['thisMonthLast']; ?>";
  var lastMonthFirst = "<?php echo $dates['lastMonthFirst']; ?>";
  var lastMonthLast = "<?php echo $dates['lastMonthLast']; ?>";
  var thisDecadeFirst = "<?php echo $dates['thisDecadeFirst']; ?>";
  var thisDecadeLast = "<?php echo $dates['thisDecadeLast']; ?>";
  var firstDecadeStart = "<?php echo $dates['firstDecadeStart']; ?>";
  var firstDecadeEnd = "<?php echo $dates['firstDecadeEnd']; ?>";
  var secondDecadeStart = "<?php echo $dates['secondDecadeStart']; ?>";
  var secondDecadeEnd = "<?php echo $dates['secondDecadeEnd']; ?>";
  var thirdDecadeStart = "<?php echo $dates['thirdDecadeStart']; ?>";
  var thirdDecadeEnd = "<?php echo $dates['thirdDecadeEnd']; ?>";

  var uniqueTaxCodes = <?php echo json_encode($uniqueTaxCodes); ?>;

  var colum_name = "<?php echo $colum_name; ?>";
  
  var is_acquiring = "<?php echo $user['acquiring']; ?>";
  var is_airline = "<?php echo $user['is_airline']; ?>";


</script>


<!-- jQuery -->
<!-- <script src="<= base_url(); ?>plugins/jquery/jquery.min.js"></script> -->


<!-- Подключение jQuery -->
<!-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>


<!-- jQuery UI 1.11.4 -->
<script src="<?= base_url(); ?>plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="<?= base_url(); ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="<?= base_url(); ?>plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="<?= base_url(); ?>plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="<?= base_url(); ?>plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="<?= base_url(); ?>plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="<?= base_url(); ?>plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="<?= base_url(); ?>plugins/moment/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/locale/ru.js"></script>
<script src="<?= base_url(); ?>plugins/daterangepicker/daterangepicker.js"></script>
<!-- Toastr -->
<script src="<?= base_url(); ?>plugins/toastr/toastr.min.js"></script>
<!-- CodeMirror -->
<script src="<?= base_url(); ?>plugins/codemirror/codemirror.js"></script>
<script src="<?= base_url(); ?>plugins/codemirror/autorefresh.js"></script>
<script src="<?= base_url(); ?>plugins/codemirror/mode/css/css.js"></script>
<script src="<?= base_url(); ?>plugins/codemirror/mode/xml/xml.js"></script>
<script src="<?= base_url(); ?>plugins/codemirror/mode/htmlmixed/htmlmixed.js"></script>

<!-- Tempusdominus Bootstrap 4 -->
<script src="<?= base_url(); ?>plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="<?= base_url(); ?>plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="<?= base_url(); ?>plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url(); ?>dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?= base_url(); ?>dist/js/demo.js"></script>

<!-- Скрипт для уведомлений -->
<script>
  $(document).ready(function() {
    // Функция для обновления уведомлений
    function updateNotifications() {
      $.ajax({
        url: '/reports/notifications',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
          const approvedBadge = $('#reports-notification-approved');
          const pendingBadge = $('#reports-notification-pending');
          const rejectedBadge = $('#reports-notification-rejected');
          
          if (response.hasNotification && response.notification) {
            const notification = response.notification;

            // Approved
            if (notification.approved > 0) {
                approvedBadge.text(notification.approved).show();
            } else {
                approvedBadge.hide();
            }

            // Pending
            if (notification.pending > 0) {
                pendingBadge.text(notification.pending).show();
            } else {
                pendingBadge.hide();
            }

            // Rejected
            if (notification.rejected > 0) {
                rejectedBadge.text(notification.rejected).show();
            } else {
                rejectedBadge.hide();
            }

          } else {
            // Скрываем уведомление, если его нет
            approvedBadge.hide();
            pendingBadge.hide();
            rejectedBadge.hide();
          }
        },
        error: function(xhr, status, error) {
          console.error('Ошибка при получении уведомлений:', error);
        }
      });
    }

    // Обновляем уведомления при загрузке страницы
    updateNotifications();

    // Обновляем уведомления каждые 30 секунд
    setInterval(updateNotifications, 30000);

    // Обновляем уведомления при фокусе на окне (когда пользователь возвращается на вкладку)
    $(window).on('focus', function() {
      updateNotifications();
    });

    // Функция для обновления уведомления из ответа AJAX
    function updateNotificationFromResponse(response) {
        if (response.hasNotification && response.notification) {
            const notification = response.notification;
            const approvedBadge = $('#reports-notification-approved');
            const pendingBadge = $('#reports-notification-pending');
            const rejectedBadge = $('#reports-notification-rejected');

            // Approved
            if (notification.approved > 0) {
                approvedBadge.text(notification.approved).show();
            } else {
                approvedBadge.hide();
            }

            // Pending
            if (notification.pending > 0) {
                pendingBadge.text(notification.pending).show();
            } else {
                pendingBadge.hide();
            }

            // Rejected
            if (notification.rejected > 0) {
                rejectedBadge.text(notification.rejected).show();
            } else {
                rejectedBadge.hide();
            }
        } else {
            $('#reports-notification-approved').hide();
            $('#reports-notification-pending').hide();
            $('#reports-notification-rejected').hide();
        }
    }

    // Глобальная функция для обновления уведомлений (доступна из других скриптов)
    window.updateNotificationFromResponse = updateNotificationFromResponse;
  });
</script>


<!-- DataTables  & Plugins -->

<!-- Select2 -->
<script src="<?= base_url(); ?>plugins/select2/js/select2.full.min.js"></script>


<!-- DATA_TABLE_EDITOR -->
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/searchbuilder/1.6.0/js/dataTables.searchBuilder.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/datetime/1.5.1/js/dataTables.dateTime.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>

<!-- colReorder -->
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/colreorder/1.7.0/js/dataTables.colReorder.min.js"></script>
<script type="text/javascript" language="javascript" src="<?= base_url(); ?>js/dataTables.editor.min.js"></script>


<!-- Data time конструктор поиска  -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>

<link href="https://nightly.datatables.net/datetime/css/dataTables.dateTime.css?_=51ef39aba7fb7ecb2ccd3a91f1dccc6e.css" rel="stylesheet" type="text/css" />
<script src="https://nightly.datatables.net/datetime/js/dataTables.dateTime.js?_=51ef39aba7fb7ecb2ccd3a91f1dccc6e"></script>
<link href="https://nightly.datatables.net/searchbuilder/css/searchBuilder.dataTables.css?_=40f0e1a3ea332af586366e40955c1713.css" rel="stylesheet" type="text/css" />
<script src="https://nightly.datatables.net/searchbuilder/js/dataTables.searchBuilder.js?_=40f0e1a3ea332af586366e40955c1713"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.19/sorting/datetime-moment.js"></script> -->


<!-- custom  -->
<script type="text/javascript" language="javascript" src="<?= base_url(); ?><?= latest('js/datatables/dashboard.js'); ?>"></script>
<script type="text/javascript" language="javascript" src="<?= base_url(); ?><?= latest('js/datatables/operations.js'); ?>"></script>
<script type="text/javascript" language="javascript" src="<?= base_url(); ?><?= latest('js/datatables/tickets.js'); ?>"></script>
<script type="text/javascript" language="javascript" src="<?= base_url(); ?><?= latest('js/datatables/emd.js'); ?>"></script>
<script type="text/javascript" language="javascript" src="<?= base_url(); ?><?= latest('js/datatables/fops.js'); ?>"></script>
<script type="text/javascript" language="javascript" src="<?= base_url(); ?><?= latest('js/datatables/segments.js'); ?>"></script>
<script type="text/javascript" language="javascript" src="<?= base_url(); ?><?= latest('js/datatables/taxes.js'); ?>"></script>
<script type="text/javascript" language="javascript" src="<?= base_url(); ?><?= latest('js/datatables/passengers.js'); ?>"></script>
<script type="text/javascript" language="javascript" src="<?= base_url(); ?><?= latest('js/datatables/default.js'); ?>"></script>
<script type="text/javascript" language="javascript" src="<?= base_url(); ?><?= latest('js/datatables/downtable.js'); ?>"></script>
<script type="text/javascript" language="javascript" src="<?= base_url(); ?><?= latest('js/datatables/transactions.js'); ?>"></script>
<script type="text/javascript" language="javascript" src="<?= base_url(); ?><?= latest('js/datatables/analytics.js'); ?>"></script>
<script type="text/javascript" language="javascript" src="<?= base_url(); ?><?= latest('js/datatables/logs.js'); ?>"></script>
<script type="text/javascript" language="javascript" src="<?= base_url(); ?><?= latest('js/datatables/flightload.js'); ?>"></script>
<script type="text/javascript" language="javascript" src="<?= base_url(); ?><?= latest('js/datatables/organization.js'); ?>"></script>
<script type="text/javascript" language="javascript" src="<?= base_url(); ?><?= latest('js/datatables/reports.js'); ?>"></script>
<script type="text/javascript" language="javascript" src="<?= base_url(); ?><?= latest('js/custom/pays.js'); ?>"></script>
<script type="text/javascript" language="javascript" src="<?= base_url(); ?><?= latest('js/datatables/services.js'); ?>"></script>

<!-- bs-custom-file-input -->
<script src="<?= base_url(); ?>plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>


<script>
  $(function () {
    bsCustomFileInput.init();
  });
</script>


<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })

  })
</script>


<!-- Дата -->
<script>
  $(document).ready(function(){

    //Timepicker
    $('#timepicker').datetimepicker({
      format: 'LT'
    })


    $('#startDatePicker').datetimepicker({
        locale: moment.locale('ru'),
        format: 'YYYY-MM-DD',

    });
    $('#endDatePicker').datetimepicker({
        locale: moment.locale('ru'),
        format: 'YYYY-MM-DD',

    });
    $('#flydatePicker').datetimepicker({
        locale: moment.locale('ru'),
        format: 'YYYY-MM-DD',

    });
  });
</script>

<!-- Уведомления -->
<script>
  $(function() {

    // Toastr
    <?php if (session()->has('success')): ?>
        toastr.success('<?= session()->get('success') ?>');
    <?php endif; ?>
    
    <?php if (session()->has('error')): ?>
        toastr.error('<?= session()->get('error') ?>');
    <?php endif; ?>
  });
</script>
<script>
    function confirmDelete() {
        return confirm('Вы уверены, что хотите удалить?');
    }
</script>
</body>
</html>