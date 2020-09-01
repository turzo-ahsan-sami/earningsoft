@extends('layouts/acc_layout')
@section('title', '| Unauthorized Voucher')
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
    $userBranchCode = Auth::user()->branchCode;

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
                    <a id="" href="javascript:;" class="btn btn-info pull-right addViewBtn authVoucher-modal <?php if(count($vouchersIdArr)==0 || ($userBranchId!=$branchSelected)){echo 'disabled';}?>" data-id="-1" >
                        <span>Authorized All Vouchers <i  class="fa fa-chevron-circle-right"></i> </span>
                    </a>
                </div>
                <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">UNAUTHORIZED VOUCHER LISTS</h3>
            </div>
            <div class="panel-body panelBodyView">

                <!-- Filtering Start-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">

                            {{-- <div class="col-md-2"> --}}
                                {{-- <div class="row"> --}}

                                    {!! Form::open(array('url' => 'unauthorizedVouchersList/', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'viewVoucherForm', 'method'=>'get')) !!}
                                    <input type="hidden" name="checkFirstLoad" value="1">

                                    @if($userBranchCode==0)
                                    <div class="col-md-1">
                                        <div class="form-group" style="">
                                            {!! Form::label('', 'Project:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                                                                
                                                {!! Form::select('projectId', $projectsOption, null ,['id'=>'projectId','class'=>'form-control input-sm', 'autocomplete'=>'off', 'autofocus']) !!}
                                                <p id='projectIde' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($userBranchCode==0)
                                    <div class="col-md-1">
                                        <div class="form-group" style="">
                                            {!! Form::label('', 'Branch:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                {!! Form::select('branchId', $branchesOption, null ,['id'=>'branchId','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($userBranchCode==0)
                                    <div class="col-md-1">
                                        <div class="form-group" style="">
                                            {!! Form::label('', 'Pro. Type:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                {!! Form::select('projectTypeId', $projectTypesOption, null ,['id'=>'projectTypeId','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus']) !!}                                                
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="col-md-1">
                                        <div class="form-group" style="">
                                            {!! Form::label('', 'Vou. Type:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                {!! Form::select('voucherTypeId', $voucherTypesOption, null ,['id'=>'voucherTypeId','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus']) !!}
                                                
                                                <p id='voucherTypeIde' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="form-group" style="">
                                            {!! Form::label('from', 'From:', ['class' => 'col-sm-12 control-label']) !!}
                                            <div class="col-sm-12">
                                                {!! Form::text('dateFrom', $startDateSelected, ['class' => 'form-control','style'=>'cursor:pointer', 'id' => 'dateFrom', 'readonly'])!!}
                                                <p id='dateFrome' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="form-group" style="">
                                            {!! Form::label('from', 'To:', ['class' => 'col-sm-12 control-label']) !!}
                                            <div class="col-sm-12">
                                                {!! Form::text('dateTo', $endDateSelected, ['class' => 'form-control','style'=>'cursor:pointer', 'id' => 'dateTo', 'readonly'])!!}
                                                <p id='dateToe' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="col-md-1"></div> --}}

                                    <div class="col-md-1">
                                        <div class="form-group" style="">
                                            {!! Form::label('', '', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12" style="padding-top: 13%;">
                                                {!! Form::submit('Search', ['id' => 'ledgerReportSubmit', 'class' => 'btn btn-primary btn-xs']); !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2"></div>

                                    {!! Form::close()  !!}
                                {{-- </div> --}}
                            {{-- </div>     --}}

                            {{-- end Div of ledgerSearch --}}

                            <div class="col-md-10"></div>
                        </div>
                    </div>

                </div>
                <!-- filtering end-->


                <div>
                    <script type="text/javascript"></script>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered dt-responsive nowrap"  id="voucherViewTable" style="color:black; font-size: 11px;">
                        <thead>
                        <tr>
                            <th style="width:4%">SL#</th>
                            <th style="width:7%">Voucher Date</th>
                            <th style="width:12%">Voucher Code</th>
                            <th style="width:9%">Voucher Type</th>
                            <th style="width:12%">Project Type</th>
                            <th style="width:8%">Amount</th>
                            <th style="width:">Global Narration</th>
                            <th style="width:7%">Entry By</th>
                            <th style="width:6%" class="">Action</th>

                            {{-- <th width="26">SL#</th>
                            <th>Project</th>
                            <th>Voucher Code</th>
                            <th>Project Type</th>
                            <th>Amount</th>
                            <th>Global Narration</th>
                            <th class="">Action</th> --}}
                        </tr>
                        {{ csrf_field() }}
                        </thead>
                        <tbody>
                        <?php
                            if (!empty($_GET['page'])) {
                                $pagebumber = (int)$_GET['page'] ;
                            }else{
                                $pagebumber=1;
                            }
                            $no= ($pagebumber-1)*100; 
                        ?>

                        @if (!$vouchers->count())
                            <tr>
                                <td colspan="10">No Voucher Available In This Search Range</td>
                            </tr>
                        @endif

                        @foreach($vouchers as $voucher)
                            <tr class="item{{$voucher->id}}">

                                {{-- <td class="text-center slNo">{{++$no}}</td> --}}
                                <td>{{++$no}}</td>
                                <td>{{date('d-m-Y',strtotime($voucher->voucherDate))}}</td>                                        
                                <td>{{$voucher->voucherCode}}</td>                                        
                                <td>
                                    {{DB::table('acc_voucher_type')->where('id',$voucher->voucherTypeId)->value('name')}}
                                </td>
                                <td class='name' >
                                    {{DB::table('gnr_project_type')->where('id',$voucher->projectTypeId)->value('name')}}
                                </td>
                                <td class="amount" style="padding-right: 5px; ">
                                    {{number_format( DB::table('acc_voucher_details')->where('voucherId', $voucher->id)->sum('amount'), 2, '.', ',')}}
                                </td>
                                {{-- <td>{{$voucher->amount}}</td> --}}
                                <td class="name" style="padding-left: 5px;">{{$voucher->globalNarration}}</td>
                                <td class="name" style="padding-left: 5px;">
                                <?php 
                                    $userIdOfpreBy=DB::table('users')->where('id', $voucher->prepBy)->value('emp_id_fk');
                                ?>
                                    {{DB::table('hr_emp_general_info')->where('id', $userIdOfpreBy)->value('emp_name_english')}}</td>
                                

                                <td class="text-center">
                                    {{-- authVoucher Voucher --}}
                                    <a href="javascript:;" class="authVoucher-modal <?php if($userBranchId!=$voucher->branchId && $userBranchCode!=0){ echo 'disabled';}?> " data-id="{{$voucher->id}}">
                                        <span style="font-size: 14px;color: #72A230;" class="fa fa-chevron-circle-right"></span>
                                    </a>
                                </td>   {{-- Action td --}}

                            </tr>
                        @endforeach                                
                        </tbody>
                    </table>
                    <div style="text-align:right;">
                        {{ $vouchers->appends(['projectSelected' => $projectSelected,'projectTypeSelected' => $projectTypeSelected,'branchSelected' => $branchSelected,'startDateSelected' => $startDateSelected,'endDateSelected' => $endDateSelected])->links() }}
                    </div>

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
                    <h5>You are about to Authenticate this voucher. This procedure is irreversible !</h5>
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
$(document).ready(function(){

    var projectSelected="{{$projectSelected}}";
    $("#projectId").val(projectSelected);
    // alert(projectSelected);
    // alert($("#projectId").val());

    var branchSelected="{{$branchSelected}}";
    $("#branchId").val(branchSelected);
    // alert(branchSelected);

    var projectTypeSelected="{{$projectTypeSelected}}";
    $("#projectTypeId").val(projectTypeSelected);

    var voucherTypeId="{{$searchedVoucherTypeId}}";
    $("#voucherTypeId").val(voucherTypeId);


    function toDate(dateStr) {
        var parts = dateStr.split("-");
        return new Date(parts[2], parts[1] - 1, parts[0]);
    }

    /* Date Range From */
    $("#dateFrom").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "2016:c",
            minDate: new Date(2016, 07 - 1, 01),
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function () {
                $('#dateFrome').hide();          
                $("#dateTo").datepicker("option","minDate",new Date(toDate($(this).val())));
                $( "#dateTo" ).datepicker( "option", "disabled", false );      
            }
        });
    /* Date Range From */       

    /* Date Range To */
    $("#dateTo").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "2016:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function () {
                $('#dateToe').hide();           
            }
        });
//$( "#dateTo" ).datepicker( "option", "disabled", true );
    /* End Date Range To */

    var dateFromData = $("#dateFrom").val();

     if (dateFromData!="") {
        $("#dateTo").datepicker("option","minDate",new Date(toDate(dateFromData)));
        //$("#dateTo").datepicker( "option", "disabled", false );  
    }


    function pad (str, max) {
        str = str.toString();
        return str.length < max ? pad("0" + str, max) : str;
    }

    $("#projectId").change(function () {
        $('#projectIde').hide();
        var projectId = this.value;
       
        var csrf = "<?php echo csrf_token(); ?>";
        $.ajax({
            type: 'post',
            url: './getBranchNProjectTypeByProject',
            data: {projectId: projectId , _token: csrf},
            dataType: 'json',
            success: function (data) {
                // alert(JSON.stringify(data)); 
                var branchList=data['branchList'];
                var projectTypeList=data['projectTypeList'];

                $("#branchId").empty();
                $("#branchId").append('<option value="">All (With Head Office)</option>');
                $("#branchId").append('<option value="0">All (WithOut Head Office)</option>');
                $("#branchId").append('<option value="1">000 - Head Office</option>');

                $.each(branchList, function( key,obj){
                    $('#branchId').append("<option value='"+obj.id+"'>"+pad(obj.branchCode,3)+" - "+obj.name+"</option>");
                });

                $("#projectTypeId").empty();
                $("#projectTypeId").prepend('<option value="">All</option>');

                $.each(projectTypeList, function( key,obj){
                    $('#projectTypeId').append("<option value='"+obj.id+"'>"+obj.projectTypeCode+" - "+obj.name+"</option>");
                });

            },
            error: function(_response){
                alert("Error");
            }
        }); 
    });

    $(document).on('click', '.authVoucher-modal', function() {
            
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
        $('.modal-title').text('Authenticate Voucher');
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

        var _token = $('input[name=_token]').val();
        var id = $('.id').text();

        if (id==-1) {
            // alert("All");
            var vouchersIdArr={{json_encode($vouchersIdArr)}};
        }else{
            // alert("else");
            var vouchersIdArr=[id];
        }
        // alert(vouchersIdArr);

        $.ajax({
            type: 'post',
            url: './authenticateVoucherItem',
            data: {'_token': _token,'vouchersIdArr': vouchersIdArr},

            success: function(_response) {
                // alert(JSON.stringify(_response));
                if (_response.responseTitle=='Success!') {
                    toastr.success(_response.responseText, _response.responseTitle, opts);
                    if (_response.updatedVoucher==1) {
                        $('.item' + $('.id').text()).remove();
                    }else{
                        setTimeout(function(){
                            location.reload();
                        }, 2000);                        
                    }
                    // location.reload();
                }else if (_response.responseTitle=='Warning!') {
                    toastr.warning(_response.responseText, _response.responseTitle, opts);
                }
                // location.reload();
            },
            error: function(_response ){
                alert('Error');
            }
        });
       
    });


});//ready function end
</script>
{{-- <script src="{{asset('js/jquery-1.11.1.min.js')}}"></script> --}}

@endsection
