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

    class MfnProcessTransactionUnauthorizationController extends Controller {        

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

        public function index() {

            $userBarnchId = Auth::user()->branchId;

            if ($userBarnchId==1) {
                $branchList = MicroFin::getBranchList();
                $data = array(
                    'branchList' => $branchList
                );
                return view('microfin.process.transactionUnauthorization.filteringPart',$data);
            }   
           
            $softwareDate = $this->getSoftwareDate();

            $authorizedDeposits = DB::table('mfn_savings_deposit')->where('softDel',0)->where('isAuthorized',1);
            $authorizedWithdraws = DB::table('mfn_savings_withdraw')->where('softDel',0)->where('isAuthorized',1);

            if ($userBarnchId!=1) {
                $authorizedDeposits = $authorizedDeposits->where('branchIdFk',$userBarnchId)->where('depositDate',$softwareDate);
                $authorizedWithdraws = $authorizedWithdraws->where('branchIdFk',$userBarnchId)->where('withdrawDate',$softwareDate);
            }

            $authorizedDeposits = $authorizedDeposits->get();
            $authorizedWithdraws = $authorizedWithdraws->get();

            $authorizedDepositsSamityIds = $authorizedDeposits->pluck('samityIdFk')->toArray();
            $authorizedWithdrawsSamityIds = $authorizedWithdraws->pluck('samityIdFk')->toArray();
           
            $samityIds = array_merge($authorizedDepositsSamityIds,$authorizedWithdrawsSamityIds);
            $samityIds = array_unique($samityIds);
            
            $samitys = DB::table('mfn_samity')->where('status',1)->whereIn('id',$samityIds)->get();            

            $TCN = $this->TCN;
            $damageData = array(
                'TCN'           =>  $TCN,
                'samitys'       =>  $samitys,
                'softwareDate'  =>  $softwareDate
            );

            return view('microfin.process.transactionUnauthorization.viewTranasctionUnautthorization',$damageData);
        }

        /**
         * [this is only for the head office users, it filters the transaction authentication list base on branch and date]
         * @param  Request $req [description]
         * @return [html content]       [list of samity with transactionn information]
         */
        public function getSearchResult(Request $req){

            $searchDate = Carbon::parse($req->filDate)->format('Y-m-d');
            $unAuthorizedDeposits = DB::table('mfn_savings_deposit')->where('softDel',0)->where('isAuthorized',1)->where('depositDate',$searchDate);
            $unAuthorizedWithdraws = DB::table('mfn_savings_withdraw')->where('softDel',0)->where('isAuthorized',1)->where('withdrawDate',$searchDate);

            if ($req->filBranch!=null || $req->filBranch!='') {
                $unAuthorizedDeposits = $unAuthorizedDeposits->where('branchIdFk',$req->filBranch);
                $unAuthorizedWithdraws = $unAuthorizedWithdraws->where('branchIdFk',$req->filBranch);
            }

            if ($req->filSamity!=null || $req->filSamity!='') {
                $unAuthorizedDeposits = $unAuthorizedDeposits->where('samityIdFk',$req->filSamity);
                $unAuthorizedWithdraws = $unAuthorizedWithdraws->where('samityIdFk',$req->filSamity);
            }

            $unAuthorizedDepositsSamityIds = $unAuthorizedDeposits/*->pluck('samityIdFk')->toArray()*/->get();
            $unAuthorizedWithdrawsSamityIds = $unAuthorizedWithdraws/*->pluck('samityIdFk')->toArray()*/->get();

            $samityIds = array_merge($unAuthorizedDepositsSamityIds,$unAuthorizedWithdrawsSamityIds);
            $samityIds = array_unique($samityIds);
            
            $samitys = DB::table('mfn_samity')->where('status',1)->whereIn('id',$samityIds)->get();

            $softwareDate = MicroFin::getSoftwareDateBranchWise($req->filBranch);

            $TCN = $this->TCN;
            $data = array(
                'TCN'                   => $TCN,
                'samitys'               => $samitys,
                'userBarnchId'          => Auth::user()->branchId,
                'searchDate'            => $searchDate,
                'softwareDate'          => $softwareDate,
                'unAuthorizedDeposits'  => $unAuthorizedDeposits
                'unAuthorizedWithdraws' => $unAuthorizedWithdraws
            );

            return view('microfin.process.transactionUnauthorization.filtertedContetnt',$data);
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
         * [this function unauthorize the transactions samity wise]
         * @param  Request $req [it holds the transaction type and its Id]
         * @return [json]       [json response]
         */
        public function unAuthorizeSamityTransaction(Request $req){            
            $samity = DB::table('mfn_samity')->where('id',$req->samityId)->select('id','branchId')->first();
            $softwareDate = MicroFin::getSoftwareDateBranchWise($samity->branchId);

            DB::table('mfn_savings_deposit')->where('softDel',0)->where('samityIdFk',$req->samityId)->where('depositDate',$softwareDate)->where('isAuthorized',1)->update(['isAuthorized'=>0]);            
            
            DB::table('mfn_savings_withdraw')->where('softDel',0)->where('samityIdFk',$req->samityId)->where('withdrawDate',$softwareDate)->where('isAuthorized',1)->update(['isAuthorized'=>0]); 

            DB::table('mfn_loan_collection')->where('softDel',0)->where('samityIdFk',$req->samityId)->where('collectionDate',$softwareDate)->where('isAuthorized',1)->update(['isAuthorized'=>0]);            
            
            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Samity transaction unauthorized successfully.'
            );
            
            return response::json($data);
        }

        /**
         * [this function unauthorize a single transaction]
         * @param  Request $req [it holds the transaction type and its Id]
         * @return [json]       [json response]
         */
        public function unAuthorizeTransaction(Request $req){
            $samity = DB::table('mfn_samity')->where('id',$req->samityId)->select('id','branchId')->first();
            $softwareDate = MicroFin::getSoftwareDateBranchWise($samity->branchId);

            DB::table('mfn_savings_deposit')->where('softDel',0)->where('samityIdFk',$req->samityId)->where('depositDate',$softwareDate)->where('isAuthorized',1)->update(['isAuthorized'=>0]);            
            
            DB::table('mfn_savings_withdraw')->where('softDel',0)->where('samityIdFk',$req->samityId)->where('withdrawDate',$softwareDate)->where('isAuthorized',1)->update(['isAuthorized'=>0]); 

            DB::table('mfn_loan_collection')->where('softDel',0)->where('samityIdFk',$req->samityId)->where('collectionDate',$softwareDate)->where('isAuthorized',1)->update(['isAuthorized'=>0]);            
            
            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Samity transaction unauthorized successfully.'
            );
            
            return response::json($data);
        }

        public function unauthorizeAllTransactions(Request $req){
            
            $userBarnchId = Auth::user()->branchId;
            if ($userBarnchId!=1) {
                $targetBranchId = $userBarnchId;
                $softwareDate = $this->getSoftwareDate();
            }
            else{
                $targetBranchId = $req->branchId;
                $softwareDate = MicroFin::getSoftwareDateBranchWise($req->branchId);
            }

            DB::table('mfn_savings_deposit')->where('softDel',0)->where('branchIdFk',$targetBranchId)->where('depositDate',$softwareDate)->where('isAuthorized',1)->update(['isAuthorized'=>0]);            
            
            DB::table('mfn_savings_withdraw')->where('softDel',0)->where('branchIdFk',$targetBranchId)->where('withdrawDate',$softwareDate)->where('isAuthorized',1)->update(['isAuthorized'=>0]); 

            DB::table('mfn_loan_collection')->where('softDel',0)->where('branchIdFk',$targetBranchId)->where('collectionDate',$softwareDate)->where('isAuthorized',1)->update(['isAuthorized'=>0]);            
            
            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'All transaction unauthorized successfully.'
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

        