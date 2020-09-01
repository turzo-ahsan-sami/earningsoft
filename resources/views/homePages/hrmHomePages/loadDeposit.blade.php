<style>
	.deposit-table tr td{
		text-align: center;
		color: #222;
		padding-top: 8px !important;
		padding-bottom: 8px !important;
		font-size: 1.1em;
	}
	.deposit-table .table-head th{
		padding-top: 15px !important;
		padding-bottom: 15px !important;
		font-size: 1.2em;
	}
	.deposit-table .amount{
		text-align: right !important;
		padding-right: 80px !important;
	}
	.table{
		margin-top: 15px;
	}
	.btn-xs{
		padding: 0 4px !important;
	}

</style>

<div class="col-md-12">
    <table class="table table-bordered deposit-table">
    	<thead>
    		<tr class="table-head">
				<th class="text-center">Fund Name</th>
    			<th class="text-center">Own</th>
    			<th class="text-center">Org.</th>
    			<th class="text-center">Interest</th>
    			<th class="text-center">Total</th>
    			<th class="text-center" style="width: 25px;">Action</th>
    		</tr>
    	</thead>
    	<tbody>
    		<tr @if ($data['pfdeposit']['total'] == 0) style="display: none;" @endif>
    			<td class="text-left" style="padding-left: 20px;"><span>Provident Fund</span></td>
    			<td class="amount">{{ number_format($data['pfdeposit']['own'], 2) }}</td>
    			<td class="amount">{{ number_format($data['pfdeposit']['org'], 2) }}</td>
    			<td class="amount">{{ number_format($data['pfdeposit']['interest'], 2) }}</td>
    			<td class="amount">{{ number_format($data['pfdeposit']['total'], 2) }}</td>
				<td class="text-center">
					<a href="{!! url('hrm/home/pfdeposit/details/'.$data['userId']) !!}" class="btn btn-xs btn-warning">
						<i class="fa fa-eye"></i>
					</a>
				</td>
    		</tr>
    		<tr @if ($data['edpsdeposit']['total'] - $data['edpsdepositWithdraw'] == 0) style="display: none;" @endif>
    			<td class="text-left" style="padding-left: 20px;"><span>Employee DPS</span></td>
    			<td class="amount">{{ number_format($data['edpsdeposit']['own'], 2) }}</td>
    			<td class="amount text-bold"> - </td>
    			<td class="amount">{{ number_format($data['edpsdeposit']['interest'], 2) }}</td>
    			<td class="amount">{{ number_format($data['edpsdeposit']['total'], 2) }}</td>
				<td class="text-center">
					<a href="{!! url('hrm/home/edpsdeposit/details/'.$data['userId']) !!}" class="btn btn-xs btn-warning">
						<i class="fa fa-eye"></i>
					</a>
				</td>
    		</tr>
    		<tr @if ($data['securitydeposit']['total'] == 0) style="display: none;" @endif>
    			<td class="text-left" style="padding-left: 20px;"><span>Security Fund</span></td>
				<td class="amount">{{ number_format($data['securitydeposit']['own'], 2) }}</td>
				<td class="amount text-bold"> - </td>
    			<td class="amount">{{ number_format($data['securitydeposit']['interest'], 2) }}</td>
    			<td class="amount">{{ number_format($data['securitydeposit']['total'], 2) }}</td>
				<td class="text-center">
					<a href="{!! url('hrm/home/securitydeposit/details/'.$data['userId']) !!}" class="btn btn-xs btn-warning">
						<i class="fa fa-eye"></i>
					</a>
				</td>
    		</tr>
    		<tr @if ($data['welfaredeposit'] == 0) style="display: none;" @endif>
    			<td class="text-left" style="padding-left: 20px;"><span>Welfare Fund</span></td>
    			<td class="amount">{{ number_format($data['welfaredeposit'], 2) }}</td>
				<td class="amount text-bold"> - </td>
    			<td class="amount text-bold"> - </td>
    			<td class="amount">{{ number_format($data['welfaredeposit'], 2) }}</td>
				<td class="text-center">
					<a href="{!! url('hrm/home/welfaredeposit/details/'.$data['userId']) !!}" class="btn btn-xs btn-warning">
						<i class="fa fa-eye"></i>
					</a>
				</td>
    		</tr>
    		<tr @if ($data['eps']['total'] == 0) style="display: none;" @endif>
    			<td class="text-left" style="padding-left: 20px;"><span>Pension Scheme</span></td>
    			<td class="amount">{{ number_format($data['eps']['own'], 2) }}</td>
				<td class="amount text-bold"> - </td>
				<td class="amount">{{ number_format($data['eps']['interest'], 2) }}</td>
    			<td class="amount">{{ number_format($data['eps']['total'], 2) }}</td>
				<td class="text-center">
					<a href="{!! url('hrm/home/eps/details/'.$data['userId']) !!}" class="btn btn-xs btn-warning">
						<i class="fa fa-eye"></i>
					</a>
				</td>
    		</tr>
			<tr @if ($data['gratuitydeposit'] == 0) style="display: none;" @endif>
    			<td class="text-left" style="padding-left: 20px;"><span>Gratuity Fund</span></td>
    			<td class="amount text-bold"> - </td>
				<td class="amount">{{ number_format($data['gratuitydeposit'], 2) }}</td>
    			<td class="amount text-bold"> - </td>
    			<td class="amount">{{ number_format($data['gratuitydeposit'], 2) }}</td>
				<td class="text-center">
					<a href="{!! url('hrm/home/gratuity/details/'.$data['userId']) !!}" class="btn btn-xs btn-warning">
						<i class="fa fa-eye"></i>
					</a></td>
    		</tr>
    	</tbody>
		<thead>
			@php
				if ($data['edpsdeposit']['total'] - $data['edpsdepositWithdraw'] == 0) {
					$totalOwnDeposit = $data['pfdeposit']['own'] + $data['securitydeposit']['own'] + $data['welfaredeposit'] + $data['eps']['own'];
					$totalOrgDeposit = $data['pfdeposit']['org'] + $data['gratuitydeposit'];
					$totalInterest = $data['pfdeposit']['interest'] + $data['securitydeposit']['interest'] + $data['eps']['interest'];
					$totalDeposit = $data['pfdeposit']['total'] + $data['securitydeposit']['total'] + $data['gratuitydeposit'] + $data['welfaredeposit'] + $data['eps']['total'];
				}
				else {
					$totalOwnDeposit = $data['pfdeposit']['own'] + $data['edpsdeposit']['own'] + $data['securitydeposit']['own'] + $data['welfaredeposit'] + $data['eps']['own'];
					$totalOrgDeposit = $data['pfdeposit']['org'] + $data['gratuitydeposit'];
					$totalInterest = $data['pfdeposit']['interest'] + $data['edpsdeposit']['interest'] + $data['securitydeposit']['interest'] + $data['eps']['interest'];
					$totalDeposit = $data['pfdeposit']['total'] + $data['edpsdeposit']['total'] + $data['securitydeposit']['total'] + $data['gratuitydeposit'] + $data['welfaredeposit'] + $data['eps']['total'];
				}

			@endphp
    		<tr>

				<th class="text-center">Total</th>
    			<th class="amount">{{ number_format($totalOwnDeposit, 2) }}</th>
    			<th class="amount">{{ number_format($totalOrgDeposit, 2) }}</th>
    			<th class="amount">{{ number_format($totalInterest, 2) }}</th>
    			<th class="amount">{{ number_format($totalDeposit, 2) }}</th>
    			<th class="amount"></th>
    		</tr>
    	</thead>
    </table>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('td').each(function(){
			if ($(this).text() == '0.00') {
				$(this).text('-');
			}
		});

		$('th').each(function(){
			if ($(this).text() == '0.00') {
				$(this).text('-');
			}
		});
	});
</script>
