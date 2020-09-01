<?php

namespace App\Http\Controllers\fams;

use App\Http\Controllers\Controller;
use App\Http\Controllers\gnr\Service;
use App\fams\FamsTraIssue;
use App\fams\FamsTraIssueDetails;
use App\GnrBranch;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class FamsTransactionIssueController extends Controller
{
    public function index(){

        $issues = DB::table('fams_tra_issue as issue')
        ->join('gnr_branch as branch', 'issue.branchId', '=', 'branch.id')
        ->select('issue.*', 'branch.id as branchId','branch.name as branchName')
        ->get();
        $issueDetails = DB::table('fams_tra_issue_details')
        ->get();
        $branches = DB::table('gnr_branch')->get();
        $products = DB::table('fams_product')->get();

        $productGroups = DB::table('fams_product_group')->get();
        $productCategories = DB::table('fams_product_category')->get();
        $productSubCategories = DB::table('fams_product_sub_category')->get();
        $productBrands = DB::table('fams_product_brand')->get();

        return view('fams/transaction/issue/famsIssue',['issues'=>$issues,'issueDetails'=>$issueDetails,'branches'=>$branches,'products'=>$products,'productGroups'=>$productGroups,'productCategories'=>$productCategories,'productSubCategories'=>$productSubCategories,'productBrands'=>$productBrands]);
        
    }

    public function addIssue()
    {
        $user = Auth::user();
        $branchId = str_pad($user->branchId, 4, "0", STR_PAD_LEFT);
        $lastColumn = str_pad(FamsTraIssue::max('id')+1, 6, "0", STR_PAD_LEFT);

        $issueNo = "IS".$branchId.$lastColumn;
        $branches = DB::table('gnr_branch')->get();
        $products = DB::table('fams_product')->get();


        $productGroups = DB::table('fams_product_group')
        ->join('fams_product','fams_product_group.id','=','fams_product.groupId')
        ->select('fams_product_group.*')
        ->distinct()
        ->get();
        $productCategories = DB::table('fams_product_category')
        ->join('fams_product','fams_product_category.id','=','fams_product.categoryId')
        ->select('fams_product_category.*')
        ->distinct()->get();
        $productSubCategories = DB::table('fams_product_sub_category')
        ->join('fams_product','fams_product_sub_category.id','=','fams_product.subCategoryId')
        ->select('fams_product_sub_category.*')
        ->distinct()->get();
        $productBrands = DB::table('fams_product_brand')
        ->join('fams_product','fams_product_brand.id','=','fams_product.brandId')
        ->select('fams_product_brand.*')
        ->distinct()->get();


        
        return view('fams/transaction/issue/famsAddIssue',['branches'=> $branches,'issueNo'=> $issueNo, 'products'=>$products,'productGroups'=>$productGroups,'productCategories'=>$productCategories,'productSubCategories'=>$productSubCategories,'productBrands'=>$productBrands]);
    }

    public function storeIssue(Request $request){

        $issue = new FamsTraIssue;

        $issue->issueBillNo = $request->issueNo;
        $issue->orderNo = $request->orderNo;
        $issue->issueOrderNo = $request->issueOrderNo;
        $issue->branchId = $request->branchId;
        $issue->totlaIssueQuantity = $request->totalIssueQuantity;
        $issue->totalIssueAmount = $request->totalIssueAmount;
        $issue->issueDate = Carbon::now();
        $issue->save();

        
        $array_size = count($request->fieldproductId);
        $data = [];
        for ($i=0; $i<$array_size; $i++){

            $costPrice = (int) json_decode($request->fieldproductPrice[$i]) * (int) json_decode($request->fieldproductQuantity[$i]);

            $data[] = array(
                'issueBillNoId' => $request->issueNo,    
                'issueProductId' => (int) json_decode($request->fieldproductId[$i]),
                'issueProductName' => (string) json_decode($request->fieldproductName[$i]),
                'issueQuantity' => (int) json_decode($request->fieldproductQuantity[$i]),
                'issueCostPrice' => $costPrice

            );            

        }
        
        DB::table('fams_tra_issue_details')->insert($data);


        $response = array(
            'status' => 'success',
            'msg' => 'Data Stored successfully',
        );
        $logArray = array(
            'moduleId'  => 2,
            'controllerName'  => 'FamsTransactionIssueController',
            'tableName'  => 'fams_tra_issue',
            'operation'  => 'insert',
            'primaryIds'  => [DB::table('fams_tra_issue')->max('id')]
        );
        Service::createLog($logArray);
        return \Response::json($response);

    }

    public function editIssue(Request $request){

        $issueBillNo = $request->input('editModalIssueBillNo');
        $branchId = $request->input('editModalBranchId');
        $array_size = count($request->editModalProductId);

        $productId = $request->input('editModalProductId');
        $quantity = $request->input('editModalQuantity');
        
        $previousdata = FamsTraIssue::find ($request->id);


        DB::table('fams_tra_issue_details')->where('issueBillNoId', $issueBillNo)->delete();

        $totalIssueQuantity = 0;
        $totalIssueAmount = 0;

        for ($i=0; $i < $array_size; $i++) {

            $name = (string) DB::table('fams_product')->where('id',$productId[$i])->value('name');
            $price = DB::table('fams_product')->where('id',$productId[$i])->value('costPrice');

            $totalIssueQuantity = $totalIssueQuantity + $quantity[$i];
            $totalIssueAmount = $totalIssueAmount + $quantity[$i] * $price;

            DB::table('fams_tra_issue_details')->insert(['issueBillNoId'=>$issueBillNo,'issueProductId'=>$productId[$i],'issueProductName'=>$name,'issueQuantity'=>$quantity[$i],'issueCostPrice'=>$quantity[$i]*$price]);
        }


        DB::table('fams_tra_issue')
        ->where('issueBillNo', $issueBillNo)
        ->update(['branchId' => $branchId,'totlaIssueQuantity'=>$totalIssueQuantity, 'totalIssueAmount'=>$totalIssueAmount]);
        $logArray = array(
            'moduleId'  => 2,
            'controllerName'  => 'FamsTransactionIssueController',
            'tableName'  => 'fams_tra_issue',
            'operation'  => 'update',
            'previousData'  => $previousdata,
            'primaryIds'  => [$previousdata->id]
        );
        Service::createLog($logArray);


        return redirect('famsIssue');
        
        
    }

    public function deleteIssue(Request $request){
      $previousdata=FamsTraIssue::find($request->id);
      DB::table('fams_tra_issue')->where('issueBillNo', $request->input("issueBillNo"))->delete();
      DB::table('fams_tra_issue_details')->where('issueBillNoId', $request->input("issueBillNo"))->delete();
      $logArray = array(
          'moduleId'  => 2,
          'controllerName'  => 'FamsTransactionIssueController',
          'tableName'  => 'fams_tra_issue',
          'operation'  => 'delete',
          'previousData'  => $previousdata,
          'primaryIds'  => [$previousdata->id]
      );
      Service::createLog($logArray);

      return redirect('famsIssue');
  }

  public function getProductPrice(Request $request){

    $productPrice = DB::table('fams_product')->where('id',(int) json_decode($request->productId))->pluck('costPrice');
    return response()->json($productPrice);
}



/* Filtering Methods */

public function onChangeBranch(Request $request){

    if ($request->branchId=="") {

        $productGroupList = DB::table('fams_product_group')
        ->join('fams_product','fams_product_group.id','=','fams_product.groupId')
        ->select('fams_product_group.*')
        ->distinct()->pluck('id','name');

        $productSubCategoryList =  DB::table('fams_product_sub_category')
        ->join('fams_product','fams_product_sub_category.id','=','fams_product.subCategoryId')
        ->select('fams_product_sub_category.*')
        ->distinct()->pluck('id','name');
        
        $productList =  DB::table('fams_product')->pluck('id','name');
    }
    else{

        $productCategoryList =  DB::table('fams_product_category')->where('productGroupId',(int) json_decode($request->productGroupId))->pluck('id','name');

        $productSubCategoryList =  DB::table('fams_product_sub_category')->where('productGroupId',(int) json_decode($request->productGroupId))->pluck('id','name');

        /*$productBrandList =  DB::table('fams_product_brand')->where('productGroupId',(int) json_decode($request->productGroupId))->pluck('id','name');*/

        $productList =  DB::table('fams_product')->where('groupId', (int) json_decode($request->productGroupId))->pluck('id','productCode');

    }

    $data = array(
        'productCategoryList' => $productCategoryList,
        'productSubCategoryList' => $productSubCategoryList,
        /*'productBrandList' => $productBrandList,*/
        'productList'         => $productList
    );
    return response()->json($data);
}



public function onChangeGroup(Request $request){
    $groupId = (int) json_decode($request->productGroupId);

    if ($request->productGroupId=="") {

        $productCategoryList = DB::table('fams_product_category')
        ->join('fams_product','fams_product_category.id','=','fams_product.categoryId')
        ->select('fams_product_category.*')
        ->distinct()->pluck('id','name');

        $productSubCategoryList = DB::table('fams_product_sub_category')
        ->join('fams_product','fams_product_sub_category.id','=','fams_product.subCategoryId')
        ->select('fams_product_sub_category.id','fams_product_sub_category.name')
        ->distinct()->pluck('id','name');


        /*$productBrandList =  DB::table('fams_product_brand')->pluck('id','name');*/
        $productList =  DB::table('fams_product')->pluck('id','productCode');
    }
    else{


        $productCategoryList = DB::table('fams_product_category')
        ->join('fams_product','fams_product_category.id','=','fams_product.categoryId')
        ->where('fams_product_category.productGroupId', $groupId)
        ->select('fams_product_category.*')
        ->distinct()->pluck('id','name');



        $productSubCategoryList = DB::table('fams_product_sub_category')
        ->join('fams_product','fams_product_sub_category.id','=','fams_product.subCategoryId')
        ->where('fams_product_sub_category.productGroupId', $groupId)
        ->select('fams_product_sub_category.*')
        ->distinct()->pluck('id','name');




        /*$productBrandList =  DB::table('fams_product_brand')->where('productGroupId',(int) json_decode($request->productGroupId))->pluck('id','name');*/

        $productList =  DB::table('fams_product')->where('groupId', $groupId)->pluck('id','productCode');

    }        

    $data = array(
        'productCategoryList' => $productCategoryList,
        'productSubCategoryList' => $productSubCategoryList,
        /*'productBrandList' => $productBrandList,*/
        'productList'         => $productList
    );
    return response()->json($data);
}






public function onChangeCategory(Request $request){
    if ($request->productGroupId=="" && $request->productCategoryId == "") {

        $productSubCategoryList =  DB::table('fams_product_sub_category')->pluck('id','name');
        /*$productBrandList =  DB::table('fams_product_brand')->pluck('id','name');*/
        $productList =  DB::table('fams_product')->pluck('id','name');
    }

    elseif ($request->productGroupId !="" && $request->productCategoryId == "") {
        $productSubCategoryList =  DB::table('fams_product_sub_category')->where('productGroupId',(int)json_decode($request->productGroupId))->pluck('id','name');
        /*$productBrandList =  DB::table('fams_product_brand')->where('productGroupId',(int)json_decode($request->productGroupId))->pluck('id','name');*/
        $productList =  DB::table('fams_product')->where('groupId',(int)json_decode($request->productGroupId))->pluck('id','name');
    }

    elseif($request->productGroupId =="" && $request->productCategoryId != ""){

        $productSubCategoryList =  DB::table('fams_product_sub_category')->where('productCategoryId',(int)json_decode($request->productCategoryId))->pluck('id','name');
        /*$productBrandList =  DB::table('fams_product_brand')->where('productCategoryId',(int)json_decode($request->productCategoryId))->pluck('id','name');*/
        $productList =  DB::table('fams_product')->where('categoryId',(int)json_decode($request->productCategoryId))->pluck('id','name');
    }

    else{

        $productSubCategoryList =  DB::table('fams_product_sub_category')->where([['productGroupId',(int)json_decode($request->productGroupId)],['productCategoryId',(int)json_decode($request->productCategoryId)]])->pluck('id','name');

        /*$productBrandList =  DB::table('fams_product_brand')->where([['productGroupId',(int)json_decode($request->productGroupId)],['productCategoryId',(int)json_decode($request->productCategoryId)]])->pluck('id','name');*/

        $productList =  DB::table('fams_product')->where([['groupId',(int)json_decode($request->productGroupId)],['categoryId',(int)json_decode($request->productCategoryId)]])->pluck('id','name');

    }        

    $data = array(
        'productSubCategoryList' => $productSubCategoryList,
        /*'productBrandList' => $productBrandList,*/
        'productList'         => $productList
    );
    return response()->json($data);
}



public function onChangeSubCategory(Request $request){


    if ($request->productGroupId=="" && $request->productCategoryId == "" && $request->productSubCategoryId == "") {
        $productBrandList =  DB::table('fams_product_brand')->pluck('id','name');
        $productList =  DB::table('fams_product')->pluck('id','name');
    }


    elseif ($request->productGroupId=="" && $request->productCategoryId == "" && $request->productSubCategoryId != "") {

            /*$productBrandList =  DB::table('fams_product_brand')
                             ->where('productSubCategoryId',(int)json_decode($request->productSubCategoryId))
                             ->pluck('id','name');*/

                             $productList =  DB::table('fams_product')
                             ->where('subCategoryId',(int)json_decode($request->productSubCategoryId))
                             ->pluck('id','name');
                         }

                         elseif ($request->productGroupId=="" && $request->productCategoryId != "" && $request->productSubCategoryId == "") {

            /*$productBrandList =  DB::table('fams_product_brand')
                             ->where('productCategoryId',(int)json_decode($request->productCategoryId))
                             ->pluck('id','name');*/

                             $productList =  DB::table('fams_product')
                             ->where('categoryId',(int)json_decode($request->productCategoryId))
                             ->pluck('id','name');
                         }

                         elseif ($request->productGroupId=="" && $request->productCategoryId != "" && $request->productSubCategoryId != "") {

            /*$productBrandList =  DB::table('fams_product_brand')
                             ->where([['productCategoryId',(int)json_decode($request->productCategoryId)],['productSubCategoryId',(int)json_decode($request->productSubCategoryId)]])
                             ->pluck('id','name');*/

                             $productList =  DB::table('fams_product')
                             ->where([['categoryId',(int)json_decode($request->productCategoryId)],['subCategoryId',(int)json_decode($request->productSubCategoryId)]])
                             ->pluck('id','name');
                         }

                         elseif ($request->productGroupId!="" && $request->productCategoryId == "" && $request->productSubCategoryId == "") {

           /* $productBrandList =  DB::table('fams_product_brand')
                             ->where('productGroupId',(int)json_decode($request->productGroupId))
                             ->pluck('id','name');*/

                             $productList =  DB::table('fams_product')
                             ->where('groupId',(int)json_decode($request->productGroupId))
                             ->pluck('id','name');
                         }

                         elseif ($request->productGroupId!="" && $request->productCategoryId == "" && $request->productSubCategoryId != "") {

            /*$productBrandList =  DB::table('fams_product_brand')
                             ->where([['productGroupId',(int)json_decode($request->productGroupId)],['productSubCategoryId',(int)json_decode($request->productSubCategoryId)]])
                             ->pluck('id','name');*/

                             $productList =  DB::table('fams_product')
                             ->where([['groupId',(int)json_decode($request->productGroupId)],['subCategoryId',(int)json_decode($request->productSubCategoryId)]])
                             ->pluck('id','name');
                         }

                         elseif ($request->productGroupId!="" && $request->productCategoryId != "" && $request->productSubCategoryId == "") {

            /*$productBrandList =  DB::table('fams_product_brand')
                             ->where([['productGroupId',(int)json_decode($request->productGroupId)],['productCategoryId',(int)json_decode($request->productCategoryId)]])
                             ->pluck('id','name');*/

                             $productList =  DB::table('fams_product')
                             ->where([['groupId',(int)json_decode($request->productGroupId)],['categoryId',(int)json_decode($request->productCategoryId)]])
                             ->pluck('id','name');
                         }

                         elseif ($request->productGroupId!="" && $request->productCategoryId != "" && $request->productSubCategoryId != "") {

            /*$productBrandList =  DB::table('fams_product_brand')
                             ->where([['productGroupId',(int)json_decode($request->productGroupId)],['productCategoryId',(int)json_decode($request->productCategoryId)],['productSubCategoryId',(int)json_decode($request->productSubCategoryId)]])
                             ->pluck('id','name');*/

                             $productList =  DB::table('fams_product')
                             ->where([['groupId',(int)json_decode($request->productGroupId)],['categoryId',(int)json_decode($request->productCategoryId)],['subCategoryId',(int)json_decode($request->productSubCategoryId)]])
                             ->pluck('id','name');
                         }


                         $data = array(
                            /*'productBrandList' => $productBrandList,*/
                            'productList'         => $productList
                        );
                         return response()->json($data);
                     }


   /* function onChangeBrand(Request $request){

        if ($request->productGroupId =="" && $request->productCategoryId == "" && $request->productSubCategoryId == "" && $request->productBrandId =="") {

            $productList =  DB::table('fams_product')->pluck('id','name');
        }

        elseif($request->productGroupId =="" && $request->productCategoryId == "" && $request->productSubCategoryId == "" && $request->productBrandId !=""){

            $productList =  DB::table('fams_product')
                                    ->where('brandId',(int)json_decode($request->productBrandId))
                                    ->pluck('id','name');

        }

        elseif($request->productGroupId =="" && $request->productCategoryId == "" && $request->productSubCategoryId != "" && $request->productBrandId ==""){
            
            $productList =  DB::table('fams_product')
                                    ->where('subCategoryId',(int)json_decode($request->productSubCategoryId))
                                    ->pluck('id','name');
        }

        elseif($request->productGroupId =="" && $request->productCategoryId == "" && $request->productSubCategoryId != "" && $request->productBrandId !=""){

            $productList =  DB::table('fams_product')
                                    ->where([['subCategoryId',(int)json_decode($request->productSubCategoryId)],['brandId',(int)json_decode($request->productBrandId)]])
                                    ->pluck('id','name');
            
        }
        elseif($request->productGroupId =="" && $request->productCategoryId != "" && $request->productSubCategoryId == "" && $request->productBrandId ==""){

            $productList =  DB::table('fams_product')
                                    ->where('categoryId',(int)json_decode($request->productCategoryId))
                                    ->pluck('id','name');
            
        }

        elseif($request->productGroupId =="" && $request->productCategoryId != "" && $request->productSubCategoryId == "" && $request->productBrandId !=""){

            $productList =  DB::table('fams_product')
                                    ->where([['categoryId',(int)json_decode($request->productCategoryId)],['brandId',(int)json_decode($request->productBrandId)]])
                                    ->pluck('id','name');
            
        }

        elseif($request->productGroupId =="" && $request->productCategoryId != "" && $request->productSubCategoryId != "" && $request->productBrandId ==""){

            $productList =  DB::table('fams_product')
                                    ->where([['categoryId',(int)json_decode($request->productCategoryId)],['subCategoryId',(int)json_decode($request->productSubCategoryId)]])
                                    ->pluck('id','name');
            
        }

        elseif($request->productGroupId =="" && $request->productCategoryId != "" && $request->productSubCategoryId != "" && $request->productBrandId !=""){

            $productList =  DB::table('fams_product')
                                    ->where([['categoryId',(int)json_decode($request->productCategoryId)],['subCategoryId',(int)json_decode($request->productSubCategoryId)],['brandId',(int)json_decode($request->productBrandId)]])
                                    ->pluck('id','name');
            
        }

        elseif($request->productGroupId !="" && $request->productCategoryId == "" && $request->productSubCategoryId == "" && $request->productBrandId ==""){

            $productList =  DB::table('fams_product')
                                    ->where('groupId',(int)json_decode($request->productGroupId))
                                    ->pluck('id','name');
            
        }

        elseif($request->productGroupId !="" && $request->productCategoryId == "" && $request->productSubCategoryId == "" && $request->productBrandId !=""){

            $productList =  DB::table('fams_product')
                                    ->where([['groupId',(int)json_decode($request->productGroupId)],['brandId',(int)json_decode($request->productBrandId)]])
                                    ->pluck('id','name');
            
        }

        elseif($request->productGroupId !="" && $request->productCategoryId == "" && $request->productSubCategoryId != "" && $request->productBrandId ==""){

            $productList =  DB::table('fams_product')
                                    ->where([['groupId',(int)json_decode($request->productGroupId)],['subCategoryId',(int)json_decode($request->productSubCategoryId)]])
                                    ->pluck('id','name');
            
        }

        elseif($request->productGroupId !="" && $request->productCategoryId == "" && $request->productSubCategoryId != "" && $request->productBrandId !=""){

            $productList =  DB::table('fams_product')
                                    ->where([['groupId',(int)json_decode($request->productGroupId)],['subCategoryId',(int)json_decode($request->productSubCategoryId)],['brandId',(int)json_decode($request->productBrandId)]])
                                    ->pluck('id','name');
            
        }

        elseif($request->productGroupId !="" && $request->productCategoryId != "" && $request->productSubCategoryId == "" && $request->productBrandId ==""){

            $productList =  DB::table('fams_product')
                                    ->where([['groupId',(int)json_decode($request->productGroupId)],['categoryId',(int)json_decode($request->productCategoryId)]])
                                    ->pluck('id','name');
            
        }

        elseif($request->productGroupId !="" && $request->productCategoryId != "" && $request->productSubCategoryId == "" && $request->productBrandId !=""){

            $productList =  DB::table('fams_product')
                                    ->where([['groupId',(int)json_decode($request->productGroupId)],['categoryId',(int)json_decode($request->productCategoryId)],['brandId',(int)json_decode($request->productBrandId)]])
                                    ->pluck('id','name');
            
        }

        elseif($request->productGroupId !="" && $request->productCategoryId != "" && $request->productSubCategoryId != "" && $request->productBrandId ==""){

            $productList =  DB::table('fams_product')
                                    ->where([['groupId',(int)json_decode($request->productGroupId)],['categoryId',(int)json_decode($request->productCategoryId)],['subCategoryId',(int)json_decode($request->productSubCategoryId)]])
                                    ->pluck('id','name');
            
        }

        elseif($request->productGroupId !="" && $request->productCategoryId != "" && $request->productSubCategoryId != "" && $request->productBrandId !=""){

            $productList =  DB::table('fams_product')
                                    ->where([['groupId',(int)json_decode($request->productGroupId)],['categoryId',(int)json_decode($request->productCategoryId)],['subCategoryId',(int)json_decode($request->productSubCategoryId)],['brandId',(int)json_decode($request->productBrandId)]])
                                    ->pluck('id','name');
            
        }

        $data = array(
        'productList'         => $productList
        );
        return response()->json($data);
    }*/
    
}
