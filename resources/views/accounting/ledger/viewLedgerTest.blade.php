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


        #ledgerSearch input{
            height:25px;
            border-radius: 5px;
        }
        #ledgerSearch select{
            height:30px;
            border-radius: 5px;
        }
        #ledgerSearch {
            padding: 2px;
            margin-top: 14px;
        }

    </style>

<?php 

    function splitProjectBranchId($projectIdArray, $branchIdArray) {
        $allLedgers = DB::table('acc_account_ledger')->select("id","projectBranchId")->get();
        $ledgerMatchedId=array();
        foreach ($allLedgers as $singleLedger) {
            $splitArray=str_replace(array('[', ']', '"', ''), '',  $singleLedger->projectBranchId);

            $splitArrayFirstValue = explode(",", $splitArray);
            $splitArraySecondValue = explode(":", $splitArrayFirstValue[0]);

            $array_length=substr_count($splitArray, ",");
            $arrayProjects=array();
            $temp=null;
            // $temp1=null;
            for($i=0; $i<$array_length+1; $i++){

                $splitArrayFirstValue = explode(",", $splitArray);

                $splitArraySecondValue = explode(":", $splitArrayFirstValue[$i]);
                $firstIndexValue=(int)$splitArraySecondValue[0];
                $secondIndexValue=(int)$splitArraySecondValue[1];

                if($firstIndexValue==0){
                    if($secondIndexValue==0){
                        array_push($ledgerMatchedId, $singleLedger->id);
                    }
                }else{

                    if(in_array($firstIndexValue,$projectIdArray)){
                        if($secondIndexValue==0){
                            array_push($ledgerMatchedId, $singleLedger->id);
                        }else{
                            if(in_array($secondIndexValue,$branchIdArray)){
                                array_push($ledgerMatchedId, $singleLedger->id);
                            }
                        }
                    }

                }
            }   //for
        }       //foreach
        $ledgerMatchedId=array_unique($ledgerMatchedId);
        return $ledgerMatchedId;

    }   //function

?>


<?php
    function eachRow($child, $loopTrack, $userBranchId) { ?>

    <tr class="item{{$child->id}}">
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
        @if ($userBranchId==1)
            <td class="text-center actionRow" width="80">
                @if($child->isGroupHead==1)
                    <a href="{{url('addLedger/'.encrypt($child->id))}}" class="" data-id="{{$child->id}}">
                        <span class="glyphicon glyphicon-plus-sign "></span>
                        {{-- <span class="glyphicon glyphicon-plus-sign "><strong>AddChild</strong></span> --}}
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
        @endif
    </tr>

    <?php return $child->id;}
?>


