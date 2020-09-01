@extends('layouts/gnr_layout')
@section('title', '| Fiscal Year')
@section('content')
    @include('successMsg')


    @php
    $foreignGroupIds = DB::table('inv_product')->distinct()->pluck('groupId')->toArray();
@endphp

<style media="screen">
    .disabled {
        pointer-events: none;
        cursor: default;
        opacity: 0.6;
    }
</style>


<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">
                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading" style="padding-bottom:0px">
                        <div class="panel-options">
                            <a href="{{url('addFiscalYear/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Fiscal Year</a>
                        </div>
                        <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">FISCAL YEAR LIST</font></h1>
                    </div>
                    <div class="panel-body panelBodyView">
                        <div>
                            <script type="text/javascript">
                            jQuery(document).ready(function($)
                            {
                                $("#gnrFiscalYrView").dataTable().yadcf([

                                ]);
                            });
                            </script>
                        </div>
                        <table class="table table-striped table-bordered" id="gnrFiscalYrView">
                            <thead>
                                <tr>
                                    <th width="32">SL#</th>
                                    <th>Fiscal Year Name</th>
                                    <th>Company Name</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th width="40">Action</th>
                                </tr>
                                {{ csrf_field() }}
                            </thead>
                            <tbody>
                                @php
                                    $no=0;
                                @endphp
                                @foreach($fiscalYears as $fiscalYear)

                                    <tr class="item{{$fiscalYear->id}}">
                                        <td  class="text-center slNo">{{++$no}}</td>
                                        <td>{{$fiscalYear->name}}</td>
                                        <td>
                                            <?php
                                            $CompanyName = DB::table('gnr_company')->select('name')->where('id',$fiscalYear->companyId)->first();
                                            ?>
                                            {{$CompanyName->name}}
                                        </td>
                                        <td>{{$fiscalYear->fyStartDate}}</td>
                                        <td>{{$fiscalYear->fyEndDate}}</td>
                                        <td  class='text-center'>
                                            <a href="javascript:;" class="edit-modal" data-id="{{$fiscalYear->id}}" data-name="{{$fiscalYear->name}}" data-companyid="{{$fiscalYear->companyId}}" data-fystartdate="{{$fiscalYear->fyStartDate}}" data-slno="{{$no}}">
                                                <span class="glyphicon glyphicon-edit"></span>
                                            </a>&nbsp
                                                <a href="javascript:;" class="delete-modal @php if($checkTransactionForYear[$fiscalYear->id] == 1) { echo 'disabled'; } @endphp" data-id="{{$fiscalYear->id}}">
                                                    <span class="glyphicon glyphicon-trash"></span>
                                                </a>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


