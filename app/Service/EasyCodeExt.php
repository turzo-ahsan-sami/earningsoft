<?php
namespace App\Service;

use App\gnr\GnrRole;
use App\gnr\GnrUserRole;
use App\hr\Constants;
use App\User;

use App\Service\Helper;

use App\hr\ActingBenefit;
use App\hr\BenefitType;
use App\hr\Edps;
use App\hr\EmployeeGeneralInfo;
use App\hr\FinalPaymentHistory;
use App\hr\HrLeaveApplication;
use App\hr\InsuranceSettings;
use App\hr\OpeningBalanceLoanPf;
use App\hr\OsfSettings;
use App\hr\Other;
use App\hr\PfLoanReceive;
use App\hr\Position;
use App\hr\PromotionIncrement;
use App\hr\ProvidentFundLoan;
use App\hr\ProvidentFundSettings;
use App\hr\SalaryGenerate;
use App\hr\SalaryStructure;
use App\hr\StopSalaryBenefit;
use App\hr\WelfareFundSettings;

use App\gnr\GnrBranch;
use App\gnr\GnrArea;
use App\gnr\GnrZone;

use DB;
use Carbon\Carbon;

class EasyCodeExt extends EasyCode
{

    public function getUserPfLoanBalanceWithoutInterest($uid)
    {
        $balance = 0.00;
        $pfloan = ProvidentFundLoan::where('users_id_fk', $uid)->where('status', 'Approved')->get();
        if (count($pfloan) > 0) {
            foreach ($pfloan as $loan):
                $loanamount = floatval(@$loan->loanReview->approved_loan_amount);
                $receivedLoanData = PfLoanReceive::select(\DB::raw('SUM(principal_amount) as amount'))->where('users_id_fk', $uid)->where('pf_loan_fk', $loan->id)->where('type', 'Regular')->groupby('users_id_fk')->limit(1)->first();
                $balance += floatval($loanamount) - floatval(@$receivedLoanData['amount']);
            endforeach;
        }
        $oppfloan = OpeningBalanceLoanPf::where('users_id_fk', $uid)->get();
        if (count($pfloan) > 0) {
            foreach ($pfloan as $loan):
                $loanamount = floatval(@$loan->loan_amount);
                $receivedLoanData = PfLoanReceive::select(\DB::raw('SUM(principal_amount) as amount'))->where('users_id_fk', $uid)->where('pf_loan_fk', $loan->id)->where('type', 'Opening Balance')->groupby('users_id_fk')->limit(1)->first();
                $balance += floatval($loanamount) - floatval(@$receivedLoanData['amount']);
            endforeach;
        }
        return $balance;
    }

    public static function getUserRole( $userId = null )
    {
        $data = null;

        // Choose user
        if($userId){
            $user = User::with([
                'employee',
                'employee.organization',
                'employee.organization.branch',
                'employee.organization.departmentInfo',
                'employee.organization.position',
            ])->where('id',$userId)->first();
        } else {
            $user = Auth::user();
        }

        $role = GnrUserRole::where('userIdFk', $userId)->first();

        if ($user->id == Constants::SUPER_ADMIN_USER_ID) {
            $data =  [
                'roleid' => 1,
                'rolename' => 'Super Admin',
                'branchid' => 1,
                'branchname' => 'Head Office',
                'departmentid' => '',
                'departmentName' => '',
                'username' => 'superadmin',
                'empIdFk' => '',
                'emp_id' => '',
                'positionname' => '',
                'positionid' => '',
            ];
        } else {
            $data =  [
                'roleid' => $role->roleId  ??  '',
                'rolename' => GnrRole::find($role->roleId)->name ?? '',
                'branchid' => $user->employee->organization->branch_id_fk ?? '',
                'branchname' => $user->employee->organization->branch->name ?? '',
                'departmentid' => $user->employee->organization->department ?? '',
                'departmentName' => $user->employee->organization->departmentInfo->name ?? '',
                'username' => $user->username ?? '',
                'empIdFk' => $user->emp_id_fk ?? '',
                'emp_id' => $user->employee->emp_id ?? '',
                'positionname' => $user->employee->organization->position->name ?? '',
                'positionid' => $user->employee->organization->position_id_fk ?? '',
            ];
        }

        return $data;
    }

    public function getPresentDays($emp, $salary_month)
    {
        if ($emp->employee->organization->job_status != 'Present') {
            $presentDay = date('d', strtotime($emp->employee->organization->terminate_resignation_date . ' -1 Day'));
            $presentDayFebruaryMonth = date('m',
                strtotime($emp->employee->organization->terminate_resignation_date . ' -1 Day'));
            // when month day exceed 30
            if ($presentDay > 30) {
                $presentDay = 30;
            }
            if ($presentDayFebruaryMonth == 2) {
                $presentDay += 2;
            }
        } else {
            $presentDay = 30;
        }

        $salaryMonth = date('Y-m', strtotime('01-' . $salary_month));
        $joiningMonth = date('Y-m', strtotime($emp->employee->organization->joining_date));
        $joiningDay = date('d', strtotime($emp->employee->organization->joining_date));
        $resignMonth = date('Y-m', strtotime($emp->employee->organization->terminate_resignation_date));
        $resignDay = date('d', strtotime($emp->employee->organization->terminate_resignation_date));

        if ($salaryMonth == $joiningMonth) {
            $presentDay = $presentDay - intval($joiningDay) + 1;
        }
        if ($joiningMonth == $resignMonth) {
            $presentDay = intval($resignDay) - $joiningDay;
        }

        $getLastMonthGenerateDate = $this->userLastSalary($emp->id)['month'];

        $countLwop = HrLeaveApplication::checkLwop(
            $emp,
            $salary_month,
            $getLastMonthGenerateDate,
            $emp->employee->organization->terminate_resignation_date
        );

        $totalPresentDay = intval($presentDay - $countLwop);

        if($totalPresentDay < 0 ){
            $totalPresentDay = 0;
        }

        return $totalPresentDay;
    }

    public static function userLastSalary($user_id_fk)
    {
        $salarySheet = SalaryGenerate::whereRaw('contents LIKE :uid', [':uid' => '%"user_id":' . $user_id_fk . ',"payment_status":"Paid",%'])->orderby('id', 'desc')->first();
        // $salarySheet = SalaryGenerate::whereRaw('contents LIKE :uid', [':uid' => '%"user_id":' . $user_id_fk . '%'])->orderby('id', 'desc')->first();
        // dd($salarySheet);
        if ($salarySheet) {
            $data = json_decode(@$salarySheet->contents);
            $res = [];
            $res['month'] = $salarySheet->target_month;
            $res['abenefit'] = 0;
            $res['bbenefit'] = 0;
            foreach ($data as $info):
                if ($info->user_id == $user_id_fk) {
                    // dd($info);
                    foreach ($info as $k => $v):
                        if (strpos($k, 'benefit-a') !== false) {
                            $res['abenefit'] += floatval($v);
                        } else if (strpos($k, 'benefit-b') !== false) {
                        $res['bbenefit'] += floatval($v);
                    }

                endforeach;
                $res['total'] = $info->net_payable;
            }
            endforeach;
            return $res;
        }
    }

    public static function getUserLastSalarySheetById($user_id_fk)
    {
        $salarySheet = SalaryGenerate::whereRaw('contents LIKE :uid',
            [':uid' => '%"user_id":' . $user_id_fk . ',"payment_status":"Paid",%'])
            ->whereRaw("status='Approved'")
            ->orderby('id', 'desc')
            ->first();

        if($salarySheet) {
            $data = json_decode($salarySheet->contents);
            foreach ($data as $info){
                if ($info->user_id == $user_id_fk) {
                    return $info;
                }
            }
        }
    }

    public static function employeeLastGivenSalary( $employee )
    {
        $userId = $employee->user->id;
        $salaryInfo = null;

        $salarySheet = SalaryGenerate::whereRaw('contents LIKE :uid', [':uid' => '%"user_id":' . $userId . ',"payment_status":"Paid",%'])
            ->orderby('id', 'desc')
            ->first();

        if( $salarySheet ) {
            $contents = json_decode($salarySheet->contents,true);
            foreach( $contents as $info ) {
                if($info['user_id'] == $userId) {
                    $salaryInfo = $info;
                    break;
                }
            }
            $salaryInfo['month'] = $salarySheet->target_month;
        }

        if( empty($salaryInfo) ){
            $salaryInfo = [];
        }

        return $salaryInfo;
    }

