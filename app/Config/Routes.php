<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Dashboard');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

// Перенаправление с корневого пути на /dashboard
$routes->get('/', function () {
    return redirect()->to('/dashboard');
});

$routes->get('/login', 'AuthController::showLoginForm');
$routes->post('/login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');

$routes->get('/dashboard', 'Dashboard::index');
$routes->post('/dashboard', 'Dashboard::index');

$routes->get('/users', 'UsersController::index');
$routes->get('/users/create', 'UsersController::create_user');
$routes->post('/users/register', 'UsersController::register');
$routes->get('/users/edit/(:num)', 'UsersController::edit/$1');
$routes->post('/users/update/(:num)', 'UsersController::update/$1');
$routes->post('/users/update_tables_states', 'UsersController::update_tables_states');
$routes->post('/users/get_tables_states', 'UsersController::get_tables_states');
$routes->get('/users/delete/(:num)', 'UsersController::delete/$1');
$routes->post('/users/update_colreorder', 'UsersController::update_colreorder');
$routes->post('/users/get_colreorder', 'UsersController::get_colreorder');


$routes->get('/agency', 'AgencyController::index');
$routes->get('/agency/create', 'AgencyController::create');
$routes->post('/agency/register', 'AgencyController::register');
$routes->get('/agency/edit/(:num)', 'AgencyController::edit/$1');
$routes->post('/agency/update/(:num)', 'AgencyController::update/$1');
$routes->get('/agency/delete/(:num)', 'AgencyController::delete/$1');
$routes->post('/agency/reg_reward', 'AgencyController::reg_reward');
$routes->get('/agency/get_reward', 'AgencyController::get_reward');
$routes->post('/agency/get_data_table', 'AgencyController::getDataTable');
$routes->post('/agency/getBalances', 'AgencyController::getBalances');


$routes->get('/stamp', 'StampController::index');
$routes->get('/stamp/create', 'StampController::create');
$routes->post('/stamp/register', 'StampController::register');
$routes->get('/stamp/edit/(:num)', 'StampController::edit/$1');
$routes->post('/stamp/update/(:num)', 'StampController::update/$1');
$routes->get('/stamp/delete/(:num)', 'StampController::delete/$1');
$routes->post('/stamp/reg_reward', 'StampController::reg_reward');


$routes->get('/tap', 'TapController::index');
$routes->get('/tap/create', 'TapController::create');
$routes->post('/tap/register', 'TapController::register');
$routes->get('/tap/edit/(:num)', 'TapController::edit/$1');
$routes->post('/tap/update/(:num)', 'TapController::update/$1');
$routes->get('/tap/delete/(:num)', 'TapController::delete/$1');
$routes->post('/tap/reg_reward', 'TapController::reg_reward');


$routes->get('/opr', 'OprController::index');
$routes->get('/opr/create', 'OprController::create');
$routes->post('/opr/register', 'OprController::register');
$routes->get('/opr/edit/(:num)', 'OprController::edit/$1');
$routes->post('/opr/update/(:num)', 'OprController::update/$1');
$routes->get('/opr/delete/(:num)', 'OprController::delete/$1');
$routes->post('/opr/reg_reward', 'OprController::reg_reward');



$routes->get('/operations', 'OperationsController::index');
$routes->post('/operations/get_active_params', 'OperationsController::get_active_params');


$routes->get('/ticket', 'Tickets::ticket');
$routes->get('/emd', 'Tickets::emd');
$routes->get('/fops', 'Tickets::fops');
$routes->get('/segments', 'Tickets::segments');
$routes->get('/taxes', 'Tickets::taxes');
$routes->post('/taxes/get-columns', 'Tickets::getUniqueColumns');
$routes->post('/taxes/add-column', 'Tickets::addColumnIfNotExists');
$routes->post('/taxes/delete-column', 'Tickets::deleteColumnIfExists');

$routes->get('/passengers', 'Passengers::index');


$routes->get('/profile', 'Profile::index');
$routes->post('/profile/update', 'Profile::update');
$routes->get('/profile/four_params_json', 'Profile::four_params_json');
$routes->post('/profile/updateSession', 'Profile::updateSession');
$routes->post('/profile/getSessionValue', 'Profile::getSessionValue2');


$routes->get('/payments', 'Payments::index');
$routes->get('/payments/dcb', 'Payments::dcb');
$routes->post('/payments/dcb', 'Payments::dcb');
$routes->get('/payments/dcb_success', 'Payments::dcb_success');
$routes->post('/payments/dcb_success', 'Payments::dcb_success');
$routes->get('/payments/dcb_error', 'Payments::dcb_error');
$routes->post('/payments/dcb_error', 'Payments::dcb_error');


