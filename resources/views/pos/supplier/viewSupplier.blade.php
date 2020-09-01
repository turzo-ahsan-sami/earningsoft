@extends('layouts/pos_layout')
@section('title', '| Supplier')
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
                            <a href="{{url('pos/addSupplier/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Supplier</a>
                        </div>


                        <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">SUPPLIER LIST</font></h1>
                    </div>
                    <div class="panel-body panelBodyView">
                        <div>
                            <div>

                                <script type="text/javascript">
                                jQuery(document).ready(function($)  {
                                    $("#supplierView").dataTable({
                                        "oLanguage": {
                                            "sEmptyTable": "No Records Available",
                                            "sLengthMenu": "Show _MENU_ "
                                        }
                                    });
                                });

                                </script>

                            </div>
                        </div>
                        <table class="table table-striped table-bordered" id="supplierView" style="color:black;">
                            <thead>
                                <tr>
                                    <th width="80">SL#</th>
                                    <th>Supplier Name</th>
                                    <th>Code</th>
                                    <th>Mobile no</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                                {{ csrf_field() }}
                            </thead>
                            <tbody>
                                <?php $no=0; ?>
                                @foreach($suppliers as $supplier)
                                    <tr class="item{{$supplier->id}}">
                                        <td class="text-center">{{++$no}}</td>
                                        <td style="text-align: left; padding-left: 5px;">{{$supplier->name}}</td>
                                        <td style="text-align: center;">{{$supplier->code}}</td>
                                        <td style="text-align: center;">{{$supplier->mobile}}</td>
                                        <td style="text-align: left; padding-left: 5px;">{{$supplier->email}}</td>
                                        <td class="text-center" width="100">

                                            <a href="javascript:;" class="form5" data-token="" data-id="{{$supplier->id}}">
                                                <span class="fa fa-eye"></span>
                                            </a>

                                            &nbsp
                                            <a href="javascript:;" class="edit-modal" SupplierId="{{$supplier->id}}">

                                                <span class="glyphicon glyphicon-edit"></span>
                                            </a>&nbsp
                                            <a id="deleteIcone" href="javascript:;" class="delete-modal" SupplierId="{{$supplier->id}}">
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
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Update Supplier</h4>
            </div>
            <div class="modal-body">
                {!! Form::open(array('url' => '','id'=>'entryForm', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                <input id="EMsupplierId" type="hidden"  value=""/>

                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('name', 'Name:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'EMname', 'type' => 'text', 'placeholder' => 'Enter Supplier name']) !!}
                                    <p id='namee' style="max-height:3px;"></p>
                                </div>
                            </div>
                             <div class="form-group">
                                    {!! Form::label('supComName', 'Company Name:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('supComName', $value = null, ['class' => 'form-control', 'id' => 'supComName', 'type' => 'text', 'placeholder' => 'Enter company Name','autocomplite'=>'off']) !!}
                                        <p id='namee' style="max-height:3px;"></p>
                                    </div>
                                </div>
                            <div class="form-group">
                                {!! Form::label('code', 'Code:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('code', $value = null, ['class' => 'form-control', 'id' => 'EMcode', 'type' => 'text', 'placeholder' => 'Enter Supplier code']) !!}
                                    <p id='codee' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('mobile', 'Mobile:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('mobile', $value = null, ['class' => 'form-control', 'id' => 'EMmobile', 'type' => 'text', 'placeholder' => 'Enter Supplier mobile']) !!}
                                    <p id='mobilee' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('email', 'Email:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('email', $value = null, ['class' => 'form-control', 'id' => 'EMemail', 'type' => 'text', 'placeholder' => 'Enter Supplier email']) !!}
                                    <p id='emaile' style="max-height:3px;"></p>
                                </div>
                            </div>
                             <div class="form-group">
                                    {!! Form::label('refNo', 'Reference No:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('refNo', $value = null, ['class' => 'form-control', 'id' => 'refNo', 'type' => 'text', 'placeholder' => 'Enter Reference No']) !!}
                                        <p id='nide' style="max-height:3px;"></p>
                                    </div>
                                </div>
                            
                        </div>

                        <div class="col-md-6">
                             <div class="form-group">
                                    {!! Form::label('address', 'Address:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                       <textarea class="form-control" id="address" name="address"></textarea>
                                        <p id='nide' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                 <div class="form-group">
                                    {!! Form::label('website', 'Website:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('website', $value = null, ['class' => 'form-control', 'id' => 'website', 'type' => 'text', 'placeholder' => 'Enter Website']) !!}
                                        <p id='nide' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                 <div class="form-group">
                                    {!! Form::label('description', 'Description:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                       <textarea class="form-control" id="description" name="description"></textarea>
                                        <p id='nide' style="max-height:3px;"></p>
                                    </div>
                                </div>


                                
                          

                            

                            <div class="modal-footer">
                                <input id="EMsupplierId" type="hidden" name="supplierId" value="">
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
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Delete Supplier</h4>
            </div>

            <div class="modal-body ">
                <div class="row" style="padding-bottom:20px;"> </div>
                <h2>Are You Confirm to Delete This Record?</h2>

                <div class="modal-footer">
                    <input id="DMSupplierPackageId" type="hidden"  value=""/>
                    <button type="button" class="btn btn-danger"  id="DMSupplier"  data-dismiss="modal">confirm</button>
                    <button type="button" class="btn btn-warning"  data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
                </div>

            </div>
        </div>
    </div>
</div>



@include('pos/supplier/supplierDetails')

<script>

//$(".select2-selection__rendered").show();
$(document).ready(function(){
    $('#supplierPackge').select2();
    $("#supplierPackge").on("select2:select", function (evt) {
        var element = evt.params.data.element;
        var $element = $(element);
        $element.detach();
        $(this).append($element);
        $(this).trigger("change");

    });
    $('#supplierPackge').next("span").css("width","100%");

    $(document).on('click', '.delete-modal', function(){
        $("#DMSupplierPackageId").val($(this).attr('supplierId'));
        $('#deleteModal').modal('show');
    });
    $("#DMSupplier").on('click',  function() {
        var supplierId= $("#DMSupplierPackageId").val();
        var csrf = "{{csrf_token()}}";
        $.ajax({
            url: './deleteSupplierItem',
            type: 'POST',
            dataType: 'json',
            data: {id:supplierId, _token:csrf},
        })
        .done(function(data) {

            location.reload();
            window.location.href = '{{url('pos/suppliers/')}}';
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
        var supplierId = $(this).attr('supplierId');
        var csrf = "{{csrf_token()}}";
        $("#EMsupplierId").val(supplierId);

        $.ajax({
            url: './getSupplierInfo',
            type: 'POST',
            dataType: 'json',
            data: {id:supplierId , _token: csrf},
            success: function(data) {
                console.log(data);
                $("#EMname").val(data['supplier'].name);
                $("#EMcode").val(data['supplier'].code);
                $("#EMnid").val(data['supplier'].nid);
                $("#EMmobile").val(data['supplier'].mobile);
                $("#EMemail").val(data['supplier'].email);
                $("#supComName").val(data['supplier'].supComName);
                $("#website").val(data['supplier'].website);
                $("#description").val(data['supplier'].description);
                $("#address").val(data['supplier'].address);
                $("#refNo").val(data['supplier'].refNo);

                var supplierId = data['supplier'].id;// Data Array

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
        var supplierId         = $("#EMsupplierId").val();
        var name              = $("#EMname").val();
        var code              = $("#EMcode").val();
        var email              = $("#EMemail").val();
        var mobile              = $("#EMmobile").val();
        var supComName              = $("#supComName").val();
        var website              = $("#website").val();
        var description              = $("#description").val();
        var address              = $("#address").val();
        var refNo              = $("#refNo").val();
        var csrf = "{{csrf_token()}}";

        $.ajax({
            url: './editSupplierItem',
            type: 'POST',
            dataType: 'json',
            data: {id:supplierId,name:name,code:code,email:email,mobile:mobile,supComName:supComName,website:website,description:description,address:address,refNo:refNo,_token: csrf},
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
                if (data.errors['nid']) {
                    $("#nide").empty();
                    $("#nide").append('<span class="errormsg" style="color:red;">'+data.errors['nid']);
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
            url: './supplierDetail',
            data: {
                '_token': $('input[name=_token]').val(),
                'id': id
            },
            dataType: 'json',
            success: function( data ){
                $.each(data, function( index, value ){
                   // console.log(data);
                    $('#supplierName').text(data.supplierName);
                    $('#mobile').text(data.supplierMobile);
                    $('#email').text(data.supplierEmail);
                    $('#supplierCode').text(data.supplierCode);
                    $('#Id').text(data.supplierIdNo);
                    $('#supplierCompany').text(data.supplierComName);
                    $('#supplierAddress').text(data.supplierAddress);
                    $('#supplierDescription').text(data.supplierDescription);
                    $('#supplierRefNo').text(data.supplierRefNo);
                    $('#supplierWebsite').text(data.supplierWebsite);
                });

                $('.modal-title').text('Supplier Details');
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