    public function dueSalaryMonth($datas, $lastSalaryDate, $save = false)
    {
        if( $datas->organization->terminate_resignation_date == '' ||
            $datas->organization->terminate_resignation_date == '0000-00-00') {
            return;
        }

        $lastSalaryDate = date('Y-m-d', strtotime($lastSalaryDate));
        $lastWorkingDate = date('Y-m-d', strtotime($datas->organization->terminate_resignation_date .' -1 Day'));

        $data = [];
        if ($lastSalaryDate != $lastWorkingDate) {
            $user = $datas->user;
            $target_month = date('d-m-Y', strtotime($lastWorkingDate));
            $info = $this->makeVirtualSalary($user, $target_month, $save);
            $data['totalday'] = $info['present_days'];
            $data['dueMonth'] = $target_month;
            $data['totaldue'] = $info['net_payable'];
        }

        return $data;
    }

    /**
    *
    * Due Salary info
    *
    * @param  EmployeeGeneralInfo $datas
    * @param  array $lastGivenSalarySheet
    * @return array $data
    *
    */
    public function dueSalaryInfo($datas, $lastGivenSalarySheet)
    {
        // Declaration
        $data                      = [];
        $dueSalaryDate             = '';
        $isDueSalaryMonthCompleted = false;

        $user         = $datas->user;
        $organization = $datas->organization;

        // Check termiante field is not empty
        if( $organization->terminate_resignation_date == '' ||
            $organization->terminate_resignation_date == '0000-00-00') {
            return $data;
        }

        // Last working date info
        $lastWorkingDate  = date('Y-m-d', strtotime($organization->terminate_resignation_date .' -1 Day'));
        $lastWorkingMonth = date('Y-m', strtotime($organization->terminate_resignation_date .' -1 Day'));


        // When last given salary sheet find
        if(!empty($lastGivenSalarySheet)) {

            $lastSalaryDate  = date('Y-m-d', strtotime($lastGivenSalarySheet['month']));
            $lastSalaryMonth = date('Y-m', strtotime($lastSalaryDate));

            if ($lastSalaryMonth != $lastWorkingMonth) {
                // Due Salary Date
                $dueSalaryDate = $lastWorkingDate;

                // Due Salary Month Complete or Not
                if( !empty($dueSalaryDate) ) {
                    $dueSalOriginalDate = date('Y-m-d', strtotime( $dueSalaryDate) );
                    $dueSalLastDate     = date('Y-m-t', strtotime( $dueSalaryDate) );
                    $dueSalLastDateDay  = date('d', strtotime( $dueSalaryDate) );
                    if( $dueSalOriginalDate == $dueSalLastDate || $dueSalLastDateDay == 30) {
                        $isDueSalaryMonthCompleted = true;
                    }
                }

                // Raw amount
                $target_month = date('d-m-Y', strtotime($dueSalaryDate));
                $info = $this->makeVirtualSalaryLessDeduction($user, $target_month,$isDueSalaryMonthCompleted);
                $data['totalday'] = $info['present_days'];
                $data['dueMonth'] = $target_month;
                $data['basic'] = $info['basic_salary'];
                $data['arrear'] = $info['arrear'];
                $data['pf_org'] = $info['pf_org'];
                $data['wf_org'] = $info['wf_org'];
                $data['totaldue'] = $info['net_payable'];
                $data['acting_benefit'] = $info['acting_benefit'];
                $data['total_salary_benefits'] = $info['total_salary_benefits'];
                $data['isDueSalaryMonthCompleted'] = $isDueSalaryMonthCompleted;

//                 dd($info);
//                 dd($info['basic_salary'],$lastGivenSalarySheet);

                // Calculated amount
                $data['benifitTypeATotal'] = array_sum(Helper::getElementsOf($info, "/benefit-a-/"));
                $data['benifitTypeBTotal'] = array_sum(Helper::getElementsOf($info, "/benefit-b-/"));

                // Modify calculated amount
                $data['org_contribution'] = $isDueSalaryMonthCompleted ? $data['pf_org'] + $data['wf_org'] : 0.00;
                $data['basic_salary_by_present_days'] = round(($data['basic'] / 30) * $data['totalday'],2);
                $data['benifitTypeATotal'] = round(($data['benifitTypeATotal'] / 30) * $data['totalday'],2);
                // $data['benifitTypeBTotal'] = round(($data['benifitTypeBTotal'] / 30) * $data['totalday'],2);
            }

            // When Employee has no due slalary
            if ($lastSalaryMonth == $lastWorkingMonth) {
                return $data;
            }
        }

        // When last given slary sheet not find
        if(empty($lastGivenSalarySheet)) {
            // Due Salary Date
            $dueSalaryDate = $lastWorkingDate;

            // Due Salary Month Complete or Not
            if( !empty($dueSalaryDate) ) {
                $dueSalOriginalDate = date('Y-m-d', strtotime( $dueSalaryDate) );
                $dueSalLastDate     = date('Y-m-t', strtotime( $dueSalaryDate) );
                $dueSalLastDateDay  = date('d', strtotime( $dueSalaryDate) );
                if( $dueSalOriginalDate == $dueSalLastDate || $dueSalLastDateDay == 30) {
                    $isDueSalaryMonthCompleted = true;
                }
            }

            // Raw amount
            $target_month = date('d-m-Y', strtotime($dueSalaryDate));
            $info = $this->makeVirtualSalaryLessDeduction($user, $target_month,$isDueSalaryMonthCompleted);
            $data['totalday'] = $info['present_days'];
            $data['dueMonth'] = $target_month;
            $data['basic'] = $info['basic_salary'];
            $data['arrear'] = $info['arrear'];
            $data['pf_org'] = $info['pf_org'];
            $data['wf_org'] = $info['wf_org'];
            $data['totaldue'] = $info['net_payable'];
            $data['acting_benefit'] = $info['acting_benefit'];
            $data['total_salary_benefits'] = $info['total_salary_benefits'];
            $data['isDueSalaryMonthCompleted'] = $isDueSalaryMonthCompleted;

            // dd($target_month);
//             dd($info);

            // Calculated amount
            $data['benifitTypeATotal'] = array_sum(Helper::getElementsOf($info, "/benefit-a-/"));
            $data['benifitTypeBTotal'] = array_sum(Helper::getElementsOf($info, "/benefit-b-/"));

            // Modify calculated amount
            $data['org_contribution'] = $isDueSalaryMonthCompleted ? $data['pf_org'] + $data['wf_org'] : 0.00;
            $data['basic_salary_by_present_days'] = round(($data['basic'] / 30) * $data['totalday'],2);
            $data['benifitTypeATotal'] = round(($data['benifitTypeATotal'] / 30) * $data['totalday'],2);
            // $data['benifitTypeBTotal'] = round(($data['benifitTypeBTotal'] / 30) * $data['totalday'],2);
        }
        return $data;
    }

