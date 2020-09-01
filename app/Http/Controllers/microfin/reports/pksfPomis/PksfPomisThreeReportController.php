<?php

namespace App\Http\Controllers\microfin\reports\pksfPomis;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\microfin\MicroFin;

class PksfPomisThreeReportController extends Controller {

    public function index() {

        $userBranchId=Auth::user()->branchId;

        /// Report Level
        $reportLevelList = array(
            'Branch'  =>  'Branch',
            'Area'    =>  'Area',
            'Zone'    =>  'Zone',
            'Region'  =>  'Region'
        );

        /// Branch
        $branchList = DB::table('gnr_branch');

        if ($userBranchId!=1) {
            $branchList = $branchList->where('id', $userBranchId);
        }

        $branchList = $branchList
        ->orderBy('branchCode')
        ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
        ->pluck('nameWithCode', 'id')
        ->all();
        /// Area
        $areaList = DB::table('gnr_area')
        ->select(DB::raw("CONCAT ( LPAD(code,3,0) ,' - ', name ) AS name"), 'id', 'branchId')
        ->get();
        /// Zone
        $zoneList = DB::table('gnr_zone')
        ->select(DB::raw("CONCAT ( LPAD(code,3,0) ,' - ', name ) AS name"), 'id', 'areaId')
        ->get();
        /// Region
        $regionList = DB::table('gnr_region')
        ->select(DB::raw("CONCAT ( LPAD(code,3,0) ,' - ', name ) AS name"), 'id', 'zoneId')
        ->get();
        /// Year
        $yearsOption = MicroFin::getYearsOption();

        /// Month
        $monthsOption = MicroFin::getMonthsOption();

        /// Loan Options
        $loanOptions = array(
            1   => 'Loan Product',
            2   => 'Loan Product Category'
        );

        /// Funding Organization List
        $fundingOrgList = DB::table('mfn_funding_organization')
        ->pluck('name','id')
        ->toArray();

        $filteringArray = array(
            'reportLevelList'     => $reportLevelList,
            'areaList'            => $areaList,
            'zoneList'            => $zoneList,
            'regionList'          => $regionList,
            'branchList'          => $branchList,
            'yearsOption'         => $yearsOption,
            'monthsOption'        => $monthsOption,
            'loanOptions'         => $loanOptions,
            'fundingOrgList'      => $fundingOrgList,
            'userBranchId'        => $userBranchId
        );

        return view('microfin.reports.pksfPomisReport.pksfPomis3Report.reportFilteringPart', $filteringArray);
    }

