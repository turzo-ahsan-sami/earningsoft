<?php

namespace App\Http\Controllers\microfin\process;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

use App\Http\Controllers\microfin\process\MonthEndStoreInfo;
use App\Http\Controllers\microfin\process\MonthEndStoreInfo25022019beforeDuplicateMemberCounting;
use App\Http\Controllers\microfin\process\MonthEndStoreInfoTOFastProcess;
use App\Http\Controllers\microfin\process\MonthEndStoreInfoFromOpening;

use App\Service\Service;
use App\Http\Controllers\microfin\MicroFin;
use App\Http\Controllers\microfin\MicroFinance;

class UpdateMonthEndInfoController extends Controller
{

    public function index($branchId = null, $startMonth = null, $endMonth = null)
    {
        // $a = MicroFin::getAllChildsOfAParentInLedgerByCode(24101);
        // dd(implode(',',$a));

        // 33 porjonto hoise

        /* $branchIds = DB::table('gnr_branch')
        ->where('id','>=',2)            
        ->where('id','<=',5)
        ->pluck('id')
        ->toArray(); */

        $branchIds = $branchId == null ? [109] : [$branchId];
        $startMonth = $startMonth == null ? '2019-08-01' : $startMonth;
        $endMonth = $endMonth == null ? '2019-08-01' : $endMonth;

        if ($startMonth> $endMonth) {
            dd('$startMonth id greater than $endMonth');
        }       

        $startMonth = Carbon::parse($startMonth)->endOfMonth();        
        $endMonth = Carbon::parse($endMonth)->endOfMonth();
        $monthEndDates = [];
        while ($startMonth->lte($endMonth)) {
            array_push($monthEndDates, $startMonth->copy()->endOfMonth()->format('Y-m-d'));
            $startMonth->addMonthNoOverflow();
        }

        /*if($monthEndDates[0]<'2018-10-31'){
            dd($monthEndDates,'problem detected');
        }*/

        /*  if(count($monthEndDates)>7){
            dd($monthEndDates,'problem detected');
        }  */

        foreach ($branchIds as $branchId) {
            $branch = DB::table('gnr_branch')->where('id', $branchId)->select('id', 'branchOpeningDate', 'softwareStartDate', 'name')->first();
            foreach ($monthEndDates as $monthEndDate) {
                $monthStartDate = null;

                if ($monthEndDate == $branch->softwareStartDate && $branch->branchOpeningDate < $branch->softwareStartDate) {
                    $updateInfo = new MonthEndStoreInfoFromOpening($branchId, $monthStartDate, $monthEndDate);
                    echo 'From opening<br>';
                } else {
                    $updateInfo = new MonthEndStoreInfo($branchId, $monthStartDate, $monthEndDate);
                }

                $updateInfo->saveData();
                echo $branch->name . ' Updated<br>' . 'On ' . $monthEndDate . '<br>';
            }
        }
    }

    public function updateInfo(Request $req)
    {
        $monthEnd = DB::table('mfn_month_end')->where('id', $req->id)->first();
        $monthStartDate = Carbon::parse($monthEnd->date)->startOfMonth()->format('Y-m-d');

        if ($monthEnd->branchIdFk > 1 && $monthEnd->branchIdFk < 84) {
            if ($monthEnd->date == '2018-10-31') {
                $updateInfo = new MonthEndStoreInfoFromOpening($monthEnd->branchIdFk, $monthStartDate, $monthEnd->date);
            } else {
                $updateInfo = new MonthEndStoreInfo($monthEnd->branchIdFk, $monthStartDate, $monthEnd->date);
            }
            $updateInfo->saveData();
        } else {
            $updateInfo = new MonthEndStoreInfo($monthEnd->branchIdFk, $monthStartDate, $monthEnd->date);
            $updateInfo->saveData();
        }

        $data = array(
            'responseTitle' =>  'Success!',
            'responseText'  =>  'Month End Information Updated Successfully.'
        );

        return response::json($data);
    }

