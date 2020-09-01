<?php
//Requesition
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addRequesition/', 'InvTransactionRequesitionController@addRequesition');
});

//Purchase
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('purchase/', 'InvTransactionPurchaseController@index');
});

//////////////////////         FAMS          /////////////////////////////


Route::group(['middleware' => ['auth','softacc']], function() {


       Route::post ( 'famsAddProductOnChangeGroup/', 'fams\FamsProductController@onChangeGroup' );
       Route::post ( 'famsAddProductOnChangeCategory/', 'fams\FamsProductController@onChangeCategory' );
       Route::post ( 'famsAddProductOnChangeSubCategory/', 'fams\FamsProductController@onChangeSubCategory' );
       Route::post ( 'famsAddProductOnChangeProductType/', 'fams\FamsProductController@onChangeProductType' );
       Route::post ( 'famsAddProductOnChangeProductName/', 'fams\FamsProductController@onChangeProductName' );
       Route::post ( 'famsAddProductOnChangeProject/', 'fams\FamsProductController@onChangeProject' );
       Route::post ( 'famsAddProductOnChangeProjectType/', 'fams\FamsProductController@onChangeProjectType' );

       Route::post ( 'famsGetInfo/', 'fams\FamsProductController@famsGetInfo');

       Route::post('famsGetFilteredProduct/',  'fams\FamsProductController@getFilteredProducts');
       Route::post ( 'validateStep1/', 'fams\FamsProductController@validateStep1' );
       Route::post ( 'validateStep2/', 'fams\FamsProductController@validateStep2' );
       Route::get('viewFamsPtype/',  'fams\FamsProductTypeController@index');
       Route::get('addFamsPtype/',  'fams\FamsProductTypeController@addProductType');
       Route::post('famsAddProductType/',  'fams\FamsProductTypeController@storeProductType');
       Route::post('deleteFamsItem/',  'fams\FamsProductTypeController@deleteProductType');
       Route::post('famsEditProductType/',  'fams\FamsProductTypeController@editProductType');


       //Product Name
       Route::get('viewFamsPname/',  'fams\FamsProductNameController@index');
       Route::get('addFamsPname/',  'fams\FamsProductNameController@addProductName');
       Route::post('storeFamsPname/',  'fams\FamsProductNameController@storeProductName');
       Route::post('deleteFamsPname/',  'fams\FamsProductNameController@deleteProductName');
       Route::post('editFamsPname/',  'fams\FamsProductNameController@editProductName');

       Route::get('famsAdditionalCharge/',  'fams\FamsTransactionAdditionalChargeController@index');
       Route::get('famsAddAdditionalCharge/',  'fams\FamsTransactionAdditionalChargeController@addAdditinalCharge');
       Route::post('famsAdChargeGetBranch/',  'fams\FamsTransactionAdditionalChargeController@getBranch');
       Route::post('famsAdChargeGetProducts/',  'fams\FamsTransactionAdditionalChargeController@getProducts');
       Route::post('famsAdChargeGetAllProducts/',  'fams\FamsTransactionAdditionalChargeController@getAllProducts');

       Route::post('storeAdditinalCharge/',  'fams\FamsTransactionAdditionalChargeController@storeAdditinalCharge');
       Route::post('editFamsAdditionalCharge/',  'fams\FamsTransactionAdditionalChargeController@editAdditinalCharge');
       Route::post('deleteFamsAdditionalCharge/',  'fams\FamsTransactionAdditionalChargeController@deleteAdditinalCharge');

       Route::post('famsOnChangeBranch/',  'fams\FamsTransactionAdditionalChargeController@onChangeBranch');
       Route::post('famsOnChangeBranch2/',  'fams\FamsTransactionAdditionalChargeController@onChangeBranch2');
       Route::post('famsOnChangeGroup/',  'fams\FamsTransactionAdditionalChargeController@onChangeGroup');
       Route::post('famsOnChangeSubCategory/',  'fams\FamsTransactionAdditionalChargeController@onChangeSubCategory');
       Route::post('famsOnChangeSubCategory2/',  'fams\FamsTransactionAdditionalChargeController@onSubCategory2');
       Route::post('famsOnChangeName/',  'fams\FamsTransactionAdditionalChargeController@onChangeName');
       Route::post('famsOnChangeProductType/',  'fams\FamsTransactionAdditionalChargeController@onChangeProductType');


       Route::get('famsAdditionalProduct/',  'fams\FamsAdditionalProductController@index');
       Route::get('addFamsAdditionalProduct/',  'fams\FamsAdditionalProductController@addProduct');
       Route::post('storeFamsAdditionalProduct/',  'fams\FamsAdditionalProductController@storeProduct');
       Route::post('ajaxStoreFamsAdditionalProduct/',  'fams\FamsAdditionalProductController@ajaxStoreProduct');

       Route::post('editFamsAdditionalProduct/',  'fams\FamsAdditionalProductController@editProduct');
       Route::post('deleteFamsAdditionalProduct/',  'fams\FamsAdditionalProductController@deleteProduct');

});


