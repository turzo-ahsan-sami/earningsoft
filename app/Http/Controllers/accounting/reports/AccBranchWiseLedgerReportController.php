<?php

namespace App\Http\Controllers\accounting\reports;


use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

use App\Service\DatabasePartitionHelper;


use App\Http\Controllers\accounting\Accounting;



class AccBranchWiseLedgerReportController extends Controller
{    

    use GetSoftwareDate;

    protected $Accounting;
    
    public function __construct() {
        $this->Accounting = new Accounting;
    }

    public function index(){
    	$userBranchId = Auth::user()->branchId;
        $userCompanyId = Auth::user()->company_id_fk;
        $userProjectId = Auth::user()->project_id_fk;
     	$userProjectTypeId = (int) DB::table('gnr_branch')->where('id',$userBranchId)->value('projectTypeId');

        if($userBranchId==1){

            $ledgerOptions =   array('' => '--Select Ledger--')
                                +
                                DB::table('acc_account_ledger')->where('isGroupHead', 0)->where('companyIdFk', $userCompanyId)->select(DB::raw("CONCAT(code, '-', name) AS nameWithCode"), 'id')->orderBy('code')->pluck('nameWithCode', 'id')->toArray();

        }else{

            $branchIdArray=[$userBranchId];
            $ledgerIdArr = $this->Accounting->getBranchArrWiseLedgerIds($userProjectId, $branchIdArray);

            $ledgerOptions =   array('' => '--Select Ledger--')
                                +
                                DB::table('acc_account_ledger')->whereIn('id', $ledgerIdArr )->where('isGroupHead', 0)->where('companyIdFk', $userCompanyId)->select(DB::raw("CONCAT(code, '-', name) AS nameWithCode"), 'id')->orderBy('code')->pluck('nameWithCode', 'id')->toArray();
		}

        $projectsOption =   array('' => '--Select Project--') 
                            +
                            $this->Accounting->getProjectList();


        $reportLevelList = array(
            'Branch'  =>  'Branch',
            'Area'    =>  'Area',
            'Zone'    =>  'Zone',
            'Region'  =>  'Region'
        );

        $zeroOption = array(
            1 => 'Yes',
            2 => 'No'
        );

        $roundUpOption = array(
            2 => 'No',
            1 => 'Yes'
        );


        $branchList = array();  
        // array('-1' => 'All with HO', '-2' => 'All with out HO')  + $this->Accounting->getBranchList();

                    
        $areaList = $this->Accounting->getAreaList();

        $zoneList = $this->Accounting->getZoneList();

        $regionList = $this->Accounting->getRegionList();


        /// Loan Options
        $reportOptions = array(
            1   => 'Consolidate',
            2   => 'Ledger Details'
        );

     
        
        $projectTypesOption =   array('-1' => 'All') 
                                +
                                $this->Accounting->getProjectTypeList();

        $voucherTypesOption =   array('-1' => 'All') 
                                +
                                $this->Accounting->getVoucherTypeList();

        $filteringArr=array(
            'userBranchId'          => $userBranchId,
            'userProjectId'         => $userProjectId,
            'userProjectTypeId'     => $userProjectTypeId, 
            'projectsOption'        => $projectsOption,
            'reportLevelList'       => $reportLevelList,
            'areaList'              => $areaList,
            'zoneList'              => $zoneList,
            'regionList'            => $regionList, 
            'branchList'            => $branchList,
            'reportOptions'         => $reportOptions, 
            'projectTypesOption'    => $projectTypesOption, 
            'ledgerOptions'         => $ledgerOptions, 
            'voucherTypesOption'    => $voucherTypesOption,
            'zeroOption'            => $zeroOption,
            'roundUpOption'         => $roundUpOption

        );
        
	    
		return view('accounting.reports.branchWiseLedgerReport.reportFilteringPart',$filteringArr);
    }


