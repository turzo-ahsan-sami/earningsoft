<?php

namespace App\Http\Controllers\accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\accounting\AddVoucher;
use App\accounting\AddVoucherDetails;
use App\gnr\GnrProject;
use App\gnr\GnrProjectType;
use App\gnr\GnrBranch;
use App\gnr\GnrCompany;
use App\gnr\GnrArea;
use App\gnr\GnrZone;
use App\gnr\GnrDepartment;
use App\accounting\AddLedger;
use App\Service\Service;
use Validator;
use App\accounting\AccComments;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon;
use App\accounting\AccApprovals;
use App\gnr\GnrPosition;
use App\gnr\GnrEmployee;
use App\hr\EmployeeOrganizationInfo;
use App\User;

class AccViewVoucherController extends Controller {

    public function test() {

        $vouchers = DB::table('acc_voucher')
        ->where('id',640)
        ->get();
        // $vouchers = AddVoucherDetails::all();
        $projects = GnrProject::select('id','name')->get();
        $projectTypes = GnrProjectType::select('id','name')->get();
        // $ledgerAccounts = AddLedger::select('id','name')->where('parentId', '>', 0)->get();
        // $maxId = AddVoucher::max('id');
        // $maxVoucherId = AddVoucher::max('voucherId')+1;

        return view('accounting.vouchers.viewVouchers',['vouchers' => $vouchers,'projects' => $projects,
            'projectTypes' => $projectTypes,
            // 'ledgerAccounts' => $ledgerAccounts,
            // 'maxId' => $maxId,
            // 'maxVoucherId' => $maxVoucherId
        ]);
    }

    //for edit append rows
    public function useVoucherRows(Request $req){
        $userCompanyId = Auth::user()->company_id_fk;
        $voucherIdValue =  AddVoucher::where('id',$req->id)->where('companyId',$userCompanyId)->select('voucherId')->first();
        $useDetailsTables =  AddVoucher::where('voucherId',$voucherIdValue->voucherId)->where('companyId',$userCompanyId)->get();

        $data = array(
            'useDetailsTables'	=> $useDetailsTables
        );
        return response()->json($data);
        //return response()->json($useDetailsTables);
    }

    //delete by VoucherID
    public function deleteItem(Request $req) {

        // dd($req->all());
        $service = new Service;
        $rowId = $req->id;
        $previousdata=AddVoucher::find($rowId);
        $voucher = AddVoucher::find ($rowId);
        $voucherDate=\Carbon\Carbon::parse($voucher->voucherDate)->format('Y-m-d');
        $branch = $voucher->branchId;
        $monthEndDate = \Carbon\Carbon::parse($voucherDate)->endOfMonth()->format('Y-m-d');
        $fiscalYearId = DB::table('gnr_fiscal_year')->where('fyStartDate', '<=', $voucherDate)->where('fyEndDate', '>=', $voucherDate)->value('id');
        $yearEndExists = (boolean) DB::table('acc_year_end')->where('branchIdFk', $branch)->where('fiscalYearId', $fiscalYearId)->first();
        $monthEndExists = (boolean) DB::table('acc_month_end')->where('branchIdFk', $branch)->where('date', $monthEndDate)->where('status', 1)->first();
        // dd($monthEndExists);

        if($yearEndExists == true){
            // dd(1);
            return response()->json(['responseText' => 'Year End Exists! Delete year end first']);
        }
//        AddVoucher::where('id',$rowId)->delete();

        // $voucherId=AddVoucherDetails::select('voucherId')->where('id',$rowId)->first();
        // $count=AddVoucherDetails::select('id')->where('voucherId', $voucherId->voucherId)->count();
        AddVoucher::where('id', $req->id)->delete();
        AddVoucherDetails::where('voucherId', $req->id)->delete();
        // dd($voucher);
        // $count='Hello';
        //execute month end
        if ($monthEndExists == true) {
            // dd(2);
            $summary = $service->monthEndExecute($branch, $voucherDate);

            // if (count($summary) > 0) {
            //     return response()->json(['responseText' => 'Voucher successfully deleted, but month end not executed!<br>'. 'Debit Amount and Credit Amount are not same!<br>'. 'Total Debit: ' . $summary['debit'] . '<br>Total Credit: ' . $summary['credit'] . '<br>Difference: '. ($summary['debit'] - $summary['credit'])]);
            // }
        }
        // dd(3);
        $logArray = array(
          'moduleId'  => 4,
          'controllerName'  => 'AccViewVoucherController',
          'tableName'  => 'acc_voucher',
          'operation'  => 'delete',
          'previousData'  => $previousdata,
          'primaryIds'  => [$previousdata->id]
      );
        \App\Http\Controllers\gnr\Service::createLog($logArray);

        return response()->json(['responseText' => 'Voucher deleted successfully!']);
    }

    public function deleteFTVoucherItem(Request $req) {

        // dd($req->all());
        $service = new Service;
        $rowId = $req->id;
        $previousdata=AddVoucher::find($rowId);
        $ftId=AddVoucher::where('id',$rowId)->value('ftId');
        $voucherId=AddVoucher::where('ftId',$ftId)->pluck('id')->toArray();
        $voucher = AddVoucher::find ($rowId);
        $voucherDate=\Carbon\Carbon::parse($voucher->voucherDate)->format('Y-m-d');
        $branch = $voucher->branchId;
        $monthEndDate = \Carbon\Carbon::parse($voucherDate)->endOfMonth()->format('Y-m-d');
        $fiscalYearId = DB::table('gnr_fiscal_year')->where('fyStartDate', '<=', $voucherDate)->where('fyEndDate', '>=', $voucherDate)->value('id');
        $yearEndExists = (boolean) DB::table('acc_year_end')->where('branchIdFk', $branch)->where('fiscalYearId', $fiscalYearId)->first();
        $branchesToUpdate = AddVoucher::where('ftId',$ftId)->distinct('branchId')->pluck('branchId')->toArray();
        // array_push($branchesToUpdate, 1);
        array_unique($branchesToUpdate);
        // dd($branchesToUpdate);

        if($yearEndExists == true){
            // dd(1);
            return response()->json(['responseText' => 'Year End Exists! Delete year end first']);
        }

        AddVoucher::where('ftId', $ftId)->delete();
        AddVoucherDetails::whereIn('voucherId', $voucherId)->delete();


        // dd($voucherDate);
        foreach ($branchesToUpdate as $key => $branch) {

            $monthEndExists = (boolean) DB::table('acc_month_end')->where('branchIdFk', $branch)->where('date', $monthEndDate)->where('status', 1)->first();
            if ($monthEndExists == true) {
                // dd(2);
                $summary = $service->monthEndExecute($branch, $voucherDate);

                // if (count($summary) > 0) {
                //     return response()->json(['responseText' => 'Voucher successfully deleted, but month end not executed!<br>'. 'Debit Amount and Credit Amount are not same!<br>'. 'Total Debit: ' . $summary['debit'] . '<br>Total Credit: ' . $summary['credit'] . '<br>Difference: '. ($summary['debit'] - $summary['credit'])]);
                // }
            }
        }
        $logArray = array(
          'moduleId'  => 4,
          'controllerName'  => 'AccViewVoucherController',
          'tableName'  => 'acc_voucher',
          'operation'  => 'delete',
          'previousData'  => $previousdata,
          'primaryIds'  => [$previousdata->id]
      );
        \App\Http\Controllers\gnr\Service::createLog($logArray);

        // $count='Hello';
        return response()->json(['responseText' => 'Voucher deleted successfully!']);
        // return response()->json($voucherId);
    }

