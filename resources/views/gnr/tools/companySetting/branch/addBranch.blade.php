@extends('layouts/gnr_layout')
@section('title', '| New Group')
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
            <div class="col-md-1"></div>
                <div class="col-md-10 fullbody">
                    <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{url('viewBranch/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Branch List</a>
                    </div>
                <div class="panel panel-default panel-border">
                                <div class="panel-heading">
                                    <div class="panel-title">New Branch</div>
                                </div>
                    <div class="panel-body">
                        
                                <!-- <div class="col-md-8"> -->
                            {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                <div class="row">
                    <div class="col-md-6">    
                            <div class="form-group">
                                {!! Form::label('name', 'Branch Name:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter branch name']) !!}
                                    <p id='namee' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('branchCode', 'Branch Code:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('branchCode', $value = null, ['class' => 'form-control', 'id' => 'branchCode', 'type' => 'text', 'placeholder' => 'Enter branc code']) !!}
                                   <p id='branchCodee' style="max-height:3px;"></p>
                                </div>
                            </div>
                            {{-- <div class="form-group">
                                {!! Form::label('groupId', 'Group Name:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                     
                                        $groupId = array('' => 'Please Select Group Name') + DB::table('gnr_group')->pluck('name','id')->all(); 
                                          
                                    {!! Form::select('groupId', ($groupId), null, array('class'=>'form-control', 'id' => 'groupId')) !!}
                                   <p id='groupIde' style="max-height:3px;"></p>
                                </div>
                            </div> --}}
                        
                            <div class="form-group">
                                {!! Form::label('projectId', 'Project Name:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    <?php 
                                        $projectId = array('' => 'Please Select Project Name') + DB::table('gnr_project')->where('companyId', Auth::user()->company_id_fk)->pluck('name','id')->all(); 
                                    ?>
                                    {!! Form::select('projectId', $projectId, null, array('class'=>'form-control', 'id' => 'projectId')) !!}
                                     <p id='projectIde' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group" hidden>
                                {!! Form::label('projectTypeId', 'Project Type:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                <?php 
                                    $projectTypeId = array('' => 'Please Select') + DB::table('gnr_project_type')->where('companyId', Auth::user()->company_id_fk)->pluck('name','id')->all(); 
                                ?>
                                    {!! Form::select('projectTypeId', $projectTypeId, null, array('class'=>'form-control', 'id' => 'projectTypeId')) !!}
                                    <p id='projectTypeIde' style="max-height:3px;"></p>
                                </div>
                            </div>  
                            <div class="form-group">
                                {!! Form::label('contactPerson', 'Contact Person:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('contactPerson', $value = null, ['class' => 'form-control', 'id' => 'contactPerson', 'type' => 'text', 'placeholder' => 'Enter contact person name']) !!}
                                    <p id='contactPersone' style="max-height:3px;"></p>
                                </div>
                            </div> 
                             <div class="form-group">
                                {!! Form::label('phone', 'Mobile:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('phone', $value = null, ['class' => 'form-control', 'id' => 'phone', 'type' => 'text', 'placeholder' => 'Enter phone number']) !!}
                                   <p id='phoneed' style="max-height:3px;"></p>
                                </div>
                            </div>  
                    </div>
                    <div class="col-md-6">    
                            <div class="form-group">
                                {!! Form::label('email', 'Email:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('email', $value = null, ['class' => 'form-control', 'id' => 'email', 'type' => 'text', 'placeholder' => 'Enter email address']) !!}
                                    <p id='emaile' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('address', 'Address:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::textarea('address', $value = null, ['class' => 'form-control', 'id' => 'address', 'rows' => 2, 'type' => 'text', 'placeholder' => 'Enter address']) !!}
                                    <p id='addresse' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('branchOpeningDate', 'B. Opening Date:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('branchOpeningDate', $value = null, ['class' => 'form-control', 'id' => 'branchOpeningDate', 'type' => 'text', 'placeholder' => 'Enter Branch Opening Date:']) !!}
                                   <p id='branchOpeningDatee' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('softwareStartDate', 'S. Opening Date:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('softwareStartDate', $value = null, ['class' => 'form-control', 'id' => 'softwareStartDate', 'type' => 'text', 'placeholder' => 'Enter Software Opening Date']) !!}
                                    <p id='softwareStartDatee' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('status', 'Status:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">     
                                    {!! Form::select('status', array('1'=>'Active', '0'=>'Inactive'), null, array('class'=>'form-control', 'id' => 'status')) !!}
                                   <p id='statuse' style="max-height:3px;"></p>
                                </div>
                            </div>
                    </div>

                        
                            <div class="form-group">
                                {!! Form::label('submit', ' ', ['class' => 'col-sm-0 control-label']) !!}
                                <div class="col-sm-12 text-center">
                                    {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                    <a href="{{url('viewBranch/')}}" class="btn btn-danger closeBtn">Close</a>
                                    <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                                </div>
                            </div>
                          
                </div>  
                            {!! Form::close() !!}
                            <!-- </div>
                                <div class="col-md-4 emptySpace vert-offset-top-12"></div> -->
                            
                        
                    </div>
                </div>
            <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>
@endsection 

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function(){

 $(function() {
    $( "#branchOpeningDate" ).datepicker({
      changeMonth: true,
      changeYear: true
    });
  });
 $(function() {
    $( "#softwareStartDate" ).datepicker({
      changeMonth: true,
      changeYear: true
    });
  });

$('form').submit(function( event ) {
    event.preventDefault();
    $.ajax({
         type: 'post',
         url: './addBranchItem',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( _response ){
			//alert(JSON.stringify(_response));

            if (_response.errors) {
            if (_response.errors['name']) {
                $('#namee').empty();
                $('#namee').append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');  
                return false;
            }
            if (_response.errors['branchCode']) {
                $('#branchCodee').empty();
                $('#branchCodee').show();
                $('#branchCodee').append('<span class="errormsg" style="color:red;">'+_response.errors.branchCode+'</span>');  
                return false;
            }   
            if (_response.errors['groupId']) {
                $('#groupIde').empty();
                $('#groupIde').append('<span class="errormsg" style="color:red;">'+_response.errors.groupId+'</span>');
                 return false;
            }
            if (_response.errors['companyId']) {
                $('#companyIde').empty();
                $('#companyIde').append('<span class="errormsg" style="color:red;">'+_response.errors.companyId+'</span>');
                return false;
            }
            if (_response.errors['projectId']) {
                $('#projectIde').empty();
                $('#projectIde').append('<span class="errormsg" style="color:red;">'+_response.errors.projectId+'</span>');
                return false;
            }
            if (_response.errors['projectTypeId']) {
                $('#projectTypeIde').empty();
                $('#projectTypeIde').append('<span class="errormsg" style="color:red;">'+_response.errors.projectTypeId+'</span>');
                return false;
            }
           
    } else {
            //alert(JSON.stringify(_response));
            $("#name").val('');
            $("#groupId").val('');
            $("#companyId").val('');
            $("#projectId").val('');
            $("#projectTypeId").val('');
            $("#branchCode").val('');
            $("#branchOpeningDate").val('');
            $("#softwareStartDate").val('');
            $('.error').addClass("hidden");
            $('#success').text(_response.responseText);
            window.location.href = '{{url('viewBranch/')}}';
            }
        },
        error: function( _response ){
            // Handle error
            alert(_response.errors);
        }
    });
});

$("input").keyup(function(){
            var name = $("#name").val();
            if(name){$('#namee').hide();}else{$('#namee').show();}
            var branchCode = $("#branchCode").val();
            if(branchCode){$('#branchCodee').hide();}else{$('#branchCodee').show();}
             //var contactPerson = $("#contactPerson").val();
            // if(contactPerson){$('#contactPersone').hide();}else{$('#contactPersone').show();}
            //  var email = $("#email").val();
            // if(email){$('#emaile').hide();}else{$('#emaile').show();}
            //  var phone = $("#phone").val();
            // if(phone){$('#phonee').hide();}else{$('#phonee').show();}
            // var email = $("#email").val();
            // if(emaile){$('#emaile').hide();}else{$('#emaile').show();}
});
$("textarea").keyup(function(){
    var address = $("#address").val();
    if(address){$('#addresse').hide();}else{$('#addresse').show();}
});
$('select').on('change', function (e) {
    var groupId = $("#groupId").val();
    if(groupId){$('#groupIde').hide();}else{$('#groupIde').show();}
     var companyId = $("#companyId").val();
    if(companyId){$('#companyIde').hide();}else{$('#companyIde').show();}
     var projectId = $("#projectId").val();
    if(projectId){$('#projectIde').hide();}else{$('#projectIde').show();}
     var projectTypeId = $("#projectTypeId").val();
    if(projectTypeId){$('#projectTypeIde').hide();}else{$('#projectTypeIde').show();}
    
});


});

$(document).ready(function(){

$("#projectId").change(function(){
        $("#projectTypeId").empty();
        //$("#projectTypeId").prepend('<option selected="selected" value="">Please Select</option>');
        var projectId = $('#projectId').val();
        
    $.ajax({
        type: 'post',
        url: './projectIdSendGetPTypeId',
        data: {
            '_token': $('input[name=_token]').val(),
            'projectId' : projectId
        },
        dataType: 'json',   
        success: function( _response ){
            //alert(JSON.stringify(_response));
        $.each(_response, function( index, value ){
                    $('#projectTypeId').append("<option value='"+index+"'>"+value+"</option>");
                    //alert(value);
                });
                }
            });
        });
});
</script> 

