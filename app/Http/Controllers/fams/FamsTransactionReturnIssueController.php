<?php

namespace App\Http\Controllers\fams;

use App\Http\Controllers\Controller;
use App\Http\Controllers\gnr\Service;
use App\fams\FamsTraIssueReturn;
use App\GnrBranch;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class FamsTransactionReturnIssueController extends Controller
{
    public function index(){
    	$returnedIssues = DB::table('fams_tra_issue_return as issueReturn')
        ->join('gnr_branch as branch','issueReturn.branchId', '=', 'branch.id')
        ->select('issueReturn.*', 'branch.name as branchName')
        ->get();
        $issueReturnDetails = DB::table('fams_tra_issue_return_details')
        ->get();

        $products = DB::table('fams_product')->get();

        $productGroups = DB::table('fams_product_group')->get();
        $productCategories = DB::table('fams_product_category')->get();
        $productSubCategories = DB::table('fams_product_sub_category')->get();
        $productBrands = DB::table('fams_product_brand')->get();


        return view('fams/transaction/issue/viewIssueReturned',['returnedIssues'=>$returnedIssues,'issueReturnDetails'=>$issueReturnDetails,'productGroups'=>$productGroups,'productCategories'=>$productCategories,'productSubCategories'=>$productSubCategories,'productBrands'=>$productBrands,'products'=>$products]);

    }

    public function addIssueReturn()
    {
        $user = Auth::user();
        $branchId = str_pad($user->branchId, 4, "0", STR_PAD_LEFT);
        $lastColumn = str_pad(DB::table('fams_tra_issue_return')->max('id')+1, 6, "0", STR_PAD_LEFT);
        $issueReturnBillNo = "IR".$branchId.$lastColumn;
        $branchName = DB::table('gnr_branch')->where('id',$user->branchId)->value('name');
        $products = DB::table('fams_product')->get();

        $productGroups = DB::table('fams_product_group')->get();
        $productCategories = DB::table('fams_product_category')->get();
        $productSubCategories = DB::table('fams_product_sub_category')->get();
        $productBrands = DB::table('fams_product_brand')->get();
        
        return view('fams/transaction/issue/famsAddIssueReturn',['branchId'=>$user->branchId,'branchName'=> $branchName,'issueReturnBillNo'=> $issueReturnBillNo, 'products'=>$products,'productGroups'=>$productGroups,'productCategories'=>$productCategories,'productSubCategories'=>$productSubCategories,'productBrands'=>$productBrands]);
    }

    public function storeIssueReturn(Request $request)
    {
        $issue_return = new FamsTraIssueReturn;

        $array_size = count($request->fieldproductId);
        $totalIssueReturnAmount = 0;

        for($i=0; $i<$array_size; $i++){
            $productId = (int) json_decode($request->fieldproductId[$i]);
            $productPrice = (float)DB::table('fams_product')->where('id',$productId)->value('costPrice');
            $productQuantity = (float) json_decode($request->fieldproductQuantity[$i]);
            $totalIssueReturnAmount = $totalIssueReturnAmount + ($productPrice*$productQuantity);
        }
        

        $issue_return->issueReturnBillNo = $request->issueReturnBillNo;
        $issue_return->branchId = $request->branchId;
        $issue_return->issueReturnDate = Carbon::now();
        $issue_return->totalIssueReturnQuantity = $request->totalIssueQuantity;
        $issue_return->totalIssueReturnAmount = $totalIssueReturnAmount;
        $issue_return->save();

        
        
        $data = [];
        for ($i=0; $i<$array_size; $i++){

            $data[] = array(
                'issueReturnBillNoId' => $request->issueReturnBillNo,    
                'productId' => (int) json_decode($request->fieldproductId[$i]),
                'issueReturnProductName' => (string) json_decode($request->fieldproductName[$i]),
                'issueReturnQuantity' => (int) json_decode($request->fieldproductQuantity[$i]),
                'issueReturnProductCostPrice' => (int) json_decode($request->fieldproductPrice[$i]) * (int) json_decode($request->fieldproductQuantity[$i]),            
            );            

        }
        DB::table('fams_tra_issue_return_details')->insert($data);


        $response = array(
            'status' => 'success',
            'msg' => 'Data Stored successfully',
        );
        $logArray = array(
            'moduleId'  => 2,
            'controllerName'  => 'FamsTransactionReturnIssueController',
            'tableName'  => 'fams_tra_issue_return',
            'operation'  => 'insert',
            'primaryIds'  => [DB::table('fams_tra_issue_return')->max('id')]
        );
        Service::createLog($logArray);
        return \Response::json($response);
    }


    public function editReturnIssue(Request $request){

        $issueReturnBillNo = $request->input('editModalreturnIssueBillNo');
        $array_size = count($request->editModalProductId);
        
        $productId = $request->input('editModalProductId');
        $quantity = $request->input('editModalQuantity');
        $previousdata = FamsTraIssueReturn::find ($request->id);

        DB::table('fams_tra_issue_return_details')->where('issueReturnBillNoId', $issueReturnBillNo)->delete();

        $totalIssueReturnQuantity = 0;
        $totalIssueReturnAmount = 0;


        for ($i=0; $i < $array_size; $i++) {

            $name = (string) DB::table('fams_product')->where('id',$productId[$i])->value('name');
            $price = DB::table('fams_product')->where('id',$productId[$i])->value('costPrice');

            $totalIssueReturnQuantity = $totalIssueReturnQuantity + $quantity[$i];
            $totalIssueReturnAmount = $totalIssueReturnAmount + $quantity[$i] * $price;

            DB::table('fams_tra_issue_return_details')->insert(['issueReturnBillNoId'=>$issueReturnBillNo,'productId'=>$productId[$i],'issueReturnProductName'=>$name,'issueReturnQuantity'=>$quantity[$i],'issueReturnProductCostPrice'=>$quantity[$i]*$price]);
        }

        DB::table('fams_tra_issue_return')
        ->where('issueReturnBillNo', $issueReturnBillNo)
        ->update(['totalIssueReturnQuantity'=>$totalIssueReturnQuantity, 'totalIssueReturnAmount'=>$totalIssueReturnAmount]);
        $logArray = array(
            'moduleId'  => 2,
            'controllerName'  => 'FamsTransactionReturnIssueController',
            'tableName'  => 'fams_tra_issue_return',
            'operation'  => 'update',
            'previousData'  => $previousdata,
            'primaryIds'  => [$previousdata->id]
        );
        Service::createLog($logArray);
        

        return redirect('famsIssueReturned');
    }

    public function deleteReturnIssue(Request $request){
        $previousdata=FamsTraIssueReturn::find($request->id);
        DB::table('fams_tra_issue_return')->where('issueReturnBillNo', $request->input("issueReturnBillNo"))->delete();
        DB::table('fams_tra_issue_return_details')->where('issueReturnBillNoId', $request->input("issueReturnBillNo"))->delete();
        $logArray = array(
          'moduleId'  => 2,
          'controllerName'  => 'FamsTransactionReturnIssueController',
          'tableName'  => 'fams_tra_issue_return',
          'operation'  => 'delete',
          'previousData'  => $previousdata,
          'primaryIds'  => [$previousdata->id]
      );
        Service::createLog($logArray);

        return redirect('famsIssueReturned');
        
    }

    

    
}
