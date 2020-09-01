<?php

namespace App\Http\Controllers\inventory;

use Illuminate\Http\Request;

//use App\Http\Controllers\gnr\Service;
use App\Http\Controllers\gnr\Service;
use App\inventory\InvProduct;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class InvProductController extends Controller
{
  public function index(Request $request){

    $products = InvProduct::all();

    $groupId = array();
    $categoryId = array();
    $subCategoryId = array();
    $brandId = array();
    $modelId = array();
    $sizeId = array();
    $colorId = array();

    //Group
    if ($request->searchGroup==null) {
      $groupSelected = null;
      $groupId = DB::table('inv_product_group')->pluck('id');
    }
    else{
      $groupSelected = (int) json_decode($request->searchGroup);
      array_push($groupId, $groupSelected);
    }

      //Category
    if ($request->searchCategory==null) {
      $categorySelected = null;
      $categoryId = DB::table('inv_product_category')->pluck('id');
    }
    else{
      $categorySelected = (int) json_decode($request->searchCategory);
      array_push($categoryId, $categorySelected);
    }

       //Sub Category
    if ($request->searchSubCategory==null) {
     $subCategorySelected = null;
     $subCategoryId = DB::table('inv_product_sub_category')->pluck('id');
   }
   else{
    $subCategorySelected = (int) json_decode($request->searchSubCategory);
    array_push($subCategoryId, $subCategorySelected);
  }

      //Brand
  if ($request->searchBrand==null) {
    $brandSelected = null;
    $brandId = DB::table('inv_product_brand')->pluck('id');
  }
  else{
    $brandSelected = (int) json_decode($request->searchBrand);
    array_push($brandId, $brandSelected);
  }

      //Model
  if ($request->searchModel==null) {
    $modelSelected = null;
    $modelId = DB::table('inv_product_model')->pluck('id');
  }
  else{
    $modelSelected = (int) json_decode($request->searchModel);
    array_push($modelId, $modelSelected);
  }

      //Size
  if ($request->searchSize==null) {
    $sizeSelected = null;
    $sizeId = DB::table('inv_product_size')->pluck('id');
  }
  else{
    $sizeSelected = (int) json_decode($request->searchSize);
    array_push($sizeId, $sizeSelected);
  }

      //Color
  if ($request->searchColor==null) {
    $colorSelected = null;
    $colorId = DB::table('inv_product_color')->pluck('id');
  }
  else{
    $colorSelected = (int) json_decode($request->searchColor);
    array_push($colorId, $colorSelected);
  }

  $groups = DB::table('inv_product_group')->get();
  $categories = DB::table('inv_product_category')->whereIn('productGroupId',$groupId)->get();
  $subCategories = DB::table('inv_product_sub_category')->whereIn('productGroupId',$groupId)->get();
  $brands = DB::table('inv_product_brand')->whereIn('productGroupId',$groupId)->get();
  $models = DB::table('inv_product_model')->whereIn('productGroupId',$groupId)->get();

  $sizes = DB::table('inv_product_size')->whereIn('productGroupId',$groupId)->get();

  $colors = DB::table('inv_product_color')->whereIn('productGroupId',$groupId)->get();


  $products=DB::table('inv_product')->whereIn('groupId',$groupId)->whereIn('categoryId',$categoryId)->whereIn('subCategoryId',$subCategoryId)->whereIn('brandId',$brandId)->whereIn('modelId',$modelId)->whereIn('sizeId',$sizeId)->whereIn('colorId',$colorId)->get();



  return view('inventory/product/productSetting/product/viewProduct',['products' => $products,'groups'=>$groups,'categories'=>$categories,'subCategories'=>$subCategories,'brands'=>$brands,'models'=>$models,'sizes'=>$sizes,'colors'=>$colors,'groupSelected'=>$groupSelected,'categorySelected'=>$categorySelected,'subCategorySelected'=>$subCategorySelected,'brandSelected'=>$brandSelected,'modelSelected'=>$modelSelected,'sizeSelected'=>$sizeSelected,'colorSelected'=>$colorSelected]);
} 


public function onChangeGroup(Request $request) {

  $productGroupId = (int)json_decode($request->productGroupId);
  if($request->productGroupId==""){
   $categories =  DB::table('inv_product_category')->select('id','name')->get();
   $subCategories =  DB::table('inv_product_sub_category')->select('id','name')->get();

 }
 else{

  $categories =  DB::table('inv_product_category')->where('productGroupId',$productGroupId)->select('id','name')->get();

  $subCategories =  DB::table('inv_product_sub_category')->where('productGroupId',$productGroupId)->select('id','name')->get();
}

$data = array(
  'categories'    => $categories,        
  'subCategories' => $subCategories

);
return response()->json($data);
}

public function onChangeCategory(Request $request) {

  $productCategoryId = (int)json_decode($request->productCategoryId);
  if($request->productCategoryId==""){
   $subCategories =  DB::table('inv_product_sub_category')->select('id','name')->get();
 }
 else{
  $subCategories =  DB::table('inv_product_sub_category')->where('productCategoryId',$productCategoryId)->select('id','name')->get();

}

$data = array(            
  'subCategories' => $subCategories,

);
return response()->json($data);
}

public function onBrandCategory(Request $request) {

  $productBrandId = (int)json_decode($request->productBrandId);

  if($request->productBrandId==""){
   $brands =  DB::table('inv_product_model')->select('id','name')->get();
 }
 else{
  $brands =  DB::table('inv_product_model')->where('productBrandId',$productBrandId)->select('id','name')->get();
}

$data = array(            
  'brands' => $brands
);
return response()->json($data);
}

public function addProduct(){
  return view('inventory/product/productSetting/product/addProduct');

}
//insert function
public function addItem(Request $req){
 // dd();

  $rules = array(
    'name' => 'required|unique:inv_product',
    'supplierId' => 'required',
    'groupId' => 'required',
    'categoryId' => 'required',
    'subCategoryId' => 'required',
    'brandId' => 'required',
    'modelId' => 'required',
    'sizeId' => 'required',
    'colorId' => 'required',
    'uomId' => 'required'
                /*'costPrice' => 'required',
                'salesPrice' => 'required'*/
              );
  $attributeNames = array(
    'name' => 'Product Name',
    'supplierId' => 'Supplier Name',
    'groupId' => 'Group Name',
    'categoryId' => 'Category Name',
    'subCategoryId' => 'Subcategory Name',
    'brandId' => 'Brand Name',
    'modelId' => 'Model Name',
    'sizeId' => 'Product Size',
    'colorId' => 'Product Color',
    'uomId' => 'Uom Name'
            /*'costPrice' => 'Product Cost Price',
            'salesPrice' => 'Product Sales Price'   */
          );
  $validator = Validator::make ( Input::all (), $rules);
  $validator->setAttributeNames($attributeNames);
  if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{

    if ($req->file('image')) {
      $productImageId = InvProduct::max('id')+1;
      $file = $req->file('image');
      $filename = str_random(10).$productImageId.'.' . $file->getClientOriginalExtension();
      $destinationPath = base_path() . '/public/images/product/';
      $file->move($destinationPath,$filename);
    } 

    $now = Carbon::now();
    $req->request->add(['createdDate' => $now]);
    $create = $req->all();
    if($req->file('image')){
      $create['image'] = $filename;
    }
    InvProduct::create($create);
    $logArray = array(
      'moduleId'  => 1,
      'controllerName'  => 'InvProductController',
      'tableName'  => 'inv_product',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('inv_product')->max('id')]
    );
    Service::createLog($logArray);

    //$serviveInfo=Service::createLog($logArray);
    //dd($serviveInfo);
    return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
  }
}

