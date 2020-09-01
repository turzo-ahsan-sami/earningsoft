<?php
namespace App\Http\Controllers\pos;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;

class VoucherSettingController extends Controller
{	
	
	public function voucherSettingList()
	{
		$setting = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();

		if($setting)
		{	
			$customer = DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)
							->where('id', $setting->customer)
							->select(DB::raw("CONCAT(acc_account_ledger.name, ' [', acc_account_ledger.code, ']') as nameWithCode"))
							->first();

			$supplier = DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)
								->where('id', $setting->supplier)
								->select(DB::raw("CONCAT(acc_account_ledger.name, ' [', acc_account_ledger.code, ']') as nameWithCode"))
								->first();
			
			$purchase = DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)
								->where('id', $setting->purchase)
								->select(DB::raw("CONCAT(acc_account_ledger.name, ' [', acc_account_ledger.code, ']') as nameWithCode"))
								->first();
			
			$purchaseReturn = DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)
								->where('id', $setting->purchase_return)
								->select(DB::raw("CONCAT(acc_account_ledger.name, ' [', acc_account_ledger.code, ']') as nameWithCode"))
								->first();
			
			$sales = DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)
								->where('id', $setting->sales)
								->select(DB::raw("CONCAT(acc_account_ledger.name, ' [', acc_account_ledger.code, ']') as nameWithCode"))
								->first();
			
			$salesReturn = DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)
								->where('id', $setting->sales_return)
								->select(DB::raw("CONCAT(acc_account_ledger.name, ' [', acc_account_ledger.code, ']') as nameWithCode"))
								->first();

			$vat = DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)
								->where('id', $setting->vat)
								->select(DB::raw("CONCAT(acc_account_ledger.name, ' [', acc_account_ledger.code, ']') as nameWithCode"))
								->first();
			
			if($setting->inventory != 0)
				$inventory = DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)
								->where('id', $setting->inventory)
								->select(DB::raw("CONCAT(acc_account_ledger.name, ' [', acc_account_ledger.code, ']') as nameWithCode"))
								->first();

			if($setting->cost_of_good_sold != 0)
				$cost_of_good_sold = DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)
									->where('id', $setting->cost_of_good_sold)
									->select(DB::raw("CONCAT(acc_account_ledger.name, ' [', acc_account_ledger.code, ']') as nameWithCode"))
									->first();
			

			$voucherSettingCollections = array(
				'customer' 				=> @$customer->nameWithCode,
				'supplier' 				=> @$supplier->nameWithCode,
				'purchase' 				=> @$purchase->nameWithCode,
				'purchaseReturn' 		=> @$purchaseReturn->nameWithCode,
				'sales' 				=> @$sales->nameWithCode,
				'salesReturn' 			=> @$salesReturn->nameWithCode,
				'vat' 					=> @$vat->nameWithCode,
				'inventory'				=> @$inventory->nameWithCode,
				'cost_of_good_sold'		=> @$cost_of_good_sold->nameWithCode

			);
		}
		else $voucherSettingCollections = array();

		return view('pos/setting/listVoucherSetting', compact('voucherSettingCollections'));
	}

	public function addVoucherSetting()
	{	
		$companyLedgers = DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)
				->where('parentId', '!=', 0)
				->select(DB::raw("CONCAT(acc_account_ledger.name, ' [', acc_account_ledger.code, ']') as nameWithCode"), 
				'id', 'isGroupHead')->get();
				
		$accountHeads = $companyLedgers->where('isGroupHead', 1)->pluck('nameWithCode', 'id')->toArray();
		$transactionHeads = $companyLedgers->where('isGroupHead', 0)->pluck('nameWithCode', 'id')->toArray();

		$stockExist = DB::table('gnr_company')->where('id', Auth::user()->company_id_fk)->pluck('stock_type');
		
		return view('pos/setting/addVoucherSetting', compact('accountHeads', 'transactionHeads', 'stockExist'));
	}

	public function editVoucherSetting()
	{
		$setting = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();
		$companyLedgers = DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)
							->where('parentId', '!=', 0)
							->select(DB::raw("CONCAT(acc_account_ledger.name, ' [', acc_account_ledger.code, ']') as nameWithCode"), 
							 'id', 'isGroupHead')->get();
		$accountHeads = $companyLedgers->where('isGroupHead', 1)->pluck('nameWithCode', 'id')->toArray();
		$transactionHeads = $companyLedgers->where('isGroupHead', 0)->pluck('nameWithCode', 'id')->toArray();
		
		$stockExist = DB::table('gnr_company')->where('id', Auth::user()->company_id_fk)->pluck('stock_type');

		return view('pos/setting/editVoucherSetting', compact('setting', 'accountHeads', 'transactionHeads', 'stockExist'));
	}

	//insert item
	public function saveSetting(Request $req)
	{	
		$stockExist = DB::table('gnr_company')->where('id', Auth::user()->company_id_fk)->pluck('stock_type');

		if($stockExist[0] == 1)
		{
			$rules = array(
				'customer' 			=> 'required',
				'supplier' 			=> 'required',
				'sales'    			=> 'required',
				'salesReturn' 		=> 'required',
				'purchase'   		=> 'Required',
				'purchaseReturn' 	=> 'required',
				'vat'				=> 'required',
				'inventory'         => 'required',
				'cost_of_good_sold'	=> 'required'
			);
	
			$attributeNames = array(
				'customer' 			=> 'Customer',
				'supplier' 			=> 'Supplier',
				'sales'    			=> 'Sales',
				'salesReturn' 		=> 'Sales Return',
				'purchase'   		=> 'Purchase',
				'purchaseReturn' 	=> 'Purchase Return',
				'vat'				=> 'Vat',
				'inventory'			=> 'Inventory',
				'cost_of_good_sold' => 'Cost of good sold'
			);
		}
		else
		{
			$rules = array(
				'customer' 			=> 'required',
				'supplier' 			=> 'required',
				'sales'    			=> 'required',
				'salesReturn' 		=> 'required',
				'purchase'   		=> 'Required',
				'purchaseReturn' 	=> 'required',
				'vat'				=> 'required',
			);
	
			$attributeNames = array(
				'customer' 			=> 'Customer',
				'supplier' 			=> 'Supplier',
				'sales'    			=> 'Sales',
				'salesReturn' 		=> 'Sales Return',
				'purchase'   		=> 'Purchase',
				'purchaseReturn' 	=> 'Purchase Return',
				'vat'				=> 'Vat',
			);
		}
		
		$validator = Validator::make ( Input::all (), $rules);
		$validator->setAttributeNames($attributeNames);
		if ($validator->fails())
		return response::json(array('errors' => $validator->getMessageBag()->toArray()));
		else {

			$setting['company_id'] = Auth::user()->company_id_fk;
			$setting['customer'] = $req->customer;
			$setting['supplier'] = $req->supplier;
			$setting['sales'] = $req->sales;
			$setting['sales_return'] = $req->salesReturn;
			$setting['purchase'] = $req->purchase;
			$setting['purchase_return'] = $req->purchaseReturn;
			$setting['vat'] = $req->vat;
			$setting['inventory'] = ($stockExist[0] == 1) ? $req->inventory : 0;
			$setting['cost_of_good_sold'] = ($stockExist[0] == 1) ? $req->cost_of_good_sold : 0;

			$voucherSetting = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();

			if($voucherSetting) {
				DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->update($setting);
			}
			else {
				DB::table('pos_voucher_setting')->insert($setting);
			}

			return response()->json();
		}
	}

}
