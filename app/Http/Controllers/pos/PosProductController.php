<?php
namespace App\Http\Controllers\pos;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\pos\PosProduct;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule; 

class PosProductController extends Controller
{
	public function index(Request $request){

		$company = Auth::user()->company;
		$units = config('constants.product_units');
		$productTypes = config('constants.product_types');
		if ($company->business_type != 'manufacture' || $company->stock_type != 1) {
			unset($productTypes['raw']);
		}
		$products = PosProduct::where('companyId', Auth::user()->company_id_fk)->get();
		// dd($units, $productTypes);

		// $groupId        = array();
		// $categoryId     = array();
		// $subCategoryId  = array();
		// $brandId        = array();
		// $modelId        = array();
		// $sizeId         = array();
		// $colorId        = array();

		// $products=DB::table('pos_product')->whereIn('groupId',$groupId)->whereIn('categoryId',$categoryId)->whereIn('subCategoryId',$subCategoryId)->whereIn('brandId',$brandId)->whereIn('modelId',$modelId)->whereIn('sizeId',$sizeId)->whereIn('colorId',$colorId)->get();


		return view('pos/product/productSetting/product/viewProduct',['products' => $products, 'units' => $units, 'productTypes' => $productTypes]);
	}

	public function addProduct(){
		return view('pos/product/productSetting/product/addProduct');
	}

	//insert function
	public function addItem(Request $req){

		$rules = array(
			'name' =>[
	            'required',
	             Rule::unique('pos_product')->where('companyId', Auth::user()->company_id_fk),
  			],
  			'code' =>[
	            'required',
	             Rule::unique('pos_product')->where('companyId', Auth::user()->company_id_fk),
  			],
			//'name'                   => 'required|unique:pos_product,name',
			//'code'                   => 'required|unique:pos_product,code',
			// 'costPrice'              => 'required',
			// 'salesPrice'			 => 'required',
			'type'					 => 'required|not_in:0',
			'unit'					 => 'required|not_in:0' 
		
		);

		$attributeNames = array(
			'name'                   => 'Name',
			'code'                   => 'Code',
			// 'costPrice'              => 'Cost Price',
			// 'salesPrice'			 => 'Sales Price',
			'type'					 => 'Type',
			'unit'					 => 'Unit'
			
		);

		$validator = Validator::make ( Input::all (), $rules);
		$validator->setAttributeNames($attributeNames);
		if ($validator->fails())
		return response::json(array('errors' => $validator->getMessageBag()->toArray()));
		else {


			// if ($req->file('image')) {
			// 	$imageFile =$req->file('image');
			// 	$imageFilename =date('Y-m-d H:i:s'). '-' .str_random(10). '.' .$imageFile->getClientOriginalExtension();
			// 	$destinationPath = base_path() . '/public/images/product/';
			// 	$images =$imageFile->move($destinationPath,$imageFilename);
			// }

			if($req->type == 'hour')
				PosProduct::where('type', 'hour')->where('companyId', Auth::user()->company_id_fk)->delete();
			

			$posProduct                 = new PosProduct();

			$posProduct->companyId = Auth::user()->company_id_fk;

			$posProduct->name           = $req->name;
			$posProduct->code           = $req->code;
			// $posProduct->groupId        = $req->groupId;
			// $posProduct->categoryId     = $req->categoryId;
			// $posProduct->subCategoryId  = $req->subCategoryId;
			// $posProduct->brandId        = $req->brandId;
			// $posProduct->modelId        = $req->modelId;
			// $posProduct->sizeId         = $req->sizeId;
			// $posProduct->colorId        = $req->colorId;
			// $posProduct->costPrice      = $req->costPrice;
			// $posProduct->salesPrice     = $req->salesPrice;
			$posProduct->type     		= $req->type;
			$posProduct->unit     		= $req->unit;
			// $posProduct->description    = $req->description;
			// $posProduct->productPackge  = json_encode($req->productPackge);
			$posProduct->createdDate    = Carbon::now();
			$posProduct->save();

			

			return response()->json(['responseText' => 'Data successfully inserted!'], 200);
		}
	}
	/*Get Data for Edit */
	public function getProductInfo(Request $req){
		$product = PosProduct::find($req->id);
		// $groupName          = DB::table('pos_product_group')->select('id')->where('id', $product->groupId)->first();
		// $categoryName       = DB::table('pos_product_category')->select('id')->where('id',$product->categoryId)->first();
		// $subCategoryName    = DB::table('pos_product_sub_category')->select('id')->where('id',$product->subCategoryId)->first();
		// $productBrandName   = DB::table('pos_product_brand')->select('id')->where('id',$product->brandId)->first();
		// $productModelName   = DB::table('pos_product_model')->select('id')->where('id',$product->modelId)->first();
		// $ProductSizeName    = DB::table('pos_product_size')->select('id')->where('id',$product->sizeId)->first();
		// $productColorName   = DB::table('pos_product_color')->select('id')->where('id',$product->colorId)->first();
		/*Product package Ritrive */

		// $productPackStr =  str_replace(array('"', '[', ']'),'', $product->productPackge);
		// $productPackIds = array_map('intval', explode(',', $productPackStr));

		$data = array(
			'product'             => $product,
			// 'groupName'           => $groupName,
			// 'categoryName'        => $categoryName,
			// 'subCategoryName'     => $subCategoryName,
			// 'productBrandName'    => $productBrandName,
			// 'productModelName'    => $productModelName,
			// 'ProductSizeName'     => $ProductSizeName,
			// 'productColorName'    => $productColorName,
			'slno'                => $req->slno,
			// 'productPackIds'      => $productPackIds,
		);
		return response()->json($data);
	}

