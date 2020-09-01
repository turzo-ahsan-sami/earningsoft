@extends('layouts/pos_layout')
@section('title', '| Order')
@section('content')'
<div class="row">
<div class="col-md-12">
@if (session('saleUpdate'))
<div class="alert alert-info alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Info!</strong> {{ session('saleUpdate') }}
</div>
@endif
@if (session('saleDelete'))
<div class="alert alert-info alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Info!</strong> {{ session('saleDelete') }}
</div>
@endif
<div class="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">          
              <a href="{{url('pos/posAddOrder/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon">
                        </i>Add Order</a>
          </div>
            <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px; color: white;">ORDERS LIST</h1>
        </div>
        <div class="panel-body panelBodyView">
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            $("#famsSaleTable").dataTable({
              "ordering": false,
                   "oLanguage": {
                  "sEmptyTable": "No Records Available",
                  "sLengthMenu": "Show _MENU_",
                  
                  }
                });
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="famsSaleTable">
                <thead>
                  <tr>
                    <th width="80">SL#</th>
                    <th>Orders Date</th>                        
                    <th>Order Number</th>
                    <th width="25%">Customer</th>  
                   {{--  <th width="15%">Total Received Quantity</th>  --}}                       
                    <th width="15%">Total Order Amount</th>                                                                    
                    <th width="8%">Action</th>
                  </tr>                 
                </thead>
                <tbody>
                  <?php $no=0; ?>
                  @foreach($orders as $order)
                    <tr class="item{{$order->id}}">
                      <td style="color: black;">{{++$no}}</td>
                      <td style="color: black;text-align: center;">{{date('d-m-Y',strtotime($order->orderDate ))}}</td>
                      <td style="color: black;">{{$order->billNo}}</td> 
                      <td style="color: black; text-align: left; padding-left: 5px;">{{$order->cusName}}</td>                              
                     {{--  <td style="color: black;">{{$order->qty}}</td>  --}}                     
                      <td style="color: black; text-align: right; padding-right: 5px;">{{$order->totalAmount}}</td>                                   
                      <td>
                        {{--  <a href="javascript:void(0);" onclick='viewOrderItem("{{$order->id}}");'>
                            <span class="fa fa-eye"></span>
                          </a> --}}
                          <a href="{{ url('pos/viewOrderItem/'.$order->id) }}">
                            <span class="fa fa-eye"></span>
                        </a>                 
                          <a href="javascript:;" onclick="editOption({{ $order->id }})">
                            <span class="glyphicon glyphicon-edit"></span>
                        </a>
                      
                        <a href="javascript:;" class="delete-modal" data-id="{{ $order->id }}">
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

      {{-- Delete Modal --}}
       <div id="myModal" class="modal fade" style="margin-top:3%">
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                      <h4 class="modal-title" style="clear:both"></h4>
                  </div>
                  <div class="modal-body">
                      <div class="deleteContent" style="padding-bottom:20px;">
                          <h4>You are about to delete this item. This procedure is irreversible !</h4>
                          <h4>Do you want to proceed ?</h4>
                          <span class="hidden id"></span>
                          {{-- <span class="hidden vouchertypeid"></span> --}}
                      </div>
                      <div class="modal-footer">
                          <p id="MSGE" class="pull-left" style="color:red"></p>
                          <p id="MSGS" class="pull-left" style="color:green"></p>
                          {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn', 'id' => 'footer_action_button'] ) !!}
                          {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn', 'data-dismiss' => 'modal', 'id' => 'footer_action_button2'] ) !!}
                          {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-warning', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_dismis'] ) !!}
                      </div>
                  </div>
              </div>
          </div>
      </div>
      {{-- End Delete Modal --}}

      <div id="myModalMsg" class="modal fade" style="margin-top:3%">
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="">
                      <h4 class="" style="clear:both"></h4>
                  </div>
                  <div class="modal-body">
                      <div class="deleteContent" style="padding-bottom:20px;">
                          <h4>You have to delete month end first for this branch !</h4>
                          <h4>Before you proceed</h4>
                      </div>
                      <div class="modal-footer">
                          <p id="MSGE" class="pull-left" style="color:red"></p>
                          <p id="MSGS" class="pull-left" style="color:green"></p>
                          {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-warning', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_dismis'] ) !!}
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <div id="myModalMsg2" class="modal fade" style="margin-top:3%">
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="">
                      <h4 class="" style="clear:both"></h4>
                  </div>
                  <div class="modal-body">
                      <div class="deleteContent" style="padding-bottom:20px;">
                          <h4>You have to do year end first for this branch !</h4>
                          <h4>Before you proceed</h4>
                      </div>
                      <div class="modal-footer">
                          <p id="MSGE" class="pull-left" style="color:red"></p>
                          <p id="MSGS" class="pull-left" style="color:green"></p>
                          {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-warning', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_dismis'] ) !!}
                      </div>
                  </div>
              </div>
          </div>
      </div>



<script type="text/javascript">

  checkTransaction = <?php echo json_encode(Session::get('msgActive')) ?>;
  if(checkTransaction == true)
  {
    $('#myModalMsg2').modal('show');
  }

  function viewOrderItem(OrderId) {

     $.ajax({
            type: 'get',
            url: './viewOrderItem',
            data: {'OrderId': OrderId},

            success: function(data) {
              //alert(OrderId); location.reload();
                 window.location.href = "{{ url('pos/viewOrderItem/')}}";
                //location.reload();
            },
            error: function(data ){
                alert('Error');
            }
    });  
  }

  function editOption(value)
  {
      $.ajax({
          url:'./monthEndCheckForOrder',
          type: 'GET',
          data: {OrderId:value},
          dataType: 'json',
          success: function(data) {
              if(data.status == true) window.location.href = "{{ url('pos/editOrderItem/') }}/"+value;
              else $('#myModalMsg').modal('show');
          }
      });
  }

