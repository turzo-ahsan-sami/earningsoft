@extends('layouts/acc_layout')
@section('title', '| OTS Period')
@section('content')

@php
  $foreignPeriodIds = DB::table('acc_ots_account')->distinct()->pluck('periodId_fk')->toArray(); 
@endphp

<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addOtsRegisterPeriod/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add OTS Period</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">PERIOD LIST</font></h1>
        </div>
        
        <div class="panel-body panelBodyView">       

        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            /*$("#famsProCtgView").dataTable().yadcf([
    
            ]);*/
            $("#otsTable").dataTable({              
                  
                   "oLanguage": {
                  "sEmptyTable": "No Records Available",
                  "sLengthMenu": "Show _MENU_ "
                  }
                });

              
       });
          
          </script>
        </div>
          <table class="table table-striped table-bordered" id="otsTable" style="color: black;">
                    <thead>
                      <tr>
                        <th width="30">SL#</th>
                        <th>Period Name</th>
                        <th>Interest Rate (%)</th>                        
                        <th>Months</th>
                        {{-- <th>Action</th> --}}
                      </tr>
                      
                    </thead>
                    <tbody>
                     @foreach($accOTSRegisterPeriods as $index => $accOTSRegisterPeriod)
                    
                     <tr>
                        <td>{{$index+1}}</td>
                        <td style="text-align:left;padding-left:2px;">{{$accOTSRegisterPeriod->name}}</td>
                        <td >{{number_format($accOTSRegisterPeriod->interestRate,2)}}</td>
                        <td>{{$accOTSRegisterPeriod->months}}</td>
                       
                        {{-- <td>
                          <a href="javascript:;" class="edit-modal" Period="{{$accOTSRegisterPeriod->id}}">
                            <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp;

                        @php
                        if (in_array($accOTSRegisterPeriod->id, $foreignPeriodIds)) {
                          $canDelete = 0;
                        }
                        else{
                          $canDelete = 1;
                        }   
                      @endphp


                        <a href="javascript:;" class="delete-modal" Period="{{$accOTSRegisterPeriod->id}}" @php if($canDelete==0){ echo "style=\"pointer-events: none;cursor: not-allowed;\"";}@endphp>
                            <span class="glyphicon glyphicon-trash"></span>
                        </a>
                        
                      </td> --}}
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