//FAMS Depreciation and Write Off
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('famsDep/',  'fams\FamsDepWriteOffController@index');
       Route::get('famsViewWriteOff/',  'fams\FamsDepWriteOffController@viewWriteOff');
       Route::get('famsWriteOff/',  'fams\FamsDepWriteOffController@writeOff');
       Route::post('famsDelteWriteOff/',  'fams\FamsDepWriteOffController@deleteWriteOff');
       Route::post('generateDep/',  'fams\FamsDepWriteOffController@generateDep');
       Route::post('generateWriteOff/',  'fams\FamsDepWriteOffController@generateWriteOff');
       Route::post('getFamsWriteOffProductInfo/',  'fams\FamsDepWriteOffController@getProductInfo');
       Route::post('deleteFamsDep/',  'fams\FamsDepWriteOffController@deleteDep');
       Route::post('getFamsDepDetails/',  'fams\FamsDepWriteOffController@getDepDetails');
});

//FAMS Sale
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('famsViewSale/',  'fams\FamsTransactionSaleController@index');
       Route::get('famsAddSale/',  'fams\FamsTransactionSaleController@addSale');
       Route::post('famsStoreSale/',  'fams\FamsTransactionSaleController@storeSale');
       Route::post('famsEditSale/',  'fams\FamsTransactionSaleController@editSale');
       Route::post('famsDelteSale/',  'fams\FamsTransactionSaleController@deleteSale');
       Route::post('getFamsSaleProductInfo/',  'fams\FamsTransactionSaleController@getProductInfo');
       Route::post('getFamsSaleBillId/',  'fams\FamsTransactionSaleController@getSaleBillId');
});

//FAMS Transfer
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('famsViewTransfer/',  'fams\FamsTransactionTransferController@index');
       Route::get('famsAddTransfer/',  'fams\FamsTransactionTransferController@addTransfer');
       Route::post('famsStoreTransfer/',  'fams\FamsTransactionTransferController@storeTransfer');
       Route::post('famsEditTransfer/',  'fams\FamsTransactionTransferController@editTransfer');
       Route::post('famsDelteTransfer/',  'fams\FamsTransactionTransferController@deleteTransfer');
       Route::post('famsTransferGetProductInfo/',  'fams\FamsTransactionTransferController@getProductInfo');
});


//Fams Issue
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('famsIssue/',            'fams\FamsTransactionIssueController@index');
       Route::get('famsAddIssue/',         'fams\FamsTransactionIssueController@addIssue');
       Route::post('famsStoreIssue/',      'fams\FamsTransactionIssueController@storeIssue');
       Route::post('famsEditIssue/',       'fams\FamsTransactionIssueController@editIssue');
       Route::post('famsDeleteIssue/',      'fams\FamsTransactionIssueController@deleteIssue');
       Route::post('famsIssueGetProtPrice/','fams\FamsTransactionIssueController@getProductPrice');
       //Route::post('famsOnChangeGroup/',   'fams\FamsTransactionIssueController@onChangeGroup');
       Route::post('famsOnChangeCategory/','fams\FamsTransactionIssueController@onChangeCategory');
       Route::post('famsOnCngSubCtgy/',     'fams\FamsTransactionIssueController@onChangeSubCategory');
       Route::post('famsOnChangeBrand/',    'fams\FamsTransactionIssueController@onChangeBrand');
});


//product Product
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addFamsProduct/',            'fams\FamsProductController@addFamsProduct');
       Route::get('viewFamsProduct/',  ['as' => 'viewFamsProduct','uses' =>'fams\FamsProductController@index']    );
       Route::post ( 'storeFamsProduct/',     'fams\FamsProductController@storeProduct' );
       Route::post ( 'editFamsProduct/',    'fams\FamsProductController@editProduct' );
       //Route::post ( 'deleteFamsProductItem/',  'fams\FamsProductController@deleteItem' );
       Route::post ( 'FamsProductDetail/',      'fams\FamsProductController@productDetails' );
       Route::post ( 'ProimageDelete/',         'fams\FamsProductController@ProimageDelete' );

       Route::post('famsGetProductInfo/',  'fams\FamsProductController@getProductInfo');
       Route::post('famsGetProductInfoForEditModal/',  'fams\FamsProductController@getProductInfoForEditModal');
});

