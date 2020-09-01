@extends('layouts/fams_layout')
@section('title', '| Color')
@section('content')

<div class="row">
<div class="col-md-3"></div>
<div class="col-md-6">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addFamsPcolor/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Color</a>
          </div>
            <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">COLOR LIST</font></h1>
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
                {{ csrf_field() }}
                </thead>
                <tbody> 
                  <?php $no=0; ?>
                  @foreach($productColors as $productColor)
                  @php                    
                    $isBelongToProduct = DB::table('fams_product')->where('colorId',$productColor->id)->value('id');                   
                  @endphp
                    <tr class="item{{$productColor->id}}">
                      <td class="text-center slNo">{{++$no}}</td>

                      <td style="text-align: left;padding-left: 15px;"><font color="black">{{$productColor->name}}</font></td>

                      <td class="text-center" width="80">

                          <a href="" data-toggle="modal" data-target="#edit-modal-{{$productColor->id}}" >
                                                <span class="glyphicon glyphicon-edit"></span>
                                            </a>&nbsp

                           <a href="" data-toggle="modal" data-target="#delete-modal-{{$productColor->id}}" @php if($isBelongToProduct>0){echo "style=\"pointer-events: none;cursor: not-allowed;\"";} @endphp>
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


@foreach($productColors as $prodColor)

{{-- Edit Modal --}}
    <div id="edit-modal-{{$prodColor->id}}" class="modal fade edit-modal" style="margin-top:3%">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Color</h4>
                </div>
                <div class="modal-body">

                   <div class="row" style="padding-bottom: 20px;">

                            <div class="col-md-12" style="padding-left:0px;">

                                <div class="col-md-12" style="padding-right:2%;">{{--1st col-md-6--}}
                                    <div class="form-horizontal form-groups">

                                    <div class="form-group">
                                    {!! Form::hidden('editModalColorId',$prodColor->id,['id'=>'editModalColorId-'.$prodColor->id]) !!}
                                        {!! Form::label('editModalColorName', 'Color Name:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                           {!! Form::text('editModalColorName',$prodColor->name,['class'=>'form-control','id'=>'editModalColorName-'.$prodColor->id,'autocomplete'=>'off']) !!}
                                           <p id="editModalColorNamee-{{$prodColor->id}}"></p>

                                    </div>
                                        </div>


                                        </div>
                                </div>
                            </div>
                      </div>


                      <div class="modal-footer">
                        <button class="btn actionBtn glyphicon glyphicon-check btn-success edit" id="update-{{$prodColor->id}}" type="button"><span> Update</span></button>
                        <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"><span id=""> Close</span></button>
                    </div>


                </div>
            </div>
        </div>
    </div>

    {{-- End Edit Modal --}}


    {{-- Delete Modal --}}
        <div id="delete-modal-{{$prodColor->id}}" class="modal fade" style="margin-top:3%">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Delete!</h4>
                    </div>
                    <div class="modal-body">
                        <h2>Are You Confirm to Delete This Record?</h2>

                        <div class="modal-footer">
                            {!! Form::open(['url' => 'deleteFamsPcolor/']) !!}
                            <input type="hidden" name="id" value={{$prodColor->id}}>
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
        var modalId = "{{$prodColor->id}}";
        $("#update-"+modalId).click(function() {

          var id = $("#editModalColorId-"+modalId).val();
          var name = $("#editModalColorName-"+modalId).val();
          var csrf = "{{csrf_token()}}";

          //debugger;

          $.ajax({
                        type: 'post',
                        url: './editFamsPcolor',
                        data: {id: id, name:name, _token: csrf},
                        dataType: 'json',
                        success: function( _response ){
                          if (_response.accessDenied) {
                              showAccessDeniedMessage();
                              return false;
                          }
                            if(_response.errors) {
                                if (_response.errors['name']) {

                                    $("#editModalColorNamee-"+modalId).empty();
                                     $('#editModalColorNamee-'+modalId).append('<span class="errormsg" style="color:red;">'+_response.errors.name+'</span>');
                                     
                                }                                
                            }
                            else{
                                
                                window.location.href = "viewFamsPcolor";
                            }
                            
                        },
                        error: function() {
                            alert("Error");
                        }

                    });

        });
      });
      {{-- End Update data --}}
      </script>

@endforeach

@include('dataTableScript')
@endsection
