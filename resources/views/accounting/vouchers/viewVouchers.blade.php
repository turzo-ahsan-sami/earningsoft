@extends('layouts/acc_layout')
@section('title', '| Voucher')
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
@php
    $branch = DB::table('gnr_branch')->where('id',Auth::user()->branchId)->select('id','branchCode')->first();
    $userBranchCode = $branch->branchCode;
@endphp
<?php
$userId=Auth::user()->id;
$userBranchId=Auth::user()->branchId;
// echo $userBranchId;

// echo $userId;
// echo $userBranchId;
// echo $page;
// echo "checkFirstLoad $checkFirstLoad";
// echo "<br/>user_branch_id $user_branch_id";
?>
<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
        <div class="panel panel-default" style="background-color:#708090;">
            <div class="panel-heading"  style="padding-bottom:0px">
                <div class="panel-options">
                    <a href="{{url('addVoucher/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Voucher</a>
                </div>
                <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">VOUCHER LISTS</h3>
            </div>
            <div class="panel-body panelBodyView">


                <!-- Filtering Start-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">

                            {{-- <div class="col-md-2"> --}}
                                {{-- <div class="row"> --}}

                                    {!! Form::open(array('url' => 'viewVoucher/', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'viewVoucherForm', 'method'=>'get')) !!}
                                    <input type="hidden" name="checkFirstLoad" value="1">

                                    {{-- @if($user_branch_id==1) --}}
                                    <div class="col-md-1" >
                                        <div class="form-group" style="">
                                            {!! Form::label('', 'Project:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                <select class="form-control" name="projectId" id="projectId">
                                                    <option value="">All</option>
                                                    @foreach ($projects as $project)
                                                        <option value={{$project->id}} @if($project->id==$projectSelected){{"selected=selected"}}@endif >{{str_pad($project->projectCode,3,"0",STR_PAD_LEFT)." - ".$project->name}}</option>
                                                        {{-- <option value={{$project->id}}>{{$project->projectCode." - ".$project->name}}</option> --}}
                                                    @endforeach
                                                </select>
                                                <p id='projectIde' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- @endif --}}

                                    {{-- @if($user_branch_id==1) --}}
                                    <div class="col-md-1">
                                        <div class="form-group" style="">
                                            {!! Form::label('', 'Branch:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                <select class="form-control" name="branchId" id="branchId">
                                                    <option value=""  @if($branchSelected==""){{"selected=selected"}}@endif >All (With HeadOffice)</option>
                                                    {{-- <option value="0"  @if($branchSelected===0){{"selected=selected"}}@endif >All (With Out HeadOffice)</option> --}}
                                                    @foreach ($branches as $branch)
                                                        {{-- <option value={{$branch->id}}>{{$branch->name}}</option> --}}
                                                        <option value={{$branch->id}}  @if($branch->id==$branchSelected){{"selected=selected"}}@endif >{{str_pad($branch->branchCode,3,"0",STR_PAD_LEFT)." - ".$branch->name}}</option>
                                                    @endforeach
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    {{-- @endif --}}

                                    {{-- @if($user_branch_id==1) --}}
                                    <div class="col-md-1" hidden>
                                        <div class="form-group" style="">
                                            {!! Form::label('', 'Pro. Type:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                <select class="form-control" name="projectTypeId" id="projectTypeId">
                                                    <option value="">All</option>
                                                    @foreach ($projectTypes as $projectType)
                                                        <option value={{$projectType->id}} @if($projectType->id==$projectTypeSelected){{"selected=selected"}}@endif >{{str_pad($projectType->projectTypeCode,3,"0",STR_PAD_LEFT)." - ".$projectType->name}}</option>
                                                        {{-- <option value={{$projectType->id}}>{{$projectType->projectTypeCode." - ".$projectType->name}}</option> --}}
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- @endif --}}

                                    <div class="col-md-1">
                                        <div class="form-group" style="">
                                            {!! Form::label('', 'Vou. Type:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                <select class="form-control" name="voucherTypeId" id="voucherTypeId">
                                                    <option value="">All</option>
                                                    @foreach ($voucherTypes as $voucherType)
                                                        <option value={{$voucherType->id}}>{{$voucherType->shortName}}</option>
                                                    @endforeach
                                                </select>
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
                            <th style="width:5.5%">Status</th>
                            <th style="width:7%" class="">Action</th>

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
                                $pagebumber = (int)$_GET['page'];
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
                            @php
                                $debitAmount = DB::table('acc_voucher_details')->where('voucherId', $voucher->id)->value('debitAcc');
                                $creditAmount = DB::table('acc_voucher_details')->where('voucherId', $voucher->id)->value('creditAcc');
                                $ftFrom = DB::table('acc_voucher_details')->where('voucherId', $voucher->id)->value('ftFrom');
                            @endphp

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
                                    $userIdOfpreBy=DB::table('users')->where('emp_id_fk', $voucher->prepBy)->value('emp_id_fk');
                                ?>
                                    {{DB::table('gnr_employee')->where('id', $userIdOfpreBy)->value('name')}}</td>
                                <td>
                                    @if ($voucher->authBy==0)
                                        <i style="color:#F00" class="fa fa-dot-circle-o" aria-hidden="true"></i>
                                    @else
                                        <i style="color:#72A230 " class="fa fa-check" aria-hidden="true"></i>
                                    @endif
                                </td>

                                <td class="text-center">
                                    {{-- {{$userId."=".$voucher->branchId}} --}}

                                    {{-- view Voucher details --}}
                                    <a href="{{url('printVoucher/'.encrypt($voucher->id))}}" target="blank" >
                                        <span class="fa fa-eye"></span>
                                    </a>
                                    &nbsp;
                                    @php
                                        $limit = \Carbon\Carbon::parse('2020-02-29');
                                        $voucherDate = \Carbon\Carbon::parse($voucher->voucherDate);
                                        if($voucherDate->lessThanOrEqualTo($limit)){
                                            $access = 0;
                                        }
                                        else {
                                            $access = 1;
                                        }
                                    @endphp

                                    @if ($voucher->authBy==0)
                                        {{-- Edit Voucher --}}
                                        @if ($voucher->voucherTypeId==5)

                                            <a href="{{url('editFTVoucher/'.encrypt($voucher->id))}}" class="<?php if($userBranchId!=$ftFrom || $debitAmount == $creditAmount || $voucher->vGenerateType == 1 || $access == 0){ echo '';}?> ">
                                            {{-- <a href="{{url('editFTVoucher/'.encrypt($voucher->id))}}" > --}}
                                        {{-- @elseif($userBranchId == 1 && $debitAmount == $creditAmount)
                                            <a href="{{url('editFTVoucher/'.encrypt($voucher->id))}}" class=""> --}}
                                        @else
                                            <a href="{{url('editVoucher/'.encrypt($voucher->id))}}"  class="
                                                <?php
                                                if ($voucher->vGenerateType == 1) {
                                                    echo 'disabled';
                                                }
                                                else {
                                                    if($userBranchId == $voucher->branchId || $userBranchId == 1){
                                                        echo '';
                                                    }else{
                                                        echo 'disabled';
                                                    }
                                                }

                                                ?>
                                            ">
                                        @endif
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                        &nbsp;

                                        {{-- Delete Voucher --}}


                                        @if ($voucher->voucherTypeId==5 || $voucher->ds==1)
                                            <a id="deleteIcone" href="javascript:;" class="delete-modal <?php if(( $userBranchId!=$ftFrom) || ($debitAmount == $creditAmount || $voucher->vGenerateType == 1 || $access == 0)){ echo 'disabled';}?> " data-id="{{$voucher->id}}" data-vouchertypeid="{{$voucher->voucherTypeId}}">
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </a>
                                        @else
                                            <a id="deleteIcone" href="javascript:;" class="delete-modal <?php if(($debitAmount == $creditAmount || $voucher->vGenerateType == 1)){ echo 'disabled';}?> " data-id="{{$voucher->id}}" data-vouchertypeid="{{$voucher->voucherTypeId}}">
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </a>
                                        @endif

                                    @else
                                        {{-- Edit Voucher --}}
                                        @if ($voucher->voucherTypeId==5 || $voucher->ds==1 || $voucher->vGenerateType == 1)
                                            <a href="{{url('editFTVoucher/'.encrypt($voucher->id))}}" class="disabled">
                                            {{-- <a href="{{url('editFTVoucher/'.encrypt($voucher->id))}}" > --}}
                                        @else
                                            <a href="{{url('editVoucher/'.encrypt($voucher->id))}}" class="disabled">
                                        @endif
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                        &nbsp;

                                        {{-- Delete Voucher --}}
                                        <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$voucher->id}}" data-vouchertypeid="{{$voucher->voucherTypeId}}">
                                            <span class="glyphicon glyphicon-trash"></span>
                                        </a>
                                    @endif
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

                <div class="deleteContent" style="padding-bottom:20px;">
                    <h4>You are about to delete this item. This procedure is irreversible !</h4>
                    <h4>Do you want to proceed ?</h4>
                    <span class="hidden id "></span>
                    <span class="hidden vouchertypeid"></span>
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
        var userBranchId = "{{ Auth::user()->branchId }}";
        var userBranchCode = "{{ $branch->branchCode }}";
        var projectId = $('#projectId').val();
        var branchId = $('#branchId').val();

        if (userBranchCode == 0) {
            getProjectTypesNBranches(projectId);
        }

        getChildrenLedgers(projectId, branchId);
        $('#projectId').change(function (event){
            var projectId = $('#projectId').val();
            //alert(projectId);
            getProjectTypesNBranches(projectId);
            var branchId = $('#branchId').val();
            getChildrenLedgers(projectId, branchId);
        });

        function getProjectTypesNBranches(projectId) {

            var csrf = "{{ csrf_token() }}";
            // alert(projectId);

            if (projectId == 0) {
                $("#branchId").empty();
                $("#branchId").append('<option value="">Select Branch</option>');
              

                $("#filProjectType").empty();
                $('#filProjectType').append("<option value='0'>Select Project</option>");
            }
            else {
                $.ajax({
                    type: 'post',
                    url: "./getProjectTypesNBranches",
                    data: {projectId: projectId , _token: csrf},
                    dataType: 'json',
                    success: function (data){
                        $("#filProjectType").empty();
                        // $('#filProjectType').append("<option value='0'>All</option>");
                        $.each(data['projectTypes'], function( key,obj){
                            $('#filProjectType').append("<option value='"+obj.id+"'>"+obj.name+"</option>");
                        });

                        $("#branchId").empty();
                        $("#branchId").append('<option value="">All(With HO)</option>');
                        // $("#branchId").append('<option value="0">All Branches</option>');
                        $("#branchId").append('<option value="{{ $userBranchData->id }}">{{ $userBranchData->nameWithCode }}</option>');

                        $.each(data['branches'], function(index,val){
                            $('#branchId').append("<option value='"+index+"'>"+val+"</option>");
                        });

                    },
                    error:  function (data){

                    }
                });
            }
        }
        
        function getChildrenLedgers(projectId, branchId) {
            var csrf = "{{ csrf_token() }}";
            // alert(projectId, branchId);

            $.ajax({
                type: 'post',
                url: "./getChildrenLedgers",
                data: {projectId: projectId, branchId: branchId, _token: csrf},
                dataType: 'json',
                success: function (data){

                    $("#ledgerId").empty();
                    $("#ledgerId").append('<option value="">Select Ledger</option>');
                    $.each(data, function(index, obj){
                        $('#ledgerId').append("<option value='"+obj.id+"'>"+obj.code+' - '+obj.name+"</option>");
                    });

                },
                error:  function (data){

                }
            });
        }
    });
