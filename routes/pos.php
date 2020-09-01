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

  //    ---------------- pos --------------
  //pos product
Route::group(['middleware' => ['auth','softacc']], function() {
    Route::get('pos/posAddProduct/',            'pos\PosProductController@addProduct');
    Route::get('pos/posViewProduct/',           'pos\PosProductController@index');
    Route::post('pos/posAddProductItem/',       'pos\PosProductController@addItem' );
    Route::post('pos/updateProductinfo/',      'pos\PosProductController@editItem' );
    Route::post('pos/posDeleteProduct/',    'pos\PosProductController@deleteItem' );
    Route::post('pos/posProductDetail/',        'pos\PosProductController@productDetails' );
    Route::post('pos/posProimageDelete/',       'pos\PosProductController@ProimageDelete' );
    Route::post('pos/posOnGroupChange/','pos\PosProductController@onChangeGroup' );
    Route::post('pos/posCategoryChange/','pos\PosProductController@onChangeCategory' );
    Route::post('pos/posBrandChange/','pos\PosProductController@onBrandCategory' );
    Route::post('pos/getProductInfo/','pos\PosProductController@getProductInfo' );
});

//product group
Route::group(['middleware' => ['auth','softacc']], function() {
   Route::get('pos/posAddProductGroup/',         'pos\PosProductGroupController@addProductGroup');
   Route::get('pos/posViewProductGroup/',        'pos\PosProductGroupController@index');
   Route::post('pos/addProductGroupItem/',    'pos\PosProductGroupController@addItem');
   Route::post('pos/posEditProductGroupItem/',   'pos\PosProductGroupController@editItem');
   Route::post('pos/posDeleteProductGroupItem/', 'pos\PosProductGroupController@deleteItem');
});

//product category group
Route::group(['middleware' => ['auth','softacc']], function() {
   Route::get('pos/posAddProductCategory/',            'pos\PosProductCategoryController@addProductCategory');
   Route::get('pos/posViewProductCategory/',           'pos\PosProductCategoryController@index');
   Route::post ('pos/addProductCategoryItem/',     'pos\PosProductCategoryController@addItem' );
   Route::post ('pos/posEditProductCategoryItem/',    'pos\PosProductCategoryController@editItem' );
   Route::post ('pos/posDeleteProductCategoryItem/',  'pos\PosProductCategoryController@deleteItem' );
});
//product subcategory group
  Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('pos/posAddProductSubCategory/',         'pos\PosProductSubCategoryController@addProductSubCategory');
       Route::get('pos/posViewProductSubCategory/',        'pos\PosProductSubCategoryController@index');
       Route::post('pos/addProductSubCategoryItem/',    'pos\PosProductSubCategoryController@addItem');
       Route::post('pos/posEditProductSubCategoryItem/',   'pos\PosProductSubCategoryController@editItem');
       Route::post('pos/posDeleteProductSubCategoryItem/', 'pos\PosProductSubCategoryController@deleteItem');
});
//product brand group
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('pos/posAddProductBrand/',           'pos\PosProductBrandController@addProductBrand');
       Route::get('pos/posViewProductBrand/',          'pos\PosProductBrandController@index');
       Route::post ('pos/productGroupIdSend/',     'pos\PosProductBrandController@productCatagoryChange');
       Route::post ('pos/productCategoryIdSend/',  'pos\PosProductBrandController@productSubCategoryChange');
       Route::post ('pos/addProductBrandItem/',    'pos\PosProductBrandController@addItem' );
       Route::post ('pos/posEditProductBrandItem/',   'pos\PosProductBrandController@editItem' );
       Route::post ('pos/posDeleteProductBrandItem/', 'pos\PosProductBrandController@deleteItem' );
});
//product model group
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('pos/posAddProductModel/',           'pos\PosProductModelController@addProductModel');
       Route::get('pos/posViewProductModel/',          'pos\PosProductModelController@index');
       Route::post('pos/productSubCategoryIdSend/', 'pos\PosProductModelController@productBrandChange');
       Route::post('pos/addProductModelItem/',      'pos\PosProductModelController@addItem');
       Route::post('pos/posEditProductModelItem/',     'pos\PosProductModelController@editItem');
       Route::post('pos/posDeleteProductModelItem/',   'pos\PosProductModelController@deleteItem');
});
//product size group
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('pos/posAddProductSize/',           'pos\PosProductSizeController@addProductSize');
       Route::get('pos/posViewProductSize/',          'pos\PosProductSizeController@index');
       Route::post ( 'pos/posProductBrandIdSend/',    'pos\PosProductSizeController@productModelChange');
        //Route::post ( 'pos/ProductCategoryIdSend/',    'pos\PosProductSizeController@productModelChange');
       Route::post ( 'pos/posAddProductSizeItem/',    'pos\PosProductSizeController@addItem' );
       Route::post ( 'pos/posEditProductSizeItem/',   'pos\PosProductSizeController@editItem' );
       Route::post ( 'pos/posDeleteProductSizeItem/', 'pos\PosProductSizeController@deleteItem' );
});
//product color group
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('pos/posAddProductColor/',           'pos\PosProductColorController@addProductColor');
       Route::get('pos/posViewProductColor/',          'pos\PosProductColorController@index');
       Route::post ( 'pos/productModelIdSend/',     'pos\PosProductColorController@productSizeChange');
       Route::post ( 'pos/posAddProductColorItem/',    'pos\PosProductColorController@addItem' );
       Route::post ( 'pos/posEditProductColorItem/',   'pos\PosProductColorController@editItem' );
       Route::post ( 'pos/posDeleteProductColorItem/', 'pos\PosProductColorController@deleteItem' );
});

