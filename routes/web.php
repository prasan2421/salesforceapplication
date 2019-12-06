<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['register' => false]);

Route::get('/', 'HomeController@index')->name('home');

Route::get('admins/data', 'AdminController@getData');

// Route::get('admins/remove-route-relationships', 'AdminController@removeRouteRelationships');

Route::get('sales-officers/data', 'SalesOfficerController@getData');

Route::get('sales-officers/export-csv', 'SalesOfficerController@exportCsv');

Route::get('sales-officers/search', 'SalesOfficerController@search');

Route::get('sales-officers/routes-data', 'SalesOfficerController@getRoutesData');

// Route::get('sales-officers/{id}/routes', 'SalesOfficerController@routes');

// Route::post('sales-officers/{id}/routes', 'SalesOfficerController@saveRoutes');

// Route::get('sales-officers/update-username', 'SalesOfficerController@updateUsername');

// Route::get('sales-officers/assign-routes', 'SalesOfficerController@assignRoutes');

Route::get('sales-officers/export-without-verticals-csv', 'SalesOfficerController@exportWithoutVerticalsCsv');

Route::get('sales-officers/export-without-division-csv', 'SalesOfficerController@exportWithoutDivisionCsv');

Route::get('sales-officers/{user_id}/routes/data', 'SalesOfficerRouteController@getData');

Route::get('sales-officers/{user_id}/routes/{id}/mark-active', 'SalesOfficerRouteController@markActive');

Route::get('sales-officers/{user_id}/routes/{id}/mark-inactive', 'SalesOfficerRouteController@markInactive');

Route::get('dsms/data', 'DsmController@getData');

// Route::get('dsms/export-excel', 'DsmController@exportExcel');

// Route::get('dsms/export-attendances-excel', 'DsmController@exportAttendancesExcel');

Route::get('dsms/export-csv', 'DsmController@exportCsv');

Route::get('dsms/export-with-beats-csv', 'DsmController@exportWithBeatsCsv');

Route::get('dsms/export-attendances-csv', 'DsmController@exportAttendancesCsv');

Route::post('dsms/export-attendances-csv', 'DsmController@submitExportAttendancesCsv');

Route::get('dsms/export-customer-visits-csv', 'DsmController@exportCustomerVisitsCsv');

Route::post('dsms/export-customer-visits-csv', 'DsmController@submitExportCustomerVisitsCsv');

Route::get('dsms/{id}/map', 'DsmController@map');

Route::get('dsms/{id}/attendances', 'DsmController@attendances');

Route::get('dsms/{id}/mark-active', 'DsmController@markActive');

Route::get('dsms/{id}/mark-inactive', 'DsmController@markInactive');

Route::get('dsms/generate-demo-dsms', 'DsmController@generateDemoDsms');

// Route::get('dsms/assign-food-verticals', 'DsmController@assignFoodVerticals');

// Route::get('dsms/update-username', 'DsmController@updateUsername');

// Route::get('dsms/remove-empty-route-users', 'DsmController@removeEmptyRouteUsers');

Route::get('dsms/export-dsm-states-csv', 'DsmController@exportDsmStatesCsv');

Route::get('dsms/{user_id}/routes/data', 'DsmRouteController@getData');

Route::get('dsms/{user_id}/routes/{id}/mark-active', 'DsmRouteController@markActive');

Route::get('dsms/{user_id}/routes/{id}/mark-inactive', 'DsmRouteController@markInactive');

Route::get('distributors/data', 'DistributorController@getData');

Route::get('distributors/export-csv', 'DistributorController@exportCsv');

Route::get('distributors/export-duplicates-csv', 'DistributorController@exportDuplicatesCsv');

Route::get('distributors/search', 'DistributorController@search');

Route::get('distributors/map-routes', 'DistributorController@mapRoutes');

Route::get('distributors/map-verticals', 'DistributorController@mapVerticals');

Route::get('states/data', 'StateController@getData');

