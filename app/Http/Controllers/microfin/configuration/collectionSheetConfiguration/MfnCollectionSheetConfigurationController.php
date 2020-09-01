<?php

	namespace App\Http\Controllers\microfin\configuration\collectionSheetConfiguration;

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

	class MfnCollectionSheetConfigurationController extends controller {

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

			$configCollectionSheet =DB::table('mfn_cfg')->where('name','collectionSheet_cfg')->value('config');
			$collectionSheetConfiguration = json_decode($configCollectionSheet,true);

			$damageData = array(
				'boolean'  =>  $this->MicroFinance->getBooleanOptions(),
				'collectionSheetConfiguration' =>$collectionSheetConfiguration
				
			);

			return view('microfin.configuration.collectionSheetConfiguration.viewCollectionSheetConfiguration',['damageData' => $damageData]);
		}

	public function addCollectionSheetConfiguration() {

		$configCollectionSheet =DB::table('mfn_cfg')->where('name','collectionSheet_cfg')->value('config');
		$collectionSheetConfiguration = json_decode($configCollectionSheet,true);

		$damageData = array(
			'boolean'  =>  $this->MicroFinance->getBooleanOptions(),
			'collectionSheetConfiguration' =>$collectionSheetConfiguration
		);

		return view('microfin.configuration.collectionSheetConfiguration.addCollectionSheetConfiguration',['damageData' => $damageData]);
	}
		/**
		 * [Insert settings genaral configuration]
		 * 
		 * @param Request $req
		 */
	public function addItem(Request $req) {
      		
      	$collectionSheet=DB::table('mfn_cfg')->where('name','collectionSheet_cfg')->value('name');
	      	
      	if($collectionSheet){
		    $collectionSheetConfigurations=MfnSamityConfiguration::where('name','collectionSheet_cfg')->first();
		    $collectionSheetConfigurations->config = json_encode(array([
		        'showAutoFieldWeekly'          => $req->showAutoFieldWeekly,
		        'memberSortingOptions'         => $req->memberSortingOptions,
			    'memberStatus'                 => $req->memberStatus,
			    'mode'                         => $req->mode,
			    'reportOption'                 => $req->reportOption,
			    'collectionSheet'              => $req->collectionSheet,
			    'showAutoAmount'               => $req->showAutoAmount,
			    'showAutoField'                => $req->showAutoField,
			    'showAlwaysExtraRow'           => $req->showAlwaysExtraRow,
			    'showExtraFooter'              => $req->showExtraFooter,
			    'showHeaderAddress'            => $req->showHeaderAddress,
			    'showHolidayWeek'              => $req->showHolidayWeek,
			    'showInterestAmount'           => $req->showInterestAmount,
			    'showIrregularCollection'      => $req->showIrregularCollection,
			    'showLastScheduleDate'         => $req->showLastScheduleDate,
                'showLoanCode'                 => $req->showLoanCode,
		        'showLoanCycle'                => $req->showLoanCycle,
			    'showLoanProductName'          => $req->showLoanProductName,
			    'showLoanPurpose'              => $req->showLoanPurpose,
			    'showLoanRebate'               => $req->showLoanRebate,
			    'showMemberAdmissionDate'      => $req->showMemberAdmissionDate,
			    'showMemberAttendance'         => $req->showMemberAttendance,
			    'showMemberCode'               => $req->showMemberCode,
			    'showMobileNo'                 => $req->showMobileNo,
			    'showMonthEndDue'              => $req->showMonthEndDue,
			    'showMonthEndLoanAdvance'      => $req->showMonthEndLoanAdvance,
			    'ShowMonthEndSavingsBalance'   => $req->ShowMonthEndSavingsBalance,
			    'showNoOfInstallment'          => $req->showNoOfInstallment,
			    'showOpeningAdvance'           => $req->showOpeningAdvance,
			    'showMonthEndLoanOutstanding'  => $req->showMonthEndLoanOutstanding,
			    'showPenaltyAmount'            => $req->showPenaltyAmount,
			    'showPrimaryProduct'           => $req->showPrimaryProduct,
			    'showReportSignature'          => $req->showReportSignature,
			    'ShowSavingsAfterMatured'      => $req->ShowSavingsAfterMatured,
			    'showSavingsCode'              => $req->showSavingsCode,
			    'showSpouseName'               => $req->showSpouseName,
			    'showTotalCollection'          => $req->showTotalCollection,
			    'showSpouseHeadAs'             => $req->showSpouseHeadAs,
		    ]));
		    $collectionSheetConfigurations->save();

		}      
		else {
            $collectionSheetConfiguration=new MfnSamityConfiguration;
            $collectionSheetConfiguration->name='collectionSheet_cfg';
            $collectionSheetConfiguration->config = json_encode([
				'showAutoFieldWeekly'                => $req->showAutoFieldWeekly,
		        'memberSortingOptions'               => $req->memberSortingOptions,
			    'memberStatus'                       => $req->memberStatus,
			    'mode'                               => $req->mode,
			    'reportOption'                       => $req->reportOption,
			    'collectionSheet'                    => $req->collectionSheet,
			    'showAutoAmount'                     => $req->showAutoAmount,
			    'showAutoField'                      => $req->showAutoField,
			    'showAlwaysExtraRow'                 => $req->showAlwaysExtraRow,
			    'showExtraFooter'                    => $req->showExtraFooter,
			    'showHeaderAddress'                  => $req->showHeaderAddress,
			    'showHolidayWeek'                    => $req->showHolidayWeek,
			    'showInterestAmount'                 => $req->showInterestAmount,
			    'showIrregularCollection'            => $req->showIrregularCollection,
			    'showLastScheduleDate'               => $req->showLastScheduleDate,
                'showLoanCode'                       => $req->showLoanCode,
		        'showLoanCycle'                      => $req->showLoanCycle,
			    'showLoanProductName'                => $req->showLoanProductName,
			    'showLoanPurpose'                    => $req->showLoanPurpose,
			    'showLoanRebate'                     => $req->showLoanRebate,
			    'showMemberAdmissionDate'            => $req->showMemberAdmissionDate,
			    'showMemberAttendance'               => $req->showMemberAttendance,
			    'showMemberCode'                     => $req->showMemberCode,
			    'showMobileNo'                       => $req->showMobileNo,
			    'showMonthEndDue'                    => $req->showMonthEndDue,
			    'showMonthEndLoanAdvance'            => $req->showMonthEndLoanAdvance,
			    'ShowMonthEndSavingsBalance'         => $req->ShowMonthEndSavingsBalance,
			    'showNoOfInstallment'                => $req->showNoOfInstallment,
			    'showOpeningAdvance'                 => $req->showOpeningAdvance,
			    'showMonthEndLoanOutstanding'        => $req->showMonthEndLoanOutstanding,
			    'showPenaltyAmount'                  => $req->showPenaltyAmount,
			    'showPrimaryProduct'                 => $req->showPrimaryProduct,
			    'showReportSignature'                => $req->showReportSignature,
			    'ShowSavingsAfterMatured'            => $req->ShowSavingsAfterMatured,
			    'showSavingsCode'                    => $req->showSavingsCode,
			    'showSpouseName'                     => $req->showSpouseName,
			    'showTotalCollection'                => $req->showTotalCollection,
			    'showSpouseHeadAs'                   => $req->showSpouseHeadAs,
				]);
				$collectionSheetConfiguration->createdDate = Carbon::now();
				$collectionSheetConfiguration->save();
			}      
			$data = array(
			    'responseTitle' =>  'Success!',
			    'responseText'  =>  'Collection Sheet Configuration insert successfully.'
			);

                return response::json($data); 
        }
	}