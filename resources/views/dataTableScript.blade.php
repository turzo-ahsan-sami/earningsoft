<!-- Bottom Scripts -->
  {{ Html::style('js/datatables/dataTables.bootstrap.css') }}
  <!-- <script src="{{asset('software/js/bootstrap.min.js')}}"></script> -->

  <script src="{{asset('software/js/datatables/js/jquery.dataTables.min.js')}}"></script>

  <!-- Imported scripts on this page -->
  <script src="{{asset('software/js/datatables/dataTables.bootstrap.js')}}"></script>
  <script src="{{asset('software/js/datatables/yadcf/jquery.dataTables.yadcf.js')}}"></script>
  <script src="{{asset('software/js/datatables/tabletools/dataTables.tableTools.min.js')}}"></script>

  <!-- JavaScripts initializations and stuff -->
  <script>
	$( document ).ready(function() {
	$(".table thead tr th").attr("class", "");
		$('.table thead tr th').click(function(){
			$(".table thead tr th").attr("class", "");
		});
		$('.pagination').click(function(){
			$(".table thead tr th").attr("class", "");
		});

	});


  </script>
