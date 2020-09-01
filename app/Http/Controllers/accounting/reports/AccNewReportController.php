<?php

/**************************************************
* Programmer: Himel Dey                           *
*  Ambala IT                                      *
*  Topic: OTS Statement Report                    *
**************************************************/

namespace App\Http\Controllers\accounting\reports;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\accounting\AddVoucher;
use App\accounting\AddVoucherDetails;
use App\accounting\AddLedger;
use App\accounting\AddVoucherType;
use App\gnr\GnrProject;
use App\gnr\GnrProjectType;
use App\gnr\GnrBranch;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;
use \stdClass;
use Carbon\Carbon;
use App\Traits\GetSoftwareDate;

// use App\Http\Controllers\accounting\AccAjaxResponseController;

use App\Http\Controllers\accounting\Accounting;



class AccNewReportController extends Controller
{
    public function __construct() {
      // $this->Accounting = new AccAjaxResponseController;
      //  $this->Accounting = new Accounting;
    }
    public function index()
    {
      $userBranchId= Auth::user()->branchId;
      $reportFilter=array(
        'userBranchId' => $userBranchId
      );

      return view('accounting/reports/New/newReportFiltering',$reportFilter);
    }//120.50.0.141\erp\resources\views\accounting\reports\New\newReportFiltering.Blade.php

}		//End AccLedgerReportsController
