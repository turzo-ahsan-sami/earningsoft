@extends('layouts/gnr_layout')
@section('title', '| Bank')
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
            <div class="col-md-3"></div>
                <div class="col-md-6 fullbody">
                    <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{url('viewBank/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Bank/Donor List</a>
                    </div>
                <div class="panel panel-default panel-border">
                                <div class="panel-heading">
                                    <div class="panel-title">Bank/Donor</div>
                                </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-12">
                            
                            <div class="form-horizontal form-groups">
                                
                            
                                <div class="form-group">
                                        {!! Form::label('bankName', 'Bank/Donor Name:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                                
                                            {!! Form::text('bankName', null, array('class'=>'form-control', 'id' => 'bankName')) !!}
                                            <p id='bankNamee' style="color:red;"></p>
                                        </div>
                                </div>
                                <div class="form-group">
                                        {!! Form::label('bankShortName', 'Bnak/Donor Short Name:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                                
                                            {!! Form::text('bankShortName', null, array('class'=>'form-control', 'id' => 'bankShortName')) !!}
                                            <p id='bankShortNamee' style="color:red;"></p>
                                        </div>
                                </div> 

                                <div class="form-group">
                                        {!! Form::label('type', 'Type:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                        @php
                                            $typeList = array(''=>'Select Type','0'=>'Bank','1'=>'Donor');
                                        @endphp
                                                
                                            {!! Form::select('type', $typeList,null, array('class'=>'form-control', 'id' => 'type')) !!}
                                            <p id='typee' style="color:red;"></p>
                                        </div>
                                </div>                              
                                  
                                <div class="form-group">
                                    {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9 text-right">
                                        {!! Form::button('Submit', ['id' => 'submitButton', 'class' => 'btn btn-info']) !!}
                                        <a href="{{url('viewBank/')}}" class="btn btn-danger closeBtn">Close</a>
                                        <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                                    </div>
                                </div>
                            </div>
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



<script type="text/javascript">
    $(document).ready(function() {
        $("#submitButton").on('click',function(event) {
            var bankName = $("#bankName").val();
            var bankShortName = $("#bankShortName").val();
            var type = $("#type").val();
            var csrf = "{{csrf_token()}}";

            $.ajax({
                url: './storeBank',
                type: 'POST',
                dataType: 'json',
                data: {bankName: bankName, bankShortName: bankShortName, type: type, _token: csrf},
            })
            .done(function(data) {
                if (data.errors) {
                    if (data.errors['bankName']) {
                        $("#bankNamee").empty();
                        $("#bankNamee").append('* '+data.errors['bankName']);
                    }
                    if (data.errors['bankShortName']) {
                        $("#bankShortNamee").empty();
                        $("#bankShortNamee").append('* '+data.errors['bankShortName']);
                    }
                    if (data.errors['type']) {
                        $("#typee").empty();
                        $("#typee").append('* '+data.errors['type']);
                    }
                }
                else{
                    location.href = "viewBank";
                }
                console.log("success");
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
            
            
        });
    });
</script>






@endsection