//edit function
public function editItem(Request $req) {
  $rules = array(
    'name' => 'required|unique:inv_product,name,'.$req->id,
    'supplierId' => 'required',
    'groupId' => 'required',
    'categoryId' => 'required',
    'subCategoryId' => 'required',
    'brandId' => 'required',
    'modelId' => 'required',
    'sizeId' => 'required',
    'colorId' => 'required',
    'uomId' => 'required'
                /*'costPrice' => 'required',
                'salesPrice' => 'required'*/
              );
  $attributeNames = array(
    'name' => 'Product Name',
    'supplierId' => 'Supplier Name',
    'groupId' => 'Group Name',
    'categoryId' => 'Category Name',
    'subCategoryId' => 'Subcategory Name',
    'brandId' => 'Brand Name',
    'modelId' => 'Model Name',
    'sizeId' => 'Product Size',
    'colorId' => 'Product Color',
    'uomId' => 'Uom Name'
            /*'costPrice' => 'Product Cost Price',
            'salesPrice' => 'Product Sales Price'   */
          );
  $validator = Validator::make ( Input::all (), $rules);
  $validator->setAttributeNames($attributeNames);
  if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
    if ($req->file('image')) {
      $productImageId = InvProduct::max('id')+1;
      $file = $req->file('image');
        //$filename = $file->getClientOriginalName();
      $filename = str_random(10).$productImageId.'.' . $file->getClientOriginalExtension();
      $destinationPath = base_path() . '/public/images/product/';
      $file->move($destinationPath,$filename);
    }
    $previousdata = InvProduct::find ($req->id);

    $product = InvProduct::find ($req->id);
    $product->name = $req->name;
    $product->description = $req->description;
    $product->supplierId = $req->supplierId;
    $product->groupId = $req->groupId;
    $product->categoryId = $req->categoryId;
    $product->subCategoryId = $req->subCategoryId;
    $product->brandId = $req->brandId;
    $product->modelId = $req->modelId;
    $product->sizeId = $req->sizeId;
    $product->colorId = $req->colorId;
    $product->uomId = $req->uomId;
    $product->vat = $req->vat;
    $product->barcode = $req->barcode;
    $product->systemBarcode = $req->systemBarcode;
    $product->warranty = $req->warranty;
    $product->serviceWarranty = $req->serviceWarranty;
    $product->compresserWarranty = $req->compresserWarranty;
    $product->costPrice = $req->costPrice;
    $product->salesPrice = $req->salesPrice;
    $product->openingStock = $req->openingStock;
    $product->minimumStock = $req->minimumStock;
    $product->openingStockAmount = $req->openingStockAmount;
    if($req->file('image')){
      $product->image = $filename;
    }
    $product->save();

    $supplierName       = DB::table('gnr_supplier')->select('supplierCompanyName')->where('id',$req->supplierId)->first();
    $groupName          = DB::table('inv_product_group')->select('name')->where('id',$req->groupId)->first();
    $categoryName       = DB::table('inv_product_category')->select('name')->where('id',$req->categoryId)->first();
    $subCategoryName    = DB::table('inv_product_sub_category')->select('name')->where('id',$req->subCategoryId)->first();    
    $productBrandName   = DB::table('inv_product_brand')->select('name')->where('id',$req->brandId)->first();
    $productModelName   = DB::table('inv_product_model')->select('name')->where('id',$req->modelId)->first();
    $ProductSizeName    = DB::table('inv_product_size')->select('name')->where('id',$req->sizeId)->first();
    $productColorName   = DB::table('inv_product_color')->select('name')->where('id',$req->colorId)->first();
    $productUomName     = DB::table('inv_product_uom')->select('name')->where('id',$req->uomId)->first();

    $data = array(
      'product'             =>$product,
      'supplierName'        => $supplierName,
      'groupName'           => $groupName,
      'categoryName'        => $categoryName,
      'subCategoryName'     => $subCategoryName,
      'productBrandName'    => $productBrandName,   
      'productModelName'    => $productModelName,
      'ProductSizeName'     => $ProductSizeName,
      'productColorName'    => $productColorName,
      'productUomName'      => $productUomName,
      'slno'                => $req->slno
    );
    $logArray = array(
      'moduleId'  => 1,
      'controllerName'  => 'InvProductController',
      'tableName'  => 'inv_product',
      'operation'  => 'update',
      'previousData'  => $previousdata,
      'primaryIds'  => [$previousdata->id]
    );
    Service::createLog($logArray);
    return response()->json($data);
  }
}

