@extends('layouts/acc_layout')
@section('title', '| Auto Voucher Configuration')
@section('content')
<style type="text/css">
    /*.select2-results__option[aria-selected=true] {
        display: none;
    }*/
</style>
<?php
$childLedgersObj=DB::table('acc_account_ledger')->where('isGroupHead', 0)->select('id','code','name')->orderBy('code')->get();
?>

<div class="row add-data-form"  style="padding-bottom: 1%">
    <div class="col-md-12">
        <div class="col-md-2"></div>
        <div class="col-md-8 fullbody">
            <div class="viewTitle" style="border-bottom: 1px solid white;">
                <a href="{{url('viewAutoVoucherConfigForAll/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                </i>Auto Voucher Configuration List</a>
            </div>
            <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <div class="panel-title">Add Auto Voucher Configuration</div>
                </div>


                <div class="panel-body">
                    {!! Form::open(array('url' => '', 'id' => 'autoVoucherFormId', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}

                    <div class="row">

                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('moduleId', 'Module:', ['class' => 'col-sm-12 control-label']) !!}
                                <div class="col-sm-12">
                                {!! Form::select('moduleId', $moduleOption, null,['id'=>'moduleId','class'=>'form-control input-sm']) !!}
                                    <p id='moduleIdE' style="max-height:3px; color:red;"></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('misType', 'MIS Type:', ['class' => 'col-sm-12 control-label']) !!}
                                <div class="col-sm-12">
                                    {!! Form::select('misType', [''=>'--Select Module First--'], null,['id'=>'misType','class'=>'form-control input-sm']) !!}
                                    <p id='misTypeE' style="max-height:3px; color:red;"></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('voucherType', 'Voucher Type:', ['class' => 'col-sm-12 control-label']) !!}
                                <div class="col-sm-12">
                                    {!! Form::select('voucherType', $voucherTypes, null,['id'=>'voucherType','class'=>'form-control input-sm']) !!}
                                    <p id='voucherTypeE' style="max-height:3px; color:red;"></p>
                                </div>
                            </div>                        
                        </div>

                        <div class="col-md-3">   
                            <div class="form-group">
                                {!! Form::label('add', ' ', ['class' => 'col-sm-12 control-label']) !!}
                                <div class="col-sm-12" style="padding-right: 30px;">
                                    <button class="btn btn-info" id="addButton" style="float: left; margin-top:15px; " type="button">Add</button>
                                </div>
                            </div>                
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <table id="addAutoVoucherConfigTable" class="table table-striped table-bordered" style="color:black; margin-bottom: 20px;">
                                <thead>
                                    <tr>
                                        <th style="width:200px; padding:7px">MIS Name</th>
                                        <th style="padding:7px">Ledger Code</th>
                                        <th style="width:150px; padding:7px">Amount Type</th>
                                    </tr>
                                </thead>
                                <tbody id="eachRow">
                                    {{-- <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr> --}}
                                </tbody>
                            </table>
                            <p id='addAutoVoucherConfigTableE' style="max-height:3px; color: red;"></p>
                            
                        </div>                        
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('localNarration', 'Local Narration:', ['class' => 'col-sm-12 control-label']) !!}
                                <div class="col-sm-12">
                                    {!! Form::text('localNarration', null,['id'=>'localNarration','class'=>'form-control input-sm', 'placeholder' => 'Enter Narration']) !!}
                                    <p id='localNarrationE' style=" color:red; max-height:3px;"></p>
                                </div>
                            </div>                     
                        </div>
                    </div>

                        
                        <div class="form-group">
                            {!! Form::label('submit', ' ', ['class' => 'col-sm-7 control-label']) !!}
                            <div class="col-sm-5 text-right">
                                {!! Form::submit('Submit', ['id' => 'submitButton', 'class' => 'btn btn-info']) !!}
                                {{ Form::reset('Reset', ['class' => 'btn btn-warning']) }}
                                <a href="{{url('viewAutoVoucherConfigForAll/')}}" class="btn btn-danger closeBtn">Close</a>
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
    

    var childLedgersObjStr = '<?php echo json_encode($childLedgersObj); ?>' ;
    var childLedgersObj = JSON.parse(childLedgersObjStr );


    var ledgerOptions= "";
    $.each(childLedgersObj, function(index, eachLedger) {
        ledgerOptions=ledgerOptions+"<option value='"+eachLedger.id+"'>"+eachLedger.code+" - "+eachLedger.name+"</option>";
    });


    var csrf = "{{csrf_token()}}";

    $('#moduleId').on('change', function () {

        var moduleId=$(this).val();
        if (!moduleId) {            
            $('#misType').empty();
            $('#misType').prepend("<option value=''>--Select Module First--</option>");
            return false;
        }

        $.ajax({
            url: './getMisTypeOption',
            type: 'POST',
            dataType: 'json',
            data: {moduleId: moduleId, _token: csrf},
        })
        .done(function(misTypeOption) {
            // alert(JSON.stringify(misTypeOption));
            // console.log("success");

            $('#misType').empty();
            $('#misType').prepend("<option value=''>--Select MIS Type--</option>");

            $.each(misTypeOption, function(id, name){
                $('#misType').append("<option value='"+id+"'>"+name+"</option>");
            });

        })
        .fail(function() {
            console.log("error");
        })
        
    })


    $("#addButton").click(function(){

        var misType=$("#misType").val();
        var voucherType=$("#voucherType").val();
        var moduleId=$("#moduleId").val();

        if (moduleId=="") {
            $("#moduleIdE").show();
            $("#moduleIdE").html("Please Select");
            $("#addAutoVoucherConfigTable tbody").empty();
            return false;
        }
        if (misType=="") {
            $("#salesTypee").show();
            $("#salesTypee").html("Please Select");
            $("#addAutoVoucherConfigTable tbody").empty();
            return false;
        }
        if (voucherType=="") {
            $("#voucherTypeE").show();
            $("#voucherTypeE").html("Please Select");
            $("#addAutoVoucherConfigTable tbody").empty();
            return false;
        }


        $.ajax({
            type: 'post',
            url: './getMISConfigData',
            data: { misType: misType, moduleId: moduleId, _token: csrf},
            dataType: 'json',
            success: function( misInfo ){
                // alert(JSON.stringify(misInfo));
                objLength=Object.keys(misInfo).length;

                $("#addAutoVoucherConfigTable tbody").empty();
                
                if(objLength==0){                    
                    var eachRow =
                        "<tr class='valueRow'>" +
                            "<td colspan='3'>No Data to Display </td>" +
                        "</tr>";
                    $("#eachRow").append(eachRow);

                }else{

                    $.each(misInfo, function(key,obj){
                        var eachRow =
                            "<tr class='valueRow'>" +                                                
                                "<td style='text-align:left; padding-left:5px;' class='misIdColumn'>"+
                                    "<input type='hidden' name='misConfigId[]' class='misConfigIdInput' value='"+obj.id+"'>"+
                                    "<input type='hidden' name='misConfigName[]' class='misConfigNameInput' value='"+obj.misName+"'>"+
                                    "<input type='hidden' name='tableFieldName[]' class='tableFieldNameInput' value='"+obj.tableFieldName+"'>"+obj.misName+
                                "</td>" +                                               
                                "<td>" +
                                    "<select name='ledgerId["+key+"][]' class='form-control ledgerOption' multiple='multiple'>"+
                                        ledgerOptions+
                                    "</select>"+
                                "</td>" +

                                "<td>" +
                                     "<select name='amountType[]' class='form-control amountTypeInput'>"+
                                        "<option value=''>---Please Select---</option>"+
                                        "<option value='1'>Debit Account</option>"+
                                        "<option value='2'>Credit Account</option>"+
                                     "</select>"+
                                "</td>" +
                            "</tr>";

                        $("#eachRow").append(eachRow);
                    });

                    $('.ledgerOption').select2({
                      placeholder: 'Please Select Ledger'
                    });

                }

                
            },
            error: function( data ){
                // Handle e
                alert("Error from getMISConfigData");
            }
        });     //2nd ajax

    });     //addButton Function




