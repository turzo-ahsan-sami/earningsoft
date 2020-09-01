<?php

namespace App\Http\Controllers\microfin\member;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\microfin\member\MfnMemberInformation;
use App\microfin\savings\MfnSavingsAccount;
use App\microfin\samity\MfnSamity;
use Session;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use App\Traits\GetSoftwareDate;
use App\Http\Controllers\microfin\MicroFinance;
use App\Http\Controllers\gnr\Service;
use App\Http\Controllers\microfin\MicroFin;
use App;

class MfnMemberInformationController extends Controller {

	protected $MicroFinance;

	use GetSoftwareDate;

	private $TCN;

	public function __construct() {

		$this->MicroFinance = new MicroFinance;

		$this->TCN = array(
			array('SL No.', 70),
			array('Code', 100),
			array('Name', 0),
			array('Primary Product', 0),
			array('Spouse/Father/Son Name', 0),
			array('Gender', 80),
			array('Branch', 0),
			array('Samity', 0),
			array('Admission Date', 95),
			array('Status', 70),
			array('Entry By', 120),
			array('Action', 80)
		);
	}

	public function index(Request $req) {

		$PAGE_SIZE = 50;
		$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);

		if(Auth::user()->branchId==1):
			$samity = [];
			$primaryProduct = $this->MicroFinance->getLoanProductsOption();
			$members = MfnMemberInformation::active();
		else:
			$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);

			//$samity = $this->MicroFinance->getBranchWiseSamityOptions(Auth::user()->branchId);
			$samity = $this->MicroFinance->getBranchWiseSamityOptions($branchIdArray);
			//$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions(Auth::user()->branchId);
			$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions($branchIdArray);
			
			//$members = MfnMemberInformation::active()->branchWise();
			$members = MfnMemberInformation::active()->whereIn('branchId', $branchIdArray);
		endif;

		if($req->has('branchId')) {
			$members->where('branchId', $req->get('branchId'));
			$samity = $this->MicroFinance->getBranchWiseSamityOptions($req->get('branchId'));
			$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions($req->get('branchId'));
		}
		if($req->has('samityId'))
			$members->where('samityId', $req->get('samityId'));

		if($req->has('primaryProductId'))
			$members->where('primaryProductId', $req->get('primaryProductId'));

		if($req->has('keyword')) {
		    /*$members->where('name', 'LIKE', '%' . $req->get('keyword') . '%')
			->orWhere('surName', 'LIKE', '%' . $req->get('keyword') . '%')
			->orWhere('code', 'LIKE', '%' . $req->get('keyword') . '%')
			->orWhere('motherName', 'LIKE', '%' . $req->get('keyword') . '%')
			->orWhere('spouseFatherSonName', 'LIKE', '%' . $req->get('keyword') . '%')
			->orWhere('birthRegNo', 'LIKE', '%' . $req->get('keyword') . '%')
			->orWhere('nID', 'LIKE', '%' . $req->get('keyword') . '%')
			->orWhere('passportNo', 'LIKE', '%' . $req->get('keyword') . '%')
			->orWhere('mobileNo', 'LIKE', '%' . $req->get('keyword') . '%')
			->orWhere('curVillageWard', 'LIKE', '%' . $req->get('keyword') . '%')
			->orWhere('permVillageWard', 'LIKE', '%' . $req->get('keyword') . '%');*/

			$keyword = $req->get('keyword');

			$members->where('softDel',0)
			->where(function ($query) use ($keyword){
				$query->where('name', 'LIKE', '%' . $keyword . '%')
				->orWhere('surName', 'LIKE', '%' . $keyword . '%')
				->orWhere('code', 'LIKE', '%' . $keyword . '%')
				->orWhere('motherName', 'LIKE', '%' . $keyword . '%')
				->orWhere('spouseFatherSonName', 'LIKE', '%' . $keyword . '%')
				->orWhere('birthRegNo', 'LIKE', '%' . $keyword . '%')
				->orWhere('nID', 'LIKE', '%' . $keyword . '%')
				->orWhere('passportNo', 'LIKE', '%' . $keyword . '%')
				->orWhere('mobileNo', 'LIKE', '%' . $keyword . '%')
				->orWhere('curVillageWard', 'LIKE', '%' . $keyword . '%')
				->orWhere('permVillageWard', 'LIKE', '%' . $keyword . '%');
			});
		}

		if($req->has('dateFrom'))
			$members->where('admissionDate', '>=', $this->MicroFinance->getDBDateFormat($req->get('dateFrom')));

		if($req->has('dateTo'))
			$members->where('admissionDate', '<=', $this->MicroFinance->getDBDateFormat($req->get('dateTo')));

		if($req->has('page'))
			$SL = $req->get('page') * $PAGE_SIZE -$PAGE_SIZE;

		if($req->has('branchId') || $req->has('samityId') || $req->has('primaryProductId') || $req->has('keyword') || $req->has('dateFrom') || $req->has('dateTo')) {
			$members = $members->get();
			$isSearch = 1;
		} else {
			$members = $members->paginate($PAGE_SIZE);
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

			//$members = $members->paginate($PAGE_SIZE);
			//dd($primaryProduct);

		$damageData = array(
			'TCN' 	   			=>	$this->TCN,
			'SL' 	   			=>	$req->has('page')?$SL:0,
			'isSearch'          =>  $isSearch,
			'branch'  			=>  $this->MicroFinance->getAllBranchOptions(),
			'samity'			=>  $samity,
			'primaryProduct'  	=>  $primaryProduct,
			'members'  			=>  $members,
			'branchList'  			=>  $branchList,
			'branchIdArray'  			=>  $branchIdArray,
			'gender'   			=>  $this->MicroFinance->getGender(),
			'dataNotAvailable'	=>	$this->MicroFinance->dataNotAvailable(),
			'MicroFinance'      =>  $this->MicroFinance,
			'req'				=>  $req
		);

		return view('microfin.member.member.viewMember', ['damageData' => $damageData]);
	}





	public function index_old(Request $req) {

		$PAGE_SIZE = 50;


		if(Auth::user()->branchId==1):
			$samity = [];
			$primaryProduct = $this->MicroFinance->getLoanProductsOption();
			$members = MfnMemberInformation::active();
		else:
			$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);

			//$samity = $this->MicroFinance->getBranchWiseSamityOptions(Auth::user()->branchId);
			$samity = $this->MicroFinance->getBranchWiseSamityOptions($branchIdArray);
			//$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions(Auth::user()->branchId);
			$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions($branchIdArray);
			
			//$members = MfnMemberInformation::active()->branchWise();
			$members = MfnMemberInformation::active()->whereIn('branchId', $branchIdArray);
		endif;

		if($req->has('branchId')) {
			$members->where('branchId', $req->get('branchId'));
			$samity = $this->MicroFinance->getBranchWiseSamityOptions($req->get('branchId'));
			$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions($req->get('branchId'));
		}
		if($req->has('samityId'))
			$members->where('samityId', $req->get('samityId'));

		if($req->has('primaryProductId'))
			$members->where('primaryProductId', $req->get('primaryProductId'));

		if($req->has('keyword')) {
		    /*$members->where('name', 'LIKE', '%' . $req->get('keyword') . '%')
			->orWhere('surName', 'LIKE', '%' . $req->get('keyword') . '%')
			->orWhere('code', 'LIKE', '%' . $req->get('keyword') . '%')
			->orWhere('motherName', 'LIKE', '%' . $req->get('keyword') . '%')
			->orWhere('spouseFatherSonName', 'LIKE', '%' . $req->get('keyword') . '%')
			->orWhere('birthRegNo', 'LIKE', '%' . $req->get('keyword') . '%')
			->orWhere('nID', 'LIKE', '%' . $req->get('keyword') . '%')
			->orWhere('passportNo', 'LIKE', '%' . $req->get('keyword') . '%')
			->orWhere('mobileNo', 'LIKE', '%' . $req->get('keyword') . '%')
			->orWhere('curVillageWard', 'LIKE', '%' . $req->get('keyword') . '%')
			->orWhere('permVillageWard', 'LIKE', '%' . $req->get('keyword') . '%');*/

			$keyword = $req->get('keyword');

			$members->where('softDel',0)
			->where(function ($query) use ($keyword){
				$query->where('name', 'LIKE', '%' . $keyword . '%')
				->orWhere('surName', 'LIKE', '%' . $keyword . '%')
				->orWhere('code', 'LIKE', '%' . $keyword . '%')
				->orWhere('motherName', 'LIKE', '%' . $keyword . '%')
				->orWhere('spouseFatherSonName', 'LIKE', '%' . $keyword . '%')
				->orWhere('birthRegNo', 'LIKE', '%' . $keyword . '%')
				->orWhere('nID', 'LIKE', '%' . $keyword . '%')
				->orWhere('passportNo', 'LIKE', '%' . $keyword . '%')
				->orWhere('mobileNo', 'LIKE', '%' . $keyword . '%')
				->orWhere('curVillageWard', 'LIKE', '%' . $keyword . '%')
				->orWhere('permVillageWard', 'LIKE', '%' . $keyword . '%');
			});
		}

		if($req->has('dateFrom'))
			$members->where('admissionDate', '>=', $this->MicroFinance->getDBDateFormat($req->get('dateFrom')));

		if($req->has('dateTo'))
			$members->where('admissionDate', '<=', $this->MicroFinance->getDBDateFormat($req->get('dateTo')));

		if($req->has('page'))
			$SL = $req->get('page') * $PAGE_SIZE -$PAGE_SIZE;

		if($req->has('branchId') || $req->has('samityId') || $req->has('primaryProductId') || $req->has('keyword') || $req->has('dateFrom') || $req->has('dateTo')) {
			$members = $members->get();
			$isSearch = 1;
		} else {
			$members = $members->paginate($PAGE_SIZE);
			$isSearch = 0;
		}

			//$members = $members->paginate($PAGE_SIZE);
			//dd($primaryProduct);

		$damageData = array(
			'TCN' 	   			=>	$this->TCN,
			'SL' 	   			=>	$req->has('page')?$SL:0,
			'isSearch'          =>  $isSearch,
			'branch'  			=>  $this->MicroFinance->getAllBranchOptions(),
			'samity'			=>  $samity,
			'primaryProduct'  	=>  $primaryProduct,
			'members'  			=>  $members,
			'gender'   			=>  $this->MicroFinance->getGender(),
			'dataNotAvailable'	=>	$this->MicroFinance->dataNotAvailable(),
			'MicroFinance'      =>  $this->MicroFinance,
			'req'				=>  $req
		);

		return view('microfin.member.member.viewMember', ['damageData' => $damageData]);
	}

	public function addMember() {

		$samities = $this->MicroFinance->getSamity();
		$villages = $this->MicroFinance->getVillages(Session::get('branchId'));
		$curResidentType = $this->MicroFinance->getCurResidentType();
		$primaryProduct = $this->MicroFinance->getActiveLoanPrimaryProductOptions();
		$mandatorySavingsProduct = $this->MicroFinance->getMandatorySavingsProduct();
		$maritalStatus = $this->MicroFinance->getMaritalStatus();
		$relationship = $this->MicroFinance->getRelationship();
		$educationLevel = $this->MicroFinance->getEducationLevel();
		$country = $this->MicroFinance->getCountry();
		$designation = $this->MicroFinance->getDesignationOfReferrer();
		$memberType = $this->MicroFinance->getMemberType();
		$professions = $this->MicroFinance->getProfessionOfMember();
		$religion = $this->MicroFinance->getReligion();

		$damageData = array(
			'samities'  	   		  =>  $samities,
			'admissionDate'    	  	  =>  GetSoftwareDate::getSoftwareDate(),
			'villages'  	   		  =>  $villages,
			'curResidentType'  		  =>  $curResidentType,
			'primaryProduct'   		  =>  $primaryProduct,
			'mandatorySavingsProduct' =>  $mandatorySavingsProduct,
			'maritalStatus'    		  =>  $maritalStatus,
			'relationship'     		  =>  $relationship,
			'educationLevel'   		  =>  $educationLevel,
			'country'   	   		  =>  $country,
			'designation'			  =>  $designation,
			'memberType'			  =>  $memberType,
			'professions'			  =>  $professions,
			'religion'				  =>  $religion
		);

		return view('microfin.member.member.addMember', ['damageData' => $damageData]);
	}

		/**
		 * [Function for return Member Code]
		 *
		 * @param  Request $req [Request for Member Code]
		 * @return [type]       [An array contains Member Code]
		 */
		public function loadMemberCode(Request $req) {

			$samityOB = DB::table('mfn_samity')
			->select('code',
				'samityTypeId',
				'workingAreaId',
				'openingDate'
			)
			->where('id', $req->id)
			->first();

			$samityCode = $samityOB->code;

			//	GET GENDER TYPE OPTIONS FOR SPECIFIC SAMITY TYPE ID.
			$samityType = $this->MicroFinance->getGenderOptions($samityOB->samityTypeId);

			// START AUTO GENERATE MEMBER CODE.
			$numRows = DB::table('mfn_member_information')->select('id')->where('samityId', $req->id)->count();
			$memberOB = DB::table('mfn_member_information')->where('samityId', $req->id)->select('code')->get();

			$memberCodeArr = [];

            //	GET ALL THE MEMBER SL NUMBER FROM MEMBER CODE OF THE SAMITY.
			foreach($memberOB as $member):
				$memberCodeArr[] = (int) substr($member->code, -5, 5);
			endforeach;

			if($numRows<=0):
				$memberSL  = sprintf('%04d', 1);
				$code = $memberSL;
			else:
				$maxMemberSL = max($memberCodeArr) + 1;
				$memberSL  = sprintf('%04d', $maxMemberSL);
				$code = $memberSL;
			endif;
            // END AUTO GENERATE MEMBER CODE.

			$memberCode = $samityCode . '.' . str_pad($code, 5, 0, STR_PAD_LEFT);

			//	START MANUFACTURING WORKING AREA OF SAMITY AS PRESENT ADDRESS.
			$workingAreaOB = DB::table('gnr_working_area')
			->where('id', $samityOB->workingAreaId)
			->select('villageId',
				'unionId',
				'upazilaId',
				'districtId'
			)
			->first();

			$presentAddress = '';
			$table = ['gnr_village', 'gnr_union', 'gnr_upzilla', 'gnr_district'];
			$title = ['Vill: ', 'Union: ', 'Upzilla: ', 'District: '];

			$i = 0;
			foreach($workingAreaOB as $val):
				$presentAddress .= $title[$i];
				$presentAddress .= $this->MicroFinance->getNameValueForId($table[$i], $val);
				$presentAddress .= ', ';
				$i++;
			endforeach;
			//	END MANUFACTURING WORKING AREA OF SAMITY AS PRESENT ADDRESS.

			$data = array(
				'memberCode'         =>  $memberCode,
				'samityTypeOptions'  =>  $samityType,
				'presentAddress'	 =>	 substr($presentAddress, 0, -2),
				'samityOpeningDate'  =>  $samityOB->openingDate,
				'softwareDate'  	 =>  GetSoftwareDate::getSoftwareDate(),
			);

			return response::json($data);
		}

		public function loadMemberAge(Request $req) {

			$catchErr = 0;
			$ageErrMsg = '';

			$dob = Carbon::parse($req->dob);
			$memberAge = $dob->diffInYears(Carbon::now());
			$monthsDiff = $dob->diffInMonths(Carbon::now());
			$daysDiff = $dob->diffInDays(Carbon::now());

			$actualDiff = $dob->diff(Carbon::now());
			$daysRemainder = $actualDiff->days - ($actualDiff->y * 365);

			if($daysRemainder>180)
				$memberAge = $memberAge + 1;
			if($daysRemainder>0 && $daysRemainder<180)
				$memberAge = $memberAge;

			if($actualDiff->y * 365<18 * 365):
				$ageErrMsg = 'The member\'s age is below 18 years. Please select a member within 18-60 years range.';
				$catchErr = 1;
			endif;
			if($actualDiff->y * 365>60 * 365):
				$ageErrMsg = 'The member\'s age is above 60 years. Please select a member within 18-60 years range.';
				$catchErr = 1;
			endif;


			$data = array(
				'dob'        =>  $req->dob,
				'memberAge'  =>  $memberAge,
				'months'     =>  $monthsDiff,
				'days'       =>  $daysDiff,
				'actualDiff' =>  $actualDiff,
				'catchErr'   =>  $catchErr,
				'errMsg'     =>  $ageErrMsg
			);

			return response::json($data);
		}

		/**
		 * [Function for retrun Mandatory Savings Details]
		 *
		 * @param  Request 	$req 	[Request for Mandatory Savings Details]
		 * @return [array]       	[An array contains Mandatory Savings Details]
		 */
		public function loadMandatorySavingsDetails(Request $req) {

			$mandatorySavingsDetailsOB = DB::table('mfn_saving_product')
			->join('mfn_savings_deposit_type', 'mfn_saving_product.depositTypeIdFK', '=', 'mfn_savings_deposit_type.id')
			->select('mfn_saving_product.weeklyDepositAmount AS weeklyDepositAmount',
				'mfn_saving_product.interestRate AS interestRate',
				'mfn_savings_deposit_type.name AS depositType'
			)
			->where('mfn_saving_product.id', $req->id)
			->first();

			//	MANUFACTURING SAVINGS CODE.
			$savingsCode = $this->MicroFinance->getSavingsProductShortName($req->id) . '.' . $req->memberCode . '.' . 1;

			//	SAVINGS CODE INSERT INTO THE OBJECT.
			$mandatorySavingsDetailsOB->savingsCode = $savingsCode;

			$data = array(
				'mandatorySavingsDetailsOB'  =>   $mandatorySavingsDetailsOB
			);

			return response::json($data);
		}

		public function nIDDuplicacyCheck(Request $req) {

			$nIDExists = DB::table('mfn_member_information')->where('nID', $req->nID)->count();

			$data = array(
				'nIDexists'  =>  ($nIDExists>=1)?1:0
			);

			return response::json($data);
		}

		public function birthRegNoDuplicacyCheck(Request $req) {

			$birthRegNoExists = DB::table('mfn_member_information')->where('birthRegNo', $req->birthRegNo)->count();

			$data = array(
				'birthRegNoExists'  =>  ($birthRegNoExists>=1)?1:0
			);

			return response::json($data);
		}

		public function passportNoDuplicacyCheck(Request $req) {

			$passportNoExists = DB::table('mfn_member_information')->where('passportNo', $req->passportNo)->count();

			$data = array(
				'passportNoExists'  =>  ($passportNoExists>=1)?1:0
			);

			return response::json($data);
		}

		public function mobileNoDuplicacyCheck(Request $req) {

			$mobileNoExists = DB::table('mfn_member_information')->where('mobileNo', $req->mobileNo)->count();

			$data = array(
				'mobileNoExists'  =>  ($mobileNoExists>=1)?1:0
			);

			return response::json($data);
		}

		/**
		 * [Insert Member Information]
		 *
		 * @param Request $req
		 */
		public function addItem(Request $req) {
		// dd($req->nid_signature_image, 'OK OK', $req->image, $req->profileImage, 'OK OK', $req->image);

			$rules = array(
				'samityId'		 		=>  'required',
				'name'		 	 		=>	'required',
				'admissionDate'  		=>  'required',
				'primaryProductId'		=>  'required',
				'code'	 		 		=>	'required|unique:mfn_member_information,code',
				'gender'  		 		=>  'required',
				'age'  		 			=>  'required',
				'dob'  		 			=>  'required',
				'maritalStatus'  		=>  'required',
				'spouseFatherSonName'	=>	'required',
				'relationship'  		=>  'required',
				'nID'  		 			=>  'unique:mfn_member_information,nID',
				'birthRegNo'  		 	=>  'unique:mfn_member_information,birthRegNo',
				'passportNo'  		 	=>  'unique:mfn_member_information,passportNo',
				'mobileNo'  		    =>  'required|unique:mfn_member_information,mobileNo',
				// 'profileImage'			=>  'required',
				// 'nid_signature_image'	=>  'required'
				//'curVillageWard'  		=>  'required',
				//'permVillageWard'  		=>  'required',
				//'nomineeId'  		 	=>  'required'
			);

			$attributesNames = array(
				'name'  =>  'name'

			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()):
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else:
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
				$req->request->add(['branchId' => Session::get('branchId')]);
				$req->request->add(['entryBy' => Auth::user()->id]);

				//	CHANGE THE ADMISSION  DATE FORMAT.
				$admissionDate = date_create($req->admissionDate);
				$req->request->add(['admissionDate' => date_format($admissionDate, "Y-m-d")]);

				//	CHANGE THE DATE OF BIRTH FORMAT.
				$dob = date_create($req->dob);
				$req->request->add(['dob' => date_format($dob, "Y-m-d")]);

				//	PROFILE IMAGE STORE.
				if($req->hasFile('profileImage')):
					$profileImage = $req->file('profileImage');
					$filename = $profileImage->getClientOriginalName();
					$EXT = $profileImage->getClientOriginalExtension();
					$profileImageFileName = $this->MicroFinance->imageNameEncoder($filename, $EXT, Session::get('branchId'), $req->code);
					$req->file('profileImage')->move('uploads/images/member/profile/', $profileImageFileName);
				endif;

				// FOR IMAGE CAPTURE
				$img    = $req->image;
				$rsimg  = $req->regular_signature_image;
				$nidimg = $req->nid_signature_image;

				if ($img != "") {
					$folderPath = public_path('uploads/images/member/profile/');

					$image_parts = explode(";base64,", $img);
					$image_type_aux = explode("image/", $image_parts[0]);
					$image_type = $image_type_aux[1];

					$image_base64 = base64_decode($image_parts[1]);

					$picFiileName = uniqid() . '.png';

					$file = $folderPath . $picFiileName;
					file_put_contents($file, $image_base64);
				}

	            //	REGULAR SIGNATURE IMAGE STORE.
				if($req->hasFile('regularSignatureImage')):
					$regularSignature = $req->file('regularSignatureImage');
					$filename = $regularSignature->getClientOriginalName();
					$EXT = $regularSignature->getClientOriginalExtension();
					$regularSignatureImageFileName = $this->MicroFinance->imageNameEncoder($filename, $EXT, Session::get('branchId'), $req->code);
					$req->file('regularSignatureImage')->move('uploads/images/member/regular-signature/', $regularSignatureImageFileName);
				endif;

				if ($rsimg != '') {
					$folderPath = public_path('uploads/images/member/regular-signature/');

					$image_parts = explode(";base64,", $rsimg);
					$image_type_aux = explode("image/", $image_parts[0]);
					$image_type = $image_type_aux[1];

					$image_base64 = base64_decode($image_parts[1]);

					$regularSignatureImageFileName = uniqid() . '.png';

					$file = $folderPath . $regularSignatureImageFileName;
					file_put_contents($file, $image_base64);
				}

	            //	NATIONAL ID SIGNATURE IMAGE STORE.
				if($req->hasFile('nIDSignatureImage')):
					$nIDSignature = $req->file('nIDSignatureImage');
					$filename = $nIDSignature->getClientOriginalName();
					$EXT = $nIDSignature->getClientOriginalExtension();
					$nIDSignatureImageFileName = $this->MicroFinance->imageNameEncoder($filename, $EXT, Session::get('branchId'), $req->code);
					$req->file('nIDSignatureImage')->move('uploads/images/member/nid-signature/', $nIDSignatureImageFileName);
				endif;

				if ($nidimg != '') {
					$folderPath = public_path('uploads/images/member/nid-signature/');

					$image_parts = explode(";base64,", $nidimg);
					$image_type_aux = explode("image/", $image_parts[0]);
					$image_type = $image_type_aux[1];

					$image_base64 = base64_decode($image_parts[1]);

					$nIDSignatureImageFileName = uniqid() . '.png';

					$file = $folderPath . $nIDSignatureImageFileName;
					file_put_contents($file, $image_base64);
				}

				$create = MfnMemberInformation::create($req->all());
				$logArray = array(
					'moduleId'  => 6,
					'controllerName'  => 'MfnMemberInformationController',
					'tableName'  => 'mfn_member_information',
					'operation'  => 'insert',
					'primaryIds'  => [DB::table('mfn_member_information')->max('id')]
				);
				Service::createLog($logArray);

				//	DATA INSERT TO mfn_savings_account TABLE.
				// $req->request->add(['savingsCode' => $savingsCode]);
				$req->request->add(['accountOpeningDate' => $req->admissionDate]);
				$memberOB = MfnMemberInformation::where('code', '=', $req->code)->first();
				$req->request->add(['savingsProductIdFk' => $req->savingsProductId]);
				$req->request->add(['memberIdFk' => $memberOB->id]);
				$req->request->add(['branchIdFk' => Auth::user()->branchId]);
				$req->request->add(['samityIdFk' => $req->samityId]);

				//	GET WORKING AREA ID OF THE SAMITY.
				$samityOB = DB::table('mfn_samity')->select('workingAreaId')->where('id', $req->samityId)->first();
				$req->request->add(['workingAreaIdFk' => $samityOB->workingAreaId]);

				//	GET DEPOSIT TYPE ID OF THE SAVINGS PRODUCT.
				$savingsProductOB = DB::table('mfn_saving_product')->select('depositTypeIdFk')->where('id', $req->savingsProductId)->first();
				$req->request->add(['depositTypeIdFk' => $savingsProductOB->depositTypeIdFk]);

				$req->request->add(['autoProcessAmount' => $req->savingsAmount]);
				$req->request->add(['savingCycle' => 1]);
				$req->request->add(['initialAmount' => 0]);
				$req->request->add(['transactionType' => 'Cash' ]);
				$req->request->add(['entryByEmployeeIdFk' => Auth::user()->emp_id_fk]);

				$create = MfnSavingsAccount::create($req->all());

				//	FOR PROFILE, REGULAR SIGNATURE AND NATIONALA ID SINGNATURE IMAGE NAME SAVE.
				$fileNameSave = MfnMemberInformation::where('code', '=', $req->code)->where('softDel', '=', 0)->first();

				if($req->hasFile('profileImage')):
					$fileNameSave->profileImage = $profileImageFileName;
				endif;

				// FOR IMAGE CAPTURE
				if ($img != "") {
					$fileNameSave->profileImage = $picFiileName;
				}

				if($req->hasFile('regularSignatureImage')):
					$fileNameSave->regularSignatureImage = $regularSignatureImageFileName;
				endif;

				if ($rsimg != "") {
					$fileNameSave->regularSignatureImage = $regularSignatureImageFileName;
				}

				if($req->hasFile('nIDSignatureImage')):
					$fileNameSave->nIDSignatureImage = $nIDSignatureImageFileName;
				endif;

				if ($nidimg != "") {
					$fileNameSave->nIDSignatureImage = $nIDSignatureImageFileName;
				}

				$fileNameSave->save();

				$data = array(
					'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
					'responseText'   =>  MicroFinance::getMessage('memberCreateSuccess'),
				);

				return response::json($data);
			endif;
		}

		/**
		 * [detailsMember description]
		 * @param  [int] 	$memberId 	[Member ID]
		 * @return [array]           	[Damage Data]
		 */
		public function detailsMember($memberId) {

			$memberDetailsTCN = array(
				'primaryProduct'			=>	'Primary Product:',
				'memberCode'				=>	'Member Code:',
				'admissionDate'				=>	'Admission Date:',
				'fatherSpouseSonName'		=>	'Father\'s/Spouse/Son Name:',
				'motherName'				=>	'Mother\'s Name:',
				'dob'						=>	'Date of Birth:',
				'presentAddress'			=>	'Present Address:',
				'permanentAddress'			=>	'Permanent Address:',
				'gender'					=>	'Gender:',
				'isSKTRequired'				=>	'Is SKT Required?',
				'SKTAmount'					=>	'SKT Amount:',
				'formApplicationNo'			=>	'Form Application No:',
				'nationalID'				=>	'National ID:',
				'regNo'						=>	'Registration No:',
				'educationalQualification'	=>	'Educational Qualification:',
				'admissionFee'				=>	'Admission Fee:',
				'mobileNumber'				=>	'Mobile Number:',
				'nationality'				=>	'Nationality:',
				'fixedAssetDesc'			=>	'Fixed Asset Description:',
				'nomineeInf'				=>	'Nominee Information:',
				'referenceInf'				=>	'Reference Information:',
				'groupName'					=>	'Group Name:',
				'subGroupName'				=>	'Sub Group Name:',
				'memberType'				=>	'Member Type:',
				'noOfFamilyMember'			=>	'No of Family Member:',
				'yearlyIncome'				=>	'Yearly Income:',
				'remarks'					=>	'Remarks:',
				'birthRegNo'				=>	'Birth Registration No:',
				'landArea'					=>	'Land Area:',
				'signature'					=>	'Signature:',
				'physicalAttribute'			=>	'Physical Attribute:'
			);

			$savingsDetailsTCN = array(
				'savingsCode'	 =>	 'Savings Code:',
				'savingsType'	 =>	 'Savings Type:',
				'interestRate'	 =>	 'Interest Rate:',
				'openingDate'	 =>	 'Opening Date:',
				'openingAmount'	 =>	 'Opening Amount:',
				'savingsCycle'	 =>	 'Savings Cycle:'
			);

			$savingsProductDetailsTCN = array(
				'product'	          =>  'Product:',
				'code'	              =>  'Code:',
				'totalSavings'	      =>  'Total Savings:',
				'openingDate'	      =>  'Opening Date:',
				'autoProcessAmount'	  =>  'Auto Process Amount:',
				'nomineeInformation'  =>  'Nominee Information:'
			);

			$loanProductDetailsTCN = array(
				'product'	         =>  'Product:',
				'interestRate'	     =>  'Interest Rate:',
				'loanAmount'	     =>  'Loan Amount:',
				'interestAmount'	 =>  'Interest Amount:',
				'totalRepayAmount'   =>  'Total Repay Amount:',
				'disbursement'  	 =>  'Disbursement:',
				'firstRepay'  	  	 =>  'First Repay:',
				'loanOutstanding'  	 =>  'Loan Outstanding:',
				'recoveryAmount'  	 =>  'Recovery Amount:',
				'advanceDueAmount'   =>  'Advance/Due Amount:',
				'loanID'  	  	     =>  'Loan ID:',
				'insuranceAmount'  	 =>  'Insurance Amount:',
				'numOfInstallment'   =>  'Number of Installment:',
				'installmentAmount'  =>  'Installment Amount:',
				'advancePayment'     =>  'Advance Payment',
				'duePayment'         =>  'Due Payment',
				'onTimePayment'      =>  'On-time Payment',
			);

			$memberId = $this->MicroFinance->getNumericValueDecoder($memberId);
			$membersDetails = $this->MicroFinance->getMembersDetails($memberId);
			$mandatorySavingsDetails = $this->MicroFinance->getMandatorySavingsDetails($memberId);
			$gender = $this->MicroFinance->getGender();
			$primaryProduct = $this->MicroFinance->getActiveLoanPrimaryProductOptions();
			$educationLevel = $this->MicroFinance->getEducationLevel();
			$memberType = $this->MicroFinance->getMemberType();
			$samityName = $this->MicroFinance->getSamityName($membersDetails->samityId);
			$fieldOfficerName = $this->MicroFinance->getFieldOfficerName($membersDetails->samityId);
			$fieldOfficerCode = $this->MicroFinance->getFieldOfficerCode($membersDetails->samityId);
			$samityCode = $this->MicroFinance->getSamityCode($membersDetails->samityId);

			$curVillageName = $membersDetails->curVillageWard;
			$permVillageName = $membersDetails->permVillageWard;

			$membersDetails->samityId  = $samityName;
			$membersDetails->curVillageWard = $curVillageName;
			$membersDetails->permVillageWard = $permVillageName;

			//	GET THE TRANSFER DATE OF THE CURRENT PRIMARY PRODUCT.
			$primaryProductTransferDate = $this->MicroFinance->getLatestPrimaryProductTransferDate($memberId);

			//	GET ALL THE lOAN ACCOUNT OF A MEMBER.
			$loanAccount = $this->MicroFinance->getLoanAccountNumberPerMember($memberId);

			$j = 0;
			$loanSummary = [];
			$totalLoanAmount = 0;

			//	GET ALL THE DETAILS OF ALL THE LOAN ACCOUNT OF A MEMBER.
			foreach($loanAccount as $loanAcc):
				$loanSummary[$j]['loanProductShortName'] = $this->MicroFinance->getNameValueForId($table='mfn_loans_product', $loanAcc['productIdFk']);
				$loanSummary[$j]['loanAmount'] = $loanAcc['loanAmount'];
				$loanSummary[$j]['loanOutstanding'] = $this->MicroFinance->getRegularLoanOutstanding($loanAcc['id'], $loanAcc['totalRepayAmount']);
				$loanSummary[$j]['loanAdvance'] = $this->MicroFinance->getRegularLoanAdvance($loanAcc['id']);
				$loanSummary[$j]['loanDue'] = $this->MicroFinance->getRegularLoanDue($loanAcc['id'], $loanAcc['installmentAmount']);
				$totalLoanAmount += $loanSummary[$j]['loanAmount'];
				$j++;
			endforeach;

			//	GET ALL THE SAVINGS ACCOUNT OF A MEMBER.
			$savingsAccount = $this->MicroFinance->getSavingsAccountNumberPerMember($memberId);

			$i = 0;
			$savingsSummary = [];
			$totalSavingsAmount = 0;


			//	GET ALL THE DETAILS OF ALL THE SAVINGS ACCOUNT OF A MEMBER.
			foreach($savingsAccount as $savingsAcc):
				if(count($loanAccount)-1==$i):
					$savingsSummary[$i]['loanProductShortName'] = $loanSummary[$i]['loanProductShortName'];
					$savingsSummary[$i]['loanAmount'] = $loanSummary[$i]['loanAmount'];
					$savingsSummary[$i]['loanOutstanding'] = $loanSummary[$i]['loanOutstanding'];
					$savingsSummary[$i]['loanAdvance'] = $loanSummary[$i]['loanAdvance'];
					$savingsSummary[$i]['loanDue'] = $loanSummary[$i]['loanDue'];
				else:
					$savingsSummary[$i]['loanProductShortName'] = '';
					$savingsSummary[$i]['loanAmount'] = '';
					$savingsSummary[$i]['loanOutstanding'] = '';
					$savingsSummary[$i]['loanAdvance'] = '';
					$savingsSummary[$i]['loanDue'] = '';
				endif;

				$savingsProductShortName = $this->MicroFinance->getMultipleValueForId($table='mfn_saving_product', $savingsAcc['savingsProductIdFk'], ['shortName']);
				$savingsSummary[$i]['savingsProductShortName'] = $savingsProductShortName->shortName;
				/*$savingsSummary[$i]['balance'] = $this->MicroFinance->getSavingsDepositPerAccount($savingsAcc['id']) -
				$this->MicroFinance->getSavingsWithdrawPerAccount($savingsAcc['id']);*/

				$savingsSummary[$i]['balance'] = $this->MicroFinance->getSavingsDepositPerAccount($savingsAcc['id'], $membersDetails->primaryProductId, $primaryProductTransferDate) -
				$this->MicroFinance->getSavingsWithdrawPerAccount($savingsAcc['id'], $membersDetails->primaryProductId, $primaryProductTransferDate);
				$totalSavingsAmount +=  $savingsSummary[$i]['balance'];
				$i++;
			endforeach;


			$damageData = array(
				'memberId'  	  		   =>  $memberId,
				'memberDetailsTCN'  	   =>  $memberDetailsTCN,
				'savingsDetailsTCN' 	   =>  $savingsDetailsTCN,
				'savingsProductDetailsTCN' =>  $savingsProductDetailsTCN,
				'loanProductDetailsTCN'    =>  $loanProductDetailsTCN,
				'membersDetails'  		   =>  $membersDetails,
				'mandatorySavingsDetails'  =>  $mandatorySavingsDetails,
				'savingsProductsDetails'   =>  $this->MicroFinance->getSavingsProductsDetails($memberId),
				'loanProductsDetails'      =>  $this->MicroFinance->getLoanProductsDetails($memberId),
				'gender'  				   =>  $gender,
				'primaryProduct'  		   =>  $primaryProduct,
				'educationLevel'  		   =>  $educationLevel,
				'memberType'  			   =>  $memberType,
				'fieldOfficerName'  	   =>  $fieldOfficerName,
				'fieldOfficerCode'  	   =>  $fieldOfficerCode,
				'samityCode'  			   =>  $samityCode,
				'loanAccount'			   =>  $loanAccount,
				'loanSummary'  		   	   =>  $loanSummary,
				'totalLoanAmount'	   	   =>  $totalLoanAmount,
				'savingsAccount'  		   =>  $savingsAccount,
				'savingsSummary'  		   =>  $savingsSummary,
				'totalSavingsAmount'	   =>  $totalSavingsAmount,
				'MicroFinance'        	   =>  $this->MicroFinance
			);

						//dd($damageData);


			return view('microfin.member.member.detailsMember', ['damageData' => $damageData]);
		}

		public function mandatorySavingsDetails(Request $req) {

			$data = array(
				'mandatorySavingsTransaction'  =>  $this->MicroFinance->getMandatorySavingsTransaction($req->savingsAccountId)
			);

			return response::json($data);
		}

		public function mandatoryLoanDetails(Request $req) {

			$data = array(
				'mandatoryLoanTransaction'  =>  $this->MicroFinance->getMandatoryLoanTransaction($req->loanProductId)
			);

			return response::json($data);
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: SEARCHING PARAMETERS FUNCTIONS FOR MEMBER INFORMATIONS.
		|--------------------------------------------------------------------------
		*/
		public function loadSamityAndPrimaryProductOptions(Request $req) {

			$data = array(
				'samity'  		  =>  $this->MicroFinance->getBranchWiseSamityOptions($req->branchId),
				'primaryProduct'  =>  $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions($req->branchId),
			);

			return response::json($data);
		}

		public function updateMember($memberId) {
			$loanExist = '';
			$savingsExist = '';

			$memberDetails = $this->MicroFinance->getMembersDetails($memberId);
			$mandatorySavingsDetails = $this->MicroFinance->getMandatorySavingsDetails($memberId);
			// dd($mandatorySavingsDetails, $memberId);

			$advancedEdit = $this->MicroFinance->getCheckLoanExistsOfMember($memberId);

			if ($advancedEdit == 0) {
				$loanExist = 'LoanExist';
				// return redirect()->route('./viewMember');
			}

			$checkSavingsAccount = DB::table('mfn_savings_account')
			->where([['memberIdFk', $memberId], ['status', '=', 1], ['softDel', '=', 0]])
			->pluck('id')
			->toArray();

			$checkSavingsDeposite = DB::table('mfn_savings_deposit')
			->where([['memberIdFk', $memberId], ['status', '=', 1], ['softDel', '=', 0]])
			->whereIn('accountIdFk', $checkSavingsAccount)
			->count();

			if ($checkSavingsDeposite > 0) {
				$savingsExist = 'SavingsExit';
				// return redirect()->route('./viewMember');
			}

			// dd($memberId, $checkSavingsAccount, $checkSavingsDeposite);

			if($advancedEdit==1):
				$samities = $this->MicroFinance->getSamity();
				$primaryProduct = $this->MicroFinance->getActiveLoanPrimaryProductOptions();

			else:
				$samities = $this->MicroFinance->getSamityOptionsSingle($memberDetails->samityId);
				$primaryProduct = $this->MicroFinance->getLoanProductsOptionSingle($memberDetails->primaryProductId);
			endif;

			if ($memberDetails->ds == 1) {
				$samities = DB::table('mfn_samity')
				->where([['status', '=', 1], ['id', $memberDetails->samityId]])
				->select(DB::raw("CONCAT(code, ' - ', name) AS nameWithCode"), 'id')
				->get()
				->pluck('nameWithCode', 'id')
				->toArray();

				$primaryProduct = DB::table('mfn_loans_product')
				->select('name', 'id')
				->get()
				->pluck('name', 'id')
				->all();
			}

			// dd($memberDetails->samityId, $memberDetails->primaryProductId, $samities,  $primaryProduct, $primaryProductAll, $advancedEdit);

			$gender = $this->MicroFinance->getGenderOptions($memberDetails->gender);
			$villages = $this->MicroFinance->getVillages(Session::get('branchId'));
			$curResidentType = $this->MicroFinance->getCurResidentType();

			$mandatorySavingsProduct = $this->MicroFinance->getMandatorySavingsProduct();
			unset($mandatorySavingsProduct['']);

			$maritalStatus = $this->MicroFinance->getMaritalStatus();
			$relationship = $this->MicroFinance->getRelationship();
			$educationLevel = $this->MicroFinance->getEducationLevel();
			$country = $this->MicroFinance->getCountry();
			$designation = $this->MicroFinance->getDesignationOfReferrer();
			$memberType = $this->MicroFinance->getMemberType();
			$professions = $this->MicroFinance->getProfessionOfMember();
			$religion = $this->MicroFinance->getReligion();
			$nominee = $this->MicroFinance->getNomineeOfMembers($memberDetails->nomineeId);
			$reference = $this->MicroFinance->getReferenceOfMembers($memberDetails->referenceId);

			// IF there exits any loan or transactions, then some fileds cant not be editabe
			$loanExists = $this->MicroFinance->checkLoanExists($memberId);
			$savingsDepositExists = $this->MicroFinance->checkSavingsDepositExists($memberId);

			$isTraExits = max($loanExists,$savingsDepositExists);

			// dd($loanExists,$savingsDepositExists);


			$damageData = array(
				'member'  	   	  		  =>  $memberDetails,
				'samities'  	   		  =>  $samities,
				'admissionDate'    	  	  =>  GetSoftwareDate::getSoftwareDate(),
				'villages'  	   		  =>  $villages,
				'curResidentType'  		  =>  $curResidentType,
				'primaryProduct'   		  =>  $primaryProduct,
				'gender'   		  		  =>  $gender,
				'mandatorySavingsProduct' =>  $mandatorySavingsProduct,
				'maritalStatus'    		  =>  $maritalStatus,
				'relationship'     		  =>  $relationship,
				'educationLevel'   		  =>  $educationLevel,
				'country'   	   		  =>  $country,
				'designation'			  =>  $designation,
				'memberType'			  =>  $memberType,
				'professions'			  =>  $professions,
				'religion'				  =>  $religion,
				//'advancedEdit'			  =>  $advancedEdit,
				'mandatorySavingsDetails' =>  $mandatorySavingsDetails,
				'nominees'			      =>  $nominee,
				'references'			  =>  $reference,
				'MicroFinance'            =>  $this->MicroFinance,
				'isTraExits'              =>  $isTraExits,
				'loanExist'               =>  $loanExist,
				'savingsExist'            =>  $savingsExist
			);

			 //dd($damageData);

			return view('microfin/member/member/editMember', $damageData);
		}

		public function checkAdmissionDate(Request $req) {

			$admissionDate = $this->MicroFinance->getDBDateFormat($req->admissionDate);

			$admissionDateChangeProceed = DB::table('mfn_samity')
			->where([['id', $req->samityId],
				['openingDate', '<=', $admissionDate]
			])
			->count();

			$curSamityOpeningDate = DB::table('mfn_samity')->where('id', $req->samityId)->value('openingDate');

			$data = array(
				'admissionDateChangeProceed'  =>  $admissionDateChangeProceed,
				'curSamityOpeningDate'        =>  $this->MicroFinance->getMicroFinanceDateFormat($curSamityOpeningDate)
			);

			return response::json($data);
		}

		public function updateItem(Request $req) {
			// $memberId =
			
			// dd($nidimg);
			
			// IF there exits any loan or transactions, then some fileds cant not be editabe
			$loanExists = $this->MicroFinance->checkLoanExists($req->memberId);
			$savingsDepositExists = $this->MicroFinance->checkSavingsDepositExists($req->memberId);

			$isTraExits = max($loanExists,$savingsDepositExists);


			//	UPDATE MEMBER.
			$member = MfnMemberInformation::find($req->memberId);
			$previousdata = $member;

			//	MEMBER.
			if ($isTraExits==0) {
				$member->samityId = $req->samityId;
				$member->primaryProductId = $req->primaryProductId;
			}
			$member->name = $req->name;
			$member->surName = $req->surName;
			// $member->admissionDate = $this->MicroFinance->getDBDateFormat($req->admissionDate);


			$member->code = $req->code;
			$member->gender = $req->gender;
			$member->dob = $this->MicroFinance->getDBDateFormat($req->dob);
			$member->age = $req->age;

			$member->maritalStatus = $req->maritalStatus;
			$member->spouseFatherSonName = $req->spouseFatherSonName;
			$member->relationship = $req->relationship;
			$member->admissionFee = $req->admissionFee;

			$member->admissionNo = $req->admissionNo;
			$member->nID = $req->nID;
			$member->birthRegNo = $req->birthRegNo;
			$member->passportNo = $req->passportNo;

			$member->groupId = $req->groupId;
			$member->subGroupId = $req->subGroupId;
			$member->maxEducation = $req->maxEducation;
			$member->mobileNo = $req->mobileNo;

			$member->formApplicationNo = $req->formApplicationNo;
			$member->nationality = $req->nationality;

			//	CONTACT DETAILS.
			$member->curVillageWard = $req->curVillageWard;
			$member->curFamilyHomeMobileNo = $req->curFamilyHomeMobileNo;
			$member->permVillageWard = $req->permVillageWard;
			$member->permFamilyHomeMobileNo = $req->permFamilyHomeMobileNo;

			//	NOMINEE DETAILS.
			$member->nomineeId = $req->nomineeId;

			//	REFERENCE DETAILS.
			$member->referenceId = $req->referenceId;

			//	OTHER INFORMATION.
			$member->memberTypeId = $req->memberTypeId;
			$member->motherName = $req->motherName;
			$member->profession = $req->profession;
			$member->religion = $req->religion;

			$member->familyMemberNum = $req->familyMemberNum;
			$member->yearlyIncome = $req->yearlyIncome;
			$member->landArea = $req->landArea;

			$member->note = $req->note;
			$member->fixedAssetDesc = $req->fixedAssetDesc;
			 //dd($member, $isTraExits);
			$member->save();

			$logArray = array(
				'moduleId'  => 6,
				'controllerName'  => 'MfnMemberInformationController',
				'tableName'  => 'mfn_member_information',
				'operation'  => 'update',
				'previousData'  => $previousdata,
				'primaryIds'  => [$previousdata->id]
			);
			Service::createLog($logArray);


			// FOR IMAGE CAPTURE
			if( $member->admissionDate > '2019-06-15'){

				$img = $req->image;
				$rsimg  = $req->regular_signature_image;
				$nidimg = $req->nid_signature_image;

				if ($img != "") {
					$folderPath = public_path('uploads/images/member/profile/');

					$image_parts = explode(";base64,", $img);
					$image_type_aux = explode("image/", $image_parts[0]);
					$image_type = $image_type_aux[1];

					$image_base64 = base64_decode($image_parts[1]);

					$picFiileName = uniqid() . '.png';

					$file = $folderPath . $picFiileName;
					file_put_contents($file, $image_base64);
				}
				else {
				//	PROFILE IMAGE STORE.
					if($req->hasFile('profileImage')):
						$profileImage = $req->file('profileImage');
						$filename = $profileImage->getClientOriginalName();
						$EXT = $profileImage->getClientOriginalExtension();
						$profileImageFileName = $this->MicroFinance->imageNameEncoder($filename, $EXT, Session::get('branchId'), $req->code);
						$req->file('profileImage')->move('uploads/images/member/profile/', $profileImageFileName);
					endif;
				}

            //	REGULAR SIGNATURE IMAGE STORE.
				if($req->hasFile('regularSignatureImage')):
					$regularSignature = $req->file('regularSignatureImage');
					$filename = $regularSignature->getClientOriginalName();
					$EXT = $regularSignature->getClientOriginalExtension();
					$regularSignatureImageFileName = $this->MicroFinance->imageNameEncoder($filename, $EXT, Session::get('branchId'), $req->code);
					$req->file('regularSignatureImage')->move('uploads/images/member/regular-signature/', $regularSignatureImageFileName);
				endif;

				if ($rsimg != '') {
					$folderPath = public_path('uploads/images/member/regular-signature/');

					$image_parts = explode(";base64,", $rsimg);
					$image_type_aux = explode("image/", $image_parts[0]);
					$image_type = $image_type_aux[1];

					$image_base64 = base64_decode($image_parts[1]);

					$regularSignatureImageFileName = uniqid() . '.png';

					$file = $folderPath . $regularSignatureImageFileName;
					file_put_contents($file, $image_base64);
				}

            //	NATIONAL ID SIGNATURE IMAGE STORE.
				if($req->hasFile('nIDSignatureImage')):
					$nIDSignature = $req->file('nIDSignatureImage');
					$filename = $nIDSignature->getClientOriginalName();
					$EXT = $nIDSignature->getClientOriginalExtension();
					$nIDSignatureImageFileName = $this->MicroFinance->imageNameEncoder($filename, $EXT, Session::get('branchId'), $req->code);
					$req->file('nIDSignatureImage')->move('uploads/images/member/nid-signature/', $nIDSignatureImageFileName);
				endif;

				if ($nidimg != '') {
					$folderPath = public_path('uploads/images/member/nid-signature/');

					$image_parts = explode(";base64,", $nidimg);
					$image_type_aux = explode("image/", $image_parts[0]);
					$image_type = $image_type_aux[1];

					$image_base64 = base64_decode($image_parts[1]);

					$nIDSignatureImageFileName = uniqid() . '.png';

					$file = $folderPath . $nIDSignatureImageFileName;
					file_put_contents($file, $image_base64);
				}

            //	FOR PROFILE, REGULAR SIGNATURE AND NATIONALA ID SINGNATURE IMAGE NAME SAVE.
				$fileNameSave = MfnMemberInformation::where('code', '=', $req->code)->first();

			// FOR IMAGE CAPTURE
				if ($img != "") {
					$fileNameSave->profileImage = $picFiileName;
				}
				else {
					if($req->hasFile('profileImage')):
						$fileNameSave->profileImage = $profileImageFileName;
					endif;
				}

				if($req->hasFile('regularSignatureImage')):
					$fileNameSave->regularSignatureImage = $regularSignatureImageFileName;
				endif;

				if ($rsimg != "") {
					$fileNameSave->regularSignatureImage = $regularSignatureImageFileName;
				}

				if($req->hasFile('nIDSignatureImage')):
					$fileNameSave->nIDSignatureImage = $nIDSignatureImageFileName;
				endif;

				if ($nidimg != '') {
					$fileNameSave->nIDSignatureImage = $nIDSignatureImageFileName;
				}

			// dd($fileNameSave);

				$fileNameSave->save();
			}

			if ($isTraExits==0) {
				// Change the savinhs accounts samity id if exits
				DB::table('mfn_savings_account')
				->where('memberIdFk', $member->id)
				->update(
					[
						'samityIdFk' => $member->samityId
					]
				);
			}

			$checkOpeningBalance = DB::table('mfn_opening_savings_account_info')
			->where('memberIdFk', $member->id)
			->pluck('id')
			->toArray();

			if (sizeof($checkOpeningBalance) > 0) {
				DB::table('mfn_opening_savings_account_info')
				->where('memberIdFk', $member->id)
				->update(
					[
						'primaryProductIdFk' => $member->primaryProductId,
						'samityIdFk' 		 => $member->samityId
					]
				);
			}

			$data = array(
				'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
				'responseText'   =>  MicroFinance::getMessage('memberUpdateSuccess'),
			);

			return response::json($data);
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: DELETE MEMBER INFORMATION.
		|--------------------------------------------------------------------------
		*/
		public function deleteItem(Request $req) {

			//	CHECK IF THERE IS ANY LOAN AGAINST THE MEMBER ID.
			$loanExists = $this->MicroFinance->checkLoanExists($req->id);
			$savingsDepositExists = $this->MicroFinance->checkSavingsDepositExists($req->id);
			$savingsWithdrawExists = $this->MicroFinance->checkSavingsWithdrawExists($req->id);
			$productTransferExists = $this->MicroFinance->checkProductTransferExists($req->id);

			//print_r($loanExists);print_r($savingsDepositExists);dd($savingsWithdrawExists);

			if($loanExists==1 || $savingsDepositExists==1 || $savingsWithdrawExists==1|| $productTransferExists==1):
				$lockDelete = 1;
			endif;

			if($loanExists==0 && $savingsDepositExists==0 && $savingsWithdrawExists==0 && $productTransferExists==0):
				$lockDelete = 0;
			endif;

			if($lockDelete==0):

				$previousdata = MfnMemberInformation::find($req->id);
				$memberDelete = $this->MicroFinance->softDelete($req->id, ['mfn_member_information', 'mfn_savings_account'], ['id', 'memberIdFk']);
				$logArray = array(
					'moduleId'  => 6,
					'controllerName'  => 'MfnMemberInformationController',
					'tableName'  => 'mfn_member_information',
					'operation'  => 'delete',
					'previousData'  => $previousdata,
					'primaryIds'  => [$previousdata->id]
				);
				Service::createLog($logArray);

			endif;

			//	GET ALL THE IMAGES NAME OF THE MEMBER.
			$images = $this->MicroFinance->getAllImagesOfMember($req->id);

			//	DELETE PROFILE, REGULAR SIGNATURE, NATIONAL ID SIGNATURE IMAGE.
			$IMAGES_DIR = ['profileImage' 		   => 'profile/',
			'regularSignatureImage' => 'regular-signature/',
			'nIDSignatureImage'     => 'nid-signature/'
		];

		$imagePath = "uploads/images/member/";

		foreach($IMAGES_DIR as $key => $DIR):
			if(File::exists($imagePath . $DIR . $images->$key)):
				File::delete($imagePath . $DIR . $images->$key);
			endif;
		endforeach;

		$data = array(
			'responseTitle' =>  $lockDelete==0?MicroFinance::getMessage('msgSuccess'):MicroFinance::getMessage('msgWarning'),
			'responseText'  =>  $lockDelete==0?MicroFinance::getMessage('memberDelSuccess'):MicroFinance::getMessage('memberDelFailed'),
		);

		return response()->json($data);
	}


}
