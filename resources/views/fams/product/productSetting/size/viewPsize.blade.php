@extends('layouts/fams_layout')
@section('title', '| Size')
@section('content')

<div class="row">
<div class="col-md-3"></div>
<div class="col-md-6">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addFamsPsize/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Size</a>
          </div>
            <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">SIZE LIST</font></h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            /*$("#famsProBrandView").dataTable().yadcf([
    
            ]);*/

            $("#famsProBrandView").dataTable({
               "oLanguage": {
              "sEmptyTable": "No Records Available",
              "sLengthMenu": "Show _MENU_ "
              }
            });
            
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="famsProBrandView">
            <thead>
                <tr>
                  <th width="32">SL#</th>
                  <th>Name</th>
                  <th>Action</th>
                </tr>
                
                </thead>
                <tbody> 
                  <?php $no=0; ?>
                  @foreach($productSizes as $productSize)
                  @php                    
                    $isBelongToProduct = DB::table('fams_product')->where('sizeId',$productSize->id)->value('id');                   
                  @endphp
                    <tr class="item{{$productSize->id}}">
                      <td class="text-center slNo">{{++$no}}</td>

                      <td style="text-align: left;padding-left: 15px;"><font color="black">{{$productSize->name}}</font></td>

                      <td class="text-center" width="80">

                          <a id="#editAnchor-{{$productSize->id}}" href="javascript:;"{{--  data-toggle="modal" data-target="#edit-modal-{{$productSize->id}}" --}}>
                                                <span class="glyphicon glyphicon-edit"></span>
                                            </a>&nbsp

                           <a href="" data-toggle="modal" data-target="#delete-modal-{{$productSize->id}}" @php if($isBelongToProduct>0){echo "style=\"pointer-events: none;cursor: not-allowed;\"";} @endphp>
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


@foreach($productSizes as $prodSize)

{{-- Edit Modal --}}
    <div id="edit-modal-{{$prodSize->id}}" class="modal fade edit-modal" style="margin-top:3%">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Size</h4>
                </div>
                <div class="modal-body">

                   <div class="row" style="padding-bottom: 20px;">

                            <div class="col-md-12" style="padding-left:0px;">

                                <div class="col-md-12" style="padding-right:2%;">{{--1st col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                    <div class="form-group">
                                    {!! Form::hidden('editModalSizeId',$prodSize->id,['id'=>'editModalSizeId-'.$prodSize->id]) !!}
                                        {!! Form::label('editModalSizeName', 'Size Name:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                           {!! Form::text('editModalSizeName',$prodSize->name,['class'=>'form-control','id'=>'editModalSizeName-'.$prodSize->id,'autocomplete'=>'off']) !!}
                                           <p id="editModalSizeNamee-{{$prodSize->id}}"></p>

                                    </div>
                                        </div>


                                        </div>
                                </div>
                            </div>
                      </div>


                      <div class="modal-footer">
                        <button class="btn actionBtn glyphicon glyphicon-check btn-success edit" id="update-{{$prodSize->id}}" type="button"><span> Update</span></button>
                        <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"><span id=""> Close</span></button>
                    </div>


                </div>
            </div>
        </div>
    </div>

    {{-- End Edit Modal --}}


    {{-- Delete Modal --}}
        <div id="delete-modal-{{$prodSize->id}}" class="modal fade" style="margin-top:3%">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Delete!</h4>
                    </div>
                    <div class="modal-body">
                        <h2>Are You Confirm to Delete This Record?</h2>

                        <div class="modal-footer">
                            {!! Form::open(['url' => 'deleteFamsPsize/']) !!}
                            <input type="hidden" name="id" value={{$prodSize->id}}>
                            <button  type="submit" class="btn actionBtn glyphicon glyphicon-check btn-success"><span id=""> Confirm</button>
                            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>
                            {!! Form::close() !!}

                        </div>

                    </div>
                </div>
            </div>
        </div>
        {{-- End Delete Modal --}}




            {{-- Update data --}}
    <script type="text/javascript">
      $(document).ready(function() {
        var modalId = "{{$prodSize->id}}";

        $("#update-"+modalId).click(function() {
          /*if(hasAccess('editFamsPsize')) {

          }*/

          var id = $("#editModalSizeId-"+modalId).val();
          var name = $("#editModalSizeName-"+modalId).val();
          var csrf = "{{csrf_token()}}";



          $.ajax({
              type: 'post',
              url: './editFamsPsize',
              data: {id: id, name:name, _token: csrf},
              dataType: 'json',
              success: function( _response ){
                  if(_response.errors) {
                      if (_response.errors['name']) {

                          $("#editModalSizeNamee-"+modalId).empty();
                           $('#editModalSizeNamee-'+modalId).append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');
                           
                      }                                
                  }
                  else{
                      
                      window.location.href = "viewFamsPsize";
                  }
                  
              },
              error: function() {
                  alert("Error");
              }

          });

        });

    



      }); /*Ready*/
      {{-- End Update data --}}
      </script>

@endforeach

@include('dataTableScript')
@endsection
