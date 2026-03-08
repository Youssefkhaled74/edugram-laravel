<?php


use Illuminate\Support\Facades\Route;

Route::prefix('amazons3')->middleware('auth')->group(function () {
    Route::get('/', 'AmazonS3Controller@index')->name('AwsS3Setting')->middleware('RoutePermissionCheck:AwsS3Setting');
    Route::post('/', 'AmazonS3Controller@store')->name('AwsS3SettingSubmit')->middleware('RoutePermissionCheck:AwsS3SettingSubmit');
});