    public function getReport(Request $req){

        $filBranchIds = $this->getFilteredBranhIds($req->filReportLevel,$req->filBranch,$req->filArea,$req->filZone,$req->filRegion,$req->filFundingOrg);

        $fundingBranchIds = DB::table('gnr_branch')->pluck('id')->toArray();
        $fundingLoanProductIds = DB::table('mfn_loans_product')->pluck('id')->toArray();
        if ($req->filFundingOrg!='') {
            if ($req->filFundingOrg=='-1') {
                // get the branch which are under PKSF and Others
                $projectTypeIds = DB::table('mfn_funding_organization')->whereIn('id',[1,2])->pluck('projectTypeIdFk')->toArray();
                $fundingBranchIds = DB::table('gnr_branch')->whereIn('projectTypeId',$projectTypeIds)->pluck('id')->toArray();
                $fundingLoanProductIds = DB::table('mfn_loans_product')->whereIn('fundingOrganizationId',[1,2])->pluck('id')->toArray();
            }
            else{
                $projectTypeIds = DB::table('mfn_funding_organization')->where('id',$req->filFundingOrg)->pluck('projectTypeIdFk')->toArray();
                $fundingBranchIds = DB::table('gnr_branch')->whereIn('projectTypeId',$projectTypeIds)->pluck('id')->toArray();
                $fundingLoanProductIds = DB::table('mfn_loans_product')->where('fundingOrganizationId',$req->filFundingOrg)->pluck('id')->toArray();
            }
        }

        $month = str_pad($req->filMonth, 2, '0',STR_PAD_LEFT);
        $filDate = Carbon::parse('01-'.$month.'-'.$req->filYear)->endOfMonth()->format('Y-m-d');

         // Get the branches which have not done the month end process
        $branchIdsHavingMonthEnd = DB::table('mfn_month_end')
        ->where('date',$filDate)
        ->pluck('branchIdFk')
        ->toArray();

        $monthEndPendingBranchIds = array_diff($filBranchIds, $branchIdsHavingMonthEnd);

        $monthEndPendingBranches = DB::table('gnr_branch')
        ->whereIn('id',$monthEndPendingBranchIds)
        ->orderBy('branchCode')
        ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"))
        ->pluck('nameWithCode')
        ->all();

        // consider the branch ids which are in month_end table
        $filBranchIds = array_intersect($filBranchIds,$branchIdsHavingMonthEnd);
        // Month End Loan Info
        $loanInfo = DB::table('mfn_month_end_process_loans')
        ->whereIn('branchIdFk',$filBranchIds)
        ->whereIn('productIdFk',$fundingLoanProductIds)
        ->where('date',$filDate)
        ->where(function($query){
            $query->where('closingOutstandingAmountWithServicesCharge','>',0)
            ->orWhere('watchfulOverdueWithServicesCharge','>',0)
            ->orWhere('watchfulOutstandingWithServicesCharge','>',0)
            ->orWhere('substandardOverdueWithServicesCharge','>',0)
            ->orWhere('substandardOutstandingWithServicesCharge','>',0)
            ->orWhere('doubtfullOverdueWithServicesCharge','>',0)
            ->orWhere('doubtfullOutstandingWithServicesCharge','>',0)
            ->orWhere('badOutstandingWithServicesCharge','>',0)
            ->orWhere('outstandingWithMoreThan2DueInstallmentsServicesCharge','>',0)
            ->orWhere('closingOutstandingAmount','>',0)
            ->orWhere('watchfulOverdue','>',0)
            ->orWhere('watchfulOutstanding','>',0)
            ->orWhere('substandardOverdue','>',0)
            ->orWhere('substandardOutstanding','>',0)
            ->orWhere('doubtfullOverdue','>',0)
            ->orWhere('doubtfullOutstanding','>',0)
            ->orWhere('badOutstanding','>',0)
            ->orWhere('outstandingWithMoreThan2DueInstallments','>',0)
            ->orWhere('savingBalanceOfOverdueLoanee','>',0);
        })
        ->get();

        // consider the branch ids which are in funding ord. list
        $filBranchIds = array_intersect($filBranchIds,$fundingBranchIds);

        $staffInfo = DB::table('mfn_month_end_staff_info')
        ->whereIn('branchIdFk',$filBranchIds)
        ->where('isGrandTotal',0)
        ->where('date',$filDate)
        ->get();

        $totalStaffInfo = DB::table('mfn_month_end_staff_info')
        ->whereIn('branchIdFk',$filBranchIds)
        ->where('isGrandTotal',1)
        ->where('date',$filDate)
        ->get();

        $fundingOrgs = DB::table('mfn_funding_organization');

        if ($req->filFundingOrg!='') {
            if ($req->filFundingOrg=='-1') {
                $fundingOrgs = $fundingOrgs->whereIn('id',[1,2]);
                $filFundingOrgName = 'PKSF & Others';
            }
            else{
                $fundingOrgs = $fundingOrgs->where('id',$req->filFundingOrg);
                $filFundingOrgName = DB::table('mfn_funding_organization')->where('id',$req->filFundingOrg)->value('name');
            }
        }
        else{
            $filFundingOrgName = 'All';
        }

        $activeFundingOrgIds = DB::table('mfn_loans_product')
        ->whereIn('id',$loanInfo->unique('productIdFk')->pluck('productIdFk'))
        ->pluck('fundingOrganizationId')
        ->toArray();

        $loanProducts = DB::table('mfn_loans_product')
        ->whereIn('id',$loanInfo->unique('productIdFk')->pluck('productIdFk'))
        ->select('id','name','shortName','productCategoryId','fundingOrganizationId','isPrimaryProduct')
        ->get();

        $loanCategories = DB::table('mfn_loans_product_category')
        ->whereIn('id',$loanProducts->unique('productCategoryId')->pluck('productCategoryId'))
        ->select('id','name','shortName')
        ->get();

        $fundingOrgs = $fundingOrgs->whereIn('id',$activeFundingOrgIds)->select('id','name','savingInterestRate','projectIdFk','projectTypeIdFk')->get();

        ////////////////////
        $activeProductIds =  DB::table('mfn_loans_product')
        ->whereIn('fundingOrganizationId',$fundingOrgs->pluck('id'))
        ->pluck('id')
        ->toArray();

        $loanInfo = $loanInfo->whereIn('productIdFk',$activeProductIds);
        ////////////////////

        $genderTypeIds = [1,2]; // 1= Male, 2=Female

        /// area manager, zonal manager is counted here
        if ($req->filReportLevel!='Branch') {
            $areaManager = 1;
            $zonalManager = 1;
        }
        else{
            if ($req->filBranch!='' || $req->filBranch!=null) {
                $areaManager = 1;
                $zonalManager = 1;
            }
            else{
                // when filReportLevel = Branch and filBranch = All
                // get the number of zonal manager on reporting date
                $areaManager = DB::table('hr_emp_org_info')
                ->where('emp_id_fk',131)
                ->where('joining_date','<=',$filDate)
                ->count();

                $zonalManager = DB::table('hr_emp_org_info')
                ->where('position_id_fk',130)
                ->where('job_status','Present')
                ->where('status','Active')
                ->where('joining_date','<=',$filDate)
                ->count();
            }
        }

        $srAsstAO = DB::table('hr_emp_org_info')
        ->whereIn('project_id_fk',$fundingOrgs->pluck('projectIdFk'))
        ->whereIn('branch_id_fk',$filBranchIds)
        ->where('department',2)
        ->where('position_id_fk',116)
        ->where('job_status','Present')
        ->where('status','Active')
        ->count();

        $headOffice = DB::table('hr_emp_org_info')
        ->whereIn('project_id_fk',$fundingOrgs->pluck('projectIdFk'))
        ->whereIn('project_type_id_fk',$fundingOrgs->pluck('projectTypeIdFk'))
        ->where('branch_id_fk',1)
        ->where('job_status','Present')
        ->where('status','Active')
        ->count();

        /// for third table
        $areaInfo = $this->getAreaInfo($filBranchIds,$filDate,$req->filLoanOption,$fundingLoanProductIds);

        $loanAreaInfo = $areaInfo['info'];
        $funOrgTotal = $areaInfo['funOrgTotal'];
        $grandTotal = $areaInfo['grandTotal'];

        $filBranch = isset($req->filBranch) ? $req->filBranch : Auth::user()->branchId;

        $data = array(
            'filReportLevel'    => $req->filReportLevel,
            'filArea'           => $req->filArea,
            'filZone'           => $req->filZone,
            'filRegion'         => $req->filRegion,
            'filBranch'         => $filBranch,
            'filYear'           => $req->filYear,
            'filMonth'          => $req->filMonth,
            'filDate'           => $filDate,
            'filServiceCharge'  => $req->filServiceCharge,
            'filRoundUup'       => $req->filRoundUup,
            'filLoanOption'     => $req->filLoanOption,
            'filFundingOrg'     => $req->filFundingOrg,
            'fundingOrgs'       => $fundingOrgs,
            'loanProducts'      => $loanProducts,
            'loanCategories'    => $loanCategories,
            'loanInfo'          => $loanInfo,
            'staffInfo'         => $staffInfo,
            'totalStaffInfo'    => $totalStaffInfo,
            'areaManager'       => $areaManager,
            'zonalManager'      => $zonalManager,
            'srAsstAO'          => $srAsstAO,
            'headOffice'        => $headOffice,
            'loanAreaInfo'      => $loanAreaInfo,
            'funOrgTotal'       => $funOrgTotal,
            'grandTotal'        => $grandTotal,
            'genderTypeIds'     => $genderTypeIds,
            'monthEndPendingBranches'   => $monthEndPendingBranches,
            'filFundingOrgName'         => $filFundingOrgName
        );

        if ($req->filLoanOption==1) {

            $staffReportInfos = DB::table('mfn_pksf_staff')->where('dataType', 'Loan Product')->get();
            return view('microfin/reports/pksfPomisReport/pksfPomis3Report/pksfPomisThreeReportLoanProduct',$data)->with('staffReportInfos', $staffReportInfos);
        }
        else {
                $staffReportInfos = DB::table('mfn_pksf_staff')->where('dataType', 'Loan Category')->get();
                return view('microfin/reports/pksfPomisReport/pksfPomis3Report/pksfPomisThreeReportLoanProductCategory',$data)->with('staffReportInfos', $staffReportInfos);
            }
        }

