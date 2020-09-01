@extends('layouts/acc_layout')
@section('title', '| View Opening Balance')
@section('content')
@include('successMsg')
<?php

// phpinfo();

?>

<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">
                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading" style="padding-bottom:0px">
                        <div class="panel-options">
                            <a href="{{url('addOpeningBalance/')}}" class="btn btn-info pull-right addViewBtn"
                               style=""><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Opening Balance</a>
                        </div>
                        <h3 style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">
                            OPENING BALANCE</h3>
                        {{-- <div class="col-sm-6"><h3 align="right" style="font-family: Antiqua; letter-spacing: 2px; color: white;">ACCOUNT TYPE</h3></div>
                        <div class="panel-options col-sm-6">
                            <a href="{{url('addAccountType/')}}" class="btn btn-info pull-right addViewBtn" style=""><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Account Type</a>
                        </div> --}}

                    </div>
                    <div class="panel-body panelBodyView">
                        <div>
                            <script type="text/javascript">
                                jQuery(document).ready(function ($) {
                                    // $("#accOpeningBalanceTable").dataTable().yadcf([]);
                                    $("#accOpeningBalanceTable").dataTable({
                                        "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]]
                                    });
                                });
                            </script>
                        </div>
                        <table class="table table-striped table-bordered" id="accOpeningBalanceTable">
                            <thead>
                            <tr>
                                <th width="80">SL#</th>
                                {{-- <th>Fiscal Year</th> --}}
                                <th>Opening Date</th>
                                <th>Project</th>
                                <th>Branch</th>
                                {{-- <th>Project Type</th> --}}
                                <th class="" width="80">Actions</th>
                            </tr>
                            {{ csrf_field() }}
                            </thead>
                            <tbody style="color: black;">
                            <?php $no = 0; ?>
                            @foreach($openingBalances as $openingBalance)
                                <tr class="item{{$openingBalance->id}}">
                                    <td class="text-center">{{++$no}}</td>
                                    {{-- <td>
                                        @php
                                            echo DB::table('gnr_fiscal_year')->where('id', $openingBalance->fiscalYearId)->value('name');
                                        @endphp                                        
                                    </td> --}}
                                    <td>{{$openingBalance->openingDate}}</td>

                                    <td>
                                        @php
                                            echo DB::table('gnr_project')->where('id', $openingBalance->projectId)->value('name');
                                        @endphp                                        
                                    </td>
                                    <td>
                                        @php
                                            echo DB::table('gnr_branch')->where('id', $openingBalance->branchId)->value('name');
                                        @endphp                                        
                                    </td>
                                    <td class="text-center" width="80">
                                        <a id="editIcone" href="{{url('editOpeningBalance/'.encrypt($openingBalance->id))}}" target="_blank" class="edit-modal" data-id="{{$openingBalance->id}}">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>&nbsp;
                                        {{-- <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$openingBalance->id}}">
                                            <span class="glyphicon glyphicon-trash"></span>
                                        </a> --}}

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


<div id="myModal" class="modal fade" style="margin-top:3%">
<div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title" style="clear:both"></h4>
      </div>
      <div class="modal-body">        
          <div class="deleteContent" style="padding-bottom:20px;">
            <h4>You are about to delete this item this procedure is irreversible !</h4>
            <h4>Do you want to proceed ?</h4> 
            <span class="hidden id"></span>
          </div>
        <div class="modal-footer">
            <p id="MSGE" class="pull-left" style="color:red"></p>
            <p id="MSGS" class="pull-left" style="color:green"></p>
          {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn', 'id' => 'footer_action_button'] ) !!}
          {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn',  'data-dismiss' => 'modal', 'id' => 'footer_action_button2'] ) !!}
          
          {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-warning', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_dismis'] ) !!}
          
        </div>
      </div>
    </div>
  </div>
</div>

@include('dataTableScript')



<script type="text/javascript">
    $(document).ready(function() {
        
//delete function
        $(document).on('click', '.delete-modal', function() {
            $('#MSGE').empty();
            $('#MSGS').empty();
            $('.actionBtn').removeClass('edit');
            $('#footer_action_button2').text(" Yes");
            $('#footer_action_button2').removeClass('glyphicon glyphicon-check');
            //$('#footer_action_button').addClass('glyphicon-trash');
            $('#footer_action_button_dismis').text(" No");
            $('#footer_action_button_dismis').removeClass('glyphicon glyphicon-remove');
            $('.actionBtn').removeClass('btn-success');
            $('.actionBtn').addClass('btn-danger');
            $('.actionBtn').addClass('delete');
            $('.modal-title').text('Delete');
            $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
            $('.modal-dialog').css('width','30%');
            $('.id').text($(this).data('id'));
            $('.deleteContent').show();
            $('.form-horizontal').hide();
            $('#footer_action_button2').show();
            $('#footer_action_button').hide();
            $('.title').html($(this).data('uname'));
            $('#myModal').modal('show');
            });

            $('.modal-footer').on('click', '.delete', function() {

                $.ajax({
                    type: 'post',
                    url: './deleteOpeningBalanceItem',
                    data: {
                      '_token': $('input[name=_token]').val(),
                      'id': $('.id').text()
                    },
                    success: function(data) {                        
                        window.location.href = '{{url('viewOpeningBalance/')}}';
                    }
                });
            });


    });
</script>





@endsection

{{-- <script src="{{asset('js/jquery-1.11.1.min.js')}}"></script> --}}