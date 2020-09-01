@extends('layouts/fams_layout')
@section('title', '| Issue')
@section('content')
@include('successMsg')
<div class="row">
  <div class="col-md-12">
    <div class="" style="">
      <div class="">
        <div class="panel panel-default" style="background-color:#708090;">
          <div class="panel-heading" style="padding-bottom:0px">
            <div class="panel-options">
              <a href="{{url('famsAddIssue/')}}" class="btn btn-info pull-right addViewBtn" style=""><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Issue</a>
            </div>
            <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">ISSUES</font></h1>

          </div>

          <div class="panel-body panelBodyView"> 

            <div>

              <script type="text/javascript">
                jQuery(document).ready(function($)
                {
                  $("#gnrGrounView").dataTable().yadcf([

                    ]);
                });
              </script>
            </div>
            <table class="table table-striped table-bordered" id="gnrGrounView">
              <thead>
                <tr>
                  <th width="32">SL#</th>
                  <th>Date</th>
                  <th>Bill No</th>
                  <th>Order No</th>
                  <th>Issue Order No</th>
                  <th>Branch Name</th>
                  <th>Quantity</th>
                  <th>Amount</th>

                  <th id="details" width="100" style="pointer-events:none;">Action</th>

                </tr>
                {{ csrf_field() }} 
              </thead>
          {{-- <tfoot>
            <th width="32">SL#</th>
                <th>Date</th>
                <th>Bill No</th>
                <th>Order No</th>
                <th>Issue Order No</th>
                <th>Branch Name</th>
                <th>Quantity</th>
                <th>Amount</th>
                
                <th id="details" style="pointer-events:none;">Action</th>
              </tfoot> --}}
              <tbody>            
                <?php $no=0; ?>
                @foreach($issues as $issue) 
                <tr class="item{{$issue->id}}">
                  <td class="text-center slNo">{{++$no}}</td>
                  <td>{{date('d/m/Y', strtotime($issue->issueDate))}}</td>
                  <td>{{$issue->issueBillNo}}</td>
                  <td>{{$issue->orderNo}}</td>
                  <td>{{$issue->issueOrderNo}}</td>
                  <td>{{$issue->branchName}}</td>
                  <td>{{$issue->totlaIssueQuantity}}</td>
                  <td>{{$issue->totalIssueAmount}}</td>

                  <td class="text-center" width="80">

                    <a href="" data-toggle="modal" data-target="#view-modal-{{$issue->id}}" >
                      <i class="fa fa-eye" aria-hidden="true" ></i>
                    </a>&nbsp

                    <a href="" id={{$issue->id}} data-toggle="modal" data-target="#edit-modal-{{$issue->id}}" >
                      <span class="glyphicon glyphicon-edit"></span>
                    </a>&nbsp

                    <a href="" data-toggle="modal" data-target="#delete-modal-{{$issue->id}}" >
                      <span class="glyphicon glyphicon-trash"></span>
                    </a>
                  </div>
                </div>
                {{-- View Modal --}}
                <div id="view-modal-{{$issue->id}}" class="modal fade" style="margin-top:3%">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Issue Details</h4>
                      </div>
                      <div class="modal-body">
                        <div>
                          <h4 align="left">Issue Bill No: {{$issue->issueBillNo}}<br>Issue Date: {{date('d/m/Y', strtotime($issue->issueDate))}} <br> Branch: {{$issue->branchName}}</h4>
                        </div>
                        <table class="table table-striped table-bordered">
                          <thead>

                            <tr>                

                              <th align="left">Product Name</th>
                              <th>Quantity</th>
                              <th>Price</th>
                              <th>Total Price</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($issueDetails as $issueDetail)
                            @if($issue->issueBillNo==$issueDetail->issueBillNoId)
                            <tr>

                              <td align="left">{{$issueDetail->issueProductName}}</td>
                              <td>{{$issueDetail->issueQuantity}}</td>
                              <td>@foreach($products as $product)
                                @if($issueDetail->issueProductId == $product->id)
                                {{$product->costPrice}}
                                @endif
                                @endforeach
                              </td>
                              <td>{{$issueDetail->issueCostPrice}}</td>
                            </tr>
                            @endif
                            @endforeach

                          </tbody>
                        </table>

                        <div class="modal-footer">                   
                          <button class="btn btn-warning glyphicon glyphicon-remove" data-dismiss="modal" id="footer_action_button_dismis" type="button"><span id=""> Close</span></button>
                        </div>


                      </div>
                    </div>
                  </div>
                </div>
                {{-- End View Modal --}}

                {{-- Edit Modal --}}

                <div id="edit-modal-{{$issue->id}}" class="modal fade edit-modal" style="margin-top:3%;">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Issue</h4>
                      </div>


                      <div class="modal-body" >



                        {!! Form::open(['url' => 'famsEditIssue/']) !!}

                        <div class="row" style="margin-left: 0px; margin-right: 0px;padding-right: 0px;">
                          <div class="col-md-12" style="margin-left: 0px">

                            {{-- Bill --}}
                            <div class="form-group col-md-6">
                              {!! Form::label('issueBillNo', 'Issue Bill No:', ['class' => 'col-sm-3 control-label']) !!}
                              <div class="col-sm-9">
                                {!! Form::text('editModalIssueBillNo', $value = $issue->issueBillNo, ['class' => 'form-control', 'id' => 'issueOrderNo', 'type' => 'text','autocomplete'=>'off','style'=>'width: 80%','readonly']) !!}
                              </div>
                            </div>

                            <div class="form-group col-md-6">
                              {!! Form::label('issueOrderNo', 'Issue Order No:', ['class' => 'col-sm-3 control-label']) !!}
                              <div class="col-sm-9">
                                {!! Form::text('issueOrderNo', $value = $issue->orderNo, ['class' => 'form-control', 'id' => 'issueOrderNo', 'type' => 'text','autocomplete'=>'off','style'=>'width: 80%','readonly']) !!}
                              </div>
                            </div>
                            <input type="hidden" value="{{$issue->id}}" name="id">
                          </div>


                        </div>{{-- End Row --}}


                        <div class="row" style="margin-left: 0px;padding-top: 3%">
                          <div class="col-md-12" style="margin-left: 0px">


                            <div class="form-group col-md-6">
                              {!! Form::label('issueDate', 'Issue Date:', ['class' => 'col-sm-3 control-label']) !!}
                              <div class="col-sm-9">
                                {!! Form::text('issueDate', $value = date('d/m/Y', strtotime($issue->issueDate)), ['class' => 'form-control', 'id' => 'issueOrderNo', 'type' => 'text','autocomplete'=>'off','style'=>'width: 80%','readonly']) !!}
                              </div>
                            </div>





                            <div class="form-group col-md-6">
                              {!! Form::label('issueBranch', 'Branch:', ['class' => 'col-sm-3 control-label']) !!}
                              <div class="col-sm-9">
                                <select name="editModalBranchId" class="form-control editModalBranchId" style="width: 80%">
                                  @foreach($branches as $bran)
                                  <option value={{$bran->id}} @if($bran->id==$issue->branchId){{"selected='selected'"}}@endif>{{$bran->name}}</option>
                                  @endforeach
                                </select>
                              </div>
                            </div>        

                          </div>
                        </div>{{-- end row --}}
                        <div><br><br></div>
                        <div style="border-style: solid;"></div>
                        <div><br><br></div>




                        {{-- Filtering Inputs --}}
                        <div class="col-md-12" style="padding-right: 0px;padding-bottom: 3%;padding-right: 0px;margin-right: 0px;">

                          <div class="form-group col-sm-3" style="padding-right: 2%;">
                            {!! Form::label('productGroupId', 'Group:', ['class' => 'control-label col-sm-6',]) !!}
                            
                            <select  id="productGroupId" class="form-control col-sm-6" style="padding-right: 0px;">
                             <option value="" selected="selected">Please Select</option>
                             @foreach($productGroups as $productGroup)
                             <option value={{$productGroup->id}}>{{$productGroup->name}}</option>
                             @endforeach
                           </select>

                         </div>

                         <div class="form-group col-sm-3" style="padding-right: 2%;">
                          {!! Form::label('productCategoryId', 'Category:', ['class' => 'control-label col-sm-6']) !!}

                          <select  id="productCategoryId" class="form-control col-sm-6">
                           <option value="" selected="selected">Please Select</option>
                           @foreach($productCategories as $productCategory)
                           <option value={{$productCategory->id}}>{{$productCategory->name}}</option>
                           @endforeach

                         </select>                                
                       </div>

                       <div class="form-group col-sm-3" style="padding-right: 2%;">
                        {!! Form::label('productSubCategoryId', 'Sub Category:', ['class' => 'control-label col-sm-6']) !!}

                        <select  id="productSubCategoryId" class="form-control col-sm-6">
                         <option value=""  selected="selected">Please Select</option>
                         @foreach($productSubCategories as $productSubCategory)
                         <option value={{$productSubCategory->id}}>{{$productSubCategory->name}}</option>
                         @endforeach

                       </select>                                
                     </div>

                     <div class="form-group col-sm-3" style="padding-right: 2%;">
                      {!! Form::label('productBrandId', 'Brand:', ['class' => 'control-label col-sm-6']) !!}

                      <select  id="productBrandId" class="form-control col-sm-6">
                       <option value="" selected="selected">Please Select</option>
                       @foreach($productBrands as $productBrand)
                       <option value={{$productBrand->id}}>{{$productBrand->name}}</option>
                       @endforeach

                     </select>                                
                   </div>                  


                 </div>
                 {{-- End Filtering Input --}}


                 <div> <br><br></div>

                 <table id="editModalTable-{{$issue->id}}" class="table table-bordered responsive">

                  <tr>

                    <th style="text-align:center;">
                      <select class="form-control selectProduct" id="product">
                        <option value="0">Select Product</option>
                        @foreach($products as $prod2)
                        <option value="{{$prod2->id}}" >{{$prod2->name}}</option>
                        @endforeach
                      </select></th>
                      <th>
                        <input type="text" class="quantity" placeholder="Product Quantiy" style="text-align:center;">
                      </th>

                      <th>
                        {{-- <input type="hidden" id="productPrice" name="productPrice"> --}}
                        <span class="price"></span>
                      </th> 
                      <th><span class="totalPrice"></span></th>
                      <th><button type="button" class='btn btn-primary btn-xs addToCart' id={{$issue->id}} style='text-align:center;border-radius:0;width:80px;'>Add to Cart</button></th>


                    </tr>
                    <tbody>
                      <tr class="editModalTableHeadRow">
                        <td>Product Name</td>
                        <td>Quantity</td>
                        <td>Price</td>
                        <td>Total</td>
                        <td>Action</td>
                      </tr>

                      @foreach($issueDetails as $issueDetail)
                      @if($issue->issueBillNo==$issueDetail->issueBillNoId)
                      <tr class="valueRow">

                        <td class="editModalTableColumnName">{{$issueDetail->issueProductName}}</td>
                        <td class="vlaueColumn"><input type="hidden" name="editModalProductId[]" class="editModalProductId" value={{$issueDetail->issueProductId}}> <input type="text" name="editModalQuantity[]" class="editModalQuantity" value={{$issueDetail->issueQuantity}} style="text-align:center;"> </td>
                        <td class="priceColumn">@foreach($products as $prod)
                          @if($issueDetail->issueProductId == $prod->id)
                          {{$prod->costPrice}}
                          @endif
                        @endforeach</td>
                        <td class="totalPriceColumn">{{$issueDetail->issueCostPrice}}</td>
                        <td><button id={{$issue->id}} class="btn btn-danger btn-x removeButton" style='text-align:center;border-radius:0;width:80px; padding: 2px 10px;'>Remove</button></td>
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

          {{-- End Edit Modal --}}


          {{-- Delete Modal --}}
          <div id="delete-modal-{{$issue->id}}" class="modal fade" style="margin-top:3%">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Delete!</h4>
                </div>
                <div class="modal-body">
                  <h2>Are You Confirm to Delete This Issue?</h2>

                  <div class="modal-footer"> 
                    {!! Form::open(['url' => 'famsDeleteIssue/']) !!}
                    <input type="hidden" name="issueBillNo" value={{$issue->issueBillNo}}>
                    <input type="hidden" name="id" value={{$issue->id}}>
                    <button  type="submit" class="btn btn-danger"><span id=""> Confirm</span></button>                  
                    <button class="btn btn-warning glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</span></button>
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
    $("#action").attr("class", "");
    $("#filterBranch").attr("class", "");
  });
  
