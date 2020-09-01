@extends('layouts/acc_layout')
@section('title', '|  Auto Voucher Configuration')
@section('content')
<?php
$childLedgersObj=DB::table('acc_account_ledger')->where('isGroupHead', 0)->select('id','code','name')->orderBy('code')->get();
?>

<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
        <div class="panel panel-default" style="background-color:#708090;">
            <div class="panel-heading" style="padding-bottom:0px">                
                <div class="panel-options">
                    <a href="{{url('addAutoVoucherConfigForAll/')}}" class="btn btn-info pull-right addViewBtn" style=""><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Auto Voucher Configuration</a>
                </div>
                <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Auto Voucher Configuration</h3>
            </div>
            <div class="panel-body panelBodyView">

                <table class="table table-striped table-bordered" id="accAccountTypeView" style="color: black;">
                    <thead>
                        <tr>
                            <th width="50">SL No.</th>
                            <th>Module</th>
                            <th>MIS Type</th>
                            <th>Voucher Type</th>
                            <th>Local Narration</th>
                            <th class="" width="80">Actions</th>
                        </tr>
                    {{ csrf_field() }}
                    </thead>
                    <tbody>
                        <?php $no=0; ?>
                        @foreach($autoVoucherConfigInfos as $autoVoucherConfig)
                            <tr class="item{{$autoVoucherConfig->id}}">
                                <td class="text-center slNo">{{++$no}}</td>
                                <td>{{$moduleOption[$autoVoucherConfig->moduleId]}}</td>
                                <td>{{$misTypeOption[$autoVoucherConfig->misTypeId_Fk]}}</td>
                                <td>{{$voucherTypes[$autoVoucherConfig->voucherType]}}</td>
                                <td>{{$autoVoucherConfig->localNarration}}</td>

                                <td class="text-center" width="">
                                    <a id="editIcone" href="javascript:;" class="edit-modal" data-id="{{$autoVoucherConfig->id}}" data-configid="{{$autoVoucherConfig->configId}}" data-moduleid="{{$autoVoucherConfig->moduleId}}"  data-mistype="{{$autoVoucherConfig->misTypeId_Fk}}" data-vouchertype="{{$autoVoucherConfig->voucherType}}" data-localnarration="{{$autoVoucherConfig->localNarration}}" data-slno="{{$no}}">
                                        <i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i>
                                    </a>&nbsp;
                                    <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$autoVoucherConfig->id}}">
                                        <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
                                    </a>
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


<div id="myModal" class="modal fade" style="margin-top:3%">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="font-weight:bold;clear:both"></h4>
            </div>
            <div class="modal-body">

                {!! Form::open(array('url' => '', 'id' => 'editAutoVoucherConfigForm', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}

                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('moduleId', 'Module:', ['class' => 'col-sm-12 control-label']) !!}
                            <div class="col-sm-12">
                                {!! Form::select('moduleId', $moduleOption, null,['id'=>'moduleId','class'=>'form-control input-sm']) !!}
                                <p id='moduleIdE' style="max-height:3px; color:red">Required</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('misType', 'MIS Type:', ['class' => 'col-sm-12 control-label']) !!}
                            <div class="col-sm-12">
                                {!! Form::select('misType', $misTypeOption, null,['id'=>'misType','class'=>'form-control input-sm', 'placeholder' => '---Select---']) !!}
                                <p id='misTypeE' style="max-height:3px; color:red">Required</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('voucherType', 'Voucher Type:', ['class' => 'col-sm-12 control-label']) !!}
                            <div class="col-sm-12">
                                {!! Form::select('voucherType', $voucherTypes, null,['id'=>'voucherType','class'=>'form-control input-sm', 'placeholder' => '---Select---']) !!}
                                <p id='voucherTypee' style="max-height:3px; color:red">Required</p>
                            </div>
                        </div>                        
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table id="editAutoVoucherConfigTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:200px; text-align:center;">MIS Name</th>
                                    <th style="text-align:center;">Ledger Code</th>
                                    <th style="width:150px; text-align:center;">Amount Type</th>
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
                        <p id='editAutoVoucherConfigTableE' style="max-height:3px; color: red;"></p>
                        
                    </div>
                    
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('localNarration', 'Local Narration:', ['class' => 'col-sm-12 control-label']) !!}
                            <div class="col-sm-12">
                                {!! Form::text('localNarration', null,['id'=>'localNarration','class'=>'form-control input-sm', 'placeholder' => 'Enter Narration']) !!}
                                <p id='localNarratione' style="max-height:3px; color:red">Required</p>
                            </div>
                        </div>                     
                    </div>
                </div>
                {!! Form::close()  !!}

                
                <div class="deleteContent" style="padding-bottom:20px;">
                    <p>You are about to delete this item this procedure is irreversible !</p>
                    <p>Do you want to proceed ?</p>
                    <span class="hidden id"></span>
                </div>
                <div style="border-top:none !important" class="modal-footer">
                    <p id="MSGE" class="pull-left" style="color:red"></p>
                    <p id="MSGS" class="pull-left" style="color:green"></p>
                    {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn', 'id' => 'footer_action_button'] ) !!}
                    {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn',  'data-dismiss' => 'modal', 'id' => 'footer_action_button2'] ) !!}

                    {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-danger', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_dismis'] ) !!}

                </div>
            </div>
        </div>
    </div>
