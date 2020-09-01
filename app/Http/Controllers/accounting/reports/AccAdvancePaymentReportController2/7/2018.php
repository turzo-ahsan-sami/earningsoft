<?php
namespace App\Http\Controllers\accounting\reports;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\gnr\GnrProject;
use App\gnr\GnrProjectType;
use App\gnr\GnrBranch;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use App\Traits\GetSoftwareDate;
use App\Http\Controllers\accounting\Accounting;



class AccAdvancePaymentReportController extends Controller
{
    public function __construct() {
        $this->Accounting = new Accounting;
    }
    public function advancePaymentReport()
    {
        $userBranchId = Auth::user()->branchId;
        $employeeList = array('all'=>'All') +DB::table('hr_emp_general_info')->pluck('emp_name_english','id')->toarray();
        $supplierList = array('all'=>'All') +DB::table('gnr_supplier')->pluck('name','id')->toarray();
        $houseOwnerList = array('all'=>'All')  +DB::table('gnr_house_Owner')->pluck('houseOwnerName','id')->toarray();
        $projectList =  array('all'=>'All')  +DB::table('gnr_project')->pluck('name','id')->toarray();
        $advRegisterType= array('all'=>'All')+DB::table('acc_adv_register_type')->pluck('name','id')->toarray();

        $projectType= array('all'=>'All');
        //    $this->Accounting->getOTSAccount();
        $filteringArr=array(
            'userBranchId'          => $userBranchId,
            'employeeList'          => $employeeList,
            'supplierList'          => $supplierList,
            'houseOwnerList'        => $houseOwnerList,
            'projectList'           => $projectList,
            'advRegisterType'       => $advRegisterType,
            'projectType'           => $projectType



        );

        return view('accounting/reports/AdvancePayment/advancePaymentReport',$filteringArr);
    }

