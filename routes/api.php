<?php

Route::group(['namespace' => 'wdna\users\Controllers'], function() {
   
       
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout');


// Password reset
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.forget');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.reset');

Route::get('/sendmail', 'Emails\UserEmailController@welcome');
Route::post('/register', 'Auth\RegisterController@register');
     
        Route::group(['middleware' => 'auth:api'], function() {
        
                        /* Information */
                Route::post('/user/update_password', 'Users\AccountController@updatePassword');
                Route::get('/user/account', 'Users\AccountController@get');
                Route::get('/user/info', 'Users\InfoController@show');
                Route::post('/user/info', 'Users\InfoController@udpate');
                
                /* User */
                Route::group(['prefix' => '/admin/user/'], function () {
                        Route::get('info','Users\InfoAdminController@getById');
                        Route::get('delete', 'Users\AccountAdminController@deleteById');
                        Route::post('update_password', 'Users\AccountAdminController@updatePasswordById');
                        Route::post('/create', 'Users\AccountAdminController@createUser');
                        Route::get('gridtables/listall', 'Users\InfoController@gridtablesGetSummaryAll');
                });
                
                /* Logs */
                Route::group(['prefix' => '/logs'], function () {
                        Route::get('/users', 'Users\LogController@logs');
                        Route::post('/add', 'Users\LogController@AddLog');
                        Route::get('/events', 'Users\LogController@events');
                        Route::post('/chart', 'Users\LogController@charts');
                
                });
        
        });
   
  
});;