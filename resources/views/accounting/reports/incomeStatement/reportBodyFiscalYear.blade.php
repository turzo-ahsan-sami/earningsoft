<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered" border="1pt solid ash" style="color:black; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Particular</th>
                        <th>Previos Year</th>
                        <th>This Year</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sectionNo = 1;
                        $levelOneLedgers = $ledgers->where('level',1);
                    @endphp
                    @foreach ($levelOneLedgers as $levelOneLedger)
                        <tr>
                            <td class="name bold">{{$levelOneLedger->name.'['.$levelOneLedger->code.']'}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        @php
                            $levelTwoLedgers = $ledgers->where('level',2)->where('parentId',$levelOneLedger->id);
                        @endphp
                        @foreach ($levelTwoLedgers as $levelTwoLedger)
                            <tr>
                                <td class="name bold">2{{str_repeat('&nbsp;', 2*2).$levelTwoLedger->name.'['.$levelTwoLedger->code.']'}}</td>
                                <td></td>
                                <td class="amount">{{number_format($cfyR->where('ledgerId',$levelTwoLedger->id)->sum('balance'),2)}}</td>
                            </tr>
                            @php
                                $levelThreeLedgers = $ledgers->where('level',3)->where('parentId',$levelTwoLedger->id);
                            @endphp
                            @foreach ($levelThreeLedgers as $levelThreeLedger)
                                <tr>
                                    <td class="name bold">3{{str_repeat('&nbsp;', 3*2).$levelThreeLedger->name.'['.$levelThreeLedger->code.']'}}</td>
                                    <td></td>
                                    <td class="amount">{{number_format($cfyR->where('ledgerId',$levelThreeLedger->id)->sum('balance'),2)}}</td>
                                </tr>
                                @php
                                    $levelFourLedgers = $ledgers->where('level',4)->where('parentId',$levelThreeLedger->id);
                                @endphp
                                @foreach ($levelFourLedgers as $levelFourLedger)
                                    <tr>
                                        <td class="name bold">4{{str_repeat('&nbsp;', 4*2).$levelFourLedger->name.'['.$levelFourLedger->code.']'}}</td>
                                        <td></td>
                                        <td class="amount">{{number_format($cfyR->where('ledgerId',$levelFourLedger->id)->sum('balance'),2)}}</td>
                                    </tr>
                                    @php
                                        $levelFiveLedgers = $ledgers->where('level',5)->where('parentId',$levelFourLedger->id);
                                    @endphp
                                    @foreach ($levelFiveLedgers as $levelFiveLedger)
                                        <tr>
                                            <td class="name">5{{str_repeat('&nbsp;', 5*2).$levelFiveLedger->name.'['.$levelFiveLedger->code.']'}}</td>
                                            <td></td>
                                            <td class="amount">{{number_format($cfyR->where('ledgerId',$levelFiveLedger->id)->sum('balance'),2)}}</td>
                                        </tr>
                                        
                                    @endforeach{{-- level 5 --}}
                                    
                                @endforeach{{-- level 4 --}}

                            @endforeach{{-- level 3 --}}
                        @endforeach{{-- level 2 --}}
                        <tr>
                            @if ($sectionNo==1)
                                <td class="name bold">TOTAL INCOME</td>
                            @elseif($sectionNo==2)
                                <td class="name bold">TOTAL EXPENSE</td>
                            @endif
                            <td></td>
                            <td class="amount">{{number_format($cfyR->where('ledgerId',$levelOneLedger->id)->sum('balance'),2)}}</td>
                            
                        </tr>
                        @php
                            $sectionNo++;
                        @endphp
                    @endforeach{{-- level 1 --}}
                </tbody>
            </table>
        </div>
    </div> 
</div>

<style type="text/css">
    td.bold{
        font-weight: bold;
    }
</style>