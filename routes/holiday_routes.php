<?php

$allHolidayRoutes = function() {
		// HOLIDAY.
	Route::get('viewHolidayCalender/', 'gnr\HolidayController@index');
	Route::post('StoreHoliday/', 'gnr\HolidayController@storeHoliday');

		// GOV HOLIDAY
	Route::get('viewGovHoliday/', 'gnr\HolidayController@viewGovHoliday');
	Route::get('addGovHoliday/', 'gnr\HolidayController@addGovHoliday');
	Route::post('storeGovHoliday/', 'gnr\HolidayController@storeGovHoliday');
	Route::post('updateGovHoliday/', 'gnr\HolidayController@updateGovHoliday');
	Route::post('deleteGovHoliday/', 'gnr\HolidayController@deleteGovHoliday');

		// ORG/BRANCH/SAMITY HOLIDAY
	Route::get('viewOrgBranchSamityHoliday/', 'gnr\HolidayController@viewOrgBranchSamityHoliday');
	Route::get('addOrgBranchSamityHoliday/', 'gnr\HolidayController@addOrgBranchSamityHoliday');
	Route::post('storeOrgBranchSamityHoliday/', 'gnr\HolidayController@storeOrgBranchSamityHoliday');
	Route::post('updateOrgBranchSamityHoliday/', 'gnr\HolidayController@updateOrgBranchSamityHoliday');
	Route::post('deleteOrgBranchSamityHoliday/', 'gnr\HolidayController@deleteOrgBranchSamityHoliday');


				//Weekly HOLIDAY
	Route::get('viewWeeklyHoliday/', 'gnr\WeeklyHolidayController@viewWeeklyHoliday');
	Route::get('addWeeklyHoliday/', 'gnr\WeeklyHolidayController@addWeeklyHoliday');
	Route::post('storeWeeklyHoliday/', 'gnr\WeeklyHolidayController@storeWeeklyHoliday');
	Route::post('updateWeeklyHoliday/', 'gnr\WeeklyHolidayController@updateWeeklyHoliday');
	Route::post('getWeeklyHolidayDetails/', 'gnr\WeeklyHolidayController@getWeeklyHolidayDetails');
	Route::post('deleteWeeklyHoliday/', 'gnr\WeeklyHolidayController@deleteWeeklyHoliday');

		// VIEW HOLIDAY LIST
	Route::get('viewHolidayList/', 'gnr\HolidayController@getHolidayList');
	Route::get('viewHolidayListReportTable/', 'gnr\HolidayController@getReportTable');
	Route::post('viewHolidayListAjaxBranch/', 'gnr\HolidayController@getHolidayListBarnchInfo');
	Route::post('viewHolidayListAjaxSamity/', 'gnr\HolidayController@getHolidayListSamityInfo');

		// AJAX
	Route::post('getHolidayDetails/', 'gnr\HolidayAjaxController@getHolidayDetails');
	Route::post('GetHolidayYearDetails/', 'gnr\HolidayAjaxController@getHolidayYearDetails');
	Route::post('getOrgHolidayDetaisToUpdate/', 'gnr\HolidayAjaxController@getOrgHolidayDetails');

};

Route::group(['prefix' => 'mfn', 'middleware' => ['auth']], $allHolidayRoutes);
Route::group(['prefix' => 'acc', 'middleware' => ['auth']], $allHolidayRoutes);
Route::group(['prefix' => 'gnr', 'middleware' => ['auth']], $allHolidayRoutes);
Route::group(['prefix' => 'inv', 'middleware' => ['auth']], $allHolidayRoutes);
Route::group(['prefix' => 'pos', 'middleware' => ['auth']], $allHolidayRoutes);
Route::group(['prefix' => 'fams', 'middleware' => ['auth']], $allHolidayRoutes);




