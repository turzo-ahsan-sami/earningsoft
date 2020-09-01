@extends('layouts/gnr_layout')
@section('title', '| Position')
@section('content')
    
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
                            <a href="{{url('gnr/addPosition/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Position</a>
                        </div>


                        <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">POSITION LIST</font></h1>
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
                                    <th>Position Name</th>
                                    <th>Department Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                {{ csrf_field() }}
                            </thead>
                            <tbody>
                                <?php $no=0; ?>
                                @foreach($positions as $position)
                                    <tr class="item{{$position->id}}">
                                        <td class="text-center">{{++$no}}</td>
                                        <td style="text-align: left; padding-left: 5px;">{{$position->name}}</td>
                                        <td style="text-align: left; padding-left: 5px;">
                                            <?php
                                                $departmentName = DB::table('gnr_department')->select('name')->where('id',$position->dep_id_fk)->first();
                                               //dd($departmentName);
                                            ?>
                                            {{$departmentName->name}}
                                        </td>
                                        <td style="text-align: center; padding-left: 5px;">
                                            @if($position->status == 0)
                                              Inactive
                                            @else
                                               Active 
                                            @endif        
                                        </td>
                                      
                                        <td class="text-center" width="100">
<!-- 
                                            <a href="" class="form5" data-token="" data-id="{{$position->id}}">
                                                <span class="fa fa-eye"></span>
                                            </a>
 -->
                                            &nbsp
                                            <a href="{{ url('gnr/editPosition/'.$position->id) }}">
                                                    <span class="glyphicon glyphicon-edit"></span>
                                                </a>
                                            @php
                                                $employeeId = DB::table('gnr_employee')->where('company_id_fk',Auth::user()->company_id_fk)->where('position_id_fk',$position->id)->value('id');
                                            @endphp
                                              
                                            </a>&nbsp
                                            <a id="deleteIcone" href="javascript:;" class="delete-modal" positionId="{{$position->id}}" data-employeeId="{{$employeeId}}">
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

<div id="deleteModal" class="modal fade" style="margin-top:3%;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Delete Position</h4>
            </div>

            <div class="modal-body ">
                <div class="row" style="padding-bottom:20px;"> </div>
                <h2>Are You Confirm to Delete This Record?</h2>

                <div class="modal-footer">
                    <input id="DMCustomerPackageId" type="hidden"  value=""/>
                    <button type="button" class="btn btn-danger"  id="DMPosition"  data-dismiss="modal">confirm</button>
                    <button type="button" class="btn btn-warning"  data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
                </div>

            </div>
        </div>
    </div>
</div>





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
        $("#DMCustomerPackageId").val($(this).attr('positionId'));
        $('#deleteModal').modal('show');
    });
    $("#DMPosition").on('click',  function() {
        var positionId= $("#DMCustomerPackageId").val();
        var csrf = "{{csrf_token()}}";
        $.ajax({
            url: './deletePositiontem',
            type: 'POST',
            dataType: 'json',
            data: {id:positionId, _token:csrf},
            success: function(data) {
                if (data.responseTitle=='Success!') {
                  toastr.success(data.responseText, data.responseTitle, opts);
                  //$("#DMCustomerPackageId").remove(); 
                  location.reload();                       
                }
                else if(data.responseTitle=='Warning!'){
                    toastr.warning(data.responseText, data.responseTitle, opts);                        
                }

            }
        })
        // .done(function(data) {

        //     location.reload();
        
        // })
        // .fail(function(){
        //     console.log("error");
        // })
        // .always(function() {
        //     console.log("complete");
        // });
    });
});
$( document ).ready(function() {
    $(document).on('click', '.edit-modal', function() {
    $('.errormsg').empty();
    $('#MSGE').empty();
    $('#MSGS').empty();
    $('#footer_action_button').text(" Update");
    $('#footer_action_button').addClass('glyphicon glyphicon-check');
    //$('#footer_action_button').removeClass('glyphicon-trash');
    $('#footer_action_button_dismis').text(" Close");
    $('#footer_action_button_dismis').addClass('glyphicon glyphicon-remove');
    $('.actionBtn').addClass('btn-success');
    $('.actionBtn').removeClass('btn-danger');
    $('.actionBtn').addClass('edit');
    $('.modal-title').text('Update Data');
    $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
    $('.modal-dialog').css('width','50%');
    $('.deleteContent').hide();
    $('.form-horizontal').show();
    $('#id').val($(this).data('id'));
    $('#slno').val($(this).data('slno'));
    $('#name').val($(this).data('name'));
    $('#footer_action_button2').hide();
    $('#footer_action_button').show();
    
    $('.actionBtn').removeClass('delete');
    $('#').modal('show');
});

    // $(document).on('click', '.edit-modal', function() {
    //     var positionId = $(this).attr('positionId');
       
    //     var csrf = "{{csrf_token()}}";
    //     $("#editPositionId").val(positionId);
       
    //     $.ajax({
    //         url: './getPositionInfo',
    //         type: 'POST',
    //         dataType: 'json',
    //         data: {id:positionId , _token: csrf},
    //         success: function(data) {
    //             console.log(data);
    //             //$("#EMname").val(data['previousdata'].name);
    //             // var customerId = data['customer'].id;// Data Array

    //             // $("#editModal").find('.modal-dialog').css('width', '80%');
    //             // $('#editModal').modal('show');

    //         },
    //         error: function(argument) {
    //             //alert('response error');
    //         }

    //     });
    // });

});//ready function end


</script>

@include('dataTableScript')
@endsection
