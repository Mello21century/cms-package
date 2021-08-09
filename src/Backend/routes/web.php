<?php
/**
 * MYMO CMS - Free Laravel CMS
 *
 * @package juzacmscms/juzacmscms
 * @author The Anh Dang
 *
 * Developed based on Laravel Framework
 * Github: https://github.com/juzacmscms/juzacmscms
*/

$adminPrefix = config('juzacms.admin_prefix', 'admin-cp');

Route::group([
    'prefix' => $adminPrefix,
    'middleware' => ['admin']
], function () {
    require __DIR__ . '/components/dashboard.route.php';
    require __DIR__ . '/components/appearance.route.php';
    require __DIR__ . '/components/setting.route.php';
    require __DIR__ . '/components/user.route.php';
    require __DIR__ . '/components/module.route.php';
    require __DIR__ . '/components/page.route.php';
    require __DIR__ . '/components/post.route.php';
    require __DIR__ . '/components/filemanager.route.php';
    require __DIR__ . '/components/media.route.php';
    require __DIR__ . '/components/email.route.php';

    Route::juzacmsResource('notification', 'Backend\NotificationController');
});

Route::group([
    'prefix' => $adminPrefix,
    'middleware' => 'guest'
], function () {
    Route::get('/login', 'Auth\LoginController@index')->name('auth.login');
    Route::post('/login', 'Auth\LoginController@login');

    Route::get('/register', 'Auth\RegisterController@index')->name('auth.register');
    Route::post('/register', 'Auth\RegisterController@register');

    Route::get('/forgot-password', 'Auth\ForgotPasswordController@index')->name('auth.forgot_password');
    Route::post('/forgot-password', 'Auth\ForgotPasswordController@forgotPassword');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/logout', 'Auth\LoginController@logout')->name('auth.logout');
});
