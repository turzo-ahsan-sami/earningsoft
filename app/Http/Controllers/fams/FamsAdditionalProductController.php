<?php

namespace App\Http\Controllers\fams;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\fams\FamsAdditionalProduct as Product;
use App\fams\FamsPcategory as Category;
use App\Http\Controllers\gnr\Service;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\fams\FamsProduct;



class FamsAdditionalProductController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        $products = Product::all();
        
        return view('fams/product/productSetting/additionalProduct/viewAdditionalProduct', ['products' => $products,'categories'=>$categories]);
    }

    public function addProduct()
    {
        $categories = Category::all();
        return view('fams/product/productSetting/additionalProduct/addAdditionalProduct',['categories'=>$categories]);
    }

//insert function
   /* public function storeProduct(Request $req)
    {
        $rules = array(
            'name' => 'required| unique:fams_additional_product,name',
            'subCategoryId' => 'required'
            );

        $attributeNames = array(
            'name' => 'Product Name',
            'subCategoryId' => 'Sub Category'
            );
        

        return redirect('famsAdditionalProduct');
    }*/

    /*This method is called when product is stored with ajax post*/
    public function ajaxStoreProduct(Request $req)
    {
        $rules = array(
            'name' => 'required| unique:fams_additional_product,name',
            'categoryId' => 'required'
        );

        $attributeNames = array(
            'name' => 'Product Name',
            'categoryId' => 'Category'
        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails())
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        else{

            $product = new Product;
            $product->name = $req->name;
            $product->categoryId = $req->categoryId;
            $product->save();
            $logArray = array(
              'moduleId'  => 2,
              'controllerName'  => 'FamsAdditionalProductController',
              'tableName'  => 'fams_additional_product',
              'operation'  => 'insert',
              'primaryIds'  => [DB::table('fams_additional_product')->max('id')]
          );
            Service::createLog($logArray);

            return response()->json("success");

        }


        
    }

//edit function
    public function editProduct(Request $req) {


        //$productId = (int)json_decode($req->id);

        $rules = array(
            'name' => 'required| unique:fams_additional_product,name,'.$req->id,
            'categoryId' => 'required'
        );

        $attributeNames = array(
            'name' => 'Product Name',
            'categoryId' => 'Category'
        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails())
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        else{

            $previousdata = Product::find($req->id);
            $product = Product::find($req->id);
            $product->name = $req->name;
            $product->categoryId = $req->categoryId;
            $product->save();
            $logArray = array(
              'moduleId'  => 2,
              'controllerName'  => 'FamsAdditionalProductController',
              'tableName'  => 'fams_additional_product',
              'operation'  => 'update',
              'previousData'  => $previousdata,
              'primaryIds'  => [$previousdata->id]
          );
            Service::createLog($logArray);

            return response()->json("success");

        }



        
    }

    //delete
    public function deleteProduct(Request $req) {
     $previousdata=Product::find($req->productId);
     Product::find($req->productId)->delete();
     $logArray = array(
        'moduleId'  => 2,
        'controllerName'  => 'FamsAdditionalProductController',
        'tableName'  => 'fams_additional_product',
        'operation'  => 'delete',
        'previousData'  => $previousdata,
        'primaryIds'  => [$previousdata->id]
    );
     Service::createLog($logArray);
     
     return redirect('famsAdditionalProduct');
 }



 public function ProimageDelete(Request $req) {
    $image = "{$req->replacedImage1}";
    \File::delete($image);
    return response()->json($image);
}

