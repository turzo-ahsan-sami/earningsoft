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

/* Route::get('/{any}', function ($any) {

  if(Auth::user()->id!=1){
    echo 'dfhfh';
  }
  else{
    echo 'fhfhjghjfgh';
  }


})->where('any', '.*'); */

//prevent browser back button
  Route::group(['middleware' => ['revalidate']], function () {
    // Route::get('/', 'HomeController@getIndex');
    // Route::get('/home', 'HomeController@getIndex');

});
Route::group(['middleware' => ['auth','']], function () {
  // Route::post('/', 'HomeController@');
});
	/*Route::get('/', function () {
	    return view('welcome');
	});*/

  // Auth::routes();

Route::group(['middleware' => 'auth'], function () {
    // Route::get('/home', 'HomeController@getIndex'); //this is for my controller
    // Route::get('/', 'HomeController@getIndex');
});

Route::group(['middleware' => 'auth'], function () {
    // Route::get('/accessNotAllowed', 'HomeController@accessNotAllowed');

});

Route::group(['middleware' => 'auth'], function () {
    // Route::get('/user/password/reset', 'Auth\UserResetPasswordController@showCheckPassowrdFormForUser');
    // Route::post('user/password/check', 'Auth\UserResetPasswordController@checkPasswordBeforeReset');
    // Route::get('/user/password/reset/{token}', 'Auth\UserResetPasswordController@showResetPassowrdFormForUser');
    // Route::post('user/password/reset', 'Auth\UserResetPasswordController@resetUserPassword');
});

include 'inventory.php';

include 'fams.php';

include 'gnr.php';

include 'home.php';

include 'acc_report.php';

include 'acc_register.php';


//Route::get('/home', 'HomeController@index');



//----------------------------------Start ACCOUNT MODULUS---------------------------


include 'accounting.php';




//----------------------------------start FAMS ROUTING-------------------------------------

// include 'hr.php';

/*
|--------------------------------------------------------------------------
| MICRO FINANCE ROUTES
|--------------------------------------------------------------------------
*/

// include 'microfin.php';

// MICRO FINANCE: Field Officer Report (Samity & Component Wise) Route
// include 'mfn_field_report.php';

// include 'atiq.php';

// include 'atiqdailycollection.php';
//
// include 'atiqMemberLedgerReport.php';
//
// include 'atiqLoanStatement.php';
//
// include 'atiqBranchWiseSamityReport.php';
//
// include 'atiqAdvanceDueList.php';
//
// include 'atiqLoanClassificationDMRreport.php';
//
// include 'atiqDailyRecoverableAndCollectionRegister.php';

// include 'mfnReportKhorshed.php';

/*
|--------------------------------------------------------------------------
| POS ROUTES
|--------------------------------------------------------------------------
*/
 include 'pos.php';
include 'billing.php';

/*
|--------------------------------------------------------------------------
| MFN : LOAN
|--------------------------------------------------------------------------
*/
// include 'mfn_loan.php';

/*
|--------------------------------------------------------------------------
| Holiday Routes
|--------------------------------------------------------------------------
*/
// include 'holiday_routes.php';
// include 'mfnReportsRegularNGeneral.php';

/*
|--------------------------------------------------------------------------
| Chart Routes
|--------------------------------------------------------------------------
*/
// include 'mfn_chart.php';
include 'acc_chart.php';
// include 'praReport.php';

/*
|--------------------------------------------------------------------------
| Attendance Routes
|--------------------------------------------------------------------------
*/

// include 'attendence.php';
