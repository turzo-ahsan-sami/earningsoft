<?php


	Route::group(['middleware' => ['auth','softacc']], function() {

		Route::get('otsRegisterList/', 'accounting\AccOtsRegisterController@index');
		Route::get('addOtsRegister/', 'accounting\AccOtsRegisterController@addOts');
		Route::post('storeOts/', 'accounting\AccOtsRegisterController@storeOts');
		Route::post('editOts/', 'accounting\AccOtsRegisterController@editOts');
		Route::post('deleteOts/', 'accounting\AccOtsRegisterController@deleteOts');
		Route::post('getEmployeeBaseOnBranch/', 'accounting\AccOtsRegisterController@getEmployeeBaseOnBranch');
		Route::post('getOtsAccountBaseOnBranch/', 'accounting\AccOtsRegisterController@getAccountBaseOnBranch');
		Route::post('getNonMonthlyOtsAccountBaseOnBranch/', 'accounting\AccOtsRegisterController@getNonMonthlyAccountBaseOnBranch');
		Route::post('getOtsAccountBaseOnEmployee/', 'accounting\AccOtsRegisterController@getAccountBaseOnEmployee');
		Route::post('otsGetAccountInfo/', 'accounting\AccOtsRegisterController@getAccountInfo');
		Route::post('otsGetAccountInfoToUpdate/', 'accounting\AccOtsRegisterController@getAccountInfo');


	});

	/*Period*/
	Route::group(['middleware' => ['auth','softacc']], function() {

		Route::get('viewOtsRegisterPeriod/', 'accounting\AccOtsRegisterController@viewPeriod');
        Route::get('addOtsRegisterPeriod/', 'accounting\AccOtsRegisterController@addPeriod');
        Route::post('storeOtsRegisterPeriod/', 'accounting\AccOtsRegisterController@storeOtsRegisterPeriod');
         Route::post('viewOtsRegisterPeriodInfo/', 'accounting\AccOtsRegisterController@viewOtsRegisterPeriodInfo');
          Route::post('editOtsRegisterPeriod/', 'accounting\AccOtsRegisterController@editOtsRegisterPeriod');

        Route::post('deleteOtsRegisterPeriod/', 'accounting\AccOtsRegisterController@deleteOtsRegisterPeriod');
	});


	/*Interest Generation*/
	Route::group(['middleware' => ['auth','softacc']], function() {

		Route::get('viewOtsInterest/', 'accounting\AccOtsRegisterController@viewInterest');
		Route::post('generateOtsInterest/', 'accounting\AccOtsRegisterController@generateInterest');
		Route::post('generateOtsInterestForParticularAccount/', 'accounting\AccOtsRegisterController@generateOtsInterestForParticularAccount');
		Route::post('otsGetInterestInfo/', 'accounting\AccOtsRegisterController@getInterestInfo');
		Route::post('deleteInterest/', 'accounting\AccOtsRegisterController@deleteInterest');

	});

	/*Payment*/
	Route::group(['middleware' => ['auth','softacc']], function() {

		Route::get('viewOtsPayment/', 'accounting\AccOtsRegisterController@viewOtsPayment');
		Route::get('addOtsPayment/', 'accounting\AccOtsRegisterController@addOtsPayment');
		Route::post('storeOtsPayment/', 'accounting\AccOtsRegisterController@storeOtsPayment');
		Route::post('getOtsAccountPaymentData/', 'accounting\AccOtsRegisterController@getAccountPaymentData');

		Route::post('getOtsAccountPaymentInfo/', 'accounting\AccOtsRegisterController@getPaymentInfo');
		Route::post('getOtsAccountPaymentInfoToUpdate/', 'accounting\AccOtsRegisterController@getPaymentInfo');

		Route::post('getOtsAccountPaymentDueInfo/', 'accounting\AccOtsRegisterController@getPaymentDueInfo');

		Route::post('updateOtsAccountPayment/', 'accounting\AccOtsRegisterController@updatePayment');

		Route::post('deleteOtsAccountPayment/', 'accounting\AccOtsRegisterController@deletePayment');

	});


	/*Principal Payment*/
	Route::group(['middleware' => ['auth','softacc']], function() {

		Route::get('viewOtsPrincipalPayment/', 'accounting\AccOtsRegisterController@viewOtsPrincipalPayment');
		Route::get('addOtsPrincipalPayment/', 'accounting\AccOtsRegisterController@addOtsPrincipalPayment');
		Route::post('storeOtsPrincipalPayment/', 'accounting\AccOtsRegisterController@storePrincipalPayment');
		Route::post('deleteOtsAccountPrincipalPayment/', 'accounting\AccOtsRegisterController@deletePrincipalPayment');

		Route::post('filterOtsAccByBranch/', 'accounting\AccOtsRegisterController@getOtsAccBaseOnBranch');
		Route::post('getOtsAccDetails/', 'accounting\AccOtsRegisterController@getAccDetails');
		Route::post('getOtsPrincipalPaymentInfo/', 'accounting\AccOtsRegisterController@getPricipalPaymentInfo');


	});




	//////////////////////////////     OTS Reports         //////////////////////////

	/*OTS Register Balance Report*/
	Route::group(['middleware' => ['auth','softacc']], function() {

		Route::get('viewOtsRegisterBalanceReport/', 'accounting\registerReport\AccOtsRegisterBalanceReportController@index');
		Route::post('otsGetAccBaseOnBranch/', 'accounting\registerReport\AccOtsRegisterBalanceReportController@getAccountBaseOnBranch');

	});

	/*OTS Account Opening Report*/
	Route::group(['middleware' => ['auth','softacc']], function() {

		Route::get('viewOtsAccountOpeningReport/', 'accounting\registerReport\AccOtsRegisterAccountOpeningReportController@index');

	});

	/*OTS Account Closing Report*/
	Route::group(['middleware' => ['auth','softacc']], function() {

		Route::get('viewOtsAccountClosingReport/', 'accounting\registerReport\AccOtsRegisterAccountClosingReportController@index');

	});

	/*OTS Interest Generate Report*/
	Route::group(['middleware' => ['auth','softacc']], function() {

		Route::get('viewOtsInterestGenerateReport/', 'accounting\registerReport\AccOtsInterestGenerateReportController@index');
		Route::post('getOtsInterestBaseOnProjectNbranch/', 'accounting\registerReport\AccOtsInterestGenerateReportController@getInterestBaseOnProjectNbranch');

	});

	/*OTS Interest Payment Report*/
	Route::group(['middleware' => ['auth','softacc']], function() {

		Route::get('viewOtsInterestPaymentReport/', 'accounting\registerReport\AccOtsInterestPaymentReportController@index');
		Route::post('getOtsPaymentBaseOnProjectNbranch/', 'accounting\registerReport\AccOtsInterestPaymentReportController@getPaymentBaseOnProjectNbranch');

	});



	///////////////////////////// FDR Register ////////////////////////////////////