Route::group(['middleware' => ['auth','','']], function() {

       Route::post ( 'deleteFamsProductItem/',  'fams\FamsProductController@deleteItem' );

});

//FAMSproductGroup group
  Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addFamsPGroup/',           'fams\FamsProductGroupController@addFamsPGroup');
       Route::get('viewFramsPgroup/',         'fams\FamsProductGroupController@index');
       Route::post ( 'addFamsPgroupItem/',    'fams\FamsProductGroupController@addItem' );
       Route::post ( 'editFamsPgroupItem/',   'fams\FamsProductGroupController@editItem' );
       Route::post ( 'deleteFamsPgroupItem/', 'fams\FamsProductGroupController@deleteItem' );
});

//FAMS product category
  Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addFamsPctg/',           'fams\FamsProductCategoryController@addFamsPctg');
       Route::get('viewFramsPctg/',         'fams\FamsProductCategoryController@index');
       Route::post ( 'addFamsPctgItem/',    'fams\FamsProductCategoryController@addItem' );
       Route::post ( 'editFamsPctgItem/',   'fams\FamsProductCategoryController@editItem' );
       Route::post ( 'deleteFamsPctgItem/', 'fams\FamsProductCategoryController@deleteItem' );
});

  //FAMS product subcategory
  Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addFamsPsubCtg/',          'fams\FamsPsubCategoryController@addPsubCtg');
       Route::get('viewFamsPsubCtg/',         'fams\FamsPsubCategoryController@index');
       Route::post ( 'addFamsPsubCtgItem/',   'fams\FamsPsubCategoryController@addItem' );
       Route::post ( 'editFamsPsubCtgItem/',  'fams\FamsPsubCategoryController@editItem' );
       Route::post ( 'deleteFamsPsubCtgItem/','fams\FamsPsubCategoryController@deleteItem' );
       Route::post ( 'FamsPgroupIdSend/',     'fams\FamsPsubCategoryController@productCatagoryChange' );
});

  //FAMS product brand
  Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addFamsPbrand/',             'fams\FamsPbrandController@addPbrand');
       Route::get('viewFamsPbrand/',            'fams\FamsPbrandController@index');
       Route::post ( 'storeFamsPbrand/',      'fams\FamsPbrandController@storeBrand' );
       Route::post ( 'editFamsPbrand/',     'fams\FamsPbrandController@editBrand' );
       Route::post ( 'deleteFamsPbrand/',       'fams\FamsPbrandController@deleteBrand' );
       Route::post ( 'PdctCategoryIdSend/',     'fams\FamsPbrandController@productSubCategoryChange' );
});

  //FAMS product model
  Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addFamsPmodel/',           'fams\FamsPmodelController@addPmodel');
       Route::get('viewFamsPmodel/',          'fams\FamsPmodelController@index');
       Route::post ( 'addFamsPmodelItem/',    'fams\FamsPmodelController@addItem' );
       Route::post ( 'editFamsPmodelItem/',   'fams\FamsPmodelController@editItem' );
       Route::post ( 'deleteFamsPmodelItem/', 'fams\FamsPmodelController@deleteItem' );
       Route::post ( 'FamsSubCtgIdSend/',     'fams\FamsPmodelController@productBrandChange' );
});

  //FAMS product size
  Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addFamsPsize/',            'fams\FamsPsizeController@addPsize');
       Route::get('viewFamsPsize/',           'fams\FamsPsizeController@index');
       Route::post ( 'storeFamsPsize/',       'fams\FamsPsizeController@storeSize' );
       Route::post ( 'editFamsPsize/',        'fams\FamsPsizeController@editSize' );
       Route::post ( 'deleteFamsPsize/',      'fams\FamsPsizeController@deleteSize' );
       Route::post ( 'FamsBrandIdSend/',      'fams\FamsPsizeController@productModelChange' );
});

  //FAMS product color
  Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addFamsPcolor/',           'fams\FamsPcolorController@addFamsPcolor');
       Route::get('viewFamsPcolor/',          'fams\FamsPcolorController@index');
       Route::post ( 'storeFamsPcolor/',      'fams\FamsPcolorController@storeColor' );
       Route::post ( 'editFamsPcolor/',       'fams\FamsPcolorController@editColor' );
       Route::post ( 'deleteFamsPcolor/',     'fams\FamsPcolorController@deleteColor' );
       Route::post ( 'FamsModelIdSend/',      'fams\FamsPcolorController@productSizeChange' );
});

  //FAMS product UOM
  Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addFamsPUom/',             'fams\FamsPuomController@addFamsPUom');
       Route::get('viewFamsPUom/',            'fams\FamsPuomController@index');
       Route::post ( 'addFamsPuomItem/',      'fams\FamsPuomController@addItem' );
       Route::post ( 'editFamsPuomItem/',     'fams\FamsPuomController@editItem' );
       Route::post ( 'deleteFamsPuomItem/',   'fams\FamsPuomController@deleteItem' );
});

  //Fams transaction use
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('famsAddUse/',                    'fams\FamsTransactionUseController@addUse');
       Route::get('famsViewUse/',                   'fams\FamsTransactionUseController@index');
       Route::post ( 'famsAddProductUseItem/',      'fams\FamsTransactionUseController@addProductUseItem' );
       Route::post ( 'famsEditProductUseItem/',     'fams\FamsTransactionUseController@editUseItem' );
       Route::post ( 'famsDeleteUseItem/',          'fams\FamsTransactionUseController@deleteItem' );
       Route::post ( 'famsUseGetProductPrice/',     'fams\FamsTransactionUseController@getProductPrice' );
       Route::post ( 'famsProductUseDetails/',      'fams\FamsTransactionUseController@productUseDetails' );
       Route::post ( 'famsUseEditAppendRows/',      'fams\FamsTransactionUseController@editAppendRows' );
       Route::post ( 'famsUseGetFilteredProduct/',      'fams\FamsTransactionUseController@getFilteredProducts' );

       Route::post ( 'famsAddUseGetEmploeeNRoomBaseOnBranch/',      'fams\FamsTransactionUseController@getEmployeeNRoomBaseOnBranch' );

       //Route::post ( 'useDetaisDeleteIdSend/',    'fams\FamsTransactionUseController@useDetaisDeleteIdSend' );
});

