@extends('layouts/pos_layout')
@section('title', '| Add Payment')
@section('content')
<div class="row add-data-form" style="height:100%">
    <div class="col-md-12">
        <div class="col-md-1"></div>
        <div class="col-md-10 fullbody">
            <div class="viewTitle" style="border-bottom: 1px solid white;">
                <a href="{{url('pos/Payments/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                </i>Payment List</a>
            </div>
            <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <div class="panel-title">New Payment</div>
                </div>
                <div class="panel-body">

                    {!! Form::open(array('url' => '' , 'enctype' => 'multipart/form-data',  'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}

                    <div class="row">
                        <div class="col-md-12">

                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('name', 'Name:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter name','autocomplite'=>'off']) !!}
                                        <p id='namee' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('code', 'Code:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('code', $value = null, ['class' => 'form-control', 'id' => 'code', 'type' => 'text', 'placeholder' => 'Enter code','autocomplite'=>'off']) !!}
                                        <p id='codee' style="max-height:3px;"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">

                                <div class="form-group">
                                    {!! Form::label('', 'Ledger Head:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                       <select name="ledgerHeadId" id="ledgerHeadId" class="form-control col-sm-9">
                                            <option value="0">Select Ledger Head</option>
                                            @foreach($ledgerHeads as $ledgerHead)
                                                <option value="{{$ledgerHead->id}}">{{$ledgerHead->name}}</option>
                                            @endforeach
                                        </select>
                                        <p id='ledgerHeadIdMsg' style="max-height:3px;"></p>
                                    </div>
                                </div>

                               <div class="form-group">
                                    {!! Form::label('description', 'Description:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                       <textarea class="form-control" id="description" name="description"></textarea>
                                        <p id='descriptione' style="max-height:3px;"></p>
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
                                {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                <a href="{{url('pos/Payments/')}}" class="btn btn-danger closeBtn">Close</a>
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
    /*Multi Select Start */

    /*Multi Select End*/
    $('#costPrice').on('input', function(event) {
        this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');
    });
    $('#salesPrice').on('input', function(event) {
        this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');
    });
    $('form').submit(function( event ) {
        event.preventDefault();
    
        $.ajax({
            type: 'post',
            url: './addPaymentItem',
            data: $('form').serialize(),
            dataType: 'json',
            success: function( _response ){

                if (_response.errors) {
                    if (_response.errors['name']) {
                        $('#namee').empty();
                        $('#namee').show();
                        $('#namee').append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');
                    }
                    if (_response.errors['code']) {
                        $('#codee').empty();
                        $('#codee').show();
                        $('#codee').append('<span class="errormsg" style="color:red;">'+_response.errors.code+'</span>');
                    }
                    
                    if (_response.errors['description']) {
                        $('#descriptione').empty();
                        $('#descriptione').show();
                        $('#descriptione').append('<span class="errormsg" style="color:red;">'+_response.errors.description+'</span>');
                    }

                    if (_response.errors['ledgerHeadId']) {
                        $('#ledgerHeadIdMsg').empty();
                        $('#ledgerHeadIdMsg').show();
                        $('#ledgerHeadIdMsg').append('<span class="errormsg" style="color:red;">'+_response.errors.ledgerHeadId+'</span>');
                    }

                }
                else {
                     $('#success').text(_response.responseText);
                     window.location.href = '{{url('pos/Payments/')}}';
                }
            },
            error: function( _response ){
                
            }

        });
    });
    /*Data Insert End */
    /*Error Alert remove Ajax Start*/
    $("input").keyup(function(){
        var name = $("#name").val();
        if(name){$('#namee').hide();}else{$('#namee').show();}
        var code = $("#code").val();
        if(code){$('#codee').hide();}else{$('#codee').show();}
        var email = $("#email").val();
        if(email){$('#emaile').hide();}else{$('#emaile').show();}
        var mobile = $("#mobile").val();
        if(mobile){$('#mobilee').hide();}else{$('#mobilee').show();}
        var nid = $("#nid").val();
        if(nid){$('#nide').hide();}else{$('#nide').show();}

    });


});
/*Error Alert remove Ajax End*/
/* Category Change Start*/

</script>
@endsection
