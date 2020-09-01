<?  php
namespace App\Http\Controllers\accounting\registerReport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Response;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;
use App\accounting\AccOTSRegisterAccount;



class AccOtsRegisterAccountClosingReportController extends Controller
{



	public function index(Request $request){

      $user_branch_id = Auth::user()->branchId;
      $user_project_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectId');


      $projectId = array();
      $branchId = array();


      //Project
      if ($request->searchProject==null) {

          if ($user_branch_id == 1) {
            $projectSelected = null;
            $projectId = DB::table('gnr_project')->pluck('id');
          }
          else{
            $projectSelected = $user_project_id;
            array_push($projectId, $user_project_id);
          }

      }
      else{
        $projectSelected = (int)json_decode($request->searchProject);
        array_push($projectId, $projectSelected);
      }



       //Branch
      if ($request->searchBranch==null) {

        if ($user_branch_id == 1) {
          $branchSelected = null;
            $branchId = DB::table('gnr_branch')->pluck('id');
          }
          else{
            $branchSelected = $user_branch_id;
            array_push($branchId, $branchSelected);
          }

      }
      else{
        $branchSelected = (int) json_decode($request->searchBranch);
          array_push($branchId, $branchSelected);
      }


       //Date From
      if ($request->dateFrom==null) {
        $dateFromSelected = null;
        $startDate = Carbon::toDay();
      }
      else{
        $dateFromSelected = $request->dateFrom;
        $startDate = date('Y-m-d', strtotime($request->dateFrom));
      }

      //Date To
      if ($request->dateTo==null) {
        $dateToSelected = null;
        $endDate = Carbon::toDay();
      }
      else{
        $dateToSelected = $request->dateTo;
        $endDate = date('Y-m-d', strtotime($request->dateTo));
      }


      if($user_branch_id!=1){
        $projects = DB::table('gnr_project')->where('id',$user_project_id)->get();
        $branches = DB::table('gnr_branch')->where('id',$user_branch_id)->get();
      }
      else{
         $projects = DB::table('gnr_project')->get();
         $branches = DB::table('gnr_branch')->whereIn('projectId',$projectId)->orWhere('id',1)->get();
      }

        //Is it the First Request
      if ($request->firstRequest==null) {
        $firstRequest = 1;
      }
      else{
        $firstRequest = 0;
      }

      $accountList = DB::table('acc_ots_account')->whereIn('projectId_fk',$projectId)->whereIn('branchId_fk',$branchId)->pluck('id')->toArray();

      $closingAccounts = DB::table('acc_ots_principal_payment')->whereIn('accId_fk',$accountList)->where('closingDate','>=',$startDate)->where('closingDate','<=',$endDate)->get();


        $memberNames = array();
        $branchNames = array();
        $principalAmounts = array();
        /*$interestDues = array();*/

        $accClosingCharge = DB::table('acc_ots_closing_charge')->where('status',1)->orderBy('id','desc')->value('amount');

        foreach ($closingAccounts as $closingAccount) {
            $account = AccOTSRegisterAccount::find($closingAccount->accId_fk);
            $member = DB::table('acc_ots_member')->where('id',$account->memberId_fk)->value('name');
            $branch = DB::table('gnr_branch')->where('id',$account->branchId_fk)->value('name');
            /*$interestDue = $this->getInterestDue($account->id);*/

            array_push($memberNames, $member);
            array_push($branchNames, $branch);
            array_push($principalAmounts, $account->amount);
            /*array_push($interestDues, $interestDue);*/
        }



        $data = array(
          'closingAccounts'=>$closingAccounts,
          'projects'=>$projects,'branches'=>$branches,
          'startDate'=>$startDate,'endDate'=>$endDate,
          'projectSelected'=>$projectSelected,
          'branchSelected'=>$branchSelected,
          'dateFromSelected'=>$dateFromSelected,
          'dateToSelected'=>$dateToSelected,
          'firstRequest'=>$firstRequest,
          'memberNames'=>$memberNames,
          'branchNames'=>$branchNames,
          'principalAmounts'=>$principalAmounts  /*,'interestDues'=>$interestDues*/

        );


      return view('accounting.registerReport.ots.otsRegisterAccountClosingReport', $data);
    }

    public function getInterestDue($accId)
    {
        $maxPaymentId = DB::table('acc_ots_payment_details')->where('accId_fk',$accId)->max('id');
        $payments = (float) DB::table('acc_ots_payment_details')->where('id','!=',$maxPaymentId)->where('accId_fk',$accId)->sum('amount');
        $openingBalance = (float) DB::table('acc_ots_account')->where('id',$accId)->value('openingBalance');
        $interests = (float) DB::table('acc_ots_interest_details')->where('accId_fk',$accId)->sum('amount');

        $totalDue = $openingBalance + $interests - $payments;

        return $totalDue;

    }


}



?>
