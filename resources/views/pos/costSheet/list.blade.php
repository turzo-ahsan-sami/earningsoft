@extends('layouts/pos_layout')
@section('title', '| Cost Sheet List')
@section('content')
   
<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">
                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading" style="padding-bottom:0px">
                        <div class="panel-options">
                            <a href="{!! url('/pos/addCostSheet'); !!}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Cost Sheet</a>
                        </div>

                        <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">COST SHEET LIST</font></h1>
                    </div>
                    <div class="panel-body panelBodyView">
                        
                        <table class="table table-striped table-bordered" id="supplierView" style="color:black;">
                            <thead>
                                <tr>
                                    <th width="60">SL#</th>
                                    <th>Product</th>
                                    <th>Effect Date</th>
                                    <th>Total Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($costSheets as $key => $record)
                                <tr>
                                    <td class="text-center">{{ ++$key }}</td>
                                    <td style="text-align: left; padding-left: 5px;">{{ $record->product->name }}</td>
                                    <td style="text-align: center;">{{ date('d-m-Y', strtotime($record->effectDate)) }}</td>
                                    <td style="text-align: right">{{ number_format($record->totalAmount, 2, '.', ',') }}</td>
                                    <td>
                                        <a href="{{ url('pos/viewCostSheet/'.$record->id) }}">
                                            <span class="fa fa-eye"></span>
                                        </a>                 
                                        <a href="{{ url('pos/editCostSheet/'.$record->id) }}">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                        <a id="deleteIcone" href="javascript:;" class="delete-modal" costSheetid="{{ $record->id }}">
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

<div id="deleteModal" class="modal fade" style="margin-top:3%;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Delete Cost Sheet</h4>
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

@include('dataTableScript')

<script type="text/javascript">

$(document).on('click', '.delete-modal', function(){
    $("#DMCustomerPackageId").val($(this).attr('costSheetId'));
    $('#deleteModal').modal('show');
});

$("#DMCustomer").on('click',  function() {

    var costSheetId = $("#DMCustomerPackageId").val();
    var csrf = "{{csrf_token()}}";

    $.ajax({
        url: './deleteCostSheet',
        type: 'POST',
        dataType: 'json',
        data: {id:costSheetId, _token:csrf},
    })
    .done(function(data) {

        location.reload();
        window.location.href = '{{url('pos/costSheetList/')}}';
    })
    .fail(function(){
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    });
});

</script>


@endsection


