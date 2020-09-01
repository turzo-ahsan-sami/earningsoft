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
    .updateContent {
        color: #222;
    }

</style>

@php
$userId=Auth::user()->id;
$userBranchId=Auth::user()->branchId;
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
                                                {!! Form::select('filProject', $projects, $projectSelected ,['id'=>'filProject','class'=>'form-control input-sm','autocomplete'=>'off']) !!}
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
                                                {!! Form::select('filBranch', [0 =>'All'], $branchSelected ,['id'=>'filBranch','class'=>'form-control input-sm','autocomplete'=>'off']) !!}
                                                <p id='filBranchE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2" id="fiscalYearDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            <div class="col-md-12">
                                                {!! Form::label('', 'Fiscal Year', ['class' => 'control-label pull-left']) !!}
                                            </div>
                                            <div class="col-md-12">
                                                {!! Form::select('fiscalYear', $fiscalYears, $fiscalYearSelected, array('id'=>'fiscalYear','class'=>'form-control input-sm')) !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2" id="accountTypeDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            <div class="col-md-12">
                                                {!! Form::label('', 'Account Type', ['class' => 'control-label pull-left']) !!}
                                            </div>
                                            <div class="col-md-12">
                                                {!! Form::select('accountType', $accountTypes, $accountTypeSelected, array('id'=>'accountType', 'class'=>'form-control input-sm')) !!}
                                            </div>
                                        </div>
                                    </div>

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
                                        <th style="width:12%">Project Name</th>
                                        <th style="width:12%">Branch Name</th>
                                        <th style="width:12%">Fiscal Year</th>
                                        <th style="width:12%">Account Type</th>
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
                                            <td>{{ $projects[$budget->projectId] }}</td>
                                            <td>{{ $branches[$budget->branchId] }}</td>
                                            <td>{{ $fiscalYears[$budget->fiscalYearId] }}</td>
                                            <td>{{ $accountTypes[$budget->accountType] }}</td>
                                            <td>
                                                @if ($budget->status == 0)
                                                    <i style="color:#F00" class="fa fa-dot-circle-o" aria-hidden="true"></i>
                                                @else
                                                    <i style="color:#72A230 " class="fa fa-check" aria-hidden="true"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{-- aprrove or disapprove budget --}}

                                                    <a href="javascript:;" class="update-modal @if (Auth::user()->branchId != 1) disabled @endif" data-id="{{ $budget->id }}" data-status="{{ $budget->status }}">
                                                        @if($budget->status == 0)
                                                            <span class="glyphicon glyphicon-chevron-right"></span>
                                                        @else
                                                            <span class="glyphicon glyphicon-chevron-left"></span>
                                                        @endif
                                                    </a>


                                                &nbsp;
                                                {{-- edit budget --}}
                                                <a href="{{ url('editBudget/'.$budget->id) }}" class="@if($budget->status == 1) disabled @endif">
                                                    <span class="glyphicon glyphicon-edit"></span>
                                                </a>
                                                &nbsp;

                                                {{-- Delete budget --}}
                                                <a href="javascript:;" class="delete-modal" data-id="{{ $budget->id }}" class="@if($budget->status == 1) disabled @endif">
                                                    <span class="glyphicon glyphicon-trash"></span>
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

                        {{-- modal div for delete --}}
                        <div id="myDeleteModal" class="modal fade" style="margin-top:3%">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" style="clear:both"></h4>
                                    </div>
                                    <div class="modal-body">

                                        <div class="deleteContent" style="padding-bottom:20px;">
                                            <h4>You are about to delete this item. This procedure is irreversible !</h4>
                                            <h4>Do you want to proceed ?</h4>
                                            <span class="hidden delete-id"></span>
                                            {{-- <span class="hidden vouchertypeid"></span> --}}
                                        </div>
                                        <div class="modal-footer">
                                            {{-- <p id="MSGE" class="pull-left" style="color:red"></p>
                                            <p id="MSGS" class="pull-left" style="color:green"></p> --}}
                                            {!! Form::button('<span id=""></span>', ['class' => 'btn deleteBtn', 'id' => 'footer_delete_button'] ) !!}
                                            {!! Form::button('<span id=""></span>', ['class' => 'btn deleteBtn', 'data-dismiss' => 'modal', 'id' => 'footer_delete_button2'] ) !!}
                                            {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-warning', 'data-dismiss' => 'modal' , 'id' => 'footer_delete_button_dismiss'] ) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- modal div for status --}}
                        <div id="myUpdateModal" class="modal fade" style="margin-top:3%">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" style="clear:both"></h4>
                                    </div>
                                    <div class="modal-body">

                                        <div class="updateContent" style="padding-bottom:20px;">
                                            <h4>Do you want to proceed ?</h4>
                                            <span class="hidden update-id"></span>
                                            {{-- <span class="hidden vouchertypeid"></span> --}}
                                        </div>
                                        <div class="modal-footer">
                                            {{-- <p id="MSGE" class="pull-left" style="color:red"></p>
                                            <p id="MSGS" class="pull-left" style="color:green"></p> --}}
                                            {!! Form::button('<span id=""></span>', ['class' => 'btn updateBtn', 'id' => 'footer_update_button'] ) !!}
                                            {!! Form::button('<span id=""></span>', ['class' => 'btn updateBtn', 'data-dismiss' => 'modal', 'id' => 'footer_update_button2'] ) !!}
                                            {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-warning', 'data-dismiss' => 'modal' , 'id' => 'footer_update_button_dismiss'] ) !!}
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

<script type="text/javascript">

$(document).ready(function(){

    // var projectId = $('#filProject').val();
    // getProjectWiseBranches(projectId);

    $('#filProject').change(function (event){
        var projectId = $('#filProject').val();
        getProjectWiseBranches(projectId);
    });

    $('.delete-modal').on('click', function() {

        $('#MSGE').empty();
        $('#MSGS').empty();
        $('#footer_delete_button2').text(" Yes");
        $('#footer_delete_button2').removeClass('glyphicon glyphicon-check');
        $('#footer_delete_button_dismiss').text(" No");
        $('#footer_delete_button_dismiss').removeClass('glyphicon glyphicon-remove');
        $('.deleteBtn').removeClass('edit');
        $('.deleteBtn').removeClass('btn-success');
        $('.deleteBtn').addClass('btn-danger');
        $('.deleteBtn').addClass('delete');
        $('.modal-title').text('Delete Budget');
        $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
        $('.modal-dialog').css('width','30%');
        $('.delete-id').text($(this).data('id'));
        $('.deleteContent').show();
        $('.form-horizontal').hide();
        $('#footer_delete_button2').show();
        $('#footer_delete_button').hide();
        $('#myDeleteModal').modal('show');
    });


    $('#footer_delete_button2').on('click', function() {

        var _token = $('input[name=_token]').val();
        var id = $('.delete-id').text();

        $.ajax({
            type: 'post',
            url: './deleteBudgetItem',
            data: {'_token': _token, 'id': id},

            success: function(_response) {
                toastr.success(_response.responseText, opts);
                setTimeout(function(){
                    location.reload();
                }, 2000);
            },
            error: function(_response ){
                alert('Error');
            }
        });

    });

    $('.update-modal').on('click', function() {

        var status = $(this).attr('data-status');

        $('#MSGE').empty();
        $('#MSGS').empty();
        $('#footer_update_button2').text(" Yes");
        $('#footer_update_button2').removeClass('glyphicon glyphicon-check');
        $('#footer_update_button_dismiss').text(" No");
        $('#footer_update_button_dismiss').removeClass('glyphicon glyphicon-remove');
        $('.updateBtn').removeClass('edit');
        $('.updateBtn').removeClass('btn-success');
        $('.updateBtn').addClass('btn-danger');
        $('.updateBtn').addClass('update');
        if (status == 0) {
            $('.modal-title').text('Approve Budget');
        }
        else {
            $('.modal-title').text('Disapprove Budget');
        }

        $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
        $('.modal-dialog').css('width','30%');
        $('.update-id').text($(this).data('id'));
        $('.updateContent').show();
        $('.form-horizontal').hide();
        $('#footer_update_button2').show();
        $('#footer_update_button').hide();
        $('#myUpdateModal').modal('show');
    });


    $('#footer_update_button2').on('click', function() {

        var _token = $('input[name=_token]').val();
        var id = $('.update-id').text();

        $.ajax({
            type: 'post',
            url: './updateBudgetItem',
            data: {'_token': _token, 'id': id},

            success: function(_response) {
                toastr.success(_response.responseText, opts);
                setTimeout(function(){
                    location.reload();
                }, 2000);
            },
            error: function(_response ){
                alert('Error');
            }
        });

    });

});

function getProjectWiseBranches(projectId) {

    var csrf = "{{ csrf_token() }}";

    $.ajax({
        type: 'post',
        url: "./getProjectWiseBranches",
        data: {projectId: projectId , _token: csrf},
        dataType: 'json',
        success: function (data){

            $("#filBranch").empty();
            $("#filBranch").append("<option value='0'>All</option>");

            $.each(data['branches'], function(index,val){
                $('#filBranch').append("<option value='"+index+"'>"+val+"</option>");
            });

        },
        error:  function (data){

        }
    });

}

</script>

@endsection
