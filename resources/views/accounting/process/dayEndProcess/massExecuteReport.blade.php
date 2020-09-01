<div id="printDiv">

    <div class="row" style="padding-bottom: 10px; text-align: center; vertical-align: middle; color: black; font-weight: bold; ">
        <span style="text-decoration: underline;  font-size:14px;">Execution Report</span></br>
    </div>

    <table id="manualMonthEndReportTable" class="table table-striped table-bordered" style="color:black; border-collapse: collapse;" border= "1px solid ash;">
        <thead>
            <tr style="vertical-align: top;">
                <th>Branch</th>
                <th>Day End Date</th>
                <th>Requirement</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($dataArr as $key => $data)
                @if ($data['responseTitle'] == 'Warning!')
                    <tr>
                        <td>{{ $data['branchId'] }}</td>
                        <td>{{ $data['date'] }}</td>
                        <td style="text-align: left; padding-left: 10px;">{!! $data['responseText'] !!}</td>
                    </tr>
                @elseif ($data['responseTitle'] == 'Success!')
                    <tr>
                        <td>{{ $data['branchId'] }}</td>
                        <td>{{ $data['date'] }}</td>
                        <td style="text-align: left; padding-left: 10px;">{!! $data['responseText'] !!}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>

    </table>

</div>
