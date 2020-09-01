@extends('layouts/gnr_layout')
@section('title', '| Home')
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
        <div class="col-md-2"></div>
        <div class="col-md-8 fullbody">
            <div class="viewTitle" style="border-bottom: 1px solid white;">
                <a href="{{url('viewSubFunction/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                </i>Sub Functionality List</a>
            </div>
            <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <div class="panel-title">New Sub Function</div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-8">
                                {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                    <div class="form-group">
                                        {!! Form::label('subFunctionName', 'Sub Function Name:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('subFunctionName', $value = null, ['class' => 'form-control', 'id' => 'subFunctionName', 'type' => 'text', 'placeholder' => 'Enter Sub Function Name']) !!}
                                            <p id='subFunctionNamee' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('description', 'Description:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::textarea('description', $value = null, ['class' => 'form-control', 'id' => 'description', 'rows' => 2, 'placeholder' => 'Enter description']) !!}  
                                            <p id='descriptione' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    <div class="form-group pull-right">
                                        
                                        <div class="col-sm-12">
                                            {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                            <a href="{{url('viewSubFunction/')}}" class="btn btn-danger closeBtn">Close</a>
                                            <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                                        </div>
                                    </div>
                                {!! Form::close() !!}
                            </div>
                            <div class="col-md-4 emptySpace vert-offset-top-0"><img src="images/catalog/image15.png" width="80%" height="" style="float:right">
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
         url: './addSubFunctionItem',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( _response ){
    if (_response.errors) {
            if (_response.errors['subFunctionName']) {
                $('#subFunctionNamee').empty();
                $('#subFunctionNamee').append('<span class="errormsg" style="color:red;">'+_response.errors.subFunctionName+'</span>');
                return false;
            }
            if (_response.errors['description']) {
                $('#descriptione').empty();
                $('#descriptione').append('<span class="errormsg" style="color:red;">'+_response.errors.description+'</span>');
            }
    } else {
            $('.error').addClass("hidden");
            $('#success').text(_response.responseText);
            window.location.href = '{{url('viewSubFunction/')}}';
            }
        },
        error: function( _response ){
            // Handle error
            alert(_response.errors);
        }
    });
});

$("input").keyup(function(){
    var subFunctionName = $("#subFunctionName").val();
    if(subFunctionName){$('#subFunctionNamee').hide();}else{$('#subFunctionNamee').show();}
    });
$("textarea").keyup(function(){
    var description = $("#description").val();
    if(description){$('#descriptione').hide();}else{$('#descriptione').show();}
    });

});

</script> 