    public function makeVirtualSalary($emp, $target_month, $saveData = false)
    {
        $req['target_month'] = $target_month;
        // dd($target_month);
        $perRow = [];
        $arearRow = [];

        $this->adjustEdps($emp->id, '01-' . $req['target_month']);
        $this->adjustSecurityAmount($emp->emp_id_fk, '01-' . $req['target_month']);
        $this->adjustPfLoanAmount($emp->id, date("Y-m-t", strtotime('01-' . $req['target_month'])), date("Y-m-d", strtotime('01-' . $req['target_month'])));
        $this->adjustVehicleLoanAmount($emp->id, date("Y-m-t", strtotime('01-' . $req['target_month'])), date("Y-m-d", strtotime('01-' . $req['target_month'])));
        $this->adjustAdvancedSalaryLoanAmount($emp->id, date("Y-m-t", strtotime('01-' . $req['target_month'])), date("Y-m-d", strtotime('01-' . $req['target_month'])));
        $this->adjustPf($emp->id, '01-' . $req['target_month']);
        $this->adjustInsurance($emp->id, '01-' . $req['target_month']);
        $this->adjustOsf($emp->id, '01-' . $req['target_month']);
        $this->adjustWf($emp->id, '01-' . $req['target_month']);
        $this->adjustIncomeTax($emp->id, '01-' . $req['target_month'], date("Y-m-d", strtotime('01-' . $req['target_month'])));

        $perRow['arrear'] = 0;
        /* Arear checking */
        $isArear = PromotionIncrement::checkArear($emp, $target_month);
        /* promotion and increment update */
        if ($isArear) {
            if ($isArear->type == 'Promotion') {
                PromotionIncrement::promotionEffect($isArear);
            } else {
                PromotionIncrement::incrementEffect($isArear);
            }

        }

        $perRow['user_id'] = $arearRow['user_id'] = $emp->id;

        $perRow['payment_status'] = $arearRow['payment_status'] = 'Paid';

        // get employee salary structure
        if (@count($emp->employee->organization) > 0) {
            $salaryStructure = SalaryStructure::getEmployeeSalaryStructure($emp->employee->organization);
        } else {
            $salaryStructure = array();
        }
        // dd($salaryStructure);

        // get yearly salary structure
        if (@count($salaryStructure) > 0) {
            $yearlySalaryStructure =
            SalaryStructure::getYearlySalaryStructure(intval($emp->employee->organization->salary_increment_year) - 1,
                $salaryStructure->salaryYearlyCal);
        } else {
            $yearlySalaryStructure = array();
        }

        // set employee name
        $perRow['name'] = $arearRow['name'] = (isset($emp->employee->emp_name_english))
            ? $emp->employee->emp_name_english
            : 'N/A';

        // set employee id number
        $perRow['emp_id'] = $arearRow['emp_id'] = (isset($emp->employee->emp_id)) ? $emp->employee->emp_id : 'N/A';

        $perRow['basic_salary'] = floatval(SalaryStructure::getPaticularItemFromYearlySalaryStructure('Total Basic', $yearlySalaryStructure));
        // dd($yearlySalaryStructure);

        /* Arear calculation */
        if ($isArear) {
            $arearEffectMonth = $month = strtotime($isArear->effect_month);
            $salaryMonth = strtotime('01-' . $target_month);
            while ($month < $salaryMonth) {
                $targetMonth = date('Y-m-d', $month);
                $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                $perRow['arrear'] += floatval($perRow['basic_salary']) - floatval($empSalaryFromSheet->basic_salary);

                $arearRow['basic_salary'] = [$targetMonth => floatval($perRow['basic_salary']) - floatval($empSalaryFromSheet->basic_salary)];

                $arearRow['working_days'] = [$targetMonth => $empSalaryFromSheet->working_days];
                $arearRow['present_days'] = [$targetMonth => $empSalaryFromSheet->present_days];

                //print_r($empSalaryFromSheet);
                $month = strtotime('+ 1 Month', $month);
            }
            //print_r($arearRow);
            //exit;
        }
        /* Arear calculation */

        //set working days
        $perRow['working_days'] = 30;
        // dd($req['target_month']);
        $perRow['present_days'] = $this->getPresentDays($emp, $req['target_month'], $req);
        // dd($perRow['present_days']);

        /* A type allowance */
        $sectionA = 0;
        if (@count($salaryStructure->salaryAllow) > 0) {
            foreach ($salaryStructure->salaryAllow as $salaryAllow) {
                if ($salaryAllow->benefit_section == 'A') {
                    $baamount = floatval(SalaryStructure::getPaticularItemFromYearlySalaryStructure('', $yearlySalaryStructure, $salaryAllow->benefit_type_fk));

                    $perRow['benefit-a-' . $salaryAllow->benefit_type_fk] = $baamount;
                    $sectionA += $perRow['benefit-a-' . $salaryAllow->benefit_type_fk];

                    /* Arear calculation */
                    if ($isArear) {
                        $arearEffectMonth = $month = strtotime($isArear->effect_month);
                        $salaryMonth = strtotime('01-' . $req['target_month']);
                        while ($month < $salaryMonth) {
                            $targetMonth = date('Y-m-d', $month);
                            $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                            $v = 'benefit-a-' . $salaryAllow->benefit_type_fk;

                            $perRow['arrear'] += floatval($perRow[$v]) - floatval(@$empSalaryFromSheet->$v);

                            $arearRow[$v] = [$targetMonth => floatval($perRow[$v]) - floatval(@$empSalaryFromSheet->$v)];

                            //print_r($empSalaryFromSheet);
                            $month = strtotime('+ 1 Month', $month);
                        }

                    }
                    /* Arear calculation */
                }
            }
        }
        /* A type allowance */

        //set gross salary
        $perRow['gross_salary'] = floatval($sectionA) + floatval($perRow['basic_salary']);

        //set gross payable
        if ($perRow['present_days'] > 0) {
            $perRow['gross_payable'] = round((floatval($perRow['gross_salary']) / floatval($perRow['working_days']) * floatval($perRow['present_days'])));
        } else {
            $perRow['gross_payable'] = 0;
        }

        //set arrear
        //$perRow['arrear']=0;

        //set acting benefit
        $perRow['acting_benefit'] = ActingBenefit::getActingBenefit($emp, @$emp->employee->organization);

        $actingBenefitData = ActingBenefit::getActingPosition($emp);

        if (count($actingBenefitData) > 0) {
            $perRow['position_type'] = $arearRow['position_type'] = @$actingBenefitData['positionType'];

            // set employee designation
            $perRow['designation'] = $arearRow['designation'] = @$actingBenefitData['positionName'];
        } else {
            $perRow['position_type'] = $arearRow['position_type'] = @$emp->employee->organization->position->type;

            // set employee designation
            $perRow['designation'] = $arearRow['designation'] = (isset($emp->employee->organization->position->name)) ? $emp->employee->organization->position->name : 'N/A';
        }

        /* B type allowance */
        $sectionB = 0;
        if (@count($salaryStructure->salaryAllow) > 0) {
            foreach ($salaryStructure->salaryAllow as $salaryAllow) {
                if ($salaryAllow->benefit_section == 'B') {
                    $isEligible = BenefitType::checkEligibleForBenefit($salaryAllow->benefit_type_fk, $emp);
                    if ($isEligible) {

                        if ($perRow['present_days'] > 0) {
                            $bbamount = BenefitType::getPariculatAmountForSalaryGenerate($salaryAllow->benefit_type_fk, @$emp->employee->organization->benefit_type_fk, @$emp->employee->organization->benefit_type_amount, $perRow['present_days']);
                        } else {
                            $bbamount = 0;
                        }

                        $perRow['benefit-b-' . $salaryAllow->benefit_type_fk] = $bbamount;
                        $sectionB += $perRow['benefit-b-' . $salaryAllow->benefit_type_fk];

                        /* Arear calculation */
                        if ($isArear) {
                            $arearEffectMonth = $month = strtotime($isArear->effect_month);
                            $salaryMonth = strtotime('01-' . $req['target_month']);
                            while ($month < $salaryMonth) {
                                $targetMonth = date('Y-m-d', $month);
                                $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                                $v = 'benefit-b-' . $salaryAllow->benefit_type_fk;

                                $va = BenefitType::getPariculatAmountForSalaryGenerate($salaryAllow->benefit_type_fk, @$emp->employee->organization->benefit_type_fk, @$emp->employee->organization->benefit_type_amount, $arearRow['present_days'][$targetMonth]);

                                $perRow['arrear'] += floatval($va) - floatval(@$empSalaryFromSheet->$v);

                                $arearRow[$v] = [$targetMonth => floatval($va) - floatval(@$empSalaryFromSheet->$v)];

                                //print_r($empSalaryFromSheet);
                                $month = strtotime('+ 1 Month', $month);
                            }

                        }
                        /* Arear calculation */
                    }
                }
            }
        }
        /* B type allowance */

        //set provident fund org
        $perRow['pf_org'] = ProvidentFundSettings::getPFOrg($req['target_month'], $emp, $perRow['basic_salary']);
        /* Arear calculation */
        if ($isArear) {
            $arearEffectMonth = $month = strtotime($isArear->effect_month);
            $salaryMonth = strtotime('01-' . $req['target_month']);
            while ($month < $salaryMonth) {
                $targetMonth = date('Y-m-d', $month);
                $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                $perRow['arrear'] += floatval($perRow['pf_org']) - floatval(@$empSalaryFromSheet->pf_org);

                $arearRow['pf_org'] = [$targetMonth => floatval($perRow['pf_org']) - floatval(@$empSalaryFromSheet->pf_org)];

                //print_r($empSalaryFromSheet);
                $month = strtotime('+ 1 Month', $month);
            }
        }
        /* Arear calculation */

        /* insurance org */
        if (@$data['otherSettings']->insurance_enable == '1') {
            //set provident fund org
            $perRow['insurance_org'] = InsuranceSettings::getINOrg($req['target_month'], $emp, $perRow['basic_salary']);

            if ($isArear) {
                $arearEffectMonth = $month = strtotime($isArear->effect_month);
                $salaryMonth = strtotime('01-' . $req['target_month']);
                while ($month < $salaryMonth) {
                    $targetMonth = date('Y-m-d', $month);
                    $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                    $perRow['arrear'] += floatval($perRow['insurance_org']) - floatval(@$empSalaryFromSheet->insurance_org);

                    $arearRow['insurance_org'] = [$targetMonth => floatval($perRow['insurance_org']) - floatval(@$empSalaryFromSheet->insurance_org)];

                    //print_r($empSalaryFromSheet);
                    $month = strtotime('+ 1 Month', $month);
                }
            }
        } else {
            $perRow['insurance_org'] = 0;
        }
        /* insurance org */

        /* osf org */
        if (@$data['otherSettings']->osf_enable == '1') {
            //set provident fund org
            $perRow['osf_org'] = OsfSettings::getOSFOrg($req['target_month'], $emp, $perRow['basic_salary']);

            if ($isArear) {
                $arearEffectMonth = $month = strtotime($isArear->effect_month);
                $salaryMonth = strtotime('01-' . $req['target_month']);
                while ($month < $salaryMonth) {
                    $targetMonth = date('Y-m-d', $month);
                    $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                    $perRow['arrear'] += floatval($perRow['osf_org']) - floatval(@$empSalaryFromSheet->osf_org);

                    $arearRow['osf_org'] = [$targetMonth => floatval($perRow['osf_org']) - floatval(@$empSalaryFromSheet->osf_org)];

                    //print_r($empSalaryFromSheet);
                    $month = strtotime('+ 1 Month', $month);
                }
            }
        } else {
            $perRow['osf_org'] = 0;
        }
        /* osf org */

        // OTHER SETTINGS
        $data['otherSettings'] = Other::where('status', 1)->first();

        /* WF org */
        if (@$data['otherSettings']->wf_enable == '1') {
            //set wf org
            if (@$emp->employee->organization->wf_active == '1') {
                // dd($emp->employee->organization->wf_active);
                $perRow['wf_org'] = WelfareFundSettings::getWFOrg($req['target_month'], $emp, $perRow['basic_salary']);
                // dd($perRow['wf_org'] );
            } else {
                $perRow['wf_org'] = 0;
            }
            /* Arear calculation */
            if ($isArear) {
                $arearEffectMonth = $month = strtotime($isArear->effect_month);
                $salaryMonth = strtotime('01-' . $req['target_month']);
                while ($month < $salaryMonth) {
                    $targetMonth = date('Y-m-d', $month);
                    $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                    $perRow['arrear'] += floatval($perRow['wf_org']) - floatval(@$empSalaryFromSheet->wf_org);

                    $arearRow['wf_org'] = [$targetMonth => floatval($perRow['wf_org']) - floatval(@$empSalaryFromSheet->wf_org)];

                    //print_r($empSalaryFromSheet);
                    $month = strtotime('+ 1 Month', $month);
                }
            }
            /* Arear calculation */
        } else {
            $perRow['wf_org'] = 0;
        }
        /* WF Org */

        //set total salary
        $perRow['total_salary'] = round(floatval($perRow['gross_payable']) + floatval($perRow['arrear']) + floatval($perRow['acting_benefit']));

        /* Deduction Start */

        //pf calculation
        $perRow['pf_self'] = ProvidentFundSettings::getPFSelf($req['target_month'], $emp, $perRow['basic_salary']);
        /* Arear calculation */
        if ($isArear) {
            $arearEffectMonth = $month = strtotime($isArear->effect_month);
            $salaryMonth = strtotime('01-' . $req['target_month']);
            while ($month < $salaryMonth) {
                $targetMonth = date('Y-m-d', $month);
                $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                $perRow['arrear'] -= floatval($perRow['pf_self']) - floatval(@$empSalaryFromSheet->pf_self);

                $perRow['arrear'] -= floatval($perRow['pf_org']) - floatval(@$empSalaryFromSheet->pf_org);

                $arearRow['pf_self'] = [$targetMonth => floatval($perRow['pf_self']) - floatval(@$empSalaryFromSheet->pf_self)];

                $arearRow['pf_org_deduct'] = [$targetMonth => floatval($perRow['pf_org']) - floatval(@$empSalaryFromSheet->pf_org)];

                //print_r($empSalaryFromSheet);
                $month = strtotime('+ 1 Month', $month);
            }

        }
        /* Arear calculation */

        //pf loan
        $perRow['pf_loan'] = $this->getPfLoanAmount($emp, $req['target_month']);

        /* Insurance Self */
        if (@$data['otherSettings']->insurance_enable == '1') {
            //insurance self calculation
            $perRow['insurance_self'] = InsuranceSettings::getINSelf($req['target_month'], $emp, $perRow['basic_salary']);
            /* Arear calculation */
            if ($isArear) {
                $arearEffectMonth = $month = strtotime($isArear->effect_month);
                $salaryMonth = strtotime('01-' . $req['target_month']);
                while ($month < $salaryMonth) {
                    $targetMonth = date('Y-m-d', $month);
                    $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                    $perRow['arrear'] -= floatval($perRow['insurance_self']) - floatval(@$empSalaryFromSheet->insurance_self);

                    $perRow['arrear'] -= floatval($perRow['insurance_org']) - floatval(@$empSalaryFromSheet->insurance_org);

                    $arearRow['insurance_self'] = [$targetMonth => floatval($perRow['insurance_self']) - floatval(@$empSalaryFromSheet->insurance_self)];

                    $arearRow['insurance_org_deduct'] = [$targetMonth => floatval($perRow['insurance_org']) - floatval(@$empSalaryFromSheet->insurance_org)];

                    //print_r($empSalaryFromSheet);
                    $month = strtotime('+ 1 Month', $month);
                }

            }
            /* Arear calculation */
        } else {
            $perRow['insurance_self'] = 0;
        }
        /* Insurance Self */

        /* OSF Self */
        if (@$data['otherSettings']->osf_enable == '1') {
            //insurance self calculation
            $perRow['osf_self'] = InsuranceSettings::getINSelf($req['target_month'], $emp, $perRow['basic_salary']);
            /* Arear calculation */
            if ($isArear) {
                $arearEffectMonth = $month = strtotime($isArear->effect_month);
                $salaryMonth = strtotime('01-' . $req['target_month']);
                while ($month < $salaryMonth) {
                    $targetMonth = date('Y-m-d', $month);
                    $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                    $perRow['arrear'] -= floatval($perRow['osf_self']) - floatval(@$empSalaryFromSheet->insurance_self);

                    $perRow['arrear'] -= floatval($perRow['osf_org']) - floatval(@$empSalaryFromSheet->osf_org);

                    $arearRow['osf_self'] = [$targetMonth => floatval($perRow['osf_self']) - floatval(@$empSalaryFromSheet->osf_self)];

                    $arearRow['osf_org_deduct'] = [$targetMonth => floatval($perRow['osf_org']) - floatval(@$empSalaryFromSheet->osf_org)];

                    //print_r($empSalaryFromSheet);
                    $month = strtotime('+ 1 Month', $month);
                }

            }
            /* Arear calculation */
        } else {
            $perRow['osf_self'] = 0;
        }
        /* OSF Self */

        /* WF Self */
        if (@$data['otherSettings']->wf_enable == '1') {
            //wf calculation
            if (@$emp->employee->organization->wf_active == 1) {
                $perRow['wf_self'] = WelfareFundSettings::getWFSelf($req['target_month'], $emp, $perRow['basic_salary'], 'WF');
            } else {
                $perRow['wf_self'] = 0;
            }
            /* Arear calculation */
            if ($isArear) {
                $arearEffectMonth = $month = strtotime($isArear->effect_month);
                $salaryMonth = strtotime('01-' . $req['target_month']);
                while ($month < $salaryMonth) {
                    $targetMonth = date('Y-m-d', $month);
                    $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                    $perRow['arrear'] -= floatval($perRow['wf_self']) - floatval(@$empSalaryFromSheet->wf_self);

                    $arearRow['wf_self'] = [$targetMonth => floatval($perRow['wf_self']) - floatval(@$empSalaryFromSheet->wf_self)];

                    //print_r($empSalaryFromSheet);
                    $month = strtotime('+ 1 Month', $month);
                }

            }
            /* Arear calculation */
        } else {
            $perRow['wf_self'] = 0;
        }
        /* WF Self */

        /* WF contri */
        if (@$data['otherSettings']->wf_enable == '1') {
            //wf contri. calculation
            if (@$emp->employee->organization->wf_active == 1) {
                $perRow['wf_contri_self'] = WelfareFundSettings::getWFSelf($req['target_month'], $emp, $perRow['basic_salary'], 'WF Contri.');
            } else {
                $perRow['wf_contri_self'] = 0;
            }

            /* Arear calculation */
            if ($isArear) {
                $arearEffectMonth = $month = strtotime($isArear->effect_month);
                $salaryMonth = strtotime('01-' . $req['target_month']);
                while ($month < $salaryMonth) {
                    $targetMonth = date('Y-m-d', $month);
                    $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                    $perRow['arrear'] -= floatval($perRow['wf_contri_self']) - floatval(@$empSalaryFromSheet->wf_contri_self);

                    $arearRow['wf_contri_self'] = [$targetMonth => floatval($perRow['wf_contri_self']) - floatval(@$empSalaryFromSheet->wf_contri_self)];

                    //print_r($empSalaryFromSheet);
                    $month = strtotime('+ 1 Month', $month);
                }

            }
            /* Arear calculation */
        } else {
            $perRow['wf_contri_self'] = 0;
        }
        /* WF contri */

        /* EPS OR EDPS */
        $perRow['eps']  = 0.00;
        $perRow['edps'] = 0.00;
        if( @$data['otherSettings']->eps_enable == '1' && Helper::isEpsEffectToEmployee($emp->employee) ) {
            $settingsEps = DB::table('hr_settings_eps')
                ->where('grade_id_fk', $emp->employee->organization->grade)
                ->whereRaw('FIND_IN_SET(?,recruitment_type_fk)', [$emp->employee->organization->recruitment_type])
                ->orderby('effect_date','desc')
                ->first();
            $perRow['eps'] += $settingsEps ? $settingsEps->amount : 0.00;
        } else {
            $perRow['edps'] += $this->getEdpsAmount($emp,$req['target_month']);
        }
        /* EPS OR EDPS */

        // VEHICLE LOAN
        $perRow['vehicle_loan'] = $this->getVehicleLoanAmount($emp, $req['target_month']);

        // SD
        // There have no sd in due month
        $perRow['security_money'] = 0.00;
        // $perRow['security_money'] = $this->getSecurityAmount($emp,$req['target_month']);

        // ADVANCE SALARY LOAN
        $perRow['advanced_salary_loan'] = $this->getAdvancedSalaryLoanAmount($emp, $req['target_month']);

        // INCOME TAX
        $perRow['income_tax'] = $this->getIncomeTax($emp, $req['target_month']);

        /* Deduction End */
        // total salary + benefits
        $perRow['total_salary_benefits'] = round(floatval($perRow['total_salary']) +
            floatval($sectionB) +
            floatval($perRow['pf_org']) + floatval($perRow['wf_org']) +
            floatval($perRow['insurance_org']) +
            floatval($perRow['osf_org']));

            // dd($perRow['wf_self']);
        $perRow['total_deductions'] = floatval($perRow['pf_self']) +
            floatval($perRow['pf_loan']) +
            floatval($perRow['pf_org']) +
            floatval($perRow['wf_self']) +
            floatval($perRow['edps']) +
            floatval($perRow['eps']) +
            floatval($perRow['vehicle_loan']) +
            floatval($perRow['security_money']) +
            floatval($perRow['advanced_salary_loan']) +
            floatval($perRow['income_tax']) +
            floatval($perRow['insurance_org']) +
            floatval($perRow['osf_org']) +
            floatval($perRow['insurance_self']) +
            floatval($perRow['osf_self']);

        $perRow['net_payable'] = floatval($perRow['total_salary_benefits']) - floatval($perRow['total_deductions']);

        if ($saveData) {
            //save pf
            $this->savePF($req['target_month'], $perRow['user_id'], $perRow['pf_org'], $perRow['pf_self'], 'Regular', $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);

            //save pf loan
            $this->savePfLoanAmount($emp, $req['target_month'], $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);

            //save insurance
            if (@$data['otherSettings']->insurance_enable == '1') {
                $this->saveInsurance($req['target_month'], $perRow['user_id'], $perRow['insurance_org'], $perRow['insurance_self'], 'Regular', $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);
            }

            //save osf
            if (@$data['otherSettings']->osf_enable == '1') {
                $this->saveOsf($req['target_month'], $perRow['user_id'], $perRow['osf_org'], $perRow['osf_self'], 'Regular', $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);
            }

            //save wf
            if (@$emp->employee->organization->wf_active == 1 && @$data['otherSettings']->wf_enable == '1') {
                $this->saveWF($req['target_month'], $perRow['user_id'], $perRow['wf_org'], $perRow['wf_self'], $perRow['wf_contri_self'], 'Regular', $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);
            }

            //save edps
            $this->saveEDPS($req['target_month'], $perRow['user_id'], $perRow['edps'], $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);

            //save vechicle loan
            $this->saveVehicleLoanAmount($emp, $req['target_month'], $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);

            //save secutiry amount
            $this->saveSecurityMoney($req['target_month'], $emp->emp_id_fk, $perRow['security_money'], $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);

            //save advanced salary loan
            $this->saveAdvancedSalaryLoanAmount($emp, $req['target_month'], $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);

            //save income tax
            $this->saveIncomeTax($emp, $req['target_month'], $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);

            $employeeModel = EmployeeGeneralInfo::find($emp->employee->id);
            $employeeModel->final_salary_content = json_encode($perRow);
            $employeeModel->final_arear_content = json_encode($arearRow);
            $employeeModel->save();

        }

        $data['row'][] = $perRow;
        $data['arearRow'][] = $arearRow;
        // dd($perRow);
        return $perRow;
    }

