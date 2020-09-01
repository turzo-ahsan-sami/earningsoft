<?php

	namespace App\Http\Controllers\microfin\configuration\reportConfiguration;

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

	class MfnReportConfigurationController extends controller {

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
            $reportconfig =DB::table('mfn_cfg')->where('name','report_cfg')->value('config');
            $reportConfiguration = json_decode($reportconfig,true);
            $damageData = array(
				'boolean'=> $this->MicroFinance->getBooleanOptions(),
				'reportConfiguration'   =>$reportConfiguration,
			);

			return view('microfin.configuration.reportConfiguration.viewReportConfiguration',['damageData' => $damageData]);
		}

		public function addReportConfiguration() {
            $reportconfig =DB::table('mfn_cfg')->where('name','report_cfg')->value('config');
            $reportConfiguration = json_decode($reportconfig,true);
            $damageData = array(
				'boolean'=> $this->MicroFinance->getBooleanOptions(),
				'reportConfiguration'   =>$reportConfiguration,
			);

			return view('microfin.configuration.reportConfiguration.addReportConfiguration',['damageData' => $damageData]);
		}

		/**
		 * [Insert Configuration Samity Configuration]
		 * 
		 * @param Request $req
		 */


		public function addItem(Request $req) {
      
      	      $report=DB::table('mfn_cfg')->where('name','report_cfg')->select('name')->first();
      	      $rules = array(
				'reportHeaderLine1'	                  => 'required',
				'showOrganizationLogoOnAllReport'              => 'required',
				'labelNameForServiceCharge'	              => 'required',
				'isHeaderFooterWillShowInPassbookCheckingReport'         => 'required',
				
			  );

			  $attributesNames = array(
				'reportHeaderLine1'	                                     => 'report Header Line1',
				'showOrganizationLogoOnAllReport'                        => 'show Organization Logo On All Report',
				'labelNameForServiceCharge'	                             => 'label Name For Service Charge',
				'isHeaderFooterWillShowInPassbookCheckingReport'         => 'is Header Footer Will Show In Passbook Checking Report'
				
			  );

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else{
              if($report){
                 $reportConfigurations=MfnSamityConfiguration::where('name','report_cfg')->first();
                 $reportConfigurations->config = json_encode([
                 	'reportHeaderLine1'=>$req->reportHeaderLine1,
                 	'reportHeaderLine2'=>$req->reportHeaderLine2,
                 	'reportHeaderLine3'=>$req->reportHeaderLine3,
                 	'reportFooterLine1'=>$req->reportFooterLine1,
                 	'reportFooterLine2'=>$req->reportFooterLine2,
                 	'showBranchAddressOnReport'=>$req->showBranchAddressOnReport,
                 	'nameBeforePomisReports'=>$req->nameBeforePomisReports,
                 	'showOrganizationLogoOnAllReport'=>$req->showOrganizationLogoOnAllReport,
                 	'labelNameForServiceCharge'=>$req->labelNameForServiceCharge,
                 	'isHeaderFooterWillShowInPassbookCheckingReport'=>$req->isHeaderFooterWillShowInPassbookCheckingReport
                 ]);
                 $reportConfigurations->save();
				  
		     }else{
		    	  $reportConfiguration=new MfnSamityConfiguration();
		          $reportConfiguration->name='report_cfg';
		          $reportConfiguration->config = json_encode([
		          	'reportHeaderLine1'=>$req->reportHeaderLine1,
                 	'reportHeaderLine2'=>$req->reportHeaderLine2,
                 	'reportHeaderLine3'=>$req->reportHeaderLine3,
                 	'reportFooterLine1'=>$req->reportFooterLine1,
                 	'reportFooterLine2'=>$req->reportFooterLine2,
                 	'showBranchAddressOnReport'=>$req->showBranchAddressOnReport,
                 	'nameBeforePomisReports'=>$req->nameBeforePomisReports,
                 	'showOrganizationLogoOnAllReport'=>$req->showOrganizationLogoOnAllReport,
                 	'labelNameForServiceCharge'=>$req->labelNameForServiceCharge,
                 	'isHeaderFooterWillShowInPassbookCheckingReport'=>$req->isHeaderFooterWillShowInPassbookCheckingReport
		          ]);
		          $reportConfiguration->createdDate = Carbon::now();
		          $reportConfiguration->save();
		        }
		     }      
             $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'report Configuration insert successfully.'
              );

               return response::json($data);

        }
    }