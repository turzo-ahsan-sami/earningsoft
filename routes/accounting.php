<?php
//====================================Account Type====================================
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('viewAccountType/',          'accounting\AccAccountTypeController@index');
    Route::get('addAccountType/',           'accounting\AccAccountTypeController@addAccountType');
    Route::post('addAccountTypeItem/',      'accounting\AccAccountTypeController@addItem');
    Route::post('deleteAccountTypeItem/',   'accounting\AccAccountTypeController@deleteItem');
    Route::post('editAccountTypeItem/',     'accounting\AccAccountTypeController@editItem');
});

//======================================Ledger======================================
    Route::get('testLedger/',                           'accounting\AccLedgerController@testLedger');
    Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('viewLedger/',                           'accounting\AccLedgerController@index');
    Route::get('addLedger/{encryptedParentId}',         'accounting\AccLedgerController@addLedger');
    Route::post('addLedger/checkUniqueLedgerCode/',         'accounting\AccLedgerController@checkUniqueLedgerCode');
    Route::post('addLedger/addLedgerItem/',             'accounting\AccLedgerController@addItem');
    Route::post('addLedger/projectIdSend/',             'accounting\AccLedgerController@branchChange');
    Route::post('deleteLedgerItem/',                    'accounting\AccLedgerController@deleteItem');
    // add transaction level ledger
    Route::get('viewTransactionLedger/',        'accounting\AccLedgerController@indexTr');
    Route::get('addTransactionLedger/',         'accounting\AccLedgerController@addTransactionLedger');
    Route::post('addTransactionLedgerItem/',         'accounting\AccLedgerController@addTransactionLedgerItem');
    

    Route::post('getBranchByProject/',                  'accounting\AccLedgerController@getBranchByProject');
    Route::post('getBranchByProjectTest/',              'accounting\AccLedgerController@getBranchByProjectTest');
    Route::post('getLedgerLevelsByProject/',            'accounting\AccLedgerController@getLedgerLevelsByProject');
    // Route::get('filterLedgerByProject/',             'accounting\AccLedgerController@viewLedgerTest');
    Route::get('viewLedgerByProjectName/',              'accounting\AccLedgerController@viewLedgerByProjectName');
    Route::get('viewLedgerByBranchName/',               'accounting\AccLedgerController@viewLedgerByBranchName');
    Route::get('viewLedgerByLedger/',                   'accounting\AccLedgerController@viewLedgerByLedger');

    // Route::get('testLedger1/',                       'accounting\AccLedgerController@testLedger1');

    Route::get('editLedger/{encryptedId}',              'accounting\AccLedgerController@editLedger');
    Route::post('editLedger/projectIdSend/',            'accounting\AccLedgerController@branchChange');
    Route::post('editLedger/filteringOrderByParent/',   'accounting\AccLedgerController@filteringOrderByParent');
    Route::post('editLedger/updateLedgerItem/',         'accounting\AccLedgerController@updateItem');
    Route::get('editLedger/{encryptedId}',              'accounting\AccLedgerController@editLedger');
});

//===========================================Voucher Type===========================================
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('viewVoucherType/',        'accounting\AccVoucherTypeController@index');
    Route::get('addVoucherType/',         'accounting\AccVoucherTypeController@addVoucherType');
    Route::post('addVoucherTypeItem/',    'accounting\AccVoucherTypeController@addItem');
});
//==========================================Starts Vouchers==========================================

//====================Insert Vouchers====================
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('addVoucher/',                               'accounting\AccAddVoucherController@addVoucher');
    Route::post('getProjectTypeNLedgersInfo/',              'accounting\AccAddVoucherController@getProjectTypeNLedgersInfo');
    Route::post('getProjectTypeNLedgersInfo1/',             'accounting\AccAddVoucherController@getProjectTypeNLedgersInfo1');
    Route::post('getVoucherCode/',                          'accounting\AccAddVoucherController@getVoucherCode');
    Route::post('getLedgersByBranches/',                    'accounting\AccAddVoucherController@getLedgersByBranches');     //For FT
    Route::post('addVoucherItem/',                          'accounting\AccAddVoucherController@addVoucherItem');
    Route::post('addFTVoucherItem/',                        'accounting\AccAddVoucherController@addFTVoucherItem');     //For FT
});