//Fams transaction use return
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addFamsUseReturn/',                  'fams\FamsTransactionUseReturnController@addFamsUseReturnF');
       Route::post ( 'addFamsUseReturnItem/',           'fams\FamsTransactionUseReturnController@addItem' );
       Route::get('viewFamsUseReturn/',                 'fams\FamsTransactionUseReturnController@index');
       Route::post ( 'famsUseItemPerUseBillNo/',        'fams\FamsTransactionUseReturnController@famsUseItemPerUseBillNo' );
       Route::post ( 'editMultipleAppenFamsRows/',      'fams\FamsTransactionUseReturnController@editAppnRows' );
       Route::post ( 'editFamsUseReturnItem/',          'fams\FamsTransactionUseReturnController@editItem' );
       Route::post ( 'deleteFamsUseReturnItem/',        'fams\FamsTransactionUseReturnController@deleteItem' );
       Route::post ( 'invFamsUseBillOnCng/',            'fams\FamsTransactionUseReturnController@invFamsUseBillOnCng' );
       Route::post ( 'getProQtyFrmFamsUseDetailsTable/','fams\FamsTransactionUseReturnController@getQtyFrmFnsUseDtlTbl' );
});

//fams Issue Return
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('famsIssueReturned/',         'fams\FamsTransactionReturnIssueController@index');
       Route::get('famsReturnIssue/',           'fams\FamsTransactionReturnIssueController@addIssueReturn');
       Route::post('famsStoreIssueReturn/',     'fams\FamsTransactionReturnIssueController@storeIssueReturn');
       Route::post('famsEditReturnIssue/',      'fams\FamsTransactionReturnIssueController@editReturnIssue');
       Route::post('deleteReturnIssue/',        'fams\FamsTransactionReturnIssueController@deleteReturnIssue');

});

