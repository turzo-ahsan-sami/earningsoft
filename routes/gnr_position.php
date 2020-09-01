<?php

	$allPositionRoutes = function() {

		/*
		|--------------------------------------------------------------------------
		| GNR: Position ROUTES
		|--------------------------------------------------------------------------
		*/	
		Route::get('viewPosition/', 'gnr\GnrPositionController@index'); 
		Route::get('addPosition/', 'gnr\GnrPositionController@addPosition');
		Route::post('addPositionItem/', 'gnr\GnrPositionController@addItem');  
		Route::get('editPosition/{id}', 'gnr\GnrPositionController@editPosition');  
		Route::post('updatePosition/', 'gnr\GnrPositionController@updateItem'); 
		Route::post('deletePositiontem/', 'gnr\GnrPositionController@deletePositiontem'); 
		Route::get('getDepartmentInfo/', 'gnr\GnrPositionController@getDepartmentInfo'); 
		Route::get('getDepartmentInfo/', 'gnr\GnrPositionController@getDepartmentInfo'); 

	
		
	};


	Route::group(['prefix' => 'gnr', 'middleware' => ['auth']], $allPositionRoutes);
	Route::group(['prefix' => 'mfn', 'middleware' => ['auth']], $allPositionRoutes);
	Route::group(['prefix' => 'inv', 'middleware' => ['auth']], $allPositionRoutes);
	Route::group(['prefix' => 'fams', 'middleware' => ['auth']], $allPositionRoutes);
	Route::group(['prefix' => 'acc', 'middleware' => ['auth']], $allPositionRoutes);
	Route::group(['prefix' => 'pos', 'middleware' => ['auth']], $allPositionRoutes);

?>
