@extends('layouts/acc_layout')
@section('title', '| OTS Period')
@section('content')

<div class="row add-data-form">
 <div class="col-md-12">
 <div class="col-md-2"></div>
 <div class="col-md-8 fullbody">
  <div class="viewTitle" style="border-bottom: 1px solid white;">
    <a href="{{url('viewOtsRegisterPeriod/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
    </i>OTS Period List</a>
 </div>
    <div class="panel panel-default panel-border">
      <div class="panel-heading">
        <div class="panel-title">OTS Period List</div>
      </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-8">
                                    {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                         
    <div class="form-group">
        {!! Form::label('name', ' Name:', ['class' => 'col-sm-3 control-label']) !!}
     <div class="col-sm-9">
      {!! Form::text('name',null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Period Name']) !!}
      <p id='namee' style="max-height:3px;"></p>
     </div>
    </div>

 <div class="form-group">
  {!! Form::label('interestRate', 'Period Interest Rate:', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
    {!! Form::text('interestRate',null, ['class' => 'form-control', 'id' => 'interestRate', 'type' => 'text', 'placeholder' => 'Enter Period Interest Rate']) !!}
    <p id='interestRatee' style="max-height:3px;"></p>
  </div>
</div>

     <div class="form-group">
      {!! Form::label('months', 'Period Month:', ['class' => 'col-sm-3 control-label']) !!}
      <div class="col-sm-9">
      {!! Form::select('months',['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10','11'=>'11','12'=>'12',],null, ['class' => 'form-control', 'id' => 'month', 'type' => 'text', 'placeholder' => 'Enter Period Month']) !!}
      <p id='monthe' style="max-height:3px;"></p>
      </div>
     </div>

                                        <div class="form-group">
                                            {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-9 text-right">
                                                {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                                <a href="{{url('viewOtsRegisterPeriod/')}}" class="btn btn-danger closeBtn">Close</a>
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

<script type="text/javascript">
    $(document).ready(function() {
       
        /*Submit the form*/
        $("form").submit(function(event) {

            event.preventDefault();
            
            $.ajax({
                 type: 'post',
                 url: './storeOtsRegisterPeriod',
                 data: $('form').serialize(),
                 dataType: 'json',
                success: function(data){

                 if (data.errors) {
                    if (data.errors['name']) {
                        $('#namee').empty();
                        $('#namee').show();
                        $('#namee').append('<span class="errormsg" style="color:red;">'+data.errors.name+'</span>');
                        //return false;
                    }
                    if (data.errors['interestRate']) {
                        $('#interestRatee').empty();data
                        $('#interestRatee').show();
                        $('#interestRatee').append('<span class="errormsg" style="color:red;">'+data.errors.interestRate+'</span>');
                        //return false;
                    }
                    if (data.errors['months']) {
                        $('#monthe').empty();
                        $('#monthe').show();
                        $('#monthe').append('<span class="errormsg" style="color:red;">'+data.errors.months+'</span>');
                        //return false;
                    }
                    
            } else {
                   
                    window.location.href = '{{url('viewOtsRegisterPeriod/')}}';
                    }
                },
                error: function( data ){
                    
                    alert('error');
                    
                }
            });
        });
        /*End Submit the form*/

      $("#name").on('input',function(){
         $('#namee').empty();
            
     });
       $("#interestRate").on('input',function(){
         $('#interestRatee').empty();
            
     });
        $("#month").on('change',function(){
         $('#monthe').empty();
            
     });


        

    });/*End Ready*/


</script>


@endsection
