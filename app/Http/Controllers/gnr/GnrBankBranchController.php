<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\gnr\GnrBank;
use App\gnr\GnrBankBranch;
use Validator;
use Response;
use App\Http\Controllers\gnr\Service;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class GnrBankBranchController extends Controller {

	public function index() {

		$branches = GnrBankBranch::all();

		return view('gnr.tools.bank.viewBankBranch',['branches'=>$branches]);
	}

	public function addBranch() {

		return view('gnr.tools.bank.addBankBranch');
	}

	public function storeBranch(Request $request) {

		$rules = array(
			'bank'		=>	'required',			
			'branchName'		=>	'required',				
			'telephoneNumber'		=>	'bail|required|numeric|unique:gnr_bank_branch',
			'branchEmail'		=>	'bail|required|email|unique:gnr_bank_branch',

			'contactPerson'		=>	'required',				
			'designation'		=>	'required',				
			'contactPersonTelephoneNumber'		=>	'bail|required|numeric|unique:gnr_bank_branch',
			'contactPersonMobileNumber'		=>	'bail|required|numeric|digits:11|unique:gnr_bank_branch',
			'contactPersonEmail'		=>	'bail|required|email|unique:gnr_bank_branch'
		);

		$attributesNames = array(
			'bank'		=>	'Bank',
			'branchName'		=>	'Branch Name',
			'telephoneNumber'		=>	'Telephone Number',
			'branchEmail'		=>	'E-mail',

			'contactPerson'		=>	'Contact Person',
			'designation'		=>	'Designation',
			'contactPersonTelephoneNumber'		=>	'Telephone Number',	
			'contactPersonMobileNumber'		=>	'Mobile Number',	
			'contactPersonEmail'		=>	'E-mail'

		);

		$validator = Validator::make(Input::all(), $rules);
		$validator->setAttributeNames($attributesNames);

		if($validator->fails()) {
			return response::json(array('errors' => $validator->getMessageBag()->toArray()));
		} 
		else {

			$branch = new GnrBankBranch;
			$branch->name = $request->branchName;
			$branch->bankId_fk = $request->bank;
				/*$branch->divisionId_fk = $request->division;
				$branch->districtId_fk = $request->district;
				$branch->upazillaId_fk = $request->upazilla;*/
				$branch->telephoneNumber = $request->telephoneNumber;
				$branch->branchEmail = $request->branchEmail;
				$branch->address = $request->address;

				$branch->contactPerson = $request->contactPerson;
				$branch->contactPersonDesiganation = $request->designation;
				$branch->contactPersonTelephoneNumber = $request->contactPersonTelephoneNumber;
				$branch->contactPersonMobileNumber = $request->contactPersonMobileNumber;
				$branch->contactPersonEmail = $request->contactPersonEmail;
				$branch->createdAt = Carbon::now();
				$branch->save();				
				
			}

			$logArray = array(
				'moduleId'  => 7,
				'controllerName'  => 'GnrBankBranchController',
				'tableName'  => 'gnr_bank_branch',
				'operation'  => 'insert',
				'primaryIds'  => [DB::table('gnr_bank_branch')->max('id')]
			);
			Service::createLog($logArray);
			return response::json('success');
		}


		public function editBranch(Request $request) {

			$rules = array(
				'bank'		=>	'required',			
				'branchName'		=>	'required',				
				'telephoneNumber'		=>	'bail|required|numeric|unique:gnr_bank_branch,telephoneNumber,'.$request->branchId,
				'branchEmail'		=>	'bail|required|email|unique:gnr_bank_branch,branchEmail,'.$request->branchId,

				'contactPerson'		=>	'required',				
				'designation'		=>	'required',				
				'contactPersonTelephoneNumber'		=>	'bail|required|numeric|unique:gnr_bank_branch,contactPersonTelephoneNumber,'.$request->branchId,
				'contactPersonMobileNumber'		=>	'bail|required|numeric|digits:11|unique:gnr_bank_branch,contactPersonMobileNumber,'.$request->branchId,
				'contactPersonEmail'		=>	'bail|required|email|unique:gnr_bank_branch,contactPersonEmail,'.$request->branchId
			);

			$attributesNames = array(
				'bank'		=>	'Bank',
				'branchName'		=>	'Branch Name',
				'telephoneNumber'		=>	'Telephone Number',
				'branchEmail'		=>	'E-mail',

				'contactPerson'		=>	'Contact Person',
				'designation'		=>	'Designation',
				'contactPersonTelephoneNumber'		=>	'Telephone Number',	
				'contactPersonMobileNumber'		=>	'Mobile Number',	
				'contactPersonEmail'		=>	'E-mail'
				
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) {
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			} 
			else {
				$previousdata = GnrBankBranch::find($request->branchId);
				$branch = GnrBankBranch::find($request->branchId);
				$branch->name = $request->branchName;
				$branch->bankId_fk = $request->bank;
				/*$branch->divisionId_fk = $request->division;
				$branch->districtId_fk = $request->district;
				$branch->upazillaId_fk = $request->upazilla;*/
				$branch->telephoneNumber = $request->telephoneNumber;
				$branch->branchEmail = $request->branchEmail;
				$branch->address = $request->address;

				$branch->contactPerson = $request->contactPerson;
				$branch->contactPersonDesiganation = $request->designation;
				$branch->contactPersonTelephoneNumber = $request->contactPersonTelephoneNumber;
				$branch->contactPersonMobileNumber = $request->contactPersonMobileNumber;
				$branch->contactPersonEmail = $request->contactPersonEmail;
				$branch->createdAt = Carbon::now();
				$branch->save();				
				
			}
			$logArray = array(
				'moduleId'  => 7,
				'controllerName'  => 'GnrBankBranchController',
				'tableName'  => 'gnr_bank_branch',
				'operation'  => 'update',
				'previousData'  => $previousdata,
				'primaryIds'  => [$previousdata->id]
			);
			Service::createLog($logArray);
			return response::json('success');
		}

		public function deleteBranch(Request $request)
		{  
			$previousdata = GnrBankBranch::find($request->branchId);
			$branch = GnrBankBranch::find($request->branchId);
			$branch->delete();
			$logArray = array(
				'moduleId'  => 7,
				'controllerName'  => 'GnrBankBranchController',
				'tableName'  => 'gnr_bank_branch',
				'operation'  => 'delete',
				'previousData'  => $previousdata,
				'primaryIds'  => [$previousdata->id]
			);
			Service::createLog($logArray);
			return response::json('success');
		}


		public function getBranchInfo(Request $request)
		{
			$branch = GnrBankBranch::find($request->branchId);
			$bank = GnrBank::where('id',$branch->bankId_fk)->select('id','name')->first();
			$division = DB::table('division')->where('id',$branch->divisionId_fk)->select('id','division_name')->first();
			$district = DB::table('district')->where('id',$branch->districtId_fk)->select('id','district_name')->first();
			$upazilla = DB::table('upzilla')->where('id',$branch->upazillaId_fk)->select('id','upzilla_name')->first();

			$data = array(
				'branch' => $branch,
				'bank' => $bank,
				'division' => $division,
				'district' => $district,
				'upazilla' => $upazilla
			);

			return response::json($data);
			
		}



		public function filterDistrictNUpazilla(Request $request)
		{
			$district = DB::table('district');
			$upazilla = DB::table('upzilla');

			if ($request->divisionId!='') {
				$district = $district->where('division_id',$request->divisionId);
				$upazilla = $upazilla->where('division_id',$request->divisionId);
			}
			if ($request->districtId!='') {
				$upazilla = $upazilla->where('district_id',$request->districtId);
			}

			$district = $district->select('id','district_name')->get();
			$upazilla = $upazilla->select('id','upzilla_name')->get();

			$data = array(
				'district' => $district, 
				'upazilla' => $upazilla
			);

			return response::json($data);
			//return response::json('df');
		}






	}