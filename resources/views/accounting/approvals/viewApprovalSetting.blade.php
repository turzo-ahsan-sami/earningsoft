@extends('layouts/acc_layout')
@section('title', '| Approval Setting')
@section('content')
@include('successMsg')
<style type="text/css">
  .positionClass{
      font-weight: bold;
      padding: 10px;
      text-decoration: underline;
  } 
  .positionStep{
      font-weight: bold;
     
  }
</style>

<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('viewApprovalType/')}}" class="btn btn-info pull-right addViewBtn" style=""><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Approval Setting</a>
          </div>
          <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">SETTING LIST</h3>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            $("#ApprovalSettingTypeView").dataTable().yadcf([
    
            ]);
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="ApprovalSettingTypeView">
            <thead>
              <tr>
                <th width="80">SL#</th>
                <th>Project Name</th>
                <th>Branch Name</th>
                <th>Date</th>
                <th width="25%">Designation Boss</th>
                <th class="" width="80">Actions</th>
              </tr>
              {{ csrf_field() }}
            </thead>
            <tbody>
              <?php $no=1; ?>
              @foreach($settingsArr as $setting)
                    <tr class="item">
                        <td class="text-center">{{$no++}}</td>
                        <td style="text-align: left; padding: 5px;">{{$setting['project']}}</td>
                        <td style="text-align: left; padding: 5px;">
                          @if($setting['branch'] == 'Head Office')
                            {{'Head Office'}}
                          @else
                            {{'All Branch'}}
                          @endif
                        </td>
                        <td>{{$setting['date']}}</td>
                        <td>
                            <div class="PositionDiv positionStep" align="left">
                                @if($setting['branch'] == 'Head Office')
                                    @if($setting['verified']['department'] !=null && $setting['reviewed']['department'] !=null && $setting['approved']['department'] !=null)
                                      <span class="positionClass"> Verified By : </span> {{$setting['verified']['department']}}  ,  {{$setting['verified']['designation']}}</br>   
                                      <span class="positionClass"> Reviewed By : </span> {{$setting['reviewed']['department']}}  ,  {{$setting['reviewed']['designation']}}</br>
                                      <span class="positionClass"> Approved By : </span> {{$setting['approved']['department']}}  ,  {{$setting['approved']['designation']}}
                                    @endif
                                     @if($setting['verified']['department'] !=null && $setting['reviewed']['department'] ==null && $setting['approved']['department'] !=null)
                                      <span class="positionClass"> Verified By : </span> {{$setting['verified']['department']}}  ,  {{$setting['verified']['designation']}}</br>   
                                      <span class="positionClass"> Approved By : </span> {{$setting['approved']['department']}}  ,  {{$setting['approved']['designation']}}</br>
                                    @endif
                                    @if($setting['verified']['department'] !=null && $setting['reviewed']['department'] ==null && $setting['approved']['department'] ==null)
                                      <span class="positionClass"> Approved By : </span> {{$setting['verified']['department']}}  ,  {{$setting['verified']['designation']}}</br>    
                                    @endif
                                    @if($setting['verified']['department'] ==null && $setting['reviewed']['department'] !=null && $setting['approved']['department'] ==null)
                                      <span class="positionClass">Approved By : </span> {{$setting['reviewed']['department']}}  ,  {{$setting['reviewed']['designation']}}</br>    
                                    @endif
                                    @if($setting['verified']['department'] ==null && $setting['reviewed']['department'] ==null && $setting['approved']['department'] !=null)
                                      <span class="positionClass"> Approved By : </span> {{$setting['approved']['department']}}  ,  {{$setting['approved']['designation']}}  
                                    @endif

                                @else
                               {{--  <span class="positionClass"> Verified By : </span> {{$setting['verified']['designation']}}</br>  
                                <span class="positionClass"> Reviewed By : </span> {{$setting['reviewed']['designation']}}</br>
                                <span class="positionClass"> Approved By : </span> {{$setting['approved']['designation']}} --}}
                                  @if($setting['verified']['designation'] !=null && $setting['reviewed']['designation'] !=null && $setting['approved']['designation'] !=null)
                                      <span class="positionClass"> Verified By : </span> {{$setting['verified']['designation']}}</br>   
                                      <span class="positionClass"> Reviewed By : </span> {{$setting['reviewed']['designation']}}</br>
                                      <span class="positionClass"> Approved By : </span> {{$setting['approved']['designation']}}
                                    @endif
                                     @if($setting['verified']['designation'] !=null && $setting['reviewed']['designation'] ==null && $setting['approved']['designation'] !=null)
                                      <span class="positionClass"> Verified By : </span>  {{$setting['verified']['designation']}}</br>   
                                      <span class="positionClass"> Approved By : </span>{{$setting['approved']['designation']}}</br>
                                    @endif
                                    @if($setting['verified']['designation'] !=null && $setting['reviewed']['designation'] ==null && $setting['approved']['designation'] ==null)
                                      <span class="positionClass"> Approved By : </span> {{$setting['verified']['designation']}}</br>    
                                    @endif
                                    @if($setting['verified']['designation'] ==null && $setting['reviewed']['designation'] !=null && $setting['approved']['designation'] ==null)
                                      <span class="positionClass"> Approved By : </span>{{$setting['reviewed']['designation']}}</br>    
                                    @endif
                                    @if($setting['verified']['designation'] ==null && $setting['reviewed']['designation'] ==null && $setting['approved']['designation'] !=null)
                                      <span class="positionClass"> Approved By : </span>{{$setting['approved']['designation']}}  
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td class="text-center" width="80">
                            <a id="editIcone" href="{{url('editApprovalSettingById/'.$setting['id'])}}" class="edit-modal" data-id="" data-name="" data-parentid="" data-description="" data-isparent=""  data-slno="">
                            <span class="glyphicon glyphicon-edit"></span>
                          </a>&nbsp;
                          <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$setting['id']}}">
                            <span class="glyphicon glyphicon-trash"></span>
                          </a>
                        </td>    
                    </tr>
                  @endforeach
          </table>
        </div>
      </div>
  </div>
  </div>
