@extends('layouts/acc_layout')
@section('title', '| Day End Process')
@section('content')

<style type="text/css">
.disabled {
    pointer-events: none;
    cursor: default;
    opacity: 0.6;

}
</style>


<div class="row">
{{-- <div class="col-md-2"></div> --}}
<div class="col-md-12">
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8 fullbody">
        <div class="panel panel-default" style="background-color:#708090;">
        	<div class="panel-heading" style="padding-bottom:0px">
        		<div class="panel-options">
                    {{-- id="exeDayEnd" --}}
                    <a href="javascript:;" class="execute-modal btn btn-info pull-right addViewBtn" id="exeDayEnd"><i class="fa fa-chevron-circle-right" aria-hidden="true"  ></i> Execute Day End</a>
                </div>
                <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Day End Process</h3>
        	</div>
        	<div class="panel-body panelBodyView">

                {{-- Filtering --}}
                {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filterFormId', 'method'=>'get')) !!}
                <input id="checkFirstLoad" type="hidden" name="checkFirstLoad" value="">

                <div class="row">
                    <div class="col-md-12">

                    <div class="col-sm-3" @if ($userBranchCode!=0) style="display: none;" @endif>
                        <div class="form-group">
                            <div class="col-sm-12">
                                {!! Form::label('', 'Branch:', ['class' => 'control-label pull-left']) !!}
                            </div>
                            <div class="col-sm-12">
                                {!! Form::select('filBranch', $branchOption, null ,['id'=>'filBranch','class'=>'form-control input-sm']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <div class="col-sm-12">
                                {!! Form::label('', 'Month:', ['class' => 'control-label pull-left']) !!}
                            </div>
                            <div class="col-sm-12">
                                {!! Form::select('filMonth', ['-1'=>'--All--']+$monthsOption, null ,['id'=>'filMonth','class'=>'form-control input-sm']) !!}
                                <p id='filMonthE' style="max-height:3px; color:red;"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <div class="col-sm-12">
                                {!! Form::label('', 'Year:', ['class' => 'control-label pull-left']) !!}
                            </div>
                            <div class="col-sm-12">
                                {!! Form::select('filYear', $yearsOption, null ,['id'=>'filYear','class'=>'form-control input-sm', 'required']) !!}
                                <p id='filYearE' style="max-height:3px; color:red;"></p>
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
                <h5 id="deleteDate"> </h5>
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



{{-- Delete Modal --}}

{{-- <div class="modal fade showModal" id="delete-confirmation-modal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h4 class="modal-title">Delete Confirmation</h4>
            <a class="close" href="#" data-dismiss="modal">X</a>
        </div>
        <div class="modal-content">
            <div class="modal-body">
                Are you sure to delete this record?
            </div>
        </div>
        <div class="modal-footer">
            <div class="form-group hidden">
                <div class="col-sm-6 col-sm-offset-2">
                {!! Form::text('id', null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text', 'readonly']) !!}
            </div>
            </div>
            <button id="yesBtn" type="button" class="btn btn-info animated slideInRight" style="margin-bottom:0!important;" >Yes</button>
            <button id="noBtn" type="button" class="btn btn-white animated slideInRight" data-dismiss="modal" style="margin-bottom:0!important;">No</button>
        </div>
    </div>
</div> --}}

{{-- End Delete Modal --}}


{{-- <link rel="stylesheet" href="{{ asset('css/mfn/style.css') }}"> --}}
{{-- <style type="text/css">
  #reportSubmit{
        font-size: 14px;
        margin-top: 20px;
    }
</style>
 --}}
<script type="text/javascript">
$(document).ready(function() {


    // alert($('#minibarDate').text());

    $('#checkFirstLoad').val('0');
    $('#loadingModal').show();
    var serializeValue=$('#filterFormId').serialize();
    // alert(serializeValue);
    $("#reportingDiv").load('{{URL::to("./loadAccDayEndProcess")}}'+'?'+serializeValue);



    $(document).on('click','.pagination a', function(event){
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        getDayEnd(page);
        // console.log(page);
    });

    function getDayEnd(page){

        var serializeValue=$('#filterFormId').serialize();
        $.ajax({
            url: './loadAccDayEndProcess?'+serializeValue+'&page=' + page
        }).done(function(data){
            $('#reportingDiv').html(data);
            // alert('pageNo: '+page);
        });
    }


    $("form").submit(function( event ) {
        event.preventDefault();
        $("#exeDayEnd").removeClass('disabled');
        // alert("form");

        $('#checkFirstLoad').val('1');
        // alert($('#checkFirstLoad').val());

        var serializeValue=$(this).serialize();
        // alert(serializeValue);

        $('#loadingModal').show();
        $("#reportingDiv").load('{{URL::to("./loadAccDayEndProcess")}}'+'?'+serializeValue);


    });



    /*exucute day end*/
    // $("#exeDayEnd").click(function(event) {
    //     // $("#exeDayEnd").prop("disabled", true);
    //     // $(this).attr('style','pointer-events: none; cursor: not-allowed; opacity: 0.6;');
    //     $("#exeDayEnd").addClass('disabled');
    //     var branchId = $("#filBranch").val();
    //     alert(branchId);
    //     $.ajax({
    //        url: './addAccDayEndProcessItem',
    //        type: 'POST',
    //        data: {branchId: branchId},
    //        dataType: 'json',
    //     })
    //     .done(function(data) {
    //         if (data.responseTitle=='Success!') {
    //             toastr.sauccess(data.responseText, data.responseTitle, opts);
    //             setTimeout(function(){
    //               location.reload();
    //             }, 2000);
    //         }
    //         else if(data.responseTitle=='Warning!'){
    //             toastr.warning(data.responseText, data.responseTitle, opts);
    //             setTimeout(function(){
    //             $("#exeDayEnd").removeClass('disabled');
    //             }, 2000);
    //         }
    //     })
    //     .fail(function() {
    //        alert("error");
    //     });

    // });
    /*end exucute day end*/




    // /*delete day end*/
    // $("#yesBtn").click(function(event) {
    //     var id = $("#id").val();
    //     $.ajax({
    //         // url: './deleteMfnDayEnd',
    //         type: 'POST',
    //         dataType: 'json',
    //         data: {id: id},
    //     })
    //     .done(function(data) {
    //         if (data.responseTitle=='Success!') {
    //           toastr.success(data.responseText, data.responseTitle, opts);
    //           setTimeout(function(){
    //               location.reload();
    //           }, 2000);
    //         }
    //         else if(data.responseTitle=='Warning!'){
    //           toastr.warning(data.responseText, data.responseTitle, opts);
    //           setTimeout(function(){
    //               $("#exeDayEnd").removeAttr('style');
    //           }, 2000);
    //         }
    //     })
    //     .fail(function() {
    //         alert("error");
    //     });

    // });
    // /*end delete day end*/

    // /*delete modal*/
    // $(document).on('click', '.deleteConfirmation', function(event) {
    //     event.preventDefault();
    //     $('#id').val($(this).data('id'));
    //     $("#delete-confirmation-modal").modal('show');
    // });
    // /*end delete modal*/








    $("#filBranch").change(function(event) {
        $("#dayEndTable").empty();
        $("#paginateDiv").hide();
        $("#exeDayEnd").addClass('disabled');
        branchId = $(this).val();

        $("#filYear").empty();

        $.ajax({
            url: './accDayEndGetYearsOption',
            type: 'POST',
            dataType: 'json',
            data: {branchId: branchId},
        })
        .done(function(yearsOption) {
            $.each(yearsOption, function(index, val) {
                 $("#filYear").append('<option value="'+index+'">'+val+'</option>');
            });
        })
        .fail(function() {
            alert("error");
        });

    });



    $(document).on('click', '.execute-modal', function() {
        //alert('asasas');

        // if(hasAccess('addAccDayEndProcessItem')){
        if(('addAccDayEndProcessItem')){

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
            $('.modal-title').text('Execute Day End');
            $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
            $('.modal-dialog').css('width','40%');
            $('.deleteContent').hide();
            $('.executeContent').show();
            // $('.form-horizontal').show();
            // $('#id').val($(this).data('id'));
            $('#footer_action_button2').hide();
            $('#footer_action_button').show();
            $('.actionBtn').removeClass('delete');

            $(".executeContent").html("<h4>Do You Want To Execute Day End?</h4><h5>Date&nbsp&nbsp&nbsp&nbsp&nbsp: "+$('#currentDate').val()+"</h5><h5>Branch : "+$("#filBranch :selected").text()+"</h5>");
            // alert($(".executeContent").html());
            $('#myModal').modal('show');
        }

    });

    $('.modal-footer').on('click', '.execute', function() {

        $('#loadingModal').show();
        $("#exeDayEnd").addClass('disabled');
        var branchId = $("#filBranch").val();
        // alert("exeDayEnd"+branchId);

        $.ajax({
           url: './addAccDayEndProcessItem',
           type: 'POST',
           data: {branchId: branchId},
           dataType: 'json',
        })
        .done(function(data) {
            if (data.responseTitle=='Success!') {
                $('#myModal').modal('hide');
                toastr.success(data.responseText, data.responseTitle, opts);
                $('#reportSubmit').trigger('click');
                $('#minibarDate').text(data.currentDate);
                setTimeout(function(){
                    // location.reload();
                    $("#exeDayEnd").removeClass('disabled');

                }, 3000);
            }
            else if(data.responseTitle=='Warning!'){
                $('#myModal').modal('hide');
                $('#loadingModal').hide();
                toastr.warning(data.responseText, data.responseTitle, opts);
                setTimeout(function(){
                    // alert('hello');
                    $("#exeDayEnd").removeClass('disabled');

                }, 2000);
            }
        })
        .fail(function() {
           alert("error");
        });

    });

    // $("#deleteDateEnd").prop('disable', 'true');
    // $(".deleteDateEnd").addClass('disabled');
    // alert($(".deleteDateEnd").html());
    // $('.deleteDateEnd').attr('style','pointer-events: none; cursor: not-allowed; opacity: 0.6;');

    $(document).on('click', '.delete-modal', function() {

        // if(hasAccess('deleteAccDayEndItem')){

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
            $('.modal-title').text('Delete Day End');
            $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
            $('.modal-dialog').css('width','30%');
            // alert($(this).data('id'));
            $('.id').text($(this).data('id'));
            $('#deleteDate').text('Day : '+$(this).data('date'));
            $('#deleteBranch').text('Branch: '+$(this).data('branchname'));
            $('.executeContent').hide();
            $('.deleteContent').show();
            // $('.form-horizontal').hide();
            $('#footer_action_button2').show();
            $('#footer_action_button').hide();
            $('#myModal').modal('show');
            // $("deleteDateEnd").addClass('disabled');
            // alert($("deleteDateEnd").html());
        // }

    });


    $('.modal-footer').on('click', '.delete', function() {

        // alert("Delete"+$('.id').text());

        $('#loadingModal').show();

        $.ajax({
            type: 'post',
            url: './deleteAccDayEndItem',
            data: {
                '_token': $('input[name=_token]').val(),
                'id': $('.id').text()
            },
            success: function(data) {

                if (data.responseTitle=='Success!') {
                    toastr.success(data.responseText, data.responseTitle, opts);
                    $('#minibarDate').text(data.currentDate);
                    $('#reportSubmit').trigger('click');
                    // setTimeout(function(){
                    // }, 2000);
                }
                else if(data.responseTitle=='Warning!'){
                    $('#myModal').modal('hide');
                    $('#loadingModal').hide();
                    toastr.warning(data.responseText, data.responseTitle, opts);
                    setTimeout(function(){
                        // $("#exeDayEnd").removeAttr('style');
                    }, 2000);
                }

                // if (data.accessDenied) {
                //     showAccessDeniedMessage();
                //     return false;
                // }
                // $('.item' + $('.id').text()).remove();
            }
        });



    });

    // alert($(".deleteDateEnd").html());











}); /*End Ready*/
</script>

@endsection