//Register FDR Account
	Route::group(['middleware' => ['auth','softacc']], function() {

		Route::get('fdrRegisterList/', 'accounting\AccFdrRegisterController@index');
		Route::get('addFdrRegister/', 'accounting\AccFdrRegisterController@addFdr');
		Route::post('storefdrRegisterAccount/', 'accounting\AccFdrRegisterController@storeFdr');
		Route::post('editAccFdrAccount/', 'accounting\AccFdrRegisterController@editFdr');
		Route::post('deleteAccFdr/', 'accounting\AccFdrRegisterController@deleteFdr');
		Route::post('accFdrGetBranchLocationBaseOnBank/', 'accounting\AccFdrRegisterController@getLocationBaseOnBank');
		Route::post('fdrGetAccountInfo/', 'accounting\AccFdrRegisterController@getAccountInfo');
		Route::post('fdrGetAccountInfoToUpdate/', 'accounting\AccFdrRegisterController@getAccountInfo');

	});

//FDR Interest
Route::group(['middleware' => ['auth','softacc']], function() {

		Route::get('viewAccFdrInterest/', 'accounting\AccFdrRegisterController@viewInterest');
		Route::get('addAccFdrInterest/', 'accounting\AccFdrRegisterController@addInterest');
		Route::post('storeFdrInterest/', 'accounting\AccFdrRegisterController@storeInterest');
		Route::post('editFdrInterest/', 'accounting\AccFdrRegisterController@editInterest');
		Route::post('deleteFdrInterest/', 'accounting\AccFdrRegisterController@deleteInterest');

		Route::post('getAccFdrFilteredAccount/', 'accounting\AccFdrRegisterController@getFilteredAccount');
		Route::post('getAccFdrInterestInfo/', 'accounting\AccFdrRegisterController@getInterestInfo');
		Route::post('getAccFdrInterestInfoToUpdate/', 'accounting\AccFdrRegisterController@getInterestInfo');

	});
