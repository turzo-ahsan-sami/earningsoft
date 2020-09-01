@extends('layouts/acc_layout')
@section('title', '| OTS Period')
@section('content')

@include('accounting/otsPeriodInterestRate/OTSperiodInterestRateAjax')

<div class="row add-data-form">
 <div class="col-md-12">
 <div class="col-md-2"></div>
 <div class="col-md-8 fullbody">
  <div class="viewTitle" style="border-bottom: 1px solid white;">
    <a href="{{url('OTSperiodInterestHistoryTable/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
    </i>OTS Period List</a>
 </div>
 @if ($Success == 'True')
   <div class="alert alert-info">
     <strong>Success!</strong> You have successfully inserted the information !
   </div>
 @endif
    <div class="panel panel-default panel-border">
      <div class="panel-heading">
        <div class="panel-title">OTS Period Add Form</div>
      </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-8">
                        {!! Form::open(array('url' => 'OTSperiodInterestHistorySubmit', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filterFormId', 'method'=>'get')) !!}

                        <div class="form-group">
                          {!! Form::label('periodName','OTS Period Name: ', ['class' => 'col-sm-3 control-label']) !!}
                          <div class="col-sm-9">
                            <select name="periodName" class="form-control input-sm" id="periodName" required="true" onclick="myFunction()">
                                <option value=""> -- Select Period Name -- </option>
                                @php
                                $OTSperiod = DB::table('acc_ots_period')
                                              ->get();
                                @endphp
                                @foreach($OTSperiod as $OTSperiods)
                                  <option value="{{ $OTSperiods->id }}"> {{ $OTSperiods->name }} </option>             //Loop for showing the data in the OTS Period Section option
                                @endforeach
                            </select>
                          </div>
                        </div>

                          <div class="form-group">
                            {!! Form::label('interestRate','Interest Rate: ', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                              <input type="text" class="form-control input-sm" id="interestRate" name="interestRate" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required="true" placeholder='Enter Interest Rate'>
                            </div>
                          </div>

                         <div class="form-group">
                           {!! Form::label('date','Date: ', ['class' => 'col-sm-3 control-label']) !!}
                           <div class="col-sm-9">
                             <input type="text" class="form-control input-sm" id="txtDate1" name="txtDate1" value="" readonly="true" required="true" placeholder='Enter Effective Date'>
                           </div>
                         </div>

                        <div class="form-group">
                          {!! Form::label('', ' ', ['class' => 'col-sm-3 control-label']) !!}
                          <div class="col-sm-9 text-right">
                            <input class="btn btn-info" type="submit" name="Submit">
                            <a href="{{url('OTSperiodInterestHistoryTable')}}" class="btn btn-danger closeBtn">Close</a>
                            <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                          </div>
                        </div>
                      {!! Form::close() !!}
                    </div>
                    <div class="col-md-4 emptySpace vert-offset-top-0"><img src="images/catalog/image15.png" width="40%" height="" style="float:right"></div>
                    </div>
                  </div>
                </div>
             </div>
             <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
            <div class="col-md-2"></div>
    </div>
</div>

<script type="text/javascript">
function myFunction() {
    document.getElementById("txtDate1").style.cursor = "pointer";
}

@if(Session::has('message'))
  var type = "{{ Session::get('alert-type', 'info') }}";
  switch(type){
      case 'info':
          toastr.info("{{ Session::get('message') }}");
          break;
      case 'warning':
          toastr.warning("{{ Session::get('message') }}");
          break;
      case 'success':
          toastr.success("{{ Session::get('message') }}");
          break;
      case 'error':
          toastr.error("{{ Session::get('message') }}");
          break;
  }
  setTimeout(function(){
      window.location.href="./OTSperiodInterestHistoryTable";
  }, 3000);
@endif

function checking(){
  this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
}

var count = 0;
function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    // this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

    if ((charCode >= 46 && charCode <=57) && charCode != 47){
      if(charCode ==46){
        count = count + 1;
      }
      else if (charCode !=46) {
        // count = count - 1;
        // if (count < 0) {
        //   count = 0;
        // }
        // return true;
      }
      if (count == 1) {
        return true;
      }
      else if (count > 1) {
        return false;
      }

    }
    else {
      return false;
    }
}


</script>


@endsection