// ==================================Submit==================================
    $('form').submit(function( event ) {
        event.preventDefault();

        // alert($(this).serialize());
        
        // $("#submitButton").prop("disabled", true);

        var moduleId = $("#moduleId option:selected").val();
        var misType = $("#misType option:selected").val();
        var voucherType = $("#voucherType option:selected").val();
        var localNarration = $("#localNarration").val();
        // var csrf = "{{csrf_token()}}";

        if((moduleId=="") || (misType=="") || (voucherType=="") || (localNarration=="")){
            $('#moduleIdE, #misTypeE, #voucherTypeE, #localNarrationE').html("Please Select");
            return false;
        }

        var amountTypeCheckArray = new Array();
        var ledgerCodeCheckArray = new Array();

        $("#addAutoVoucherConfigTable tbody tr").each(function(rowNo){
            
            if( ($(this).find('.ledgerOption').val()==null) || ($(this).find('.amountTypeInput').val()=="") ){

                // alert("rowNo: "+rowNo+"; ledgerOption: "+$(this).find('.ledgerOption').val()+"; amountTypeInput: "+$(this).find('.amountTypeInput').val());

                // ledgerCodeCheckArray.push($(this).find('.ledgerOption').val());
                $(this).find('.ledgerOption').val('').trigger('change');
                $(this).find('.amountTypeInput').val('');
            }
            amountTypeCheckArray.push($(this).find('.amountTypeInput').val());

        });

        drCheck=crCheck=false;
        $.each(amountTypeCheckArray, function(index, value){
            if (value==1) {
                drCheck=true;                
            }else if(value==2) {
                crCheck=true;                
            }
        });
        if ((drCheck==false) || (crCheck==false)) {
            // alert("Required at least One Dedit or Credit Account");
            toastr.error("Required at least One Dedit or Credit Account");
            return false;
        }

        $.ajax({
             type: 'post',
             url: './addAutoVoucherConfigForAllItem',
             data: $(this).serialize(),
             dataType: 'json',
            success: function( _response ){
                // alert(JSON.stringify(_response));
                if (_response.errors) {
                    // $("#submitButton").prop("disabled", false);
                    // alert('required');

                    if (_response.errors['moduleId']) {
                        $('#moduleIdE').empty();
                        $('#moduleIdE').show();
                        $('#moduleIdE').append('<span style="color:red;">'+_response.errors.moduleId+'</span>');
                        return false;
                    }
                    if (_response.errors['misType']) {
                        $('#misTypeE').empty();
                        $('#misTypeE').show();
                        $('#misTypeE').append('<span style="color:red;">'+_response.errors.misType+'</span>');
                        return false;
                    }
                    if (_response.errors['voucherType']) {
                        $('#voucherTypeE').empty();        
                        $('#voucherTypeE').show();
                        $('#voucherTypeE').append('<span style="color:red;">'+_response.errors.voucherType+'</span>');
                        return false;
                    }
                    if (_response.errors['localNarration']) {
                        $('#localNarrationE').empty();
                        $('#localNarrationE').show();
                        $('#localNarrationE').append('<span style="color:red;">'+_response.errors.localNarration+'</span>');
                        return false;
                    }

                } else { 
                    // alert(JSON.stringify(_response));
                    // alert(JSON.stringify(_response.dataInsert));
                    if (_response.responseTitle=='Success!') {
                        toastr.success(_response.responseText, _response.responseTitle, opts);
                        
                        setTimeout(function(){
                            window.location.href = '{{url('viewAutoVoucherConfigForAll/')}}';
                        }, 2000);
                    }else if (_response.responseTitle=='Warning!') {
                        toastr.warning(_response.responseText, _response.responseTitle, opts);
                    }
                }
            },
            error: function( _response ){
                // Handle error
                // alert(_response.errors);
                alert('_response.errors');
            }
        });     //ajax
    });     //submit


    $("input").keyup(function(){
        var localNarration = $("#localNarration").val();
        if(localNarration){$('#localNarrationE').hide();}else{$('#localNarrationE').show();}
    });

    $('select').on('change', function (e) {
        $("#addAutoVoucherConfigTable tbody").empty();

        var moduleId = $("#moduleId").val();
        if(moduleId){$('#moduleIdE').hide();}else{$('#moduleIdE').show();}

        var misType = $("#misType").val();
        if(misType){$('#misTypeE').hide();}else{$('#misTypeE').show();}

        var voucherType = $("#voucherType").val();
        if(voucherType){$('#voucherTypeE').hide();}else{$('#voucherTypeE').show();}

    });

  
});
    
</script> 
 


@endsection 