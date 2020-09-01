@extends('layouts/gnr_layout')
@section('title', '| Role')
@section('content')
@include('successMsg')
<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">
              <div class="panel panel-default" style="background-color:#708090;">
                <div class="panel-heading" style="padding-bottom:0px">
                  <div class="panel-options">
                      <a href="{{url('addRole/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Role</a>
                  </div>
                  <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">ROLE LIST</font></h1>
              </div>
              <div class="panel-body panelBodyView"> 
                <div>
                  <script type="text/javascript">
                      jQuery(document).ready(function($)
                      {

                        $("#routeOperationView").dataTable({
                            "oLanguage": {
                                "sEmptyTable": "No Records Available",
                                "sLengthMenu": "Show _MENU_ "
                            }
                        });

                    });
                </script>
            </div>
            <table class="table table-striped table-bordered" id="routeOperationView">
                <thead>
                  <tr>
                    <th width="30">SL#</th>
                    <th>Role Name</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=0; ?>
                @foreach($roles as $role)

                <td class="text-center slNo">{{++$no}}</td>
                <td class="name">{{$role->name}}</td>
                <td class="name">{{$role->description}}</td>                    


                <td class="text-center" width="80">
                  <a href="javascript:;" class="edit-modal" roleId="{{$role->id}}">
                    <span class="glyphicon glyphicon-edit"></span>
                </a>&nbsp
                @if ($role->id!=1 && $role->id!=2)
                <a href="javascript:;" class="delete-modal" roleId="{{$role->id}}">
                    <span class="glyphicon glyphicon-trash"></span>
                </a>
                @endif

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
    <div class="modal-dialog modal-lg" style="width: 1050px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Update Route Operation</h4>
            </div>
            <div class="modal-body">

                <div class="row" style="padding-bottom: 20px;">


                    {!! Form::open(['url'=>'','class'=>'form-horizontal form-groups']) !!}
                    <div class="col-md-12">                    
                        {!! Form::hidden('EMroleId',null,['id'=>'EMroleId']) !!}

                        <div class="form-group">
                            {!! Form::label('name', 'Role Name:', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-4">
                                {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Role Name']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('module', 'Modules:', ['class' => 'col-sm-2 control-label']) !!}

                            <div class="col-sm-10">

                             @php
                             $modules = DB::table('gnr_module')->where('status', 1)->select('id','name','code')->get();
                             $subFunctions = DB::table('gnr_sub_function')->select('id','subfunctionName')->get();
                             @endphp


                             {{-- /////////// --}}
                             <ul id="navTabs" class="nav nav-tabs">
                                @foreach ($modules as $key => $module1)
                                <li class="@if($key==0){{"active"}}@endif">
                                    <a href="#{{$module1->code}}" data-toggle="tab" aria-expanded="@if($key==0){{"true"}}@else{{"false"}}@endif">
                                        <span class="visible-xs"><i class="fa-home"></i></span>
                                        <span class="hidden-xs">{{$module1->name}}</span>
                                    </a>
                                </li>
                                @endforeach
                            </ul>

                            <div class="tab-content">

                                @foreach ($modules as $index => $module2)
                                <div class="tab-pane @if($index==0){{"active"}}@endif" id="{{$module2->code}}">
                                    @php
                                    $functions = DB::table('gnr_function')->where('moduleIdFK',$module2->id)->select('id','name')->orderBy('name')->get();
                                    @endphp

                                    {{-- Print Functions --}}
                                    <div>

                                        @foreach($functions as $function)
                                        <div class="functionDiv">
                                            {!! Form::label($function->name,$function->name,['class'=>'textBold']) !!} &nbsp;&nbsp;&nbsp;
                                            {!! Form::checkbox('checkAll', null, false,['class'=>'checkAll']) !!}
                                            {!! Form::label($function->name,'Check/Uncheck All') !!} <br>
                                            <div class="form-group" style="padding-left: 20px;">
                                                @foreach($subFunctions as $subFunction)
                                                {!! Form::checkbox($function->name, $subFunction->id, false,['moduleId'=>$module2->id,'functionId'=>$function->id,'class'=>'checkItem']) !!} 
                                                {!! Form::label($subFunction->subfunctionName,$subFunction->subfunctionName) !!} 
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                @endforeach
                                            </div>
                                            <br>
                                        </div>
                                        @endforeach
                                    </div>
                                    {{-- End Print Functions --}}

                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div> {{-- form-group --}}
                    {{-- ///////////// --}}

                    <div class="form-group">
                        {!! Form::label('description', 'Description:', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-8">
                            {!! Form::textArea('description', $value = null, ['class' => 'form-control', 'id' => 'description','rows'=>'2', 'placeholder' => 'Enter Description']) !!}
                        </div>
                    </div>


                </div> {{-- End col-12 --}}


                {!! Form::close() !!}



            </div>{{--row--}}



            {{-- View ModalFooter--}}
            <div class="modal-footer">

             <button id="updateButton" class="btn actionBtn glyphicon glyphicon-check btn-success" type="button"><span> Update</span></button>
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
            {!! Form::hidden('DMroleId',null,['id'=>'DMroleId']) !!}
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
.textBold{
    font-weight: bold;
    font-size: 15px;
}
</style>




<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', '.edit-modal', function(event) {
            $("input:checkbox").prop('checked',false);
            var roleId = $(this).attr('roleId');
            var csrf = "{{csrf_token()}}";

            $("#EMroleId").val(roleId);

            $.ajax({
                url: './getGnrRoleInfo',
                type: 'POST',
                dataType: 'json',
                data: {roleId: roleId, _token: csrf},
            })
            .done(function(data) {

                $("#name").val(data['roleName']);
                $("#description").val(data['description']);

                $.each(data['functionality'], function(index, functionality) {
                 if (functionality.length>0) {
                    var funArray = functionality.split(':');
                    var element = $(".checkItem[moduleId="+funArray[0]+"][functionId="+funArray[1]+"][value="+funArray[2]+"]");
                    element.prop('checked',true);  
                }                 

            });

                $(".checkItem").each(function(index, el) {
                    $(el).trigger('change');
                });

                $("#editModal").modal('show');
            })
            .fail(function() {
                alert("error");
            })
            


        });

        $(document).on('click', '.delete-modal', function(event) {
            $("#DMroleId").val($(this).attr('roleId'));
            $("#deleteModal").modal('show');
        });

        /*Update Data*/
        $("#updateButton").click(function(event) {

            $(".error").remove();
            var roleId = $("#EMroleId").val();
            var moduleIds = new Array();        
            var functionIds = new Array();      
            var subFunctionIds = new Array();       
            $(".checkItem:checked").each(function(index, el) {                
                moduleIds.push($(el).attr('moduleId'));
                functionIds.push($(el).attr('functionId'));
                subFunctionIds.push($(el).val()); 
            });  


            var name = $("#name").val();
            var description = $("#description").val();
            var csrf = "{{csrf_token()}}";

            $.ajax({
                url: './editRoleItem',
                type: 'POST',
                dataType: 'json',
                data: {roleId: roleId, name: name,description: description, moduleIds: moduleIds, functionIds: functionIds, subFunctionIds: subFunctionIds, _token: csrf},
            })
            .done(function(data) {
                // Print Error
                if(data.errors) {

                    $.each(data.errors, function(name, error) {                         
                     $("#"+name).after("<p class='error' style='color:red;'>* "+data.errors[name]+"</p>");
                 });
                }
                else{
                    location.href = "viewRoleList";
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

            var roleId = $("#DMroleId").val();
            var csrf = "{{csrf_token()}}";

            $.ajax({
                url: './deleteGnrRoleItem',
                type: 'POST',
                dataType: 'json',
                data: {roleId: roleId, _token: csrf},
            })
            .done(function() {
                location.reload();
            })
            .fail(function() {
                alert("error");
            })
            
            
        });


        /*Check/Uncheck All*/
        $(document).on('click', '.checkAll', function(event) {
            if($(this).is(":checked")){             
                $(this).closest('div').find('.checkItem').prop('checked',true);
            }
            else{
                $(this).closest('div').find('.checkItem').prop('checked',false);
            }
        });
        /*End Check/Uncheck All*/

        /*If all checked then make the all check true */
        $(document).on('change', '.checkItem', function(event) {
            var numberOfSubFuntions = $(this).closest('div').find('.checkItem').length;
            var numberOfCheckedSubFuntions = $(this).closest('div').find('.checkItem:checked').length;

            if(numberOfSubFuntions == numberOfCheckedSubFuntions){              
                $(this).closest('.functionDiv').find('.checkAll').prop('checked',true);
            }
            else{               
                $(this).closest('.functionDiv').find('.checkAll').prop('checked',false);
            }
        });
        /*end If all checked then make the all check true */

        $(document).on('change', '.checkItem', function(event) {

            if ($(this).is(":checked")){

                $("#navTabs").closest('div').find('.error').remove();
            }           
        });

        
    });/*Ready*/
</script>






@include('dataTableScript')
@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
