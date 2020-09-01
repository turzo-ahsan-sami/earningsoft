<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

//prevent browser back button
  /*Route::group(['middleware' => ['revalidate']], function () {
    Route::get('/home', 'HomeController@getIndex');
    Route::get('/',     'HomeController@getIndex');
});*/
	/*Route::get('/', function () {
	    return view('welcome');
	});*/
 /* Auth::routes();
  Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', 'HomeController@getIndex'); //this is for my controller
    Route::get('/',     'HomeController@getIndex');
    });*/




  //    ---------------- inventory --------------
  //inventory product
Route::group(['middleware' => ['auth','softacc']], function() {

    Route::get('addProduct/',            'inventory\InvProductController@addProduct');
    Route::get('viewProduct/',           'inventory\InvProductController@index');
    Route::post('addProductItem/',       'inventory\InvProductController@addItem' );
    Route::post('editProductItem/',      'inventory\InvProductController@editItem' );
    Route::post('deleteProductItem/',    'inventory\InvProductController@deleteItem' );
    Route::post('productDetail/',        'inventory\InvProductController@productDetails' );
    Route::post('ProimageDelete/',       'inventory\InvProductController@ProimageDelete' );
    Route::post('invOnGroupChange/','inventory\InvProductController@onChangeGroup' );
    Route::post('invCategoryChange/','inventory\InvProductController@onChangeCategory' );
    Route::post('invBrandChange/','inventory\InvProductController@onBrandCategory' );
});
//product group
  Route::group(['middleware' => ['auth','softacc']], function() {
   Route::get('addProductGroup/',           'inventory\InvProductGroupController@addProductGroup');
   Route::get('viewProductGroup/',          'inventory\InvProductGroupController@index');
   Route::post ( 'addProductGroupItem/',    'inventory\InvProductGroupController@addItem');
   Route::post ( 'editProductGroupItem/',   'inventory\InvProductGroupController@editItem' );
   Route::post ( 'deleteProductGroupItem/', 'inventory\InvProductGroupController@deleteItem' );
});

//product category group
Route::group(['middleware' => ['auth','softacc']], function() {
   Route::get('addProductCategory/',            'inventory\InvProductCategoryController@addProductCategory');
   Route::get('viewProductCategory/',           'inventory\InvProductCategoryController@index');
   Route::post ( 'addProductCategoryItem/',     'inventory\InvProductCategoryController@addItem' );
   Route::post ( 'editProductCategoryItem/',    'inventory\InvProductCategoryController@editItem' );
   Route::post ( 'deleteProductCategoryItem/',  'inventory\InvProductCategoryController@deleteItem' );
});
//product subcategory group
  Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addProductSubCategory/',           'inventory\InvProductSubCategoryController@addProductSubCategory');
       Route::get('viewProductSubCategory/',          'inventory\InvProductSubCategoryController@index');
       Route::post ( 'addProductSubCategoryItem/',    'inventory\InvProductSubCategoryController@addItem' );
       Route::post ( 'editProductSubCategoryItem/',   'inventory\InvProductSubCategoryController@editItem' );
       Route::post ( 'deleteProductSubCategoryItem/', 'inventory\InvProductSubCategoryController@deleteItem' );
});
//product brand group
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addProductBrand/',           'inventory\InvProductBrandController@addProductBrand');
       Route::get('viewProductBrand/',          'inventory\InvProductBrandController@index');
       Route::post ( 'productGroupIdSend/',     'inventory\InvProductBrandController@productCatagoryChange');
       Route::post ( 'productCategoryIdSend/',  'inventory\InvProductBrandController@productSubCategoryChange');
       Route::post ( 'addProductBrandItem/',    'inventory\InvProductBrandController@addItem' );
       Route::post ( 'editProductBrandItem/',   'inventory\InvProductBrandController@editItem' );
       Route::post ( 'deleteProductBrandItem/', 'inventory\InvProductBrandController@deleteItem' );
});
//product model group
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addProductModel/',             'inventory\InvProductModelController@addProductModel');
       Route::get('viewProductModel/',            'inventory\InvProductModelController@index');
       Route::post ( 'productSubCategoryIdSend/', 'inventory\InvProductModelController@productBrandChange');
       Route::post ( 'addProductModelItem/',      'inventory\InvProductModelController@addItem' );
       Route::post ( 'editProductModelItem/',     'inventory\InvProductModelController@editItem' );
       Route::post ( 'deleteProductModelItem/',   'inventory\InvProductModelController@deleteItem' );
});
//product size group
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addProductSize/',           'inventory\InvProductSizeController@addProductSize');
       Route::get('viewProductSize/',          'inventory\InvProductSizeController@index');
       Route::post ( 'productBrandIdSend/',    'inventory\InvProductSizeController@productModelChange');
       Route::post ( 'addProductSizeItem/',    'inventory\InvProductSizeController@addItem' );
       Route::post ( 'editProductSizeItem/',   'inventory\InvProductSizeController@editItem' );
       Route::post ( 'deleteProductSizeItem/', 'inventory\InvProductSizeController@deleteItem' );
});
//product color group
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addProductColor/',           'inventory\InvProductColorController@addProductColor');
       Route::get('viewProductColor/',          'inventory\InvProductColorController@index');
       Route::post ( 'productModelIdSend/',     'inventory\InvProductColorController@productSizeChange');
       Route::post ( 'addProductColorItem/',    'inventory\InvProductColorController@addItem' );
       Route::post ( 'editProductColorItem/',   'inventory\InvProductColorController@editItem' );
       Route::post ( 'deleteProductColorItem/', 'inventory\InvProductColorController@deleteItem' );
});