</div>

{{-- <script src="{{asset('js/jquery-1.11.1.min.js')}}"></script> --}}
<script type="text/javascript">
$( document ).ready(function() {
    $('#moduleId, #misType, #voucherType').prop("disabled", true);
    $('#moduleIdE, #misTypeE, #voucherTypee, #localNarratione').hide();


    var childLedgersObjStr = '<?php echo json_encode($childLedgersObj); ?>' ;
    var childLedgersObj = JSON.parse(childLedgersObjStr );

    var ledgerOptions= "";
    $.each(childLedgersObj, function(index, eachLedger) {
        ledgerOptions=ledgerOptions+"<option value='"+eachLedger.id+"'>"+eachLedger.code+" - "+eachLedger.name+"</option>";
    });

    $(document).on('click', '.edit-modal', function() {
        $('.errormsg').empty();
        $('#MSGE').empty();
        $('#MSGS').empty();
        $('#footer_action_button').text("Update");
        $('#footer_action_button').addClass('btn btn-info');
        $('#footer_action_button_dismis').text("Close");
        $('.actionBtn').addClass('btn-success');
        $('.actionBtn').removeClass('btn-danger');
        $('.actionBtn').addClass('edit');
        $('.modal-title').text('Update Auto Voucher Configration');
        $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
        $('.modal-dialog').css('width','50%');
        $('.deleteContent').hide();
        $('.form-horizontal').show();
        $('#id').val($(this).data('id'));
        $('#slno').val($(this).data('slno'));
        $('#moduleId').val($(this).data('moduleid'));
        $('#misType').val($(this).data('mistype'));
        $('#voucherType').val($(this).data('vouchertype'));
        $('#localNarration').val($(this).data('localnarration'));
        $('#footer_action_button2').hide();
        $('#footer_action_button').show();
        $('.actionBtn').removeClass('delete');
        $('#myModal').modal('show');

        var configId=$(this).data('configid');
        var csrf = "{{csrf_token()}}";
        // alert(configId);

        $.ajax({
            type: 'post',
            url: './editAutoVoucherConfigItem',
            data: { _token:csrf, configId:configId },
            dataType: 'json',
            success: function(autoVoucherConfigDetails) {
                // alert(JSON.stringify(autoVoucherConfigDetails));
                // alert(autoVoucherConfigDetails);

                $("#editAutoVoucherConfigTable tbody").empty();
                $.each(autoVoucherConfigDetails, function(key,obj){
                    var eachRow =
                        "<tr class='valueRow'>" +                                                
                            "<td style='text-align:left; padding-left:5px;' class='autoVouIdColumn'>"+
                                "<input type='hidden' name='autoVouConfigId[]' class='autoVouConfigIdInput' value='"+obj.id+"'>"+
                                "<input type='hidden' name='misConfigName[]' class='misConfigNameInput' value='"+obj.misConfigName+"'>"+obj.misConfigName+
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
                    $("#editAutoVoucherConfigTable #eachRow tr").eq(key).closest('tr').find('.amountTypeInput').val(obj.amountType);
                    var tempLedgerIdArr=JSON.parse(obj.ledgerId);
                    $.each(tempLedgerIdArr, function(index, ledgerIdVal) {
                        $("#editAutoVoucherConfigTable #eachRow tr").eq(key).closest('tr').find(".ledgerOption option[value="+ledgerIdVal+"]").prop('selected', true);
                    });

                });

                $('.ledgerOption').select2();
                $('.ledgerOption').next("span").css("width","100%");

            },
            error: function( _response ){
                // Handle error  
                alert("Error");    
            }
        });     //ajax
    });         //edit Model




    //Edit Data (Modal and function edit data)
    $('.modal-footer').on('click', '.edit', function() {
        $('#moduleId, #misType, #voucherType').prop("disabled", false);
        // alert($('form').serialize());
        var misType = $("#misType option:selected").val();
        var voucherType = $("#voucherType option:selected").val();
        var localNarration = $("#localNarration").val();
        var csrf = "{{csrf_token()}}";

        var amountTypeCheckArray = new Array();
        var ledgerCodeCheckArray = new Array();

        $("#editAutoVoucherConfigTable tbody tr").each(function(rowNo){

            if( ($(this).find('.ledgerOption').val()=="") ){
                toastr.error("Please Select Ledger");
                return false;
            }

            if( ($(this).find('.amountTypeInput').val()=="") ){

                toastr.error("Please Select Dedit or Credit Account");
                return false;
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
            url: './updateAutoVoucherConfigItem',
            // data: { _token:csrf, misType:misType, voucherType:voucherType, autoVouConfigIdArray:autoVouConfigIdArray, ledgerCodeArray:ledgerCodeArray, amountTypeArray:amountTypeArray, misConfigNameArray:misConfigNameArray , localNarration:localNarration },
            data: $('form').serialize(),
            dataType: 'json',
            success: function(_response){
                // alert(JSON.stringify(data));
                if (_response.errors) {
                    $('#moduleId, #misType, #voucherType').prop("disabled", true);

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

                }else{
                    // location.reload();
                    // alert(JSON.stringify(_response));
                    
                    if (_response.responseTitle=='Success!') {
                        $('.item' + $('.id').text()).remove();                        
                        toastr.success(_response.responseText, _response.responseTitle, opts);
                        setTimeout(function(){
                            location.reload();
                        },2000);

                    }else if (_response.responseTitle=='Warning!') {
                        toastr.warning(_response.responseText, _response.responseTitle, opts);
                    }

                }
            },
            error: function( _response ){
                // Handle error  
                // alert(_response.responseText);    
                alert("Could Not Update");    
            }
        });     //ajax
    });         //Edit

//delete function
    $(document).on('click', '.delete-modal', function() {
        $('#MSGE').empty();
        $('#MSGS').empty();
        $('.actionBtn').removeClass('edit');
        $('#footer_action_button2').text(" Yes");
        $('#footer_action_button2').removeClass('glyphicon glyphicon-check');
        $('#footer_action_button_dismis').text(" No");
        $('#footer_action_button_dismis').removeClass('glyphicon glyphicon-remove');
        $('.actionBtn').removeClass('btn-success');
        $('.actionBtn').addClass('btn btn-info');
        $('.actionBtn').addClass('delete');
        $('.modal-title').text('Delete Auto Voucher Configration');
        $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
        $('.modal-dialog').css('width','30%');
        $('.id').text($(this).data('id'));
        $('.deleteContent').show();
        $('.form-horizontal').hide();
        $('#footer_action_button2').show();
        $('#footer_action_button').hide();
        $('.title').html($(this).data('uname'));
        $('#myModal').modal('show');
    });

    $('.modal-footer').on('click', '.delete', function() {
        $.ajax({
            type: 'post',
            url: './deleteAutoVoucherConfigItem',
            data: {
                '_token': $('input[name=_token]').val(),
                'id': $('.id').text()
            },
            success: function(_response) {
                // alert(JSON.stringify(_response));
                // $('.item' + $('.id').text()).remove();

                if (_response.responseTitle=='Success!') {
                    $('.item' + $('.id').text()).remove();                        
                    toastr.success(_response.responseText, _response.responseTitle, opts);
                    // setTimeout(2000);
                }else if (_response.responseTitle=='Warning!') {
                    toastr.warning(_response.responseText, _response.responseTitle, opts);
                }


            }
        });
    });


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



});//ready function end


</script>

@endsection