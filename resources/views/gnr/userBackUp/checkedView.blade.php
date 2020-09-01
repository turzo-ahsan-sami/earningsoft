@extends('layouts/gnr_layout')
@section('title', '| Home')
@section('content')
	<div class="row add-data-form" style="height:100%">
	    <div class="col-md-12">
            <div class="col-md-1"></div>
                <div class="col-md-10 fullbody">
                    <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{url('viewGnrUserRole/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>User Role Lists</a>
                    </div>
	                <div class="panel panel-default panel-border">
                        <div class="panel-heading">
                            <div class="panel-title">Update User Rule</div>
                        </div>
	                    <div class="panel-body">
	                        <div class="row">
	                            
									{!! Form::open(array('url' => '', 'role' => 'form', 'class' => 'form-horizontal form-groups')) !!}	             
										 {!! Form::text('id', $value = null, ['class' => 'form-control hidden', 'id' => 'id', 'type' => 'text']) !!}
				                        <div class="form-group">
				                            {!! Form::label('userId', 'User Name:', ['class' => 'col-sm-2 control-label']) !!}
				                            <div class="col-sm-4">
				                            <?php 
                                    			$userName = array('' => 'Select User Name') + DB::table('users')->pluck('name','id')->all(); 
                                			?>      
                                			{!! Form::select('userId', ($userName), null, array('class'=>'form-control', 'id' => 'userId')) !!}
				                                <p id='roleName' style="max-height:3px;"></p>
				                            </div>
				                        </div>
				                        <div class="form-group">
				                            {!! Form::label('roleId', 'Role:', ['class' => 'col-sm-2 control-label']) !!}
				                            <div class="col-sm-4">
				                            	<?php 
	                                                $roleId = array('' => 'Select') + DB::table('gnr_role')->pluck('name', 'id')->all(); 
	                                            ?>  
	                                            {!! Form::select('roleId', ($roleId), null, array('class'=>'form-control', 'id' => 'roleId')) !!}
	                                            <p id='moduleName' style="max-height:3px;"></p>
				                            </div>
				                        </div>
				                        <div class="form-group">
				                            {!! Form::label('functionalityId', 'Function:', ['class' => 'col-sm-2 control-label']) !!}
			                            	<div class="col-sm-10">
				                            	<div class="form-block">
<?php 
foreach($subFunctions as $subFunction){

$id 	= $subFunction->id;
$userId = $subFunction->userId;
$roleId = $subFunction->roleId;
$array 	= $subFunction->functionalityId;

$result = array();
foreach($array as $arrays) {
$keys = key($arrays);
$currentkey = current($arrays);

if(!isset($result[$keys])) {
    $result[$keys] = array();
}
$result[$keys][] = $currentkey;
}

/*echo "<pre>";
    print_r($result);
echo "</pre>";*/

/*foreach($result as $keys => $currentkeys) {
    echo '<h2>'.$keys.'</h2>';
    echo '<ul>';
    foreach($currentkeys as $currentkey) {
        echo '<li>'.$currentkey.'</li>';
    }
    echo '</ul>';
}*/
}
?>				                            	
				                            	<?php $i=1; ?>
					                            	@foreach(App\gnr\GnrFunctionality::all() as $functionality)

						                            	{!! Form::label(Illuminate\Support\Str::lower($functionality->functionName), $functionality->functionName) !!}
						                            	<br>
						                            	<div class="col-sm-12" id="subfunctionDiv<?php echo $i; ?>">
							                            	<p>
								                            	@foreach(App\gnr\GnrSubFunctionality::all() as $subFunctionality)
										                            <div class="col-sm-3">
											                            {!! Form::checkbox("functionalityId[][$i]", $subFunctionality->id, null, array('class' => 'cbr')) !!}
											                            {!! Form::label(Illuminate\Support\Str::lower($subFunctionality->subFunctionName), $subFunctionality->subFunctionName) !!}
										                            </div>
								                            	@endforeach
							                            	</p>
						                            	</div>
						                            	<?php $i++; ?>

					                            	@endforeach
				                            	</div>
			                            	</div>
				                        </div>
				                        
				                        <div class="form-group">
                                            <div class="col-sm-10 col-sm-offset-2">
                                                {!! Form::submit('Submit', ['id' => 'addUserRole', 'class' => 'btn btn-info']); !!}
                                                <a href="{{url('viewGnrUserRole/')}}" class="btn btn-danger closeBtn">Close</a>
                                                <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                                            </div>
                                        </div>
	                            	{!! Form::close() !!}

	                            
                        	</div>
                   	 	</div>
                	</div>
                <div class="footerTitle" style="border-top:1px solid white"></div>
	        </div>
	        <div class="col-md-1"></div>
	    </div>
	</div>




@endsection;

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function() {

var id = <?php echo json_encode($id) ;?>;
var userId = <?php echo json_encode($userId) ;?>;
var roleId = <?php echo json_encode($roleId) ;?>;

$('#id').val(id);
$('#userId').val(userId);
$('#roleId').val(roleId);
var ar = <?php echo json_encode($result) ;?>;//alert(JSON.stringify(ar));
		
/*$.each(ar, function(k, v) {
    //display the key and value paira
    //alert(k);
    var kh = k; 
    if(kh!==''){ alert(kh);
    	$("#subfunctionDiv"+kh+" :checkbox[value='1']").prop('checked', true);
    }
    //alert(k + ' is ' + v);
});*/

$.each(ar, function (key, data) {
	var kh= key;
    //alert(key);
    $.each(data, function (index, data) {
    	//alert(key);
        //alert(data);
        $("#subfunctionDiv"+kh+" :checkbox[value="+data+"]").prop('checked', true);
    })
})


$('form').submit(function( event ) {
	    	//var data = $('form').serializeArray();
		    event.preventDefault();
		    //alert($('form').serialize());
		    $.ajax({
				type: 'post',
				url: './updateGnrUserRoleItem',
				data: $('form').serialize(),
				dataType: 'json',
		        success: function( _response ) {
		        	//alert(JSON.stringify(_response));
		    		window.location.href = '{{url('viewGnrUserRole/')}}';
		        },
		        error: function( _response ) {
		            // ERROR HANDLER
		            //alert(JSON.stringify('_response'));
		        }
		    });
		});

});
</script> 
