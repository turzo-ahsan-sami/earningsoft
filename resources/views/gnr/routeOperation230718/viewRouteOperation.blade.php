@extends('layouts/gnr_layout')
@section('title', '| Route Operation')
@section('content')
@include('successMsg')

<style type="text/css">
    #searchButton{
        font-size: 12px;
        margin-top: 20px;
    }
</style>

<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addRouteOperation/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Route Operation</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">ROUTE OPERATION LIST</font></h1>
        </div>
        <div class="panel-body panelBodyView"> 

         <!-- Filtering Start-->
                {!! Form::open(array('url' => 'viewRouteOperation', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filterFormId', 'method'=>'get')) !!}
                <div class="row">
                    <div class="col-md-12">

                        <div class="col-md-1">
                            <div class="form-group">
                                <div class="col-md-12">
                                    {!! Form::label('', 'Module:', ['class' => 'control-label pull-left']) !!}
                                </div>
                                <div class="col-md-12">
                                    {!! Form::select('filModule', $moduleList, null ,['id'=>'filModule','class'=>'form-control input-sm','autocomplete'=>'off']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-1" id="branchDiv">
                            <div class="form-group">
                                <div class="col-md-12">
                                    {!! Form::label('', 'Function:', ['class' => 'control-label pull-left']) !!}
                                </div>
                                <div class="col-md-12">
                                    {!! Form::select('filFunction', $functionList, null ,['id'=>'filFunction','class'=>'form-control input-sm','autocomplete'=>'off']) !!}
                                </div>
                            </div>
                        </div>

                       
                        <div class="col-md-1">
                            <div class="form-group">
                                {!! Form::label('', '', ['class' => 'control-label col-md-12']) !!}
                                <div class="col-md-12">
                                    {!! Form::submit('Search', ['id' => 'searchButton', 'class' => 'btn btn-primary btn-xs']); !!}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {!! Form::close()  !!}
                <!-- filtering end-->


       {{--  <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            
            $("#routeOperationView").dataTable({
                "oLanguage": {
                "sEmptyTable": "No Records Available",
                "sLengthMenu": "Show _MENU_ "
                },
                "lengthMenu": [[100, -1], [100, "All"]]
            });

          });
          </script>
        </div> --}}
          <table class="table table-striped table-bordered" id="routeOperationView">
            <thead>
                  <tr>
                    <th width="30">SL#</th>
                    <th>Route Name</th>
                    <th>Module Name</th>
                    <th>Function Name</th>
                    <th>Sub Function Name</th>
                    <th>Description</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                    <?php $no=0; ?>
                    @foreach($routeOperations as $routeOperation)

                        @php
                            $moduleName = DB::table('gnr_module')->where('id',$routeOperation->moduleIdFK)->value('name');
                            $functionName = DB::table('gnr_function')->where('id',$routeOperation->functionIdFK)->value('name');
                            $subfunctionName = DB::table('gnr_sub_function')->where('id',$routeOperation->subFunctionIdFK)->value('subFunctionName');
                        @endphp

                        <td class="text-center slNo">{{++$no}}</td>
                        <td class="name">{{$routeOperation->routeName}}</td>
                        <td class="name">{{$moduleName}}</td>
                        <td class="name">{{$functionName}}</td>
                        <td class="name">{{$subfunctionName}}</td>
                        <td class="name">{{$routeOperation->description}}</td>
                        
                        
                        <td class="text-center" width="80">
                          <a href="javascript:;" class="edit-modal" funId="{{$routeOperation->id}}" funRouteName="{{$routeOperation->routeName}}" funModuleId="{{$routeOperation->moduleIdFK}}" funFunctionId="{{$routeOperation->functionIdFK}}" funSubFunctionId="{{$routeOperation->subFunctionIdFK}}" funDescription="{{$routeOperation->description}}">
                            <span class="glyphicon glyphicon-edit"></span>
                          </a>&nbsp
                          <a href="javascript:;" class="delete-modal" funId="{{$routeOperation->id}}">
                            <span class="glyphicon glyphicon-trash"></span>
                          </a>
                        </td>
                      </tr>
                    @endforeach
                </tbody>  
          </table>
        </div>
      </div>
  </div>
</div>
</div>
</div>



{{-- Edit Modal --}}
<div id="editModal" class="modal fade" style="margin-top:3%">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Update Route Operation</h4>
            </div>
            <div class="modal-body">

                <div class="row" style="padding-bottom: 20px;">

                
                {!! Form::open(['url'=>'','class'=>'form-horizontal form-groups']) !!}
                    <div class="col-md-12">                    
                    {!! Form::hidden('EMrouteOperationId',null,['id'=>'EMrouteOperationId']) !!}

                    <div class="form-group">
                        {!! Form::label('routeName', 'Route Name:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                            {!! Form::text('routeName', $value = null, ['class' => 'form-control', 'id' => 'routeName', 'type' => 'text', 'placeholder' => 'Enter Route Name']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('moduleId', 'Module:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                            @php
                                $moduleId = array('' => 'Select module') + DB::table('gnr_module')->pluck('name','id')->all(); 
                            @endphp  
                            {!! Form::select('moduleId', ($moduleId), null, array('class'=>'form-control', 'id' => 'moduleId')) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('functionId', 'Function Name:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                            @php
                                $functionId = array('' => 'Select Function') + DB::table('gnr_function')->pluck('name','id')->all(); 
                            @endphp  
                            {!! Form::select('functionId', ($functionId), null, array('class'=>'form-control', 'id' => 'functionId')) !!}
                        </div>
                    </div>
                    

                    <div class="form-group">
                        {!! Form::label('subFunctionId', 'Sub Function Name:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                            <?php 
                                $subFunctionId = array('' => 'Select Sub Function') + DB::table('gnr_sub_function')->pluck('subFunctionName','id')->all(); 
                            ?>      
                            {!! Form::select('subFunctionId', ($subFunctionId), null, array('class'=>'form-control', 'id' => 'subFunctionId')) !!}
                            <p id='subFunctionIde' style="max-height:3px;"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('description', 'Description:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                            {!! Form::textArea('description', $value = null, ['class' => 'form-control', 'id' => 'description','rows'=>'2', 'placeholder' => 'Enter Description']) !!}
                        </div>
                    </div>

                
            </div> {{-- End col-12 --}}


                {!! Form::close() !!}
            
            

                </div>{{--row--}}

                

                {{-- View ModalFooter--}}
                <div class="modal-footer">
               
                     <button id="updateButton" class="btn actionBtn glyphicon glyphicon-check btn-success" type="submit"><span> Update</span></button>
                    <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"><span id=""> Close</span></button>
                </div>


            </div> {{-- End View Modal Body--}}

        </div>
    </div>
</div>
{{-- End View Modal --}}

{{-- Delete Modal --}}
  <div id="deleteModal" class="modal fade" style="margin-top:3%">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Delete!</h4>
        </div>
        <div class="modal-body">
          <h2>Are You Confirm to Delete This Record?</h2>

          <div class="modal-footer">
            {!! Form::hidden('DMrouteOperationId',null,['id'=>'DMrouteOperationId']) !!}
            <button id="DMconfirmButton" type="button" class="btn actionBtn glyphicon glyphicon-check btn-danger"> Confirm</button>
            <button  class="btn btn-warning glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>
            
          </div>

        </div>
      </div>
    </div>
  </div>
  {{-- End Delete Modal --}}


<style type="text/css">
    table tbody tr td.name{
        text-align: left;
        padding-left: 5px;
    }
</style>




<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', '.edit-modal', function(event) {
            $("#EMrouteOperationId").val($(this).attr('funId'));
            $("#routeName").val($(this).attr('funRouteName'));
            $("#moduleId").val($(this).attr('funModuleId'));
            $("#functionId").val($(this).attr('funFunctionId'));
            $("#subFunctionId").val($(this).attr('funSubFunctionId'));
            $("#description").val($(this).attr('funDescription'));
            $("#editModal"). modal('show');
        });

        $(document).on('click', '.delete-modal', function(event) {
            $("#DMrouteOperationId").val($(this).attr('funId'));
            $("#deleteModal"). modal('show');
        });

        /*Update Data*/
        $("#updateButton").click(function(event) {
            $('.error').remove();
            $.ajax({
                url: './editRouteOperationItem',
                type: 'POST',
                dataType: 'json',
                data: $('form').serialize()
            })
            .done(function(data) {
                if (data.errors) {
                        // Print Error
                    if(data.errors) {
                        $.each(data.errors, function(name, error) {
                             $("#"+name).after("<p class='error' style='color:red;'>* "+data.errors[name]+"</p>");
                        });
                    }

                } 
                else{
                    location.reload();
                }
                
                
                console.log("success");
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
            
        });
        /*Update Data*/


        $("#DMconfirmButton").click(function(event) {
            var id =  $("#DMrouteOperationId").val();
            var csrf = "{{csrf_token()}}";

            $.ajax({
                url: './deleteRouteOperationItem',
                type: 'POST',
                dataType: 'json',
                data: {id: id, _token: csrf},
            })
            .done(function(_response) {                
                
                location.reload();               
                
                console.log("success");
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
            
        });



        /*Filter Function on change Module*/
    $("#moduleId").change(function(event) {
        var moduleId = $(this).val();
        var csrf = "{{csrf_token()}}";
        $.ajax({
            url: './gnrGetFunctionBaseOnModule',
            type: 'POST',
            dataType: 'json',
            data: {moduleId: moduleId, _token: csrf},
        })
        .done(function(functions) {

            $("#functionId").empty();
            $("#functionId").append("<option value=''>Select Function</option>");
            $.each(functions, function(index, obj) {
                 $("#functionId").append("<option value='"+obj.id+"'>"+obj.name+"</option>");
            });
            console.log("success");
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
        
    });
    /*End Filter Function on change Module*/
        
    });/*Ready*/
</script>






@include('dataTableScript')
@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
