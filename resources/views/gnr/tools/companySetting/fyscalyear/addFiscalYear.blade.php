@extends('layouts/gnr_layout')
@section('title', '| Home')
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
        <div class="col-md-2"></div>
            <div class="col-md-8 fullbody">
                <div class="viewTitle" style="border-bottom: 1px solid white;">
                    <a href="{{url('viewFiscalYear/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                    </i>Fiscal List</a>
                </div>
                <div class="panel panel-default panel-border">
                            <div class="panel-heading">
                                <div class="panel-title">New Fiscal Year</div>
                            </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-8">
                    {!! Form::open(array('url' => 'addFiscalYearItem', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                        <!-- <input type = "hidden" name = "_token" value = ""> -->
                        <div class="form-group">
                            {!! Form::label('name', 'Fiscal Year Name:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Fiscal Year Name']) !!}
                                <p id='namee' style="max-height:3px;"></p>
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
                            {!! Form::label('fyStartDate', 'Fiscal Year Start Date:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('fyStartDate', $value = null, ['class' => 'form-control', 'id' => 'fyStartDate', 'type' => 'text', 'placeholder' => 'Enter Fiscal Year Start Date']) !!}
                                <p id='fyStartDatee' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                <a href="{{url('viewGroup/')}}" class="btn btn-danger closeBtn">Close</a>
                                <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                            </div>
                        </div>
                    {!! Form::close()  !!}
                                </div>
                                <div class="col-md-4 emptySpace vert-offset-top-0"><img src="images/catalog/image15.png" width="60%" height="" style="float:right"></div>
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
$( document ).ready(function() {
$(function() {
    $( "#fyStartDate" ).datepicker({
      changeMonth: true,
      changeYear: true
    });
  });

$('form').submit(function( event ) {
    event.preventDefault();
    $.ajax({
         type: 'post',
         url: './addFiscalYearItem',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( _response ){
            if (_response.errors) {
            if (_response.errors['name']) {
                $('#namee').empty();
                $('#namee').append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');
                return false;
            }    
            if (_response.errors['companyId']) {
                $('#companyIde').empty();
                $('#companyIde').append('<span class="errormsg" style="color:red;">'+_response.errors.companyId+'</span>');
                return false;
            }
            if (_response.errors['fyStartDate']) {
                $('#fyStartDatee').empty();
                $('#fyStartDatee').append('<span class="errormsg" style="color:red;">'+_response.errors.fyStartDate+'</span>');
                 return false;
            }
    } else {
        //alert(JSON.stringify(_response));
            $("#name").val('');
            $("#fyStartDate").val('');
            $("#companyId").val('');
            $('.error').addClass("hidden");
            $('#success').text(_response.responseText);
            window.location.href = '{{url('viewFiscalYear/')}}';
            }    
        },
        error: function( _response ){
            // Handle error
            alert(JSON.stringify(_response));
        }
    });
});

$("input").keyup(function(){
            var name = $("#name").val();
            if(name){$('#namee').hide();}else{$('#namee').show();}
});

$('select').on('change', function (e) {
     var companyId = $("#companyId").val();
    if(companyId){$('#companyIde').hide();}else{$('#companyIde').show();} 
});

$("#fyStartDate").blur(function(){
        var fyStartDate = $("#fyStartDate").val();
        if(fyStartDate){$('#fyStartDatee').hide();}else{$('#fyStartDatee').show();}
});

});
</script>  