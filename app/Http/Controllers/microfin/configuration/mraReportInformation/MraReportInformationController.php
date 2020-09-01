<?php
namespace App\Http\Controllers\microfin\configuration\mraReportInformation;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use App\microfin\configuration\MfnMraReportInformation;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MraReportInformationController extends Controller
{
       

 	public function __construct() {

            
        }

    public function index(Request $req) {

        $user = Auth::user();
        Session::put('branchId', $user->branchId);
        $gnrBranchId = Session::get('branchId');
        $logedUserName = $user->name;

        $mfnMraReportInformationInfos = DB::table('mfn_mra_report_information')->paginate(15);

      return view('microfin/configuration/mraReportInformation/mraReportInformationList',['mfnMraReportInformationInfos'=>$mfnMraReportInformationInfos]);
     
    }

    public function addMraReportInformation() {
        return view('microfin/configuration/mraReportInformation/addMraReportInformation');
    }

 public function storeMRAReportInformationItem(Request $req) {
    $new = date('Y-m-d');
    list($periodDateForm,$periodDateTo)=explode(',', $req->reportPeriod);

    $thisPeriodDate = date('Y-m-d',strtotime($periodDateForm));

    $thisPeriodInformationExistOrNot = DB::table('mfn_mra_report_information')->where('periodDateFrom',$thisPeriodDate)->count('periodDateFrom');

  //dd($thisPeriodInformationExistOrNot);
   
    //list($periodDateForm,$periodDateTo)=explode(',', $req->reportPeriod);

     if($thisPeriodInformationExistOrNot > 0) {
            $existingInformation = 'This period information already existing,Try another period!!!';
            return response::json(array('errors' =>$existingInformation ));
     } else { 

        $mraReportInformationInfos = new MfnMraReportInformation;

      $mraReportInformationInfos->periodDateFrom            = date('Y-m-d',strtotime($periodDateForm)); 
      $mraReportInformationInfos->periodDateTo              = date('Y-m-d',strtotime($periodDateTo)); 
      $mraReportInformationInfos->gnrBodysExpirationDate    = $req->generalBodyExpirationDate; 
      $mraReportInformationInfos->gnrMale                   = $req->numberGeneralOfMaleMembers; 
      $mraReportInformationInfos->gnrFemale                 = $req->numberGeneralOfFemaleMembers; 
      $mraReportInformationInfos->gnrTrans                  = $req->numberGeneralOfTMembers; 
      $mraReportInformationInfos->gnrNoOfYMeetingHe         = $req->numberOfYearlyGeneralMeetinHeld; 
      $mraReportInformationInfos->gnrLastMeetingDate        = $req->lastGeneralMeetingDate; 
      $mraReportInformationInfos->gnrMemPreLastMeeting      = $req->memberPresentAtLastGeneralMeeting; 
      $mraReportInformationInfos->excutiveBodyExirationDate = $req->excutiveBodyExpirationDate; 
      $mraReportInformationInfos->excutiveMale              = $req->numberExcutiveOfMaleMembers; 
      $mraReportInformationInfos->excutiveFemale            = $req->numberExcutiveOfFemaleMembers; 
      $mraReportInformationInfos->excutiveTrans             = $req->numberExcutiveOfTMembers; 
      $mraReportInformationInfos->excutiveNoOfYMeetingHe    = $req->numberOfYearlyExcutiveMeetinHeld; 
      $mraReportInformationInfos->excutiveLastMeetingDate   = $req->lastExcutiveMeetingDate; 
      $mraReportInformationInfos->excutiveMemPreLastMeeting = $req->memberPresentAtLastExcutiveMeeting; 
      $mraReportInformationInfos->serviceRules              = $req->oparatinalServiceRule; 
      $mraReportInformationInfos->financialPolicy           = $req->oparatinalFinancialPolicy; 
      $mraReportInformationInfos->serviceCreditPolicy       = $req->oparatinalServeceAndCreditPolicy; 
      $mraReportInformationInfos->nisAntiMoneyLaGuidLine    = $req->oparatinalNISAntimony; 
      $mraReportInformationInfos->citizenCharter            = $req->oparatinalCitizenCharter; 
      $mraReportInformationInfos->createdDate               = $new; 

      $mraReportInformationInfos->save();

      return response()->json("Success");
     }
     
  }
  public function detailsMraReportInformation($mraReportInformationId) {

            $mfnMraReportInformationDetails = DB::table('mfn_mra_report_information')->where('id',$mraReportInformationId)->first();
            
            return view('microfin/configuration/mraReportInformation/mraReportInformationDetails',['mfnMraReportInformationDetails'=>$mfnMraReportInformationDetails]);
        }

