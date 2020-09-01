<?php

namespace App\Http\Controllers\inventory\transaction\issue;

use App\Http\Controllers\Controller;
use App\inventory\transaction\issue\InvTraIssueReturn;
use App\inventory\transaction\issue\InvTraIssueReturnDetails;
use App\GnrBranch;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;



class InvTransactionReturnIssueController extends Controller
{
    public function index(){

        $issueReturns = InvTraIssueReturn::where('branchId', Auth::user()->branchId)->get();
    	return view('inventory/transaction/issue/IssueReturned',['issueReturns' => $issueReturns]);
    }

    public function addIssueReturn()
    {
        
        return view('inventory/transaction/issue/addIssueReturn');
    }

    // Function for insert data
    public function insertIssueReturn(Request $req){

        $rules = array(
                //'branchId' => 'required'
              );
      $attributeNames = array(
              //'branchId' => 'Branch Name'
          );
      $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
      else{
            $forCountAppnProId = count($req->productId5);
            $forCountAppnProQty = count($req->productQntty5);
          
          if($forCountAppnProId<1){return response()->json('false'); return false;}
            
            //$useBillNo  = substr($req->useBillNo, 2);
            $useMaxId = DB::table('inv_tra_issue_return')->max('id')+1;
            $branchCode = DB::table('gnr_branch')->where('id', Auth::user()->branchId)->value('branchCode');
            $valueForField = 'ISR.'.sprintf('%04d.', $branchCode) . sprintf('%06d', $useMaxId);

            $now = Carbon::now();
            $req->request->add(['issueReturnBillNo' => $valueForField, 'createdDate' => $now]);
            $create = InvTraIssueReturn::create($req->all());
            //return response()->json($create);
            $productDetails = new InvTraIssueReturnDetails;
             
        $dataSet = [];
        for ($i=0; $i < $forCountAppnProId; $i++){
          $dataSet[]= array(
              'issueReturnId'           => $create->id,
              'issueReturnBillNo'       => $create->issueReturnBillNo,
              'productId'               => $req->productId5[$i],
              'quantity'                => $req->productQntty5[$i],
              'price'                   => $req->productPrice[$i],
              'totalAmount'             => $req->proTotalPrice[$i],
              'createdDate'             => $now
          );
        }
        DB::table('inv_tra_issue_return_details')->insert($dataSet);
        $create = InvTraIssueReturn::find ($req->id);
        return response()->json('Success'); 
    }
  }

    public function editInvIssueReturn(Request $req){

        $rules = array(
                //'employeeId' => 'required'
          //'useBillNo' => 'required'
              );
      $attributeNames = array(
              //'employeeId' => 'Employee Name'
          //'useBillNo' => 'Use BillNo'
          );

    $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
      else{
    $idCount = count($req->productId5);
    if($idCount>0){

        $rowFectcs = InvTraIssueReturnDetails::select('id')->where('issueReturnId',$req->id)->get();
        foreach($rowFectcs as $rowFectc){
          InvTraIssueReturnDetails::find($rowFectc->id)->delete();
        }

          $productDetails = new InvTraIssueReturnDetails;
        $dataSet = [];
      for ($i=0; $i < $idCount; $i++){

        $dataSet[]= array(
          'issueReturnId'     => $req->id,
          'issueReturnBillNo' => $req->issueReturnBillNo,
          'productId'         => $req->productId5[$i],
          'quantity'          => $req->productQntty5[$i],
          'price'             => $req->productPrice[$i],
          'totalAmount'       => $req->proTotalPrice[$i],
          'createdDate'       => DB::table('inv_tra_issue_return')->where('id', $req->id)->value('createdDate')
        );
      }
      DB::table('inv_tra_issue_return_details')->insert($dataSet);
      
        $updateIssueReTable = InvTraIssueReturn::find ($req->id);
        $updateIssueReTable->branchId           = $req->branchId;
        $updateIssueReTable->totalQuantity      = $req->totalQuantity;
        $updateIssueReTable->totalAmount        = $req->totalAmount;
        $updateIssueReTable->save();

        $updateDatas = InvTraIssueReturn::where('id', $req->id)->get();
        foreach($updateDatas as $updateData){
          $brnchName    = DB::table('gnr_branch')->select('name')->where('id',$updateData->branchId)->first();
          $dateFromarte = date('d-m-Y', strtotime($updateData->createdDate));
        }
        $data = array(
            'updateDatas'   => $updateDatas,
            'brnchName'     => $brnchName,
            'dateFromarte'  => $dateFromarte,
            'slno'          => $req->slno
          );
      return response()->json($data);
      }
    }
  }

    public function deleteReturnIssue(Request $request){

    	DB::table('inv_tra_issue_return')->where('issueReturnBillNo', $request->input("issueReturnBillNo"))->delete();
        DB::table('inv_tra_issue_return_details')->where('issueReturnBillNoId', $request->input("issueReturnBillNo"))->delete();

        return redirect('issueReturned');
        
    }

    public function getQtyFrmIssueDtlTblfContorller(Request $req) {
        $issueQty = DB::table('inv_tra_issue')
                            ->join('inv_tra_issue_details', 'inv_tra_issue.id', '=', 'inv_tra_issue_details.issueId')
                            ->select('inv_tra_issue_details.issueQuantity')
                            ->where('inv_tra_issue.branchId', $req->branchId)
                            ->where('inv_tra_issue_details.issueProductId', $req->productId)
                            ->sum('inv_tra_issue_details.issueQuantity');

        $issueRetQty = DB::table('inv_tra_issue_return')
                            ->join('inv_tra_issue_return_details', 'inv_tra_issue_return.id', '=', 'inv_tra_issue_return_details.issueReturnId')
                            ->select('inv_tra_issue_return_details.quantity')
                            ->where('inv_tra_issue_return.branchId', $req->branchId)
                            ->where('inv_tra_issue_return_details.productId', $req->productId)
                            ->sum('inv_tra_issue_return_details.quantity');
        $currentIsseQty = (int)($issueQty-$issueRetQty);

        return response()->json($currentIsseQty); 
    }

    public function deitedDataIssueReturnShow(Request $req){
      $editedDatas = InvTraIssueReturn::where('id', $req->id)->get();
       return response()->json($editedDatas);
    }

    //for edit append rows
    public function issueReturnEditAppendRows(Request $req){
            $useDetailsTables =  InvTraIssueReturnDetails::where('issueReturnId',$req->id)->get();
            //$productId = DB::table('inv_product')->select('name','id')->get(); 

            $products = DB::table('inv_tra_issue')
                      ->join('inv_tra_issue_details', 'inv_tra_issue.id', '=', 'inv_tra_issue_details.issueId')
                      ->select('inv_tra_issue.issueBillNo', 'inv_tra_issue_details.issueProductId')
                      ->where('inv_tra_issue.branchId',Auth::user()->branchId)
                      ->get();
                $productSize = count($products);
                    if($productSize>0){
                        foreach($products as $product){
                            $productNames [] =  DB::table('inv_product')->select('name','id')->where('id',$product->issueProductId)->first();   
                        }
                        $productId = array_map("unserialize", array_unique(array_map("serialize", $productNames)));
                    }
            
            $data = array(
            'useDetailsTables'  => $useDetailsTables,
            'productId'         => $productId
            );
            return response()->json($data);
            //return response()->json($useDetailsTables);
        }

    
}
