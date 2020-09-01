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
use App;

class MraDbms2ReportController extends Controller {
  protected $MicroFinance;
  public function __construct() {

    $this->MicroFinance = new MicroFinance;


  }
  public function index_backup() {

    $userBranchId = Auth::user()->branchId;
    $branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
    if ($userBranchId==1) {
      $branchList = MicroFin::getBranchList();

    }
    else{
      $branchList = DB::table('gnr_branch')
      ->whereIn('id',$branchIdArray )
      ->orderBy('branchCode')
      ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
      ->pluck('nameWithCode', 'id')
      ->all();
    }


    // $branchList = DB::table('gnr_branch');
    // if ($userBranchId!=1) {
    //   $branchList = $branchList->where('id', $userBranchId);
    // }
    // $branchList = $branchList
    // ->whereIn('id',$branchIdArray )
    // ->orderBy('branchCode')
    // ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
    // ->pluck('nameWithCode', 'id')
    // ->all();
    $data = array(
      'branchList' => $branchList,
      'userBranchId' => $userBranchId,
      'branchIdArray' => $branchIdArray
    );

    return view('microfin.reports.mra.dbms2.reportFilteringPart',$data);
  }



  public function index() {

    $userBranchId = Auth::user()->branchId;

    $branchList = DB::table('gnr_branch');
    if ($userBranchId!=1) {
      $branchList = $branchList->where('id', $userBranchId);
    }
    $branchList = $branchList
    ->orderBy('branchCode')
    ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
    ->pluck('nameWithCode', 'id')
    ->all();
    $data = array(
      'branchList' => $branchList
    );

    return view('microfin.reports.mra.dbms2.reportFilteringPart',$data);
  }