<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
        <div class="panel panel-default" style="background-color:#708090;">
            <div class="panel-heading" style="padding-bottom:0px">
                @if($userBranchId==1)
                    <div class="panel-options"> <?php $grandParent=0; ?>
                        <a href="{{url('addLedger/'.encrypt($grandParent))}}" class="btn btn-info pull-right addViewBtn " style=""><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Ledger</a>
                    </div>
                @endif
                <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Chart Of Accounts</h3>
            </div>
            {{-- <h5>kxghixd</h5> --}}

            <div class="panel-body panelBodyView">


                <!-- Filtering Start-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">

                            {!! Form::open(array('url' => 'testLedger/', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'ledgerSearch', 'method'=>'get')) !!}

                            <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:#212F3C">
                                    {!! Form::label('', 'Ledger:', ['class' => 'control-label col-sm-12']) !!}

                                    <?php
                                    $grandIds=DB::table('acc_account_ledger')->where('parentId', 0)->select('id', 'name')->get();
                                    ?>

                                    <div class="col-sm-12">
                                        <select class="form-control" name="ledgerId" id="ledgerId">
                                            <option value="">All Ledger</option>
                                            @foreach ($grandIds as $grandId)
                                                <option value={{$grandId->id}}>{{$grandId->name}}</option>
                                                <?php
                                                $childLedgers=DB::table('acc_account_ledger')->where('parentId', $grandId->id)->select('id', 'name')->get();
                                                ?>

                                                @foreach ($childLedgers as $childLedger)
                                                    <option value={{$childLedger->id}}><?php echo str_repeat('&nbsp;', 5); ?>{{$childLedger->name}}</option>
                                                @endforeach
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:#212F3C">
                                    {!! Form::label('', 'Project:', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12">
                                        @php
                                        $project = array('' => 'All Projects') + DB::table('gnr_project')->pluck('name','id')->all();
                                        @endphp
                                        {!! Form::select('projectId', ($project), null, array('class'=>'form-control', 'id' => 'projectId')) !!}

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group" style="font-size: 13px; color:#212F3C">
                                    {!! Form::label('', 'Branch:', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12">
                                        @php
                                        $branch = array('' => 'All Branches') + DB::table('gnr_branch')->pluck('name','id')->all();
                                        @endphp
                                        {!! Form::select('branchId', ($branch), null, array('class'=>'form-control', 'id' => 'branchId')) !!}

                                    </div>
                                </div>
                            </div>


                            {{-- <div class="col-md-1"></div> --}}

                            <div class="col-md-1">
                                <div class="form-group" style="">
                                    {!! Form::label('', '', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12" style="padding-top: 13%;">
                                        {!! Form::submit('search', ['id' => 'balanceStatementSearch', 'class' => 'btn btn-primary btn-xs', 'style'=>'font-size:15px']); !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2"></div>

                            {!! Form::close()  !!}

                            {{-- end Div of ledgerSearch --}}

                            <div class="col-md-7"></div>
                        </div>
                    </div>

                </div>
                <!-- filtering end-->

                <div id="printView">
                    <table class="table table-striped table-bordered" id="accledgerView" style="color:black;">
                        <thead>
                        <tr>
                            {{-- <th style="padding: 10px 5px;" class="actionRow" width="5"></th> --}}
                            {{--<th>Client Code</th>--}}
                            <th  style="padding: 10px 5px; text-align: left;">Name</th>
                            {{-- <th width="" class="projectColumn">Project</th> --}}
                            <th  style="padding: 10px 5px;"width="100">Code</th>
                            <th style="padding: 10px 5px;" width="150">Account Type</th>
                            @if($userBranchId==1)
                                <th style="padding: 10px 5px;" id="actionRow"  width="80" class="">Actions</th>
                            @endif
                        </tr>
                        {{ csrf_field() }}
                        </thead>
                        <tbody>

                        <?php

                        $projectIdArray = array();
                        $branchIdArray = array();

                        // echo "<br> searchedLedgerId:".$searchedLedgerId;
                        // echo "<br> searchedProjectId:".$searchedProjectId;
                        // echo "<br> searchedBranchId:".$searchedBranchId;

                        //Ledger
                        if ($searchedLedgerId==null) {
                            $ledgers =  DB::table('acc_account_ledger')->where('parentId', 0)->orderBy('ordering', 'asc')->get();
                        }
                        else{
                            $ledgers =  DB::table('acc_account_ledger')->where('id',$searchedLedgerId)->orderBy('ordering', 'asc')->get();
                        }

                        //Project
                        if ($searchedProjectId==null) {
                            $projectIdArray = DB::table('gnr_project')->pluck('id')->toArray();
                        }
                        else{
                            array_push($projectIdArray, (int) json_decode($searchedProjectId));
                        }

                        //Branch
                        if ($searchedBranchId==null) {
                            if ($searchedProjectId == null) {
                                $branchIdArray = DB::table('gnr_branch')->whereIn('projectId', $projectIdArray)->orWhere('id', 1)->pluck('id')->toArray();
                            }
                            else{
                                $branchIdArray = DB::table('gnr_branch')->whereIn('projectId', $projectIdArray)->orWhere('id', 1)->pluck('id')->toArray();
                            }
                        }
                        else{
                            array_push($branchIdArray, (int) json_decode($searchedBranchId));
                        }

                        // echo "<br/>";
                        // var_dump($projectIdArray);
                        // echo "<br/>";
                        // var_dump($branchIdArray);

                        $ledgerMatchedId=splitProjectBranchId($projectIdArray, $branchIdArray);

                        
                        ?>

                        <?php $no=0; $loopTrack=0; ?>
                        @foreach($ledgers as $ledger)
                        @if(in_array($ledger->id,$ledgerMatchedId))
                            <?php
                            $loopTrack=0;
                            eachRow($ledger, $loopTrack, $userBranchId);

                            if($ledger->isGroupHead==1){
                            $children1=DB::table('acc_account_ledger')->where('parentId', $ledger->id)->orderBy('ordering', 'asc')->get();
                            ?>

                            @foreach($children1 as $child1)
                            @if(in_array($child1->id,$ledgerMatchedId))
                                <?php
                                $loopTrack=1;
                                eachRow($child1, $loopTrack, $userBranchId);

                                if($child1->isGroupHead==1){
                                $children2=DB::table('acc_account_ledger')->where('parentId', $child1->id)->orderBy('ordering', 'asc')->get();
                                ?>

                                @foreach($children2 as $child2)
                                @if(in_array($child2->id,$ledgerMatchedId))
                                    <?php
                                    $loopTrack=2;
                                     eachRow($child2, $loopTrack, $userBranchId);

                                    if($child2->isGroupHead==1){
                                    $children3=DB::table('acc_account_ledger')->where('parentId', $child2->id)->orderBy('ordering', 'asc')->get();
                                    ?>
                                    @foreach($children3 as $child3)
                                    @if(in_array($child3->id,$ledgerMatchedId))
                                        <?php
                                        $loopTrack=3;
                                         eachRow($child3, $loopTrack, $userBranchId);

                                        if($child3->isGroupHead==1){
                                        $children4=DB::table('acc_account_ledger')->where('parentId', $child3->id)->orderBy('ordering', 'asc')->get();
                                        ?>
                                        @foreach($children4 as $child4)
                                        @if(in_array($child4->id,$ledgerMatchedId))
                                            <?php
                                            $loopTrack=4;
                                             eachRow($child4, $loopTrack, $userBranchId);

                                            if($child4->isGroupHead==1){
                                            $children5=DB::table('acc_account_ledger')->where('parentId', $child4->id)->orderBy('ordering', 'asc')->get();

                                            ?>
                                            @foreach($children5 as $child5)
                                            @if(in_array($child5->id,$ledgerMatchedId))
                                                <?php
                                                $loopTrack=5;
                                                eachRow($child5, $loopTrack, $userBranchId);
                                                ?>
                                            @endif
                                            @endforeach <?php }?>          {{-- End foreach loop for Child5 --}}
                                        @endif
                                        @endforeach <?php }?>           {{-- End foreach loop for Child4 --}}
                                    @endif
                                    @endforeach <?php }?>          {{-- End foreach loop for Child3 --}}
                                @endif
                                @endforeach <?php }?>         {{-- End foreach loop for Child2 --}}
                            @endif
                            @endforeach <?php }?>       {{-- End foreach loop for Child1 --}}
                        @endif
                        @endforeach           {{-- End foreach loop for ledger --}}


                        </tbody>
                    </table>
                </div>  {{-- div for print --}}
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




{{-- <script src="{{asset('js/jquery-1.11.1.min.js')}}"></script> --}}

<script type="text/javascript">

    $(document).ready(function(){


        $("#projectId").change(function () {

            var projectId = this.value;
            // alert(projectId);

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './getBranchByProjectTest',
                data: {projectId: projectId , _token: csrf},
                dataType: 'json',
                success: function (branchList) {

                    // alert(JSON.stringify(branchList));

                    $("#branchId").empty();
                    // $("#branchId").prepend('<option selected="selected" value="">Select Branch</option>');
                    $("#branchId").append('<option  value="">All Branches</option>');
                    $("#branchId").append('<option value="1">Head Office</option>');

                    $.each(branchList, function( index, value){
                        $('#branchId').append("<option value='"+index+"'>"+value+"</option>");
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

        var searchedLedgerId = "{{$searchedLedgerId}}";
        $('#ledgerId').val(searchedLedgerId);

        var searchedProjectId = "{{$searchedProjectId}}";
        $('#projectId').val(searchedProjectId);

        var searchedBranchId = "{{$searchedBranchId}}";
        $('#branchId').val(searchedBranchId);

//delete function
        $(document).on('click', '.delete-modal', function() {

            if(hasAccess('deleteLedgerItem')){
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
            }
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


<script type="text/javascript">
    $(function(){
        $("#printList").click(function(){

            $(".dataTables_filter, .dataTables_info").css("display", "none");
            $(".stockViewTable_length, .dataTables_paginate").css("display", "none");
            $("#stockViewTable_length").hide();


            // $("#accledgerView").find('.actionRow').css("display", "none");
            // $("#accledgerView").find('.actionRow').hide();
            $(".actionRow").hide();
            $("#actionRow").hide();

            // $(".rowno").hide();
            // $('.projectColumn').hide();

            var printContents = document.getElementById("printView").innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML ="" + printContents;
            window.print();
            document.body.innerHTML = originalContents;
        });
    });

</script>


<style type="text/css" media="print">
    @media print {
        thead {display: table-header-group;}

    }
</style>


@endsection