    public function updateCumulativeLoanNBorrowerNo()
    {

        $branchIds = DB::table('gnr_branch')
            ->where('id', '>=', 4)
            ->where('id', '<=', 10)
            ->pluck('id')
            ->toArray();

        foreach ($branchIds as $branchId) {

            $monthDate = '2018-11-01';

            $monthEndDate = Carbon::parse($monthDate)->endOfMonth()->format('Y-m-d');

            $monthendInfos = DB::table('mfn_month_end_process_loans')
                ->where('date', $monthEndDate)
                ->where('branchIdFk', $branchId)
                ->orderBy('branchIdFk')
                ->get();

            $lastMonthEndDate = Carbon::parse($monthDate)->subDay()->format('Y-m-d');

            $lastMonthEndInfos = DB::table('mfn_month_end_process_loans')
                ->where('date', $lastMonthEndDate)
                ->where('branchIdFk', $branchId)
                ->orderBy('branchIdFk')
                ->get();

            foreach ($monthendInfos as $monthendInfo) {

                $openingCumBorrowerNo = $lastMonthEndInfos
                    ->where('branchIdFk', $monthendInfo->branchIdFk)
                    ->where('productIdFk', $monthendInfo->productIdFk)
                    ->where('genderTypeId', $monthendInfo->genderTypeId)
                    ->sum('cumBorrowerNo');

                $thisMonthNewBowwerNo = DB::table('mfn_loan')
                    ->join('mfn_member_information', 'mfn_member_information.id', 'mfn_loan.memberIdFk')
                    ->where('mfn_loan.softDel', 0)
                    ->where('mfn_loan.branchIdFk', $branchId)
                    ->where('mfn_loan.productIdFk', $monthendInfo->productIdFk)
                    ->where('mfn_member_information.gender', $monthendInfo->genderTypeId)
                    ->where('mfn_loan.isFromOpening', 0)
                    ->where('mfn_loan.disbursementDate', '>', $lastMonthEndDate)
                    ->where('mfn_loan.disbursementDate', '<=', $monthEndDate)
                    ->where('mfn_loan.loanCycle', 1)
                    ->groupBy('mfn_loan.memberIdFk')
                    ->count();

                $cumBorrowerNo = $openingCumBorrowerNo + $thisMonthNewBowwerNo;

                $openingCumLoanNo = $lastMonthEndInfos
                    ->where('branchIdFk', $monthendInfo->branchIdFk)
                    ->where('productIdFk', $monthendInfo->productIdFk)
                    ->where('genderTypeId', $monthendInfo->genderTypeId)
                    ->sum('cumLoanNo');

                $thisMonthNewLoanNo = DB::table('mfn_loan')
                    ->join('mfn_member_information', 'mfn_member_information.id', 'mfn_loan.memberIdFk')
                    ->where('mfn_loan.softDel', 0)
                    ->where('mfn_loan.branchIdFk', $branchId)
                    ->where('mfn_loan.productIdFk', $monthendInfo->productIdFk)
                    ->where('mfn_member_information.gender', $monthendInfo->genderTypeId)
                    ->where('mfn_loan.isFromOpening', 0)
                    ->where('mfn_loan.disbursementDate', '>', $lastMonthEndDate)
                    ->where('mfn_loan.disbursementDate', '<=', $monthEndDate)
                    ->count();


                $cumLoanNo = $openingCumLoanNo + $thisMonthNewLoanNo;

                DB::table('mfn_month_end_process_loans')
                    ->where('date', $monthendInfo->date)
                    ->where('branchIdFk', $monthendInfo->branchIdFk)
                    ->where('productIdFk', $monthendInfo->productIdFk)
                    ->where('genderTypeId', $monthendInfo->genderTypeId)
                    ->update([
                        'cumBorrowerNo' => $cumBorrowerNo,
                        'cumLoanNo'     => $cumLoanNo
                    ]);
            }
        }

        dd('done');
    }
}
