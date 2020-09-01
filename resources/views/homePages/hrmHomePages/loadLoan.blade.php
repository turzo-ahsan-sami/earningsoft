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
				<th class="text-center" rowspan="2">Loan Name</th>
    			<th class="text-center" rowspan="2">Loan Amount</th>
    			<th class="text-center" colspan="3" rowspan="1">Paid Amount</th>
    			<th class="text-center" rowspan="2">Outstanding</th>
    			<th class="text-center" rowspan="2" >Action</th>
    		</tr>
    		<tr>
				<th class="text-center" colspan="1" rowspan="1">Principle</th>
    			<th class="text-center" colspan="1" rowspan="1">Interest</th>
    			<th class="text-center" colspan="1" rowspan="1">Total</th>
    		</tr>
    	</thead>
    	<tbody>
			@php
				$totalLoanAmount = 0;
				$totalPaidPrincipal = 0;
				$totalPaidInterest = 0;
				$totalOutstanding = 0;

				$pfloans = $data['pfloan']['curBalance'];
				$opPfloans = $data['pfloan']['opBalance'];
				$countPfRows = count($pfloans) + count($opPfloans);
			@endphp

			{{-- pf loans view --}}
			@if (count($pfloans) > 0)
				@foreach ($pfloans as $key => $loan)

					<tr @if (number_format($loan['outstanding']) == 0 or number_format($loan['principal']) == 0) style="display: none" @endif>
		    			{{-- <td class="text-left" style="padding-left: 20px;" @if ($data['pfloan']['opOutstanding'] != 0 or $data['pfloan']['opPrincipal'] != 0) rowspan="2" @endif> --}}
		    			{{-- <td class="text-left" style="padding-left: 20px;" rowspan="{{ $countPfRows }}"> --}}
		    			<td class="text-left" style="padding-left: 20px;">
							<span>Provident Fund Loan</span>
						</td>
						<td class="amount">{{ number_format($loan['principal']) }}</td>
						<td class="amount">{{ number_format($loan['collectedPrincipal']) }}</td>
						<td class="amount">{{ number_format($loan['collectedInterest']) }}</td>
						<td class="amount">{{ number_format($loan['collectedPrincipal'] + $loan['collectedInterest']) }}</td>
						<td class="amount">{{ number_format($loan['outstanding']) }}</td>
						<td class="text-center">
							<a href="{!! url('hr/pfloan/view/'.$loan['loanId']) !!}" class="btn btn-xs btn-warning">
								<i class="fa fa-eye"></i>
							</a>
						</td>
		    		</tr>

					@php
					$totalLoanAmount += $loan['principal'];
					$totalPaidPrincipal += $loan['collectedPrincipal'];
					$totalPaidInterest += $loan['collectedInterest'];
					$totalOutstanding += $loan['outstanding'];
					@endphp

				@endforeach

			@endif

			@if (count($opPfloans) > 0)
				@foreach ($opPfloans as $key => $loan)

					<tr @if (number_format($loan['opOutstanding']) == 0 or number_format($loan['opPrincipal']) == 0) style="display: none" @endif>
						<td class="text-left" style="padding-left: 20px;">
							<span>Provident Fund Loan</span>
						</td>
						<td class="amount">{{ number_format($loan['opPrincipal']) }}</td>
						<td class="amount">{{ number_format($loan['opCollectedPrincipal']) }}</td>
						<td class="amount">{{ number_format($loan['opCollectedInterest']) }}</td>
						<td class="amount">{{ number_format($loan['opCollectedPrincipal'] + $loan['opCollectedInterest']) }}</td>
						<td class="amount">{{ number_format($loan['opOutstanding']) }}</td>
						<td class="text-center">
							<a href="{!! url('hr/openingBalanceLoanPf/view/'.$loan['opLoanId']) !!}" class="btn btn-xs btn-warning">
								<i class="fa fa-eye"></i>
							</a>
						</td>
					</tr>

					@php
					$totalLoanAmount += $loan['opPrincipal'];
					$totalPaidPrincipal += $loan['opCollectedPrincipal'];
					$totalPaidInterest += $loan['opCollectedInterest'];
					$totalOutstanding += $loan['opOutstanding'];
					@endphp

				@endforeach
			@endif

			{{-- pf loans view end --}}

			{{-- motorcyle loan view --}}
			@php
				$motorCycleLoans = $data['motorcyleloan']['curBalance'];
				$opMotorCycleloans = $data['motorcyleloan']['opBalance'];
				$countMotorCycleRows = count($motorCycleLoans) + count($opMotorCycleloans);
			@endphp

			@if (count($motorCycleLoans) > 0)
				@foreach ($motorCycleLoans as $key => $loan)

					<tr @if (number_format($loan['outstanding']) == 0 or number_format($loan['principal']) == 0) style="display: none" @endif>
		    			{{-- <td class="text-left" style="padding-left: 20px;" @if ($data['pfloan']['opOutstanding'] != 0 or $data['pfloan']['opPrincipal'] != 0) rowspan="2" @endif> --}}
		    			<td class="text-left" style="padding-left: 20px;">
							<span>Motor Cycle Loan</span>
						</td>
						<td class="amount">{{ number_format($loan['principal']) }}</td>
						<td class="amount">{{ number_format($loan['collectedPrincipal']) }}</td>
						<td class="amount">{{ number_format($loan['collectedInterest']) }}</td>
						<td class="amount">{{ number_format($loan['collectedPrincipal'] + $loan['collectedInterest']) }}</td>
						<td class="amount">{{ number_format($loan['outstanding']) }}</td>
						<td class="text-center">
							<a href="{!! url('hr/vehicleLoan/view/'.$loan['loanId']) !!}" class="btn btn-xs btn-warning">
								<i class="fa fa-eye"></i>
							</a>
						</td>
		    		</tr>

					@php
					$totalLoanAmount += $loan['principal'];
					$totalPaidPrincipal += $loan['collectedPrincipal'];
					$totalPaidInterest += $loan['collectedInterest'];
					$totalOutstanding += $loan['outstanding'];
					@endphp

				@endforeach

			@endif

			@if (count($opMotorCycleloans) > 0)
				@foreach ($opMotorCycleloans as $key => $loan)

					<tr @if (number_format($loan['opOutstanding']) == 0 or number_format($loan['opPrincipal']) == 0) style="display: none" @endif>
						<td class="text-left" style="padding-left: 20px;">
							<span>Motor Cycle Loan</span>
						</td>
						<td class="amount">{{ number_format($loan['opPrincipal']) }}</td>
						<td class="amount">{{ number_format($loan['opCollectedPrincipal']) }}</td>
						<td class="amount">{{ number_format($loan['opCollectedInterest']) }}</td>
						<td class="amount">{{ number_format($loan['opCollectedPrincipal'] + $loan['opCollectedInterest']) }}</td>
						<td class="amount">{{ number_format($loan['opOutstanding']) }}</td>
						<td class="text-center">
							<a href="{!! url('hr/openingBalanceLoanVehicle/view/'.$loan['opLoanId']) !!}" class="btn btn-xs btn-warning">
								<i class="fa fa-eye"></i>
							</a>
						</td>
					</tr>

					@php
					$totalLoanAmount += $loan['opPrincipal'];
					$totalPaidPrincipal += $loan['opCollectedPrincipal'];
					$totalPaidInterest += $loan['opCollectedInterest'];
					$totalOutstanding += $loan['opOutstanding'];
					@endphp

				@endforeach
			@endif

			{{-- motor cycle loan view end --}}

			{{-- bicyle loan view --}}

			@php
				$biCycleLoans = $data['bicyleloan']['curBalance'];
				$opBiCycleloans = $data['bicyleloan']['opBalance'];
				$countBiCycleRows = count($biCycleLoans) + count($opBiCycleloans);
			@endphp

			@if (count($biCycleLoans) > 0)
				@foreach ($biCycleLoans as $key => $loan)

					<tr @if (number_format($loan['outstanding']) == 0 or number_format($loan['principal']) == 0) style="display: none" @endif>
		    			{{-- <td class="text-left" style="padding-left: 20px;" @if ($data['pfloan']['opOutstanding'] != 0 or $data['pfloan']['opPrincipal'] != 0) rowspan="2" @endif> --}}
		    			<td class="text-left" style="padding-left: 20px;">
							<span>Motor Cycle Loan</span>
						</td>
						<td class="amount">{{ number_format($loan['principal']) }}</td>
						<td class="amount">{{ number_format($loan['collectedPrincipal']) }}</td>
						<td class="amount">{{ number_format($loan['collectedInterest']) }}</td>
						<td class="amount">{{ number_format($loan['collectedPrincipal'] + $loan['collectedInterest']) }}</td>
						<td class="amount">{{ number_format($loan['outstanding']) }}</td>
						<td class="text-center">
							<a href="{!! url('hr/vehicleLoan/view/'.$loan['loanId']) !!}" class="btn btn-xs btn-warning">
								<i class="fa fa-eye"></i>
							</a>
						</td>
		    		</tr>

					@php
					$totalLoanAmount += $loan['principal'];
					$totalPaidPrincipal += $loan['collectedPrincipal'];
					$totalPaidInterest += $loan['collectedInterest'];
					$totalOutstanding += $loan['outstanding'];
					@endphp

				@endforeach

			@endif

			@if (count($opBiCycleloans) > 0)
				@foreach ($opBiCycleloans as $key => $loan)

					<tr @if (number_format($loan['opOutstanding']) == 0 or number_format($loan['opPrincipal']) == 0) style="display: none" @endif>
						<td class="text-left" style="padding-left: 20px;">
							<span>Bi-cycle Loan</span>
						</td>
						<td class="amount">{{ number_format($loan['opPrincipal']) }}</td>
						<td class="amount">{{ number_format($loan['opCollectedPrincipal']) }}</td>
						<td class="amount">{{ number_format($loan['opCollectedInterest']) }}</td>
						<td class="amount">{{ number_format($loan['opCollectedPrincipal'] + $loan['opCollectedInterest']) }}</td>
						<td class="amount">{{ number_format($loan['opOutstanding']) }}</td>
						<td class="text-center">
							<a href="{!! url('hr/openingBalanceLoanVehicle/view/'.$loan['opLoanId']) !!}" class="btn btn-xs btn-warning">
								<i class="fa fa-eye"></i>
							</a>
						</td>
					</tr>

					@php
					$totalLoanAmount += $loan['opPrincipal'];
					$totalPaidPrincipal += $loan['opCollectedPrincipal'];
					$totalPaidInterest += $loan['opCollectedInterest'];
					$totalOutstanding += $loan['opOutstanding'];
					@endphp

				@endforeach
			@endif

			{{-- bi cycle loan view end --}}

			{{-- advance salary loan view --}}

    		<tr @if (number_format($data['advancedloan']['outstanding']) == 0 or number_format($data['advancedloan']['loanAmount']) == 0) style="display: none" @endif>
    			<td class="text-left" style="padding-left: 20px;"><span>Advanced Salary</span></td>
				<td class="amount">{{ number_format($data['advancedloan']['loanAmount']) }}</td>
				<td class="amount">{{ number_format($data['advancedloan']['collectionAmount']) }}</td>
				<td class="amount">- </td>
				<td class="amount">{{ number_format($data['advancedloan']['collectionAmount']) }}</td>
				<td class="amount">{{ number_format($data['advancedloan']['outstanding']) }}</td>

				@php
				$totalLoanAmount += $data['advancedloan']['loanAmount'];
				$totalPaidPrincipal += $data['advancedloan']['collectionAmount'];
				$totalOutstanding += $data['advancedloan']['outstanding'];
				@endphp

				@if ($data['advancedloan']['loanType'] == 'obasloan')
					<td class="text-center">
						<a target="_blank" href="{!! url('hr/openingBalanceLoanAdvancedSalary/view/'.$data['advancedloan']['loanId']) !!}" class="btn btn-xs btn-warning">
							<i class="fa fa-eye"></i>
						</a>
					</td>
				@elseif ($data['advancedloan']['loanType'] == 'asloan')
					<td class="text-center">
						<a href="{!! url('hr/advancedSalaryLoan/view/'.$data['advancedloan']['loanId']) !!}" class="btn btn-xs btn-warning">
							<i class="fa fa-eye"></i>
						</a>
					</td>
				@endif
    		</tr>

			{{-- advance salary loan view end --}}

    	</tbody>
		<thead>
			@php
				if ($totalOutstanding == 0) {
					$totalLoanAmount = 0;
					$totalPaidPrincipal = 0;
					$totalPaidInterest = 0;
				}
			@endphp
    		<tr>
				<th class="text-center">Total</th>
    			<th class="amount">{{ number_format($totalLoanAmount) }}</th>
    			<th class="amount">{{ number_format($totalPaidPrincipal) }}</th>
    			<th class="amount">{{ number_format($totalPaidInterest) }}</th>
    			<th class="amount">{{ number_format($totalPaidPrincipal + $totalPaidInterest) }}</th>
    			<th class="amount">{{ number_format($totalOutstanding) }}</th>
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
