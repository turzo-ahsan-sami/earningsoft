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
		<thead class=" animated fadeInUp">
			<tr>
				@foreach ($tableHeaderItemsArray as $key => $item)
					<th style="text-align:center; width:{{ $item[1] }};">{{ $item[0] }}</th>
				@endforeach
			</tr>
		</thead>
		@php
		$Count = 1;
		$sumPreviousCash  = 0;
		$sumPreviousBank  = 0;
		$sumCurrentCash   = 0;
		$sumCurrentBank   = 0;
		$sumTotal  		  = 0;
		@endphp
		<tbody>
			@if (count($branchInfoArr) > 0)
				@foreach ($branchInfoArr as $key => $info)
					<tr>
						<td class="slNo">{{ $Count }}</td>
						<td style="text-align: left;">[{{ str_pad($info['branchCode'], 3, '0', STR_PAD_LEFT) }}] {{ $info['branchName'] }}</td>
						<td>{{ date('d-m-Y', strtotime($info['branchOpeningDate'])) }}</td>
						<td>{{ date('d-m-Y', strtotime($info['branchDate'])) }}</td>
						<td class="amount">{{ number_format($info['previousMonthCash'], 2) }}</td>
						<td class="amount">{{ number_format($info['previousMonthBank'], 2) }}</td>
						<td class="amount">{{ number_format($info['currentMonthCash'], 2) }}</td>
						<td class="amount">{{ number_format($info['currentMonthBank'], 2) }}</td>
						<td class="amount">{{ number_format($info['total'], 2) }}</td>
						<td>{{ $info['progress'] }} D</td>
						{{-- <td align="center">
							@if ($info['progress'] >= 0)
								<img src="{{ asset('images/dashboards/accDashboard/greenSquare.png') }}" border="0" style="margin-top:3px;">
							@else
								<img src="{{ asset('images/dashboards/accDashboard/redSquare.png') }}" border="0" style="margin-top:3px;">
							@endif
						</td> --}}
						<td @if ($info['today'] > $info['branchDate'])
								style="color:red"
							@else
								style="color:green"
							@endif>
							{{ $info['lag'] }} D
						</td>
					</tr>
					@php
					$Count++;
					$sumPreviousCash += $info['previousMonthCash'];
					$sumPreviousBank += $info['previousMonthBank'];
					$sumCurrentCash  += $info['currentMonthCash'];
					$sumCurrentBank  += $info['currentMonthBank'];
					$sumTotal        += $info['total'];
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
				<th colspan="4">Total</th>
				<th>{{ $sumPreviousCash }}</th>
				<th>{{ $sumPreviousBank }}</th>
				<th style="text-align: right;">{{ number_format($sumCurrentCash, 2) }}</th>
				<th style="text-align: right;">{{ number_format($sumCurrentBank, 2) }}</th>
				<th style="text-align: right;">{{ number_format($sumTotal, 2) }}</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
		</tfoot>
	</table>



</div>