    public function branchWiseLedgerReport(Request $request){
        $dbhelper = new DatabasePartitionHelper();        
    
        $projectValue       = (int)$request->filProject;
        $filReportLevel     =  $request->filReportLevel;
        $branchValue        = (int)$request->filBranch;
        $areaValue          = (int)$request->filArea;
        $zoneValue          = (int)$request->filZone;
        $regionValue        = (int)$request->filRegion;
        $projectTypeValue   = (int)$request->filProjectType;
        $ledgerIdValue      = (int)$request->filLedgerId;
        $voucherTypeValue   = (int)$request->filVoucherType;
        $filReportOption    = (int)$request->filReportOption;
        $startDateValue     = Carbon::parse($request->filStartDate)->format('Y-m-d');
        $endDateValue       = Carbon::parse($request->filEndDate)->format('Y-m-d');

        $searchedProjectId       = (int)$request->filProject;
        $searchedReportLevel     = $request->filReportLevel;
        $searchedBranchId        = (int)$request->filBranch;
        $searchedProjectTypeId   = (int)$request->filProjectType;
        $searchedLedgerId        = (int)$request->filLedgerId;
        $searchedVoucherTypeId   = (int)$request->filVoucherType;
        $filReportOption         = (int)$request->filReportOption;
        $searchedRoundUp         = (int)$request->fillRoundUp;
        $searchedWithZero        = (int)$request->fillWithZero;
        $searchedDateFrom        = Carbon::parse($request->filStartDate)->format('Y-m-d');
        $searchedDateTo          = Carbon::parse($request->filEndDate)->format('Y-m-d');

        
        $userBranchId = Auth::user()->branchId;
        $userCompanyId = Auth::user()->company_id_fk;
        $userProjectId = (int) DB::table('gnr_branch')->where('id',$userBranchId)->value('projectId');
        $userProjectTypeId = (int) DB::table('gnr_branch')->where('id',$userBranchId)->value('projectTypeId');
        $areaName =  DB::table('gnr_area')->where('id',$areaValue)->value('name');
        $zoneName =  DB::table('gnr_zone')->where('id',$zoneValue)->value('name');
        $regionName =  DB::table('gnr_region')->where('id',$regionValue)->value('name');

        
        if($filReportLevel == 'Branch') $branchIdArr=DB::table('gnr_branch')->pluck('id')->toArray();
        else $branchIdArr=$this->getFilteredBranhIds($request->filReportLevel,$request->filBranch,$request->filArea,$request->filZone,$request->filRegion);
       
        $projectTypeIdArr=DB::table('gnr_project_type');

        if ($userBranchId==1) {
            if($projectValue==1){
                if ($branchValue==-1) $branchIdArr=$branchIdArr;
                else if ($branchValue==-2) $branchIdArr=DB::table('gnr_branch')->where('id', '!=' , 1)->pluck('id')->toArray();
                else $branchIdArr=DB::table('gnr_branch')->where('id',$branchValue)->pluck('id')->toArray();
            }
            else{
                $branchIdArr=DB::table('gnr_branch')->where('id', 1)->pluck('id')->toArray();
            }

            if ($projectTypeValue==-1) $projectTypeIdArr=$projectTypeIdArr;
            else $projectTypeIdArr=$projectTypeIdArr->where('id',$projectTypeValue);  
        }
        else{     
            $branchIdArr=DB::table('gnr_branch')->where('id',$userBranchId)->pluck('id')->toArray();
            $projectTypeIdArr=$projectTypeIdArr->where('id',$userProjectTypeId);
            $projectValue=$userProjectId;
            $branchValue=$userBranchId;
            $projectTypeValue=$userProjectTypeId;
        }

        //VoucherType
        $voucherTypeIdArray = array();
        if ($voucherTypeValue==-1) $voucherTypeIdArray = DB::table('acc_voucher_type')->pluck('id')->toArray();
        else array_push($voucherTypeIdArray, (int) json_decode($voucherTypeValue));
        
        
        $projectTypeIdArr = $projectTypeIdArr->pluck('id')->toArray();


        $branchIdArr = DB::table('gnr_branch')
        ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')                                               
        ->whereIn('id', $branchIdArr)
        ->orderBy('branchCode')
        ->pluck('nameWithCode', 'id')
        ->toArray();
        
        $currFiYr = DB::table('gnr_fiscal_year')
        ->where('fyStartDate','<=', $startDateValue)
        ->where('fyEndDate','>=', $startDateValue)
        ->select('fyStartDate', 'fyEndDate', 'id')
        ->first();
        

        $preFiYrStartDate = Carbon::parse($currFiYr->fyStartDate)->subYear()->format('Y-m-d');
        $preFiYrEndDate = Carbon::parse($currFiYr->fyEndDate)->subYear()->format('Y-m-d');
        
        $preFiYrId = DB::table('gnr_fiscal_year')->where('fyStartDate',$preFiYrStartDate)->where('fyEndDate',$preFiYrEndDate)->value('id');

        $object = array();

        $totalOpeningBalanceAmount = $totalDebitAmount = $totalCreditAmount = $totalClosingBalance=0;

        $index=0;
         

        $acc_opening_balance = $dbhelper->getUserPartitionWiseDBTableName('acc_opening_balance');        
        $acc_voucher = $dbhelper->getUserPartitionWiseDBTableName('acc_voucher');
        // $acc_voucher = $dbhelper->getPartitionWiseDBTableNameForJoin('acc_voucher', 'av');
        
        $voucherIdMatchedForOpBal = DB::table($acc_voucher)
        ->where('companyId',$userCompanyId)
        ->where('projectId', $projectValue)
        ->whereIn('projectTypeId', $projectTypeIdArr)
        ->whereIn('voucherTypeId',$voucherTypeIdArray)
        ->where('voucherDate','>=', $currFiYr->fyStartDate)
        ->where('voucherDate','<', $startDateValue)
        ->where('status', 1);

        $voucherIdMatched = DB::table($acc_voucher)
        ->where('companyId',$userCompanyId)
        ->where('projectId', $projectValue)
        ->whereIn('projectTypeId', $projectTypeIdArr)
        ->whereIn('voucherTypeId', $voucherTypeIdArray)
        ->where('voucherDate','>=',$startDateValue)
        ->where('voucherDate','<=',$endDateValue)
        ->where('status', 1);

        $debitAccVoDetails = DB::table('acc_voucher_details')
        ->where('debitAcc', $ledgerIdValue)
        ->where('status', 1);

        $creditAccVoDetails = DB::table('acc_voucher_details')
        ->where('creditAcc', $ledgerIdValue)
        ->where('status', 1);

                                       
        // dd($voucherIdMatchedForOpBal, $voucherIdMatched);


        foreach ($branchIdArr as $branchIdVal => $nameWithCode) {

            $opBalAmountByPreFiYr = DB::table($acc_opening_balance)
            ->where('companyIdFk',$userCompanyId)
            ->where('projectId',$projectValue)
            ->whereIn('projectTypeId',$projectTypeIdArr)
            ->where('branchId',$branchIdVal)
            ->where('ledgerId', $ledgerIdValue)
            ->where('fiscalYearId', $preFiYrId)
            ->sum('balanceAmount');
            
            $voucherIdMatchedForOpBalByBranch = clone($voucherIdMatchedForOpBal);
            $voucherIdMatchedForOpBalByBranch = $voucherIdMatchedForOpBalByBranch->where('branchId', $branchIdVal)->pluck('id')->toArray();

            $debitAccAmountForOpBal = clone($debitAccVoDetails);
            $debitAccAmountForOpBal = $debitAccAmountForOpBal->whereIn('voucherId', $voucherIdMatchedForOpBalByBranch)->sum('amount');

            $creditAccAmountForOpBal = clone($creditAccVoDetails);
            $creditAccAmountForOpBal = $creditAccAmountForOpBal->whereIn('voucherId', $voucherIdMatchedForOpBalByBranch)->sum('amount');            

            $openingBalanceAmount = $opBalAmountByPreFiYr+$debitAccAmountForOpBal-$creditAccAmountForOpBal;

            $voucherIdMatchedByBranch = clone($voucherIdMatched);
            $voucherIdMatchedByBranch = $voucherIdMatchedByBranch->where('branchId', $branchIdVal)->pluck('id')->toArray();
            
            $debitAmountByRange = clone($debitAccVoDetails);
            $debitAmountByRange = $debitAmountByRange->whereIn('voucherId', $voucherIdMatchedByBranch)->sum('amount');

            $creditAmountByRange = clone($creditAccVoDetails);
            $creditAmountByRange = $creditAmountByRange->whereIn('voucherId', $voucherIdMatchedByBranch)->sum('amount');

            $closingBalance = $openingBalanceAmount+$debitAmountByRange-$creditAmountByRange;

            $totalOpeningBalanceAmount  +=$openingBalanceAmount;
            $totalDebitAmount           +=$debitAmountByRange;
            $totalCreditAmount          +=$creditAmountByRange;
            $totalClosingBalance        +=$closingBalance;

            $branchesInfoObj[$index] = (object) array(
                'id'                => $branchIdVal,
                'slNo'              => $index+1,
                'nameWithCode'      => $nameWithCode,
                'openingBalance'    => $openingBalanceAmount,
                'debitAmount'       => $debitAmountByRange,
                'creditAmount'      => $creditAmountByRange,
                'closingBalance'    => $closingBalance,
            );

            $index++;
        }


        $data = array(
            'projectValue'              => $projectValue,
            'branchValue'               => $branchValue,
            'areaName'                  => $areaName,
            'zoneName'                  => $zoneName,
            'regionName'                => $regionName,
            'projectTypeValue'          => $projectTypeValue,
            'ledgerIdValue'             => $ledgerIdValue,
            'voucherTypeValue'          => $voucherTypeValue,
            'startDateValue'            => $startDateValue,
            'endDateValue'              => $endDateValue,
            'userBranchId'              => $userBranchId,
            'user_branch_id'            => $userBranchId,
            'userCompanyId'             => $userCompanyId,
            'branchesInfoObj'           => $branchesInfoObj,
            'totalOpeningBalanceAmount' => $totalOpeningBalanceAmount,
            'totalDebitAmount'          => $totalDebitAmount,
            'totalCreditAmount'         => $totalCreditAmount,
            'totalClosingBalance'       => $totalClosingBalance,
            'filReportOption'           => $filReportOption,
            'searchedProjectId'         => $searchedProjectId,
            'searchedProjectTypeId'     => $searchedProjectTypeId, 
            'searchedBranchId'          => $searchedBranchId, 
            'searchedLedgerId'          => $searchedLedgerId, 
            'searchedVoucherTypeId'     => $searchedVoucherTypeId, 
            'searchedDateFrom'          => $searchedDateFrom,
            'searchedDateTo'            => $searchedDateTo,             
            'userProjectId'             => $userProjectId,
            'searchedReportLevel'       => $searchedReportLevel,
            'searchedRoundUp'           => $searchedRoundUp,
            'searchedWithZero'          => $searchedWithZero
        );
        // dd($data);

        return view('accounting.reports.branchWiseLedgerReport.consolidateLedgerReport', $data);    
    }



