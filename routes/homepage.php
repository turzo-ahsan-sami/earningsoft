<?php
Route::group(['middleware' => ['auth','softacc']], function() {

Route::get('homepage','FrontEnd\HomePageController@index');

});
