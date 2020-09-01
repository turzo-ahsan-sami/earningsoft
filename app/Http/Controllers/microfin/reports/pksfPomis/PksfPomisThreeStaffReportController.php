<?php

namespace App\Http\Controllers\microfin\reports\pksfPomis;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use Route;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\microfin\MicroFin;

class PksfPomisThreeStaffReportController extends Controller {

    public function index() {

        $url = Route::current()->uri;
        $userBranchId=Auth::user()->branchId;

        /// Funding Organization List
        $fundingOrgList = DB::table('mfn_funding_organization')->pluck('name','id')->toArray();
        // funding loan products
        $fundingLoanProducts = DB::table('mfn_loans_product')->select('id','name','shortName','productCategoryId','fundingOrganizationId','isPrimaryProduct')->get();
        // loan categories
        $loanCategories = DB::table('mfn_loans_product_category')->select('id','name','shortName')->get();
        // staff info
        $staffInfo = DB::table('mfn_pksf_staff')->get();

        $filteringArray = array(
            'fundingOrgList'            => $fundingOrgList,
            'loanProducts'              => $fundingLoanProducts,
            'loanCategories'            => $loanCategories,
            'staffInfo'                 => $staffInfo,
        );

        if ($url == 'mfn/pksfPomisThreeStaffReportLoanProduct') {
            return view('microfin.reports.pksfPomisReport.pksfPomisThreeStaffReport.pksfPomisThreeStaffReportLoanProduct', $filteringArray);
        }
        elseif ($url == 'mfn/pksfPomisThreeStaffReportLoanProductCategory') {
            return view('microfin.reports.pksfPomisReport.pksfPomisThreeStaffReport.pksfPomisThreeStaffReportLoanProductCategory', $filteringArray);
        }
    }

    public function saveLoanWiseStaffData(Request $req) {

        $requestData = $req->all();
        unset($requestData['_token']);
        $dataArray = [];
        // dd($requestData);
        foreach ($requestData as $key => $value) {

            $arr = explode('-', $key);

            if ($arr[0] == 'subtotal') {
                $columnName = $arr[1];
                $fundOrgId = (int)$arr[2];
                $fundLoanProductId = 0;

                if (isset($dataArray[$arr[0]][$fundOrgId])) {
                    $dataArray[$arr[0]][$fundOrgId][$columnName] = (double) $value;
                }
                else {
                    $dataArray[$arr[0]][$fundOrgId]['fundLoanProductCategoryId'] = 0;
                    $dataArray[$arr[0]][$fundOrgId]['fundLoanProductId'] = $fundLoanProductId;
                    $dataArray[$arr[0]][$fundOrgId]['fundOrgId'] = $fundOrgId;
                    $dataArray[$arr[0]][$fundOrgId]['dataType'] = 'Loan Product';
                    $dataArray[$arr[0]][$fundOrgId][$columnName] = (double) $value;
                }

            }
            elseif ($arr[0] == 'grandtotal') {
                $columnName = $arr[1];
                $fundOrgId = 0;
                $fundLoanProductId = 0;

                if (isset($dataArray[$arr[0]])) {
                    $dataArray[$arr[0]][$columnName] = (double) $value;
                }
                else {
                    $dataArray[$arr[0]]['fundLoanProductCategoryId'] = 0;
                    $dataArray[$arr[0]]['fundLoanProductId'] = $fundLoanProductId;
                    $dataArray[$arr[0]]['fundOrgId'] = $fundOrgId;
                    $dataArray[$arr[0]]['dataType'] = 'Loan Product';
                    $dataArray[$arr[0]][$columnName] = (double) $value;
                }
            }
            else {
                $columnName = $arr[0];
                $fundOrgId = (int)$arr[1];
                $fundLoanProductId = (int)$arr[2];
                // $loanProductTotalStaff[$fundLoanProductId] += (double) $value;

                if (isset($dataArray['loan'][$fundLoanProductId])) {
                    $dataArray['loan'][$fundLoanProductId][$columnName] = (double) $value;
                    $dataArray['loan'][$fundLoanProductId]['totalStaff'] += (double) $value;
                }
                else {
                    $dataArray['loan'][$fundLoanProductId]['fundLoanProductCategoryId'] = 0;
                    $dataArray['loan'][$fundLoanProductId]['fundLoanProductId'] = $fundLoanProductId;
                    $dataArray['loan'][$fundLoanProductId]['fundOrgId'] = $fundOrgId;
                    $dataArray['loan'][$fundLoanProductId]['dataType'] = 'Loan Product';
                    $dataArray['loan'][$fundLoanProductId][$columnName] = (double) $value;
                    $dataArray['loan'][$fundLoanProductId]['totalStaff'] = (double) $value;
                }
            }

        } // loop close
        // dd($dataArray);

        foreach ($dataArray['loan'] as $key => $data) {
            if($data['totalStaff'] == 0) {
                unset($dataArray['loan'][$key]);
            }
            else {
                unset($dataArray['loan'][$key]['totalStaff']);
            }
        }

        $loanWiseData = $dataArray['loan'];
        $subtotalData = $dataArray['subtotal'];
        $grandtotalData = $dataArray['grandtotal'];

        DB::table('mfn_pksf_staff')->where('dataType', 'Loan Product')->delete();
        DB::table('mfn_pksf_staff')->insert($loanWiseData);
        DB::table('mfn_pksf_staff')->insert($subtotalData);
        DB::table('mfn_pksf_staff')->insert($grandtotalData);

        $data = array(
            'responseTitle' => 'Success!',
            'responseText' => 'Data Saved Successfully!',
        );

        return response()->json($data);

    }

