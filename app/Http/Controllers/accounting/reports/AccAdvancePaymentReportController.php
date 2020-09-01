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
        $employeeList = array('all'=>'--Select--') +DB::table('hr_emp_general_info')->pluck('emp_name_english','id')->toarray();
        $supplierList = array('all'=>'--Select--') +DB::table('gnr_supplier')->pluck('name','id')->toarray();
        $houseOwnerList = array('all'=>'--Select--')  +DB::table('gnr_house_Owner')->pluck('houseOwnerName','id')->toarray();
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

            if($employee =="all"&& $supplier =="all" &&$houseOwner =="all" )
            {
                $tableType="1";
                $advanceTypeCollections = DB::table('acc_adv_register_type')
                                             ->select('*')
                                             ->get();
                /*filtering type 1*/
                if($projectTypeId =="all"&& $projectId =="all" && $request->filadvType =="all")
                {

                $advanceGeneratesEmployees= DB::table('acc_adv_register as t1')
                                              ->select('t1.employeeId')
                                              ->get();

                $generateEmployees = array();
                $generateSuppliers = array();
                $generateHouseOwners=array();
                $paymentEmployees=array();
                $paymentSuppliers=array();
                $paymentHouseOwners=array();
                $collectionsAll =[];


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
                $registerTypeCollections= DB::table('acc_adv_register_type')
                                                      ->select('*')
                                                      ->get();
                // $registerTypeEmployeeArray=array();
                  foreach($registerTypeCollections as $registerTypeEmployeeCollection)
                  {
                    for($x=0;$x<$maxIdEmployee;$x++)
                    {   if($generateEmployees[$x] !=0)
                        {
                        $generateOpenEmployee=0;
                        $payOpenEmployee=0;
                        $resultOpenEmployee=0;

                        $advanceRegisterSumEmployee=DB::table('acc_adv_register')
                                                        ->where('employeeId',$generateEmployees[$x])
                                                        ->where('advRegType',$registerTypeEmployeeCollection->id)
                                                        ->where(function ($query) use ($fromDate,$toDate){
                                                            $query->where('advPaymentDate','<',$fromDate);
                                                        })
                                                        ->sum('amount');
                        $generateOpenEmployee   = $advanceRegisterSumEmployee;





                        $advanceAmountTotalSumEmployee=DB::table('acc_adv_register')
                                                            ->where('employeeId',$generateEmployees[$x])
                                                            ->where('advRegType',$registerTypeEmployeeCollection->id)
                                                            ->where(function ($query) use ($fromDate,$toDate){
                                                                $query->where('advPaymentDate','>=',$fromDate)
                                                                      ->where('advPaymentDate','<=',$toDate);
                                                            })
                                                            ->sum('amount');


                        $advancePaymentSumEmployee= DB::table('acc_adv_receive')
                                                        ->where('employeeId',$generateEmployees[$x])
                                                        ->where('regTypeId',$registerTypeEmployeeCollection->id)
                                                        ->where(function ($query) use ($fromDate,$toDate){
                                                            $query->where('receivePaymentDate','<',$fromDate);
                                                        })
                                                        ->sum('amount');
                        $advancePaymentTotalSumEmployee= DB::table('acc_adv_receive')
                                                            ->where('employeeId',$generateEmployees[$x])
                                                            ->where('regTypeId',$registerTypeEmployeeCollection->id)
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
                        if($resultOpenEmployee!=0 || $advanceAmountTotalSumEmployee !=0 || $advancePaymentTotalSumEmployee!=0 ||$resultCloseEmployee !=0)
                        {
                            $collectionsAll[] =collect(["openningBalance" => $resultOpenEmployee,"id" => $generateEmployees[$x],"amount" => $advanceAmountTotalSumEmployee,"payment" =>$advancePaymentTotalSumEmployee,"closingBalance" => $resultCloseEmployee, "registerType" =>$registerTypeEmployeeCollection->id,"type"=> "1" ]);


                       }
                    }
                  }
                }

                /**************************************
                final supplier part starts*******************/
                $maxIdSupplier=sizeof($generateSuppliers);
                $subTotalOpBalanceSupplier =0;
                $subTotalClosingBalanceSupplier =0;
                $subTotalDebitAmountSupplier=0;
                $subTotalCreditAmountSupplier =0;
                foreach($registerTypeCollections as $registerTypeSupplierCollection)
                {
                    for($x=0;$x<$maxIdSupplier;$x++)
                    {
                        if($generateSuppliers[$x] !=0){
                        $generateOpenSupplier=0;
                        $payOpenSupplier=0;
                        $resultOpenSupplier=0;
                        $advanceRegisterSumSupplier=DB::table('acc_adv_register')
                                                        ->where('supplierId',$generateSuppliers[$x])
                                                        ->where('advRegType',$registerTypeSupplierCollection->id)
                                                        ->where(function ($query) use ($fromDate,$toDate){
                                                            $query->where('advPaymentDate','<',$fromDate);
                                                        })
                                                        ->sum('amount');
                        $advanceAmountTotalSumSupplier=DB::table('acc_adv_register')
                                                            ->where('supplierId',$generateSuppliers[$x])
                                                            ->where('advRegType',$registerTypeSupplierCollection->id)
                                                            ->where(function ($query) use ($fromDate,$toDate){
                                                                $query->where('advPaymentDate','>=',$fromDate)
                                                                      ->where('advPaymentDate','<=',$toDate);
                                                            })
                                                            ->sum('amount');
                        $generateOpenSupplier=$advanceRegisterSumSupplier;
                        $advancePaymentSumSupplier= DB::table('acc_adv_receive')
                                                        ->where('supplierId',$generateSuppliers[$x])
                                                        ->where('regTypeId',$registerTypeSupplierCollection->id)
                                                        ->where(function ($query) use ($fromDate,$toDate){
                                                            $query->where('receivePaymentDate','<',$fromDate);
                                                        })
                                                        ->sum('amount');
                        $advancePaymentTotalSumSupplier= DB::table('acc_adv_receive')
                                                            ->where('supplierId',$generateSuppliers[$x])
                                                            ->where('regTypeId',$registerTypeSupplierCollection->id)

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
                        if($resultOpenSupplier!=0 || $advanceAmountTotalSumSupplier !=0 || $advancePaymentTotalSumSupplier!=0 ||$resultCloseSupplier !=0)
                        {
                            $collectionsAll[] =collect(["openningBalance" => $resultOpenSupplier,"id" => $generateSuppliers[$x],"amount" => $advanceAmountTotalSumSupplier,"payment" =>$advancePaymentTotalSumSupplier,"closingBalance" => $resultCloseSupplier,"registerType" =>$registerTypeSupplierCollection->id,"type"=> "2"]);
                        }

                      }
                    }
             }


                /**************************************
                final HouseOwner part starts*******************/
                $maxIdHouseOwner=sizeof($generateHouseOwners);
                $subTotalOpBalanceHouseOwner =0;
                $subTotalClosingBalanceHouseOwner =0;
                $subTotalDebitAmountHouseOwner=0;
                $subTotalCreditAmountHouseOwner =0;
                foreach($registerTypeCollections as $registerTypeHouseOwnerCollection)
                {
                   for($x=0;$x<$maxIdHouseOwner;$x++)
                   {
                     if($generateHouseOwners[$x] !=0)
                     {
                        $generateOpenHouseOwner=0;
                        $payOpenHouseOwner=0;
                        $resultOpenHouseOwner=0;
                        $advanceRegisterSumHouseOwner=DB::table('acc_adv_register')
                                                        ->where('houseOwnerId',$generateHouseOwners[$x])
                                                        ->where('advRegType',$registerTypeHouseOwnerCollection->id)
                                                        ->where(function ($query) use ($fromDate,$toDate){
                                                            $query->where('advPaymentDate','<',$fromDate);
                                                        })
                                                        ->sum('amount');
                        $advanceAmountTotalSumHouseOwner=DB::table('acc_adv_register')
                                                            ->where('houseOwnerId',$generateHouseOwners[$x])
                                                            ->where('advRegType',$registerTypeHouseOwnerCollection->id)
                                                            ->where(function ($query) use ($fromDate,$toDate){
                                                                $query->where('advPaymentDate','>=',$fromDate)
                                                                      ->where('advPaymentDate','<=',$toDate);
                                                            })
                                                            ->sum('amount');
                        $generateOpenHouseOwner=$advanceRegisterSumHouseOwner;
                        $advancePaymentSumHouseOwner= DB::table('acc_adv_receive')
                                                        ->where('houseOwnerId',$generateHouseOwners[$x])
                                                        ->where('regTypeId',$registerTypeHouseOwnerCollection->id)
                                                        ->where(function ($query) use ($fromDate,$toDate){
                                                            $query->where('receivePaymentDate','<',$fromDate);
                                                        })
                                                        ->sum('amount');
                        $advancePaymentTotalSumHouseOwner= DB::table('acc_adv_receive')
                                                                ->where('houseOwnerId',$generateHouseOwners[$x])
                                                                ->where('regTypeId',$registerTypeHouseOwnerCollection->id)
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

                        if($resultOpenHouseOwner!=0 || $advanceAmountTotalSumHouseOwner !=0 || $advancePaymentTotalSumHouseOwner!=0 ||$resultCloseHouseOwner !=0)
                        {
                            $collectionsAll[] =collect(["openningBalance" => $resultOpenHouseOwner,"id" => $generateHouseOwners[$x],"amount" => $advanceAmountTotalSumHouseOwner,"payment" =>$advancePaymentTotalSumHouseOwner,"closingBalance" => $resultCloseHouseOwner,
                        "registerType" =>$registerTypeHouseOwnerCollection->id,"type"=> "3"]);
                        }


                    }
                 }
              }
           }

            /*filtering type 2*/

            if($projectTypeId =="all"&& $projectId !="all" && $request->filadvType =="all")
            {

            $advanceGeneratesEmployees= DB::table('acc_adv_register as t1')
                                          ->select('t1.employeeId')
                                          ->where('t1.projectId',$projectId)
                                          ->get();

            $generateEmployees = array();
            $generateSuppliers = array();
            $generateHouseOwners=array();
            $paymentEmployees=array();
            $paymentSuppliers=array();
            $paymentHouseOwners=array();
            $collectionsAll =[];


            foreach($advanceGeneratesEmployees  as $advanceGeneratesEmployee ) {
                array_push($generateEmployees,$advanceGeneratesEmployee->employeeId);
            }
            $generateEmployees=array_unique($generateEmployees);
            sort($generateEmployees);
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

            $advancePaymentsEmployees= DB::table('acc_adv_receive as t1')
                                            ->join('hr_emp_general_info as t2','t1.employeeId','=','t2.id')
                                            ->select('t1.*','t2.emp_name_english')
                                            ->where('t1.projectId',$projectId)

                                            ->get();

            foreach($advancePaymentsEmployees  as $advancePaymentsEmployee )
            {
                array_push($paymentEmployees,$advancePaymentsEmployee->employeeId);
            }

            sort($paymentEmployees);
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
            final employee part starts*******************/
            $maxIdEmployee=sizeof($generateEmployees);
            $subTotalOpBalanceEmp =0;
            $subTotalClosingBalanceEmp =0;
            $subTotalDebitAmountEmp=0;
            $subTotalCreditAmountEmp =0;
            $registerTypeCollections= DB::table('acc_adv_register_type')
                                                  ->select('*')
                                                  ->get();
            // $registerTypeEmployeeArray=array();
              foreach($registerTypeCollections as $registerTypeEmployeeCollection)
              {
                for($x=0;$x<$maxIdEmployee;$x++)
                {   if($generateEmployees[$x] !=0)
                    {
                    $generateOpenEmployee=0;
                    $payOpenEmployee=0;
                    $resultOpenEmployee=0;

                    $advanceRegisterSumEmployee=DB::table('acc_adv_register')
                                                    ->where('employeeId',$generateEmployees[$x])
                                                    ->where('advRegType',$registerTypeEmployeeCollection->id)
                                                    ->where('projectId',$projectId)
                                                    ->where(function ($query) use ($fromDate,$toDate){
                                                        $query->where('advPaymentDate','<',$fromDate);
                                                    })
                                                    ->sum('amount');
                    $generateOpenEmployee   = $advanceRegisterSumEmployee;





                    $advanceAmountTotalSumEmployee=DB::table('acc_adv_register')
                                                        ->where('employeeId',$generateEmployees[$x])
                                                        ->where('advRegType',$registerTypeEmployeeCollection->id)
                                                        ->where('projectId',$projectId)
                                                        ->where(function ($query) use ($fromDate,$toDate){
                                                            $query->where('advPaymentDate','>=',$fromDate)
                                                                  ->where('advPaymentDate','<=',$toDate);
                                                        })
                                                        ->sum('amount');


                    $advancePaymentSumEmployee= DB::table('acc_adv_receive')
                                                    ->where('employeeId',$generateEmployees[$x])
                                                    ->where('regTypeId',$registerTypeEmployeeCollection->id)
                                                    ->where('projectId',$projectId)
                                                    ->where(function ($query) use ($fromDate,$toDate){
                                                        $query->where('receivePaymentDate','<',$fromDate);
                                                    })
                                                    ->sum('amount');
                    $advancePaymentTotalSumEmployee= DB::table('acc_adv_receive')
                                                        ->where('employeeId',$generateEmployees[$x])
                                                        ->where('projectId',$projectId)
                                                        ->where('regTypeId',$registerTypeEmployeeCollection->id)
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
                    if($resultOpenEmployee!=0 || $advanceAmountTotalSumEmployee !=0 || $advancePaymentTotalSumEmployee!=0 ||$resultCloseEmployee !=0)
                    {
                        $collectionsAll[] =collect(["openningBalance" => $resultOpenEmployee,"id" => $generateEmployees[$x],"amount" => $advanceAmountTotalSumEmployee,"payment" =>$advancePaymentTotalSumEmployee,"closingBalance" => $resultCloseEmployee, "registerType" =>$registerTypeEmployeeCollection->id,"type"=> "1" ]);


                   }
                }
              }
            }

            /**************************************
            final supplier part starts*******************/
            $maxIdSupplier=sizeof($generateSuppliers);
            $subTotalOpBalanceSupplier =0;
            $subTotalClosingBalanceSupplier =0;
            $subTotalDebitAmountSupplier=0;
            $subTotalCreditAmountSupplier =0;
            foreach($registerTypeCollections as $registerTypeSupplierCollection)
            {
                for($x=0;$x<$maxIdSupplier;$x++)
                {
                    if($generateSuppliers[$x] !=0){
                    $generateOpenSupplier=0;
                    $payOpenSupplier=0;
                    $resultOpenSupplier=0;
                    $advanceRegisterSumSupplier=DB::table('acc_adv_register')
                                                    ->where('supplierId',$generateSuppliers[$x])
                                                    ->where('projectId',$projectId)
                                                    ->where('advRegType',$registerTypeSupplierCollection->id)
                                                    ->where(function ($query) use ($fromDate,$toDate){
                                                        $query->where('advPaymentDate','<',$fromDate);
                                                    })
                                                    ->sum('amount');
                    $advanceAmountTotalSumSupplier=DB::table('acc_adv_register')
                                                        ->where('supplierId',$generateSuppliers[$x])
                                                        ->where('advRegType',$registerTypeSupplierCollection->id)
                                                        ->where('projectId',$projectId)
                                                        ->where(function ($query) use ($fromDate,$toDate){
                                                            $query->where('advPaymentDate','>=',$fromDate)
                                                                  ->where('advPaymentDate','<=',$toDate);
                                                        })
                                                        ->sum('amount');
                    $generateOpenSupplier=$advanceRegisterSumSupplier;
                    $advancePaymentSumSupplier= DB::table('acc_adv_receive')
                                                    ->where('supplierId',$generateSuppliers[$x])
                                                    ->where('regTypeId',$registerTypeSupplierCollection->id)
                                                    ->where('projectId',$projectId)
                                                    ->where(function ($query) use ($fromDate,$toDate){
                                                        $query->where('receivePaymentDate','<',$fromDate);
                                                    })
                                                    ->sum('amount');
                    $advancePaymentTotalSumSupplier= DB::table('acc_adv_receive')
                                                        ->where('supplierId',$generateSuppliers[$x])
                                                        ->where('projectId',$projectId)
                                                        ->where('regTypeId',$registerTypeSupplierCollection->id)

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
                    if($resultOpenSupplier!=0 || $advanceAmountTotalSumSupplier !=0 || $advancePaymentTotalSumSupplier!=0 ||$resultCloseSupplier !=0)
                    {
                        $collectionsAll[] =collect(["openningBalance" => $resultOpenSupplier,"id" => $generateSuppliers[$x],"amount" => $advanceAmountTotalSumSupplier,"payment" =>$advancePaymentTotalSumSupplier,"closingBalance" => $resultCloseSupplier,"registerType" =>$registerTypeSupplierCollection->id,"type"=> "2"]);
                    }

                  }
                }
         }


            /**************************************
            final HouseOwner part starts*******************/
            $maxIdHouseOwner=sizeof($generateHouseOwners);
            $subTotalOpBalanceHouseOwner =0;
            $subTotalClosingBalanceHouseOwner =0;
            $subTotalDebitAmountHouseOwner=0;
            $subTotalCreditAmountHouseOwner =0;
            foreach($registerTypeCollections as $registerTypeHouseOwnerCollection)
            {
               for($x=0;$x<$maxIdHouseOwner;$x++)
               {
                 if($generateHouseOwners[$x] !=0)
                 {
                    $generateOpenHouseOwner=0;
                    $payOpenHouseOwner=0;
                    $resultOpenHouseOwner=0;
                    $advanceRegisterSumHouseOwner=DB::table('acc_adv_register')
                                                    ->where('houseOwnerId',$generateHouseOwners[$x])
                                                    ->where('advRegType',$registerTypeHouseOwnerCollection->id)
                                                    ->where('projectId',$projectId)
                                                    ->where(function ($query) use ($fromDate,$toDate){
                                                        $query->where('advPaymentDate','<',$fromDate);
                                                    })
                                                    ->sum('amount');
                    $advanceAmountTotalSumHouseOwner=DB::table('acc_adv_register')
                                                        ->where('houseOwnerId',$generateHouseOwners[$x])
                                                        ->where('advRegType',$registerTypeHouseOwnerCollection->id)
                                                        ->where('projectId',$projectId)
                                                        ->where(function ($query) use ($fromDate,$toDate){
                                                            $query->where('advPaymentDate','>=',$fromDate)
                                                                  ->where('advPaymentDate','<=',$toDate);
                                                        })
                                                        ->sum('amount');
                    $generateOpenHouseOwner=$advanceRegisterSumHouseOwner;
                    $advancePaymentSumHouseOwner= DB::table('acc_adv_receive')
                                                    ->where('houseOwnerId',$generateHouseOwners[$x])
                                                    ->where('regTypeId',$registerTypeHouseOwnerCollection->id)
                                                    ->where('projectId',$projectId)
                                                    ->where(function ($query) use ($fromDate,$toDate){
                                                        $query->where('receivePaymentDate','<',$fromDate);
                                                    })
                                                    ->sum('amount');
                    $advancePaymentTotalSumHouseOwner= DB::table('acc_adv_receive')
                                                            ->where('houseOwnerId',$generateHouseOwners[$x])
                                                            ->where('regTypeId',$registerTypeHouseOwnerCollection->id)
                                                            ->where('projectId',$projectId)
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

                    if($resultOpenHouseOwner!=0 || $advanceAmountTotalSumHouseOwner !=0 || $advancePaymentTotalSumHouseOwner!=0 ||$resultCloseHouseOwner !=0)
                    {
                        $collectionsAll[] =collect(["openningBalance" => $resultOpenHouseOwner,"id" => $generateHouseOwners[$x],"amount" => $advanceAmountTotalSumHouseOwner,"payment" =>$advancePaymentTotalSumHouseOwner,"closingBalance" => $resultCloseHouseOwner,
                    "registerType" =>$registerTypeHouseOwnerCollection->id,"type"=> "3"]);
                    }


                }
             }
          }
       }



                    /*filtering type 3*/

                    if($projectTypeId !="all"&& $projectId !="all" && $request->filadvType =="all")
                    {

                    $advanceGeneratesEmployees= DB::table('acc_adv_register as t1')
                                                  ->select('t1.employeeId')
                                                  ->where('t1.projectId',$projectId)
                                                  ->where('t1.projectTypeId',$projectTypeId)
                                                  ->get();

                    $generateEmployees = array();
                    $generateSuppliers = array();
                    $generateHouseOwners=array();
                    $paymentEmployees=array();
                    $paymentSuppliers=array();
                    $paymentHouseOwners=array();
                    $collectionsAll =[];


                    foreach($advanceGeneratesEmployees  as $advanceGeneratesEmployee ) {
                        array_push($generateEmployees,$advanceGeneratesEmployee->employeeId);
                    }
                    $generateEmployees=array_unique($generateEmployees);
                    sort($generateEmployees);
                    $advanceGeneratesSuppliers= DB::table('acc_adv_register as t1')
                                                    ->select('t1.*')
                                                    ->where('t1.projectId',$projectId)
                                                    ->where('t1.projectTypeId',$projectTypeId)
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
                                                        ->where('t1.projectTypeId',$projectTypeId)

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
                                                    ->where('t1.projectId',$projectId)
                                                    ->where('t1.projectTypeId',$projectTypeId)

                                                    ->get();

                    foreach($advancePaymentsEmployees  as $advancePaymentsEmployee )
                    {
                        array_push($paymentEmployees,$advancePaymentsEmployee->employeeId);
                    }

                    sort($paymentEmployees);
                    $advancePaymentsSuppliers= DB::table('acc_adv_receive as t1')
                                                    ->join('gnr_supplier as t2','t1.supplierId','=','t2.id')
                                                    ->select('t1.*','t2.name')
                                                    ->where('t1.projectId',$projectId)
                                                    ->where('t1.projectTypeId',$projectTypeId)

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
                                                    ->where('t1.projectTypeId',$projectTypeId)

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
                    $registerTypeCollections= DB::table('acc_adv_register_type')
                                                          ->select('*')
                                                          ->get();
                    // $registerTypeEmployeeArray=array();
                      foreach($registerTypeCollections as $registerTypeEmployeeCollection)
                      {
                        for($x=0;$x<$maxIdEmployee;$x++)
                        {   if($generateEmployees[$x] !=0)
                            {
                            $generateOpenEmployee=0;
                            $payOpenEmployee=0;
                            $resultOpenEmployee=0;

                            $advanceRegisterSumEmployee=DB::table('acc_adv_register')
                                                            ->where('employeeId',$generateEmployees[$x])
                                                            ->where('advRegType',$registerTypeEmployeeCollection->id)
                                                            ->where('projectId',$projectId)
                                                            ->where('projectTypeId',$projectTypeId)
                                                            ->where(function ($query) use ($fromDate,$toDate){
                                                                $query->where('advPaymentDate','<',$fromDate);
                                                            })
                                                            ->sum('amount');
                            $generateOpenEmployee   = $advanceRegisterSumEmployee;





                            $advanceAmountTotalSumEmployee=DB::table('acc_adv_register')
                                                                ->where('employeeId',$generateEmployees[$x])
                                                                ->where('advRegType',$registerTypeEmployeeCollection->id)
                                                                ->where('projectId',$projectId)
                                                                ->where('projectTypeId',$projectTypeId)
                                                                ->where(function ($query) use ($fromDate,$toDate){
                                                                    $query->where('advPaymentDate','>=',$fromDate)
                                                                          ->where('advPaymentDate','<=',$toDate);
                                                                })
                                                                ->sum('amount');


                            $advancePaymentSumEmployee= DB::table('acc_adv_receive')
                                                            ->where('employeeId',$generateEmployees[$x])
                                                            ->where('regTypeId',$registerTypeEmployeeCollection->id)
                                                            ->where('projectId',$projectId)
                                                            ->where('projectTypeId',$projectTypeId)
                                                            ->where(function ($query) use ($fromDate,$toDate){
                                                                $query->where('receivePaymentDate','<',$fromDate);
                                                            })
                                                            ->sum('amount');
                            $advancePaymentTotalSumEmployee= DB::table('acc_adv_receive')
                                                                ->where('employeeId',$generateEmployees[$x])
                                                                ->where('projectId',$projectId)
                                                                ->where('projectTypeId',$projectTypeId)
                                                                ->where('regTypeId',$registerTypeEmployeeCollection->id)
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
                            if($resultOpenEmployee!=0 || $advanceAmountTotalSumEmployee !=0 || $advancePaymentTotalSumEmployee!=0 ||$resultCloseEmployee !=0)
                            {
                                $collectionsAll[] =collect(["openningBalance" => $resultOpenEmployee,"id" => $generateEmployees[$x],"amount" => $advanceAmountTotalSumEmployee,"payment" =>$advancePaymentTotalSumEmployee,"closingBalance" => $resultCloseEmployee, "registerType" =>$registerTypeEmployeeCollection->id,"type"=> "1" ]);


                           }
                        }
                      }
                    }

                    /**************************************
                    final supplier part starts*******************/
                    $maxIdSupplier=sizeof($generateSuppliers);
                    $subTotalOpBalanceSupplier =0;
                    $subTotalClosingBalanceSupplier =0;
                    $subTotalDebitAmountSupplier=0;
                    $subTotalCreditAmountSupplier =0;
                    foreach($registerTypeCollections as $registerTypeSupplierCollection)
                    {
                        for($x=0;$x<$maxIdSupplier;$x++)
                        {
                            if($generateSuppliers[$x] !=0){
                            $generateOpenSupplier=0;
                            $payOpenSupplier=0;
                            $resultOpenSupplier=0;
                            $advanceRegisterSumSupplier=DB::table('acc_adv_register')
                                                            ->where('supplierId',$generateSuppliers[$x])
                                                            ->where('projectId',$projectId)
                                                            ->where('projectTypeId',$projectTypeId)
                                                            ->where('advRegType',$registerTypeSupplierCollection->id)
                                                            ->where(function ($query) use ($fromDate,$toDate){
                                                                $query->where('advPaymentDate','<',$fromDate);
                                                            })
                                                            ->sum('amount');
                            $advanceAmountTotalSumSupplier=DB::table('acc_adv_register')
                                                                ->where('supplierId',$generateSuppliers[$x])
                                                                ->where('advRegType',$registerTypeSupplierCollection->id)
                                                                ->where('projectId',$projectId)
                                                                ->where('projectTypeId',$projectTypeId)
                                                                ->where(function ($query) use ($fromDate,$toDate){
                                                                    $query->where('advPaymentDate','>=',$fromDate)
                                                                          ->where('advPaymentDate','<=',$toDate);
                                                                })
                                                                ->sum('amount');
                            $generateOpenSupplier=$advanceRegisterSumSupplier;
                            $advancePaymentSumSupplier= DB::table('acc_adv_receive')
                                                            ->where('supplierId',$generateSuppliers[$x])
                                                            ->where('regTypeId',$registerTypeSupplierCollection->id)
                                                            ->where('projectId',$projectId)
                                                            ->where('projectTypeId',$projectTypeId)
                                                            ->where(function ($query) use ($fromDate,$toDate){
                                                                $query->where('receivePaymentDate','<',$fromDate);
                                                            })
                                                            ->sum('amount');
                            $advancePaymentTotalSumSupplier= DB::table('acc_adv_receive')
                                                                ->where('supplierId',$generateSuppliers[$x])
                                                                ->where('projectId',$projectId)
                                                                ->where('projectTypeId',$projectTypeId)
                                                                ->where('regTypeId',$registerTypeSupplierCollection->id)

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
                            if($resultOpenSupplier!=0 || $advanceAmountTotalSumSupplier !=0 || $advancePaymentTotalSumSupplier!=0 ||$resultCloseSupplier !=0)
                            {
                                $collectionsAll[] =collect(["openningBalance" => $resultOpenSupplier,"id" => $generateSuppliers[$x],"amount" => $advanceAmountTotalSumSupplier,"payment" =>$advancePaymentTotalSumSupplier,"closingBalance" => $resultCloseSupplier,"registerType" =>$registerTypeSupplierCollection->id,"type"=> "2"]);
                            }

                          }
                        }
                 }


                    /**************************************
                    final HouseOwner part starts*******************/
                    $maxIdHouseOwner=sizeof($generateHouseOwners);
                    $subTotalOpBalanceHouseOwner =0;
                    $subTotalClosingBalanceHouseOwner =0;
                    $subTotalDebitAmountHouseOwner=0;
                    $subTotalCreditAmountHouseOwner =0;
                    foreach($registerTypeCollections as $registerTypeHouseOwnerCollection)
                    {
                       for($x=0;$x<$maxIdHouseOwner;$x++)
                       {
                         if($generateHouseOwners[$x] !=0)
                         {
                            $generateOpenHouseOwner=0;
                            $payOpenHouseOwner=0;
                            $resultOpenHouseOwner=0;
                            $advanceRegisterSumHouseOwner=DB::table('acc_adv_register')
                                                            ->where('houseOwnerId',$generateHouseOwners[$x])
                                                            ->where('advRegType',$registerTypeHouseOwnerCollection->id)
                                                            ->where('projectId',$projectId)
                                                            ->where('projectTypeId',$projectTypeId)
                                                            ->where(function ($query) use ($fromDate,$toDate){
                                                                $query->where('advPaymentDate','<',$fromDate);
                                                            })
                                                            ->sum('amount');
                            $advanceAmountTotalSumHouseOwner=DB::table('acc_adv_register')
                                                                ->where('houseOwnerId',$generateHouseOwners[$x])
                                                                ->where('advRegType',$registerTypeHouseOwnerCollection->id)
                                                                ->where('projectId',$projectId)
                                                                ->where('projectTypeId',$projectTypeId)
                                                                ->where(function ($query) use ($fromDate,$toDate){
                                                                    $query->where('advPaymentDate','>=',$fromDate)
                                                                          ->where('advPaymentDate','<=',$toDate);
                                                                })
                                                                ->sum('amount');
                            $generateOpenHouseOwner=$advanceRegisterSumHouseOwner;
                            $advancePaymentSumHouseOwner= DB::table('acc_adv_receive')
                                                            ->where('houseOwnerId',$generateHouseOwners[$x])
                                                            ->where('regTypeId',$registerTypeHouseOwnerCollection->id)
                                                            ->where('projectId',$projectId)
                                                            ->where('projectTypeId',$projectTypeId)
                                                            ->where(function ($query) use ($fromDate,$toDate){
                                                                $query->where('receivePaymentDate','<',$fromDate);
                                                            })
                                                            ->sum('amount');
                            $advancePaymentTotalSumHouseOwner= DB::table('acc_adv_receive')
                                                                    ->where('houseOwnerId',$generateHouseOwners[$x])
                                                                    ->where('regTypeId',$registerTypeHouseOwnerCollection->id)
                                                                    ->where('projectId',$projectId)
                                                                    ->where('projectTypeId',$projectTypeId)
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

                            if($resultOpenHouseOwner!=0 || $advanceAmountTotalSumHouseOwner !=0 || $advancePaymentTotalSumHouseOwner!=0 ||$resultCloseHouseOwner !=0)
                            {
                                $collectionsAll[] =collect(["openningBalance" => $resultOpenHouseOwner,"id" => $generateHouseOwners[$x],"amount" => $advanceAmountTotalSumHouseOwner,"payment" =>$advancePaymentTotalSumHouseOwner,"closingBalance" => $resultCloseHouseOwner,
                            "registerType" =>$registerTypeHouseOwnerCollection->id,"type"=> "3"]);
                            }


                        }
                     }
                  }
               }

           //Ends Here type 3


           /*filtering type 5*/
           if($projectTypeId =="all"&& $projectId =="all" && $request->filadvType !="all" )
          {

          $advanceGeneratesEmployees= DB::table('acc_adv_register as t1')
                                        ->select('t1.employeeId')
                                        ->where('t1.advRegType',$request->filadvType)

                                        ->get();

          $generateEmployees = array();
          $generateSuppliers = array();
          $generateHouseOwners=array();
          $paymentEmployees=array();
          $paymentSuppliers=array();
          $paymentHouseOwners=array();
          $collectionsAll =[];


          foreach($advanceGeneratesEmployees  as $advanceGeneratesEmployee ) {
              array_push($generateEmployees,$advanceGeneratesEmployee->employeeId);
          }
          $generateEmployees=array_unique($generateEmployees);
          sort($generateEmployees);
          $advanceGeneratesSuppliers= DB::table('acc_adv_register as t1')
                                          ->select('t1.*')
                                          ->where('t1.advRegType',$request->filadvType)
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
                                              ->where('t1.advRegType',$request->filadvType)

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
                                          ->where('t1.regTypeId',$request->filadvType)

                                          ->get();

          foreach($advancePaymentsEmployees  as $advancePaymentsEmployee )
          {
              array_push($paymentEmployees,$advancePaymentsEmployee->employeeId);
          }

          sort($paymentEmployees);
          $advancePaymentsSuppliers= DB::table('acc_adv_receive as t1')
                                          ->join('gnr_supplier as t2','t1.supplierId','=','t2.id')
                                          ->select('t1.*','t2.name')
                                          ->where('t1.regTypeId',$request->filadvType)
                                          ->get();
          foreach($advancePaymentsSuppliers  as $advancePaymentsSupplier )
          {
              array_push($paymentSuppliers,$advancePaymentsSupplier->supplierId);
          }

          sort($paymentSuppliers);
          $advancePaymentsHouseOwners= DB::table('acc_adv_receive as t1')
                                          ->join('gnr_house_Owner as t2','t1.houseOwnerId','=','t2.id')
                                          ->select('t1.*','t2.houseOwnerName')
                                          ->where('t1.regTypeId',$request->filadvType)

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
          $registerTypeCollections= DB::table('acc_adv_register_type')
                                                ->select('*')
                                                ->get();
          // $registerTypeEmployeeArray=array();

              for($x=0;$x<$maxIdEmployee;$x++)
              {   if($generateEmployees[$x] !=0)
                  {
                  $generateOpenEmployee=0;
                  $payOpenEmployee=0;
                  $resultOpenEmployee=0;

                  $advanceRegisterSumEmployee=DB::table('acc_adv_register')
                                                  ->where('employeeId',$generateEmployees[$x])
                                                  ->where('advRegType',$request->filadvType)

                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                      $query->where('advPaymentDate','<',$fromDate);
                                                  })
                                                  ->sum('amount');
                  $generateOpenEmployee   = $advanceRegisterSumEmployee;





                  $advanceAmountTotalSumEmployee=DB::table('acc_adv_register')
                                                      ->where('employeeId',$generateEmployees[$x])
                                                      ->where('advRegType',$request->filadvType)

                                                      ->where(function ($query) use ($fromDate,$toDate){
                                                          $query->where('advPaymentDate','>=',$fromDate)
                                                                ->where('advPaymentDate','<=',$toDate);
                                                      })
                                                      ->sum('amount');


                  $advancePaymentSumEmployee= DB::table('acc_adv_receive')
                                                  ->where('employeeId',$generateEmployees[$x])
                                                  ->where('regTypeId',$request->filadvType)

                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                      $query->where('receivePaymentDate','<',$fromDate);
                                                  })
                                                  ->sum('amount');
                  $advancePaymentTotalSumEmployee= DB::table('acc_adv_receive')
                                                      ->where('employeeId',$generateEmployees[$x])

                                                      ->where('regTypeId',$request->filadvType)
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
                  if($resultOpenEmployee!=0 || $advanceAmountTotalSumEmployee !=0 || $advancePaymentTotalSumEmployee!=0 ||$resultCloseEmployee !=0)
                  {
                      $collectionsAll[] =collect(["openningBalance" => $resultOpenEmployee,"id" => $generateEmployees[$x],"amount" => $advanceAmountTotalSumEmployee,"payment" =>$advancePaymentTotalSumEmployee,"closingBalance" => $resultCloseEmployee, "registerType" =>$request->filadvType,"type"=> "1" ]);


                 }
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
              {
                  if($generateSuppliers[$x] !=0){
                  $generateOpenSupplier=0;
                  $payOpenSupplier=0;
                  $resultOpenSupplier=0;
                  $advanceRegisterSumSupplier=DB::table('acc_adv_register')
                                                  ->where('supplierId',$generateSuppliers[$x])

                                                  ->where('advRegType',$request->filadvType)
                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                      $query->where('advPaymentDate','<',$fromDate);
                                                  })
                                                  ->sum('amount');
                  $advanceAmountTotalSumSupplier=DB::table('acc_adv_register')
                                                      ->where('supplierId',$generateSuppliers[$x])
                                                      ->where('advRegType',$request->filadvType)

                                                      ->where(function ($query) use ($fromDate,$toDate){
                                                          $query->where('advPaymentDate','>=',$fromDate)
                                                                ->where('advPaymentDate','<=',$toDate);
                                                      })
                                                      ->sum('amount');
                  $generateOpenSupplier=$advanceRegisterSumSupplier;
                  $advancePaymentSumSupplier= DB::table('acc_adv_receive')
                                                  ->where('supplierId',$generateSuppliers[$x])
                                                  ->where('regTypeId',$request->filadvType)

                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                      $query->where('receivePaymentDate','<',$fromDate);
                                                  })
                                                  ->sum('amount');
                  $advancePaymentTotalSumSupplier= DB::table('acc_adv_receive')
                                                      ->where('supplierId',$generateSuppliers[$x])

                                                      ->where('regTypeId',$request->filadvType)

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
                  if($resultOpenSupplier!=0 || $advanceAmountTotalSumSupplier !=0 || $advancePaymentTotalSumSupplier!=0 ||$resultCloseSupplier !=0)
                  {
                      $collectionsAll[] =collect(["openningBalance" => $resultOpenSupplier,"id" => $generateSuppliers[$x],"amount" => $advanceAmountTotalSumSupplier,"payment" =>$advancePaymentTotalSumSupplier,"closingBalance" => $resultCloseSupplier,"registerType" =>$request->filadvType,"type"=> "2"]);
                  }

                }
              }



          /**************************************
          final HouseOwner part starts*******************/
          $maxIdHouseOwner=sizeof($generateHouseOwners);
          $subTotalOpBalanceHouseOwner =0;
          $subTotalClosingBalanceHouseOwner =0;
          $subTotalDebitAmountHouseOwner=0;
          $subTotalCreditAmountHouseOwner =0;

             for($x=0;$x<$maxIdHouseOwner;$x++)
             {
               if($generateHouseOwners[$x] !=0)
               {
                  $generateOpenHouseOwner=0;
                  $payOpenHouseOwner=0;
                  $resultOpenHouseOwner=0;
                  $advanceRegisterSumHouseOwner=DB::table('acc_adv_register')
                                                  ->where('houseOwnerId',$generateHouseOwners[$x])
                                                  ->where('advRegType',$request->filadvType)

                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                      $query->where('advPaymentDate','<',$fromDate);
                                                  })
                                                  ->sum('amount');
                  $advanceAmountTotalSumHouseOwner=DB::table('acc_adv_register')
                                                      ->where('houseOwnerId',$generateHouseOwners[$x])
                                                      ->where('advRegType',$request->filadvType)

                                                      ->where(function ($query) use ($fromDate,$toDate){
                                                          $query->where('advPaymentDate','>=',$fromDate)
                                                                ->where('advPaymentDate','<=',$toDate);
                                                      })
                                                      ->sum('amount');
                  $generateOpenHouseOwner=$advanceRegisterSumHouseOwner;
                  $advancePaymentSumHouseOwner= DB::table('acc_adv_receive')
                                                  ->where('houseOwnerId',$generateHouseOwners[$x])
                                                  ->where('regTypeId',$request->filadvType)

                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                      $query->where('receivePaymentDate','<',$fromDate);
                                                  })
                                                  ->sum('amount');
                  $advancePaymentTotalSumHouseOwner= DB::table('acc_adv_receive')
                                                          ->where('houseOwnerId',$generateHouseOwners[$x])
                                                          ->where('regTypeId',$request->filadvType)

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

                  if($resultOpenHouseOwner!=0 || $advanceAmountTotalSumHouseOwner !=0 || $advancePaymentTotalSumHouseOwner!=0 ||$resultCloseHouseOwner !=0)
                  {
                      $collectionsAll[] =collect(["openningBalance" => $resultOpenHouseOwner,"id" => $generateHouseOwners[$x],"amount" => $advanceAmountTotalSumHouseOwner,"payment" =>$advancePaymentTotalSumHouseOwner,"closingBalance" => $resultCloseHouseOwner,
                  "registerType" =>$request->filadvType,"type"=> "3"]);
                  }


              }
           }

     }




                  /*filtering type 4*/

                if($projectTypeId !="all"&& $projectId !="all" && $request->filadvType !="all" )
               {

               $advanceGeneratesEmployees= DB::table('acc_adv_register as t1')
                                             ->select('t1.employeeId')
                                             ->where('t1.advRegType',$request->filadvType)
                                             ->where('t1.projectId',$projectId)
                                             ->where('t1.projectTypeId',$projectTypeId)

                                             ->get();

               $generateEmployees = array();
               $generateSuppliers = array();
               $generateHouseOwners=array();
               $paymentEmployees=array();
               $paymentSuppliers=array();
               $paymentHouseOwners=array();
               $collectionsAll =[];


               foreach($advanceGeneratesEmployees  as $advanceGeneratesEmployee ) {
                   array_push($generateEmployees,$advanceGeneratesEmployee->employeeId);
               }
               $generateEmployees=array_unique($generateEmployees);
               sort($generateEmployees);
               $advanceGeneratesSuppliers= DB::table('acc_adv_register as t1')
                                               ->select('t1.*')
                                               ->where('t1.advRegType',$request->filadvType)
                                               ->where('t1.projectId',$projectId)
                                               ->where('t1.projectTypeId',$projectTypeId)
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
                                                   ->where('t1.advRegType',$request->filadvType)
                                                   ->where('t1.projectId',$projectId)
                                                   ->where('t1.projectTypeId',$projectTypeId)

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
                                               ->where('t1.regTypeId',$request->filadvType)
                                               ->where('t1.projectId',$projectId)
                                               ->where('t1.projectTypeId',$projectTypeId)

                                               ->get();

               foreach($advancePaymentsEmployees  as $advancePaymentsEmployee )
               {
                   array_push($paymentEmployees,$advancePaymentsEmployee->employeeId);
               }

               sort($paymentEmployees);
               $advancePaymentsSuppliers= DB::table('acc_adv_receive as t1')
                                               ->join('gnr_supplier as t2','t1.supplierId','=','t2.id')
                                               ->select('t1.*','t2.name')
                                               ->where('t1.regTypeId',$request->filadvType)
                                               ->where('t1.projectId',$projectId)
                                               ->where('t1.projectTypeId',$projectTypeId)
                                               ->get();
               foreach($advancePaymentsSuppliers  as $advancePaymentsSupplier )
               {
                   array_push($paymentSuppliers,$advancePaymentsSupplier->supplierId);
               }

               sort($paymentSuppliers);
               $advancePaymentsHouseOwners= DB::table('acc_adv_receive as t1')
                                               ->join('gnr_house_Owner as t2','t1.houseOwnerId','=','t2.id')
                                               ->select('t1.*','t2.houseOwnerName')
                                               ->where('t1.regTypeId',$request->filadvType)
                                               ->where('t1.projectId',$projectId)
                                               ->where('t1.projectTypeId',$projectTypeId)

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
               $registerTypeCollections= DB::table('acc_adv_register_type')
                                                     ->select('*')
                                                     ->get();
               // $registerTypeEmployeeArray=array();

                   for($x=0;$x<$maxIdEmployee;$x++)
                   {   if($generateEmployees[$x] !=0)
                       {
                       $generateOpenEmployee=0;
                       $payOpenEmployee=0;
                       $resultOpenEmployee=0;

                       $advanceRegisterSumEmployee=DB::table('acc_adv_register')
                                                       ->where('employeeId',$generateEmployees[$x])
                                                       ->where('advRegType',$request->filadvType)
                                                       ->where('projectId',$projectId)
                                                       ->where('projectTypeId',$projectTypeId)

                                                       ->where(function ($query) use ($fromDate,$toDate){
                                                           $query->where('advPaymentDate','<',$fromDate);
                                                       })
                                                       ->sum('amount');
                       $generateOpenEmployee   = $advanceRegisterSumEmployee;





                       $advanceAmountTotalSumEmployee=DB::table('acc_adv_register')
                                                           ->where('employeeId',$generateEmployees[$x])
                                                           ->where('advRegType',$request->filadvType)
                                                           ->where('projectId',$projectId)
                                                           ->where('projectTypeId',$projectTypeId)

                                                           ->where(function ($query) use ($fromDate,$toDate){
                                                               $query->where('advPaymentDate','>=',$fromDate)
                                                                     ->where('advPaymentDate','<=',$toDate);
                                                           })
                                                           ->sum('amount');


                       $advancePaymentSumEmployee= DB::table('acc_adv_receive')
                                                       ->where('employeeId',$generateEmployees[$x])
                                                       ->where('regTypeId',$request->filadvType)
                                                       ->where('projectId',$projectId)
                                                       ->where('projectTypeId',$projectTypeId)

                                                       ->where(function ($query) use ($fromDate,$toDate){
                                                           $query->where('receivePaymentDate','<',$fromDate);
                                                       })
                                                       ->sum('amount');
                       $advancePaymentTotalSumEmployee= DB::table('acc_adv_receive')
                                                           ->where('employeeId',$generateEmployees[$x])
                                                           ->where('projectId',$projectId)
                                                           ->where('projectTypeId',$projectTypeId)

                                                           ->where('regTypeId',$request->filadvType)
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
                       if($resultOpenEmployee!=0 || $advanceAmountTotalSumEmployee !=0 || $advancePaymentTotalSumEmployee!=0 ||$resultCloseEmployee !=0)
                       {
                           $collectionsAll[] =collect(["openningBalance" => $resultOpenEmployee,"id" => $generateEmployees[$x],"amount" => $advanceAmountTotalSumEmployee,"payment" =>$advancePaymentTotalSumEmployee,"closingBalance" => $resultCloseEmployee, "registerType" =>$request->filadvType,"type"=> "1" ]);


                      }
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
                   {
                       if($generateSuppliers[$x] !=0){
                       $generateOpenSupplier=0;
                       $payOpenSupplier=0;
                       $resultOpenSupplier=0;
                       $advanceRegisterSumSupplier=DB::table('acc_adv_register')
                                                       ->where('supplierId',$generateSuppliers[$x])
                                                       ->where('projectId',$projectId)
                                                       ->where('projectTypeId',$projectTypeId)

                                                       ->where('advRegType',$request->filadvType)
                                                       ->where(function ($query) use ($fromDate,$toDate){
                                                           $query->where('advPaymentDate','<',$fromDate);
                                                       })
                                                       ->sum('amount');
                       $advanceAmountTotalSumSupplier=DB::table('acc_adv_register')
                                                           ->where('supplierId',$generateSuppliers[$x])
                                                           ->where('advRegType',$request->filadvType)
                                                           ->where('projectId',$projectId)
                                                           ->where('projectTypeId',$projectTypeId)

                                                           ->where(function ($query) use ($fromDate,$toDate){
                                                               $query->where('advPaymentDate','>=',$fromDate)
                                                                     ->where('advPaymentDate','<=',$toDate);
                                                           })
                                                           ->sum('amount');
                       $generateOpenSupplier=$advanceRegisterSumSupplier;
                       $advancePaymentSumSupplier= DB::table('acc_adv_receive')
                                                       ->where('supplierId',$generateSuppliers[$x])
                                                       ->where('regTypeId',$request->filadvType)
                                                       ->where('projectId',$projectId)
                                                       ->where('projectTypeId',$projectTypeId)

                                                       ->where(function ($query) use ($fromDate,$toDate){
                                                           $query->where('receivePaymentDate','<',$fromDate);
                                                       })
                                                       ->sum('amount');
                       $advancePaymentTotalSumSupplier= DB::table('acc_adv_receive')
                                                           ->where('supplierId',$generateSuppliers[$x])

                                                           ->where('regTypeId',$request->filadvType)
                                                           ->where('projectId',$projectId)
                                                           ->where('projectTypeId',$projectTypeId)

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
                       if($resultOpenSupplier!=0 || $advanceAmountTotalSumSupplier !=0 || $advancePaymentTotalSumSupplier!=0 ||$resultCloseSupplier !=0)
                       {
                           $collectionsAll[] =collect(["openningBalance" => $resultOpenSupplier,"id" => $generateSuppliers[$x],"amount" => $advanceAmountTotalSumSupplier,"payment" =>$advancePaymentTotalSumSupplier,"closingBalance" => $resultCloseSupplier,"registerType" =>$request->filadvType,"type"=> "2"]);
                       }

                     }
                   }



               /**************************************
               final HouseOwner part starts*******************/
               $maxIdHouseOwner=sizeof($generateHouseOwners);
               $subTotalOpBalanceHouseOwner =0;
               $subTotalClosingBalanceHouseOwner =0;
               $subTotalDebitAmountHouseOwner=0;
               $subTotalCreditAmountHouseOwner =0;

                  for($x=0;$x<$maxIdHouseOwner;$x++)
                  {
                    if($generateHouseOwners[$x] !=0)
                    {
                       $generateOpenHouseOwner=0;
                       $payOpenHouseOwner=0;
                       $resultOpenHouseOwner=0;
                       $advanceRegisterSumHouseOwner=DB::table('acc_adv_register')
                                                       ->where('houseOwnerId',$generateHouseOwners[$x])
                                                       ->where('advRegType',$request->filadvType)
                                                       ->where('projectId',$projectId)
                                                       ->where('projectTypeId',$projectTypeId)

                                                       ->where(function ($query) use ($fromDate,$toDate){
                                                           $query->where('advPaymentDate','<',$fromDate);
                                                       })
                                                       ->sum('amount');
                       $advanceAmountTotalSumHouseOwner=DB::table('acc_adv_register')
                                                           ->where('houseOwnerId',$generateHouseOwners[$x])
                                                           ->where('advRegType',$request->filadvType)
                                                           ->where('projectId',$projectId)
                                                           ->where('projectTypeId',$projectTypeId)

                                                           ->where(function ($query) use ($fromDate,$toDate){
                                                               $query->where('advPaymentDate','>=',$fromDate)
                                                                     ->where('advPaymentDate','<=',$toDate);
                                                           })
                                                           ->sum('amount');
                       $generateOpenHouseOwner=$advanceRegisterSumHouseOwner;
                       $advancePaymentSumHouseOwner= DB::table('acc_adv_receive')
                                                       ->where('houseOwnerId',$generateHouseOwners[$x])
                                                       ->where('regTypeId',$request->filadvType)
                                                       ->where('projectId',$projectId)
                                                       ->where('projectTypeId',$projectTypeId)

                                                       ->where(function ($query) use ($fromDate,$toDate){
                                                           $query->where('receivePaymentDate','<',$fromDate);
                                                       })
                                                       ->sum('amount');
                       $advancePaymentTotalSumHouseOwner= DB::table('acc_adv_receive')
                                                               ->where('houseOwnerId',$generateHouseOwners[$x])
                                                               ->where('regTypeId',$request->filadvType)
                                                               ->where('projectId',$projectId)
                                                               ->where('projectTypeId',$projectTypeId)

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

                       if($resultOpenHouseOwner!=0 || $advanceAmountTotalSumHouseOwner !=0 || $advancePaymentTotalSumHouseOwner!=0 ||$resultCloseHouseOwner !=0)
                       {
                           $collectionsAll[] =collect(["openningBalance" => $resultOpenHouseOwner,"id" => $generateHouseOwners[$x],"amount" => $advanceAmountTotalSumHouseOwner,"payment" =>$advancePaymentTotalSumHouseOwner,"closingBalance" => $resultCloseHouseOwner,
                       "registerType" =>$request->filadvType,"type"=> "3"]);
                       }


                   }
                }

           }

           // type 6
           if($projectTypeId =="all"&& $projectId !="all" && $request->filadvType !="all" )
          {

          $advanceGeneratesEmployees= DB::table('acc_adv_register as t1')
                                        ->select('t1.employeeId')
                                        ->where('t1.advRegType',$request->filadvType)
                                        ->where('t1.projectId',$projectId)


                                        ->get();

          $generateEmployees = array();
          $generateSuppliers = array();
          $generateHouseOwners=array();
          $paymentEmployees=array();
          $paymentSuppliers=array();
          $paymentHouseOwners=array();
          $collectionsAll =[];


          foreach($advanceGeneratesEmployees  as $advanceGeneratesEmployee ) {
              array_push($generateEmployees,$advanceGeneratesEmployee->employeeId);
          }
          $generateEmployees=array_unique($generateEmployees);
          sort($generateEmployees);
          $advanceGeneratesSuppliers= DB::table('acc_adv_register as t1')
                                          ->select('t1.*')
                                          ->where('t1.advRegType',$request->filadvType)
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
                                              ->where('t1.advRegType',$request->filadvType)
                                              ->where('t1.projectId',$projectId)


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
                                          ->where('t1.regTypeId',$request->filadvType)
                                          ->where('t1.projectId',$projectId)


                                          ->get();

          foreach($advancePaymentsEmployees  as $advancePaymentsEmployee )
          {
              array_push($paymentEmployees,$advancePaymentsEmployee->employeeId);
          }

          sort($paymentEmployees);
          $advancePaymentsSuppliers= DB::table('acc_adv_receive as t1')
                                          ->join('gnr_supplier as t2','t1.supplierId','=','t2.id')
                                          ->select('t1.*','t2.name')
                                          ->where('t1.regTypeId',$request->filadvType)
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
                                          ->where('t1.regTypeId',$request->filadvType)
                                          ->where('t1.projectId',$projectId)


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
          $registerTypeCollections= DB::table('acc_adv_register_type')
                                                ->select('*')
                                                ->get();
          // $registerTypeEmployeeArray=array();

              for($x=0;$x<$maxIdEmployee;$x++)
              {   if($generateEmployees[$x] !=0)
                  {
                  $generateOpenEmployee=0;
                  $payOpenEmployee=0;
                  $resultOpenEmployee=0;

                  $advanceRegisterSumEmployee=DB::table('acc_adv_register')
                                                  ->where('employeeId',$generateEmployees[$x])
                                                  ->where('advRegType',$request->filadvType)
                                                  ->where('projectId',$projectId)


                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                      $query->where('advPaymentDate','<',$fromDate);
                                                  })
                                                  ->sum('amount');
                  $generateOpenEmployee   = $advanceRegisterSumEmployee;





                  $advanceAmountTotalSumEmployee=DB::table('acc_adv_register')
                                                      ->where('employeeId',$generateEmployees[$x])
                                                      ->where('advRegType',$request->filadvType)
                                                      ->where('projectId',$projectId)


                                                      ->where(function ($query) use ($fromDate,$toDate){
                                                          $query->where('advPaymentDate','>=',$fromDate)
                                                                ->where('advPaymentDate','<=',$toDate);
                                                      })
                                                      ->sum('amount');


                  $advancePaymentSumEmployee= DB::table('acc_adv_receive')
                                                  ->where('employeeId',$generateEmployees[$x])
                                                  ->where('regTypeId',$request->filadvType)
                                                  ->where('projectId',$projectId)


                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                      $query->where('receivePaymentDate','<',$fromDate);
                                                  })
                                                  ->sum('amount');
                  $advancePaymentTotalSumEmployee= DB::table('acc_adv_receive')
                                                      ->where('employeeId',$generateEmployees[$x])
                                                      ->where('projectId',$projectId)


                                                      ->where('regTypeId',$request->filadvType)
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
                  if($resultOpenEmployee!=0 || $advanceAmountTotalSumEmployee !=0 || $advancePaymentTotalSumEmployee!=0 ||$resultCloseEmployee !=0)
                  {
                      $collectionsAll[] =collect(["openningBalance" => $resultOpenEmployee,"id" => $generateEmployees[$x],"amount" => $advanceAmountTotalSumEmployee,"payment" =>$advancePaymentTotalSumEmployee,"closingBalance" => $resultCloseEmployee, "registerType" =>$request->filadvType,"type"=> "1" ]);


                 }
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
              {
                  if($generateSuppliers[$x] !=0){
                  $generateOpenSupplier=0;
                  $payOpenSupplier=0;
                  $resultOpenSupplier=0;
                  $advanceRegisterSumSupplier=DB::table('acc_adv_register')
                                                  ->where('supplierId',$generateSuppliers[$x])
                                                  ->where('projectId',$projectId)


                                                  ->where('advRegType',$request->filadvType)
                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                      $query->where('advPaymentDate','<',$fromDate);
                                                  })
                                                  ->sum('amount');
                  $advanceAmountTotalSumSupplier=DB::table('acc_adv_register')
                                                      ->where('supplierId',$generateSuppliers[$x])
                                                      ->where('advRegType',$request->filadvType)
                                                      ->where('projectId',$projectId)


                                                      ->where(function ($query) use ($fromDate,$toDate){
                                                          $query->where('advPaymentDate','>=',$fromDate)
                                                                ->where('advPaymentDate','<=',$toDate);
                                                      })
                                                      ->sum('amount');
                  $generateOpenSupplier=$advanceRegisterSumSupplier;
                  $advancePaymentSumSupplier= DB::table('acc_adv_receive')
                                                  ->where('supplierId',$generateSuppliers[$x])
                                                  ->where('regTypeId',$request->filadvType)
                                                  ->where('projectId',$projectId)


                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                      $query->where('receivePaymentDate','<',$fromDate);
                                                  })
                                                  ->sum('amount');
                  $advancePaymentTotalSumSupplier= DB::table('acc_adv_receive')
                                                      ->where('supplierId',$generateSuppliers[$x])

                                                      ->where('regTypeId',$request->filadvType)
                                                      ->where('projectId',$projectId)


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
                  if($resultOpenSupplier!=0 || $advanceAmountTotalSumSupplier !=0 || $advancePaymentTotalSumSupplier!=0 ||$resultCloseSupplier !=0)
                  {
                      $collectionsAll[] =collect(["openningBalance" => $resultOpenSupplier,"id" => $generateSuppliers[$x],"amount" => $advanceAmountTotalSumSupplier,"payment" =>$advancePaymentTotalSumSupplier,"closingBalance" => $resultCloseSupplier,"registerType" =>$request->filadvType,"type"=> "2"]);
                  }

                }
              }



          /**************************************
          final HouseOwner part starts*******************/
          $maxIdHouseOwner=sizeof($generateHouseOwners);
          $subTotalOpBalanceHouseOwner =0;
          $subTotalClosingBalanceHouseOwner =0;
          $subTotalDebitAmountHouseOwner=0;
          $subTotalCreditAmountHouseOwner =0;

             for($x=0;$x<$maxIdHouseOwner;$x++)
             {
               if($generateHouseOwners[$x] !=0)
               {
                  $generateOpenHouseOwner=0;
                  $payOpenHouseOwner=0;
                  $resultOpenHouseOwner=0;
                  $advanceRegisterSumHouseOwner=DB::table('acc_adv_register')
                                                  ->where('houseOwnerId',$generateHouseOwners[$x])
                                                  ->where('advRegType',$request->filadvType)
                                                  ->where('projectId',$projectId)


                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                      $query->where('advPaymentDate','<',$fromDate);
                                                  })
                                                  ->sum('amount');
                  $advanceAmountTotalSumHouseOwner=DB::table('acc_adv_register')
                                                      ->where('houseOwnerId',$generateHouseOwners[$x])
                                                      ->where('advRegType',$request->filadvType)
                                                      ->where('projectId',$projectId)


                                                      ->where(function ($query) use ($fromDate,$toDate){
                                                          $query->where('advPaymentDate','>=',$fromDate)
                                                                ->where('advPaymentDate','<=',$toDate);
                                                      })
                                                      ->sum('amount');
                  $generateOpenHouseOwner=$advanceRegisterSumHouseOwner;
                  $advancePaymentSumHouseOwner= DB::table('acc_adv_receive')
                                                  ->where('houseOwnerId',$generateHouseOwners[$x])
                                                  ->where('regTypeId',$request->filadvType)
                                                  ->where('projectId',$projectId)


                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                      $query->where('receivePaymentDate','<',$fromDate);
                                                  })
                                                  ->sum('amount');
                  $advancePaymentTotalSumHouseOwner= DB::table('acc_adv_receive')
                                                          ->where('houseOwnerId',$generateHouseOwners[$x])
                                                          ->where('regTypeId',$request->filadvType)
                                                          ->where('projectId',$projectId)


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

                  if($resultOpenHouseOwner!=0 || $advanceAmountTotalSumHouseOwner !=0 || $advancePaymentTotalSumHouseOwner!=0 ||$resultCloseHouseOwner !=0)
                  {
                      $collectionsAll[] =collect(["openningBalance" => $resultOpenHouseOwner,"id" => $generateHouseOwners[$x],"amount" => $advanceAmountTotalSumHouseOwner,"payment" =>$advancePaymentTotalSumHouseOwner,"closingBalance" => $resultCloseHouseOwner,
                  "registerType" =>$request->filadvType,"type"=> "3"]);
                  }


              }
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

                'collectionsAll'   => $collectionsAll,


            );
         }

        elseif($supplier =="all" && $houseOwner =="all" && $employee != "all" )
        {
            $tableType="2";
            $generateSuppliers=array();
            $generateHouseOwners=array();
            $paymentSuppliers=array();
            $paymentHouseOwners=array();
            $collectionsAll =[];
            $collectionsAll =[];
            $collectionsAll =[];
            if($request->filProject != "all" && $request->filProjectType =="all" && $request->filadvType=="all" )
            {
                 echo $tableType;
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

           }

            /**************************************
            next part*******************/
            else if($request->filProject == "all" && $request->filProjectType =="all" && $request->filadvType!="all" )
            {
                $generateDatesEmployee = DB::table('acc_adv_register')
                                            ->select('advPaymentDate')
                                            ->where('employeeId',$employee)
                                            ->where('advRegType',$request->filadvType)
                                            ->where(function ($query) use ($fromDate,$toDate){
                                                $query->where('advPaymentDate','>=',$fromDate)
                                                ->where('advPaymentDate','<=',$toDate);
                                            })
                                            ->get();

                $paymentDatesEmployee = DB::table('acc_adv_receive')
                                            ->select('receivePaymentDate')
                                            ->where('employeeId',$employee)
                                            ->where('regTypeId',$request->filadvType)

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
                                                ->where('t1.advRegType',$request->filadvType)

                                                ->get();

                $advancePaymentsEmployee= DB::table('acc_adv_receive as t1')
                                                ->join('hr_emp_general_info as t2','t1.employeeId','=','t2.id')
                                                ->select('t1.*','t2.emp_name_english')
                                                ->where('t1.employeeId',$employee)
                                                ->where('t1.regTypeId',$request->filadvType)

                                                ->get();


                $advanceRegisterSumEmployee=DB::table('acc_adv_register')

                                                ->where('employeeId',$employee)
                                                ->where('advRegType',$request->filadvType)



                                                ->where(function ($query) use ($fromDate,$toDate){
                                                    $query->where('advPaymentDate','<',$fromDate);
                                                })
                                                ->sum('amount');

                $advancePaymentSumEmployee= DB::table('acc_adv_receive')
                                                ->where('employeeId',$employee)
                                                ->where('regTypeId',$request->filadvType)


                                                ->where(function ($query) use ($fromDate,$toDate){
                                                    $query->where('receivePaymentDate','<',$fromDate);
                                                })
                                                ->sum('amount');

                $openningBalanceEmployee=$advanceRegisterSumEmployee-$advancePaymentSumEmployee;

                 /*ends employee part  */

              }

              /**************************************
              next part*******************/
              else if($request->filProject != "all" && $request->filProjectType !="all" && $request->filadvType =="all" )
              {
                  $generateDatesEmployee = DB::table('acc_adv_register')
                                              ->select('advPaymentDate')
                                              ->where('employeeId',$employee)
                                              ->where('projectTypeId',$request->filProjectType)
                                              ->where(function ($query) use ($fromDate,$toDate){
                                                  $query->where('advPaymentDate','>=',$fromDate)
                                                  ->where('advPaymentDate','<=',$toDate);
                                              })
                                              ->get();

                  $paymentDatesEmployee = DB::table('acc_adv_receive')
                                              ->select('receivePaymentDate')
                                              ->where('employeeId',$employee)
                                              ->where('projectTypeId',$request->filProjectType)

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
                                                  ->where('t1.projectTypeId',$request->filProjectType)

                                                  ->get();

                  $advancePaymentsEmployee= DB::table('acc_adv_receive as t1')
                                                  ->join('hr_emp_general_info as t2','t1.employeeId','=','t2.id')
                                                  ->select('t1.*','t2.emp_name_english')
                                                  ->where('t1.employeeId',$employee)
                                                  ->where('t1.projectTypeId',$request->filProjectType)

                                                  ->get();


                  $advanceRegisterSumEmployee=DB::table('acc_adv_register')

                                                  ->where('employeeId',$employee)
                                                  ->where('projectTypeId',$request->filProjectType)



                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                      $query->where('advPaymentDate','<',$fromDate);
                                                  })
                                                  ->sum('amount');

                  $advancePaymentSumEmployee= DB::table('acc_adv_receive')
                                                  ->where('employeeId',$employee)
                                                  ->where('projectTypeId',$request->filProjectType)


                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                      $query->where('receivePaymentDate','<',$fromDate);
                                                  })
                                                  ->sum('amount');

                  $openningBalanceEmployee=$advanceRegisterSumEmployee-$advancePaymentSumEmployee;

                   /*ends employee part  */

                }
                /**************************************
                next part*******************/
                else if($request->filProject == "all" && $request->filProjectType =="all" && $request->filadvType =="all" )
                {
                    $generateDatesEmployee = DB::table('acc_adv_register')
                                                ->select('advPaymentDate')
                                                ->where('employeeId',$employee)

                                                ->where(function ($query) use ($fromDate,$toDate){
                                                    $query->where('advPaymentDate','>=',$fromDate)
                                                    ->where('advPaymentDate','<=',$toDate);
                                                })
                                                ->get();

                    $paymentDatesEmployee = DB::table('acc_adv_receive')
                                                ->select('receivePaymentDate')
                                                ->where('employeeId',$employee)

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


                                                    ->get();

                    $advancePaymentsEmployee= DB::table('acc_adv_receive as t1')
                                                    ->join('hr_emp_general_info as t2','t1.employeeId','=','t2.id')
                                                    ->select('t1.*','t2.emp_name_english')
                                                    ->where('t1.employeeId',$employee)


                                                    ->get();


                    $advanceRegisterSumEmployee=DB::table('acc_adv_register')

                                                    ->where('employeeId',$employee)



                                                    ->where(function ($query) use ($fromDate,$toDate){
                                                        $query->where('advPaymentDate','<',$fromDate);
                                                    })
                                                    ->sum('amount');

                    $advancePaymentSumEmployee= DB::table('acc_adv_receive')
                                                    ->where('employeeId',$employee)



                                                    ->where(function ($query) use ($fromDate,$toDate){
                                                        $query->where('receivePaymentDate','<',$fromDate);
                                                    })
                                                    ->sum('amount');

                    $openningBalanceEmployee=$advanceRegisterSumEmployee-$advancePaymentSumEmployee;

                     /*ends employee part  */

                  }
                  /**************************************
                  next part*******************/
                  else if($request->filProject != "all" && $request->filProjectType !="all" && $request->filadvType!="all" )
                  {
                      $generateDatesEmployee = DB::table('acc_adv_register')
                                                  ->select('advPaymentDate')
                                                  ->where('employeeId',$employee)
                                                  ->where('advRegType',$request->filadvType)
                                                  ->where('projectTypeId',$request->filProjectType)
                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                      $query->where('advPaymentDate','>=',$fromDate)
                                                      ->where('advPaymentDate','<=',$toDate);
                                                  })
                                                  ->get();

                      $paymentDatesEmployee = DB::table('acc_adv_receive')
                                                  ->select('receivePaymentDate')
                                                  ->where('employeeId',$employee)
                                                  ->where('regTypeId',$request->filadvType)
                                                  ->where('projectTypeId',$request->filProjectType)

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
                                                      ->where('t1.advRegType',$request->filadvType)
                                                      ->where('projectTypeId',$request->filProjectType)

                                                      ->get();

                      $advancePaymentsEmployee= DB::table('acc_adv_receive as t1')
                                                      ->join('hr_emp_general_info as t2','t1.employeeId','=','t2.id')
                                                      ->select('t1.*','t2.emp_name_english')
                                                      ->where('t1.employeeId',$employee)
                                                      ->where('t1.regTypeId',$request->filadvType)
                                                      ->where('projectTypeId',$request->filProjectType)

                                                      ->get();


                      $advanceRegisterSumEmployee=DB::table('acc_adv_register')

                                                      ->where('employeeId',$employee)
                                                      ->where('advRegType',$request->filadvType)
                                                      ->where('projectTypeId',$request->filProjectType)



                                                      ->where(function ($query) use ($fromDate,$toDate){
                                                          $query->where('advPaymentDate','<',$fromDate);
                                                      })
                                                      ->sum('amount');

                      $advancePaymentSumEmployee= DB::table('acc_adv_receive')
                                                      ->where('employeeId',$employee)
                                                      ->where('regTypeId',$request->filadvType)
                                                      ->where('projectTypeId',$request->filProjectType)


                                                      ->where(function ($query) use ($fromDate,$toDate){
                                                          $query->where('receivePaymentDate','<',$fromDate);
                                                      })
                                                      ->sum('amount');

                      $openningBalanceEmployee=$advanceRegisterSumEmployee-$advancePaymentSumEmployee;

                       /*ends employee part  */

                    }

                    /**************************************
                    next part*******************/
                    else if($request->filProject != "all" && $request->filProjectType =="all" && $request->filadvType!="all" )
                    {   echo "there you go";
                        $generateDatesEmployee = DB::table('acc_adv_register')
                                                    ->select('advPaymentDate')
                                                    ->where('employeeId',$employee)
                                                    ->where('advRegType',$request->filadvType)
                                                    ->where('projectId',$request->filProject)
                                                    ->where(function ($query) use ($fromDate,$toDate){
                                                        $query->where('advPaymentDate','>=',$fromDate)
                                                        ->where('advPaymentDate','<=',$toDate);
                                                    })
                                                    ->get();

                        $paymentDatesEmployee = DB::table('acc_adv_receive')
                                                    ->select('receivePaymentDate')
                                                    ->where('employeeId',$employee)
                                                    ->where('regTypeId',$request->filadvType)
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
                                                        ->where('t1.advRegType',$request->filadvType)
                                                        ->where('t1.projectId',$request->filProject)

                                                        ->get();

                        $advancePaymentsEmployee= DB::table('acc_adv_receive as t1')
                                                        ->join('hr_emp_general_info as t2','t1.employeeId','=','t2.id')
                                                        ->select('t1.*','t2.emp_name_english')
                                                        ->where('t1.employeeId',$employee)
                                                        ->where('t1.regTypeId',$request->filadvType)
                                                        ->where('t1.projectId',$request->filProject)

                                                        ->get();


                        $advanceRegisterSumEmployee=DB::table('acc_adv_register')

                                                        ->where('employeeId',$employee)
                                                        ->where('advRegType',$request->filadvType)
                                                        ->where('projectId',$request->filProject)



                                                        ->where(function ($query) use ($fromDate,$toDate){
                                                            $query->where('advPaymentDate','<',$fromDate);
                                                        })
                                                        ->sum('amount');

                        $advancePaymentSumEmployee= DB::table('acc_adv_receive')
                                                        ->where('employeeId',$employee)
                                                        ->where('regTypeId',$request->filadvType)
                                                        ->where('projectId',$request->filProject)


                                                        ->where(function ($query) use ($fromDate,$toDate){
                                                            $query->where('receivePaymentDate','<',$fromDate);
                                                        })
                                                        ->sum('amount');

                        $openningBalanceEmployee=$advanceRegisterSumEmployee-$advancePaymentSumEmployee;

                         /*ends employee part  */

                      }





        $vatRegisterReportLowerPartArr= array(
            'tableType'             => $tableType,
            'fromDate'              => $fromDate,
            'toDate'                => $toDate,
            'empId'                 => $request->filEmployee,

            'openningBalanceEmployee'=> $openningBalanceEmployee,
            'advanceGeneratesEmployee' => $advanceGeneratesEmployee,
            'generateAndPaymentDatesEmployee' => $generateAndPaymentDatesEmployee,
            'advancePaymentsEmployee'         => $advancePaymentsEmployee


        );
        }


        /*individual supplier search starts here ***/

        elseif($supplier !="all" && $houseOwner =="all" && $employee == "all" )
        {
            $tableType="3";
            $generateSuppliers=array();
            $generateHouseOwners=array();
            $paymentSuppliers=array();
            $paymentHouseOwners=array();
            $collectionsAll =[];
            $collectionsAll =[];
            $collectionsAll =[];
            if($request->filProject != "all" && $request->filProjectType =="all" && $request->filadvType=="all" )
            {

                $generateDatesSupplier = DB::table('acc_adv_register')
                                            ->select('advPaymentDate')
                                            ->where('supplierId',$supplier)
                                            ->where('projectId',$request->filProject)
                                            ->where(function ($query) use ($fromDate,$toDate){
                                                $query->where('advPaymentDate','>=',$fromDate)
                                                ->where('advPaymentDate','<=',$toDate);
                                            })
                                            ->get();

                $paymentDatesSupplier = DB::table('acc_adv_receive')
                                            ->select('receivePaymentDate')
                                            ->where('supplierId',$supplier)
                                            ->where('projectId',$request->filProject)
                                            ->where(function ($query) use ($fromDate,$toDate){
                                            $query->where('receivePaymentDate','>=',$fromDate)
                                            ->where('receivePaymentDate','<=',$toDate);
                                            })
                                            ->get();

                $generateAndPaymentDatesSupplier=array();

                foreach($generateDatesSupplier  as $generateDateSupplier )
                {
                    array_push($generateAndPaymentDatesSupplier,$generateDateSupplier->advPaymentDate);
                }

                foreach($paymentDatesSupplier as $paymentDateSupplier)
                {
                    array_push($generateAndPaymentDatesSupplier,$paymentDateSupplier ->receivePaymentDate);
                }
                sort($generateAndPaymentDatesSupplier);


                $advanceGeneratesSupplier= DB::table('acc_adv_register as t1')
                                                ->join('gnr_supplier as t2','t1.supplierId','=','t2.id')
                                                ->select('t1.*','t2.name')
                                                ->where('t1.supplierId',$supplier)
                                                ->where('t1.projectId',$projectId)
                                                ->get();

                $advancePaymentsSupplier= DB::table('acc_adv_receive as t1')
                                                ->join('gnr_supplier as t2','t1.supplierId','=','t2.id')
                                                ->select('t1.*','t2.name')
                                                ->where('t1.supplierId',$supplier)
                                                ->where('t1.projectId',$projectId)
                                                ->get();


                $advanceRegisterSumSupplier=DB::table('acc_adv_register')

                                                ->where('supplierId',$supplier)

                                                ->where('projectId',$projectId)

                                                ->where(function ($query) use ($fromDate,$toDate){
                                                    $query->where('advPaymentDate','<',$fromDate);
                                                })
                                                ->sum('amount');

                $advancePaymentSumSupplier= DB::table('acc_adv_receive')
                                                ->where('supplierId',$supplier)
                                                ->where('projectId',$projectId)

                                                ->where(function ($query) use ($fromDate,$toDate){
                                                    $query->where('receivePaymentDate','<',$fromDate);
                                                })
                                                ->sum('amount');

                $openningBalanceSupplier=$advanceRegisterSumSupplier-$advancePaymentSumSupplier;

             /*ends employee part  */

           }

            /**************************************
            next part*******************/
            else if($request->filProject == "all" && $request->filProjectType =="all" && $request->filadvType!="all" )
            {
                $generateDatesSupplier = DB::table('acc_adv_register')
                                            ->select('advPaymentDate')
                                            ->where('supplierId',$supplier)
                                            ->where('advRegType',$request->filadvType)
                                            ->where(function ($query) use ($fromDate,$toDate){
                                                $query->where('advPaymentDate','>=',$fromDate)
                                                ->where('advPaymentDate','<=',$toDate);
                                            })
                                            ->get();


                $paymentDatesSupplier = DB::table('acc_adv_receive')
                                            ->select('receivePaymentDate')
                                            ->where('supplierId',$supplier)
                                            ->where('regTypeId',$request->filadvType)
                                            ->where(function ($query) use ($fromDate,$toDate){
                                            $query->where('receivePaymentDate','>=',$fromDate)
                                            ->where('receivePaymentDate','<=',$toDate);
                                            })
                                            ->get();


                $generateAndPaymentDatesSupplier=array();

                foreach($generateDatesSupplier  as $generateDateSupplier )
                {
                    array_push($generateAndPaymentDatesSupplier,$generateDateSupplier ->advPaymentDate);
                }

                foreach($paymentDatesSupplier as $paymentDateSupplier)
                {
                    array_push($generateAndPaymentDatesSupplier,$paymentDateSupplier ->receivePaymentDate);
                }
                sort($generateAndPaymentDatesSupplier);


                $advanceGeneratesSupplier= DB::table('acc_adv_register as t1')
                                                ->join('gnr_supplier as t2','t1.supplierId','=','t2.id')
                                                ->select('t1.*','t2.name')
                                                ->where('t1.supplierId',$supplier)
                                                ->where('t1.advRegType',$request->filadvType)

                                                ->get();


                $advancePaymentsSupplier= DB::table('acc_adv_receive as t1')
                                                ->join('gnr_supplier as t2','t1.supplierId','=','t2.id')
                                                ->select('t1.*','t2.name')
                                                ->where('t1.supplierId',$supplier)
                                                ->where('t1.regTypeId',$request->filadvType)

                                                ->get();


                $advanceRegisterSumSupplier=DB::table('acc_adv_register')

                                                ->where('supplierId',$supplier)
                                                ->where('advRegType',$request->filadvType)
                                                ->where(function ($query) use ($fromDate,$toDate){
                                                    $query->where('advPaymentDate','<',$fromDate);
                                                })
                                                ->sum('amount');

                $advancePaymentSumSupplier= DB::table('acc_adv_receive')
                                                ->where('supplierId',$supplier)
                                                ->where('regTypeId',$request->filadvType)


                                                ->where(function ($query) use ($fromDate,$toDate){
                                                    $query->where('receivePaymentDate','<',$fromDate);
                                                })
                                                ->sum('amount');

                $openningBalanceSupplier=$advanceRegisterSumSupplier-$advancePaymentSumSupplier;

                 /*ends employee part  */

              }

              /**************************************
              next part*******************/
              else if($request->filProject != "all" && $request->filProjectType !="all" && $request->filadvType =="all" )
              {
                  $generateDatesSupplier = DB::table('acc_adv_register')
                                              ->select('advPaymentDate')
                                              ->where('supplierId',$supplier)
                                              ->where('projectTypeId',$request->filProjectType)
                                              ->where(function ($query) use ($fromDate,$toDate){
                                                  $query->where('advPaymentDate','>=',$fromDate)
                                                  ->where('advPaymentDate','<=',$toDate);
                                              })
                                              ->get();

                  $paymentDatesSupplier = DB::table('acc_adv_receive')
                                              ->select('receivePaymentDate')
                                              ->where('supplierId',$supplier)
                                              ->where('projectTypeId',$request->filProjectType)

                                              ->where(function ($query) use ($fromDate,$toDate){
                                              $query->where('receivePaymentDate','>=',$fromDate)
                                              ->where('receivePaymentDate','<=',$toDate);
                                              })
                                              ->get();

                  $generateAndPaymentDatesSupplier=array();

                  foreach($generateDatesSupplier  as $generateDateSupplier )
                  {
                      array_push($generateAndPaymentDatesSupplier,$generateDateSupplier ->advPaymentDate);
                  }

                  foreach($paymentDatesSupplier as $paymentDateSupplier)
                  {
                      array_push($generateAndPaymentDatesSupplier,$paymentDateSupplier ->receivePaymentDate);
                  }
                  sort($generateAndPaymentDatesSupplier);


                  $advanceGeneratesSupplier= DB::table('acc_adv_register as t1')
                                                  ->join('gnr_supplier as t2','t1.supplierId','=','t2.id')
                                                  ->select('t1.*','t2.name')
                                                  ->where('t1.supplierId',$supplier)
                                                  ->where('t1.projectTypeId',$request->filProjectType)

                                                  ->get();

                  $advancePaymentsSupplier= DB::table('acc_adv_receive as t1')
                                                  ->join('gnr_supplier as t2','t1.supplierId','=','t2.id')
                                                  ->select('t1.*','t2.name')
                                                  ->where('t1.supplierId',$supplier)
                                                  ->where('t1.projectTypeId',$request->filProjectType)

                                                  ->get();


                  $advanceRegisterSumSupplier=DB::table('acc_adv_register')

                                                  ->where('supplierId',$supplier)
                                                  ->where('projectTypeId',$request->filProjectType)



                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                      $query->where('advPaymentDate','<',$fromDate);
                                                  })
                                                  ->sum('amount');

                  $advancePaymentSumSupplier= DB::table('acc_adv_receive')
                                                  ->where('supplierId',$supplier)
                                                  ->where('projectTypeId',$request->filProjectType)


                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                      $query->where('receivePaymentDate','<',$fromDate);
                                                  })
                                                  ->sum('amount');

                  $openningBalanceSupplier=$advanceRegisterSumSupplier-$advancePaymentSumSupplier;

                   /*ends employee part  */

                }
                /**************************************
                next part*******************/
                else if($request->filProject == "all" && $request->filProjectType =="all" && $request->filadvType =="all" )
                {
                    $generateDatesSupplier = DB::table('acc_adv_register')
                                                ->select('advPaymentDate')
                                                ->where('supplierId',$supplier)

                                                ->where(function ($query) use ($fromDate,$toDate){
                                                    $query->where('advPaymentDate','>=',$fromDate)
                                                    ->where('advPaymentDate','<=',$toDate);
                                                })
                                                ->get();

                    $paymentDatesSupplier = DB::table('acc_adv_receive')
                                                ->select('receivePaymentDate')
                                                ->where('supplierId',$supplier)
                                                ->where(function ($query) use ($fromDate,$toDate){
                                                $query->where('receivePaymentDate','>=',$fromDate)
                                                ->where('receivePaymentDate','<=',$toDate);
                                                })
                                                ->get();

                    $generateAndPaymentDatesSupplier=array();

                    foreach($generateDatesSupplier  as $generateDateSupplier )
                    {
                        array_push($generateAndPaymentDatesSupplier,$generateDateSupplier ->advPaymentDate);
                    }

                    foreach($paymentDatesSupplier as $paymentDateSupplier)
                    {
                        array_push($generateAndPaymentDatesSupplier,$paymentDateSupplier ->receivePaymentDate);
                    }
                    sort($generateAndPaymentDatesSupplier);



                    $advanceGeneratesSupplier= DB::table('acc_adv_register as t1')
                                                    ->join('gnr_supplier as t2','t1.supplierId','=','t2.id')
                                                    ->select('t1.*','t2.name')
                                                    ->where('t1.supplierId',$supplier)
                                                    ->get();

                    $advancePaymentsSupplier= DB::table('acc_adv_receive as t1')
                                                    ->join('gnr_supplier as t2','t1.supplierId','=','t2.id')
                                                    ->select('t1.*','t2.name')
                                                    ->where('t1.supplierId',$supplier)
                                                    ->get();


                    $advanceRegisterSumSupplier=DB::table('acc_adv_register')

                                                    ->where('supplierId',$supplier)
                                                    ->where(function ($query) use ($fromDate,$toDate){
                                                        $query->where('advPaymentDate','<',$fromDate);
                                                    })
                                                    ->sum('amount');

                    $advancePaymentSumSupplier= DB::table('acc_adv_receive')
                                                    ->where('supplierId',$supplier)
                                                    ->where(function ($query) use ($fromDate,$toDate){
                                                        $query->where('receivePaymentDate','<',$fromDate);
                                                    })
                                                    ->sum('amount');

                    $openningBalanceSupplier=$advanceRegisterSumSupplier-$advancePaymentSumSupplier;

                     /*ends employee part  */

                  }
                  /**************************************
                  next part*******************/
                  else if($request->filProject != "all" && $request->filProjectType !="all" && $request->filadvType!="all" )
                  {
                      $generateDatesSupplier = DB::table('acc_adv_register')
                                                  ->select('advPaymentDate')
                                                  ->where('supplierId',$supplier)
                                                  ->where('advRegType',$request->filadvType)
                                                  ->where('projectTypeId',$request->filProjectType)
                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                      $query->where('advPaymentDate','>=',$fromDate)
                                                      ->where('advPaymentDate','<=',$toDate);
                                                  })
                                                  ->get();

                      $paymentDatesSupplier = DB::table('acc_adv_receive')
                                                  ->select('receivePaymentDate')
                                                 ->where('supplierId',$supplier)
                                                  ->where('regTypeId',$request->filadvType)
                                                  ->where('projectTypeId',$request->filProjectType)

                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                  $query->where('receivePaymentDate','>=',$fromDate)
                                                  ->where('receivePaymentDate','<=',$toDate);
                                                  })
                                                  ->get();

                      $generateAndPaymentDatesSupplier=array();

                      foreach($generateDatesSupplier  as $generateDateSupplier )
                      {
                          array_push($generateAndPaymentDatesSupplier,$generateDateSupplier ->advPaymentDate);
                      }

                      foreach($paymentDatesSupplier as $paymentDateSupplier)
                      {
                          array_push($generateAndPaymentDatesSupplier,$paymentDateSupplier ->receivePaymentDate);
                      }
                      sort($generateAndPaymentDatesSupplier);


                      $advanceGeneratesSupplier= DB::table('acc_adv_register as t1')
                                                      ->join('gnr_supplier as t2','t1.supplierId','=','t2.id')
                                                      ->select('t1.*','t2.name')
                                                      ->where('t1.supplierId',$supplier)
                                                      ->where('t1.advRegType',$request->filadvType)
                                                      ->where('t1.projectTypeId',$request->filProjectType)

                                                      ->get();

                      $advancePaymentsSupplier= DB::table('acc_adv_receive as t1')
                                                      ->join('gnr_supplier as t2','t1.supplierId','=','t2.id')
                                                      ->select('t1.*','t2.name')
                                                      ->where('t1.supplierId',$supplier)
                                                      ->where('t1.regTypeId',$request->filadvType)
                                                      ->where('t1.projectTypeId',$request->filProjectType)

                                                      ->get();


                      $advanceRegisterSumSupplier=DB::table('acc_adv_register')

                                                      ->where('supplierId',$supplier)
                                                      ->where('advRegType',$request->filadvType)
                                                      ->where('projectTypeId',$request->filProjectType)



                                                      ->where(function ($query) use ($fromDate,$toDate){
                                                          $query->where('advPaymentDate','<',$fromDate);
                                                      })
                                                      ->sum('amount');

                      $advancePaymentSumSupplier= DB::table('acc_adv_receive')
                                                     ->where('supplierId',$supplier)
                                                      ->where('regTypeId',$request->filadvType)
                                                      ->where('projectTypeId',$request->filProjectType)


                                                      ->where(function ($query) use ($fromDate,$toDate){
                                                          $query->where('receivePaymentDate','<',$fromDate);
                                                      })
                                                      ->sum('amount');

                      $openningBalanceSupplier=$advanceRegisterSumSupplier-$advancePaymentSumSupplier;

                       /*ends employee part  */

                    }

                    /**************************************
                    next part*******************/
                    else if($request->filProject != "all" && $request->filProjectType =="all" && $request->filadvType!="all" )
                    {   echo "there you go";
                        $generateDatesSupplier = DB::table('acc_adv_register')
                                                    ->select('advPaymentDate')
                                                    ->where('supplierId',$supplier)
                                                    ->where('advRegType',$request->filadvType)
                                                    ->where('projectId',$request->filProject)
                                                    ->where(function ($query) use ($fromDate,$toDate){
                                                        $query->where('advPaymentDate','>=',$fromDate)
                                                        ->where('advPaymentDate','<=',$toDate);
                                                    })
                                                    ->get();

                        $paymentDatesSupplier = DB::table('acc_adv_receive')
                                                    ->select('receivePaymentDate')
                                                    ->where('supplierId',$supplier)
                                                    ->where('regTypeId',$request->filadvType)
                                                    ->where('projectId',$request->filProject)

                                                    ->where(function ($query) use ($fromDate,$toDate){
                                                    $query->where('receivePaymentDate','>=',$fromDate)
                                                    ->where('receivePaymentDate','<=',$toDate);
                                                    })
                                                    ->get();

                        $generateAndPaymentDatesSupplier=array();

                        foreach($generateDatesSupplier  as $generateDateSupplier )
                        {
                            array_push($generateAndPaymentDatesSupplier,$generateDateSupplier->advPaymentDate);
                        }

                        foreach($paymentDatesSupplier as $paymentDateSupplier)
                        {
                            array_push($generateAndPaymentDatesSupplier,$paymentDateSupplier ->receivePaymentDate);
                        }
                        sort($generateAndPaymentDatesSupplier);


                        $advanceGeneratesSupplier= DB::table('acc_adv_register as t1')
                                                        ->join('gnr_branch as t2','t1.supplierId','=','t2.id')
                                                        ->select('t1.*','t2.name')
                                                        ->where('t1.supplierId',$supplier)
                                                        ->where('t1.advRegType',$request->filadvType)
                                                        ->where('t1.projectId',$request->filProject)

                                                        ->get();

                        $advancePaymentsSupplier= DB::table('acc_adv_receive as t1')
                                                        ->join('gnr_branch as t2','t1.supplierId','=','t2.id')
                                                        ->select('t1.*','t2.name')
                                                        ->where('t1.supplierId',$supplier)
                                                        ->where('t1.regTypeId',$request->filadvType)
                                                        ->where('t1.projectId',$request->filProject)
                                                        ->get();


                        $advanceRegisterSumSupplier=DB::table('acc_adv_register')
                                                        ->where('supplierId',$supplier)
                                                        ->where('advRegType',$request->filadvType)
                                                        ->where('projectId',$request->filProject)
                                                        ->where(function ($query) use ($fromDate,$toDate){
                                                            $query->where('advPaymentDate','<',$fromDate);
                                                        })
                                                        ->sum('amount');

                        $advancePaymentSumSupplier= DB::table('acc_adv_receive')
                                                        ->where('supplierId',$supplier)
                                                        ->where('regTypeId',$request->filadvType)
                                                        ->where('projectId',$request->filProject)
                                                        ->where(function ($query) use ($fromDate,$toDate){
                                                            $query->where('receivePaymentDate','<',$fromDate);
                                                        })
                                                        ->sum('amount');

                        $openningBalanceSupplier=$advanceRegisterSumSupplier-$advancePaymentSumSupplier;

                         /*ends employee part  */

                      }





        $vatRegisterReportLowerPartArr= array(
            'tableType'             => $tableType,
            'fromDate'              => $fromDate,
            'toDate'                => $toDate,
            'supId'                 => $request->filSupplier,
            'openningBalanceSupplier'=> $openningBalanceSupplier,
            'advanceGeneratesSupplier' => $advanceGeneratesSupplier,
            'generateAndPaymentDatesSupplier' => $generateAndPaymentDatesSupplier,
            'advancePaymentsSupplier'         => $advancePaymentsSupplier


        );
        }

        /*individual houseOwner starts*/

        elseif($supplier =="all" && $houseOwner !="all" && $employee == "all" )
        {
            $tableType="4";
            $generateSuppliers=array();
            $generateHouseOwners=array();
            $paymentSuppliers=array();
            $paymentHouseOwners=array();
            $collectionsAll =[];
            $collectionsAll =[];
            $collectionsAll =[];
            if($request->filProject != "all" && $request->filProjectType =="all" && $request->filadvType=="all" )
            {

                $generateDatesHouseOwner = DB::table('acc_adv_register')
                                            ->select('advPaymentDate')
                                            ->where('houseOwnerId',$houseOwner)
                                            ->where('projectId',$request->filProject)
                                            ->where(function ($query) use ($fromDate,$toDate){
                                                $query->where('advPaymentDate','>=',$fromDate)
                                                ->where('advPaymentDate','<=',$toDate);
                                            })
                                            ->get();

                $paymentDatesHouseOwner = DB::table('acc_adv_receive')
                                            ->select('receivePaymentDate')
                                            ->where('houseOwnerId',$houseOwner)
                                            ->where('projectId',$request->filProject)
                                            ->where(function ($query) use ($fromDate,$toDate){
                                            $query->where('receivePaymentDate','>=',$fromDate)
                                            ->where('receivePaymentDate','<=',$toDate);
                                            })
                                            ->get();

                $generateAndPaymentDatesHouseOwner=array();

                foreach($generateDatesHouseOwner  as $generateDateHouseOwner )
                {
                    array_push($generateAndPaymentDatesHouseOwner,$generateDateHouseOwner->advPaymentDate);
                }

                foreach($paymentDatesHouseOwner as $paymentDateHouseOwner)
                {
                    array_push($generateAndPaymentDatesHouseOwner,$paymentDateHouseOwner ->receivePaymentDate);
                }
                sort($generateAndPaymentDatesHouseOwner);


                $advanceGeneratesHouseOwner= DB::table('acc_adv_register as t1')
                                                ->join('gnr_house_Owner as t2','t1.houseOwnerId','=','t2.id')
                                                ->select('t1.*','t2.houseOwnerName')
                                                ->where('t1.houseOwnerId',$houseOwner)
                                                ->where('t1.projectId',$projectId)
                                                ->get();

                $advancePaymentsHouseOwner= DB::table('acc_adv_receive as t1')
                                                ->join('gnr_house_Owner as t2','t1.houseOwnerId','=','t2.id')
                                                ->select('t1.*','t2.houseOwnerName')
                                                ->where('t1.houseOwnerId',$houseOwner)
                                                ->where('t1.projectId',$projectId)
                                                ->get();


                $advanceRegisterSumHouseOwner=DB::table('acc_adv_register')
                                                ->where('houseOwnerId',$houseOwner)
                                                ->where('projectId',$projectId)
                                                ->where(function ($query) use ($fromDate,$toDate){
                                                    $query->where('advPaymentDate','<',$fromDate);
                                                })
                                                ->sum('amount');

                $advancePaymentSumHouseOwner= DB::table('acc_adv_receive')
                                                ->where('houseOwnerId',$houseOwner)
                                                ->where('projectId',$projectId)

                                                ->where(function ($query) use ($fromDate,$toDate){
                                                    $query->where('receivePaymentDate','<',$fromDate);
                                                })
                                                ->sum('amount');

                $openningBalanceHouseOwner=$advanceRegisterSumHouseOwner-$advancePaymentSumHouseOwner;

             /*ends employee part  */

           }

            /**************************************
            next part*******************/
            else if($request->filProject == "all" && $request->filProjectType =="all" && $request->filadvType!="all" )
            {
                $generateDatesHouseOwner = DB::table('acc_adv_register')
                                            ->select('advPaymentDate')
                                            ->where('houseOwnerId',$houseOwner)
                                            ->where('advRegType',$request->filadvType)
                                            ->where(function ($query) use ($fromDate,$toDate){
                                                $query->where('advPaymentDate','>=',$fromDate)
                                                ->where('advPaymentDate','<=',$toDate);
                                            })
                                            ->get();


                $paymentDatesHouseOwner = DB::table('acc_adv_receive')
                                            ->select('receivePaymentDate')
                                            ->where('houseOwnerId',$houseOwner)
                                            ->where('regTypeId',$request->filadvType)
                                            ->where(function ($query) use ($fromDate,$toDate){
                                            $query->where('receivePaymentDate','>=',$fromDate)
                                            ->where('receivePaymentDate','<=',$toDate);
                                            })
                                            ->get();


                $generateAndPaymentDatesHouseOwner=array();

                foreach($generateDatesHouseOwner  as $generateDateHouseOwner )
                {
                    array_push($generateAndPaymentDatesHouseOwner,$generateDateHouseOwner ->advPaymentDate);
                }

                foreach($paymentDatesHouseOwner as $paymentDateHouseOwner)
                {
                    array_push($generateAndPaymentDatesHouseOwner,$paymentDateHouseOwner ->receivePaymentDate);
                }
                sort($generateAndPaymentDatesHouseOwner);


                $advanceGeneratesHouseOwner= DB::table('acc_adv_register as t1')
                                                ->join('gnr_house_Owner as t2','t1.houseOwnerId','=','t2.id')
                                                ->select('t1.*','t2.houseOwnerName')
                                                ->where('t1.houseOwnerId',$houseOwner)
                                                ->where('t1.advRegType',$request->filadvType)

                                                ->get();


                $advancePaymentsHouseOwner= DB::table('acc_adv_receive as t1')
                                                ->join('gnr_house_Owner as t2','t1.houseOwnerId','=','t2.id')
                                                ->select('t1.*','t2.houseOwnerName')
                                                ->where('t1.houseOwnerId',$houseOwner)
                                                ->where('t1.regTypeId',$request->filadvType)

                                                ->get();


                $advanceRegisterSumHouseOwner=DB::table('acc_adv_register')

                                                ->where('houseOwnerId',$houseOwner)
                                                ->where('advRegType',$request->filadvType)
                                                ->where(function ($query) use ($fromDate,$toDate){
                                                    $query->where('advPaymentDate','<',$fromDate);
                                                })
                                                ->sum('amount');

                $advancePaymentSumHouseOwner= DB::table('acc_adv_receive')
                                                ->where('supplierId',$supplier)
                                                ->where('regTypeId',$request->filadvType)


                                                ->where(function ($query) use ($fromDate,$toDate){
                                                    $query->where('receivePaymentDate','<',$fromDate);
                                                })
                                                ->sum('amount');

                $openningBalanceHouseOwner=$advanceRegisterSumHouseOwner-$advancePaymentSumHouseOwner;



              }

              /**************************************
              next part*******************/
              else if($request->filProject != "all" && $request->filProjectType !="all" && $request->filadvType =="all" )
              {
                  $generateDatesHouseOwner = DB::table('acc_adv_register')
                                              ->select('advPaymentDate')
                                              ->where('houseOwnerId',$houseOwner)
                                              ->where('projectTypeId',$request->filProjectType)
                                              ->where(function ($query) use ($fromDate,$toDate){
                                                  $query->where('advPaymentDate','>=',$fromDate)
                                                  ->where('advPaymentDate','<=',$toDate);
                                              })
                                              ->get();

                  $paymentDatesHouseOwner = DB::table('acc_adv_receive')
                                              ->select('receivePaymentDate')
                                              ->where('houseOwnerId',$houseOwner)
                                              ->where('projectTypeId',$request->filProjectType)

                                              ->where(function ($query) use ($fromDate,$toDate){
                                              $query->where('receivePaymentDate','>=',$fromDate)
                                              ->where('receivePaymentDate','<=',$toDate);
                                              })
                                              ->get();

                  $generateAndPaymentDatesHouseOwner=array();

                  foreach($generateDatesHouseOwner  as $generateDateHouseOwner )
                  {
                      array_push($generateAndPaymentDatesHouseOwner,$generateDateHouseOwner ->advPaymentDate);
                  }

                  foreach($paymentDatesHouseOwner as $paymentDateHouseOwner)
                  {
                      array_push($generateAndPaymentDatesHouseOwner,$paymentDateHouseOwner ->receivePaymentDate);
                  }
                  sort($generateAndPaymentDatesHouseOwner);


                  $advanceGeneratesHouseOwner= DB::table('acc_adv_register as t1')
                                                  ->join('gnr_house_Owner as t2','t1.houseOwnerId','=','t2.id')
                                                  ->select('t1.*','t2.houseOwnerName')
                                                  ->where('t1.houseOwnerId',$houseOwner)
                                                  ->where('t1.projectTypeId',$request->filProjectType)

                                                  ->get();

                  $advancePaymentsHouseOwner= DB::table('acc_adv_receive as t1')
                                                  ->join('gnr_house_Owner as t2','t1.houseOwnerId','=','t2.id')
                                                  ->select('t1.*','t2.houseOwnerName')
                                                  ->where('t1.houseOwnerId',$houseOwner)
                                                  ->where('t1.projectTypeId',$request->filProjectType)
                                                  ->get();


                  $advanceRegisterSumHouseOwner=DB::table('acc_adv_register')
                                                  ->where('houseOwnerId',$houseOwner)
                                                  ->where('projectTypeId',$request->filProjectType)



                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                      $query->where('advPaymentDate','<',$fromDate);
                                                  })
                                                  ->sum('amount');

                  $advancePaymentSumHouseOwner= DB::table('acc_adv_receive')
                                                  ->where('houseOwnerId',$houseOwner)
                                                  ->where('projectTypeId',$request->filProjectType)


                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                      $query->where('receivePaymentDate','<',$fromDate);
                                                  })
                                                  ->sum('amount');

                  $openningBalanceHouseOwner=$advanceRegisterSumHouseOwner-$advancePaymentSumHouseOwner;

                   /*ends employee part  */

                }
                /**************************************
                next part*******************/
                else if($request->filProject == "all" && $request->filProjectType =="all" && $request->filadvType =="all" )
                {
                    $generateDatesHouseOwner = DB::table('acc_adv_register')
                                                ->select('advPaymentDate')
                                                ->where('houseOwnerId',$houseOwner)
                                                ->where(function ($query) use ($fromDate,$toDate){
                                                    $query->where('advPaymentDate','>=',$fromDate)
                                                    ->where('advPaymentDate','<=',$toDate);
                                                })
                                                ->get();

                    $paymentDatesHouseOwner = DB::table('acc_adv_receive')
                                                ->select('receivePaymentDate')
                                                ->where('houseOwnerId',$houseOwner)
                                                ->where(function ($query) use ($fromDate,$toDate){
                                                $query->where('receivePaymentDate','>=',$fromDate)
                                                ->where('receivePaymentDate','<=',$toDate);
                                                })
                                                ->get();

                    $generateAndPaymentDatesHouseOwner=array();

                    foreach($generateDatesHouseOwner  as $generateDateHouseOwner )
                    {
                        array_push($generateAndPaymentDatesHouseOwner,$generateDateHouseOwner ->advPaymentDate);
                    }

                    foreach($paymentDatesHouseOwner as $paymentDateHouseOwner)
                    {
                        array_push($generateAndPaymentDatesHouseOwner,$paymentDateHouseOwner ->receivePaymentDate);
                    }
                    sort($generateAndPaymentDatesHouseOwner);



                    $advanceGeneratesHouseOwner= DB::table('acc_adv_register as t1')
                                                    ->join('gnr_house_Owner as t2','t1.houseOwnerId','=','t2.id')
                                                    ->select('t1.*','t2.houseOwnerName')
                                                    ->where('t1.houseOwnerId',$houseOwner)
                                                    ->get();

                    $advancePaymentsHouseOwner= DB::table('acc_adv_receive as t1')
                                                    ->join('gnr_house_Owner as t2','t1.houseOwnerId','=','t2.id')
                                                    ->select('t1.*','t2.houseOwnerName')
                                                    ->where('t1.houseOwnerId',$houseOwner)
                                                    ->get();


                    $advanceRegisterSumHouseOwner=DB::table('acc_adv_register')

                                                    ->where('houseOwnerId',$houseOwner)
                                                    ->where(function ($query) use ($fromDate,$toDate){
                                                        $query->where('advPaymentDate','<',$fromDate);
                                                    })
                                                    ->sum('amount');

                    $advancePaymentSumHouseOwner= DB::table('acc_adv_receive')
                                                    ->where('houseOwnerId',$houseOwner)
                                                    ->where(function ($query) use ($fromDate,$toDate){
                                                        $query->where('receivePaymentDate','<',$fromDate);
                                                    })
                                                    ->sum('amount');

                    $openningBalanceHouseOwner=$advanceRegisterSumHouseOwner-$advancePaymentSumHouseOwner;

                     /*ends employee part  */

                  }
                  /**************************************
                  next part*******************/
                  else if($request->filProject != "all" && $request->filProjectType !="all" && $request->filadvType!="all" )
                  {
                      $generateDatesHouseOwner = DB::table('acc_adv_register')
                                                  ->select('advPaymentDate')
                                                  ->where('houseOwnerId',$houseOwner)
                                                  ->where('advRegType',$request->filadvType)
                                                  ->where('projectTypeId',$request->filProjectType)
                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                      $query->where('advPaymentDate','>=',$fromDate)
                                                      ->where('advPaymentDate','<=',$toDate);
                                                  })
                                                  ->get();

                      $paymentDatesHouseOwner = DB::table('acc_adv_receive')
                                                  ->select('receivePaymentDate')
                                                  ->where('houseOwnerId',$houseOwner)
                                                  ->where('regTypeId',$request->filadvType)
                                                  ->where('projectTypeId',$request->filProjectType)

                                                  ->where(function ($query) use ($fromDate,$toDate){
                                                  $query->where('receivePaymentDate','>=',$fromDate)
                                                  ->where('receivePaymentDate','<=',$toDate);
                                                  })
                                                  ->get();

                      $generateAndPaymentDatesHouseOwner=array();

                      foreach($generateDatesHouseOwner  as $generateDateHouseOwner )
                      {
                          array_push($generateAndPaymentDatesHouseOwner,$generateDateHouseOwner ->advPaymentDate);
                      }

                      foreach($paymentDatesHouseOwner as $paymentDateHouseOwner)
                      {
                          array_push($generateAndPaymentDatesHouseOwner,$paymentDateHouseOwner ->receivePaymentDate);
                      }
                      sort($generateAndPaymentDatesHouseOwner);


                      $advanceGeneratesHouseOwner= DB::table('acc_adv_register as t1')
                                                      ->join('gnr_house_Owner as t2','t1.houseOwnerId','=','t2.id')
                                                      ->select('t1.*','t2.houseOwnerName')
                                                      ->where('t1.houseOwnerId',$houseOwner)
                                                      ->where('t1.advRegType',$request->filadvType)
                                                      ->where('projectTypeId',$request->filProjectType)

                                                      ->get();

                      $advancePaymentsHouseOwner= DB::table('acc_adv_receive as t1')
                                                      ->join('gnr_house_Owner as t2','t1.houseOwnerId','=','t2.id')
                                                      ->select('t1.*','t2.houseOwnerName')
                                                      ->where('t1.houseOwnerId',$houseOwner)
                                                      ->where('t1.regTypeId',$request->filadvType)
                                                      ->where('projectTypeId',$request->filProjectType)

                                                      ->get();


                      $advanceRegisterSumHouseOwner=DB::table('acc_adv_register')

                                                      ->where('houseOwnerId',$houseOwner)
                                                      ->where('advRegType',$request->filadvType)
                                                      ->where('projectTypeId',$request->filProjectType)
                                                      ->where(function ($query) use ($fromDate,$toDate){
                                                          $query->where('advPaymentDate','<',$fromDate);
                                                      })
                                                      ->sum('amount');

                      $advancePaymentSumHouseOwner= DB::table('acc_adv_receive')
                                                      ->where('houseOwnerId',$houseOwner)
                                                      ->where('regTypeId',$request->filadvType)
                                                      ->where('projectTypeId',$request->filProjectType)


                                                      ->where(function ($query) use ($fromDate,$toDate){
                                                          $query->where('receivePaymentDate','<',$fromDate);
                                                      })
                                                      ->sum('amount');

                      $openningBalanceHouseOwner=$advanceRegisterSumHouseOwner-$advancePaymentSumHouseOwner;

                       /*ends employee part  */

                    }

                    /**************************************
                    next part*******************/
                    else if($request->filProject != "all" && $request->filProjectType =="all" && $request->filadvType!="all" )
                    {
                        $generateDatesHouseOwner = DB::table('acc_adv_register')
                                                    ->select('advPaymentDate')
                                                    ->where('houseOwnerId',$houseOwner)
                                                    ->where('advRegType',$request->filadvType)
                                                    ->where('projectId',$request->filProject)
                                                    ->where(function ($query) use ($fromDate,$toDate){
                                                        $query->where('advPaymentDate','>=',$fromDate)
                                                        ->where('advPaymentDate','<=',$toDate);
                                                    })
                                                    ->get();

                        $paymentDatesHouseOwner = DB::table('acc_adv_receive')
                                                    ->select('receivePaymentDate')
                                                    ->where('houseOwnerId',$houseOwner)
                                                    ->where('regTypeId',$request->filadvType)
                                                    ->where('projectId',$request->filProject)

                                                    ->where(function ($query) use ($fromDate,$toDate){
                                                    $query->where('receivePaymentDate','>=',$fromDate)
                                                    ->where('receivePaymentDate','<=',$toDate);
                                                    })
                                                    ->get();

                        $generateAndPaymentDatesHouseOwner=array();

                        foreach($generateDatesHouseOwner  as $generateDateHouseOwner )
                        {
                            array_push($generateAndPaymentDatesHouseOwner,$generateDateHouseOwner ->advPaymentDate);
                        }

                        foreach($paymentDatesHouseOwner as $paymentDateHouseOwner)
                        {
                            array_push($generateAndPaymentDatesHouseOwner,$paymentDateHouseOwner ->receivePaymentDate);
                        }
                        sort($generateAndPaymentDatesHouseOwner);


                        $advanceGeneratesHouseOwner= DB::table('acc_adv_register as t1')
                                                        ->join('gnr_house_Owner as t2','t1.houseOwnerId','=','t2.id')
                                                        ->select('t1.*','t2.houseOwnerName')
                                                        ->where('t1.houseOwnerId',$houseOwner)
                                                        ->where('t1.advRegType',$request->filadvType)
                                                        ->where('t1.projectId',$request->filProject)

                                                        ->get();

                        $advancePaymentsHouseOwner= DB::table('acc_adv_receive as t1')
                                                        ->join('gnr_house_Owner as t2','t1.houseOwnerId','=','t2.id')
                                                        ->select('t1.*','t2.houseOwnerName')
                                                        ->where('t1.houseOwnerId',$houseOwner)
                                                        ->where('t1.regTypeId',$request->filadvType)
                                                        ->where('t1.projectId',$request->filProject)
                                                        ->get();


                        $advanceRegisterSumHouseOwner=DB::table('acc_adv_register')
                                                        ->where('houseOwnerId',$houseOwner)
                                                        ->where('advRegType',$request->filadvType)
                                                        ->where('projectId',$request->filProject)
                                                        ->where(function ($query) use ($fromDate,$toDate){
                                                            $query->where('advPaymentDate','<',$fromDate);
                                                        })
                                                        ->sum('amount');

                        $advancePaymentSumHouseOwner= DB::table('acc_adv_receive')
                                                        ->where('houseOwnerId',$houseOwner)
                                                        ->where('regTypeId',$request->filadvType)
                                                        ->where('projectId',$request->filProject)
                                                        ->where(function ($query) use ($fromDate,$toDate){
                                                            $query->where('receivePaymentDate','<',$fromDate);
                                                        })
                                                        ->sum('amount');

                        $openningBalanceHouseOwner=$advanceRegisterSumHouseOwner-$advancePaymentSumHouseOwner;

                         /*ends employee part  */

                      }





        $vatRegisterReportLowerPartArr= array(
            'tableType'             => $tableType,
            'fromDate'              => $fromDate,
            'toDate'                => $toDate,
            'houseId'                 => $request->filHouseOwner,
            'openningBalanceHouseOwner'=> $openningBalanceHouseOwner,
            'advanceGeneratesHouseOwner' => $advanceGeneratesHouseOwner,
            'generateAndPaymentDatesHouseOwner' => $generateAndPaymentDatesHouseOwner,
            'advancePaymentsHouseOwner'         => $advancePaymentsHouseOwner


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