  public function getUpdateMraReportInformation($mraReportInformationId) {

            $mfnMraReportInformationGetUpdateValues = DB::table('mfn_mra_report_information')->where('id',$mraReportInformationId)->first();
            
            return view('microfin/configuration/mraReportInformation/updateMraReportInformation',['mfnMraReportInformationGetUpdateValues'=>$mfnMraReportInformationGetUpdateValues]);
        }

    public function updateMRAReportInformationItem(Request $req) {

        list($periodDateForm,$periodDateTo)=explode(',', $req->reportPeriod);

        $mraReportInformationInfos = MfnMraReportInformation::find($req->mraReportInfoId);
        
          $mraReportInformationInfos->periodDateFrom            = date('Y-m-d',strtotime($periodDateForm)); 
          $mraReportInformationInfos->periodDateTo              = date('Y-m-d',strtotime($periodDateTo)); 
          $mraReportInformationInfos->gnrBodysExpirationDate    = $req->generalBodyExpirationDate; 
          $mraReportInformationInfos->gnrMale                   = $req->numberGeneralOfMaleMembers; 
          $mraReportInformationInfos->gnrFemale                 = $req->numberGeneralOfFemaleMembers; 
          $mraReportInformationInfos->gnrTrans                  = $req->numberGeneralOfTMembers; 
          $mraReportInformationInfos->gnrNoOfYMeetingHe         = $req->numberOfYearlyGeneralMeetinHeld; 
          $mraReportInformationInfos->gnrLastMeetingDate        = $req->lastGeneralMeetingDate; 
          $mraReportInformationInfos->gnrMemPreLastMeeting      = $req->memberPresentAtLastGeneralMeeting; 
          $mraReportInformationInfos->excutiveBodyExirationDate = $req->excutiveBodyExpirationDate; 
          $mraReportInformationInfos->excutiveMale              = $req->numberExcutiveOfMaleMembers; 
          $mraReportInformationInfos->excutiveFemale            = $req->numberExcutiveOfFemaleMembers; 
          $mraReportInformationInfos->excutiveTrans             = $req->numberExcutiveOfTMembers; 
          $mraReportInformationInfos->excutiveNoOfYMeetingHe    = $req->numberOfYearlyExcutiveMeetinHeld; 
          $mraReportInformationInfos->excutiveLastMeetingDate   = $req->lastExcutiveMeetingDate; 
          $mraReportInformationInfos->excutiveMemPreLastMeeting = $req->memberPresentAtLastExcutiveMeeting; 
          $mraReportInformationInfos->serviceRules              = $req->oparatinalServiceRule; 
          $mraReportInformationInfos->financialPolicy           = $req->oparatinalFinancialPolicy; 
          $mraReportInformationInfos->serviceCreditPolicy       = $req->oparatinalServeceAndCreditPolicy; 
          $mraReportInformationInfos->nisAntiMoneyLaGuidLine    = $req->oparatinalNISAntimony; 
          $mraReportInformationInfos->citizenCharter            = $req->oparatinalCitizenCharter; 

          $mraReportInformationInfos->save();

          return response()->json("Success");


        }


        public function mraReportInformationDelete(Request $req) {

                MfnMraReportInformation::find($req->id)->delete();
        return response()->json('success');

    }
}