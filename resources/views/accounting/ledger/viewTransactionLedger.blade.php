@extends('layouts/acc_layout')
@section('title', '| Ledger Accounts List')
@section('content')
@include('successMsg')
<style>
    .rowno{
        background-color: #696969;
    }
    .disabled {
        pointer-events: none;
        cursor: default;
        opacity: 0.6;
    }



#viewLedgerByLedger input{
  height:25px;
  border-radius: 5px;

}
#viewLedgerByProjectName input{
  height:25px;
  border-radius: 5px;
}
#viewLedgerByBranchName input{
  height:25px;
  border-radius: 5px;
}

#viewLedgerByLedger select{
    height:30px;
    border-radius: 5px;
}

#viewLedgerByProjectName select{
    height:30px;
    border-radius: 5px;
}
#viewLedgerByBranchName select{
    height:30px;
    border-radius: 5px;
}
#projectSubmit, #branchSubmit, #ledgerSubmit {
    padding: 2px;
    margin-top: 14px;
}


</style>


<?php
    // $projectBranches=DB::table('gnr_branch')->select('name')->where('projectId',9)->get();

?>

{{-- @foreach ($projectBranches as $projectBranch)
    {{$projectBranch->name}}
@endforeach --}}

{{--Function for loop--}}

<?php
    function eachRow($child, $loopTrack) {?>

    <tr class="item{{$child->id}}">
        <td class=" rowno"></td>
        <td style="text-align: left; padding-left: 10px;">


            <?php
                switch ($loopTrack) {
                    case "0":
                        break;
                    case "1":
                        echo str_repeat('&nbsp;', 7);
                        break;
                    case "2":
                        echo str_repeat('&nbsp;', (7*2));
                        break;
                    case "3":
                        echo str_repeat('&nbsp;',  (7*3));
                        break;
                    case "4":
                        echo str_repeat('&nbsp;',  (7*4));
                        break;
                    case "5":
                        echo str_repeat('&nbsp;',  (7*5));
                        break;
                    case "6":
                        echo str_repeat('&nbsp;',  (7*6));
                        break;
                    default:
                        echo str_repeat('&nbsp;',   (7*7));
                }
            ?>

            <?php

            if($child->isGroupHead==1){
                echo '<span class="fa fa-folder-open"></span>';
            }else{
                echo '<span class="fa fa-angle-double-right"></span>';
            }
            ?>
            {{$child->name}} 

        </td>

        <td style="text-align: left; padding-left: 20px;">{{$child->code}}</td>
        <td style="text-align: left; padding-left: 20px;">
            <?php  $accountTypename = DB::table('acc_account_type')->select('name')->where('id', $child->accountTypeId)->first();  ?>
            {{$accountTypename->name}}

        </td>
        <td class="text-center" width="80">

            @if ($child->isGroupHead==0)
                <a id="editIcone" href="{{url('editLedger/'.encrypt($child->id))}}">
                <span class="glyphicon glyphicon-edit"></span>
            </a>&nbsp;
            <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$child->id}}">
                <span class="glyphicon glyphicon-trash"></span>
            </a>
            @endif
            
        </td>
    </tr>

    <?php return $child->id;}
?>


