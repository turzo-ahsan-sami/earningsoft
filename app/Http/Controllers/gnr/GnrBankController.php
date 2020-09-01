<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\gnr\GnrBank;
use Validator;
use Response;
use App\Http\Controllers\gnr\Service;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class GnrBankController extends Controller {

	public function index() {

		$banks = GnrBank::all();

		return view('gnr.tools.bank.viewBank',['banks'=>$banks]);
	}

	public function addBank() {

		return view('gnr.tools.bank.addBank');
	}

	public function storeBank(Request $request) {

		$rules = array(
			'bankName'		=>	'required|unique:gnr_bank,name',			
			'bankShortName'		=>	'required|unique:gnr_bank,shortName',		
			'type'		=>	'required'
		);

		$attributesNames = array(
			'bankName'		=>	'Bank/Donor Name',
			'bankShortName'		=>	'Short Name',
			'type'		=>	'Type'

		);

		$validator = Validator::make(Input::all(), $rules);
		$validator->setAttributeNames($attributesNames);

		if($validator->fails()) {
			return response::json(array('errors' => $validator->getMessageBag()->toArray()));
		} 
		else {

			$bank = new GnrBank;
			$bank->name = $request->bankName;
			$bank->shortName = $request->bankShortName;
			$bank->isDonor = $request->type;
			$bank->createdAt = Carbon::now();
			$bank->save();	
			$logArray = array(
				'moduleId'  => 7,
				'controllerName'  => 'GnrBankController',
				'tableName'  => 'gnr_bank',
				'operation'  => 'insert',
				'primaryIds'  => [DB::table('gnr_bank')->max('id')]
			);
			Service::createLog($logArray);			

		}
		return response::json('success');
	}


	public function editBank(Request $request) {

		$rules = array(
			'bankName'		=>	'required|unique:gnr_bank,name,'.$request->bankId,			
			'bankShortName'		=>	'required|unique:gnr_bank,shortName,'.$request->bankId,
			'type'		=>	'required'		
		);

		$attributesNames = array(
			'bankName'		=>	'Bank/Donor Name',
			'bankShortName'		=>	'Short Name',
			'type'		=>	'Type'

		);

		$validator = Validator::make(Input::all(), $rules);
		$validator->setAttributeNames($attributesNames);

		if($validator->fails()) {
			return response::json(array('errors' => $validator->getMessageBag()->toArray()));
		} 
		else {
			$previousdata = GnrBank::find ($request->bankId);
			$bank = GnrBank::find($request->bankId);
			$bank->name = $request->bankName;
			$bank->shortName = $request->bankShortName;
			$bank->isDonor = $request->type;
			$bank->save();				

		}
		$logArray = array(
			'moduleId'  => 7,
			'controllerName'  => 'GnrBankController',
			'tableName'  => 'gnr_bank',
			'operation'  => 'update',
			'previousData'  => $previousdata,
			'primaryIds'  => [$previousdata->id]
		);
		Service::createLog($logArray);
		return response::json('success');
	}

	public function deleteBank(Request $request)
	{
		$previousdata=GnrBank::find($request->bankId);
		$bank = GnrBank::find($request->bankId);
		$bank->delete();
		$logArray = array(
			'moduleId'  => 7,
			'controllerName'  => 'GnrBankController',
			'tableName'  => 'gnr_bank',
			'operation'  => 'delete',
			'previousData'  => $previousdata,
			'primaryIds'  => [$previousdata->id]
		);
		Service::createLog($logArray);
		return response::json('success');
	}


	public function getBankInfo(Request $request)
	{
		$bank = GnrBank::find($request->bankId);
		return response::json($bank);

	}






}