//Pos Client
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('pos/posAddClient/',           'pos\PosClientController@addClient');
       Route::get('pos/posViewClient/',          'pos\PosClientController@index');
       Route::post('pos/posGetinfo/',    'pos\PosClientController@getClientData' );
       Route::post('pos/posAddClientItem/',    'pos\PosClientController@addItem' );
       Route::post('pos/posEditClientItem/',   'pos\PosClientController@editItem' );
       Route::post('pos/posDeleteClientItem/', 'pos\PosClientController@deleteItem' );
});

//Pos Product Assaign
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('pos/posAddProductAssaign/',  'pos\PosProdcutAssaignController@addProductAssaign');
       Route::get('pos/posViewProductAssaing/',  'pos\PosProdcutAssaignController@index');
       Route::post('pos/posAssainngOnChangeProductId/',  'pos\PosProdcutAssaignController@onChangeProductId');
       Route::post('pos/posAddProductAssaignItem/',    'pos\PosProdcutAssaignController@addItem' );
       Route::post('pos/posGetProductAssignInfo/',    'pos\PosProdcutAssaignController@getClientData' );
       Route::post('pos/posEditProductAssignItem/',   'pos\PosProdcutAssaignController@editItem' );
       Route::post('pos/posDeleteProductAssign/',   'pos\PosProdcutAssaignController@deleteItem' );
       Route::post('pos/posProductAssignDetail/',   'pos\PosProdcutAssaignController@productAssignDetails' );

});

// pos sales
Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('pos/addPosSalesRequiF/',         'pos\transaction\sales\PosSalesController@addPosSalesRequiF');
       Route::get('pos/viewPosSalesList/',          'pos\transaction\sales\PosSalesController@index');
       Route::post('pos/addPosSalesItem/',          'pos\transaction\sales\PosSalesController@addItem');
       Route::post('pos/editPosSalesItem/',         'pos\transaction\sales\PosSalesController@editPosSalesItem');
       Route::post('pos/deletPosSalesItem/',        'pos\transaction\sales\PosSalesController@deleteItem');
       Route::post('pos/posSalesDetails/',          'pos\transaction\sales\PosSalesController@posSalesDetails');
       Route::post('pos/posSalesEditAppendRows/',   'pos\transaction\sales\PosSalesController@editAppendRows');
       Route::post('pos/posPurOnCngSupl/',          'pos\transaction\sales\PosSalesController@posPurOnCngSupl');
       Route::post('pos/posSalesOnCngGrp/',         'pos\transaction\sales\PosSalesController@posSalesOnCngGrp');
       Route::post('pos/posSalesOnCngCtg/',         'pos\transaction\sales\PosSalesController@posSalesOnCngCtg');
       Route::post('pos/posSalesOnCngSubCtg/',      'pos\transaction\sales\PosSalesController@posSalesOnCngSubCtg');
       Route::post('pos/selPurOptForEditProductRow/','pos\transaction\sales\PosSalesController@productAccSuppId');
       Route::get('pos/sendToSuppler/',             'pos\transaction\sales\PosSalesController@sendToSuppler');
       Route::post('pos/posClientsProductsAssign/',   'pos\transaction\sales\PosSalesController@changeProduct');
       Route::post('pos/posBranchChange/',   'pos\transaction\sales\PosSalesController@posChangeBranch');
       Route::post('pos/addPosSalesItem/',   'pos\transaction\sales\PosSalesController@addItem');
       Route::post('pos/salesEditAppendRows/',   'pos\transaction\sales\PosSalesController@editAppendRows');
       Route::post('pos/editPosProductItem/',   'pos\transaction\sales\PosSalesController@editSalesItem');
       Route::post('pos/salesEditData/',   'pos\transaction\sales\PosSalesController@editdata');
       Route::get('pos/salesInvoicePrint/{id}',         'pos\transaction\sales\PosSalesController@salesInvoicePrint');
});

