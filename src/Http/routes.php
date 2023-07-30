<?php
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'namespace' => 'SrDev93\VisitLog\Http\Controllers',
        'prefix' => config('visitlog.route', 'visitlog'),
        'middleware' => config('visitlog.middleware')
    ],
    function () {
        Route::get('/', 'VisitLogController@index')->name('__visitlog__');
        Route::delete('delete_visitlog/{id}', 'VisitLogController@destroy')->name('__delete_visitlog__');
        Route::post('ban_user_ip/{id}', 'VisitLogController@banOrUnbanUserIp')->name('__ban_or_unban_user_ip__');
        Route::delete('delete_visitlog_all', 'VisitLogController@destroyAll')->name('__delete_visitlog_all__');
    }
);