//product uom group
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addProductUom/',           'inventory\InvProductUomController@addProductUom');
       Route::get('viewProductUom/',          'inventory\InvProductUomController@index');
       Route::post ( 'addProductUomItem/',    'inventory\InvProductUomController@addItem' );
       Route::post ( 'editProductUomItem/',   'inventory\InvProductUomController@editItem' );
       Route::post ( 'deleteProductUomItem/', 'inventory\InvProductUomController@deleteItem' );
});

//inventory transaction use
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addUse/',                        'inventory\InvTransactionUseController@addUse');
       Route::get('viewUse/',                       'inventory\InvTransactionUseController@index');
       Route::post ( 'addProductUseItem/',          'inventory\InvTransactionUseController@addProductUseItem' );
       Route::post ( 'editProductUseItem/',         'inventory\InvTransactionUseController@editUseItem' );
       Route::post ( 'deleteUseItem/',              'inventory\InvTransactionUseController@deleteItem' );
       Route::post ( 'UseGetProductPrice/',         'inventory\InvTransactionUseController@getProductPrice' );
       Route::post ( 'productUseDetails/',          'inventory\InvTransactionUseController@productUseDetails' );
       Route::post ( 'useEditAppendRows/',          'inventory\InvTransactionUseController@editAppendRows' );
       Route::post ( 'deitedDataUseShow/',          'inventory\InvTransactionUseController@deitedDataUseShow' );
       Route::post ( 'invGetdepOnChangeRoom/',      'inventory\InvTransactionUseController@getdepOnChangeRoom' );
       //Route::post ( 'useDetaisDeleteIdSend/',      'inventory\InvTransactionUseController@useDetaisDeleteIdSend' );
});