Route::get('routes/data', 'RouteController@getData');

// Route::get('routes/export-excel', 'RouteController@exportExcel');

Route::get('routes/export-csv', 'RouteController@exportCsv');

Route::get('routes/search', 'RouteController@search');

// Route::get('routes/generate-sap-code', 'RouteController@generateSapCode');

// Route::get('routes/set-counter', 'RouteController@setCounter');

// Route::get('routes/remove-routes', 'RouteController@removeRoutes');

// Route::get('routes/remove-routes-without-customers', 'RouteController@removeRoutesWithoutCustomers');

// Route::get('routes/remove-empty-route-users', 'RouteController@removeEmptyRouteUsers');

Route::get('routes/export-unassigned-csv', 'RouteController@exportUnassignedToCsv');

Route::get('routes/export-multiple-distributors-csv', 'RouteController@exportMultipleDistributorsCsv');

Route::get('routes/export-route-users-csv', 'RouteController@exportRouteUsersCsv');

Route::get('users/data', 'UserController@getData');

Route::get('users/{id}/map', 'UserController@map');

Route::get('categories/data', 'CategoryController@getData');

Route::get('divisions/data', 'DivisionController@getData');

Route::get('verticals/data', 'VerticalController@getData');

// Route::get('verticals/remove-relationships', 'VerticalController@removeRelationships');

// Step 1
// Route::get('verticals/remove-distributors', 'VerticalController@removeDistributors');

// Route::get('verticals/remove-distributors-2', 'VerticalController@removeDistributors2');

// Route::get('verticals/export-routes-to-remove-csv', 'VerticalController@exportRoutesToRemoveCsv');

Route::get('verticals/export-routes-to-remove-csv-2', 'VerticalController@exportRoutesToRemoveCsv2');

Route::get('verticals/export-routes-to-remove-csv-3', 'VerticalController@exportRoutesToRemoveCsv3');

// Route::get('verticals/export-routes-to-remove-csv-4', 'VerticalController@exportRoutesToRemoveCsv4');

// Route::get('verticals/export-distributors-to-remove-csv', 'VerticalController@exportDistributorsToRemoveCsv');

// Route::get('verticals/export-dsms-to-remove-csv', 'VerticalController@exportDsmsToRemoveCsv');

// Route::get('verticals/export-dsms-to-remove-csv-2', 'VerticalController@exportDsmsToRemoveCsv2');

// Route::get('verticals/export-dsms-to-remove-csv-3', 'VerticalController@exportDsmsToRemoveCsv3');

// Route::get('verticals/export-so-to-remove-csv', 'VerticalController@exportSoToRemoveCsv');

// Step 2
// Route::get('verticals/remove-customer-visits', 'VerticalController@removeCustomerVisits');

// Step 3
// Route::get('verticals/remove-customers', 'VerticalController@removeCustomers');

// Step 4
// Route::get('verticals/remove-routes', 'VerticalController@removeRoutes');

// Step 5
// Route::get('verticals/remove-users-data', 'VerticalController@removeUsersData');

// Step 6
// Route::get('verticals/remove-users', 'VerticalController@removeUsers');

// Route::get('verticals/remove-so', 'VerticalController@removeSo');

Route::get('brands/data', 'BrandController@getData');

Route::get('units/data', 'UnitController@getData');

Route::get('products/data', 'ProductController@getData');

Route::get('products/import', 'ProductController@import');

Route::post('products/import', 'ProductController@saveImport');

Route::get('products/update-prices', 'ProductController@updatePrices');

Route::get('products/export-zero-price-csv', 'ProductController@exportZeroPriceCsv');

Route::get('schemes/data', 'SchemeController@getData');

Route::get('locations/data', 'LocationController@getData');

Route::get('customer-types/data', 'CustomerTypeController@getData');

Route::get('customer-classes/data', 'CustomerClassController@getData');

Route::get('customer-categories/data', 'CustomerCategoryController@getData');

