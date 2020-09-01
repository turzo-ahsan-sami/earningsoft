@extends('layouts/gnr_layout')
@section('title', '| Route Operation')
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
        <div class="col-md-1"></div>
        <div class="col-md-10 fullbody">
            <div class="viewTitle" style="border-bottom: 1px solid white;">
                <a href="{{url('viewRouteOperation/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                </i>Route Operation List</a>
            </div>
            <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <div class="panel-title">New Route Operation</div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            
                                {!! Form::open(array('url' => 'addRouteOperationItem', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}

                            <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-9">

                                    <div class="form-group">
                                        {!! Form::label('routeName', 'Route Name:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('routeName', $value = null, ['class' => 'form-control', 'id' => 'routeName', 'type' => 'text', 'placeholder' => 'Enter Route Name']) !!}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('moduleId', 'Module:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            @php
                                                $moduleId = array('' => 'Select module') + DB::table('gnr_module')->pluck('name','id')->all(); 
                                            @endphp  
                                            {!! Form::select('moduleId', ($moduleId), null, array('class'=>'form-control', 'id' => 'moduleId')) !!}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('functionId', 'Function Name:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            @php
                                                $functionId = array('' => 'Select Function') + DB::table('gnr_function')->orderBy('name')->pluck('name','id')->all(); 
                                            @endphp  
                                            {!! Form::select('functionId', ($functionId), null, array('class'=>'form-control', 'id' => 'functionId')) !!}
                                        </div>
                                    </div>
                                    

                                    <div class="form-group">
                                        {!! Form::label('subFunctionId', 'Sub Function Name:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            <?php 
                                                $subFunctionId = array('' => 'Select Sub Function') + DB::table('gnr_sub_function')->pluck('subFunctionName','id')->all(); 
                                            ?>      
                                            {!! Form::select('subFunctionId', ($subFunctionId), null, array('class'=>'form-control', 'id' => 'subFunctionId')) !!}
                                            <p id='subFunctionIde' style="max-height:3px;"></p>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('description', 'Description:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::textArea('description', $value = null, ['class' => 'form-control', 'id' => 'description','rows'=>'2', 'placeholder' => 'Enter Description']) !!}
                                        </div>
                                    </div>

                                    <div class="form-group pull-right">
                                        
                                        <div class="col-sm-12">
                                            {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                            <a href="{{url('viewRouteOperation/')}}" class="btn btn-danger closeBtn">Close</a>
                                            <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-md-3 emptySpace vert-offset-top-0"><img src="images/catalog/image15.png" width="80%" height="" style="float:right">
                            </div>
                                
                                
                            </div>
                            </div>  
                                
                                {!! Form::close() !!}
                           
                        </div>
                    </div>
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

$('form').submit(function( event ) {
    $(".error").remove();
    event.preventDefault();
    $.ajax({
         type: 'post',
         url: './addRouteOperationItem',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( data ){
            //alert(JSON.stringify(data));
        if (data.errors) {
                // Print Error
            if(data.errors) {
                $.each(data.errors, function(name, error) {
                     $("#"+name).after("<p class='error' style='color:red;'>* "+data.errors[name]+"</p>");
                });
            }

        } 
        else {
            //$('#success').text(data.responseText);
            window.location.href = '{{url('viewRouteOperation/')}}';
            }
        },
        error: function( data ){
            // Handle error
            alert(data.errors);
        }
    });
});

    /*Filter Function on change Module*/
    $("#moduleId").change(function(event) {
        var moduleId = $(this).val();
        var csrf = "{{csrf_token()}}";
        $.ajax({
            url: './gnrGetFunctionBaseOnModule',
            type: 'POST',
            dataType: 'json',
            data: {moduleId: moduleId, _token: csrf},
        })
        .done(function(functions) {

            $("#functionId").empty();
            $("#functionId").append("<option value=''>Select Function</option>");
            $.each(functions, function(index, obj) {
                 $("#functionId").append("<option value='"+obj.id+"'>"+obj.name+"</option>");
            });
            console.log("success");
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
        
    });
    /*End Filter Function on change Module*/

    // Hide Eddor
    $(document).on('input', 'input', function() {
        $(this).closest('div').find('.error').remove();
    });
    $(document).on('change', 'select', function() {
        $(this).closest('div').find('.error').remove();
    });


}); /*Ready*/

</script> 