</div>
</div>
{{--delete modal--}}
<div id="myModal" class="modal fade" style="margin-top:3%">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="clear:both"></h4>
            </div>
            <div class="modal-body">
                <div class="deleteContent" style="padding-bottom:20px;">
                    <h4>You are about to delete this item. This procedure is irreversible !</h4>
                    <h4>Do you want to proceed ?</h4>
                    <span class="hidden id "></span>
                    <span class="hidden vouchertypeid"></span>
                </div>
                <div class="modal-footer">
                    <p id="MSGE" class="pull-left" style="color:red"></p>
                    <p id="MSGS" class="pull-left" style="color:green"></p>
                    {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn', 'id' => 'footer_action_button'] ) !!}
                    {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn', 'data-dismiss' => 'modal', 'id' => 'footer_action_button2'] ) !!}

                    {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-warning', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_dismis'] ) !!}
                </div>
            </div>
        </div>
    </div>
</div>

@include('dataTableScript')
@endsection

  {{-- End Delete Modal --}}
<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
<script type="text/javascript">
$( document ).ready(function() {
   
$(document).on('click', '.delete-modal', function() {

  //if(softacc('deleteAccountTypeItem')){

      $('#MSGE').empty();
      $('#MSGS').empty();
      $('.actionBtn').removeClass('edit');
      $('#footer_action_button2').text(" Yes");
      $('#footer_action_button2').removeClass('glyphicon glyphicon-check');
      //$('#footer_action_button').addClass('glyphicon-trash');
      $('#footer_action_button_dismis').text(" No");
      $('#footer_action_button_dismis').removeClass('glyphicon glyphicon-remove');
      $('.actionBtn').removeClass('btn-success');
      $('.actionBtn').addClass('btn-danger');
      $('.actionBtn').addClass('delete');
      $('.modal-title').text('Delete');
      $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
      $('.modal-dialog').css('width','30%');
      $('.id').text($(this).data('id'));
      $('.deleteContent').show();
      $('.form-horizontal').hide();
      $('#footer_action_button2').show();
      $('#footer_action_button').hide();
      $('.title').html($(this).data('uname'));
      $('#myModal').modal('show');
    //}
    
});

    $('.modal-footer').on('click', '.delete', function() {
        $.ajax({
            type: 'post',
            url: './deleteAccApprovalItem',
            data: {
              '_token': $('input[name=_token]').val(),
              'id': $('.id').text()
            },
            success: function(data) {
                //alert(JSON.stringify(data.responseText));
    
                location.reload();
            },
            error: function(data ){
                alert('Error');
            }
        });
    });


});//ready function end
</script>




