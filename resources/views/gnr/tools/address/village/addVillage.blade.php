

@extends($route['layout'])
@section('title', '| Village')
@section('content')
<?php
$user = Auth::user();
Session::put('branchId', $user->branchId);
$branchId = Session::get('branchId');
//echo $branchId;
?>
<div class="row add-data-form">
    <div class="col-md-12">
        <div class="col-md-2"></div>
            <div class="col-md-8 fullbody">
                <div class="viewTitle" style="border-bottom: 1px solid white;">
                    <a href="{{url($route['path'].'/viewVillage/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                    </i>Village List</a>
                </div>
                <div class="panel panel-default panel-border">
                        <div class="panel-heading">
                            <div class="panel-title">New Village</div>
                        </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-8">
                    {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                        <!-- <input type = "hidden" name = "_token" value = ""> -->
                        {!! Form::text('branchId', $value = $branchId, ['class' => 'form-control hidden', 'id' => 'branchId']) !!}
                        <div class="form-group">
                            {!! Form::label('name', 'Village/Locality Name:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter village']) !!}
                                <p id='namee' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('divisionId', 'Division:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                <?php
                                    $divisionId = array('' => 'Please Select Division') + DB::table('division')->pluck('division_name','id')->all();
                                ?>
                                {!! Form::select('divisionId', ($divisionId), null, array('class'=>'form-control', 'id' => 'divisionId')) !!}
                                <p id='divisionIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('districtId', 'District:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                <?php
                                    $districtId = array('' => 'Please Select District') + DB::table('gnr_district')->pluck('name','id')->all();
                                ?>
                                {!! Form::select('districtId', ($districtId), null, array('class'=>'form-control', 'id' => 'districtId')) !!}
                                <p id='districtIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('upzillaId', 'Upazila:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                <?php
                                    $upzillaId = array('' => 'Please Select Upazila') + DB::table('gnr_upzilla')->pluck('name','id')->all();
                                ?>
                                {!! Form::select('upzillaId', ($upzillaId), null, array('class'=>'form-control', 'id' => 'upzillaId')) !!}
                                <p id='upzillaIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('unionId', 'Union/Zone:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                <?php
                                    $unionId = array('' => 'Please Select Union') + DB::table('gnr_union')->pluck('name','id')->all();
                                ?>
                                {!! Form::select('unionId', ($unionId), null, array('class'=>'form-control', 'id' => 'unionId')) !!}
                               <p id='unionIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                <a href="{{url($route['path'].'/viewVillage/')}}" class="btn btn-danger closeBtn">Close</a>
                                <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                            </div>
                        </div>
                    {!! Form::close() !!}
                            </div>
                            <div class="col-md-4 emptySpace vert-offset-top-2"><img src="images/catalog/image15.png" width="80%" height="" style="float:right"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
            <div class="col-md-2"></div>
    </div>
</div>
@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function(){

$('form').submit(function( event ) {
    event.preventDefault();
    $.ajax({
         type: 'post',
         url: './addVillageItem',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( _response ){
            if (_response.errors) {
            if (_response.errors['name']) {
                $('#namee').empty();
                $('#namee').append('<span class="errormsg" style="color:red">'+_response.errors.name+'</span>');
                return false;
            }
            if (_response.errors['divisionId']) {
                $('#divisionIde').empty();
                $('#divisionIde').append('<span class="errormsg" style="color:red">'+_response.errors.divisionId+'</span>');
                return false;
            }
            if (_response.errors['districtId']) {
                $('#districtIde').empty();
                $('#districtIde').append('<span class="errormsg" style="color:red">'+_response.errors.districtId+'</span>');
                return false;
            }
            if (_response.errors['upzillaId']) {
                $('#upzillaIde').empty();
                $('#upzillaIde').append('<span class="errormsg" style="color:red">'+_response.errors.upzillaId+'</span>');
                return false;
            }
            if (_response.errors['unionId']) {
                $('#unionIde').empty();
                $('#unionIde').append('<span class="errormsg" style="color:red">'+_response.errors.unionId+'</span>');
                return false;
            }
    } else {
            $("#divisionId").val('');
            $("#districtId").val('');
            $("#name").val('');
            $("#upzillaId").val('');
            $("#unionIde").val('');
            $('.error').addClass("hidden");
            $('#success').text(_response.responseText);
            window.location.href = '{{url($route['path'].'/viewVillage/')}}';
            }
        },
        error: function( _response ){
            // Handle error
            alert(_response.errors);

        }
    });
});
});

