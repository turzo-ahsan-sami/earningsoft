<?php
	/*
	|--------------------------------------------------------------------------
	| ACCOUNTING: REPORT : TRIALBALANCE , RECEIPTPAYMENTSTATEMENT
	|--------------------------------------------------------------------------
	*/
	Route::group(['middleware' => ['auth','softacc']], function() {
		Route::get('trialBalanceReport/', 'accounting\reports\AccTrialBalanceReportController@index');
		Route::get('receiptPaymentStatement/', 'accounting\reports\AccReceiptPaymentStatementController@index');
	});

	/*
	|--------------------------------------------------------------------------
	| ACCOUNTING: REPORT : OTS ACCOUNT STATEMENT
	|--------------------------------------------------------------------------
	*/
	Route::group(['middleware' => ['auth','softacc']], function() {
    Route::get('otsAccountStatement/', 'accounting\reports\OtsAccountStatementController@index');//OTS Account StatementReport
		Route::get('otsAccountStatementReportLowerPart/', 'accounting\reports\OtsAccountStatementController@reportingTable');//OTS Account StatementReport
		Route::post('otsAccountStatementReportAccountingList/', 'accounting\reports\OtsAccountStatementController@AccountNumber');//OTS Account StatementReport
    Route::get('newReportFilteringTemplate/', 'accounting\reports\AccNewReportController@index');//new
	});



	/*
	|--------------------------------------------------------------------------
	| ACCOUNTING: REPORT : Cash Flow STATEMENT
	|--------------------------------------------------------------------------
	*/
	Route::group(['middleware' => ['auth','softacc']], function() {
		Route::get('cashFlowStatement/', 'accounting\reports\AccCashFlowStatementController@index');//new
		Route::post('cashFlowStatementProjectType/', 'accounting\reports\AccCashFlowStatementController@cashFlowStatementProjectType');
		Route::get('cashFlowStatementLoadTable/', 'accounting\reports\AccCashFlowStatementController@cashFlowStatementLoadTable');
	});

	/*
	|--------------------------------------------------------------------------
	| ACCOUNTING: REPORT : Trial Balance
	|--------------------------------------------------------------------------
	*/
	Route::group(['middleware' => ['auth','softacc']], function() {
		Route::get('trialBalance/', 'accounting\reports\TrialBalanceReportController@index');//new
		Route::post('trialBalanceProjectType/', 'accounting\reports\TrialBalanceReportController@trialBalanceProjectType');
		Route::get('trialBalanceLoadReport/', 'accounting\reports\TrialBalanceReportController@trialBalanceLoadReport');
	});

	/*
	|--------------------------------------------------------------------------
	| ACCOUNTING: REPORT : Balance sheet
	|--------------------------------------------------------------------------
	*/
	Route::group(['middleware' => ['auth']], function() {
		Route::get('financialPositionStatement/', 'accounting\reports\FinancialPositionStatementController@index');//new
		Route::post('financialStatementProjectType/', 'accounting\reports\FinancialPositionStatementController@getProjectType');
		Route::get('viewFinancialStatementTable/', 'accounting\reports\FinancialPositionStatementController@financialStatementLoadReport');
	});

	/*
	|--------------------------------------------------------------------------
	| ACCOUNTING: REPORT : income expense statement
	|--------------------------------------------------------------------------
	*/
	Route::group(['middleware' => ['auth']], function() {
		Route::get('comprehensiveIncomeStatement/', 'accounting\reports\ComprehensiveIncomeStatementController@index');//new
		Route::post('comprehensiveIncomeProjectType/', 'accounting\reports\ComprehensiveIncomeStatementController@getProjectType');
		Route::get('comprehensiveIncomeLoadReport/', 'accounting\reports\ComprehensiveIncomeStatementController@comprehensiveIncomeLoadReport');
	});

	/*
	|--------------------------------------------------------------------------
	| ACCOUNTING: REPORT : Receipt payment statement
	|--------------------------------------------------------------------------
	*/
	Route::group(['middleware' => ['auth']], function() {
		Route::get('receiptPaymentReport/', 'accounting\reports\ReceiptPaymentReportController@index');//new
		Route::post('receiptPaymentProjectType/', 'accounting\reports\ReceiptPaymentReportController@getProjectType');
		Route::get('receiptPaymentLoadReport/', 'accounting\reports\ReceiptPaymentReportController@receiptPaymentLoadReport');
	});

	////// projectwise data ajax routes //////////
	Route::post('getProjectTypesNBranches/', 'accounting\reports\LedgerReportController@getProjectTypesNBranches');
	Route::post('getChildrenLedgers/', 'accounting\reports\LedgerReportController@getChildrenLedgers');
	////////////////
	/*
	|--------------------------------------------------------------------------
	| ACCOUNTING: REPORT : New Ledger Report
	|--------------------------------------------------------------------------
	*/
	Route::group(['middleware' => ['auth','softacc']], function() {
		Route::get('ledgerReport/', 'accounting\reports\LedgerReportController@index');
		// Route::post('getProjectTypesNBranches/', 'accounting\reports\LedgerReportController@getProjectTypesNBranches');
		// Route::post('getChildrenLedgers/', 'accounting\reports\LedgerReportController@getChildrenLedgers');
		Route::get('loadLedgerReport/', 'accounting\reports\LedgerReportController@loadLedgerReport');
	});

	/*
	|--------------------------------------------------------------------------
	| ACCOUNTING: REPORT : New cash book Report
	|--------------------------------------------------------------------------
	*/
	Route::group(['middleware' => ['auth','softacc']], function() {
		Route::get('cashBookReport/', 'accounting\reports\CashBookReportController@index');
		// Route::post('getProjectTypesNBranches/', 'accounting\reports\CashBookReportController@getProjectTypesNBranches');
		// Route::post('getChildrenLedgers/', 'accounting\reports\CashBookReportController@getChildrenLedgers');
		Route::get('loadCashBookReport/', 'accounting\reports\CashBookReportController@loadCashBookReport');
	});

	/*
	|--------------------------------------------------------------------------
	| ACCOUNTING: REPORT : New bank book Report
	|--------------------------------------------------------------------------
	*/
	Route::group(['middleware' => ['auth','softacc']], function() {
		Route::get('bankBookReport/', 'accounting\reports\BankBookReportController@index');
		// Route::post('getProjectTypesNBranches/', 'accounting\reports\BankBookReportController@getProjectTypesNBranches');
		Route::post('getChildrenBankLedgers/', 'accounting\reports\BankBookReportController@getChildrenBankLedgers');
		Route::get('loadBankBookReport/', 'accounting\reports\BankBookReportController@loadBankBookReport');
	});



		/*
		|--------------------------------------------------------------------------
		| ACCOUNTING: REPORT : VAT Register Report
		|--------------------------------------------------------------------------
		*/
		Route::group(['middleware' => ['auth','softacc']], function() {
		Route::get('vatRegisterReport/', 'accounting\reports\VatRegisterReportController@vatRegisterReport');//new
		Route::get('vatRegisterReportLowerPart/', 'accounting\reports\VatRegisterReportController@vatRegisterReportLowerPart');
		Route::post('vatRegisterReportProjectTypeAjax/', 'accounting\reports\VatRegisterReportController@vatRegisterReportProjectTypeAjax');

			});



			/*
			|--------------------------------------------------------------------------
			| ACCOUNTING: REPORT : Advance Payment Report
			|--------------------------------------------------------------------------
			*/
  	Route::group(['middleware' => ['auth','softacc']], function() {
  	Route::get('advancePaymentReport/', 'accounting\reports\AccAdvancePaymentReportController@advancePaymentReport');
		Route::get('advancePaymentReportLowerPart/', 'accounting\reports\AccAdvancePaymentReportController@advancePaymentReportLowerPart');
		Route::post('advancePaymentReportProjectTypeAjax/', 'accounting\reports\AccAdvancePaymentReportController@advancePaymentReportProjectTypeAjax');

	  });
