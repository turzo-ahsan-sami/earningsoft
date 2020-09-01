@extends('layouts/pos_layout')
@section('title', '| Payment')
@section('content')
    @include('successMsg')
    <style type="text/css">
    .select2-results__option[aria-selected=true] {
        display: none;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">
                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading" style="padding-bottom:0px">
                        <div class="panel-options">
                            <a href="{{url('pos/addPayment/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Payment</a>
                        </div>


                        <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">PAYMENT LIST</font></h1>
                    </div>
                    <div class="panel-body panelBodyView">
                        <div>
                            <div>

                                <script type="text/javascript">
                                jQuery(document).ready(function($)  {
                                    $("#supplierView").dataTable({
                                        "oLanguage": {
                                            "sEmptyTable": "No Records Available",
                                            "sLengthMenu": "Show _MENU_ "
                                        }
                                    });
                                });

                                </script>

                            </div>
                        </div>
                        <table class="table table-striped table-bordered" id="supplierView" style="color:black;">
                            <thead>
                                <tr>
                                    <th width="80">SL#</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                             </thead>
                                 <?php $no=0; ?>
                               @foreach($posPayments as $posPayment)
                                <tr class="item{{$posPayment->id}}">
                                    <td>{{++$no}}</td>
                                    <td style="text-align: left; padding-left: 5px;">{{$posPayment->name}}</td>
                                    <td style="text-align: center;">{{$posPayment->code}}</td>
                                    <td style="text-align: left; padding-left: 5px;">{{$posPayment->description}}</td>
                                    
                                    <td class="text-center" width="100">
                                        <a href="javascript:;" class="form5" data-token="" data-id="{{$posPayment->id}}">
                                            <span class="fa fa-eye"></span>
                                        </a>
                                        &nbsp
                                        <a href="javascript:;" class="edit-modal" paymentId="{{$posPayment->id}}">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>&nbsp
                                        <a id="deleteIcone" href="javascript:;" class="delete-modal" paymentId="{{$posPayment->id}}">
                                            <span class="glyphicon glyphicon-trash"></span>
                                        </a>
                                    </td>
                                </tr>
                               @endforeach
                            <tbody>
                            
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Modal -->
<div id="editModal" class="modal fade" style="margin-top:3%;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Update Payment</h4>
            </div>
            <div class="modal-body">
                {!! Form::open(array('url' => '','id'=>'entryForm', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                <input id="EMpaymentId" type="hidden"  value=""/>

                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('name', 'Name:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'EMname', 'type' => 'text', 'placeholder' => 'Enter Supplier name']) !!}
                                    <p id='namee' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('code', 'Code:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('code', $value = null, ['class' => 'form-control', 'id' => 'EMcode', 'type' => 'text', 'placeholder' => 'Enter Supplier code']) !!}
                                    <p id='codee' style="max-height:3px;"></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                {!! Form::label('', 'Ledger Head:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    <select name="ledgerHeadId" id="ledgerHeadId" class="form-control col-sm-9">
                                        <option value="0">Select Ledger Head</option>
                                    </select>
                                    <p id='ledgerHeadIdMsg' style="max-height:3px;"></p>
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('description', 'Description:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    <textarea class="form-control" id="description" name="description"></textarea>
                                    <p id='description_msg' style="max-height:3px;"></p>
                                </div>
                            </div>   
                    
                            <div class="modal-footer">
                                <input id="EMpaymentId" type="hidden" name="paymentId" value="">
                                <button type="button" id="updateButton" class="btn btn-success"><span class="glyphicon glyphicon-edit" style="padding-right:4px;"></span>Update</button>
                                <button type="button" class="btn btn-danger " data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--delete modal-->
<style type="text/css">
    .detailsTable th{
        text-align: left !important;
    }
    .detailsTable td{
        text-align: left !important;
    }
</style>
<div id="myModal3" class="modal fade" style="margin-top:2%">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
               <h4 class="modal-title" style="clear:both"></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class='table table-hover detailsTable'>
                            <tr>
                                <th>Name :</th><td id="paymentName"></td>
                            </tr>
                            
                            <tr>
                                <th>Code :</th><td id="paymentCode"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class='table table-hover detailsTable'>
                            <tr>
                                <th>Description :</th><td id="paymentDescription"></td>
                            </tr>

                            <tr>
                                <th>Ledger Head :</th><td id="paymentLedgerHead"></td>
                            </tr>
                           
                        </table>
                    </div>
                </div>
                    {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-danger pull-right closeBtn', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_dismis'] ) !!}
            </div>
        </div>
    </div>
</div>



<!--delete modal-->
<div id="deleteModal" class="modal fade" style="margin-top:3%;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Delete Supplier</h4>
            </div>

            <div class="modal-body ">
                <div class="row" style="padding-bottom:20px;"> </div>
                <h2>Are You Confirm to Delete This Record?</h2>

                <div class="modal-footer">
                    <input id="DMPaymentPackageId" type="hidden"  value=""/>
                    <button type="button" class="btn btn-danger"  id="DMPayment"  data-dismiss="modal">confirm</button>
                    <button type="button" class="btn btn-warning"  data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
                </div>

            </div>
        </div>
    </div>
</div>



@include('pos/supplier/supplierDetails')

<script>

//$(".select2-selection__rendered").show();
$(document).ready(function(){
   

    $(document).on('click', '.delete-modal', function(){
        $("#DMPaymentPackageId").val($(this).attr('paymentId'));

        $('#deleteModal').modal('show');
    });
    $("#DMPayment").on('click',  function() {
        var paymentId= $("#DMPaymentPackageId").val();

        var csrf = "{{csrf_token()}}";
        $.ajax({
            url: './deletePaymentItem',
            type: 'POST',
            dataType: 'json',
            data: {id:paymentId, _token:csrf},
        })
        .done(function(data) {

            location.reload();
             window.location.href = '{{url('pos/Payments/')}}';
        })
        .fail(function(){
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
    });
});
$( document ).ready(function() {

    $(document).on('click', '.edit-modal', function() {

        var paymentId = $(this).attr('paymentId');
       
        var csrf = "{{csrf_token()}}";
        $("#EMpaymentId").val(paymentId);

        $.ajax({
            url: './getPaymentInfo',
            type: 'POST',
            dataType: 'json',
            data: {id:paymentId , _token: csrf},
            success: function(data) {

                $("#EMname").val(data['payment'].name);
                $("#EMcode").val(data['payment'].code);
                $("#description").val(data['payment'].description);

                $('#ledgerHeadId').html('');
                $('#ledgerHeadId').append('<option value="0">Select Ledger Head</option>');
            
                for(i=0; i<data.ledgerHeads.length; i++)
                {   
                    if(data.payment.ledgerHeadId == data.ledgerHeads[i].id)
                        $('#ledgerHeadId').append('<option value='+data.ledgerHeads[i].id+' selected>'+data.ledgerHeads[i].name+'</option>');
                    else
                        $('#ledgerHeadId').append('<option value='+data.ledgerHeads[i].id+'>'+data.ledgerHeads[i].name+'</option>');
                }

                $('.modal-title').text('Edit Payment');
                $("#editModal").find('.modal-dialog').css('width', '80%');
                $('#editModal').modal('show');

            },
            error: function(argument) {
                //alert('response error');
            }

        });
    });



    $("#updateButton").on('click', function() {
        // $("#updateButton").prop("disabled", true);
        var paymentId         = $("#EMpaymentId").val();
        var name              = $("#EMname").val();
        var code              = $("#EMcode").val();
        var description       = $("#description").val();
        var ledgerHeadId      = $("#ledgerHeadId").val();
        var csrf = "{{csrf_token()}}";

        $.ajax({
            url: './editPaymentItem',
            type: 'POST',
            dataType: 'json',
            data: {id:paymentId,name:name,code:code,description:description,ledgerHeadId: ledgerHeadId,_token: csrf},
        })
        .done(function(data) {
            //console.log(data);
            if (data.errors) {
                if (data.errors['name']) {
                    $("#namee").empty();
                    $("#namee").append('<span class="errormsg" style="color:red;">'+data.errors['name']);
                }
                if (data.errors['code']) {
                    $("#codee").empty();
                    $("#codee").append('<span class="errormsg" style="color:red;">'+data.errors['code']);
                }
                if (data.errors['description']) {
                    $("#description_msg").empty();
                    $("#description_msg").append('<span class="errormsg" style="color:red;">'+data.errors['description']);
                }
                if (data.errors['ledgerHeadId']) {
                    $("#ledgerHeadIdMsg").empty();
                    $("#ledgerHeadIdMsg").append('<span class="errormsg" style="color:red;">'+data.errors['ledgerHeadId']);
                }
            }
            else {
                
                window.location.href = '{{url('pos/Payments/')}}';
            }


           
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        })
    });


    $(document).on('click', '.form5', function() {
        var id = ($(this).data('id'));
        var crsf = ($(this).data('token'));
        $.ajax({
            type: 'post',
            url: './paymentDetail',
            data: {
                '_token': $('input[name=_token]').val(),
                'id': id
            },
            dataType: 'json',
            success: function( data ){
                console.log(data);
                $.each(data, function( index, value ){
                    $('#paymentName').text(data.paymentName);
                    $('#paymentCode').text(data.paymentCode);
                    $('#paymentDescription').text(data.paymentDescription);
                    $('#paymentLedgerHead').text(data.paymentLedgerHead);
                });

                $('.modal-title').text('Payment Details');
                $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
                $('.modal-dialog').css('width','90%');
                $('.form-horizontal').show();
                $('#myModal3').modal('show');
            },
            error: function( data ){
                alert();
            }
        });
    });

    $("input").keyup(function(){
        var name = $("#EMname").val();
        if(name){$('#namee').hide();}else{$('#namee').show();}
        var code = $("#EMcode").val();
        if(code){$('#codee').hide();}else{$('#codee').show();}
        var mobile = $("#EMmobile").val();
        if(mobile){$('#mobilee').hide();}else{$('#mobilee').show();}
        var email = $("#EMemail").val();
        if(email){$('#emaile').hide();}else{$('#emaile').show();}
        var nid = $("#EMnid").val();
        if(nid){$('#nide').hide();}else{$('#nide').show();}
       

    });


});//ready function end


</script>

@include('dataTableScript')
@endsection
