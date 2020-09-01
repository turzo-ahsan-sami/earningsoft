
<input type="hidden" name="currentMonth" id="currentMonth" value="{{$currentMonth}}"> 
<div class="row">
    <div class="col-md-12">
        <table class="table table-striped table-bordered" id="monthEndTable" style="color: black;">
            <thead>
                <tr>
                    <th width="50px">Sl No</th>
                    <th>Branch Name</th>
                    <th>Month</th>
                        <th width="100px">Action</th>
                </tr>
            </thead>
            <tbody>

                @if (!$monthEndInfo->count())
                    <tr>
                        <td style="background: white !important;color: black !important;" colspan="4">No Month End Available In This Search Range</td>
                    </tr>
                @endif
                
                <?php
                    if (!empty($_GET['page'])) {
                        $pagebumber = (int)$_GET['page'] ;
                    }else{
                        $pagebumber=1;
                    }
                    $slNo= ($pagebumber-1)*50; 
                ?>

                @foreach($monthEndInfo as $monthEnd)
                    <tr>
                        <td>{{++$slNo}}</td>
                        <td>{{$branchName}}</td>
                        <td>{{Carbon\Carbon::parse($monthEnd->date)->format('F Y')}}</td>

                        <td>
                            
                            @if($activeDeleteMonthEndId==$monthEnd->id)
                                <a id="" href="javascript:;" class="delete-modal deleteMonthEnd" data-id="{{ $monthEnd->id }}" data-month="{{ Carbon\Carbon::parse($monthEnd->date)->format('F Y') }}" data-branchname="{{ $branchName }}">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </a>
                            @endif

                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
        
        <div id="paginateDiv" style="text-align:right;">
            {{ $monthEndInfo->links() }}
        </div>

    </div>
</div>


<script type="text/javascript">
$(document).ready(function() {

    var currentMonth="{{$currentMonth}}";
    var targetBranchId="{{$targetBranchId}}";
    var userBranchId="{{$userBranchId}}";

    $('#currentMonth').text(currentMonth);

    // if (userBranchId!=1) {
    //     $(".deleteMonthEnd").addClass('disabled');
    // }


    $('#loadingModal').hide();
});

</script>