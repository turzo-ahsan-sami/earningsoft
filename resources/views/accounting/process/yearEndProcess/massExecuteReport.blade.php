<div id="printDiv">

    <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; ">
        <span style="text-decoration: underline;  font-size:14px;">Error Report</span></br>
    </div>

    <table id="manualYearEndReportTable" class="table table-striped table-bordered" style="color:black; border-collapse: collapse;" border= "1px solid ash;">
        <thead>
            <tr style="vertical-align: top;">
                <th>Branch</th>
                <th>Project</th> {{-- Extra column for Notes --}}
                <th>Fiscal Year</th>
                <th>Total Debit</th>
                <th>Total Credit</th>
                <th>Difference</th>
            </tr>
        </thead>

        <tbody>
            @if (count($failedData) > 0)
                @foreach ($failedData as $key => $dataArr)
                    @foreach ($dataArr as $key => $data)
                        <tr>
                            <td class="text-center text-bold" colspan="6">
                                Branch: {{ DB::table('gnr_branch')->where('id', $data['branchId'])->value(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS branchName")) }}
                            </td>
                        </tr>
                        <tr>
                            <td>{{ $data['branchId'] }}</td>
                            <td>{{ $data['projectId'] }}</td>
                            <td>{{ $fiscalYearId }}</td>
                            <td>{{ $data['debit'] }}</td>
                            <td>{{ $data['credit'] }}</td>
                            <td>{{ $data['diff'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-center text-bold" colspan="6">Ledger wise details data</td>
                        </tr>
                        <tr>
                            <th colspan="2">Ledger Id</th>
                            <th colspan="2">Debit Amount</th>
                            <th colspan="2">Credit Amount</th>
                        </tr>
                        @foreach ($data['dataArr'] as $key => $item)
                            <tr>
                                <td colspan="2">{{ $item['ledgerId'] }}</td>
                                <td colspan="2">{{ $item['debitAmount'] }}</td>
                                <td colspan="2">{{ $item['creditAmount'] }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            @else
                <td colspan="6">Congratulations, no error! All year ends have been executed successfully!</td>
            @endif
        </tbody>

    </table>

</div>
