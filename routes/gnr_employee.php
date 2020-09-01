<?php

    $allEmployeeRoutes = function() {

    	Route::get('posHrEmployeeList/', 'gnr\EmployeeController@index');
    	Route::get('posAddHrEmployee/', 'gnr\EmployeeController@posAddHrEmployee');
    	Route::post('preDistricFiltering/', 'gnr\EmployeeController@preDistricFiltering');
    	Route::post('preUpzillaDataFiltering/', 'gnr\EmployeeController@preUpzillaDataFiltering');
    	Route::post('preUnionDataFiltering/', 'gnr\EmployeeController@preUnionDataFiltering');
    	Route::post('projectTypeFiltering/', 'gnr\EmployeeController@projectTypeFiltering');
    	Route::post('storeEmployeeItem/', 'gnr\EmployeeController@addItem');
    	Route::post('employeeGetInfo/', 'gnr\EmployeeController@employeeGetInfo');
        Route::get('hrEditEmployee/{id}', 'gnr\EmployeeController@employeeEdit');
    	Route::post('updateHrEployeeInfo/', 'gnr\EmployeeController@updateHrEployeeInfo');
    	Route::post('hrDeleteEmployee/', 'gnr\EmployeeController@hrDeleteEmployee');
    	Route::get('hrDetailsEmployee/{employeeId}', 'gnr\EmployeeController@hrDetailsEmployee');
    	Route::post('branchFilteringByProject/', 'gnr\EmployeeController@branchFilteringByProject');
        Route::get('getBranchInfo/', 'gnr\EmployeeController@getBranchInfo');
        Route::get('getPositionInfo/', 'gnr\EmployeeController@getPositionInfo');

	};

    // new add on 16 feb 2020
    $empManagementRoutes = function() {

        /* Resign */
    	Route::get('resignInfo', 'hr\ResignInfoController@index');
    	Route::any('resignInfo/create', 'hr\ResignInfoController@create');
    	Route::any('resignInfo/update/{id}', 'hr\ResignInfoController@update');
    	Route::any('resignInfo/approved/{id}', 'hr\ResignInfoController@approved');
    	Route::any('resignInfo/delete', 'hr\ResignInfoController@delete');
    	Route::any('resignInfo/view/{id}', 'hr\ResignInfoController@view');
    	Route::any('resignInfo/cancel/{id}', 'hr\ResignInfoController@cancel');
    	Route::any('resignInfo/getEmployeeCurrentData', 'hr\ResignInfoController@getEmployeeCurrentData');

    	/* Terminate */
    	Route::get('terminateInfo', 'hr\TerminateInfoController@index');
    	Route::any('terminateInfo/create', 'hr\TerminateInfoController@create');
    	Route::any('terminateInfo/update/{id}', 'hr\TerminateInfoController@update');
    	Route::any('terminateInfo/approved/{id}', 'hr\TerminateInfoController@approved');
    	Route::any('terminateInfo/delete', 'hr\TerminateInfoController@delete');
    	Route::any('terminateInfo/view/{id}', 'hr\TerminateInfoController@view');
    	Route::any('terminateInfo/cancel/{id}', 'hr\TerminateInfoController@cancel');
    	Route::any('terminateInfo/getEmployeeCurrentData', 'hr\TerminateInfoController@getEmployeeCurrentData');

    	/* Transfer */
    	Route::get('transfer', 'hr\TransferController@index');
    	Route::any('transfer/create', 'hr\TransferController@create');
    	Route::any('transfer/update/{id}', 'hr\TransferController@update');
    	Route::any('transfer/view/{id}', 'hr\TransferController@view');
    	Route::any('transfer/approved', 'hr\TransferController@approved');
    	Route::any('transfer/confirmed', 'hr\TransferController@confirmed');
    	Route::any('transfer/getEmployeeCurrentData', 'hr\TransferController@getEmployeeCurrentData');
    	/* Transfer */
    };

    $prefixes = array('mfn', 'acc', 'gnr', 'inv', 'pos', 'fams');
    $empManagementPrefixes = array('hr', 'mfn', 'acc', 'gnr', 'inv', 'pos', 'fams');

    foreach ($prefixes as $prefix) {
        Route::group(['prefix' => $prefix, 'middleware' => ['auth','softacc']], $allEmployeeRoutes);
    }

    foreach ($empManagementPrefixes as $empPrefix) {
        Route::group(['prefix' => $empPrefix, 'middleware' => ['auth','softacc']], $empManagementRoutes);
    }
