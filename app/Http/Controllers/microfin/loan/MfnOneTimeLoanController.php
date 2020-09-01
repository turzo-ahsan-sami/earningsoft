<?php

namespace App\Http\Controllers\microfin\loan;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\microfin\loan\MfnLoan;
use App\microfin\loan\MfnProduct;
use App\microfin\loan\MfnLoanSchedule;
use App\microfin\loan\MfnLoanReschedule;
use App\microfin\loan\MfnLoanRepayPeriod;
use App\microfin\loan\MfnFees;
use App\microfin\settings\MfnLoanProductInterestRate;
use App\microfin\configuration\openingBalance\MfnloanOpeningBalance;
use Session;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Traits\GetSoftwareDate;
use App\Http\Controllers\microfin\MicroFinance;
use App\Http\Controllers\gnr\Service;
use App;
use App\Http\Controllers\microfin\MicroFin;

class MfnOneTimeLoanController extends Controller {

	protected $MicroFinance;

	use GetSoftwareDate;

	public function __construct() {

		$this->MicroFinance = New MicroFinance;

		$this->TCN = array(
			array('SL No.', 70),
			array('Loan Code', 0),
			array('Member Code', 100),
			array('Member Name', 0),
			array('Loan Amount', 0),
			array('Int. Rate', 80),
			array('Disburse Date', 0),
			array('Repay Date', 0),
			array('Auth. Status', 70),
			array('Loan Status', 0),
			array('Entry By', 0),
			array('Action', 80)
		);
	}

	public function index(Request $req) {

		$PAGE_SIZE = 20;
		$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);

		if(Auth::user()->branchId==1):
			$samity = [];
			$primaryProduct = $this->MicroFinance->getLoanProductsOption();
			$loan = MfnLoan::where('softDel', 0)->where('status', 1)->oneTimeLoan();
		else:
				/*$samity = $this->MicroFinance->getBranchWiseSamityOptions(Auth::user()->branchId);
				$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions(Auth::user()->branchId);*/

				// $branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
				$samity = $this->MicroFinance->getBranchWiseSamityOptions($branchIdArray);
				$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions($branchIdArray);
				$loan = MfnLoan::where('softDel', 0)->where('status', 1)->branchWise()->oneTimeLoan();
			endif;

			if($req->has('branchId')) {
				$loan->where('branchIdFk', $req->get('branchId'));
				$samity = $this->MicroFinance->getBranchWiseSamityOptions($req->get('branchId'));
				$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions($req->get('branchId'));
			}
			// dd($loan);

			if($req->has('samityId'))
				$loan->where('samityIdFk', $req->get('samityId'));

			if($req->has('primaryProductId'))
				$loan->where('productIdFk', $req->get('primaryProductId'));

			if($req->has('dateFrom'))
				$loan->where('disbursementDate', '>=', $this->MicroFinance->getDBDateFormat($req->get('dateFrom')));

			if($req->has('dateTo'))
				$loan->where('disbursementDate', '<=', $this->MicroFinance->getDBDateFormat($req->get('dateTo')));

			if($req->has('loanFrom'))
				$loan->where('loanAmount', '>=', $req->get('loanFrom'));

			if($req->has('loanTo'))
				$loan->where('loanAmount', '<=', $req->get('loanTo'));

			// if($req->has('noi'))
			// 	$loan->where('repaymentNo', '=', $req->get('noi'));

			if($req->has('loanCode'))
				$loan->where('loanCode', 'LIKE', '%' . $req->get('loanCode') . '%');

			if($req->has('page'))
				$SL = $req->get('page') * $PAGE_SIZE - $PAGE_SIZE;

			if($req->has('branchId') || $req->has('samityId') || $req->has('primaryProductId') || $req->has('name') || $req->has('dateFrom') || $req->has('dateTo') || $req->has('loanFrom') || $req->has('loanTo') || $req->has('noi') || $req->has('loanCode')) {
				$isSearch = 1;
			} else {

				$isSearch = 0;
			}

			if (Auth::user()->branchId==1) {
				$branchList = MicroFin::getBranchList();

			}
			else{
				$branchList = DB::table('gnr_branch')
				->whereIn('id',$branchIdArray )
				->orderBy('branchCode')
				->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
				->pluck('nameWithCode', 'id')
				->all();
			}
			// dd($loan);
			$loan = $loan->paginate($PAGE_SIZE);
			// dd($loan);

			$damageData = array(
				'TCN'               =>  $this->TCN,
				'SL' 	   		    =>  $req->has('page')?$SL:0,
				'isSearch'          =>  $isSearch,
				'branch'  		    =>  $this->MicroFinance->getAllBranchOptions(),
				'samity'		    =>  $samity,
				'primaryProduct'    =>  $primaryProduct,
				// 'oneTimeLoans'      =>  $this->MicroFinance->getActiveOneTimeLoan(),
				'oneTimeLoans'      =>  $loan,
				'branchIdArray'     => $branchIdArray,
				'branchList'    =>  $branchList,
				'dataNotAvailable'	=>	$this->MicroFinance->dataNotAvailable(),
				'MicroFinance'      =>  $this->MicroFinance
			);