//FDR Receivable
Route::group(['middleware' => ['auth','softacc']], function() {

		Route::get('viewAccFdrReceivable/', 'accounting\AccFdrRegisterController@viewReceivable');
		Route::get('addAccFdrReceivable/', 'accounting\AccFdrRegisterController@addReceivable');
		Route::post('storeFdrReceivable/', 'accounting\AccFdrRegisterController@storeReceivable');
		Route::post('editFdrReceivable/', 'accounting\AccFdrRegisterController@editReceivable');
		Route::post('deleteFdrReceivable/', 'accounting\AccFdrRegisterController@deleteReceivable');

		Route::post('getAccFdrReceivableInfo/', 'accounting\AccFdrRegisterController@getReceivableInfo');
		Route::post('getAccFdrReceivableInfoToUpdate/', 'accounting\AccFdrRegisterController@getReceivableInfo');

	});


//FDR Account Closing
Route::group(['middleware' => ['auth','softacc']], function() {

		Route::get('viewFdrAccountClose/', 'accounting\AccFdrRegisterController@viewAccountClose');
		Route::get('addFdrAccountClose/', 'accounting\AccFdrRegisterController@addAccountClose');
		Route::post('storeFdrAccountClose/', 'accounting\AccFdrRegisterController@storeAccountClose');
		Route::post('editFdrAccountClose/', 'accounting\AccFdrRegisterController@editAccountClose');
		Route::post('deleteFdrAccountClose/', 'accounting\AccFdrRegisterController@deleteAccountClose');

		Route::post('fdrClosingGetAccountInfo/', 'accounting\AccFdrRegisterController@getAccountInfo');
		Route::post('fdrClosingGetAccountInfoToUpdate/', 'accounting\AccFdrRegisterController@getAccountInfo');

	});


////////   FRD Reports   /////////

/*FDR Account Opening Report*/
Route::group(['middleware' => ['auth','softacc']], function() {
	Route::get('viewFdrAccountOpeningReport/', 'accounting\registerReport\AccFdrRegisterAccountOpeningReportController@index');
});

/*FDR Account Closing Report*/
Route::group(['middleware' => ['auth','softacc']], function() {
	Route::get('viewFdrAccountClosingReport/', 'accounting\registerReport\AccFdrRegisterAccountClosingReportController@index');
});

/*FDR Register Report*/
Route::group(['middleware' => ['auth','softacc']], function() {
	Route::get('viewFdrRegisterReport/', 'accounting\registerReport\AccFdrRegisterReportController@index');
});

/*Loan Register Report*/
Route::group(['middleware' => ['auth'/*,''*/]], function() {
	Route::get('viewLoanRegisterReport/', 'accounting\registerReport\AccLoanRegisterReportController@index');
});




////////////////     Loan Register    ////////////////

Route::group(['middleware' => ['auth','softacc']], function() {

		Route::get('viewLoanRegisterAccount/', 'accounting\AccLoanRegisterController@index');
		Route::get('addLoanRegisterAccount/', 'accounting\AccLoanRegisterController@addLoanRegister');
		Route::post('storeLoanRegisterAccount/', 'accounting\AccLoanRegisterController@storeLoanRegister');
		Route::post('editLoanRegisterAccount/', 'accounting\AccLoanRegisterController@editLoanRegister');
		Route::post('deleteLoanRegisterAccount/', 'accounting\AccLoanRegisterController@deleteLoanRegister');
		Route::post('gnrLoanRegisterAccountValidateFirstStep/', 'accounting\AccLoanRegisterController@validateFirstStep');

		Route::post('getLoanRegisterInstallmentInfo/', 'accounting\AccLoanRegisterController@getInstallmentInfo');
		Route::post('getLoanProductBaseOnDonor/', 'accounting\AccLoanRegisterController@getLoanProductBaseOnDonor');
		Route::post('getLoanRegisterInfo/', 'accounting\AccLoanRegisterController@getLoanRegisterInfo');
		Route::post('getLoanRegisterInfoToUpdate/', 'accounting\AccLoanRegisterController@getLoanRegisterInfo');

	});

//// Loan Payment