</script>

<script type="text/javascript">
  $(document).ready(function(){
    /*Change Price*/
    $(".selectProduct").change(function(){

      var productId = $(".selectProduct option:selected").val();
      var quantity = $(".quantity").val();
      var csrf = "<?php echo csrf_token(); ?>";

      $.ajax({
        type:'post',
        url: './issueGetProductPrice',
        data: {productId: productId, _token: csrf},
        dataType: 'json',
        success: function( price ) {
          $(".price").html(price);
          if (price=="") {$(".totalPrice").html("");}
          else{$(".totalPrice").html(price*quantity);}
        },
        error: function( _response ){
        }
      });
    });

    /*Change Total Price*/
    $(".quantity").on('input',function(){
      this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

      var productPrice = $(".price").html();
      $(".totalPrice").html(productPrice*this.value);
      
    });

    /*Change Price*/
    $(".editModalQuantity").on('input',function(){
      this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

      var productPrice = parseInt($(this).closest('tr').find(".priceColumn").html());
      $(this).closest('tr').find(".totalPriceColumn").html(productPrice*this.value);

    });


    /*Remove Button*/
    $(document).on('click', 'button.removeButton', function () {
      var modalId = this.id;

      var productId = $(this).closest('tr').find(".editModalProductId").val();

      $("#edit-modal-"+modalId+" .selectProduct option[value='"+productId+"']").show();
      $(this).closest('tr').remove();
      return false;
    });

    /*Add to Cart*/
    $(document).on('click','button.addToCart',function(){

      var modalId = this.id;
      var productId = $("#edit-modal-"+modalId+" .selectProduct option:selected").val();
      /*var flag = true;


       $("#editModalTable tr.valueRow").each(function(){

          var a = $(this).find('.editModalProductId').val();
          if (productId==a) {
            alert("It is already in the Cart");
           flag = false;
        }

      
      }); */


      // flag = false;

       //if (flag==true) {
        var productName = $("#edit-modal-"+modalId+" .selectProduct option:selected").html();
        var quantity = parseInt($("#edit-modal-"+modalId+" .quantity").val());
        var price = $("#edit-modal-"+modalId+" .price").html();
        var totalPrice = parseInt($("#edit-modal-"+modalId+" .totalPrice").html());


        if (productId !="" && quantity>0) {


          var flag = true;

          if ($("#editModalTable-"+modalId+" tr.valueRow").length > 0) {                  

            $("#editModalTable-"+modalId+" tr.valueRow").each(function(){
              if(parseInt($(this).find('.editModalProductId').val()) == productId){                  

                var previousQuantity =  parseInt($(this).find('.editModalQuantity').val());
                var previousTotal =  parseInt($(this).find('.totalPriceColumn').html());

                $(this).find('.editModalQuantity').val(previousQuantity+quantity)


                $(this).find('.totalPriceColumn').html("");
                $(this).find('.totalPriceColumn').html(previousTotal+totalPrice);

                flag = false;

              }

            });   

          }

          if(flag==true){
            var markup = "<tr class='valueRow'><td>"+productName+"</td><td><input type='hidden' name='editModalProductId[]' class='editModalProductId' value="+productId+"><input type='text'  name='editModalQuantity[]' style='text-align:center;' class='editModalQuantity' value='"+quantity+"'></td><td class='priceColumn'>"+price+"</td><td class='totalPriceColumn'>"+totalPrice+"</td><td><button id='"+modalId+"' class='btn btn-danger btn-x removeButton' style='text-align:center;border-radius:0;width:80px; padding: 2px 10px;'>Remove</button></td> </tr>";

            $("#editModalTable-"+modalId+" tr.editModalTableHeadRow").after(markup);
          }



          $("#edit-modal-"+modalId+" .selectProduct").val("0");
      //$("#edit-modal-"+modalId+" .selectProduct option[value="+productId+"]").hide();
      $("#edit-modal-"+modalId+" .quantity").val("");
      $("#edit-modal-"+modalId+" .price").html("");
      $("#edit-modal-"+modalId+" .totalPrice").html("");
    }


   // }

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
    url: './onChangeGroup',
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
                url: './onChangeCategory',
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
          url: './onChangeSubCategory',
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
          url: './onChangeBrand',
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
  $(document).ready(function(){
    $(".editModalLink").on('click',function(){
      var modalId = this.id;

      $("#editModalTable-"+modalId+" tr.valueRow").each(function(){
        var productId = $(this).find('.editModalProductId').val();
        $("#edit-modal-"+modalId+" .selectProduct option[value='"+productId+"']").hide();
      });

      
    });
  });
</script>


@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