//===========View, Edit, Delete, Update Vouchers===========
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('viewVoucher/',                              'accounting\AccViewVoucherController@index');
    Route::post('deleteVoucherItem/',                       'accounting\AccViewVoucherController@deleteItem');
    Route::post('deleteFTVoucherItem/',                     'accounting\AccViewVoucherController@deleteFTVoucherItem');
    // Route::post('useVoucherRows/',                       'accounting\AccViewVoucherController@useVoucherRows');
    // Route::get('viewVoucherTest/',                          'accounting\AccViewVoucherController@voucherTest');

//For all Voucher(with out FT)
    Route::get('editVoucher/{encryptedId}',                 'accounting\AccViewVoucherController@editVoucher');
    Route::post('editVoucher/getVoucherCode/',              'accounting\AccAddVoucherController@getVoucherCode');
    Route::post('editVoucher/updateVoucherItem/',           'accounting\AccViewVoucherController@updateVoucherItem');
    Route::post('editVoucher/getProjectTypeNLedgersInfo/',  'accounting\AccAddVoucherController@getProjectTypeNLedgersInfo');

//For FT Voucher
    Route::get('editFTVoucher/{encryptedId}',                       'accounting\AccViewVoucherController@editFTVoucher');
    Route::post('editFTVoucher/getBranchNProjectTypeByProject/',    'accounting\AccOpeningBalanceController@getBranchNProjectTypeByProject');
    Route::post('editFTVoucher/getLedgersByBranches/',              'accounting\AccAddVoucherController@getLedgersByBranches');
    Route::post('editFTVoucher/updateFTVoucherItem/',               'accounting\AccViewVoucherController@updateFTVoucherItem');

    Route::get('printVoucher/{encryptedId}',                        'accounting\AccViewVoucherController@printVoucher');


    //Authorized Vouchers
    Route::get('authorizedVouchersList/',                   'accounting\AccAuthenticateVoucherController@authorizedVouchersList');
    Route::post('unauthenticateVoucherItem/',               'accounting\AccAuthenticateVoucherController@unauthenticateVoucherItem');

    //Unauthorized Vouchers
    Route::get('unauthorizedVouchersList/',                 'accounting\AccAuthenticateVoucherController@unauthorizedVouchersList');
    Route::post('authenticateVoucherItem/',                 'accounting\AccAuthenticateVoucherController@authenticateVoucherItem');


});

// //============================================ Start of Voucher Register Report==========================
// Route::group(['middleware' => ['auth', ]], function(){

// });


// //============================================ End of Voucher Register Report============================

//=============================================Opening Balance=============================================
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('viewOpeningBalance/',                           'accounting\AccOpeningBalanceController@index');
    Route::get('addOpeningBalance/',                            'accounting\AccOpeningBalanceController@addOpeningBalance');
    Route::post('checkPreOpeningBalance/',                      'accounting\AccOpeningBalanceController@checkPreOpeningBalance');
    Route::get('editOpeningBalance/{encryptedId}',              'accounting\AccOpeningBalanceController@editOpeningBalance');
    Route::post('editOpeningBalance/updateOpeningBalanceItem/', 'accounting\AccOpeningBalanceController@updateOpeningBalanceItem');
    Route::post('getBranchNProjectTypeByProject/',              'accounting\AccOpeningBalanceController@getBranchNProjectTypeByProject');
    Route::post('getLedgerHeader/',                             'accounting\AccOpeningBalanceController@getLedgerHeader');
    Route::post('addOpeningBalanceItem/',                       'accounting\AccOpeningBalanceController@addOpeningBalanceItem');

});



//==============================================Auto Voucher Configuration ========================================
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('viewAutoVoucherConfig/',        'accounting\autoVouchers\AccAutoVoucherConfigController@viewAutoVoucherConfig');
    Route::post('addAutoVoucherConfigItem/',    'accounting\autoVouchers\AccAutoVoucherConfigController@addAutoVoucherConfigItem');
});


//=================================================MIS Configuration ===========================================

Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('addMisConfiguration/',           'accounting\autoVouchers\AccMisConfigurationController@addMisConfiguration');
    Route::post('getMisTypeOption/',             'accounting\autoVouchers\AccMisConfigurationController@getMisTypeOption');
    Route::get('viewMisConfiguration/',          'accounting\autoVouchers\AccMisConfigurationController@index');
    Route::post('addMisConfigurationItem/',      'accounting\autoVouchers\AccMisConfigurationController@addMisConfigurationItem');
    Route::post('updateMisConfigurationItem/',   'accounting\autoVouchers\AccMisConfigurationController@updateMisConfigurationItem');
    Route::post('deleteMisConfigurationItem/',   'accounting\autoVouchers\AccMisConfigurationController@deleteMisConfigurationItem');
});


