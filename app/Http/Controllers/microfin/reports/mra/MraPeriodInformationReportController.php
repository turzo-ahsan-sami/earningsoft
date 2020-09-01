<?php

namespace App\Http\Controllers\microfin\reports\mra;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use App\hr\Exam;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\microfin\MicroFin;
use App\Http\Controllers\microfin\MicroFinance;
use App\microfin\settings\MfnProfession;

class MraPeriodInformationReportController extends Controller {
  protected $MicroFinance;
  public function __construct() {

    $this->MicroFinance = new MicroFinance;

    
  }
  public function index() {

    $userBranchId = Auth::user()->branchId;
    
    return view('microfin.reports.mra.mraPeriodInformationReport.reportFilteringPart');
  }

  public function getReport(Request $req) {

    $userBranchId=Auth::user()->branchId;
    list($periodDateForm,$periodDateTo)=explode(',', $req->reportPeriod);
    $periodDateForm = Carbon::parse($periodDateForm)->format('Y-m-d');
    $periodDateTo = Carbon::parse($periodDateTo)->format('Y-m-d');
        //dd($periodDateForm);
    $allPeriodInformations = DB::table('mfn_mra_report_information')->where('periodDateFrom',$periodDateForm)->first();
        //dd($allPeriodInformations);

    $hrAllEmployeeInfos = DB::table('hr_emp_general_info as t1')
    ->join('hr_emp_org_info as t2','t2.emp_id_fk','=','t1.id')
    ->where('t2.joining_date','<=',$periodDateTo)
    ->where('t2.terminate_resignation_date','<=',$periodDateTo)
    ->where(function($query) use ($periodDateTo) {
      $query->where('t2.terminate_resignation_date','1970-01-01')
      ->orWhere('t2.terminate_resignation_date','>',$periodDateTo);
    })
    ->select('t1.sex','t2.emp_id_fk','t2.project_id_fk','t2.branch_id_fk','t2.position_id_fk','t2.terminate_resignation_date','t2.joining_date','t2.job_status')
    ->get();

        //START ALL BRANCHES MALE AND FEMALE COUNT
        //dd($hrAllEmployeeInfos);                           
    $allBranchMale = $hrAllEmployeeInfos->where('project_id_fk',1)
    ->where('sex','Male')
    ->where('branch_id_fk','!=',1)
    ->where('position_id_fk','!=',130)
    ->where('position_id_fk','!=',131)
    ->count('Male');
    $allBranchFeMale = $hrAllEmployeeInfos->where('project_id_fk',1)
    ->where('sex','Female')
    ->where('branch_id_fk','!=',1)
    ->where('position_id_fk','!=',130)
    ->where('position_id_fk','!=',131)
    ->count('Female');
         //END ALL BRANCHES MALE AND FEMALE COUNT 

         //START ALL AREA OFFICE MALE AND FEMALE COUNT                           
    $allAreaOfficeMale = $hrAllEmployeeInfos->where('project_id_fk',1)
    ->where('sex','Male')
    ->where('branch_id_fk','!=',1)
    ->whereIn('position_id_fk',[130,131])
    ->count('Male');
    $allAreaOfficeFeMale = $hrAllEmployeeInfos->where('project_id_fk',1)
    ->where('sex','Female')
    ->where('branch_id_fk','!=',1)
    ->whereIn('position_id_fk',[130,131])
    ->count('Female');
         //END AREA OFFICE MALE AND FEMALE COUNT   

        //START ALL HEAD OFFICE MALE AND FEMALE COUNT   
    $allHeadOfficeMale = $hrAllEmployeeInfos->where('project_id_fk',1)
    ->where('sex','Male')
    ->where('branch_id_fk',1)
    ->count('Male');
    $allHeadOfficeFeMale = $hrAllEmployeeInfos->where('project_id_fk',1)
    ->where('sex','Female')
    ->where('branch_id_fk',1)
    ->count('Female');
         //END ALL BRANCHES MALE AND FEMALE COUNT

    $hrAllProjectsEmployeeInfos = DB::table('hr_emp_general_info as t1')
    ->join('hr_emp_org_info as t2','t2.emp_id_fk','=','t1.id')
    ->where('t2.joining_date','<=',$periodDateTo)
    ->where('t2.terminate_resignation_date','<=',$periodDateTo)
    ->where(function($query) use ($periodDateTo) {
      $query->where('t2.terminate_resignation_date','1970-01-01')
      ->orWhere('t2.terminate_resignation_date','>',$periodDateTo);
    })
    ->select('t1.sex','t2.emp_id_fk','t2.project_id_fk','t2.branch_id_fk','t2.position_id_fk','t2.terminate_resignation_date','t2.joining_date','t2.job_status')
    ->get();

         //START ALL PROJECT BRANCHES MALE AND FEMALE COUNT                           
    $allProjectBranchMale = $hrAllProjectsEmployeeInfos->where('sex','Male')
    ->where('branch_id_fk','!=',1)
    ->where('position_id_fk','!=',130)
    ->where('position_id_fk','!=',131)
    ->count('Male');
    $allProjectBranchFeMale = $hrAllProjectsEmployeeInfos->where('sex','Female')
    ->where('branch_id_fk','!=',1)
    ->where('position_id_fk','!=',130)
    ->where('position_id_fk','!=',131)
    ->count('Female');
         //END ALL PROJECT BRANCHES MALE AND FEMALE COUNT 

         //START ALL PROJECT AREA OFFICE MALE AND FEMALE COUNT                           
    $allProjectAreaOfficeMale = $hrAllProjectsEmployeeInfos->where('sex','Male')
    ->where('branch_id_fk','!=',1)
    ->whereIn('position_id_fk',[130,131])
    ->count('Male');
    $allProjectAreaOfficeFeMale = $hrAllProjectsEmployeeInfos->where('sex','Female')
    ->where('branch_id_fk','!=',1)
    ->whereIn('position_id_fk',[130,131])
    ->count('Female');
         //END PROJECT AREA OFFICE MALE AND FEMALE COUNT   

        //START ALL PROJECT HEAD OFFICE MALE AND FEMALE COUNT   
    $allProjectHeadOfficeMale = $hrAllProjectsEmployeeInfos->where('sex','Male')
    ->where('branch_id_fk',1)
    ->count('Male');
    $allProjectHeadOfficeFeMale = $hrAllProjectsEmployeeInfos->where('sex','Female')
    ->where('branch_id_fk',1)
    ->count('Female');
         //END ALL PROJECT BRANCHES MALE AND FEMALE COUNT 

        //START CALCULATED HIGHEST AND LOWEST SALARY

    $heighestSalaryPositionMaxFyscalYearId = DB::table('hr_emp_org_info')->where('position_id_fk',104)->max('fiscal_year_fk');
    $lowestSalaryPositionMinFyscalYearId = DB::table('hr_emp_org_info')->where('position_id_fk',122)->min('fiscal_year_fk');

    $heighestSalaryPositionMaxIncrimentYearId = DB::table('hr_emp_org_info')->where('position_id_fk',104)->max('salary_increment_year');
        // $lowestSalaryPositionMinIncrimentYearId = DB::table('hr_emp_org_info')->where('position_id_fk',122)->min('salary_increment_year');
    $lowestSalaryPositionMinIncrimentYearId = 1;

    $heighestSalaryPositionMaxSalaryStructuerId = DB::table('hr_salary_structure')->where('recruitment_type_fk',1)
    ->where('fiscal_year_fk',$heighestSalaryPositionMaxFyscalYearId)
    ->whereRaw('FIND_IN_SET(?,position_id_fk)',[104])->value('id');

    $lowestSalaryPositionMaxSalaryStructuerId = DB::table('hr_salary_structure')->where('recruitment_type_fk',1)
    ->where('fiscal_year_fk',$heighestSalaryPositionMaxFyscalYearId)
    ->whereRaw('FIND_IN_SET(?,position_id_fk)',[122])->value('id');


    $heigestSalaryContains = DB::table('hr_salary_structure_yearly_cal')
    ->where('salary_struc_id_fk',$heighestSalaryPositionMaxSalaryStructuerId)
    ->value('contents');
    $heigestSalaryContainsArr = json_decode($heigestSalaryContains,true);

    $lowestSalaryContains = DB::table('hr_salary_structure_yearly_cal')
    ->where('salary_struc_id_fk',$lowestSalaryPositionMaxSalaryStructuerId)
    ->value('contents');
    $lowestSalaryContainsArr = json_decode($lowestSalaryContains,true);



    $heighestNetSalary = $lowestNetSalary = 0;

    /*$yearStacks = json_decode($salaryStructureYearlyCal->contents);*/
               // get the basic salary
    foreach ($heigestSalaryContainsArr as $year) {
     if($heighestSalaryPositionMaxIncrimentYearId == $year[0]['value']) {
       $heighestNetSalary = $year[8]['value'];
       break;
     }
   }

   foreach ($lowestSalaryContainsArr as $year) {
     if($lowestSalaryPositionMinIncrimentYearId == $year[0]['value']) {
       $lowestNetSalary = $year[15]['value'];
       break;
     }
   }

   /*DB::table('hr_salary_structure')->where('company_id_fk',$orgInfo->company_id_fk)->where('project_id_fk',$orgInfo->project_id_fk)->where('grade_id_fk',$orgInfo->grade)->where('level_id_fk',$orgInfo->level_id_fk)->where('fiscal_year_fk',$orgInfo->fiscal_year_fk)->whereRaw('FIND_IN_SET(?,position_id_fk)', [$orgInfo->position_id_fk])->whereRaw('FIND_IN_SET(?,recruitment_type_fk)', [$orgInfo->recruitment_type])->orderby('id','desc')->limit(1)->first()*/


   
        /*$data = array(
                'heighestSalaryPositionMaxFyscalYearId'         => $heighestSalaryPositionMaxFyscalYearId,
                'lowestSalaryPositionMinFyscalYearId'           => $lowestSalaryPositionMinFyscalYearId,
                'heighestSalaryPositionMaxIncrimentYearId'      => $heighestSalaryPositionMaxIncrimentYearId,
                'lowestSalaryPositionMinIncrimentYearId'        => $lowestSalaryPositionMinIncrimentYearId,
                'heighestSalaryPositionMaxSalaryStructuerId'    => $heighestSalaryPositionMaxSalaryStructuerId,
                'lowestSalaryPositionMaxSalaryStructuerId'      => $lowestSalaryPositionMaxSalaryStructuerId,
                'heigestSalaryContainsArr'                      => $heigestSalaryContainsArr,
                'lowestSalaryContainsArr'                       => $lowestSalaryContainsArr,
                'heighestNetSalary'                             => $heighestNetSalary,
                'lowestNetSalary'                               => $lowestNetSalary,
         )
         dd($data);*/

        //END CALCULATED HIGHEST AND LOWEST SALARY
        //dd($allBranchFeMale);

         $data = array(
          'periodDateForm'                => $periodDateForm,
          'periodDateTo'                  => $periodDateTo,
          'allPeriodInformations'         => $allPeriodInformations,
          'hrAllEmployeeInfos'            => $hrAllEmployeeInfos,
          'allBranchMale'                 => $allBranchMale,
          'allBranchFeMale'               => $allBranchFeMale,
          'allAreaOfficeMale'             => $allAreaOfficeMale,
          'allAreaOfficeFeMale'           => $allAreaOfficeFeMale,
          'allHeadOfficeMale'             => $allHeadOfficeMale,
          'allHeadOfficeFeMale'           => $allHeadOfficeFeMale,
          'allProjectBranchMale'          => $allProjectBranchMale,
          'allProjectBranchFeMale'        => $allProjectBranchFeMale,
          'allProjectAreaOfficeMale'      => $allProjectAreaOfficeMale,
          'allProjectAreaOfficeFeMale'    => $allProjectAreaOfficeFeMale,
          'allProjectHeadOfficeMale'      => $allProjectHeadOfficeMale,
          'allProjectHeadOfficeFeMale'    => $allProjectHeadOfficeFeMale,
          'heighestNetSalary'             => $heighestNetSalary,
          'lowestNetSalary'               => $lowestNetSalary,
        );
         
         return view('microfin.reports.mra.mraPeriodInformationReport.reportPage', $data);
         
       }

     }