public function productDetails(Request $req){
  $productIds =  InvProduct::where('id',$req->id)->get();
  foreach ($productIds as $productId) {
    $productName        = $productId->name;
    $productIdNo        = $productId->id;
    $description        = $productId->description;
    $supplierName       = DB::table('gnr_supplier')->select('supplierCompanyName')->where('id',$productId->supplierId)->first();
    $groupName          = DB::table('inv_product_group')->select('name')->where('id',$productId->groupId)->first();
    $categoryName       = DB::table('inv_product_category')->select('name')->where('id',$productId->categoryId)->first();
    $subCategoryName    = DB::table('inv_product_sub_category')->select('name')->where('id',$productId->subCategoryId)->first();    
    $productBrandName   = DB::table('inv_product_brand')->select('name')->where('id',$productId->brandId)->first();
    $productModelName   = DB::table('inv_product_model')->select('name')->where('id',$productId->modelId)->first();
    $ProductSizeName    = DB::table('inv_product_size')->select('name')->where('id',$productId->sizeId)->first();
    $productColorName   = DB::table('inv_product_color')->select('name')->where('id',$productId->colorId)->first();
    $productUomName     = DB::table('inv_product_uom')->select('name')->where('id',$productId->uomId)->first();
    $costPrice          = $productId->costPrice;
    $salesPrice         = $productId->salesPrice;
    $openingStock       = $productId->openingStock;
    $openingStockAmount = $productId->openingStockAmount;
    $minimumStock       = $productId->minimumStock;
    $vat                = $productId->vat;
    $barcode            = $productId->barcode;
    $systemBarcode      = $productId->systemBarcode;
    $warranty           = $productId->warranty;
    $serviceWarranty    = $productId->serviceWarranty;
    $compresserWarranty = $productId->compresserWarranty;
    $imageName          = $productId->image;
  }

  $data = array(
    'productName'         => $productName,
    'productIdNo'         => $productIdNo,
    'description'         => $description,   
    'supplierName'        => $supplierName,
    'groupName'           => $groupName,
    'categoryName'        => $categoryName,
    'subCategoryName'     => $subCategoryName,
    'productBrandName'    => $productBrandName,   
    'productModelName'    => $productModelName,
    'ProductSizeName'     => $ProductSizeName,
    'productColorName'    => $productColorName,
    'productUomName'      => $productUomName,
    'costPrice'           => $costPrice,   
    'salesPrice'          => $salesPrice,
    'openingStock'        => $openingStock,
    'openingStockAmount'  => $openingStockAmount,
    'minimumStock'        => $minimumStock,
    'vat'                 => $vat,   
    'barcode'             => $barcode,
    'systemBarcode'       => $systemBarcode,
    'warranty'            => $warranty,
    'serviceWarranty'     => $serviceWarranty,
    'compresserWarranty'  => $compresserWarranty,
    'imageName'           => $imageName
  );
  return response()->json($data);
} 

    //delete
public function deleteItem(Request $req) {
  $imgName = InvProduct::select('image')->where('id',$req->id)->first();
  $previousdata=InvProduct::find($req->id);
  InvProduct::find($req->id)->delete();
  $logArray = array(
    'moduleId'  => 1,
    'controllerName'  => 'InvProductController',
    'tableName'  => 'inv_product',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);
  return response()->json($imgName);
}

public function ProimageDelete(Request $req) {
  $image = "{$req->replacedImage1}";
  File::delete($image);
  return response()->json($image);
}



}