Route::get('customers/data', 'CustomerController@getData');

// Route::get('customers/export-excel', 'CustomerController@exportExcel');

Route::get('customers/export-csv', 'CustomerController@exportCsv');

// Route::get('customers/set-customer-class-id', 'CustomerController@setCustomerClassId');

// Route::get('customers/set-state-id', 'CustomerController@setStateId');

// Route::get('customers/generate-sap-code', 'CustomerController@generateSapCode');

// Route::get('customers/remove-customers-without-route', 'CustomerController@removeCustomersWithoutRoute');

Route::get('customers/export-unassigned-csv', 'CustomerController@exportUnassignedToCsv');

Route::get('customers/export-without-state-csv', 'CustomerController@exportWithoutStateCsv');

Route::get('customers/export-without-route-csv', 'CustomerController@exportWithoutRouteCsv');

Route::get('customers/export-backup-csv', 'CustomerController@exportBackupCsv');

Route::get('customers/export-mobile-numbers-csv', 'CustomerController@exportMobileNumbersCsv');

Route::get('customers/export-wrong-division-beats-csv', 'CustomerController@exportWrongDivisionBeatsCsv');

Route::get('customers/export-wrong-state-beats-csv', 'CustomerController@exportWrongStateBeatsCsv');

Route::get('orders/data', 'OrderController@getData');

// Route::get('orders/export-excel', 'OrderController@exportExcel');

// Route::post('orders/export-excel', 'OrderController@submitExportExcel');

// Route::get('orders/export-summary-excel', 'OrderController@exportSummaryExcel');

// Route::post('orders/export-summary-excel', 'OrderController@submitExportSummaryExcel');

Route::get('orders/export-csv', 'OrderController@exportCsv');

Route::post('orders/export-csv', 'OrderController@submitExportCsv');

Route::get('orders/export-summary-csv', 'OrderController@exportSummaryCsv');

Route::post('orders/export-summary-csv', 'OrderController@submitExportSummaryCsv');

// Route::get('orders/set-pos-notified-status', 'OrderController@setPosNotifiedStatus');

Route::get('imports/data', 'ImportController@getData');

Route::get('error-logs/data', 'ErrorLogController@getData');

Route::get('task-logs/data', 'TaskLogController@getData');

Route::get('import-excel', 'ImportExcelController@index');

Route::post('import-excel', 'ImportExcelController@store');

Route::get('my-routes/data', 'MyRouteController@getData');

Route::get('my-routes/{id}/mark-active', 'MyRouteController@markActive');

Route::get('my-routes/{id}/mark-inactive', 'MyRouteController@markInactive');

Route::resource('admins', 'AdminController');

Route::resource('sales-officers', 'SalesOfficerController');

Route::resource('sales-officers/{user_id}/routes', 'SalesOfficerRouteController');

Route::resource('dsms', 'DsmController');

Route::resource('dsms/{user_id}/routes', 'DsmRouteController');

Route::resource('distributors', 'DistributorController');

Route::resource('states', 'StateController');

Route::resource('routes', 'RouteController');

Route::resource('users', 'UserController');

Route::resource('categories', 'CategoryController');

Route::resource('divisions', 'DivisionController');

Route::resource('verticals', 'VerticalController');

Route::resource('brands', 'BrandController');

Route::resource('units', 'UnitController');

Route::resource('products', 'ProductController');

Route::resource('schemes', 'SchemeController');

Route::resource('locations', 'LocationController');

Route::resource('customer-types', 'CustomerTypeController');

Route::resource('customer-classes', 'CustomerClassController');

Route::resource('customer-categories', 'CustomerCategoryController');

Route::resource('customers', 'CustomerController');

Route::resource('orders', 'OrderController');

Route::resource('imports', 'ImportController');

Route::resource('error-logs', 'ErrorLogController');

Route::resource('task-logs', 'TaskLogController');

Route::resource('my-routes', 'MyRouteController')->except([ 'destroy' ]);
