@extends('layouts/pos_layout')
@section('title', '| Day End List')
@section('content')
@php
  $branchSelected = isset($_GET['filBranch']) ? $_GET['filBranch'] : $userBranchId;
  $monthSelected = isset($_GET['filMonth']) ? $_GET['filMonth'] : '';
  $yearSelected = isset($_GET['filYear']) ? $_GET['filYear'] : $lastYear;
  $pageNum = isset($_GET['page']) ? $_GET['page'] : 1;
@endphp
<div class="row">
<div class="col-md-12">
	<div class="panel panel-default" style="background-color:#708090;">
		<div class="panel-heading" style="padding-bottom:0px">
			<div class="panel-options">
              <a id="exeDayEnd" href="javascript:;" class="btn btn-info pull-right addViewBtn"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i> Execute Day End</a>
            </div>
			<h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">DAY END LIST</font></h1>
		</div>
		<div class="panel-body panelBodyView">
                 {{-- Filtering --}}
      {!! Form::open(array('url' => 'pos/posDayEndList/', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filterFormId', 'method'=>'get')) !!}
      <div class="row">
        <div class="col-md-12">
            <div class="col-md-2" @if ($userBranchId!=1) style="display: none;" @endif>
                  <div class="form-group">
                      <div class="col-md-12">
                          {!! Form::label('', 'Branch:', ['class' => 'control-label pull-left']) !!}
                      </div>
                      <div class="col-md-12">
                          {!! Form::select('filBranch', $branchList,$branchSelected ,['id'=>'filBranch','class'=>'form-control input-sm']) !!}
                      </div>
                  </div>
              </div>

              <div class="col-md-2">
                  <div class="form-group">
                      <div class="col-md-12">
                          {!! Form::label('', 'Month:', ['class' => 'control-label pull-left']) !!}
                      </div>
                      <div class="col-md-12">
                          {!! Form::select('filMonth', [''=>'Select']+$monthArray, $monthSelected ,['id'=>'filMonth','class'=>'form-control input-sm']) !!}
                          <p id='filMonthE' style="max-height:3px; color:red;"></p>
                      </div>
                  </div>
             </div>
              <div class="col-md-2">
              <div class="form-group">
                  <div class="col-md-12">
                      {!! Form::label('', 'Year:', ['class' => 'control-label pull-left']) !!}
                  </div>
                  <div class="col-md-12">
                      {!! Form::select('filYear', $yearArray, $yearSelected ,['id'=>'filYear','class'=>'form-control input-sm', 'required']) !!}
                      <p id='filYearE' style="max-height:3px; color:red;"></p>
                  </div>
              </div>
            </div>
              <div class="col-md-1">
                  <div class="form-group">
                      {!! Form::label('', '', ['class' => 'control-label col-md-12']) !!}
                      <div class="col-md-12">

                          {!! Form::submit('search', ['id' => 'reportSubmit', 'class' => 'btn btn-primary btn-xs']); !!}
                      </div>
                  </div>
              </div>
        </div>
      </div>
      {!! Form::close() !!}
      {{-- End Filtering --}}
        <table class="table table-striped table-bordered" id="areaListView">
                <thead>
                    <tr>
                       <th width="50px;">SL No.</th>
                       <th>Branch Name</th>
                       <th>Branch Date</th>
                       <th>Total Sales Quantity</th>
                       <th>Total Sales Amount</th>
                       <th>Sales Pay Amount</th>
                       <th>Total Collection</th>
                       <th>Action</th>
                    </tr>
                  </thead>
                    <tbody>
                        @foreach ($dayEnds as $key => $dayEnd)
                        <tr>
                          <td>{{(($pageNum-1) * 31) + $key+1}}</td>
                          <td class="name">{{$branchName}}</td>
                          <td>{{date('d-m-Y',strtotime($dayEnd->branchDate))}}</td>
                          <td>{{$dayEnd->totalSalesQuantity}}</td>
                          <td>{{$dayEnd->totalSalesAmount}}</td>
                          <td>{{$dayEnd->totalSalesPayAmount}}</td>
                          <td>{{$dayEnd->totalCollectionAmount}}</td>

                          <td>
                          @if ($dayEnd->id==$maxDayEndId)
                          <a href="javascript:;" class="deleteConfirmation" data-id="{{ $dayEnd->id }}">
                              <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
                          </a>
                        @endif
                      </td>
                    </tr>
                  @endforeach
             </tbody>
        </table>
        <div class="pull-right">
        </div>
		</div>
	</div>
</div>
</div>


{{-- Delete Modal --}}
<div class="modal fade" id="delete-confirmation-modal">
  <div class="modal-dialog">
    <div class="modal-header">
      <h4 class="modal-title">Delete Confirmation</h4>
      <a class="close" href="#" data-dismiss="modal">X</a>
    </div>
    <div class="modal-content">
      <div class="modal-body">
        Are you sure to delete this record?
      </div>
    </div>
    <div class="modal-footer">
      <div class="form-group hidden">
        <div class="col-sm-6 col-sm-offset-2">
          {!! Form::text('id', null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text', 'readonly']) !!}
        </div>
      </div>
      <button id="yesBtn" type="button" class="btn btn-info animated slideInRight" style="margin-bottom:0!important;" >Yes</button>
      <button id="noBtn" type="button" class="btn btn-white animated slideInRight" data-dismiss="modal" style="margin-bottom:0!important;">No</button>
    </div>
  </div>
</div>
{{-- End Delete Modal --}}


<link rel="stylesheet" href="{{ asset('software/css/mfn/style.css') }}">
<style type="text/css">
  #reportSubmit{
        font-size: 14px;
        margin-top: 20px;
    }
</style>

<script type="text/javascript">
	$(document).ready(function() {

        /*exucute day end*/
        $("#exeDayEnd").click(function(event) {

          $(this).attr('style','pointer-events: none;cursor: not-allowed;');
          var branchId = $("#filBranch").val();
           $.ajax({
               url: './storeDayEnd',
               type: 'POST',
               data: {branchId: branchId},
               dataType: 'json',
           })
           .done(function(data) {
                if (data.responseTitle=='Success!') {
                  toastr.success(data.responseText, data.responseTitle, opts);
                  setTimeout(function(){
                      location.reload();
                  }, 2000);
                }
                else if(data.responseTitle=='Warning!'){
                  toastr.warning(data.responseText, data.responseTitle, opts);
                  setTimeout(function(){
                      $("#exeDayEnd").removeAttr('style');
                  }, 2000);
                }
           })
           .fail(function() {
               alert("error");
           });

        });
        /*end exucute day end*/

        /*delete day end*/
        $("#yesBtn").click(function(event) {
            var id = $("#id").val();
            $.ajax({
                url: './deletePosDayEnd',
                type: 'POST',
                dataType: 'json',
                data: {id: id},
            })
            .done(function(data) {
                if (data.responseTitle=='Success!') {
                  toastr.success(data.responseText, data.responseTitle, opts);
                  setTimeout(function(){
                      location.reload();
                  }, 2000);
                }
                else if(data.responseTitle=='Warning!'){
                  toastr.warning(data.responseText, data.responseTitle, opts);
                  setTimeout(function(){
                      $("#exeDayEnd").removeAttr('style');
                  }, 2000);
                }
            })
            .fail(function() {
                alert("error");
            });

        });
        /*end delete day end*/

        /*delete modal*/
        $(document).on('click', '.deleteConfirmation', function(event) {
            event.preventDefault();
            $('#id').val($(this).data('id'));
            $("#delete-confirmation-modal").modal('show');
        });
        /*end delete modal*/

        $("#filBranch").change(function(event) {
            $("#areaListView tbody").empty();
            branchId = $(this).val();

            $("#filYear").empty();

            $.ajax({
                url: './posDayEndGetYears',
                type: 'POST',
                dataType: 'json',
                data: {branchId: branchId},
            })
            .done(function(yearArray) {
                $.each(yearArray, function(index, val) {
                     $("#filYear").append('<option value="'+index+'">'+val+'</option>');
                });
            })
            .fail(function() {
                alert("error");
            });

        });


	}); /*End Ready*/
</script>

@endsection
