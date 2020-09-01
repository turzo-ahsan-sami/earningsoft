<?php

	$allDepartmentRoutes = function() {

		/*
		|--------------------------------------------------------------------------
		| GNR: Position ROUTES
		|--------------------------------------------------------------------------
		*/	
		Route::get('viewDepartment/', 'gnr\GnrDepartmentController@index'); 
		Route::get('addDivision/', 'gnr\GnrDepartmentController@addDivision');
		Route::post('addDivisionItem/', 'gnr\GnrDepartmentController@addItem');  
		Route::post('updateDivisionItem/', 'gnr\GnrDepartmentController@updateItem'); 
		Route::post('deleteDepartmentItem/', 'gnr\GnrDepartmentController@deleteItem'); 
		Route::post('editDepartmentItem/', 'gnr\GnrDepartmentController@editItem'); 

	
		
	};


	Route::group(['prefix' => 'gnr', 'middleware' => ['auth']], $allDepartmentRoutes);
	Route::group(['prefix' => 'mfn', 'middleware' => ['auth']], $allDepartmentRoutes);
	Route::group(['prefix' => 'inv', 'middleware' => ['auth']], $allDepartmentRoutes);
	Route::group(['prefix' => 'fams', 'middleware' => ['auth']], $allDepartmentRoutes);
	Route::group(['prefix' => 'acc', 'middleware' => ['auth']], $allDepartmentRoutes);
	Route::group(['prefix' => 'pos', 'middleware' => ['auth']], $allDepartmentRoutes);

?>
