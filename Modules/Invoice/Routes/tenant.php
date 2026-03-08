<?php

use Illuminate\Support\Facades\Route;

Route::prefix('invoice-admin')->group(function () {


});
Route::group(['as'=>'invoice.', 'prefix'=> 'invoice-admin', 'middleware'=>['auth', 'admin']], function ($routes) {

    $routes->put('/{id}', 'SettingsController@update')->name('settings.update');
    $routes->get('/settings', 'SettingsController@index')->name('settings.index')->middleware('RoutePermissionCheck:invoice.settings.index');   
     
    $routes->get('/', 'InvoiceController@index')->name('index')->middleware('RoutePermissionCheck:invoice.index');
    $routes->post('/', 'InvoiceController@index')->name('index.search');
    $routes->get('/create', 'InvoiceController@create')->name('create')->middleware('RoutePermissionCheck:invoice.create');
    $routes->post('/invoice/store', 'InvoiceController@store')->name('store');
    $routes->get('/show/{id}', 'InvoiceController@show')->name('show')->middleware('RoutePermissionCheck:invoice.show');
    $routes->get('/{id}/edit', 'InvoiceController@edit')->name('edit')->middleware('RoutePermissionCheck:invoice.edit');
    $routes->put('/update/{id}', 'InvoiceController@update')->name('update');
    $routes->get('/delete/{id}', 'InvoiceController@destroy')->name('destroy')->middleware('RoutePermissionCheck:invoice.destroy');
    $routes->get('get-price/{course_id}', 'InvoiceController@getCourse')->name('get-price');
    $routes->get('offline-payment', 'OfflinePaymentController@index')->name('offline-payment')->middleware('RoutePermissionCheck:invoice.offline-payment');

    $routes->put('offline-payment-approve/{id}', 'OfflinePaymentController@approve')->name('offline-payment.approve')->middleware('RoutePermissionCheck:invoice.offline-payment.approve');
    $routes->get('offline-payment-delete/{id}', 'OfflinePaymentController@destroy')->name('offline-payment.destroy')->middleware('RoutePermissionCheck:invoice.offline-payment.destroy');
});
Route::group(['as'=>'prc.', 'prefix'=> 'printed-certificate', 'middleware'=>['auth', 'admin']], function ($routes){
    $routes->get('/', 'PrintedCertificateController@index')->name('index');
    $routes->put('/update/{id}', 'PrintedCertificateController@update')->name('update');
    $routes->get('/settings', 'PrcSettingsController@index')->name('settings.index');
    $routes->put('/settings/{id}', 'PrcSettingsController@update')->name('settings.update');
    $routes->get('order-list', 'OrderListController@index')->name('order.index');
    $routes->post('order-list', 'OrderListController@index')->name('order.index.search');
    $routes->put('certificate/shipped/{id}', 'OrderListController@shipped')->name('certificate.shipped');
    $routes->get('certificate/{id}', 'OrderListController@pdfPrint')->name('certificate.pdfPrint');
});
// for student
Route::group(['middleware' => ['student']], function () {
    Route::get('my-invoice', 'MyInvoiceController@index')->name('myInvoice');
    Route::get('/invoice-student/show/{id}', 'InvoiceController@show')->name('invoice.student.show');
    Route::get('billing-update-student', 'InvoiceController@billingUpdate')->name('invoice.billing.update.student');
    Route::get('/billing-data', 'InvoiceController@billingData')->name('invoice.billing.data');
    Route::post('offline-payment-store', 'OfflinePaymentController@store')->name('invoice.offline-payment.store');
    Route::get('/invoice/payment/{invoice_id}', 'MyInvoiceController@payment')->name('invoice.orderPayment');
    Route::get('/certificate/payment/{certificate_number}', 'OrderListController@orderNow')->name('prc.order.now');
});
