<?php

namespace App\Http\Controllers\microfin\reports\pra;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Exception;

use App\microfin\loan\MfnLoanWriteOff;

use App\Http\Controllers\microfin\MicroFin;

class PraOneReportController extends Controller {

    private $monthEndMemberInfo;
    private $monthEndTotalMemberInfo;
    private $monthEndLoanInfo;
    private $monthEndLoanInfos;
    private $monthEndLastMonthLoanInfo;
    private $monthEndSavingsInfo;
    private $funOrgs;
    private $loanProducts;
    private $savingProducts;
    private $fiscalYearOpeningDate;

    // for AIS
    private $accOpeningBalance;
    private $vouchers;
    private $voucherDetails;

    private $loanRegister;
    private $loanRegisterPayments;
    private $loanRegisterSchedules;

    // HR
    private $empOrgInfo;

    private $result;

    function __construct(){
        $this->result = collect();
        $this->funOrgs = DB::table('mfn_funding_organization')->get();
        $this->loanProducts = DB::table('mfn_loans_product')->select('id','fundingOrganizationId','pksfFundIdFk')->get();
        $this->savingProducts = DB::table('mfn_saving_product')->select('id','depositTypeIdFk')->get();
    }

    public function index(){

        $yearsOptions = [''=>'--Select Year--'] + MicroFin::getYearsOption();
        $monthsOptions = [''=>'--Select Month--'] + MicroFin::getMonthsOption();
        $loanOptions = array(
            ''   => '--Select--',
            1    => 'With Grihayan',
            2    => 'Without Grihayan'
        );

        $data = array(
            'yearsOptions'  => $yearsOptions,
            'monthsOptions' => $monthsOptions,
            'loanOptions'   => $loanOptions
        );

        return view('microfin/reports/pra/reportFilteringPart',$data);
    }

    public function getReport(Request $req){

        try{
            $filDate = Carbon::parse('01-'.$req->filMonth.'-'.$req->filYear)->endOfMonth()->format('Y-m-d');
            $lastMonthDate = Carbon::parse('01-'.$req->filMonth.'-'.$req->filYear)->subDay()->format('Y-m-d');

            // AIS
            $openingDate = DB::table('acc_opening_balance')->where('openingDate','<',$filDate)->orderBy('openingDate','desc')->value('openingDate');
            $this->accOpeningBalance = DB::table('acc_opening_balance')->where('openingDate',$openingDate);
            $this->vouchers = DB::table('acc_voucher')->where('voucherDate','>',$openingDate)->where('voucherDate','<=',$filDate)->get();
            $this->voucherDetails = DB::table('acc_voucher_details')->whereIn('voucherId',$this->vouchers->pluck('id'))->get();
            $this->loanRegister = DB::table('acc_loan_register_account')->where('loanDate','<=',$filDate)->get();
            $this->loanRegisterPayments = DB::table('acc_loan_register_payments')->where('paymentDate','<=',$filDate)->get();
            $this->loanRegisterSchedules = DB::table('acc_loan_register_payment_schedule')->where('paymentDate','<=',$filDate)->get();
            $this->fiscalYearOpeningDate = $openingDate;

            // MIS
            $this->monthEndMemberInfo = DB::table('mfn_month_end_process_members')->where('date',$filDate)->get();
            $this->monthEndTotalMemberInfo = DB::table('mfn_month_end_process_total_members')->where('date',$filDate)->get();
            if ($openingDate!=null) {
                $this->monthEndLoanInfos = DB::table('mfn_month_end_process_loans')->where('date','>',$openingDate)->where('date','<=',$filDate)->get();
            }
            else{
                $this->monthEndLoanInfos = DB::table('mfn_month_end_process_loans')->where('date','<=',$filDate)->get();                
            }
            $this->monthEndLoanInfo = DB::table('mfn_month_end_process_loans')->where('date',$filDate)->get();
            $this->monthEndLastMonthLoanInfo = DB::table('mfn_month_end_process_loans')->where('date',$lastMonthDate)->get();
            $this->monthEndSavingsInfo = DB::table('mfn_month_end_process_savings')->where('date',$filDate)->get();

            // HR
            $this->empOrgInfo = DB::table('hr_emp_org_info')
                                    ->where('joining_date','<=',$filDate)
                                    ->where(function ($query) use ($filDate){
                                        $query->where('terminate_resignation_date','>',$filDate)
                                            ->orWhere('terminate_resignation_date','0000-00-0');
                                    })
                                    ->select('project_id_fk','project_type_id_fk','position_id_fk')
                                    ->get();

            $pksfFunds = DB::table('mfn_pksf_funds')->get();
            $indicators = $this->getIndicators();

            /// for Other Fund
            $this->createResult($req->filLoanOption,0,[]);

            /// for PKSF fund
            foreach ($pksfFunds as $key => $pksfFund) {
                $productIds = $this->loanProducts->where('pksfFundIdFk',$pksfFund->id)->pluck('id')->toArray();
                $this->createResult($req->filLoanOption,$pksfFund->id,$productIds);
            }

            // this is the array indicating which field should be number format
            $numberFormatCodes = ['012','013','014','015','016','017','020','021','022','023','024','025','026','027','029','031','032','033','034','035','036','037','038','039','040','041'];

            $data = array(
                'pksfFunds' => $pksfFunds,
                'indicators' => $indicators,
                'loanProducts' => $this->loanProducts,
                'dataCollection' => $this->result,
                'numberFormatCodes' => $numberFormatCodes,
            );

        }

        catch(Exception $e){
            dd($e);
            return view('microfin/reports/reportingErrorMessage',['errorMsg'=>$e->getMessage()]);
        }

        return view('microfin/reports/pra/reportBody',$data);    
    }