// fams employee requestion
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addFamsEmpRequi/',            'fams\FamsEmployeeRequisitionController@addFamsEmployeeRequisition');
       Route::post('addFamsEmpRequisitionItem/', 'fams\FamsEmployeeRequisitionController@addItem');
       Route::get('viewFamsEmpRequiItem/',       'fams\FamsEmployeeRequisitionController@index');
       Route::post('famsEmpReqAppendRows/',      'fams\FamsEmployeeRequisitionController@editAppendRows');
       Route::post('editFamsEmpReqItem/',        'fams\FamsEmployeeRequisitionController@editRequItem');
       Route::post('deleteFamsEmpReqItem/',      'fams\FamsEmployeeRequisitionController@deleteItem');
       Route::post('famsEmpRequiDetails/',       'fams\FamsEmployeeRequisitionController@requsitionDetails');
       Route::post('famsEmpReqOnCngGrp/',        'fams\FamsEmployeeRequisitionController@famsEmpReqOnCngGrp');
       Route::post('famsEmpReqOnCngCtg/',        'fams\FamsEmployeeRequisitionController@famsEmpReqOnCngCtg');
});

// fams branch requestion
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('addFamsBrnRequiF/',           'fams\FamsBranchRequisitionController@addFamsBrnRequiF');
       Route::post('addFamsBrnRequiItem/',       'fams\FamsBranchRequisitionController@addItem');
       Route::get('viewFamsBrnRequiItem/',       'fams\FamsBranchRequisitionController@index');
       Route::post('famsBrnReqApnRows/',         'fams\FamsBranchRequisitionController@editAppendRows');
       Route::post('editFamsBrnReqItem/',        'fams\FamsBranchRequisitionController@editRequItem');
       Route::post('deleteFamsBrnReqItem/',      'fams\FamsBranchRequisitionController@deleteItem');
       Route::post('famsBrnRequiDetails/',       'fams\FamsBranchRequisitionController@requisitionDetails');
});

//Fams Reports
Route::group(['middleware' => ['auth','softacc']], function() {
      // Asset Register Report
      Route::get('famsAssetRegisterReport/', 'fams\FamsFixedAssetsRegisterReportController@index');
      Route::get('getfamsAssetRegisterReport/', 'fams\FamsFixedAssetsRegisterReportController@getReportOnFilteredData');

      // Fixed Assets Depreciation Report
      Route::get('famsFixedAssetsDepReport/', 'fams\FamsFixedAssetsDepReportController@index');
      Route::post('famsFixedAssetsDepReportOnChngeCategory/', 'fams\FamsFixedAssetsDepReportController@onChangeCategory');

      // Fixed Assets Transfer Report
      Route::get('famsFixedAssetsTransferReport/', 'fams\FamsFixedAssetsTransferReportController@index');

      // Fixed Assets Sales Report
      Route::get('famsFixedAssetsSalesReport/', 'fams\FamsFixedAssetsSalesReportController@index');

      // Fixed Assets Wtite Off Report
      Route::get('famsFixedAssetsWtiteOffReport/', 'fams\FamsFixedAssetsWriteOffReportController@index');

      // Fixed Assets Schedule Report
      Route::get('famsFixedAssetsScheduleReport/', 'fams\FamsFixedAssetsScheduleReportController@index');

      // Fixed Assets Purchase Report
      Route::get('famsFixedAssetsPurchaseReport/', 'fams\FamsFixedAssetsPurchaseReportController@index');

      // Register Use Report
      Route::get('famsRegisterUseReport/', 'fams\FamsRegisterUseReportController@index');

      // Product ID Print Report
      Route::get('famsFixedAssetsIdPrintReport/', 'fams\FamsProductIdPrintReportController@index');

      //Correction
      Route::get('famsUpdateProductDep/', 'fams\FamsProductController@updateProductDep');
      Route::get('famsCorrectProjectTypeAssetNo/', 'fams\FamsProductController@correctProjectTypeAssetNo');
      Route::get('famsCorrectSubCategoryAssetNo/', 'fams\FamsProductController@correctSubCategoryAssetNo');
});


//Fams Product Prefix
Route::group(['middleware' => ['auth','softacc']], function() {
      Route::get('viewFamsProductPrefix/', 'fams\FamsProductPrefixController@index');
      Route::get('addFamsProductPrefix/', 'fams\FamsProductPrefixController@addFamsProductPrefix');

      Route::post('storeFamsProductFrefix/', 'fams\FamsProductPrefixController@storeFamsProductFrefix');
      Route::post('getFamsProductFrefixinfo/', 'fams\FamsProductPrefixController@getFamsProductFrefixinfo');

      Route::post('editFamsProductPrefix/', 'fams\FamsProductPrefixController@editFamsProductPrefix');
      Route::post('deleteFamsProductPrefix/', 'fams\FamsProductPrefixController@deleteFamsProductPrefix');

});





?>