Route::group(['middleware' => ['auth','softacc']], function() {

		Route::get('viewLoanRegisterPayment/', 'accounting\AccLoanRegisterController@viewPayment');
		Route::get('addLoanRegisterPayment/', 'accounting\AccLoanRegisterController@addPayment');
		Route::post('storeLoanRegisterPayment/', 'accounting\AccLoanRegisterController@storePayment');
		Route::post('editLoanRegisterPayment/', 'accounting\AccLoanRegisterController@editPayment');
		Route::post('deleteLoanRegisterPayment/', 'accounting\AccLoanRegisterController@deletePayment');

		Route::post('loanRegisterOnChangeLoanProduct/', 'accounting\AccLoanRegisterController@onChangeLoanProduct');
		Route::post('loanRegisterFilterOnChangeProjectType/', 'accounting\AccLoanRegisterController@onChangeProjectType');
		Route::post('getLoanProductsBaseOnBranch/', 'accounting\AccLoanRegisterController@getLoanProductsBaseOnBranch');
		Route::post('getCyclesBaseOnPhaseNLoanProduct/', 'accounting\AccLoanRegisterController@onChangePhase');
		Route::post('getLoanAccountNpaymentInfo/', 'accounting\AccLoanRegisterController@getLoanAccNpaymentInfo');
		Route::post('loanRegisterGetRebateData/', 'accounting\AccLoanRegisterController@getRebateData');

		Route::post('getLoanRegisterPaymentInfo/', 'accounting\AccLoanRegisterController@getPaymentInfo');
		Route::post('getLoanRegisterPaymentInfoToUpdate/', 'accounting\AccLoanRegisterController@getPaymentInfo');


	});


// Loan Register Report
//
Route::group(['middleware' => ['auth','softacc']], function() {

		Route::get('loanRegisterInstallmentReport/', 'accounting\registerReport\AccLoanRegisterInstallmentReportController@index');

	});


////////////////

Route::group(['middleware' => ['auth','softacc']], function() {

		Route::get('sendFdrReportMail/', 'accounting\mailSetting\AccMailSettingController@sendMail');

	});
/*******************************
* VAT Register                 *
********************************/
Route::group(['middleware' => ['auth','softacc']], function() {

			Route::get('accVatRegister/', 'accounting\AccVatRegisterController@vatRegister');//vat register add form
			Route::get('accVatBillType/', 'accounting\AccVatRegisterController@vatBillType');//bill type add form
			Route::post('addVatBillType/', 'accounting\AccVatRegisterController@addVatBillType');
      Route::post('vatCalculationFromBillType/', 'accounting\AccVatRegisterController@vatCalculationFromBillType');
			//vatCalculationFromBillType calculates vat from vat register form page(http://120.50.0.141/erp/public/accVatRegister) in bill Type input
		  Route::post('accVatProjectTypeFiltering/', 'accounting\AccVatRegisterController@accVatProjectTypeFiltering');
			//accVatProjectTypeFiltering filters project type from vat register form (http://120.50.0.141/erp/public/accVatRegister) in project input..where we can filter project type by selecting projects
			Route::post('accAddVatregister', 'accounting\AccVatRegisterController@accAddVatregister');
			//accAddVatregister
			Route::post('accVatRegisterBillNoGenerate', 'accounting\AccVatRegisterController@accVatRegisterBillNoGenerate');
      Route::post('accVatRegisterBillNoGenerateProjectType', 'accounting\AccVatRegisterController@accVatRegisterBillNoGenerateProjectType');
			Route::get('accViewVatRegister/', 'accounting\AccVatRegisterController@accViewVatRegister');
			Route::get('accViewVatBillType/', 'accounting\AccVatRegisterController@accViewVatBillType');
			Route::post('accViewVatBillTypeModal/', 'accounting\AccVatRegisterController@accViewVatBillTypeModal');
			Route::post('accViewVatBillTypeModalUpdate/', 'accounting\AccVatRegisterController@accViewVatBillTypeModalUpdate');
			//Route::post('accViewVatBillTypeModalDelete/', 'accounting\AccVatRegisterController@accViewVatBillTypeModalDelete');

			Route::post('accAddVatregisterPayVat/', 'accounting\AccVatRegisterController@accAddVatregisterPayVat');
			Route::post('accAddVatregisterPayVatBillNoGenerate/', 'accounting\AccVatRegisterController@accAddVatregisterPayVatBillNoGenerate');
      Route::post('accViewVatBillTypeUpdateAjax/', 'accounting\AccVatRegisterController@accViewVatBillTypeUpdateAjax');
			Route::post('accViewVatBillTypeGetAjax/', 'accounting\AccVatRegisterController@accViewVatBillTypeGetAjax');
			Route::post('accViewVatBillRegisterFullUpdate/', 'accounting\AccVatRegisterController@accViewVatBillRegisterFullUpdate');
			Route::post('accViewVatBillRegisterViewModal/', 'accounting\AccVatRegisterController@accViewVatBillRegisterViewModal');
      Route::post('accViewVatBillRegisterDeleteModal/', 'accounting\AccVatRegisterController@accViewVatBillRegisterDeleteModal');
			Route::get('accVatRegisterPaymentList/', 'accounting\AccVatRegisterController@accVatRegisterPaymentList');

     Route::post('accVatRegisterGetLedgerAjax/', 'accounting\AccVatRegisterController@accVatRegisterGetLedgerAjax');

		});