    public function getIndicators(){

        $indicators = array(
            ['code' => '152', 'name' =>'Number of Zilla', 'gender' => 'N'],
            ['code' => '153', 'name' =>'Number of Upzilla', 'gender' => 'N'],
            ['code' => '154', 'name' =>'Number of Union/Pourasava', 'gender' => 'N'],
            ['code' => '155', 'name' =>'Number of Village/Ward', 'gender' => 'N'],
            ['code' => '156', 'name' =>'Number of Household', 'gender' => 'N'],

            ['code' => '001', 'name' =>'Number Of Branch', 'gender' => 'N'],
            ['code' => '002', 'name' =>'Number Of Samity', 'gender' => 'M'],
            ['code' => '003', 'name' =>'Number Of Samity', 'gender' => 'F'],
            ['code' => '004', 'name' =>'Number of Member', 'gender' => 'M'],
            ['code' => '005', 'name' =>'Number of Member', 'gender' => 'F'],
            ['code' => '006', 'name' =>'Number of Borrower', 'gender' => 'M'],
            ['code' => '007', 'name' =>'Number of Borrower', 'gender' => 'F'],
            ['code' => '008', 'name' =>'Cum. Number of Loan', 'gender' => 'M'],
            ['code' => '009', 'name' =>'Cum. Number of Loan', 'gender' => 'F'],
            ['code' => '010', 'name' =>'Cum. Number of Borrower', 'gender' => 'M'],

            ['code' => '011', 'name' =>'Cum. Number of Borrower', 'gender' => 'F'],
            ['code' => '012', 'name' =>'Savings (Opening Balance) Regular', 'gender' => 'M'],
            ['code' => '013', 'name' =>'Savings (Opening Balance) Regular', 'gender' => 'F'],
            ['code' => '014', 'name' =>'This Month Savings Collection (Regular)', 'gender' => 'M'],
            ['code' => '015', 'name' =>'This Month Savings Collection (Regular)', 'gender' => 'F'],
            ['code' => '016', 'name' =>'This Month Savings Return (Regular)', 'gender' => 'M'],
            ['code' => '017', 'name' =>'This Month Savings Return (Regular)', 'gender' => 'F'],
            ['code' => '018', 'name' =>'This Month Savings Return Number (Regular)', 'gender' => 'N'],
            ['code' => '019', 'name' =>'This Month Savings Depositors Number (Regular)', 'gender' => 'N'],
            ['code' => '020', 'name' =>'Total Regular Savings', 'gender' => 'M'],

            ['code' => '021', 'name' =>'Total Regular Savings', 'gender' => 'F'],
            ['code' => '022', 'name' =>'Total Voluntary Savings', 'gender' => 'M'],
            ['code' => '023', 'name' =>'Total Voluntary Savings', 'gender' => 'F'],
            ['code' => '024', 'name' =>'Total Other Savings', 'gender' => 'M'],
            ['code' => '025', 'name' =>'Total Other Savings', 'gender' => 'F'],
            ['code' => '026', 'name' =>'Total Savings (Regular+Voluntary+Others)', 'gender' => 'M'],
            ['code' => '027', 'name' =>'Total Savings (Regular+Voluntary+Others)', 'gender' => 'F'],
            ['code' => '028', 'name' =>'Average member attendance in weekly meeting', 'gender' => 'N'],
            ['code' => '029', 'name' =>'This month Disbursement', 'gender' => 'N'],
            ['code' => '030', 'name' =>'This month Disburse Number', 'gender' => 'N'],

            ['code' => '031', 'name' =>'Opening Overdue', 'gender' => 'M'],
            ['code' => '032', 'name' =>'Opening Overdue', 'gender' => 'F'],
            ['code' => '033', 'name' =>'This month regular recoverable', 'gender' => 'M'],
            ['code' => '034', 'name' =>'This month regular recoverable', 'gender' => 'F'],
            ['code' => '035', 'name' =>'This Month Regular Recovery', 'gender' => 'M'],
            ['code' => '036', 'name' =>'This Month Regular Recovery', 'gender' => 'F'],
            ['code' => '037', 'name' =>'This Month OD Recovery', 'gender' => 'M'],
            ['code' => '038', 'name' =>'This Month OD Recovery', 'gender' => 'F'],
            ['code' => '039', 'name' =>'This Month Advance Recovery', 'gender' => 'M'],
            ['code' => '040', 'name' =>'This Month Advance Recovery', 'gender' => 'F'],

            ['code' => '041', 'name' =>'This Month total Loan Recovery', 'gender' => 'N'],
            ['code' => '042', 'name' =>'Total Overdue (At the end of month)', 'gender' => 'M'],
            ['code' => '043', 'name' =>'Total Overdue (At the end of month)', 'gender' => 'F'],
            ['code' => '044', 'name' =>'Number of OD Borrower', 'gender' => 'M'],
            ['code' => '045', 'name' =>'Number of OD Borrower', 'gender' => 'F'],
            ['code' => '134', 'name' =>'Overdue (1-30 days) ', 'gender' => 'N'],
            ['code' => '046', 'name' =>'Overdue (31-180 days)', 'gender' => 'N'],
            ['code' => '047', 'name' =>'Overdue (181-365 days)', 'gender' => 'N'],
            ['code' => '048', 'name' =>'Overdue (Above 365 days)', 'gender' => 'N'],
            ['code' => '135', 'name' =>'Watchful Loan Outstanding (Total loan outstanding (Principal) against overdue of 1-30 days)', 'gender' => 'N'],

            ['code' => '049', 'name' =>'Sub standard Loan Outstanding (Total loan outstanding (Principal) against overdue of 31-180 days)', 'gender' => 'N'],
            ['code' => '050', 'name' =>'Doubtful Loan Outstanding  (Total loan outstanding (Principal) against overdue of 181-365 days)', 'gender' => 'N'],
            ['code' => '051', 'name' =>'Bad Loan Outstanding (Total loan outstanding (Principal) against overdue of above 365 days)', 'gender' => 'N'],
            ['code' => '052', 'name' =>'Outstanding of OD Borrower', 'gender' => 'N'],
            ['code' => '053', 'name' =>'Loan Loss Provision (LLP)', 'gender' => 'N'],
            ['code' => '054', 'name' =>'Savings of OD Borrower', 'gender' => 'N'],
            ['code' => '055', 'name' =>'Actual Collection out of regular recoverable amount of this month', 'gender' => 'N'],
            ['code' => '056', 'name' =>'Regular Recoverable amount of this month', 'gender' => 'N'],
            ['code' => '057', 'name' =>'Number of Staff', 'gender' => 'N'],
            ['code' => '058', 'name' =>'Number of Credit Officer/Field Worker', 'gender' => 'N'],
            ['code' => '059', 'name' =>'Loan disbursement (Cumulative)', 'gender' => 'M'],
            ['code' => '060', 'name' =>'Loan disbursement (Cumulative)', 'gender' => 'F'],

            ['code' => '061', 'name' =>'Loan Recovery (Cumulative)', 'gender' => 'M'],
            ['code' => '062', 'name' =>'Loan Recovery (Cumulative)', 'gender' => 'F'],
            ['code' => '063', 'name' =>'Loan Outstanding', 'gender' => 'M'],
            ['code' => '064', 'name' =>'Loan Outstanding', 'gender' => 'F'],
            ['code' => '065', 'name' =>'Total Loan Outstanding of borrower who have more than two payment missing', 'gender' => 'N'],
            ['code' => '066', 'name' =>'Write off Loan (Cumulative)', 'gender' => 'N'],
            ['code' => '067', 'name' =>'Loan outstanding before write off at field', 'gender' => 'N'],
            ['code' => '068', 'name' =>'Fund received (Cumulative)', 'gender' => 'N'],
            ['code' => '069', 'name' =>'Fund refund (Cumulative)', 'gender' => 'N'],
            ['code' => '070', 'name' =>'Fund refund (FY)', 'gender' => 'N'],

            ['code' => '071', 'name' =>'Fund overdue at the end of month', 'gender' => 'N'],
            ['code' => '072', 'name' =>'Service Charge Income (FY)', 'gender' => 'N'],
            ['code' => '073', 'name' =>'Service Charge Income (Cumulative)', 'gender' => 'N'],
            ['code' => '074', 'name' =>'Service Charge Paid  (Cumulative) excluding ID Loan', 'gender' => 'N'],
            ['code' => '075', 'name' =>'Service Charge paid (FY)', 'gender' => 'N'],
            ['code' => '076', 'name' =>'Service charge overdue at the end of month', 'gender' => 'N'],
            ['code' => '077', 'name' =>'Fund refundable (Prin.) in next 12 months', 'gender' => 'N'],
            ['code' => '078', 'name' =>'This Month Service Charge Income', 'gender' => 'N'],
            ['code' => '079', 'name' =>'ID Loan Received (Cumulative)', 'gender' => 'N'],
            ['code' => '080', 'name' =>'ID Loan Refund (Cumulative)', 'gender' => 'N'],
            ['code' => '081', 'name' =>'Service Charge of ID Loan Refund', 'gender' => 'N']            
        );

        return $indicators;
    }