    public function editVoucher($encryptedId) {
        $userCompanyId = Auth::user()->company_id_fk;
        $decryptedId=decrypt($encryptedId);
        $voucherInfo = AddVoucher::where('id', $decryptedId)->where('companyId', $userCompanyId)->first();
        $projects = GnrProject::select('id','name')->where('companyId', $userCompanyId)->get();
        $projectTypes = GnrProjectType::select('id','name')->where('companyId', $userCompanyId)->get();
        // $ledgerAccounts = AddLedger::select('id','name')->where('parentId', '>', 0)->get();

        return view('accounting.vouchers.editVouchers',['voucherInfo' => $voucherInfo, 'projects' => $projects,'projectTypes' => $projectTypes]);

    }

    public function updateVoucherItem(Request $request){
        //dd($request->all());
        

        $fetchPreviousImagesById =  AddVoucher::find($request->voucherId);
        
        $beforeImage =$fetchPreviousImagesById->image;

        $previousImages = json_decode($fetchPreviousImagesById->image);
        $previousRemoveImages = json_decode($request->previousImages);
        
        //dd();
        if($previousImages && $request->image){      
            foreach($request->image as $image){
                $data = array_diff($previousImages, $previousRemoveImages);
                $filename = str_random(10) . '.' . $image->getClientOriginalExtension();
                $destinationPath = base_path() . '/public/images/vouchers/';
                $image->move($destinationPath,$filename);
                array_push($data, $filename);
            }
            
        }elseif($previousImages &&  $previousRemoveImages){
            if($previousRemoveImages && $request->image){
                foreach($request->image as $image){
                    $data = array_diff($previousImages, $previousRemoveImages);
                    $filename = str_random(10) . '.' . $image->getClientOriginalExtension();
                    $destinationPath = base_path() . '/public/images/vouchers/';
                    $image->move($destinationPath,$filename);
                    array_push($data, $filename);
               
                }
            }else{
                $data = null;
                //dd('not ok');
            }
            
            
        }elseif($request->image){
            foreach($request->image as $image){
                $filename = str_random(10) . '.' . $image->getClientOriginalExtension();
                $destinationPath = base_path() . '/public/images/vouchers/';
                 $image->move($destinationPath,$filename);
                $data[] = $filename;
            }
            
        }

        $rules = array(
            'projectId' => 'required',
            'projectTypeId' => 'required',
            'voucherCode' => 'required',
            'amountColumn' => 'required',
            'globalNarration' => 'required'
        );

        $attributeNames = array(
            'projectId'    => 'Project Name',
            'projectTypeId'   => 'Project Type',
            'voucherCode'   => 'Voucher Code',
            'amountColumn'   => 'Table Data',
            'globalNarration'   => 'Global Narration'
        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails())
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        else{

            $service = new Service;
            $previousdata = AddVoucher::find ($request->voucherId);
            $voucherDate=\Carbon\Carbon::parse($request->voucherDate);
            $voucher = AddVoucher::find ($request->voucherId);
            //dd($voucher);
            $branch = $voucher->branchId;
            $voucherCreateDate = \Carbon\Carbon::parse($voucherDate)->format('Y-m-d');
            $monthEndDate = \Carbon\Carbon::parse($voucherCreateDate)->endOfMonth()->format('Y-m-d');
            $fiscalYearId = DB::table('gnr_fiscal_year')->where('fyStartDate', '<=', $voucherCreateDate)->where('fyEndDate', '>=', $voucherCreateDate)->value('id');
            $yearEndExists = (boolean) DB::table('acc_year_end')->where('branchIdFk', $branch)->where('fiscalYearId', $fiscalYearId)->first();
            $monthEndExists = (boolean) DB::table('acc_month_end')->where('branchIdFk', $branch)->where('date', $monthEndDate)->where('status', 1)->first();
            // dd($monthEndExists);

            if($yearEndExists == true){

                return response()->json(['responseText' => 'Year End Exists! Delete year end first']);
            }


            $voucher->voucherTypeId = $request->voucherTypeId;
            // $voucher->projectId = $request->projectId;
            // $voucher->projectTypeId = $request->projectTypeId;
            $voucher->voucherDate = $voucherDate;
            $voucher->voucherCode = $request->voucherCode;
            $voucher->globalNarration = $request->globalNarration;
            // $voucher->branchId = $request->branchId;
            $voucher->companyId = $request->companyId;
            $voucher->vGenerateType = 2;
            $voucher->editedBy = $request->prepBy;
            if($previousImages && $request->image){
                $voucher->image = json_encode($data);
            }elseif($request->image){
                $voucher->image = json_encode($data);
            }elseif($previousImages &&  $previousRemoveImages){
               if($request->image){
                 $voucher->image = json_encode($data);
             }else{
                
                $voucher->image = null;
             }
            }
           

            
            $voucher->save();

            if($voucher){
                AccComments::truncate();
            }

            AddVoucherDetails::where('voucherId', $request->voucherId)->delete();

            $array_size = count(json_decode($request->tableAmount));
            $data = [];
            for ($i=0; $i<$array_size; $i++){

                $data[] = array(
                    'voucherId' => $request->voucherId,
                    'debitAcc' => (int) json_decode($request->tableDebitAcc)[$i],
                    'creditAcc' =>(int) json_decode($request->tableCreditAcc)[$i],
                    'amount' => (float) json_decode($request->tableAmount)[$i],
                    'localNarration' => (string) json_decode($request->tableNarration)[$i]
                );
            }
            //dd($data);
            DB::table('acc_voucher_details')->insert($data);

            //execute month end
            if ($monthEndExists == true) {
                // dd(1);
                $summary = $service->monthEndExecute($branch, $voucherCreateDate);
            }
            $logArray = array(
                'moduleId'  => 4,
                'controllerName'  => 'AccViewVoucherController',
                'tableName'  => 'acc_voucher',
                'operation'  => 'update',
                'previousData'  => $previousdata,
                'primaryIds'  => [$previousdata->id]
            );
            //dd($logArray);
            //\App\Http\Controllers\gnr\Service::createLog($logArray);

            return response()->json(['responseText' => 'Voucher successfully updated!']);
        }
    }

    public function editFTVoucher($encryptedId) {
       // dd($encryptedId);
        $decryptedId=decrypt($encryptedId);
        $voucherInfo = AddVoucher::where('id', $decryptedId)->first();
        $projects = GnrProject::select('id','name')->where('companyId',Auth::user()->company_id_fk)->get();
        $projectTypes = GnrProjectType::select('id','name')->where('companyId',Auth::user()->company_id_fk)->get();
        //dd($projectTypes);
        return view('accounting.vouchers.editFTVouchers',['voucherInfo' => $voucherInfo, 'projects' => $projects,'projectTypes' => $projectTypes]);
    }