$routes->post('/calculateSummary', 'BigExportController::calculateSummary');
$routes->post('/downTable', 'BigExportController::downTable3');
$routes->post('/settings', 'BigExportController::settings');
$routes->post('/bigexport', 'BigExportController::exportData');
$routes->post('/summaryTable', 'BigExportController::summaryTable');
$routes->get('/download/(:any)', 'BigExportController::download/$1');
$routes->get('/cancelExport', 'BigExportController::cancelExport');
$routes->post('/allexport', 'BigExportController::allExport');


$routes->get('/transactions', 'Transactions::index');
$routes->post('/transactions', 'Transactions::index');
$routes->get('/transactions/create', 'Transactions::create');
$routes->post('/transactions/register', 'Transactions::register');
$routes->post('/transactions/update/(:num)', 'Transactions::update/$1');
$routes->get('/transactions/delete/(:num)', 'Transactions::delete/$1');
$routes->get('/transactions/edit/(:num)', 'Transactions::edit/$1');
$routes->get('/gettable', 'Transactions::gettable_json');
$routes->get('/transactions/export', 'Transactions::export');
$routes->post('/transactions/get_data_trans', 'Transactions::get_data_trans');



$routes->get('/analytics', 'AnalyticsController::index');
$routes->post('/analytics', 'AnalyticsController::index');
$routes->post('/analytics/report', 'AnalyticsController::report');
$routes->post('/analytics/getdatatable', 'AnalyticsController::getDataTable');


$routes->get('/currencies', 'CurrenciesController::index');
$routes->get('/currencies/create', 'CurrenciesController::create');
$routes->post('/currencies/register', 'CurrenciesController::register');
$routes->post('/currencies/update/(:num)', 'CurrenciesController::update/$1');
$routes->get('/currencies/delete/(:num)', 'CurrenciesController::delete/$1');
$routes->get('/currencies/edit/(:num)', 'CurrenciesController::edit/$1');


$routes->get('/logs', 'LogsController::index');
$routes->get('/logs/getLog/(:segment)', 'LogsController::getLog/$1');
$routes->post('logs/executeCommand', 'LogsController::executeCommand');
$routes->post('logs/getData', 'LogsController::getData');
$routes->post('logs/getById', 'LogsController::getById');



$routes->get('/flightload', 'FlightLoadController::index');
$routes->post('/flightload/getdata', 'FlightLoadController::getData');
$routes->post('/flightload/fetchdata', 'FlightLoadController::fetchData');
$routes->post('/flightload/popularflights', 'FlightLoadController::popularFlights');


$routes->get('/taxestest', 'TaxesTestController::index');
$routes->get('/taxescontroller/getTaxesData', 'TaxesTestController::getTaxesData');


$routes->get('/pays', 'PaysController::index');
$routes->post('/pays/fetchdata', 'PaysController::fetchdata');
$routes->get('/pays/export', 'PaysController::export');
$routes->post('/pays/downtable', 'PaysController::downtable');
$routes->post('/pays/get_name_acq_json', 'PaysController::get_name_acq_json');


$routes->get('/reports', 'ReportsController::index');
$routes->post('/reports/sendreport', 'ReportsController::sendreport');
$routes->post('/reports/get_reports', 'ReportsController::get_reports');
$routes->post('/reports/show_report', 'ReportsController::show_report');
$routes->post('/reports/updateStatus', 'ReportsController::updateStatus');
$routes->post('/reports/delete', 'ReportsController::deleteReport');
$routes->post('/reports/create_qrcode', 'ReportsController::create_qrcode');



$routes->get('/services', 'ServicesController::index');
$routes->post('/services/get_services', 'ServicesController::get_services');
$routes->post('/services/get_downtable', 'ServicesController::get_downtable');
$routes->get('/services/create', 'ServicesController::create');
$routes->post('/services/register', 'ServicesController::register');
$routes->post('/services/update/(:num)', 'ServicesController::update/$1');
$routes->get('/services/delete/(:num)', 'ServicesController::delete/$1');
$routes->get('/services/edit/(:num)', 'ServicesController::edit/$1');



$routes->get('/share', 'ShareController::index');
$routes->post('/share/getDataTable', 'ShareController::getDataTable'); // Для DataTables
$routes->get('/share/create', 'ShareController::create');
$routes->post('/share/register', 'ShareController::register');
$routes->get('/share/edit/(:num)', 'ShareController::edit/$1');
$routes->post('/share/update/(:num)', 'ShareController::update/$1');
$routes->get('/share/delete/(:num)', 'ShareController::delete/$1'); // Или POST, если используете форму для удаления
$routes->post('/share/reg_reward', 'ShareController::reg_reward');




/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
