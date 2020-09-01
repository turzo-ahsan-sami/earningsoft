@extends('layouts/pos_layout')
@section('title', '| Product Assign')
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
                          <a href="{{url('pos/posAddProductAssaign/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Product Assign</a>
                      </div>
                      <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">Product Assign LIST</font></h1>
                    </div>
                    <div class="panel-body panelBodyView"> 
                      <div>
                        <script type="text/javascript">
                        jQuery(document).ready(function($)
                        {
                          $("#ProCategoryView").dataTable().yadcf([
                  
                          ]);
                        });
                        </script>
                    </div>
                    <table class="table table-striped table-bordered" id="ProCategoryView" style="color:black;">
                          <thead>
                            <tr>
                              <th width="30">SL#</th>
                              <th>Company Name</th>
                              <th>Product Name</th>
                              <th>Product Package</th>
                              <th>Action</th>
                            </tr>
                            {{ csrf_field() }}
                          </thead>
                            <tbody>
                                <?php $no=0; ?>
                                @foreach($PosProductAssaigns as $PosProductAssaign)
                                  <tr class="item{{$PosProductAssaign->id}}">
                                    <td class="text-center slNo">{{++$no}}</td>
                                    <td style="padding-left: 25px; text-align: left;">
                                        {{DB::table('pos_client')->where('id',$PosProductAssaign->clientcompanyId)->value('clientCompanyName')}}
                                    </td>
                                    <td style="padding-left: 25px; text-align: left;">
                                        {{DB::table('pos_product')->where('id',$PosProductAssaign->productId)->value('name')}}
                                    </td>
                                    <td>
                                        @php
                                            $productPackageIds =  DB::table('pos_product')->where('id',$PosProductAssaign->productId)->value('productPackge');
                                            if($productPackageIds!=null){
                                            $productStr =  str_replace(array('"', '[', ']'),'', $productPackageIds);
                                            $productArr = array_map('intval', explode(',', $productStr));
                                            $productName='';
                                            
                                            foreach ($productArr as $key => $prodcutId) {
                                                $temp = DB::table('pos_product')->where('id',$prodcutId)->value('name');
                                                if ($key==0) {
                                                    $productName=$temp;
                                                }else{
                                                    $productName=$productName.', '.$temp;
                                                }
                                            }
                                        } else {
                                            $productName='';
                                        } 
                                        @endphp
                                        {{$productName}}
                                    </td>
                                    <td  class="text-center" width="80">
                                      <a id="viewIcone" href="javascript:;" class="view-modal" productAssignId="{{$PosProductAssaign->id}}">
                                        <span class="fa fa-eye"></span>
                                      </a> &nbsp;
                                      <a id="editIcone" href="javascript:;" class="edit-modal" productAssignId="{{$PosProductAssaign->id}}">
                                        <span class="glyphicon glyphicon-edit"></span>
                                      </a> &nbsp;
                                      <a id="deleteIcone" href="javascript:;" class="delete-modal" productAssignId="{{$PosProductAssaign->id}}">
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
<!-- Edit Modal Start -->
<div id="editModal" class="modal fade" style="margin-top:3%;">
  <div class="modal-dialog">
   <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
       {!! Form::open(array('url' => '','id'=>'entryForm', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
        <input id="EMproductAssignId" type="hidden"  value="" />
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                              {!! Form::label('clientCompanyId', 'Company Name:', ['class' => 'col-sm-3 control-label']) !!}
                              <div class="col-sm-9">
                                      <?php 
                                        $posClients = array('' => 'Please Select Client Company') + DB::table('pos_client')->pluck('clientCompanyName','id')->all(); 
                                      ?>      
                                      {!! Form::select('clientCompanyId', ($posClients), null, array('class'=>'form-control', 'id' => 'EMclientCompanyId')) !!}
                                            <p id='EMclientCompanyIde' style="max-height:3px;"></p>
                              </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                           {!! Form::label('productId', 'Product Name:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                <?php 
                                       $posProducts = array('' => 'Please Select Product') + DB::table('pos_product')->pluck('name','id')->all(); 
                                ?>      
                                    {!! Form::select('productId', ($posProducts), null, array('class'=>'form-control', 'id' => 'EMproductId')) !!}
                                    <p id='EMproductIde' style="max-height:3px;"></p>
                                </div>
                        </div>
                    </div>
                    <div class="col-md-12" id="packageDiv">
                        <div class="form-group">
                            {!! Form::label('productPackage', 'Product Packages:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('productPackage', $value = null, ['class' => 'form-control', 'id' => 'EMproductPackage', 'type' => 'text', 'readonly']) !!}
                            </div>
                        </div>
                    </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                              <h5 style="color:black;"><u>Sales Price :</u></h5>
                          </div>
                          <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('salesEmployeeId', 'Sales Person:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    <?php 
                                    $salesEmployeeIds =DB::table('hr_emp_general_info')->orderBy('emp_id')
                                    ->select(DB::raw("CONCAT(emp_id ,' - ', emp_name_english ) AS name"),'id')->get(); 
                                    ?>  
                                    <select id="salesEmployeeId" name="salesPerson[]" class="form-control" multiple="multiple">    
                                        @foreach ($salesEmployeeIds as $salesEmployeeId)
                                           <option  value="{{$salesEmployeeId->id}}" >{{$salesEmployeeId->name}}</option>
                                       @endforeach
                                   </select>
                                </div>
                            </div>
                        </div>
                         <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('salesPriceHo', 'Head Office:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('salesPriceHo', $value = null, ['class' => 'form-control', 'id' =>'EMsalesPriceHo', 'type' => 'text', 'placeholder' => 'Enter Head Office']) !!}
                                    <p id='EMsalesPriceHoe' style="max-height:3px;"></p>
                                </div>
                            </div>
                         </div>
                         <div class="col-md-12">
                              <div class="form-group">
                                {!! Form::label('salesPriceBo', 'Branch:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('salesPriceBo', $value = null, ['class' => 'form-control', 'id' =>'EMsalesPriceBo', 'type' => 'text', 'placeholder' => 'Enter sales Price']) !!}
                                    <p id='EMsalesPriceBoe' style="max-height:3px;"></p>
                                </div>
                              </div>
                          </div>
                        </div>
                       
                        <div class="row">
                          <div class="col-md-12">
                              <h5 style="color:black;"><u>Service Charge :</u></h5>
                          </div>
                          <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('serviceEmployeeId', 'Service Person:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    <?php 
                                    $serviceEmployeeIds =DB::table('hr_emp_general_info')->orderBy('emp_id')
                                    ->select(DB::raw("CONCAT(emp_id ,' - ', emp_name_english ) AS name"),'id')->get(); 
                                    ?>  
                                    <select id="serviceEmployeeId" name="servicePerson[]" class="form-control" multiple="multiple">    
                                        @foreach ($serviceEmployeeIds as $serviceEmployeeId)
                                           <option  value="{{$serviceEmployeeId->id}}" >{{$serviceEmployeeId->name}}</option>
                                       @endforeach
                                   </select>
                                </div>
                            </div>
                         </div>
                         <div class="col-md-12">
                             <div class="form-group">
                                {!! Form::label('serviceChargeHo', 'Head Office:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('serviceChargeHo', $value = null, ['class' => 'form-control', 'id' =>'EMserviceChargeHo', 'type' => 'text', 'placeholder' => 'Enter service Charge']) !!}
                                    <p id='EMserviceChargeHoe' style="max-height:3px;"></p>
                                </div>
                            </div>
                        </div>
                     <div class="col-md-12">
                             <div class="form-group">
                                {!! Form::label('serviceCharge', 'Branch:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('serviceCharge', $value = null, ['class' => 'form-control', 'id' =>'EMserviceCharge', 'type' => 'text', 'placeholder' => 'Enter service Charge']) !!}
                                    <p id='EMserviceChargee' style="max-height:3px;"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input id="EMproductAssignId" type="hidden" name="productAssignId" value="">
                        <button type="button" id="updateButton" class="btn btn-success"><span class="glyphicon glyphicon-edit" style="padding-right:4px;"></span>Update</button>
                        <button type="button" class="btn btn-danger " data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
                    </div>
               </div>
            </div>
        </div>
 </div>
 <!-- Edit Modal End -->
 <!-- Delete Modal Start -->
   <div id="deleteModal" class="modal fade" style="margin-top:3%;">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"></h4>
          </div>

         <div class="modal-body ">
           <div class="row" style="padding-bottom:20px;"> </div>
            <h2>Are You Confirm to Delete This Record?</h2>

          <div class="modal-footer">
             <input id="DMproductAssignId" type="hidden"  value=""/>
             <button type="button" class="btn btn-danger"  id="DMproductAssign"  data-dismiss="modal">confirm</button>
             <button type="button" class="btn btn-warning"  data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
          </div>

         </div>
        </div>
      </div>
   </div>
   <!-- Edit Modal End -->
@include('pos.productAssaign.productAssignDetails')  

<script>
 
  /*Delete Ajax Start*/
 $(document).ready(function(){ 
 $('#salesEmployeeId,#serviceEmployeeId').select2(); 
$("#salesEmployeeId,#serviceEmployeeId").on("select2:select", function (evt) {
    var element = evt.params.data.element;
    var $element = $(element);
    $element.detach();
    $(this).append($element);
    $(this).trigger("change");
});
$('#salesEmployeeId,#serviceEmployeeId').next("span").css("width","100%");


  $(document).on('click', '.delete-modal', function(){
        $("#DMproductAssignId").val($(this).attr('productAssignId'));
        $('.modal-title').text('Delete Product Assign Info');
        $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"}); 
        $('#deleteModal').modal('show');
      });
        $("#DMproductAssign").on('click',  function() {
            var productAssignId= $("#DMproductAssignId").val();
            var csrf = "{{csrf_token()}}";
              $.ajax({
                   url: './posDeleteProductAssign',
                   type: 'POST',
                   dataType: 'json',
                   data: {id:productAssignId, _token:csrf},
              })
              .done(function(data) {
                   location.reload();
                   window.location.href = '{{url('pos/posViewProductAssaing/')}}';
               })
              .fail(function(){
                  console.log("error");
              })
              .always(function() {
                  console.log("complete");
              });
        });     
      /*Delete Ajax End*/
   /*Number Validation Start*/   

  $('#EMphone').on('input', function(event) {
       this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1'); 
  });
 $('#EMmobile').on('input', function(event) {
       this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1'); 
 });
 $('#EMnationalId').on('input', function(event) {
       this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1'); 
 });
 /*Number Validation End*/

          /*Edit Modal Ajax Start*/
          $(document).on('click', '.edit-modal', function() {
               var productAssignId = $(this).attr('productAssignId');
               var csrf = "{{csrf_token()}}";
               $("#EMproductAssignId").val(productAssignId);
                  $.ajax({
                     url: './posGetProductAssignInfo',
                     type: 'POST',
                     dataType: 'json',
                     data: {id:productAssignId , _token: csrf},
                     success: function(data) {
                        $("#EMclientCompanyId").val(data['PosProductAssaign'].clientcompanyId); 
                        $("#EMproductId").val(data['PosProductAssaign'].productId); 
                        $("#EMproductPackage").val(data['productName']); 
                        $("#EMsalesPriceHo").val(data['PosProductAssaign'].salesPriceHo);
                        $("#EMsalesPriceBo").val(data['PosProductAssaign'].salesPriceBo);
                        $("#EMserviceChargeHo").val(data['PosProductAssaign'].serviceChargeHo);
                        $("#EMserviceCharge").val(data['PosProductAssaign'].serviceChargeHo);
                         $('#EMproductPackage').val(data.productName);
                         if(data.productName!=''){
                             $("#packageDiv").show();
                          } else {
                             $("#packageDiv").hide();
                          }

                          var salesPersonId = data['salesPersonArr'];// Data Array
                          var servicePersonId = data['servicePersonArr'];// Data Array
                          //alert(JSON.stringify(data['servicePersonArr']));
                        $('#salesEmployeeId').val(salesPersonId);

                         // Create a DOM Option and pre-select by default
                        var newOption = new Option(salesPersonId.text, salesPersonId.id, true, true);
                            // Append it to the select
                            $('#salesEmployeeId').append(newOption).trigger('change');
                            $("#salesEmployeeId option[selected]").remove();

                        $('#serviceEmployeeId').val(servicePersonId); 
                         // Create a DOM Option and pre-select by default
                        var newOption = new Option(servicePersonId.text, servicePersonId.id, true, true);
                            // Append it to the select
                            $('#serviceEmployeeId').append(newOption).trigger('change');
                            $("#serviceEmployeeId option[selected]").remove();

                         $('.modal-title').text('Update Product Assign Info');
                         $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});   
                         $("#editModal").find('.modal-dialog').css('width', '55%');
                         $('#editModal').modal('show');

                          },
                               error: function(argument) {
                                alert('response error');
                    }
                });
          });

      /*Edit Modal Ajax End*/
      /*Update Start*/
    $("#updateButton").on('click', function() {
         $("#updateButton").prop("disabled", true);
           var productAssign           = $("#EMproductAssignId").val(); 
           var clientCompanyId         = $("#EMclientCompanyId").val();
           var productId               = $("#EMproductId").val();
           var salesPriceHo            = $("#EMsalesPriceHo").val();
           var salesPriceBo            = $("#EMsalesPriceBo").val();
           var serviceChargeHo         = $("#EMserviceChargeHo").val();
           var serviceCharge           = $("#EMserviceCharge").val();
           var selesPerson             = $("#salesEmployeeId").val();
           var servicePerson           = $("#serviceEmployeeId").val();
           var csrf = "{{csrf_token()}}";

           $.ajax({
               url: './posEditProductAssignItem',
               type: 'POST',
               dataType: 'json',
               data: {id:productAssign,clientCompanyId:clientCompanyId,productId:productId,salesPriceHo:salesPriceHo,salesPriceBo:salesPriceBo,serviceChargeHo:serviceChargeHo,serviceCharge:serviceCharge,selesPerson:selesPerson,servicePerson:servicePerson,_token: csrf},
          })
           .done(function(data) {
              if (data.errors) {
                if (data.errors['clientCompanyId']) {
                    $("#EMclientCompanyIde").empty();
                    $("#EMclientCompanyIde").append('<span class="errormsg" style="color:red;">'+data.errors['clientCompanyId']);
                }
                if (data.errors['productId']) {
                    $("#EMproductIde").empty();
                    $("#EMproductIde").append('<span class="errormsg" style="color:red;"> '+data.errors['productId']);
                }
                if (data.errors['salesPriceHo']) {
                    $("#EMsalesPriceHoe").empty();
                    $("#EMsalesPriceHoe").append('<span class="errormsg" style="color:red;"> '+data.errors['salesPriceHo']);
                }
                if (data.errors['salesPriceBo']) {
                    $("#EMsalesPriceBoe").empty();
                    $("#EMsalesPriceBoe").append('<span class="errormsg" style="color:red;">'+data.errors['salesPriceBo']);
                }
                if (data.errors['serviceChargeHo']) {
                    $("#EMserviceChargeHoe").empty();
                    $("#EMserviceChargeHoe").append('<span class="errormsg" style="color:red;">'+data.errors['serviceChargeHo']);
                }
                if (data.errors['serviceCharge']) {
                    $("#EMserviceChargee").empty();
                    $("#EMserviceChargee").append('<span class="errormsg" style="color:red;">'+data.errors['serviceCharge']);
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
    /*Update End*/
    /*Error Remove Ajax*/
     $("input").keyup(function(){
          var salesPriceHo = $("#EMsalesPriceHo").val();
          if(salesPriceHo){$('#EMsalesPriceHoe').hide();}else{$('#EMsalesPriceHoe').show();}
          var salesPriceBo = $("#EMsalesPriceBo").val();
          if(salesPriceBo){$('#EMsalesPriceBoe').hide();}else{$('#EMsalesPriceBoe').show();}
          var serviceChargeHo = $("#EMserviceChargeHo").val();
          if(serviceChargeHo){$('#EMserviceChargeHoe').hide();}else{$('#EMserviceChargeHoe').show();}
          var serviceChargee = $("#EMserviceCharge").val();
          if(serviceChargee){$('#EMserviceChargee').hide();}else{$('#EMserviceChargee').show();}
     });

     $('select').on('change', function (e) {
          var clientCompanyId = $("#EMclientCompanyId").val();
          if(clientCompanyId){$('#EMclientCompanyIde').hide();}else{$('#EMclientCompanyIde').show();}
          var productId = $("#EMproductId").val();
          if(productId){$('#EMproductIde').hide();}else{$('#EMproductIde').show();}
     });

     /*Product Change Start*/
     
      $("#EMproductId").change(function(){
            var productId = $(this).val();
            if(productId==''){
                $('#EMproductPackage').val('');
                return false;
            }
            var csrf = "<?php echo csrf_token(); ?>";
            $.ajax({
                type: 'post',
                url: './posAssainngOnChangeProductId',
                data: {productId:productId,_token: csrf},
                async: false,
                dataType: 'json',
                success: function(data){
                    $('#EMproductPackage').val(data.productName);
                    if(data.productName!=''){
                        $("#packageDiv").show();
                    } else {
                        $("#packageDiv").hide();
                    }
                },
                error: function(_response){
                    alert("error");
                }

        });/*End Ajax*/
    });/*End Change Product*/
});/*End Ready Function */
</script>
<!-- Start View Modal -->
<script type="text/javascript">
  $( document ).ready(function() {
      $(document).on('click', '.view-modal', function() {
          var id = $(this).attr('productAssignId');
          var csrf = "{{csrf_token()}}";
          $.ajax({
             type: 'post',
             url: './posProductAssignDetail',
             data:{id:id,_token: csrf},
             dataType: 'json',
             success: function(data) {
                $.each(data, function( index, value ) {
                  $('#VMsalesPriceHo').text(data.salesPriceHo);
                  $('#VMsalesPriceBo').text(data.salesPriceBo);
                  $('#VMserviceChargeHo').text(data.serviceChargeHo);
                  $('#VMserviceChargeBo').text(data.serviceChargeBo);
                  $('#VMcompanyName').text(data['companyName'].clientCompanyName);
                  $('#VMproductName').text(data['productNamee'].name);
                  $('#VMsalesPerson').text(data.salesPerson);
                  $('#VMservicePerson').text(data.servicePerson);
                  if(data.productName==''){
                    $("#VMproductPackaged").hide();
                  } else {
                     $("#VMproductPackaged").show();
                     $('#VMproductPakage').text(data.productName);
                  }
                 
                });       
                  $('.modal-title').text('Product Assign Details');
                  $('.modal-header').css({"background-color":"black","color":"white", "padding":"10px"});
                  $("#myModal2").find('.modal-dialog').css('width', '55%');
                  $('#myModal2').modal('show'); 

             },
                error: function(argument) {
                  alert('response error');
                }
           });
        });

});
</script>
<!-- End View Modal -->
@include('dataTableScript')
@endsection