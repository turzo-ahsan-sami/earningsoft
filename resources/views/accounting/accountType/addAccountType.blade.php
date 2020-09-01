@extends('layouts/acc_layout')
@section('title', '| New Account Type')
@section('content')

<div class="row add-data-form"  style="padding-bottom: 1%">
    <div class="col-md-12">
    		<div class="col-md-2"></div>
    			<div class="col-md-8 fullbody">
    				<div class="viewTitle" style="border-bottom: 1px solid white;">
            			<a href="{{url('viewAccountType/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
            			</i>Account Type List</a>
        			</div>
        		<div class="panel panel-default panel-border">
    				<div class="panel-heading">
        				<div class="panel-title">Add Account Type</div>
    				</div>
                	<div class="panel-body">
                		<div class="row">
                			<div class="col-md-12">
                				<div class="col-md-8">
					                {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
					                    <div class="form-group">
					                        {!! Form::label('name', 'Name:', ['class' => 'col-sm-3 control-label']) !!}
					                        <div class="col-sm-9">
					                            {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Account Name']) !!}
					                            <p id='namee' style="max-height:3px;"></p>
					                        </div>
					                    </div>
                                        <div class="form-group">
                                            {!! Form::label('parentId', 'Parent:', ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-9">

                                                <select class ="form-control" id = "parentId" name="parentId">
                                                    <option value="">Select one</option>
                                                    <option value="0">Grand Parent</option>
                                                    @foreach($accountTypes as $accountType)
                                                        <option value="{{$accountType->id}}">{{$accountType->name}}</option>
                                                    @endforeach
                                                </select>
                                                <p id='parentIde' style="max-height:3px;"></p>
                                            </div>
                                        </div>
                                        

					                    <div class="form-group">
					                        {!! Form::label('description', 'Description:', ['class' => 'col-sm-3 control-label']) !!}
					                        <div class="col-sm-9">                    
					                            {!! Form::textarea('description', $value = null, ['class' => 'form-control textarea', 'id' => 'description', 'rows' => 5, 'placeholder' => 'Enter description', 'type' => 'textarea']) !!}  
					                            <p id='descriptione' style="max-height:3px;"></p>
					                        </div>
					                    </div>
					                    <div class="form-group">
					                        {!! Form::label('isParent', 'Is Parent:', ['class' => 'col-sm-3 control-label']) !!}
					                        <div class="col-sm-9">
                                                {{Form::checkbox('isParent', 1)}}
					                            <p id='isParente' style="max-height:3px;"></p>
					                        </div>
					                    </div>
					                    <div class="form-group">
					                        {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
					                        <div class="col-sm-9 text-right">
					                            {!! Form::submit('submit', ['id' => 'add', 'class' => 'btn btn-info']) !!}
                                                {{ Form::reset('Reset', ['class' => 'btn btn-warning']) }}
                                                <a href="{{url('viewAccountType/')}}" class="btn btn-danger closeBtn">Close</a>
					                            <span id="success" style="color:green; font-size:16px;" class="pull-right"></span>
					                        </div>
					                    </div>
					                {!! Form::close()  !!}
                    			 </div>
		                    	<div class="col-md-4 emptySpace vert-offset-top-4"><img src="images/image15.png" width="90%" height="" style="float:right"></div>
                    		</div>
                    	</div>
                  	</div>
             </div>
             <div class="footerTitle" style="border-top:1px solid white"></div>
    	</div>
    		<div class="col-md-2"></div>
    </div>
</div>
@endsection 

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>

<script type="text/javascript">
$(document).ready(function(){
   
$('form').submit(function( event ) {
    event.preventDefault();
    $.ajax({
         type: 'post',
         url: './addAccountTypeItem',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( _response ){
            if (_response.accessDenied) {
                showAccessDeniedMessage();
                return false;
            }
            // alert(JSON.stringify(_response));
    if (_response.errors) {
            if (_response.errors['name']) {
                $('#namee').empty();
                $('#namee').append('<span style="color:red;">'+_response.errors.name+'</span>');
                return false;
            }
            if (_response.errors['parentId']) {
                $('#parentIde').empty();
                $('#parentIde').show();
                $('#parentIde').append('<span style="color:red;">'+_response.errors.parentId+'</span>');
                return false;
            }

    } else {
            
            window.location.href = '{{url('viewAccountType/')}}';
            }
        },
        error: function( _response ){
            // Handle error
            alert(_response.errors);
        }
    });
});

$("input").keyup(function(){
            var name = $("#name").val();
            if(name){$('#namee').hide();}else{$('#namee').show();}
             var isParent = $("#isParent").val();
            if(isParent){$('#isParente').hide();}else{$('#isParente').show();}
});

$('select').on('change', function (e) {

             var parentId = $("#parentId").val();
            if(parentId){$('#parentIde').hide();}else{$('#parentIde').show();}

});



$("textarea").keyup(function(){
             var description = $("#description").val();
            if(description){$('#descriptione').hide();}else{$('#descriptione').show();}
});

});
</script> 
 
