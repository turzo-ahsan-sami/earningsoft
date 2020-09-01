<?php

namespace App\Http\Controllers\inventory\transaction\issue;

use App\Http\Controllers\Controller;
use App\InvTraIssue;
use App\InvTraIssueDetails;
use App\GnrBranch;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;


class InvTransactionIssueController extends Controller
{
    public function index(){
    	
        $issues = DB::table('inv_tra_issue as issue')
                    ->join('gnr_branch as branch', 'issue.branchId', '=', 'branch.id')
                    ->select('issue.*', 'branch.name as branchName')
                    ->get();
        $issueDetails = DB::table('inv_tra_issue_details')
                    ->get();
    	return view('inventory/transaction/issue/issue',['issues'=>$issues,'issueDetails'=>$issueDetails]);
    	
    }

    public function addIssue()
    {
        $issueNo = InvTraIssue::max('id')+1;
    	$branches = DB::table('gnr_branch')->get();
    	$products = DB::table('inv_product')->get();
        
    	return view('inventory/transaction/issue/addIssue',['branches'=> $branches,'issueNo'=> $issueNo, 'products'=>$products]);
    }

    public function storeIssue(Request $request){

        $issue = new InvTraIssue;
        $issue_details = new InvTraIssueDetails;

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
            $data[] = array(
            'issueBillNoId' => $request->issueNo,    
            'issueProductId' => (int) json_decode($request->fieldproductId[$i]),
            'issueProductName' => (string) json_decode($request->fieldproductName[$i]),
            'issueQuantity' => (int) json_decode($request->fieldproductQuantity[$i]),
            'issueCostPrice' => (int) json_decode($request->fieldproductPrice[$i])
            
            );            
        
        }
        DB::table('inv_tra_issue_details')->insert($data);
            
                 
        


        $response = array(
            'status' => 'success',
            'msg' => 'Data Stored successfully',
        );
        return \Response::json($response);

    }

    
}