    public function getFilteredBranhIds($filReportLevel,$filBranch,$filArea,$filZone,$filRegion){
        $userBranchId = Auth::user()->branchId;
        
        if ($userBranchId!=1) {
            $filBranchIds = [$userBranchId];
        }
        else{
            /// Report Level Branch
            if ($filReportLevel=="Branch") {             
                if ($filBranch!='' || $filBranch!=null) {
                    $filBranch = (int) $filBranch;
                    $filBranchIds = [$filBranch];
                }
                else{
                    $filBranchIds = array_map('intval',DB::table('gnr_branch')->pluck('id')->toArray());
                }
            }
            
            /// Report Level Area
            elseif ($filReportLevel=="Area") {
                $filBranchIds = array_map('intval',explode(',',str_replace(['"','[',']'],'',DB::table('gnr_area')->where('id',$filArea)->value('branchId'))));
            }
            /// Report Level Zone
            elseif ($filReportLevel=="Zone") {
                $areaIds =  explode(',',str_replace(['"','[',']'],'',DB::table('gnr_zone')->where('id',$filZone)->value('areaId')));

                $filBranchIds = array();
                foreach ($areaIds as $key => $areaId) {
                    $branchIds = array_map('intval',explode(',',str_replace(['"','[',']'],'',DB::table('gnr_area')->where('id',$areaId)->value('branchId'))));
                    $filBranchIds = $filBranchIds +  $branchIds;                   
                }               
            }
            /// Report Level Region
            elseif ($filReportLevel=="Region") {
                $zoneIds = explode(',',str_replace(['"','[',']'],'',DB::table('gnr_region')->where('id',$filRegion)->value('zoneId')));

                $filBranchIds = array();
                foreach ($zoneIds as $zoneId) {
                    $areaIds =  explode(',',str_replace(['"','[',']'],'',DB::table('gnr_zone')->where('id',$zoneId)->value('areaId')));                
                    foreach ($areaIds as $key => $areaId) {
                        $branchIds = array_map('intval',explode(',',str_replace(['"','[',']'],'',DB::table('gnr_area')->where('id',$areaId)->value('branchId'))));
                        $filBranchIds = $filBranchIds +  $branchIds;                   
                    }
                }
            }
        }
        
        return $filBranchIds ;
    }



}


