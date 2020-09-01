@extends('layouts/pos_layout')
@section('title', '| Customer')
@section('content')
    @include('successMsg')
    <style type="text/css">
    .select2-results__option[aria-selected=true] {
        display: none;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">
                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading" style="padding-bottom:0px">
                        <div class="panel-options">
                            <a href="{{url('pos/addCustomer/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Customer</a>
                        </div>


                        <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">CUSTOMER LIST</font></h1>
                    </div>
                    <div class="panel-body panelBodyView">
                        <div>
                            <div>

                                <script type="text/javascript">
                                jQuery(document).ready(function($)  {
                                    $("#customerView").dataTable({
                                        "oLanguage": {
                                            "sEmptyTable": "No Records Available",
                                            "sLengthMenu": "Show _MENU_ "
                                        }
                                    });
                                });

                                </script>

                            </div>
                        </div>
                        <table class="table table-striped table-bordered" id="customerView" style="color:black;">
                            <thead>
                                <tr>
                                    <th width="80">SL#</th>
                                    <th>Customer Name</th>
                                    <th>Code</th>
                                   <!--  <th>NID</th> -->
                                    <th>Mobile No</th>
                                   <!--  <th>Image</th> -->
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                                {{ csrf_field() }}
                            </thead>
                            <tbody>
                                <?php $no=0; ?>
                                @foreach($customers as $customer)
                                    <tr class="item{{$customer->id}}">
                                        <td>{{++$no}}</td>
                                        <td style="text-align: left; padding-left: 5px;">{{$customer->name}}</td>
                                        <td style="text-align: center; ">{{$customer->code}}</td>
                                        <!-- <td style="text-align: left; padding-left: 5px;font-weight: bold;">{{$customer->nid}}</td> -->

                                        <td style="text-align: center;">{{$customer->mobile}}</td>
                                        <!--  <td style="text-align: left; padding-left: 5px;font-weight: bold;" height="20px"><img src="{{asset('/customer-image/'.$customer->customerImg)}}" height="20px" width="20px"></td> -->
                                        <td style="text-align: left; padding-left: 5px;">{{$customer->email}}</td>
                                        <td class="text-center" width="100">

                                            <a href="javascript:;" class="form5" data-token="" data-id="{{$customer->id}}">
                                                <span class="fa fa-eye"></span>
                                            </a>

                                            &nbsp
                                            <a href="javascript:;" class="edit-modal" customerId="{{$customer->id}}">

                                                <span class="glyphicon glyphicon-edit"></span>
                                            </a>&nbsp
                                            <a id="deleteIcone" href="javascript:;" class="delete-modal" customerId="{{$customer->id}}">
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Modal -->
<div id="editModal" class="modal fade" style="margin-top:3%;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Update Customer</h4>
            </div>
            <div class="modal-body">
                    {!! Form::open(array('url' => '' , 'enctype' => 'multipart/form-data',  'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                <input id="EMcustomerId" type="hidden"  value=""/>

                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('name', 'Name:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'EMname', 'type' => 'text', 'placeholder' => 'Enter name']) !!}
                                    <p id='namee' style="max-height:3px;"></p>
                                </div>
                            </div>
                           <!--  <div class="form-group">
                                {!! Form::label('name', 'Fother Name:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('fothersName', $value = null, ['class' => 'form-control', 'id' => 'fothersName', 'type' => 'text', 'placeholder' => 'Enter fothersName','autocomplite'=>'off']) !!}
                                    <p id='namee' style="max-height:3px;"></p>
                                </div>
                            </div>
 -->
                            <div class="form-group">
                                {!! Form::label('code', 'Code:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('code', $value = null, ['class' => 'form-control', 'id' => 'EMcode', 'type' => 'text', 'placeholder' => 'Enter  code']) !!}
                                    <p id='codee' style="max-height:3px;"></p>
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('email', 'Email:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('email', $value = null, ['class' => 'form-control', 'id' => 'EMemail', 'type' => 'text', 'placeholder' => 'Enter  email']) !!}
                                    <p id='emaile' style="max-height:3px;"></p>
                                </div>
                            </div>
                                <div class="form-group">
                                    {!! Form::label('preAddress', 'Address:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::textarea('preAddress', $value = null, ['class' => 'form-control', 'id' => 'preAddress', 'type' => 'text', 'placeholder' => 'Enter Present Address no','autocomplite'=>'off']) !!}
                                        <!-- <p id='mobilee' style="max-height:3px;"></p> -->
                                    </div>
                                </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('mobile', 'Mobile:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('mobile', $value = null, ['class' => 'form-control', 'id' => 'EMmobile', 'type' => 'text', 'placeholder' => 'Enter  mobile no']) !!}
                                    <p id='mobilee' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                    {!! Form::label('desccription', 'Description:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::textarea('cusDes', $value = null, ['class' => 'form-control', 'id' => 'cusDescription', 'type' => 'text', 'placeholder' => 'Enter Description','autocomplite'=>'off']) !!}
                                        <!-- <p id='mobilee' style="max-height:3px;"></p> -->
                                    </div>
                                </div>

                            <div class="modal-footer">
                                <input id="EMcustomerId" type="hidden" name="customerId" value="">
                                <button type="button" id="updateButton" class="btn btn-success"><span class="glyphicon glyphicon-edit" style="padding-right:4px;"></span>Update</button>
                                <button type="button" class="btn btn-danger " data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="deleteModal" class="modal fade" style="margin-top:3%;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Delete Customer</h4>
            </div>

            <div class="modal-body ">
                <div class="row" style="padding-bottom:20px;"> </div>
                <h2>Are You Confirm to Delete This Record?</h2>

                <div class="modal-footer">
                    <input id="DMCustomerPackageId" type="hidden"  value=""/>
                    <button type="button" class="btn btn-danger"  id="DMCustomer"  data-dismiss="modal">confirm</button>
                    <button type="button" class="btn btn-warning"  data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
                </div>

            </div>
        </div>
    </div>
</div>



@include('pos/customer/customerDetails')

<script>

//$(".select2-selection__rendered").show();
$(document).ready(function(){
    $('#customerPackge').select2();
    $("#customerPackge").on("select2:select", function (evt) {
        var element = evt.params.data.element;
        var $element = $(element);
        $element.detach();
        $(this).append($element);
        $(this).trigger("change");

    });
    $('#customerPackge').next("span").css("width","100%");

    $(document).on('click', '.delete-modal', function(){
        $("#DMCustomerPackageId").val($(this).attr('customerId'));
        $('#deleteModal').modal('show');
    });
    $("#DMCustomer").on('click',  function() {
        var customerId= $("#DMCustomerPackageId").val();
        var csrf = "{{csrf_token()}}";
        $.ajax({
            url: './deleteCustomerItem',
            type: 'POST',
            dataType: 'json',
            data: {id:customerId, _token:csrf},
        })
        .done(function(data) {

            location.reload();
            window.location.href = '{{url('pos/customers/')}}';
        })
        .fail(function(){
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
    });
});
$( document ).ready(function() {

    $(document).on('click', '.edit-modal', function() {
        var customerId = $(this).attr('customerId');
        var csrf = "{{csrf_token()}}";
        $("#EMcustomerId").val(customerId);
       
        $.ajax({
            url: './getCustomerInfo',
            type: 'POST',
            dataType: 'json',
            data: {id:customerId , _token: csrf},
            success: function(data) {
               
                var a = data['customer'].customerImg;
                console.log(data);
                $("#EMname").val(data['customer'].name);
                $("#EMcode").val(data['customer'].code);
               // $("#EMnid").val(data['customer'].nid);
                $("#EMmobile").val(data['customer'].mobile);
                $("#EMemail").val(data['customer'].email);
                //$("#fothersName").val(data['customer'].fothersName);
                $("#preAddress").text(data['customer'].preAddress);
                //$("#parAddress").text(data['customer'].paramaAddress);
                $("#cusDescription").text(data['customer'].cusDes);
                //$("#customerImage").text(data['customer'].cusDes);
                //$('#imgwrap img').attr("src", a).show();
                //$('#customerImage').append('<img src="' +a +'">');
                

                var customerId = data['customer'].id;// Data Array

                $("#editModal").find('.modal-dialog').css('width', '80%');
                $('#editModal').modal('show');

            },
            error: function(argument) {
                //alert('response error');
            }

        });
    });



    $("#updateButton").on('click', function() {
        $("#updateButton").prop("disabled", true);
        var id         = $("#EMcustomerId").val();
        var name               = $("#EMname").val();
        var code               = $("#EMcode").val();
        // var nid                = $("#EMnid").val();
        var email              = $("#EMemail").val();
        var mobile              = $("#EMmobile").val();
        //var fothersName         = $("#fothersName").val();
        var preAddress          = $("#preAddress").val();
        //var paramaAddress       = $("#parAddress").text();
        var cusDescription      =   $("#cusDescription").val();
        //var image               = $('#cusImg')[0].files[0];
        var csrf = "{{csrf_token()}}";
        
        var formData = new FormData();
        formData.append('name',name);
        formData.append('id',id);
        formData.append('code',code);
        //formData.append('nid',nid);
        formData.append('email',email);
        formData.append('mobile',mobile);
       // formData.append('fothersName',fothersName);
        formData.append('preAddress',preAddress);
        //formData.append('paramaAddress',paramaAddress);
        formData.append('cusDescription',cusDescription);
        //formData.append('image',image);


        $.ajax({
            type: 'post',
            url: './editCustomerItem',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
        })
        .done(function(data) {
            //alert(JSON.stringify(data.errors));
            if (data.errors) {
                if (data.errors['name']) {
                    $("#namee").empty();
                    $("#namee").append('<span class="errormsg" style="color:red;">'+data.errors['name']);
                }
                if (data.errors['code']) {
                    $("#codee").empty();
                    $("#codee").append('<span class="errormsg" style="color:red;">'+data.errors['code']);
                }

                if (data.errors['email']) {
                    $("#emaile").empty();
                    $("#emaile").append('<span class="errormsg" style="color:red;">'+data.errors['email']);
                }
                if (data.errors['mobile']) {
                    $("#mobilee").empty();
                    $("#mobilee").append('<span class="errormsg" style="color:red;">'+data.errors['mobile']);
                }
               
            }
            else {
                location.reload();
            }
            console.log("success");
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        })
    });


    $(document).on('click', '.form5', function() {

        $('#imageShow3').empty();
        var id = ($(this).data('id'));
        var crsf = ($(this).data('token'));
        $.ajax({
            type: 'post',
            url: './customerDetail',
            data: {
                '_token': $('input[name=_token]').val(),
                'id': id
            },
            dataType: 'json',
            success: function( data ){
                $.each(data, function( index, value ){
                    console.log(data);
                    $('#customerName').text(data.customerName);
                    $('#mobile').text(data.customerMobile);
                    $('#customerEmail').text(data.customerEmail);
                    $('#nid').text(data.customerNid);
                    $('#customerCode').text(data.customerCode);
                    $('#Id').text(data.customerIdNo);
                    $('#customerFothersName').text(data.customerFothersName);
                    $('#customerDescription').text(data.customerDescription);
                    $('#customerpreAddress').text(data.customerpreAddress);
                    $('#customerparmaAddress').text(data.customerparmaAddress);
                });

                $('.modal-title').text('Customer Details');
                $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
                $('.modal-dialog').css('width','90%');
                $('.form-horizontal').show();
                $('#myModal2').modal('show');
            },
            error: function( data ){
                alert();
            }
        });
    });

    $("input").keyup(function(){
        var name = $("#EMname").val();
        if(name){$('#namee').hide();}else{$('#namee').show();}
        var code = $("#EMcode").val();
        if(code){$('#codee').hide();}else{$('#codee').show();}
        var mobile = $("#EMmobile").val();
        if(mobile){$('#mobilee').hide();}else{$('#mobilee').show();}
        var email = $("#EMemail").val();
        if(email){$('#emaile').hide();}else{$('#emaile').show();}
        var nid = $("#EMnid").val();
        if(nid){$('#nide').hide();}else{$('#nide').show();}

    });


});//ready function end


</script>

@include('dataTableScript')
@endsection
