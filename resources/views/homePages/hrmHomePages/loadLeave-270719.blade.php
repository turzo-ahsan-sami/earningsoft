<style>
	.leave-table tr th{
		padding: 7px !important;
	}
	.leave-table tr th[rowspan = '2']{
		padding-top: 7px !important;
	}
	.leave-table tr td{
		text-align: center;
		color: #222;
		padding-top: 8px !important;
		padding-bottom: 8px !important;
		font-size: 1.1em;
	}
	.table{
		margin-top: 15px;
	}

	.table thead tr th, .table tbody tr th{
		vertical-align: middle;
	}

	.table-bordered>thead>tr>th,
	.table-bordered>tbody>tr>th,
	.table-bordered>tfoot>tr>th,
	.table-bordered>thead>tr>td,
	.table-bordered>tbody>tr>td,
	.table-bordered>tfoot>tr>td {
    	border: 1px solid #eee !important;
	}
</style>

<div class="col-md-12">
    <table class="table table-bordered leave-table">
    	<thead>
    		<tr .table-head>
				<th class="text-center" rowspan="2">Leave Type</th>
				<th class="text-center" rowspan="2">Opening Balance</th>
    			<th class="text-center" colspan="3" rowspan="1">Financial Year 2018-2019</th>
    			<th class="text-center" rowspan="2">Remaining Leave</th>
    			<th class="text-center" rowspan="2">Action</th>
    		</tr>
    		<tr .table-head>
				<th class="text-center" style="width: 200px;">Leave</th>
    			<th class="text-center">Eligible Leave</th>
    			<th class="text-center">Spent Leave</th>
    		</tr>
    	</thead>
    	<tbody>
			@php
				$totalOpLeave 			= 0;
				$totalLeave 			= 0;
				$totalEligibleLeave 	= 0;
				$totalSpentLeave 		= 0;
				$totalRemainingLeave 	= 0;
			@endphp
			{{-- @if (count($data) > 0) --}}
				@foreach ($data as $key => $value)
					<tr>
		    			<td class="text-left" style="padding-left: 20px;"><span>{{ $value['leaveType'] }}</span></td>
		    			<td class="leave-day"><span>{{ $value['totalLeave'] }}</span></td>
						<td class="leave-day"><span>{{ $value['totalLeave'] }}</span></td>
						<td class="leave-day"><span>{{ $value['totalLeave'] }}</span></td>
						<td class="leave-day"><span>{{ $value['totalSpentLeave'] }}</span></td>
						<td class="leave-day"><span>{{ $value['remaining_leave'] }}</span></td>
						<td class="text-center" style="width: 75px;">
							<a href="{!! url('hr/leave/view/') !!}" class="btn btn-xs btn-warning">
								<i class="fa fa-eye"></i>
							</a>
						</td>
		    		</tr>

					@php
						$totalOpLeave 			+= $value['totalLeave'];
						$totalLeave 			+= $value['totalLeave'];
						$totalEligibleLeave 	+= $value['totalLeave'];
						$totalSpentLeave 		+= $value['totalSpentLeave'];
						$totalRemainingLeave 	+= $value['remaining_leave'];
					@endphp
				@endforeach
			{{-- @else --}}
				{{-- <tr>
					<td colspan="4"><span>No leave information is available.</span></td>
				</tr> --}}
			{{-- @endif --}}

    	</tbody>
		<thead>
    		<tr .table-head>
				<th class="text-center">Total</th>
    			<th class="text-center">{{ $totalOpLeave }}</th>
    			<th class="text-center">{{ $totalLeave }}</th>
    			<th class="text-center">{{ $totalEligibleLeave }}</th>
    			<th class="text-center">{{ $totalSpentLeave }}</th>
    			<th class="text-center">{{ $totalRemainingLeave }}</th>
    			<th></th>
    		</tr>
    	</thead>
    </table>
</div>
