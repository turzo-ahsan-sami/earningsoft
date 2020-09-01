<?php



/*-----------------------------------Register Type----------------------------*/
	Route::group(['middleware' => ['auth','softacc']], function() {

		Route::get('viwRegisterTypeList/', 'accounting\advRegister\AccAdvRegisterTypeController@viewRegisterType');
		Route::get('createAdvRegister/', 'accounting\advRegister\AccAdvRegisterTypeController@createAdvRegisterType');
		Route::post('addAdvRegister/', 'accounting\advRegister\AccAdvRegisterTypeController@addAdvRegisterType');
		Route::post('getAdvRegType/', 'accounting\advRegister\AccAdvRegisterTypeController@getAdvRegType');
        Route::post('updateAdvRegTypeInfo/', 'accounting\advRegister\AccAdvRegisterTypeController@updateAdvRegTypeInfo');
		Route::post('deleteAdvRegister/', 'accounting\advRegister\AccAdvRegisterTypeController@deleteAdvRegisterType');

   });

/*----------------------------Advance Register-----------------------------*/

	Route::group(['middleware' => ['auth','softacc']], function() {

		Route::get('viewAdvRegisterList/', 'accounting\advRegister\AccAdvRegisterController@index');
		Route::post('viewAdvRegisterListReceiveModal/', 'accounting\advRegister\AccAdvRegisterController@viewAdvRegisterListReceiveModal');

		Route::get('createAdvanceRegesterFrom/', 'accounting\advRegister\AccAdvRegisterController@createAdvanceRegesterFrom');

		Route::post('deleteadvanceRegister/', 'accounting\advRegister\AccAdvRegisterController@deleteadvanceRegister');

		Route::get('advanceRegisterChangeHouseOwner/', 'accounting\advRegister\AccAdvRegisterController@advanceRegisterChangeHouseOwner');

		Route::post('advanceRegisterChange/', 'accounting\advRegister\AccAdvRegisterController@advanceRegisterChange');

		Route::post('storeAdvanceReg/','accounting\advRegister\AccAdvRegisterController@storeAdvanceReg');

		Route::post('getAdvRegInfo/', 'accounting\advRegister\AccAdvRegisterController@getAdvRegInfo');

		Route::post('viewAdvRegData/','accounting\advRegister\AccAdvRegisterController@viewAdvRegData');
		Route::post('updateAdvRegInfo/','accounting\advRegister\AccAdvRegisterController@updateAdvRegInfo');
		Route::post('changeProject/','accounting\advRegister\AccAdvRegisterController@changeProject');
		Route::post('paymentTypeChange/','accounting\advRegister\AccAdvRegisterController@paymentTypeChange');


		});



	/*----------------------------Advance Register Report----------------------*/



	Route::group(['middleware' => ['auth','softacc']], function() {

		Route::get('advanceRegister/', 'accounting\advRegReports\AccAdvRegReportController@index');
		Route::post('advRegChangeProjectType/', 'accounting\advRegReports\AccAdvRegReportController@changeProjectType');

	});



	/*================================== Advance Receive ======================== */

 Route::group(['middleware' => ['auth','softacc']], function() {

		Route::get('formAdvReceive/','accounting\advRegister\AccAdvRegReceiveController@createAdvanceReceive');
		Route::get('viewAdvanceReceivelist/','accounting\advRegister\AccAdvRegReceiveController@index');
		Route::post('advanceReceiveChange/','accounting\advRegister\AccAdvRegReceiveController@advanceReceiveChange');
		Route::post('storeAdvanceReceive/','accounting\advRegister\AccAdvRegReceiveController@storeAdvanceReceive');
		Route::post('deleteadvReceive/','accounting\advRegister\AccAdvRegReceiveController@deleteadvReceive');
		//Route::post('viewAdvReceive/','accounting\advRegister\AccAdvRegReceiveController@viewAdvReceive');
		Route::post('viewAdvReceive/','accounting\advRegister\AccAdvRegReceiveController@viewAdvReceive');
		Route::post('getAdvRecInfo/','accounting\advRegister\AccAdvRegReceiveController@getAdvRecInfo');
		Route::post('statusChange/','accounting\advRegister\AccAdvRegReceiveController@statusChange');
		Route::post('advReceChange/','accounting\advRegister\AccAdvRegReceiveController@advReceChange');
		Route::post('updateAdvReceiveInfo/','accounting\advRegister\AccAdvRegReceiveController@updateAdvReceiveInfo');
		Route::post('advanceReceiveAmountChange/','accounting\advRegister\AccAdvRegReceiveController@advanceReceiveAmountChange');



});
