<?php

	namespace App\Http\Controllers\microfin\configuration\dailyWeeklyConfiguration;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\configuration\MfnSamityConfiguration;
	use Session;
	use Validator;
	use Response;
	use DB;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\Auth;
	use App\Http\Controllers\Controller;
	use App\Http\Controllers\microfin\MicroFinance;

	class MfnDailyWeeklyConfigurationController extends controller {

		protected $MicroFinance;

		public function __construct() {

			$this->MicroFinance = New MicroFinance;

			$this->TCN = array(
				array('SL No.', 70), 
	            array('Name', 0),
	            array('Status', 70),
			    array('Action', 80)
			);	
		}
        public function index() {

			  $configDailyWeekly =DB::table('mfn_cfg')->where('name','dailyWeekly_cfg')->value('config');
			  $dailyWeeklyConfigurationData = json_decode($configDailyWeekly,true);


			$damageData = array(
				'boolean'  =>  $this->MicroFinance->getBooleanOptions(),
				'dailyWeeklyConfigurationData' =>$dailyWeeklyConfigurationData
				
			);

			return view('microfin.configuration.dailyWeeklyConfiguration.viewDailyWeeklyConfiguration',['damageData' => $damageData]);
		 }

		public function addDailyWeeklyConfiguration() {

			  $configDailyWeekly =DB::table('mfn_cfg')->where('name','dailyWeekly_cfg')->value('config');
			  $dailyWeeklyConfigurationData = json_decode($configDailyWeekly,true);


			$damageData = array(
				'boolean'  =>  $this->MicroFinance->getBooleanOptions(),
				'dailyWeeklyConfigurationData' =>$dailyWeeklyConfigurationData
				
			);

			return view('microfin.configuration.dailyWeeklyConfiguration.addDailyWeeklyConfiguration',['damageData' => $damageData]);
		}

		/**
		 * [Insert settings genaral configuration]
		 * 
		 * @param Request $req
		 */
	public function addItem(Request $req) {
      		
      	    $dailyWeekly=DB::table('mfn_cfg')->where('name','dailyWeekly_cfg')->value('name');
	      	
      	     if($dailyWeekly){
		          $dailyWeeklyConfigurations=MfnSamityConfiguration::where('name','dailyWeekly_cfg')->first();
		          $dailyWeeklyConfigurations->config = json_encode([
		                'insClaim'=>$req->insClaim,
		                'fullyPaymentLoan'=>$req->fullyPaymentLoan,
		                'loanDisbursement'=>$req->loanDisbursement,
		                'showOptionalProductFullyPaid'=>$req->showOptionalProductFullyPaid,
		                'passBookCollectionFeeOnAnd'=>$req->passBookCollectionFeeOnAnd,
		                'showRedColorSamityOnFoReport'=>$req->showRedColorSamityOnFoReport,
		                'showSamityListInBM'=>$req->showSamityListInBM,
		                'showLoanFromFee'=>$req->showLoanFromFee,
		                'showMultipleInsuranceAt'=>$req->showMultipleInsuranceAt,
		                'todaysDue'=>$req->todaysDue,
		                'todaysTotalCollection'=>$req->todaysTotalCollection,
		                'todaysTotalNetCollection'=>$req->todaysTotalNetCollection,
		                'todaysTotalRefund'=>$req->todaysTotalRefund,
		                'totalNoInstallDue'=>$req->totalNoInstallDue,
		                'totalNoInstallPayer'=>$req->totalNoInstallPayer,
		                'totalLoanee'=>$req->totalLoanee,
		                'totalMembers'=>$req->totalMembers,
		                ]);
		                $dailyWeeklyConfigurations->save();

		              }      
		    else {
                         $dailyWeeklyConfiguration=new MfnSamityConfiguration;
                         $dailyWeeklyConfiguration->name='dailyWeekly_cfg';
                         $dailyWeeklyConfiguration->config = json_encode([
				          	'insClaim'=>$req->insClaim,
		                   	'fullyPaymentLoan'=>$req->fullyPaymentLoan,
			                'loanDisbursement'=>$req->loanDisbursement,
			                'showOptionalProductFullyPaid'=>$req->showOptionalProductFullyPaid,
			                'passBookCollectionFeeOnAnd'=>$req->passBookCollectionFeeOnAnd,
			                'showRedColorSamityOnFoReport'=>$req->showRedColorSamityOnFoReport,
			                'showSamityListInBM'=>$req->showSamityListInBM,
			                'showLoanFromFee'=>$req->showLoanFromFee,
			                'showMultipleInsuranceAt'=>$req->showMultipleInsuranceAt,
			                'todaysDue'=>$req->todaysDue,
			                'todaysTotalCollection'=>$req->todaysTotalCollection,
			                'todaysTotalNetCollection'=>$req->todaysTotalNetCollection,
			                'todaysTotalRefund'=>$req->todaysTotalRefund,
			                'totalNoInstallDue'=>$req->totalNoInstallDue,
			                'totalNoInstallPayer'=>$req->totalNoInstallPayer,
			                'totalLoanee'=>$req->totalLoanee,
			                'totalMembers'=>$req->totalMembers,
				          ]);
				          $dailyWeeklyConfiguration->createdDate = Carbon::now();
				          $dailyWeeklyConfiguration->save();
				         

				    }      
			        $data = array(
			             'responseTitle' =>  'Success!',
			             'responseText'  =>  'Daily & Weekly Configuration insert successfully.'
			        );

                  return response::json($data);        
			      
      	 
      	    
                  	

        }

		
	}