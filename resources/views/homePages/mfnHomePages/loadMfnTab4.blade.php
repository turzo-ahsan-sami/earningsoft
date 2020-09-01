<style type="text/css">
table thead tr th{
	padding: 5px 3px !important;
}
table tbody tr td {
	font-size: 11px;
	padding: 3px 5px !important;
}
table tfoot tr th {
	padding: 5px 3px !important;
	text-align: center;
}
table.table-bordered.dataTable {
	border-collapse: separate !important;
}
table.table-bordered thead th{
	border-left-width: 0;
	border-top-width: 0;
}
table.table-bordered tbody td {
	border-left-width: 0;
	border-bottom-width: 0;
}
.table > thead > tr > td:after, .table > thead > tr > th:after {
	display: none !important;
}
.timeline{
	padding: 0 17px;
}
.timeline p{
	color: #000;
	font-size: 11px;
}
</style>
<div class="timeline">
	<p class="text-right">Last Update: {{ $lastUpdateFormatedTime }}</p>
</div>
<div class="col-xs-12">
	<table class="table table-striped table-bordered dataTable" style="color:black; width:100%;">
		<thead class="animated fadeInUp">
			<tr role="row">
				@foreach ($tableHeaderItemsArray as $key => $item)
					<th style="text-align:center; width:{{ $item[1] }};">{{ $item[0] }}</th>
				@endforeach
			</tr>
		</thead>
		@php
		$Count = 1;
		$sumMemberCount  = 0;
		$sumBorrowerCount  = 0;
		$sumTodayDue  = 0;
		$sumLastMonthDue  = 0;
		$sumTotalDue  = 0;
		$sumTotalOutstandning  = 0;
		@endphp
		<tbody>
			@if (count($branchInfoArr) > 0)
				@foreach ($branchInfoArr as $key => $info)
					<tr>
						<td class="slNo">{{ $Count }}</td>
						<td style="text-align: left;">[{{ str_pad($info['branchCode'], 3, '0', STR_PAD_LEFT) }}] {{ $info['branchName'] }}</td>
						<td>{{ date('d-m-Y', strtotime($info['branchOpeningDate'])) }}</td>
						<td>{{ date('d-m-Y', strtotime($info['softwareStartDate'])) }}</td>
						<td>{{ date('d-m-Y', strtotime($info['branchDate'])) }}</td>
						<td>{{ $info['memberCount'] }}</td>
						<td>{{ $info['borrowerCount'] }}</td>
						<td class="amount">{{ number_format($info['lastMonthDue'], 2) }}</td>
						<td class="amount">{{ number_format($info['todayDue'], 2) }}</td>
						<td class="amount">{{ number_format($info['totalDue'], 2) }}</td>
						<td class="amount">{{ number_format($info['totalOutstandning'], 2) }}</td>
						<td style="color:green">{{ $info['lag'] }} D</td>
					</tr>
					@php
					$Count++;
					$sumMemberCount += $info['memberCount'];
					$sumBorrowerCount += $info['borrowerCount'];
					$sumTodayDue += $info['todayDue'];
					$sumLastMonthDue += $info['lastMonthDue'];
					$sumTotalDue += $info['totalDue'];
					$sumTotalOutstandning += $info['totalOutstandning'];
					@endphp
				@endforeach

			@else
				<tr>
					<td colspan="12" class="text-bold" style="color: #696969; font-size: 1.3em;">Please wait for server update.</td>
				</tr>
			@endif

		</tbody>
		<tfoot @if(count($branchInfoArr) == 0) style="display: none;"@endif>
			<tr>
				<th colspan="5">Total</th>
				<th>{{ $sumMemberCount }}</th>
				<th>{{ $sumBorrowerCount }}</th>
				<th style="text-align: right;">{{ number_format($sumLastMonthDue, 2) }}</th>
				<th style="text-align: right;">{{ number_format($sumTodayDue, 2) }}</th>
				<th style="text-align: right;">{{ number_format($sumTotalDue, 2) }}</th>
				<th style="text-align: right;">{{ number_format($sumTotalOutstandning, 2) }}</th>
				<th>&nbsp;</th>
			</tr>
		</tfoot>
	</table>


</div>
