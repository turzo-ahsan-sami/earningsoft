<?php


Route::group(['middleware' => ['auth','softacc']], function() {

	Route::get('viewHouseOwnerRegisterList/', 'gnr\GnrHouseOwnerController@index');
	Route::get('createHouseOwnerFrom/', 'gnr\GnrHouseOwnerController@createHouseOwner');
	Route::post('addHouseOwner/', 'gnr\GnrHouseOwnerController@addHouseOwner');
	Route::post('getHouseOwnerInfo/', 'gnr\GnrHouseOwnerController@getHouseOwnerInfo');
	Route::post('deletehouseOwner/', 'gnr\GnrHouseOwnerController@deletehouseOwner');
	Route::post('viewHouseOwnerInfo/', 'gnr\GnrHouseOwnerController@viewHouseOwnerInfo');
	Route::post('updateHouseOwnerInfo/', 'gnr\GnrHouseOwnerController@houseOwnerInfoupdate');
	Route::post('getHouseOwnerdata/', 'gnr\GnrHouseOwnerController@getHouseOwnerdata');
});

Route::group(['middleware' => ['auth','softacc']], function() {

	// Route::get('viewHouseOwnerRegisterList/', 'gnr\GnrHouseOwnerController@index');
	Route::get('gnrModuleAdd/', 'gnr\GnrModuleController@addModule');
	Route::post('chamgeGroupName/', 'gnr\GnrModuleController@groupchange');
	Route::post('chamgeCompanyName/', 'gnr\GnrModuleController@companychange');
	Route::post('chamgeProjectName/', 'gnr\GnrModuleController@projectchange');
	Route::post('changeProjectTypeName/', 'gnr\GnrModuleController@projectTypechange');
	Route::post('storeModuleInfo/', 'gnr\GnrModuleController@addItem');
	Route::post('validationProject/', 'gnr\GnrModuleController@fiieldValidation');
	Route::post('serchModule/', 'gnr\GnrModuleController@serchModule');
});