    /**
    *
    * Virtual salary Less deduction
    *
    */
    public function makeVirtualSalaryLessDeduction($emp, $target_month, $isDueSalaryMonthCompleted, $saveData = false)
    {
        // dd($emp);
        // Here $emp contains User model
        $employee = $emp->employee;
        $organization = $employee->organization;

        $req['target_month'] = $target_month;
        $perRow              = [];
        $arearRow            = [];

        // dd($target_month);
        $this->adjustEdps($emp->id, '01-' . $req['target_month']);
        $this->adjustSecurityAmount($emp->emp_id_fk, '01-' . $req['target_month']);
        $this->adjustPfLoanAmount($emp->id, date("Y-m-t", strtotime('01-' . $req['target_month'])), date("Y-m-d", strtotime('01-' . $req['target_month'])));
        $this->adjustVehicleLoanAmount($emp->id, date("Y-m-t", strtotime('01-' . $req['target_month'])), date("Y-m-d", strtotime('01-' . $req['target_month'])));
        $this->adjustAdvancedSalaryLoanAmount($emp->id, date("Y-m-t", strtotime('01-' . $req['target_month'])), date("Y-m-d", strtotime('01-' . $req['target_month'])));
        $this->adjustPf($emp->id, '01-' . $req['target_month']);
        $this->adjustInsurance($emp->id, '01-' . $req['target_month']);
        $this->adjustOsf($emp->id, '01-' . $req['target_month']);
        $this->adjustWf($emp->id, '01-' . $req['target_month']);
        $this->adjustIncomeTax($emp->id, '01-' . $req['target_month'], date("Y-m-d", strtotime('01-' . $req['target_month'])));

        $perRow['arrear'] = 0;
        /* Arear checking */
        $isArear = PromotionIncrement::checkArear($emp, $target_month);

        /* promotion and increment update */
        if ($isArear) {
            if ($isArear->type == 'Promotion') {
                PromotionIncrement::promotionEffect($isArear);
            } else {
                PromotionIncrement::incrementEffect($isArear);
            }

        }

        $perRow['user_id'] = $arearRow['user_id'] = $emp->id;

        $perRow['payment_status'] = $arearRow['payment_status'] = 'Paid';

        // get employee salary structure
        if (@count($emp->employee->organization) > 0) {
            $salaryStructure = SalaryStructure::getEmployeeSalaryStructure($emp->employee->organization);
        } else {
            $salaryStructure = array();
        }
        // dd($salaryStructure);

        // get yearly salary structure
        if (@count($salaryStructure) > 0) {
            $yearlySalaryStructure =
            SalaryStructure::getYearlySalaryStructure(intval($emp->employee->organization->salary_increment_year) - 1,
                $salaryStructure->salaryYearlyCal);
        } else {
            $yearlySalaryStructure = array();
        }

        // set employee name
        $perRow['name'] = $arearRow['name'] = (isset($emp->employee->emp_name_english))
            ? $emp->employee->emp_name_english
            : 'N/A';

        // set employee id number
        $perRow['emp_id'] = $arearRow['emp_id'] = (isset($emp->employee->emp_id)) ? $emp->employee->emp_id : 'N/A';

        $perRow['basic_salary'] = floatval(SalaryStructure::getPaticularItemFromYearlySalaryStructure('Total Basic', $yearlySalaryStructure));
        // dd($yearlySalaryStructure);

        /* Arear calculation */
        if ($isArear) {
            $arearEffectMonth = $month = strtotime($isArear->effect_month);
            $salaryMonth = strtotime('01-' . $target_month);
            while ($month < $salaryMonth) {
                $targetMonth = date('Y-m-d', $month);
                $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                $perRow['arrear'] += floatval($perRow['basic_salary']) - floatval($empSalaryFromSheet->basic_salary);

                $arearRow['basic_salary'] = [$targetMonth => floatval($perRow['basic_salary']) - floatval($empSalaryFromSheet->basic_salary)];

                $arearRow['working_days'] = [$targetMonth => $empSalaryFromSheet->working_days];
                $arearRow['present_days'] = [$targetMonth => $empSalaryFromSheet->present_days];

                //print_r($empSalaryFromSheet);
                $month = strtotime('+ 1 Month', $month);
            }
            //print_r($arearRow);
            //exit;
        }
        /* Arear calculation */

        //set working days
        $perRow['working_days'] = 30;
        // dd($req['target_month']);
        $perRow['present_days'] = $this->getPresentDays($emp, $req['target_month'], $req);
        // dd($perRow['present_days']);
        // dd($req['target_month']);

        /* A type allowance */
        $sectionA = 0;
        if (@count($salaryStructure->salaryAllow) > 0) {
            foreach ($salaryStructure->salaryAllow as $salaryAllow) {
                if ($salaryAllow->benefit_section == 'A') {
                    $baamount = floatval(SalaryStructure::getPaticularItemFromYearlySalaryStructure('', $yearlySalaryStructure, $salaryAllow->benefit_type_fk));

                    $perRow['benefit-a-' . $salaryAllow->benefit_type_fk] = $baamount;
                    $sectionA += $perRow['benefit-a-' . $salaryAllow->benefit_type_fk];

                    /* Arear calculation */
                    if ($isArear) {
                        $arearEffectMonth = $month = strtotime($isArear->effect_month);
                        $salaryMonth = strtotime('01-' . $req['target_month']);
                        while ($month < $salaryMonth) {
                            $targetMonth = date('Y-m-d', $month);
                            $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                            $v = 'benefit-a-' . $salaryAllow->benefit_type_fk;

                            $perRow['arrear'] += floatval($perRow[$v]) - floatval(@$empSalaryFromSheet->$v);

                            $arearRow[$v] = [$targetMonth => floatval($perRow[$v]) - floatval(@$empSalaryFromSheet->$v)];

                            //print_r($empSalaryFromSheet);
                            $month = strtotime('+ 1 Month', $month);
                        }

                    }
                    /* Arear calculation */
                }
            }
        }
        /* A type allowance */

        //set gross salary
        $perRow['gross_salary'] = floatval($sectionA) + floatval($perRow['basic_salary']);

        //set gross payable
        if ($perRow['present_days'] > 0) {
            $perRow['gross_payable'] = round((floatval($perRow['gross_salary']) / floatval($perRow['working_days']) * floatval($perRow['present_days'])));
        } else {
            $perRow['gross_payable'] = 0;
        }

        //set arrear
        //$perRow['arrear']=0;

        //set acting benefit
        $perRow['acting_benefit'] = ActingBenefit::getActingBenefit($emp, @$emp->employee->organization);

        $actingBenefitData = ActingBenefit::getActingPosition($emp);

        if (count($actingBenefitData) > 0) {
            $perRow['position_type'] = $arearRow['position_type'] = @$actingBenefitData['positionType'];

            // set employee designation
            $perRow['designation'] = $arearRow['designation'] = @$actingBenefitData['positionName'];
        } else {
            $perRow['position_type'] = $arearRow['position_type'] = @$emp->employee->organization->position->type;

            // set employee designation
            $perRow['designation'] = $arearRow['designation'] = (isset($emp->employee->organization->position->name)) ? $emp->employee->organization->position->name : 'N/A';
        }

        /* B type allowance */
        $sectionB = 0;
        if (@count($salaryStructure->salaryAllow) > 0) {
            foreach ($salaryStructure->salaryAllow as $salaryAllow) {
                if ($salaryAllow->benefit_section == 'B') {
                    $isEligible = BenefitType::checkEligibleForBenefit($salaryAllow->benefit_type_fk, $emp);
                    if ($isEligible) {

                        if ($perRow['present_days'] > 0) {
                            $bbamount = BenefitType::getPariculatAmountForSalaryGenerate($salaryAllow->benefit_type_fk,
                                @$emp->employee->organization->benefit_type_fk,
                                @$emp->employee->organization->benefit_type_amount,
                                $perRow['present_days']);
                                // dd(@$emp->employee->organization);
                        } else {
                            $bbamount = 0;
                        }

                        $perRow['benefit-b-' . $salaryAllow->benefit_type_fk] = $bbamount;
                        $sectionB += $perRow['benefit-b-' . $salaryAllow->benefit_type_fk];
                        // dd($bbamount);
                        /* Arear calculation */
                        if ($isArear) {
                            $arearEffectMonth = $month = strtotime($isArear->effect_month);
                            $salaryMonth = strtotime('01-' . $req['target_month']);
                            while ($month < $salaryMonth) {
                                $targetMonth = date('Y-m-d', $month);
                                $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                                $v = 'benefit-b-' . $salaryAllow->benefit_type_fk;

                                $va = BenefitType::getPariculatAmountForSalaryGenerate($salaryAllow->benefit_type_fk,
                                @$emp->employee->organization->benefit_type_fk,
                                @$emp->employee->organization->benefit_type_amount,
                                $arearRow['present_days'][$targetMonth]);

                                $perRow['arrear'] += (floatval($va) - floatval(@$empSalaryFromSheet->$v));

                                $arearRow[$v] = [$targetMonth => floatval($va) - floatval(@$empSalaryFromSheet->$v)];

                                //print_r($empSalaryFromSheet);
                                $month = strtotime('+ 1 Month', $month);
                            }

                        }
                        /* Arear calculation */
                    }
                }
            }
        }
        /* B type allowance */
        // dd($sectionB);

        //set provident fund org
        $perRow['pf_org'] = ProvidentFundSettings::getPFOrg($req['target_month'], $emp, $perRow['basic_salary']);
        /* Arear calculation */
        if ($isArear) {
            $arearEffectMonth = $month = strtotime($isArear->effect_month);
            $salaryMonth = strtotime('01-' . $req['target_month']);
            while ($month < $salaryMonth) {
                $targetMonth = date('Y-m-d', $month);
                $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                $perRow['arrear'] += floatval($perRow['pf_org']) - floatval(@$empSalaryFromSheet->pf_org);

                $arearRow['pf_org'] = [$targetMonth => floatval($perRow['pf_org']) - floatval(@$empSalaryFromSheet->pf_org)];

                //print_r($empSalaryFromSheet);
                $month = strtotime('+ 1 Month', $month);
            }
        }
        /* Arear calculation */

        /* insurance org */
        if (@$data['otherSettings']->insurance_enable == '1') {
            //set provident fund org
            $perRow['insurance_org'] = InsuranceSettings::getINOrg($req['target_month'], $emp, $perRow['basic_salary']);

            if ($isArear) {
                $arearEffectMonth = $month = strtotime($isArear->effect_month);
                $salaryMonth = strtotime('01-' . $req['target_month']);
                while ($month < $salaryMonth) {
                    $targetMonth = date('Y-m-d', $month);
                    $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                    $perRow['arrear'] += floatval($perRow['insurance_org']) - floatval(@$empSalaryFromSheet->insurance_org);

                    $arearRow['insurance_org'] = [$targetMonth => floatval($perRow['insurance_org']) - floatval(@$empSalaryFromSheet->insurance_org)];

                    //print_r($empSalaryFromSheet);
                    $month = strtotime('+ 1 Month', $month);
                }
            }
        } else {
            $perRow['insurance_org'] = 0;
        }
        /* insurance org */

        /* osf org */
        if (@$data['otherSettings']->osf_enable == '1') {
            //set provident fund org
            $perRow['osf_org'] = OsfSettings::getOSFOrg($req['target_month'], $emp, $perRow['basic_salary']);

            if ($isArear) {
                $arearEffectMonth = $month = strtotime($isArear->effect_month);
                $salaryMonth = strtotime('01-' . $req['target_month']);
                while ($month < $salaryMonth) {
                    $targetMonth = date('Y-m-d', $month);
                    $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                    $perRow['arrear'] += floatval($perRow['osf_org']) - floatval(@$empSalaryFromSheet->osf_org);

                    $arearRow['osf_org'] = [$targetMonth => floatval($perRow['osf_org']) - floatval(@$empSalaryFromSheet->osf_org)];

                    //print_r($empSalaryFromSheet);
                    $month = strtotime('+ 1 Month', $month);
                }
            }
        } else {
            $perRow['osf_org'] = 0;
        }
        /* osf org */

        // OTHER SETTINGS
        $data['otherSettings'] = Other::where('status', 1)->first();

        /* WF org */
        if (@$data['otherSettings']->wf_enable == '1') {
            //set wf org
            if (@$emp->employee->organization->wf_active == '1') {
                // dd($emp->employee->organization->wf_active);
                $perRow['wf_org'] = WelfareFundSettings::getWFOrg($req['target_month'], $emp, $perRow['basic_salary']);
                // dd($perRow['wf_org'] );
            } else {
                $perRow['wf_org'] = 0;
            }
            /* Arear calculation */
            if ($isArear) {
                $arearEffectMonth = $month = strtotime($isArear->effect_month);
                $salaryMonth = strtotime('01-' . $req['target_month']);
                while ($month < $salaryMonth) {
                    $targetMonth = date('Y-m-d', $month);
                    $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                    $perRow['arrear'] += floatval($perRow['wf_org']) - floatval(@$empSalaryFromSheet->wf_org);

                    $arearRow['wf_org'] = [$targetMonth => floatval($perRow['wf_org']) - floatval(@$empSalaryFromSheet->wf_org)];

                    //print_r($empSalaryFromSheet);
                    $month = strtotime('+ 1 Month', $month);
                }
            }
            /* Arear calculation */
        } else {
            $perRow['wf_org'] = 0;
        }
        /* WF Org */

        //set total salary
        $perRow['total_salary'] = round(floatval($perRow['gross_payable']) + floatval($perRow['arrear']) + floatval($perRow['acting_benefit']));

        /* Deduction Start */

        //pf calculation
        $perRow['pf_self'] = ProvidentFundSettings::getPFSelf($req['target_month'], $emp, $perRow['basic_salary']);
        /* Arear calculation */
        if ($isArear) {
            $arearEffectMonth = $month = strtotime($isArear->effect_month);
            $salaryMonth = strtotime('01-' . $req['target_month']);
            while ($month < $salaryMonth) {
                $targetMonth = date('Y-m-d', $month);
                $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                $perRow['arrear'] -= floatval($perRow['pf_self']) - floatval(@$empSalaryFromSheet->pf_self);

                $perRow['arrear'] -= floatval($perRow['pf_org']) - floatval(@$empSalaryFromSheet->pf_org);

                $arearRow['pf_self'] = [$targetMonth => floatval($perRow['pf_self']) - floatval(@$empSalaryFromSheet->pf_self)];

                $arearRow['pf_org_deduct'] = [$targetMonth => floatval($perRow['pf_org']) - floatval(@$empSalaryFromSheet->pf_org)];

                //print_r($empSalaryFromSheet);
                $month = strtotime('+ 1 Month', $month);
            }

        }
        /* Arear calculation */

        //pf loan
        $perRow['pf_loan'] = $this->getPfLoanAmount($emp, $req['target_month']);

        /* Insurance Self */
        if (@$data['otherSettings']->insurance_enable == '1') {
            //insurance self calculation
            $perRow['insurance_self'] = InsuranceSettings::getINSelf($req['target_month'], $emp, $perRow['basic_salary']);
            /* Arear calculation */
            if ($isArear) {
                $arearEffectMonth = $month = strtotime($isArear->effect_month);
                $salaryMonth = strtotime('01-' . $req['target_month']);
                while ($month < $salaryMonth) {
                    $targetMonth = date('Y-m-d', $month);
                    $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                    $perRow['arrear'] -= floatval($perRow['insurance_self']) - floatval(@$empSalaryFromSheet->insurance_self);

                    $perRow['arrear'] -= floatval($perRow['insurance_org']) - floatval(@$empSalaryFromSheet->insurance_org);

                    $arearRow['insurance_self'] = [$targetMonth => floatval($perRow['insurance_self']) - floatval(@$empSalaryFromSheet->insurance_self)];

                    $arearRow['insurance_org_deduct'] = [$targetMonth => floatval($perRow['insurance_org']) - floatval(@$empSalaryFromSheet->insurance_org)];

                    //print_r($empSalaryFromSheet);
                    $month = strtotime('+ 1 Month', $month);
                }

            }
            /* Arear calculation */
        } else {
            $perRow['insurance_self'] = 0;
        }
        /* Insurance Self */

        /* OSF Self */
        if (@$data['otherSettings']->osf_enable == '1') {
            //insurance self calculation
            $perRow['osf_self'] = InsuranceSettings::getINSelf($req['target_month'], $emp, $perRow['basic_salary']);
            /* Arear calculation */
            if ($isArear) {
                $arearEffectMonth = $month = strtotime($isArear->effect_month);
                $salaryMonth = strtotime('01-' . $req['target_month']);
                while ($month < $salaryMonth) {
                    $targetMonth = date('Y-m-d', $month);
                    $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                    $perRow['arrear'] -= floatval($perRow['osf_self']) - floatval(@$empSalaryFromSheet->insurance_self);

                    $perRow['arrear'] -= floatval($perRow['osf_org']) - floatval(@$empSalaryFromSheet->osf_org);

                    $arearRow['osf_self'] = [$targetMonth => floatval($perRow['osf_self']) - floatval(@$empSalaryFromSheet->osf_self)];

                    $arearRow['osf_org_deduct'] = [$targetMonth => floatval($perRow['osf_org']) - floatval(@$empSalaryFromSheet->osf_org)];

                    //print_r($empSalaryFromSheet);
                    $month = strtotime('+ 1 Month', $month);
                }

            }
            /* Arear calculation */
        } else {
            $perRow['osf_self'] = 0;
        }
        /* OSF Self */

        /* WF Self */
        if (@$data['otherSettings']->wf_enable == '1') {
            //wf calculation
            if (@$emp->employee->organization->wf_active == 1) {
                $perRow['wf_self'] = WelfareFundSettings::getWFSelf($req['target_month'], $emp, $perRow['basic_salary'], 'WF');
            } else {
                $perRow['wf_self'] = 0;
            }
            /* Arear calculation */
            if ($isArear) {
                $arearEffectMonth = $month = strtotime($isArear->effect_month);
                $salaryMonth = strtotime('01-' . $req['target_month']);
                while ($month < $salaryMonth) {
                    $targetMonth = date('Y-m-d', $month);
                    $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                    $perRow['arrear'] -= floatval($perRow['wf_self']) - floatval(@$empSalaryFromSheet->wf_self);

                    $arearRow['wf_self'] = [$targetMonth => floatval($perRow['wf_self']) - floatval(@$empSalaryFromSheet->wf_self)];

                    //print_r($empSalaryFromSheet);
                    $month = strtotime('+ 1 Month', $month);
                }

            }
            /* Arear calculation */
        } else {
            $perRow['wf_self'] = 0;
        }
        /* WF Self */

        /* WF contri */
        if (@$data['otherSettings']->wf_enable == '1') {
            //wf contri. calculation
            if (@$emp->employee->organization->wf_active == 1) {
                $perRow['wf_contri_self'] = WelfareFundSettings::getWFSelf($req['target_month'], $emp, $perRow['basic_salary'], 'WF Contri.');
            } else {
                $perRow['wf_contri_self'] = 0;
            }

            /* Arear calculation */
            if ($isArear) {
                $arearEffectMonth = $month = strtotime($isArear->effect_month);
                $salaryMonth = strtotime('01-' . $req['target_month']);
                while ($month < $salaryMonth) {
                    $targetMonth = date('Y-m-d', $month);
                    $empSalaryFromSheet = SalaryGenerate::getEmployeeSalaryInfo($emp, $targetMonth);

                    $perRow['arrear'] -= floatval($perRow['wf_contri_self']) - floatval(@$empSalaryFromSheet->wf_contri_self);

                    $arearRow['wf_contri_self'] = [$targetMonth => floatval($perRow['wf_contri_self']) - floatval(@$empSalaryFromSheet->wf_contri_self)];

                    //print_r($empSalaryFromSheet);
                    $month = strtotime('+ 1 Month', $month);
                }

            }
            /* Arear calculation */
        } else {
            $perRow['wf_contri_self'] = 0;
        }
        /* WF contri */

        /* Eps & Eps */
        $perRow['eps']  = 0.00;
        $perRow['edps'] = 0.00;
        if( @$data['otherSettings']->eps_enable == '1' && Helper::isEpsEffectToEmployee($emp->employee) ) {
            $settingsEps = DB::table('hr_settings_eps')
                ->where('grade_id_fk', $emp->employee->organization->grade)
                ->whereRaw('FIND_IN_SET(?,recruitment_type_fk)', [$emp->employee->organization->recruitment_type])
                ->orderby('effect_date','desc')
                ->first();
            $perRow['eps'] += $settingsEps ? $settingsEps->amount : 0.00;
        } else {
            // Stop edps amount
            // $perRow['edps'] += $this->getEdpsAmount($emp,$req['target_month']);
            $perRow['edps'] = 0.00;
        }
        /* Eps & Eps End */

        // Vehicle Loan
        $perRow['vehicle_loan'] = $this->getVehicleLoanAmount($emp, $req['target_month']);

        // Sd
        $perRow['security_money'] = $this->getSecurityAmount($emp,$req['target_month']);

        // Advance Salary Loan
        $perRow['advanced_salary_loan'] = $this->getAdvancedSalaryLoanAmount($emp, $req['target_month']);

        // Income Tax
        $perRow['income_tax'] = $this->getIncomeTax($emp, $req['target_month']);


        // if present day is zero
        if ($perRow['present_days'] == 0) {
            $perRow['pf_self'] = 0.00;
            $perRow['pf_loan'] = 0.00;
            $perRow['pf_org'] = 0.00;
            $perRow['wf_self'] = 0.00;
            $perRow['wf_contri_self'] = 0.00;
            $perRow['wf_org'] = 0.00;
            $perRow['edps'] = 0.00;
            $perRow['eps'] = 0.00;
            $perRow['vehicle_loan'] = 0.00;
            $perRow['security_money'] = 0.00;
            $perRow['advanced_salary_loan'] = 0.00;
            $perRow['income_tax'] = 0.00;
            $perRow['insurance_org'] = 0.00;
            $perRow['osf_org'] = 0.00;
            $perRow['insurance_self'] = 0.00;
            $perRow['osf_self'] = 0.00;
        }

        // Total salary + Benefits
        // Completed month wise benifit added
        if($isDueSalaryMonthCompleted) {
            $perRow['total_salary_benefits'] =
            round(floatval($perRow['total_salary']) +
            floatval($sectionB) +
            floatval($perRow['pf_org']) +
            floatval($perRow['wf_org']) +
            floatval($perRow['insurance_org']) +
            floatval($perRow['osf_org']));
        }

        if(!$isDueSalaryMonthCompleted) {
            $perRow['total_salary_benefits'] =
            round(floatval($perRow['total_salary']) +
            floatval($sectionB)) -
            // Acting benefit is not coundted in incompleted month.
            floatval($perRow['acting_benefit']);
            $perRow['acting_benefit'] = 0.00;
            // floatval($perRow['pf_org']) +
            // floatval($perRow['wf_org']) +
            // floatval($perRow['insurance_org']) +
            // floatval($perRow['osf_org']));
        }

        // dd($perRow['acting_benefit']);
        // dd(floatval($perRow['total_salary']));
        // dd( $sectionB);

        // Deduction List
        // floatval($perRow['pf_self'])
        // floatval($perRow['pf_loan'])
        // floatval($perRow['pf_org'])
        // floatval($perRow['wf_self'])
        // floatval($perRow['wf_contri_self'])
        // floatval($perRow['wf_org'])
        // floatval($perRow['edps'])
        // floatval($perRow['eps'])
        // floatval($perRow['vehicle_loan'])
        // floatval($perRow['security_money'])
        // floatval($perRow['advanced_salary_loan'])
        // floatval($perRow['income_tax'])
        // floatval($perRow['insurance_org'])
        // floatval($perRow['osf_org'])
        // floatval($perRow['insurance_self'])
        // floatval($perRow['osf_self'])

        // 1. Completed month
        // 2. Incompleted month
        //----------------------------------

        if( $isDueSalaryMonthCompleted ) {
            $perRow['total_deductions'] =

            // Pf
            floatval($perRow['pf_self']) +
            floatval($perRow['pf_loan']) +
            floatval($perRow['pf_org']) +

            // wf
            floatval($perRow['wf_self']) +
            // Later deducton
            // floatval($perRow['wf_contri_self']) +
            floatval($perRow['wf_org']) +

            //Edps
            floatval($perRow['edps']) +

            // Eps
            floatval($perRow['eps']);

            // floatval($perRow['vehicle_loan']) +
            // floatval($perRow['security_money']) +
            // floatval($perRow['advanced_salary_loan']) +
            // floatval($perRow['income_tax']) +
            // floatval($perRow['insurance_org']) +
            // floatval($perRow['osf_org']) +
            // floatval($perRow['insurance_self']) +
            // floatval($perRow['osf_self']);

        }

        if( !$isDueSalaryMonthCompleted ) {
            $perRow['total_deductions'] = 0.00;

            // Pf
            // floatval($perRow['pf_self']) +
            // floatval($perRow['pf_loan']) +
            // floatval($perRow['pf_org']) +

            // dd($perRow['pf_org']);

            // Wf
            // floatval($perRow['wf_self']);
            // Later deducton
            // floatval($perRow['wf_contri_self']) +
            // floatval($perRow['wf_org']) +

            // floatval($perRow['edps']) +
            // floatval($perRow['eps']);
            // floatval($perRow['vehicle_loan']) +
            // floatval($perRow['security_money']) +
            // floatval($perRow['advanced_salary_loan']) +
            // floatval($perRow['income_tax']) +
            // floatval($perRow['insurance_org']) +
            // floatval($perRow['osf_org']) +
            // floatval($perRow['insurance_self']) +
            // floatval($perRow['osf_self']);
        }

        // Net Payable calculation
        $perRow['net_payable'] = floatval($perRow['total_salary_benefits']) - floatval($perRow['total_deductions']);
        // dd($perRow);
        if ($saveData) {
            //save pf
            $this->savePF($req['target_month'], $perRow['user_id'], $perRow['pf_org'], $perRow['pf_self'], 'Regular', $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);

            //save pf loan
            $this->savePfLoanAmount($emp, $req['target_month'], $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);

            //save insurance
            if (@$data['otherSettings']->insurance_enable == '1') {
                $this->saveInsurance($req['target_month'], $perRow['user_id'], $perRow['insurance_org'], $perRow['insurance_self'], 'Regular', $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);
            }

            //save osf
            if (@$data['otherSettings']->osf_enable == '1') {
                $this->saveOsf($req['target_month'], $perRow['user_id'], $perRow['osf_org'], $perRow['osf_self'], 'Regular', $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);
            }

            //save wf
            if (@$emp->employee->organization->wf_active == 1 && @$data['otherSettings']->wf_enable == '1') {
                $this->saveWF($req['target_month'], $perRow['user_id'], $perRow['wf_org'], $perRow['wf_self'], $perRow['wf_contri_self'], 'Regular', $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);
            }

            //save edps
            $this->saveEDPS($req['target_month'], $perRow['user_id'], $perRow['edps'], $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);

            //save vechicle loan
            $this->saveVehicleLoanAmount($emp, $req['target_month'], $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);

            //save secutiry amount
            $this->saveSecurityMoney($req['target_month'], $emp->emp_id_fk, $perRow['security_money'], $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);

            //save advanced salary loan
            $this->saveAdvancedSalaryLoanAmount($emp, $req['target_month'], $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);

            //save income tax
            $this->saveIncomeTax($emp, $req['target_month'], $perRow['payment_status'], $emp->employee->organization->terminate_resignation_date);

            $employeeModel = EmployeeGeneralInfo::find($emp->employee->id);
            $employeeModel->final_salary_content = json_encode($perRow);
            $employeeModel->final_arear_content = json_encode($arearRow);
            $employeeModel->save();

        }

        $data['row'][] = $perRow;
        $data['arearRow'][] = $arearRow;
        // dd($perRow);
        return $perRow;
    }

