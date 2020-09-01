<?php
Auth::routes(['register' => false]);

// Change Password Routes...

Route::get('change_password', 'Auth\ChangePasswordController@showChangePasswordForm')->name('auth.change_password');
Route::patch('change_password', 'Auth\ChangePasswordController@changePassword')->name('auth.change_password');

// admin routes
include 'admin.php';

//front end routes
include 'frontend.php';

//software routes
include 'software.php';
include 'holiday_routes.php';
Route::get('profile{id}', 'gnr\ProfileController@profile')->name('profile');
Route::get('subscriptionDetails', 'gnr\SubscriptionController@subscriptionDetails')->name('subscriptionDetails');

Route::get('/nexmo', 'NexmoController@show')->name('nexmo');
Route::post('/nexmo', 'NexmoController@verify')->name('nexmo');

