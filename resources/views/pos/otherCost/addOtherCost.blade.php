@extends('layouts/pos_layout')
@section('title', '| Add Other Cost')
@section('content')
<div class="row add-data-form" style="height:100%">
    <div class="col-md-12">
        <div class="col-md-1"></div>
        <div class="col-md-10 fullbody">
            <div class="viewTitle" style="border-bottom: 1px solid white;">
                <div class="panel-options">
                    <a href="{!! url('/pos/otherCostList'); !!}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Other Cost List</a>
                </div>
               
            </div>
            <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <div class="panel-title">Add Other Cost</div>
                </div>
               
                <div class="panel-body">

                    {!! Form::open(array('url' => '' , 'enctype' => 'multipart/form-data',  'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                    
                    <div class="row">
                        <div class="col-md-12">

                            <div class="col-md-6">

                                <div class="form-group">
                                    {!! Form::label('Other Cost', 'Other Cost:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {!! Form::text('other_cost', '', ['class' => 'form-control', 'id' => 'other_cost', 'type' => 'text', 'placeholder' => 'Other Cost','autocomplete'=>'off']) !!}
                                        <p id='other_cost_msg' style="max-height:4px; color:red;"></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('Ledger', 'Ledger:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        <select id="ledger" class="form-control" name="ledger">
                                            <option value='0'>Select Ledger</option>
                                            @foreach($ledgers as $record)
                                                <option value='{{ $record->ledgerId }}'>{{ $record->ledger}}</option>
                                            @endforeach
                                        </select>
                                        <p id='ledger_msg' style="max-height:3px;"></p>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="form-group col-md-4 text-center">
                            {!! Form::label('submit', ' ', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-12">
                                {!! Form::submit('Save', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                <a href="{!! url('/pos/otherCostList'); !!}" class="btn btn-danger closeBtn">Close</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <span id="success" style="color:green; font-size:20px;"></span>
                        </div>
                    </div>

                    {!! Form::close() !!}

                </div>
            </div>
            <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>


<script type="text/javascript">

/*Data Insert Start*/
$(document).ready(function(){

  
    $('form').submit(function( event ) {
        event.preventDefault();
    
        $.ajax({
            type: 'post',
            url: './insertOtherCost',
            data: $('form').serialize(),
            dataType: 'json',
            success: function( _response ){

                if (_response.errors) {
                    if (_response.errors['other_cost']) {
                        $('#other_cost_msg').empty();
                        $('#other_cost_msg').show();
                        $('#other_cost_msg').append('<span class="errormsg" style="color:red;">'+_response.errors.other_cost+'</span>');
                    }
                    if (_response.errors['ledger']) {
                        $('#ledger_msg').empty();
                        $('#ledger_msg').show();
                        $('#ledger_msg').append('<span class="errormsg" style="color:red;">'+_response.errors.ledger+'</span>');
                    }
                }
                else {
                     $('#success').text(_response.msg);
                     window.location.href = '{{url('pos/otherCostList/')}}';
                }
            },
            error: function( _response ){
                
            }

        });
    });
    /*Data Insert End */


});
/*Error Alert remove Ajax End*/
/* Category Change Start*/

</script>
@endsection
