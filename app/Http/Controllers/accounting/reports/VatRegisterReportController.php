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



class VatRegisterReportController extends Controller
{


    public function __construct() {

        // $this->Accounting = new AccAjaxResponseController;
        $this->Accounting = new Accounting;
    }
    public function vatRegisterReport()
    {
      $userBranchId = Auth::user()->branchId;

      $supplierList =   array('null'=>'--Select Supplier--')  +DB::table('gnr_supplier')->pluck('name','id')->toarray();
      $projectList =   array('null'=>'--Select Project--')  +DB::table('gnr_project')->pluck('name','id')->toarray();

      $projectType= array('--Select Project Type--');
                        //    $this->Accounting->getOTSAccount();
      $filteringArr=array(
                    'userBranchId'          => $userBranchId,

                    'supplierList'          => $supplierList,
                    'projectList'           => $projectList,
                    'projectType'           => $projectType



                     );

    return view('accounting/reports/VATRegister/vatRegisterReport',$filteringArr);
   }

    public function vatRegisterReportLowerPart(Request $request)
   {
       $fromDate=Carbon::parse($request->filStartDate)->format('Y-m-d');
       $toDate=Carbon::parse($request->filEndDate)->format('Y-m-d');
       $supplier=$request->filSupplier;
       $projectId=$request->filProject;
       $projectTypeId=$request->filProjectType;

       $generateDates = DB::table('acc_vat_generate')
                            ->select('billDate')
                            ->where('softDel',0)
                            ->where('supplierIdFk',$supplier)
                            ->where('projectId_Fk',$request->filProject)
                            ->where('project_TypeId_Fk',$request->filProjectType)
                            ->where(function ($query) use ($fromDate,$toDate){
                                $query->where('billDate','>=',$fromDate)
                                 ->where('billDate','<=',$toDate);
                              })
                            ->get();

      $paymentDates = DB::table('acc_vat_payment')
                      ->select('paymentDate')
                      ->where('supplierIdFk',$supplier)
                      ->where('projectId_Fk',$request->filProject)
                      ->where('project_TypeId_Fk',$request->filProjectType)
                      ->where(function ($query) use ($fromDate,$toDate){
                          $query->where('paymentDate','>=',$fromDate)
                                ->where('paymentDate','<=',$toDate);
                      })
                      ->get();

     $generateAndPaymentDates=array();
     foreach($generateDates as $generateDate)
        {
          array_push($generateAndPaymentDates,$generateDate->billDate);
        }

        foreach($paymentDates as $paymentDate)
           {
             array_push($generateAndPaymentDates,$paymentDate->paymentDate);
           }
     sort($generateAndPaymentDates);

     $vatGenerates= DB::table('acc_vat_generate as t1')
                        ->join('gnr_supplier as t2','t1.supplierIdFk','=','t2.id')
                        ->select('t1.*','t2.name')
                        ->where('t1.softDel',0)
                        ->where('t1.supplierIdFk','=',$supplier)
                        ->where('t1.projectId_Fk',$projectId)
                        ->where('t1.project_TypeId_Fk',$projectTypeId)
                        ->get();
     $vatPayments= DB::table('acc_vat_payment as t1')
                     ->join('gnr_supplier as t2','t1.supplierIdFk','=','t2.id')
                     ->select('t1.*','t2.name')
                     ->where('t1.supplierIdFk','=',$supplier)
                     ->where('t1.projectId_Fk',$projectId)
                     ->where('t1.project_TypeId_Fk',$projectTypeId)
                     ->get();

    $vatRegisterSum=DB::table('acc_vat_generate')

                  ->where('supplierIdFk','=',$supplier)
                  ->where('softDel',0)
                  ->where('projectId_Fk',$projectId)
                  ->where('project_TypeId_Fk',$projectTypeId)
                  ->where(function ($query) use ($fromDate,$toDate){
                            $query->where('billDate','<',$fromDate);
                       })
                  ->sum('vatAmount');

   $vatPaymentSum= DB::table('acc_vat_payment')
                       ->where('supplierIdFk','=',$supplier)
                       ->where('projectId_Fk',$projectId)
                       ->where('project_TypeId_Fk',$projectTypeId)
                       ->where(function ($query) use ($fromDate,$toDate){
                           $query->where('paymentDate','<',$fromDate);
                        })
                       ->sum('amount');
  $openningBalance=$vatRegisterSum-$vatPaymentSum;




    $vatRegisterReportLowerPartArr= array(
     'supplier'   => $supplier,
     'supplier' => $supplier,
     'projectId'=> $projectId,
     'projectTypeId'=>$projectTypeId,
     'vatGenerates' => $vatGenerates,
     'vatPayments'  => $vatPayments,
     'openningBalance'=> $openningBalance,
     'fromDate'       => $fromDate,
     'toDate'         => $toDate,
     'generateAndPaymentDates' => $generateAndPaymentDates
     );
    return view('accounting/reports/VATRegister/vatRegisterReportLowerPart',$vatRegisterReportLowerPartArr);
}

public function vatRegisterReportProjectTypeAjax(Request $request)
{
  $projectType= DB::table('gnr_project_type')
                    ->select('name','id')
                    ->where('projectId',$request->projectAjax)
                    ->get();

  return response()->json($projectType);
}

}		//End AccLedgerReportsController
