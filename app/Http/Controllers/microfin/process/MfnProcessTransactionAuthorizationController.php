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
use App\microfin\MfnMemberType;
use App\Traits\CreateForm;
use App\microfin\savings\MfnSavingsDeposit;
use App\microfin\savings\MfnSavingsWithdraw;
use Auth;
use App\Http\Controllers\microfin\MicroFin;
use App\Http\Controllers\gnr\Service;
use App;

class MfnProcessTransactionAuthorizationController extends Controller {

    private $TCN;
    
    public function __construct() {

        $this->TCN = array(
            array('SL#', 70),
            array('SAMITY CODE', 0),
            array('SAMITY NAME', 0),
            array('LOAN DISBURSEMENT AMOUNT', 0),
            array('SAVINGS COLLECTION AMOUNT', 0),
            array('WITHDRAW AMOUNT', 0),
            array('LOAN TRANSACTION AMOUNT', 0),                
            array('ACTION', 80)
        );
    }

    public function index(){
        $userBarnchId = Auth::user()->branchId;

        if ($userBarnchId==1) {
            $branchList = DB::table('gnr_branch')
            ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
            ->orderBy('branchCode')
            ->get()
            ->pluck('nameWithCode', 'id') 
            ->all();
            $data = array(
                'branchList' => $branchList
            );
            return view('microfin/process/transactionAuthorization/filteringPart',$data);
        }

        $softwareDate = $this->getSoftwareDate();

        $unAuthorizedDeposits = DB::table('mfn_savings_deposit')->where('softDel',0)->where('isAuthorized',0);
        $unAuthorizedWithdraws = DB::table('mfn_savings_withdraw')->where('softDel',0)->where('isAuthorized',0);
        $unAuthorizedCollections = DB::table('mfn_loan_collection')->where('softDel',0)->where('isAuthorized',0);

        if ($userBarnchId!=1) {
            $branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);

            /*$unAuthorizedDeposits = $unAuthorizedDeposits->where('branchIdFk',$userBarnchId)->where('depositDate',$softwareDate);
            $unAuthorizedWithdraws = $unAuthorizedWithdraws->where('branchIdFk',$userBarnchId)->where('withdrawDate',$softwareDate);
            $unAuthorizedCollections = $unAuthorizedCollections->where('branchIdFk',$userBarnchId)->where('collectionDate',$softwareDate);*/

            $unAuthorizedDeposits = $unAuthorizedDeposits->whereIn('branchIdFk',$branchIdArray)->where('depositDate',$softwareDate);
            $unAuthorizedWithdraws = $unAuthorizedWithdraws->whereIn('branchIdFk',$branchIdArray)->where('withdrawDate',$softwareDate);
            $unAuthorizedCollections = $unAuthorizedCollections->whereIn('branchIdFk',$branchIdArray)->where('collectionDate',$softwareDate);
        }

        $unAuthorizedDeposits = $unAuthorizedDeposits->get();
        $unAuthorizedWithdraws = $unAuthorizedWithdraws->get();
        $unAuthorizedCollections = $unAuthorizedCollections->get();

        $unAuthorizedDepositsSamityIds = $unAuthorizedDeposits->pluck('samityIdFk')->toArray();
        $unAuthorizedWithdrawsSamityIds = $unAuthorizedWithdraws->pluck('samityIdFk')->toArray();
        $unAuthorizedCollectionsSamityIds = $unAuthorizedCollections->pluck('samityIdFk')->toArray();
        
        $samityIds = array_merge($unAuthorizedDepositsSamityIds,$unAuthorizedWithdrawsSamityIds,$unAuthorizedCollectionsSamityIds);
        $samityIds = array_unique($samityIds);
        
        if ($userBarnchId==1) {
            $samitys = DB::table('mfn_samity')->where('status',1)/*->where('branchId',$userBarnchId)*/->whereIn('id',$samityIds)->get();
        }
        else{
            $samitys = DB::table('mfn_samity')->where('status',1)->where('branchId',$userBarnchId)->whereIn('id',$samityIds)->get();
        }

        $TCN = $this->TCN;
        $damageData = array(
            'TCN'           =>  $TCN,
            'samitys'       =>  $samitys,
            'softwareDate'  =>  $softwareDate
        );