    public function createResult($filLoanOption,$fundId,$productIds){
        if ($fundId==0) {
            $queryData = $this->getDataForOthers($filLoanOption);
        }
        else{
            $queryData = $this->getDataForPksf($fundId,$productIds);
        }
        if ($queryData==null) {
            return false;
        }
        
        // Number Of Branch
        $info['code'] = '001';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['numBranch'];
        $this->result->push((object) $info);

        // Number Of Samity Male
        $info['code'] = '002';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['numSamityM'];
        $this->result->push((object) $info);

        // Number Of Samity Female
        $info['code'] = '003';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['numSamityF'];
        $this->result->push((object) $info);

        // Number of Member Male
        $info['code'] = '004';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['numMemberM'];
        $this->result->push((object) $info);

        // Number of Member Female
        $info['code'] = '005';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['numMemberF'];
        $this->result->push((object) $info);

        // Number of Borrower Male
        $info['code'] = '006';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['numBorrowerM'];
        $this->result->push((object) $info);

        // Number of Borrower Female
        $info['code'] = '007';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['numBorrowerF'];
        $this->result->push((object) $info);

        // Cum. Number of Loan Male
        $info['code'] = '008';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['cumNumLoanM'];
        $this->result->push((object) $info);

        // Cum. Number of Loan Female
        $info['code'] = '009';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['cumNumLoanF'];
        $this->result->push((object) $info);

        // Cum. Number of Borrower Male
        $info['code'] = '010';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['cumNumBorrowerM'];
        $this->result->push((object) $info);

        // Cum. Number of Borrower Female
        $info['code'] = '011';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['cumNumBorrowerF'];
        $this->result->push((object) $info);

        // Savings (Opening Balance) Regular Male
        $info['code'] = '012';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['savOpeBalRegM'];
        $this->result->push((object) $info);

        // Savings (Opening Balance) Regular Female
        $info['code'] = '013';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['savOpeBalRegF'];
        $this->result->push((object) $info);

        // This Month Savings Collection (Regular) Male
        $info['code'] = '014';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['savCollRegM'];
        $this->result->push((object) $info);

        // This Month Savings Collection (Regular) Female
        $info['code'] = '015';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['savCollRegF'];
        $this->result->push((object) $info);

        // This Month Savings Return (Regular) Male
        $info['code'] = '016';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['savWithdrawRegM'];
        $this->result->push((object) $info);

        // This Month Savings Return (Regular) Female
        $info['code'] = '017';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['savWithdrawRegF'];
        $this->result->push((object) $info);

        // Total Regular Savings Male
        $info['code'] = '020';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['tRegSavingsM'];
        $this->result->push((object) $info);

        // Total Regular Savings Female
        $info['code'] = '021';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['tRegSavingsF'];
        $this->result->push((object) $info);

        // Total Voluntary Savings Male
        $info['code'] = '022';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['tVolSavingsM'];
        $this->result->push((object) $info);

        // Total Voluntary Savings Female
        $info['code'] = '023';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['tVolSavingsF'];
        $this->result->push((object) $info);

        // Total Other Savings Male
        $info['code'] = '024';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['tOtherSavingsM'];
        $this->result->push((object) $info);

        // Total Other Savings Female
        $info['code'] = '025';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['tOtherSavingsF'];
        $this->result->push((object) $info);

        // Total Savings Male
        $info['code'] = '026';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['totalSavingsM'];
        $this->result->push((object) $info);

        // Total Savings Female
        $info['code'] = '027';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['totalSavingsF'];
        $this->result->push((object) $info);

        // This Month Disbursement
        $info['code'] = '029';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['disbursement'];
        $this->result->push((object) $info);

        // Opening Overdue Male
        $info['code'] = '031';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['opOverDueM'];
        $this->result->push((object) $info);

        // Opening Overdue Female
        $info['code'] = '032';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['opOverDueF'];
        $this->result->push((object) $info);

        // This Month Regular Recoverable Male
        $info['code'] = '033';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['regRecoverableM'];
        $this->result->push((object) $info);

        // This Month Regular Recoverable Female
        $info['code'] = '034';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['regRecoverableF'];
        $this->result->push((object) $info);

        // This Month Regular Recovery Male
        $info['code'] = '035';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['regRecoveryM'];
        $this->result->push((object) $info);

        // This Month Regular Recovery Female
        $info['code'] = '036';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['regRecoveryF'];
        $this->result->push((object) $info);

        // This Month OD Recovery Male
        $info['code'] = '037';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['odRecoveryM'];
        $this->result->push((object) $info);

        // This Month OD Recovery Female
        $info['code'] = '038';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['odRecoveryF'];
        $this->result->push((object) $info);
        
        // This Month Advance Recovery Male
        $info['code'] = '039';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['advRecoveryM'];
        $this->result->push((object) $info);

        // This Month Advance Recovery Female
        $info['code'] = '040';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['advRecoveryF'];
        $this->result->push((object) $info);

        // This Month Total Recovery
        $info['code'] = '041';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['totalRecovery'];
        $this->result->push((object) $info);

        // Total Overdue Male
        $info['code'] = '042';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['totalOdM'];
        $this->result->push((object) $info);

        // Total Overdue Female
        $info['code'] = '043';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['totalOdF'];
        $this->result->push((object) $info);

        // Number Of OD Borrower Male
        $info['code'] = '044';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['numOdBorrowerM'];
        $this->result->push((object) $info);

        // Number Of OD Borrower Female
        $info['code'] = '045';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['numOdBorrowerF'];
        $this->result->push((object) $info);

        // Overdue (1-30 Days)
        $info['code'] = '134';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['od1to30'];
        $this->result->push((object) $info);

        // Overdue (31-180 Days)
        $info['code'] = '046';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['od31to180'];
        $this->result->push((object) $info);

        // Overdue (181-365 Days)
        $info['code'] = '047';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['od181to365'];
        $this->result->push((object) $info);

        // Overdue (Above 365 Days)
        $info['code'] = '048';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['odBad'];
        $this->result->push((object) $info);

        // Watchful Loan Outstanding (Total Loan Outstanding (Principal) Against Overdue Of 1-30 Days)
        $info['code'] = '135';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['watchLoanOutStan'];
        $this->result->push((object) $info);

        // Sub Standard Loan Outstanding (Total Loan Outstanding (Principal) Against Overdue Of 31-180 Days)
        $info['code'] = '049';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['subLoanOutStan'];
        $this->result->push((object) $info);

        // Doubtful Loan Outstanding (Total Loan Outstanding (Principal) Against Overdue Of 181-365 Days)
        $info['code'] = '050';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['doubtLoanOutStan'];
        $this->result->push((object) $info);

        // Bad Loan Outstanding (Total Loan Outstanding (Principal) Against Overdue Of Above 365 Days)
        $info['code'] = '051';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['badLoanOutStan'];
        $this->result->push((object) $info);

        // Outstanding Of OD Borrower
        $info['code'] = '052';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['odOutStan'];
        $this->result->push((object) $info);

        // Loan Loss Provision (LLP)
        $info['code'] = '053';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['llpBalance'];
        $this->result->push((object) $info);

        // Savings Of OD Borrower
        $info['code'] = '054';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['savBalOdBorrower'];
        $this->result->push((object) $info);

        // Actual Collection Out Of Regular Recoverable Amount Of This Month
        $info['code'] = '055';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['regRecovery'];
        $this->result->push((object) $info);

        // Regular Recoverable Amount Of This Month
        $info['code'] = '056';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['regRecoverable'];
        $this->result->push((object) $info);

        // Number Of Staff
        $info['code'] = '057';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['numStaff'];
        $this->result->push((object) $info);

        // Number Of Credit Officer/Field Worker
        $info['code'] = '058';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['numCO'];
        $this->result->push((object) $info);

        // Loan Disbursement (Cumulative) Male
        $info['code'] = '059';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['cumLoanDisM'];
        $this->result->push((object) $info);

        // Loan Disbursement (Cumulative) Female
        $info['code'] = '060';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['cumLoanDisF'];
        $this->result->push((object) $info);

        // Loan Recovery (Cumulative) Male
        $info['code'] = '061';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['cumRecoveryM'];
        $this->result->push((object) $info);

        // Loan Recovery (Cumulative) Female
        $info['code'] = '062';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['cumRecoveryF'];
        $this->result->push((object) $info);

        // Loan Outstanding Male
        $info['code'] = '063';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['loanOutstandingM'];
        $this->result->push((object) $info);

        // Loan Outstanding Female
        $info['code'] = '064';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['loanOutstandingF'];
        $this->result->push((object) $info);

        // Total Loan Outstanding Of Borrower Who Have More Than Two Payment Missing
        $info['code'] = '065';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['loanOutstan2Due'];
        $this->result->push((object) $info);

        // Write Off Loan (Cumulative)
        $info['code'] = '066';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['cumWrtiteOff'];
        $this->result->push((object) $info);

        // Loan Outstanding Before Write Off At Field
        $info['code'] = '067';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['loanOutBefWriteOff'];
        $this->result->push((object) $info);

        // Fund Received (Cumulative)
        $info['code'] = '068';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['cumFundReceived'];
        $this->result->push((object) $info);

        // Fund Refund (Cumulative)
        $info['code'] = '069';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['cumFundRefund'];
        $this->result->push((object) $info);

        // Fund Refund (Cumulative)
        $info['code'] = '070';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['fundRefundFY'];
        $this->result->push((object) $info);

        // Fund Refund (Cumulative)
        $info['code'] = '071';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['fundOverDue'];
        $this->result->push((object) $info);

        // Service Charge Income (FY)
        $info['code'] = '072';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['serChargeIncomeFY'];
        $this->result->push((object) $info);

        // Service Charge Income (Cumulative)
        $info['code'] = '073';
        $info['pksfFundId'] = $fundId;
        $info['result'] = $queryData['cumSerChargeIncome'];
        $this->result->push((object) $info);

    }

