<?php 
	$productId = array('' => 'Please Select product') + DB::table('inv_product')->pluck('name','id')->all(); 
?>
{!! Form::select('productId', ($productId), null, array('class'=>'form-control input-sm', 'id' => 'productId')) !!}