//inventory transaction use return
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addUseReturn/',                  'inventory\InvTransactionUseReturnController@addUseReturnF');
       Route::post ( 'addUseReturnItem/',           'inventory\InvTransactionUseReturnController@addItem' );
       Route::get('viewUseReturn/',                 'inventory\InvTransactionUseReturnController@index');
       Route::post ( 'useItemPerUseBillNo/',        'inventory\InvTransactionUseReturnController@useItemPerUseBillNo' );
       Route::post ( 'editMultipleAppenRows/',      'inventory\InvTransactionUseReturnController@editAppnRows' );
       Route::post ( 'editUseReturnItem/',          'inventory\InvTransactionUseReturnController@editItem' );
       Route::post ( 'deleteUseReturnItem/',        'inventory\InvTransactionUseReturnController@deleteItem' );
       Route::post ( 'invUseBillOnCng/',            'inventory\InvTransactionUseReturnController@invUseBillOnCng' );
       Route::post ( 'getProQtyFrmUseDetailsTable/','inventory\InvTransactionUseReturnController@getQtyFroUseDtlTbl' );
       Route::post ( 'deitedDataUseReturnShow/',    'inventory\InvTransactionUseReturnController@deitedDataUseReturnShow' );
});

// inventory employee requestion
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addInvEmpRequi/',             'inventory\InvEmployeeRequisitionController@addInvProEmployeeRequisition');
       Route::post('addProRrnsRequisitionItem/', 'inventory\InvEmployeeRequisitionController@addItem');
       Route::get('viewInvEmpRequiItem/',        'inventory\InvEmployeeRequisitionController@index');
       Route::post('invRequisitionAppendRows/',  'inventory\InvEmployeeRequisitionController@editAppendRows');
       Route::post('editProReqItem/',            'inventory\InvEmployeeRequisitionController@editRequItem');
       Route::post('deleteProEmpReqItem/',       'inventory\InvEmployeeRequisitionController@deleteItem');
       Route::post('invProRequiDetails/',        'inventory\InvEmployeeRequisitionController@requsitionDetails');
       Route::post('invEmpReqOnCngGrp/',         'inventory\InvEmployeeRequisitionController@invEmpReqOnCngGrp');
       Route::post('invEmpReqOnCngCtg/',         'inventory\InvEmployeeRequisitionController@invEmpReqOnCngCtg');
});

// inventory branch requestion
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addInvBrnRequiF/',            'inventory\InvBranchRequisitionController@addInvBrnRequiF');
       Route::post('addBrnRequiItem/',           'inventory\InvBranchRequisitionController@addItem');
       Route::get('viewInvBrnRequiItem/',        'inventory\InvBranchRequisitionController@index');
       Route::post('invBrnReqApnRows/',          'inventory\InvBranchRequisitionController@editAppendRows');
       Route::post('editInvBrnReqItem/',         'inventory\InvBranchRequisitionController@editRequItem');
       Route::post('deleteInvBrnReqItem/',       'inventory\InvBranchRequisitionController@deleteItem');
       Route::post('invBrnRequiDetails/',        'inventory\InvBranchRequisitionController@requisitionDetails');
});


// inventory purchase
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addInvPurchaseRequiF/',         'inventory\transaction\purchase\InvPurchaseController@addInvPurchaseRequiF');
       Route::get('viewInvPurchaseList/',          'inventory\transaction\purchase\InvPurchaseController@index');
       Route::post('addInvPurchaseItem/',          'inventory\transaction\purchase\InvPurchaseController@addItem');
       Route::post('editInvPurchaseItem/',         'inventory\transaction\purchase\InvPurchaseController@editPurchaseItem');
       Route::post('deletInvPurchaseItem/',        'inventory\transaction\purchase\InvPurchaseController@deleteItem');
       Route::post('invPurchaseDetails/',          'inventory\transaction\purchase\InvPurchaseController@purchaseDetails');
       Route::post('purchaseEditAppendRows/',      'inventory\transaction\purchase\InvPurchaseController@editAppendRows');
       Route::post('invPurOnCngSupl/',             'inventory\transaction\purchase\InvPurchaseController@invPurOnCngSupl');
       Route::post('invPurchaseOnCngGrp/',         'inventory\transaction\purchase\InvPurchaseController@invPurchaseOnCngGrp');
       Route::post('invPurchaseOnCngCtg/',         'inventory\transaction\purchase\InvPurchaseController@invPurchaseOnCngCtg');
       Route::post('invPurchaseOnCngSubCtg/',      'inventory\transaction\purchase\InvPurchaseController@invPurchaseOnCngSubCtg');
       Route::post('selPurOptForEditProductRow/',  'inventory\transaction\purchase\InvPurchaseController@productAccSuppId');
       Route::get('sendToSuppler/',                'inventory\transaction\purchase\InvPurchaseController@sendToSuppler');
});

