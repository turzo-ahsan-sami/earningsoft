@extends('layouts/acc_layout')
@section('title', '| Ledger Accounts List')
@section('content')
@include('successMsg')
<style>
    .rowno{
        background-color: #696969;
        width: 3px;
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
                    case "7":
                        echo str_repeat('&nbsp;',  (7*7));
                        break;
                    case "8":
                        echo str_repeat('&nbsp;',  (7*8));
                        break;                    
                    case "9":
                        echo str_repeat('&nbsp;',  (7*9));
                        break;
                    case "10":
                        echo str_repeat('&nbsp;',  (7*10));
                        break;
                    case "11":
                        echo str_repeat('&nbsp;',  (7*11));
                        break;
                    case "12":
                        echo str_repeat('&nbsp;',  (7*12));
                        break;
                    case "13":
                        echo str_repeat('&nbsp;',  (7*13));
                        break;
                    case "14":
                        echo str_repeat('&nbsp;',  (7*14));
                        break;
                    case "15":
                        echo str_repeat('&nbsp;',   (7*15));
                        break;
                    default:
                        echo str_repeat('&nbsp;',   (7*16));
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

            @if($child->isGroupHead==1)
                <a href="{{url('addLedger/'.encrypt($child->id))}}" class="" data-id="{{$child->id}}">
                    <span class="glyphicon glyphicon-plus-sign "></span>
                </a>
            @else
                <a href="{{url('addLedger/'.$child->id)}}" class="disabled">
                    <span class="glyphicon glyphicon-plus-sign"></span>
                </a>
            @endif

            &nbsp;
            <a id="editIcone" href="{{url('editLedger/'.encrypt($child->id))}}">
                <span class="glyphicon glyphicon-edit"></span>
            </a>&nbsp;
            <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$child->id}}">
                <span class="glyphicon glyphicon-trash"></span>
            </a>
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
              <a href="{{url('addLedger/'.encrypt($grandParent))}}" class="btn btn-info pull-right addViewBtn" style=""><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Ledger</a>
          </div>
          <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">LEDGER ACCOUNTS</h3>
        </div>
        {{-- <h5>kxghixd</h5> --}}
        
        <div class="panel-body panelBodyView"> 





            <!-- Filtering Start-->
            <div class="row">
                <div class="col-md-11">
                    <div class="row">
                        
                        <div class="col-md-2">
                            <div class="row">

                                {!! Form::open(array('url' => 'viewLedgerByLedger/', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'viewLedgerByLedger', 'method'=>'get')) !!}

                                <div class="col-md-8">
                                    <div class="form-group" style="font-size: 13px; color:#212F3C">
                                        {!! Form::label('', 'Ledger:', ['class' => 'control-label col-sm-12']) !!}

                                        <?php
                                            $grandIds=DB::table('acc_account_ledger')->where('companyIdFk',Auth::user()->company_id_fk)->where('parentId', 0)->select('id', 'name')->get();
                                        ?>

                                        <div class="col-sm-12">

                                            <select class="form-control" name="ledgerId" id="ledgerId" >
                                                <option value="">Select Ledger</option>
                                                @foreach ($grandIds as $grandId)
                                                    <option value={{$grandId->id}}>{{$grandId->name}}</option>
                                                    <?php
                                                    $childLedgers=DB::table('acc_account_ledger')->where('companyIdFk',Auth::user()->company_id_fk)->where('parentId', $grandId->id)->select('id', 'name')->get();
                                                    ?>
                        
                                                    @foreach ($childLedgers as $childLedger)
                                                        <option value={{$childLedger->id}}><?php echo str_repeat('&nbsp;', 5); ?>{{$childLedger->name}}</option>
                                                    @endforeach
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>
                                </div>

                                
                                {{-- <div class="col-md-1"></div> --}}


                                <div class="col-md-2">
                                    <div class="form-group" style="">
                                        {!! Form::label('', '', ['class' => 'control-label col-sm-12']) !!}
                                        <div class="col-sm-12" style="padding-top: 13%;">
                                            {!! Form::submit('search', ['id' => 'ledgerSubmit', 'class' => 'btn btn-primary btn-xs']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2"></div>

                                {!! Form::close()  !!}
                            </div>
                        </div>

                        {{-- <div class="col-md-1" style="padding: 0px; margin: 0px;"></div> --}}

                        <div class="col-md-2">
                            <div class="row">


                                {!! Form::open(array('url' => 'viewLedgerByProjectName/', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'viewLedgerByProjectName', 'method'=>'get')) !!}

                                {{-- {!! Form::open(array('url' => 'viewLedgerByLedger/', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filteringForm', 'method'=>'get')) !!} --}}


                                <div class="col-md-8">
                                    <div class="form-group" style="font-size: 13px; color:#212F3C">
                                        {!! Form::label('', 'Project:', ['class' => 'control-label col-sm-12']) !!}

                                        <div class="col-sm-12">

                                            @php
                                            $project = array('' => 'Select Project') + DB::table('gnr_project')->where('companyId',Auth::user()->company_id_fk)->pluck('name','id')->all();
                                            @endphp
                                            {!! Form::select('projectName', ($project), null, array('class'=>'form-control', 'id' => 'filterProjectName')) !!}

                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="col-md-1">
                                    <div class="form-group" style="font-size: 13px; color:#212F3C">
                                        {!! Form::label('', 'Branch:', ['class' => 'control-label col-sm-12']) !!}

                                        <div class="col-sm-12">
                                         @php
                                            $branch = array('' => 'Select') + DB::table('gnr_branch')->where('companyId',Auth::user()->company_id_fk)->pluck('name','id')->all();
                                        @endphp
                                        {!! Form::select('branchName', ($branch), null, array('class'=>'form-control', 'id' => 'filterBranchName')) !!}

                                        </div>

                                    </div>
                                </div> --}}

                                {{-- <div class="col-md-2"></div> --}}


                                <div class="col-md-2">
                                    <div class="form-group" style="">
                                        {!! Form::label('', '', ['class' => 'control-label col-sm-12']) !!}
                                        <div class="col-sm-12" style="padding-top: 13%;">
                                            {!! Form::submit('search', ['id' => 'projectSubmit', 'class' => 'btn btn-primary btn-xs']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2"></div>

                                {!! Form::close()  !!}
                            </div>
                        </div>

                        {{-- <div class="col-md-1" style="padding: 0px; margin: 0px;"></div> --}}

                        <div class="col-md-2">
                            <div class="row">


                                {!! Form::open(array('url' => 'viewLedgerByBranchName/', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'viewLedgerByBranchName', 'method'=>'get')) !!}

                                <div class="col-md-8">
                                    <div class="form-group" style="font-size: 13px; color:#212F3C">
                                        {!! Form::label('', 'Branch:', ['class' => 'control-label col-sm-12']) !!}

                                        <div class="col-sm-12">
                                         @php
                                            $branch = array('' => 'Select') + DB::table('gnr_branch')->where('companyId',Auth::user()->company_id_fk)->pluck('name','id')->all();
                                        @endphp
                                        {!! Form::select('branchName', ($branch), null, array('class'=>'form-control', 'id' => 'filterBranchName')) !!}

                                        </div>

                                    </div>
                                </div>

                                {{-- <div class="col-md-1"></div> --}}


                                <div class="col-md-2">
                                    <div class="form-group" style="">
                                        {!! Form::label('', '', ['class' => 'control-label col-sm-12']) !!}
                                        <div class="col-sm-12" style="padding-top: 13%;">
                                            {!! Form::submit('search', ['id' => 'branchSubmit', 'class' => 'btn btn-primary btn-xs']); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2"></div>

                                {!! Form::close()  !!}
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                </div>

                <div class="col-md-1">
                    {{-- <div class="form-group">
                      {!! Form::label('printList', '', ['class' => 'control-label col-sm-12 hidden']) !!}
                        <div class="col-sm-12" style="padding-top: 25%; color: black">
                          <button id="printList" style="background-color:transparent;border:none;float:left;">
                            <i class="fa fa-print fa-lg" aria-hidden="true"></i>
                          </button>
                        </div>
                    </div> --}}
                </div>              <!-- for print-->



            </div>
            <!-- filtering end-->





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
                <th class=" rowno"></th>
                {{--<th>Client Code</th>--}}
                <th  style="text-align: left;">Name</th>
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
                $children1=DB::table('acc_account_ledger')->where('parentId', $ledger->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children1 as $child1)
                    <?php
                        $loopTrack=1;
                        eachRow($child1, $loopTrack);
                    ?>

                <?php
                if($child1->isGroupHead==1){
                $children2=DB::table('acc_account_ledger')->where('parentId', $child1->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children2 as $child2)
                    <?php
                        $loopTrack=2;
                        eachRow($child2, $loopTrack);
                    ?>

                <?php
                if($child2->isGroupHead==1){
                $children3=DB::table('acc_account_ledger')->where('parentId', $child2->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children3 as $child3)
                    <?php
                        $loopTrack=3;
                        eachRow($child3, $loopTrack);
                    ?>

                <?php
                if($child3->isGroupHead==1){
                $children4=DB::table('acc_account_ledger')->where('parentId', $child3->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children4 as $child4)
                    <?php
                        $loopTrack=4;
                        eachRow($child4, $loopTrack);
                    ?>

                <?php
                if($child4->isGroupHead==1){
                $children5=DB::table('acc_account_ledger')->where('parentId', $child4->id)->orderBy('ordering', 'asc')->get();

                ?>
                @foreach($children5 as $child5)
                    <?php
                        $loopTrack=5;
                        eachRow($child5, $loopTrack);
                    ?>
                                                                                            
                <?php
                if($child5->isGroupHead==1){
                $children6=DB::table('acc_account_ledger')->where('parentId', $child5->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children6 as $child6)
                    <?php
                        $loopTrack=6;
                        eachRow($child6, $loopTrack);
                    ?>

                                                                                    
                <?php
                if($child6->isGroupHead==1){
                $children7=DB::table('acc_account_ledger')->where('parentId', $child6->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children7 as $child7)
                    <?php
                        $loopTrack=7;
                        eachRow($child7, $loopTrack);
                    ?>

                                                                                    
                <?php
                if($child7->isGroupHead==1){
                $children8=DB::table('acc_account_ledger')->where('parentId', $child7->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children8 as $child8)
                    <?php
                        $loopTrack=8;
                        eachRow($child8, $loopTrack);
                    ?>
                                                                                    
                <?php
                if($child8->isGroupHead==1){
                $children9=DB::table('acc_account_ledger')->where('parentId', $child8->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children9 as $child9)
                    <?php
                        $loopTrack=9;
                        eachRow($child9, $loopTrack);
                    ?>
                                                                                    
                <?php
                if($child9->isGroupHead==1){
                $children10=DB::table('acc_account_ledger')->where('parentId', $child9->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children10 as $child10)
                    <?php
                        $loopTrack=10;
                        eachRow($child10, $loopTrack);
                    ?>

                <?php
                if($child10->isGroupHead==1){
                $children11=DB::table('acc_account_ledger')->where('parentId', $child10->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children11 as $child11)
                    <?php
                        $loopTrack=11;
                        eachRow($child11, $loopTrack);
                    ?>

                <?php
                if($child11->isGroupHead==1){
                $children12=DB::table('acc_account_ledger')->where('parentId', $child11->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children12 as $child12)
                    <?php
                        $loopTrack=12;
                        eachRow($child12, $loopTrack);
                    ?>

                <?php
                if($child12->isGroupHead==1){
                $children13=DB::table('acc_account_ledger')->where('parentId', $child12->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children13 as $child13)
                    <?php
                        $loopTrack=13;
                        eachRow($child13, $loopTrack);
                    ?>

                <?php
                if($child13->isGroupHead==1){
                $children14=DB::table('acc_account_ledger')->where('parentId', $child13->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children14 as $child14)
                    <?php
                        $loopTrack=14;
                        eachRow($child14, $loopTrack);
                    ?>

                <?php
                if($child14->isGroupHead==1){
                $children15=DB::table('acc_account_ledger')->where('parentId', $child14->id)->orderBy('ordering', 'asc')->get();
                ?>
                @foreach($children15 as $child15)
                    <?php
                        $loopTrack=15;
                        eachRow($child15, $loopTrack);
                    ?>


                                            @endforeach <?php }?>        {{-- End foreach loop for Child15 --}}            
                                          @endforeach <?php }?>        {{-- End foreach loop for Child14 --}}            
                                        @endforeach <?php }?>        {{-- End foreach loop for Child13 --}}            
                                      @endforeach <?php }?>        {{-- End foreach loop for Child12 --}}            
                                    @endforeach <?php }?>       {{-- End foreach loop for Child11 --}}             
                                  @endforeach <?php }?>       {{-- End foreach loop for Child10 --}}             
                                @endforeach <?php }?>        {{-- End foreach loop for Child9 --}}            
                              @endforeach <?php }?>         {{-- End foreach loop for Child8 --}}                
                            @endforeach <?php }?>          {{-- End foreach loop for Child7 --}}       
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
        var ledgerIdValue = "<?php echo $ledgerId;?>";
        $('#ledgerId').val(ledgerIdValue);         

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



