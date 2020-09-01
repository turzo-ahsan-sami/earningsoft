<?php

namespace App\Http\Controllers\fams;

use App\Http\Controllers\Controller;
use App\fams\FamsAdditionalCharge as FAC;
use App\fams\FamsAdditionalChargeDetails as FACD;
use App\fams\FamsProduct as Product;
use App\fams\FamsAdditionalProduct;
use App\gnr\GnrBranch;
use App\Http\Controllers\gnr\Service;
use App\fams\FamsPgroup;
use App\fams\FamsPsubcategory;
use App\fams\FamsPcategory;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class FamsTransactionAdditionalChargeController extends Controller
{
    public function index(){
    	
        $additionalCharges = FAC::all();
        $additionalChargeDetails = FACD::all();

        $productGroups = FamsPgroup::all();
        $branches = GnrBranch::all();
        $productSubCategories = FamsPsubcategory::all();
        $branches = GnrBranch::all();
        $products = Product::all();

        $additionalProducts = FamsAdditionalProduct::all();

        $lastAdChargeId = str_pad( (FAC::max('id')+1), 4, "0", STR_PAD_LEFT );

        $prefix = DB::table('fams_product_prefix')->where('status',1)->value('name');


        return view('fams/transaction/additionalCharge/additionalCharge',['additionalCharges'=>$additionalCharges,'additionalChargeDetails'=>$additionalChargeDetails,'productGroups'=>$productGroups,'productSubCategories'=>$productSubCategories,'products'=>$products,'branches'=>$branches,'lastAdChargeId'=>$lastAdChargeId,'additionalProducts'=>$additionalProducts,'prefix'=>$prefix]);

    }

    public function addAdditinalCharge()
    {
        $additionalCharges = FAC::all();
        $lastAdChargeId = str_pad( (FAC::max('id')+1), 4, "0", STR_PAD_LEFT );
        $additionalChargeDetails = FACD::all();

        $products = Product::all();
        $productGroups = FamsPgroup::all();
        $productCategories = FamsPcategory::all();
        
        $branches = GnrBranch::all();
        $productSubCategories = FamsPsubcategory::all();       

        $additionalProducts = FamsAdditionalProduct::all();

        $userBranchId = (int) Auth::user()->branchId;
        $productTypes = DB::table('fams_product_type')->orderBy('productTypeCode')->get();
        $productNames = DB::table('fams_product_name')->orderBy('productNameCode')->get();

        $prefix = DB::table('fams_product_prefix')->where('status',1)->value('name');


        return view('fams/transaction/additionalCharge/addAdditionalCharge',['additionalCharges'=>$additionalCharges,'additionalChargeDetails'=>$additionalChargeDetails,'productGroups'=>$productGroups,'productCategories'=>$productCategories,'productSubCategories'=>$productSubCategories,'products'=>$products,'branches'=>$branches,'lastAdChargeId'=>$lastAdChargeId,'additionalProducts'=>$additionalProducts,'userBranchId'=>$userBranchId,'productTypes'=>$productTypes,'productNames'=>$productNames,'prefix'=>$prefix]);
    }

    public function storeAdditinalCharge(Request $request){

        $userId = Auth::id();

        $fac = new FAC;

        $fac->billNo = $request->billNo;
        $fac->productId = Product::where('id',$request->productId)->value('id');
        $fac->productCode = $request->productCode;
        $fac->quantity = $request->totalQuantity;
        $fac->branchId = GnrBranch::where('name',$request->branchName)->value('id');
        $fac->amount = $request->totalAmount;
        $fac->entryBy = $userId;
        $fac->purchaseDate = Carbon::parse($request->date);

        $mainProduct = Product::where('productCode',$request->productCode)->first();

        $mainProductUsefulLifeEndDate = Carbon::parse($mainProduct->purchaseDate)->addYears($mainProduct->usefulLifeYear)->addMonthsNoOverflow($mainProduct->usefulLifeMonth);
        
        $productPurchaseDate = Carbon::parse($request->date);
        
        $remainingDays = $productPurchaseDate->diffInDays($mainProductUsefulLifeEndDate);
        $fac->depDayforProduct = round((float)($request->totalAmount/$remainingDays),2);

        $fac->save();


        $array_size = count($request->fieldproductName);
        $data = [];
        for ($i=0; $i<$array_size; $i++){

            $facd = new FACD;

            $facd->additionalChargeBillNoId =  $request->billNo;
            $facd->productId =  FACD::max('productId')+1;
            $facd->productName =  json_decode($request->fieldproductName[$i]);
            $facd->quantity =  json_decode($request->fieldproductQuantity[$i]);
            $facd->productPrice =  json_decode($request->fieldproductPrice[$i]);
            $facd->totalPrice =  json_decode($request->fieldproductTotalPrice[$i]);
            $facd->purchaseDate =  $productPurchaseDate;
            $facd->save();

        }


        $response = array(
            'status' => 'success',
            'msg' => 'Data Stored successfully',
        );
        $logArray = array(
            'moduleId'  => 2,
            'controllerName'  => 'FamsTransactionAdditionalChargeController',
            'tableName'  => 'fams_additional_charge',
            'operation'  => 'insert',
            'primaryIds'  => [DB::table('fams_additional_charge')->max('id')]
        );
        Service::createLog($logArray);
        return \Response::json($response);


    }

    public function editAdditinalCharge(Request $request){

        $userId = Auth::id();
        $billNo = $request->billNo;
        $quantity = $request->quantity;
        $amount = $request->amount;

        $id = FAC::where('billNo',$billNo)->value('id');
        $fac = FAC::find($id);

        $fac->quantity = $quantity;
        $fac->amount = $amount;

        $poductCode = FAC::where('billNo',$billNo)->value('productCode');

        $mainProduct = Product::where('productCode',$poductCode)->first();

        $mainProductUsefulLifeEndDate = Carbon::parse($mainProduct->purchaseDate)->addYears($mainProduct->usefulLifeYear)->addMonthsNoOverflow($mainProduct->usefulLifeMonth);   
        
        $productPurchaseDate = Carbon::parse($request->date);        
        $remainingDays = $productPurchaseDate->diffInDays($mainProductUsefulLifeEndDate);
        $fac->depDayforProduct = round((float)($amount/$remainingDays),2);
        
        $fac->save();
        $previousdata = FamsTrnsUseReturn::find ($request->adChargeId);

        FACD::where(['additionalChargeBillNoId'=>$billNo])->delete();


        $array_size = count($request->fieldproductName);


        $data = [];
        for ($i=0; $i<$array_size; $i++){            
            $productId = (int) json_decode($request->fieldproductId[$i]);        
            
            $new_facd = new FACD;            
            $new_facd->additionalChargeBillNoId =  $billNo;
            $new_facd->productId =   $productId;
            $new_facd->productName = $request->fieldproductName[$i];
            $new_facd->quantity =  (float)$request->fieldproductQuantity[$i];
            $new_facd->productPrice =  (float)$request->fieldproductPrice[$i];
            $new_facd->totalPrice =  (float)$request->fieldproductTotalPrice[$i];
            $new_facd->save();            

        }

        $logArray = array(
            'moduleId'  => 2,
            'controllerName'  => 'FamsTransactionAdditionalChargeController',
            'tableName'  => 'fams_additional_charge',
            'operation'  => 'update',
            'previousData'  => $previousdata,
            'primaryIds'  => [$previousdata->id]
        );
        Service::createLog($logArray);             

        return response()->json("success");


    }

    public function deleteAdditinalCharge(Request $request){
        $previousdata=FAC::find($request->adChargeId);

        FAC::find($request->adChargeId)->delete();
        $logArray = array(
            'moduleId'  => 2,
            'controllerName'  => 'FamsTransactionAdditionalChargeController',
            'tableName'  => 'fams_additional_charge',
            'operation'  => 'delete',
            'previousData'  => $previousdata,
            'primaryIds'  => [$previousdata->id]
        );
        Service::createLog($logArray);
        return redirect('famsAdditionalCharge');

    }
    
    public function getBranch(Request $request){
        $branchId = Product::where('id',$request->productId)->value('branchId');
        $branchCode = GnrBranch::where('id',$branchId)->value('branchCode');
        $branchName = GnrBranch::where('id',$branchId)->value('name');

        $formatedBranchCode = str_pad( $branchCode, 4, "0", STR_PAD_LEFT );
        $data = array(
            'branchCode' => $formatedBranchCode,
            'branchName' => $branchName
        );
        return response()->json($data);
    }


    public function getProducts(Request $request){
        $categoryId = Product::where('id',$request->productId)->value('categoryId');

        $productList = FamsAdditionalProduct::where('categoryId',$categoryId)->pluck('id','name');

        $data = array(
            'productList'         => $productList
        );
        
        return response()->json($data);
    }

    public function getAllProducts(Request $request){
        if ($request->productCode!="") {
            $categoryId = Product::where('id',$request->productId)->value('categoryId');
            $productList = FamsAdditionalProduct::where('categoryId',$categoryId)->pluck('id','name');
        }
        else{
            $productList = DB::table('fams_additional_product')->pluck('id','name');
        }  

        $data = array(
            'productList'         => $productList
        );
        
        return response()->json($data);
    }



    /* Filtering Methods */

    public function onChangeBranch(Request $request){
        $branchId = $request->branchId;
        $productGroupId = $request->productGroupId;
        $productSubCategoryId = $request->productSubCategoryId;

        $writeOffProducts = DB::table('fams_write_off')->pluck('productId')->toArray();
        $soldProducts = DB::table('fams_sale')->pluck('productId')->toArray();
        $result = array_merge($writeOffProducts, $soldProducts);


        if ($branchId=="") {

            if($productGroupId=="" && $productSubCategoryId==""){
                $productList =  DB::table('fams_product')->whereNotIn('id', $result)->pluck('id','productCode');
            }
            elseif($productGroupId=="" && $productSubCategoryId!=""){
                $productList =  DB::table('fams_product')->whereNotIn('id', $result)->where('subCategoryId',$productSubCategoryId)->pluck('id','productCode');
            }
            elseif($productGroupId!="" && $productSubCategoryId==""){
                $productList =  DB::table('fams_product')->whereNotIn('id', $result)->where('groupId',$productGroupId)->pluck('id','productCode');
            }
            elseif($productGroupId!="" && $productSubCategoryId!=""){
                $productList =  DB::table('fams_product')->whereNotIn('id', $result)->where([['groupId',$productGroupId],['subCategoryId',$productSubCategoryId]])->pluck('id','productCode');
            }

        }
        else{

            if($productGroupId=="" && $productSubCategoryId==""){
                $productList =  DB::table('fams_product')->whereNotIn('id', $result)->where('branchId',$branchId)->pluck('id','productCode');
            }
            elseif($productGroupId=="" && $productSubCategoryId!=""){
                $productList =  DB::table('fams_product')->whereNotIn('id', $result)->where([['subCategoryId',$productSubCategoryId],['branchId',$branchId]])->pluck('id','productCode');
            }
            elseif($productGroupId!="" && $productSubCategoryId==""){
                $productList =  DB::table('fams_product')->whereNotIn('id', $result)->where([['groupId',$productGroupId],['branchId',$branchId]])->pluck('id','productCode');
            }
            elseif($productGroupId!="" && $productSubCategoryId!=""){
                $productList =  DB::table('fams_product')->whereNotIn('id', $result)->where([['groupId',$productGroupId],['subCategoryId',$productSubCategoryId],['branchId',$branchId]])->pluck('id','productCode');
            }
        }




        $data = array(
            'productList'         => $productList
        );
        return response()->json($data);
    }



    public function onChangeGroup(Request $request){
        $branchId = $request->branchId;
        $productGroupId = $request->productGroupId;
        $productSubCategoryId = $request->productSubCategoryId;

        $writeOffProducts = DB::table('fams_write_off')->pluck('productId')->toArray();
        $soldProducts = DB::table('fams_sale')->pluck('productId')->toArray();
        $result = array_merge($writeOffProducts, $soldProducts);


        if ($branchId=="") {

            if($productGroupId=="" && $productSubCategoryId==""){
                $productList =  DB::table('fams_product')->whereNotIn('id', $result)->pluck('id','productCode');
                $productSubCategoryList = DB::table('fams_product_sub_category')
                ->join('fams_product','fams_product_sub_category.id','=','fams_product.subCategoryId')
                ->select('fams_product_sub_category.*')
                ->pluck('id','name');
            }
            elseif($productGroupId=="" && $productSubCategoryId!=""){
                $productList =  DB::table('fams_product')->whereNotIn('id', $result)->where('subCategoryId',$productSubCategoryId)->pluck('id','productCode');
                $productSubCategoryList = DB::table('fams_product_sub_category')
                ->join('fams_product','fams_product_sub_category.id','=','fams_product.subCategoryId')
                ->select('fams_product_sub_category.*')
                ->pluck('id','name');
            }
            elseif($productGroupId!="" && $productSubCategoryId==""){
                $productList =  DB::table('fams_product')->whereNotIn('id', $result)->where('groupId',$productGroupId)->pluck('id','productCode');
                $productSubCategoryList =  DB::table('fams_product_sub_category')
                ->join('fams_product','fams_product_sub_category.id','=','fams_product.subCategoryId')
                ->where('fams_product_sub_category.productGroupId',$productGroupId)
                ->select('fams_product_sub_category.*')
                ->distinct()->pluck('id','name');

            }
            elseif($productGroupId!="" && $productSubCategoryId!=""){
                $productList =  DB::table('fams_product')->whereNotIn('id', $result)->where([['groupId',$productGroupId],['subCategoryId',$productSubCategoryId]])->pluck('id','productCode');
                $productSubCategoryList =  DB::table('fams_product_sub_category')
                ->join('fams_product','fams_product_sub_category.id','=','fams_product.subCategoryId')
                ->where('fams_product_sub_category.productGroupId',$productGroupId)
                ->select('fams_product_sub_category.*')
                ->distinct()->pluck('id','name');
            }

        }


        else{

            $productSubCategoryList = DB::table('fams_product_sub_category')
            ->join('fams_product','fams_product_sub_category.id','=','fams_product.subCategoryId')
            ->select('fams_product_sub_category.*')
            ->pluck('id','name');

            if($productGroupId=="" && $productSubCategoryId==""){
                $productList =  DB::table('fams_product')->whereNotIn('id', $result)->where('branchId',$branchId)->pluck('id','productCode');
            }
            elseif($productGroupId=="" && $productSubCategoryId!=""){
                $productList =  DB::table('fams_product')->whereNotIn('id', $result)->where([['subCategoryId',$productSubCategoryId],['branchId',$branchId]])->pluck('id','productCode');
            }
            elseif($productGroupId!="" && $productSubCategoryId==""){
                $productList =  DB::table('fams_product')->whereNotIn('id', $result)->where([['groupId',$productGroupId],['branchId',$branchId]])->pluck('id','productCode');
                $productSubCategoryList =  DB::table('fams_product_sub_category')
                ->join('fams_product','fams_product_sub_category.id','=','fams_product.subCategoryId')
                ->where('fams_product_sub_category.productGroupId',$productGroupId)
                ->select('fams_product_sub_category.*')
                ->distinct()->pluck('id','name');
            }
            elseif($productGroupId!="" && $productSubCategoryId!=""){
                $productList =  DB::table('fams_product')->whereNotIn('id', $result)->where([['groupId',$productGroupId],['subCategoryId',$productSubCategoryId],['branchId',$branchId]])->pluck('id','productCode');
                $productSubCategoryList =  DB::table('fams_product_sub_category')
                ->join('fams_product','fams_product_sub_category.id','=','fams_product.subCategoryId')
                ->where('fams_product_sub_category.productGroupId',$productGroupId)
                ->select('fams_product_sub_category.*')
                ->distinct()->pluck('id','name');
            }
        }


        $data = array(
            'productSubCategoryList' => $productSubCategoryList,
            'productList'         => $productList
        );
        return response()->json($data);
    }

    public function onChangeSubCategory(Request $request)
    {

        $branchId = $request->branchId;
        $productGroupId = $request->productGroupId;
        $productSubCategoryId = $request->productSubCategoryId;

        $writeOffProducts = DB::table('fams_write_off')->pluck('productId')->toArray();
        $soldProducts = DB::table('fams_sale')->pluck('productId')->toArray();
        $result = array_merge($writeOffProducts, $soldProducts);

        if ($branchId=="") {

            if ($productGroupId=="") {
                if ($productSubCategoryId=="") {
                    $productList =  DB::table('fams_product')->whereNotIn('id', $result)->pluck('id','productCode');
                }
                else{
                    $productList =  DB::table('fams_product')->whereNotIn('id', $result)->where('subCategoryId',$productSubCategoryId)->pluck('id','productCode');
                }
            }

            else{
                if ($productSubCategoryId=="") {
                    $productList =  DB::table('fams_product')->whereNotIn('id', $result)->where('groupId',$productGroupId)->pluck('id','productCode');
                }
                else{
                    $productList =  DB::table('fams_product')->whereNotIn('id', $result)->where('subCategoryId',$productSubCategoryId)->where('groupId',$productGroupId)->pluck('id','productCode');
                }
                
            }

        }
        else{

            if ($productGroupId=="") {
                if ($productSubCategoryId=="") {
                    $productList =  DB::table('fams_product')->whereNotIn('id', $result)->where('branchId',$branchId)->pluck('id','productCode');
                }
                else{
                    $productList =  DB::table('fams_product')->whereNotIn('id', $result)->where('branchId',$branchId)->where('subCategoryId',$productSubCategoryId)->pluck('id','productCode');
                }
            }

            else{
                if ($productSubCategoryId=="") {
                    $productList =  DB::table('fams_product')->whereNotIn('id', $result)->where('branchId',$branchId)->where('groupId',$productGroupId)->pluck('id','productCode');
                }
                else{
                    $productList =  DB::table('fams_product')->whereNotIn('id', $result)->where('branchId',$branchId)->where('subCategoryId',$productSubCategoryId)->where('groupId',$productGroupId)->pluck('id','productCode');
                }
                
            }

        }

        $data = array(
            'productList'         => $productList
        );
        return response()->json($data);

    }


    public function onChangeName(Request $request){

        $branchId = $request->branchId;
        $productNameId = $request->productNameId;

        $branchIdArray = array();
        $productNameIdArray = array();

        if($branchId==""){
            $branchIdArray = DB::table('gnr_branch')->pluck('id');
        }
        else{
            $tempBranchId = (int) json_decode($branchId);
            array_push($branchIdArray,$tempBranchId);
        }

        if($productNameId==""){
            $productNameIdArray = DB::table('fams_product_name')->pluck('id');
        }
        else{

            array_push($productNameIdArray,(int) json_decode($productNameId));
        }

        $writeOffProducts = DB::table('fams_write_off')->pluck('productId')->toArray();
        $soldProducts = DB::table('fams_sale')->pluck('productId')->toArray();
        $result = array_merge($writeOffProducts, $soldProducts);

        
        $productList = DB::table('fams_product')->whereNotIn('id', $result)->whereIn('branchId',$branchIdArray)->whereIn('productNameId',$productNameIdArray)->pluck('id','productCode');        

        $data = array(
            'productList'         => $productList
        );
        return response()->json($data);

    }

    public function onChangeBranch2(Request $request)
    {
        $branchId = array();
        $productTypeId = array();
        $productNameId = array();        

        //Branch
        if($request->branchId==""){
            $branchId = DB::table('gnr_branch')->pluck('id');
        }
        else{            
            array_push($branchId,(int) json_decode($request->branchId));
        }

        //Product Type
        if($request->productTypeId==""){
            $productTypeId = DB::table('fams_product_type')->pluck('id');
        }
        else{            
            array_push($productTypeId,(int) json_decode($request->productTypeId));
        }

        //Product Name
        if($request->productNameId==""){
            $productNameId = DB::table('fams_product_name')->pluck('id');
        }
        else{            
            array_push($productNameId,(int) json_decode($request->productNameId));
        }

        //Write Off and Sold Products
        $writeOffProducts = DB::table('fams_write_off')->pluck('productId')->toArray();
        $soldProducts = DB::table('fams_sale')->pluck('productId')->toArray();
        $result = array_merge($writeOffProducts, $soldProducts);

        $productList = DB::table('fams_product')->whereNotIn('id', $result)->whereIn('branchId',$branchId)->whereIn('productTypeId',$productTypeId)->whereIn('productNameId',$productNameId)->pluck('id','productCode');
        

        $data = array(
            'productList'         => $productList
        );
        return response()->json($data);

    }

    public function onSubCategory2(Request $request)
    {
        $branchId = array();
        $subCategoryId = array();     

        //Branch
        if($request->branchId==""){
            $branchId = DB::table('gnr_branch')->pluck('id');
        }
        else{            
            array_push($branchId,(int) json_decode($request->branchId));
        }

        //Sub Category
        if($request->productSubCategoryId==""){
            $subCategoryId = DB::table('fams_product_sub_category')->pluck('id');
        }
        else{            
            array_push($subCategoryId,(int) json_decode($request->productSubCategoryId));
        }


        //Write Off and Sold Products
        $writeOffProducts = DB::table('fams_write_off')->pluck('productId')->toArray();
        $soldProducts = DB::table('fams_sale')->pluck('productId')->toArray();
        $result = array_merge($writeOffProducts, $soldProducts);

        $productList = DB::table('fams_product')->whereNotIn('id', $result)->whereIn('branchId',$branchId)->whereIn('subCategoryId',$subCategoryId)->pluck('id','productCode');
        $productNameList = DB::table('fams_product_name')->whereIn('productSubCategoryId',$subCategoryId)->orderBy('productNameCode')->pluck('id','name');    

        $data = array(
            'productList'         => $productList,
            'productNameList' => $productNameList
        );
        return response()->json($data);

    }

    public function onChangeProductType(Request $request)
    {
        $branchId = array();
        $productTypeId = array();     

        //Branch
        if($request->branchId==""){
            $branchId = DB::table('gnr_branch')->pluck('id');
        }
        else{            
            array_push($branchId,(int) json_decode($request->branchId));
        }

        //Product Type
        if($request->productTypeId==""){
            $productTypeId = DB::table('fams_product_type')->pluck('id');
        }
        else{            
            array_push($productTypeId,(int) json_decode($request->productTypeId));
        }


        //Write Off and Sold Products
        $writeOffProducts = DB::table('fams_write_off')->pluck('productId')->toArray();
        $soldProducts = DB::table('fams_sale')->pluck('productId')->toArray();
        $result = array_merge($writeOffProducts, $soldProducts);

        $productList = DB::table('fams_product')->whereNotIn('id', $result)->whereIn('branchId',$branchId)->whereIn('productTypeId',$productTypeId)->pluck('id','productCode');
        $productNameList = DB::table('fams_product_name')->select('id','name','productNameCode')->whereIn('productTypeId',$productTypeId)->orderBy('productNameCode')->get();    

        $data = array(
            'productList'         => $productList,
            'productNameList' => $productNameList
        );
        return response()->json($data);
    }
    

}