			return view('microfin.loan.oneTimeLoan.viewOneTimeLoan', ['damageData' => $damageData]);
		}


		public function index_old(Request $req) {

			$PAGE_SIZE = 20;

			if(Auth::user()->branchId==1):
				$samity = [];
				$primaryProduct = $this->MicroFinance->getLoanProductsOption();
				$loan = MfnLoan::where('softDel', 0)->where('status', 1)->oneTimeLoan();
			else:
				/*$samity = $this->MicroFinance->getBranchWiseSamityOptions(Auth::user()->branchId);
				$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions(Auth::user()->branchId);*/

				$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
				$samity = $this->MicroFinance->getBranchWiseSamityOptions($branchIdArray);
				$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions($branchIdArray);
				$loan = MfnLoan::where('softDel', 0)->where('status', 1)->branchWise()->oneTimeLoan();
			endif;

			if($req->has('branchId')) {
				$loan->where('branchIdFk', $req->get('branchId'));
				$samity = $this->MicroFinance->getBranchWiseSamityOptions($req->get('branchId'));
				$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions($req->get('branchId'));
			}
			// dd($loan);

			if($req->has('samityId'))
				$loan->where('samityIdFk', $req->get('samityId'));

			if($req->has('primaryProductId'))
				$loan->where('productIdFk', $req->get('primaryProductId'));

			if($req->has('dateFrom'))
				$loan->where('disbursementDate', '>=', $this->MicroFinance->getDBDateFormat($req->get('dateFrom')));

			if($req->has('dateTo'))
				$loan->where('disbursementDate', '<=', $this->MicroFinance->getDBDateFormat($req->get('dateTo')));

			if($req->has('loanFrom'))
				$loan->where('loanAmount', '>=', $req->get('loanFrom'));

			if($req->has('loanTo'))
				$loan->where('loanAmount', '<=', $req->get('loanTo'));

			// if($req->has('noi'))
			// 	$loan->where('repaymentNo', '=', $req->get('noi'));

			if($req->has('loanCode'))
				$loan->where('loanCode', 'LIKE', '%' . $req->get('loanCode') . '%');

			if($req->has('page'))
				$SL = $req->get('page') * $PAGE_SIZE - $PAGE_SIZE;

			if($req->has('branchId') || $req->has('samityId') || $req->has('primaryProductId') || $req->has('name') || $req->has('dateFrom') || $req->has('dateTo') || $req->has('loanFrom') || $req->has('loanTo') || $req->has('noi') || $req->has('loanCode')) {
				$isSearch = 1;
			} else {

				$isSearch = 0;
			}
			// dd($loan);
			$loan = $loan->paginate($PAGE_SIZE);
			// dd($loan);

			$damageData = array(
				'TCN'               =>  $this->TCN,
				'SL' 	   		    =>  $req->has('page')?$SL:0,
				'isSearch'          =>  $isSearch,
				'branch'  		    =>  $this->MicroFinance->getAllBranchOptions(),
				'samity'		    =>  $samity,
				'primaryProduct'    =>  $primaryProduct,
				// 'oneTimeLoans'      =>  $this->MicroFinance->getActiveOneTimeLoan(),
				'oneTimeLoans'      =>  $loan,
				'dataNotAvailable'	=>	$this->MicroFinance->dataNotAvailable(),
				'MicroFinance'      =>  $this->MicroFinance
			);

			return view('microfin.loan.oneTimeLoan.viewOneTimeLoan', ['damageData' => $damageData]);
		}

		public function addOneTimeLoan() {

			$damageData = array(
				'member'  			  =>  $this->MicroFinance->getMemberOptionsForLoan(Auth::user()->branchId),
				//'disbursementDate'    =>  Carbon::today()->toDateString(),
				'disbursementDate'    =>  GetSoftwareDate::getSoftwareDate(),
				'repaymentFrequency'  =>  $this->MicroFinance->getRepaymentFrequencyOptions(),
				'loanRepayPeriod'     =>  $this->MicroFinance->getLoanRepayPeriod(),
				'paymentType'         =>  $this->MicroFinance->getPaymentType(),
				'loanPurpose'         =>  $this->MicroFinance->getLoanPurpose(),
				'boolean'  			  =>  $this->MicroFinance->getBooleanOptions()
			);

			// dd($damageData);

			return view('microfin.loan.oneTimeLoan.addOneTimeLoan', ['damageData' => $damageData]);
		}

		public function getTotalLoanInfo (Request $req) {
			// dd($req);

			$interestRateOB = MfnLoanProductInterestRate::where('loanProductId', $req->productId)
			->select('interestModeId',
				'interestRate',
				'interestCalculationMethodShortName',
				'installmentNum',
				'interestRateIndex',
				'repaymentFrequencyId'
			)
			->first();

			$insuranceAmount = DB::table('mfn_loans_product')
			->where('id', $req->productId)
			->pluck('insuranceAmount')
			->toArray();

			$interestMode = $this->MicroFinance->getInterestModeOptions();

			// $loanProductOB = MfnProduct::where('id', $req->productId)->select('maxLoanAmount', 'minLoanAmount', 'installmentNum', 'additionalFee', 'formFee', 'maxInsuranceAmount')->first();

			$loanRepayPeriodOB = MfnLoanRepayPeriod::where('id', $req->loanRepayPeriodId)->select('inMonths')->first();

			$yearCount = 0;
			if($loanRepayPeriodOB->inMonths==12)
				$yearCount = 1;
			if($loanRepayPeriodOB->inMonths==24)
				$yearCount = 2;
			if($loanRepayPeriodOB->inMonths==36)
				$yearCount = 3;

			//	INTEREST AMOUNT CALCULATION.
			$interestAmount = sprintf("%.2f", ($interestRateOB->interestRateIndex / 100) * (float)$req->loanAmount * 365 * $yearCount);
			$totalRepayAmount = (float)$req->loanAmount + $interestAmount;

			$loanProductOB = MfnProduct::where('id', $req->productId)->select('isInsuranceApplicable','insuranceAmount')->first();
			$insuranceAmount = 0;
			if ($loanProductOB->isInsuranceApplicable==1) {
				$insuranceAmount = (float)$req->loanAmount * $loanProductOB->insuranceAmount/100;
			}

			$data = array(
				'totalRepayAmount' 	=> $totalRepayAmount,
				'interestAmount'   	=> $interestAmount,
				'insuranceAmount'   => $insuranceAmount
			);

			// dd($data);

			return response::json($data);
		}

		public function updateLoanInfoProductWise (Request $req) {
			$getBranch = DB::table('mfn_member_information')
			->where('id', $req->id)
			->pluck('branchId')
			->toArray();

			$getLoanProductIds = DB::table('gnr_branch')
			->where('id', $getBranch[0])
			->pluck('loanProductId')
			->toArray();

			// dd($getLoanProductIds);

			$getDecodedProductIds = json_decode($getLoanProductIds[0]);

			$count = 0;

			foreach ($getDecodedProductIds as $key => $getDecodedProductId) {
				$productInfoName = DB::table('mfn_loans_product')
				->where([['id', $getDecodedProductId], ['productTypeId', '=', 2]])
				->pluck('name')
				->toArray();

				$productInfoId = DB::table('mfn_loans_product')
				->where([['id', $getDecodedProductId], ['productTypeId', '=', 2]])
				->pluck('id')
				->toArray();

				if (sizeof($productInfoName) > 0) {
					++$count;
					$productInfoArray[$productInfoId[0]] = [$productInfoName[0]];
				}
			}

			foreach ($productInfoArray as $key1 => $productInfoA) {
				foreach ($productInfoA as $key2 => $productInfoB) {
					$productInfo[] = $key1.':'.$productInfoB;
				}
			}
			
			// dd($getDecodedProductIds, $productInfoArray, $productInfo);
			return response()->json($productInfo);
		}

		public function updateOneTimeLoanItem (Request $req) {
			
			$pImg    = $req->image;
			$pRsImg  = $req->member_signature_image;
			$pNidImg = $req->member_nid_image;
			$gImg    = $req->guarantor_image;
			$gRsImg  = $req->guarantor_signature_image;
			$gNidImg = $req->guarantor_nid_image;

			// dd('gNidImg', $gNidImg);

			DB::beginTransaction();
			try{
				$loanOB = DB::table('mfn_loan')->where('loanCode',$req->loanCode)->select('branchIdFk','disbursementDate', 'isFromOpening')->first();
				$softDate = MicroFin::getSoftwareDateBranchWise($loanOB->branchIdFk); 
				$branchSoftwareDate = DB::table('gnr_branch')->where('id',$loanOB->branchIdFk)->select('softwareStartDate')->first();

				if ($loanOB->isFromOpening == 1) {
					if ($softDate != $branchSoftwareDate->softwareStartDate) {
						$data = array(
							'responseTitle'  =>  MicroFinance::getMessage('msgWarning'),
							'responseText'   =>  MicroFinance::getMessage('oneTimeLoanUpdateWarning'),
						);

						return response()->json($data);
					}
				}
				else {
					if ($softDate != $loanOB->disbursementDate) {
						$data = array(
							'responseTitle'  =>  MicroFinance::getMessage('msgWarning'),
							'responseText'   =>  MicroFinance::getMessage('oneTimeLoanUpdateWarning'),
						);

						return response()->json($data);
					}
				}

				$interestRateOB = MfnLoanProductInterestRate::where('loanProductId', $req->productIdFk)
				->select('interestRateIndex')
				->first();

				$interestRateIndex = (fmod($interestRateOB->interestRateIndex, 1) * 100);

				if ($req->paymentTypeIdFk == 'Cash') {
					$ledgerId = DB::table('acc_account_ledger')->where('accountTypeId', 4)->where('isGroupHead', 0)->select('id')->first();
					$ledgerId = $ledgerId->id;
					$chequeNo = 0;
					$chequeDate = '0000-00-00';
				}
				else {
					$ledgerId = $req->ledgerId;
					$chequeNo = $req->chequeNo;
					$chequeDate = date_format(date_create($req->chequeDate), 'Y-m-d');
				}

				// IMAGE AND SIGNATURE UPLOAD START
				$loanData = $req->all();
				if( $loanOB->disbursementDate > '2019-06-15'){

					if ($req->hasFile('profileImage')) {
						$profileImage = $req->file('profileImage');
						$filename = $profileImage->getClientOriginalName();
						$EXT = $profileImage->getClientOriginalExtension();
						$profileImageFileName = $this->MicroFinance->imageNameEncoder($filename, $EXT, Session::get('branchId'), $req->code);
						$req->file('profileImage')->move('uploads/images/member/profile/', $profileImageFileName);

						$checkUpdate = DB::table('mfn_member_information')
						->where('id', $req->memberIdFk)
						->update(
							[
								'profileImage'	=> $profileImageFileName
							]
						);

					}
					elseif ($pImg != '') {
						$folderPath = public_path('uploads/images/member/profile/');

						$image_parts = explode(";base64,", $pImg);
						$image_type_aux = explode("image/", $image_parts[0]);
						$image_type = $image_type_aux[1];

						$image_base64 = base64_decode($image_parts[1]);

						$picFiileName = uniqid() . '.png';

						$file = $folderPath . $picFiileName;
						file_put_contents($file, $image_base64);

						$checkQuery = DB::table('mfn_member_information')
						->where('id', $req->memberIdFk)
						->update(
							[
								'profileImage'	=> $picFiileName
							]
						);
					}

					if ($req->hasFile('memberRegularSignatureImage')) {
						$profileImage = $req->file('memberRegularSignatureImage');
						$filename = $profileImage->getClientOriginalName();
						$EXT = $profileImage->getClientOriginalExtension();
						$profileImageFileName = $this->MicroFinance->imageNameEncoder($filename, $EXT, Session::get('branchId'), $req->code);
						$req->file('memberRegularSignatureImage')->move('uploads/images/member/regular-signature/', $profileImageFileName);

						$checkUpdate = DB::table('mfn_member_information')
						->where('id', $req->memberIdFk)
						->update(
							[
								'regularSignatureImage'	=> $profileImageFileName
							]
						);

					}
					elseif ($pRsImg != '') {
						$folderPath = public_path('uploads/images/member/regular-signature/');

						$image_parts = explode(";base64,", $pRsImg);
						$image_type_aux = explode("image/", $image_parts[0]);
						$image_type = $image_type_aux[1];

						$image_base64 = base64_decode($image_parts[1]);

						$picFiileName = uniqid() . '.png';

						$file = $folderPath . $picFiileName;
						file_put_contents($file, $image_base64);

						$checkQuery = DB::table('mfn_member_information')
						->where('id', $req->memberIdFk)
						->update(
							[
								'regularSignatureImage'	=> $picFiileName
							]
						);
					}

					if ($req->hasFile('memberNationaIdImage')) {
						$profileImage = $req->file('memberNationaIdImage');
						$filename = $profileImage->getClientOriginalName();
						$EXT = $profileImage->getClientOriginalExtension();
						$profileImageFileName = $this->MicroFinance->imageNameEncoder($filename, $EXT, Session::get('branchId'), $req->code);
						$req->file('memberNationaIdImage')->move('uploads/images/member/nid-signature/', $profileImageFileName);

						$checkUpdate = DB::table('mfn_member_information')
						->where('id', $req->memberIdFk)
						->update(
							[
								'nIDSignatureImage'	=> $profileImageFileName
							]
						);

					}
					elseif ($pNidImg != '') {
						$folderPath = public_path('uploads/images/member/nid-signature/');

						$image_parts = explode(";base64,", $pNidImg);
						$image_type_aux = explode("image/", $image_parts[0]);
						$image_type = $image_type_aux[1];

						$image_base64 = base64_decode($image_parts[1]);

						$picFiileName = uniqid() . '.png';

						$file = $folderPath . $picFiileName;
						file_put_contents($file, $image_base64);

						$checkQuery = DB::table('mfn_member_information')
						->where('id', $req->memberIdFk)
						->update(
							[
								'nIDSignatureImage'	=> $picFiileName
							]
						);
					}

				// ASSIGN ALL THE DATA FOR MASS ASSIGNMENT

					if ($req->hasFile('guarantorSignatureImage')) {
						$regularSignature = $req->file('guarantorSignatureImage');
						$filename = $regularSignature->getClientOriginalName();
						$EXT = $regularSignature->getClientOriginalExtension();
						$guarantorSignatureImageFileName = $this->MicroFinance->imageNameEncoder($filename, $EXT, Session::get('branchId'), $req->code);
						$req->file('guarantorSignatureImage')->move('uploads/images/member/regular-signature/', $guarantorSignatureImageFileName);
						$loanData['guarantorSignatureImage'] = $guarantorSignatureImageFileName;
					}
					elseif ($gRsImg != '') {
						$folderPath = public_path('uploads/images/member/regular-signature/');

						$image_parts = explode(";base64,", $gRsImg);
						$image_type_aux = explode("image/", $image_parts[0]);
						$image_type = $image_type_aux[1];

						$image_base64 = base64_decode($image_parts[1]);

						$picFiileName = uniqid() . '.png';

						$file = $folderPath . $picFiileName;
						file_put_contents($file, $image_base64);
						$loanData['guarantorSignatureImage'] = $picFiileName;
					}
					else {
						$loanData['guarantorSignatureImage'] = DB::table('mfn_loan')
						->where('id', $req->loanId)
						->value('guarantorSignatureImage');

					}

					if ($req->hasFile('guarantorNidImage')) {
						$nIDSignature = $req->file('guarantorNidImage');
						$filename = $nIDSignature->getClientOriginalName();
						$EXT = $nIDSignature->getClientOriginalExtension();
						$guarantorNidImageFileName = $this->MicroFinance->imageNameEncoder($filename, $EXT, Session::get('branchId'), $req->code);
						$req->file('guarantorNidImage')->move('uploads/images/member/nid-signature/', $guarantorNidImageFileName);
						$loanData['guarantorNidImage'] = $guarantorNidImageFileName;
					}
					elseif ($gNidImg != '') {
						$folderPath = public_path('uploads/images/member/nid-signature/');

						$image_parts = explode(";base64,", $gNidImg);
						$image_type_aux = explode("image/", $image_parts[0]);
						$image_type = $image_type_aux[1];

						$image_base64 = base64_decode($image_parts[1]);

						$picFiileName = uniqid() . '.png';

						$file = $folderPath . $picFiileName;
						file_put_contents($file, $image_base64);
						$loanData['guarantorNidImage'] = $picFiileName;
					}
					else {
						$loanData['guarantorNidImage'] = DB::table('mfn_loan')
						->where('id', $req->loanId)
						->value('guarantorNidImage');

					}

					if ($req->hasFile('guarantorImage')) {
						$guarantorImage = $req->file('guarantorImage');
						$filename = $guarantorImage->getClientOriginalName();
						$EXT = $guarantorImage->getClientOriginalExtension();
						$guarantorImageFileName = $this->MicroFinance->imageNameEncoder($filename, $EXT, Session::get('branchId'), $req->code);
						$req->file('guarantorImage')->move('uploads/images/member/guarantor/', $guarantorImageFileName);
						$loanData['guarantorImage'] = $guarantorImageFileName;
					}
					elseif ($gImg != '') {
						$folderPath = public_path('uploads/images/member/guarantor/');

						$image_parts = explode(";base64,", $gImg);
						$image_type_aux = explode("image/", $image_parts[0]);
						$image_type = $image_type_aux[1];

						$image_base64 = base64_decode($image_parts[1]);

						$picFiileName = uniqid() . '.png';

						$file = $folderPath . $picFiileName;
						file_put_contents($file, $image_base64);
						$loanData['guarantorImage'] = $picFiileName;
					}
					else {
						$loanData['guarantorImage'] = DB::table('mfn_loan')
						->where('id', $req->loanId)
						->value('guarantorImage');
					}
				}
				// IMAGE AND SIGNATURE UPLOAD END

				$updateLoanTable = DB::table('mfn_loan')
				->where('id', $req->loanId);

				$previousdata = $updateLoanTable;
				$updateLoanTable->update(
					[
						'productIdFk'       => $loanData['productIdFk'],
						'loanAmount'        => $loanData['loanAmount'],
						'interestMode'      => $loanData['interestMode'],
						'interestRate'      => $loanData['interestRate'],
						'interestRateIndex' => $interestRateIndex,
						'totalRepayAmount'  => $loanData['totalRepayAmount'],
						'interestAmount'    => $loanData['interestAmount'],
						'installmentAmount' => $loanData['installmentAmount'],
						'paymentTypeIdFk'   => $loanData['paymentTypeIdFk'],
						'ledgerId'          => $ledgerId,
						'chequeNo'          => $chequeNo,
						'chequeDate'        => $chequeDate,
						'additionalFee'     => $loanData['additionalFee'],
						'guarantorImage'	=> $loanData['guarantorImage'],
						'guarantorSignatureImage' => $loanData['guarantorSignatureImage'],
						'guarantorNidImage' => $loanData['guarantorNidImage'],
						'updatedDate'       => date("Y-m-d")
					]
				);

				$logArray = array(
					'moduleId'  => 6,
					'controllerName'  => 'MfnOneTimeLoanController',
					'tableName'  => 'mfn_loan',
					'operation'  => 'update',
					'previousData'  => $previousdata,
					'primaryIds'  => [$previousdata->id]
				);
				Service::createLog($logArray);

				$updateScheduleTable = DB::table('mfn_loan_schedule')
				->where('loanIdFk', $req->loanId)
				->update(
					[
						'installmentAmount'       => $loanData['totalRepayAmount'],
						'actualInstallmentAmount' => $loanData['totalRepayAmount'],
						'principalAmount'         => $loanData['loanAmount'],
						'interestAmount'          => $loanData['interestAmount'],
						'updatedDate'             => date("Y-m-d")
					]
				);

				DB::commit();

				if ($updateLoanTable == 1) {
					$data = array(
						'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
						'responseText'   =>  MicroFinance::getMessage('oneTimeLoanUpdateSuccess'),
					);

					return response()->json($data);
				}
				else {
					$data = array(
						'responseTitle'  =>  MicroFinance::getMessage('msgWarning'),
						'responseText'   =>  MicroFinance::getMessage('oneTimeLoanUpdateWarning'),
					);

					return response()->json($data);
				}
			}
			catch(\Exception $e){
				DB::rollback();
				$data = array(
					'responseTitle'  =>  'Warning!',
					'responseText'   =>  'Something went wrong. Please try again.'
				);
				return response::json($data);
			}

		}

		public function updateOneTimeLoan($regularLoanId) {
			// dd($regularLoanId);

			//	GET ALL THE DETAILS OF THE LOAN.
			$regularLoanDetails = $this->MicroFinance->getLoanDetails($regularLoanId);

			//	GET THE DETAILS OF THE PRODUCT OF THE LOAN.
			$loanProductOB = MfnProduct::where('id', $regularLoanDetails->productIdFk)
			->select('avgLoanAmount',
				'maxLoanAmount',
				'minLoanAmount',
				'installmentNum',
				'eligibleRepaymentFrequencyId',
				'principalAmountOfLoan'
			)
			->first();

			//	GET LOAN REPAY PERIOD OPTIONS.
			$loanRepayPeriodOption = $this->MicroFinance->getLoanRepayPeriod();

			//	LOAN REPAY PERIOD.
			$loanRepayPeriod = [];

			//	LOAN REPAY PERIOD FOR WEEKLY REPAYMENT FREQUENCY.
			foreach($loanRepayPeriodOption as $key => $val):
				if($key>=$regularLoanDetails->loanRepayPeriodIdFk && $key<=$regularLoanDetails->loanRepayPeriodIdFk):
					$loanRepayPeriod[$key] = $val;
					break;
				endif;
			endforeach;

			//	LOAN REPAY PERIOD FOR MONTHLY REPAYMENT FREQUENCY.
			foreach($loanRepayPeriodOption as $key => $val):
				if($key>=$regularLoanDetails->loanRepayPeriodIdFk && $key<=$regularLoanDetails->loanRepayPeriodIdFk):
					$loanRepayPeriod[$key] = $val;
					break;
				endif;
			endforeach;

			//	MANUFACTURING NO OF REPAYMENT OPTIONS.
			$repaymentNo = explode(',', $loanProductOB->installmentNum);

			$repaymentNoOptions = [];

			foreach($repaymentNo as $key => $val):
				$repaymentNoOptions[$val] = $val;
			endforeach;

			if($regularLoanDetails->repaymentFrequencyIdFk==1):
				unset($repaymentNoOptions[12]);
				unset($repaymentNoOptions[24]);
				unset($repaymentNoOptions[36]);
			else:
				unset($repaymentNoOptions[46]);
			endif;

			//	FOR REPAYMENT FREQUENCY OPTION.
			$repaymentFrequencyOption = $this->MicroFinance->getRepaymentFrequencyOptions();

			

			//	LOCK LOAN AMOUNT FIELD FOR UPDATE.
			$lockField = $this->MicroFinance->getRegularLoanCollectionStatus($regularLoanId);
			$rescheduleExists = $this->MicroFinance->getRegularLoanRescheduleExists($regularLoanId);

			$curLoanOB = MfnLoan::where('id', $regularLoanId)->select('memberIdFk', 'repaymentFrequencyIdFk', 'loanRepayPeriodIdFk', 'repaymentNo','branchIdFk')->first();

			// dd($loanRepayPeriodOption, $regularLoanId, $regularLoanDetails->repaymentFrequencyIdFk, $loanRepayPeriod, $curLoanOB->loanRepayPeriodIdFk, $regularLoanDetails);

			if($lockField==1 || $rescheduleExists==1):
				//	GET CURRENT LOAN INFORMATION.
				$repaymentFrequencyOption = $this->MicroFinance->getArrayCutOff($repaymentFrequencyOption, (int) $curLoanOB->repaymentFrequencyIdFk);
				$loanRepayPeriod = $this->MicroFinance->getArrayCutOff($loanRepayPeriod, (int) $curLoanOB->loanRepayPeriodIdFk);
				$repaymentNoOptions = $this->MicroFinance->getArrayCutOff($repaymentNoOptions, (int) $curLoanOB->repaymentNo);
			endif;

			$loanBranchId = $curLoanOB->branchIdFk;
			$loanBranchProjectId = (int) DB::table('gnr_branch')->where('id',$loanBranchId)->value('projectId');

			// MEMBER & GUARANTOR IMAGE STARTS

			$memberImage = DB::table('mfn_member_information')
			->where('id', $regularLoanDetails->memberIdFk)
			->value('profileImage');

			if ($memberImage != '') {
				$memberImage = 'uploads/images/member/profile/'.ltrim($memberImage, "/");
			}

			$memberRegularSignatureImage = DB::table('mfn_member_information')
			->where('id', $regularLoanDetails->memberIdFk)
			->value('regularSignatureImage');

			if ($memberRegularSignatureImage != '') {
				$memberRegularSignatureImage = 'uploads/images/member/regular-signature/'.ltrim($memberRegularSignatureImage, "/");
			}

			$memberNidImage = DB::table('mfn_member_information')
			->where('id', $regularLoanDetails->memberIdFk)
			->value('nIDSignatureImage');

			if ($memberNidImage != '') {
				$memberNidImage = 'uploads/images/member/nid-signature/'.ltrim($memberNidImage, "/");
			}

			$guarantorImage = DB::table('mfn_loan')
			->where('id', $regularLoanId)
			->value('guarantorImage');

			if ($guarantorImage != '') {
				$guarantorImage = 'uploads/images/member/guarantor/'.ltrim($guarantorImage, "/");
			}

			$guarantorSignatureImage = DB::table('mfn_loan')
			->where('id', $regularLoanId)
			->value('guarantorSignatureImage');

			if ($guarantorSignatureImage != '') {
				$guarantorSignatureImage = 'uploads/images/member/regular-signature/'.ltrim($guarantorSignatureImage, "/");
			}

			$guarantorNidImage = DB::table('mfn_loan')
			->where('id', $regularLoanId)
			->value('guarantorNidImage');

			if ($guarantorNidImage != '') {
				$guarantorNidImage = 'uploads/images/member/nid-signature/'.ltrim($guarantorNidImage, "/");
			}

			// MEMBER & GUARANTOR IMAGE ENDS

			$damageData = array(
				'memberImage'				=>  $memberImage,
				'memberRegularSignatureImage'=> $memberRegularSignatureImage,
				'memberNidImage'			=>  $memberNidImage,
				'guarantorImage'			=>  $guarantorImage,
				'guarantorSignatureImage'	=>  $guarantorSignatureImage,
				'guarantorNidImage'			=>  $guarantorNidImage,
				'loanId'					=>  $regularLoanId,
				'regularLoanDetails'  		=>  $regularLoanDetails,
				'member'  			  		=>  $this->MicroFinance->getMemberOptionsSingle($regularLoanDetails->memberIdFk),
				'product'			  		=>  $this->MicroFinance->getLoanProductsOptionSingle($regularLoanDetails->productIdFk),
				'productDetails'			=>  $loanProductOB,
				'repaymentFrequencyOption'  =>  $repaymentFrequencyOption,
				'loanRepayPeriod'  			=>  $loanRepayPeriod,
				'repaymentNo'   	 		=>  $repaymentNoOptions,
				'paymentType'         		=>  $this->MicroFinance->getPaymentType(),
				'loanPurpose'         		=>  $this->MicroFinance->getLoanPurpose(),
				'boolean'  			  		=>  $this->MicroFinance->getBooleanOptions(),
				'lockField'       		    =>  $lockField==0?0:1,
				'rescheduleExists'          =>  $rescheduleExists==0?0:1,
				'loanBranchId'				=> $loanBranchId,
				'loanBranchProjectId'		=> $loanBranchProjectId
			);

			// dd($damageData);

			return view('microfin.loan.oneTimeLoan.editOneTimeLoan', $damageData);
		}

		public function loadLoanProductList(Request $req) {

			/*$checkRegularLoanCompleted = MfnLoan::where('memberIdFk', $req->memberId)->loanCompleted()->count();
			$newRegularLoan = MfnLoan::where('memberIdFk', $req->memberId)->count();

			if($checkRegularLoanCompleted==1 || $newRegularLoan==0)
				$loanProduct = $this->MicroFinance->getActiveLoanOthersProductOptions();
			else
			$loanProduct = array();*/

			//	GET MEMBER ADMISSION DATE.
			$memberOB = DB::table('mfn_member_information')->where('id', $req->memberId)->select('admissionDate', 'profileImage', 'nIDSignatureImage')->first();
			
			$data = array(
				//'loanProduct'        =>  $loanProduct,
				'loanProduct'  		   =>  $this->MicroFinance->getActiveLoanOthersProductOptions($req->memberId),
				'memberAdmissionDate'  =>  $memberOB->admissionDate,
				'profileImage'		   =>  $memberOB->profileImage,
				'nIDSignatureImage'	   =>  $memberOB->nIDSignatureImage,
				'softwareDate' 		   =>  GetSoftwareDate::getSoftwareDate()
			);

			return response::json($data);
		}

		public function loadOneTimeLoanSupportData(Request $req) {

			//	START FOR GENERATE LOAN REPAY DATE.
			$getLoanRepayPeriod = DB::table('mfn_loan_repay_period')->where('id', $req->loanRepayPeriodId)->select('inMonths')->first();

			//	GET SAMITY DAY ID OF THE SAMITY OF THE MEMBER.
			$samityDayIdOB = DB::table('mfn_member_information')
			->join('mfn_samity', 'mfn_member_information.samityId', '=', 'mfn_samity.id')
			->where('mfn_member_information.id', $req->memberId)
			->select('mfn_samity.samityDayId AS samityDayId')
			->first();

			$dt = Carbon::parse($req->disbursementDate);
			$loanRepayDate = $dt->addMonths($getLoanRepayPeriod->inMonths)->toDateString();
			//	END FOR GENERATE LOAN REPAY DATE.

			//	IF LOAN REPAY DATE DOESN'T MATCHES TO SAMITY DAY, THEN SET LOAN REPAY DATE TO NEXT SAMITY DAY.
			if(date('l', strtotime($loanRepayDate))!=$this->MicroFinance->getSamityDayNameValue($samityDayIdOB->samityDayId))
				$loanRepayDate = $this->MicroFinance->getNextSamityDate($dt->toDateString(), $samityDayIdOB->samityDayId);

			$getMemberCode = $this->MicroFinance->getSingleValueForId($table='mfn_member_information', $req->memberId, 'code');
			$regularLoanShortName = $this->MicroFinance->getSingleValueForId($table='mfn_loans_product', $req->id, 'shortName');
			$regularLoanSLNum = $this->MicroFinance->getRegularLoanSLNum($req->memberId, $req->id);

			$loanCode = $regularLoanShortName . '.' . $getMemberCode . '.' . $regularLoanSLNum;

			$interestRateOB = MfnLoanProductInterestRate::where('loanProductId', $req->id)
			->select('interestModeId',
				'interestRate',
				'interestCalculationMethodShortName',
				'installmentNum',
				'interestRateIndex',
				'repaymentFrequencyId'
			)
			->first();

			$interestMode = $this->MicroFinance->getInterestModeOptions();

			$loanProductOB = MfnProduct::where('id', $req->id)->select('maxLoanAmount', 'minLoanAmount', 'installmentNum', 'additionalFee', 'formFee','isInsuranceApplicable','insuranceAmount','maxInsuranceAmount')->first();

			$loanRepayPeriodOB = MfnLoanRepayPeriod::where('id', $req->loanRepayPeriodId)->select('inMonths')->first();

			$yearCount = 0;
			if($loanRepayPeriodOB->inMonths==12)
				$yearCount = 1;
			if($loanRepayPeriodOB->inMonths==24)
				$yearCount = 2;
			if($loanRepayPeriodOB->inMonths==36)
				$yearCount = 3;

			//	INTEREST AMOUNT CALCULATION.
			$interestAmount = sprintf("%.2f", ($interestRateOB->interestRateIndex / 100) * $loanProductOB->maxLoanAmount * 365 * $yearCount);
			$totalRepayAmount = $loanProductOB->maxLoanAmount + $interestAmount;

			$interestAmountByMin = sprintf("%.2f", ($interestRateOB->interestRateIndex / 100) * $loanProductOB->minLoanAmount * 365 * $yearCount);
			$totalRepayAmountByMin = $loanProductOB->minLoanAmount + $interestAmount;

			$insuranceAmount = 0;
			if ($loanProductOB->isInsuranceApplicable==1) {
				$insuranceAmount = $loanProductOB->maxLoanAmount * $loanProductOB->insuranceAmount/100;
			}

			$data = array(
				'loanRepayDate'  			 =>  Carbon::parse($loanRepayDate)->format('d-m-Y'),
				'loanProduct' 	 			 =>  $this->MicroFinance->getActiveLoanOthersProductOptions(),
				'repaymentFrequency'   		 =>  $interestRateOB->repaymentFrequencyId,
				'loanCode'    	 			 =>  $loanCode,
				'loanCycle'   	 			 =>  $regularLoanSLNum,
				'loanAmount'   	 			 =>  sprintf("%.2f", $loanProductOB->maxLoanAmount),
				'maxLoanAmount'              =>  sprintf("%.2f", $loanProductOB->maxLoanAmount),
				'minLoanAmount'              =>  sprintf("%.2f", $loanProductOB->minLoanAmount),
				// 'insuranceAmount'   	 	 =>  $loanProductOB->maxInsuranceAmount,
				'insuranceAmount'   	 	 =>  $insuranceAmount,
				'interestMode'   			 =>  $interestMode[$interestRateOB->interestModeId] . ' (Daily)',
				'interestCalculationMethod'  =>  $interestRateOB->interestCalculationMethodShortName,
				'interestRate'  			 =>  $interestRateOB->interestRate,
				'installmentNum'  			 =>  $interestRateOB->installmentNum,
				'totalRepayAmount'  		 =>  sprintf("%.2f", $totalRepayAmount),
				'interestAmount'  			 =>  $interestAmount,
				'installmentAmount'  		 =>  sprintf("%.2f", $totalRepayAmount),
				'additionalFee'  			 =>  $loanProductOB->additionalFee,
				'formFee'  			 		 =>  $loanProductOB->formFee,
			);

			return response::json($data);
		}

		public function loadLoanRepayDate(Request $req) {

			$getLoanRepayPeriod = DB::table('mfn_loan_repay_period')->where('id', $req->loanRepayPeriodId)->select('inMonths')->first();

			//	GET SAMITY DAY ID OF THE SAMITY OF THE MEMBER.
			$samityDayIdOB = DB::table('mfn_member_information')
			->join('mfn_samity', 'mfn_member_information.samityId', '=', 'mfn_samity.id')
			->where('mfn_member_information.id', $req->memberId)
			->select('mfn_samity.samityDayId AS samityDayId')
			->first();

			$dt = Carbon::parse($req->disbursementDate);
			$loanRepayDate = $dt->addMonths($getLoanRepayPeriod->inMonths)->toDateString();

			//	IF LOAN REPAY DATE DOESN'T MATCHES TO SAMITY DAY, THEN SET LOAN REPAY DATE TO NEXT SAMITY DAY.
			if(date('l', strtotime($loanRepayDate))!=$this->MicroFinance->getSamityDayNameValue($samityDayIdOB->samityDayId))
				$loanRepayDate = $this->MicroFinance->getNextSamityDate($dt->toDateString(), $samityDayIdOB->samityDayId);

			$data = array(
				'loanRepayDate'  =>  $loanRepayDate
			);

			return response::json($data);
		}

		public function loadOneLoanSupportDataRepayPeriodWise(Request $req) {

			//	START FOR GENERATE LOAN REPAY DATE.
			$getLoanRepayPeriod = DB::table('mfn_loan_repay_period')->where('id', $req->loanRepayPeriodId)->select('inMonths')->first();

			//	GET SAMITY DAY ID OF THE SAMITY OF THE MEMBER.
			$samityDayIdOB = DB::table('mfn_member_information')
			->join('mfn_samity', 'mfn_member_information.samityId', '=', 'mfn_samity.id')
			->where('mfn_member_information.id', $req->memberId)
			->select('mfn_samity.samityDayId AS samityDayId')
			->first();

			$dt = Carbon::parse($req->disbursementDate);
			$loanRepayDate = $dt->addMonths($getLoanRepayPeriod->inMonths)->toDateString();
			//	END FOR GENERATE LOAN REPAY DATE.

			//	IF LOAN REPAY DATE DOESN'T MATCHES TO SAMITY DAY, THEN SET LOAN REPAY DATE TO NEXT SAMITY DAY.
			if(date('l', strtotime($loanRepayDate))!=$this->MicroFinance->getSamityDayNameValue($samityDayIdOB->samityDayId))
				$loanRepayDate = $this->MicroFinance->getNextSamityDate($dt->toDateString(), $samityDayIdOB->samityDayId);

			$getMemberCode = $this->MicroFinance->getSingleValueForId($table='mfn_member_information', $req->memberId, 'code');
			$regularLoanShortName = $this->MicroFinance->getSingleValueForId($table='mfn_loans_product', $req->id, 'shortName');
			$regularLoanSLNum = $this->MicroFinance->getRegularLoanSLNum($req->memberId, $req->id);

			$loanCode = $regularLoanShortName . '.' . $getMemberCode . '.' . $regularLoanSLNum;

			$interestRateOB = MfnLoanProductInterestRate::where('loanProductId', $req->id)
			->select('interestModeId',
				'interestRate',
				'interestCalculationMethodShortName',
				'installmentNum',
				'interestRateIndex',
				'repaymentFrequencyId'
			)
			->first();

			$interestMode = $this->MicroFinance->getInterestModeOptions();

			$loanProductOB = MfnProduct::where('id', $req->id)->select('maxLoanAmount', 'installmentNum', 'additionalFee', 'formFee', 'maxInsuranceAmount')->first();

			$loanRepayPeriodOB = MfnLoanRepayPeriod::where('id', $req->loanRepayPeriodId)->select('inMonths')->first();

			$yearCount = 0;
			if($loanRepayPeriodOB->inMonths==12)
				$yearCount = 1;
			if($loanRepayPeriodOB->inMonths==24)
				$yearCount = 2;
			if($loanRepayPeriodOB->inMonths==36)
				$yearCount = 3;

			//	INTEREST AMOUNT CALCULATION.
			$interestAmount = sprintf("%.2f", ($interestRateOB->interestRateIndex / 100) * $loanProductOB->maxLoanAmount * 365 * $yearCount);
			$totalRepayAmount = $loanProductOB->maxLoanAmount + $interestAmount;

			$data = array(
				'loanRepayDate'  			 =>  Carbon::parse($loanRepayDate)->format('d-m-Y'),
				'loanProduct' 	 			 =>  $this->MicroFinance->getActiveLoanOthersProductOptions(),
				'repaymentFrequency'   		 =>  $interestRateOB->repaymentFrequencyId,
				'loanCode'    	 			 =>  $loanCode,
				'loanCycle'   	 			 =>  $regularLoanSLNum,
				'loanAmount'   	 			 =>  sprintf("%.2f", $loanProductOB->maxLoanAmount),
				'insuranceAmount'   	 	 =>  $loanProductOB->maxInsuranceAmount,
				'interestMode'   			 =>  $interestMode[$interestRateOB->interestModeId] . ' (Daily)',
				'interestCalculationMethod'  =>  $interestRateOB->interestCalculationMethodShortName,
				'interestRate'  			 =>  $interestRateOB->interestRate,
				'installmentNum'  			 =>  $interestRateOB->installmentNum,
				'totalRepayAmount'  		 =>  sprintf("%.2f", $totalRepayAmount),
				'interestAmount'  			 =>  $interestAmount,
				'installmentAmount'  		 =>  sprintf("%.2f", $totalRepayAmount),
				'additionalFee'  			 =>  $loanProductOB->additionalFee,
				'formFee'  			 		 =>  $loanProductOB->formFee,
				'repaymentNo'  			 	 =>  [$loanProductOB->installmentNum]
			);

			return response::json($data);
		}

		public function addItem(Request $req) {
			// dd($req->profileImage, $req);

			$pImg    = $req->image;
			$pRsImg  = $req->member_signature_image;
			$pNidImg = $req->member_nid_image;
			$gImg    = $req->guarantor_image;
			$gRsImg  = $req->guarantor_signature_image;
			$gNidImg = $req->guarantor_nid_image;

			// dd('gNidImg', $gNidImg);

			// if loan code exits than return an error message
			$isLoanCodeExits = (int) DB::table('mfn_loan')->where('softDel',0)->where('loanCode',$req->loanCode)->value('id');

			if ($isLoanCodeExits>0) {
				$data = array(
					'responseTitle' =>  'Warning!',
					'responseText'  =>  'Loan Code alreary exits!'
				);

				return response::json($data);
			}
			
			$getBranch = DB::table('mfn_member_information')
			->where('id', $req->memberIdFk)
			->pluck('branchId')
			->toArray();

			$softDate = MicroFin::getSoftwareDateBranchWise($getBranch[0]);

			if ($softDate == $req->disbursementDate) {
				$rules = array(
					'memberIdFk'  				 =>  'required',
					'disbursementDate'  		 =>  'required',
					'productIdFk' 	 			 =>  'required',
					// 'loanCode'		   			 =>  'required|unique:mfn_loan,loanCode',
					'loanRepayPeriodIdFk'  	  	 =>  'required',
					'firstRepayDate'  		  	 =>  'required',
					'loanAmount'  			  	 =>  'required',
					'repaymentNo'  			  	 =>  'required',
					'loanSubPurposeIdFk'  		 =>  'required',
					'interestMode'  		     =>  'required',
					'interestCalculationMethod'  =>  'required',
					'interestRate'  			 =>  'required',
					'totalRepayAmount'  		 =>  'required',
					'installmentAmount'  		 =>  'required',
					'FEFullTimeMale'			 =>  'required',
					'FEFullTimeFemale' 			 =>  'required',
					'OFEFullTimeMale'			 =>  'required',
					'OFEFullTimeFemale'			 =>  'required',
					'FEPartTimeMale'			 =>  'required',
					'FEPartTimeFemale'			 =>  'required',
					'OFEPartTimeMale'			 =>  'required',
					'OFEPartTimeFemale'			 =>  'required',
					'FEFullTimeMaleWage'		 =>  'required',
					'FEFullTimeFemaleWage'		 =>  'required',
					'OFEPartTimeMaleWage'		 =>  'required',
					'OFEPartTimeFemaleWage'		 =>  'required',
					'businessName'				 =>   'required',
					'businessLocation'			 =>   'required',
					'businessType'				 =>   'required'
				);

				$attributesNames = array(
					'loanCode'  =>	'loan code',
					'FEFullTimeMale'			 =>  'family employment full time male',
					'FEFullTimeFemale' 			 =>  'family employment full time female',
					'OFEFullTimeMale'			 =>  'outside employment full time male',
					'OFEFullTimeFemale'			 =>  'outside employment full time female',
					'FEPartTimeMale'			 =>  'family employment part time male',
					'FEPartTimeFemale'			 =>  'family employment part time female',
					'OFEPartTimeMale'			 =>  'outside family employment part time male',
					'OFEPartTimeFemale'			 =>  'outside family employment part time female',
					'FEFullTimeMaleWage'		 =>  'family employment full time wages based male',
					'FEFullTimeFemaleWage'		 =>  'family employment full time wages based female',
					'OFEPartTimeMaleWage'		 =>  'outside employment full time wages based male',
					'OFEPartTimeFemaleWage'		 =>  'outside employment full time wages based female',
					'businessName'				 =>   'business name',
					'businessLocation'			 =>   'business location',
					'businessType'				 =>   'business type'
				);

				$validator = Validator::make(Input::all(), $rules);
				$validator->setAttributeNames($attributesNames);

				if($validator->fails())
					return response::json(array('errors' => $validator->getMessageBag()->toArray()));
				else {
					$now = Carbon::now();
					$req->request->add(['createdDate' => $now]);
					$req->request->add(['entryBy' => Auth::user()->emp_id_fk]);

					//	FOR ONE TIME LOAN loanTypeId = 2
					$req->request->add(['loanTypeId' => 2]);

					//	WHEN PAYMENT TYPE IS CASH, THEN SET CASH IN HAND LEDGER ID.
					if($req->paymentTypeIdFk=='Cash'):
						$cashLedgerId = DB::table('acc_account_ledger')->where('accountTypeId', 4)->where('isGroupHead', 0)->select('id')->first();
						$req->request->add(['ledgerId' => $cashLedgerId->id]);
					endif;

					//	GET SAMITY ID OF THE MEMBER.
					$samityIdOB = DB::table('mfn_member_information')->where('id', $req->memberIdFk)->select('samityId','primaryProductId')->first();
					$req->request->add(['samityIdFk' => $samityIdOB->samityId]);
					$req->request->add(['primaryProductIdFk' => $samityIdOB->primaryProductId]);

					//  GET INTEREST RATE INDEX.
					$interestRateIndexOB = MfnLoanProductInterestRate::where('loanProductId', $req->productIdFk)->select('interestRateIndex')->first();
					// dd($interestRateIndexOB);
					$req->request->add(['interestRateIndex' => $interestRateIndexOB->interestRateIndex]);
					$req->request->add(['branchIdFk' => Auth::user()->branchId]);
					$req->merge(['disbursementDate' => Carbon::parse($req->disbursementDate)->format('Y-m-d')]);
					$req->merge(['firstRepayDate' => Carbon::parse($req->firstRepayDate)->format('Y-m-d')]);
					if ($req->chequeDate!='') {
						$req->merge(['chequeDate' => Carbon::parse($req->chequeDate)->format('Y-m-d')]);
					}
					// dd($req->interestCalculationMethodId, $req);

					// IMAGE AND SIGNATURE UPLOAD START
					
					if ($req->hasFile('profileImage')) {
						$profileImage = $req->file('profileImage');
						$filename = $profileImage->getClientOriginalName();
						$EXT = $profileImage->getClientOriginalExtension();
						$profileImageFileName = $this->MicroFinance->imageNameEncoder($filename, $EXT, Session::get('branchId'), $req->code);
						$req->file('profileImage')->move('uploads/images/member/profile/', $profileImageFileName);
						
						$checkUpdate = DB::table('mfn_member_information')
						->where('id', $req->memberIdFk)
						->update(
							[
								'profileImage'	=> $profileImageFileName
							]
						);
					}
					elseif ($pImg != '') {
						$folderPath = public_path('uploads/images/member/profile/');

						$image_parts = explode(";base64,", $pImg);
						$image_type_aux = explode("image/", $image_parts[0]);
						$image_type = $image_type_aux[1];

						$image_base64 = base64_decode($image_parts[1]);

						$picFiileName = uniqid() . '.png';
						
						$file = $folderPath . $picFiileName;
						file_put_contents($file, $image_base64);
						
						$checkQuery = DB::table('mfn_member_information')
						->where('id', $req->memberIdFk)
						->update(
							[
								'profileImage'	=> $picFiileName
							]
						);
					}

					if ($req->hasFile('memberRegularSignatureImage')) {
						$profileImage = $req->file('memberRegularSignatureImage');
						$filename = $profileImage->getClientOriginalName();
						$EXT = $profileImage->getClientOriginalExtension();
						$profileImageFileName = $this->MicroFinance->imageNameEncoder($filename, $EXT, Session::get('branchId'), $req->code);
						$req->file('memberRegularSignatureImage')->move('uploads/images/member/regular-signature/', $profileImageFileName);
						
						$checkUpdate = DB::table('mfn_member_information')
						->where('id', $req->memberIdFk)
						->update(
							[
								'regularSignatureImage'	=> $profileImageFileName
							]
						);
					}
					elseif ($pRsImg != '') {
						$folderPath = public_path('uploads/images/member/regular-signature/');

						$image_parts = explode(";base64,", $pRsImg);
						$image_type_aux = explode("image/", $image_parts[0]);
						$image_type = $image_type_aux[1];

						$image_base64 = base64_decode($image_parts[1]);

						$picFiileName = uniqid() . '.png';
						
						$file = $folderPath . $picFiileName;
						file_put_contents($file, $image_base64);
						
						$checkQuery = DB::table('mfn_member_information')
						->where('id', $req->memberIdFk)
						->update(
							[
								'regularSignatureImage'	=> $picFiileName
							]
						);
					}

					if ($req->hasFile('memberNidImage')) {
						$profileImage = $req->file('memberNidImage');
						$filename = $profileImage->getClientOriginalName();
						$EXT = $profileImage->getClientOriginalExtension();
						$profileImageFileName = $this->MicroFinance->imageNameEncoder($filename, $EXT, Session::get('branchId'), $req->code);
						$req->file('memberNidImage')->move('uploads/images/member/nid-signature/', $profileImageFileName);
						
						$checkUpdate = DB::table('mfn_member_information')
						->where('id', $req->memberIdFk)
						->update(
							[
								'nIDSignatureImage'	=> $profileImageFileName
							]
						);
					}
					elseif ($pNidImg != '') {
						$folderPath = public_path('uploads/images/member/nid-signature/');

						$image_parts = explode(";base64,", $pNidImg);
						$image_type_aux = explode("image/", $image_parts[0]);
						$image_type = $image_type_aux[1];

						$image_base64 = base64_decode($image_parts[1]);

						$picFiileName = uniqid() . '.png';
						
						$file = $folderPath . $picFiileName;
						file_put_contents($file, $image_base64);
						
						$checkQuery = DB::table('mfn_member_information')
						->where('id', $req->memberIdFk)
						->update(
							[
								'nIDSignatureImage'	=> $picFiileName
							]
						);
					}

					// ASSIGN ALL THE DATA FOR MASS ASSIGNMENT
					$loanData = $req->all();

					if ($req->hasFile('guarantorRegularSignatureImage')) {
						$regularSignature = $req->file('guarantorRegularSignatureImage');
						$filename = $regularSignature->getClientOriginalName();
						$EXT = $regularSignature->getClientOriginalExtension();
						$guarantorSignatureImageFileName = $this->MicroFinance->imageNameEncoder($filename, $EXT, Session::get('branchId'), $req->code);
						$req->file('guarantorRegularSignatureImage')->move('uploads/images/member/regular-signature/', $guarantorSignatureImageFileName);
						$loanData['guarantorSignatureImage'] = $guarantorSignatureImageFileName;
					}
					elseif ($gRsImg != '') {
						$folderPath = public_path('uploads/images/member/regular-signature/');

						$image_parts = explode(";base64,", $gRsImg);
						$image_type_aux = explode("image/", $image_parts[0]);
						$image_type = $image_type_aux[1];

						$image_base64 = base64_decode($image_parts[1]);

						$picFiileName = uniqid() . '.png';
						
						$file = $folderPath . $picFiileName;
						file_put_contents($file, $image_base64);
						$loanData['guarantorSignatureImage'] = $picFiileName;
					}

					if ($req->hasFile('guarantorNidImage')) {
						$nIDSignature = $req->file('guarantorNidImage');
						$filename = $nIDSignature->getClientOriginalName();
						$EXT = $nIDSignature->getClientOriginalExtension();
						$guarantorNidImageFileName = $this->MicroFinance->imageNameEncoder($filename, $EXT, Session::get('branchId'), $req->code);
						$req->file('guarantorNidImage')->move('uploads/images/member/nid-signature/', $guarantorNidImageFileName);
						$loanData['guarantorNidImage'] = $guarantorNidImageFileName;
					}
					elseif ($gNidImg != '') {
						$folderPath = public_path('uploads/images/member/nid-signature/');

						$image_parts = explode(";base64,", $gNidImg);
						$image_type_aux = explode("image/", $image_parts[0]);
						$image_type = $image_type_aux[1];

						$image_base64 = base64_decode($image_parts[1]);

						$picFiileName = uniqid() . '.png';
						
						$file = $folderPath . $picFiileName;
						file_put_contents($file, $image_base64);
						$loanData['guarantorNidImage'] = $picFiileName;
					}
					
					if ($req->hasFile('guarantorImage')) {
						$guarantorImage = $req->file('guarantorImage');
						$filename = $guarantorImage->getClientOriginalName();
						$EXT = $guarantorImage->getClientOriginalExtension();
						$guarantorImageFileName = $this->MicroFinance->imageNameEncoder($filename, $EXT, Session::get('branchId'), $req->code);
						$req->file('guarantorImage')->move('uploads/images/member/guarantor/', $guarantorImageFileName);
						$loanData['guarantorImage'] = $guarantorImageFileName;
					}
					elseif ($gImg != '') {
						$folderPath = public_path('uploads/images/member/guarantor/');

						$image_parts = explode(";base64,", $gImg);
						$image_type_aux = explode("image/", $image_parts[0]);
						$image_type = $image_type_aux[1];

						$image_base64 = base64_decode($image_parts[1]);

						$picFiileName = uniqid() . '.png';
						
						$file = $folderPath . $picFiileName;
						file_put_contents($file, $image_base64);
						$loanData['guarantorImage'] = $picFiileName;
					}

					// IMAGE AND SIGNATURE UPLOAD END

					// dd($loanData);

					$create = MfnLoan::create($loanData);
					$logArray = array(
						'moduleId'  => 6,
						'controllerName'  => 'MfnOneTimeLoanController',
						'tableName'  => 'mfn_loan',
						'operation'  => 'insert',
						'primaryIds'  => [DB::table('mfn_loan')->max('id')]
					);
					Service::createLog($logArray);

					/*shutting the code
					$repaymentFrequencyWiseRepayDate = [
						'1'	 =>  7,
						'2'  =>  28
					];

					$scheduleDateArr = [];
					for($i=0;$i<$req->repaymentNo;$i++):
						$dayDiff = ($repaymentFrequencyWiseRepayDate[1] * $i) . 'days';
						$date=date_create($req->firstRepayDate);
						date_add($date,date_interval_create_from_date_string($dayDiff));
						$scheduleDateArr[] = date_format($date,"Y-m-d");
					endfor;
					//dd($scheduleDateArr);


					$interestRateOB = MfnLoanProductInterestRate::where('loanProductId', $req->productIdFk)
																->select('interestRateIndex')
																->first();

					$interestRateIndex = (fmod($interestRateOB->interestRateIndex, 1) * 100);
					$principalAmount = $req->installmentAmount - $req->interestAmount;

					//	GET LOAN ID.
					$loanIdOB = DB::table('mfn_loan')->where([['loanCode', $req->loanCode], ['softDel', '=', 0]])->select('id')->first();

					//	GENERATE LOAN SCHEDULE.
					for($i=0;$i<$req->repaymentNo;$i++):
						$req->request->add(['loanIdFk' => $loanIdOB->id]);
						$req->request->add(['installmentSl' => $i+1]);
						$req->request->add(['interestAmount' => sprintf("%.2f", $req->interestAmount)]);
						$req->request->add(['principalAmount' => sprintf("%.2f", $principalAmount)]);
						$req->request->add(['scheduleDate' => $scheduleDateArr[$i]]);
						$create = MfnLoanSchedule::create($req->all());
					endfor;

					shutting the code*/

					$data = array(
						'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
						'responseText'   =>  MicroFinance::getMessage('oneTimeLoanCreateSuccess'),
					);

					return response::json($data);
				}
			}
			else {
				$data = array(
					'responseTitle' =>  'Warning!',
					'responseText'  =>  'Transaction date is not matching with software date! Please check day end again!'
				);

				return response::json($data);
			}

			
		}

		public function detailsOneTimeLoan($regularLoanId) {

			$loanDetailsTCN = [
				'loanId'					   =>  'Loan ID:',
				'product'					   =>  'Product:',
				'memberName'				   =>  'Member Name:',
				'loanCycle'					   =>  'Loan Cycle:',
				'fatherSpouseName'			   =>  'Father\'s/Spouse Name:',
				'paymentMode'				   =>  'Mode of payment:',
				'age'						   =>  'Age:',
				'mobileNo'					   =>  'Mobile No:',
				'samity'					   =>  'Samity:',
				'transferDate'				   =>  'Transfer In Date:',
				'disbursementDate'			   =>  'Disbursement Date:',
				'dueAmount'				       =>  'Due Amount:',
				'firstRepayDate'			   =>  'First Repay Date:',
				'advanceAmount'				   =>  'Advance Amount:',
				'interestRate'				   =>  'Interest Rate:',
				'recoveryAmount'			   =>  'Recovery Amount:',
				'extraInstallmentAmount'	   =>  'Extra Installment Amount:',
				'openingLoanOutstanding'	   =>  'Opening Loan Outstanding:',
				'currentStatus'		  		   =>  'Current Status:',
				'rebate'				  	   =>  'Rebate:',
				'repaymentFrequency'		   =>  'Repayment Frequency:',
				'loanOutstanding'			   =>  'Loan Outstanding:',
				'interestMode'				   =>  'Mode of interest:',
				'loanPurpose'				   =>  'Loan Purpose:',
				'loanAmount'				   =>  'Loan Amount:',
				'loanSubPurpose'			   =>  'Loan Sub Purpose:',
				'interestAmount'			   =>  'Interest Amount:',
				'guarantorNameFirst'		   =>  'Guarantor\'s Name #1:',
				'totalRepayAmount'	  		   =>  'Total Repay Amount: ',
				'guarantorRelationshipFirst'   =>  'Guarantor\'s Relationship #1:',
				'installmentNum'  			   =>  'Number of Installment:',
				'guarantorAddressFirst'		   =>  'Guarantor\'s Address #1:',
				'loanPeriodInMonth'			   =>  'Loan Period in Month:',
				'guarantorNameSecond'		   =>  'Guarantor\'s Name #2:',
				'loanApplicationNo'			   =>  'Loan Application No:',
				'guarantorRelationshipSecond'  =>  'Guarantor\'s Relationship #2:',
				'insuranceGuarantorAmount'	   =>  'Insurance/Guarantor\'s Amount:',
				'guarantorAddressSecond'	   =>  'Guarantor\'s Address #2:',
				'loanClosingDate'			   =>  'Loan Closing Date:',
				'transferOutDate'			   =>  'Transfer Out Date:',
				'installmentAmount'			   =>  'Installment Amount:',
				'folioNumber'				   =>  'Folio Number:',
				'additionalFee'				   =>  'Additional Fee:',
				'loanFormFee'				   =>  'Loan Form Fee:',
				'payment'				       =>  'Payment:',
				'employment'				   =>  'Employment:',
			];

			$loanScheduleTCN = array(
				array('Date.', 70),
				array('Installment Amount', 0),
				array('Actual Installment Amount', 100),
				array('Extra Installment Amount', 0),
				array('Principal Amount', 0),
				array('Interest Amount', 0),
				array('Transaction Amount', 80),
				array('Status', 80)
			);

			$regularLoanDetails = $this->MicroFinance->getLoanDetails($regularLoanId);
			// if ($regularLoanDetails->loanSubPurposeIdFk != 0) {
			// 	# code...
			// }
			// else {
			// 	$regularLoanDetails->loanSubPurposeIdFk = 1;
			// }
			
			$loanPurposeOB = DB::table('mfn_loans_sub_purpose')->where('id', $regularLoanDetails->loanSubPurposeIdFk)->select('purposeIdFK')->first();
			$samityInfoOB = $this->MicroFinance->getMultipleValueForId($table='mfn_samity', $regularLoanDetails->samityIdFk, ['name', 'code', 'samityDayId', 'fixedDate']);

			// dd($loanPurposeOB, $regularLoanDetails, $samityInfoOB,  $regularLoanDetails->loanSubPurposeIdFk, $regularLoanId);

			// MEMBER & GUARANTOR IMAGE STARTS

			$memberImage = DB::table('mfn_member_information')
			->where('id', $regularLoanDetails->memberIdFk)
			->value('profileImage');

			if ($memberImage != '') {
				$memberImage = 'uploads/images/member/profile/'.ltrim($memberImage, "/");
			}

			$memberNidImage = DB::table('mfn_member_information')
			->where('id', $regularLoanDetails->memberIdFk)
			->value('nIDSignatureImage');

			if ($memberNidImage != '') {
				$memberNidImage = 'uploads/images/member/nid-signature/'.ltrim($memberNidImage, "/");
			}

			// dd($memberNidImage);

			$guarantorImage = DB::table('mfn_loan')
			->where('id', $regularLoanId)
			->value('guarantorImage');

			if ($guarantorImage != '') {
				$guarantorImage = 'uploads/images/member/guarantor/'.ltrim($guarantorImage, "/");
			}

			$guarantorSignatureImage = DB::table('mfn_loan')
			->where('id', $regularLoanId)
			->value('guarantorSignatureImage');

			if ($guarantorSignatureImage != '') {
				$guarantorSignatureImage = 'uploads/images/member/regular-signature/'.ltrim($guarantorSignatureImage, "/");
			}

			$guarantorNidImage = DB::table('mfn_loan')
			->where('id', $regularLoanId)
			->value('guarantorNidImage');

			if ($guarantorNidImage != '') {
				$guarantorNidImage = 'uploads/images/member/nid-signature/'.ltrim($guarantorNidImage, "/");
			}

			// MEMBER & GUARANTOR IMAGE ENDS

			if ($loanPurposeOB != null) {
				$damageData = array(
					'memberImage'				=>  $memberImage,
					'memberNidImage'			=>  $memberNidImage,
					'guarantorImage'			=>  $guarantorImage,
					'guarantorSignatureImage'	=>  $guarantorSignatureImage,
					'guarantorNidImage'			=>  $guarantorNidImage,
					'loanDetailsTCN'      =>  $loanDetailsTCN,
					'loanScheduleTCN'     =>  $loanScheduleTCN,
					'regularLoanDetails'  =>  $regularLoanDetails,
					'regularLoanDetail'   =>  array(
						'loanCode'					=>  $regularLoanDetails->loanCode,
						'loanCycle'					=>  $regularLoanDetails->loanCycle,
						'paymentTypeIdFk'			=>  $regularLoanDetails->paymentTypeIdFk,
						'disbursementDate'			=>  $regularLoanDetails->disbursementDate,
						'firstRepayDate'			=>  $regularLoanDetails->firstRepayDate,
						'loanApplicationNo'			=>  $regularLoanDetails->loanApplicationNo,
						'interestRate'			    =>  $regularLoanDetails->interestRate,
						'interestRateIndex'			=>  $regularLoanDetails->interestRateIndex,
						'interestCalculationMethod'	=>  $regularLoanDetails->interestCalculationMethod,
						'extraInstallmentAmount'	=>  $regularLoanDetails->extraInstallmentAmount,
						'totalRepayAmount'			=>  $regularLoanDetails->totalRepayAmount,
						'interestMode'				=>  $regularLoanDetails->interestMode,
						'loanAmount'				=>  $regularLoanDetails->loanAmount,
						'interestAmount'			=>  $regularLoanDetails->interestAmount,
						'repaymentNo'				=>  $regularLoanDetails->repaymentNo,
						'installmentAmount'			=>  $regularLoanDetails->installmentAmount,
						'actualInstallmentAmount'	=>  $regularLoanDetails->actualInstallmentAmount,
						'extraInstallmentAmount'	=>  $regularLoanDetails->extraInstallmentAmount,
						'folioNum'					=>  $regularLoanDetails->folioNum,
						'additionalFee'				=>  $regularLoanDetails->additionalFee,
						'loanFormFee'				=>  $regularLoanDetails->loanFormFee,
						'firstGuarantorName'		=>  $regularLoanDetails->firstGuarantorName,
						'firstGuarantorRelation'	=>  $regularLoanDetails->firstGuarantorRelation,
						'firstGuarantorAddress'		=>  $regularLoanDetails->firstGuarantorAddress,
						'secondGuarantorName'		=>  $regularLoanDetails->secondGuarantorName,
						'secondGuarantorRelation'	=>  $regularLoanDetails->secondGuarantorRelation,
						'secondGuarantorAddress'	=>  $regularLoanDetails->secondGuarantorAddress,
						'isSelfEmployment'			=>  $regularLoanDetails->isSelfEmployment,
						'isLoanCompleted'			=>  $regularLoanDetails->isLoanCompleted,
						'productIdFk'	 			=>  $this->MicroFinance->getNameValueForId($table='mfn_loans_product', $regularLoanDetails->productIdFk),
						'memberInfoOB'			    =>  $this->MicroFinance->getMultipleValueForId($table='mfn_member_information', $regularLoanDetails->memberIdFk, ['name', 'code', 'age', 'spouseFatherSonName', 'mobileNo']),
						'samityName'			    =>  $samityInfoOB->name,
						'samityCode'			    =>  $samityInfoOB->code,
						'samityDay'				    =>  $this->MicroFinance->getSamityDayName($samityInfoOB->samityDayId, $samityInfoOB->fixedDate),
						//'repaymentFrequencyIdFk'	=>  $repaymentFrequencyIdFk = $this->MicroFinance->getNameValueForId($table='mfn_repayment_frequency', $regularLoanDetails->repaymentFrequencyIdFk),
						'loanRepayPeriodIdFk'		=>  $this->MicroFinance->getSingleValueForId($table='mfn_loan_repay_period', $regularLoanDetails->loanRepayPeriodIdFk, 'inMonths'),
						'loanPurpose'				=>  $this->MicroFinance->getNameValueForId($table='mfn_loans_purpose', $loanPurposeOB->purposeIdFK),
						'loanSubPurposeIdFk'		=>  $this->MicroFinance->getNameValueForId($table='mfn_loans_sub_purpose', $regularLoanDetails->loanSubPurposeIdFk),
					),
					/*'loanSchedules'       =>  $this->MicroFinance->getLoanSchedule($regularLoanDetails->id, $regularLoanDetails->loanTypeId),*/
					'loanReSchedules'         =>  $this->MicroFinance->getLoanReSchedule($regularLoanDetails->id),

					'loanSchedules'         =>  $this->MicroFinance->generateLoanSchedule([0=>$regularLoanDetails->id])[$regularLoanDetails->id],
					'MicroFinance'        =>  $this->MicroFinance
				);
				//dd($damageData['loanSchedules']);
}
else {
	$damageData = array(
		'memberImage'				=>  $memberImage,
		'guarantorImage'			=>  $guarantorImage,
		'guarantorSignatureImage'	=>  $guarantorSignatureImage,
		'guarantorNidImage'			=>  $guarantorNidImage,
		'loanDetailsTCN'      =>  $loanDetailsTCN,
		'loanScheduleTCN'     =>  $loanScheduleTCN,
		'regularLoanDetails'  =>  $regularLoanDetails,
		'regularLoanDetail'   =>  array(
			'loanCode'					=>  $regularLoanDetails->loanCode,
			'loanCycle'					=>  $regularLoanDetails->loanCycle,
			'paymentTypeIdFk'			=>  $regularLoanDetails->paymentTypeIdFk,
			'disbursementDate'			=>  $regularLoanDetails->disbursementDate,
			'firstRepayDate'			=>  $regularLoanDetails->firstRepayDate,
			'loanApplicationNo'			=>  $regularLoanDetails->loanApplicationNo,
			'interestRate'			    =>  $regularLoanDetails->interestRate,
			'interestRateIndex'			=>  $regularLoanDetails->interestRateIndex,
			'interestCalculationMethod'	=>  $regularLoanDetails->interestCalculationMethod,
			'extraInstallmentAmount'	=>  $regularLoanDetails->extraInstallmentAmount,
			'totalRepayAmount'			=>  $regularLoanDetails->totalRepayAmount,
			'interestMode'				=>  $regularLoanDetails->interestMode,
			'loanAmount'				=>  $regularLoanDetails->loanAmount,
			'interestAmount'			=>  $regularLoanDetails->interestAmount,
			'repaymentNo'				=>  $regularLoanDetails->repaymentNo,
			'installmentAmount'			=>  $regularLoanDetails->installmentAmount,
			'actualInstallmentAmount'	=>  $regularLoanDetails->actualInstallmentAmount,
			'extraInstallmentAmount'	=>  $regularLoanDetails->extraInstallmentAmount,
			'folioNum'					=>  $regularLoanDetails->folioNum,
			'additionalFee'				=>  $regularLoanDetails->additionalFee,
			'loanFormFee'				=>  $regularLoanDetails->loanFormFee,
			'firstGuarantorName'		=>  $regularLoanDetails->firstGuarantorName,
			'firstGuarantorRelation'	=>  $regularLoanDetails->firstGuarantorRelation,
			'firstGuarantorAddress'		=>  $regularLoanDetails->firstGuarantorAddress,
			'secondGuarantorName'		=>  $regularLoanDetails->secondGuarantorName,
			'secondGuarantorRelation'	=>  $regularLoanDetails->secondGuarantorRelation,
			'secondGuarantorAddress'	=>  $regularLoanDetails->secondGuarantorAddress,
			'isSelfEmployment'			=>  $regularLoanDetails->isSelfEmployment,
			'isLoanCompleted'			=>  $regularLoanDetails->isLoanCompleted,
			'productIdFk'	 			=>  $this->MicroFinance->getNameValueForId($table='mfn_loans_product', $regularLoanDetails->productIdFk),
			'memberInfoOB'			    =>  $this->MicroFinance->getMultipleValueForId($table='mfn_member_information', $regularLoanDetails->memberIdFk, ['name', 'code', 'age', 'spouseFatherSonName', 'mobileNo']),
			'samityName'			    =>  $samityInfoOB->name,
			'samityCode'			    =>  $samityInfoOB->code,
			'samityDay'				    =>  $this->MicroFinance->getSamityDayName($samityInfoOB->samityDayId, $samityInfoOB->fixedDate),
						//'repaymentFrequencyIdFk'	=>  $repaymentFrequencyIdFk = $this->MicroFinance->getNameValueForId($table='mfn_repayment_frequency', $regularLoanDetails->repaymentFrequencyIdFk),
			'loanRepayPeriodIdFk'		=>  $this->MicroFinance->getSingleValueForId($table='mfn_loan_repay_period', $regularLoanDetails->loanRepayPeriodIdFk, 'inMonths'),
						// 'loanPurpose'				=>  $this->MicroFinance->getNameValueForId($table='mfn_loans_purpose', $loanPurposeOB->purposeIdFK),
						// 'loanSubPurposeIdFk'		=>  $this->MicroFinance->getNameValueForId($table='mfn_loans_sub_purpose', $regularLoanDetails->loanSubPurposeIdFk),
			'loanPurpose'               => '',
			'loanSubPurposeIdFk'        => '',
		),
		'loanSchedules'       =>  $this->MicroFinance->getLoanSchedule($regularLoanDetails->id, $regularLoanDetails->loanTypeId),
		'MicroFinance'        =>  $this->MicroFinance
	);
}


return view('microfin.loan.oneTimeLoan.detailsOneTimeLoan', ['damageData' => $damageData]);
}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: DELETE ONE TIME LOAN CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function deleteItem(Request $req) {
			// $branchId = DB::table('mfn_loan')->where('id',$req->id)->select('branchIdFk')->first();
			// $softDate = MicroFin::getSoftwareDateBranchWise($branchId->branchIdFk); 
			// $disbursementDate = DB::table('mfn_loan')->where('id',$req->id)->select('disbursementDate')->first();
			DB::beginTransaction();
			try{	
				$loanOB = DB::table('mfn_loan')->where('id',$req->id)->select('branchIdFk','disbursementDate', 'isFromOpening')->first();
				$softDate = MicroFin::getSoftwareDateBranchWise($loanOB->branchIdFk); 
				$branchSoftwareDate = DB::table('gnr_branch')->where('id',$loanOB->branchIdFk)->select('softwareStartDate')->first();

				if ($loanOB->isFromOpening == 1) {
					if ($softDate != $branchSoftwareDate->softwareStartDate) {
						$data = array(
							'responseTitle'  =>  MicroFinance::getMessage('msgWarning'),
							'responseText'   =>  MicroFinance::getMessage('regularLoanDelFailedOpening'),
						);

						return response()->json($data);
					}
				}
				else {
					if ($softDate != $loanOB->disbursementDate) {
						$data = array(
							'responseTitle'  =>  MicroFinance::getMessage('msgWarning'),
							'responseText'   =>  MicroFinance::getMessage('regularLoanDelFaileddisbursed'),
						);

						return response()->json($data);
					}
				}

				// dd($branchId, $softDate, $disbursementDate->disbursementDate);
				// if ($softDate == $disbursementDate->disbursementDate) {

					// MfnLoan::find($req->id)->delete();
					// MfnLoanSchedule::where('loanIdFk', $req->id)->delete();
					// MfnFees::where('loanIdFk', $req->id)->delete();
					// MfnLoanReschedule::where('loanIdFk', $req->id)->delete();
					// MfnloanOpeningBalance::where('loanIdFk', $req->id)->delete();

				$loanCollectionExists = $this->MicroFinance->getRegularLoanCollectionStatus($req->id);
				$rescheduleExists = $this->MicroFinance->getRegularLoanRescheduleExists($req->id);

					// CHECK IF ANY COLLECTION EXIST OR NOT (WITHOUT SOFT DELETE)
				$collectionCheck = DB::table('mfn_loan_collection')
				->where([['loanIdFk', $req->id], ['softDel', '=', 0], ['amount', '>', 0]])
				->count();

				if($collectionCheck == 0)
					$lockDelete = 1;
				else
					$lockDelete = 0;

					// dd($req->id);

				if($lockDelete==1):
						// MfnLoan::find($req->id)->delete(); 
					$loanSoftDelete = DB::table('mfn_loan')
					->where('id', $req->id);
					$previousdata = $loanSoftDelete;
					$loanSoftDelete ->update(
						[
							'status' => 0, 
							'softDel' => 1
						]
					);
					$logArray = array(
						'moduleId'  => 6,
						'controllerName'  => 'MfnOneTimeLoanController',
						'tableName'  => 'mfn_loan',
						'operation'  => 'delete',
						'previousData'  => $previousdata,
						'primaryIds'  => [$previousdata->id]
					);
					Service::createLog($logArray);
						// MfnLoanSchedule::where('loanIdFk', $req->id)->delete();
					$scheduleSoftDelete = DB::table('mfn_loan_schedule')
					->where('loanIdFk', $req->id)
					->update(
						[
							'status' => 0, 
							'softDel' => 1
						]
					);

						// MfnFees::where('loanIdFk', $req->id)->delete();
					$feesSoftDelete = DB::table('mfn_fees')
					->where('loanIdFk', $req->id)
					->update(
						[
							'status' => 0, 
							'softDel' => 1
						]
					);

						// MfnLoanReschedule::where('loanIdFk', $req->id)->delete();
					$reScheduleSoftDelete = DB::table('mfn_loan_reschedule')
					->where('loanIdFk', $req->id)
					->update(
						[
							'status' => 0, 
							'softDel' => 1
						]
					);

						// MfnloanOpeningBalance::where('loanIdFk', $req->id)->delete();
					$openingnBalanceSoftDelete = DB::table('mfn_opening_balance_loan')
					->where('loanIdFk', $req->id)
					->update(
						[
							'status' => 0, 
							'softDel' => 1
						]
					);

				endif;
				DB::commit();
				$data = array(
					'responseTitle' => $lockDelete==1?MicroFinance::getMessage('msgSuccess'):MicroFinance::getMessage('msgWarning'),
					'responseText'  => $lockDelete==1?MicroFinance::getMessage('regularLoanDelSuccess'):MicroFinance::getMessage('regularLoanDelFailed'),
				);

				return response()->json($data);
				// }
				// else {
				// 	$data = array(
				//                 'responseTitle' =>  'Warning!',
				//                 'responseText'  =>  'Transaction date is not matching with software date! Please check day end again!'
				//             );

				//     return response::json($data);
				// }
				
			}
			catch(\Exception $e){
				DB::rollback();
				$data = array(
					'responseTitle'  =>  'Warning!',
					'responseText'   =>  'Something went wrong. Please try again.'
				);
				return response::json($data);
			}

		}

	}