  public function getReport(Request $req) {

           /*$allkindOfBorrowers = DB::table('mfn_member_information as t1')
                                   ->join('mfn_loan as t2','t2.memberIdFk','=','t1.id')
                                   ->join('gnr_branch as t3','t1.branchId','=','t3.id')
                                   ->where('t2.status',1)->select('t1.gender','t1.admissionDate','t1.status','t3.projectId','t3.projectTypeId')->get();*/
            //dd($allkindOfBorrowers);

        /*$allkindOfBorrowers = DB::table('mfn_member_information as t1')
                                   ->join('mfn_loan as t2','t2.memberIdFk','=','t1.id')
                                   ->join('gnr_branch as t3','t1.branchId','=','t3.id')
                                   ->where('t2.status',1)->select('t1.gender','t1.admissionDate','t1.status','t3.projectId','t3.projectTypeId')->get();*/
                                   
                                   //dd($allkindOfBorrowers);



       /*$allkindOfWorkingAreaInfos = DB::table('gnr_working_area as t1')
                                   ->join('mfn_samity as t2','t2.workingAreaId','=','t1.id')
                                   ->join('gnr_branch as t3','t1.branchId','=','t3.id')
                                   ->where('t2.status',1)
                                   ->select('t1.*','t3.projectId','t3.projectTypeId','t2.openingDate')
                                   ->get();*/

       //dd($req);
                                   $userBranchId=Auth::user()->branchId;
                                   $branchId = $req->filBranch;
        //dd($branchId);
                                   list($periodDateForm,$periodDateTo)=explode(',', $req->reportPeriod);
                                   $periodDateForm = Carbon::parse($periodDateForm)->format('Y-m-d');
                                   $periodDateTo = Carbon::parse($periodDateTo)->format('Y-m-d');
        //dd($periodDateTo);

//START ALL MEMBERINFORMATION COLLECTION FILTERING============================================= 
                                   $allkindOfMembers = DB::table('mfn_member_information as t1')
                                   ->join('gnr_branch as t3','t1.branchId','=','t3.id')
                                   ->where('t1.softDel',0);

                                   if($req->search_funding_id_fk != '') {

                                     if($req->search_funding_id_fk=='-2') {
                                       $funOrg = DB::table('mfn_funding_organization')->where('id' ,'!=',3)->get();
                                       $allkindOfMembers = $allkindOfMembers->whereIn('t3.projectId',$funOrg->pluck('projectIdFk'))->whereIn('t3.projectTypeId',$funOrg->pluck('projectTypeIdFk'));
                                     } else {
                                      $funOrg = DB::table('mfn_funding_organization')->where('id',$req->search_funding_id_fk)->first();
                                      $allkindOfMembers = $allkindOfMembers->where('t3.projectId',$funOrg->projectIdFk)->where('t3.projectTypeId',$funOrg->projectTypeIdFk);
                                    }

                                  }

                                  if($req->filBranch != -1) {
                                    $allkindOfMembers = $allkindOfMembers->where('t1.branchId',$req->filBranch);
                                  } 

                                  $allkindOfMembers = $allkindOfMembers->select('t1.gender','t1.admissionDate','t3.projectId','t3.projectTypeId')->get();
            //dd($allkindOfMembers);
//END ALL MEMBERINFORMATION COLLECTION FILTERING============================================= 


//START ALL WORKING AREA COLLECTION FILTERING=============================================          
                                  $allkindOfWorkingAreaInfos = DB::table('gnr_working_area as t1')
                                  ->join('mfn_samity as t2','t2.workingAreaId','=','t1.id')
                                  ->join('gnr_branch as t3','t1.branchId','=','t3.id')
                                  ->where('t1.status',1);

                                  if($req->search_funding_id_fk != '') {

                                    if($req->search_funding_id_fk=='-2') {
                                     $funOrg = DB::table('mfn_funding_organization')->where('id' ,'!=',3)->get();
                                     $allkindOfWorkingAreaInfos = $allkindOfWorkingAreaInfos->whereIn('t3.projectId',$funOrg->pluck('projectIdFk'))->whereIn('t3.projectTypeId',$funOrg->pluck('projectTypeIdFk'));
                                   } else {
                                    $funOrg = DB::table('mfn_funding_organization')->where('id',$req->search_funding_id_fk)->first();
                                    $allkindOfWorkingAreaInfos = $allkindOfWorkingAreaInfos->where('t3.projectId',$funOrg->projectIdFk)->where('t3.projectTypeId',$funOrg->projectTypeIdFk);
                                  }

                                }

                                if($req->filBranch != -1) {
                                  $allkindOfWorkingAreaInfos = $allkindOfWorkingAreaInfos->where('t1.branchId',$req->filBranch);
                                } 

                                $allkindOfWorkingAreaInfos = $allkindOfWorkingAreaInfos->select('t1.*','t3.projectId','t3.projectTypeId','t2.openingDate')->get();

//END ALL WORKING AREA COLLECTION FILTERING============================================= 

//START ALL SAMITY COLLECTION FILTERING============================================= 
                                $totalAllKindSamity = DB::table('mfn_samity as t1')
                                ->join('gnr_branch as t2','t1.branchId','=','t2.id')
                                ->where('t1.softDel',0);

                                if($req->search_funding_id_fk != '') {

                                  if($req->search_funding_id_fk=='-2') {

                                   $funOrg = DB::table('mfn_funding_organization')->where('id' ,'!=',3)->get();
                                   $totalAllKindSamity = $totalAllKindSamity->whereIn('t2.projectId',$funOrg->pluck('projectIdFk'))->whereIn('t2.projectTypeId',$funOrg->pluck('projectTypeIdFk'));
                                 } else {

                                  $funOrg = DB::table('mfn_funding_organization')->where('id',$req->search_funding_id_fk)->first();
                                  $totalAllKindSamity = $totalAllKindSamity->where('t2.projectId',$funOrg->projectIdFk)->where('t2.projectTypeId',$funOrg->projectTypeIdFk);
                                }

                              }

                              if($req->filBranch != -1){
                                $totalAllKindSamity = $totalAllKindSamity->where('t1.branchId',$req->filBranch);
                              } 

                              $totalAllKindSamity = $totalAllKindSamity->select('t1.samityTypeId','t1.openingDate','t1.status','t2.projectId','t2.projectTypeId')->get();
        //dd($totalAllKindSamity);
//END ALL SAMITY COLLECTION FILTERING============================================= 

//START ALL MEMBERINFORMATION COLLECTION FILTERING============================================= 
                              $allkindOfBorrowers = DB::table('mfn_loan as t1')
                              ->join('mfn_member_information as t2','t1.memberIdFk','=','t2.id')
                              ->join('gnr_branch as t3','t1.branchIdFk','=','t3.id')
                              ->where('t1.softDel',0);

                              if($req->search_funding_id_fk != '') {

                               if($req->search_funding_id_fk=='-2') {
                                 $funOrg = DB::table('mfn_funding_organization')->where('id' ,'!=',3)->get();
                                 $allkindOfBorrowers = $allkindOfBorrowers->whereIn('t3.projectId',$funOrg->pluck('projectIdFk'))->whereIn('t3.projectTypeId',$funOrg->pluck('projectTypeIdFk'));
                               } else {
                                $funOrg = DB::table('mfn_funding_organization')->where('id',$req->search_funding_id_fk)->first();
                                $allkindOfBorrowers = $allkindOfBorrowers->where('t3.projectId',$funOrg->projectIdFk)->where('t3.projectTypeId',$funOrg->projectTypeIdFk);
                              }

                            }

                            if($req->filBranch != -1) {
                              $allkindOfBorrowers = $allkindOfBorrowers->where('t1.branchId',$req->filBranch);
                            } 

                            $allkindOfBorrowers = $allkindOfBorrowers->select('t2.gender','t1.disbursementDate','t1.status','t3.projectId','t3.projectTypeId')->get();
//END ALL MEMBERINFORMATION COLLECTION FILTERING============================================= 


                            $noOfTotalDistrictOfActivities = $allkindOfWorkingAreaInfos
                            ->where('openingDate','<=',$periodDateTo)
                            ->groupBy('districtId')
                            ->count('districtId');


                            $noOfTotalUpzilaOfActivities =$allkindOfWorkingAreaInfos
                            ->where('openingDate','<=',$periodDateTo)
                            ->groupBy('upazilaId')
                            ->count('upazilaId');

                            $noOfTotalUnionOfActivities = $allkindOfWorkingAreaInfos
                            ->where('openingDate','<=',$periodDateTo)
                            ->groupBy('unionId')
                            ->count('unionId');

                            $noOfTotalVillageOfActivities = $allkindOfWorkingAreaInfos
                            ->where('openingDate','<=',$periodDateTo)
                            ->groupBy('villageId')
                            ->count('villageId');

                            $noOfTotalBranchOfActivities = $allkindOfWorkingAreaInfos
                            ->where('openingDate','<=',$periodDateTo)
                            ->groupBy('branchId')
                            ->count('branchId');

                            $totalMaleSamity = $totalAllKindSamity
                            ->where('samityTypeId',1)
                            ->where('openingDate','<=',$periodDateTo)
                            ->count('samityTypeId');

                            $totalFemaleSamity = $totalAllKindSamity
                            ->where('samityTypeId',2)
                            ->where('openingDate','<=',$periodDateTo)
                            ->count('samityTypeId');

                            $totalMaleMember = $allkindOfMembers
                            ->where('gender',1)
                            ->where('admissionDate','<=',$periodDateTo)
                            ->count('gender');

                            $totalFemaleMember = $allkindOfMembers
                            ->where('gender',2)
                            ->where('admissionDate','<=',$periodDateTo)
                            ->count('gender');

                            $totalMaleBorrowers = $allkindOfBorrowers->where('gender',1)
                            ->where('disbursementDate','<=',$periodDateTo)
                            ->count('gender');
                            $totalFemaleBorrowers = $allkindOfBorrowers->where('gender',2)
                            ->where('disbursementDate','<=',$periodDateTo)
                            ->count('gender');


            //START SECOND TABLE QUERY
                            $allBrachNameAndAderssInfos = DB::table('gnr_branch');

                            if($req->search_funding_id_fk != '') {

                              if($req->search_funding_id_fk=='-2') {

                               $funOrg = DB::table('mfn_funding_organization')->where('id' ,'!=',3)->get();
                               $allBrachNameAndAderssInfos = $allBrachNameAndAderssInfos->whereIn('projectId',$funOrg->pluck('projectIdFk'))->whereIn('projectTypeId',$funOrg->pluck('projectTypeIdFk'));
                             } else {

                               $funOrg = DB::table('mfn_funding_organization')->where('id',$req->search_funding_id_fk)->first();
                               $allBrachNameAndAderssInfos = $allBrachNameAndAderssInfos->where('projectId',$funOrg->projectIdFk)->where('projectTypeId',$funOrg->projectTypeIdFk);
                             }

                           }

        /*if($req->search_project_id_fk != '') {
            $allBrachNameAndAderssInfos = $allBrachNameAndAderssInfos->where('projectId',$req->search_project_id_fk);
        }
       
      if($req->project_type_id_fk != '') {
            $allBrachNameAndAderssInfos = $allBrachNameAndAderssInfos->where('projectTypeId',$req->project_type_id_fk);
        }
*/
        if($req->filBranch != -1){
          $allBrachNameAndAderssInfos = $allBrachNameAndAderssInfos->where('id',$req->filBranch);
        } 

        if($periodDateTo){
          $allBrachNameAndAderssInfos = $allBrachNameAndAderssInfos->where('branchOpeningDate','<=',$periodDateTo);
        } 

        $allBrachNameAndAderssInfos = $allBrachNameAndAderssInfos->orWhere('id',1)->get();


        $data = array(
          'periodDateForm' => $periodDateForm,
          'periodDateTo' => $periodDateTo,
          'noOfTotalDistrictOfActivities' => $noOfTotalDistrictOfActivities,
          'noOfTotalUpzilaOfActivities' => $noOfTotalUpzilaOfActivities,
          'noOfTotalUnionOfActivities' => $noOfTotalUnionOfActivities,
          'noOfTotalVillageOfActivities' => $noOfTotalVillageOfActivities,
          'noOfTotalBranchOfActivities' => $noOfTotalBranchOfActivities,
          'totalMaleMember' => $totalMaleMember,
          'totalFemaleMember' => $totalFemaleMember,
          'totalMaleBorrowers' => $totalMaleBorrowers,
          'totalFemaleBorrowers' => $totalFemaleBorrowers,
          'totalMaleSamity' => $totalMaleSamity,
          'totalFemaleSamity' => $totalFemaleSamity,
          'allBrachNameAndAderssInfos'=>$allBrachNameAndAderssInfos
        );

        return view('microfin.reports.mra.dbms2.reportPage',$data);
        
      }