        return view('microfin/process/transactionAuthorization/viewTranasctionAutthorization',$damageData);
    }

        /**
         * [this is only for the head office users, it filters the transaction authentication list base on branch and date]
         * @param  Request $req [description]
         * @return [html content]       [list of samity with transactionn information]
         */
        public function getSearchResult(Request $req){

            $searchDate = Carbon::parse($req->filDate)->format('Y-m-d');
            $unAuthorizedDeposits = DB::table('mfn_savings_deposit')->where('softDel',0)->where('isAuthorized',0)->where('depositDate',$searchDate);
            $unAuthorizedWithdraws = DB::table('mfn_savings_withdraw')->where('softDel',0)->where('isAuthorized',0)->where('withdrawDate',$searchDate);
            $unAuthorizedCollections = DB::table('mfn_loan_collection')->where('softDel',0)->where('isAuthorized',0)->where('collectionDate',$searchDate);

            if ($req->filBranch!=null || $req->filBranch!='') {
                $unAuthorizedDeposits = $unAuthorizedDeposits->where('branchIdFk',$req->filBranch);
                $unAuthorizedWithdraws = $unAuthorizedWithdraws->where('branchIdFk',$req->filBranch);
                $unAuthorizedCollections = $unAuthorizedCollections->where('branchIdFk',$req->filBranch);
            }

            $unAuthorizedDepositsSamityIds = $unAuthorizedDeposits->pluck('samityIdFk')->toArray();
            $unAuthorizedWithdrawsSamityIds = $unAuthorizedWithdraws->pluck('samityIdFk')->toArray();
            $unAuthorizedCollectionsSamityIds = $unAuthorizedCollections->pluck('samityIdFk')->toArray();

            $samityIds = array_merge($unAuthorizedDepositsSamityIds,$unAuthorizedWithdrawsSamityIds,$unAuthorizedCollectionsSamityIds);
            $samityIds = array_unique($samityIds);
            
            $samitys = DB::table('mfn_samity')->where('status',1)->whereIn('id',$samityIds)->get();

            $TCN = $this->TCN;
            $data = array(
                'TCN'           =>  $TCN,
                'samitys'       =>  $samitys,
                'userBarnchId'  =>  Auth::user()->branchId,
                'searchDate'    =>  $searchDate
            );

            return view('microfin/process/transactionAuthorization/filtertedContetnt',$data);
        }

        /**
         * [makeCollectionWithAdditionalAttribute description]
         * @param  [collection] $collectionObj
         * @param  [string] $attrName
         * @param  [string] $attrValue
         * @return [coeelction]
         */
        public function makeCollectionWithAdditionalAttribute($collectionObj,$attrName,$attrValue){
            $collectionObj = json_decode($collectionObj, true);
            $collectionObj = collect($collectionObj);
            $collectionObj = $collectionObj->map(function ($collection) use  ($attrName, $attrValue){
                $collection[$attrName] = $attrValue;
                return $collection;
            });

            return $collectionObj;
        }

        /**
         * [this function authorize the samity transaction]
         * @param  Request $req [it holds the samity id]
         * @return [json]       [json response]
         */
        public function authorizeSamityTransaction(Request $req){

            $samity = DB::table('mfn_samity')->where('id',$req->samityId)->select('id','branchId')->first();
            $softwareDate = MicroFin::getSoftwareDateBranchWise($samity->branchId);

            DB::table('mfn_savings_deposit')->where('softDel',0)->where('samityIdFk',$req->samityId)->where('depositDate',$softwareDate)->where('isAuthorized',0)->update(['isAuthorized'=>1]);  
            $logArray = array(
                        'moduleId'  => 6,
                        'controllerName'  => 'MfnProcessTransactionAuthorizationController',
                        'tableName'  => 'mfn_savings_deposit',
                        'operation'  => 'insert',
                        'primaryIds'  => [DB::table('mfn_savings_deposit')->max('id')]
                        );
                Service::createLog($logArray);          
            
            DB::table('mfn_savings_withdraw')->where('softDel',0)->where('samityIdFk',$req->samityId)->where('withdrawDate',$softwareDate)->where('isAuthorized',0)->update(['isAuthorized'=>1]);

            DB::table('mfn_loan')->where('softDel',0)->where('samityIdFk',$req->samityId)->where('disbursementDate',$softwareDate)->where('isAuthorized',0)->update(['isAuthorized'=>1]);

            DB::table('mfn_loan_collection')->where('softDel',0)->where('samityIdFk',$req->samityId)->where('collectionDate',$softwareDate)->where('isAuthorized',0)->update(['isAuthorized'=>1]);          
            
            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Samity transaction authorized successfully.'
            );
            
            return response::json($data);
        }


        public function authorizeAllTransactions(Request $req){

            $userBarnchId = Auth::user()->branchId;
            if ($userBarnchId!=1) {
                $targetBranchId = $userBarnchId;
                $softwareDate = $this->getSoftwareDate();
            }
            else{
                $targetBranchId = $req->branchId;
                $softwareDate = MicroFin::getSoftwareDateBranchWise($req->branchId);
            }

            DB::table('mfn_savings_deposit')->where('softDel',0)->where('branchIdFk',$targetBranchId)->where('depositDate',$softwareDate)->where('isAuthorized',0)->update(['isAuthorized'=>1]);            
            
            DB::table('mfn_savings_withdraw')->where('softDel',0)->where('branchIdFk',$targetBranchId)->where('withdrawDate',$softwareDate)->where('isAuthorized',0)->update(['isAuthorized'=>1]);

            DB::table('mfn_loan')->where('softDel',0)->where('branchIdFk',$targetBranchId)->where('disbursementDate',$softwareDate)->where('isAuthorized',0)->update(['isAuthorized'=>1]);

            DB::table('mfn_loan_collection')->where('softDel',0)->where('branchIdFk',$targetBranchId)->where('collectionDate',$softwareDate)->where('isAuthorized',0)->update(['isAuthorized'=>1]);            
            
            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'All transaction authorized successfully.'
            );
            
            return response::json($data);
        }

        public function getSoftwareDate(){
            $userBarnchId = Auth::user()->branchId;

            $softwareDate = DB::table('mfn_day_end')->where('branchIdFk',$userBarnchId)->where('isLocked',0)->value('date');
            if ($softwareDate=='' || $softwareDate==null) {
                $softwareDate = DB::table('gnr_branch')->where('id',$userBarnchId)->value('softwareStartDate');
            }            

            return $softwareDate;
        }

        
    }

    