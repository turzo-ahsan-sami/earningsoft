<style media="screen">

	.loan-table tr th{
		padding: 7px !important;
	}
	.loan-table tr th[rowspan = '2']{
		padding-top: 17px !important;
	}
	.loan-table tr td{
		text-align: center;
		color: #222;
		padding-top: 8px !important;
		padding-bottom: 8px !important;
		font-size: 1.1em;
	}
	.loan-table .amount{
		text-align: right !important;
		padding-right: 80px !important;
	}
	.table{
		margin-top: 15px;
	}
</style>

<div class="col-md-12">
    <table class="table table-bordered loan-table">
    	<thead>
    		<tr>
				<th class="text-center">Advance Type</th>
    			<th class="text-center">Payment</th>
    			<th class="text-center">Receive</th>
    			<th class="text-center">Balance</th>
    			<th class="text-center">Action</th>
    		</tr>
    	</thead>
    	<tbody>
			@php
			$totalPayment = 0;
			$totalReceive = 0;
			$totalBalance = 0;
			@endphp

			@foreach ($data as $key => $item)
				<tr @if ($item['balance'] == 0) style="display: none;" @endif>
	    			<td class="text-left" style="padding-left: 20px;">
						<span>{{ $item['type'] }}</span>
					</td>
					<td class="amount">{{ number_format($item['payment']) }}</td>
					<td class="amount">{{ number_format($item['receive']) }}</td>
					<td class="amount">{{ number_format($item['balance']) }}</td>
					<td class="text-center">
						<a href="{!! url('hrm/home/advanceDetails/'.$item['empId'].'/'.$item['advId']) !!}" class="btn btn-xs btn-warning">
							<i class="fa fa-eye"></i>
						</a>
					</td>
	    		</tr>
				@php
					if ($item['balance'] == 0) {
						$item['payment'] = 0;
						$item['receive'] = 0;
					}
					$totalPayment += $item['payment'];
					$totalReceive += $item['receive'];
					$totalBalance += $item['balance'];
				@endphp
			@endforeach

    	</tbody>
		<thead>
    		<tr>
				<th class="text-center">Total</th>
    			<th class="amount">{{ number_format($totalPayment) }}</th>
    			<th class="amount">{{ number_format($totalReceive) }}</th>
    			<th class="amount">{{ number_format($totalBalance) }}</th>
    			<th></th>
    		</tr>
    	</thead>
    </table>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('td').each(function(){
			if ($(this).text() == '0') {
				$(this).text('-');
			}
		});

		$('th').each(function(){
			if ($(this).text() == '0') {
				$(this).text('-');
			}
		});
	});
</script>
