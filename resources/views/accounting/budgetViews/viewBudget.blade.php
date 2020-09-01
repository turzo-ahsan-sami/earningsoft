@extends('layouts/acc_layout')
@section('title', '| Budget')
@section('content')

    <style>
    #viewBudgetForm select, #viewBudgetForm input{
        height:30px;
        border-radius: 5px;
        cursor: pointer;
    }
    .disabled {
        pointer-events: none;
        cursor: default;
        opacity: 0.6;
    }
    #budgetViewTable > thead >tr> th{
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
    .deleteContent {
        color: #222;
    } 
    .approveContent {
        color: #222;
    }

</style>

@php
$userId=Auth::user()->id;
$userBranchId=Auth::user()->branchId;
$branch = DB::table('gnr_branch')->where('id',Auth::user()->branchId)->select('id','branchCode')->first();
$userBranchCode = $branch->branchCode;
@endphp

<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">
                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading"  style="padding-bottom:0px">
                        <div class="panel-options">
                            <a href="{{ url('addBudget/') }}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Budget</a>
                        </div>
                        <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Budget Lists</h3>
                    </div>
                    <div class="panel-body panelBodyView">


                        <!-- Filtering Start-->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    {!! Form::open(array('url' => 'viewBudget/', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'viewBudgetForm', 'method'=>'get')) !!}
                                    {!! Form::hidden('checkFirstLoad', 1) !!}
                                    <div class="col-md-2" id="projectDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            <div class="col-md-12">
                                                {!! Form::label('', 'Project:', ['class' => 'control-label pull-left']) !!}
                                            </div>
                                            <div class="col-md-12">
                                                {!! Form::select('filProject', $projects, null ,['id'=>'filProject','class'=>'form-control input-sm','autocomplete'=>'off']) !!}
                                                <p id='filProjectE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="col-md-2" id="branchDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            <div class="col-md-12">
                                                {!! Form::label('', 'Branch:', ['class' => 'control-label pull-left']) !!}
                                            </div>

                                            <div class="col-md-12">
                                                {{-- {!! Form::select('branchId', 'All', null ,['id'=>'filBranch','class'=>'form-control input-sm','autocomplete'=>'off']) !!} --}}
                                                <select name="branchId" class="form-control input-sm" id="filBranch">
                                                    <option value="">Select Branch</option>
                                                </select>
                                                <p id='filBranchE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>
                                   <div class="col-md-2" id="accountTypeDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Account Type:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                <select class="form-control" name="filAccountType" id="filAccountType">
                                                   {{--  <option value="">All</option> --}}
                                                    @foreach ($accountTypes as $key => $name)
                                                        <option value={{ $key }}  >{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                                <p id='filAccountTypeE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2" id="fiscalYearDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            <div class="col-md-12">
                                                {!! Form::label('', 'Fiscal Year:', ['class' => 'control-label pull-left']) !!}
                                            </div>
                                            <div class="col-md-12">
                                                 {!! Form::select('fiscalYear', $fiscalYears, $fiscalYearSelected, array('class'=>'form-control input-sm', 'id' => 'fiscalYear')) !!}
                                                <p id='filFiscalYearE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>
                                   {{--  <div class="col-md-2" id="fiscalYearDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', ' Fiscal Year', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12" style="padding-top: 18px;">
                                                {!! Form::select('fiscalYear', $fiscalYears, $fiscalYearSelected, array('class'=>'form-control input-sm', 'id' => 'fiscalYear')) !!}
                                            </div>
                                        </div>
                                    </div> --}}

                                    <div class="col-md-1">
                                        <div class="form-group" style="">
                                            {!! Form::label('', '', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12" style="padding-top: 13%;">
                                                {!! Form::submit('Search', ['id' => 'budgetSearchSubmit', 'class' => 'btn btn-primary btn-xs']); !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2"></div>

                                    {!! Form::close()  !!}

                                    <div class="col-md-10"></div>
                                </div>
                            </div>
                        </div>
                        <!-- filtering end-->
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap"  id="budgetViewTable" style="color:black; font-size: 11px;">
                                <thead>
                                    <tr>
                                        <th style="width:2%">SL#</th>
                                        <th style="width:15%;text-align: center;">Project Name</th>
                                        <th style="width:15%;text-align: center;">Branch Name</th>
                                        <th style="width:7%;text-align: center;">Account Type</th>                                  
                                        <th style="width:20%">Fiscal Year</th>
                                        <th style="width:7%">Status</th>
                                        <th style="width:5%" class="">Action</th>
                                    </tr>
                                    {{ csrf_field() }}
                                </thead>
                                <tbody>
                                    @php
                                        if (!empty($_GET['page'])) {
                                            $pageNumber = (int)$_GET['page'];
                                        }
                                        else {
                                            $pageNumber = 1;
                                        }
                                        // dd($pageNumber);
                                        $no = ($pageNumber - 1) * 20;
                                    @endphp

                                    @if (!$budgets->count())
                                        <tr>
                                            <td colspan="10">No Budget Available In This Search Range</td>
                                        </tr>
                                    @endif

                                    @foreach($budgets as $budget)
                                        <tr class="item{{ $budget->id }}">
                                            <td>{{ ++ $no }}</td>
                                            <td style="text-align: left; padding-left: 5px;">{{ $projects[$budget->projectId] }}</td>
                                             <td style="text-align: left; padding-left: 5px;">{{ $branchLists[$budget->branchId] }}</td>
                                            <td>{{ $accountTypes[$budget->accountType]}}</td>
                                         
                                            <td>{{ $fiscalYears[$budget->fiscalYearId]}}</td>
                                           
                                           
                                            <td>
                                                @if ($budget->status == 0)
                                                    <i style="color:#F00" class="fa fa-dot-circle-o" aria-hidden="true"></i>
                                                @else
                                                    <i style="color:#72A230 " class="fa fa-check" aria-hidden="true"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{-- edit budget --}}
                                                <a href="{{ url('editBudget/'.$budget->id) }}">
                                                    <span class="glyphicon glyphicon-edit"></span>
                                                </a>
                                                &nbsp;

                                                {{-- Delete budget --}}
                                                <a href="javascript:;" class="delete-modal" data-id="{{ $budget->id }}" class="@if($budget->status == 1) disabled @endif">
                                                    <span class="glyphicon glyphicon-trash"></span>
                                                </a>
                                                 &nbsp;
                                                {{-- approve budget --}}
                                                <a href="javascript:;" class="approve-modal" data-id="{{ $budget->id }}" class="@if($budget->status == 1) disabled @endif">
                                                    <span class="fa fa-check"></span>
                                                </a>
                                              
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div style="text-align:right;">
                                {{ $budgets->links() }}
                            </div>

                        </div>

                        {{-- modal div --}}
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
                                            <span class="hidden id"></span>
                                            {{-- <span class="hidden vouchertypeid"></span> --}}
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

                        {{-- budget approve modal --}}
                         <div id="approveModal" class="modal fade" style="margin-top:3%">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" style="clear:both"></h4>
                                    </div>
                                    <div class="modal-body">

                                        <div class="approveContent" style="padding-bottom:20px;">
                                            <h4>You are about to approve this item. This procedure is irreversible !</h4>
                                            <h4>Do you want to proceed ?</h4>
                                            <span class="hidden budgetId"></span>
                                            {{-- <span class="hidden vouchertypeid"></span> --}}
                                        </div>
                                        <div class="modal-footer">
                                            <p id="MSGE" class="pull-left" style="color:red"></p>
                                            <p id="MSGS" class="pull-left" style="color:green"></p>
                                            {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn', 'id' => 'footer_action_approve_button'] ) !!}
                                            {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn', 'data-dismiss' => 'modal', 'id' => 'footer_action_approve_button2'] ) !!}
                                            {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-warning', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_approve_dismis'] ) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- get branch by projectId --}}
<script type="text/javascript">
    $(document).ready(function(){
        var userBranchId = "{{ Auth::user()->branchId }}";
        var userBranchCode = "{{ $branch->branchCode }}";
        var projectId = $('#filProject').val();
        var branchId = $('#filBranch').val();

        if (userBranchCode == 0) {
            getProjectTypesNBranches(projectId);
        }

        getChildrenLedgers(projectId, branchId);
        $('#filProject').change(function (event){
            var projectId = $('#filProject').val();
            //alert(projectId);
            getProjectTypesNBranches(projectId);
            var branchId = $('#filBranch').val();
            getChildrenLedgers(projectId, branchId);
        });

        function getProjectTypesNBranches(projectId) {

            var csrf = "{{ csrf_token() }}";
            // alert(projectId);

            if (projectId == 0) {
                $("#filBranch").empty();
                $("#filBranch").append('<option value="">Select Branch</option>');
                // $("#filBranch").append('<option value="{{ $userBranchData->id }}">{{ $userBranchData->nameWithCode }}</option>');

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

                        $("#filBranch").empty();
                        $("#filBranch").append('<option value="">All(With HO)</option>');
                        // $("#filBranch").append('<option value="0">All Branches</option>');
                        $("#filBranch").append('<option value="{{ $userBranchData->id }}">{{ $userBranchData->nameWithCode }}</option>');

                        $.each(data['branches'], function(index,val){
                            $('#filBranch').append("<option value='"+index+"'>"+val+"</option>");
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
        function pad (str, max) {
            str = str.toString();
            return str.length < max ? pad("0" + str, max) : str;
        }
        // $("#filProject").change(function () {
        //         $('#projectIde').hide();
        //         var projectId = this.value;
        //         //alert(projectId);
        //         var csrf = "<?php echo csrf_token(); ?>";
        //         $.ajax({
        //             type: 'post',
        //             url: './getBranchNProjectTypeByProject',
        //             data: {projectId: projectId , _token: csrf},
        //             dataType: 'json',
        //             success: function (data) {
        //                 //alert(JSON.stringify(data));
        //                 var branchList=data['branchList'];
        //                 var projectTypeList=data['projectTypeList'];

        //                 $("#filBranch").empty();
        //                 $("#filBranch").append('<option value="">All (With Head Office)</option>');
        //                 $("#filBranch").append('<option value="0">All (WithOut Head Office)</option>');
        //                 $("#filBranch").append('<option value="1">000 - Head Office</option>');
        //                 //console.log(branchList);
        //                 $.each(branchList, function( key,obj){
        //                     $('#filBranch').append("<option value='"+obj.id+"'>"+pad(obj.branchCode,3)+" - "+obj.name+"</option>");
        //                 });

        //                 $("#projectTypeId").empty();
        //                 //$("#projectTypeId").prepend('<option value="">All</option>');

        //                 $.each(projectTypeList, function( key,obj){
        //                     $('#projectTypeId').append("<option value='"+obj.id+"'>"+obj.projectTypeCode+" - "+obj.name+"</option>");
        //                 });

        //             },
        //             error: function(_response){
        //                 alert("Error");
        //             }
        //         });
        //     });
        $(document).on('click', '.delete-modal', function() {

            $('#MSGE').empty();
            $('#MSGS').empty();
            $('#footer_action_button2').text(" Yes");
            $('#footer_action_button2').removeClass('glyphicon glyphicon-check');
            $('#footer_action_button_dismis').text(" No");
            $('#footer_action_button_dismis').removeClass('glyphicon glyphicon-remove');
            $('.actionBtn').removeClass('edit');
            $('.actionBtn').removeClass('btn-success');
            $('.actionBtn').addClass('btn-danger');
            $('.actionBtn').addClass('delete');
            $('.modal-title').text('Delete Budget');
            $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
            $('.modal-dialog').css('width','30%');
            $('.id').text($(this).data('id'));
            $('.deleteContent').show();
            $('.form-horizontal').hide();
            $('#footer_action_button2').show();
            $('#footer_action_button').hide();
            $('#myModal').modal('show');
        });


        $('.modal-footer').on('click', '.delete', function() {

            var _token = $('input[name=_token]').val();
            var id = $('.id').text();

            $.ajax({
                type: 'post',
                url: './deleteBudgetItem',
                data: {'_token': _token, 'id': id},

                success: function(data) {
                    alert(data);
                    location.reload();
                },
                error: function(data ){
                    alert('Error');
                }
            });

        });

        //approve modal
        $(document).on('click', '.approve-modal', function() {   
            $('#MSGE').empty();
            $('#MSGS').empty();
            $('#footer_action_approve_button2').text(" Yes");
            $('#footer_action_approve_button2').removeClass('glyphicon glyphicon-check');
            $('#footer_action_button_approve_dismis').text(" No");
            $('#footer_action_button_approve_dismis').removeClass('glyphicon glyphicon-remove');
            $('.actionBtn').removeClass('edit');
            $('.actionBtn').removeClass('btn-success');
            $('.actionBtn').addClass('btn-danger');
            $('.actionBtn').addClass('approve');
            $('.modal-title').text('Approve Budget');
            $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
            $('.modal-dialog').css('width','30%');
            $('.budgetId').text($(this).data('id'));
            $('.approveContent').show();
            $('.form-horizontal').hide();
            $('#footer_action_approve_button2').show();
            $('#footer_action_approve_button').hide();
            $('#approveModal').modal('show');
        });

        $('.modal-footer').on('click', '.approve', function() {

            var _token = $('input[name=_token]').val();
            var budgetId = $('.budgetId').text();
            //alert(budgetId);

            $.ajax({
                type: 'post',
                url: './approveBudgetItem',
                data: {'_token': _token, 'budgetId': budgetId},

                success: function(data) {
                    alert(data);
                    location.reload();
                },
                error: function(data ){
                    alert('Error');
                }
            });

        });

    });

</script>

@endsection
