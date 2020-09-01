<?php

	$allAddressRoutes = function() {

		/*
		|--------------------------------------------------------------------------
		| GNR: DIVISION ROUTES
		|--------------------------------------------------------------------------
		*/	
		Route::get('viewDivision/', 'gnr\GnrDivisionController@index'); 
		Route::get('addDivision/', 'gnr\GnrDivisionController@addDivision');
		Route::post('addDivisionItem/', 'gnr\GnrDivisionController@addItem');  
		Route::post('updateDivisionItem/', 'gnr\GnrDivisionController@updateItem'); 
		Route::post('deleteDivisionItem/', 'gnr\GnrDivisionController@deleteItem'); 

		/*
		|--------------------------------------------------------------------------
		| GNR: DISTRICT ROUTES
		|--------------------------------------------------------------------------
		*/
		Route::get('viewDistrict/', 'gnr\GnrDistrictController@index'); 
		Route::get('addDistrict/', 'gnr\GnrDistrictController@addDistrict');
		Route::post('addDistrictItem/', 'gnr\GnrDistrictController@addItem');  
		Route::post('updateDistrictItem/', 'gnr\GnrDistrictController@updateItem'); 
		Route::post('deleteDistrictItem/', 'gnr\GnrDistrictController@deleteItem');

		/*
		|--------------------------------------------------------------------------
		| GNR: UPAZILA ROUTES
		|--------------------------------------------------------------------------
		*/

		Route::get('viewUpazila/', 'gnr\GnrUpazilaController@index'); 
		Route::get('addUpazila/', 'gnr\GnrUpazilaController@addUpazila');
		Route::post('addUpazilaItem/', 'gnr\GnrUpazilaController@addItem');  
		Route::post('updateUpazilaItem/', 'gnr\GnrUpazilaController@updateItem'); 
		Route::post('deleteUpazilaItem/', 'gnr\GnrUpazilaController@deleteItem'); 
		
		/*
		|--------------------------------------------------------------------------
		| GNR: UNION ROUTES
		|--------------------------------------------------------------------------
		*/
		Route::get('addUnion/', 'gnr\GnrUnionController@addUnion');
  	    Route::get('viewUnion/', 'gnr\GnrUnionController@index');
  	    Route::post ('divisionIdSend/', 'gnr\GnrUnionController@districtChange');
        Route::post ('districtSendId/', 'gnr\GnrUnionController@upazilaChange');
        Route::post ('addUnionItem/', 'gnr\GnrUnionController@addItem');
        Route::post ('editUnionItem/', 'gnr\GnrUnionController@editItem');
  	    Route::post ('deleteUnionItem/', 'gnr\GnrUnionController@deleteItem');

  	    /*
		|--------------------------------------------------------------------------
		| GNR: VILLAGE ROUTES
		|--------------------------------------------------------------------------
		*/
		Route::get('addVillage/', 'gnr\GnrVillageController@addVillage');
        Route::get('viewVillage/', 'gnr\GnrVillageController@index');
        Route::post('unionSendId/', 'gnr\GnrVillageController@changeUnion');
        Route::post ('addVillageItem/', 'gnr\GnrVillageController@addItem');
        Route::post ('editVillageItem/', 'gnr\GnrVillageController@editItem');
        Route::post ('deleteVillageItem/', 'gnr\GnrVillageController@deleteItem');

        /*
		|--------------------------------------------------------------------------
		| GNR: WORKING AREA ROUTES
		|--------------------------------------------------------------------------
		*/
		Route::get('viewWorkingArea/', 'gnr\GnrWorkingAreaController@index'); 
		Route::get('addWorkingArea/', 'gnr\GnrWorkingAreaController@addWorkingArea');
		Route::post('reqDistrict/', 'gnr\GnrWorkingAreaController@loadDistrict'); 
		Route::post('reqUpzilla/', 'gnr\GnrWorkingAreaController@loadUpzilla');  
		Route::post('reqUnion/', 'gnr\GnrWorkingAreaController@loadUnion'); 
		Route::post('reqVillage/', 'gnr\GnrWorkingAreaController@loadVillage');
		Route::post('reqDistrictUpzillaUnionVillage/', 'gnr\GnrWorkingAreaController@loadDistrictUpzillaUnionVillage');      
		Route::post('addWorkingAreaItem/', 'gnr\GnrWorkingAreaController@addItem');  
		Route::post('updateWorkingAreaItem/', 'gnr\GnrWorkingAreaController@updateItem'); 
		Route::post('deleteWorkingAreaItem/', 'gnr\GnrWorkingAreaController@deleteItem');
		
	};


	Route::group(['prefix' => 'gnr', 'middleware' => ['auth']], $allAddressRoutes);
	Route::group(['prefix' => 'mfn', 'middleware' => ['auth']], $allAddressRoutes);
	Route::group(['prefix' => 'inv', 'middleware' => ['auth']], $allAddressRoutes);
	Route::group(['prefix' => 'fams', 'middleware' => ['auth']], $allAddressRoutes);
	Route::group(['prefix' => 'acc', 'middleware' => ['auth']], $allAddressRoutes);
	Route::group(['prefix' => 'pos', 'middleware' => ['auth']], $allAddressRoutes);

?>