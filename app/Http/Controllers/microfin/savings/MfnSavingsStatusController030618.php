<?php

    namespace App\Http\Controllers\microfin\savings;

    use Illuminate\Http\Request;
    use App\Http\Requests;
    use Validator;
    use Response;
    use DB;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\Hash;
    use App\Http\Controllers\Controller;
    use App\microfin\MfnMemberType;
    use App\Traits\CreateForm;
    use App\Traits\GetSoftwareDate;
    use App\microfin\savings\MfnSavingsDeposit;
    use App\microfin\savings\MfnSavingsWithdraw;
    use App\microfin\savings\MfnSavingsAccount;
    use App\microfin\samity\MfnSamity;
    use App\microfin\member\MfnMemberInformation;
    use Auth;

    class MfnSavingsStatusController extends Controller {
        use CreateForm;
        use GetSoftwareDate;        

        public function index() {           

           $userBranchId = Auth::user()->branchId;
            $samityList = MfnSamity::active();
            if ($userBranchId!=1) {
                $samityList = $samityList->where('branchId',$userBranchId);
            }
            $samityList = $samityList->select(DB::raw("CONCAT(code, ' - ', name) AS samityName"), 'id')->pluck('samityName','id')->toArray();

            $softwareDate = GetSoftwareDate::getSoftwareDateInFormat();
            
            $data = array(
                'samityList'    =>  $samityList
            );

            return view('microfin.savings.savingsStatus.filteringPart',$data);
        }

        public function printReport(Request $req){
            $userBranchId = Auth::user()->branchId;

            $samityList = MfnSamity::active();
            if ($userBranchId!=1) {
                $samityList = $samityList->where('branchId',$userBranchId);
            }
            if ($req->filSamity!=null) {
                $samityList = $samityList->where('id',$req->filSamity);
            }

            $samityList = $samityList->select('id','name','code')->get();

            $members = DB::table('mfn_member_information')->where('softDel',0)->whereIn('samityId',$samityList->pluck('id'));
            if ($req->filMemberCode!=null) {
                $members = $members->where('code',$req->filMemberCode);
            }

            $members = $members->orderBy('samityId')->get();

            $savings = DB::table('mfn_savings_account')->where('softDel',0)->whereIn('memberIdFk',$members->pluck('id'));
            if ($req->filDateFrom!=null) {
                $dateFrom = Carbon::parse($req->filDateFrom)->format('Y-m-d');
                $savings = $savings->where('accountOpeningDate','>=',$dateFrom);
            }
            if ($req->filDateTo!=null) {
                $dateTo = Carbon::parse($req->filDateFrom)->format('Y-m-d');
                $savings = $savings->where('accountOpeningDate','<=',$dateTo);
            }

            $savings = $savings->orderBy('memberIdFk')->get();

            $deposits = DB::table('mfn_savings_deposit')->where('softDel',0)->whereIn('accountIdFk',$savings->pluck('id'))->get();
            $interests = DB::table('mfn_savings_interest')->whereIn('accIdFk',$savings->pluck('id'))->get();
            $withdraws = DB::table('mfn_savings_withdraw')->where('softDel',0)->whereIn('accountIdFk',$savings->pluck('id'))->get();

            $data = array(
                'samityList'    => $samityList,
                'members'       => $members,
                'savings'       => $savings,
                'deposits'      => $deposits,
                'interests'     => $interests,
                'withdraws'     => $withdraws,
                'filDateFrom'   => $req->filDateFrom,
                'filDateTo'     => $req->filDateTo
            );

            return view('microfin.savings.savingsStatus.viewSavingsStatus',$data);
        }

        public function viewSavingDetails($savingsId, Request $req){
            $savingsId = decrypt($savingsId);

            $deposits = DB::table('mfn_savings_deposit')->where('softDel',0)->where('accountIdFk',$savingsId)->where('amount','>',0);
            $interests = DB::table('mfn_savings_interest')->where('accIdFk',$savingsId)->where('interestAmount','>',0);
            $withdraws = DB::table('mfn_savings_withdraw')->where('softDel',0)->where('accountIdFk',$savingsId)->where('amount','>',0);

            if ($req->dateFrom!=null) {
                $dateFrom = Carbon::parse($req->dateFrom)->format('Y-m-d');
                $deposits = $deposits->where('depositDate','>=',$dateFrom);
                $interests = $interests->where('date','>=',$dateFrom);
                $withdraws = $withdraws->where('withdrawDate','>=',$dateFrom);
            }
            if ($req->dateTo!=null) {
                $dateTo = Carbon::parse($req->dateTo)->format('Y-m-d');
                $deposits = $deposits->where('depositDate','<=',$dateTo);
                $interests = $interests->where('date','<=',$dateTo);
                $withdraws = $withdraws->where('withdrawDate','<=',$dateTo);
            }

            $deposits = $deposits->get();
            $interests = $interests->get();
            $withdraws = $withdraws->get();            


            $dates = $deposits->pluck('depositDate')->toArray();
            $dates = array_merge($dates , $interests->pluck('date')->toArray());
            $dates = array_merge($dates , $withdraws->pluck('withdrawDate')->toArray());            

            $dates = array_unique($dates);
            sort($dates);

            $saving = DB::table('mfn_savings_account')->where('id',$savingsId)->select('savingsCode','memberIdFk')->first();
            $member = DB::table('mfn_member_information')->where('id',$saving->memberIdFk)->select('name','code')->first();

            if ($req->dateFrom!=null) {
                $dateFrom = Carbon::parse($req->dateFrom)->format('d F Y');
            }
            else{
                if (isset($dates[0])) {
                    $dateFrom = Carbon::parse($dates[0])->format('d F Y');
                }
                else{
                    $dateFrom = '';
                }
                
            }
            $dateTo = Carbon::parse($req->dateTo)->format('d F Y');

            $data = array(
                'dates'     => $dates,
                'deposits'  => $deposits,
                'interests' => $interests,
                'withdraws' => $withdraws,
                'saving'    => $saving,
                'member'    => $member,
                'dateFrom'  => $dateFrom,
                'dateTo'    => $dateTo
            );

            return view('microfin.savings.savingsStatus.viewSavingsDetails',$data);
        }

        
    }

        