    public function getDataForOthers($filLoanOption){
       
        if ($filLoanOption==1) {
            $funOrgIds = $this->funOrgs->where('id','!=',1)->pluck('id')->toArray();
            $loanIdProductIds = $this->loanProducts->whereIn('fundingOrganizationId',$funOrgIds)->pluck('id')->toArray();
            $projectIds = $this->funOrgs->where('id','!=',1)->pluck('projectIdFk')->toArray();   
            $projectTypeIds = $this->funOrgs->where('id','!=',1)->pluck('projectTypeIdFk')->toArray();   
        }
        else{
            $funOrgIds = $this->funOrgs->where('id','!=',1)->where('id','!=',3)->pluck('id')->toArray();
            $loanIdProductIds = $this->loanProducts->whereIn('fundingOrganizationId',$funOrgIds)->pluck('id')->toArray();
            $projectIds = $this->funOrgs->where('id','!=',1)->where('id','!=',3)->pluck('projectIdFk')->toArray();   
            $projectTypeIds = $this->funOrgs->where('id','!=',1)->where('id','!=',3)->pluck('projectTypeIdFk')->toArray();
        }
        $projectIds = array_unique($projectIds);
        $projectTypeIds = array_unique($projectTypeIds);

        $curMemberInfo = $this->monthEndTotalMemberInfo->whereIn('fundingOrgIdFk',$funOrgIds);
        $curLoanInfo = $this->monthEndLoanInfo->whereIn('productIdFk',$loanIdProductIds);
        $curLoanInfos = $this->monthEndLoanInfos->whereIn('productIdFk',$loanIdProductIds);
        $lastMonthLoanInfo = $this->monthEndLastMonthLoanInfo->whereIn('productIdFk',$loanIdProductIds);
        $curSavingsInfo = $this->monthEndSavingsInfo->whereIn('productIdFk',$loanIdProductIds);
        $curEmpInfo = $this->empOrgInfo->whereIn('project_id_fk',$projectIds)->whereIn('project_type_id_fk',$projectTypeIds);
        $curLoanRegisters = $this->loanRegister->whereIn('projectId_fk',$projectIds)->whereIn('projectTypeId_fk',$projectTypeIds);
        $loanRegisterAccIds = $curLoanRegisters->pluck('id')->toArray();
        $curLoanRegisterPayments = $this->loanRegisterPayments->whereIn('accId_fk',$loanRegisterAccIds);

        $numBranch = $curMemberInfo->groupBy('branchIdFk')->count();
        $numSamityM = $curMemberInfo->sum('mClosingSamityNo');
        $numSamityF = $curMemberInfo->sum('fClosingSamityNo');
        $numMemberM = $curMemberInfo->sum('mClosingMember');
        $numMemberF = $curMemberInfo->sum('fClosingMember');

        $numBorrowerM = $curLoanInfo->where('genderTypeId',1)->sum('borrowerNo');
        $numBorrowerF = $curLoanInfo->where('genderTypeId',2)->sum('borrowerNo');
        $cumNumLoanM = $curLoanInfo->where('genderTypeId',1)->sum('cumLoanNo');
        $cumNumLoanF = $curLoanInfo->where('genderTypeId',2)->sum('cumLoanNo');
        $cumNumBorrowerM = $curLoanInfo->where('genderTypeId',1)->sum('cumBorrowerNo');
        $cumNumBorrowerF = $curLoanInfo->where('genderTypeId',2)->sum('cumBorrowerNo');

        $savOpeBalRegM = $curSavingsInfo->where('savingProductIdFk',1)->where('genderTypeId',1)->sum('openingBalance');
        $savOpeBalRegF = $curSavingsInfo->where('savingProductIdFk',1)->where('genderTypeId',2)->sum('openingBalance');

        // This Month Savings Collection (Regular)
        $savCollRegM = $curSavingsInfo->where('savingProductIdFk',1)->where('genderTypeId',1)->sum('depositCollection');
        $savCollRegF = $curSavingsInfo->where('savingProductIdFk',1)->where('genderTypeId',2)->sum('depositCollection');

        // This Month Savings Return (Regular)
        $savWithdrawRegM = $curSavingsInfo->where('savingProductIdFk',1)->where('genderTypeId',1)->sum('savingRefund');
        $savWithdrawRegF = $curSavingsInfo->where('savingProductIdFk',1)->where('genderTypeId',2)->sum('savingRefund');

        // Total Regular Savings
        $tRegSavingsM = $curSavingsInfo->where('savingProductIdFk',1)->where('genderTypeId',1)->sum('closingBalance');
        $tRegSavingsF = $curSavingsInfo->where('savingProductIdFk',1)->where('genderTypeId',2)->sum('closingBalance');

        // get the Voluntary Savings Product Ids
        $volSavProductIds = $this->savingProducts->where('depositTypeIdFk',2)->pluck('id')->toArray();

        // Total Voluntary Savings
        $tVolSavingsM = $curSavingsInfo->whereIn('savingProductIdFk',$volSavProductIds)->where('genderTypeId',1)->sum('closingBalance');
        $tVolSavingsF = $curSavingsInfo->whereIn('savingProductIdFk',$volSavProductIds)->where('genderTypeId',2)->sum('closingBalance');

        // get the Other Savings Product Ids
        $otherSavProductIds = $this->savingProducts->where('depositTypeIdFk','>',2)->pluck('id')->toArray();

        // Total Other Savings
        $tOtherSavingsM = $curSavingsInfo->whereIn('savingProductIdFk',$otherSavProductIds)->where('genderTypeId',1)->sum('closingBalance');
        $tOtherSavingsF = $curSavingsInfo->whereIn('savingProductIdFk',$otherSavProductIds)->where('genderTypeId',2)->sum('closingBalance');

        // Total Savings (Regular+Voluntary+Others)
        $totalSavingsM = $curSavingsInfo->where('genderTypeId',1)->sum('closingBalance');
        $totalSavingsF = $curSavingsInfo->where('genderTypeId',2)->sum('closingBalance');

        //  This Month Disbursement
        $disbursement = $curLoanInfo->sum('disbursedAmount');

        // This Month Disburse Number
        $disbursementNum = $curLoanInfo->sum('disbursedAmount');

        // Opening Overdue
        $opOverDueM = $lastMonthLoanInfo->where('genderTypeId',1)->sum(function ($obj){
                return $obj->watchfulOverdue + $obj->substandardOverdue + $obj->doubtfullOverdue + $obj->badOverdue;
            });
        $opOverDueF = $lastMonthLoanInfo->where('genderTypeId',2)->sum(function ($obj){
                return $obj->watchfulOverdue + $obj->substandardOverdue + $obj->doubtfullOverdue + $obj->badOverdue;
            });

        // This Month Regular Recoverable
        $regRecoverableM = $curLoanInfo->where('genderTypeId',1)->sum('principalRecoverableAmount');
        $regRecoverableF = $curLoanInfo->where('genderTypeId',2)->sum('principalRecoverableAmount');

        // This Month Regular Recovery
        $regRecoveryM = $curLoanInfo->where('genderTypeId',1)->sum('principalRegularAmount');
        $regRecoveryF = $curLoanInfo->where('genderTypeId',2)->sum('principalRegularAmount');

        // This Month OD Recovery
        $odRecoveryM = $curLoanInfo->where('genderTypeId',1)->sum('principalDueAmount');
        $odRecoveryF = $curLoanInfo->where('genderTypeId',2)->sum('principalDueAmount');

        // This Month Advance Recovery
        $advRecoveryM = $curLoanInfo->where('genderTypeId',1)->sum('principalAdvanceAmount');
        $advRecoveryF = $curLoanInfo->where('genderTypeId',2)->sum('principalAdvanceAmount');

        // This Month Total Recovery
        $totalRecovery = $curLoanInfo->sum('principalRecoveryAmount');

        // Total Overdue
        $totalOdM = $curLoanInfo->where('genderTypeId',1)->sum(function ($obj){
                return $obj->watchfulOverdue + $obj->substandardOverdue + $obj->doubtfullOverdue + $obj->badOverdue;
            });
        $totalOdF = $curLoanInfo->where('genderTypeId',2)->sum(function ($obj){
                return $obj->watchfulOverdue + $obj->substandardOverdue + $obj->doubtfullOverdue + $obj->badOverdue;
            });

        // Number Of OD Borrower
        $numOdBorrowerM = $curLoanInfo->where('genderTypeId',1)->sum('noOfDueLoanee');
        $numOdBorrowerF = $curLoanInfo->where('genderTypeId',2)->sum('noOfDueLoanee');

        // Overdue (1-30 Days)
        $od1to30 = $curLoanInfo->sum('watchfulOverdue');

        // Overdue (31-180 Days)
        $od31to180 = $curLoanInfo->sum('substandardOverdue');

        // Overdue (181-365 Days)
        $od181to365 = $curLoanInfo->sum('doubtfullOverdue');

        // Overdue (Above 365 Days)
        $odBad = $curLoanInfo->sum('badOverdue');

        // Watchful Loan Outstanding (Total Loan Outstanding (Principal) Against Overdue Of 1-30 Days)
        $watchLoanOutStan = $curLoanInfo->sum('watchfulOutstanding');

        // Sub Standard Loan Outstanding (Total Loan Outstanding (Principal) Against Overdue Of 31-180 Days)
        $subLoanOutStan = $curLoanInfo->sum('substandardOutstanding');

        // Doubtful Loan Outstanding (Total Loan Outstanding (Principal) Against Overdue Of 181-365 Days)
        $doubtLoanOutStan = $curLoanInfo->sum('doubtfullOutstanding');

        // Bad Loan Outstanding (Total Loan Outstanding (Principal) Against Overdue Of Above 365 Days)
        $badLoanOutStan = $curLoanInfo->sum('badOutstanding');

        // Outstanding Of OD Borrower
        $odOutStan = $watchLoanOutStan + $subLoanOutStan + $doubtLoanOutStan + $badLoanOutStan;

        // LLP
        // llp head id is 127
        $llpLegderIds = MicroFin::getAllChildsOfAParentInLedger(127);
        $openingDebit = $this->accOpeningBalance->whereIn('projectId',$projectIds)->whereIn('projectTypeId',$projectTypeIds)->whereIn('ledgerId',$llpLegderIds)->sum('debitAmount');
        $openingCredit = $this->accOpeningBalance->whereIn('projectId',$projectIds)->whereIn('projectTypeId',$projectTypeIds)->whereIn('ledgerId',$llpLegderIds)->sum('creditAmount');
        $voucherIds = $this->vouchers->whereIn('projectId',$projectIds)->whereIn('projectTypeId',$projectTypeIds)->pluck('id')->toArray();
        $currDebit = $this->voucherDetails->whereIn('voucherId',$voucherIds)->whereIn('debitAcc',$llpLegderIds)->sum('amount');
        $currCredit = $this->voucherDetails->whereIn('voucherId',$voucherIds)->whereIn('creditAcc',$llpLegderIds)->sum('amount');
        $llpBalance = $openingCredit + $currCredit - $openingDebit - $currDebit;

        // 054 Savings Of OD Borrower
        $savBalOdBorrower = $curLoanInfo->sum('savingBalanceOfOverdueLoanee');

        // 055 Actual Collection Out Of Regular Recoverable Amount Of This Month
        $regRecovery = $curLoanInfo->sum('principalRegularAmount');

        // 056 Regular Recoverable Amount Of This Month
        $regRecoverable = $curLoanInfo->sum('principalRecoverableAmount');

        // 057 Number Of Staff
        $numStaff = count($curEmpInfo);

        // 058 Number Of Credit Officer/Field Worker
        $numCO = count($curEmpInfo->where('position_id_fk',122));

        // 059-060 Loan Disbursement (Cumulative)
        $cumLoanDisM = $curLoanInfo->where('genderTypeId',1)->sum('closingDisbursedAmount');
        $cumLoanDisF = $curLoanInfo->where('genderTypeId',2)->sum('closingDisbursedAmount');

        // 061-062 Loan Recovery (Cumulative)
        $cumRecoveryM = $curLoanInfo->where('genderTypeId',1)->sum('cumRecoveryPrincipal');
        $cumRecoveryF = $curLoanInfo->where('genderTypeId',2)->sum('cumRecoveryPrincipal');

        // 063-064 Loan Outstanding
        $loanOutstandingM = $curLoanInfo->where('genderTypeId',1)->sum('closingOutstandingAmount');
        $loanOutstandingF = $curLoanInfo->where('genderTypeId',2)->sum('closingOutstandingAmount');

        // 065 Total Loan Outstanding Of Borrower Who Have More Than Two Payment Missing
        $loanOutstan2Due = $curLoanInfo->sum('outstandingWithMoreThan2DueInstallments');

        // 066 Write Off Loan (Cumulative)
        $cumWrtiteOff = $curLoanInfo->sum('cumWriteOffPrincipal');

        // 067 Loan Outstanding Before Write Off At Field
        $loanOutBefWriteOff = $curLoanInfo->sum('closingOutstandingAmount') + $curLoanInfo->sum('cumWriteOffPrincipal');

        // 068 Fund Received (Cumulative)
        $cumFundReceived = $curLoanRegisters->sum('loanAmount');

        // 069 Fund Refund (Cumulative)
        $cumFundRefund = $curLoanRegisterPayments->sum('principalAmount');

        // 070 Fund Refund (FY)
        if ($this->fiscalYearOpeningDate!=null) {
            $fundRefundFY = $curLoanRegisterPayments->where('paymentDate','>',$this->fiscalYearOpeningDate)->sum('principalAmount');
        }
        else{
            $fundRefundFY = $curLoanRegisterPayments->sum('principalAmount');            
        }

        // 071 Fund Overdue At The End Of Month
        $fundOverDue = 0;
        foreach ($curLoanRegisters as $curLoanRegister) {
            $curFundOverDue = $this->loanRegisterSchedules->where('accId_fk',$curLoanRegister->id)->sum('principalAmount') - $curLoanRegisterPayments->sum('principalAmount');
            if ($curFundOverDue>=1) {
                $fundOverDue += $curFundOverDue;
            }            
        }

        // 072 Service Charge Income (FY)
        $serChargeIncomeFY = $curLoanInfos->sum('recoveryAmount') - $curLoanInfos->sum('principalRecoveryAmount');

        // 073 Service Charge Income (Cumulative)
        $cumSerChargeIncome = $curLoanInfo->sum('cumRecovery') - $curLoanInfo->sum('cumRecoveryPrincipal');

        $data = array(
            'numBranch'             => $numBranch,
            'numSamityM'            => $numSamityM,
            'numSamityF'            => $numSamityF,
            'numMemberM'            => $numMemberM,
            'numMemberF'            => $numMemberF,
            'numBorrowerM'          => $numBorrowerM,
            'numBorrowerF'          => $numBorrowerF,
            'cumNumLoanM'           => $cumNumLoanM,
            'cumNumLoanF'           => $cumNumLoanF,
            'cumNumBorrowerM'       => $cumNumBorrowerM,
            'cumNumBorrowerF'       => $cumNumBorrowerF,
            'savOpeBalRegM'         => $savOpeBalRegM,
            'savOpeBalRegF'         => $savOpeBalRegF,
            'savCollRegM'           => $savCollRegM,
            'savCollRegF'           => $savCollRegF,
            'savWithdrawRegM'       => $savWithdrawRegM,
            'savWithdrawRegF'       => $savWithdrawRegF,
            'tRegSavingsM'          => $tRegSavingsM,
            'tRegSavingsF'          => $tRegSavingsF,
            'tVolSavingsM'          => $tVolSavingsM,
            'tVolSavingsF'          => $tVolSavingsF,
            'tOtherSavingsM'        => $tOtherSavingsM,
            'tOtherSavingsF'        => $tOtherSavingsF,
            'totalSavingsM'         => $totalSavingsM,
            'totalSavingsF'         => $totalSavingsF,
            'disbursement'          => $disbursement,
            'opOverDueM'            => $opOverDueM,
            'opOverDueF'            => $opOverDueF,
            'regRecoverableM'       => $regRecoverableM,
            'regRecoverableF'       => $regRecoverableF,
            'regRecoveryM'          => $regRecoveryM,
            'regRecoveryF'          => $regRecoveryF,
            'odRecoveryM'           => $odRecoveryM,
            'odRecoveryF'           => $odRecoveryF,
            'advRecoveryM'          => $advRecoveryM,
            'advRecoveryF'          => $advRecoveryF,
            'totalRecovery'         => $totalRecovery,
            'totalOdM'              => $totalOdM,
            'totalOdF'              => $totalOdF,
            'numOdBorrowerM'        => $numOdBorrowerM,
            'numOdBorrowerF'        => $numOdBorrowerF,
            'od1to30'               => $od1to30,
            'od31to180'             => $od31to180,
            'od181to365'            => $od181to365,
            'odBad'                 => $odBad,
            'watchLoanOutStan'      => $watchLoanOutStan,
            'subLoanOutStan'        => $subLoanOutStan,
            'doubtLoanOutStan'      => $doubtLoanOutStan,
            'badLoanOutStan'        => $badLoanOutStan,
            'odOutStan'             => $odOutStan,
            'llpBalance'            => $llpBalance,
            'savBalOdBorrower'      => $savBalOdBorrower,
            'regRecovery'           => $regRecovery,
            'regRecoverable'        => $regRecoverable,
            'numStaff'              => $numStaff,
            'numCO'                 => $numCO,
            'cumLoanDisM'           => $cumLoanDisM,
            'cumLoanDisF'           => $cumLoanDisF,
            'cumRecoveryM'          => $cumRecoveryM,
            'cumRecoveryF'          => $cumRecoveryF,
            'loanOutstandingM'      => $loanOutstandingM,
            'loanOutstandingF'      => $loanOutstandingF,
            'loanOutstan2Due'       => $loanOutstan2Due,
            'cumWrtiteOff'          => $cumWrtiteOff,
            'loanOutBefWriteOff'    => $loanOutBefWriteOff,
            'cumFundReceived'       => $cumFundReceived,
            'cumFundRefund'         => $cumFundRefund,
            'fundRefundFY'          => $fundRefundFY,
            'fundOverDue'           => $fundOverDue,
            'serChargeIncomeFY'     => $serChargeIncomeFY,
            'cumSerChargeIncome'    => $cumSerChargeIncome,
        );

        return $data;
    }