        public function getFilteredBranhIds($filReportLevel,$filBranch,$filArea,$filZone,$filRegion,$filFundingOrg){
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
                        $filBranchIds = array_merge($filBranchIds,$branchIds);
                        $filBranchIds = array_unique($filBranchIds);
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
                            $filBranchIds = array_merge($filBranchIds,$branchIds);
                            $filBranchIds = array_unique($filBranchIds);
                        }
                    }
                }
            }

            ///// FILTER THE BRANCES ACCORDING TO THE FUNDING ORGANIZATION
            if ($filFundingOrg!='') {
                if ($filFundingOrg=='-1') {
                    // BRANCH OF PKSF AND OTHRS
                    $projectIds = DB::table('mfn_funding_organization')->whereIn('id',[1,2])->groupBy('projectIdFk')->pluck('projectIdFk')->toArray();
                    $projectTypeIds = DB::table('mfn_funding_organization')->whereIn('id',[1,2])->groupBy('projectTypeIdFk')->pluck('projectTypeIdFk')->toArray();
                    $fundingBranchIds = DB::table('gnr_branch')->whereIn('projectId',$projectIds)->whereIn('projectTypeId',$projectTypeIds)->pluck('id')->toArray();
                }
                elseif ($filFundingOrg==3) {
                    // IF IT IS GRIHAYAN THEN INCLUDE THE 3,8 NO BRANCH IDS.
                    $projectIds = DB::table('mfn_funding_organization')->where('id',$filFundingOrg)->groupBy('projectIdFk')->pluck('projectIdFk')->toArray();
                    $projectTypeIds = DB::table('mfn_funding_organization')->where('id',$filFundingOrg)->groupBy('projectTypeIdFk')->pluck('projectTypeIdFk')->toArray();
                    $fundingBranchIds = DB::table('gnr_branch')
                    ->whereIn('projectId',$projectIds)
                    ->whereIn('projectTypeId',$projectTypeIds)
                    ->orWhereIn('id',[3,8])
                    ->pluck('id')->toArray();
                }
                else{
                    $projectIds = DB::table('mfn_funding_organization')->where('id',$filFundingOrg)->groupBy('projectIdFk')->pluck('projectIdFk')->toArray();
                    $projectTypeIds = DB::table('mfn_funding_organization')->where('id',$filFundingOrg)->groupBy('projectTypeIdFk')->pluck('projectTypeIdFk')->toArray();
                    $fundingBranchIds = DB::table('gnr_branch')
                    ->whereIn('projectId',$projectIds)
                    ->whereIn('projectTypeId',$projectTypeIds)
                    ->pluck('id')->toArray();
                }

                $filBranchIds = array_intersect($filBranchIds,$fundingBranchIds);
            }

            return $filBranchIds;
        }

        public function getAreaInfo($brancIds,$date,$loanOption,$fundingLoanProductIds){

            $loans = DB::table('mfn_loan')
            ->where('softDel',0)
            ->whereIn('branchIdFk',$brancIds)
            ->where('disbursementDate','<=',$date)
            ->select('productIdFk','samityIdFk')
            ->get();

            $loanProducts = DB::table('mfn_loans_product')
            ->where('softDel',0)
            ->whereIn('id',$loans->unique('productIdFk')->pluck('productIdFk'))
            ->whereIn('id',$fundingLoanProductIds)
            ->select('id','name','fundingOrganizationId','productCategoryId')
            ->get();

            $fundingOrgs = DB::table('mfn_funding_organization')
            ->whereIn('id',$loanProducts->unique('fundingOrganizationId')->pluck('fundingOrganizationId'))
            ->get();

            $loanCategories = DB::table('mfn_loans_product_category')
            ->whereIn('id',$loanProducts->unique('productCategoryId')->pluck('productCategoryId'))
            ->select('id','name')
            ->get();

            $samities = DB::table('mfn_samity')
            ->whereIn('id',$loans->unique('samityIdFk')->pluck('samityIdFk'))
            ->select('id','workingAreaId')
            ->get();

            $workingAreas = DB::table('gnr_working_area')->get();

            /// for grand total
            $currentSamitIds = $loans->whereIn('productIdFk',$loanProducts->unique('id')->pluck('id'))->pluck('samityIdFk')->toArray();
            $currentWorkingAreaIds = $samities->whereIn('id',$currentSamitIds)->pluck('workingAreaId')->toArray();
            $currentWorkingAreas = $workingAreas->whereIn('id',$currentWorkingAreaIds);

            $districtNo = $currentWorkingAreas->unique('districtId')->count();
            $upazillaNo = $currentWorkingAreas->unique('upazilaId')->count();
            $unionNo = $currentWorkingAreas->unique('unionId')->count();
            $villageNo = $currentWorkingAreas->unique('villageId')->count();

            $grandTotal = array(
                'districtNo'        => $districtNo,
                'upazillaNo'        => $upazillaNo,
                'unionNo'           => $unionNo,
                'villageNo'         => $villageNo
            );

            $grandTotal = collect($grandTotal);

            /// for funding Organization total
            $funOrgTotal = array();
            foreach ($fundingOrgs as $fundingOrg) {
                $loanProductIds = $loanProducts->where('fundingOrganizationId',$fundingOrg->id)->pluck('id')->toArray();

                $currentSamitIds = $loans->whereIn('productIdFk',$loanProductIds)->pluck('samityIdFk')->toArray();
                $currentWorkingAreaIds = $samities->whereIn('id',$currentSamitIds)->pluck('workingAreaId')->toArray();
                $currentWorkingAreas = $workingAreas->whereIn('id',$currentWorkingAreaIds);

                $districtNo = $currentWorkingAreas->unique('districtId')->count();
                $upazillaNo = $currentWorkingAreas->unique('upazilaId')->count();
                $unionNo = $currentWorkingAreas->unique('unionId')->count();
                $villageNo = $currentWorkingAreas->unique('villageId')->count();

                $currentInfo = array(
                    'funOrgId'          => $fundingOrg->id,
                    'funOrgName'        => $fundingOrg->name,
                    'districtNo'        => $districtNo,
                    'upazillaNo'        => $upazillaNo,
                    'unionNo'           => $unionNo,
                    'villageNo'         => $villageNo
                );

                array_push($funOrgTotal, $currentInfo);
            }

            $funOrgTotal = collect($funOrgTotal);

            /// for loan product/ loan category
            $info = array();

            if ($loanOption==1) {
                foreach ($loanProducts as $loanProduct) {
                    $currentSamitIds = $loans->where('productIdFk',$loanProduct->id)->pluck('samityIdFk')->toArray();
                    $currentWorkingAreaIds = $samities->whereIn('id',$currentSamitIds)->pluck('workingAreaId')->toArray();
                    $currentWorkingAreas = $workingAreas->whereIn('id',$currentWorkingAreaIds);

                    $districtNo = $currentWorkingAreas->unique('districtId')->count();
                    $upazillaNo = $currentWorkingAreas->unique('upazilaId')->count();
                    $unionNo = $currentWorkingAreas->unique('unionId')->count();
                    $villageNo = $currentWorkingAreas->unique('villageId')->count();

                    $funOrgName = $fundingOrgs->where('id',$loanProduct->fundingOrganizationId)->max('name');

                    $currentInfo = array(
                        'funOrgId'          => $loanProduct->fundingOrganizationId,
                        'funOrgName'        => $funOrgName,
                        'loanProductId'     => $loanProduct->id,
                        'loanProductName'   => $loanProduct->name,
                        'districtNo'        => $districtNo,
                        'upazillaNo'        => $upazillaNo,
                        'unionNo'           => $unionNo,
                        'villageNo'         => $villageNo
                    );

                    array_push($info, $currentInfo);
                }
            }
            else{
                foreach ($fundingOrgs as $fundingOrg) {
                    foreach ($loanCategories as $loanCategory) {
                        $loanProductIds = $loanProducts->where('fundingOrganizationId',$fundingOrg->id)->where('productCategoryId',$loanCategory->id)->pluck('id')->toArray();

                        $currentSamitIds = $loans->whereIn('productIdFk',$loanProductIds)->pluck('samityIdFk')->toArray();
                        $currentWorkingAreaIds = $samities->whereIn('id',$currentSamitIds)->pluck('workingAreaId')->toArray();
                        $currentWorkingAreas = $workingAreas->whereIn('id',$currentWorkingAreaIds);

                        $districtNo = $currentWorkingAreas->unique('districtId')->count();
                        $upazillaNo = $currentWorkingAreas->unique('upazilaId')->count();
                        $unionNo = $currentWorkingAreas->unique('unionId')->count();
                        $villageNo = $currentWorkingAreas->unique('villageId')->count();

                        $funOrgName = $fundingOrgs->where('id',$fundingOrg->id)->max('name');

                        if ($districtNo + $upazillaNo + $unionNo + $villageNo == 0) {
                            continue;
                        }

                        $currentInfo = array(
                            'funOrgId'          => $fundingOrg->id,
                            'funOrgName'        => $funOrgName,
                            'loanCategoryId'    => $loanCategory->id,
                            'loanCategoryName'  => $loanCategory->name,
                            'districtNo'        => $districtNo,
                            'upazillaNo'        => $upazillaNo,
                            'unionNo'           => $unionNo,
                            'villageNo'         => $villageNo
                        );

                        array_push($info, $currentInfo);
                    }
                }

            }

            $info = collect($info);

            $data = array(
                'info'          => $info,
                'funOrgTotal'   => $funOrgTotal,
                'grandTotal'    => $grandTotal
            );

            return $data;

        }


    }
