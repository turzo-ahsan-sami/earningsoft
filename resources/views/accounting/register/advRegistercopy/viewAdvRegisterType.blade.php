
@extends('layouts/acc_layout')
@section('title', '| Addvance Register Type')
@section('content')

<div class="row">
   <div class="col-md-1"></div>
     <div class="col-md-12">
        <div class="" style="">
           <div class="">
              <div class="panel panel-default" style="background-color:#708090;">
                  <div class="panel-heading" style="padding-bottom:0px">
                      <div class="panel-options">
                         <a href="{{url('createAdvRegister/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Register</a>

                      </div>

                      <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white"><h3>ADVANCE REGISTER TYPE LIST<h3></font></h1>
                  </div>
        
          <div class="panel-body panelBodyView">
             <table class="table table-striped table-bordered" id="otsTable" style="color: black;">
                  <thead>
                      <tr>
                          <th width="55">SL#</th>
                          <th>Type Code</th>
                          <th>Type Name</th>
                          <th>Action</th>
                          </tr>
                   </thead>
                      <tbody>
                           @foreach ($accAdvRegisterType as $index => $accAdvRegisterType)

                                 <tr style:"float:left;">
                                      <td>{{$index+1}}</td>
                                      <td>{{$accAdvRegisterType->code}}</td>
                                      <td>{{$accAdvRegisterType->name}}</td>
                                      <td width="80">
                                          <a href="javascript:;" class="edit-modal" registerTypeId="{{$accAdvRegisterType->id}}">
                                          <span class="glyphicon glyphicon-edit"></span>
                                          </a>&nbsp;
                                          <a href="javascript:;" class="delete-modal" registerTypeId="{{$accAdvRegisterType->id}}">
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
  <div class="col-md-3"></div>
</div>
{{-- Edit Modal--}}
 <div id="editModal" class="modal fade" style="margin-top:3%">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                  <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Update Register Type</h4>
              </div>

             <div class="modal-body">
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-8">
                      {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                          <input id="EMadvRegTypeId" type="hidden" name="advRegTypeId" value="">
                         <div class="form-group">
                            {!! Form::label('code', 'Code', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-1 control-label">: </div>
                            <div class="col-sm-8">                  
                               {!! Form::text('code', null,['id'=>'EMaccCode','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}                                                          <p id='AdvRegisterTypeCode' class="error" style="max-height:3px;color: red;"></p>
                            </div>
                         </div>
                         <div class="form-group">
                             {!! Form::label('Name', 'Name', ['class' => 'col-sm-3 control-label']) !!}
                             <div class="col-sm-1 control-label">: </div>
                             <div class="col-sm-8">   
                                {!! Form::text('Name', null,['id'=>'EMaccName','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}                                        <p id='AdvRegisterTypeName' class="error" style="max-height:3px;color: red;"></p>
                             </div>
                         </div>
                         <div class="form-group">
                           <div class="modal-footer">
                               <button type="button" id="updateButton" class="btn btn-success"><span class="glyphicon glyphicon-edit" style="padding-right:4px;"></span>Update</button>

                                <button type="button" class="btn btn-danger " data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
                           </div>
                         </div>
                    {!! Form::close() !!}
                            </div>
                            <div class="col-md-4 emptySpace vert-offset-top-0"><img src="images/catalog/image15.png" width="80%" height="" style="float:right">
                            </div>
                </div>
            </div>
        </div>


                  </div>
               </div>
           </div>
        </div>
    </div>
  </div>
</div>     
</div>
<div class="form-group">
{{-- Delete Modal--}}

<div id="deleteModal" class="modal fade" style="margin-top:3%">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Delete Register Type</h4>
          </div>
          <div class="modal-body">
              <div class="row" style="padding-bottom: 20px;">
                <div class="col-md-12">
                  <div class="col-md-6" style="padding-right:2%;">
                      <div class="form-horizontal form-groups">
                          <input id="DMadvRegTypeId" type="hidden" name="advRegTypeId" value="">
                               <h2>Are You Confirm to Delete This Record?</h2>
                      </div>
                  </div>
                        <div class="col-md-6" style="padding-left:2%;"></div>
                </div>
              </div>
          </div>
          <div class="modal-footer">
              <input id="DMadvReg" type="hidden" name="advRegType" value="">
              <button type="button" class="btn btn-danger"  id="DMadvRegType"  data-dismiss="modal">confirm</button>
              <button type="button" class="btn btn-warning"  data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
          </div>

                     </div>
                  </div>
              </div>
          </div>
      </div>
   </div>
</div>

{{--end delete modal--}}

<script>

$(document).ready(function(){ 
   $(document).on('click', '.delete-modal', function() {
    if(hasAccess('deleteAdvRegister')){
      $("#DMadvReg").val($(this).attr('registerTypeId'));
      $("#deleteModal").find('.modal-dialog').css('width', '60%');
      $('#deleteModal').modal('show');
    }
   });
      $("#DMadvRegType").on('click',  function(){
          var advRegType= $("#DMadvReg").val();
          var csrf = "{{csrf_token()}}";
          $.ajax({
              url: './deleteAdvRegister',
              type: 'POST',
              dataType: 'json',
              data: {id:advRegType, _token:csrf},
          })
          .done(function(data) {
              location.reload();
          })
          .fail(function(){  
            console.log("error");

          })
          .always(function() { 
            console.log("complete");
          });
 });
      $(document).on('click', '.edit-modal', function(){
        if(hasAccess('getAdvRegType')){
          var advRegTypeId= $(this).attr('registerTypeId');
          var csrf = "{{csrf_token()}}";
          $("#EMadvRegTypeId").val(advRegTypeId);
          $.ajax({
              url: './getAdvRegType',
              type: 'POST',
              dataType: 'json',
              data: {id:advRegTypeId , _token: csrf},
              success: function(data) {
                    //alert(data);
                 $("#EMaccCode").val(data['accAdvRegisterType'].code);
                 $("#EMaccName").val(data['accAdvRegisterType'].name,);

                 $("#editModal").find('.modal-dialog').css('width', '60%');
                 $("#editModal").modal('show');
              },
             error: function(argument) {
               alert('response error');
            }
      });
        }
    });
        /*End Update the data*/
  });     
       


</script>

<script type="text/javascript">
$(document).ready(function(){ 
  $("#updateButton").on('click', function() {
       var advRegTypeId = $("#EMadvRegTypeId").val();
       var code= $("#EMaccCode").val();
       var name= $("#EMaccName").val();
       var csrf = "{{csrf_token()}}";
      $.ajax({
          url: './updateAdvRegTypeInfo',
          type: 'POST',
          dataType: 'json',
          data: {id:advRegTypeId,code:code,name:name, _token: csrf},
      })
      .done(function(data) {
         if (data.errors) {
             if (data.errors['code']) {
                 $("#AdvRegisterTypeCode").empty();
                 $("#AdvRegisterTypeCode").append('* '+data.errors['code']);
             }
             if (data.errors['name']) {
                 $("#AdvRegisterTypeName").empty();
                 $("#AdvRegisterTypeName").append('* '+data.errors['name']);
             }     
         }    
         else {
            location.reload();
         }
           console.log("success");
      })
        .fail(function(){ 
           console.log("error");
        })
        .always(function(){
          console.log("complete");
       })
    });
});
</script>

@include('dataTableScript')
@endsection