</script>
<script type="text/javascript">
$(document).ready(function(){

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
            yearRange : "2010:c",
            minDate: new Date(2010, 07 - 1, 01),
            // maxDate: "dateToday",
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
            yearRange : "2010:c",
            // maxDate: "dateToday",
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

    // $("#projectId").change(function () {
    //     $('#projectIde').hide();
    //     var projectId = this.value;

    //     var csrf = "<?php echo csrf_token(); ?>";
    //     $.ajax({
    //         type: 'post',
    //         url: './getBranchNProjectTypeByProject',
    //         data: {projectId: projectId , _token: csrf},
    //         dataType: 'json',
    //         success: function (data) {
    //             // alert(JSON.stringify(data));
    //             var branchList=data['branchList'];
    //             var projectTypeList=data['projectTypeList'];

    //             $("#branchId").empty();
    //             $("#branchId").append('<option value="">All (With Head Office)</option>');
    //             $("#branchId").append('<option value="0">All (WithOut Head Office)</option>');
    //             $("#branchId").append('<option value="1">000 - Head Office</option>');
    //             //console.log(data);
    //             $.each(branchList, function( key,obj){
    //                 $('#branchId').append("<option value='"+obj.id+"'>"+pad(obj.branchCode,3)+" - "+obj.name+"</option>");
    //             });

    //             $("#projectTypeId").empty();
    //             //$("#projectTypeId").prepend('<option value="">All</option>');

    //             $.each(projectTypeList, function( key,obj){
    //                 $('#projectTypeId').append("<option value='"+obj.id+"'>"+obj.projectTypeCode+" - "+obj.name+"</option>");
    //             });

    //         },
    //         error: function(_response){
    //             alert("Error");
    //         }
    //     });
    // });
    // var voucherTypeId;
    $(document).on('click', '.delete-modal', function() {
         //if(softacc('deleteProductBrandItem')){
           // if(softacc('deleteFTVoucherItem') && softacc('deleteVoucherItem')){

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
                $('.actionBtn').addClass('delete');
                $('.modal-title').text('Delete Journal Voucher');
                $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
                $('.modal-dialog').css('width','30%');
                $('.id').text($(this).data('id'));
               // alert($(this).data('id'));
                $('.vouchertypeid').text($(this).data('vouchertypeid'));
               // alert($(this).data('vouchertypeid'));
                $('.deleteContent').show();
                $('.form-horizontal').hide();
                $('#footer_action_button2').show();
                $('#footer_action_button').hide();
                $('#myModal').modal('show');

            //}
         //}
    });


    $('.modal-footer').on('click', '.delete', function() {

        var _token = $('input[name=_token]').val();
        var id = $('.id').text();
        var voucherTypeId = $('.vouchertypeid').text();



       // alert(id);
       // alert(voucherTypeId);
        $.ajax({
            type: 'post',
            // url: './deleteFTVoucherItem',
            url: (voucherTypeId==5) ? './deleteFTVoucherItem' : './deleteVoucherItem',
            data: {'_token': _token,'id': id},

            success: function(data) {
                alert(JSON.stringify(data.responseText));
                //$('.item' + $('.id').text()).remove();
                location.reload();
            },
            error: function(data ){
                alert('Error');
            }
        });

    });


});//ready function end
</script>
{{-- <script src="{{asset('js/jquery-1.11.1.min.js')}}"></script> --}}

@endsection