    public function getEdpsAmount($emp, $salaryMonth)
    {
        $this->adjustEdps($emp->id, '01-' . $salaryMonth);
        /* Check security deposit stop or not */
        if (StopSalaryBenefit::checkStopSalaryBenefit($emp->id, '01-' . $salaryMonth, 'EDPS')) {
            return 0;
        }
        $edps = Edps::select(\DB::raw('SUM(amount) as amount'))
            ->where('status', 'Active')
            ->where('users_id_fk', $emp->id)
            ->whereRaW('? BETWEEN `start_month` and `end_month`', [date('Y-m-01', strtotime($salaryMonth))])
            ->first();
        return floatval($edps->amount);
    }

    // public function getEpsAmount($emp, $salaryMonth)
    // {
    //     $this->adjustEdps($emp->id, '01-' . $salaryMonth);
    //     /* Check security deposit stop or not */
    //     if (StopSalaryBenefit::checkStopSalaryBenefit($emp->id, '01-' . $salaryMonth, 'EDPS')) {
    //         return 0;
    //     }
    //     $edps = Edps::select(\DB::raw('SUM(amount) as amount'))
    //         ->where('status', 'Active')
    //         ->where('users_id_fk', $emp->id)
    //         ->whereRaW('? BETWEEN `start_month` and `end_month`', [date('Y-m-01', strtotime($salaryMonth))])
    //         ->first();
    //     return floatval($edps->amount);
    // }

