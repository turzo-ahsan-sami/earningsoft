@extends('layouts/acc_layout')
@section('title', '| Year End Process')
@section('content')
@include('accounting/process/yearEndProcess/yearEndAjax')

<style type="text/css">
.disabled {
    pointer-events: none;
    cursor: default;
    opacity: 0.6;
}

</style>
@php
    //dd($fiscalYearInfo->name);
@endphp

<div class="row">
{{-- <div class="col-md-2"></div> --}}
<div class="col-md-12">
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8 fullbody">
        <div class="panel panel-default" style="background-color:#708090;">
        	<div class="panel-heading" style="padding-bottom:0px">
        		<div class="panel-options">
                    {{-- id="exeYearEnd" --}}
                    <a href="javascript:;" class="execute-modal btn btn-info pull-right addViewBtn" id="exeYearEnd"><i class="fa fa-chevron-circle-right" aria-hidden="true"  ></i> Execute Year End</a>
                </div>
                <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Year End Process</h3>
        	</div>
        	<div class="panel-body panelBodyView">

            {{-- Filtering --}}
                {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filterFormId', 'method'=>'get')) !!}
                <input id="checkFirstLoad" type="hidden" name="checkFirstLoad" value="">

                <div class="row">
                    <div class="col-md-12"  @if ($userBranchCode!=0) style="display: none;" @endif>

                   {{--  <div class="col-sm-3">
                        <div class="form-group">
                            <div class="col-sm-12">
                                {!! Form::label('', 'Company:', ['class' => 'control-label pull-left']) !!}
                            </div>
                            <div class="col-sm-12">
                                {!! Form::select('filCompany', $companyOption,  null , ['id'=>'filCompany','class'=>'form-control input-sm', 'required', 'placeholder'=> 'Select Company']) !!}
                            </div>
                        </div>
                    </div> --}}
                    
                    <div class="col-sm-3">
                        <div class="form-group">
                            <div class="col-sm-12">
                                {!! Form::label('', 'Branch:', ['class' => 'control-label pull-left']) !!}
                            </div>
                            <div class="col-sm-12">
                                {!! Form::select('filBranch', $branchOption, null ,['id'=>'filBranch','class'=>'form-control input-sm','required']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-1">
                        <div class="form-group">
                            {!! Form::label('', '', ['class' => 'control-label col-sm-12']) !!}
                            <div class="col-sm-12">
                                {!! Form::submit('Search', ['id' => 'reportSubmit', 'class' => 'btn btn-primary btn-xs', 'style'=>'margin-top: 20px']); !!}
                            </div>
                        </div>
                    </div>
                    </div>
                </div>

                {!! Form::close() !!}
            {{-- End Filtering --}}


            <div class="row">
                <div class="col-md-12"  id="reportingDiv">

                </div>
            </div>


        	</div>     {{-- panelBodyView --}}

        </div>
    </div>
    <div class="col-md-2"></div>
</div>
</div>
</div>


<div id="myModal" class="modal fade" style="padding-top:7%">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" style="clear:both"></h4>
        </div>
        <div class="modal-body">

            <div class="executeContent" style="padding-bottom:20px;">
                <h4>Do you want to proceed ?</h4>
            </div>

            <div class="deleteContent" style="padding-bottom:20px;">
                <h4>You are about to delete this item, this procedure is irreversible !</h4>
                <h5 id="deleteYear"> </h5>
                <h5 id="deleteBranch"> </h5>
                <h4>Do you want to proceed ?</h4>
                <span class="hidden id"></span>
            </div>
            <div class="modal-footer">
                <p id="MSGE" class="pull-left" style="color:red"></p>
                <p id="MSGS" class="pull-left" style="color:green"></p>
              {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn', 'id' => 'footer_action_button'] ) !!}
              {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn',  'data-dismiss' => 'modal', 'id' => 'footer_action_button2'] ) !!}

              {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-warning', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_dismis'] ) !!}

            </div>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">
$(document).ready(function() {


    $('#checkFirstLoad').val('0');
    $('#loadingModal').show();
    var serializeValue=$('#filterFormId').serialize();
    // alert(serializeValue);
    $("#reportingDiv").load('{{URL::to("./loadAccYearEndProcess")}}'+'?'+serializeValue);



    $(document).on('click','.pagination a', function(event){
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        getDayEnd(page);
        // console.log(page);
    });

    function getDayEnd(page){

        var serializeValue=$('#filterFormId').serialize();
        $.ajax({
            url: './loadAccYearEndProcess?'+serializeValue+'&page=' + page
        }).done(function(data){
            $('#reportingDiv').html(data);
            // alert('pageNo: '+page);
        });
    }


    $("form").submit(function( event ) {
        event.preventDefault();
        $("#exeYearEnd").removeClass('disabled');
        // alert("form");

        $('#checkFirstLoad').val('1');
        // alert($('#checkFirstLoad').val());

        var serializeValue=$(this).serialize();
        // alert(serializeValue);

        $('#loadingModal').show();
        $("#reportingDiv").load('{{URL::to("./loadAccYearEndProcess")}}'+'?'+serializeValue);


    });

    $(document).on('click', '.execute-modal', function() {

        // if(hasAccess('addAccYearEndProcessItem')){
             if(('addAccYearEndProcessItem')){

            $('.errormsg').empty();
            $('#MSGE').empty();
            $('#MSGS').empty();
            $('.actionBtn').removeClass('delete');
            $('#footer_action_button').text("Execute");
            $('#footer_action_button').addClass('glyphicon glyphicon-check');
            //$('#footer_action_button').removeClass('glyphicon-trash');
            $('#footer_action_button_dismis').text(" Close");
            $('#footer_action_button_dismis').addClass('glyphicon glyphicon-remove');
            $('.actionBtn').addClass('btn-success');
            $('.actionBtn').removeClass('btn-danger');
            $('.actionBtn').addClass('execute');
            $('.modal-title').text('Execute Year End');
            $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
            $('.modal-dialog').css('width','50%');
            $('.deleteContent').hide();
            $('.executeContent').show();
            // $('.form-horizontal').show();
            // $('#id').val($(this).data('id'));
            $('#footer_action_button2').hide();
            $('#footer_action_button').show();
            $('.actionBtn').removeClass('delete');


            var branchId = {{ $userBranchId }};
            var currentYear = "{{$fiscalYearInfo->name }}";
            // alert(branchId);
            if($("#filBranch :selected").val() == branchId){
                var branchName = $("#filBranch :selected").text();
                $(".executeContent").html("<h4>Do You Want To Execute Year End?</h4><h5>Year &nbsp;: "+currentYear+"</h5><h5>Branch : "+branchName+"</h5>");
            }

            $('#myModal').modal('show');

        }

    });

    $("#filBranch").change(function(event) {

        branchId = $(this).val();

        $.ajax({
            type: 'POST',
            url: './getBranchInfo',
            dataType: 'json',
            data: {branchId: branchId},
        })

        .done(function(branchInfo) {
            // alert(branchInfo.branchId);
            $(".executeContent").html();
            $(".executeContent").html("<h4>Do You Want To Execute Year End?</h4><h5>Year &nbsp;: "+branchInfo.currentYear+"</h5><h5>Branch : "+branchInfo.branchName+"</h5>");
        })
        .fail(function() {
            alert("error");
        });

    });


    $('.modal-footer').on('click', '.execute', function() {

        $('#loadingModal').show();
        $("#exeYearEnd").addClass('disabled');
        var branchId = $("#filBranch").val();
        var companyId = $("#filCompany").val();
        // alert("exeYearEnd"+companyId);

        $.ajax({
           url: './addAccYearEndProcessItem',
           type: 'POST',
           data: {branchId: branchId, companyId: companyId},
           dataType: 'json',
        })
        .done(function(data) {
            // alert('hello');

            // $('#loadingModal').hide();
            if (data.responseTitle=='Success!') {
                toastr.success(data.responseText, data.responseTitle, opts);
                    // $('#reportSubmit').trigger('click');
                    setTimeout(function(){
                        // alert('hello');
                        window.location.reload();

                    }, 2000);

            }
            else if(data.responseTitle=='Warning!'){
                $('#loadingModal').hide();
                toastr.warning(data.responseText, data.responseTitle, opts);
                setTimeout(function(){
                    // alert('hello');
                    $("#exeYearEnd").removeClass('disabled');

                }, 2000);
            }
            $('#myModal').modal('hide');
        })
        .fail(function() {
           alert("error");
        });

    });

    // $("#deleteYearEnd").prop('disable', 'true');
    // $(".deleteYearEnd").addClass('disabled');
    // alert($(".deleteYearEnd").html());
    // $('.deleteYearEnd').attr('style','pointer-events: none; cursor: not-allowed; opacity: 0.6;');

    $(document).on('click', '.delete-modal', function() {

        // if(hasAccess('deleteAccYearEndItem')){

            $('#MSGE').empty();
            $('#MSGS').empty();
            $('.actionBtn').removeClass('execute');
            $('#footer_action_button2').text(" Yes");
            $('#footer_action_button2').removeClass('glyphicon glyphicon-check');
            //$('#footer_action_button').addClass('glyphicon-trash');
            $('#footer_action_button_dismis').text(" No");
            $('#footer_action_button_dismis').removeClass('glyphicon glyphicon-remove');
            $('.actionBtn').removeClass('btn-success');
            $('.actionBtn').addClass('btn-danger');
            $('.actionBtn').addClass('delete');
            $('.modal-title').text('Delete Year End');
            $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
            $('.modal-dialog').css('width','50%');
            // alert($(this).data('id'));
            $('.id').text($(this).data('id'));
            $('#deleteBranch').text('Branch: '+$(this).data('branchname'));
            $('#deleteYear').text('Year : '+$(this).data('year'));
            $('.executeContent').hide();
            $('.deleteContent').show();
            // $('.form-horizontal').hide();
            $('#footer_action_button2').show();
            $('#footer_action_button').hide();
            $('#myModal').modal('show');
            // $("deleteYearEnd").addClass('disabled');
            // alert($("deleteYearEnd").html());
        // }

    });


    $('.modal-footer').on('click', '.delete', function() {

        $('#loadingModal').show();

        $.ajax({
            type: 'post',
            url: './deleteAccYearEndItem',
            data: {
                '_token': $('input[name=_token]').val(),
                'id': $('.id').text()
            },
            success: function(data) {

                if (data.responseTitle=='Success!') {
                    toastr.success(data.responseText, data.responseTitle, opts);
                    // alert($("#filCompany :selected").val());
                        // $('#reportSubmit').trigger('click');
                        setTimeout(function(){
                            window.location.reload();
                        }, 2000);

                }
                else if(data.responseTitle=='Warning!'){
                    toastr.warning(data.responseText, data.responseTitle, opts);
                    $('#loadingModal').hide();
                    setTimeout(function(){
                        // $("#exeYearEnd").removeAttr('style');
                    }, 2000);
                }
                $('#myModal').modal('hide');

            }
        });



    });





}); /*End Ready*/
</script>

@endsection
