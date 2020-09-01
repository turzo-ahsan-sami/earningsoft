@extends('layouts/gnr_layout')
@section('title', '| Loan Product')
@section('content')


@php
  $foreignLoanProductIds = DB::table('acc_loan_register_account')->distinct()->pluck('loanProductId_fk')->toArray(); 
@endphp

<div class="row">
<div class="col-md-2"></div>
<div class="col-md-8">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('gnr/addLoanProduct/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Loan Product</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">LOAN PRODUCT LIST</font></h1>
        </div>
        
        <div class="panel-body panelBodyView">       

        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            /*$("#loanProductTable").dataTable().yadcf([
    
            ]);*/
            $("#loanProductTable").dataTable({              
                  
                   "oLanguage": {
                  "sEmptyTable": "No Records Available",
                  "sLengthMenu": "Show _MENU_ "
                  }
                });

              
       });
          
          </script>
        </div>
          <table class="table table-striped table-bordered" id="loanProductTable" style="color:black;">
                    <thead>
                      <tr>
                        <th width="30">SL#</th>
                        <th>Name</th>
                        <th>Product Code</th>
                        <th>Donar</th>                        
                        <th>Action</th>
                      </tr>
                      
                    </thead>
                    <tbody>
                    @foreach($loanProducts as $index => $loanProduct)

                    @php
                      $donarName = DB::table('gnr_bank')->where('id',$loanProduct->donorId_fk)->value('name');
                    @endphp

                    <tr>
                      <td>{{$index+1}}</td>
                      <td class="name">{{$loanProduct->name}}</td>
                      <td>{{$loanProduct->productCode}}</td>
                      <td class="name">{{$donarName}}</td>
                      


                       <td width="80">

                       {{--  <a href="javascript:;" class="view-modal" bankId="{{$bank->id}}" >
                            <i class="fa fa-eye" aria-hidden="true"></i>
                          </a>&nbsp;  --}}
                          <a href="javascript:;" class="edit-modal" productId="{{$loanProduct->id}}">
                            <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp;

                        @php
                        if (in_array($loanProduct->id, $foreignLoanProductIds)) {
                          $canDelete = 0;
                        }
                        else{
                          $canDelete = 1;
                        }   
                      @endphp

                        <a href="javascript:;" class="delete-modal" productId="{{$loanProduct->id}}" @php if($canDelete==0){ echo "style=\"pointer-events: none;cursor: not-allowed;\"";}@endphp>
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
<div class="col-md-2"></div>



