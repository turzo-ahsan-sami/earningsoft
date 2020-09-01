@extends('layouts/fams_layout')
@section('title', '| Brand')
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
        <div class="col-md-2"></div>
            <div class="col-md-8 fullbody">
                <div class="viewTitle" style="border-bottom: 1px solid white;">
                    <a href="{{url('viewFamsPbrand/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                    </i>Product Brand List</a>
                </div>
                <div class="panel panel-default panel-border">
                        <div class="panel-heading">
                            <div class="panel-title">Brand</div>
                        </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-8">
                    {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                         
                        <div class="form-group">
                            {!! Form::label('name', 'Brand Name:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter product brand name']) !!}
                                <p id='namee' style="max-height:3px;"></p>
                            </div>
                        </div>
                        
                        
                        <div class="form-group">
                            {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9 text-right">
                                {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                <a href="{{url('viewFamsPbrand/')}}" class="btn btn-danger closeBtn">Close</a>
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


<script type="text/javascript">
    $(document).ready(function() {
       
        $('form').submit(function( event ) {
    event.preventDefault();
    $.ajax({
         type: 'post',
         url: './storeFamsPbrand',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( _response ){
            if (_response.accessDenied) {
                showAccessDeniedMessage();
                return false;
            }   
            //alert(JSON.stringify(_response));
    if (_response.errors) {
            if (_response.errors['name']) {
                $('#namee').empty();
                $('#namee').append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');
                return false;
            }
            
    } else {
            
            $('#success').text(_response.responseText);
            window.location.href = '{{url('viewFamsPbrand/')}}';
            }
        },
        error: function( _response ){
            // Handle error
            //alert(JSON.stringify(_response.errors));
            
        }
    });
});
    });
</script>


@endsection