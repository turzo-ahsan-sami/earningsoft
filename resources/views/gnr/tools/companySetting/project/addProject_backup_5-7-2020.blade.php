@extends('layouts/gnr_layout')
@section('title', '| Home')
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
            <div class="col-md-2"></div>
                <div class="col-md-8 fullbody">
                    <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{url('viewProject/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Project List</a>
                    </div>
                <div class="panel panel-default panel-border">
                                <div class="panel-heading">
                                    <div class="panel-title">New Project</div>
                                </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-8">                
                    {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                         <!-- <input type = "hidden" name = "_token" value = ""> -->
                        <div class="form-group">
                            {!! Form::label('name', 'Project Name:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter project name']) !!}
                                <p id='namee' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('projectCode', 'Project Code:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('projectCode', $value = null, ['class' => 'form-control', 'id' => 'projectCode', 'type' => 'text', 'placeholder' => 'Enter project code']) !!}
                                <p id='projectCodee' style="max-height:3px;"></p>
                            </div>
                        </div> 
                        <div class="form-group">
                            {!! Form::label('groupId', 'Group Name:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                <?php 
                                    $groupId = array('' => 'Please Select Group Name') + DB::table('gnr_group')->pluck('name','id')->all(); 
                                ?>      
                                {!! Form::select('groupId', ($groupId), null, array('class'=>'form-control', 'id' => 'groupId')) !!}
                                <p id='groupIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('companyId', 'Company Name:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                <?php 
                                    $companyId = array('' => 'Please Select Company Name') + DB::table('gnr_company')->pluck('name','id')->all(); 
                                ?>   
                                {!! Form::select('companyId', $companyId, null, array('class'=>'form-control', 'id' => 'companyId')) !!}
                                <p id='companyIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                <a href="{{url('viewProject/')}}" class="btn btn-danger closeBtn">Close</a>
                                <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                            </div>
                        </div>
                    {!! Form::close()  !!}
                                </div>
                                <div class="col-md-4 emptySpace vert-offset-top-4"><img src="images/image15.png" width="60%" height="" style="float:right">
                                </div>
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
         url: './addProjectItem',//
         data: $('form').serialize(), // Remember that you need to have your csrf token included
         dataType: 'json',
        success: function( _response ){
            //alert(JSON.stringify(_response));
            if (_response.errors) {
            if (_response.errors['name']) {
                $('#namee').empty();
                $('#namee').append('<span class="errormsg" style="color:red">'+_response.errors.name+'</span>');
                return false;
            }
            if (_response.errors['projectCode']) {
                $('#projectCodee').empty();
                $('#projectCodee').show();
                $('#projectCodee').append('<span class="errormsg" style="color:red">'+_response.errors.projectCode+'</span>');
                return false;
            }
            if (_response.errors['groupId']) {
                $('#groupIde').empty();
                $('#groupIde').append('<span class="errormsg" style="color:red">'+_response.errors.groupId+'</span>');
                return false;
            }
            if (_response.errors['companyId']) {
                $('#companyIde').empty();
                $('#companyIde').append('<span class="errormsg" style="color:red">'+_response.errors.companyId+'</span>');
                return false;
            }
            
    } else {
            $("#groupId").val('');
            $("#companyId").val('');
            $("#name").val('');
            $('.error').addClass("hidden");
            $('#success').text(_response.responseText);
            window.location.href = '{{url('viewProject/')}}';
            }
        },
        error: function( _response ){
            // Handle error
            //alert(_response.errors);
            
        }
    });
});
$("input").keyup(function(){
            var name = $("#name").val();
            if(name){$('#namee').hide();}else{$('#namee').show();}
            var projectCode = $("#projectCode").val();
            if(projectCode){$('#projectCodee').hide();}else{$('#projectCodee').show();}
});
$('select').on('change', function (e) {
    var groupId = $("#groupId").val();
    if(groupId){$('#groupIde').hide();}else{$('#groupIde').show();}
     var companyId = $("#companyId").val();
    if(companyId){$('#companyIde').hide();}else{$('#companyIde').show();}
});

});
</script> 



