@extends('layouts/fams_layout')
@section('title', '| Prefix')
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
            <div class="col-md-2"></div>
                <div class="col-md-8 fullbody">
                    <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{url('viewFamsProductPrefix/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Prefix List</a>
                    </div>
                <div class="panel panel-default panel-border">
                                <div class="panel-heading">
                                    <div class="panel-title">Product Prefix</div>
                                </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-8">
                                    {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                         
                                        <div class="form-group">
                                            {!! Form::label('name', 'Prefix Name:', ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-9">
                                                {!! Form::text('name',null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Prefix Name']) !!}
                                                <p id='namee' style="max-height:3px;"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('prefixCode', 'Prefix Code:', ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-9">
                                                {!! Form::text('code', null, ['class' => 'form-control', 'id' => 'prefixCode', 'type' => 'text', 'placeholder' => 'Enter Prefix Code']) !!}
                                               <p id='prefixCodee' style="max-height:3px;"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-9 text-right">
                                                {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                                <a href="{{url('viewFamsProductPrefix/')}}" class="btn btn-danger closeBtn">Close</a>
                                                <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                                            </div>
                                        </div>
                                    {!! Form::close() !!}
                                </div>
                                <div class="col-md-4 emptySpace vert-offset-top-0"><img src="images/catalog/image15.png" width="40%" height="" style="float:right"></div>
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
         url: './storeFamsProductFrefix',
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
            if (_response.errors['code']) {
                $('#prefixCodee').empty();
                 $('#prefixCodee').show();
                $('#prefixCodee').append('<span class="errormsg" style="color:red;">'+_response.errors.code+'</span>');
                return false;
            }
    } else {
            
            location.href = '{{url('viewFamsProductPrefix/')}}';
            }
        },
        error: function( _response ){
            // Handle error
            //alert(JSON.stringify(_response.errors));
            
        }
    });
});

$("input").keyup(function(){
            var name = $("#name").val();
            if(name){$('#namee').hide();}else{$('#namee').show();}
            var groupCode = $("#prefixCode").val();
            if(groupCode){$('#prefixCodee').hide();}else{$('#prefixCodee').show();}
});

$("#groupCode").on('input', function() {    
    this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
});

});
</script> 
 


