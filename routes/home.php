<?php

Route::group(['middleware' => ['auth','softacc']], function() {

	Route::get('gnr/home', 	'home\GnrHomeController@index');
	Route::get('gnr/loadgnrTab1', 	'home\GnrHomeController@loadgnrTab1');
	Route::get('gnr/loadgnrTab3', 	'home\GnrHomeController@loadgnrTab3');
	


	Route::get('hrm/home', 			'home\HrmHomeController@index');
	Route::get('hrm/loadHrmTab1', 	'home\HrmHomeController@loadHrmTab1');
	Route::get('hrm/loadHrmTab2', 	'home\HrmHomeController@loadHrmTab2');
	Route::get('hrm/loadHrmTab3', 	'home\HrmHomeController@loadHrmTab3');
	Route::get('hrm/loadHrmTab4', 	'home\HrmHomeController@loadHrmTab4');

	Route::get('mfn/home', 			'home\MfnHomeController@index');
	Route::get('mfn/loadMfnTab1', 	'home\MfnHomeController@loadMfnTab1');
	Route::get('mfn/loadMfnTab2', 	'home\MfnHomeController@loadMfnTab2');
	Route::get('mfn/loadMfnTab3', 	'home\MfnHomeController@loadMfnTab3');
	Route::get('mfn/loadMfnTab4', 	'home\MfnHomeController@loadMfnTab4');


	Route::get('fams/home', 			'home\FamsHomeController@index');
	Route::get('fams/loadFamsTab1', 	'home\FamsHomeController@loadFamsTab1');
	Route::get('fams/loadFamsTab2', 	'home\FamsHomeController@loadFamsTab2');
	Route::get('fams/loadFamsTab3', 	'home\FamsHomeController@loadFamsTab3');
	Route::get('fams/loadFamsTab4', 	'home\FamsHomeController@loadFamsTab4');

	Route::get('inv/home', 			'home\InvHomeController@index');
	Route::get('inv/loadInvTab1', 	'home\InvHomeController@loadInvTab1');
	Route::get('inv/loadInvTab2', 	'home\InvHomeController@loadInvTab2');
	Route::get('inv/loadInvTab3', 	'home\InvHomeController@loadInvTab3');
	Route::get('inv/loadInvTab4', 	'home\InvHomeController@loadInvTab4');

	Route::get('acc/home', 			'home\AccHomeController@index');
	Route::get('acc/loadAccTab1', 	'home\AccHomeController@loadAccTab1');
	Route::get('acc/loadAccTab2', 	'home\AccHomeController@loadAccTab2');
	Route::get('acc/loadAccTab3', 	'home\AccHomeController@loadAccTab3');
	Route::get('acc/loadAccTab4', 	'home\AccHomeController@loadAccTab4');
	Route::get('acc/homeTest', 		'home\AccHomeController@homeTest');


	Route::get('pos/home', 			'home\PosHomeController@index');
	Route::get('pos/loadPosTab1', 	'home\PosHomeController@loadPosTab1');
	Route::get('pos/loadPosTab2', 	'home\PosHomeController@loadPosTab2');
	Route::get('pos/loadPosTab3', 	'home\PosHomeController@loadPosTab3');
	Route::get('pos/loadPosTab4', 	'home\PosHomeController@loadPosTab4');

	Route::get('attendence/home', 			'home\AttendenceHomeController@index');
	
	Route::get('report/home', 			'home\ReportHomeController@index');
	Route::get('report/loadReportTab1', 	'home\ReportHomeController@loadReportTab1');
	Route::get('report/loadReportTab2', 	'home\ReportHomeController@loadReportTab2');
	Route::get('report/loadReportTab3', 	'home\ReportHomeController@loadReportTab3');
	Route::get('report/loadReportTab4', 	'home\ReportHomeController@loadReportTab4');
	Route::get('report/viewAccReport', 	'home\ReportHomeController@viewAccReport');

	Route::get('welcome/loadwelcomeTab1', 	'home\DashboardHomeController@loadwelcomeTab1');
	Route::get('welcome/loadwelcomeTab2', 	'home\DashboardHomeController@loadwelcomeTab2');
	Route::get('welcome/loadwelcomeTab3', 	'home\DashboardHomeController@loadwelcomeTab3');
	Route::get('welcome/loadwelcomeTab4', 	'home\DashboardHomeController@loadwelcomeTab4');
	
	
	
});
