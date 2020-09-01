@extends('layouts/acc_layout')
{{-- @section('title', '| Print Voucher') --}}
@section('title')
@if ($voucherIdInfo->voucherTypeId==1)
{{" | Debit Voucher"}}
@elseif ($voucherIdInfo->voucherTypeId==2)
{{" | Credit Voucher"}}
@elseif ($voucherIdInfo->voucherTypeId==3)
{{" | Journal Voucher"}}
@elseif ($voucherIdInfo->voucherTypeId==4)
{{" | Contra Voucher"}}
@elseif ($voucherIdInfo->voucherTypeId==5)
{{" | Fund Transferred Voucher"}}
@endif
@endsection
@section('content')
@include('convert_word')
<?php
$user = Auth::user();
Session::put('branchId', $user->branchId);
$branchId = Session::get('branchId');
//dd($userBranchCode);
Session::put('id', $user->id);
$userId = Session::get('id');
//dd($settingsExist);
$userEmployeeId = Auth::user()->emp_id_fk;
//dd(Auth::user()->user_type);
//dd($settingsExist);
if($settingsExist == 1) {
    if($verifyEmployee != null && $reviewEmployee == null && $approveEmployee ==null && $voucherIdInfo->prepBy !=0){
        $verifyEmployeeId = $verifyEmployee->id;
        $reviewEmployeeId = 0;
        $approveEmployeeId = 0;
    }
    elseif($verifyEmployee == null && $reviewEmployee != null && $approveEmployee ==null && $voucherIdInfo->prepBy !=0){
        $verifyEmployeeId = 0;
        $reviewEmployeeId = $reviewEmployee->id;
        $approveEmployeeId = 0;
    }
    elseif($verifyEmployee == null && $reviewEmployee == null && $approveEmployee !=null && $voucherIdInfo->prepBy !=0){
        $verifyEmployeeId = 0;
        $reviewEmployeeId = 0;
        $approveEmployeeId = $approveEmployee->id;
    }elseif ($verifyEmployee != null && $reviewEmployee == null && $approveEmployee !=null && $voucherIdInfo->prepBy !=0) {
        $verifyEmployeeId = $verifyEmployee->id;
        $approveEmployeeId = $approveEmployee->id;
        $reviewEmployeeId =0;
    }elseif ($verifyEmployee != null && $reviewEmployee != null && $approveEmployee !=null && $voucherIdInfo->prepBy !=0) {
        $verifyEmployeeId = $verifyEmployee->id;
        $reviewEmployeeId = $reviewEmployee->id;
        $approveEmployeeId = $approveEmployee->id;
    }
    else{
        $verifyEmployeeId = 0;
        $reviewEmployeeId = 0;
        $approveEmployeeId = 0;
    }
    
}else{
    $verifyEmployeeId = 0;
    $reviewEmployeeId =0;
    $approveEmployeeId = 0;
}

