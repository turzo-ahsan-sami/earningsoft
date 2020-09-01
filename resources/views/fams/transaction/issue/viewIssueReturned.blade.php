@extends('layouts/fams_layout')
@section('title', '| Issue Return')
@section('content')
@include('successMsg')
<div class="row">
  <div class="col-md-12">
    <div class="" style="">
      <div class="">
        <div class="panel panel-default" style="background-color:#708090;">
          <div class="panel-heading" style="padding-bottom:0px">
            <div class="panel-options">
              <a href="{{url('famsReturnIssue/')}}" class="btn btn-info pull-right addViewBtn" style=""><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Issue Return</a>
            </div>

            <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">ISSUE RETURN</h1>
            </div>
            <div class="panel-body panelBodyView"> 
              <div>
                <script type="text/javascript">
                  jQuery(document).ready(function($)
                  {
            /*$("#gnrGrounView").dataTable().yadcf([
    
            ]);*/
            $("#gnrGrounView").dataTable({
             "oLanguage": {
              "sEmptyTable": "No Records Available",
              "sLengthMenu": "Show _MENU_ "
            }
          });
          });
        </script>
      </div>
      <table class="table table-striped table-bordered" id="gnrGrounView">
        <thead>
          <tr>
            <th width="32">SL#</th>
            <th>Date</th>
            <th>Bill No</th>
            <th>Branch Name</th>
            <th>Quantity</th>
            <th style="display:none;">Amount</th>

            <th id="details" style="pointer-events:none;">Action</th>


          </tr>
          {{ csrf_field() }} 
        </thead>
        <tbody>            
          <?php $no=0; ?>
          @foreach($returnedIssues as $returnedIssue) 
          <tr class="item{{$returnedIssue->id}}">
            <td class="text-center slNo">{{++$no}}</td>
            <td>{{date('d/m/Y', strtotime($returnedIssue->issueReturnDate))}}</td>
            <td>{{$returnedIssue->issueReturnBillNo}}</td>
            <td>{{$returnedIssue->branchName}}</td>
            <td>{{$returnedIssue->totalIssueReturnQuantity}}</td>
            <td style="display:none;">{{$returnedIssue->totalIssueReturnAmount}}</td>

            <td class="text-center" width="80">

              <a href=""   data-toggle="modal" data-target="#view-modal-{{$returnedIssue->id}}" >
                <i class="fa fa-eye" aria-hidden="true"></i>
              </a>&nbsp

              <a href=""  class="editModalLink" data-toggle="modal" data-target="#edit-modal-{{$returnedIssue->id}}" >
                <span class="glyphicon glyphicon-edit"></span>
              </a>&nbsp

              <a href=""  data-toggle="modal" data-target="#delete-modal-{{$returnedIssue->id}}" >
                <span class="glyphicon glyphicon-trash"></span>
              </a>

              {{-- View Modal --}}
              <div id="view-modal-{{$returnedIssue->id}}" class="modal fade" style="margin-top:3%">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Issue Return Details</h4>
                    </div>
                    <div class="modal-body">
                      <div>
                        <h4 align="left">Issue Return Bill No: {{$returnedIssue->issueReturnBillNo}}<br>Issue Return Date: {{date('d/m/Y', strtotime($returnedIssue->issueReturnDate))}} <br> Branh: {{$returnedIssue->branchName}}</h4>
                      </div>
                      <table class="table table-striped table-bordered" id="gnrGrounView">
                        <thead>

                          <tr>                

                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th style="display:none;">Per piece Cost Price</th>
                            <th style="display:none;">Total Cost Price</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($issueReturnDetails as $issueReturnDetail)
                          @if($returnedIssue->issueReturnBillNo==$issueReturnDetail->issueReturnBillNoId)
                          <tr>

                            <td>{{$issueReturnDetail->issueReturnProductName}}</td>
                            <td>{{$issueReturnDetail->issueReturnQuantity}}</td>
                            <td style="display:none;">{{$issueReturnDetail->issueReturnProductCostPrice}}</td>
                            <td style="display:none;">{{$issueReturnDetail->issueReturnQuantity*$issueReturnDetail->issueReturnProductCostPrice}}</td>
                          </tr>
                          @endif
                          @endforeach

                        </tbody>
                      </table>

                      <div class="modal-footer">                   
                        <button class="btn btn-warning" data-dismiss="modal" id="footer_action_button_dismis" type="button"><span id=""> Close</span></button>
                      </div>


                    </div>
                  </div>
                </div>
              </div>
              {{-- End View Modal --}}



              {{-- Edit Modal --}}

              <div id="edit-modal-{{$returnedIssue->id}}" class="modal fade edit-modal" style="margin-top:3%;">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Issue Return</h4>
                    </div>


                    <div class="modal-body" >

                      {!! Form::open(['url' => 'famsEditReturnIssue/']) !!}

                      <div class="row">
                        <div class="col-md-12">
                          <div class="col-md-6">
                            <div class="form-group">
                              {!! Form::label('returnIssueBillNo', 'Issue Bill No:', ['class' => 'col-sm-6 control-label']) !!}
                              <div class="col-sm-6">
                                {!! Form::text('editModalreturnIssueBillNo', $value = $returnedIssue->issueReturnBillNo, ['class' => 'form-control', 'id' => 'issueOrderNo', 'type' => 'text','autocomplete'=>'off','readonly']) !!}
                              </div>
                            </div>
                            <input type="hidden" value="{{$returnedIssue->id}}" name="id">
                          </div>

                          <div class="col-md-6">
                            <div class="form-group">
                              {!! Form::label('issueDate', 'Issue Date:', ['class' => 'col-sm-6 control-label']) !!}
                              <div class="col-sm-6">
                                {!! Form::text('issueDate', $value = date('d/m/Y', strtotime($returnedIssue->issueReturnDate)), ['class' => 'form-control', 'id' => 'issueOrderNo', 'type' => 'text','autocomplete'=>'off','readonly']) !!}
                              </div>
                            </div>

                          </div>


                        </div>
                      </div>

                      <div> <br><br></div>

                      <div>


                        {{-- Filtering Inputs --}}
                        <div class="col-md-12" style="padding-right: 0px;">
                          <div class="form-group col-sm-3" style="padding-right: 2%;">
                            {!! Form::label('productGroupId', 'Group:', ['class' => 'control-label',]) !!}
                            
                            <select  id="productGroupId" class="form-control input-sm" style="padding-right: 0px;">
                             <option value="" selected="selected">Please Select</option>
                             @foreach($productGroups as $productGroup)
                             <option value={{$productGroup->id}}>{{$productGroup->name}}</option>
                             @endforeach
                           </select>

                         </div>

                         <div class="form-group col-sm-3" style="padding-right: 2%;">
                          {!! Form::label('productCategoryId', 'Category:', ['class' => 'control-label']) !!}

                          <select  id="productCategoryId" class="form-control input-sm">
                           <option value="" selected="selected">Please Select</option>
                           @foreach($productCategories as $productCategory)
                           <option value={{$productCategory->id}}>{{$productCategory->name}}</option>
                           @endforeach

                         </select>                                
                       </div>

                       <div class="form-group col-sm-3" style="padding-right: 2%;">
                        {!! Form::label('productSubCategoryId', 'Sub Category:', ['class' => 'control-label']) !!}

                        <select  id="productSubCategoryId" class="form-control input-sm">
                         <option value=""  selected="selected">Please Select</option>
                         @foreach($productSubCategories as $productSubCategory)
                         <option value={{$productSubCategory->id}}>{{$productSubCategory->name}}</option>
                         @endforeach

                       </select>                                
                     </div>

                     <div class="form-group col-sm-3" style="padding-right: 2%;">
                      {!! Form::label('productBrandId', 'Brand:', ['class' => 'control-label']) !!}

                      <select  id="productBrandId" class="form-control input-sm">
                       <option value="" selected="selected">Please Select</option>
                       @foreach($productBrands as $productBrand)
                       <option value={{$productBrand->id}}>{{$productBrand->name}}</option>
                       @endforeach

                     </select>                                
                   </div>                  


                 </div>
                 {{-- End Filtering Input --}}


                 <div> <br><br></div>

                 <table id="editModalTable-{{$returnedIssue->id}}" class="table table-bordered responsive">

                  <tr>

                    <th style="text-align:center;">
                      <select class="form-control selectProduct" id="product">
                        <option value="">Select Product</option>
                        @foreach($products as $prod2)
                        <option value="{{$prod2->id}}" >{{$prod2->name}}</option>
                        @endforeach
                      </select></th>
                      <th>
                        <input type="text" class="quantity" id="quantity" placeholder="Product Quantiy" style="text-align:center;">
                      </th>



                      <th><button type="button" class='btn btn-primary btn-xs addToCart' id="{{$returnedIssue->id}}" style='text-align:center;border-radius:0;width:80px;'>Add to Cart</button></th>


                    </tr>
                    <tbody>
                      <tr class="editModalTableHeadRow">
                        <td>Product Name</td>
                        <td>Quantity</td>
                        <td>Action</td>
                      </tr>

                      @foreach($issueReturnDetails as $issueReturnDetail)
                      @if($returnedIssue->issueReturnBillNo==$issueReturnDetail->issueReturnBillNoId)
                      <tr class="valueRow">

                        <td>{{$issueReturnDetail->issueReturnProductName}}</td>
                        <td><input type="hidden" name="editModalProductId[]" class="editModalProductId" value={{$issueReturnDetail->productId}}> <input type="text" name="editModalQuantity[]" class="editModalQuantity" value={{$issueReturnDetail->issueReturnQuantity}} style="text-align:center;"> </td>
                        <td><button type="button" id="{{$returnedIssue->id}}" class="btn btn-danger btn-x removeButton" style='text-align:center;border-radius:0;width:80px; padding: 2px 10px;'>Remove</button></td>
                      </tr>





                      @endif
                      @endforeach                               


                    </tbody>
                  </table>




                  <div class="modal-footer">
                    <button class="btn actionBtn glyphicon glyphicon-check btn-success edit" type="submit"> Update</button>
                    <button class="btn btn-warning glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>
                  </div>

                  {!! Form::close() !!}

                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- End Edit Modal --}}



        {{-- Delete Modal --}}
        <div id="delete-modal-{{$returnedIssue->id}}" class="modal fade" style="margin-top:3%">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Delete!</h4>
              </div>
              <div class="modal-body">
                <h2>Are You Confirm to Delete This Issue Return?</h2>

                <div class="modal-footer"> 
                  {!! Form::open(['url' => 'deleteReturnIssue/']) !!}
                  <input type="hidden" name="issueReturnBillNo" value={{$returnedIssue->issueReturnBillNo}}>
                  <input type="hidden" name="id" value={{$returnedIssue->id}}>
                  <button  type="submit" class="btn btn-danger"> Confirm</button>
                  <button class="btn btn-warning glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>
                  {!! Form::close() !!}

                </div>

              </div>
            </div>
          </div>
        </div>
        {{-- End Delete Modal --}}





      </div>

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


