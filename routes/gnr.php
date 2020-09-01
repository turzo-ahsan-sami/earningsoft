<?php

//	ROLE
Route::group(['middleware' => ['auth','softacc']], function () {


    Route::get('addRole/', 'gnr\GnrRoleController@addRole');
    Route::post('addRoleItem/', 'gnr\GnrRoleController@addItem');
    Route::get('viewRoleList/', 'gnr\GnrRoleController@index');
    Route::post('roleCheckedValue/', 'gnr\GnrRoleController@roleCheckedValue');
    Route::post('editRoleItem/', 'gnr\GnrRoleController@editItem');
    Route::post('deleteGnrRoleItem/', 'gnr\GnrRoleController@deleteItem');
    Route::post('getGnrRoleInfo/', 'gnr\GnrRoleController@getRoleInfo');

});


/*
|--------------------------------------------------------------------------
| GNR: ZONE ROUTES
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['auth','softacc']], function () {

    Route::get('viewZone/', 'gnr\GnrZoneController@index');
    Route::get('addZone/', 'gnr\GnrZoneController@addZone');
    Route::post('addZoneItem/', 'gnr\GnrZoneController@addItem');
    Route::post('updateZoneItem/', 'gnr\GnrZoneController@updateItem');
    Route::post('deleteZoneItem/', 'gnr\GnrZoneController@deleteItem');
    Route::post('checkZoneAssignAvailability/', 'gnr\GnrZoneController@checkZoneAssign');
    Route::post('zoneAssignPermitted/', 'gnr\GnrZoneController@assignPermitted');
    Route::get('underPreparationReport/', 'gnr\UnderConstructionController@underPreparationReport');
});

/*
|--------------------------------------------------------------------------
| GNR: REGION ROUTES
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['auth','softacc']], function () {

    Route::get('viewRegion/', 'gnr\GnrRegionController@index');
    Route::get('addRegion/', 'gnr\GnrRegionController@addRegion');
    Route::post('addRegionItem/', 'gnr\GnrRegionController@addItem');
    Route::post('updateRegionItem/', 'gnr\GnrRegionController@updateItem');
    Route::post('deleteRegionItem/', 'gnr\GnrRegionController@deleteItem');
    Route::post('checkRegionAssignAvailability/', 'gnr\GnrRegionController@checkRegionAssign');
    Route::post('regionAssignPermitted/', 'gnr\GnrRegionController@assignPermitted');
});


/*
|--------------------------------------------------------------------------
| GNR: RESPONSIBLE EMPLOYEE ROUTES
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('gnrResponsibility', 'gnr\GnrResponsibilityController@index')->name('gnrResponsibility.index');
    Route::get('gnrResponsibility/create', 'gnr\GnrResponsibilityController@create')->name('gnrResponsibility.create');
    Route::get('gnrResponsibility/edit', 'gnr\GnrResponsibilityController@edit')->name('gnrResponsibility.edit');
    Route::post('gnrResponsibility/store', 'gnr\GnrResponsibilityController@store')->name('gnrResponsibility.store');
    Route::post('gnrResponsibility/update', 'gnr\GnrResponsibilityController@update')->name('gnrResponsibility.update');
    Route::post('gnrResponsibility/delete', 'gnr\GnrResponsibilityController@delete')->name('gnrResponsibility.delete');
});


/*
|--------------------------------------------------------------------------
| GNR: AREA ROUTES
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['auth','softacc']], function () {

    Route::get('viewArea/', 'gnr\GnrAreaController@index');
    Route::get('addArea/', 'gnr\GnrAreaController@addArea');
    Route::post('addAreaItem/', 'gnr\GnrAreaController@addItem');
    Route::post('updateAreaItem/', 'gnr\GnrAreaController@updateItem');
    Route::post('deleteAreaItem/', 'gnr\GnrAreaController@deleteItem');
    Route::post('checkAreaAssignAvailability/', 'gnr\GnrAreaController@checkAreaAssign');
    Route::post('areaAssignPermitted/', 'gnr\GnrAreaController@assignPermitted');
});


/*
|--------------------------------------------------------------------------
| BANK
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['auth','softacc']], function () {

    Route::get('viewBank/', 'gnr\GnrBankController@index');
    Route::get('addBank/', 'gnr\GnrBankController@addBank');
    Route::post('storeBank/', 'gnr\GnrBankController@storeBank');
    Route::post('gnrEditBank/', 'gnr\GnrBankController@editBank');
    Route::post('gnrDeleteBank/', 'gnr\GnrBankController@deleteBank');
    Route::post('gnrGetBankInfo/', 'gnr\GnrBankController@getBankInfo');
});

/////  Branch   /////////

Route::group(['middleware' => ['auth','softacc']], function () {

    Route::get('gnrViewBankBranch/', 'gnr\GnrBankBranchController@index');
    Route::get('gnrAddBankBranch/', 'gnr\GnrBankBranchController@addBranch');
    Route::post('gnrStoreBankBranch/', 'gnr\GnrBankBranchController@storeBranch');
    Route::post('gnrEditBankBranch/', 'gnr\GnrBankBranchController@editBranch');
    Route::post('gnrDeleteBankBranch/', 'gnr\GnrBankBranchController@deleteBranch');
    Route::post('gnrGetBankBranchInfo/', 'gnr\GnrBankBranchController@getBranchInfo');

    Route::post('gnrGetfilteredDistrictNUpazilla/', 'gnr\GnrBankBranchController@filterDistrictNUpazilla');
});


//////   Loan Product /////////
Route::group(['middleware' => ['auth','softacc'], 'prefix' => 'gnr'], function () {

    Route::get('viewLoanProduct/', 'gnr\GnrLoanProductController@index');
    Route::get('addLoanProduct/', 'gnr\GnrLoanProductController@addLoanProduct');
    Route::post('storeLoanProduct/', 'gnr\GnrLoanProductController@storeLoanProduct');
    Route::post('editLoanProduct/', 'gnr\GnrLoanProductController@editLoanProduct');
    Route::post('deleteLoanProduct/', 'gnr\GnrLoanProductController@deleteLoanProduct');
    Route::post('getLoanProductInfo/', 'gnr\GnrLoanProductController@getLoanProductInfo');
});



//general group
Route::group(['middleware' => ['auth','softacc']], function () {


    Route::get('addGroup/', 'gnr\GnrGroupController@addGroup');
    Route::get('viewGroup/', 'gnr\GnrGroupController@index');
    Route::post('addItem/', 'gnr\GnrGroupController@addItem');
    Route::post('editItem/', 'gnr\GnrGroupController@editItem');
    Route::post('deleteItem/', 'gnr\GnrGroupController@deleteItem');
});
//general company
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('addCompany/', 'gnr\GnrCompanyController@addCompany');
    Route::get('viewCompany/', 'gnr\GnrCompanyController@index');
    Route::post('addCompanyItem/', 'gnr\GnrCompanyController@addItem');
    Route::post('editCompanyItem/', 'gnr\GnrCompanyController@editItem');
    Route::post('deleteCompanyItem/', 'gnr\GnrCompanyController@deleteItem');
    Route::post('imageDelete/', 'gnr\GnrCompanyController@imageDelete');
});
//general project
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('addProject/', 'gnr\GnrProjectController@addProject');
    Route::get('viewProject/', 'gnr\GnrProjectController@index');
    Route::post('addProjectItem/', 'gnr\GnrProjectController@addItem');
    Route::post('editProjectItem/', 'gnr\GnrProjectController@editItem');
    Route::post('deleteProjectItem/', 'gnr\GnrProjectController@deleteItem');
});


//notice setup
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('addNotice/', 'gnr\GnrNoticeController@addNotice');
    Route::get('viewNotice/', 'gnr\GnrNoticeController@index');
    Route::post('addNoticeItem/', 'gnr\GnrNoticeController@addItem');
    Route::post('editNoticeItem/', 'gnr\GnrNoticeController@editItem');
    Route::post('deleteNoticeItem/', 'gnr\GnrNoticeController@deleteItem');
    Route::post('checkNoticeAssignAvailability/', 'gnr\GnrNoticeController@checkNoticeAssign');
});
//general project type
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('addProjectType/', 'gnr\GnrProjectTypeController@addProjectType');
    Route::get('viewProjectType/', 'gnr\GnrProjectTypeController@index');
    Route::post('addProjectTypeItem/', 'gnr\GnrProjectTypeController@addItem');
    Route::post('editProjectTypeItem/', 'gnr\GnrProjectTypeController@editItem');
    Route::post('deleteProjectTypeItem/', 'gnr\GnrProjectTypeController@deleteItem');
});
//company branch
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('addBranch/', 'gnr\GnrBranchController@addBranch');
    Route::get('viewBranch/', 'gnr\GnrBranchController@index');
    Route::post('addBranchItem/', 'gnr\GnrBranchController@addItem');
    Route::post('editBranchItem/', 'gnr\GnrBranchController@editItem');
    Route::post('deleteBranchItem/', 'gnr\GnrBranchController@deleteItem');
    Route::post('projectIdSendGetPTypeId/', 'gnr\GnrBranchController@projectTypeChanged');
});
//company fiscalyear
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('addFiscalYear/', 'gnr\GnrFiscalYearController@addFiscalYear');
    Route::get('viewFiscalYear/', 'gnr\GnrFiscalYearController@index');
    Route::post('addFiscalYearItem/', 'gnr\GnrFiscalYearController@addItem');
    Route::post('editFiscalYearItem/', 'gnr\GnrFiscalYearController@editItem');
    Route::post('deleteFiscalYearItem/', 'gnr\GnrFiscalYearController@deleteItem');
});
//supplier group
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('addSupplier/', 'gnr\GnrSupplierController@addSupplier');
    Route::get('viewSupplier/', 'gnr\GnrSupplierController@index');
    Route::post('addSupplierItem/', 'gnr\GnrSupplierController@addItem');
    Route::post('editSupplierItem/', 'gnr\GnrSupplierController@editItem');
    Route::post('deleteSupplierItem/', 'gnr\GnrSupplierController@deleteItem');
});

//Department
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('addDepartmentF/', 'gnr\GnrDepartmentController@addDepartmentForm');
    Route::get('viewDepartmentList/', 'gnr\GnrDepartmentController@index');
    Route::post('addDepartmentItem/', 'gnr\GnrDepartmentController@addItem');
    Route::post('editDepartmentItem/', 'gnr\GnrDepartmentController@editItem');
    Route::post('deleteDepartmentItem/', 'gnr\GnrDepartmentController@deleteItem');
});

//room
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('addRoomF/', 'gnr\GnrRoomController@addRoomForm');
    Route::get('viewRoomList/', 'gnr\GnrRoomController@index');
    Route::post('addRoomItem/', 'gnr\GnrRoomController@addItem');
    Route::post('editRoomItem/', 'gnr\GnrRoomController@editItem');
    Route::post('deleteRoomItem/', 'gnr\GnrRoomController@deleteItem');
    Route::post('bringDepartments/', 'gnr\GnrRoomController@bringDepartments');
});

//Route Operation
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('viewRouteOperation/', 'gnr\GnrRouteOperationController@index');
    Route::get('addRouteOperation/', 'gnr\GnrRouteOperationController@addRouteOperation');
    Route::post('addRouteOperationItem/', 'gnr\GnrRouteOperationController@addItem');
    Route::post('editRouteOperationItem/', 'gnr\GnrRouteOperationController@editItem');
    Route::post('deleteRouteOperationItem/', 'gnr\GnrRouteOperationController@deleteItem');
    Route::post('gnrGetFunctionBaseOnModule/', 'gnr\GnrRouteOperationController@getFunctionBaseOnModule');
});


//subfunctionality
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('addSubFunction/', 'gnr\GnrSubFunctionController@addSubFunctionality');
    Route::get('viewSubFunction/', 'gnr\GnrSubFunctionController@index');
    Route::post('addSubFunctionItem/', 'gnr\GnrSubFunctionController@addItem');
    Route::post('editsubfunctionItem/', 'gnr\GnrSubFunctionController@editItem');
    Route::post('deleteSubfunctionItem/', 'gnr\GnrSubFunctionController@deleteItem');
});


//function
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('viewFunction/', 'gnr\GnrFunctionController@index');
    Route::get('addFunction/', 'gnr\GnrFunctionController@addFunction');
    Route::post('storeFunction/', 'gnr\GnrFunctionController@storeFunction');
    Route::post('updateFunction/', 'gnr\GnrFunctionController@updateFunction');
    Route::post('deleteFunction/', 'gnr\GnrFunctionController@deleteFunction');
});


// General user
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::any('guestUser', 'gnr\GnrGuestUserController@index')->name('guestUser.index');
    Route::any('guestUser/create', 'gnr\GnrGuestUserController@create')->name('guestUser.create');
    Route::any('guestUser/store', 'gnr\GnrGuestUserController@store')->name('guestUser.store');
    Route::any('guestUser/edit/{id}', 'gnr\GnrGuestUserController@edit')->name('guestUser.edit');
    Route::any('guestUser/update/{id}', 'gnr\GnrGuestUserController@update')->name('guestUser.update');
    Route::any('guestUser/delete/{id}', 'gnr\GnrGuestUserController@delete')->name('guestUser.delete');
    Route::any('guestUser/getProjectsByCompanyId/{id}', 'gnr\GnrGuestUserController@getProjectsByCompanyId')->name('guestUser.getProjectsByCompanyId');
});

//general user role
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('viewGnrUserRole/', 'gnr\GnrUserRoleController@index');
    Route::get('addGnrUserRole/', 'gnr\GnrUserRoleController@addUserRole');
    Route::post('storeGnrUserRole/', 'gnr\GnrUserRoleController@storeUserRole');
    Route::post('getCheckedValue/', 'gnr\GnrUserRoleController@getCheckedValue');
    Route::post('editGnrUserRole/', 'gnr\GnrUserRoleController@editUserRole');
    Route::post('deleteGnrUserRole/', 'gnr\GnrUserRoleController@deleteUserRole');
    Route::post('getGnrUserRoleInfo/', 'gnr\GnrUserRoleController@getUserRoleInfo');
});


//general Signature Setting role
/* Route::group(['middleware' => ['auth','softacc']], function() {

  Route::get('addSignatureSetting/',    'gnr\GnrSignatureSettingController@createSignatureSetting');
  Route::post('changeGnrUserRole/', 'gnr\GnrSignatureSettingController@changeUserRole');
  Route::post('onChangeBranch/', 'gnr\GnrSignatureSettingController@onChangeBranch');
  Route::get('SignatureSettingList/',   'gnr\GnrSignatureSettingController@index');
  Route::post('storeSignatureSetting/',        'gnr\GnrSignatureSettingController@storeSignatureSetting');
  Route::post('deleteDMsignatureSetting/',  'gnr\GnrSignatureSettingController@deleteSignatureSetting');
  Route::post('viewSignatureSetting/',  'gnr\GnrSignatureSettingController@viewSignatureSettingInfo');
  Route::post('changeGnrUserBranch/',  'gnr\GnrSignatureSettingController@changeUserBranch');
  Route::post('getDataSignatureSetting/',  'gnr\GnrSignatureSettingController@getDataSignatureSetting');
  Route::post('getDataSignatureSettingToUpdate/',  'gnr\GnrSignatureSettingController@viewSignatureSettingInfo');
 Route::post('signatureSettingInfoupdate/',  'gnr\GnrSignatureSettingController@signatureSettingInfoupdate');

 Route::get('getSignatureTemplate/',  'gnr\GnrSignatureSettingController@getSignatureTemplate');

});*/

