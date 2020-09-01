<?php

namespace App\Http\Controllers\microfin\process;

    /*use Illuminate\Http\Request;
    use App\Http\Requests;
    use Validator;
    use Response;*/
    use Carbon\Carbon;
    use DB;
    use App\Http\Controllers\Controller;
    use App\Http\Controllers\accounting\Accounting;


    class RedoAutoVouchersController extends Controller {

        private $dbLoan;
        private $dbSavDeposit;
        private $dbSavWithdraw;
        private $dbMembers;
        private $dbLoanCollection;
        private $vouchers;

        public function index(){

            // dd('stoped');

            $targetBranchId = 109;
            // $startDate = DB::table('mfn_day_end')->where('branchIdFk',$targetBranchId)->min('date');
            // $startDate = Carbon::parse($startDate);
            $startDate = Carbon::parse('2019-03-06');
            $endDate = $startDate->copy();//->endOfMonth();
            // $endDate = Carbon::parse('2018-11-01');

            $branch = DB::table('gnr_branch')->where('id',$targetBranchId)->first();
            echo 'Branch: '.$branch->branchCode.'--'.$branch->name.' starts from '.$startDate->format('d-m-Y').'<br>';
            while ($startDate->lte($endDate)) {
                $this->redoAutoVouchers($targetBranchId,$startDate->format('Y-m-d'));
                echo $startDate->format('d-m-Y').' Completed<br>';
                $startDate->addDay();
            }
        }

        public function redoAutoVouchers($targetBranchId,$softwareDateFormat){

            $dbSavDeposit = DB::table('mfn_savings_deposit')
            ->where('softDel',0)
            ->where('branchIdFk',$targetBranchId)
            ->where('depositDate','=',$softwareDateFormat)
            ->select('id','accountIdFk','productIdFk','primaryProductIdFk','isAuthorized','depositDate','isTransferred','ledgerIdFk','amount')
            ->get();

            $dbSavWithdraw = DB::table('mfn_savings_withdraw')
            ->where('softDel',0)
            ->where('amount','>',0)
            ->where('branchIdFk',$targetBranchId)
            ->where('withdrawDate','=',$softwareDateFormat)
            ->select('id','amount','ledgerIdFk','accountIdFk','productIdFk','primaryProductIdFk','isAuthorized','withdrawDate','isTransferred')
            ->get();

            $dbLoan = DB::table('mfn_loan')
            ->where('softDel',0)
            ->where(function($query) use ($softwareDateFormat){
                $query->where('isLoanCompleted',0)
                ->orWhere('loanCompletedDate','>=',$softwareDateFormat)
                ->orWhere('loanCompletedDate','=','0000-00-00');
            })
            ->where('branchIdFk',$targetBranchId)
            ->where('disbursementDate','<=',$softwareDateFormat)
            ->select('id','loanAmount','totalRepayAmount','lastInstallmentDate','interestCalculationMethodId','samityIdFk','memberIdFk','additionalFee','productIdFk','loanFormFee','insuranceAmount','ledgerId','disbursementDate','primaryProductIdFk')
            ->get();

            $dbMembers = DB::table('mfn_member_information')
            ->where('softDel',0)
            ->where('status',1)
            ->where('admissionDate','=',$softwareDateFormat)
            ->where('branchId',$targetBranchId)
            ->select('id','primaryProductId','admissionFee','admissionDate','samityId','gender')
            ->get();

            $dbLoanCollection = DB::table('mfn_loan_collection')
            ->where('softDel',0)
            ->where('branchIdFk',$targetBranchId)
            ->where('collectionDate','=',$softwareDateFormat)
            ->select('id','loanIdFk','memberIdFk','primaryProductIdFk','amount','principalAmount','interestAmount','productIdFk','principalAmount','interestAmount','ledgerIdFk','collectionDate','isAuthorized')
            ->get();


            $consData = array(
                'targetBranchId'    => $targetBranchId,
                'softwareDate'      => $softwareDateFormat,
                'dbSavDeposit'      => $dbSavDeposit,
                'dbSavWithdraw'     => $dbSavWithdraw,
                'dbLoan'            => $dbLoan,
                'dbMembers'         => $dbMembers,
                'dbLoanCollection'  => $dbLoanCollection
            );
            
            $autoVoucher = New MfnAutoVoucher($consData);
            $autoVoucher->createCreditVoucher();
            $autoVoucher->createDebitVoucher();    
            $autoVoucher->createJournalVoucher();
        }

    }