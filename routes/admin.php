<?php

Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {

    Route::get('/', 'HomeController@index')->name('home');
    // permissions
    Route::resource('permissions', 'Admin\PermissionsController');
    Route::delete('permissions_mass_destroy', 'Admin\PermissionsController@massDestroy')->name('permissions.mass_destroy');
    // roles
    Route::resource('roles', 'Admin\RolesController');
    Route::delete('roles_mass_destroy', 'Admin\RolesController@massDestroy')->name('roles.mass_destroy');
    // users
    Route::resource('users', 'Admin\UsersController');
    Route::delete('users_mass_destroy', 'Admin\UsersController@massDestroy')->name('users.mass_destroy');
   

    //update Users Profile

    // modules
    Route::resource('modules', 'Admin\ModulesController');
    Route::delete('modules_mass_destroy', 'Admin\ModulesController@massDestroy')->name('modules.mass_destroy');
    // products
    // Route::resource('products', 'Admin\ProductsController');
    // Route::delete('products_mass_destroy', 'Admin\ProductsController@massDestroy')->name('products.mass_destroy');
    Route::resource('plans', 'Admin\PlansController');
    Route::delete('plans_mass_destroy', 'Admin\PlansController@massDestroy')->name('plans.mass_destroy');
    // planTypes
    Route::resource('planTypes', 'Admin\PlanTypesController');
    Route::delete('planTypes_mass_destroy', 'Admin\PlanTypesController@massDestroy')->name('planTypes.mass_destroy');
    // trainings
    Route::resource('trainings', 'Admin\TrainingController');
    Route::delete('trainings_mass_destroy', 'Admin\TrainingController@massDestroy')->name('trainings.mass_destroy');
    // discounts
    Route::resource('discounts', 'Admin\DiscountController');
    Route::delete('discounts_mass_destroy', 'Admin\DiscountController@massDestroy')->name('discounts.mass_destroy');
    // companies
    Route::resource('companies', 'Admin\CompaniesController');
    Route::delete('companies_mass_destroy', 'Admin\CompaniesController@massDestroy')->name('companies.mass_destroy');
    // banners
    Route::resource('banners', 'Admin\BannersController');
    Route::delete('banners_mass_destroy', 'Admin\BannersController@massDestroy')->name('banners.mass_destroy');

      // Feature Section
    Route::resource('featureSection', 'Admin\FeatureSectionController');
    Route::delete('featureSection_mass_destroy', 'Admin\FeatureSectionController@massDestroy')->name('featureSection.mass_destroy');

     // Feature Section
    Route::resource('userReview', 'Admin\UserReviewController');
    Route::delete('userReview_mass_destroy', 'Admin\UserReviewController@massDestroy')->name('userReview.mass_destroy');
});
