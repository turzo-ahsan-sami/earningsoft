
<input type="hidden" name="currentYear" id="currentYear" value="{{$currentYear}}">
<div class="row">
    <div class="col-md-12">
        <table class="table table-striped table-bordered" id="yearEndTable" style="color: black;">
            <thead>
                <tr>
                    <th width="50px">Sl No</th>
                    <th>Branch Name</th>
                    <th>Year</th>
                    <th @if ($userBranch->branchCode != 0) style="display:none;" @endif width="100px">Action</th>
                </tr>
            </thead>
            <tbody>

                @if (!$yearEndInfo->count())
                    <tr>
                        <td style="background: white !important;color: black !important;" colspan="4">No Year End Available In This Search Range</td>
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

                @foreach($yearEndInfo as $yearEnd)
                    <tr>
                        <td>{{++$slNo}}</td>
                        <td>{{$branchInfo->where('id', $yearEnd->branchIdFk)->first()->name}}</td>
                        <td>{{$fiscalYearInfo->where('id', $yearEnd->fiscalYearId)->first()->name}}</td>
                        <td @if ($userBranch->branchCode != 0) style="display:none;" @endif>
                            @if($activeDeleteYearEndId==$yearEnd->id)
                                <a id="" href="javascript:;" class="delete-modal deleteYearEnd" data-id="{{ $yearEnd->id }}" data-year="{{$fiscalYearInfo->where('id', $yearEnd->fiscalYearId)->first()->name}}" data-branchname="{{$branchInfo->where('id', $yearEnd->branchIdFk)->first()->name}}">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>

        <div id="paginateDiv" style="text-align:right;">
            {{ $yearEndInfo->links() }}
        </div>

    </div>
</div>


<script type="text/javascript">
$(document).ready(function() {

    var currentYear="{{$currentYear}}";
    var targetBranchId="{{$BranchId}}";
    var userBranchCode="{{$userBranch->branchCode}}";

    $('#currentYear').text(currentYear);

    if (userBranchCode!=0) {
        $(".deleteYearEnd").addClass('disabled');
    }


    $('#loadingModal').hide();
});

</script>
