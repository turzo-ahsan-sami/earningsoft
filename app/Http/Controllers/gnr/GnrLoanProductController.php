<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use App\Http\Controllers\gnr\Service;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class GnrLoanProductController extends Controller {

	public function index() 
	{
		$loanProducts = DB::table('gnr_loan_product')->get();

		return view('gnr.tools.loanProduct.viewLoanProduct',['loanProducts'=>$loanProducts]);
	}

	public function addLoanProduct(){
		$donors = array(''=>'Select Donor') + DB::table('gnr_bank')->pluck('name','id')->toArray();

		$data = array(
			'donors'	=> $donors
		);

		return view('gnr.tools.loanProduct.addLoanProduct',$data);
	}

	public function storeLoanProduct(Request $request)
	{

		$rules = array(
			'name' => 'required|unique:gnr_loan_product',
			'productCode' => 'unique:gnr_loan_product',
			'donor' => 'required'
		);
		$attributeNames = array(
			'name' => 'Product Name',
			'productCode' => 'Product Code',
			'donor' => 'Donor'
		);

		$validator = Validator::make ( Input::all (), $rules);
		$validator->setAttributeNames($attributeNames);
		if ($validator->fails()){
			return response::json(array('errors' => $validator->getMessageBag()->toArray()));
		}


	          ///Store Information
		else{

			DB::table('gnr_loan_product')->insert([
				'name' => $request->name, 
				'productCode' => $request->productCode,
				'donorId_fk' => $request->donor,
				'createdAt' => Carbon::now()				    
			]);
		}

		$logArray = array(
			'moduleId'  => 7,
			'controllerName'  => 'GnrLoanProductController',
			'tableName'  => 'gnr_loan_product',
			'operation'  => 'insert',
			'primaryIds'  => [DB::table('gnr_loan_product')->max('id')]
		);
		Service::createLog($logArray);

		return response::json('success');
	}

	public function editLoanProduct(Request $request)
	{

		$rules = array(
			'name' => 'required|unique:gnr_loan_product,name,'.$request->productId,
			'productCode' => 'unique:gnr_loan_product,productCode,'.$request->productId,
			'donor' => 'required'
		);
		$attributeNames = array(
			'name' => 'Product Name',
			'productCode' => 'Product Code',
			'donor' => 'Donor'
		);
		$previousdata = DB::table('gnr_loan_product')->find ($request->productId);
		$validator = Validator::make ( Input::all (), $rules);
		$validator->setAttributeNames($attributeNames);
		if ($validator->fails()){
			return response::json(array('errors' => $validator->getMessageBag()->toArray()));
		}


	          ///Store Information
		else{

			DB::table('gnr_loan_product')
			->where('id',$request->productId)
			->update([
				'name' => $request->name, 
				'productCode' => $request->productCode,
				'donorId_fk' => $request->donor
			]);
		}
		$logArray = array(
			'moduleId'  => 7,
			'controllerName'  => 'GnrLoanProductController',
			'tableName'  => 'gnr_loan_product',
			'operation'  => 'update',
			'previousData'  => $previousdata,
			'primaryIds'  => [$previousdata->id]
		);
		Service::createLog($logArray);

		return response::json('success');

	}

	public function deleteLoanProduct(Request $request)
	{   
		$previousdata = DB::table('gnr_loan_product')->find ($request->productId);
		DB::table('gnr_loan_product')->where('id',$request->productId)->delete();
		$logArray = array(
			'moduleId'  => 7,
			'controllerName'  => 'GnrLoanProductController',
			'tableName'  => 'gnr_loan_product',
			'operation'  => 'delete',
			'previousData'  => $previousdata,
			'primaryIds'  => [$previousdata->id]
		);
		Service::createLog($logArray);
		return response::json('success');
	}


	public function getLoanProductInfo(Request $request)
	{
		$product = DB::table('gnr_loan_product')->where('id',$request->productId)->first();

		return response::json($product);
	}

}