<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options"> <?php $grandParent=0; ?>
              <a href="{{url('addTransactionLedger/')}}" class="btn btn-info pull-right addViewBtn" style=""><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Ledger</a>
          </div>
          <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">LEDGER ACCOUNTS</h3>
        </div>
        {{-- <h5>kxghixd</h5> --}}

        <div class="panel-body panelBodyView">


            


        <div>
          <script type="text/javascript">
          // jQuery(document).ready(function($)
          // {
          //   $("#accledgerView").dataTable().yadcf([

          //   ]);
          // });
          </script>


        <script type="text/javascript">
            jQuery(document).ready(function($)
            {
                /*$("#famsProductTable").dataTable().yadcf([

                ]);*/
                $("#accledgerView").dataTable({
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "sDom":"lrtip",

                      "lengthMenu": [[-1], ["All"]],
                      // "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
                       "oLanguage": {
                      "sEmptyTable": "No Records Available",
                      "sLengthMenu": ""
                      // "sLengthMenu": "Show _MENU_"
                      }
                    });


                var oTable;
                oTable = $('#accledgerView').dataTable();

                 $('#filterProjectName').change( function() {
                oTable.fnFilter( $(this).find(":selected").text(),8 );
                // oTable.fnFilter( $(this).find(":selected").text(),2 );
                });

                  $('#filterBranchName').change( function() {
                oTable.fnFilter( $(this).find(":selected").text(),8 );
                // oTable.fnFilter( $(this).find(":selected").text(),2 );
                });


                  // $('#accledgerView_info').hide();
                  $('#accledgerView_paginate').hide();
                  $('.projectColumn').hide();

            });
        </script>

        </div>


          <table class="table table-striped table-bordered" id="accledgerView">
            <thead>
              <tr>
                <th width="5"></th>
                {{--<th>Client Code</th>--}}
                <th  style="text-align: left;">Name</th>
                {{-- <th width="" class="projectColumn">Project</th> --}}
                <th width="100">Code</th>
                <th width="150">Account Type</th>
                <th class=""  width="80">Actions</th>
              </tr>
              {{ csrf_field() }}
            </thead>
            <tbody>

              <?php $no=0; $loopTrack=0; ?>
              @foreach($ledgers as $ledger)
                  <?php
                      $loopTrack=0;
                      eachRow($ledger, $loopTrack);
                  ?>

                <?php
                if($ledger->isGroupHead==1){
                $children1=DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('parentId', $ledger->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children1 as $child1)
                    <?php
                        $loopTrack=1;
                        eachRow($child1, $loopTrack);
                    ?>

                <?php
                if($child1->isGroupHead==1){
                $children2=DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('parentId', $child1->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children2 as $child2)
                    <?php
                        $loopTrack=2;
                        eachRow($child2, $loopTrack);
                    ?>

                <?php
                if($child2->isGroupHead==1){
                $children3=DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('parentId', $child2->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children3 as $child3)
                    <?php
                        $loopTrack=3;
                        eachRow($child3, $loopTrack);
                    ?>

                <?php
                if($child3->isGroupHead==1){
                $children4=DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('parentId', $child3->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children4 as $child4)
                    <?php
                        $loopTrack=4;
                        eachRow($child4, $loopTrack);
                    ?>

                <?php
                if($child4->isGroupHead==1){
                $children5=DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('parentId', $child4->id)->orderBy('ordering', 'asc')->get();

                ?>
                @foreach($children5 as $child5)
                    <?php
                        $loopTrack=5;
                        eachRow($child5, $loopTrack);
                    ?>

                <?php
                if($child5->isGroupHead==1){
                $children6=DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('parentId', $child5->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children6 as $child6)
                    <?php
                        $loopTrack=6;
                        eachRow($child6, $loopTrack);
                    ?>

                           @endforeach <?php }?>         {{-- End foreach loop for Child6 --}}
                         @endforeach <?php }?>          {{-- End foreach loop for Child5 --}}
                       @endforeach <?php }?>           {{-- End foreach loop for Child4 --}}
                     @endforeach <?php }?>          {{-- End foreach loop for Child3 --}}
                  @endforeach <?php }?>         {{-- End foreach loop for Child2 --}}
                @endforeach <?php }?>       {{-- End foreach loop for Child1 --}}
              @endforeach           {{-- End foreach loop for ledger --}}

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

@endsection


<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>

<script type="text/javascript">

    $(document).ready(function(){


        $("#filterProjectName").change(function () {

            var projectId = this.value;
            // alert(projectId);

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './getBranchByProject',
                data: {projectId: projectId , _token: csrf},
                dataType: 'json',
                success: function (branchList) {

                       // alert(JSON.stringify(branchList));

                    $("#filterBranchName").empty();
                    // $("#filterBranchName").prepend('<option  value="">All Branches</option>');
                    // $("#filterBranchName").prepend('<option value="1">Head Office</option>');
                    $("#filterBranchName").prepend('<option selected="selected" value="">Select Branch</option>');

                    $.each(branchList, function( value ,index){
                        $('#filterBranchName').append("<option value='"+index+"'>"+value+"</option>");
                    });


                },
                error: function(_response){
                    alert("Error");
                }
            });

        });
    });

</script>



<script type="text/javascript">
    $( document ).ready(function() {

//delete function
        $(document).on('click', '.delete-modal', function() {
            // if(hasAccess('deleteLedgerItem')){
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
            // }
        });

        $('.modal-footer').on('click', '.delete', function() {
            $.ajax({
                type: 'post',
                url: './deleteLedgerItem',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'id': $('.id').text()
                },
                success: function(data) {
                    alert(JSON.stringify(data));
                    location.reload();
                    // $('.item' + $('.id').text()).remove();
                }
            });
        });
    });//ready function end
</script>
