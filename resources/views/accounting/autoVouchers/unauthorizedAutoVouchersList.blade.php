@extends('layouts/acc_layout')
@section('title', '| Unauthorized Auto Voucher')
@section('content')

<style> 
#viewVoucherForm select, #viewVoucherForm input{
    height:30px; 
    border-radius: 5px;
    cursor: pointer;
}
.disabled {
    pointer-events: none;
    cursor: default;
    opacity: 0.6;
}
#voucherViewTable > thead >tr> th{
    padding:5px;
}
.form-group{
    color: black;
    font-size: 11px;
}
.form-control {
    padding: 5px;
    font-size: 11px;
}

</style>

<?php
$userId=Auth::user()->id;
$userBranchId=Auth::user()->branchId;

    // echo $userId;
    // echo $userBranchId;
    // echo $page;
    // echo "checkFirstLoad $checkFirstLoad";
    // echo "<br/>user_branch_id $user_branch_id";
    // echo "<pre>";
    // echo "projects";print_r($projects);
    // echo "branches";print_r($branches); 
    // echo "projectTypes";print_r($projectTypes); 
    // echo "voucherTypes";print_r($voucherTypes);
    // echo "vouchersIdArr";print_r($vouchersIdArr);
    // echo "</pre>";

?>
<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
        <div class="panel panel-default" style="background-color:#708090;">
            <div class="panel-heading"  style="padding-bottom:0px">
                <div class="panel-options">
                    {{-- <a href="{{url('addVoucher/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Voucher</a> --}}
                    <a id="" href="javascript:;" class="btn btn-info pull-right addViewBtn unauthVoucher-modal <?php if(count($autoVouchers)==0 ){echo 'disabled';}?>" data-id="-1" >
                        <span>Authorize All Auto Vouchers <i class="fa fa-chevron-circle-left"></i> </span>
                    </a>
                </div>
                <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">UNAUTHORIZED AUTO VOUCHERS LIST</h3>
            </div>
            <div class="panel-body panelBodyView">


                <div>
                    <script type="text/javascript"></script>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered dt-responsive nowrap"  id="voucherViewTable" style="color:black; font-size: 11px;">
                        <thead>
                        <tr>
                            <th style="width:4%">SL#</th>
                            <th style="width:10%">Auto Voucher Date</th>
                            <th style="width:12%">Auto Voucher Code</th>
                            <th style="width:9%">Auto Voucher Type</th>
                            <th style="width:12%">Project Type</th>
                            <th style="width:8%">Amount</th>
                            <th style="width:">Global Narration</th>
                            <th style="width:10%">Entry By</th>
                        </tr>
                        {{ csrf_field() }}
                        </thead>
                        <tbody>


                        @if (!$autoVouchers->count())
                            <tr>
                                <td colspan="10">No Voucher Available In This Search Range</td>
                            </tr>
                        @endif

                        @foreach($autoVouchers as $key=>$autoVoucher)
                            <tr class="item{{$autoVoucher->id}}">

                                {{-- <td class="text-center slNo">{{++$no}}</td> --}}
                                <td>{{$key+1}}</td>
                                <td>{{date('d-m-Y',strtotime($autoVoucher->voucherDate))}}</td>                                        
                                <td>{{$autoVoucher->voucherCode}}</td>                                        
                                <td>
                                    {{DB::table('acc_voucher_type')->where('id',$autoVoucher->voucherTypeId)->value('name')}}
                                </td>
                                <td class='name' >
                                    {{DB::table('gnr_project_type')->where('id',$autoVoucher->projectTypeId)->value('name')}}
                                </td>
                                <td class="amount" style="padding-right: 5px; ">
                                    {{number_format( DB::table('acc_voucher_details')->where('voucherId', $autoVoucher->id)->sum('amount'), 2, '.', ',')}}
                                </td>
                                {{-- <td>{{$voucher->amount}}</td> --}}
                                <td class="name" style="padding-left: 5px;">{{$autoVoucher->globalNarration}}
                                </td>
                                <td class="name" style="padding-left: 5px;">
                                <?php 
                                    $userIdOfpreBy=DB::table('users')->where('id', $autoVoucher->prepBy)->value('emp_id_fk');
                                ?>
                                    {{DB::table('hr_emp_general_info')->where('id', $userIdOfpreBy)->value('emp_name_english')}}
                                </td>

                            </tr>
                        @endforeach                                
                        </tbody>
                    </table>
                    

                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<div id="myModal" class="modal fade" style="margin-top:3%">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="clear:both"></h4>
            </div>
            <div class="modal-body">
                
                <div class="infoContent" style="padding-bottom:20px;">
                    <h5>You are about to Authenticate this auto voucher. This procedure is irreversible !</h5>
                    <h4>Do you want to proceed ?</h4>
                    <span class="hidden id "></span>
                </div>
                <div class="modal-footer">
                    <p id="MSGE" class="pull-left" style="color:red"></p>
                    <p id="MSGS" class="pull-left" style="color:green"></p>
                    {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn', 'id' => 'footer_action_button'] ) !!}
                    {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn', 'data-dismiss' => 'modal', 'id' => 'footer_action_button2'] ) !!}

                    {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-warning', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_dismis'] ) !!}
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $(document).on('click', '.unauthVoucher-modal', function() {
            
        $('#MSGE').empty();
        $('#MSGS').empty();
        $('#footer_action_button2').text(" Yes");
        $('#footer_action_button2').removeClass('glyphicon glyphicon-check');
        //$('#footer_action_button').addClass('glyphicon-trash');
        $('#footer_action_button_dismis').text(" No");
        $('#footer_action_button_dismis').removeClass('glyphicon glyphicon-remove');
        $('.actionBtn').removeClass('edit');
        $('.actionBtn').removeClass('btn-success');
        $('.actionBtn').addClass('btn-danger');
        $('.actionBtn').addClass('authVoucher');
        $('.modal-title').text('Authenticate Auto Voucher');
        $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
        $('.modal-dialog').css('width','30%');
        $('.id').text($(this).data('id'));
       // alert($(this).data('id'));
       // alert($(this).data('vouchertypeid'));
        $('.infoContent').show();
        // $('.form-horizontal').hide();
        $('#footer_action_button2').show();
        $('#footer_action_button').hide();
        $('#myModal').modal('show');
    });

$('.modal-footer').on('click', '.authVoucher', function() {

        var moduleId={{json_encode($moduleId)}};

        $.ajax({
            url: './../authenticateAutoVoucherItem',
            type: 'POST',
            // dataType: 'json',
            data: {'moduleId': moduleId},
        })

    .done(function(data) {
        if (data.responseTitle=='Success!') {
                   toastr.success(data.responseText, data.responseTitle, opts);
                    
                setTimeout(function(){
                    location.reload();
                }, 3000);
            }
    })
    .fail(function() {
        alert('Error');
    })
    .always(function() {
        console.log("complete");
           
        });

});



</script>

@endsection