{{-- Edit Modal --}}
        <div id="editModal" class="modal fade" style="margin-top:3%">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Update Product</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row" style="padding-bottom: 20px;">

                            <div class="col-md-12">
                                <div class="col-md-12" style="padding-right:2%;">{{--1st col-md-6--}}
                                    
                                    {!! Form::open(['url'=>'','class'=>'form-horizontal form-groups']) !!}

                                    {!! Form::hidden('productId',null,['id'=>'EMproductId']) !!}

                                        <div class="form-group">
                                            {!! Form::label('name', 'Product Name:', ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-9">                                               
                                                {!! Form::text('name', null,['id'=>'EMname','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                                <p id='namee' class="error"></p>
                                            </div>
                                        </div>

                                    <div class="form-group">
                                        {!! Form::label('productCode', 'Product Code:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                                
                                            {!! Form::text('productCode', null, array('class'=>'form-control', 'id' => 'EMproductCode')) !!}
                                            <p id='productCodee' class="error"></p>
                                        </div>
                                </div>

                                <div class="form-group">
                                        {!! Form::label('donor', 'Donor:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                        @php
                                            $donors = array(''=>'Select Donor') + DB::table('gnr_bank')->pluck('name','id')->toArray();
                                        @endphp
                                                
                                            {!! Form::select('donor', $donors,null, array('class'=>'form-control', 'id' => 'EMdonor')) !!}
                                            <p id='donore' class="error"></p>
                                        </div>
                                </div>

                                       {!! Form::close() !!}
                                       </div>
                            </div>

                        </div>{{--row--}}

                        {{-- Edit ModalFooter--}}
                        <div class="modal-footer">
                        <button id="updateButton" class="btn actionBtn glyphicon glyphicon-check btn-success edit" paymentId="0" type="button"> Update</button>
                            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" id="footer_action_button_dismis" type="button"><span> Close</span></button>
                        </div>


                    </div> {{-- End Edit Modal Body--}}

                </div>
            </div>
        </div>
        {{-- End Edit Modal --}}


  
{{-- Delete Modal --}}
  <div id="deleteModal" class="modal fade" style="margin-top:3%">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Delete!</h4>
        </div>
        <div class="modal-body">
          <h2>Are You Confirm to Delete This Record?</h2>

          <div class="modal-footer">
            <input id="DMproductId" type="hidden" name="DMproductId" value="">
            <button id="DMconfirmButton" type="button" class="btn btn-danger"> Confirm</button>
            <button  class="btn btn-warning glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>
            
          </div>

        </div>
      </div>
    </div>
  </div>
  {{-- End Delete Modal --}}



<style type="text/css">
  .error{
    color: red;
  }
</style>


<script type="text/javascript">
  $(document).ready(function() {


    /*Edit Modal*/
    $(document).on('click', '.edit-modal', function() {
      
      var productId = $(this).attr('productId');
      var csrf = "{{csrf_token()}}";
      
      $("#EMproductId").val(productId);

      $.ajax({
        url: './getLoanProductInfo',
        type: 'POST',
        dataType: 'json',
        data: {productId: productId, _token: csrf},
      })
      .done(function(product) {
        
        $("#EMname").val(product.name);
        $("#EMproductCode").val(product.productCode);
        $("#EMdonor").val(product.donorId_fk);
        

        $("#editModal").modal('show');

        console.log("success");
      })
      .fail(function() {
        console.log("error");
      })
      .always(function() {
        console.log("complete");
      });
      
    });/*End Edit Modal*/




    /*Update Data*/
    $("#updateButton").on('click',function() {
     

      $.ajax({
        url: './editLoanProduct',
        type: 'POST',
        dataType: 'json',
        data: $('form').serialize(),
      })
      .done(function(data) {

         if (data.errors) {
            if (data.errors['name']) {
                $("#namee").empty();
                $("#namee").append('* '+data.errors['name']);
            }
            if (data.errors['productCode']) {
                $("#productCodee").empty();
                $("#productCodee").append('* '+data.errors['productCode']);
            }
            if (data.errors['donor']) {
                $("#donore").empty();
                $("#donore").append('* '+data.errors['donor']);
            }
        }
        else{
          location.reload();
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
    /*End Update Data*/



    /*Delete Modal*/
    $(document).on('click', '.delete-modal', function() {
      var productId = $(this).attr('productId');

      $("#DMproductId").val(productId);
      $("#deleteModal").modal('show');
      
    });
    /*End Delete Modal*/


    /*Delete Data*/
    $("#DMconfirmButton").on('click', function() {
      var productId = $("#DMproductId").val();
      var csrf = "{{csrf_token()}}";

      $.ajax({
        url: './deleteLoanProduct',
        type: 'POST',
        dataType: 'json',
        data: {productId: productId,  _token: csrf},
      })
      .done(function(data) {
        location.reload();
        console.log("success");
      })
      .fail(function() {
        console.log("error");
      })
      .always(function() {
        console.log("complete");
      });
      
      
    });
    /*End Delete Data*/



     /*On input/change Hide the Errors*/
     $(document).on('input', 'input', function() {
         $(this).closest('div').find('.error').empty();
     });
      $(document).on('change', 'select', function() {
         $(this).closest('div').find('.error').empty();
     });
     /*End On input/change Hide the Errors*/




  });/*Ready*/
</script>


@include('dataTableScript')


<style type="text/css">
  #loanProductTable thead tr th{
    border-bottom: 1px solid white !important;
  }
  #loanProductTable tbody tr td.name{
    text-align: left;
    padding-left: 5px;
  }
</style>

@endsection