public function onChangeGroup(Request $request){
    if ($request->productGroupId=="") {
        $productCategoryList =  DB::table('fams_product_category')->pluck('id','name');
        $productSubCategoryList =  DB::table('fams_product_sub_category')->pluck('id','name');
            /*$productBrandList =  DB::table('fams_product_brand')->pluck('id','name');
            $productModelList =  DB::table('fams_product_model')->pluck('id','name');
        }

        else{

            $productCategoryList =  DB::table('fams_product_category')->where('productGroupId',(int) json_decode($request->productGroupId))->pluck('id','name');

            $productSubCategoryList =  DB::table('fams_product_sub_category')->where('productGroupId',(int) json_decode($request->productGroupId))->pluck('id','name');

            /*$productBrandList =  DB::table('fams_product_brand')->where('productGroupId',(int) json_decode($request->productGroupId))->pluck('id','name');*/
            $productModelList =  DB::table('fams_product_model')->where('productGroupId', (int) json_decode($request->productGroupId))->pluck('id','name');

        }


        $data = array(
            'productCategoryList' => $productCategoryList,
            'productSubCategoryList' => $productSubCategoryList,
            /*'productBrandList' => $productBrandList,*/
            'productModelList' => $productModelList
        );

        return response()->json($data);
    }

    public function onChangeCategory(Request $request){
        if ($request->productGroupId=="" && $request->productCategoryId == "") {

            $productSubCategoryList =  DB::table('fams_product_sub_category')->pluck('id','name');
            /*$productBrandList =  DB::table('fams_product_brand')->pluck('id','name');*/
            $productModelList =  DB::table('fams_product_model')->pluck('id','name');
        }

        elseif ($request->productGroupId !="" && $request->productCategoryId == "") {
            $productSubCategoryList =  DB::table('fams_product_sub_category')->where('productGroupId',(int)json_decode($request->productGroupId))->pluck('id','name');
            /*$productBrandList =  DB::table('fams_product_brand')->where('productGroupId',(int)json_decode($request->productGroupId))->pluck('id','name');*/
            $productModelList =  DB::table('fams_product_model')->where('productGroupId',(int)json_decode($request->productGroupId))->pluck('id','name');
        }

        elseif($request->productGroupId =="" && $request->productCategoryId != ""){

            $productSubCategoryList =  DB::table('fams_product_sub_category')->where('productCategoryId',(int)json_decode($request->productCategoryId))->pluck('id','name');
            /*$productBrandList =  DB::table('fams_product_brand')->where('productCategoryId',(int)json_decode($request->productCategoryId))->pluck('id','name');*/
            $productModelList =  DB::table('fams_product_model')->where('productCategoryId',(int)json_decode($request->productCategoryId))->pluck('id','name');
        }

        else{

            $productSubCategoryList =  DB::table('fams_product_sub_category')->where([['productGroupId',(int)json_decode($request->productGroupId)],['productCategoryId',(int)json_decode($request->productCategoryId)]])->pluck('id','name');

            /*$productBrandList =  DB::table('fams_product_brand')->where([['productGroupId',(int)json_decode($request->productGroupId)],['productCategoryId',(int)json_decode($request->productCategoryId)]])->pluck('id','name');*/

            $productModelList =  DB::table('fams_product_model')->where([['productGroupId',(int)json_decode($request->productGroupId)],['productCategoryId',(int)json_decode($request->productCategoryId)]])->pluck('id','name');

        }

        $data = array(
            'productSubCategoryList' => $productSubCategoryList,
            /*'productBrandList' => $productBrandList,*/
            'productModelList'         => $productModelList
        );
        return response()->json($data);
    }


    public function onChangeSubCategory(Request $request){


        if ($request->productGroupId=="" && $request->productCategoryId == "" && $request->productSubproductCategoryId == "") {
            /*$productBrandList =  DB::table('fams_product_brand')->pluck('id','name');*/
            $productModelList =  DB::table('fams_product_model')->pluck('id','name');
        }


        elseif ($request->productGroupId=="" && $request->productCategoryId == "" && $request->productSubCategoryId != "") {

            /*$productBrandList =  DB::table('fams_product_brand')
                ->where('productSubCategoryId',(int)json_decode($request->productSubCategoryId))
                ->pluck('id','name');*/

                $productModelList =  DB::table('fams_product_model')
                ->where('productSubCategoryId',(int)json_decode($request->productSubCategoryId))
                ->pluck('id','name');
            }

            elseif ($request->productGroupId=="" && $request->productCategoryId != "" && $request->productSubCategoryId == "") {

            /*$productBrandList =  DB::table('fams_product_brand')
                ->where('productCategoryId',(int)json_decode($request->productCategoryId))
                ->pluck('id','name');*/

                $productModelList =  DB::table('fams_product_model')
                ->where('productCategoryId',(int)json_decode($request->productCategoryId))
                ->pluck('id','name');
            }

            elseif ($request->productGroupId=="" && $request->productCategoryId != "" && $request->productSubCategoryId != "") {

            /*$productBrandList =  DB::table('fams_product_brand')
                ->where([['productCategoryId',(int)json_decode($request->productCategoryId)],['productSubCategoryId',(int)json_decode($request->productSubCategoryId)]])
                ->pluck('id','name');*/

                $productModelList =  DB::table('fams_product_model')
                ->where([['productCategoryId',(int)json_decode($request->productCategoryId)],['productSubCategoryId',(int)json_decode($request->productSubCategoryId)]])
                ->pluck('id','name');
            }

            elseif ($request->productGroupId!="" && $request->productCategoryId == "" && $request->productSubCategoryId == "") {

            /*$productBrandList =  DB::table('fams_product_brand')
                ->where('productGroupId',(int)json_decode($request->productGroupId))
                ->pluck('id','name');*/

                $productModelList =  DB::table('fams_product_model')
                ->where('productGroupId',(int)json_decode($request->productGroupId))
                ->pluck('id','name');
            }

            elseif ($request->productGroupId!="" && $request->productCategoryId == "" && $request->productSubCategoryId != "") {

            /*$productBrandList =  DB::table('fams_product_brand')
                ->where([['productGroupId',(int)json_decode($request->productGroupId)],['productSubCategoryId',(int)json_decode($request->productSubCategoryId)]])
                ->pluck('id','name');*/

                $productModelList =  DB::table('fams_product_model')
                ->where([['productGroupId',(int)json_decode($request->productGroupId)],['productSubCategoryId',(int)json_decode($request->productSubCategoryId)]])
                ->pluck('id','name');
            }

            elseif ($request->productGroupId!="" && $request->productCategoryId != "" && $request->productSubCategoryId == "") {

            /*$productBrandList =  DB::table('fams_product_brand')
                ->where([['productGroupId',(int)json_decode($request->productGroupId)],['productCategoryId',(int)json_decode($request->productCategoryId)]])
                ->pluck('id','name');*/

                $productModelList =  DB::table('fams_product_model')
                ->where([['productGroupId',(int)json_decode($request->productGroupId)],['productCategoryId',(int)json_decode($request->productCategoryId)]])
                ->pluck('id','name');
            }

            elseif ($request->productGroupId!="" && $request->productCategoryId != "" && $request->productSubCategoryId != "") {

            /*$productBrandList =  DB::table('fams_product_brand')
                ->where([['productGroupId',(int)json_decode($request->productGroupId)],['productCategoryId',(int)json_decode($request->productCategoryId)],['productSubCategoryId',(int)json_decode($request->productSubCategoryId)]])
                ->pluck('id','name');*/

                $productModelList =  DB::table('fams_product_model')
                ->where([['productGroupId',(int)json_decode($request->productGroupId)],['productCategoryId',(int)json_decode($request->productCategoryId)],['productSubCategoryId',(int)json_decode($request->productSubCategoryId)]])
                ->pluck('id','name');
            }


            $data = array(
                /*'productBrandList' => $productBrandList,*/
                'productModelList'         => $productModelList
            );
            return response()->json($data);
        }



        function onChangeBrand(Request $request){

            if ($request->productGroupId =="" && $request->productCategoryId == "" && $request->productSubCategoryId == "" && $request->productBrandId =="") {

                $productModelList =  DB::table('fams_product_model')->pluck('id','name');
            }

            elseif($request->productGroupId =="" && $request->productCategoryId == "" && $request->productSubCategoryId == "" && $request->productBrandId !=""){

                $productModelList =  DB::table('fams_product_model')
                ->where('productBrandId',(int)json_decode($request->productBrandId))
                ->pluck('id','name');

            }

            elseif($request->productGroupId =="" && $request->productCategoryId == "" && $request->productSubCategoryId != "" && $request->productBrandId ==""){

                $productModelList =  DB::table('fams_product_model')
                ->where('productSubCategoryId',(int)json_decode($request->productSubCategoryId))
                ->pluck('id','name');
            }

            elseif($request->productGroupId =="" && $request->productCategoryId == "" && $request->productSubCategoryId != "" && $request->productBrandId !=""){

                $productModelList =  DB::table('fams_product_model')
                ->where([['productSubCategoryId',(int)json_decode($request->productSubCategoryId)],['productBrandId',(int)json_decode($request->productBrandId)]])
                ->pluck('id','name');

            }
            elseif($request->productGroupId =="" && $request->productCategoryId != "" && $request->productSubCategoryId == "" && $request->productBrandId ==""){

                $productModelList =  DB::table('fams_product_model')
                ->where('productCategoryId',(int)json_decode($request->productCategoryId))
                ->pluck('id','name');

            }

            elseif($request->productGroupId =="" && $request->productCategoryId != "" && $request->productSubCategoryId == "" && $request->productBrandId !=""){

                $productModelList =  DB::table('fams_product_model')
                ->where([['productCategoryId',(int)json_decode($request->productCategoryId)],['productBrandId',(int)json_decode($request->productBrandId)]])
                ->pluck('id','name');

            }

            elseif($request->productGroupId =="" && $request->productCategoryId != "" && $request->productSubCategoryId != "" && $request->productBrandId ==""){

                $productModelList =  DB::table('fams_product_model')
                ->where([['productCategoryId',(int)json_decode($request->productCategoryId)],['productSubCategoryId',(int)json_decode($request->productSubCategoryId)]])
                ->pluck('id','name');

            }

            elseif($request->productGroupId =="" && $request->productCategoryId != "" && $request->productSubCategoryId != "" && $request->productBrandId !=""){

                $productModelList =  DB::table('fams_product_model')
                ->where([['productCategoryId',(int)json_decode($request->productCategoryId)],['productSubCategoryId',(int)json_decode($request->productSubCategoryId)],['productBrandId',(int)json_decode($request->productBrandId)]])
                ->pluck('id','name');

            }

            elseif($request->productGroupId !="" && $request->productCategoryId == "" && $request->productSubCategoryId == "" && $request->productBrandId ==""){

                $productModelList =  DB::table('fams_product_model')
                ->where('productGroupId',(int)json_decode($request->productGroupId))
                ->pluck('id','name');

            }

            elseif($request->productGroupId !="" && $request->productCategoryId == "" && $request->productSubCategoryId == "" && $request->productBrandId !=""){

                $productModelList =  DB::table('fams_product_model')
                ->where([['productGroupId',(int)json_decode($request->productGroupId)],['productBrandId',(int)json_decode($request->productBrandId)]])
                ->pluck('id','name');

            }

            elseif($request->productGroupId !="" && $request->productCategoryId == "" && $request->productSubCategoryId != "" && $request->productBrandId ==""){

                $productModelList =  DB::table('fams_product_model')
                ->where([['productGroupId',(int)json_decode($request->productGroupId)],['productSubCategoryId',(int)json_decode($request->productSubCategoryId)]])
                ->pluck('id','name');

            }

            elseif($request->productGroupId !="" && $request->productCategoryId == "" && $request->productSubCategoryId != "" && $request->productBrandId !=""){

                $productModelList =  DB::table('fams_product_model')
                ->where([['productGroupId',(int)json_decode($request->productGroupId)],['productSubCategoryId',(int)json_decode($request->productSubCategoryId)],['productBrandId',(int)json_decode($request->productBrandId)]])
                ->pluck('id','name');

            }

            elseif($request->productGroupId !="" && $request->productCategoryId != "" && $request->productSubCategoryId == "" && $request->productBrandId ==""){

                $productModelList =  DB::table('fams_product_model')
                ->where([['productGroupId',(int)json_decode($request->productGroupId)],['productCategoryId',(int)json_decode($request->productCategoryId)]])
                ->pluck('id','name');

            }

            elseif($request->productGroupId !="" && $request->productCategoryId != "" && $request->productSubCategoryId == "" && $request->productBrandId !=""){

                $productModelList =  DB::table('fams_product_model')
                ->where([['productGroupId',(int)json_decode($request->productGroupId)],['productCategoryId',(int)json_decode($request->productCategoryId)],['productBrandId',(int)json_decode($request->productBrandId)]])
                ->pluck('id','name');

            }

            elseif($request->productGroupId !="" && $request->productCategoryId != "" && $request->productSubCategoryId != "" && $request->productBrandId ==""){

                $productModelList =  DB::table('fams_product_model')
                ->where([['productGroupId',(int)json_decode($request->productGroupId)],['productCategoryId',(int)json_decode($request->productCategoryId)],['productSubCategoryId',(int)json_decode($request->productSubCategoryId)]])
                ->pluck('id','name');

            }

            elseif($request->productGroupId !="" && $request->productCategoryId != "" && $request->productSubCategoryId != "" && $request->productBrandId !=""){

                $productModelList =  DB::table('fams_product_model')
                ->where([['productGroupId',(int)json_decode($request->productGroupId)],['productCategoryId',(int)json_decode($request->productCategoryId)],['productSubCategoryId',(int)json_decode($request->productSubCategoryId)],['productBrandId',(int)json_decode($request->productBrandId)]])
                ->pluck('id','name');

            }

            $data = array(
                'productModelList'         => $productModelList
            );
            return response()->json($data);
        }


        public function validateStep1(Request $request){
            $rules = array(
                'supplierId' => 'required',
                'productGroupId' => 'required',
                'categoryId' => 'required',
                'subCategoryId' => 'required',
                'brandId' => 'required',
                'modelId' => 'required',
                'sizeId' => 'required',
                'colorId' => 'required',
                'uomId' => 'required',
                'projectId' => 'required',
                'branchId' => 'required',
                'item' => 'required',
                'purchaseDate' => 'required',
                'warranty' => 'required',
                'serviceWarranty' => 'required'
            );
            $attributeNames = array(
                'supplierId' => 'Supplier Name',
                'productGroupId' => 'Group Name',
                'categoryId' => 'Category Name',
                'subCategoryId' => 'Subcategory Name',
                'brandId' => 'Brand Name',
                'modelId' => 'Model Name',
                'sizeId' => 'Product Size',
                'colorId' => 'Product Color',
                'uomId' => 'Uom Name',
                'costPrice' => 'Product Cost Price',
                'projectId' => 'Product Sales Price',
                'item' => 'Product Sales Price',
                'warranty' => 'Product Sales Price',
                'serviceWarranty' => 'Product Sales Price',
            );

            $validator = Validator::make ( Input::all (), $rules);
            $validator->setAttributeNames($attributeNames);
            if ($validator->fails())
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else{
                return response::json("Success");
            }

        }

    /*public function validateStep3(Request $request){
        $rules = array(
            'productImage' => 'max:512',
            'warrantyImage' => 'max:512',
            'billImage' => 'max:512',
            'additionalVoucherImage' => 'max:512'
        );
        $attributeNames = array(
            'productImage' => 'Product Image',
            'warrantyImage' => 'Warranty Image',
            'billImage' => 'Bill Image',
            'additionalVoucherImage' => 'Additional Voucher Image'
        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails())
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        else{
            return response::json("Success");
        }

    }*/

    public function famsGetInfo(Request $request){
        if($request->key=="AmbalaAsset"){
            $productAssetNo = DB::table('fams_product')->max('assetNo')+1;
            $formatedData = str_pad( $productAssetNo, 4, "0", STR_PAD_LEFT );
            return response()->json($formatedData);
        }

        if($request->key=="project"){
            $projectId = $request->projectId;
            $projectCode = DB::table('gnr_project')->where('id',$projectId)->value('projectCode');
            $formatedData = str_pad( $projectCode, 2, "0", STR_PAD_LEFT );
            return response()->json($formatedData);
        }

        if($request->key=="projectType"){
            $projectType = $request->projectType;
            $projectTypeCode = DB::table('gnr_project_type')->where('id',$projectType)->value('projectTypeCode');
            $formatedData = str_pad( $projectTypeCode, 2, "0", STR_PAD_LEFT );
            return response()->json($formatedData);
        }

        if($request->key=="projectAsset"){
            $projectId = $request->projectId;
            $projectType = $request->projectType;
            $productProjectAssetNo = DB::table('fams_product')->where([['projectId',$projectId],['projectTypeId',$projectType]])->max('projectAssetNo')+1;
            $formatedData = str_pad( $productProjectAssetNo, 4, "0", STR_PAD_LEFT );
            return response()->json($formatedData);
        }

        if($request->key=="branch"){
            $branchId = $request->branchId;
            $branchCode = DB::table('gnr_branch')->where('id',$branchId)->value('branchCode');
            $formatedBranchData = str_pad( $branchCode, 4, "0", STR_PAD_LEFT );
            $productBranchAssetNo = DB::table('fams_product')->where([['branchId',$branchId]])->max('branchAssetNo')+1;
            $formatedBranchAssetData = str_pad( $productBranchAssetNo, 4, "0", STR_PAD_LEFT );
            $data = array(
                'branch' => $formatedBranchData,
                'assetNo' => $formatedBranchAssetData
            );

            return response()->json($data);
        }

        if($request->key=="group"){
            $productGroupId = $request->productGroupId;
            $productGroupCode = DB::table('fams_product_group')->where('id',$productGroupId)->value('groupCode');
            $formatedGroupCodeData = str_pad( $productGroupCode , 2, "0", STR_PAD_LEFT );

            /*$productItemAssetNo = DB::table('fams_product')->where([['itemTypeId',$itemId]])->max('itemTypeAssetNo')+1;
            $formatedproductItemAssetData = str_pad( $productItemAssetNo, 4, "0", STR_PAD_LEFT );*/
            $data = array(
                'groupCode' => $formatedGroupCodeData
            );
            return response()->json($data);
        }

        if($request->key=="subCategory"){
            $productSubCategoryId = $request->productSubCategoryId;
            $productSubCategoryCode = DB::table('fams_product_sub_category')->where('id',$productSubCategoryId)->value('subCategoryCode');
            $formatedProductSubCategoryCode = str_pad( $productSubCategoryCode , 2, "0", STR_PAD_LEFT );

            $subCategoryAssetNo = DB::table('fams_product')->where([['subCategoryId',$productSubCategoryId]])->max('subCategoryAssetNo')+1;
            $formatedSubCategoryAssetNo = str_pad( $subCategoryAssetNo, 4, "0", STR_PAD_LEFT );
            $data = array(
                'subCategoryCode' => $formatedProductSubCategoryCode,
                'assetNo' => $formatedSubCategoryAssetNo
            );
            return response()->json($data);
        }

        /*$productCategoryId = $request->productCategoryId;
        $productBrandId = $request->productBrandId;
        $projectId = $request->projectId;
        $projectType = $request->projectType;
        $branchId = $request->branchId;

        $branchCode = DB::table('gnr_branch')->where('id',$branchId)->value('branchCode');
        $projectCode = DB::table('gnr_project')->where('id',$projectId)->value('projectCode');
        $projectTypeCode = DB::table('gnr_project_type')->where('id',$projectType)->value('projectTypeCode');
        $categoryCode = DB::table('fams_product_category')->where('id',$productCategoryId)->value('categoryCode');

        $productAssetNo = DB::table('fams_product')->max('id')+1;
        $productProjectAssetNo = DB::table('fams_product')->where([['projectId',$projectId],['projectTypeId',$projectType]])->max('id')+1;
        $productBranchAssetNo = DB::table('fams_product')->where([['branchId',$branchId]])->max('id')+1;
        $productCategoryAssetNo = DB::table('fams_product')->where([['categoryId',$productCategoryId]])->max('id')+1;

        $data = array(
            'productAssetNo' => $productAssetNo,
            'projectCode' => $projectCode,
            'projectTypeCode' => $projectTypeCode,
            'productProjectAssetNo' => $productProjectAssetNo,
            'branchCode' => $branchCode,
            'productBranchAssetNo' => $productBranchAssetNo,
            'categoryCode' => $categoryCode,
            'productCategoryAssetNo' => $productCategoryAssetNo
        );

        return response()->json($data);*/
    }

    public function validateStep2(Request $request){

        $rules = array(
            'costPrice' => 'required',
            'usefulLifeYear' => 'required_without_all:usefulLifeMonth',
            'usefulLifeMonth' => 'required_without_all:usefulLifeYear',
            'productQuantity' => 'required',
            'resellValue' => 'required'
        );
        /* $attributeNames = array(
             'costPrice' => 'Cost Price',
             'usefulLife' => 'Useful Life'
         );*/

         $validator = Validator::make ( Input::all (), $rules);
        //$validator->setAttributeNames($attributeNames);
         if ($validator->fails())
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        //return response::json("Required");
        else{
            return response::json("Success");
        }
    }

    

}


