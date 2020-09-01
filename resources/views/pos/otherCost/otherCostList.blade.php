@extends('layouts/pos_layout')
@section('title', '| Other Cost List')
@section('content')
@include('successMsg')
<style type="text/css">
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
                        <a href="{{url('pos/AddOtherCost/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Other Cost</a>
                    </div>
                    <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">OTHER COST LIST</font></h1>
                </div>
             
                    <div class="panel-body panelBodyView"> 
                        <table class="table table-striped table-bordered" id="otherCostView" style="color:black;">
                            <thead>
                                <tr>
                                <th width="80">SL#</th>
                                <th width="25%">Other Cost</th>
                                <th>Ledger</th>
                                <th>Action</th>
                                </tr>
                            </thead>
                            {{ csrf_field() }}
                            <tbody>
                                @php $no = 0; @endphp
                                @foreach($otherCosts as $record)
                                <tr>
                                    <td class="text-center">{{ ++$no }}</td>
                                    <td style="text-align: left; padding-left: 5px;">{{ $record->name }}</td> 
                                    <td style="text-align: center;">{{ $record->ledger->name }}</td>
                                    
                                    <td  class="text-center" width="80">
                                        <a id="editIcone" href="{{url('pos/editOtherCost/'.$record->id)}}" class="edit-modal"">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a> &nbsp;
                                        <a id="deleteIcone" href="javascript:;" class="delete-modal" itemId="{{ $record->id }}">
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
 <!-- Start Edit Modal -->

<!-- Start Delete Modal -->
<div id="deleteModal" class="modal fade" style="margin-top:3%;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            
            <div class="modal-header">
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Delete Other Cost</h4>
            </div>

            <div class="modal-body ">
                <div class="row" style="padding-bottom:20px;"> </div>
                    <h2>Are You Confirm to Delete This Record?</h2>

                    <div class="modal-footer">
                        <input id="DMItemId" type="hidden"  value=""/>
                        <button type="button" class="btn btn-danger"  id="DMItem"  data-dismiss="modal">confirm</button>
                        <button type="button" class="btn btn-warning"  data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
                    </div>

                </div>
            </div>
        </div>
   </div>
</div>
<!-- End Delete Modal -->

@include('dataTableScript')
<script type="text/javascript">
    jQuery(document).ready(function($)
    {
        $("#otherCostView").dataTable({
            "ordering": false,
            "oLanguage": {
                "sEmptyTable": "No Records Available",
                "sLengthMenu": "Show _MENU_",
            
            }
        });
    });
</script>

<script type="text/javascript">

    $(document).ready(function(){ 
        
        $(document).on('click', '.delete-modal', function(){
            $("#DMItemId").val($(this).attr('itemId'));
            $('#deleteModal').modal('show');
        });

        $("#DMItem").on('click',  function() 
        {
            var id = $("#DMItemId").val();
            var csrf = "{{csrf_token()}}";
            $.ajax({
                url: './deleteOtherCost',
                type: 'POST',
                data: {id:id, _token:csrf},
                dataType: 'json',
            })
            .done(function(data) {
                location.reload();
                window.location.href = '{{url('pos/otherCostList/')}}';
            })
            .fail(function(){
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
        });

    });
</script>
@endsection