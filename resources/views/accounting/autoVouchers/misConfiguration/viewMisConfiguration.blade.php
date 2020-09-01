@extends('layouts/acc_layout')
@section('title', '| MIS Configuration')
@section('content')


<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
        <div class="panel panel-default" style="background-color:#708090;">
            <div class="panel-heading" style="padding-bottom:0px">                
                <div class="panel-options">
                    <a href="{{url('addMisConfiguration/')}}" class="btn btn-info pull-right addViewBtn" style=""><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add MIS Configuration</a>
                </div>
                <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">MIS Configuration</h3>
            </div>
            <div class="panel-body panelBodyView">

                <table class="table table-striped table-bordered" id="accMisConfigurationTable" style="color: black;">
                    <thead>
                    <tr>
                        <th width="50">SL No.</th>
                        <th>Module</th>
                        <th>MIS Type</th>
                        <th>MIS Name</th>
                        <th>Table Field Name</th>
                        <th class="" width="80">Actions</th>
                    </tr>
                    {{ csrf_field() }}
                    </thead>
                    <tbody>
                    <?php $no=0; ?>
                    @foreach($misConfigurationInfos as $misConfig)
                        <tr class="item{{$misConfig->id}}">
                            <td class="text-center slNo">{{++$no}}</td>
                            <td>{{$moduleOption[$misConfig->moduleId]}}</td>
                            <td>{{DB::table('acc_mis_type')->where('id',$misConfig->misTypeId_Fk)->value('name')}}</td>
                            <td>{{$misConfig->misName}}</td>
                            <td>{{$misConfig->tableFieldName}}</td>

                            <td class="text-center" width="">
                                <a id="editIcone" href="javascript:;" class="edit-modal" data-id="{{$misConfig->id}}" data-moduleid="{{$misConfig->moduleId}}"  data-mistype="{{$misConfig->misTypeId_Fk}}" data-misname="{{$misConfig->misName}}" data-tablefieldname="{{$misConfig->tableFieldName}}" data-slno="{{$no}}">
                                    <i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i>
                                </a>&nbsp;
                                <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$misConfig->id}}">
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
                {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                    <div class="form-group hidd hidden">
                        {!! Form::label('id', 'ID:', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! Form::text('id', $value = null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text', 'readonly']) !!}
                            {!! Form::text('slno', $value = null, ['class' => 'form-control', 'id' => 'slno', 'type' => 'text', 'readonly']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('moduleId', 'Module:', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-5">
                            {!! Form::select('moduleId', $moduleOption, null,['id'=>'moduleId','class'=>'form-control input-sm']) !!}
                            <p id='moduleIdE' style="max-height:3px;"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('misType', 'MIS Type:', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-5">
                            {!! Form::select('misTypeId_Fk', $misTypes, null,['id'=>'misType','class'=>'form-control input-sm']) !!}
                            <p id='misTypeE' style="max-height:3px;"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('misName', 'MIS Name:', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-5">
                            {!! Form::text('misName', $value = null, ['class' => 'form-control', 'id' => 'misName', 'type' => 'text', 'placeholder' => 'Enter MIS Name']) !!}
                            <p id='misNameE' style="max-height:3px;"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('tableFieldName', 'Table Field Name:', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-5">
                            {!! Form::text('tableFieldName', $value = null, ['class' => 'form-control', 'id' => 'tableFieldName', 'type' => 'text', 'placeholder' => 'Enter Table Field Name']) !!}
                            <p id='tableFieldNameE' style="max-height:3px;"></p>
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
    $(document).on('click', '.edit-modal', function() {
        $('.errormsg').empty();
        $('#MSGE').empty();
        $('#MSGS').empty();
        $('#footer_action_button').text("Update");
        $('#footer_action_button').addClass('glyphicon glyphicon-check');
        $('#footer_action_button_dismis').text("Close");
        $('.actionBtn').addClass('btn-success');
        $('.actionBtn').removeClass('btn-danger');
        $('.actionBtn').addClass('edit');
        $('.modal-title').text('Update MIS Configuration');
        $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
        $('.modal-dialog').css('width','50%');
        $('.deleteContent').hide();
        $('.form-horizontal').show();
        $('#id').val($(this).data('id'));
        $('#slno').val($(this).data('slno'));
        $('#misType').val($(this).data('mistype'));
        $('#moduleId').val($(this).data('moduleid'));
//    alert(($(this).data('parentid')));
        $('#misName').val($(this).data('misname'));
        $('#tableFieldName').val($(this).data('tablefieldname'));
        $('#footer_action_button2').hide();
        $('#footer_action_button').show();
        $('.actionBtn').removeClass('delete');
        $('#myModal').modal('show');

    });
    // Edit Data (Modal and function edit data)
    $('.modal-footer').on('click', '.edit', function() {
        // alert($('form').serialize());
        $.ajax({
            type: 'post',
            url: './updateMisConfigurationItem',
            data: $('form').serialize(),
            dataType: 'json',
            success: function(_response){
                // alert(JSON.stringify(_response));
                if (_response.errors) {
                    if (_response.errors['moduleId']) {
                        $('#moduleIdE').empty();
                        $('#moduleIdE').append('<span class="errormsg" style="color:red;">'+_response.errors.moduleId+'</span>');
                        return false;
                    }
                    if (_response.errors['misTypeId_Fk']) {
                        $('#misTypeE').empty();
                        $('#misTypeE').append('<span class="errormsg" style="color:red;">'+_response.errors.misTypeId_Fk+'</span>');
                        return false;
                    }
                    if (_response.errors['misName']) {
                        $('#misNameE').empty();
                        $('#misNameE').show();
                        $('#misNameE').append('<span class="errormsg" style="color:red;">'+_response.errors.misName+'</span>');
                        return false;
                    }
                    if (_response.errors['tableFieldName']) {
                        $('#tableFieldNameE').empty();
                        $('#tableFieldNameE').show();
                        $('#tableFieldNameE').append('<span class="errormsg" style="color:red;">'+_response.errors.tableFieldName+'</span>');
                        return false;
                    }

                }else{
                    // location.reload();
                    // alert('updated');

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
                //alert(_response.responseText);    
            }
        });
    });

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
        $('.modal-title').text('Delete MIS Type');
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
            url: './deleteMisConfigurationItem',
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
        var misName = $("#misName").val();
        if(misName){$('#misNameE').hide();}else{$('#misNameE').show();}

        var tableFieldName = $("#tableFieldName").val();
        if(tableFieldName){$('#tableFieldNameE').hide();}else{$('#tableFieldNameE').show();}
    });

    $('select').on('change', function (e) {
        var moduleId = $("#moduleId").val();
        if(moduleId){$('#moduleIdE').hide();}else{$('#moduleIdE').show();}

        var misType = $("#misType").val();
        if(misType){$('#misTypeE').hide();}else{$('#misTypeE').show();}
    });


});//ready function end


</script>



@endsection