$(document).ready(function(){
        var divisionId = $('#divisionId').val();
        if(divisionId==''){$("#districtId").prop("disabled", true);}
$("#divisionId").change(function(){
        $("#districtId").empty();
        $("#districtId").prepend('<option selected="selected" value="">Please Select District</option>');
        var divisionId = $('#divisionId').val();
        if(divisionId!==''){$("#districtId").prop("disabled", false);}else{$("#districtId").prop("disabled", true);}
    $.ajax({
        type: 'post',
        url: './divisionIdSend',//invUnionController
        data: $('form').serialize(),
        dataType: 'json',
        success: function( _response ){
            //alert(JSON.stringify(_response));
        $.each(_response, function( index, value ){
                    $('#districtId').append("<option value='"+index+"'>"+value+"</option>");
                    //alert(value);
                });

                }
            });

        });
});

$(document).ready(function(){
        var districtId = $('#districtId').val();
        if(districtId==''){$("#upzillaId").prop("disabled", true);}
$("#districtId").change(function(){
        $("#upzillaId").empty();
        $("#upzillaId").prepend('<option selected="selected" value="">Please Select Upazila</option>');
        var districtId = $('#districtId').val();
        if(districtId!==''){$("#upzillaId").prop("disabled", false);}else{$("#upzillaId").prop("disabled", true);}
    $.ajax({
        type: 'post',
        url: './districtSendId',//invUnionController
        data: $('form').serialize(),
        dataType: 'json',
        success: function( _response ){
            //alert(JSON.stringify(_response));
        $.each(_response, function( index, value ){
                    $('#upzillaId').append("<option value='"+index+"'>"+value+"</option>");
                    //alert(value);
                });

                }
            });

        });
});

$(document).ready(function(){
        var upzillaId = $('#upzillaId').val();
        if(upzillaId ==''){$("#unionId").prop("disabled", true);}
$("#upzillaId").change(function(){
        $("#unionId").empty();
        $("#unionId").prepend('<option selected="selected" value="">Please Select Union</option>');
        var upzillaId = $('#upzillaId').val();
        if(upzillaId!==''){$("#unionId").prop("disabled", false);}else{$("#unionId").prop("disabled", true);}
    $.ajax({
        type: 'post',
        url: './unionSendId',//invUnionController
        data: $('form').serialize(),
        dataType: 'json',
        success: function( _response ){
            $.each(_response, function( index, value ){
                    $('#unionId').append("<option value='"+index+"'>"+value+"</option>");
                    //alert(value);
                });
                }
            });
        });
});

$(document).ready(function(){
$("input").keyup(function(){
            var name = $("#name").val();
            if(name){$('#namee').hide();}else{$('#namee').show();}
});
$('select').on('change', function (e) {
    var divisionId = $("#divisionId").val();
    if(divisionId){$('#divisionIde').hide();}else{$('#divisionIde').show();}
     var districtId = $("#districtId").val();
    if(districtId){$('#districtIde').hide();}else{$('#districtIde').show();}
     var upzillaId = $("#upzillaId").val();
    if(upzillaId){$('#upzillaIde').hide();}else{$('#upzillaIde').show();}
    var unionId = $("#unionId").val();
    if(unionId){$('#unionIde').hide();}else{$('#unionIde').show();}
});

});
</script>
