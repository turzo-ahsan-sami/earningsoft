@extends('layouts/acc_layout')
@section('title', '| FDR Register')
@section('content')

<div class="row add-data-form">
    <div class="col-md-12">
        <div class="col-md-2"></div>
            <div class="col-md-8 fullbody">
                <div class="viewTitle" style="border-bottom: 1px solid white;">
                    <a href="{{url('viwRegisterTypeList/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                    </i>Advance Register Type</a>
                </div>
    <div class="panel panel-default panel-border">
      <div class="panel-heading">
           <div class="panel-title">Register Type</div>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-12">
            <div class="col-md-8">
               {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                         
        <div class="form-group">

            {!! Form::label('', 'CODE', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-1 control-label">: </div>
             <div class="col-sm-8">
                {!! Form::text('code', null, ['class'=>'form-control', 'id' => 'regTypeCode']) !!}
               <p id='regTypeCodee' class="error" style="max-height:3px;color: red;"> </p>

             </div>

        </div>


        <div class="form-group">

            {!! Form::label('', 'REGISTER TYPE', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-1 control-label">: </div>
            <div class="col-sm-8">
                {!! Form::text('name', null, ['class'=>'form-control', 'id' => 'accNo']) !!}
                <p id='accNoe' class="error" style="max-height:3px;color: red;"> </p>

            </div>

        </div>
            
                        
        <div class="form-group">
            {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
            <div class="col-sm-9 text-right">
                {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
               <a href="{{url('viwRegisterTypeList/')}}" class="btn btn-danger closeBtn">Close</a>
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

    $(document).ready(function()

                 {
       
                /*Store Information*/
                 $('form').submit(function(event)

                         {

                      $("#add").prop("disabled", true);
                                event.preventDefault();

                            $.ajax({
                                 url: './addAdvRegister',
                                 type: 'POST',
                                 dataType: 'json',
                                 data: $('form').serialize(),
                             })

                             .done(function(data) 

                                {
                                    if(data.errors)
                                        {

                                            if (data.errors['code']) 
                                                       {
                                                               
                                                        $("#regTypeCodee").empty();
                                                        $("#regTypeCodee").append('* '+data.errors['code']);
                                                               
                                                         } 

                                            if (data.errors['name']) 
                                                       {
                                                               
                                                        $("#accNoe").empty();
                                                        $("#accNoe").append('* '+data.errors['name']);
                                                               
                                                         } 
                                        }
                                                     
                                                        
                                                     else{
                                                      
                                                        
                                                     location.href = 'viwRegisterTypeList';

                                                       }
                                });
                   
                     
                        });
                 $(document).on('input','input',function() {
                 $(this).closest('div').find('p').remove();
            });

                 

                });

</script>



@endsection
