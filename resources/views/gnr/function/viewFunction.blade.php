@extends('layouts/gnr_layout')
@section('title', '| Function')
@section('content')
@include('successMsg')

@php
    $pageNo = isset($_GET['page']) ? $_GET['page'] : 1;
    $moduleSelected = isset($_GET['filModule']) ? $_GET['filModule'] : null;
@endphp

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
              <a href="{{url('addFunction/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Function</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">FUNCTION LIST</font></h1>
        </div>
        <div class="panel-body panelBodyView"> 

            <!-- Filtering Start-->
                {!! Form::open(array('url' => 'viewFunction', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filterFormId', 'method'=>'get')) !!}
                <div class="row">
                    <div class="col-md-12">

                        <div class="col-md-1">
                            <div class="form-group">
                                <div class="col-md-12">
                                    {!! Form::label('', 'Module:', ['class' => 'control-label pull-left']) !!}
                                </div>
                                <div class="col-md-12">
                                    {!! Form::select('filModule', $moduleList, $moduleSelected ,['id'=>'filModule','class'=>'form-control input-sm','autocomplete'=>'off']) !!}
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

                
                <!-- filtering end-->


        {{-- <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            $("#functionListTable").dataTable({
               "oLanguage": {
               "sEmptyTable": "No Records Available",
               "sLengthMenu": "Show _MENU_ "
               }
            });

          });
          </script>
        </div> --}}
          <table class="table table-striped table-bordered" id="functionListTable" style="color: black;">
            <thead>
                  <tr>
                    <th width="60">SL#</th>
                    <th>Function Name</th>
                    <th>Module Name</th>
                    <th>Description</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                    <?php $no=1; ?>
                    @foreach($functions as $function)
                        @php
                            $moduleName = DB::table('gnr_module')->where('id',$function->moduleIdFK)->value('name');
                        @endphp
                        <td class="text-center slNo">{{ ($pageNo-1)*50 + $no++ }}</td>
                        <td class="text-center">{{$function->name}}</td>
                        <td class="text-center">{{$moduleName}}</td>
                        <td class="text-center">{{$function->description}}</td>
                        <td class="text-center" width="80">
                          <a href="javascript:;" class="edit-modal" functionId="{{$function->id}}" functionName="{{$function->name}}" functionCode="{{$function->functionCode}}" moduleId="{{$function->moduleIdFK}}" functionDescription="{{$function->description}}">
                            <span class="glyphicon glyphicon-edit"></span>
                          </a>&nbsp
                          <a href="javascript:;" class="delete-modal" functionId="{{$function->id}}">
                            <span class="glyphicon glyphicon-trash"></span>
                          </a>
                        </td>
                      </tr>
                    @endforeach
                </tbody>  
            </table>
            <div style="text-align:right;">
                {{ $functions->appends(request()->input())->links() }}
            </div>
            {!! Form::close()  !!}
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
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Function</h4>
            </div>
            <div class="modal-body">

                <div class="row" style="padding-bottom: 20px;">

                
                {!! Form::open(['url'=>'','class'=>'form-horizontal form-groups']) !!}
                    <div class="col-md-12">                    
                    {!! Form::hidden('EMfunctionId',null,['id'=>'EMfunctionId']) !!}

                    <div class="form-group">
                        {!! Form::label('name', 'Function Name:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                            {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Function Name']) !!}
                            <p id='namee' style="max-height:3px;"></p>
                        </div>
                    </div>
                    @php
                        $moduleList = array(''=>'Select Module') + DB::table('gnr_module')->pluck('name','id')->toArray();
                    @endphp
                    <div class="form-group">
                        {!! Form::label('moduleId', 'Module:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                            {!! Form::select('moduleId', $moduleList, null, ['class' => 'form-control', 'id' => 'moduleId',]) !!}
                            <p id='moduleIde' style="max-height:3px;"></p>
                        </div>
                    </div>

                    {{-- Functtion Code --}}
                    <div class="form-group">
                        {!! Form::label('functionCode', 'Function Code:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                            {!! Form::text('functionCode', null, ['class' => 'form-control', 'id' => 'functionCode', 'maxlength'=>5]) !!}
                            <p id='functionCodee' style="max-height:3px;"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('description', 'Description:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                            {!! Form::textarea('description', $value = null, ['class' => 'form-control', 'id' => 'description', 'rows' => 2, 'placeholder' => 'Enter description']) !!}  
                            <p id='descriptione' style="max-height:3px;"></p>
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
            {!! Form::hidden('DMfunctionId',null,['id'=>'DMfunctionId']) !!}
            <button id="DMconfirmButton" type="button" class="btn actionBtn glyphicon glyphicon-check btn-danger"> Confirm</button>
            <button  class="btn btn-warning glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>
            
          </div>

        </div>
      </div>
    </div>
  </div>
  {{-- End Delete Modal --}}



<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', '.edit-modal', function() {

            $("#EMfunctionId").val($(this).attr('functionId'));
            $("#name").val($(this).attr('functionName'));
            $("#moduleId").val($(this).attr('moduleId'));
            $("#functionCode").val($(this).attr('functionCode'));
            $("#description").val($(this).attr('functionDescription'));
            $("#editModal").modal('show');            
        });

        $(document).on('click', '.delete-modal', function() {
            $("#DMfunctionId").val($(this).attr('functionId'));
            $("#deleteModal").modal('show');            
        });

        $("#updateButton").click(function(event) {
            $(".error").remove();
            $.ajax({
                url: './updateFunction',
                type: 'POST',
                dataType: 'json',
                data: $('form').serialize()
            })
            .done(function(_response) {
                
                if (_response.errors) {
                    // Print Error
                    if(_response.errors) {
                        $.each(_response.errors, function(name, error) {
                             $("#"+name).after("<p class='error' style='color:red;'>* "+_response.errors[name]+"</p>");
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



        $("#DMconfirmButton").click(function(event) {
            var id =  $("#DMfunctionId").val();
            var csrf = "{{csrf_token()}}";

            $.ajax({
                url: './deleteFunction',
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
    });/*Ready*/
</script>


@include('dataTableScript')
@endsection