// //==============================================Auto Voucher Configuration ========================================
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('addAutoVoucherConfigForAll/',             'accounting\autoVouchers\AccAutoVoucherConfigForAllController@addAutoVoucherConfigForAll');
    Route::get('viewAutoVoucherConfigForAll/',            'accounting\autoVouchers\AccAutoVoucherConfigForAllController@index');
    Route::post('addAutoVoucherConfigForAllItem/',        'accounting\autoVouchers\AccAutoVoucherConfigForAllController@addAutoVoucherConfigForAllItem');
    Route::post('editAutoVoucherConfigItem/',       'accounting\autoVouchers\AccAutoVoucherConfigForAllController@editAutoVoucherConfigItem');
    Route::post('updateAutoVoucherConfigItem/',     'accounting\autoVouchers\AccAutoVoucherConfigForAllController@updateAutoVoucherConfigItem');
    Route::post('deleteAutoVoucherConfigItem/',     'accounting\autoVouchers\AccAutoVoucherConfigForAllController@deleteAutoVoucherConfigItem');

    Route::post('checkPreMISConfigData/',           'accounting\autoVouchers\AccAutoVoucherConfigForAllController@checkPreMISConfigData');
    Route::post('getMISConfigData/',                'accounting\autoVouchers\AccAutoVoucherConfigForAllController@getMISConfigData');
});