//dd($verifyEmployeeId, $reviewEmployeeId, $approveEmployeeId);
//if(Auth::user()->emp_id_fk)
$userEmployeeId = Auth::user()->emp_id_fk;
$userType = Auth::user()->user_type;
//dd($voucherIdInfo->authBy);
//dd(Auth::user());
//dd($userType);
//dd($userEmployeeId);
//$branchId = $user->branchId;
Session::put('branchId', $user->branchId);
$branchId = Session::get('branchId');
Session::put('id', $user->id);
$userId = Session::get('id');
$branchArr = DB::table('gnr_branch')->pluck('name','id')->toArray();
$ledgerInfo =DB::table('acc_account_ledger')->select('id','code','name')->get();
$approval_step =DB::table('gnr_company')->where('id',Auth::user()->company_id_fk)->value('voucher_type_step');
//dd($settingsExist);
//dd($branchArr);
//dd($verifyEmployeeId,$userEmployeeId);
//$verifiedBy = $verifiedBy['voucherId'];
// $branch = DB::table('gnr_branch')->where('id',$branchId)->select('name','companyId','branchCode')->first();
$branchArr = DB::table('gnr_branch')->pluck('name','id')->toArray();
$ledgerInfo =DB::table('acc_account_ledger')->where('companyIdFk',Auth::user()->company_id_fk)->select('id','code','name')->get();
//dd($ledgerInfo);
?>
<style type="text/css">
.positionLabel{
font-size: 15px;
font-weight: bold;
color:#666161;
}
.positionClass{
font-size: 15px;
font-weight: bold;
color:#666161;
padding-left: 15px;
}
.positionClass p{
font-size: 10px;
color: #ccc;
margin-top: 10px;
}
.modal-center{
top:10%;
transform: translateX(-10%);
}
.modal-image{
width: 100%;
height: 400px;
}
.position{
color: #666161;
font-size: 11px;
}
.img_modal{
cursor: pointer;
}
.img_modal:hover{
border: 1px solid #666161;
opacity: 0.6;
transition: width 2s;
}
.commentStyle{
font-size: 12px !important;
font-weight: 100 !important;
}
.status{
font-weight: bold;
}
</style>
<div class="row add-data-form">
    <div class="col-md-12">
        <div class="col-md-1"></div>
        <div class="col-md-10 fullbody">
            <div class="viewTitle" style="border-bottom: 1px solid white;">
                <a href="{{url('/viewVoucher')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                </i>Voucher List</a>
            </div>
            <div class="panel panel-default panel-border">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div style="float: right; padding-right: 30px; font-size:18px; color: #64363F">
                                    <button id="printList" style="background-color:transparent; float:left; border: 3px solid #a1a1a1; border-radius: 25px; padding:0px 10px 0px 10px">
                                    <i class="fa fa-print fa-lg" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                            {{-- <div class="" style="border-bottom: 1px solid white;">
                                <a href="{{url('/viewVoucher')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                                </i>Voucher List</a>
                            </div> --}}
                            <div id="printView">
                                <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; "> {{-- div for Company --}}
                                    <?php
                                    $company = DB::table('gnr_company')->where('id',$voucherIdInfo->companyId)->select('name','address')->first();
                                    $voucherTitleName = DB::table('acc_voucher_type')->where('id',$voucherIdInfo->voucherTypeId)->value('titleName');
                                    ?>
                                    <span style="font-size:14px;">{{ $company->name}}</span><br/>
                                    <span style="font-size:12px;">{{ $company->address}}</span><br/>
                                    <span style="text-decoration: underline;  font-size:14px;">{{$voucherTitleName}}</span>
                                </div>
                                <div class="row" style="padding: 0px 15px;">       {{-- div for Voucher --}}
                                    <?php
                                    $projectName = DB::table('gnr_project')->where('id',$voucherIdInfo->projectId)->value('name');
                                    $projectTypeName = DB::table('gnr_project_type')->where('id',$voucherIdInfo->projectTypeId)->value('name');
                                    ?>
                                    <table id="voucherInfoTable">
                                        <tbody>
                                            <tr>
                                                <td style="font-weight: bold; width: 7%;"> Project Name</td>
                                                <td style="width: 1%;">:</td>
                                                <td style="width: 36%;">{{$projectName}}</td>
                                                <td style="font-weight: bold; width: 7%;"> Voucher Code</td>
                                                <td style="width: 1%;">:</td>
                                                <td style="width: 8%;">{{$voucherIdInfo->voucherCode}}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold;"> Project Type</td>
                                                <td>:</td>
                                                <td>{{$projectTypeName}}</td>
                                                <td style="font-weight: bold;"> Voucher Date</td>
                                                <td>:</td>
                                                <td>{{date('d-m-Y',strtotime($voucherIdInfo->voucherDate))}}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold;"> Branch Name</td>
                                                <td>:</td>
                                                <td>{{$branchArr[$voucherIdInfo->branchId]}}</td>
                                                <td style="font-weight: bold;"> Print Date</td>
                                                <td>:</td>
                                                <td>{{\Carbon\Carbon::now()->format('d-m-Y g:i A')}}</td>
                                            </tr>
                                        </tbody>  
                                    </table>
                                </div>
                                <div class="row" style="padding: 20px 14px 5px 14px; ">  {{-- div for Voucher Details Table --}}
                                    <table id="voucherView" border="1pt solid ash" style="border-collapse: collapse;">
                                        <thead>
                                            <tr>
                                                <th style="">Ledger Head</th>
                                                <th style=" width: 7%;">Code</th>
                                                <th style=" width: 15%;">Debit Amount</th>
                                                <th style=" width: 16%;">Credit Amount</th>
                                                <th style=" width: 20%;">Line Note</th>
                                                @if($voucherIdInfo->voucherTypeId==5)
                                                <?php
                                                $transferredFromOrTo = DB::table('acc_voucher_details')->where('voucherId',$voucherIdInfo->id)->select('ftFrom','ftTo')->first();
                                                ?>
                                                <th class="name" style="width:15%;">Transferred
                                                    @if($voucherIdInfo->branchId==$transferredFromOrTo->ftFrom)
                                                    To
                                                    @elseif($voucherIdInfo->branchId==$transferredFromOrTo->ftTo)
                                                    From
                                                    @endif
                                                </th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // $voucherIdInfoDetails = DB::table('acc_voucher_details')->where('voucherId',$voucherIdInfo->id)->get();
                                            $voucherIdInfoTotalAmount = DB::table('acc_voucher_details')->where('voucherId',$voucherIdInfo->id)->sum('amount');
                                            if ($voucherIdInfo->voucherTypeId==5) {
                                            $creditHeadDetails = DB::table('acc_voucher_details')->select('voucherId', 'creditAcc', 'localNarration', 'amount', 'ftFrom', 'ftTo')->where('voucherId',$voucherIdInfo->id)->orderBy('id','desc')->get();
                                            $debitHeadDetails = DB::table('acc_voucher_details')->select('voucherId', 'debitAcc', 'localNarration', 'amount', 'ftFrom', 'ftTo')->where('voucherId',$voucherIdInfo->id)->orderBy('id','desc')->get();
                                            }else{
                                            $creditHeadDetails = DB::table('acc_voucher_details')->select('voucherId', 'creditAcc', 'localNarration', 'ftFrom', 'ftTo')->where('voucherId',$voucherIdInfo->id)->groupBy('creditAcc')->orderBy('id','desc')->get();
                                            $debitHeadDetails = DB::table('acc_voucher_details')->select('voucherId', 'debitAcc', 'localNarration', 'ftFrom', 'ftTo')->where('voucherId',$voucherIdInfo->id)->groupBy('debitAcc')->orderBy('id','desc')->get();
                                            }
                                            ?>
                                            @foreach ($debitHeadDetails as $debitHeadDetail)
                                            <?php
                                            $debitAcc=$ledgerInfo->where('id', $debitHeadDetail->debitAcc)->first();
                                            //dd($ledgerInfo);
                                            if ($voucherIdInfo->voucherTypeId!=5){
                                            $debitAmount=DB::table('acc_voucher_details')->where('voucherId',$debitHeadDetail->voucherId)->where('debitAcc', $debitHeadDetail->debitAcc)->sum('amount');
                                            }
                                            ?>
                                            {{-- Debit Row --}}
                                            <tr>
                                                <td class="name">{{$debitAcc->name}}</td>
                                                <td>{{$debitAcc->code}}</td>
                                                @if ($voucherIdInfo->voucherTypeId==5)
                                                <td class="amount">{{number_format($debitHeadDetail->amount, 2, '.', ',')}}</td>
                                                @else
                                                <td class="amount">{{number_format($debitAmount, 2, '.', ',')}}</td>
                                                @endif
                                                <td class="amount">-</td>
                                                <td class="name">{{$debitHeadDetail->localNarration}}</td>
                                                @if($voucherIdInfo->voucherTypeId==5)
                                                @if($voucherIdInfo->branchId==$transferredFromOrTo->ftFrom)
                                                <td class="name">{{$branchArr[$debitHeadDetail->ftTo]}}</td>
                                                @elseif($voucherIdInfo->branchId==$transferredFromOrTo->ftTo)
                                                <td class="name">{{$branchArr[$debitHeadDetail->ftFrom]}}</td>
                                                @elseif($voucherIdInfo->branchId!=$transferredFromOrTo->ftFrom && $voucherIdInfo->branchId!=$transferredFromOrTo->ftTo)
                                                <td class="name">{{$branchArr[$debitHeadDetail->ftFrom]}}</td>
                                                @endif
                                                @endif
                                                {{-- @if($voucherIdInfo->voucherTypeId==5)
                                                @if($voucherIdInfo->branchId!=$transferredFromOrTo->ftFrom && $voucherIdInfo->branchId!=$transferredFromOrTo->ftFrom)
                                                <td class="name">{{$branchArr[$debitHeadDetail->ftFrom]}}</td>
                                                @endif
                                                @endif --}}
                                            </tr>
                                            @endforeach
                                            @foreach ($creditHeadDetails as $creditHeadDetail)
                                            <?php
                                            $creditAcc=$ledgerInfo->where('id', $creditHeadDetail->creditAcc)->first();
                                            if ($voucherIdInfo->voucherTypeId!=5){
                                            $creditAmount=DB::table('acc_voucher_details')->where('voucherId',$creditHeadDetail->voucherId)->where('creditAcc', $creditHeadDetail->creditAcc)->sum('amount');
                                            }
                                            ?>
                                            {{-- Credit Row --}}
                                            <tr>
                                                <td class="name">{{$creditAcc->name}}</td>
                                                <td>{{$creditAcc->code}}</td>
                                                <td class="amount">-</td>
                                                @if ($voucherIdInfo->voucherTypeId==5)
                                                <td class="amount">{{number_format($creditHeadDetail->amount, 2, '.', ',')}}</td>
                                                @else
                                                <td class="amount">{{number_format($creditAmount, 2, '.', ',')}}</td>
                                                @endif
                                                <td class="name">{{$creditHeadDetail->localNarration}}</td>
                                                @if($voucherIdInfo->voucherTypeId==5)
                                                @if($voucherIdInfo->branchId==$transferredFromOrTo->ftFrom)
                                                <td class="name">{{$branchArr[$creditHeadDetail->ftTo]}}</td>
                                                @elseif($voucherIdInfo->branchId==$transferredFromOrTo->ftTo)
                                                <td class="name">{{$branchArr[$creditHeadDetail->ftFrom]}}</td>
                                                @elseif($voucherIdInfo->branchId!=$transferredFromOrTo->ftFrom && $voucherIdInfo->branchId!=$transferredFromOrTo->ftTo)
                                                <td class="name">{{$branchArr[$creditHeadDetail->ftTo]}}</td>
                                                @endif
                                                @endif
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="2">
                                                    <span style="color: black; font-weight: bold;" >Total: </span>
                                                </td>
                                                <td class="amount" style="font-weight: bold;">{{number_format($voucherIdInfoTotalAmount, 2, '.', ',')}}</td>
                                                <td class="amount" style="font-weight: bold;">{{number_format($voucherIdInfoTotalAmount, 2, '.', ',')}}</td>
                                                <td></td>
                                                @if($voucherIdInfo->voucherTypeId==5) <td></td> @endif
                                            </tr>
                                            <tr>
                                                <td colspan="<?php if($voucherIdInfo->voucherTypeId==5){echo "6";}else{echo "5";} ?>"  class="name">
                                                    <span style="color: black; font-weight: bold;">In Words (Tk): </span>
                                                    <span style="color: black; "><?php echo convert_number_to_words($voucherIdInfoTotalAmount); ?></span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row" style="padding: 10px 14px 10px 14px; ">  {{-- div for Global Narration --}}
                                    <table style="width:100%;" class="table table-striped table-bordered" id="globalNarrationTable">
                                        <tbody>
                                            <tr>
                                                <td class="name" style="padding:5px;">
                                                    <span style="font-weight: bold; text-decoration: underline;">Narration/Cheque Details:</span>
                                                    <span>{{$voucherIdInfo->globalNarration}}</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                @if($approval_step == 0)
                                @elseif ($settingsExist == 1 && $voucherIdInfo->prepBy !=0)
                                    <div class="row userInfoMainDiv" style="padding: 30px 15px 5px 15px; "> {{-- div for User Informations --}}
                                        <table class="" id="" style="color:black;font-size: 12px; font-weight: bold; width:100%">
                                            <tr>
                                               @if($userBranchCode == 0)
                                                    <td style="text-align:left; background:white; width: 25%" >
                                                        Prepared By</br>
                                                        <span class="position">{{$prepBy['emp_name_english']}} - {{$prepBy['emp_id']}}</span> </br>
                                                        <span class="position">Department : {{$prepBy['dep_name']}}
                                                            </br>
                                                        Position: {{$prepBy['name']}}</span>
                                                    </td>
                                                @elseif($userBranchCode != 0)
                                                    <td style="text-align:left; background:white; width: 25%" >
                                                        Prepared By</br>
                                                        <span class="position">{{$prepBy['emp_name_english']}} - {{$prepBy['emp_id']}}</span> </br>
                                                        <span class="position">Position : {{$prepBy['name']}}
                                                            </br>
                                                        Branch: {{$prepBy['name']}}</span>
                                                    </td>
                                                @endif
                                                @if($verifyEmployee !=null && $reviewEmployee ==null && $approveEmployee ==null)
                                                    <td style="text-align:left; background:white; width: 25%">Approved By </br>
                                                        @if($accComment)
                                                           @if($accComment->verified_by != null)
                                                               @if($userBranchCode == 0)
                                                                    <span class="position">{{$verifyEmployee->name}} - {{$verifyEmployee->employeeId}}</span></br>
                                                                    <span class="position">Department : {{$departmentNameUnderVerify}} </span></br>
                                                                    <span class="position">  Position: {{$positionNameUnderVerify}}</span>
                                                                @elseif($userBranchCode != 0)
                                                                    <span class="position">{{$verifiedBy['emp_name_english']}} - {{$verifiedBy['emp_id']}}</span> </br>
                                                                    <span class="position">Position : {{$positionNameUnderVerify}}</br>
                                                                     Branch : {{$branchName}}</span>
                                                                  @endif
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td colspan="2"></td>
                                                @elseif($verifyEmployee ==null && $reviewEmployee !=null && $approveEmployee ==null)
                                                    <td style="text-align:left; background:white; width: 25%">Approved By </br>
                                                        @if($accComment)
                                                            @if($accComment->reviewed_by != null)
                                                                @if($userBranchCode == 0)
                                                                    <span class="position">{{$reviewEmployee->name}} - {{$reviewEmployee->employeeId}}</span> </br>
                                                                    <span class="position">Department :  {{$departmentNameUnderReview}}
                                                                    </br> Position : {{$positionNameUnderReview}}</span>
                                                                @elseif($userBranchCode != 0)
                                                                    <span class="position">{{$reviewEmployee->name}} - {{$reviewEmployee->employeeId}}</span> </br>
                                                                    <span class="position">Position : {{$positionNameUnderReview}}, Branch : {{$branchName}}</span>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </td>
                                                <td colspan="2"></td>
                                                @elseif($verifyEmployee ==null && $reviewEmployee ==null && $approveEmployee !=null)
                                                    <td style="text-align:left; background:white;" >Approved By </br>
                                                        @if($accComment)
                                                            @if($accComment->approved_by != null)
                                                                @if($userBranchCode == 0)
                                                                    <span class="position">{{$approveEmployee->name}} - {{$approveEmployee->employeeId}}</span> </br>
                                                                    <span class="position">Department :  {{$departmentNameUnderApprove}}</br>  Position: {{$positionNameUnderApprove}}</span>
                                                                @elseif($userBranchCode != 0)
                                                                    <span class="position">{{$approvedBy['emp_name_english']}} - {{$approvedBy['emp_id']}}</span> </br>
                                                                    <span class="position">Position : {{$positionNameUnderApprove}} </br> Branch : {{$branchName}}</span>
                                                                @endif
                                                            @endif
                                                        @endif
                                                     </td>
                                                    <td colspan="2"></td>
                                                @elseif($verifyEmployee !=null && $reviewEmployee ==null  && $approveEmployee !=null)
                                                    <td style="text-align:left; background:white; width: 25%">Verified By </br>
                                                        @if($accComment)
                                                            @if($accComment->verified_by != null)
                                                                @if($userBranchCode == 0)
                                                                    <span class="position">{{$verifyEmployee->name}} - {{$verifyEmployee->employeeId}}</span></br>
                                                                    <span class="position">Department : {{$departmentNameUnderVerify}} </span></br>
                                                                    <span class="position">  Position: {{$positionNameUnderVerify}}</span>
                                                                @elseif($userBranchCode != 0)
                                                                    <span class="position">{{$verifiedBy['emp_name_english']}} - {{$verifiedBy['emp_id']}}</span> </br>
                                                                    <span class="position">Position : {{$positionNameUnderVerify}}</br>
                                                                    Branch : {{$branchName}}</span>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </td>
                                                    
                                                   <td style="text-align:left; background:white; width: 25%">Approved By </br>
                                                        @if($accComment)
                                                            @if($accComment->approved_by != null)
                                                                @if($userBranchCode == 0)
                                                                    <span class="position">{{$approveEmployee->name}} - {{$approveEmployee->employeeId}}</span> </br>
                                                                    <span class="position">Department :  {{$departmentNameUnderApprove}}
                                                                    </br> Position : {{$positionNameUnderApprove}}</span>
                                                                @elseif($userBranchCode != 0)
                                                                    <span class="position">{{$approveEmployee->name}} - {{$approveEmployee->employeeId}}</span> </br>
                                                                    <span class="position">Position : {{$positionNameUnderApprove}} </br>
                                                                     Branch : {{$branchName}}</span>
                                                                @endif
                                                             @endif
                                                        @endif
                                                    </td>
                                                @elseif($verifyEmployee !=null && $reviewEmployee !=null  && $approveEmployee !=null)
                                                    <td style="text-align:left; background:white; width: 25%">Verified By </br>
                                                        @if($accComment)
                                                            @if($accComment->verified_by != null)
                                                                @if($userBranchCode == 0)
                                                                    <span class="position">{{$verifyEmployee->name}} - {{$verifyEmployee->employeeId}}</span></br>
                                                                    <span class="position">Department : {{$departmentNameUnderVerify}} </span></br>
                                                                    <span class="position">  Position: {{$positionNameUnderVerify}}</span>
                                                                @elseif($userBranchCode != 0)
                                                                    <span class="position">{{$verifiedBy['emp_name_english']}} - {{$verifiedBy['emp_id']}}</span> </br>
                                                                    <span class="position">Position : {{$positionNameUnderVerify}}</br>
                                                                    Branch : {{$branchName}}</span>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </td>
                                                   <td style="text-align:left; background:white; width: 25%">Reviewed By </br>
                                                        @if($accComment)
                                                            @if($accComment->reviewed_by != null)
                                                                @if($userBranchCode == 0)
                                                                    <span class="position">{{$reviewEmployee->name}} - {{$reviewEmployee->employeeId}}</span> </br>
                                                                    <span class="position">Department :  {{$departmentNameUnderReview}}
                                                                    </br> Position : {{$positionNameUnderReview}}</span>
                                                                @elseif($userBranchCode != 0)
                                                                    <span class="position">{{$reviewEmployee->name}} - {{$reviewEmployee->employeeId}}</span> </br>
                                                                    <span class="position">Position : {{$positionNameUnderReview}}</br> Branch : {{$branchName}}</span>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </td>
                                                     <td style="text-align:left; background:white;" >Approved By </br>
                                                        @if($accComment)
                                                            @if($accComment->approved_by != null)
                                                                @if($userBranchCode == 0)
                                                                    <span class="position">{{$approveEmployee->name}} - {{$approveEmployee->employeeId}}</span> </br>
                                                                    <span class="position">Department :  {{$departmentNameUnderApprove}}</br>  Position: {{$positionNameUnderApprove}}</span>
                                                                @elseif($userBranchCode != 0)
                                                                    <span class="position">{{$approvedBy['emp_name_english']}} - {{$approvedBy['emp_id']}}</span> </br>
                                                                    <span class="position">Position : {{$positionNameUnderApprove}} </br> Branch : {{$branchName}}</span>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </td>
                                                @endif  
                                            </tr>
                                        </table>
                                    </div>
                                @endif<!--end settings exists-->
                                <div id="divFooter">
                                    <span style="font-size: 12px;">&copy; Ambala IT</span>
                                </div>
                            </div> {{-- PrintDiv --}}
                            <div class="row" class="imageInfoDiv" style="padding: 20px 14px 5px 14px; ">{{-- div for Voucher  Images --}}
                                <h4 style="text-align:center; text-decoration: underline; font-size: 15px;font-weight: bold; margin-bottom: 30px;">{{$voucherTitleName}} Images</h4>
                                    @if($voucherIdInfo->image)
                                        @foreach(json_decode($voucherIdInfo->image, true) as $images)
                                            <div class="col-sm-2 imageResize img_show view overlay">
                                                <img src="{{ asset('/images/vouchers/'.$images) }}" data-toggle="modal" name="{{$images}}"    class="img-responsive img_modal" data-target="#myModal">
                                            </div>
                                        @endforeach
                                    @else
                                    <h4>No Image Available</h4>
                                    @endif
                            </div>{{--end imageDiv--}}
                            <!-- -------------------------------------------------start condition for superadmin----------------------------------------------------------->
                            @if ($settingsExist == 1)
                                @if($voucherIdInfo->prepBy  == $userEmployeeId)
                                    @if($accComment)
                                        @if($accComment->status == 'Rejected')
                                            @if($accComment->comments_details_verify != '')
                                                <div class="row" class="firstStepVerify" style="padding: 20px 14px 5px 14px; ">
                                                    {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                    <div class="row">
                                                        {!! Form::label('name', 'First Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <span class="position">{{$rejectedBy['emp_name_english']}} - {{ $rejectedBy['emp_id']}}</span></br>
                                                                    <span class="position">Position : {{$positionNameUnderVerify}}, Department : {{$departmentNameUnderVerify}}</span></br>
                                                                    <span class="position status">Status : {{$rejectedBy['status']}}</span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group positionClass">
                                                                <p>Comment Box</p>
                                                                <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" id="verifyCommentsDetails" class="commentsDetails">{{$accComment->comments_details_verify}}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {!! Form::close()  !!}
                                                </div>
                                            @elseif($accComment->comments_details_review != '')
                                                <div class="row" class="firstStepVerify" style="padding: 20px 14px 5px 14px; ">
                                                    {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                    <div class="row">
                                                        {!! Form::label('name', 'Second Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <span class="position">{{$rejectedBy['emp_name_english']}} - {{ $rejectedBy['emp_id']}}</span></br>
                                                                    <span class="position">Position : {{$positionNameUnderReview}}, Department : {{$departmentNameUnderReview}}</span></br>
                                                                    <span class="position status">Status : {{$rejectedBy['status']}}</span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group positionClass">
                                                                <p>Comment Box</p>
                                                                <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" id="verifyCommentsDetails" class="commentsDetails">{{$accComment->comments_details_review}}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {!! Form::close()  !!}
                                                </div>
                                            @elseif($accComment->comments_details_approve != '')
                                                <div class="row" class="firstStepVerify" style="padding: 20px 14px 5px 14px; ">
                                                    {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                    <div class="row">
                                                        {!! Form::label('name', 'Third Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <span class="position">{{$rejectedBy['emp_name_english']}} - {{ $rejectedBy['emp_id']}}</span></br>
                                                                    <span class="position">Position : {{$positionNameUnderApprove}}, Department : {{$departmentNameUnderApprove}}</span></br>
                                                                    <span class="position status">Status : {{$rejectedBy['status']}}</span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group positionClass">
                                                                <p>Comment Box</p>
                                                                <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" id="verifyCommentsDetails" class="commentsDetails">{{$accComment->comments_details_approve}}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {!! Form::close()  !!}
                                                </div>
                                            @endif
                                        @endif
                                    @endif
                                @endif
                            <!-- -------------------------------------------------start condition for superadmin----------------------------------------------------------->
                            @if($userType == 'master' && $verifyEmployeeId !=0 && $reviewEmployeeId !=0 && $approveEmployeeId !=0)
                                @if($userBranchCode == 0)
                                    <div class="row" class="firstStepVerify" style="padding: 20px 14px 5px 14px; ">
                                        {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                        <div class="row">
                                            {!! Form::label('name', 'First Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <span class="position">{{$verifyEmployee->name}} - {{$verifyEmployee->employeeId}}</span></br>
                                                        <span class="position">Position : {{$positionNameUnderVerify}}, Department : {{$departmentNameUnderVerify}}</span>
                                                    </div>
                                                </div>
                                                <div class="form-group positionClass">
                                                    <p>Comment Box</p>
                                                    <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" id="verifyCommentsDetails" class="commentsDetails"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group">
                                                {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-md-12 " align="center">
                                                    @if($accComment == null)
                                                    {!! Form::submit('Proceed', ['id' => 'submitFirstStep', 'class' => 'btn btn-info']); !!}
                                                    {!! Form::submit('Reject', ['id' => 'submitFirstStep', 'class' => 'btn btn-danger']); !!}
                                                    @else
                                                    <a href="javascript:void(0);" onclick="return updateProcced();" class="btn btn-info closeBtn">Proceed</a>
                                                    <a href="javascript:void(0);" onclick="return rejectApprovalVoucher();" class="btn btn-danger closeBtn">Reject</a>
                                                    @endif
                                                    <span id="success" style="color:green; font-size:16px;" class=""></span>
                                                </div>
                                            </div>
                                        </div>
                                        {!! Form::close()  !!}
                                    </div>
                            <!-- -------------------------------------------------end first step verification for Head Office----------------------------------------------------------->
                                @else
                                    <div class="row" class="firstStepVerify" style="padding: 20px 14px 5px 14px; ">
                                        {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                        <div class="row">
                                            {!! Form::label('name', 'First Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <span class="position">{{$verifyEmployee->name}} - {{$verifyEmployee->employeeId}}</span></br>
                                                        <span class="position">Position : {{$positionNameUnderVerify}}, Branch : {{$branchName}}</span>
                                                    </div>
                                                </div>
                                                <div class="form-group positionClass">
                                                    <p>Comment Box</p>
                                                    <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" class="commentsDetails"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group">
                                                {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-md-12 " align="center">
                                                    @if($accComment == null)
                                                    {!! Form::submit('Proceed', ['id' => 'submitFirstStep', 'class' => 'btn btn-info']); !!}
                                                    @else
                                                    <a href="javascript:void(0);" onclick="return updateProcced();" class="btn btn-info closeBtn">Proceed</a>
                                                    <a href="javascript:void(0);" onclick="return rejectApprovalVoucher();" class="btn btn-danger closeBtn">Reject</a>
                                                    @endif
                                                    <span id="success" style="color:green; font-size:16px;" class=""></span>
                                                </div>
                                            </div>
                                        </div>
                                        {!! Form::close()  !!}
                                    </div>
                                @endif
                            <!-- -------------------------------------------------end first step verification for branch----------------------------------------------------------->
                            @if($userBranchCode == 0)
                                <div class="row" class="secondStepVerify" style="padding: 20px 14px 5px 14px; ">
                                    {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                    <div class="row">
                                        {!! Form::label('name', 'Second Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <span class="position">{{$reviewEmployee->name}} - {{$reviewEmployee->employeeId}}</span></br>
                                                    <span class="position">Position : {{$positionNameUnderReview}}, Department : {{$departmentNameUnderReview}}</span>
                                                </div>
                                            </div>
                                            <div class="form-group positionClass">
                                                <p>Comment Box</p>
                                                <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" class="commentsDetails"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-md-12 " align="center">
                                            {!! Form::submit('Proceed', ['id' => 'secondStepverification', 'class' => 'btn btn-info']); !!}
                                            <a href="{{url('viewGroup/')}}" class="btn btn-danger closeBtn">Reject</a>
                                            <span id="success" style="color:green; font-size:16px;" class=""></span>
                                        </div>
                                    </div>
                                    {!! Form::close()  !!}
                                </div>
                            <!-- -------------------------------------------------end second step verification for Head Office----------------------------------------------------------->
                            @else
                                <div class="row" class="secondStepVerify" style="padding: 20px 14px 5px 14px; ">
                                    {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                    <div class="row">
                                        {!! Form::label('name', 'Second Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <span class="position">{{$reviewEmployee->name}}-{{$reviewEmployee->employeeId}}</span></br>
                                                    <span class="position">Position : {{$positionNameUnderReview}}, Branch : {{$branchName}}</span>
                                                </div>
                                            </div>
                                            <div class="form-group positionClass">
                                                <p>Comment Box</p>
                                                <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" class="commentsDetails"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-md-12 " align="center">
                                            {!! Form::submit('Proceed', ['id' => 'secondStepverification', 'class' => 'btn btn-info']); !!}
                                            <a href="{{url('viewGroup/')}}" class="btn btn-danger closeBtn">Reject</a>
                                            <span id="success" style="color:green; font-size:16px;" class=""></span>
                                        </div>
                                    </div>
                                    {!! Form::close()  !!}
                                </div>
                            @endif
                            <!-- -------------------------------------------------end second step verification for Branch----------------------------------------------------------->
                            @if($userBranchCode == 0)
                                <div class="row" class="thirdtStepVerify" style="padding: 20px 14px 5px 14px; ">
                                    {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                    <div class="row">
                                        {!! Form::label('name', 'Third Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <span class="position">{{$approveEmployee->name}} - {{$approveEmployee->employeeId}}</span></br>
                                                    <span class="position">Position : {{$positionNameUnderApprove}}, Department : {{$departmentNameUnderApprove}}</span>
                                                </div>
                                            </div>
                                            <div class="form-group positionClass">
                                                <p>Comment Box</p>
                                                <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" class="commentsDetails"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-md-12 " align="center">
                                            {!! Form::submit('Proceed', ['id' => 'thirdStepverification', 'class' => 'btn btn-info']); !!}
                                            <a href="{{url('viewGroup/')}}" class="btn btn-danger closeBtn">Reject</a>
                                            <span id="success" style="color:green; font-size:16px;" class=""></span>
                                        </div>
                                    </div>
                                    {!! Form::close()  !!}
                                </div>
                            <!-- -------------------------------------------------end third step verification for Head Office----------------------------------------------------------->
                            @else
                                <div class="row" class="thirdStepVerify" style="padding: 20px 14px 5px 14px; ">
                                    {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                    <div class="row">
                                        {!! Form::label('name', 'Third Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <span class="position">{{$approveEmployee->name}}-{{$approveEmployee->employeeId}}</span></br>
                                                    <span class="position">Position : {{$positionNameUnderApprove}}, Branch : {{$branchName}}</span>
                                                </div>
                                            </div>
                                            <div class="form-group positionClass">
                                                <p>Comment Box</p>
                                                <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" class="commentsDetails"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-md-12 " align="center">
                                            {!! Form::submit('Proceed', ['id' => 'thirdStepverification', 'class' => 'btn btn-info']); !!}
                                            <a href="{{url('viewGroup/')}}" class="btn btn-danger closeBtn">Reject</a>
                                            <span id="success" style="color:green; font-size:16px;" class=""></span>
                                        </div>
                                    </div>
                                    {!! Form::close()  !!}
                                </div>
                            <!-- -------------------------------------------------end third step verification for branch----------------------------------------------------------->
                            @endif
                            
                            <!-- -------------------------------------------------end  verification condition  for superadmin----------------------------------------------------------->
                            
                            
                            <!-- -------------------------------------------------end  verification condition  for superadmin----------------------------------------------------------->
                            @elseif($verifyEmployee !=null && $reviewEmployee ==null  && $approveEmployee ==null && $userEmployeeId == $verifyEmployeeId && $voucherIdInfo->authBy ==0)
                                @if($accComment)
                                @else
                                    <div class="row thirdStepVerify"  style="padding: 20px 14px 5px 14px; " id="removeFirstStepDiv">
                                        {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                        <div class="row">
                                            {!! Form::label('name', 'Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-4" class="verified">
                                                        <span class="position">{{$verifyEmployee->name}} - {{$verifyEmployee->employeeId}}</span></br>
                                                        <span class="position">Position : {{$positionNameUnderVerify}},  Department : {{$departmentNameUnderVerify}}</span></br>
                                                    </div>
                                                </div>
                                                <div class="form-group positionClass">
                                                    <p>Comment Box</p>
                                                    <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" id="verifyCommentsDetails"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group">
                                                {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-md-12 " align="center">
                                                    <a href="javascript:void(0);" id="approveBtn" class="btn btn-info closeBtn">Approved</a>
                                                    <span id="success" style="color:green; font-size:16px;" class=""></span>
                                                </div>
                                            </div>
                                        </div>
                                        {!! Form::close()  !!}
                                    @endif
                                @elseif($verifyEmployee ==null && $reviewEmployee !=null  && $approveEmployee ==null && $userEmployeeId == $reviewEmployeeId && $voucherIdInfo->authBy ==0)
                                    @if($accComment)
                                    
                                    @else
                                    <div class="row thirdStepVerify"  style="padding: 20px 14px 5px 14px; " id="removeFirstStepDiv">
                                        {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                        <div class="row">
                                            {!! Form::label('name', 'Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-4" class="verified">
                                                        <span class="position">{{$reviewEmployee->name}} - {{$reviewEmployee->employeeId}}</span></br>
                                                        <span class="position">Position : {{$positionNameUnderReview}},  Department : {{$departmentNameUnderReview}}</span></br>
                                                    </div>
                                                </div>
                                                <div class="form-group positionClass">
                                                    <p>Comment Box</p>
                                                    <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" id="reviewCommentsDetails"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group">
                                                {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-md-12 " align="center">
                                                    <a href="javascript:void(0);" id="approveBtn" class="btn btn-info closeBtn">Approved</a>
                                                    <span id="success" style="color:green; font-size:16px;" class=""></span>
                                                </div>
                                            </div>
                                        </div>
                                        {!! Form::close()  !!}
                                        @endif
                                    @elseif($verifyEmployee ==null && $reviewEmployee ==null  && $approveEmployee !=null && $userEmployeeId == $approveEmployeeId && $voucherIdInfo->authBy ==0)
                                        @if($accComment)
                                        
                                        @else
                                        <div class="row thirdStepVerify"  style="padding: 20px 14px 5px 14px; " id="removeFirstStepDiv">
                                            {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                            <div class="row">
                                                {!! Form::label('name', 'Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-4" class="verified">
                                                            <span class="position">{{$approveEmployee->name}} - {{$approveEmployee->employeeId}}</span></br>
                                                            <span class="position">Position : {{$positionNameUnderApprove}},  Department : {{$departmentNameUnderApprove}}</span></br>
                                                        </div>
                                                    </div>
                                                    <div class="form-group positionClass">
                                                        <p>Comment Box</p>
                                                        <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" id="approveCommentsDetails"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group">
                                                    {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                                    <div class="col-md-12 " align="center">
                                                        <a href="javascript:void(0);" id="approveBtn" class="btn btn-info closeBtn">Approved</a>
                                                        <span id="success" style="color:green; font-size:16px;" class=""></span>
                                                    </div>
                                                </div>
                                            </div>
                                            {!! Form::close()  !!}
                                        @endif
                                    @elseif($verifyEmployee !=null && $reviewEmployee ==null  && $approveEmployee !=null)
                                        @if($userEmployeeId == $verifyEmployeeId)
                                            @if($userBranchCode == 0)
                                                @if($accComment)
                                                    @if($accComment->verified_by != 0)
                                                        <div class="row firstStepVerify"  style="padding: 20px 14px 5px 14px; " id="removeFirstStepDiv">
                                                            {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                            <div class="row">
                                                                {!! Form::label('name', 'First Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                                <div class="col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-md-4" class="verified">
                                                                            <span class="position">{{$verifyEmployee->name}} - {{$verifyEmployee->employeeId}}</span></br>
                                                                            <span class="position">Position : {{$positionNameUnderVerify}}, Department : {{$departmentNameUnderVerify}}</span></br>
                                                                            <span class="position status" >Status : {{$verifiedBy['status']}}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group positionClass">
                                                                        <p>Comment Box</p>
                                                                        <textarea class="col-sm-12 form-groups commentStyle"  name="commentsDetails" id="verifyCommentsDetails">{{$accComment->comments_details_verify}}</textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            {!! Form::close()  !!}
                                                        </div>
                                                    @elseif($accComment->rejected_by != 0)
                                                        <div class="row firstStepVerify"  style="padding: 20px 14px 5px 14px; " id="removeFirstStepDiv">
                                                            {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                            <div class="row">
                                                                {!! Form::label('name', 'First Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                                <div class="col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-md-4" class="verified">
                                                                            <span class="position">{{$verifyEmployee->name}} - {{$verifyEmployee->employeeId}}</span></br>
                                                                            <span class="position">Position : {{$positionNameUnderVerify}}, Department : {{$departmentNameUnderVerify}}</span></br>
                                                                            
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group positionClass">
                                                                        <p>Comment Box</p>
                                                                        <textarea class="col-sm-12 form-groups commentStyle"  name="commentsDetails" id="verifyCommentsDetails"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="form-group">
                                                                    {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                                                    <div class="col-md-12 " align="center">
                                                                        
                                                                        {!! Form::submit('Proceed', ['id' => 'submitFirstStep', 'class' => 'btn btn-info']); !!}
                                                                        {!! Form::submit('Reject', ['id' => 'rejectFirstStepVerify', 'class' => 'btn btn-danger']); !!}
                                                                        <span id="success" style="color:green; font-size:16px;" class=""></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            {!! Form::close()  !!}
                                                        </div>
                                                    @endif {{-- status condition end --}}
                                                @else
                                                    <div class="row" class="firstStepVerify" style="padding: 20px 14px 5px 14px; " id="removeFirstStepDiv">
                                                        {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                        <div class="row">
                                                            {!! Form::label('name', 'First Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                            <div class="col-md-12">
                                                                <div class="row">
                                                                    <div class="col-md-4" class="verified">
                                                                        <span class="position">{{$verifyEmployee->name}} - {{$verifyEmployee->employeeId}}</span></br>
                                                                        <span class="position">Position : {{$positionNameUnderVerify}}, Department : {{$departmentNameUnderVerify}}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group positionClass">
                                                                    <p>Comment Box</p>
                                                                    <textarea class="col-sm-12 form-groups commentStyle"  name="commentsDetails" id="verifyCommentsDetails"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="form-group">
                                                                {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                                                <div class="col-md-12 " align="center">
                                                                    @if($accComment == null)
                                                                    {!! Form::submit('Proceed', ['id' => 'submitFirstStep', 'class' => 'btn btn-info']); !!}
                                                                     {!! Form::submit('Reject', ['id' => 'rejectFirstStepVerify', 'class' => 'btn btn-danger']); !!}
                                                                    @else
                                                                    <a href="javascript:void(0);" onclick="return updateProcced();" class="btn btn-info closeBtn">Proceed</a>
                                                                    <a href="javascript:void(0);" onclick="return rejectApprovalVoucher();" class="btn btn-danger closeBtn">Reject</a>
                                                                    @endif
                                                                    <span id="success" style="color:green; font-size:16px;" class=""></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {!! Form::close()  !!}
                                                    </div>
                                                @endif {{-- end acccomment condition --}}
                                            @else
                                            @if($accComment)
                                            @if($accComment->verified_by != 0)
                                                <div class="row firstStepVerify"  style="padding: 20px 14px 5px 14px; " id="removeFirstStepDiv">
                                                    {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                    <div class="row">
                                                        {!! Form::label('name', 'First Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-4" class="verified">
                                                                    <span class="position">{{$verifyEmployee->name}} - {{$verifyEmployee->employeeId}}</span></br>
                                                                    <span class="position">Position : {{$positionNameUnderVerify}}, Department : {{$departmentNameUnderVerify}}</span></br>
                                                                    <span class="position status" >Status : {{$verifiedBy['status']}}</span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group positionClass">
                                                                <p>Comment Box</p>
                                                                <textarea class="col-sm-12 form-groups commentStyle"  name="commentsDetails" id="verifyCommentsDetails">{{$accComment->comments_details_verify}}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {!! Form::close()  !!}
                                                </div>
                                            @elseif($accComment->rejected_by != 0)
                                                <div class="row firstStepVerify"  style="padding: 20px 14px 5px 14px; " id="removeFirstStepDiv">
                                                    {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                    <div class="row">
                                                        {!! Form::label('name', 'First Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-4" class="verified">
                                                                    <span class="position">{{$verifyEmployee->name}} - {{$verifyEmployee->employeeId}}</span></br>
                                                                    <span class="position">Position : {{$positionNameUnderVerify}}, Department : {{$departmentNameUnderVerify}}</span></br>
                                                                    
                                                                </div>
                                                            </div>
                                                            <div class="form-group positionClass">
                                                                <p>Comment Box</p>
                                                                <textarea class="col-sm-12 form-groups commentStyle"  name="commentsDetails" id="verifyCommentsDetails"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group">
                                                            {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                                            <div class="col-md-12 " align="center">
                                                                
                                                                {!! Form::submit('Proceed', ['id' => 'submitFirstStep', 'class' => 'btn btn-info']); !!}
                                                                {!! Form::submit('Reject', ['id' => 'rejectFirstStepVerify', 'class' => 'btn btn-danger']); !!}
                                                                <span id="success" style="color:green; font-size:16px;" class=""></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {!! Form::close()  !!}
                                                </div>
                                            @endif {{-- status condition end --}}
                                        @else
                                            <div class="row" class="firstStepVerify" style="padding: 20px 14px 5px 14px; " id="removeFirstStepDiv">
                                                {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                <div class="row">
                                                    {!! Form::label('name', 'First Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-4" class="verified">
                                                                <span class="position">{{$verifyEmployee->name}} - {{$verifyEmployee->employeeId}}</span></br>
                                                                <span class="position">Position : {{$positionNameUnderVerify}}, Department : {{$departmentNameUnderVerify}}</span>
                                                            </div>
                                                        </div>
                                                        <div class="form-group positionClass">
                                                            <p>Comment Box</p>
                                                            <textarea class="col-sm-12 form-groups commentStyle"  name="commentsDetails" id="verifyCommentsDetails"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group">
                                                        {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                                        <div class="col-md-12 " align="center">
                                                            @if($accComment == null)
                                                            {!! Form::submit('Proceed', ['id' => 'submitFirstStep', 'class' => 'btn btn-info']); !!}
                                                             {!! Form::submit('Reject', ['id' => 'rejectFirstStepVerify', 'class' => 'btn btn-danger']); !!}
                                                            @else
                                                            <a href="javascript:void(0);" onclick="return updateProcced();" class="btn btn-info closeBtn">Proceed</a>
                                                            <a href="javascript:void(0);" onclick="return rejectApprovalVoucher();" class="btn btn-danger closeBtn">Reject</a>
                                                            @endif
                                                            <span id="success" style="color:green; font-size:16px;" class=""></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                {!! Form::close()  !!}
                                            </div>
                                        @endif {{-- end acccomment condition --}}
                                            @endif {{-- end acccomment condition --}}
                                        @elseif($userEmployeeId == $approveEmployeeId)
                                         
                                            @if($accComment)
                                            
                                                @if($accComment->approved_by != 0)
                                                   {{--  <div class="row" class="secondStepVerify" style="padding: 20px 14px 5px 14px; " id="removeSecondStepDiv">
                                                        {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                        <div class="row">
                                                            {!! Form::label('name', 'Second Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                            <div class="col-md-12">
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <span class="position">{{$reviewEmployee->name}}-{{$reviewEmployee->employeeId}}</span></br>
                                                                        <span class="position">Position : {{$positionNameUnderReview}}, Branch : {{$branchName}}</span></br>
                                                                        <b><span class="position">Status : Approved</span></b>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group positionClass">
                                                                    <p>Comment Box</p>
                                                                    <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" id ="reviewCommentsDetails" class="commentsDetails">{{$accComment->comments_details_review}}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {!! Form::close()  !!}
                                                    </div> --}}
                                                @else
                                                @if($accComment)
                                                    @if($accComment->verified_by != 0)
                                                        {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                        <div class="row">
                                                            {!! Form::label('name', 'First Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                            <div class="col-md-12">
                                                                <div class="row">
                                                                    <div class="col-md-4" class="verified">
                                                                        <span class="position">{{$verifyEmployee->name}} - {{$verifyEmployee->employeeId}}</span></br>
                                                                        <span class="position">Position : {{$positionNameUnderVerify}}, Department : {{$departmentNameUnderVerify}}</span></br>
                                                                        <b><span class="position">Status : {{$verifiedBy['status']}}</span></b>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group positionClass">
                                                                    <p>Comment Box</p>
                                                                    <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" id="verifyCommentsDetails">{{$accComment->comments_details_verify}}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {!! Form::close()  !!}
                                                    @endif {{-- status condition end --}}
                                                @endif {{-- end acccomment condition --}}
                                                    <div class="row" class="secondStepVerify" style="padding: 20px 14px 5px 14px; " id="removeSecondStepDiv">
                                                        {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                        <div class="row">
                                                            {!! Form::label('name', 'Second Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                            <div class="col-md-12">
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <span class="position">{{$approveEmployee->name}}-{{$approveEmployee->employeeId}}</span></br>
                                                                        <span class="position">Position : {{$positionNameUnderApprove}}, Branch : {{$branchName}}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group positionClass">
                                                                    <p>Comment Box</p>
                                                                    <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" id ="approveCommentsDetails" class="reviewCommentsDetails"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                                            <div class="col-md-12 " align="center">
                                                                <a href="javascript:void(0);" id="approveSecondStep" class="btn btn-info closeBtn">Approved</a>
                                                            </div>
                                                        </div>
                                                        {!! Form::close()  !!}
                                                    </div>
                                                @endif {{-- status condition end --}}
                                            @else
                                          
                                            @endif
                                {{-- start second step reviewed --}}
                                <!-- ---------------------------------------------end verifiedby first step verification for Head office----------------------------------------------------------->
                                        @endif
                                    @elseif($userEmployeeId == $verifyEmployeeId)
                                        @if($userBranchCode == 0)
                                            @if($accComment)
                                                @if($accComment->verified_by != 0)
                                                    <div class="row firstStepVerify"  style="padding: 20px 14px 5px 14px; " id="removeFirstStepDiv">
                                                        {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                        <div class="row">
                                                            {!! Form::label('name', 'First Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                            <div class="col-md-12">
                                                                <div class="row">
                                                                    <div class="col-md-4" class="verified">
                                                                        <span class="position">{{$verifyEmployee->name}} - {{$verifyEmployee->employeeId}}</span></br>
                                                                        <span class="position">Position : {{$positionNameUnderVerify}}, Department : {{$departmentNameUnderVerify}}</span></br>
                                                                        <span class="position status" >Status : {{$verifiedBy['status']}}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group positionClass">
                                                                    <p>Comment Box</p>
                                                                    <textarea class="col-sm-12 form-groups commentStyle"  name="commentsDetails" id="verifyCommentsDetails">{{$accComment->comments_details_verify}}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {!! Form::close()  !!}
                                                    </div>
                                                @elseif($accComment->rejected_by != 0)
                                                    <div class="row firstStepVerify"  style="padding: 20px 14px 5px 14px; " id="removeFirstStepDiv">
                                                        {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                        <div class="row">
                                                            {!! Form::label('name', 'First Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                            <div class="col-md-12">
                                                                <div class="row">
                                                                    <div class="col-md-4" class="verified">
                                                                        <span class="position">{{$verifyEmployee->name}} - {{$verifyEmployee->employeeId}}</span></br>
                                                                        <span class="position">Position : {{$positionNameUnderVerify}}, Department : {{$departmentNameUnderVerify}}</span></br>
                                                                        
                                                                    </div>
                                                                </div>
                                                                <div class="form-group positionClass">
                                                                    <p>Comment Box</p>
                                                                    <textarea class="col-sm-12 form-groups commentStyle"  name="commentsDetails" id="verifyCommentsDetails"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="form-group">
                                                                {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                                                <div class="col-md-12 " align="center">
                                                                    
                                                                    {!! Form::submit('Proceed', ['id' => 'submitFirstStep', 'class' => 'btn btn-info']); !!}
                                                                    {!! Form::submit('Reject', ['id' => 'rejectFirstStepVerify', 'class' => 'btn btn-danger']); !!}
                                                                    <span id="success" style="color:green; font-size:16px;" class=""></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {!! Form::close()  !!}
                                                    </div>
                                                @endif {{-- status condition end --}}
                                            @else
                                                <div class="row" class="firstStepVerify" style="padding: 20px 14px 5px 14px; " id="removeFirstStepDiv">
                                                    {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                    <div class="row">
                                                        {!! Form::label('name', 'First Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-4" class="verified">
                                                                    <span class="position">{{$verifyEmployee->name}} - {{$verifyEmployee->employeeId}}</span></br>
                                                                    <span class="position">Position : {{$positionNameUnderVerify}}, Department : {{$departmentNameUnderVerify}}</span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group positionClass">
                                                                <p>Comment Box</p>
                                                                <textarea class="col-sm-12 form-groups commentStyle"  name="commentsDetails" id="verifyCommentsDetails"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group">
                                                            {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                                            <div class="col-md-12 " align="center">
                                                                @if($accComment == null)
                                                                {!! Form::submit('Proceed', ['id' => 'submitFirstStep', 'class' => 'btn btn-info']); !!}
                                                                {!! Form::submit('Reject', ['id' => 'rejectFirstStepVerify', 'class' => 'btn btn-danger']); !!}
                                                                @else
                                                                <a href="javascript:void(0);" onclick="return updateProcced();" class="btn btn-info closeBtn">Proceed</a>
                                                                <a href="javascript:void(0);" onclick="return rejectApprovalVoucher();" class="btn btn-danger closeBtn">Reject</a>
                                                                @endif
                                                                <span id="success" style="color:green; font-size:16px;" class=""></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {!! Form::close()  !!}
                                                </div>
                                            @endif {{-- end acccomment condition --}}
                                        <!-- ---------------------------------------------end verifiedby first step verification for Head office----------------------------------------------------------->
                                        @else {{-- branch condition --}}
                                            @if($accComment)
                                                @if($accComment->verified_by != 0)
                                                    {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                    <div class="row">
                                                        {!! Form::label('name', 'First Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-4" class="verified">
                                                                    <span class="position">{{$verifyEmployee->name}} - {{$verifyEmployee->employeeId}}</span></br>
                                                                    <span class="position">Position : {{$positionNameUnderVerify}}, Department : {{$departmentNameUnderVerify}}</span></br>
                                                                    <span class="position status">Status : {{$verifiedBy['status']}}</span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group positionClass">
                                                                <p>Comment Box</p>
                                                                <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" id="verifyCommentsDetails">{{$accComment->comments_details_verify}}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {!! Form::close()  !!}
                                                    <br>
                                                 @endif {{-- status condition end --}}
                                            @else
                                                <div class="row" class="firstStepVerify" style="padding: 20px 14px 5px 14px; " id="removeFirstStepDiv">
                                                    {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                    <div class="row">
                                                        {!! Form::label('name', 'First Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-4" class="verified">
                                                                    <span class="position">{{$verifyEmployee->name}} - {{$verifyEmployee->employeeId}}</span></br>
                                                                    <span class="position">Position : {{$positionNameUnderVerify}}, Department : {{$departmentNameUnderVerify}}</span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group positionClass">
                                                                <p>Comment Box</p>
                                                                <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" id="verifyCommentsDetails"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group">
                                                            {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                                            <div class="col-md-12 " align="center">
                                                                @if($accComment == null)
                                                                {!! Form::submit('Proceed', ['id' => 'submitFirstStep', 'class' => 'btn btn-info']); !!}
                                                                {!! Form::submit('Reject', ['id' => 'rejectFirstStepVerify', 'class' => 'btn btn-danger']); !!}
                                                                @else
                                                                <a href="javascript:void(0);" onclick="return updateProcced();" class="btn btn-info closeBtn">Proceed</a>
                                                                <a href="javascript:void(0);" onclick="return rejectApprovalVoucher();" class="btn btn-danger closeBtn">Reject</a>
                                                                @endif
                                                                <span id="success" style="color:green; font-size:16px;" class=""></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {!! Form::close()  !!}
                                                </div>
                                            @endif {{-- end acccomment condition --}}
                                
                                        <!-- ------------------------------end verified first step for branch--------------------------------------------------->
                                        <!-- --------------start revieewd first step for head office----------------------------------------------------------->
                                        @endif
                                        @elseif($userEmployeeId == $reviewEmployeeId)
                                            @if($userBranchCode == 0)
                                                @if($accComment)
                                                    @if($accComment->verified_by != 0)
                                                        {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                        <div class="row">
                                                            {!! Form::label('name', 'First Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                            <div class="col-md-12">
                                                                <div class="row">
                                                                    <div class="col-md-4" class="verified">
                                                                        <span class="position">{{$verifyEmployee->name}} - {{$verifyEmployee->employeeId}}</span></br>
                                                                        <span class="position">Position : {{$positionNameUnderVerify}}, Department : {{$departmentNameUnderVerify}}</span></br>
                                                                        <b><span class="position">Status : {{$verifiedBy['status']}}</span></b>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group positionClass">
                                                                    <p>Comment Box</p>
                                                                    <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" id="verifyCommentsDetails">{{$accComment->comments_details_verify}}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {!! Form::close()  !!}
                                                     @endif {{-- status condition end --}}
                                                @endif {{-- end acccomment condition --}}
                                            {{-- end head office condition --}}
                                            @else                                
                                            @if($accComment)
                                                @if($accComment->verified_by != 0)
                                                    <div class="row" class="secondStepVerify" style="padding: 20px 14px 5px 14px; " id="removeSecondStepDiv">
                                                        {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                        <div class="row">
                                                            {!! Form::label('name', 'First Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                            <div class="col-md-12">
                                                                <div class="row">
                                                                    <div class="col-md-4" class="verified">
                                                                        <span class="position">{{$verifyEmployee->name}} - {{$verifyEmployee->employeeId}}</span></br>
                                                                        <span class="position">Position : {{$positionNameUnderVerify}}, Branch : {{$branchName}}</span></br>
                                                                        <b><span class="position">Status : {{$verifiedBy['status']}}</span></b>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group positionClass">
                                                                    <p>Comment Box</p>
                                                                    <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" id="verifyCommentsDetails">{{$accComment->comments_details_verify}}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {!! Form::close()  !!}
                                                    </div>
                                                 @endif {{-- status condition end --}}
                                            @endif {{-- end acccomment condition --}}
                                        @endif {{-- end branch condition --}}
                                    {{-- start second step reviewed --}}
                                        </br>
                                        @if($accComment)
                                            @if($accComment->reviewed_by != 0 )
                                                <div class="row" class="secondStepVerify" style="padding: 20px 14px 5px 14px; " id="removeSecondStepDiv">
                                                    {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                    <div class="row">
                                                        {!! Form::label('name', 'Second Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <span class="position">{{$reviewEmployee->name}}-{{$reviewEmployee->employeeId}}</span></br>
                                                                    <span class="position">Position : {{$positionNameUnderReview}}, Branch : {{$branchName}}</span></br>
                                                                    <b><span class="position">Status : {{$reviewedBy['status']}}</span></b>
                                                                </div>
                                                            </div>
                                                            <div class="form-group positionClass">
                                                                <p>Comment Box</p>
                                                                <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" id ="reviewCommentsDetails" class="commentsDetails">{{$accComment->comments_details_review}}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {!! Form::close()  !!}
                                                </div>
                                            @else
                                                <div class="row" class="secondStepVerify" style="padding: 20px 14px 5px 14px; " id="removeSecondStepDiv">
                                                    {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                    <div class="row">
                                                        {!! Form::label('name', 'Second Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <span class="position">{{$reviewEmployee->name}}-{{$reviewEmployee->employeeId}}</span></br>
                                                                    <span class="position">Position : {{$positionNameUnderReview}}, Branch : {{$branchName}}</span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group positionClass">
                                                                <p>Comment Box</p>
                                                                <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" id ="reviewCommentsDetails" class="commentsDetails"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                                        <div class="col-md-12 " align="center">
                                                            <a href="javascript:void(0);" onclick="return updateProcced();" class="btn btn-info closeBtn">Proceed</a>
                                                            <a href="javascript:void(0);" onclick="return rejectApprovalVoucher();" class="btn btn-danger closeBtn">Reject</a>
                                                        </div>
                                                    </div>
                                                    {!! Form::close()  !!}
                                                </div>
                                            @endif {{-- status condition end --}}
                                        @else
                                        @endif
                        <!-- --------------start reviewed  branch  step for head office----------------------------------------------------------->
                        <!-- --------------start approved first step for head office----------------------------------------------------------->
                        @endif
                        @if($userEmployeeId == $approveEmployeeId && $v_approval_step == 3)
                            @if($accComment)
                                @if($accComment->approved_by != 0)
                                @else
                                    @if($userBranchCode == 0)
                                        @if($accComment)
                                            @if($accComment->verified_by != 0 )
                                                <div class="row firstStepVerify"  style="padding: 20px 14px 5px 14px; " id="removeSecondStepDiv">
                                                    {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                    <div class="row">
                                                        {!! Form::label('name', 'First Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <span class="position">{{$verifyEmployee->name}} - {{$verifyEmployee->employeeId}}</span></br>
                                                                    <span class="position">Position : {{$positionNameUnderVerify}},  Department : {{$departmentNameUnderVerify}}</span></br>
                                                                    <b><span class="position">Status : {{$verifiedBy['status']}}</span></b>
                                                                </div>
                                                            </div>
                                                            <div class="form-group positionClass">
                                                                <p>Comment Box</p>
                                                                <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" id ="verifyCommentsDetails" class="commentsDetails">{{$accComment->comments_details_verify}}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {!! Form::close()  !!}
                                                </div>
                                            @endif {{-- status condition end --}}
                                        @endif {{-- end acccomment condition --}}
                                    @else
                                        @if($accComment->approved_by != 0)
                                        @else
                                            @if($accComment)
                                                @if($accComment->verified_by != 0)
                                                    <div class="row" class="firstStepVerify" style="padding: 20px 14px 5px 14px; " id="removeSecondStepDiv">
                                                        {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                        <div class="row">
                                                            {!! Form::label('name', 'First Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                            <div class="col-md-12">
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <span class="position">{{$verifyEmployee->name}} - {{$verifyEmployee->employeeId}}</span></br>
                                                                        <span class="position">Position : {{$positionNameUnderVerify}},  Department : {{$departmentNameUnderVerify}}</span></br>
                                                                        <b><span class="position">Status : {{$verifiedBy['status']}}</span></b>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group positionClass">
                                                                    <p>Comment Box</p>
                                                                    <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" id ="verifyCommentsDetails" class="commentsDetails">{{$accComment->comments_details_verify}}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <br>
                                                @else
                                                @endif {{-- status condition end --}}
                                             @endif {{-- end acccomment condition --}}
                                        @endif {{-- end branch condition --}}
                                    @endif
                                    {{-- start second step reviewed --}}
                                    </br>
                                        @if($accComment)
                                            @if($accComment->reviewed_by != 0)
                                                <div class="row" class="secondStepVerify" style="padding: 20px 14px 5px 14px; " id="removeSecondStepDiv">
                                                    {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                    <div class="row">
                                                        {!! Form::label('name', 'Second Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <span class="position">{{$reviewEmployee->name}} - {{$reviewEmployee->employeeId}}</span></br>
                                                                    <span class="position">Position : {{$positionNameUnderReview}}, Department : {{$departmentNameUnderReview}}</span></br>
                                                                    <b><span class="position">Status : {{$reviewedBy['status']}}</span></b>
                                                                </div>
                                                            </div>
                                                            <div class="form-group positionClass">
                                                                <p>Comment Box</p>
                                                                <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" id ="reviewCommentsDetails" class="commentsDetails">{{$accComment->comments_details_review}}</textarea>
                                                            </div>
                                                        </div>
                                                        {!! Form::close()  !!}
                                                    </div>
                                                </div>
                                                        @else
                                                @if($accComment->status == 'Rejected')
                                                @endif
                                            @endif
                                            @if($accComment->reviewed_by == 0)
                                            @else
                                                <div class="row thirdStepVerify"  style="padding: 20px 14px 5px 14px; " id="removeFirstStepDiv">
                                                    {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                                    <div class="row">
                                                        {!! Form::label('name', 'Third Step Verification:', ['class' => 'col-sm-12 control-label positionLabel']) !!}
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-4" class="verified">
                                                                    <span class="position">{{$approveEmployee->name}} - {{$approveEmployee->employeeId}}</span></br>
                                                                    <span class="position">Position : {{$positionNameUnderApprove}}, Department : {{$departmentNameUnderApprove}}</span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group positionClass">
                                                                <p>Comment Box</p>
                                                                <textarea class="col-sm-12 form-groups commentStyle" name="commentsDetails" id="comments_details_approve"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group">
                                                            {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                                            <div class="col-md-12 " align="center">
                                                                @if($accComment == null)
                                                                <a href="javascript:void(0);" onclick="return updateApproval();" class="btn btn-info closeBtn">Approved</a>
                                                                <a href="javascript:void(0);" onclick="return rejectApprovalVoucher();" class="btn btn-danger closeBtn">Reject</a>
                                                                @else
                                                                <a href="javascript:void(0);" onclick="return updateApprovalSetting();" class="btn btn-info closeBtn">Approved</a>
                                                                <a href="javascript:void(0);" onclick="return rejectApprovalSetting();" class="btn btn-danger closeBtn">Reject</a>
                                                                @endif
                                                                <span id="success" style="color:green; font-size:16px;" class=""></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                     {!! Form::close()  !!}
                                                </div>
                                            @endif
                                        @endif
                                    @endif {{-- status condition end --}}
                                @else
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="footerTitle" style="border-top:1px solid white"></div>
            </div>
            <div class="col-md-1"></div>
        </div>
    </div>
    <div class="modal fade" id="myModal" role="dialog" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg  modal-center" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Voucher Image</h4>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).on('click','#approveBtn',function(){
            event.preventDefault();
            var verified_by ={{$verifyEmployeeId}};
            var reviewed_by = {{$reviewEmployeeId}};
            var approved_by = {{$approveEmployeeId}};
            var userEmployeeId = {{$userEmployeeId}};
            var comments_details_verify = $('#verifyCommentsDetails').val();
            var comments_details_review = $('#reviewCommentsDetails').val();
            var comments_details_approve = $('#approveCommentsDetails').val();
            var voucher_id = {{$voucherIdInfo->id}} ;
            var csrf = "<?php echo csrf_token(); ?>";

            if(comments_details_verify == ''){
                alert('Please Insert Comment'); 
            }else if(comments_details_review == ''){
                 alert('Please Insert Comment');
            }else if(comments_details_approve == ''){
                 alert('Please Insert Comment');
            }

            formData = new FormData();
            formData.append('comments_details_verify', comments_details_verify);
            formData.append('comments_details_review', comments_details_review);
            formData.append('comments_details_approve', comments_details_approve);
            formData.append('voucher_id', voucher_id);
            formData.append('verified_by', verified_by);
            formData.append('reviewed_by', reviewed_by);
            formData.append('approved_by', approved_by);
            formData.append('userEmployeeId', userEmployeeId);
            formData.append('_token', csrf);

            $.ajax({
                processData: false,
                contentType: false,
                type: 'post',
                url: '../addFirstStepApproval',
                data:formData,
                dataType: 'json',
                success: function(_response ){
                    //location.reload();
                if (_response.errors) {
                    if (_response.errors['comments_details_verify']) {
                        alert('Please Insert Comment');
                    }else if(_response.errors['comments_details_review']){
                        alert('Please Insert Comment');
                    }else if(response.errors['comments_details_approve']){
                        alert('Please Insert Comment');
                    }
                }else{
                    location.reload();
                }
                },
                error: function( _response ){
                //alert( _response);
                }
            });
        });
    </script>
    <script type="text/javascript">
        $(document).on('click','#approveSecondStep',function(){
            event.preventDefault();
            var voucher_id= {{$voucherIdInfo->id }} ;
            var verify_emp_id = {{$verifyEmployeeId}};
            var review_emp_id = {{$reviewEmployeeId}};
            var approve_emp_id = {{$approveEmployeeId}};
            var comments_details_verify = $('#verifyCommentsDetails').val();
            var comments_details_review = $('#reviewCommentsDetails').val();
            var comments_details_approve = $('#approveCommentsDetails').val();
            //alert(comments_details_review);
            var csrf = "<?php echo csrf_token(); ?>";
            
            formData = new FormData();
            formData.append('voucher_id', voucher_id);
            formData.append('verify_emp_id', verify_emp_id);
            formData.append('review_emp_id', review_emp_id);
            formData.append('approve_emp_id', approve_emp_id);
            formData.append('comments_details_verify', comments_details_verify);
            formData.append('comments_details_review', comments_details_review);
            formData.append('comments_details_approve', comments_details_approve);
            formData.append('_token', csrf);
        
            $.ajax({
                processData: false,
                contentType: false,
                type: 'post',
                url: '../secondStepApproval',
                data:formData,
                dataType: 'json',
                success: function(_response ){
                //location.reload();
                if (_response.errors) {
                    if (_response.errors['comments_details_review']) {
                        alert('Please Insert Comment');
                    }
                }else{
                  location.reload();
                }
                },
                error: function( _response ){
                //alert( _response);
                }
            });
        });
    </script>
    {{-- <script src="{{asset('js/jquery-1.11.1.min.js')}}"></script> --}}
    {{-- Print Page --}}
    <script type="text/javascript">
        @php
            if ($verifyEmployee) {
                $verifyEmployeeEmpId = $verifyEmployee->id;
            }
            else {
                $verifyEmployeeEmpId = 0;
            }
        @endphp
    // first step proced
    $("#submitFirstStep").click(function(){
        if (this.id == 'submitFirstStep') {
            var btnValue=0;
        }
        $("#submitFirstStep").prop("disabled", true);
            event.preventDefault();
            var commentsDetails = $('#verifyCommentsDetails').val();
            var verifiedBy = {{$verifyEmployeeEmpId}};
            var voucherId = {{$voucherIdInfo->id}} ;
            var csrf = "<?php echo csrf_token(); ?>";
            formData = new FormData();
            formData.append('commentsDetails', commentsDetails);
            formData.append('voucherId', voucherId);
            formData.append('verifiedBy', verifiedBy);
            formData.append('_token', csrf);
        $.ajax({
            processData: false,
            contentType: false,
            type: 'post',
            url: '../addAccCommentsItem',
            data:formData,
            dataType: 'json',
            success: function( _response ){
                if (_response.errors) {
                    if (_response.errors['commentsDetails']) {
                        alert('Please Insert Comment');
                    }
                }
                else{
                    location.reload();
                }
            },
            error: function( _response ){
            //alert( _response);
            }
        });
    });
    
    function updateProcced() {
        var voucher_id= {{$voucherIdInfo->id }} ;
        var verify_emp_id = {{$verifyEmployeeId}};
        var review_emp_id = {{$reviewEmployeeId}};
        var approve_emp_id = {{$approveEmployeeId}};
        var comments_details_verify = $('#verifyCommentsDetails').val();
        var comments_details_review = $('#reviewCommentsDetails').val();
        var comments_details_approve = $('#comments_details_approve').val();
        //alert(comments_details_approve);
        var csrf = "<?php echo csrf_token(); ?>";

        $.ajax({
            type: 'POST',
            url: '../settingsApprovalProccesing',
            data: {
                'comments_details_verify': comments_details_verify,
                'comments_details_review': comments_details_review,
                'comments_details_approve': comments_details_approve,
                'verify_emp_id': verify_emp_id,
                'review_emp_id': review_emp_id,
                'approve_emp_id': approve_emp_id,
                'voucher_id': voucher_id,
                'csrf': csrf
            },
            success: function( _response ){
                if (_response.errors) {
                    if (_response.errors['comments_details_review']) {
                            alert('Please Insert Comment');
                    }
                }else{
                    location.reload();
                }
            },
            error: function( _response ){
            //alert( _response);
            }
        });
    }
    //Update Approval Setting
    function updateApprovalSetting() {
        var voucher_id= {{$voucherIdInfo->id }} ;
        var verify_emp_id = {{$verifyEmployeeId}};
        var review_emp_id = {{$reviewEmployeeId}};
        var approve_emp_id = {{$approveEmployeeId}};
        var userEmployeeId = {{$userEmployeeId}};
        var comments_details_verify = $('#verifyCommentsDetails').val();
        var comments_details_review = $('#reviewCommentsDetails').val();
        var comments_details_approve = $('#comments_details_approve').val();
        //alert(comments_details_approve);
        var csrf = "<?php echo csrf_token(); ?>";
        $.ajax({
            type: 'POST',
            url: '../updateApprovalSettingProccesing',
            data: {
                'comments_details_verify': comments_details_verify,
                'comments_details_review': comments_details_review,
                'comments_details_approve': comments_details_approve,
                'verify_emp_id': verify_emp_id,
                'review_emp_id': review_emp_id,
                'approve_emp_id': approve_emp_id,
                'userEmployeeId': userEmployeeId,
                'voucher_id': voucher_id,
                'csrf': csrf
            },
            success: function( _response ){
                if (_response.errors) {
                    if (_response.errors['comments_details_approve']) {
                        alert('Please Insert Comment');
                    }
                }else{
                location.reload();
                }
            },
            error: function( _response ){
            //alert( _response);
            }
        });
    }

    //Reject last step Approval Setting
    function rejectApprovalSetting() {
        var voucher_id= {{$voucherIdInfo->id }} ;
        var verify_emp_id = {{$verifyEmployeeId}};
        var review_emp_id = {{$reviewEmployeeId}};
        var approve_emp_id = {{$approveEmployeeId}};
        var userEmployeeId = {{$userEmployeeId}};
        var comments_details_verify = $('#verifyCommentsDetails').val();
        var comments_details_review = $('#reviewCommentsDetails').val();
        var comments_details_approve = $('#comments_details_approve').val();
        var csrf = "<?php echo csrf_token(); ?>";

        $.ajax({
            type: 'POST',
            url: '../updateApprovalSettingReject',
            data: {
                'comments_details_verify': comments_details_verify,
                'comments_details_review': comments_details_review,
                'comments_details_approve': comments_details_approve,
                'verify_emp_id': verify_emp_id,
                'review_emp_id': review_emp_id,
                'approve_emp_id': approve_emp_id,
                'userEmployeeId': userEmployeeId,
                'voucher_id': voucher_id,
                'csrf': csrf
            },
            success: function( _response ){
                if (_response.errors) {
                    if (_response.errors['comments_details_approve']) {
                        alert('Please Insert Comment');
                    }
                }else{
                    location.reload();
                }
            },
            error: function( _response ){
            //alert( _response);
            }
        });
    }


    function rejectApprovalVoucher(){
        var voucher_id= {{$voucherIdInfo->id }} ;
        var verify_emp_id = {{$verifyEmployeeId}};
        var review_emp_id = {{$reviewEmployeeId}};
        var approve_emp_id = {{$approveEmployeeId}};
        var userEmployeeId = {{$userEmployeeId}};
        var comments_details_verify = $('#verifyCommentsDetails').val();
        var comments_details_review = $('#reviewCommentsDetails').val();
        var comments_details_approve = $('#comments_details_approve').val();
        var csrf = "<?php echo csrf_token(); ?>";

        $.ajax({
            type: 'POST',
            url: '../settingsApprovalReject',
            data: {
                'comments_details_verify': comments_details_verify,
                'comments_details_review': comments_details_review,
                'comments_details_approve': comments_details_approve,
                'verify_emp_id': verify_emp_id,
                'review_emp_id': review_emp_id,
                'approve_emp_id': approve_emp_id,
                'voucher_id': voucher_id,
                'userEmployeeId': userEmployeeId,
                'csrf': csrf
            },
            success: function( _response ){
                if (_response.errors) {
                    if (_response.errors['comments_details_review']) {
                        alert('Please Insert Comment');
                     }
                }else{
                    location.reload();
                }
            },
            error: function( _response ){
            //alert( _response);
            }
        });
    }


    // reviewed by update
    //rejected verification
    $("#rejectFirstStepVerify").click(function(){
        if (this.id == 'rejectFirstStepVerify') {
            var btnValue=1;
        }
    $("#rejectFirstStepVerify").prop("disabled", true);
        event.preventDefault();
        var commentsDetails = $('#verifyCommentsDetails').val();
        var verifiedBy = {{$verifyEmployeeEmpId}};
        var voucherId = {{$voucherIdInfo->id}} ;
        var userEmployeeId = {{$userEmployeeId}} ;
        var csrf = "<?php echo csrf_token(); ?>";
        formData = new FormData();
        formData.append('commentsDetails', commentsDetails);
        formData.append('voucherId', voucherId);
        formData.append('verifiedBy', verifiedBy);
        formData.append('userEmployeeId', userEmployeeId);
        formData.append('_token', csrf);

        $.ajax({
            processData: false,
            contentType: false,
            type: 'post',
            url: '../rejectedAccCommentsItem',
            data:formData,
            dataType: 'json',
            success: function( _response ){
                if (_response.errors) {
                    if (_response.errors['commentsDetails']) {
                        alert('Please Insert Comment');
                    }
                }else{
                    location.reload();
                }
            },
            error: function( _response ){
            //alert( _response);
            }
        });
    });


    $(function(){
        $("#printList").click(function(event) {
            var mainContents = document.getElementById("printView").innerHTML;
            var headerContents = '';
            var printStyle = '<style>#voucherView{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt ash; page-break-inside:auto;}  #voucherView thead tr th{text-align:center;vertical-align: middle; padding: 5px!important; font-size:11px;} #voucherView tbody tr td {text-align:center;vertical-align: middle;padding:3px ;font-size:11px;} tr{ page-break-inside:avoid; page-break-after:auto }#voucherView tr td.amount{ text-align: right; padding-right: 5px; }#voucherView tr td.name{text-align: left; padding-left: 5px;}#voucherInfoTable tbody tr td{ font-size:12px;}#globalNarrationTable{padding: 10px 0; }#globalNarrationTable tbody tr td{font-size: 12px;color: black;padding-bottom: 15px ;text-align: justify;vertical-align: middle;border:1px solid black !important;}#divFooter{text-align:center;position: fixed;bottom:12;display:block; color:#A3A3A3;}</style><style>@page {size: auto;margin: 0;}</style>';
            var printContents = '<div id="order-details-wrapper" style="padding: 30px 30px 0px 40px;">' + headerContents + printStyle + mainContents +'</div>';
            var win = window.open('','printwindow');
            win.document.write(printContents);
            win.print();
        // $("#voucherView").addClass('table table-striped table-bordered');
            win.close();
        });
    });
    //image show
    $(document).ready(function() {
        $('.img_modal').on('click',function(){
            var imgname = $(this).attr('name');
            var imgSrc ="{{ asset('/images/vouchers') }}/"+imgname;
            $('.modal-body').empty();
            $('.modal-body').append('<img src="'+imgSrc+'" class="img-responsive modal-image">');
        });
    });
    </script>


    {{-- EndPrint Page --}}
    <style type="text/css">
    #divFooter{ text-align: center; width:100%; }

    @media screen {
         #divFooter { display: none; }
    }

    #voucherView{
        font-size:12px;
        margin-bottom:0;
        margin-left:0;
        width: 100%;
    }
    #voucherView thead tr th,
    #voucherView tbody tr td{
        text-align: center;
        color: black /*!important*/;
        vertical-align: middle;
        border:1px solid black !important;
    /*padding-bottom: 100px;*/
    /*height: 20%;*/
    }
    #voucherView thead tr th{
        color: black /*!important*/;
        font-size: 13px;
        background-color: white !important;
    }
    #voucherView tbody tr td{
        padding: 6px 0;  10px top & bottom padding, 0px left & right ;  /*for td height*/
    }
    #voucherView tr{background-color:  white !important;}
    #voucherView tr:hover { background-color:    white !important;          /* lightyellow */ }
    #voucherView tr td.amount{text-align: right;padding-right: 5px;}
    #voucherView tr td.name{text-align: left; padding-left: 5px;}
    #globalNarrationTable tbody tr td{
        font-size: 12px;
        color: black;
        text-align: justify;
        padding-bottom: 15px ;
        vertical-align: middle;
        border:1px solid black !important;
    }
    #globalNarrationTable tbody tr{background-color:  white !important;}
    .userInfoMainDiv{
    width:100%%;
    }
    .userInfoDiv{
        width:33.33%;
        color: black;
        text-align: center;
        float: left;
    /*padding-top: 40px;*/
    /*margin-left: 10px;*/
    }
    #voucherInfoTable{
    color: black;
    font-size: 12px;
    width: 100%;
    }
    #voucherInfoTable tbody tr td{ text-align: left; }
    /*#voucherInfoTable tbody tr td:last-child{
    text-align: right;
    }*/
    </style>
    @endsection