@include('dataTableScript');
<script type="text/javascript">
  $(document).ready(function(){       
    $("#details").attr("class", "");

  });
</script>

<script type="text/javascript">

  $(document).ready(function(){
    $(".edit-modal").find(".modal-dialog").css("width","80%");


    /*Add to Cart*/
    $(document).on('click','button.addToCart',function(){

      var modalId = this.id;
      var productId = $("#edit-modal-"+modalId+" .selectProduct option:selected").val();
      
      var productName = $("#edit-modal-"+modalId+" .selectProduct option:selected").html();
      var quantity = parseInt($("#edit-modal-"+modalId+" .quantity").val());
      
      

     /* alert(productId);
      alert(productName);
      alert(quantity);*/

      if (productId !="" && quantity>0) {

        var flag = true;

        if ($("#editModalTable-"+modalId+" tr.valueRow").length > 0) {                  

          $("#editModalTable-"+modalId+" tr.valueRow").each(function(){
            if(parseInt($(this).find('.editModalProductId').val()) == productId){                  

              var previousQuantity =  parseInt($(this).find('.editModalQuantity').val());
              var previousTotal =  parseInt($(this).find('.totalPriceColumn').html());

              $(this).find('.editModalQuantity').val(previousQuantity+quantity)



              flag = false;

            }

          });   

        }

        if(flag==true){

          var markup = "<tr class='valueRow'><td>"+productName+"</td><td><input type='hidden' name='editModalProductId[]' class='editModalProductId' value="+productId+"><input type='text'  name='editModalQuantity[]' style='text-align:center;' class='editModalQuantity' value='"+quantity+"'></td><td><button id='"+modalId+"' class='btn btn-danger btn-x removeButton' style='text-align:center;border-radius:0;width:80px; padding: 2px 10px;'>Remove</button></td> </tr>";

          $("#editModalTable-"+modalId+" tr.editModalTableHeadRow").after(markup);

        }



        $("#edit-modal-"+modalId+" .selectProduct").val("");
        $("#edit-modal-"+modalId+" .selectProduct option[value="+productId+"]").hide();
        $("#edit-modal-"+modalId+" .quantity").val("");
        $("#edit-modal-"+modalId+" .price").html("");
        $("#edit-modal-"+modalId+" .totalPrice").html("");
      }

      


    });


    /*Remove Button*/
    $(document).on('click', 'button.removeButton', function () {
      var modalId = this.id;

      var productId = $(this).closest('tr').find(".editModalProductId").val();

      $("#edit-modal-"+modalId+" .selectProduct option[value='"+productId+"']").show();
      $(this).closest('tr').remove();
      return false;
    });


  });
