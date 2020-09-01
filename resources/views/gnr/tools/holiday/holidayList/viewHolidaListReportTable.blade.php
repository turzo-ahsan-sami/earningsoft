<script type="text/javascript">
	$('#loadingModal').hide();
</script>

<script type="text/javascript">
  $('#loadingModal').hide();
</script>

<style media="screen">
.table > thead > tr > th,
.table > tbody > tr > th,
.table > tfoot > tr > th,
.table > thead > tr > td,
.table > tbody > tr > td,
.table > tfoot > tr > td  {

  line-height:1;

}

.table thead tr th {
  position: relative;
  word-break: normal;
}

.left{
  float: left;
}
.right{
  float: right;
}
.center{
  text-align:left;
  margin:0 auto !important;
  display:inline-block
}
.left{
  text-align: left;
  margin-left: 80px !important;
  display: inline-block;
}

th, td {
  padding-top: 5px !important;
  padding-bottom: 2px !important;
}
</style>

<div class="container-fluid">
	{{-- {{$checkSubmit}} <br> --}}
	{{-- {{$date}} --}}
	@if ($error != '')
		<p style="color: red;"> **{{$error}}</p>
	@else
		<div class="row">
			{{-- <div class="table-responsive"> --}}

			<table class="table table-bordered" border="1pt solid ash" style=" text-align: center; font-family: arial; color:black; border-collapse: collapse;" width="100%">
				<thead style="font-size:11px;">

					<tr>

						<th colspan="1" rowspan="1" width="10%">
							<div align="center">SL. No.
							</div>
						</th>
						<th colspan="1" rowspan="1" width="45%">
							<div align="center">Date
							</div>
						</th>
						<th colspan="1" rowspan="1" width="45%">
							<div align="center">Holiday Name
							</div>
						</th>
					</tr>
				</thead>

				@php
					$slNo = 0;
					$check = '';
				@endphp

				<tbody>
					@foreach ($finalHolidayData as $key => $finalData)
							<tr>
									<td>
											{{++$slNo}}
									</td>
									<td>
											{{$finalData->date}}
									</td>
									<td style="text-align: left; margin-left: 20px !important;">
											@if ($finalData->description == '{"govHoliday":""}')
													Weekly
											@elseif (strpos($finalData->description, '{"govHoliday":"') !== false)
													@php
															$myJSON = json_decode($finalData->description, true);
															// $check = $myJSON['govHoliday'];
													@endphp

													{{-- @if ($check != $myJSON['govHoliday'])
															@php
																	$check = $myJSON['govHoliday'];
															@endphp
															{{$myJSON['govHoliday']}}
													@endif --}}
													{{$myJSON['govHoliday']}}
											@else
													{{$finalData->description}}
											@endif
									</td>
							</tr>
					@endforeach
				</tbody>

			</table>
		</div>
	@endif

   </div>


 </div>