    public function saveLoanCategoryWiseStaffData(Request $req) {

        $requestData = $req->all();
        unset($requestData['_token']);
        $dataArray = [];
        // dd($requestData);
        foreach ($requestData as $key => $value) {

            $arr = explode('-', $key);

            if ($arr[0] == 'subtotal') {
                $columnName = $arr[1];
                $fundOrgId = (int)$arr[2];
                $fundLoanProductCategoryId = 0;

                if (isset($dataArray[$arr[0]][$fundOrgId])) {
                    $dataArray[$arr[0]][$fundOrgId][$columnName] = (double) $value;
                }
                else {
                    $dataArray[$arr[0]][$fundOrgId]['fundLoanProductCategoryId'] = $fundLoanProductCategoryId;
                    $dataArray[$arr[0]][$fundOrgId]['fundLoanProductId'] = 0;
                    $dataArray[$arr[0]][$fundOrgId]['fundOrgId'] = $fundOrgId;
                    $dataArray[$arr[0]][$fundOrgId]['dataType'] = 'Loan Category';
                    $dataArray[$arr[0]][$fundOrgId][$columnName] = (double) $value;
                }

            }
            elseif ($arr[0] == 'grandtotal') {
                $columnName = $arr[1];
                $fundOrgId = 0;
                $fundLoanProductCategoryId = 0;

                if (isset($dataArray[$arr[0]])) {
                    $dataArray[$arr[0]][$columnName] = (double) $value;
                }
                else {
                    $dataArray[$arr[0]]['fundLoanProductCategoryId'] = $fundLoanProductCategoryId;
                    $dataArray[$arr[0]]['fundLoanProductId'] = 0;
                    $dataArray[$arr[0]]['fundOrgId'] = $fundOrgId;
                    $dataArray[$arr[0]]['dataType'] = 'Loan Category';
                    $dataArray[$arr[0]][$columnName] = (double) $value;
                }
            }
            else {
                $columnName = $arr[0];
                $fundOrgId = (int)$arr[1];
                $fundLoanProductCategoryId = (int)$arr[2];

                if (isset($dataArray['loanCategory'][$fundOrgId][$fundLoanProductCategoryId])) {
                    $dataArray['loanCategory'][$fundOrgId][$fundLoanProductCategoryId][$columnName] = (double) $value;
                    $dataArray['loanCategory'][$fundOrgId][$fundLoanProductCategoryId]['totalStaff'] += (double) $value;
                }
                else {
                    $dataArray['loanCategory'][$fundOrgId][$fundLoanProductCategoryId]['fundLoanProductCategoryId'] = $fundLoanProductCategoryId;
                    $dataArray['loanCategory'][$fundOrgId][$fundLoanProductCategoryId]['fundLoanProductId'] = 0;
                    $dataArray['loanCategory'][$fundOrgId][$fundLoanProductCategoryId]['fundOrgId'] = $fundOrgId;
                    $dataArray['loanCategory'][$fundOrgId][$fundLoanProductCategoryId]['dataType'] = 'Loan Category';
                    $dataArray['loanCategory'][$fundOrgId][$fundLoanProductCategoryId][$columnName] = (double) $value;
                    $dataArray['loanCategory'][$fundOrgId][$fundLoanProductCategoryId]['totalStaff'] = (double) $value;
                }
            }

        } // loop close
        // dd($dataArray['loanCategory']);

        $loanCategoryWiseData = $dataArray['loanCategory'];
        $subtotalData = $dataArray['subtotal'];
        $grandtotalData = $dataArray['grandtotal'];

        DB::table('mfn_pksf_staff')->where('dataType', 'Loan Category')->delete();

        foreach ($loanCategoryWiseData as $fundOrgkey => $data) {
            foreach ($data as $key => $array) {
                if($array['totalStaff'] != 0) {
                    unset($array['totalStaff']);
                    DB::table('mfn_pksf_staff')->insert($array);
                }

            }
        }

        DB::table('mfn_pksf_staff')->insert($subtotalData);
        DB::table('mfn_pksf_staff')->insert($grandtotalData);

        $data = array(
            'responseTitle' => 'Success!',
            'responseText' => 'Data Saved Successfully!',
        );

        return response()->json($data);

    }


}
