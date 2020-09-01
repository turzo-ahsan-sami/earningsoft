@extends('layouts/fams_layout')
@section('title', '| Additional Product')
@section('content')
<div class="row">
    <div class="col-md-2"></div>
<div class="col-md-8">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addFamsAdditionalProduct/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Additional Product</a>
          </div>
            <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px;color:white">ADDITIONAL PRODUCTS</h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            /*$("#famsProGroupView").dataTable().yadcf([
    
            ]);*/
            $("#famsProGroupView").dataTable({
                   "oLanguage": {
                  "sEmptyTable": "No Records Available",
                  "sLengthMenu": "Show _MENU_ "
                  }
                });
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="famsProGroupView">
            <thead>
                <tr>
                    <th width="30">SL#</th>
                    <th>Name</th>
                    <th >Category</th>
                    <th>Action</th>
                </tr>
                  
                </thead>
                <tbody>
                  <?php $no=0; ?>
                  @foreach($products as $product)
                   @php                    
                    $isBelongToProduct = DB::table('fams_additional_charge_details')->where('productId',$product->id)->value('id');                   
                  @endphp
                    <tr>
                      <td class="text-center slNo">{{++$no}}</td>
                      <td style="color:black;text-align:left;padding-left: 15px;">{{$product->name}}</td>
                      @php
                        $categoryName = DB::table('fams_product_category')->where('id',$product->categoryId)->value('name');
                      @endphp
                      <td style="color:black;">{{$categoryName}}</td>
                       <td class="text-center" width="80">
                                            
                                            <a href="javascript:;" class="edit-modal" productId="{{$product->id}}" productName="{{$product->name}}" categoryId="{{$product->categoryId}}" >
                                                <span class="glyphicon glyphicon-edit"></span>
                                            </a>&nbsp

                                            <a href="javascript:;" class="delete-modal" productId="{{$product->id}}" @php if($isBelongToProduct>0){echo "style=\"pointer-events: none;cursor: not-allowed;\"";} @endphp>
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
        <div class="col-md-2"></div>
</div>



{{-- Edit Modal --}}
    <div id="editModal" class="modal fade" style="margin-top:3%">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Additional Charge Details</h4>
                </div>
                <div class="modal-body">

                   <div class="row" style="padding-bottom: 20px;">

                            <div class="col-md-12" style="padding-left:0px;">

                                <div class="col-md-12" style="padding-right:2%;">{{--1st col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                    <div class="form-group">
                                    {!! Form::hidden('EMproductId',null,['id'=>'EMproductId']) !!}
                                        {!! Form::label('EMproductName', 'Product Name:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                           {!! Form::text('EMproductName',null,['id'=>'EMproductName','class'=>'form-control','autocomplete'=>'off']) !!}
                                           <p id="EMproductNamee"></p>

                                    </div>
                                        </div>

                                        <div class="form-group">
                                        {!! Form::label('EMcategoryId', 'Category:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                        @php
                                          $categories = DB::table('fams_product_category')->pluck('name','id');
                                        @endphp
                                        {!! Form::select('EMcategoryId',$categories,null,['id'=>'EMcategoryId','class'=>'form-control']) !!}
                                           
                                           <p id="EMcategoryIde"></p>

                                    </div>
                                        </div>



                                        </div>
                                </div>
                            </div>
                      </div>


                      <div class="modal-footer">
                        <button class="btn actionBtn glyphicon glyphicon-check btn-success edit" id="update" type="button"><span> Update</span></button>
                        <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"><span id=""> Close</span></button>
                    </div>


                </div>
            </div>
        </div>
    </div>

    {{-- End Edit Modal --}}


    {{-- Delete Modal --}}
        <div id="deleteModal" class="modal fade" style="margin-top:3%">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Delete!</h4>
                    </div>
                    <div class="modal-body">
                        <h2>Are You Confirm to Delete This Record?</h2>

                        <div class="modal-footer">
                            {!! Form::open(['url' => 'deleteFamsAdditionalProduct/']) !!}
                            <input type="hidden" name="productId" id="DMproductId">
                            <button  type="submit" class="btn actionBtn glyphicon glyphicon-check btn-success"><span id=""> Confirm</button>
                            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>
                            {!! Form::close() !!}

                        </div>

                    </div>
                </div>
            </div>
        </div>
        {{-- End Delete Modal --}}


    {{-- Update data --}}
    <script type="text/javascript">
      $(document).ready(function() {
        $("#update").click(function() {

          var id = $("#EMproductId").val();
          var name = $("#EMproductName").val();
          var categoryId = $("#EMcategoryId").val();
          var csrf = "{{csrf_token()}}";

          
          $.ajax({
                        type: 'post',
                        url: './editFamsAdditionalProduct',
                        data: {id: id, name:name,categoryId: categoryId, _token: csrf},
                        dataType: 'json',
                        success: function( _response ){
                          if (_response.accessDenied) {
                              showAccessDeniedMessage();
                              return false;
                          }
                            if(_response.errors) {
                                if (_response.errors['name']) {

                                    $("#EMproductNamee").empty();
                                     $('#EMproductNamee').append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');
                                     $("#EMproductNamee").show();
                                }
                                if (_response.errors['groupId']) {

                                    $("#EMcategoryIde").empty();
                                     $('#EMcategoryIde').append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');
                                     $("#EMcategoryIde").show();
                                }
                            }
                            else{
                                
                                window.location.href = "famsAdditionalProduct";
                            }
                            
                        },
                        error: function() {
                            alert("Error");
                        }

                    });


        });
      });
      {{-- End Update data --}}
    </script>



<script type="text/javascript">
  $(document).ready(function() {
    /*Edit Modal*/
    $(".edit-modal").on('click', function() {

      $("#EMproductId").val($(this).attr('productId'));
      $("#EMproductName").val($(this).attr('productName'));
      $("#EMcategoryId").val($(this).attr('categoryId'));
      
      $("#editModal").modal('show');
    });
    /*End Edit Modal*/

    /*Delete Modal*/
    $(".delete-modal").on('click', function() {

      $("#DMproductId").val($(this).attr('productId'));
      
      
      $("#deleteModal").modal('show');
    });
    /*End Delete Modal*/

  });
</script>



<script type="text/javascript">


    $(document).ready(function(){
        $(".edit-modal").find(".modal-dialog").css("width","50%");
        
    });
</script>

@include('dataTableScript')

@endsection