/*
|--------------------------------------------------------------------------
| Accounting: PROCESS : DAY END PROCESS
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['auth','softacc']], function() {
    Route::get('accDayEndProcess/',             'accounting\process\AccDayEndProcessController@accDayEndProcess');
    Route::get('loadAccDayEndProcess/',         'accounting\process\AccDayEndProcessController@loadAccDayEndProcess');
    // Route::get('loadAccDayEndProcessPage/',     'accounting\process\AccDayEndProcessController@loadAccDayEndProcessPage');
    Route::post('addAccDayEndProcessItem/',     'accounting\process\AccDayEndProcessController@addAccDayEndProcessItem');
    Route::post('accDayEndGetYearsOption/',     'accounting\process\AccDayEndProcessController@getYearsOption');
    Route::post('deleteAccDayEndItem/',         'accounting\process\AccDayEndProcessController@deleteAccDayEndItem');
});


/*
|--------------------------------------------------------------------------
| Accounting: PROCESS : Month END PROCESS
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['auth','softacc']], function() {
    Route::get('accMonthEndProcess/',             'accounting\process\AccMonthEndProcessController@accMonthEndProcess');
    Route::get('loadAccMonthEndProcess/',         'accounting\process\AccMonthEndProcessController@loadAccMonthEndProcess');
    Route::post('addAccMonthEndProcessItem/',     'accounting\process\AccMonthEndProcessController@addAccMonthEndProcessItem');
    Route::post('deleteAccMonthEndItem/',         'accounting\process\AccMonthEndProcessController@deleteAccMonthEndItem');
});


/*
|--------------------------------------------------------------------------
| Accounting: PROCESS : Year END PROCESS
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['auth','softacc']], function() {
    Route::get('accYearEndProcess/',             'accounting\process\AccYearEndProcessController@accYearEndProcess');
    Route::get('ajaxBranch/',                    'accounting\process\AccYearEndProcessController@ajaxBranch');
    Route::post('getBranchInfo/',                 'accounting\process\AccYearEndProcessController@getBranchInfo');
    Route::get('loadAccYearEndProcess/',         'accounting\process\AccYearEndProcessController@loadAccYearEndProcess');
    Route::post('addAccYearEndProcessItem/',     'accounting\process\AccYearEndProcessController@addAccYearEndProcessItem');
    Route::post('deleteAccYearEndItem/',         'accounting\process\AccYearEndProcessController@deleteAccYearEndItem');
});



//=================================================Reporting===========================================

Route::group(['middleware' => ['auth','softacc']], function () {

    //============================== Voucher Register Report ========================================
    Route::get('voucherRegisterReport/',          'accounting\reports\AccVoucherRegisterReportController@voucherRegister');

    // Ledger Report
    // Route::get('ledgerReport/',             'accounting\reports\AccLedgerReportController@ledgerReport');


    // incomeStatement Report
    Route::get('incomeStatement/',          'accounting\reports\AccIncomeStatementReportController@incomeStatement');
    // Route::get('incomeStatement/',          'accounting\reports\AccIncomeStatementReportController@index');
    Route::get('incomeStatementReport/',          'accounting\reports\AccIncomeStatementReportController@getRport');
    // balanceSheet Report
    Route::get('balanceSheet/',             'accounting\reports\AccBalanceSheetReportController@balanceSheet');
    // capitalFund Report
    Route::get('capitalFund/',              'accounting\reports\AccCapitalFundReportController@capitalFund');
    // cashFlows Report
    Route::get('cashFlows/',                'accounting\reports\AccCashFlowsController@cashFlows');

    // Route::get('cashBookReport/',           'accounting\reports\AccCashNBankBookReportController@cashBookReport');
    // Route::get('bankBookReport/',           'accounting\reports\AccCashNBankBookReportController@bankBookReport');
    Route::get('cashNbankBookReport/',      'accounting\reports\AccCashNBankBookReportController@cashNbankBookReport');


    // Route::get('balanceSheetTest/',             'accounting\reports\AccBalanceSheetReportTestController@balanceSheet');

    Route::get('branchWiseLedgerCopy1/',                 'accounting\reports\AccBranchWiseLedgerReportControllerCopy1@index');
    Route::get('branchWiseLedgerReportCopy1/',           'accounting\reports\AccBranchWiseLedgerReportControllerCopy1@branchWiseLedgerReport');

    Route::post('getLedgersOptionByBranch/',          'accounting\AccAjaxResponseController@getLedgersOptionByBranch');

    Route::post('getSoftwareDateByBranch/',          'accounting\AccAjaxResponseController@getSoftwareDateByBranch');



    //===================== Branch Wise Ledger Report Copy ======================================================

     Route::get('branchWiseLedger/',                 'accounting\reports\AccBranchWiseLedgerReportController@index');
    Route::get('branchWiseLedgerReport/',           'accounting\reports\AccBranchWiseLedgerReportController@branchWiseLedgerReport');

    // Route::post('getLedgersOptionByBranch/',          'accounting\AccAjaxResponseController@getLedgersOptionByBranch');

    // Route::post('getSoftwareDateByBranch/',          'accounting\AccAjaxResponseController@getSoftwareDateByBranch');


});

//=================================================Email Setting===========================================
// Email Setting
 Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('viewMailSetting/',          'accounting\mailSetting\AccMailSettingController@index');
    Route::post('addMailSetting/',          'accounting\mailSetting\AccMailSettingController@addMailSetting');
    Route::get('updateMailSetting/',        'accounting\mailSetting\AccMailSettingController@balanceSheet');

});


 //====================================Authorize Auto Voucher=======================================

Route::group(['middleware' => ['auth','softacc']], function () {

    //Authorized Vouchers
    Route::get('unauthorizedAutoVouchersList/{moduleId}',                   'accounting\AccAuthenticateAutoVoucherController@index');
    Route::post('authenticateAutoVoucherItem/',               'accounting\AccAuthenticateAutoVoucherController@authenticateAutoVoucherItem');

});

// day end
Route::group(['middleware' => ['auth', ]], function () {
    Route::get('manualDayEnd', 'accounting\process\AccDayEndProcessController@manualDayEnd');
    Route::get('executeDayEnd', 'accounting\process\AccDayEndProcessController@executeDayEnd');
});

// month end
Route::group(['middleware' => ['auth', ]], function () {
    Route::get('manualMonthEnd', 'accounting\process\AccMonthEndProcessController@manualMonthEnd');
    Route::post('getMonthsByFiscalYear', 'accounting\process\AccMonthEndProcessController@getMonthsByFiscalYear');
    Route::get('executeMonthEnd', 'accounting\process\AccMonthEndProcessController@executeMonthEnd');
});
// year end
Route::group(['middleware' => ['auth', ]], function () {
    Route::get('manualYearEnd', 'accounting\process\AccYearEndProcessController@manualYearEnd');
    Route::get('executeYearEnd', 'accounting\process\AccYearEndProcessController@executeYearEnd');
});

//Budget route
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('viewBudget', 'accounting\AccBudgetController@index');
    Route::get('addBudget', 'accounting\AccBudgetController@addBudget');
    Route::post('checkBudgetItem/', 'accounting\AccBudgetController@checkBudgetItem');
    Route::get('loadBudget/', 'accounting\AccBudgetController@loadBudget');
    Route::post('addBudgetItem/', 'accounting\AccBudgetController@addBudgetItem');
    Route::get('editBudget/{id}', 'accounting\AccBudgetController@editBudget');
    Route::post('editBudgetItem/', 'accounting\AccBudgetController@editBudgetItem');
    Route::post ('deleteBudgetItem/', 'accounting\AccBudgetController@deleteItem' );
    Route::post ('approveBudgetItem/', 'accounting\AccBudgetController@approveBudgetItem' );
    Route::post ('getBranchByProjectId/', 'accounting\AccBudgetController@getBranchByProjectId');
});

//Revised Budget

Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('viewRevisedBudget', 'accounting\AccRevisedBudgetController@index');
    Route::get('addRevisedBudget', 'accounting\AccRevisedBudgetController@addRevisedBudget');
    Route::post('checkRevisedBudgetItem', 'accounting\AccRevisedBudgetController@checkRevisedBudgetItem');
    Route::get('loadRevisedBudget/', 'accounting\AccRevisedBudgetController@loadRevisedBudget');
    Route::post('addRevisedBudgetItem/', 'accounting\AccRevisedBudgetController@addRevisedBudgetItem');
    Route::get('editRevisedBudget/{id}', 'accounting\AccRevisedBudgetController@loadRevisedBudget');
    //Route::post('editRevisedBudgetItem', 'accounting\AccRevisedBudgetController@updateRevisedBudget');
    // Route::post('editBudgetItem/', 'accounting\AccBudgetController@editBudgetItem');
     Route::post ('deleteRevisedBudgetItem/', 'accounting\AccRevisedBudgetController@deleteRevisedBudgetItem' );
});
//====================================Ledger Relation====================================
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('viewLedgerRelation/',          'accounting\AccLedgerRelationController@index');
    Route::get('addLedgerRelation/',           'accounting\AccLedgerRelationController@addLedgerRelation');
    Route::post('getLedgersByProject/',      'accounting\AccLedgerRelationController@getLedgersByProject');
    Route::post('addLedgerRelationItem/',      'accounting\AccLedgerRelationController@addItem');
    Route::post('deleteLedgerRelationItem/',   'accounting\AccLedgerRelationController@deleteItem');
    Route::post('editLedgerRelationItem/',     'accounting\AccLedgerRelationController@editItem');
});


//=============================================Voucher Approval=============================================
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::get('selectApprovalAItem/',                        'accounting\AccVoucherApprovalController@viewGroupById');
    Route::get('projectTypeItem/',                            'accounting\AccVoucherApprovalController@projectTypeItem');
    Route::get('branchTypeItem/',                            'accounting\AccVoucherApprovalController@branchTypeItem');
    Route::post('addAccApprovalItem/',                            'accounting\AccAddApprovalController@addAccApprovalItem');
    Route::get('viewApprovalType/',                           'accounting\AccAddApprovalController@index');
    Route::get('viewApprovalSetting/',                            'accounting\AccAddApprovalController@viewApprovalSetting');
    Route::get('editApprovalSettingById/{id}',                            'accounting\AccAddApprovalController@editApprovalSettingById');
    Route::post('updateApprovalSetting/',                            'accounting\AccAddApprovalController@updateApprovalSetting');
    Route::post('deleteAccApprovalItem/',                            'accounting\AccAddApprovalController@deleteAccApprovalItem');
    Route::get('getPositionVerifiedBy/',                            'accounting\AccAddApprovalController@getPositionVerifiedBy');
    Route::get('getPositionReviewedBy/',                            'accounting\AccAddApprovalController@getPositionReviewedBy');
    Route::get('getPositionAprovedBy/',                            'accounting\AccAddApprovalController@getPositionAprovedBy');



});


//=============================================comments settings=============================================
Route::group(['middleware' => ['auth','softacc']], function () {
    Route::post('addAccCommentsItem/',                            'accounting\AccAddApprovalController@addAccCommentsItem');
    Route::post('rejectedAccCommentsItem/',                            'accounting\AccAddApprovalController@rejectedAccCommentsItem');
   Route::post('settingsApprovalProccesing/',                            'accounting\AccAddApprovalController@settingsApprovalProcced');
   Route::post('settingsApprovalReject',                            'accounting\AccAddApprovalController@settingsApprovalReject');
   Route::post('settingsApprovalReviewed',                            'accounting\AccAddApprovalController@settingsApprovalReviewed');
   Route::post('updateApprovalSettingProccesing',                            'accounting\AccAddApprovalController@updateApprovalSettingProccesing');
   Route::post('updateApprovalSettingReject',                            'accounting\AccAddApprovalController@updateApprovalSettingReject');
   Route::post('addFirstStepApproval',                            'accounting\AccAddApprovalController@addFirstStepApproval');
   Route::post('secondStepApproval',                            'accounting\AccAddApprovalController@secondStepApproval');
});




?>