window.hasAnyError = 0;
  window.onerror = function(){
    hasAnyError = 1;
  }
  if (hasAnyError || !hasAnyError) {

    window.onload = function(){


    /*View Modal*/
    $(".view-modal").on('click', function() {
      
        $("#VMsaleId").val($(this).attr('saleId'));
        $("#VMproductName").val($(this).attr('productName'));
        $("#VMproductCode").val($(this).attr('productCode'));
        $("#VMbranchName").val($(this).attr('productOfBranch'));
        $("#VMsaleByBranchName").val($(this).attr('saleByBranch'));
        $("#VMsaleDate").val($(this).attr('saleDate'));
        $("#VMsalePrice").val($(this).attr('salePrice'));
        $("#VMproductCost").val($(this).attr('productCost'));
        $("#VMaccDep").val($(this).attr('accDep'));
        $("#VMprofitAmount").val($(this).attr('profitAmount'));
        $("#VMlossAmount").val($(this).attr('lossAmount'));
        $("#viewModal").modal('show');
    
    });/*End View Modal*/

    /*Edit Modal*/
    $(".edit-modal").on('click', function() {

      $("#EMsaleRowId").val($(this).attr('saleRowId'));
      $("#EMsaleId").val($(this).attr('saleId'));
      $("#EMproductId").val($(this).attr('productId'));
      $("#EMproductName").val($(this).attr('productName'));
      $("#EMproductCode").val($(this).attr('productCode'));
      $("#EMProductOfBranch").val($(this).attr('productOfBranch'));
      $("#EMsaleByBranchName").val($(this).attr('saleByBranch'));
      $("#EMsaleDate").val($(this).attr('saleDate'));
      $("#EMsaleAmount").val($(this).attr('salePrice'));
      $("#EMproductCost").val($(this).attr('productCost'));
      $("#EMaccDep").val($(this).attr('accDep'));
      $("#EMprofitAmount").val($(this).attr('profitAmount'));
      $("#EMlossAmount").val($(this).attr('lossAmount'));
      $("#editModal").modal('show');
    
    });/*End Edit Modal*/


    /*Delete Modal*/
   $(document).on('click', '.delete-modal', function() {

        OrderId = $(this).data('id');

        $.ajax({
            url:'./monthEndCheckForOrder',
            type: 'GET',
            data: {OrderId:OrderId},
            dataType: 'json',
            success: function(data) {
                if(data.status == false)
                {
                    $('#myModalMsg').modal('show');
                }
                else
                {
                      $('#MSGE').empty();
                      $('#MSGS').empty();
                      $('#footer_action_button2').text(" Yes");
                      $('#footer_action_button2').removeClass('glyphicon glyphicon-check');
                      $('#footer_action_button_dismis').text(" No");
                      $('#footer_action_button_dismis').removeClass('glyphicon glyphicon-remove');
                      $('.actionBtn').removeClass('edit');
                      $('.actionBtn').removeClass('btn-success');
                      $('.actionBtn').addClass('btn-danger');
                      $('.actionBtn').addClass('delete');
                      $('.modal-title').text('Delete Order');
                      $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
                      $('.modal-dialog').css('width','30%');
                      $('.id').text(OrderId);
                      $('.deleteContent').show();
                      $('.form-horizontal').hide();
                      $('#footer_action_button2').show();
                      $('#footer_action_button').hide();
                      $('#myModal').modal('show');
                }
            }
        });
    });
    
    // $('.modal-footer').on('click', '.delete', function() {

    //     var _token = $('input[name=_token]').val();
    //     var id = $('.id').text();

    //     $.ajax({
    //         type: 'post',
    //         url: './deleteOrderItem',
    //         data: {'_token': _token, 'id': id},

    //         success: function(data) {
    //              alert('Order deleted successfully!');
    //             // location.reload();
    //             window.location.href = "{{ url('pos/Order/')}}";
    //         },
    //         error: function(data ){
    //             alert('Error');
    //         }
    //     });

    // });
    /*End Delete Modal*/

    $('#footer_action_button2').click(function(){

        var _token = $('input[name=_token]').val();
        var id = $('.id').text();

        $.ajax({
            type: 'post',
            url: './deleteOrderItem',
            data: {'_token': _token, 'id': id},

            success: function(data) {
                alert('Order deleted successfully!');
                location.reload();
                window.location.href = "{{url('pos/order/')}}";
            },
            error: function(data ){
                alert('Error');
            }
        });
    });


    $("#EMsaleAmount").on('input', function() {
    this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
    $("#EMsaleAmounte").hide();
  });


   

   $("#submit").on('click', function(event) {
    
    var amount = $("#EMsaleAmount").val();
    
    if(amount==""){
      event.preventDefault();
      $("#EMsaleAmounte").empty();
      $("#EMsaleAmounte").append('<span class="errormsg" style="color:red;">*Required</span>');
      $("#EMsaleAmounte").show();
    }
     
     
   });

   $("#famsSaleTable tr").find(".dataTables_empty").css("color","black");
  $("#famsSaleTable_info").hide();

  } /*End On Load*/


    //$("#famsWriteOffTable tr").find(".dataTables_empty").css("color","black");
    //$("#famsWriteOffTable_info").hide();
    $("#viewModal").find(".modal-dialog").css("width","80%");
    $("#editModal").find(".modal-dialog").css("width","80%");
    
  }/*End has Error*/
  
</script> 




@include('dataTableScript')


@endsection