// pos Service
       Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('pos/addPosServiceRequiF/',         'pos\transaction\service\PosServiceController@addPosServiceRequiF');
       Route::post('pos/posClientsProductsAssignChange/',   'pos\transaction\service\PosServiceController@changeProductpackage');
       Route::post('pos/addPosServiceItem/',          'pos\transaction\service\PosServiceController@addItem');
       Route::post('pos/posClientsProducts/',   'pos\transaction\service\PosServiceController@changeProductId');
       Route::post('pos/posServiceGroupChange/',   'pos\transaction\service\PosServiceController@onChangeGroup');
       Route::post('pos/posServiceCategoryChange/',   'pos\transaction\service\PosServiceController@onChangeCategory');
       Route::post('pos/posServiceSubCategoryChange/',   'pos\transaction\service\PosServiceController@onChangeSubCategory');
       Route::post('pos/editPosServiceProductItem/',   'pos\transaction\service\PosServiceController@editServiceItem');
       Route::post('pos/posProductChange/',   'pos\transaction\service\PosServiceController@changeProductPrice');
       Route::get('pos/posViewList/',   'pos\transaction\service\PosServiceController@index');
       Route::get('pos/serviceInvoicePrint/{id}',         'pos\transaction\service\PosServiceController@serviceInvoicePrint');
});


// pos collection
Route::group(['middleware' => ['auth','softacc']], function() {
      Route::get('pos/addPosCollection/',         'pos\transaction\collection\PosCollectionController@addPosCollection');
      Route::post('pos/insertPosCollection/',         'pos\transaction\collection\PosCollectionController@insertPosCollection');
      Route::get('pos/viewPosCollectionList/',         'pos\transaction\collection\PosCollectionController@index');
      Route::post('pos/posClientsCollectionInfo/',         'pos\transaction\collection\PosCollectionController@posClientsCollection');
      Route::post('pos/posClientsBillCollectionInfo/',         'pos\transaction\collection\PosCollectionController@posClientsBillCollectionInfo');
      Route::post('pos/deleteCollection/',         'pos\transaction\collection\PosCollectionController@deleteCollection');
      Route::post('pos/collectionInfoByCollectionId/',         'pos\transaction\collection\PosCollectionController@collectionInfoByCollectionId');
      Route::post('pos/posEditCollectionItem/',         'pos\transaction\collection\PosCollectionController@editCollectionItem');

});

// pos day month end
Route::group(['middleware' => ['auth','softacc']], function() {
      Route::get('pos/posDayEndList/',        'pos\process\PosProcessDayEndController@index');
      Route::post('pos/storeDayEnd/',         'pos\process\PosProcessDayEndController@storeDayEnd');
      Route::post('pos/deletePosDayEnd/',     'pos\process\PosProcessDayEndController@deletePosDayEnd');
      Route::post('pos/posDayEndGetYears/',    'pos\process\PosProcessDayEndController@getYears');
});