<!-- Edit Modal -->


 <div id="editModal" class="modal fade" style="margin-top:3%;">
  <div class="modal-dialog">
   <div class="modal-content">
    <div class="modal-header">
     <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Update Advance Register</h4>
    </div>

    <div class="modal-body">
     <div class="panel-body float-left">
      <div class="row">   
       <div class="col-md-12">
       {!! Form::open(array('url' => '','id'=>'entryForm', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
       <div class="col-md-12">


        <div class="form-group">
        {!! Form::label('name', ' Name:', ['class' => 'col-sm-3 control-label']) !!}
     <div class="col-sm-9">
      {!! Form::text('name',null, ['class' => 'form-control', 'id' => 'EMname', 'type' => 'text']) !!}
      <p id='namee' style="max-height:3px;color:red;"></p>
     </div>
    </div>

 <div class="form-group">
  {!! Form::label('interestRate', 'Period Interest Rate:', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
    {!! Form::text('interestRate',null, ['class' => 'form-control', 'id' => 'EMinterestRate', 'type' => 'text']) !!}
    <p id='interestRatee' style="max-height:3px;color:red;"></p>
  </div>
</div>

 <div class="form-group">
      {!! Form::label('months', 'Period Month:', ['class' => 'col-sm-3 control-label']) !!}
      <div class="col-sm-9">
      {!! Form::select('months',['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10','11'=>'11','12'=>'12',],null, ['class' => 'form-control', 'id' => 'EMmonths', 'type' => 'text']) !!}
      <p id='monthe' style="max-height:3px;color:red;"></p>
      </div>
     </div>

  <div class="modal-footer">
   <input id="EMotsPeriod" type="hidden" name="otsPeriod" value="">
    <button type="button" id="updateButton" class="btn btn-success"><span class="glyphicon glyphicon-edit" style="padding-right:4px;"></span>Update</button><button type="button" class="btn btn-danger " data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
  </div>
                             
                 </div>


                      </div><!-- row-->


                      </div><!-- panel-body float-left-->

            </div><!-- modal-body -->
          </div><!-- modal-header-->
      </div><!-- modal-content-->
  </div> <!-- end modal-dialog -->
</div> 

<!--  delete Modal -->

  <div id="deleteModal" class="modal fade" style="margin-top:3%">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">OTS Register Period Delete!</h4>
                    </div>
                    <div class="modal-body">
                        <h2>Are You Confirm to Delete This Record?</h2>

                        <div class="modal-footer">
                            {!! Form::open(['url' => '/']) !!}
                            <input type="hidden" id="DMperiod" value=>
                            <button  type="button" class="btn actionBtn glyphicon glyphicon-check btn-success" id="DMoTSperiod"><span id=""> Confirm</button>
                            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>
                            {!! Form::close() !!}

                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- end Delete Modal -->

        <!-- Delete data Modal -->

<script>         
    $(document).ready(function(){ 
      $(document).on('click', '.delete-modal', function(){
        if(hasAccess('deleteOtsRegisterPeriod')){

          $("#DMperiod").val($(this).attr('Period'));
                    $("#deleteModal").find('.modal-dialog').css('width', '55%');
                    $('#deleteModal').modal('show');
                  }
              });
        $("#DMoTSperiod").on('click',  function(){ 
                  var Period= $("#DMperiod").val();
                  var csrf = "{{csrf_token()}}";

                  $.ajax({
                            url: './deleteOtsRegisterPeriod',
                            type: 'POST',
                            dataType: 'json',
                            data: {id:Period, _token:csrf},
                        })

                  .done(function(data){ 
                    location.reload();

                       })

                  .fail(function(){

                    console.log("error");

                    })
                  .always(function(){

                   console.log("complete");

                     });


         });

              
    });     
                  

    </script>

            <!-- end Delete data  --> 

<script type="text/javascript">
   $(document).ready(function(){ 
         
     $(document).on('click', '.edit-modal', function(){
      if(hasAccess('viewOtsRegisterPeriodInfo')){

            var Period = $(this).attr('Period');
            var csrf = "{{csrf_token()}}";
            $("#EMotsPeriod").val(Period);
            $("#updateButton").prop("disabled",false);
           
             $('.errors').empty();
              $('#namee').empty();
              $('#interestRatee').empty();
              $('#monthe').empty();


         // alert(JSON.stringify(data));

                $.ajax({
                  url: './viewOtsRegisterPeriodInfo',
                  type: 'POST',
                  dataType: 'json',
                  data: {id:Period, _token: csrf},
                  success: function(data){
 //alert(JSON.stringify(data));

              $("#EMname").val(data['accOTSRegisterPeriod'].name);
                $("#EMinterestRate").val(data['accOTSRegisterPeriod'].interestRate);
                $("#EMmonths").val(data['accOTSRegisterPeriod'].months);

                $("#editModal").find('.modal-dialog').css('width', '60%');
                $("#editModal").modal('show');


                },
              error: function(argument){
                    alert('response error');          
                  }

               });
              }
         });

    });

  </script>

<script type="text/javascript">
   $(document).ready(function(){ 
     $("#updateButton").on('click', function() {

      $("#updateButton").prop("disabled", true);

            var Period = $("#EMotsPeriod").val();
            var name = $("#EMname").val();
            var interestRate= $("#EMinterestRate").val();
            var months= $("#EMmonths").val();
            var csrf = "{{csrf_token()}}";
            //alert(status);

           $.ajax({
                url: './editOtsRegisterPeriod',
                type: 'POST',
                dataType: 'json',
                data: {id:Period,name:name,interestRate:interestRate,months:months,_token: csrf},
 
})
           


     .done(function(data) {

                 if (data.errors) {
                    if (data.errors['name']) { 
                        $("#namee").empty();
                        $("#namee").append('*'+data.errors['name']);
                       }
                    
                   if (data.errors['interestRate']) {
                        $("#interestRatee").empty();
                        $("#interestRatee").append('*'+data.errors['interestRate']);

                      }
                   if (data.errors['months']) {interestRate
                        $("#monthe").empty();
                        $("#monthe").append('*'+data.errors['months']);

                      }  
                                   
                    }
                    
                else{
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


$("#EMinterestRate").on('input',function(){
this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
});



});

</script> 



@include('dataTableScript')
@endsection