// inventory purchase return
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addInvPurcReqReturnF/',         'inventory\InvPurchaseReturnController@addInvPurchaseReqReturnF');
       Route::get('viewInvPurchaseReturnList/',    'inventory\InvPurchaseReturnController@index');
       Route::post('dataFetchFromPurchaseTable/',  'inventory\InvPurchaseReturnController@dataSendToPurchaseReturnTable');
       Route::post('getTtlValFmPurDtlsTab/',       'inventory\InvPurchaseReturnController@totalamoutofproduct');
       Route::post('addInvPurchaseReturnItem/',    'inventory\InvPurchaseReturnController@addItem');
       Route::post('editInvPurRetItem/',           'inventory\InvPurchaseReturnController@editItem');
       Route::post('deletInvPurRetItem/',          'inventory\InvPurchaseReturnController@deleteItem');
       Route::post('purReturnEditAppendRows/',     'inventory\InvPurchaseReturnController@editPurchaseRetAppnItem');
       Route::post('selectOptForEditProductRow/',  'inventory\InvPurchaseReturnController@selectBillNoProduct');
       Route::post('productQuantityForAppenR/',    'inventory\InvPurchaseReturnController@productQnatityForPreventAppnRow');
});

//Issue
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('viewIssue/', 'inventory\transaction\issue\InvTransactionIssueController@index');
       Route::get('addIssue/', 'inventory\transaction\issue\InvTransactionIssueController@addIssue');
       Route::post('addInvIssueItems/', 'inventory\transaction\issue\InvTransactionIssueController@addItem');
       Route::post('editInvIssue/', 'inventory\transaction\issue\InvTransactionIssueController@editInvIssue');
       Route::post('deleteIssueItem/', 'inventory\transaction\issue\InvTransactionIssueController@deleteIssue');
       Route::post('deitedDataShow/', 'inventory\transaction\issue\InvTransactionIssueController@deitedDataShow');
       Route::post('issueEditAppendRows/', 'inventory\transaction\issue\InvTransactionIssueController@issueEditAppendRows');
      Route::post('viewIssueData/', 'inventory\transaction\issue\InvTransactionIssueController@viewIssueData');
});

//Issue Return
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('viewInvissueReturn/', 'inventory\transaction\issue\InvTransactionReturnIssueController@index');
       Route::get('addInvissueReturn/', 'inventory\transaction\issue\InvTransactionReturnIssueController@addIssueReturn');
       Route::post('addIssueReturnItem/', 'inventory\transaction\issue\InvTransactionReturnIssueController@insertIssueReturn');
       Route::post('editInvIssueReturn/', 'inventory\transaction\issue\InvTransactionReturnIssueController@editInvIssueReturn');
       Route::post('deleteReturnIssue/', 'inventory\transaction\issue\InvTransactionReturnIssueController@deleteReturnIssue');
       Route::post('deitedDataIssueReturnShow/', 'inventory\transaction\issue\InvTransactionReturnIssueController@deitedDataIssueReturnShow');
       Route::post('getQtyFrmIssueDtlTblfContorller/', 'inventory\transaction\issue\InvTransactionReturnIssueController@getQtyFrmIssueDtlTblfContorller');
       Route::post('issueReturnEditAppendRows/', 'inventory\transaction\issue\InvTransactionReturnIssueController@issueReturnEditAppendRows');

});


