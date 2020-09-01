<?php
namespace App\Http\Controllers\pos;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\pos\PosCustomer;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule; 

class PosCustomerController extends Controller
{
	public function index(Request $request){

		$customers = PosCustomer::where('companyId', Auth::user()->company_id_fk)->get();
		return view('pos/customer/viewCustomer',['customers' => $customers]);
	}

	public function addCustomer()
	{
		$setting = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();
		return view('pos/customer/addCustomer', compact('setting'));
	}
	
	//insert function
	public function addItem(Request $req){
		//dd($req->all());
		$rules = array(
			// 'name'                  => 'required|unique:pos_customer,name',
			// 'code'                  => 'required|unique:pos_customer,code',
			'name' =>[
	            'required',
	             Rule::unique('pos_customer')->where('companyId', Auth::user()->company_id_fk),
  			],
  			'code' =>[
	            'required',
	             Rule::unique('pos_customer')->where('companyId', Auth::user()->company_id_fk),
  			],
			'email'              	=> 'required',
			'mobile'             	=> 'required',
			'preAddress'            => 'required',
			//'desccription'            => 'required',

		);
		$attributeNames = array(
			'name'                  => 'Name',
			'code'                  => 'Code',
			'email'              	=> 'Email',
			'mobile'             	=> 'Mobile',
			'preAddress'            => 'Address',
			//'desccription'            => 'Description',
		);

		$validator = Validator::make ( Input::all (), $rules);
		$validator->setAttributeNames($attributeNames);
		if ($validator->fails())
		return response::json(array('errors' => $validator->getMessageBag()->toArray()));
		else {

			$voucherConfigData = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();

			$accountLedgerParentData = DB::table('acc_account_ledger')->where('id', $voucherConfigData->customer)->first();
		    $accountLedgerChildData = DB::table('acc_account_ledger')
									  ->where('accountTypeId', $accountLedgerParentData->accountTypeId)
									  ->where('companyIdFk', $accountLedgerParentData->companyIdFk)
									  ->where('parentId', $accountLedgerParentData->id)
									  ->orderBy('code', 'desc')->first();

									
			if($accountLedgerChildData) 
			{
				$code = (int)$accountLedgerChildData->code + 1;
				$ordering = (int)$accountLedgerChildData->ordering + 1;
				$level = (int)$accountLedgerChildData->level + 1;
			}
			else 
			{
				$code = (int)$accountLedgerParentData->code + 1;
				$ordering = (int)$accountLedgerParentData->ordering + 1;
				$level = (int)$accountLedgerParentData->level + 1;

			}

			$now = Carbon::now();
			$data[] = array(
				'name' => $req->name,
				'code' => $code,
				'companyIdFk' => Auth::user()->company_id_fk,
				'description' => '',
				'accountTypeId' => $accountLedgerParentData->accountTypeId,
				'ordering' => $ordering,
				'parentId' => $accountLedgerParentData->id,
				'level' => $level,
				'isGroupHead' => 0,
				'createdDate' => $now,
				'projectBranchId' => $accountLedgerParentData->projectBranchId
			);

			DB::table('acc_account_ledger')->insert($data);

			$insertInfo = DB::table('acc_account_ledger')->where('code', $code)
														->where('accountTypeId', $accountLedgerParentData->accountTypeId)
														->where('companyIdFk', Auth::user()->company_id_fk)
														->where('parentId', $accountLedgerParentData->id) 
														->first();

			$posCustomer                 = new PosCustomer();
			
			$posCustomer->companyId = Auth::user()->company_id_fk;

			$posCustomer->name           = $req->name;
			$posCustomer->preAddress     = $req->preAddress;
			$posCustomer->code           = $req->code;
			$posCustomer->mobile         = $req->mobile;
			$posCustomer->accAccountLedgerId = $insertInfo->id;
			$posCustomer->email          = $req->email;
			$posCustomer->cusDes         = $req->cusDescription;
			$posCustomer->createdDate    = Carbon::now();

			$posCustomer->save();	
			return response()->json(['id' => $posCustomer->id]);
		}
	}
	/*Get Data for Edit */
	public function getCustomerInfo(Request $req){
		$customer = PosCustomer::find($req->id);

		$data = array(
			'customer'             => $customer,
			'slno'                => $req->slno,
		);
		return response()->json($data);
	}

	//edit function
	public function editItem(Request $req) {
		//dd($req->all());
		$rules = array(
			// 'name'                  => 'required',
			// 'code'                  => 'required',
			'name' =>[
	            'required',
	             Rule::unique('pos_customer')->where('companyId', Auth::user()->company_id_fk).$req->id,
  			],
  			'code' =>[
	            'required',
	             Rule::unique('pos_customer')->where('companyId', Auth::user()->company_id_fk).$req->id,
  			],
			'email'              	=> 'required',
			'mobile'             	=> 'required',
		

		);
		$attributeNames = array(
			'name'                  => 'Name',
			'code'                  => 'Code',
			'email'              	=> 'Email',
			'mobile'             	=> 'Mobile',
		
		);

		$validator = Validator::make ( Input::all (), $rules);
		$validator->setAttributeNames($attributeNames);
		if ($validator->fails())
		return response::json(array('errors' => $validator->getMessageBag()->toArray()));
		else{

			$customer = PosCustomer::find ($req->id);
			$customer->name           = $req->name;
			//$customer->fothersName    = $req->fothersName;
			$customer->preAddress     = $req->preAddress;
			//$customer->paramaAddress  = $req->paramaAddress;
			$customer->code           = $req->code;
			$customer->mobile         = $req->mobile;
			$customer->email          = $req->email;
			//$customer->nid            = $req->nid;
			$customer->cusDes         = $req->cusDescription;
			//dd($customer);
			$customer->save();

			$data = array(
				'customer'            => $customer,
				'slno'                => $req->slno,
			);
			return response()->json($data);
		}
	}
	/*Customer Details Function*/
	public function customerDetails(Request $req){
		// dd($req->all());
		$customerId =  PosCustomer::where('id',$req->id)->first();
		$customerName        	= $customerId->name;
		$customerCode        	= $customerId->code;
		$customerMobile        	= $customerId->mobile;
		$customerEmail        	= $customerId->email;
		$customerIdNo        	= $customerId->id;
		$customerpreAddress     = $customerId->preAddress;
		$customerDescription    = $customerId->cusDes;

		$data = array(
			'customerName'                      => $customerName,
			'customerCode'                      => $customerCode,
			'customerIdNo'                      => $customerIdNo,
			'customerMobile'        			=> $customerMobile,
			'customerEmail'        				=> $customerEmail,
			'customerpreAddress'        	    => $customerpreAddress,
			'customerDescription'        	    => $customerDescription,

		);
		return response()->json($data);
	}

	//delete function
	public function deleteItem(Request $req) {
		$imgName = PosCustomer::find($req->id);
		DB::table('acc_account_ledger')->where('id', $imgName->accAccountLedgerId)->delete();
		$imgName->delete();
		$data = ['text' => 'Deleted successfully!'];
		return response()->json($data);
	}

}
