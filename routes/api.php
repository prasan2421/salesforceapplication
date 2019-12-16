<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('users/login', 'Api\UserController@login');

Route::post('users/verify-login', 'Api\UserController@verifyLogin');

Route::get('users/profile', 'Api\UserController@getProfile');

Route::post('users/profile', 'Api\UserController@updateProfile');

Route::post('users/change-password', 'Api\UserController@changePassword');

Route::get('customer-types', 'Api\CustomerTypeController@getCustomerTypes');

Route::get('customer-classes', 'Api\CustomerClassController@getCustomerClasses');

Route::get('customers', 'Api\CustomerController@getCustomers');

Route::get('customers/today', 'Api\CustomerController@getTodayCustomers');

Route::get('customers/scheduled/{date}', 'Api\CustomerController@getScheduledCustomers');

Route::get('customers/schedule-summary/{date}', 'Api\CustomerController@getScheduleSummary');

Route::get('customers/monthly-summary', 'Api\CustomerController@getMonthlySummary');

Route::get('customers/{id}', 'Api\CustomerController@getCustomerDetails');

Route::post('customers', 'Api\CustomerController@addCustomer');

Route::put('customers/{id}', 'Api\CustomerController@updateCustomer');

Route::get('locations', 'Api\LocationController@getLocations');

Route::get('locations/{id}', 'Api\LocationController@getLocationDetails');

Route::get('categories/{id?}', 'Api\CategoryController@getCategories');

Route::get('verticals', 'Api\VerticalController@getVerticals');

Route::get('verticals/{vertical_id}/brands', 'Api\BrandController@getBrands');

Route::get('brands/{brand_id}/products', 'Api\ProductController@getProducts');

Route::get('brands/{brand_id}/scheme-products', 'Api\ProductController@getSchemeProducts');

Route::get('brands/{brand_id}/schemes', 'Api\SchemeController@getSchemes');

Route::get('products/{id}', 'Api\ProductController@getProductDetails');

Route::get('routes', 'Api\RouteController@getRoutes');

Route::get('routes/today', 'Api\RouteController@getTodayRoutes');

Route::get('routes/{id}', 'Api\RouteController@getRouteDetails');

Route::get('schedules', 'Api\ScheduleController@getSchedules');

Route::get('schedules/{id}', 'Api\ScheduleController@getScheduleDetails');

Route::post('schedules', 'Api\ScheduleController@addSchedule');

Route::put('schedules/{id}', 'Api\ScheduleController@updateSchedule');

Route::delete('schedules/{id}', 'Api\ScheduleController@deleteSchedule');

Route::get('orders', 'Api\OrderController@getOrders');

Route::get('orders/{id}', 'Api\OrderController@getOrderDetails');

Route::post('orders', 'Api\OrderController@addOrder');

Route::put('orders/{id}', 'Api\OrderController@updateOrder');

Route::delete('orders/{id}', 'Api\OrderController@deleteOrder');

Route::get('orders/{id}/download-excel', 'Api\OrderController@downloadExcel');

Route::get('orders/{id}/download-pdf', 'Api\OrderController@downloadPdf');

Route::get('orders/{id}/send-email-excel', 'Api\OrderController@sendEmailExcel');

Route::get('orders/{id}/send-email-pdf', 'Api\OrderController@sendEmailPdf');

Route::get('invoices', 'Api\InvoiceController@getInvoices');

Route::get('invoices/{id}', 'Api\InvoiceController@getInvoiceDetails');

Route::post('invoices', 'Api\InvoiceController@addInvoice');

Route::get('attendances/punch-status', 'Api\AttendanceController@getPunchStatus');

Route::post('attendances/punch-in', 'Api\AttendanceController@punchIn');

Route::post('attendances/punch-out', 'Api\AttendanceController@punchOut');

Route::get('attendances/{date}', 'Api\AttendanceController@getAttendances');

Route::get('customer-visits/status', 'Api\CustomerVisitController@getStatus');

Route::post('customer-visits/check-in', 'Api\CustomerVisitController@checkIn');

Route::post('customer-visits/check-out', 'Api\CustomerVisitController@checkOut');

Route::get('customer-visits/{date}', 'Api\CustomerVisitController@getCustomerVisits');

Route::get('geolocations/{date}', 'Api\GeolocationController@getGeolocations');

Route::post('geolocations', 'Api\GeolocationController@addGeolocation');

Route::get('performances/total-achievements', 'Api\PerformanceController@getTotalAchievements');

Route::post('feedbacks', 'Api\FeedbackController@addFeedback');

Route::post('remarks', 'Api\RemarksController@addRemarks');

Route::get('states', 'Api\StateController@getStates');

Route::post('error-logs', 'Api\ErrorLogController@addErrorLog');

Route::get('pos/order-details/{id}', 'Api\PosController@getOrderDetails');

Route::get('pos/mark-order-synced/{id}', 'Api\PosController@markOrderSynced');

Route::get('pos/retailers/{db_code}', 'Api\PosController@getRetailers');
