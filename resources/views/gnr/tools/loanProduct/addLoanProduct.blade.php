@extends('layouts/gnr_layout')
@section('title', '| Loan Product')
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
            <div class="col-md-3"></div>
                <div class="col-md-6 fullbody">
                    <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{url('gnr/viewLoanProduct/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Loan Product List</a>
                    </div>
                <div class="panel panel-default panel-border">
                                <div class="panel-heading">
                                    <div class="panel-title">Loan Product</div>
                                </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-12">
                            
                            

                            {!! Form::open(['url'=>'','class'=>'form-horizontal form-groups']) !!}
                                
                            
                                <div class="form-group">
                                        {!! Form::label('name', 'Product Name:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                                
                                            {!! Form::text('name', null, array('class'=>'form-control', 'id' => 'name')) !!}
                                            <p id='namee' class="error"></p>
                                        </div>
                                </div>

                                <div class="form-group">
                                        {!! Form::label('productCode', 'Product Code:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                                
                                            {!! Form::text('productCode', null, array('class'=>'form-control', 'id' => 'productCode')) !!}
                                            <p id='productCodee' class="error"></p>
                                        </div>
                                </div>

                                <div class="form-group">
                                        {!! Form::label('donor', 'Donor:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">                                                
                                            {!! Form::select('donor', $donors,null, array('class'=>'form-control', 'id' => 'donor')) !!}
                                            <p id='donore' class="error"></p>
                                        </div>
                                </div>
                          
                                  
                                <div class="form-group">
                                    {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9 text-right">
                                        {!! Form::button('Submit', ['id' => 'submitButton', 'class' => 'btn btn-info']) !!}
                                        <a href="{{url('gnr/viewLoanProduct/')}}" class="btn btn-danger closeBtn">Close</a>
                                        <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                                    </div>
                                </div>
                            
                            {!! Form::close() !!}
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
            <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
            <div class="col-md-3"></div>
    </div>
</div>


<style type="text/css">
    p.error{
        color:red;
    }
</style>



<script type="text/javascript">
    $(document).ready(function() {



        $("#submitButton").on('click',function(event) {

            $.ajax({
                url: './storeLoanProduct',
                type: 'POST',
                dataType: 'json',
                data: $("form").serialize(),
            })
            .done(function(data) {
                
                if (data.errors) {
                    if (data.errors['name']) {
                        $("#namee").empty();
                        $("#namee").append('* '+data.errors['name']);
                    }
                    if (data.errors['productCode']) {
                        $("#productCodee").empty();
                        $("#productCodee").append('* '+data.errors['productCode']);
                    }
                    if (data.errors['donor']) {
                        $("#donore").empty();
                        $("#donore").append('* '+data.errors['donor']);
                    }
                }
                else{
                    location.href = "viewLoanProduct";
                }
                console.log("success");
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
            
            
        }); /*End Submit Button Click*/



       



          /*On input/change Hide the Errors*/
         $(document).on('input', 'input', function() {
             $(this).closest('div').find('.error').empty();
         });
          $(document).on('change', 'select', function() {
             $(this).closest('div').find('.error').empty();
         });
         /*End On input/change Hide the Errors*/



    });/*Ready*/
</script>






@endsection