    public function updateFTVoucherItem(Request $request){
        //dd($request->all());
       
        // $fetchPreviousImagesById =  AddVoucher::find($request->voucherIdFrom);

        // $previousImages = json_decode($fetchPreviousImagesById->image);
        // $previousRemoveImages = json_decode($request->previousImages);

        // $data = array_diff($previousImages, $previousRemoveImages);

        // if ($request->image != null) {
        //     foreach($request->image as $image){
        //         $filename = str_random(10) . '.' . $image->getClientOriginalExtension();
        //         $destinationPath = base_path() . '/public/images/vouchers/';
        //         $image->move($destinationPath,$filename);
        //         array_push($data, $filename);
        //     }
        // }


        $user_branch_id = Auth::user()->branchId;


        $rules = array(
            'projectId' => 'required',
            'projectTypeId' => 'required',
            'voucherCode' => 'required',
            'amountColumn' => 'required',
            'globalNarration' => 'required'
        );

        $attributeNames = array(
            'projectId'    => 'Project Name',
            'projectTypeId'   => 'Project Type',
            'voucherCode'   => 'Voucher Code',
            'amountColumn'   => 'Table Data',
            'globalNarration'   => 'Global Narration'
        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()){
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        else{
           $previousdata = AddVoucher::find ($request->voucherId);

           $voucherDate=\Carbon\Carbon::parse($request->voucherDate)->format('Y-m-d');
           $sourceBranch = (int) $request->branchIdFrom;
           $fiscalYearId = DB::table('gnr_fiscal_year')->where('fyStartDate', '<=', $voucherDate)->where('fyEndDate', '>=', $voucherDate)->value('id');
           $yearEndExists = (boolean) DB::table('acc_year_end')->where('branchIdFk', $sourceBranch)->where('fiscalYearId', $fiscalYearId)->first();
            // dd($yearEndExists);
            // check year end
           if($yearEndExists == true){

            return response()->json(['responseText' => 'Year End Exists! Delete year end first']);
        }

            // if year not ended then proceed
        $newTargetBranchArray=array();
        $targetHeadOfficeId = array(1);
        $match=array();
        $oldTargetBranchArray = json_decode($request->oldTargetBranchArray);

        foreach (json_decode($request->tableTargetBranch) as $eachValue) {
            array_push($newTargetBranchArray, (int) json_decode($eachValue));
        }

        $newUniTargetBranchArray = array_values(array_unique($newTargetBranchArray));

        $targetBranchIdsArrWithoutHeadOffice = array_diff($newTargetBranchArray, $targetHeadOfficeId);

        if($user_branch_id != 1 && count($targetBranchIdsArrWithoutHeadOffice) >0){

                // Delete All Voucher Details Info
            $allFTVoucherIds=DB::table('acc_voucher')->where('ftId', $request->ftId)->pluck('id')->toArray();

            $ftVoucherIdForHeadOffice = DB::table('acc_voucher')->where('ftId', $request->ftId)->where('branchId', 1)->value('id');
            AddVoucherDetails::whereIn('voucherId', $allFTVoucherIds)->delete();

            $referenceVoucherId = AddVoucherDetails::where('voucherId', $ftVoucherIdForHeadOffice)->where('ftFrom', '!=', 1)->where('ftTo', '!=', 1)->value('voucherId');
                // $idsOfVoucherDetails=DB::table('acc_voucher_details')->whereIn('voucherId', $allFTVoucherIds)->pluck('id')->toArray();

                // Update data of Loged In Branch
            $voucherDate=\Carbon\Carbon::parse($request->voucherDate);
            $voucherFrom = AddVoucher::find ($request->voucherIdFrom);

            $voucherFrom->voucherTypeId = $request->voucherTypeId;
                // $voucherFrom->projectId = $request->projectId;
                // $voucherFrom->projectTypeId = $request->projectTypeId;
            $voucherFrom->voucherDate = $voucherDate;
                // $voucherFrom->voucherCode = $request->voucherCode;
            $voucherFrom->globalNarration = $request->globalNarration;
                // $voucherFrom->branchId = $request->branchIdFrom;
            $voucherFrom->companyId = $request->companyId;
            $voucherFrom->vGenerateType = 2;
            $voucherFrom->editedBy = $request->prepBy;
            $voucherFrom->ftId = $request->ftId;
            // if(count($data) > 0 ){
            //     $voucher->image = json_encode($data);
            // }
            //$voucherFrom->save();

            if (!$referenceVoucherId) {
                $previousVoucherCodesForHeadOffice = DB::table('acc_voucher')
                ->where('projectTypeId', $request->projectTypeId)
                ->where('branchId', 1)
                ->where('voucherTypeId', $request->voucherTypeId)
                ->pluck('voucherCode')
                ->toArray();

                $preVoucherCodeForHeadOffice = DB::table('acc_voucher')
                ->where('branchId', 1)
                ->where('projectTypeId', $request->projectTypeId)
                ->where('voucherTypeId', $request->voucherTypeId)
                ->max('voucherCode');


                if(in_array($preVoucherCodeForHeadOffice, $previousVoucherCodesForHeadOffice)) {
                 $splitPreVoucherCode = explode(".", $preVoucherCodeForHeadOffice);
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
                $voucherCodeForHeadOffice=$shortNameOfVoucherType.".".$branchCode.".".$projectTypeCode.".".$newCode;
            }else{
                $voucherCodeForHeadOffice = $request->voucherCode;
            }



            $voucherFromHeadOffice = new AddVoucher;
            $voucherDate=\Carbon\Carbon::parse($request->voucherDate);
            $now = \Carbon\Carbon::now();

            $voucherFromHeadOffice->voucherTypeId = $request->voucherTypeId;
            $voucherFromHeadOffice->projectId = $request->projectId;
            $voucherFromHeadOffice->projectTypeId = $request->projectTypeId;
            $voucherFromHeadOffice->voucherDate = $voucherDate;
            $voucherFromHeadOffice->voucherCode = $voucherCodeForHeadOffice;
            $voucherFromHeadOffice->globalNarration = $request->globalNarration;
            $voucherFromHeadOffice->branchId = Auth::user()->branchId;
            $voucherFromHeadOffice->companyId = $request->companyId;
            $voucherFromHeadOffice->vGenerateType = 2;
            $voucherFromHeadOffice->prepBy = $request->prepBy;
            $voucherFromHeadOffice->ftId = $request->ftId;
            $voucherFromHeadOffice->createdDate = $now;
            // if(count($data) > 0 ){
            //     $voucher->image = json_encode($data);
            // }

            $voucherFromHeadOffice->save();
        }else{

                        //update reference voucher
            $voucherFromHeadOffice = AddVoucher::find ($ftVoucherIdForHeadOffice);

                        // $voucherFromHeadOffice = new AddVoucher;

            $voucherFromHeadOffice->voucherTypeId = $request->voucherTypeId;
                        // $voucherFrom->projectId = $request->projectId;
                        // $voucherFrom->projectTypeId = $request->projectTypeId;
            $voucherFromHeadOffice->voucherDate = $voucherDate;
                        // $voucherFrom->voucherCode = $request->voucherCode;
            $voucherFromHeadOffice->globalNarration = $request->globalNarration;
                        // $voucherFrom->branchId = $request->branchIdFrom;
            $voucherFromHeadOffice->companyId = $request->companyId;
            $voucherFromHeadOffice->vGenerateType = 2;
            $voucherFromHeadOffice->prepBy = $request->prepBy;
            $voucherFromHeadOffice->ftId = $request->ftId;
            //$voucher->image = json_encode($data);
             if(count($data) > 0 ){
                $voucher->image = json_encode($data);
             }
            $voucherFromHeadOffice->save();
        }
                // }



        $dataFrom = [];
        $dataFromHeadOffice = [];

        for ($i=0; $i<count($newTargetBranchArray); $i++){

                    //for branch
            $dataFrom[] = array(
                'voucherId' => (int) $request->voucherIdFrom,
                'createdDate' => $request->createdDate,

                'debitAcc' => (int) json_decode($request->tableDebitAcc)[$i],
                'creditAcc' =>(int) json_decode($request->tableCreditAcc)[$i],
                'amount' => (float) json_decode($request->tableAmount)[$i],
                'ftFrom' => (int) $request->branchIdFrom,
                'ftTo' => (int) $newTargetBranchArray[$i],
                'ftTargetAcc' => (int) json_decode($request->tableTargetBranchHead)[$i],
                'localNarration' => trim((string) json_decode($request->tableNarration[$i]))
            );

            if((int) $newTargetBranchArray[$i] == 1){
                continue;
                        // dd((int) $newTargetBranchArray[$i]);
            }

                    // for Head office
            $dataFromHeadOffice[] = array(
                'voucherId' => $voucherFromHeadOffice->id,
                'createdDate' => $request->createdDate,

                'debitAcc' => (int) json_decode($request->tableDebitAcc)[$i],
                'creditAcc' =>(int) json_decode($request->tableCreditAcc)[$i],
                'amount' => (float) json_decode($request->tableAmount)[$i],
                'ftFrom' => (int) $request->branchIdFrom,
                'ftTo' => (int) $newTargetBranchArray[$i],
                'ftTargetAcc' => (int) json_decode($request->tableTargetBranchHead)[$i],
                'localNarration' => trim((string) json_decode($request->tableNarration[$i]))
            );
        }

        //dd($dataFrom);

    }else{
                 // Delete All Voucher Details Info
        $allFTVoucherIds=DB::table('acc_voucher')->where('ftId', $request->ftId)->pluck('id')->toArray();
        AddVoucherDetails::whereIn('voucherId', $allFTVoucherIds)->delete();

                // $idsOfVoucherDetails=DB::table('acc_voucher_details')->whereIn('voucherId', $allFTVoucherIds)->pluck('id')->toArray();

                // Update data of Loged In
        $voucherDate=\Carbon\Carbon::parse($request->voucherDate);
        $voucherFrom = AddVoucher::find ($request->voucherIdFrom);

        $voucherFrom->voucherTypeId = $request->voucherTypeId;
                // $voucherFrom->projectId = $request->projectId;
                // $voucherFrom->projectTypeId = $request->projectTypeId;
        $voucherFrom->voucherDate = $voucherDate;
                // $voucherFrom->voucherCode = $request->voucherCode;
        $voucherFrom->globalNarration = $request->globalNarration;
                // $voucherFrom->branchId = $request->branchIdFrom;
        $voucherFrom->companyId = Auth::user()->company_id_fk;
        $voucherFrom->vGenerateType = 2;
        $voucherFrom->prepBy = $request->prepBy;
        $voucherFrom->ftId = $request->ftId;
        //$voucher->image = json_encode($data);
        // if(count($data) > 0 ){
        //     $voucherFrom->image = json_encode($data);
        // }
        $voucherFrom->save();
                // AddVoucherDetails::where('voucherId', $request->voucherIdFrom)->delete();
        //dd(json_decode($request->tableAmount));
        $dataFrom = [];
        for ($i=0; $i<count($newTargetBranchArray); $i++){
            $dataFrom[] = array(
                'voucherId' => (int) $request->voucherIdFrom,
                'createdDate' => $request->createdDate,

                'debitAcc' => (int) json_decode($request->tableDebitAcc)[$i],
                'creditAcc' =>(int) json_decode($request->tableCreditAcc)[$i],
                'amount' => (float) json_decode($request->tableAmount)[$i],
                'ftFrom' => (int) $request->branchIdFrom,
                'ftTo' => (int) $newTargetBranchArray[$i],
                'ftTargetAcc' => (int) json_decode($request->tableTargetBranchHead)[$i],
                'localNarration' => trim((string) json_decode($request->tableNarration[$i]))
            );
        }

            } //end of else

           //dd($dataFrom);


            DB::table('acc_voucher_details')->insert($dataFrom);
            if(isset($dataFromHeadOffice)){
                DB::table('acc_voucher_details')->insert($dataFromHeadOffice);
            }

            //Delete Previous Branch Absent Branches
            foreach ($oldTargetBranchArray as $oldTargetBranchId) {
                if (!in_array($oldTargetBranchId, $newUniTargetBranchArray)) {
                    $fullVoucherDeleteId=AddVoucher::where('ftId',$request->ftId)->where('branchId',$oldTargetBranchId)->value('id');
                    AddVoucher::where('id', $fullVoucherDeleteId)->delete();
                    // AddVoucherDetails::where('voucherId', $fullVoucherDeleteId)->delete();
                    // array_push($match, $fullVoucherDeleteId);
                }
            }

            //Update data of Targeted Branches
            $dataTarget = [];
            foreach ($newUniTargetBranchArray as $index1 => $newUniTargetBranchId) {
                if (in_array($newUniTargetBranchId, $oldTargetBranchArray)) {

                    //Update data of Target Branch which are already inserted
                    $voucherIdTarget = AddVoucher::where('ftId',$request->ftId)->where('branchId',$newUniTargetBranchId)->value('id');
                    $voucherTargetBranch = AddVoucher::find($voucherIdTarget);

                    $voucherTargetBranch->voucherTypeId = $request->voucherTypeId;
                    $voucherTargetBranch->projectId = $request->projectId;
                    $voucherTargetBranch->projectTypeId = $request->projectTypeId;
                    $voucherTargetBranch->voucherDate = $voucherDate;
                    // $voucherTargetBranch->voucherCode = $request->voucherCode;
                    $voucherTargetBranch->globalNarration = $request->globalNarration;
                    $voucherTargetBranch->branchId = $newUniTargetBranchId;
                    $voucherTargetBranch->companyId = Auth::user()->company_id_fk;
                    $voucherTargetBranch->vGenerateType = 2;
                    $voucherTargetBranch->prepBy = $request->prepBy;
                    $voucherTargetBranch->ftId = $request->ftId;
                    //$voucher->image = json_encode($data);
                     // if(count($data) > 0 ){
                     //  $voucherTargetBranch->image = json_encode($data);
                     // }
                    $voucherTargetBranch->save();

                    // AddVoucherDetails::where('voucherId', $voucherIdTarget)->delete();

                    foreach ($newTargetBranchArray as $index2 => $newTargetBranchId) {
                        if($newUniTargetBranchId==$newTargetBranchId){
                            $dataTarget[] = array(
                                'voucherId' => $voucherIdTarget,
                                'createdDate' => $request->createdDate,

                                'debitAcc' => ((int) json_decode($request->tableTargetBranchHead[$index2])==0 ? (int) json_decode($request->tableCreditAcc[$index2]) : (int) json_decode($request->tableTargetBranchHead[$index2])),
                                'creditAcc' =>(int) json_decode($request->tableDebitAcc[$index2]),
                                'amount' => (float) json_decode($request->tableAmount[$index2]),
                                'ftFrom' => (int) $request->branchIdFrom,
                                'ftTo' => (int) $newTargetBranchId,
                                'ftTargetAcc' => (int) json_decode($request->tableTargetBranchHead[$index2]),
                                'localNarration' => trim((string) json_decode($request->tableNarration[$index2]))
                            );
                        }
                    }
                }else{

                    $tempVoucherCode = DB::table('acc_voucher')->where('branchId', $newUniTargetBranchId)->where('projectTypeId', $request->projectTypeId)->where('voucherTypeId', $request->voucherTypeId)->max('voucherCode');

                    if($tempVoucherCode){
                        $splitPreVoucherCode = explode(".", $tempVoucherCode);
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
                        $voucherCodeTo=$shortNameOfVoucherType.".".$branchCode.".".$projectTypeCode.".".$newCode;
                    }else{
                        $shortNameOfVoucherType = DB::table('acc_voucher_type')->where('id',  $request->voucherTypeId)->value('shortName');
                        $branchCode =str_pad( DB::table('gnr_branch')->where('id',  (int) $newUniTargetBranchId)->value('branchCode') , 3, "0", STR_PAD_LEFT);
                        $projectTypeCode =str_pad( DB::table('gnr_project_type')->where('id', $request->projectTypeId)->value('projectTypeCode') , 5, "0", STR_PAD_LEFT);
                        $newCode=str_pad(1, 5,"0",STR_PAD_LEFT);
                        $voucherCodeTo=$shortNameOfVoucherType.".".$branchCode.".".$projectTypeCode.".".$newCode;
                    }

                    $voucherTargetBranch = new AddVoucher;

                    $voucherTargetBranch->voucherTypeId = $request->voucherTypeId;
                    $voucherTargetBranch->projectId = $request->projectId;
                    $voucherTargetBranch->projectTypeId = $request->projectTypeId;
                    $voucherTargetBranch->voucherDate = $voucherDate;
                    $voucherTargetBranch->voucherCode = $voucherCodeTo;
                    $voucherTargetBranch->globalNarration = $request->globalNarration;
                    $voucherTargetBranch->branchId = (int) $newUniTargetBranchId;
                    $voucherTargetBranch->companyId = Auth::user()->company_id_fk;
                    $voucherTargetBranch->vGenerateType = 2;
                    $voucherTargetBranch->prepBy = $request->prepBy;
                    $voucherTargetBranch->ftId = $request->ftId;
                    $voucherTargetBranch->createdDate = $request->createdDate;
                    //$voucher->image = json_encode($data);
                    //  if(count($data) > 0 ){
                    //     $voucherTargetBranch->image = json_encode($data);
                    // }
                    $voucherTargetBranch->save();

                    $lastVoucherIdTarget = DB::table('acc_voucher')->where('voucherCode',$voucherCodeTo)->value('id');

                    foreach ($newTargetBranchArray as $index3 => $newTarBraId) {
                        if($newUniTargetBranchId==$newTarBraId){
                            $dataTarget[] = array(
                                'voucherId' => $lastVoucherIdTarget,
                                'createdDate' => $request->createdDate,

                                'debitAcc' => ((int) json_decode($request->tableTargetBranchHead[$index3])==0 ? (int) json_decode($request->tableCreditAcc[$index3]) : (int) json_decode($request->tableTargetBranchHead[$index3])),
                                'creditAcc' =>(int) json_decode($request->tableDebitAcc[$index3]),
                                'amount' => (float) json_decode($request->tableAmount[$index3]),
                                'ftFrom' => (int) $request->branchIdFrom,
                                'ftTo' => (int) $newTarBraId,
                                'ftTargetAcc' => (int) json_decode($request->tableTargetBranchHead[$index3]),
                                'localNarration' => trim((string) json_decode($request->tableNarration[$index3]))
                            );
                        }
                    }

                }       //End Else
            }
                  //newUniTargetBranchArray foreach Loop

            DB::table('acc_voucher_details')->insert($dataTarget);

            //execute month end
            // dd($request->all());
            $service = new Service;
            // branch id collection
            $branchIdArr = array();
            $newTargetBranchArr = array();

            $sourceBranch = (int) $request->branchIdFrom;
            $oldTargetBranchArr = json_decode($request->oldTargetBranchArray);
            array_push($branchIdArr, $sourceBranch);
            foreach (json_decode($request->tableTargetBranch) as $value) {
                array_push($newTargetBranchArr, (int) json_decode($value));
            }
            $branchIdArr = array_unique(array_merge($branchIdArr, $oldTargetBranchArr, $newTargetBranchArr));
            // dd($branchIdArr);

            $voucherCreatedDate = \Carbon\Carbon::parse($request->voucherDate)->format('Y-m-d');
            $monthEndDate = \Carbon\Carbon::parse($voucherCreatedDate)->endOfMonth()->format('Y-m-d');

            foreach ($branchIdArr as $key => $branch) {

                $monthEndExists = (boolean) DB::table('acc_month_end')->where('branchIdFk', $branch)->where('date', $monthEndDate)->where('status', 1)->first();
                // dd($monthEndExists);
                // check month end
                if ($monthEndExists == true) {
                    // dd(1);
                    $summary = $service->monthEndExecute($branch, $monthEndDate);

                    // if (count($summary) > 0) {
                    //     return response()->json(['responseText' => 'Voucher successfully updated, but month end not executed! || '. 'Debit Amount and Credit Amount are not same! || '. 'Total Debit: ' . $summary['debit'] . ' || Total Credit: ' . $summary['credit'] . ' || Difference: '. ($summary['debit'] - $summary['credit'])]);
                    // }
                }
            }

            // $data=array(
            //     'oldTargetBranchArray'=>$oldTargetBranchArray,
            //     'newTargetBranchArray'=>$newTargetBranchArray,
            //     'newUniTargetBranchArray'=>$newUniTargetBranchArray,
            //     'voucherTargetBranch'=>$voucherTargetBranch,
            //     'allFTVoucherIds'=>$allFTVoucherIds,
            //     // 'idsOfVoucherDetails'=>$idsOfVoucherDetails
            //     );
            //
            // return response()->json($data);
            // $logArray = array(
            //     'moduleId'  => 4,
            //     'controllerName'  => 'AccViewVoucherController',
            //     'tableName'  => 'acc_voucher',
            //     'operation'  => 'update',
            //     'previousData'  => $previousdata,
            //     'primaryIds'  => [$previousdata->id]
            // );
            // \App\Http\Controllers\gnr\Service::createLog($logArray);
           // dd($dataFrom);
            return response()->json(['responseText' => 'Voucher successfully updated!'], 200);
        }
    }



    public function printVoucher($encryptedId){
        //dd('ok');
        $decryptedId   = decrypt($encryptedId);
        $voucherIdInfo = AddVoucher::where('id', $decryptedId)->first();
        $userEmployeesId = Auth::user()->emp_id_fk;
        $userCompanyId = Auth::user()->company_id_fk;
        $branchId      = $voucherIdInfo->branchId;
        $areas         = GnrArea::select('id','branchId')->get();
        $zones         = GnrZone::select('id','areaId')->get();

        $branchInfo = GnrBranch::where('id',$branchId)->where('companyId',$userCompanyId)->first();
      
        //dd($branchInfo);
         $prepBy = [];
         $verifiedBy = [];
         $reviewedBy = [];
         $approvedBy = [];
         $rejectedBy = [];


        //verified by information


        //dd(Auth::user());
        foreach ($areas as $area) {
            $branchIdArr = $area->branchId;
            $areasId = $area->id;

            if(in_array($areasId, $branchIdArr)){
                $areaId = $area->id;
            }
        }
        //dd($areaId);

        $branchUnderArea  = GnrArea::where('id',$areaId)->value('branchId');
        $branchUnderArea = array_map('intVal', $branchUnderArea);

        //branch collection end

        //area collection

        foreach ($zones as $zone) {
            $zoneIdArr = $zone->areaId;

            if(in_array($areaId, $zoneIdArr)){
                $zoneId = $zone->id;
            }
        }


        $areasUnderZone = GnrZone::where('id',$zoneId)->value('areaId');
        //dd($areasUnderZone);

        $branchesUnderZoneArr = [];


        foreach ($areasUnderZone as  $area) {
            $branchesUnderZone = GnrArea::where('id',$area)->value('branchId');

            foreach ($branchesUnderZone as  $brancheUnderZone) {
                array_push($branchesUnderZoneArr,$brancheUnderZone);

            }
        }

        //dd($branchesUnderZone);

        $brancheUnderZone = array_map('intVal', $branchesUnderZoneArr);

        //area collection end
        //dd($branchInfo);
        if($branchInfo){
            if($branchInfo->branchCode == 0){
            $branch = 'Head Office';
            }else{
              $branch = 'All Branch'  ;
            } 

        }else{
            $branchInfo = '';
            $branch = '';
        }
        //dd($voucherIdInfo->branchId);
         $branchName =GnrBranch::where('id', $voucherIdInfo->branchId)->value('name');
         //dd($branchId);
       //settings collection start

        $settings = AccApprovals::where('projectId', $voucherIdInfo->projectId)->where('branch', $branch)->orderBy('date','DESC')->first();
        //dd( $settings);

        if ($settings) {
            $settingsExist = 1;
            $verifyId   =json_decode($settings->verifiedById);
            $reviewId   =json_decode($settings->reviewedById);
            $approveId  =json_decode($settings->approvedById);
            //dd($verifyId );
            //settings collection end

            //employees  information collection start

            $employees  =  DB::table('gnr_employee')->where('company_id_fk',$userCompanyId)->where('status','1')->get();
            //dd($employees);
            // dd($voucherIdInfo->prepBy,$employees);
            $preparedBy =  $employees->where('id',$voucherIdInfo->prepBy)->first();
            //dd($preparedBy);
            if($preparedBy){
              $prepBy['emp_name_english'] = GnrEmployee::where('id', $preparedBy->id)->value('name');
                $prepBy['emp_id']     = GnrEmployee::where('id', $preparedBy->id)->value('employeeId');
                $prepBy['name']       = GnrPosition::where('id',  $preparedBy->position_id_fk)->where('companyId',$userCompanyId)->value('name');
                $prepBy['dep_name']   = GnrDepartment::where('id',$preparedBy->department_id_fk)->where('companyId',$userCompanyId)->value('name');   
              
            }else{
                $prepBy['emp_name_english'] = '';
                $prepBy['emp_id'] = '';
                $prepBy['name'] = '';
                $prepBy['dep_name'] = '';
            }
           
                
           
           //dd($prepBy);
            

            //user information collection for created verification  using verified_by
            $accComment = AccComments::where('voucher_id',$voucherIdInfo->id)->orderBy('updated_at','DESC')->first();
            //dd($accComment);
            // $accComment = AccComments::select('id','rejected_by','voucher_id','verified_by','reviewed_by','approved_by','comments_details_verify','comments_details_review','comments_details_approve','created_at','status')->where('voucher_id',$voucherIdInfo->id)->orderBy('updated_at','DESC')->first();
            //dd($accComment);
            if($accComment){
                if($accComment->verified_by  != 0){
                    $verifiedBy['emp_name_english'] = GnrEmployee::where('id',$accComment->verified_by)->value('name');
                    $verifiedBy['emp_id']  = GnrEmployee::where('id', $userEmployeesId)->value('employeeId');
                    $verifiedBy['name'] = GnrDepartment::where('id',  $userEmployeesId)->where('companyId',$userCompanyId)->value('name');
                    $verifiedBy['status'] = 'Verified';
                }else{
                      $verifiedBy = '';
                    }

            }else{
              $accComment = '' ;
            }

            if($accComment){
                if($accComment->reviewed_by  != 0){
                    $reviewedBy['emp_name_english'] = GnrEmployee::where('id',$accComment->reviewed_by)->value('name');
                    $reviewedBy['emp_id']  = GnrEmployee::where('id',$accComment->reviewed_by)->value('employeeId');
                    $reviewedBy['name'] = GnrDepartment::where('id',  $accComment->reviewed_by)->where('companyId',$userCompanyId)->value('name');
                    $reviewedBy['status'] = 'Reviewed';
                }else{
                      $reviewedBy = '';
                    }

            }else{
              $accComment = '' ;
            }

            if($accComment){
                if($accComment->approved_by  != 0){
                    $approvedBy['emp_name_english'] = GnrEmployee::where('id',$userEmployeesId)->value('name');
                    $approvedBy['emp_id']  = GnrEmployee::where('id', $accComment->approved_by)->value('employeeId');
                    $approvedBy['name'] = GnrPosition::where('id',  $accComment->approved_by)->where('companyId',$userCompanyId)->value('name');
                    $approvedBy['status'] = 'Approved';
                }else{
                      $approvedBy = '';
                    }

            }else{
              $accComment = '' ;
            }

            if($accComment){
                if($accComment->rejected_by  != 1){
                    $rejectedBy['emp_name_english'] = GnrEmployee::where('id',$accComment->rejected_by)->value('name');
                    $rejectedBy['emp_id']  = GnrEmployee::where('id', $accComment->rejected_by)->value('employeeId');
                    $rejectedBy['name'] = //;
                    //$rejectedBy['comment'] = AccComments::select('comments_details_verify','comments_details_review','comments_details_approve')->first();
                    $rejectedBy['status'] = 'Rejected';
                }else{
                      $rejectedBy = '';
                    }

            }else{
              $accComment = '' ;
            }

          

            //dd($reviewedBy);

            $positionNameUnderVerify =GnrPosition::where('id',$verifyId->designation)->where('companyId',$userCompanyId)->value('name');
            //dd($positionNameUnderVerify);
            $positionNameUnderReview =GnrPosition::where('id',$reviewId->designation)->where('companyId',$userCompanyId)->value('name');
            $positionNameUnderApprove =GnrPosition::where('id',$approveId->designation)->where('companyId',$userCompanyId)->value('name');

            $departmentNameUnderVerify =GnrDepartment::where('id',$verifyId->department)->where('companyId',$userCompanyId)->value('name');
            $departmentNameUnderReview =GnrDepartment::where('id',$reviewId->department)->where('companyId',$userCompanyId)->value('name');
            $departmentNameUnderApprove =GnrDepartment::where('id',$approveId->department)->where('companyId',$userCompanyId)->value('name');
            //dd($positionNameUnderApprove, $departmentNameUnderApprove);

            //dd($branch);
            if($branchInfo->branchCode == 0){
                $verifyEmployee  = $employees->where('department_id_fk',$verifyId->department)->where('position_id_fk',$verifyId->designation)->where('branchId',$voucherIdInfo->branchId)->first();
               
                $reviewEmployee  = $employees->where('department_id_fk',$reviewId->department)->where('position_id_fk',$reviewId->designation)->where('branchId',$voucherIdInfo->branchId)->first();
                $approveEmployee = $employees->where('department_id_fk',$approveId->department)->where('position_id_fk',$approveId->designation)->where('branchId',$voucherIdInfo->branchId)->first();
                //dd($approveId->department,$approveId->designation);
                

            }else{
                $verifyEmployee = $employees->where('position_id_fk',$verifyId->designation)->where('branchId',$voucherIdInfo->branchId)->first();
                $reviewEmployee = $employees->where('position_id_fk',$reviewId->designation)->where('branchId',$voucherIdInfo->branchId)->first();
                $approveEmployee = $employees->where('position_id_fk',$approveId->designation)->where('branchId',$voucherIdInfo->branchId)->first();
                
            }

                if($verifyId !=null){
                    $verifyEmployee  = $employees->where('department_id_fk',$verifyId->department)->where('position_id_fk',$verifyId->designation)->where('branchId',$voucherIdInfo->branchId)->first();
                }elseif($reviewId !=null){
                     $reviewEmployee  = $employees->where('department_id_fk',$reviewId->department)->where('position_id_fk',$reviewId->designation)->where('branchId',$voucherIdInfo->branchId)->first();
                }elseif($reviewId !=null){
                    $approveEmployee = $employees->where('department_id_fk',$approveId->department)->where('position_id_fk',$approveId->designation)->where('branchId',$voucherIdInfo->branchId)->first();
                }else{
                    $verifyId = 0;
                    $reviewedBy = 0;
                    $approvedBy = 0; 
                }

                if($verifyId !=null && $reviewId !=null){
                  $verifyEmployee  = $employees->where('department_id_fk',$verifyId->department)->where('position_id_fk',$verifyId->designation)->where('branchId',$voucherIdInfo->branchId)->first();
                  $reviewEmployee  = $employees->where('department_id_fk',$reviewId->department)->where('position_id_fk',$reviewId->designation)->where('branchId',$voucherIdInfo->branchId)->first();
                }else{
                    $verifyId = 0;
                    $reviewedBy = 0;
                    $approvedBy = 0;
                }

                if($verifyId !=null && $reviewId !=null && $approveId !=null){
                  $verifyEmployee  = $employees->where('department_id_fk',$verifyId->department)->where('position_id_fk',$verifyId->designation)->where('branchId',$voucherIdInfo->branchId)->first();
                  $reviewEmployee  = $employees->where('department_id_fk',$reviewId->department)->where('position_id_fk',$reviewId->designation)->where('branchId',$voucherIdInfo->branchId)->first();
                  $approveEmployee = $employees->where('department_id_fk',$approveId->department)->where('position_id_fk',$approveId->designation)->where('branchId',$voucherIdInfo->branchId)->first();
                }else{
                    $verifyId = 0;
                    $reviewedBy = 0;
                    $approvedBy = 0;
                }

               //dd($verifyEmployee,$reviewEmployee,$approveEmployee);

            //dd($verifyEmployee,$reviewEmployee,$approveEmployee);
            //dd($verifyEmployee,$reviewEmployee,$approveEmployee);
            // if ($verifyEmployee == null || $reviewEmployee == null || $approveEmployee == null) {
            //     $settingsExist = 0;
            //     $verifyEmployee = null;
            //     $reviewEmployee = null;
            //     $approveEmployee = null;
            //     $positionNameUnderVerify = '';
            //     $positionNameUnderReview = '';
            //     $positionNameUnderApprove = '';
            //     $departmentNameUnderVerify = '';
            //     $departmentNameUnderReview = '';
            //     $departmentNameUnderApprove = '';
            //     $verifiedBy = '';
            //     $reviewedBy = '';
            //     $approvedBy = '';
            //     $rejectedBy = '';
            //     $accComment = '';
            // }
            
        }

        else {

            $settingsExist = 0;
            $verifyEmployee = null;
            $reviewEmployee = null;
            $approveEmployee = null;
            $positionNameUnderVerify = '';
            $positionNameUnderReview = '';
            $positionNameUnderApprove = '';
            $departmentNameUnderVerify = '';
            $departmentNameUnderReview = '';
            $departmentNameUnderApprove = '';
            $verifiedBy = '';
            $reviewedBy = '';
            $approvedBy = '';
            $rejectedBy = '';
            $accComment = '';

        }
        if($branchInfo){
           $userBranchCode = $branchInfo->branchCode; 
       }else{
            $userBranchCode = '';
       }
       $v_approval_step = GnrCompany::where('id',Auth::user()->company_id_fk)->value('voucher_type_step');
       // dd($verifyEmployee,$reviewEmployee,$approveEmployee);

        //dd($prepBy);

        return view('accounting.vouchers.printVoucher',[
            'voucherIdInfo'=>$voucherIdInfo,
            'verifyEmployee'=>$verifyEmployee,
            'reviewEmployee'=>$reviewEmployee,
            'approveEmployee'=>$approveEmployee,
            'positionNameUnderVerify'=>$positionNameUnderVerify,
            'positionNameUnderReview'=>$positionNameUnderReview,
            'positionNameUnderApprove'=>$positionNameUnderApprove,
            'departmentNameUnderVerify'=>$departmentNameUnderVerify,
            'departmentNameUnderReview'=>$departmentNameUnderReview,
            'departmentNameUnderApprove'=>$departmentNameUnderApprove,
            'branchName'=>$branchName,
            'prepBy'=>$prepBy,
            'verifiedBy'=>$verifiedBy,
            'reviewedBy'=>$reviewedBy,
            'approvedBy'=>$approvedBy,
            'rejectedBy'=>$rejectedBy,
            'accComment'=>$accComment,
            'userBranchCode'=>$userBranchCode,
            'v_approval_step'=>$v_approval_step,
            //'preparedBy'=>$preparedBy,
            'settingsExist'=>$settingsExist,

            // 'employeeIdUnderVerify'=>$employeeIdUnderVerify,
            // 'employeeNameUnderVerify'=>$employeeNameUnderVerify

        ]);

    }

    public function index(Request $request) {
        $userCompanyId = Auth::user()->company_id_fk;
        $user_branch_id = Auth::user()->branchId;
        $branch = GnrBranch::where('id',$user_branch_id)->first();
        $user_project_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->where('companyId',$userCompanyId)->value('projectId');
        //dd($user_project_id);
        $user_project_type_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->where('companyId',$userCompanyId)->value('projectTypeId');
        //dd($user_branch_id,  $user_project_id,   $user_project_type_id);
        if($request->checkFirstLoad==null){
            $checkFirstLoad=0;
        }else{
            $checkFirstLoad=(int)$request->checkFirstLoad;
        }
        // echo "checkFirstLoad $checkFirstLoad"; exit();
        $searchedProjectId=$request->projectId;
        $searchedProjectTypeId=$request->projectTypeId;
        $searchedBranchId=$request->branchId;
        $searchedVoucherTypeId=$request->voucherTypeId;
        $searchedDateFrom=$request->dateFrom;
        $searchedDateTo=$request->dateTo;

        $projectIdArray = array();
        $projectTypeIdArray = array();
        $branchIdArray = array();
        $voucherTypeIdArray = array();


         //Project
        if ($searchedProjectId==null) {
            // if ($branch->branchCode == 0) {
                $projectSelected = null;
                $projectIdArray = DB::table('gnr_project')->where('companyId',$userCompanyId)->pluck('id')->toArray();
                //dd($projectIdArray);
            // }else{
            //     $projectSelected= (int) json_decode($user_project_id);
            //     array_push($projectIdArray, (int) json_decode($user_project_id));
            // }
        }else{
            $projectSelected= (int) json_decode($searchedProjectId);
            array_push($projectIdArray, (int) json_decode($searchedProjectId));
        }

        //Project Type
        if ($searchedProjectTypeId==null) {
            // if ($branch->branchCode == 0) {
                $projectTypeSelected = null;
                $projectTypeIdArray = DB::table('gnr_project_type')->where('companyId',$userCompanyId)->pluck('id')->toArray();
            // }else{
            //     $projectTypeSelected=(int) json_decode($user_project_type_id);
            //     array_push($projectTypeIdArray, (int) json_decode($user_project_type_id));
            //     // dd($projectTypeIdArray);
            // }
        }else{
            $projectTypeSelected=(int) json_decode($searchedProjectTypeId);
            array_push($projectTypeIdArray, (int) json_decode($searchedProjectTypeId));
        }

        //Branch
        if ($searchedBranchId==null) {
            // if ($user_branch_id == 1) {
            //     if ($checkFirstLoad ==0) {
            //         $branchSelected = 1;
            //         array_push($branchIdArray, 1);
            //     }else{
                    $branchSelected = null;
                    $branchIdArray = DB::table('gnr_branch')->where('companyId', $userCompanyId)->pluck('id')->toArray();
            //     }
            // }else{
            //     $branchSelected=(int) json_decode($user_branch_id);
            //     array_push($branchIdArray, (int) json_decode($user_branch_id));
            // }
        }
        // elseif ($searchedBranchId==0) {
        //     $branchSelected = 0;
        //     $branchIdArray = DB::table('gnr_branch')->whereIn('projectId', $projectIdArray)->where('id', '!=', 1)->pluck('id')->toArray();
        // }
        else{
            $branchSelected=(int) json_decode($searchedBranchId);
            array_push($branchIdArray, (int) json_decode($searchedBranchId));
        }

        //dd($branchIdArray);
        //voucherTypeId
        if ($searchedVoucherTypeId==null) {
            $voucherTypeIdArray = DB::table('acc_voucher_type')->pluck('id')->toArray();
        }else{
            array_push($voucherTypeIdArray, (int) json_decode($searchedVoucherTypeId));
        }

        if($searchedDateFrom==null && $searchedDateTo==null){
            $toDate = date("Y-m-d");
            $startDate=DB::table('gnr_fiscal_year')->where('companyId', $userCompanyId)->where('fyStartDate','<=',$toDate)->where('fyEndDate','>=',$toDate)->value('fyStartDate');
            // $startDate = date('Y-m-d', strtotime("-1 week +1 day".$toDate));
            $endDate = $toDate;
            $startDateSelected=date('d-m-Y',strtotime($startDate));
            $endDateSelected=date('d-m-Y',strtotime($endDate));
        }else{
            $startDate = date('Y-m-d',strtotime($searchedDateFrom));
            $endDate = date('Y-m-d',strtotime($searchedDateTo));
            $startDateSelected=$searchedDateFrom;
            $endDateSelected=$searchedDateTo;
        }
        // echo $startDate." ".$endDate;
        // dd($startDate, $endDate);

        $projects = GnrProject::select('id','name','projectCode')->where('companyId',$userCompanyId)->get();

        $branches = GnrBranch::select('id','name','branchCode')->where('companyId',$userCompanyId)->whereIn('projectId',$projectIdArray)->orWhere('id',1)->get();
        
        $projectTypes = GnrProjectType::select('id','name','projectTypeCode')->where('companyId',$userCompanyId)->whereIn('projectId',$projectIdArray)->get();
        $voucherTypes=DB::table('acc_voucher_type')->select('id','shortName')->get();
        //dd($voucherTypes);
       
       //dd($voucherTypeIdArray);   
        if($branch->branchCode == 0){
            $vouchers = DB::table('acc_voucher')
            ->whereIn('projectId', $projectIdArray)
            ->whereIn('projectTypeId', $projectTypeIdArray)
            ->whereIn('branchId', $branchIdArray)
            ->whereIn('voucherTypeId', $voucherTypeIdArray)
            ->where('companyId',$userCompanyId)
            ->where('status', 1)
            ->where(function ($query) use ($startDate,$endDate){
                $query->where('voucherDate','>=',$startDate)
                ->where('voucherDate','<=',$endDate);
            })
            ->orderBy('voucherDate', 'desc')
            ->paginate(100);
            //dd($vouchers);
        }else{
            $vouchers = DB::table('acc_voucher')
            ->whereIn('projectId', $projectIdArray)
            ->whereIn('branchId', $branchIdArray)
            ->whereIn('voucherTypeId', $voucherTypeIdArray)
            ->where('companyId',$userCompanyId)
            ->where('status', 1)
            ->where(function ($query) use ($startDate,$endDate){
                $query->where('voucherDate','>=',$startDate)
                ->where('voucherDate','<=',$endDate);
            })
            ->orderBy('voucherDate', 'desc')
            ->paginate(100);
        }
        //dd($vouchers);
        $userBranchId = Auth::user()->branchId;
        $userBranchData = DB::table('gnr_branch')
                        ->where('id', $userBranchId)
                        ->select('id', DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"))
                        ->first();
                        //dd($userBranchData);
        return view('accounting.vouchers.viewVouchers',[
            'checkFirstLoad' => $checkFirstLoad,
            'projectSelected' => $projectSelected,
            'projectTypeSelected' => $projectTypeSelected,
            'branchSelected' => $branchSelected,
            'startDateSelected' => $startDateSelected,
            'endDateSelected' => $endDateSelected,
            'searchedVoucherTypeId' => $searchedVoucherTypeId,
            'user_branch_id' => $user_branch_id,
            'user_project_id' => $user_project_id,
            'user_project_type_id' => $user_project_type_id,
            'projects' => $projects,
            'branches' => $branches, 
            'projectTypes' => $projectTypes,
            'voucherTypes' => $voucherTypes, 
            'vouchers' => $vouchers,
            'userBranchData' => $userBranchData,
         ]);


    }       //End voucherTest function



}   //END AccViewVoucherController