    public function getDataForPksf($fundId,$productIds){
        if (count($productIds)==0) {
            $data = array(
                'numBranch'  => '',
                'numSamityM' => '',
                'numSamityF' => '',
            );
            return null;
        }

        $projectId = $this->funOrgs->where('id',1)->first()->projectIdFk;
        $projectTypeId = $this->funOrgs->where('id',1)->first()->projectTypeIdFk;
        
        $curMemberInfo = $this->monthEndMemberInfo->whereIn('loanProductIdFk',$productIds);
        $curLoanInfos = $this->monthEndLoanInfos->whereIn('productIdFk',$productIds);
        $curLoanInfo = $this->monthEndLoanInfo->whereIn('productIdFk',$productIds);
        $curSavingsInfo = $this->monthEndSavingsInfo->whereIn('productIdFk',$productIds);
        $curEmpInfo = $this->empOrgInfo->where('project_id_fk',$projectId)->where('project_type_id_fk',$projectTypeId);
        $lastMonthLoanInfo = $this->monthEndLastMonthLoanInfo->whereIn('productIdFk',$productIds);
        $curLoanRegisters = $this->loanRegister->where('projectId_fk',$projectId)->where('projectTypeId_fk',$projectTypeId);
        $loanRegisterAccIds = $curLoanRegisters->pluck('id')->toArray();
        $curLoanRegisterPayments = $this->loanRegisterPayments->whereIn('accId_fk',$loanRegisterAccIds);

        
        $numBranch = $curMemberInfo->groupBy('branchIdFk')->count();     
        $numSamityM = $curMemberInfo->where('genderTypeId',1)->sum('closingSamityNo');
        $numSamityF = $curMemberInfo->where('genderTypeId',2)->sum('closingSamityNo');
        $numMemberM = $curMemberInfo->where('genderTypeId',1)->sum('closingMember');
        $numMemberF = $curMemberInfo->where('genderTypeId',2)->sum('closingMember');

        $numBorrowerM = $curLoanInfo->where('genderTypeId',1)->sum('borrowerNo');
        $numBorrowerF = $curLoanInfo->where('genderTypeId',2)->sum('borrowerNo');
        $cumNumLoanM = $curLoanInfo->where('genderTypeId',1)->sum('cumLoanNo');
        $cumNumLoanF = $curLoanInfo->where('genderTypeId',2)->sum('cumLoanNo');
        $cumNumBorrowerM = $curLoanInfo->where('genderTypeId',1)->sum('cumBorrowerNo');
        $cumNumBorrowerF = $curLoanInfo->where('genderTypeId',2)->sum('cumBorrowerNo');

        $savOpeBalRegM = $curSavingsInfo->where('savingProductIdFk',1)->where('genderTypeId',1)->sum('openingBalance');
        $savOpeBalRegF = $curSavingsInfo->where('savingProductIdFk',1)->where('genderTypeId',2)->sum('openingBalance');

        // This Month Savings Collection (Regular)
        $savCollRegM = $curSavingsInfo->where('savingProductIdFk',1)->where('genderTypeId',1)->sum('depositCollection');
        $savCollRegF = $curSavingsInfo->where('savingProductIdFk',1)->where('genderTypeId',2)->sum('depositCollection');

        // This Month Savings Return (Regular)
        $savWithdrawRegM = $curSavingsInfo->where('savingProductIdFk',1)->where('genderTypeId',1)->sum('savingRefund');
        $savWithdrawRegF = $curSavingsInfo->where('savingProductIdFk',1)->where('genderTypeId',2)->sum('savingRefund');

        // Total Regular Savings
        $tRegSavingsM = $curSavingsInfo->where('savingProductIdFk',1)->where('genderTypeId',1)->sum('closingBalance');
        $tRegSavingsF = $curSavingsInfo->where('savingProductIdFk',1)->where('genderTypeId',2)->sum('closingBalance');

        // get the Voluntary Savings Product Ids
        $volSavProductIds = $this->savingProducts->where('depositTypeIdFk',2)->pluck('id')->toArray();

        // Total Voluntary Savings
        $tVolSavingsM = $curSavingsInfo->whereIn('savingProductIdFk',$volSavProductIds)->where('genderTypeId',1)->sum('closingBalance');
        $tVolSavingsF = $curSavingsInfo->whereIn('savingProductIdFk',$volSavProductIds)->where('genderTypeId',2)->sum('closingBalance');

        // get the Other Savings Product Ids
        $otherSavProductIds = $this->savingProducts->where('depositTypeIdFk','>',2)->pluck('id')->toArray();

        // Total Other Savings
        $tOtherSavingsM = $curSavingsInfo->whereIn('savingProductIdFk',$otherSavProductIds)->where('genderTypeId',1)->sum('closingBalance');
        $tOtherSavingsF = $curSavingsInfo->whereIn('savingProductIdFk',$otherSavProductIds)->where('genderTypeId',2)->sum('closingBalance');

        // Total Savings (Regular+Voluntary+Others)
        $totalSavingsM = $curSavingsInfo->where('genderTypeId',1)->sum('closingBalance');
        $totalSavingsF = $curSavingsInfo->where('genderTypeId',2)->sum('closingBalance');

        //  This Month Disbursement
        $disbursement = $curLoanInfo->sum('disbursedAmount');

        // This Month Disburse Number
        $disbursementNum = $curLoanInfo->sum('disbursedAmount');

        // Opening Overdue
        $opOverDueM = $lastMonthLoanInfo->where('genderTypeId',1)->sum(function ($obj){
                return $obj->watchfulOverdue + $obj->substandardOverdue + $obj->doubtfullOverdue + $obj->badOverdue;
            });
        $opOverDueF = $lastMonthLoanInfo->where('genderTypeId',2)->sum(function ($obj){
                return $obj->watchfulOverdue + $obj->substandardOverdue + $obj->doubtfullOverdue + $obj->badOverdue;
            });

        // This Month Regular Recoverable
        $regRecoverableM = $curLoanInfo->where('genderTypeId',1)->sum('principalRecoverableAmount');
        $regRecoverableF = $curLoanInfo->where('genderTypeId',2)->sum('principalRecoverableAmount');

        // This Month Regular Recovery
        $regRecoveryM = $curLoanInfo->where('genderTypeId',1)->sum('principalRegularAmount');
        $regRecoveryF = $curLoanInfo->where('genderTypeId',2)->sum('principalRegularAmount');

        // This Month OD Recovery
        $odRecoveryM = $curLoanInfo->where('genderTypeId',1)->sum('principalDueAmount');
        $odRecoveryF = $curLoanInfo->where('genderTypeId',2)->sum('principalDueAmount');

        // This Month Advance Recovery
        $advRecoveryM = $curLoanInfo->where('genderTypeId',1)->sum('principalAdvanceAmount');
        $advRecoveryF = $curLoanInfo->where('genderTypeId',2)->sum('principalAdvanceAmount');

        // This Month Total Recovery
        $totalRecovery = $curLoanInfo->sum('principalRecoveryAmount');

        // Total Overdue
        $totalOdM = $curLoanInfo->where('genderTypeId',1)->sum(function ($obj){
                return $obj->watchfulOverdue + $obj->substandardOverdue + $obj->doubtfullOverdue + $obj->badOverdue;
            });
        $totalOdF = $curLoanInfo->where('genderTypeId',2)->sum(function ($obj){
                return $obj->watchfulOverdue + $obj->substandardOverdue + $obj->doubtfullOverdue + $obj->badOverdue;
            });

        // Number Of OD Borrower
        $numOdBorrowerM = $curLoanInfo->where('genderTypeId',1)->sum('noOfDueLoanee');
        $numOdBorrowerF = $curLoanInfo->where('genderTypeId',2)->sum('noOfDueLoanee');

        // Overdue (1-30 Days)
        $od1to30 = $curLoanInfo->sum('watchfulOverdue');

        // Overdue (31-180 Days)
        $od31to180 = $curLoanInfo->sum('substandardOverdue');

        // Overdue (181-365 Days)
        $od181to365 = $curLoanInfo->sum('doubtfullOverdue');

        // Overdue (Above 365 Days)
        $odBad = $curLoanInfo->sum('badOverdue');

        // Watchful Loan Outstanding (Total Loan Outstanding (Principal) Against Overdue Of 1-30 Days)
        $watchLoanOutStan = $curLoanInfo->sum('watchfulOutstanding');

        // Sub Standard Loan Outstanding (Total Loan Outstanding (Principal) Against Overdue Of 31-180 Days)
        $subLoanOutStan = $curLoanInfo->sum('substandardOutstanding');

        // Doubtful Loan Outstanding (Total Loan Outstanding (Principal) Against Overdue Of 181-365 Days)
        $doubtLoanOutStan = $curLoanInfo->sum('doubtfullOutstanding');

        // Bad Loan Outstanding (Total Loan Outstanding (Principal) Against Overdue Of Above 365 Days)
        $badLoanOutStan = $curLoanInfo->sum('badOutstanding');

        // Outstanding Of OD Borrower
        $odOutStan = $watchLoanOutStan + $subLoanOutStan + $doubtLoanOutStan + $badLoanOutStan;

        // LLP
        // llp head id is 127
        $llpLegderIds = MicroFin::getAllChildsOfAParentInLedger(127);
        $openingDebit = $this->accOpeningBalance->where('projectId',$projectId)->where('projectTypeId',$projectTypeId)->whereIn('ledgerId',$llpLegderIds)->sum('debitAmount');
        $openingCredit = $this->accOpeningBalance->where('projectId',$projectId)->where('projectTypeId',$projectTypeId)->whereIn('ledgerId',$llpLegderIds)->sum('creditAmount');
        $voucherIds = $this->vouchers->where('projectId',$projectId)->where('projectTypeId',$projectTypeId)->pluck('id')->toArray();
        $currDebit = $this->voucherDetails->whereIn('voucherId',$voucherIds)->whereIn('debitAcc',$llpLegderIds)->sum('amount');
        $currCredit = $this->voucherDetails->whereIn('voucherId',$voucherIds)->whereIn('creditAcc',$llpLegderIds)->sum('amount');
        $llpBalance = $openingCredit + $currCredit - $openingDebit - $currDebit;

        // 054 Savings Of OD Borrower
        $savBalOdBorrower = $curLoanInfo->sum('savingBalanceOfOverdueLoanee');

        // 055 Actual Collection Out Of Regular Recoverable Amount Of This Month
        $regRecovery = $curLoanInfo->sum('principalRegularAmount');

        // 056 Regular Recoverable Amount Of This Month
        $regRecoverable = $curLoanInfo->sum('principalRecoverableAmount');

        // 057 Number Of Staff
        $numStaff = count($curEmpInfo);

        // 058 Number Of Credit Officer/Field Worker
        $numCO = count($curEmpInfo->where('position_id_fk',122));

        // 059-060 Loan Disbursement (Cumulative)
        $cumLoanDisM = $curLoanInfo->where('genderTypeId',1)->sum('closingDisbursedAmount');
        $cumLoanDisF = $curLoanInfo->where('genderTypeId',2)->sum('closingDisbursedAmount');

        // 061-062 Loan Recovery (Cumulative)
        $cumRecoveryM = $curLoanInfo->where('genderTypeId',1)->sum('cumRecoveryPrincipal');
        $cumRecoveryF = $curLoanInfo->where('genderTypeId',2)->sum('cumRecoveryPrincipal');

        // 063-064 Loan Outstanding
        $loanOutstandingM = $curLoanInfo->where('genderTypeId',1)->sum('closingOutstandingAmount');
        $loanOutstandingF = $curLoanInfo->where('genderTypeId',2)->sum('closingOutstandingAmount');

        // 065 Total Loan Outstanding Of Borrower Who Have More Than Two Payment Missing
        $loanOutstan2Due = $curLoanInfo->sum('outstandingWithMoreThan2DueInstallments');

        // 066 Write Off Loan (Cumulative)
        $cumWrtiteOff = $curLoanInfo->sum('cumWriteOffPrincipal');

        // 067 Loan Outstanding Before Write Off At Field
        $loanOutBefWriteOff = $curLoanInfo->sum('closingOutstandingAmount') + $curLoanInfo->sum('cumWriteOffPrincipal');

        // 068 Fund Received (Cumulative)
        $cumFundReceived = $curLoanRegisters->sum('loanAmount');

        // 069 Fund Refund (Cumulative)
        $cumFundRefund = $curLoanRegisterPayments->sum('principalAmount');

        // 070 Fund Refund (FY)
        if ($this->fiscalYearOpeningDate!=null) {
            $fundRefundFY = $curLoanRegisterPayments->where('paymentDate','>',$this->fiscalYearOpeningDate)->sum('principalAmount');
        }
        else{
            $fundRefundFY = $curLoanRegisterPayments->sum('principalAmount');            
        }

        // 071 Fund Overdue At The End Of Month
        $fundOverDue = 0;
        foreach ($curLoanRegisters as $curLoanRegister) {
            $curFundOverDue = $this->loanRegisterSchedules->where('accId_fk',$curLoanRegister->id)->sum('principalAmount') - $curLoanRegisterPayments->sum('principalAmount');
            if ($curFundOverDue>=1) {
                $fundOverDue += $curFundOverDue;
            }            
        }

        // 072 Service Charge Income (FY)
        $serChargeIncomeFY = $curLoanInfos->sum('recoveryAmount') - $curLoanInfos->sum('principalRecoveryAmount');

        // 073 Service Charge Income (Cumulative)
        $cumSerChargeIncome = $curLoanInfo->sum('cumRecovery') - $curLoanInfo->sum('cumRecoveryPrincipal');


        $data = array(
            'numBranch'             => $numBranch,
            'numSamityM'            => $numSamityM,
            'numSamityF'            => $numSamityF,
            'numMemberM'            => $numMemberM,
            'numMemberF'            => $numMemberF,
            'numBorrowerM'          => $numBorrowerM,
            'numBorrowerF'          => $numBorrowerF,
            'cumNumLoanM'           => $cumNumLoanM,
            'cumNumLoanF'           => $cumNumLoanF,
            'cumNumBorrowerM'       => $cumNumBorrowerM,
            'cumNumBorrowerF'       => $cumNumBorrowerF,
            'savOpeBalRegM'         => $savOpeBalRegM,
            'savOpeBalRegF'         => $savOpeBalRegF,
            'savCollRegM'           => $savCollRegM,
            'savCollRegF'           => $savCollRegF,
            'savWithdrawRegM'       => $savWithdrawRegM,
            'savWithdrawRegF'       => $savWithdrawRegF,
            'tRegSavingsM'          => $tRegSavingsM,
            'tRegSavingsF'          => $tRegSavingsF,
            'tVolSavingsM'          => $tVolSavingsM,
            'tVolSavingsF'          => $tVolSavingsF,
            'tOtherSavingsM'        => $tOtherSavingsM,
            'tOtherSavingsF'        => $tOtherSavingsF,
            'totalSavingsM'         => $totalSavingsM,
            'totalSavingsF'         => $totalSavingsF,
            'disbursement'          => $disbursement,
            'opOverDueM'            => $opOverDueM,
            'opOverDueF'            => $opOverDueF,
            'regRecoverableM'       => $regRecoverableM,
            'regRecoverableF'       => $regRecoverableF,
            'regRecoveryM'          => $regRecoveryM,
            'regRecoveryF'          => $regRecoveryF,
            'odRecoveryM'           => $odRecoveryM,
            'odRecoveryF'           => $odRecoveryF,
            'advRecoveryM'          => $advRecoveryM,
            'advRecoveryF'          => $advRecoveryF,
            'totalRecovery'         => $totalRecovery,
            'totalOdM'              => $totalOdM,
            'totalOdF'              => $totalOdF,
            'numOdBorrowerM'        => $numOdBorrowerM,
            'numOdBorrowerF'        => $numOdBorrowerF,
            'od1to30'               => $od1to30,
            'od31to180'             => $od31to180,
            'od181to365'            => $od181to365,
            'odBad'                 => $odBad,
            'watchLoanOutStan'      => $watchLoanOutStan,
            'subLoanOutStan'        => $subLoanOutStan,
            'doubtLoanOutStan'      => $doubtLoanOutStan,
            'badLoanOutStan'        => $badLoanOutStan,
            'odOutStan'             => $odOutStan,
            'llpBalance'            => $llpBalance,
            'savBalOdBorrower'      => $savBalOdBorrower,
            'regRecovery'           => $regRecovery,
            'regRecoverable'        => $regRecoverable,
            'numStaff'              => $numStaff,
            'numCO'                 => $numCO,
            'cumLoanDisM'           => $cumLoanDisM,
            'cumLoanDisF'           => $cumLoanDisF,
            'cumRecoveryM'          => $cumRecoveryM,
            'cumRecoveryF'          => $cumRecoveryF,
            'loanOutstandingM'      => $loanOutstandingM,
            'loanOutstandingF'      => $loanOutstandingF,
            'loanOutstan2Due'       => $loanOutstan2Due,
            'cumWrtiteOff'          => $cumWrtiteOff,
            'loanOutBefWriteOff'    => $loanOutBefWriteOff,
            'cumFundReceived'       => $cumFundReceived,
            'cumFundRefund'         => $cumFundRefund,
            'fundRefundFY'          => $fundRefundFY,
            'fundOverDue'           => $fundOverDue,
            'serChargeIncomeFY'     => $serChargeIncomeFY,
            'cumSerChargeIncome'    => $cumSerChargeIncome,
        );

        return $data;
    }

}