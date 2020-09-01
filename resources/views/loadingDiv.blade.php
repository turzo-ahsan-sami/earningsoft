<!-- Loadding Modal -->
  <div id="loadingModal" data-backdrop="static" data-keyboard="false" class="modal fade" style="margin-top:3%;background-color: black;opacity:0.8 !important;">
    <div class="modal-dialog" style="text-align: center; padding-top: 30%;">
        <div class="modal-body">
            <!-- <i id="loaddingLogo" class="fa fa-spinner fa-spin fa-3x fa-fw" style="font-size:100px; color: gray;"></i>             -->
            <img src="{{asset('software/images/loading/loading.svg')}}">
        </div>
    </div>
  </div>
<!-- End Loadding Modal -->


<!--

// javascript

|*******************************************
|	Show The Modal (aslo disable Esc button)
|*******************************************

<script type="text/javascript">
	//$("#loadingModal").modal('show');
	//or
	$("#loadingModal").show();

	$(document).keydown(function(e) {
	    if (e.keyCode == 27) return false;
	});
</script>


|**********************************
|	Hide The Modal
|**********************************

<script type="text/javascript">
	$("#loadingModal").hide();
</script>


 -->
