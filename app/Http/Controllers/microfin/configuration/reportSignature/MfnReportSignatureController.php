<?php

	namespace App\Http\Controllers\microfin\configuration\reportSignature;

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

	class MfnReportSignatureController extends controller {

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

			  $configSignature =DB::table('mfn_cfg')->where('name','signature_cfg')->value('config');
			  $signatureconfiguration = json_decode($configSignature,true);


			$damageData = array(
				'boolean'  =>  $this->MicroFinance->getBooleanOptions(),
				'signatureconfiguration' =>$signatureconfiguration
				
			);

			return view('microfin.configuration.reportSignature.viewReportSignature',['damageData' => $damageData]);
		}

		public function addReportSignature() {

			  $configSignature =DB::table('mfn_cfg')->where('name','signature_cfg')->value('config');
			  $signatureconfiguration = json_decode($configSignature,true);


			$damageData = array(
				'boolean'  =>  $this->MicroFinance->getBooleanOptions(),
				'signatureconfiguration' =>$signatureconfiguration
				
			);

			return view('microfin.configuration.reportSignature.addReportSignature',['damageData' => $damageData]);
		}

		/**
		 * [Insert settings genaral configuration]
		 * 
		 * @param Request $req
		 */
	public function addItem(Request $req) {
      		
      	    $reportSignature=DB::table('mfn_cfg')->where('name','signature_cfg')->value('name');
	      	    
      	    
      	        if($reportSignature){
		                $signatureConfigurations=MfnSamityConfiguration::where('name','signature_cfg')->first();
		                $signatureConfigurations->config = json_encode([
		                    'footerLevel1'=>$req->footerLevel1,
				          	'value1'=>$req->value1,
				          	'FooterLevel2'=>$req->FooterLevel2,
				          	'value2'=>$req->value2,
				          	'footerLevel3'=>$req->footerLevel3,
				          	'value3'=>$req->value3,
				          	'Footer2Level3'=>$req->Footer2Level3,
				          	'value4'=>$req->value4,
				          	'footer2Level2'=>$req->footer2Level2,
				          	'value5'=>$req->value5,
				          	'footer2Level3'=>$req->footer2Level3,
				          	'value6'=>$req->value6,
				          	'footer3Level1'=>$req->footer3Level1,
				          	'value7'=>$req->value7,
				          	'footer3Level2'=>$req->footer3Level2,
				          	'value8'=>$req->value8,
				          	'footer3Level3'=>$req->footer3Level3,
				          	'value9'=>$req->value9
		                 ]);
		                 $signatureConfigurations->save();

		              }      
		         else {
                         $signatureConfiguration=new MfnSamityConfiguration;
                         $signatureConfiguration->name='signature_cfg';
                         $signatureConfiguration->config = json_encode([
				          	'footerLevel1'=>$req->footerLevel1,
				          	'value1'=>$req->value1,
				          	'FooterLevel2'=>$req->FooterLevel2,
				          	'value2'=>$req->value2,
				          	'footerLevel3'=>$req->footerLevel3,
				          	'value3'=>$req->value3,
				          	'Footer2Level3'=>$req->Footer2Level3,
				          	'value4'=>$req->value4,
				          	'footer2Level2'=>$req->footer2Level2,
				          	'value5'=>$req->value5,
				          	'footer2Level3'=>$req->footer2Level3,
				          	'value6'=>$req->value6,
				          	'footer3Level1'=>$req->footer3Level1,
				          	'value7'=>$req->value7,
				          	'footer3Level2'=>$req->footer3Level2,
				          	'value8'=>$req->value8,
				          	'footer3Level3'=>$req->footer3Level3,
				          	'value9'=>$req->value9
				          ]);
				          $signatureConfiguration->createdDate = Carbon::now();
				          $signatureConfiguration->save();
				         

				      }      
			          $data = array(
			             'responseTitle' =>  'Success!',
			             'responseText'  =>  'Report Signature insert successfully.'
			        );

                  return response::json($data);        
			      
      	 }

		
	}