/*******************************
 *  End VAT Register           *
 *******************************/



 /*******************************
  *  Starts TAX Register        *
  *******************************/
	Route::group(['middleware' => ['auth','softacc']], function() {
		 Route::get('accTaxBillType/', 'accounting\AccTaxRegisterController@taxBillType');//bill type add form
		 Route::post('addTaxBillType/', 'accounting\AccTaxRegisterController@addTaxBillType');
		 Route::get('accViewTaxBillType/', 'accounting\AccTaxRegisterController@accViewTaxBillType');


    Route::post('accViewTaxBillTypeModal/', 'accounting\AccTaxRegisterController@accViewTaxBillTypeModal');
   	Route::post('accViewTaxBillTypeModalUpdate/', 'accounting\AccTaxRegisterController@accViewTaxBillTypeModalUpdate');
		Route::get('accTaxRegisterForm/', 'accounting\AccTaxRegisterController@accTaxRegisterForm');
		Route::post('taxCalculationFromBillType/', 'accounting\AccTaxRegisterController@taxCalculationFromBillType');
		//this route is defined to calculate tax when project type is being selected and controller returns taxRate in reply
	  Route::post('accTaxProjectTypeFiltering/', 'accounting\AccTaxRegisterController@accTaxProjectTypeFiltering');
		//this route is defined to filter project type when user will select project
		Route::post('accTaxRegisterBillNoGenerate/', 'accounting\AccTaxRegisterController@accTaxRegisterBillNoGenerate');
		//this route is defined to generate tax bill no suffix part
		Route::post('accTaxRegisterBillNoGenerateProjectType/', 'accounting\AccTaxRegisterController@accTaxRegisterBillNoGenerateProjectType');
		//this route is defined to generate tax bill no project type part or midfix part
		Route::post('accAddTaxRegister/', 'accounting\AccTaxRegisterController@accAddTaxRegister');
		//submit form
		Route::get('accViewTaxRegister/', 'accounting\AccTaxRegisterController@accViewTaxRegister');
		//TAX list
		Route::post('accViewTaxRegisterPayTaxBillNoGenerate/', 'accounting\AccTaxRegisterController@accViewTaxRegisterPayTaxBillNoGenerate');

		Route::post('accViewTaxRegisterPayTax/', 'accounting\AccTaxRegisterController@accViewTaxRegisterPayTax');

		Route::post('accViewTaxRegisterViewModal/', 'accounting\AccTaxRegisterController@accViewTaxRegisterViewModal');

		Route::post('accViewTaxRegisterUpdateAjax/', 'accounting\AccTaxRegisterController@accViewTaxRegisterUpdateAjax');

		Route::post('accViewTaxRegisterUpdateGetAjax/', 'accounting\AccTaxRegisterController@accViewTaxRegisterUpdateGetAjax');

	  Route::post('accViewTaxRegisterFullUpdate/', 'accounting\AccTaxRegisterController@accViewTaxRegisterFullUpdate');
		Route::post('accViewTaxRegisterDeleteModal/', 'accounting\AccTaxRegisterController@accViewTaxRegisterDeleteModal');
		Route::get('accTaxRegisterPaymentList/', 'accounting\AccTaxRegisterController@accTaxRegisterPaymentList');

	});




	/*******************************
   *  Ends TAX Register        *
   *******************************/

include 'acc_adv_register.php';
