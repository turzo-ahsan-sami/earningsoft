@extends('layouts/gnr_layout')
@section('title', '| Function')
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
        <div class="col-md-2"></div>
        <div class="col-md-8 fullbody">
            <div class="viewTitle" style="border-bottom: 1px solid white;">
                <a href="{{url('viewFunction/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                </i>Function List</a>
            </div>
            <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <div class="panel-title">New Function</div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-8">
                                {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                    <div class="form-group">
                                        {!! Form::label('name', 'Function Name:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Function Name']) !!}
                                            
                                        </div>
                                    </div>
                                    @php
                                        $moduleList = array(''=>'Select Module') + DB::table('gnr_module')->pluck('name','id')->toArray();
                                    @endphp
                                    <div class="form-group">
                                        {!! Form::label('moduleId', 'Module:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::select('moduleId', $moduleList, null, ['class' => 'form-control', 'id' => 'moduleId',]) !!}
                                            
                                        </div>
                                    </div>

                                    {{-- Function Code --}}
                                    <div class="form-group">
                                        {!! Form::label('functionCode', 'Function Code:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('functionCode', null, ['class' => 'form-control', 'id' => 'functionCode','maxlength'=>5,'placeholder' => 'Enter Function Code']) !!}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('description', 'Description:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::textarea('description', $value = null, ['class' => 'form-control', 'id' => 'description', 'rows' => 2, 'placeholder' => 'Enter description']) !!}  
                                            
                                        </div>
                                    </div>
                                    <div class="form-group pull-right">
                                       
                                        <div class="col-sm-12">
                                            {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                            <a href="{{url('viewFunction/')}}" class="btn btn-danger closeBtn">Close</a>
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

<style type="text/css">
    .error{
        color: red;
    }
</style>

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function(){

$('form').submit(function( event ) {
    event.preventDefault();
    $(".error").remove();
    $.ajax({
         type: 'post',
         url: './storeFunction',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( _response ){
    if (_response.errors) {

        // Print Error
        if(_response.errors) {
            $.each(_response.errors, function(name, error) {
                 $("#"+name).after("<p class='error'>* "+_response.errors[name]+"</p>");
            });
        }
            
    } else {
            $('.error').addClass("hidden");
            $('#success').text(_response.responseText);
            window.location.href = '{{url('viewFunction/')}}';
            }
        },
        error: function(){
            // Handle error
            alert('Error');
        }
    });
});


// Hide Eddor
$(document).on('input', 'input', function() {
    $(this).closest('div').find('.error').remove();
});
$(document).on('change', 'select', function() {
    $(this).closest('div').find('.error').remove();
});


}); /*Ready*/

</script> 

