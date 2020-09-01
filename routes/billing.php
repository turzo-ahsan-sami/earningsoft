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
Route::group(['middleware' => ['auth','softacc'], 'prefix' => 'pos'], function() {
    // products
    Route::get('addProduct/',               'pos\PosProductController@addProduct');
    Route::get('products/',                 'pos\PosProductController@index');
    Route::post('addProductItem/',          'pos\PosProductController@addItem');
    Route::post('editProductItem/',         'pos\PosProductController@editItem');
    Route::post('getProductInfo/',          'pos\PosProductController@getProductInfo');
    Route::post('deleteProductItem/',       'pos\PosProductController@deleteItem');
    Route::post('productDetail/',           'pos\PosProductController@productDetails');

    // customers
    Route::get('addCustomer/',               'pos\PosCustomerController@addCustomer');
    Route::get('customers/',                 'pos\PosCustomerController@index');
    Route::post('addCustomerItem/',          'pos\PosCustomerController@addItem' );
    Route::post('getCustomerInfo/',          'pos\PosCustomerController@getCustomerInfo' );
    Route::post('editCustomerItem/',         'pos\PosCustomerController@editItem' );
    Route::post('deleteCustomerItem/',       'pos\PosCustomerController@deleteItem' );
    Route::post('customerDetail/',           'pos\PosCustomerController@customerDetails' );
    
    // suppliers
    Route::get('addSupplier/',               'pos\PosSupplierController@addSupplier');
    Route::get('suppliers/',                 'pos\PosSupplierController@index');
    Route::post('addSupplierItem/',          'pos\PosSupplierController@addItem' );
    Route::post('getSupplierInfo/',          'pos\PosSupplierController@getSupplierInfo' );
    Route::post('editSupplierItem/',         'pos\PosSupplierController@editItem' );
    Route::post('deleteSupplierItem/',       'pos\PosSupplierController@deleteItem' );
    Route::post('supplierDetail/',           'pos\PosSupplierController@supplierDetails');
    
    
    // Pos Sales
    Route::get('sales/',  'pos\transaction\sales\PosTransactionSaleController@index');
    Route::get('addSale/',  'pos\transaction\sales\PosTransactionSaleController@addSale')->middleware('CheckTransaction'); 
    Route::get('salesAddProductNameOnChangeProductCode/',  'pos\transaction\sales\PosTransactionSaleController@salesAddProductNameOnChangeProductCode');    
    Route::get('getBillNoOnChangeCustomer/',  'pos\transaction\sales\PosTransactionSaleController@getBillNoOnChangeCustomer');    
    Route::post('addPosSalesItem/',  'pos\transaction\sales\PosTransactionSaleController@addPosSalesItem');    
    Route::get('editsalesItem/{id}',  'pos\transaction\sales\PosTransactionSaleController@editsalesItem');
    Route::post('updateSalesItem/',  'pos\transaction\sales\PosTransactionSaleController@updateSalesItem');
    Route::post('deleteSalesItem/',  'pos\transaction\sales\PosTransactionSaleController@deleteSalesItem');   
    Route::get('viewSalesDetailsItemId/{id}',  'pos\transaction\sales\PosTransactionSaleController@viewSalesDetailsItemId');
    Route::get('getLedgerForSales/',  'pos\transaction\sales\PosTransactionSaleController@getLedger');
    Route::get('monthEndCheckForSales',  'pos\transaction\sales\PosTransactionSaleController@monthEndCheck');
    Route::get('checkQty', 'pos\transaction\sales\PosTransactionSaleController@checkQty');
    // Route::post('getSaleBillId/',  'pos\transaction\sales\PosTransactionSaleController@getSaleBillId');

    //Pos Purchase

    Route::get('purchase/',  'pos\transaction\purchase\PosTransactionPurchaseController@index');
    Route::get('posAddPurchase/',  'pos\transaction\purchase\PosTransactionPurchaseController@posAddPurchase')->middleware('CheckTransaction');
    Route::get('purchaseAddProductNameOnChangeProductCode/',  'pos\transaction\purchase\PosTransactionPurchaseController@purchaseAddProductNameOnChangeProductCode');
   
    Route::get('getBillNoOnChangeSupplierId/',  'pos\transaction\purchase\PosTransactionPurchaseController@getBillNoOnChangeSupplierId');
   
    Route::post('posSavePurchaseItem/',  'pos\transaction\purchase\PosTransactionPurchaseController@posSavePurchaseItem');
    Route::get('editPurchaseItem/{id}',  'pos\transaction\purchase\PosTransactionPurchaseController@editPurchaseItem');
    Route::post('posUpdatePurchaseItem/',  'pos\transaction\purchase\PosTransactionPurchaseController@posUpdatePurchaseItem');
    Route::post('deletePurchaseItem/',  'pos\transaction\purchase\PosTransactionPurchaseController@deletePurchaseItem');
    Route::get('viewPurchaseItem/{id}',  'pos\transaction\purchase\PosTransactionPurchaseController@viewPurchaseItem');
    Route::get('getLedgerForPurchase/',  'pos\transaction\purchase\PosTransactionPurchaseController@getLedger');
    Route::get('monthEndCheckForPurchase/',  'pos\transaction\purchase\PosTransactionPurchaseController@monthEndCheck');
    Route::post('addToChartItem/',  'pos\transaction\purchase\PosTransactionPurchaseController@addToChartItem');


    //pos payments

    Route::get('Payments/',  'pos\transaction\payments\PosPaymentsController@index');
    Route::get('addPayment/',  'pos\transaction\payments\PosPaymentsController@addPayment');
    Route::post('addPaymentItem/',  'pos\transaction\payments\PosPaymentsController@addPaymentItem');
    Route::post('paymentDetail/',  'pos\transaction\payments\PosPaymentsController@paymentDetailItem');
    Route::post('getPaymentInfo/',  'pos\transaction\payments\PosPaymentsController@getPaymentInfo');
    Route::post('editPaymentItem/',  'pos\transaction\payments\PosPaymentsController@editPaymentItem');
    Route::post('deletePaymentItem/',  'pos\transaction\payments\PosPaymentsController@deletePaymentItem');

    //pos purchase return
    Route::get('purchaseReturn/',  'pos\transaction\puchaseReturn\PosTransactionPurchaseReturnController@index');
    Route::get('addPurchaseReturn/',  'pos\transaction\puchaseReturn\PosTransactionPurchaseReturnController@addPurchaseReturnItem')->middleware('CheckTransaction');
    Route::get('getBillNoOnChangeSupplier/',  'pos\transaction\puchaseReturn\PosTransactionPurchaseReturnController@getBillNoOnChangeSupplier');
    Route::get('getProductOnChangeSupplier/',  'pos\transaction\puchaseReturn\PosTransactionPurchaseReturnController@getProductOnChangeSupplier');
    Route::post('posSavePurchaseReturnItem/',  'pos\transaction\puchaseReturn\PosTransactionPurchaseReturnController@posSavePurchaseReturnItem');
    Route::post('deletePurchaseReturnItem/',  'pos\transaction\puchaseReturn\PosTransactionPurchaseReturnController@deletePurchaseReturnItem');

    Route::get('editPurchaseReturnItem/{id}',  'pos\transaction\puchaseReturn\PosTransactionPurchaseReturnController@editPurchaseReturnItem');
    Route::get('viewPurchaseReturnItem/{id}',  'pos\transaction\puchaseReturn\PosTransactionPurchaseReturnController@viewPurchaseReturnItem'); 
    Route::post('posupdatePurchaseReturnItem/',  'pos\transaction\puchaseReturn\PosTransactionPurchaseReturnController@posupdatePurchaseReturnItem');
    Route::get('getProjectDetailsForPurchaseReturn/',  'pos\transaction\puchaseReturn\PosTransactionPurchaseReturnController@getProjectDetails');
    Route::get('monthEndCheckForPurchaseReturn/',  'pos\transaction\puchaseReturn\PosTransactionPurchaseReturnController@monthEndCheck');
    

    //pos sales return
    Route::get('salesReturn/',  'pos\transaction\salesReturn\PosTransactionSalesReturnController@index');
    Route::get('addSalesReturn/',  'pos\transaction\salesReturn\PosTransactionSalesReturnController@addSalesReturnItem')->middleware('CheckTransaction');
    Route::get('getBillNoOnChangeCustomerReturn/',  'pos\transaction\salesReturn\PosTransactionSalesReturnController@getBillNoOnChangeCustomerReturn');
    Route::get('getProductOnChangeCustomer/',  'pos\transaction\salesReturn\PosTransactionSalesReturnController@getProductOnChangeCustomer');
    Route::post('posSaveSalesReturnItem/',  'pos\transaction\salesReturn\PosTransactionSalesReturnController@posSaveSalesReturnItem');
    Route::get('viewSalesReturnItem/{id}',  'pos\transaction\salesReturn\PosTransactionSalesReturnController@viewSalesReturnItem');
    Route::get('editSalesReturnItem/{id}',  'pos\transaction\salesReturn\PosTransactionSalesReturnController@editSalesReturnItem');
    Route::post('posUpdateSalesReturnItem/',  'pos\transaction\salesReturn\PosTransactionSalesReturnController@posUpdateSalesReturnItem');
    Route::post('deleteSalesReturnItem/',  'pos\transaction\salesReturn\PosTransactionSalesReturnController@deleteSalesReturnItem');
    Route::get('getProjectDetailsForSalesReturn/',  'pos\transaction\salesReturn\PosTransactionSalesReturnController@getProjectDetails');
    Route::get('monthEndCheckForSalesReturn',  'pos\transaction\salesReturn\PosTransactionSalesReturnController@monthEndCheck');

    // pos Collection
    Route::get('listCollection', 'pos\transaction\collection\PosCollectionController@listCollection');
    Route::get('addCollection', 'pos\transaction\collection\PosCollectionController@addPosCollection');
    Route::get('getSalesBillNo', 'pos\transaction\collection\PosCollectionController@getSalesBillNo');
    Route::get('getSalesInfo', 'pos\transaction\collection\PosCollectionController@getSalesInfo');
    Route::post('insertCollection', 'pos\transaction\collection\PosCollectionController@insertCollection');
    Route::get('getLedgerForCollection/',  'pos\transaction\collection\PosCollectionController@getLedger');
    Route::get('editPosCollection/{id}',  'pos\transaction\collection\PosCollectionController@editPosCollection');
    Route::post('updateCollection/',  'pos\transaction\collection\PosCollectionController@updateCollection');
    Route::post('deleteCollection/',  'pos\transaction\collection\PosCollectionController@deleteCollection');
 
    // pos supplier payment
    Route::get('addSupplierPayment', 'pos\transaction\supplierPayment\PosSupplierPaymentController@addSupplierPayment');
    Route::get('viewSupplierPayment', 'pos\transaction\supplierPayment\PosSupplierPaymentController@viewSupplierPayment');
    Route::get('getPurchaseBillNo', 'pos\transaction\supplierPayment\PosSupplierPaymentController@getPurchaseBillNo');
    Route::get('getPurchaseInfo', 'pos\transaction\supplierPayment\PosSupplierPaymentController@getPurchaseInfo');
    Route::post('insertSupplierPayment', 'pos\transaction\supplierPayment\PosSupplierPaymentController@insertPayment');
    Route::get('getLedgerForSupplierPayment', 'pos\transaction\supplierPayment\PosSupplierPaymentController@getLedger');
    Route::get('editPayment/{id}', 'pos\transaction\supplierPayment\PosSupplierPaymentController@editPayment');
    Route::post('updateSupplierPayment', 'pos\transaction\supplierPayment\PosSupplierPaymentController@updateSupplierPayment');
    Route::post('deletePayment', 'pos\transaction\supplierPayment\PosSupplierPaymentController@deletePayment');

    // voucher setting
    Route::get('voucherSettingList/',  'pos\VoucherSettingController@voucherSettingList');
    Route::get('addVoucherSetting/',  'pos\VoucherSettingController@addVoucherSetting');
    Route::get('editVoucherSetting/',  'pos\VoucherSettingController@editVoucherSetting');
    Route::post('saveVoucherSetting/',  'pos\VoucherSettingController@saveSetting');


    Route::group(['middleware' => 'CheckManufacture'], function () {
       
        // Other Cost
        Route::get('otherCostList', 'pos\OtherCostController@otherCostList');
        Route::get('AddOtherCost', 'pos\OtherCostController@addOtherCost');
        Route::post('insertOtherCost', 'pos\OtherCostController@insertOtherCost');
        Route::get('editOtherCost/{id}', 'pos\OtherCostController@editOtherCost');
        Route::post('updateOtherCost', 'pos\OtherCostController@updateOtherCost');
        Route::post('deleteOtherCost', 'pos\OtherCostController@deleteOtherCost');

        // Cost Sheet
        Route::get('costSheetList', 'pos\CostSheetController@costSheetList');
        Route::get('addCostSheet', 'pos\CostSheetController@addCostSheet');
        Route::post('insertCostSheet', 'pos\CostSheetController@insertCostSheet');
        Route::get('viewCostSheet/{id}', 'pos\CostSheetController@viewCostSheet');
        Route::get('editCostSheet/{id}', 'pos\CostSheetController@editCostSheet');
        Route::post('updateCostSheet', 'pos\CostSheetController@updateCostSheet');
        Route::post('deleteCostSheet', 'pos\CostSheetController@deleteCostSheet');
        Route::get('getProductPrice', 'pos\CostSheetController@getProductPrice');
        
    });

    // order
    Route::get('order/', 'pos\transaction\order\PosTransactionOrderController@index');
    Route::get('posAddOrder/',  'pos\transaction\order\PosTransactionOrderController@posAddOrder')->middleware('CheckTransaction');
    // Route::get('purchaseAddProductNameOnChangeProductCode/',  'pos\transaction\purchase\PosTransactionPurchaseController@purchaseAddProductNameOnChangeProductCode');

    Route::get('OrderProductNameOnChangeProductCode/',  'pos\transaction\order\PosTransactionOrderController@OrderAddProductNameOnChangeProductCode')->middleware('CheckTransaction');
    Route::get('getBillNoOnChangeCustomerId/',  'pos\transaction\order\PosTransactionOrderController@getBillNoOnChangeCustomerId')->middleware('CheckTransaction');
    Route::get('getLedgerForOrder/',  'pos\transaction\order\PosTransactionOrderController@getLedger')->middleware('CheckTransaction');
    Route::get('viewOrderItem/{id}',  'pos\transaction\order\PosTransactionOrderController@viewOrderItem')->middleware('CheckTransaction');
    Route::post('posSaveOrderItem/',  'pos\transaction\order\PosTransactionOrderController@posSaveOrderItem')->middleware('CheckTransaction');
    Route::get('monthEndCheckForOrder/',  'pos\transaction\order\PosTransactionOrderController@monthEndCheck')->middleware('CheckTransaction');
    Route::get('editOrderItem/{id}',  'pos\transaction\order\PosTransactionOrderController@editOrderItem')->middleware('CheckTransaction');
    Route::post('posUpdateorderItem/',  'pos\transaction\order\PosTransactionOrderController@posUpdateorderItem')->middleware('CheckTransaction');
    Route::post('deleteOrderItem/',  'pos\transaction\order\PosTransactionOrderController@deleteOrderItem')->middleware('CheckTransaction');
    Route::post('addToChartOrderItem/',  'pos\transaction\order\PosTransactionOrderController@addToChartOrderItem')->middleware('CheckTransaction');

    //Route::post('posSaveOrderItem/',  'pos\transaction\purchase\PosTransactionPurchaseController@posSaveOrderItem');
    // Route::get('editPurchaseItem/{id}',  'pos\transaction\purchase\PosTransactionPurchaseController@editPurchaseItem');
    // Route::post('posUpdatePurchaseItem/',  'pos\transaction\purchase\PosTransactionPurchaseController@posUpdatePurchaseItem');
    // Route::post('deletePurchaseItem/',  'pos\transaction\purchase\PosTransactionPurchaseController@deletePurchaseItem');
    // Route::get('viewPurchaseItem/{id}',  'pos\transaction\purchase\PosTransactionPurchaseController@viewPurchaseItem');
    // Route::get('getLedgerForPurchase/',  'pos\transaction\purchase\PosTransactionPurchaseController@getLedger');
    // Route::get('monthEndCheckForPurchase/',  'pos\transaction\purchase\PosTransactionPurchaseController@monthEndCheck');
    
});
