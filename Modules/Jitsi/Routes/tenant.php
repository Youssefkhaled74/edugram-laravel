<?php

use Illuminate\Support\Facades\Route;



Route::prefix('jitsi')->group(function () {
    Route::name('jitsi.')->middleware('auth')->group(function () {

        Route::get('meetings', 'JitsiMeetingController@index')->name('meetings')->middleware('RoutePermissionCheck:Jitsi.meetings.index');
        Route::post('meetings', 'JitsiMeetingController@store')->name('meetings.store')->middleware('RoutePermissionCheck:Jitsi.meetings.store');
        // A teacher needs to start their own class even when they do not have
        // the back-office Jitsi management permission. Ownership is verified
        // in the controller.
        Route::get('meetings-show/{id}', 'JitsiMeetingController@show')->name('meetings.show');
        Route::get('meetings-edit/{id}', 'JitsiMeetingController@edit')->name('meetings.edit')->middleware('RoutePermissionCheck:Jitsi.meetings.edit');
        Route::post('meetings/{id}', 'JitsiMeetingController@update')->name('meetings.update')->middleware('RoutePermissionCheck:Jitsi.meetings.edit');
        Route::delete('meetings/{id}', 'JitsiMeetingController@destroy')->name('meetings.destroy')->middleware('RoutePermissionCheck:Jitsi.meetings.destroy');
        Route::get('settings', 'JitsiSettingController@settings')->name('settings')->middleware('RoutePermissionCheck:Jitsi.settings');
        Route::post('settings', 'JitsiSettingController@updateSettings')->name('settings.update')->middleware('RoutePermissionCheck:Jitsi.settings');
        Route::get('user-list-user-type-wise', 'JitsiMeetingController@userWiseUserList')->name('user.list.user.type.wise');
        Route::get('virtual-class-room/{id}', 'JitsiMeetingController@meetingStart')->name('meeting.join');
        Route::post('meetings', 'JitsiMeetingController@store')->name('meetings.store');


        Route::get('meeting-start/{course_id}/{meeting_id}', 'JitsiMeetingController@meetingStart')->name('meetingStart');

    });
});
