<?php

namespace App\Http\Controllers\microfin\reports\transferRegisterReportController;

use App\Http\Controllers\Controller;
use DB;
use Auth;
use App\Http\Requests;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;
use App\Http\Controllers\microfin\MicroFin;
//
// use Illuminate\Http\Request;
// use App\Http\Requests;
// use Validator;
// use Response;
// use App\Traits\GetSoftwareDate;
// use Illuminate\Support\Facades\Input;
// use Illuminate\Support\Facades\Hash;
// use App\Http\Controllers\Controller;

/**
*
*/
class transferRegisterReportController extends Controller
{
    public function index(){
        $userBranchId = Auth::user()->branchId;
        $BranchDatas = DB::table('gnr_branch')->get();
        $SamityDatas = DB::table('mfn_samity')->where('branchId', $userBranchId)->get();

        // dd($SamityDatas);

        return view("microfin.reports.transferRegisterReportsViews.TransferRegisterReportForm", compact('BranchDatas', 'SamityDatas', 'userBranchId'));
    }

    public function getSamityByBranchAjax(){

        $branchId = Input::get('branchId');
        $SamityLists = DB::table("mfn_samity")
        ->where("branchId", '=', $branchId)
        ->get();

        return response::json($SamityLists);
    }

    public function viewReport(Request $request){
        $userBranchId = Auth::user()->branchId;
        $RequestBranch = $request->searchBranch;
        $RequestSamity = $request->searchSamity;

        // date input
        $dateTo = Carbon::parse($request->dateTo)->format('Y-m-d');
        $dateFrom = Carbon::parse($request->dateFrom)->format('Y-m-d');
        // dd($dateTo);

        $requestBranchInfos = array();
        $requestSamityInfos = array();

        if ($RequestBranch != 'All') {
            $requestBranchInfos = DB::table('gnr_branch')
            ->where('id', $RequestBranch)
            ->get();
        }
        elseif ($RequestBranch == 'All') {
            $requestBranchInfos = DB::table('gnr_branch')
            ->get();
        }

        if ($RequestSamity != 'All') {
            $requestSamityInfos = DB::table('mfn_samity')
            ->where('id', $RequestSamity)
            ->get();
        }
        elseif ($RequestSamity == 'All') {
            $requestSamityInfos = DB::table('mfn_samity')
            ->get();
        }

        //product transfer codes

        if($request->reportType == 0){

            $allTransferredProducts = DB::table('mfn_loan_primary_product_transfer')
            ->where('status', 1)
            ->where('softDel', 0)
            ->where('transferDate', '<=', $dateTo)
            ->where('transferDate', '>=', $dateFrom)
            ->get();

            // joining information

            $branchInfos = DB::table('gnr_branch')
            ->whereIn('id', $allTransferredProducts->unique('branchIdFk')->pluck('branchIdFk'))
            ->select('id','name', 'branchCode')
            ->get();

            $samityInfos = DB::table('mfn_samity')
            ->whereIn('id', $allTransferredProducts->unique('samityIdFk')->pluck('samityIdFk'))
            ->select('id','name', 'code')
            ->get();

            $memberInfos = DB::table('mfn_member_information')
            ->whereIn('id', $allTransferredProducts->unique('memberIdFk')->pluck('memberIdFk'))
            ->select('id','name', 'code')
            ->get();

            $savingsProducts = DB::table('mfn_saving_product')
            ->select('id','shortName', 'code')
            ->get();

            // conditions start

            if($request->searchBranch == '' || $request->searchBranch == Null) {
                $allTransferredProducts = $allTransferredProducts->where('branchIdFk', $userBranchId);
            }
            elseif((int)$request->searchBranch > 0) {
                $allTransferredProducts = $allTransferredProducts->where('branchIdFk', $request->searchBranch);
            }

            if((int)$request->searchSamity > 0) {
                $allTransferredProducts = $allTransferredProducts->where('samityIdFk', $request->searchSamity);
            }

            // dd($allTransferredProducts);
            $data = array(
                'allTransferredProducts'              => $allTransferredProducts,
                'requestBranchInfos'                  => $requestBranchInfos,
                'userBranchId'                        => $userBranchId,
                'RequestBranch'                       => $RequestBranch,
                'branchInfos'                         => $branchInfos,
                'requestSamityInfos'                  => $requestSamityInfos,
                'RequestSamity'                       => $RequestSamity,
                'samityInfos'                         => $samityInfos,
                'memberInfos'                         => $memberInfos,
                'savingsProducts'                     => $savingsProducts,
                'dateFrom'                            => $dateFrom,
                'dateTo'                              => $dateTo,
                'reportType'                          => 'Product Transfer'
            );

        }
        elseif($request->reportType == 1){

            // $allBranchTransferred = DB::table('mfn_member_branch_transfer')
            //                     ->where('status', 1)
            //                     ->where('softDel', 0)
            //                     ->where('transferDate', '<=', $dateTo)
            //                     ->where('transferDate', '>=', $dateFrom);

            // $allBranchTransferredCollection = $allBranchTransferred->get();

            // // joining information

            // $oldBranchInfos = DB::table('gnr_branch')
            //             ->whereIn('id', $allBranchTransferredCollection->unique('previousBranchIdFk')->pluck('previousBranchIdFk'))
            //             ->select('id','name', 'branchCode')
            //             ->get();

            // $newBranchInfos = DB::table('gnr_branch')
            //             ->whereIn('id', $allBranchTransferredCollection->unique('newBranchIdFk')->pluck('newBranchIdFk'))
            //             ->select('id','name', 'branchCode')
            //             ->get();

            // $oldSamityInfos = DB::table('mfn_samity')
            //             ->whereIn('id', $allBranchTransferredCollection->unique('previousSamityIdFk')->pluck('previousSamityIdFk'))
            //             ->select('id','name', 'code')
            //             ->get();

            // $newSamityInfos = DB::table('mfn_samity')
            //             ->whereIn('id', $allBranchTransferredCollection->unique('newSamityIdFk')->pluck('newSamityIdFk'))
            //             ->select('id','name', 'code')
            //             ->get();

            // $memberInfos = DB::table('mfn_member_information')
            //             ->whereIn('id', $allBranchTransferredCollection->unique('memberIdFk')->pluck('memberIdFk'))
            //             ->select('id','name', 'code')
            //             ->get();

            // $savingsProducts = DB::table('mfn_saving_product')
            //             ->select('id','shortName', 'code')
            //             ->get();

            // // conditions start

            // if($request->searchBranch == '' || $request->searchBranch == Null) {
            //     $allBranchTransferred = $allBranchTransferred->where('branchIdFk', $userBranchId);
            // }
            // elseif((int)$request->searchBranch > 0) {
            //     $allBranchTransferred = $allBranchTransferred
            //                             ->where('previousBranchIdFk', $request->searchBranch)
            //                             ->orWhere('newBranchIdFk', $request->searchBranch);
            // }

            // if((int)$request->searchSamity > 0) {
            //     $allBranchTransferred = $allBranchTransferred
            //                             ->where('previousSamityIdFk', $request->searchSamity)
            //                             ->orWhere('newSamityIdFk', $request->searchSamity);
            // }

            // $allBranchTransferred = $allBranchTransferred->get();

            // // dd($allBranchTransferred);
            // $data = array(
            //       'allBranchTransferred'                => $allBranchTransferred,
            //       'requestBranchInfos'                  => $requestBranchInfos,
            //       'RequestBranch'                       => $RequestBranch,
            //       'userBranchId'                        => $userBranchId,
            //       'oldBranchInfos'                      => $oldBranchInfos,
            //       'newBranchInfos'                      => $newBranchInfos,
            //       'requestSamityInfos'                  => $requestSamityInfos,
            //       'RequestSamity'                       => $RequestSamity,
            //       'oldSamityInfos'                      => $oldSamityInfos,
            //       'newSamityInfos'                      => $newSamityInfos,
            //       'memberInfos'                         => $memberInfos,
            //       'savingsProducts'                     => $savingsProducts,
            //       'dateFrom'                            => $dateFrom,
            //       'dateTo'                              => $dateTo,
            //       'reportType'                          => 'Branch Transfer'
            // );
        }
        elseif($request->reportType == 2){

            $allSamityTransferred = DB::table('mfn_member_samity_transfer')
            ->where('status', 1)
            ->where('softDel', 0)
            ->where('transferDate', '<=', $dateTo)
            ->where('transferDate', '>=', $dateFrom);

            $allSamityTransferredCollection = $allSamityTransferred->get();

            // joining information

            $branchInfos = DB::table('gnr_branch')
            ->whereIn('id', $allSamityTransferredCollection->unique('branchIdFk')->pluck('branchIdFk'))
            ->select('id','name', 'branchCode')
            ->get();

            $oldSamityInfos = DB::table('mfn_samity')
            ->whereIn('id', $allSamityTransferredCollection->unique('previousSamityIdFk')->pluck('previousSamityIdFk'))
            ->select('id','name', 'code')
            ->get();

            $newSamityInfos = DB::table('mfn_samity')
            ->whereIn('id', $allSamityTransferredCollection->unique('newSamityIdFk')->pluck('newSamityIdFk'))
            ->select('id','name', 'code')
            ->get();

            $memberInfos = DB::table('mfn_member_information')
            ->whereIn('id', $allSamityTransferredCollection->unique('memberIdFk')->pluck('memberIdFk'))
            ->select('id','name', 'code')
            ->get();

            $savingsProducts = DB::table('mfn_saving_product')
            ->select('id','shortName', 'code')
            ->get();

            // conditions start

            if($request->searchBranch == '' || $request->searchBranch == Null) {
                $allSamityTransferred = $allSamityTransferred->where('branchIdFk', $userBranchId);
            }
            elseif((int)$request->searchBranch > 0) {
                $allSamityTransferred = $allSamityTransferred->where('branchIdFk', $request->searchBranch);
            }

            if((int)$request->searchSamity > 0) {
                $allSamityTransferred = $allSamityTransferred
                ->where('newSamityIdFk', $request->searchSamity)
                ->orWhere('previousSamityIdFk', $request->searchSamity);
            }

            $allSamityTransferred = $allSamityTransferred->get();

            // dd($allSamityTransferred);
            $data = array(
                'allSamityTransferred'                => $allSamityTransferred,
                'requestBranchInfos'                  => $requestBranchInfos,
                'RequestBranch'                       => $RequestBranch,
                'userBranchId'                        => $userBranchId,
                'branchInfos'                         => $branchInfos,
                'requestSamityInfos'                  => $requestSamityInfos,
                'RequestSamity'                       => $RequestSamity,
                'oldSamityInfos'                      => $oldSamityInfos,
                'newSamityInfos'                      => $newSamityInfos,
                'memberInfos'                         => $memberInfos,
                'savingsProducts'                     => $savingsProducts,
                'dateFrom'                            => $dateFrom,
                'dateTo'                              => $dateTo,
                'reportType'                          => 'Samity Transfer'
            );
        }

        return view("microfin.reports.transferRegisterReportsViews.TransferRegisterReportView", $data);
    }

}
