<?php

// var_dump($dayEndInfomation);
// echo $targetBranchId;
// echo $userBranchId;

// echo $currentDate;


?>

<input type="hidden" name="currentDate" id="currentDate" value="{{$currentDate}}"> 

<div class="row">
    <div class="col-md-12">
        <table class="table table-striped table-bordered" id="dayEndTable" style="color: black;">
            <thead>
                <tr>
                    <th width="50px">Sl No</th>
                    <th>Branch Name</th>
                    <th>Date</th>
                    {{-- @if($userBranchId==1) --}}
                        <th width="100px">Action</th>
                    {{-- @endif --}}
                </tr>
            </thead>
            <tbody>

                @if (!$dayEndInfomation->count())
                    <tr>
                        <td style="background: white !important;color: black !important;" colspan="4">No Day End Available In This Search Range</td>
                    </tr>
                @endif
                
                <?php
                    if (!empty($_GET['page'])) {
                        $pagebumber = (int)$_GET['page'] ;
                    }else{
                        $pagebumber=1;
                    }
                    $slNo= ($pagebumber-1)*60; 
                ?>

                @foreach ($dayEndInfomation as $dayEndInfo)
                    <tr>
                        <td>{{++$slNo}}</td>
                        <td>{{$branchName}}</td>
                        <td>{{date('d-m-Y',strtotime($dayEndInfo->date))}}</td>
                        {{-- @if($userBranchId==1) --}}
                            <td>
                                @if ($dayEndInfo->id==$activeDeleteDayEndId)
                                    {{-- <a href="javascript:;" class="deleteConfirmation" data-id="{{ $dayEndInfo->id }}">
                                      <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
                                    </a> --}}

                                    <a id="" href="javascript:;" class="delete-modal deleteDateEnd" data-id="{{ $dayEndInfo->id }}" data-date="{{date('d-m-Y',strtotime($dayEndInfo->date))}}" data-branchname="{{ $branchName }}">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </a>
                                @endif                                
                            </td>
                        {{-- @endif --}}
                    </tr>
                @endforeach

                {{-- @foreach($branchInfo as $key => $branch)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td class="name">{{$branch->name}}</td>
                        <td class="amount">{{$branch->branchCode}}</td> 
                    </tr>

                @endforeach --}}

            </tbody>
        </table>
        <div id="paginateDiv" style="text-align:right;">
            {{ $dayEndInfomation->links() }}
        </div>

        {{-- {{ $dayEndInfomation->appends(['filBranch' => $targetBranchId,'filMonth' => $targetMonth,'filYear' => $targetYear])->links() }} --}}

        {{-- {{ $branchInfo->links() }} --}}
    </div>
</div>


<script type="text/javascript">
$(document).ready(function() {


    var targetBranchId="{{$targetBranchId}}";
    var userBranchId="{{$userBranchId}}";

    // if (userBranchId!=1) {
        // $(".deleteDateEnd").addClass('disabled');
    // }


    // alert($(".deleteDateEnd").html());

    var currentDate="{{$currentDate}}";
    $('#currentDate').text(currentDate);
    // alert($('#currentDate').text());



    
    $('#loadingModal').hide();
});

</script>