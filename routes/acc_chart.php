<?php
Route::group(['middleware' => ['auth','softacc']], function() {

	Route::get('acc/loadChart/Tab1', 'home\AccChartController@loadChart1');
	Route::get('acc/loadFullChart/Tab1', 'home\AccChartController@loadChart1');

	Route::get('acc/loadChart/Tab2', 'home\AccChartController@loadChart2');
	Route::get('acc/loadFullChart/Tab2', 'home\AccChartController@loadChart2');

	Route::get('acc/loadChart/Tab3', 'home\AccChartController@loadChart3');
	Route::get('acc/loadFullChart/Tab3', 'home\AccChartController@loadChart3');

	Route::get('acc/loadChart/Tab4', 'home\AccChartController@loadChart4');
	Route::get('acc/loadFullChart/Tab4', 'home\AccChartController@loadChart4');

});
?>