</script>

{{-- Filtering --}}
<script type="text/javascript">

  $("#productGroupId").change(function(){ 

   var productGroupId = $('#productGroupId').val();


   var csrf = "<?php echo csrf_token(); ?>";

   $.ajax({
    type: 'post',
    url: './famsOnChangeGroup',
    data: {productGroupId:productGroupId,_token: csrf},
    dataType: 'json',   
    success: function( _response ){

      $("#productCategoryId").empty();
      $("#productCategoryId").prepend('<option selected="selected" value="">Please Select</option>');

      $("#productSubCategoryId").empty();
      $("#productSubCategoryId").prepend('<option selected="selected" value="">Please Select</option>');

      $("#productBrandId").empty();
      $("#productBrandId").prepend('<option selected="selected" value="">Please Select</option>');

      $("#product").empty();
      $("#product").prepend('<option selected="selected" value="">Please Select</option>');

      $.each(_response, function (key, value) {
        {
          if (key == "productCategoryList") {
            $.each(value, function (key1,value1) {

              $('#productCategoryId').append("<option value='"+ value1+"'>"+key1+"</option>");
            });
          }

          if (key == "productSubCategoryList") {
            $.each(value, function (key1,value1) {

              $('#productSubCategoryId').append("<option value='"+ value1+"'>"+key1+"</option>");
            });
          }

          if (key == "productBrandList") {
            $.each(value, function (key1,value1) {

              $('#productBrandId').append("<option value='"+ value1+"'>"+key1+"</option>");
            });
          }

          if (key == "productList") {
            $.each(value, function (key1,value1) {

              $('#product').append("<option value='"+ value1+"'>"+key1+"</option>");

            });
          } 
        }
      });

    },
    error: function(_response){
      alert("error");
    }

  });/*End Ajax*/

 }); /*End Change Product Group*/



        //Change Category
        $("#productCategoryId").change(function(){ 
         var productGroupId = $('#productGroupId').val();
         var productCategoryId = $('#productCategoryId').val();


         var csrf = "<?php echo csrf_token(); ?>";

              //alert(productGroupId);
              //alert(productCategoryId);
              
              $.ajax({
                type: 'post',
                url: './famsOnChangeCategory',
                data: {productGroupId:productGroupId, productCategoryId:productCategoryId,_token: csrf},
                dataType: 'json',   
                success: function( _response ){

                  $("#productSubCategoryId").empty();
                  $("#productSubCategoryId").prepend('<option selected="selected" value="">Please Select</option>');

                  $("#productBrandId").empty();
                  $("#productBrandId").prepend('<option selected="selected" value="">Please Select</option>');

                  $("#product").empty();
                  $("#product").prepend('<option selected="selected" value="">Please Select</option>');

                  $.each(_response, function (key, value) {
                    {
                      if (key == "productSubCategoryList") {
                        $.each(value, function (key1,value1) {

                          $('#productSubCategoryId').append("<option value='"+ value1+"'>"+key1+"</option>");
                        });
                      }

                      if (key == "productBrandList") {
                        $.each(value, function (key1,value1) {

                          $('#productBrandId').append("<option value='"+ value1+"'>"+key1+"</option>");
                        });
                      }

                      if (key == "productList") {
                        $.each(value, function (key1,value1) {

                          $('#product').append("<option value='"+ value1+"'>"+key1+"</option>");

                        });
                      } 
                    }
                  });

                },
                error: function(_response){
                  alert("error");
                }

              });/*End Ajax*/
              

            }); /*End Change Category*/


        //Change Sub Category
        $("#productSubCategoryId").change(function(){ 
         var productGroupId = $('#productGroupId').val();
         var productCategoryId = $('#productCategoryId').val();
         var productSubCategoryId = $('#productSubCategoryId').val();


         var csrf = "<?php echo csrf_token(); ?>";

         $.ajax({
          type: 'post',
          url: './famsOnCngSubCtgy',
          data: {productGroupId:productGroupId, productCategoryId:productCategoryId, productSubCategoryId: productSubCategoryId,_token: csrf},
          dataType: 'json',   
          success: function( _response ){

            $("#productBrandId").empty();
            $("#productBrandId").prepend('<option selected="selected" value="">Please Select</option>');

            $("#product").empty();
            $("#product").prepend('<option selected="selected" value="">Please Select</option>');

            $.each(_response, function (key, value) {
              {
                if (key == "productBrandList") {
                  $.each(value, function (key1,value1) {

                    $('#productBrandId').append("<option value='"+ value1+"'>"+key1+"</option>");
                  });
                }

                if (key == "productList") {
                  $.each(value, function (key1,value1) {

                    $('#product').append("<option value='"+ value1+"'>"+key1+"</option>");

                  });
                } 
              }
            });

          },
          error: function(_response){
            alert("error");
          }

        });/*End Ajax*/          


       }); /*End Change Category*/


        //Change Sub Brand
        $("#productBrandId").change(function(){ 
         var productGroupId = $('#productGroupId').val();
         var productCategoryId = $('#productCategoryId').val();
         var productSubCategoryId = $('#productSubCategoryId').val();
         var productBrandId = $('#productBrandId').val();


         var csrf = "<?php echo csrf_token(); ?>";

         $.ajax({
          type: 'post',
          url: './famsOnChangeBrand',
          data: {productGroupId:productGroupId, productCategoryId:productCategoryId, productSubCategoryId: productSubCategoryId,productBrandId: productBrandId,_token: csrf},
          dataType: 'json',   
          success: function( _response ){

            $("#product").empty();
            $("#product").prepend('<option selected="selected" value="">Please Select</option>');

            $.each(_response, function (key, value) {
              {


                if (key == "productList") {
                  $.each(value, function (key1,value1) {

                    $('#product').append("<option value='"+ value1+"'>"+key1+"</option>");

                  });
                } 
              }
            });

          },
          error: function(_response){
            alert("error");
          }

        });/*End Ajax*/          


       }); /*End Change Brand*/

     </script>

     <script type="text/javascript">

      $(document).ready(function(){
        $(".edit-modal").find(".modal-dialog").css("width","80%");
      });
    </script>

    <script type="text/javascript">
 /* 
  $(document).ready(function(){
    $("#editModalTable tr.valueRow").each(function(){

      var a = $(this).find('.editModalProductId').val();
      $("#product > option[value=="+a+"]").hide();
      
      }); 
  });
  */

</script>

<script type="text/javascript">
  /*$(document).ready(function(){
    $(".editModalLink").on('click',function(){
      var modalId = this.id;

      $("#editModalTable-"+modalId+" tr.valueRow").each(function(){
        var productId = $(this).find('.editModalProductId').val();
        $("#edit-modal-"+modalId+" .selectProduct option[value='"+productId+"']").hide();
      });

      
    });
  });*/
</script>

@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>