<div id="myModal" class="modal fade" style="margin-top:3%">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="clear:both"></h4>
            </div>
            <div class="modal-body">
                {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                <!-- <input type = "hidden" name = "_token" value = ""> -->
                <div class="form-group hidden">
                    {!! Form::label('id', 'ID:', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-10">
                        {!! Form::text('id', $value = null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text', 'readonly']) !!}
                        {!! Form::text('slno', $value = null, ['class' => 'form-control', 'id' => 'slno', 'type' => 'text', 'readonly']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('name', 'Fiscal Year Name:', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-10">
                        {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Fiscal Year Name']) !!}
                        <p id='namee' style="max-height:3px;"></p>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('companyId', 'Company Name:', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-10">
                        <?php
                        $companyId = array('' => 'Please Select Company Name') + DB::table('gnr_company')->pluck('name','id')->all();
                        ?>
                        {!! Form::select('companyId', $companyId, null, array('class'=>'form-control', 'id' => 'companyId')) !!}
                        <p id='companyIde' style="max-height:3px;"></p>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('fyStartDate', 'Fiscal Year Start Date:', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-10">
                        {!! Form::text('fyStartDate', $value = null, ['class' => 'form-control', 'id' => 'fyStartDate', 'type' => 'text', 'placeholder' => 'Enter Fiscal Year Start Date']) !!}
                        <p id='fyStartDatee' style="max-height:3px;"></p>
                    </div>
                </div>
                {!! Form::close()  !!}
                <div class="deleteContent" style="padding-bottom:20px;">
                    <h4>You are about to delete this item this procedure is irreversible !</h4>
                    <h4>Do you want to proceed ?</h4>
                    <span class="hidden id"></span>
                </div>
                <div class="modal-footer">
                    <p id="MSGE" class="pull-left" style="color:red"></p>
                    <p id="MSGS" class="pull-left" style="color:green"></p>
                    {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn', 'id' => 'footer_action_button'] ) !!}
                    {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn',  'data-dismiss' => 'modal', 'id' => 'footer_action_button2'] ) !!}

                    {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-warning', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_dismis'] ) !!}
                </div>
            </div>
        </div>
    </div>
</div>
@include('dataTableScript')
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

    $(document).on('click', '.edit-modal', function() {
        $('.errormsg').empty();
        $('#MSGE').empty();
        $('#MSGS').empty();
        $('#footer_action_button').text(" Update");
        $('#footer_action_button').addClass('glyphicon glyphicon-check');
        //$('#footer_action_button').removeClass('glyphicon-trash');
        $('#footer_action_button_dismis').text(" Close");
        $('#footer_action_button_dismis').addClass('glyphicon glyphicon-remove');
        $('.actionBtn').addClass('btn-success');
        $('.actionBtn').removeClass('btn-danger');
        $('.actionBtn').addClass('edit');
        $('.modal-title').text('Update Data');
        $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
        $('.modal-dialog').css('width','50%');
        $('.deleteContent').hide();
        $('.form-horizontal').show();
        $('#id').val($(this).data('id'));
        $('#slno').val($(this).data('slno'));
        $('#name').val($(this).data('name'));
        $('#companyId').val($(this).data('companyid'));
        $('#fyStartDate').val($(this).data('fystartdate'));
        $('#footer_action_button2').hide();
        $('#footer_action_button').show();
        $('.actionBtn').removeClass('delete');
        $('#myModal').modal('show');
    });
    // Edit Data (Modal and function edit data)
    $('.modal-footer').on('click', '.edit', function() {
        $.ajax({
            type: 'post',
            url: './editFiscalYearItem',
            data: $('form').serialize(),
            dataType: 'json',
            success: function( data ){
                if(data.errors){
                    //alert(JSON.stringify(data));
                    if (data.errors['name']) {
                        $('#namee').empty();
                        $('#namee').append('<span class="errormsg" style="color:red;">'+data.errors.name+'</span>');
                        return false;
                    }
                    if (data.errors['companyId']) {
                        $('#companyIde').empty();
                        $('#companyIde').append('<span class="errormsg" style="color:red;">'+data.errors.companyId+'</span>');
                        return false;
                    }
                    if (data.errors['fyStartDate']) {
                        $('#fyStartDatee').empty();
                        $('#fyStartDatee').append('<span class="errormsg" style="color:red;">'+data.errors.fyStartDate+'</span>');
                        return false;
                    }
                }
                else{
                    $('#MSGE').addClass("hidden");
                    $('#MSGS').text('Data successfully inserted!');
                    $('#myModal').modal('hide');
                    //alert(JSON.stringify(data));
                    $('.item' + data["fiscalyear"].id).replaceWith(
                        "<tr class='item" + data["fiscalyear"].id + "'><td  class='text-center slNo'>" + data.slno +
                        "</td><td class='hidden'>" + data["fiscalyear"].id +
                        "</td><td>" + data["fiscalyear"].name +
                        "</td><td>" + data["companyName"].name +
                        "</td><td>" + data["fiscalyear"].fyStartDate +
                        "</td><td>" + data["fiscalyear"].fyEndDate +
                        "</td><td  class='text-center'><a href='javascript:;' class='edit-modal' data-id='" + data["fiscalyear"].id + "' data-name='" + data["fiscalyear"].name + "' data-companyid='" + data["fiscalyear"].companyId + "'  data-fystartdate='" + data["fiscalyear"].fyStartDate + "' data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["fiscalyear"].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
                        $('.succsMsg').removeClass('hidden');
                        $('.succsMsg').show();
                        setTimeout(function(){ $('.succsMsg').hide(); }, 5000);
                    }
                    $("#name").val('');
                    $("#fystartdate").val('');
                    $("#companyId").val('');
                },
                error: function( data ){
                    // Handle error
                    //alert('hi');
                }
            });
        });

        //delete function
        $(document).on('click', '.delete-modal', function() {
            $('#MSGE').empty();
            $('#MSGS').empty();
            $('#footer_action_button2').text(" Yes");
            $('#footer_action_button2').removeClass('glyphicon glyphicon-check');
            //$('#footer_action_button').addClass('glyphicon-trash');
            $('#footer_action_button_dismis').text(" No");
            $('#footer_action_button_dismis').removeClass('glyphicon glyphicon-remove');
            $('.actionBtn').removeClass('edit');
            $('.actionBtn').removeClass('btn-success');
            $('.actionBtn').addClass('btn-danger');
            $('.actionBtn').addClass('delete');
            $('.modal-title').text('Delete');
            $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
            $('.modal-dialog').css('width','50%');
            $('.id').text($(this).data('id'));
            $('.deleteContent').show();
            $('.form-horizontal').hide();
            $('#footer_action_button2').show();
            $('#footer_action_button').hide();
            $('#myModal').modal('show');
        });

        $('.modal-footer').on('click', '.delete', function() {
            $.ajax({
                type: 'post',
                url: './deleteFiscalYearItem',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'id': $('.id').text()
                },
                success: function(data) {
                    $('.item' + $('.id').text()).remove();
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

    });//ready function end
    </script>