    public static function getLastSalaryInfoByUserId($userId)
    {
        $salaryInfo = null;
        $salarySheet = SalaryGenerate::whereRaw('contents LIKE :uid', [':uid' => '%"user_id":' . $userId . ',"payment_status":"Paid",%'])
            ->orderby('id', 'desc')
            ->first();
        if ($salarySheet) {
            $data = json_decode(@$salarySheet->contents);
            foreach ($data as $info) {
                if ($info->user_id == $userId) {
                    $salaryInfo = $info;
                }
            }
        }
        return $salaryInfo;
    }

    public function getFinalPaymentCompletedDate($user)
    {
        $date = '';
        $finalPaymentHistory = FinalPaymentHistory::where(['status' => 'Paid', 'users_id_fk' => $user->id])
            ->orderby('id', 'desc')->first();
        if ($finalPaymentHistory) {
            $data = json_decode($finalPaymentHistory->additional_data);
            if (isset($data->date)) {
                $date = $data->date;
            }
        }
        return $date;
    }

    public function getFormatedDateAndDurationForFinalPayment($initialDate, $submitedDate)
    {
        $calculatedDate = '';
        $duration       = 0;

        $parsingInitialDate  = Carbon::parse($initialDate);
        $parsingSubmitedDate = Carbon::parse($submitedDate);

        $calculatedDate = $parsingSubmitedDate->format('d-m-Y');
        if($parsingInitialDate->format('Y-m-d') != $parsingSubmitedDate->format('Y-m-d')) {
            $duration = $parsingInitialDate->diffInDays($parsingSubmitedDate->addDay());
        }

        return $calculatedDate . " [ Count Down Day - " . $duration . " Days ]";
    }
}
