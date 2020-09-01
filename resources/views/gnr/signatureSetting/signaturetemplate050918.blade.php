@php
    $bootRowSize = 12/$numOfSignators;
@endphp
<div id="sigDiv" class="row" style="padding-top: 20px;color: white;">
    @foreach ($employeeNames as $key => $employeeName)
        <div class="col-sm-{{$bootRowSize}}" style="padding: 1px;">
            <p>{{$signatureRoles[$key]}}</p>
            <p>{{$employeeName}}</p>
            <p>{{$employeeIds[$key]}}</p>
            <p>{{$positionNames[$key]}}</p>
        </div>
    @endforeach
    
</div>

<style type="text/css">
    #sigDiv p{
        color: white;
        font-size: 12px;
        line-height : 5px;
    }
</style>