Route::group(['middleware' => ['auth','softacc'], 'prefix' => 'gnr'], function () {

    Route::get('signatureSettingList/', 'gnr\GnrSignatureSettingController@index');
    Route::get('addSignatureSetting/', 'gnr\GnrSignatureSettingController@addSignatureSetting');
    Route::post('storeSignatureSetting/', 'gnr\GnrSignatureSettingController@checkIsExits');
    Route::post('updateSignatureSetting/', 'gnr\GnrSignatureSettingController@updateSignatureSetting');
    Route::post('getRolesForSignature/', 'gnr\GnrSignatureSettingController@getRolesForSignature');
    Route::post('getPositionsForSignature/', 'gnr\GnrSignatureSettingController@getPositionsForSignature');
    Route::post('getEmpoyeeForSignature/', 'gnr\GnrSignatureSettingController@getEmpoyeeForSignature');

    Route::get('getSignatureTemplate/', 'gnr\GnrSignatureSettingController@getSignatureTemplate');

});



//////   Handle Maintenance Mode /////////


/*Route::group(array('before' => 'auth'), function()
{

   Route::get('setSiteInMaintenanceMode/', 'gnr\GnrMaintenanceModeController@siteDown');
   Route::get('setSiteInUpMode/', 'gnr\GnrMaintenanceModeController@siteUp');

});*/


include 'gnr2.php';
include 'gnr_address.php';
include 'gnr_department.php';
include 'gnr_position.php';
include 'gnr_employee.php';