//Transfer
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('transfer/',              'inventory\transaction\transfer\InvTransactionTransferController@index');
       Route::get('addTransfer/',           'inventory\transaction\transfer\InvTransactionTransferController@addTransfer');
       Route::post('addInvTransferItems/',  'inventory\transaction\transfer\InvTransactionTransferController@addInvTransferItems');
       Route::post('editInvTransfer/',      'inventory\transaction\transfer\InvTransactionTransferController@editInvTransfer');
       Route::post('deleteInvTransferItem/','inventory\transaction\transfer\InvTransactionTransferController@deleteTransfer');
       Route::post('deitedInvTransferDataShow/', 'inventory\transaction\transfer\InvTransactionTransferController@deitedInvTransferDataShow');
       Route::post('trnasferEditAppendRows/', 'inventory\transaction\transfer\InvTransactionTransferController@trnasferEditAppendRows');
});

// inventory Stock quantity Reports
Route::group(['middleware' => ['auth','softacc']], function() {

       Route::post('calculationStockForBrnNhedo/','inventory\reports\InvStockReportController@headofficestockevery');
       Route::post('calculationInvAverageprice/', 'inventory\reports\InvStockReportController@averagecostpricecalculation');
       Route::post('currentYearFscYrFdLd/',       'inventory\reports\InvStockReportController@currentYearFscYrFdLd');

});

// inventory Stock Amount Reports
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('invStockAmountReport/',         'inventory\reports\InvStockAmountReportController@index');
       Route::get('filterStockAmountReport/',      'inventory\reports\InvStockAmountReportController@filterStockAmountReport');
       Route::get('invBranchStockAmountReport/',   'inventory\reports\InvStockAmountReportController@invBranchStockAmountReport');
       Route::get('filterStockAmountReportBranch/','inventory\reports\InvStockAmountReportController@filterStockAmountReportBranch');

});

// inventory Stock quantity Reports
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('invStockReport/',         'inventory\reports\InvStockQuantityReportController@index');
       Route::get('filterStockReport/',      'inventory\reports\InvStockQuantityReportController@filterStockReport');
       Route::get('invBranchStockReport/',   'inventory\reports\InvStockQuantityReportController@invBranchStockReport');
       Route::get('filterStockReportBranch/','inventory\reports\InvStockQuantityReportController@filterStockReportBranch');


});

// inventory Stock Purchase Details Report
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('invPurchaseDetailsReport/','inventory\reports\InvStockPurchaseDetailsReportController@index');
        Route::post('invPurchaseGroupChange/','inventory\reports\InvStockPurchaseDetailsReportController@onChangeGroup');
        Route::post('invCategoryChange/','inventory\reports\InvStockPurchaseDetailsReportController@onChangeCategory');
        Route::post('invSubCategoryChange/','inventory\reports\InvStockPurchaseDetailsReportController@onChangeSubCategory');

});

// inventory Stock Purchase Report
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('invPurchaseReport/','inventory\reports\InvStockPurchaseReportController@index');
        //Route::post('invPurchaseGroupChange/','inventory\reports\InvStockPurchaseReportController@onChangeGroup');
        //Route::post('invCategoryChange/','inventory\reports\InvStockPurchaseReportController@onChangeCategory');
       // Route::post('invSubCategoryChange/','inventory\reports\InvStockPurchaseReportController@onChangeSubCategory');

});



// inventory Use  Report
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('viewUseReport/','inventory\reports\InvUseReportController@index');


});


// inventory Issue  Report
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('viewIssueReport/','inventory\reports\InvIssueReportController@index');


});

// inventory Issue Details  Report
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('viewIssueDetailsReport/','inventory\reports\InvIssueDetailsReportController@index');


});


//Route::get('/home', 'HomeController@index');


//----------------------------------start FAMS ROUTING-------------------------------------
