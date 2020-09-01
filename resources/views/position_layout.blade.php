@include('hr_partials._head')
<body class="page-body">
	@include('hr_partials._pane')
	@include('hr_partials._header_attendence')
	<div class="page-container">
		<div class="main-content">
			@include('hr_partials._message')
			@yield('content')
			@include('hr_partials._footer')
		</div>
	</div>

<!-- for date picker
	<script src="{{ asset('https://code.jquery.com/ui/1.12.1/jquery-ui.js') }}"></script>-->

	<?php /* <script src="{{asset('hr_asset/js/jquery-1.11.1.min.js')}}"></script> */?>
	<script src="{{asset('hr_asset/js/bootstrap.min.js')}}"></script>
	<script src="{{asset('hr_asset/js/TweenMax.min.js')}}"></script>
	<script src="{{asset('hr_asset/js/resizeable.js')}}"></script>
	<script src="{{asset('hr_asset/js/joinable.js')}}"></script>
	<script src="{{asset('hr_asset/js/xenon-api.js')}}"></script>
	<script src="{{asset('hr_asset/js/xenon-toggles.js')}}"></script>

	<script type="text/javascript" src="{{asset('hr_asset/js/timepicker/bootstrap-timepicker.min.js')}}"></script>

	<script src="{{asset('hr_asset/js/daterangepicker/daterangepicker.js')}}"></script>
	<script src="{{asset('hr_asset/js/datepicker/bootstrap-datepicker.js')}}"></script>
	<script src="{{asset('hr_asset/js/timepicker/bootstrap-timepicker.min.js')}}"></script>

	<script src="{{asset('hr_asset/js/select2/select2.min.js')}}"></script>

	<script src="{{asset('hr_asset/js/jquery-ui/jquery-ui.min.js')}}"></script>

	<!-- Datatables -->
	<script src="{{asset('hr_asset/js/datatables-new/jquery.dataTables.min.js')}}"></script>
	<script src="{{asset('hr_asset/js/datatables-new/dataTables.colReorder.min.js')}}"></script>

	{{ Html::style('hr_asset/js/datatables-new/jquery.dataTables.min.css') }}
	{{ Html::style('hr_asset/js/datatables-new/colReorder.dataTables.min.css') }}

	<!-- Datatables -->

	<script src="{{ asset('hr_asset/js/custom.js') }}"></script>
	@yield('footerAssets')

	<!-- JavaScripts initializations and stuff -->
	<script src="{{ asset('hr_asset/js/xenon-custom.js') }}"></script>
</body>
</html>