Route::group(['middleware' => ['auth','softacc']], function() {
      Route::get('pos/posMonthEndList/', 'pos\process\PosProcessMonthEndController@index');
      Route::post('pos/storeMonthEnd/',  'pos\process\PosProcessMonthEndController@storeMonthEnd');
      Route::post('pos/deletePosMonthEnd/','pos\process\PosProcessMonthEndController@deletePosMonthEnd');
      Route::post('pos/posMonthEndGetYears/', 'pos\process\PosProcessMonthEndController@getYears');

});
//Billing Report//
Route::group(['middleware' => ['auth','softacc']], function() {
      
       Route::get('pos/posSalesNServiceReport/', 'pos\report\PosSalesNServiceReportController@index');
       Route::get('pos/getBillNoCustomer/', 'pos\report\PosSalesNServiceReportController@getBillNoCustomer');
       Route::get('pos/posSalesReturnServiceReport/', 'pos\report\PosSalesReturnServiceReportController@index');
       Route::get('pos/getSellsBillNoCustomer/', 'pos\report\PosSalesReturnServiceReportController@getSellsBillNoCustomer');
       Route::get('pos/posSalesWiseProfitReport/', 'pos\report\PosSalesProfitReportController@salesWiseProfitReport');
       Route::get('pos/posInvoiceWiseProfitReport/', 'pos\report\PosSalesProfitReportController@invoiceWiseProfitReport');
       
       Route::get('pos/posPurchaseReport/', 'pos\report\PosPurchaseReportController@index');
       Route::get('pos/posPurchaseGetReport/', 'pos\report\PosPurchaseReportController@getPurchaseReport');
       Route::get('pos/getBillNoOnChangeSupplierPurchase/',  'pos\report\PosPurchaseReportController@getBillNoOnChangeSupplierPurchase');
       Route::get('pos/getProductOnChangeSupplierPurchase/',  'pos\report\PosPurchaseReportController@getProductOnChangeSupplierPurchase');
       Route::get('pos/posPurchaseReturnReport/', 'pos\report\PosPurchaseReturnReportController@index');
       Route::get('pos/posPurchaseReturnGetReport/', 'pos\report\PosPurchaseReturnReportController@getPurchaseReturnReport');
});

Route::group(['middleware' => ['auth','softacc']], function() {
      Route::get('pos/posCollectionReport/', 'pos\report\PosCollectionReportController@index');
      Route::post('pos/salesBillNoFillter/', 'pos\report\PosCollectionReportController@salesBillNoFillter');
});
Route::group(['middleware' => ['auth','softacc']], function() {
      Route::get('pos/posCollectionClientReport/', 'pos\report\PosCollectionClientReportController@index');
      Route::post('pos/salesBillNoFillter/', 'pos\report\PosCollectionClientReportController@salesBillNoFillter');
});

Route::group(['middleware' => ['auth','softacc']], function() {
       Route::get('pos/posHrEmployeeList/', 'pos\employee\PosHrEmployeeController@index');
       Route::get('pos/posAddHrEmployee/', 'pos\employee\PosHrEmployeeController@posAddHrEmployee');
       Route::post('pos/preDistricFiltering/', 'pos\employee\PosHrEmployeeController@preDistricFiltering');
       Route::post('pos/preUpzillaDataFiltering/', 'pos\employee\PosHrEmployeeController@preUpzillaDataFiltering');
       Route::post('pos/preUnionDataFiltering/', 'pos\employee\PosHrEmployeeController@preUnionDataFiltering');
       Route::post('pos/projectTypeFiltering/', 'pos\employee\PosHrEmployeeController@projectTypeFiltering');
       Route::post('pos/storeEmployeeItem/', 'pos\employee\PosHrEmployeeController@addItem');
       Route::post('pos/employeeGetInfo/', 'pos\employee\PosHrEmployeeController@employeeGetInfo');
       Route::post('pos/updateHrEployeeInfo/', 'pos\employee\PosHrEmployeeController@updateHrEployeeInfo');
       Route::post('pos/hrDeleteEmployee/', 'pos\employee\PosHrEmployeeController@hrDeleteEmployee');
       Route::get('pos/hrDetailsEmployee/{employeeId}', 'pos\employee\PosHrEmployeeController@hrDetailsEmployee');
       Route::post('pos/branchFilteringByProject/', 'pos\employee\PosHrEmployeeController@branchFilteringByProject');
});

//Route::get('/home', 'HomeController@index');
