<div id="sigDiv" class="row" style="padding-top: 20px;color: white;">
    <table style="width: 100%;">
        <tbody >
            <tr>
                @foreach ($employeeNames as $key => $employeeName)
                    <td class="sigDivTd" style="text-align: center;">
                        {{$signatureRoles[$key]}} <br>
                        {{$employeeName}} <br>
                        {{$employeeIds[$key]}} <br>
                        {{$positionNames[$key]}}
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>
</div>
