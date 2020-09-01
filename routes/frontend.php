<?php

Route::group(['middleware' => [], 'prefix' => '', 'as' => ''], function () {
    // homepage
    Route::get('/', 'FrontEnd\HomepageController@index')->name('homepage');
    // product page
    Route::get('products', 'FrontEnd\ProductPageController@index')->name('productpage');
    // pricing page
    Route::get('pricing', 'FrontEnd\PricingPageController@index')->name('pricingpage');

});

Route::group(['middleware' => [], 'prefix' => '', 'as' => ''], function () {
    // customer trial active
    Route::get('package/trial/{planId}', 'FrontEnd\CustomerController@activateTrial')->name('activateTrial');
    // customer buy active
    Route::get('package/buy/{planId}', 'FrontEnd\CustomerController@activatePlan')->name('activatePlan');
     // customer payment active
    Route::post('customer/customerPayment/', 'FrontEnd\CustomerController@customerPayment');

    Route::get('customer/customerPaymentFail','FrontEnd\CustomerController@customerPaymentFail')->name('customer/customerPaymentFail');

    Route::get('customer/getTraining', 'FrontEnd\CustomerController@getTraining');
    Route::get('customer/getDiscount', 'FrontEnd\CustomerController@getDiscount');

    // customer sign up trial
    Route::get('customer/signup/{planId}/trial', 'FrontEnd\CustomerController@customerSignup')->name('trial');
    
    // customer sign up buy
    Route::get('customer/signupBuy/{planId}/buy', 'FrontEnd\CustomerController@customerSignupBuy')->name('buy');
    // customer subscription
    Route::post('customer/store', 'FrontEnd\CustomerController@customerSubscription');
    Route::post('customer/successPayment', 'FrontEnd\CustomerController@successPayment');

    //Buy customer subscription
    Route::post('customer/storeBuy', 'FrontEnd\CustomerController@customerSubscriptionBuy');
    // customer sign in
    Route::get('customer/signin', 'FrontEnd\CustomerController@customerLogin');
    Route::get('customer/checkout/{planId}', 'FrontEnd\CustomerController@checkout');
    // customer sign in to dashboard
    Route::post('customer/login-redirect', 'FrontEnd\CustomerController@customerLoginToDashboard');

});

Route::group(['middleware' => ['auth', 'softacc'], 'prefix' => '', 'as' => ''], function () {
    //customer-business details
    Route::get('customer/business-setup', 'FrontEnd\CustomerController@businessSetup');
    // finish business setup and to dashboard
    Route::post('customer/all-set', 'FrontEnd\CustomerController@finishSetup');
    // customer dashboard
    Route::get('dashboard', 'HomeController@dashboard');
   
});
