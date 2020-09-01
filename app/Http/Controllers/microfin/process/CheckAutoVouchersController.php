<?php

    namespace App\Http\Controllers\microfin\process;

    use Carbon\Carbon;
    use DB;
    use App\Http\Controllers\Controller;


    class CheckAutoVouchersController extends Controller {

        public function index(){
            $branchId = 87;
            $startDate = Carbon::parse('2018-12-01');
            $endDate = Carbon::parse('2018-12-10');
        }

        public function checkInapproprirateAutoVouchers($branchId,$date){
            // GET THE AUTO VOUCHERS ON THIS DATE
            // $vouchers = DB::table('mfn_')
        }
    }