    public function advancePaymentReportLowerPart(Request $request)
    {
        $fromDate=Carbon::parse($request->filStartDate)->format('Y-m-d');
        $toDate=Carbon::parse($request->filEndDate)->format('Y-m-d');
        $employee=$request->filEmployee;
        $supplier=$request->filSupplier;
        $houseOwner=$request->filHouseOwner;
        $projectId=$request->filProject;
        $projectTypeId=$request->filProjectType;
        $tableType=0;

        if($employee =="all"&& $supplier =="all" &&$houseOwner =="all" && $projectTypeId =="all"&& $projectId =="all")
        {
            $tableType="1";
            $advanceGeneratesEmployees= DB::table('acc_adv_register as t1')
                                          ->select('t1.employeeId')
                                          ->get();

            $generateEmployees = array();
            $generateSuppliers = array();
            $generateHouseOwners=array();
            $paymentEmployees=array();
            $paymentSuppliers=array();
            $paymentHouseOwners=array();
            $collectionsHouseOwner =[];
            $collectionsSuppliers =[];
            $collectionsEmployee =[];


            foreach($advanceGeneratesEmployees  as $advanceGeneratesEmployee ) {
                array_push($generateEmployees,$advanceGeneratesEmployee->employeeId);
            }
            $generateEmployees=array_unique($generateEmployees);
            sort($generateEmployees);
            $advanceGeneratesSuppliers= DB::table('acc_adv_register as t1')
                                            ->select('t1.*')

                                            ->get();


            foreach($advanceGeneratesSuppliers  as $advanceGeneratesSupplier )
            {
                array_push($generateSuppliers,$advanceGeneratesSupplier->supplierId);
            }
            $generateSuppliers=array_unique($generateSuppliers);
            sort($generateSuppliers);


            $advanceGeneratesHouseOwners= DB::table('acc_adv_register as t1')
                                                ->join('gnr_house_Owner as t2','t1.houseOwnerId','=','t2.id')
                                                ->select('t1.*','t2.houseOwnerName')

                                                ->get();
            foreach($advanceGeneratesHouseOwners  as $advanceGeneratesHouseOwner )
            {
                array_push($generateHouseOwners,$advanceGeneratesHouseOwner->houseOwnerId);
            }
            $generateHouseOwners=array_unique($generateHouseOwners);
            sort($generateHouseOwners);

            $advancePaymentsEmployees= DB::table('acc_adv_receive as t1')
                                            ->join('hr_emp_general_info as t2','t1.employeeId','=','t2.id')
                                            ->select('t1.*','t2.emp_name_english')

                                            ->get();

            foreach($advancePaymentsEmployees  as $advancePaymentsEmployee )
            {
                array_push($paymentEmployees,$advancePaymentsEmployee->employeeId);
            }

            sort($paymentEmployees);
            $advancePaymentsSuppliers= DB::table('acc_adv_receive as t1')
            ->join('gnr_supplier as t2','t1.supplierId','=','t2.id')
            ->select('t1.*','t2.name')

            ->get();
            foreach($advancePaymentsSuppliers  as $advancePaymentsSupplier )
            {
                array_push($paymentSuppliers,$advancePaymentsSupplier->supplierId);
            }

            sort($paymentSuppliers);
            $advancePaymentsHouseOwners= DB::table('acc_adv_receive as t1')
            ->join('gnr_house_Owner as t2','t1.houseOwnerId','=','t2.id')
            ->select('t1.*','t2.houseOwnerName')

            ->get();
            foreach($advancePaymentsHouseOwners  as $advancePaymentsHouseOwner )
            {
                array_push($paymentHouseOwners,$advancePaymentsHouseOwner->houseOwnerId);
            }
            sort($paymentHouseOwners);
            /**************************************
            final employee part starts*******************/
            $maxIdEmployee=sizeof($generateEmployees);
            $subTotalOpBalanceEmp =0;
            $subTotalClosingBalanceEmp =0;
            $subTotalDebitAmountEmp=0;
            $subTotalCreditAmountEmp =0;
            for($x=0;$x<$maxIdEmployee;$x++)
            {   if($generateEmployees[$x] !=0){
                $generateOpenEmployee=0;
                $payOpenEmployee=0;
                $resultOpenEmployee=0;

                $advanceRegisterSumEmployee=DB::table('acc_adv_register')
                ->where('employeeId',$generateEmployees[$x])

                ->where(function ($query) use ($fromDate,$toDate){
                    $query->where('advPaymentDate','<',$fromDate);
                })
                ->sum('amount');
                $generateOpenEmployee   = $advanceRegisterSumEmployee;





                $advanceAmountTotalSumEmployee=DB::table('acc_adv_register')
                ->where('employeeId',$generateEmployees[$x])

                ->where(function ($query) use ($fromDate,$toDate){
                    $query->where('advPaymentDate','>=',$fromDate)
                          ->where('advPaymentDate','<=',$toDate);
                })
                ->sum('amount');


                $advancePaymentSumEmployee= DB::table('acc_adv_receive')
                ->where('employeeId',$generateEmployees[$x])

                ->where(function ($query) use ($fromDate,$toDate){
                    $query->where('receivePaymentDate','<',$fromDate);
                })
                ->sum('amount');
                $advancePaymentTotalSumEmployee= DB::table('acc_adv_receive')
                ->where('employeeId',$generateEmployees[$x])

                ->where(function ($query) use ($fromDate,$toDate){
                    $query->where('receivePaymentDate','>=',$fromDate)
                          ->where('receivePaymentDate','<=',$toDate);
                })
                ->sum('amount');
                $payOpenEmployee=$advancePaymentSumEmployee;
                $resultOpenEmployee=$generateOpenEmployee-$payOpenEmployee;
                $resultCloseEmployee=$resultOpenEmployee+$advanceAmountTotalSumEmployee-$advancePaymentTotalSumEmployee;
                $subTotalDebitAmountEmp=$subTotalDebitAmountEmp+$advanceAmountTotalSumEmployee;
                $subTotalCreditAmountEmp =$subTotalCreditAmountEmp+$advancePaymentTotalSumEmployee;
                $subTotalOpBalanceEmp=$subTotalOpBalanceEmp+$resultOpenEmployee;
                $subTotalClosingBalanceEmp=$subTotalClosingBalanceEmp+$resultCloseEmployee;

                $collectionsEmployee[] =collect(["openningBalance" => $resultOpenEmployee,"id" => $generateEmployees[$x],"amount" => $advanceAmountTotalSumEmployee,"payment" =>$advancePaymentTotalSumEmployee,"closingBalance" => $resultCloseEmployee]);
            }
        }

        /**************************************
        final supplier part starts*******************/
        $maxIdSupplier=sizeof($generateSuppliers);
        $subTotalOpBalanceSupplier =0;
        $subTotalClosingBalanceSupplier =0;
        $subTotalDebitAmountSupplier=0;
        $subTotalCreditAmountSupplier =0;
        for($x=0;$x<$maxIdSupplier;$x++)
        {   if($generateSuppliers[$x] !=0){
            $generateOpenSupplier=0;
            $payOpenSupplier=0;
            $resultOpenSupplier=0;
            $advanceRegisterSumSupplier=DB::table('acc_adv_register')
            ->where('supplierId',$generateSuppliers[$x])

            ->where(function ($query) use ($fromDate,$toDate){
                $query->where('advPaymentDate','<',$fromDate);
            })
            ->sum('amount');
            $advanceAmountTotalSumSupplier=DB::table('acc_adv_register')
            ->where('supplierId',$generateSuppliers[$x])

            ->where(function ($query) use ($fromDate,$toDate){
                $query->where('advPaymentDate','>=',$fromDate)
                      ->where('advPaymentDate','<=',$toDate);
            })
            ->sum('amount');
            $generateOpenSupplier=$advanceRegisterSumSupplier;
            $advancePaymentSumSupplier= DB::table('acc_adv_receive')
            ->where('supplierId',$generateSuppliers[$x])

            ->where(function ($query) use ($fromDate,$toDate){
                $query->where('receivePaymentDate','<',$fromDate);
            })
            ->sum('amount');
            $advancePaymentTotalSumSupplier= DB::table('acc_adv_receive')
            ->where('supplierId',$generateSuppliers[$x])

            ->where(function ($query) use ($fromDate,$toDate){
                $query->where('receivePaymentDate','>=',$fromDate)
                      ->where('receivePaymentDate','<=',$toDate);
            })
            ->sum('amount');

            $payOpenSupplier=$advancePaymentSumSupplier;
            $resultOpenSupplier=$generateOpenSupplier-$payOpenSupplier;
            $resultCloseSupplier=$resultOpenSupplier+$advanceAmountTotalSumSupplier-$advancePaymentTotalSumSupplier;

            $subTotalOpBalanceSupplier =$subTotalOpBalanceSupplier+$resultOpenSupplier;
            $subTotalClosingBalanceSupplier =$subTotalClosingBalanceSupplier+$resultCloseSupplier;
            $subTotalDebitAmountSupplier=$subTotalDebitAmountSupplier+$advanceAmountTotalSumSupplier;
            $subTotalCreditAmountSupplier =$subTotalCreditAmountSupplier+$advancePaymentTotalSumSupplier;

            $collectionsSupplier[] =collect(["openningBalance" => $resultOpenSupplier,"id" => $generateSuppliers[$x],"amount" => $advanceAmountTotalSumSupplier,"payment" =>$advancePaymentTotalSumSupplier,"closingBalance" => $resultCloseSupplier]);

        }
    }


    /**************************************
    final HouseOwner part starts*******************/
    $maxIdHouseOwner=sizeof($generateHouseOwners);
    $subTotalOpBalanceHouseOwner =0;
    $subTotalClosingBalanceHouseOwner =0;
    $subTotalDebitAmountHouseOwner=0;
    $subTotalCreditAmountHouseOwner =0;
    for($x=0;$x<$maxIdHouseOwner;$x++) {
        if($generateHouseOwners[$x] !=0) {
        $generateOpenHouseOwner=0;
        $payOpenHouseOwner=0;
        $resultOpenHouseOwner=0;
        $advanceRegisterSumHouseOwner=DB::table('acc_adv_register')
                                        ->where('HouseOwnerId',$generateHouseOwners[$x])

                                        ->where(function ($query) use ($fromDate,$toDate){
                                            $query->where('advPaymentDate','<',$fromDate);
                                        })
                                        ->sum('amount');
        $advanceAmountTotalSumHouseOwner=DB::table('acc_adv_register')
                                            ->where('HouseOwnerId',$generateHouseOwners[$x])

                                            ->where(function ($query) use ($fromDate,$toDate){
                                                $query->where('advPaymentDate','>=',$fromDate)
                                                      ->where('advPaymentDate','<=',$toDate);
                                            })
                                            ->sum('amount');
        $generateOpenHouseOwner=$advanceRegisterSumHouseOwner;
        $advancePaymentSumHouseOwner= DB::table('acc_adv_receive')
                                        ->where('houseOwnerId',$generateHouseOwners[$x])

                                        ->where(function ($query) use ($fromDate,$toDate){
                                            $query->where('receivePaymentDate','<',$fromDate);
                                        })
                                        ->sum('amount');
        $advancePaymentTotalSumHouseOwner= DB::table('acc_adv_receive')
        ->where('houseOwnerId',$generateHouseOwners[$x])

        ->where(function ($query) use ($fromDate,$toDate){
            $query->where('receivePaymentDate','>=',$fromDate)
                  ->where('receivePaymentDate','<=',$toDate);
        })
        ->sum('amount');

        $payOpenHouseOwner=$payOpenHouseOwner+$advancePaymentSumHouseOwner;
        $resultOpenHouseOwner=$generateOpenHouseOwner-$payOpenHouseOwner;
        $resultCloseHouseOwner=$resultOpenHouseOwner+$advanceAmountTotalSumHouseOwner-$advancePaymentTotalSumHouseOwner;

        $subTotalOpBalanceHouseOwner =$subTotalOpBalanceHouseOwner+$resultOpenHouseOwner;
        $subTotalClosingBalanceHouseOwner =$subTotalClosingBalanceHouseOwner+$resultCloseHouseOwner;
        $subTotalDebitAmountHouseOwner=$subTotalDebitAmountHouseOwner+$advanceAmountTotalSumHouseOwner;
        $subTotalCreditAmountHouseOwner =$subTotalCreditAmountHouseOwner+$advancePaymentTotalSumHouseOwner;

        $collectionsHouseOwner[] =collect(["openningBalance" => $resultOpenHouseOwner,"id" => $generateHouseOwners[$x],"amount" => $advanceAmountTotalSumHouseOwner,"payment" =>$advancePaymentTotalSumHouseOwner,"closingBalance" => $resultCloseHouseOwner]);
    }
}


$vatRegisterReportLowerPartArr= array(
    'tableType'             => $tableType,
    'fromDate'              => $fromDate,
    'toDate'                => $toDate,
    'subTotalOpBalanceEmp'     => $subTotalOpBalanceEmp,
    'subTotalClosingBalanceEmp'=> $subTotalClosingBalanceEmp,
    'subTotalDebitAmountEmp'  => $subTotalDebitAmountEmp,
    'subTotalCreditAmountEmp' => $subTotalCreditAmountEmp,

    'subTotalOpBalanceSupplier'     => $subTotalOpBalanceSupplier,
    'subTotalClosingBalanceSupplier'=> $subTotalClosingBalanceSupplier,
    'subTotalDebitAmountSupplier'  => $subTotalDebitAmountSupplier,
    'subTotalCreditAmountSupplier' => $subTotalCreditAmountSupplier,

    'subTotalOpBalanceHouseOwner'     => $subTotalOpBalanceHouseOwner,
    'subTotalClosingBalanceHouseOwner'=> $subTotalClosingBalanceHouseOwner,
    'subTotalDebitAmountHouseOwner'  => $subTotalDebitAmountHouseOwner,
    'subTotalCreditAmountHouseOwner' => $subTotalCreditAmountHouseOwner,

    'collectionsEmployee'   => $collectionsEmployee,
    'collectionsSupplier'   => $collectionsSupplier,
    'collectionsHouseOwner' => $collectionsHouseOwner

);
}

elseif($supplier =="all"&&$houseOwner =="all"&&$projectTypeId =="all")
{   $tableType="2";
    $generateSuppliers=array();
    $generateHouseOwners=array();
    $paymentSuppliers=array();
    $paymentHouseOwners=array();
    $collectionsHouseOwner =[];
    $collectionsSuppliers =[];
    $collectionsEmployee =[];

    $generateDatesEmployee = DB::table('acc_adv_register')
                                ->select('advPaymentDate')
                                ->where('employeeId',$employee)
                                ->where('projectId',$request->filProject)
                                ->where(function ($query) use ($fromDate,$toDate){
                                    $query->where('advPaymentDate','>=',$fromDate)
                                    ->where('advPaymentDate','<=',$toDate);
                                })
                                ->get();

    $paymentDatesEmployee = DB::table('acc_adv_receive')
                                ->select('receivePaymentDate')
                                ->where('employeeId',$employee)
                                ->where('projectId',$request->filProject)
                                ->where(function ($query) use ($fromDate,$toDate){
                                $query->where('receivePaymentDate','>=',$fromDate)
                                ->where('receivePaymentDate','<=',$toDate);
                                })
                                ->get();

    $generateAndPaymentDatesEmployee=array();

    foreach($generateDatesEmployee  as $generateDateEmployee )
    {
        array_push($generateAndPaymentDatesEmployee,$generateDateEmployee ->advPaymentDate);
    }

    foreach($paymentDatesEmployee as $paymentDateEmployee)
    {
        array_push($generateAndPaymentDatesEmployee,$paymentDateEmployee ->receivePaymentDate);
    }
    sort($generateAndPaymentDatesEmployee);


    $advanceGeneratesEmployee= DB::table('acc_adv_register as t1')
                                    ->join('hr_emp_general_info as t2','t1.employeeId','=','t2.id')
                                    ->select('t1.*','t2.emp_name_english')
                                    ->where('t1.employeeId',$employee)
                                    ->where('t1.projectId',$projectId)
                                    ->get();

    $advancePaymentsEmployee= DB::table('acc_adv_receive as t1')
                                    ->join('hr_emp_general_info as t2','t1.employeeId','=','t2.id')
                                    ->select('t1.*','t2.emp_name_english')
                                    ->where('t1.employeeId',$employee)
                                    ->where('t1.projectId',$projectId)
                                    ->get();


    $advanceRegisterSumEmployee=DB::table('acc_adv_register')

                                    ->where('employeeId',$employee)

                                    ->where('projectId',$projectId)

                                    ->where(function ($query) use ($fromDate,$toDate){
                                        $query->where('advPaymentDate','<',$fromDate);
                                    })
                                    ->sum('amount');

    $advancePaymentSumEmployee= DB::table('acc_adv_receive')
                                    ->where('employeeId',$employee)
                                    ->where('projectId',$projectId)

                                    ->where(function ($query) use ($fromDate,$toDate){
                                        $query->where('receivePaymentDate','<',$fromDate);
                                    })
                                    ->sum('amount');

    $openningBalanceEmployee=$advanceRegisterSumEmployee-$advancePaymentSumEmployee;

     /*ends employee part  */



    $advanceGeneratesSuppliers= DB::table('acc_adv_register as t1')
                                    ->select('t1.*')
                                    ->where('t1.projectId',$projectId)
                                    ->get();
    foreach($advanceGeneratesSuppliers  as $advanceGeneratesSupplier )
    {
        array_push($generateSuppliers,$advanceGeneratesSupplier->supplierId);
    }
    $generateSuppliers=array_unique($generateSuppliers);
    sort($generateSuppliers);
    $advanceGeneratesHouseOwners= DB::table('acc_adv_register as t1')
                                        ->join('gnr_house_Owner as t2','t1.houseOwnerId','=','t2.id')
                                        ->select('t1.*','t2.houseOwnerName')
                                        ->where('t1.projectId',$projectId)
                                        ->get();
    foreach($advanceGeneratesHouseOwners  as $advanceGeneratesHouseOwner )
    {
        array_push($generateHouseOwners,$advanceGeneratesHouseOwner->houseOwnerId);
    }
    $generateHouseOwners=array_unique($generateHouseOwners);
    sort($generateHouseOwners);
    $advancePaymentsSuppliers= DB::table('acc_adv_receive as t1')
                                    ->join('gnr_supplier as t2','t1.supplierId','=','t2.id')
                                    ->select('t1.*','t2.name')
                                    ->where('t1.projectId',$projectId)
                                    ->get();
    foreach($advancePaymentsSuppliers  as $advancePaymentsSupplier )
    {
        array_push($paymentSuppliers,$advancePaymentsSupplier->supplierId);
    }
    sort($paymentSuppliers);
    $advancePaymentsHouseOwners= DB::table('acc_adv_receive as t1')
                                    ->join('gnr_house_Owner as t2','t1.houseOwnerId','=','t2.id')
                                    ->select('t1.*','t2.houseOwnerName')
                                    ->where('t1.projectId',$projectId)
                                    ->get();
    foreach($advancePaymentsHouseOwners  as $advancePaymentsHouseOwner )
    {
        array_push($paymentHouseOwners,$advancePaymentsHouseOwner->houseOwnerId);
    }
    sort($paymentHouseOwners);

/**************************************
final supplier part starts*******************/
$maxIdSupplier=sizeof($generateSuppliers);

for($x=0;$x<$maxIdSupplier;$x++)
{   if($generateSuppliers[$x] !=0){
    $generateOpenSupplier=0;
    $payOpenSupplier=0;
    $resultOpenSupplier=0;
    $advanceRegisterSumSupplier=DB::table('acc_adv_register')
                                    ->where('supplierId',$generateSuppliers[$x])
                                    ->where('projectId',$projectId)
                                    ->where(function ($query) use ($fromDate,$toDate){
                                        $query->where('advPaymentDate','<',$fromDate);
                                    })
                                    ->sum('amount');
    $advanceAmountTotalSumSupplier=DB::table('acc_adv_register')
                                        ->where('supplierId',$generateSuppliers[$x])
                                        ->where('projectId',$projectId)
                                        ->where(function ($query) use ($fromDate,$toDate){
                                            $query->where('advPaymentDate','>=',$fromDate)
                                                  ->where('advPaymentDate','<=',$toDate);
                                        })
                                        ->sum('amount');
    $generateOpenSupplier=$advanceRegisterSumSupplier;
    $advancePaymentSumSupplier= DB::table('acc_adv_receive')
                                    ->where('supplierId',$generateSuppliers[$x])
                                    ->where('projectId',$projectId)
                                    ->where(function ($query) use ($fromDate,$toDate){
                                        $query->where('receivePaymentDate','<',$fromDate);
                                    })
                                    ->sum('amount');
    $advancePaymentTotalSumSupplier= DB::table('acc_adv_receive')
                                        ->where('supplierId',$generateSuppliers[$x])
                                        ->where('projectId',$projectId)
                                        ->where(function ($query) use ($fromDate,$toDate){
                                            $query->where('receivePaymentDate','>=',$fromDate)
                                                  ->where('receivePaymentDate','<=',$toDate);
                                        })
                                        ->sum('amount');
    $payOpenSupplier=$advancePaymentSumSupplier;

    $resultOpenSupplier=$generateOpenSupplier-$payOpenSupplier;
    $resultCloseSupplier=$resultOpenSupplier+$advanceAmountTotalSumSupplier-$advancePaymentTotalSumSupplier;

    $collectionsSupplier[] =collect(["openningBalance" => $resultOpenSupplier,"id" => $generateSuppliers[$x],"amount" => $advanceAmountTotalSumSupplier,"payment" =>$advancePaymentTotalSumSupplier,"closingBalance" => $resultCloseSupplier]);

}
}
/**************************************
final HouseOwner part starts*******************/
$maxIdHouseOwner=sizeof($generateHouseOwners);

for($x=0;$x<$maxIdHouseOwner;$x++)
{   if($generateHouseOwners[$x] !=0){
    $generateOpenHouseOwner=0;
    $payOpenHouseOwner=0;
    $resultOpenHouseOwner=0;
    $advanceRegisterSumHouseOwner=DB::table('acc_adv_register')
    ->where('HouseOwnerId',$generateHouseOwners[$x])
    ->where('projectId',$projectId)
    ->where(function ($query) use ($fromDate,$toDate){
        $query->where('advPaymentDate','<',$fromDate);
    })
    ->sum('amount');
    $advanceAmountTotalSumHouseOwner=DB::table('acc_adv_register')
    ->where('HouseOwnerId',$generateHouseOwners[$x])
    ->where('projectId',$projectId)
    ->where(function ($query) use ($fromDate,$toDate){
        $query->where('advPaymentDate','>=',$fromDate)
              ->where('advPaymentDate','<=',$toDate);
    })
    ->sum('amount');
    $generateOpenHouseOwner=$advanceRegisterSumHouseOwner;
    $advancePaymentSumHouseOwner= DB::table('acc_adv_receive')
    ->where('houseOwnerId',$generateHouseOwners[$x])
    ->where('projectId',$projectId)
    ->where(function ($query) use ($fromDate,$toDate){
        $query->where('receivePaymentDate','<',$fromDate);
    })
    ->sum('amount');
    $advancePaymentTotalSumHouseOwner= DB::table('acc_adv_receive')
    ->where('houseOwnerId',$generateHouseOwners[$x])
    ->where('projectId',$projectId)
    ->where(function ($query) use ($fromDate,$toDate){
        $query->where('receivePaymentDate','>=',$fromDate)
              ->where('receivePaymentDate','<=',$toDate);
    })
    ->sum('amount');

    $payOpenHouseOwner=$payOpenHouseOwner+$advancePaymentSumHouseOwner;
    $resultOpenHouseOwner=$generateOpenHouseOwner-$payOpenHouseOwner;
    $resultCloseHouseOwner=$resultOpenHouseOwner+$advanceAmountTotalSumHouseOwner-$advancePaymentTotalSumHouseOwner;

    $collectionsHouseOwner[] =collect(["openningBalance" => $resultOpenHouseOwner,"id" => $generateHouseOwners[$x],"amount" => $advanceAmountTotalSumHouseOwner,"payment" =>$advancePaymentTotalSumHouseOwner,"closingBalance" => $resultCloseHouseOwner]);
}
}


$vatRegisterReportLowerPartArr= array(
    'tableType'             => $tableType,
    'fromDate'              => $fromDate,
    'toDate'                => $toDate,
    'openningBalanceEmployee'=> $openningBalanceEmployee,
    'advanceGeneratesEmployee' => $advanceGeneratesEmployee,
    'generateAndPaymentDatesEmployee' => $generateAndPaymentDatesEmployee,
    'advancePaymentsEmployee'         => $advancePaymentsEmployee,
    'collectionsSupplier'   => $collectionsSupplier,
    'collectionsHouseOwner' => $collectionsHouseOwner

);
}
else{

    /*
    */
    $vatRegisterReportLowerPartArr=array(
        'fromDate'       => $fromDate,
        'toDate'         => $toDate,
        'tableType'     => $tableType
    );
}

return view('accounting/reports/AdvancePayment/advancePaymentReportLowerPart',$vatRegisterReportLowerPartArr);
}


public function advancePaymentReportProjectTypeAjax(Request $request)
{
    $projectType= DB::table('gnr_project_type')
    ->select('name','id')
    ->where('projectId',$request->projectAjax)
    ->get();

    return response()->json($projectType);
}

}
