@extends('layouts/acc_layout')
@section('title', '| Addvance Register')
@section('content')

<div class="row">
<div class="col-md-3"></div>
<div class="col-md-6">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('createAdvRegister/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Register</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">ADVANCE REGISTRATION LIST</font></h1>
        </div>
        
        <div class="panel-body panelBodyView">       

        <div>
        <!-- 
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            /*$("#otsTable").dataTable().yadcf([
            
            ]);*/
          /*  $("#otsTable").dataTable({  
                          
                  
                   "oLanguage": {
                  "sEmptyTable": "No Records Available",
                  "sLengthMenu": "Show _MENU_ "
                  }
                });*/
        
              
               });
          
          </script> -->
        </div>
          <table class="table table-striped table-bordered" id="otsTable" style="color: black;">
                    <thead>
                      <tr>
                        <th width="30">SL#</th>
                        <th>Type Name</th>
                        <th>Action</th>
                      </tr>
                      
                    </thead>
                    <tbody>
                    
                         
                     @foreach ($accAdvRegisterType as $index => $accAdvRegisterType)
                        <tr style:"float:left;">
                        <td>{{$index+1}}</td>
                        <td>{{$accAdvRegisterType->name}}</td>
                            <td width="80">
                              <a href="javascript:;" class="edit-model" registerTypeId="{{$accAdvRegisterType->id}}" data-name="">
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
         


 <div class="row" style="padding-bottom: 20px;">

                    <div class="col-md-12">
                        <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                            <div class="form-horizontal form-groups">

                            {!! Form::hidden('fdrRowId',null,['id'=>'EMfdrRowId']) !!}

                                

                                <div class="form-group">
                                    {!! Form::label('Name', 'Name:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">                                               
                                        {!! Form::text('Name', null,['id'=>'EMaccName','class'=>'form-control','type' => 'text','autocomplete'=>'off']) !!}
                                        <p id='accNamee' class="error" style="max-height:3px;color: red;"></p>
                                    </div>
                                </div>
                               


                             

                            </div>{{--form-horizontal form-groups--}}
                        </div>{{--End 1st col-md-6--}}

                    <div class="col-md-6" style="padding-left:2%;">{{--2nd col-md-6--}}
                         
                        </div> {{--End 2nd col-md-6--}}
                  </div>

                </div>


        </div>
        <div class="modal-footer">
         <button type="button" class="btn btn-success" data-dismiss="modal"><span class="glyphicon glyphicon-edit" style="padding-right:4px;"></span>Update</button>

         <button type="button" class="btn btn-danger " data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
        </div>
      </div>
      
    </div>
  </div>
  
</div>
  </div>
  </div>
  
</div>



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
                        <div class="col-md-6" style="padding-right:2%;">{{--1st col-md-6--}}
                            <div class="form-horizontal form-groups">

                          
                               Are You Sure Delete This Data


                    </div>{{--form-horizontal form-groups--}}
                        </div>{{--End 1st col-md-6--}}

                    <div class="col-md-6" style="padding-left:2%;">{{--2nd col-md-6--}}
                         
                        </div> {{--End 2nd col-md-6--}}
                  </div>

                </div>


        </div>
        <div class="modal-footer">
         <button type="button" class="btn btn-danger"  id="delete"  data-dismiss="modal">confrime</button>

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
/*$(document).ready(function(){
    $(".edit-model").click(function(){
        $("#editModal").modal();
    });

 $(document).on('click', '.edit-modal', function() {

      var registerTypeId = $(this).attr('accountId');
      var csrf = "{{csrf_token()}}";
      
      $.ajax({
        url: './editAdvRegister',
        type: 'POST',
        dataType: 'json',
        data: {registerTypeId: registerTypeId, _token: csrf},
        success: function(data) {
          
        $("#EMfdrRowId").val(data['name']);
         
        
        },
        error: function(argument) {
          alert('response error');
        }
      });

      
    });


  $(".delete-modal").click(function(){
        $("#deleteModal").modal();
    });

 $(document).on('click', '.delete-modal', function() {

      var registerTypeId = $(this).attr('name');
      var csrf = "{{csrf_token()}}";
      
      $.ajax({
        url: './deleteAdvRegister',
        type: 'POST',
        dataType: 'json',
        data: {registerTypeId: registerTypeId, _token: csrf},
        success: function(data) {
          
        $("#EMfdrRowId").val(data['name']);
         */
        

$(document).on('click', '.delete-modal', function() {
        
        $('#delete').html($(this).data('name'));
        $('#deleteModal').modal('show');
    });


$('.modal-footer').on('click', '#delete', function() {
        $.ajax({
            type: 'post',
            url: '/deleteAdvRegister',
            data: {
                '_token': $('input[name=_token]').val(),
                'id': $('.did').text()
            },
            success: function(data) {
                $('.item' + $('.did').text()).remove();
            }
        });
    });

     /*   },
        
      });

      
    });








});*/
</script>

 



 

@include('dataTableScript')
@endsection