	//edit function
	public function editItem(Request $req) {
		$rules = array(
			// 'name'                 => 'required|unique:pos_product,name,'.$req->id,
			// 'code'                 => 'required|unique:pos_product,code,'.$req->id,
			'name' =>[
	            'required',
	             Rule::unique('pos_product')->where('companyId', Auth::user()->company_id_fk).$req->id,
  			],
  			'code' =>[
	            'required',
	             Rule::unique('pos_product')->where('companyId', Auth::user()->company_id_fk).$req->id,
  			],
			// 'groupId'              => 'required',
			// 'categoryId'           => 'required',
			// 'subCategoryId'        => 'required',
			// 'brandId'              => 'required',
			// 'modelId'              => 'required',
			// 'sizeId'               => 'required',
			// 'colorId'              => 'required',
			// 'salesPrice'           => 'required',
			// 'costPrice'            => 'required',
			'type'				   => 'required|not_in:0',
			'unit'				   => 'required|not_in:0'

		);
		$attributeNames = array(
			'name'                     => 'Product Name',
			'code'                     => 'Product Code',
			// 'groupId'                  => 'Group Name',
			// 'categoryId'               => 'Category Name',
			// 'subCategoryId'            => 'Subcategory Name',
			// 'brandId'                  => 'Brand Name',
			// 'modelId'                  => 'Model Name',
			// 'sizeId'                   => 'Product Size',
			// 'colorId'                  => 'Product Color',
			// 'salesPrice'               => 'Seles Price',
			// 'costPrice'                => 'Cost Price',
			'type'					   => 'Type',
			'unit'					   => 'Unit'
		);

		$validator = Validator::make ( Input::all (), $rules);
		$validator->setAttributeNames($attributeNames);
		if ($validator->fails())
		return response::json(array('errors' => $validator->getMessageBag()->toArray()));
		else{

			$product = PosProduct::find ($req->id);
			$product->name                  = $req->name;
			$product->code                  = $req->code;
			// $product->description           = $req->description;
			// $product->groupId               = $req->groupId;
			// $product->categoryId            = $req->categoryId;
			// $product->subCategoryId         = $req->subCategoryId;
			// $product->brandId               = $req->brandId;
			// $product->modelId               = $req->modelId;
			// $product->sizeId                = $req->sizeId;
			// $product->colorId               = $req->colorId;
			// $product->costPrice             = $req->costPrice;
			// $product->salesPrice            = $req->salesPrice;
			$product->type            		= $req->type;
			$product->unit            		= $req->unit;
			// $product->productPackge         = json_encode($req->productPackage);

			$product->save();
			// $groupName          = DB::table('pos_product_group')->select('name')->where('id',$req->groupId)->first();
			// $categoryName       = DB::table('pos_product_category')->select('name')->where('id',$req->categoryId)->first();
			// $subCategoryName    = DB::table('pos_product_sub_category')->select('name')->where('id',$req->subCategoryId)->first();
			// $productBrandName   = DB::table('pos_product_brand')->select('name')->where('id',$req->brandId)->first();
			// $productModelName   = DB::table('pos_product_model')->select('name')->where('id',$req->modelId)->first();
			// $ProductSizeName    = DB::table('pos_product_size')->select('name')->where('id',$req->sizeId)->first();
			// $productColorName   = DB::table('pos_product_color')->select('name')->where('id',$req->colorId)->first();
			// $productName =DB::table('pos_product')->select('salesPrice')->get();

			$data = array(
				'product'                        =>$product,
				// 'groupName'                      => $groupName,
				// 'categoryName'                   => $categoryName,
				// 'subCategoryName'                => $subCategoryName,
				// 'productBrandName'               => $productBrandName,
				// 'productModelName'               => $productModelName,
				// 'ProductSizeName'                => $ProductSizeName,
				// 'productColorName'               => $productColorName,
				'slno'                           => $req->slno,
			);
			return response()->json($data);
		}
	}
	/*Product Details Function*/
	public function productDetails(Request $req){
		// dd($req->all());
		$productIds =  PosProduct::where('id',$req->id)->get();
		foreach ($productIds as $productId) {
			$productName        = $productId->name;
			$productCode        = $productId->code;
			$productIdNo        = $productId->id;
			if($productId->type == 'product') $productType = 'Product';
			else if($productId->type == 'hour') $productType = 'Hour';
			else $productType = 'Raw material';
			// $description        = $productId->description;

			// $groupName          = DB::table('pos_product_group')->select('name')->where('id',$productId->groupId)->first();
			// $categoryName       = DB::table('pos_product_category')->select('name')->where('id',$productId->categoryId)->first();
			// $subCategoryName    = DB::table('pos_product_sub_category')->select('name')->where('id',$productId->subCategoryId)->first();
			// $productBrandName   = DB::table('pos_product_brand')->select('name')->where('id',$productId->brandId)->first();
			// $productModelName   = DB::table('pos_product_model')->select('name')->where('id',$productId->modelId)->first();
			// $ProductSizeName    = DB::table('pos_product_size')->select('name')->where('id',$productId->sizeId)->first();
			// $productColorName   = DB::table('pos_product_color')->select('name')->where('id',$productId->colorId)->first();
			// $costPrice          = $productId->costPrice;
			// $salesPrice         = $productId->salesPrice;

			$units = config('constants.product_units');
			if($productId->unit != null) $productUnit = $units[$productId->unit];

			// $productUnit = $productId->unit;

			// $productPackageIds = explode(',',$productId->productPackge);
			// $productPackIds = str_replace(['"','[',']'],'',$productPackageIds);
			// $productPackageName = "";
			// foreach ($productPackIds as $key => $productPackId) {
			// 	if ($key>0) {
			// 		$productPackageName = $productPackageName . ' / ' . DB::table('pos_product')->where('id',$productPackId)->value('name');
			// 	}
			// 	else{
			// 		$productPackageName = DB::table('pos_product')->where('id',$productPackId)->value('name');
			// 	}
			// }
		}

		$data = array(
			'productName'                          => $productName,
			'productCode'                          => $productCode,
			'productIdNo'                          => $productIdNo,
			'productType'						   => $productType,
			'productUnit'						   => @$productUnit 
			// 'description'                          => $description,
			// 'groupName'                            => $groupName,
			// 'categoryName'                         => $categoryName,
			// 'subCategoryName'                      => $subCategoryName,
			// 'productBrandName'                     => $productBrandName,
			// 'productModelName'                     => $productModelName,
			// 'ProductSizeName'                      => $ProductSizeName,
			// 'productColorName'                     => $productColorName,
			// 'costPrice'                            => $costPrice,
			// 'salesPrice'                           => $salesPrice,
			// 'productPackageName'                   => $productPackageName,
		);
		return response()->json($data);
	}

	//delete function
	public function deleteItem(Request $req) {
		$imgName = PosProduct::find($req->id);
		$imgName->delete();
		$data = ['text' => 'Deleted successfully!'];
		return response()->json($data);
	}

}
