<?php

namespace App\Http\Controllers\accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\accounting\AddVoucherType;
use App\accounting\AddVoucher;
use App\accounting\AddVoucherDetails;
use App\accounting\AccMisType;
use App\accounting\AccMisConfiguration;
use App\gnr\GnrProject;
use App\gnr\GnrProjectType;
use App\gnr\GnrBranch;
use App\accounting\AddLedger;
use App\accounting\AccDayEnd;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon;


class Accounting
{ 

    public function getBranchArrWiseLedgerIds($projectId, $branchIdArray){


        $allLedgersIdArr = DB::table('acc_account_ledger')->where('isGroupHead', 0)->pluck('projectBranchId', 'id')->toArray();

        $ledgerMatchedId=array();
        
        foreach ($allLedgersIdArr as $ledgerId => $projectBranchId) {
            $splitArray=str_replace(array('[', ']', '"', ''), '',  $projectBranchId); 

            $splitArrayFirstValue = explode(",", $splitArray);
            $splitArraySecondValue = explode(":", $splitArrayFirstValue[0]);

            $array_length=substr_count($splitArray, ",");

            for($i=0; $i<$array_length+1; $i++){

                $splitArrayFirstValue = explode(",", $splitArray);

                $splitArraySecondValue = explode(":", $splitArrayFirstValue[$i]);
                $firstIndexValue=(int)$splitArraySecondValue[0];
                $secondIndexValue=(int)$splitArraySecondValue[1];

                if($firstIndexValue==0){
                    if($secondIndexValue==0){
                        array_push($ledgerMatchedId, $ledgerId);
                    }
                }else{
                    // if($firstIndexValue!=$temp){
                        if($firstIndexValue==$projectId){
                            if($secondIndexValue==0){
                                array_push($ledgerMatchedId, $ledgerId);
                            }else{

                                if (in_array($secondIndexValue, $branchIdArray)) {
                                    array_push($ledgerMatchedId, $ledgerId);
                                } 
                                
                                // foreach ($branchIdArray as $checkBranchId) {
                                //     if($checkBranchId==$secondIndexValue){
                                //         array_push($ledgerMatchedId, $ledgerId);                                        
                                //     }
                                // }

                            }
                        }
                    // }
                    // $temp=$firstIndexValue;
                }
            }   //for
        }       //foreach

        return $ledgerMatchedId;

    }       //getBranchArrWiseLedgerIds Function

    public function getProjectOption()
    {
        
        $projectsOption =   GnrProject::select(DB::raw("CONCAT(LPAD(projectCode, 3, 0), '-', name) AS nameWithCode"), 'id')
                                ->pluck('nameWithCode', 'id')
                                ->toArray();

        return $projectsOption;
    }

    public function getProjectTypeInfoOption()
    {
        
        $projectTypesOption =   GnrProjectType::select(DB::raw("CONCAT(LPAD(projectTypeCode, 3, 0), '-', name) AS nameWithCode"), 'id')
                                    ->pluck('nameWithCode', 'id')
                                    ->toArray();

        return $projectTypesOption;
    }

    public function getVoucherTypesOption ()
    {
        $voucherTypesOption =   AddVoucherType::pluck('shortName', 'id')->toArray();

        return $voucherTypesOption;
    }

    public function getBranchOption()
    {
        // array('-1' => 'All with HO', '-2' => 'All with out HO') +

        $branchesOption =   GnrBranch::select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                                ->pluck('nameWithCode', 'id')
                                ->toArray();

        return $branchesOption;
    }


    public function getModuleOption()
    {
        $modulesOption =   DB::table('gnr_module')->whereNotIn('id', [4,7])->pluck('name', 'id')->toArray();

        return $modulesOption;
    }

    public function getMisTypeOption()
    {
        $misTypesOption=AccMisType::pluck('name','id')->toArray();

        return $misTypesOption;
    }


    public function getVoucherCodeForAV($voucherCodeArr){

        $voucherTypeId=$voucherCodeArr['voucherTypeId'];
        $projectTypeId=$voucherCodeArr['projectTypeId'];
        $branchId=$voucherCodeArr['branchId'];

        $preVoucherCode=DB::table('acc_voucher')
                                ->where('voucherTypeId', $voucherTypeId)
                                ->where('projectTypeId', $projectTypeId)
                                ->where('branchId', $branchId)
                                ->max('voucherCode');

        if ($preVoucherCode!=null) {
            
            $splitPreVoucherCode = explode(".", $preVoucherCode);

            foreach ($splitPreVoucherCode as $key => $value) {
                if ($key==0) {
                    $shortNameOfVoucherType=$value;
                }elseif ($key==1) {
                    $branchCode=$value;
                }elseif ($key==2) {
                    $projectTypeCode=$value;
                }elseif ($key==3) {
                    $newCode=str_pad(((int)$value)+1, 5,"0",STR_PAD_LEFT);
                }
            }

        }else{

            $shortNameOfVoucherType=DB::table('acc_voucher_type')->where('id',$voucherTypeId)->value('shortName');
            $branchCode=str_pad(DB::table('gnr_branch')->where('id',$branchId)->value('branchCode'), 3,"0",STR_PAD_LEFT);
            $projectTypeCode=str_pad(DB::table('gnr_project_type')->where('id',$projectTypeId)->value('projectTypeCode'), 5,"0",STR_PAD_LEFT);
            $newCode=str_pad(1, 5,"0",STR_PAD_LEFT);

        }        
        $voucherCode=$shortNameOfVoucherType.".".$branchCode.".".$projectTypeCode.".".$newCode;

        return $voucherCode;
    }




}       //Accounting Class
