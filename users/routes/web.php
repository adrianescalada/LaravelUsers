<?php

Route::group(['namespace' => 'Wdna\Users\Controllers'], function() {
   
        Route::resource('users', 'Users/UserController');
        
  
});;