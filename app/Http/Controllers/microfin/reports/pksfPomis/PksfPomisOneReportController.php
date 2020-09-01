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
use App;

class PksfPomisOneReportController extends Controller
{

    public function index()
    {

        $userBranchId = Auth::user()->branchId;
        $branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);

        /// Report Level
        $reportLevelList = array(
            'Branch'  =>  'Branch',
            'Area'    =>  'Area',
            'Zone'    =>  'Zone',
            'Region'  =>  'Region'
        );

        /// Branch
        // $branchList = DB::table('gnr_branch');

        // if ($userBranchId != 1) {
        //     $branchList = $branchList->where('id', $userBranchId);
        // }
        // $branchList = $branchList
        // ->orderBy('branchCode')
        // ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
        // ->pluck('nameWithCode', 'id')
        // ->all();


        if ($userBranchId == 1) {
            $branchList = MicroFin::getBranchList();
        } else {
            $branchList = DB::table('gnr_branch')
            ->whereIn('id', $branchIdArray)
            ->orderBy('branchCode')
            ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
            ->pluck('nameWithCode', 'id')
            ->all();
        }

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
        ->pluck('name', 'id')
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
            'branchIdArray'         => $branchIdArray,
            'fundingOrgList'      => $fundingOrgList,
            'userBranchId'        => $userBranchId
        );

        return view('microfin/reports/pksfPomisReport/pksfPomis1Report/reportFilteringPart', $filteringArray);
    }


    public function index_old()
    {

        // dd(MicroFin::getAllChildsOfAParentInLedger(272));

        $userBranchId = Auth::user()->branchId;

        /// Report Level
        $reportLevelList = array(
            'Branch'  =>  'Branch',
            'Area'    =>  'Area',
            'Zone'    =>  'Zone',
            'Region'  =>  'Region'
        );

        /// Branch
        $branchList = DB::table('gnr_branch');

        if ($userBranchId != 1) {
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
        ->pluck('name', 'id')
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

        return view('microfin/reports/pksfPomisReport/pksfPomis1Report/reportFilteringPart', $filteringArray);
    }

    public function getReport(Request $req)
    {

        $filBranchIds = $this->getFilteredBranhIds($req->filReportLevel, $req->filBranch, $req->filArea, $req->filZone, $req->filRegion, $req->filFundingOrg);

        $month = str_pad($req->filMonth, 2, '0', STR_PAD_LEFT);
        $filDate = Carbon::parse('01-' . $month . '-' . $req->filYear)->endOfMonth()->format('Y-m-d');

        // Get the branches which have not done the month end process
        $branchIdsHavingMonthEnd = DB::table('mfn_month_end')
        ->where('date', $filDate)
        ->pluck('branchIdFk')
        ->toArray();

        $monthEndPendingBranchIds = array_diff($filBranchIds, $branchIdsHavingMonthEnd);

        $monthEndPendingBranches = DB::table('gnr_branch')
        ->whereIn('id', $monthEndPendingBranchIds)
        ->orderBy('branchCode')
        ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"))
        ->pluck('nameWithCode')
        ->all();

        // consider the branch ids which are in month_end table
        $filBranchIds = array_intersect($filBranchIds, $branchIdsHavingMonthEnd);

        // This Data is Loan Product Wise
        $sMInfo = DB::table('mfn_month_end_process_members')
        ->where('loanProductIdFk', '>', 0)
        ->where(function ($query) {
            $query->where('closingSamityNo', '>', 0)
            ->orWhere('closingMember', '>', 0);
        })
        ->whereIn('branchIdFk', $filBranchIds)
        ->where('date', '=', $filDate)
        ->get();

        $cumSMInfo = DB::table('mfn_month_end_process_members')
        ->where('loanProductIdFk', '>', 0)
        ->where(function ($query) {
            $query->where('closingSamityNo', '>', 0)
            ->orWhere('closingMember', '>', 0);
        })
        ->whereIn('branchIdFk', $filBranchIds)
        ->where('date', '<=', $filDate)
        ->get();

        // This Data is Loan Product Category Wise
        $sMInfoCategory = DB::table('mfn_month_end_process_members')
        ->where('loanProductCategoryIdFk', '>', 0)
        ->where(function ($query) {
            $query->where('closingSamityNo', '>', 0)
            ->orWhere('closingMember', '>', 0);
        })
        ->whereIn('branchIdFk', $filBranchIds)
        ->where('date', '=', $filDate)
        ->get();

        $cumSMInfoCategory = DB::table('mfn_month_end_process_members')
        ->where('loanProductCategoryIdFk', '>', 0)
        ->where(function ($query) {
            $query->where('closingSamityNo', '>', 0)
            ->orWhere('closingMember', '>', 0);
        })
        ->whereIn('branchIdFk', $filBranchIds)
        ->where('date', '=', $filDate)
        ->get();

        $sMTInfo = DB::table('mfn_month_end_process_total_members')
        ->whereIn('branchIdFk', $filBranchIds)
        ->where('date', '=', $filDate)
        ->get();

        $fundingOrgs = DB::table('mfn_funding_organization');

        if ($req->filFundingOrg != '') {
            if ($req->filFundingOrg == '-1') {
                $fundingOrgs = $fundingOrgs->whereIn('id', [1, 2]);
                $filFundingOrgName = 'PKSF & Others';
            } else {
                $fundingOrgs = $fundingOrgs->where('id', $req->filFundingOrg);
                $filFundingOrgName = DB::table('mfn_funding_organization')->where('id', $req->filFundingOrg)->value('name');
            }
        } else {
            $filFundingOrgName = 'All';
        }

        $allSavingsInfo = DB::table('mfn_month_end_process_savings')
        ->where('date', $filDate)
        ->whereIn('branchIdFk', $filBranchIds)
        ->get();

        $activeFundingOrgIds = DB::table('mfn_loans_product')
        ->whereIn('id', $sMInfo->pluck('loanProductIdFk'))
        ->orWhereIn('id', $allSavingsInfo->pluck('productIdFk'))
        ->pluck('fundingOrganizationId')
        ->toArray();

        $loanProductIds = array_merge($sMInfo->pluck('loanProductIdFk')->toArray(), $allSavingsInfo->pluck('productIdFk')->toArray());

        $loanProducts = DB::table('mfn_loans_product')
            // ->whereIn('id',$sMInfo->pluck('loanProductIdFk'))
        ->whereIn('id', $loanProductIds)
        ->select('id', 'name', 'shortName', 'productCategoryId', 'fundingOrganizationId', 'isPrimaryProduct')
        ->get();

        $loanCategories = DB::table('mfn_loans_product_category')
        ->whereIn('id', $sMInfoCategory->pluck('loanProductCategoryIdFk'))
        ->select('id', 'name', 'shortName')
        ->get();

        if ($req->filLoanOption == 1) {
            $fundingOrgs = $fundingOrgs->whereIn('id', $activeFundingOrgIds)->select('id', 'name', 'savingInterestRate')->get();
        } else {
            $funOrgIds = array_merge($sMTInfo->pluck('fundingOrgIdFk')->toArray(), $sMInfoCategory->pluck('fundingOrgIdFk')->toArray());
            $funOrgIds = array_unique($funOrgIds);
            $fundingOrgs = $fundingOrgs->whereIn('id', $funOrgIds)->select('id', 'name', 'savingInterestRate')->get();
        }

        //////////////
        $activeProductIds =  DB::table('mfn_loans_product')
        ->whereIn('fundingOrganizationId', $fundingOrgs->pluck('id'))
        ->pluck('id')
        ->toArray();

        $activeProducCaregoryIds = DB::table('mfn_loans_product')
        ->whereIn('fundingOrganizationId', $fundingOrgs->pluck('id'))
        ->pluck('productCategoryId')
        ->toArray();

        $sMInfo = $sMInfo->whereIn('loanProductIdFk', $activeProductIds);
        $cumSMInfo = $cumSMInfo->whereIn('loanProductIdFk', $activeProductIds);
        $sMInfoCategory = $sMInfoCategory->whereIn('loanProductCategoryIdFk', $activeProducCaregoryIds);
        $cumSMInfoCategory = $cumSMInfoCategory->whereIn('loanProductCategoryIdFk', $activeProducCaregoryIds);
        $sMTInfo = $sMTInfo->whereIn('fundingOrgIdFk', $fundingOrgs->pluck('id'));
        $allSavingsInfo = $allSavingsInfo->whereIn('productIdFk', $activeProductIds);
        //////////////


        $allSavingProducts = DB::table('mfn_saving_product')
        ->select('id', 'name', 'shortName', 'code')
        ->get();

        $filBranch = isset($req->filBranch) ? $req->filBranch : Auth::user()->branchId;

        // dd($sMInfoCategory);


        $data = array(
            'filReportLevel'            => $req->filReportLevel,
            'filArea'                   => $req->filArea,
            'filZone'                   => $req->filZone,
            'filRegion'                 => $req->filRegion,
            'filBranch'                 => $filBranch,
            'filYear'                   => $req->filYear,
            'filMonth'                  => $req->filMonth,
            'filDate'                   => $filDate,
            'filLoanOption'             => $req->filLoanOption,
            'filFundingOrg'             => $req->filFundingOrg,
            'fundingOrgs'               => $fundingOrgs,
            'loanProducts'              => $loanProducts,
            'loanCategories'            => $loanCategories,
            'allSavingsInfo'            => $allSavingsInfo,
            'allSavingProducts'         => $allSavingProducts,
            'sMInfo'                    => $sMInfo,
            'cumSMInfo'                 => $cumSMInfo,
            'sMInfoCategory'            => $sMInfoCategory,
            'cumSMInfoCategory'         => $cumSMInfoCategory,
            'sMTInfo'                   => $sMTInfo,
            'monthEndPendingBranches'   => $monthEndPendingBranches,
            'filFundingOrgName'         => $filFundingOrgName
        );

        if ($req->filLoanOption == 1) {
            return view('microfin/reports/pksfPomisReport/pksfPomis1Report/pksfPomisOneReportLoanProduct', $data);
        } else {
            return view('microfin/reports/pksfPomisReport/pksfPomis1Report/pksfPomisOneReportLoanProductCategory', $data);
        }
    }

    public function getFilteredBranhIds($filReportLevel, $filBranch, $filArea, $filZone, $filRegion, $filFundingOrg)
    {
        $userBranchId = Auth::user()->branchId;

        if ($userBranchId != 1) {
            $filBranchIds = [$userBranchId];
        } else {
            /// Report Level Branch
            if ($filReportLevel == "Branch") {
                if ($filBranch != '' || $filBranch != null) {
                    $filBranch = (int) $filBranch;
                    $filBranchIds = [$filBranch];
                } else {
                    $filBranchIds = array_map('intval', DB::table('gnr_branch')->pluck('id')->toArray());
                }
            }

            /// Report Level Area
            elseif ($filReportLevel == "Area") {
                $filBranchIds = array_map('intval', explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_area')->where('id', $filArea)->value('branchId'))));
            }
            /// Report Level Zone
            elseif ($filReportLevel == "Zone") {
                $areaIds =  explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_zone')->where('id', $filZone)->value('areaId')));

                $filBranchIds = array();
                foreach ($areaIds as $key => $areaId) {
                    $branchIds = array_map('intval', explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_area')->where('id', $areaId)->value('branchId'))));
                    $filBranchIds = array_merge($filBranchIds, $branchIds);
                    $filBranchIds = array_unique($filBranchIds);
                }
            }
            /// Report Level Region
            elseif ($filReportLevel == "Region") {
                $zoneIds = explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_region')->where('id', $filRegion)->value('zoneId')));

                $filBranchIds = array();
                foreach ($zoneIds as $zoneId) {
                    $areaIds =  explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_zone')->where('id', $zoneId)->value('areaId')));
                    foreach ($areaIds as $key => $areaId) {
                        $branchIds = array_map('intval', explode(',', str_replace(['"', '[', ']'], '', DB::table('gnr_area')->where('id', $areaId)->value('branchId'))));
                        $filBranchIds = array_merge($filBranchIds, $branchIds);
                        $filBranchIds = array_unique($filBranchIds);
                    }
                }
            }
        }

        ///// FILTER THE BRANCES ACCORDING TO THE FUNDING ORGANIZATION
        if ($filFundingOrg != '') {
            if ($filFundingOrg == '-1') {
                // BRANCH OF PKSF AND OTHRS
                $projectIds = DB::table('mfn_funding_organization')->whereIn('id', [1, 2])->groupBy('projectIdFk')->pluck('projectIdFk')->toArray();
                $projectTypeIds = DB::table('mfn_funding_organization')->whereIn('id', [1, 2])->groupBy('projectTypeIdFk')->pluck('projectTypeIdFk')->toArray();
                $fundingBranchIds = DB::table('gnr_branch')->whereIn('projectId', $projectIds)->whereIn('projectTypeId', $projectTypeIds)->pluck('id')->toArray();
            } elseif ($filFundingOrg == 3) {
                // IF IT IS OUTHES THEN INCLUDE THE 3,8 NO BRANCH IDS.
                $projectIds = DB::table('mfn_funding_organization')->where('id', $filFundingOrg)->groupBy('projectIdFk')->pluck('projectIdFk')->toArray();
                $projectTypeIds = DB::table('mfn_funding_organization')->where('id', $filFundingOrg)->groupBy('projectTypeIdFk')->pluck('projectTypeIdFk')->toArray();
                $fundingBranchIds = DB::table('gnr_branch')
                ->whereIn('projectId', $projectIds)
                ->whereIn('projectTypeId', $projectTypeIds)
                ->orWhereIn('id', [3, 8])
                ->pluck('id')->toArray();
            } else {
                $projectIds = DB::table('mfn_funding_organization')->where('id', $filFundingOrg)->groupBy('projectIdFk')->pluck('projectIdFk')->toArray();
                $projectTypeIds = DB::table('mfn_funding_organization')->where('id', $filFundingOrg)->groupBy('projectTypeIdFk')->pluck('projectTypeIdFk')->toArray();
                $fundingBranchIds = DB::table('gnr_branch')
                ->whereIn('projectId', $projectIds)
                ->whereIn('projectTypeId', $projectTypeIds)
                ->pluck('id')->toArray();
            }

            $filBranchIds = array_intersect($filBranchIds, $fundingBranchIds);
        }

        return $filBranchIds;
    }
}
