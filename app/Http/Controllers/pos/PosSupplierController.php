<?php
namespace App\Http\Controllers\pos;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\pos\PosSupplier;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule; 

class PosSupplierController extends Controller
{
	public function index(Request $request){

		$suppliers = PosSupplier::where('companyId', Auth::user()->company_id_fk)->get();

		return view('pos/supplier/viewSupplier',['suppliers' => $suppliers]);
	}

	public function addSupplier()
	{	
		$setting = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();
		return view('pos/supplier/addSupplier', compact('setting'));
	}
	
	//insert function
	public function addItem(Request $req){
		//dd($req->all());
		$rules = array(
			// 'name'                  => 'required|unique:pos_supplier,name',
			// 'code'                  => 'required|unique:pos_supplier,code',
			'name' =>[
	            'required',
	             Rule::unique('pos_supplier')->where('companyId', Auth::user()->company_id_fk),
  			],
  			'code' =>[
	            'required',
	             Rule::unique('pos_supplier')->where('companyId', Auth::user()->company_id_fk),
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
		else {

			$voucherConfigData = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();

			$accountLedgerParentData = DB::table('acc_account_ledger')->where('id', $voucherConfigData->supplier)->first();
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
				'name' => $req->supComName,
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


			$posSupplier                 = new PosSupplier();

			$posSupplier->companyId = Auth::user()->company_id_fk;

			$posSupplier->name           = $req->name;
			$posSupplier->supComName     = $req->supComName;
			$posSupplier->code           = $req->code;
			$posSupplier->mobile         = $req->mobile;
			$posSupplier->accAccountLedgerId = $insertInfo->id;
			$posSupplier->email          = $req->email;
			$posSupplier->website        = $req->website;
			$posSupplier->description    = $req->description;
			$posSupplier->address        = $req->address;
			$posSupplier->refNo          = $req->refNo;
			
			$posSupplier->createdDate    = Carbon::now();
			//dd($posSupplier);
			$posSupplier->save();

			return response()->json(['responseText' => 'Data successfully inserted!'], 200);
		}
	}
	/*Get Data for Edit */
	public function getSupplierInfo(Request $req){
		$supplier = PosSupplier::find($req->id);

		$data = array(
			'supplier'             => $supplier,
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
	             Rule::unique('pos_supplier')->where('companyId', Auth::user()->company_id_fk).$req->id,
  			],
  			'code' =>[
	            'required',
	             Rule::unique('pos_supplier')->where('companyId', Auth::user()->company_id_fk).$req->id,
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

			$supplier = PosSupplier::find ($req->id);
			$supplier->name           = $req->name;
			$supplier->code           = $req->code;
			$supplier->mobile         = $req->mobile;
			$supplier->email          = $req->email;
			$supplier->supComName     = $req->supComName;
			$supplier->address        = $req->address;
			$supplier->website        = $req->website;
			$supplier->description    = $req->description;
			$supplier->refNo    	  = $req->refNo;

			$supplier->save();

			$data = array(
				'supplier'            => $supplier,
				'slno'                => $req->slno,
			);
			return response()->json($data);
		}
	}
	/*supplier Details Function*/
	public function supplierDetails(Request $req){
		// dd($req->all());
		$supplierId =  PosSupplier::where('id',$req->id)->first();

		$supplierName        	= $supplierId->name;
		$supplierCode        	= $supplierId->code;
		$supplierMobile        	= $supplierId->mobile;
		$supplierNid        	= $supplierId->nid;
		$supplierEmail        	= $supplierId->email;
		$supplierIdNo        	= $supplierId->id;
		$supplierComName        		= $supplierId->supComName;
		$supplierAddress        		= $supplierId->address;
		$supplierWebsite        		= $supplierId->website;
		$supplierDescription            = $supplierId->description;
		$supplierRefNo            		= $supplierId->refNo;

		$data = array(
			'supplierName'                      => $supplierName,
			'supplierCode'                      => $supplierCode,
			'supplierIdNo'                      => $supplierIdNo,
			'supplierMobile'        			=> $supplierMobile,
			'supplierNid'        				=> $supplierNid,
			'supplierEmail'        				=> $supplierEmail,
			'supplierComName'        			=> $supplierComName,
			'supplierAddress'        			=> $supplierAddress,
			'supplierDescription'        	    => $supplierDescription,
			'supplierWebsite'        	    	=> $supplierWebsite,
			'supplierRefNo'        			    => $supplierRefNo

		);
		//dd($data);
		return response()->json($data);
	}

	//delete function
	public function deleteItem(Request $req) {
		$imgName = PosSupplier::find($req->id);
		$imgName->delete();
		$data = ['text' => 'Deleted successfully!'];
		return response()->json($data);
	}

}
