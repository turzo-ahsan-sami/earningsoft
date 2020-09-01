@extends('layouts/acc_layout')
@section('title', '| Auto Voucher Configuration')
@section('content')
<style type="text/css">
    .ledgerCode {
        text-align:right
    }
    #autoVouchersTable1 thead tr th, #autoVouchersTable2 thead tr th, #autoVouchersTable3 thead tr th{
        padding:5px ;
    }
</style>

<?php

// echo "<pre>";
// print_r($loanConfig);
// print_r($savingsConfig);
// print_r($sktConfig);
// echo "</pre>";


?>

<div class="row add-data-form"  style="padding-bottom: 1%">
    <div class="col-md-12">
        <div class="col-md-2"></div>
        <div class="col-md-8 fullbody">
            <div class="viewTitle" style="border-bottom: 1px solid white;">
                {{-- <a href="{{url('viewAutoVoucherConfig/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                </i>Auto Voucher Configuration List</a> --}}
            </div>
            <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <div class="panel-title">Auto Voucher Configuration For Microfinance</div>
                </div>


                <div class="panel-body">
                    {!! Form::open(array('url' => '', 'id' => 'autoVoucherFormId', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                

                    <div class="row">
                        <div class="col-md-12">

                            <h4 style="color: black; "><u> Loan </u></h4>
                            <table class="table table-striped table-bordered" id="autoVouchersTable1" style="width:100%; color:black; margin-bottom: 20px; ">
                                <thead>
                                    <tr>
                                        <th width="15%">Funding Organization</th>
                                        <th>Loan Primary Product</th>
                                        <th width="15%">Principal Code</th>
                                        <th width="15%">Service Charge Code</th>
                                        <th width="15%">Risk Insurance Code</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($loanProductOption as$key => $loanProductInfo)
                                    <?php 
                                        // $ledgerInfo=$loanConfig->where('loanProductId', $loanProductInfo->id)->first();
                                        // var_dump($ledgerInfo);echo "<br/>";
                                        $ledgerIdForPrincipalOfLoan=$loanConfig->where('loanProductId', $loanProductInfo->id)->min('ledgerIdForPrincipal');
                                        $ledgerIdForInterestOfLoan=$loanConfig->where('loanProductId', $loanProductInfo->id)->min('ledgerIdForInterest');
                                        $ledgerIdForRiskInsuranceOfLoan=$loanConfig->where('loanProductId', $loanProductInfo->id)->min('ledgerIdForRiskInsurance');
                                    ?>
                                        <tr>
                                            <td class="name">{{$fundOrgArr[$loanProductInfo->fundingOrganizationId]}}</td>
                                            <td class="name">{{$loanProductInfo->name}}</td>
                                            <td>
                                                <input type='hidden' name='loanProductIdForLoan[]' value='{{$loanProductInfo->id}}'>
                                                <input type='text' name='principalCodeForLoan[]'  class='form-control ledgerCode input-sm' value='{{$ledgerCodeArr[$ledgerIdForPrincipalOfLoan]}}' autocomplete='off'>
                                            </td>
                                            <td>
                                                <input type='text' name='interestCodeForLoan[]'  class='form-control ledgerCode input-sm' value='{{$ledgerCodeArr[$ledgerIdForInterestOfLoan]}}' autocomplete='off'>
                                            </td>
                                            <td>
                                                <input type='text' name='riskInsuranceCodeForLoan[]'  class='form-control ledgerCode input-sm' value='{{$ledgerCodeArr[$ledgerIdForRiskInsuranceOfLoan]}}' autocomplete='off'>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12">

                            <h4 style="color: black; "><u> Savings </u></h4>
                            <table class="table table-striped table-bordered" id="autoVouchersTable2" style="width:100%; color:black; margin: 20px 0px;">
                                <thead>
                                    <tr>
                                        <th width="10%">Funding Org</th>
                                        <th width="10%">Loan Primary Product</th>
                                        <th width="">Savings Product</th>
                                        <th width="15%">Principal Code</th>
                                        <th width="15%">Interest Code</th>
                                        <th width="15%">Interest Provision</th>
                                        <th width="15%">Unsettled Claim</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $loanProductOption = collect($loanProductOption);
                                        $index = 0;
                                        // $loanProductOptionKeys = $loanProductOption->keys();
                                        $currentLoanProductId = $loanProductOption[0]->id;
                                        $isLoanProductChanged = 0;
                                                    $rowSpan = count($savingsProductOption);
                                    ?>
                                    @foreach ($loanProductOption as $loanProductInfo)
                                        @foreach ($savingsProductOption as$savingsProductId => $savingsProductName)
                                            <?php
                                                if ($currentLoanProductId!=$loanProductInfo->id) {
                                                    $currentLoanProductId = $loanProductInfo->id;
                                                    $isLoanProductChanged = 1;
                                                }
                                                else{
                                                    $isLoanProductChanged = 0;
                                                }

                                                $ledgerIdForPrincipalOfSaving=$savingsConfig->where('loanProductId', $loanProductInfo->id)->where('savingProductId', $savingsProductId)->min('ledgerIdForPrincipal');
                                                $ledgerIdForInterestOfSaving=$savingsConfig->where('loanProductId', $loanProductInfo->id)->where('savingProductId', $savingsProductId)->min('ledgerIdForInterest');
                                                $ledgerIdForInterestProvisionOfSaving=$savingsConfig->where('loanProductId', $loanProductInfo->id)->where('savingProductId', $savingsProductId)->min('ledgerIdForInterestProvision');
                                                $ledgerIdForUnsettledClaimOfSaving=$savingsConfig->where('loanProductId', $loanProductInfo->id)->where('savingProductId', $savingsProductId)->min('ledgerIdForUnsettledClaim');
                                            ?>

                                            <tr>
                                                @if ($index==0 || $isLoanProductChanged==1)
                                                    <td class="name" rowspan="{{$rowSpan}}">{{$fundOrgArr[$loanProductInfo->fundingOrganizationId]}}</td>
                                                    <td class="name" rowspan="{{$rowSpan}}">{{$loanProductInfo->name}}</td>
                                                @endif
                                                <td class="name">{{$savingsProductName}}</td>
                                                <td>
                                                    <input type='hidden' name='loanProductIdForSaving[]' value='{{$loanProductInfo->id}}'>
                                                    <input type='hidden' name='savingProductIdForSaving[]' value='{{$savingsProductId}}'>
                                                    <input type='text' name='principalCodeForSaving[]'  class='form-control ledgerCode input-sm' value='{{$ledgerCodeArr[$ledgerIdForPrincipalOfSaving]}}' autocomplete='off'>
                                                </td>
                                                <td>
                                                    <input type='text' name='interestCodeForSaving[]'  class='form-control ledgerCode input-sm' value='{{$ledgerCodeArr[$ledgerIdForInterestOfSaving]}}' autocomplete='off'>
                                                </td>
                                                <td>
                                                    <input type='text' name='interestProvisionForSaving[]'  class='form-control ledgerCode input-sm' value='{{$ledgerCodeArr[$ledgerIdForInterestProvisionOfSaving]}}' autocomplete='off'>
                                                </td>
                                                <td>
                                                    <input type='text' name='unsettledClaimForSaving[]'  class='form-control ledgerCode input-sm' value='{{$ledgerCodeArr[$ledgerIdForUnsettledClaimOfSaving]}}' autocomplete='off'>
                                                </td>
                                            </tr>
                                            <?php $index++; ?>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12">

                            <h4 style="color: black; "><u> Insurence & SKT Configuratio </u></h4>
                            <table class="table table-striped table-bordered" id="autoVouchersTable3" style="width:100%; color:black; margin-bottom: 20px; ">
                                <thead>
                                    <tr>
                                        <th rowspan="2" width="15%">Funding Organization</th>
                                        <th rowspan="2">Loan Primary Product</th>
                                        <th colspan="2">Insurence</th>
                                        <th rowspan="2" width="15%">SKT Amount Code</th>
                                    </tr>
                                    <tr>                                        
                                        <th width="15%">Code1</th>
                                        <th width="15%">Code2</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($loanProductOption as$key => $loanProductInfo)
                                    <?php

                                        $ledgerIdForInsurence1OfLoan=$sktConfig->where('loanProductId', $loanProductInfo->id)->min('ledgerIdForInsurence1');
                                        $ledgerIdForInsurence2OfLoan=$sktConfig->where('loanProductId', $loanProductInfo->id)->min('ledgerIdForInsurence2');
                                        $ledgerIdForSktAmountOfLoan=$sktConfig->where('loanProductId', $loanProductInfo->id)->min('ledgerIdForSktAmount');
                                    ?>
                                        <tr>
                                            <td class="name">{{$fundOrgArr[$loanProductInfo->fundingOrganizationId]}}</td>
                                            <td class="name">{{$loanProductInfo->name}}</td>
                                            <td>
                                                <input type='hidden' name='loanProductIdForSKT[]' value='{{$loanProductInfo->id}}'>
                                                <input type='text' name='insurenceCode1ForSKT[]'  class='form-control ledgerCode input-sm' value='{{$ledgerCodeArr[$ledgerIdForInsurence1OfLoan]}}' autocomplete='off'>
                                            </td>
                                            <td>
                                                <input type='text' name='insurenceCode2ForSKT[]'  class='form-control ledgerCode input-sm' value='{{$ledgerCodeArr[$ledgerIdForInsurence2OfLoan]}}' autocomplete='off'>
                                            </td>
                                            <td>
                                                <input type='text' name='sktAmountCodeForSKT[]'  class='form-control ledgerCode input-sm' value='{{$ledgerCodeArr[$ledgerIdForSktAmountOfLoan]}}' autocomplete='off'>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>

                    
                    <div class="form-group">
                        {!! Form::label('submit', ' ', ['class' => 'col-sm-7 control-label']) !!}
                        <div class="col-sm-5 text-right">
                            {!! Form::submit('Submit', ['id' => 'submitButton', 'class' => 'btn btn-info']) !!}
                            {{ Form::reset('Reset', ['class' => 'btn btn-warning']) }}
                            <a href="{{url('viewAutoVoucherConfig/')}}" class="btn btn-danger closeBtn">Close</a>
                            <span id="success" style="color:green; font-size:16px;" class="pull-right"></span>
                        </div>
                    </div>
                    {!! Form::close()  !!}

                </div>

            </div>
        <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>


<script type="text/javascript">

$(document).ready(function(){
   

    var csrf = "{{csrf_token()}}";


    var childLedgerId="{{json_encode($childLedgerIdsArray)}}";
    childLedgerId=childLedgerId.replace("[","").replace("]","");

    var childLedgerIdsArray=childLedgerId.split(",");

    $(document).on('keyup','.ledgerCode',function(){
        var ledgerCode=$(this).val();
        if (ledgerCode.length>=5) {
            // alert(principalCode);
            if (!childLedgerIdsArray.includes(ledgerCode)) {
                // alert("Invalid Code");
                toastr.error($(this).val()+" Is Invalid Ledger Code");
                $(this).val('');
            }
        }
    });


// ==================================Submit==================================
    $('form').submit(function( event ) {
        event.preventDefault();

        // alert($(this).serialize());
        
        $("#submitButton").prop("disabled", true);

        var ledgerCodeCheckArray = new Array();

        $("#autoVouchersTable1 tbody tr").each(function(){            
            $(this).find('.ledgerCode').each(function() {
                if ($(this).val()!="") {
                    ledgerCodeCheckArray.push($(this).val());
                    // alert($(this).val());
                }                
            });
        });
        $("#autoVouchersTable2 tbody tr").each(function(){            
            $(this).find('.ledgerCode').each(function() {
                if ($(this).val()!="") {
                    ledgerCodeCheckArray.push($(this).val());
                    // alert($(this).val());
                }                
            });
        });
        $("#autoVouchersTable3 tbody tr").each(function(){
            $(this).find('.ledgerCode').each(function() {
                if ($(this).val()!="") {
                    ledgerCodeCheckArray.push($(this).val());
                    // alert($(this).val());
                }                
            });
        });
        
        var ledgerCodeFlag=true;
        $.each(ledgerCodeCheckArray, function(index1, value1){
            if (!childLedgerIdsArray.includes(value1)) {
                ledgerCodeFlag=false;
                // alert(index1+"-"+value1);
                toastr.error(value1+" Is Invalid Ledger Code. Can Not Submit.");
                $("#submitButton").prop("disabled", false);
                return false;
            }
        });
        
        if (!ledgerCodeFlag) {
            return false;   //can not submit if ledgerCode is invalid
        }


        $.ajax({
             type: 'post',
             url: './addAutoVoucherConfigItem',
             data: $(this).serialize(),
             dataType: 'json',
            success: function( _response ){

                // alert(JSON.stringify(_response));

                if (_response.responseTitle=='Success!') {
                    toastr.success(_response.responseText, _response.responseTitle, opts);
                    
                    setTimeout(function(){
                        location.reload();
                    }, 3000);
                }else if (_response.responseTitle=='Warning!') {
                    toastr.warning(_response.responseText, _response.responseTitle, opts);
                }

            },
            error: function( _response ){
                // Handle error
                // alert(_response.errors);
                alert('_response.errors');
            }
        });     //ajax
    });     //submit


  
});
    
</script> 
 

@endsection 