      public function projectTypeFiltering(Request $req) {

        $projectTypeData =DB::table('gnr_project_type')
        ->where('projectId',$req->project_id_fk)->select(DB::raw("CONCAT(projectTypeCode ,' - ', name ) AS name"),'id')->get();
        $data =  array(
          'projectTypeData' => $projectTypeData
        );
        return response()->json($data);
      }

      public function branchFilteringByProjectType(Request $req) {

        $branchData =DB::table('gnr_branch');



        if($req->search_funding_id_fk != '') {

          if($req->search_funding_id_fk=='-2') {

           $funOrg = DB::table('mfn_funding_organization')->where('id' ,'!=',3)->get();
           $branchData = $branchData->whereIn('projectId',$funOrg->pluck('projectIdFk'))->whereIn('projectTypeId',$funOrg->pluck('projectTypeIdFk'));
         } else {

           $funOrg = DB::table('mfn_funding_organization')->where('id',$req->search_funding_id_fk)->first();
           $branchData = $branchData->where('projectId',$funOrg->projectIdFk)->where('projectTypeId',$funOrg->projectTypeIdFk);
         }

       }

       $branchData =$branchData->select('branchCode','name','id')->orWhere('id','=',1)->get();
       $data =  array(
        'branchData' => $branchData
      );
       return response()->json($data);
     }

   }
