<?php

namespace App\Http\Controllers\accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\accounting\AddVoucher;
use App\accounting\AddVoucherDetails;
use App\gnr\GnrProject;
use App\gnr\GnrProjectType;
use App\gnr\GnrBranch;
use App\accounting\AddLedger;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon;
use App\Traits\GetSoftwareDate;



class AccAuthenticateAutoVoucherController extends Controller {

    use GetSoftwareDate;

    public function index(Request $request) {

       $user_branch_id = Auth::user()->branchId;
       $moduleId= (int)$request->moduleId;
       $softDate = GetSoftwareDate::getAccountingSoftwareDate();
       // var_dump($moduleId);
       // exit();

		 $autoVouchers = DB::table('acc_voucher')
		            ->where('branchId', $user_branch_id)
		            ->where('vGenerateType', 1)
		            ->where('moduleIdFk', $moduleId)
		            ->where('status', 0)
		            ->where('voucherDate', $softDate)
		            ->get();

		  $data = array(
		  	'autoVouchers' => $autoVouchers,
		  	'moduleId' => $moduleId
		  );


        return view('accounting.autoVouchers.unauthorizedAutoVouchersList', $data);    
       // var_dump($autoVouchers);

    } //End unauthorizedVouchersList function


    // authenticate auto vouchers
    public function authenticateAutoVoucherItem(Request $request){

    	$user_branch_id = Auth::user()->branchId;
    	 $authBy = Auth::user()->emp_id_fk;
    	$moduleId= $request->moduleId;
    	$softDate = GetSoftwareDate::getAccountingSoftwareDate();

    	$voucherIds=DB::table('acc_voucher')
		            ->where('branchId', $user_branch_id)
		            ->where('vGenerateType', 1)
		            ->where('moduleIdFk', $moduleId)
		            ->where('status', 0)
		            ->where('voucherDate', $softDate)
		            ->pluck('id')
		            ->toArray();

    	DB::table('acc_voucher')
            ->whereIn('id', $voucherIds)            
            ->update(
            	['status'=> 1, 'authBy' =>$authBy]
            );

        DB::table('acc_voucher_details')
            ->whereIn('voucherId', $voucherIds)            
            ->update(
            	['status'=> 1]
            );

		$data = array(
               'responseTitle' =>  'Success!',
               'responseText'  =>  'Authenticated successfully.'
           );

           return response::json($data);
    }

    
}   //END Controller
