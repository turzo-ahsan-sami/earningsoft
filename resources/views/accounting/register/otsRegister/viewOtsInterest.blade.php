@extends('layouts/acc_layout')
@section('title', '| OTS Interest')
@section('content')


<div class="row">
  <div class="col-md-12">
    <div class="" style="">
      <div class="">
        <div class="panel panel-default" style="background-color:#708090;">
          <div class="panel-heading" style="padding-bottom:0px">
            <div class="panel-options">
              <a href="javascript:;" id="generateButton" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Generate OTS Interest</a>
            </div>
            <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">OTS INTEREST</font></h1>
          </div>
          
          <div class="panel-body panelBodyView">       

            <div>
              <script type="text/javascript">
                jQuery(document).ready(function($)
                {
           /* $("#otsTable").dataTable().yadcf([
    
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
          <th>Date</th>
          <th>OTS ID</th>                        
          <th>Amount (Tk)</th>
          <th width="80">Action</th>
        </tr>
        
      </thead>
      <tbody>
       @foreach($interests as $index => $interest)
       
       <tr>
        <td>{{$index+1}}</td>
        <td>{{$interest->dateTo}}</td>
        <td>{{$interest->interestId}}</td>
        <td class="amount">{{$interest->amount}}</td>                        
        <td>

          @php
          $maxDate = DB::table('acc_ots_interest')->max('dateTo');
          if($interest->getOriginal()['dateTo']==$maxDate){
            $canDelete = 1;
          }
          else{
            $canDelete = 0;
          }

          
          @endphp

          <a href="javascript:;" class="view-modal" interestId="{{$interest->id}}">
            <i class="fa fa-eye" aria-hidden="true"></i>
          </a>&nbsp
          <a href="javascript:;" class="delete-modal" interestId="{{$interest->id}}" @php if($canDelete<1){ echo "style=\"pointer-events: none;cursor: not-allowed;\"";}@endphp>
            <span class="glyphicon glyphicon-trash" ></span>
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
</div>


{{-- View Modal --}}
<div id="viewModal" class="modal fade" style="margin-top:3%">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center">Interest Details</h4>
      </div>
      <div class="modal-body">

        <div id="contectHolder">
          
        </div>

        {{-- View ModalFooter--}}
        <div class="modal-footer">
          <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" id="footer_action_button_dismis" type="button"><span> Close</span></button>
        </div>


      </div> {{-- End View Modal Body--}}

    </div>
  </div>
</div>
{{-- End View Modal --}}



{{-- Delete Modal --}}
<div id="deleteModal" class="modal fade" style="margin-top:3%">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Delete!</h4>
      </div>
      <div class="modal-body">
        
        <h2>Are You Confirm to Delete This Record?</h2>
        

        <div class="modal-footer">
          
          <input type="hidden" name="interestId" id="interestId">
          
          <button id="DMconfirm" type="button" class="btn actionBtn glyphicon glyphicon-check btn-success"><span> Confirm</button>
            
            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>
            

          </div>

        </div>
      </div>
    </div>
  </div>
  {{-- End Delete Modal --}}
  

  {{-- OTS Interest Generate Modal --}}
  <div id="generateModal" class="modal fade" style="margin-top:3%">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Generate!</h4>
        </div>
        
        <div class="modal-body">

          <div class="row" style="padding-bottom: 20px;">
            <div class="col-md-12">
              <div class="form-horizontal form-groups">

                <div class="form-group">
                  {!! Form::label('generateFor', 'Generate For:', ['class' => 'col-sm-4 control-label']) !!}
                  <div class="col-sm-8">
                    {!! Form::radio('generateFor', 1, true) !!} All &nbsp
                    {!! Form::radio('generateFor', 2) !!} Particular Account
                  </div>
                </div>

                <div id="branchAccountDiv" style="display: none;">
                  <div class="form-group">
                    {!! Form::label('branch', 'Branch:', ['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-8">
                      {!! Form::select('branch', $branchList,null,['id'=>'branch','class'=>'form-control','autocomplete'=>'off']) !!}
                    </div>
                  </div>
                  <div class="form-group">
                    {!! Form::label('otsAccId', 'OTS Account:', ['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-8">
                      {!! Form::select('otsAccId', [''=>'Select'] , null,['id'=>'otsAccId','class'=>'form-control','autocomplete'=>'off']) !!}
                      <p id="otsAccIdE" style="max-height: 10px;display: none;color: red;">*Required</p>
                    </div>
                  </div>
                </div>

                <div class="form-group" id="dateDiv">
                  {!! Form::label('date', 'Date:', ['class' => 'col-sm-4 control-label']) !!}
                  <div class="col-sm-8">
                    {!! Form::text('date', null,['id'=>'date','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly','style'=>'cursor:pointer']) !!}
                    <p id="datee" style="max-height: 10px;display: none;color: red;">*Required</p>
                  </div>
                </div>

                <div class="form-group" id="dateTwoDiv" style="display: none;">
                  {!! Form::label('dateTwo', 'Date:', ['class' => 'col-sm-4 control-label']) !!}
                  <div class="col-sm-8">
                    {!! Form::text('dateTwo', null,['id'=>'dateTwo','class'=>'form-control','type' => 'text','autocomplete'=>'off','readonly','style'=>'cursor:pointer']) !!}
                    <p id="dateTwoE" style="max-height: 10px;display: none;color: red;">*Required</p>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">            
          <button id="generateSubmitButton" type="button" class="btn actionBtn glyphicon glyphicon-check btn-success"><span> Generate</span></button>
          <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"><span> Close</span></button>
        </div>
      </div>
      
    </div>
  </div>
</div>
{{-- End OTS Interest Generate Modal --}}



{{-- Loadding Modal --}}
<div id="loadingModal" data-backdrop="static" data-keyboard="false" class="modal fade" style="margin-top:3%;background-color: black;opacity:0.8 !important;">
  <div class="modal-dialog" style="text-align: center; padding-top: 15%;"> 
    <div class="modal-body">
      <i id="loaddingLogo" class="fa fa-spinner fa-spin fa-3x fa-fw" style="font-size:70px;"></i>            
    </div>
  </div>
</div>
{{-- End Loadding Modal --}}





<script type="text/javascript">
  $(document).ready(function() {


   function pad (str, max) {
    str = str.toString();
    return str.length < max ? pad("0" + str, max) : str;
  }

  function newDateFormate(argument) {
    var formattedDate = new Date(argument);
    var d = formattedDate.getDate();
    var m =  formattedDate.getMonth();
        m += 1;  // JavaScript months are 0-11
        var y = formattedDate.getFullYear();

        return (pad(d,2) + "-" + pad(m,2) + "-" + y);
      }


      /*View Modal*/    
      
      $(document).on('click', '.view-modal', function() {
        if(hasAccess('otsGetInterestInfo')){

          var interestId = $(this).attr('interestId');
          var csrf = "{{csrf_token()}}";
          $.ajax({
            url: './otsGetInterestInfo',
            type: 'POST',
            dataType: 'json',
            data: {interestId: interestId, _token: csrf},
            success: function(data) {
              
              $("#contectHolder").empty();
              $.each(data['branches'], function(index, branch) {
                textMarkup = "<br><div class='row viewModalLabel' style='color: black;'><div class='col-md-12'> <div class='form-horizontal form-groups'><div class='form-group'><label for='VMbranch' class='col-sm-2 control-label'>Branch:</label> <div class='col-sm-4'><input type='text' name='VMdepId' value='"+branch.name+"' class='form-control VMbranch' autocomplete='off' readonly></div></div></div></div></div>";

                if (index>0) {
                  $(".VMtable:last").after(textMarkup);
                }
                else{
                  $("#contectHolder").append(textMarkup);
                }


                tableMarkUp = "<br><table width='100%' class='table table-striped table-bordered VMtable'><thead><tr><th>SL#</th><th>Acount No</th><th>Account Holder</th><th>Account Nature</th><th>Date From</th><th>Date To</th><th>Principal Amount</th><th>Interest</th></tr></thead><tbody class='tbody'></tbody><tr><td colspan='6'><span style='font-weight:bold;font-size:15;'>Total</span></td><td style='text-align:right;padding-right:5px;'><span class='totalPrincipalAmount'style='font-weight:bold;font-size:15;'></span></td><td style='text-align:right;padding-right:5px;'><span class='totalInterest'style='font-weight:bold;font-size:15;'></span></td></tr></tbody></table>";

                $(".viewModalLabel:last").after(tableMarkUp);
                totalPrincipalAmount = 0;
                totalInterest = 0;
                count=1;
                $.each(data['interestDetails'], function(index2, intOb) {
                 if (branch.id==intOb.branchId_fk) {
                  var accNature = "";
                  $.each(data['periods'], function(index3, period) {
                   if (period.id==intOb.periodId_fk) {
                    accNature = period.name;
                  }
                });

                  markup = "<tr style='line-height: 30px;'><td style='text-align:center;'>"+count+"</td><td style='text-align:center;'>"+intOb.accNo+"</td><td style='text-align:left;padding-left:5px;'>"+intOb.name+"</td><td style='text-align:center;'>"+accNature+"</td><td style='text-align:center;'>"+newDateFormate(intOb.dateFrom)+"</td><td style='text-align:center;'>"+newDateFormate(intOb.dateTo)+"</td><td style='text-align:right;padding-right:5px;'>"+intOb.pAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })+"</td><td style='text-align:right;padding-right:5px;'>"+intOb.amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })+"</td></tr>";
                  $(".tbody:last").before(markup);
                  totalPrincipalAmount = totalPrincipalAmount + Number((intOb.pAmount).toFixed(2));
                  totalInterest = totalInterest +  Number((intOb.amount).toFixed(2));
                  count++;
                }
              }); /*End Each for Interest Details*/


                $(".totalPrincipalAmount:last").html(totalPrincipalAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $(".totalInterest:last").html(totalInterest.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

                

              });/*End Each of Branch*/


              $("#viewModal").modal('show');

            },
            error: function(argument) {
              alert('response error');
            }
          });
        }
        
      });
      /*End View Modal*/

      /*Delete Modal*/
      $(document).on('click',".delete-modal" ,function() {
        if(hasAccess('deleteInterest')){
          
          $("#interestId").val($(this).attr('interestId'));      
          $("#deleteModal").modal('show');
        }
      });
      /*End Delete Modal*/

      /*delele Record*/
      $("#DMconfirm").on('click', function() {
        
        
        var interestId = $("#interestId").val();
        var csrf = "{{csrf_token()}}";
        $.ajax({
         type: 'post',
         url: './deleteInterest',
         data: {interestId: interestId, _token: csrf},
         dataType: 'json',
         success: function( _response ){
          location.reload();
        },                   
        
        
        error: function( data ){
          
          alert('error');
          
        }
      }); 
      });
      /*End delele Record*/


      $("#generateButton").on('click', function() {
        if(hasAccess('generateOtsInterest')){   
          $("#date").val('');
          $("#datee").hide();
          $("#generateModal").modal('show');
        }
        
      });



      var minDate = new Date("{{$minDate}}");

      /*//////////////*/

      $("#dateTwo").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange : "2000:c",
        maxDate: "dateToday",
        dateFormat: 'dd-mm-yy',
        onSelect: function () {
          $("#dateTwoE").hide();
        }
      });

      $("#date").datepicker({
        dateFormat: 'MM yy',
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        

        onClose: function(dateText, inst) {
          var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
          var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
          $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
          $("#datee").hide();
        }
      });

      $("#date").focus(function () {
        $(".ui-datepicker-calendar").hide();
        $("#ui-datepicker-div").position({
          my: "center top",
          at: "center bottom",
          of: $(this)
        });
      });
      /*//////////////*/


      /*Submit the form*/
      /*$("#generateSubmitButton").click(function() {*/
        $("#generateSubmitButton").on('click',  function(event) {
          var generateFor = $("[name=generateFor]:checked").val();
          if (generateFor==1) {
            generateInterestForAll();
          }
          else{
            generateInterestForParticularAccount();
          }
        });
        /*End Submit the form*/

        function generateInterestForAll() {
          
          var date = $("#date").val();
          
          if ($("#date").val()=="") {
            $("#datee").show();                
          }
          else{
            $("#generateModal").modal('hide');
            $("#loadingModal").modal('show');
            $(document).keydown(function(e) {
              if (e.keyCode == 27) return false;
            });
            
            $.ajax({
             type: 'post',
             url: './generateOtsInterest',
             data: {date: date},
             dataType: 'json',
             success: function( data ){
              $("#loadingModal").modal('hide');
              if (data.responseTitle=="Info!") {
                toastr.warning(data.responseText, data.responseTitle, opts);
              }
              else{
                toastr.success(data.responseText, data.responseTitle, opts);
              }   
              setTimeout(function(){
                location.reload();
              }, 3000);
            },
            
            error: function( data ){
              
              alert('error');
              
            }
          }); 
          }
          
        }

        function generateInterestForParticularAccount() {

          var otsAccId = $("#otsAccId").val();     
          var date = $("#dateTwo").val();

          if (otsAccId=='') {
            $("#otsAccIdE").show();
          }

          if (date=="") {
            $("#dateTwoE").show();             
          }

          if (otsAccId!='' && date!='') {

            $("#generateModal").modal('hide');
            $("#loadingModal").modal('show');
            $(document).keydown(function(e) {
              if (e.keyCode == 27) return false;
            });
            
            $.ajax({
             type: 'post',
             url: './generateOtsInterestForParticularAccount',
             data: {otsAccId: otsAccId, date: date},
             dataType: 'json',
             success: function( data ){
              $("#loadingModal").modal('hide');
              if (data.responseTitle=="Info!") {
                toastr.warning(data.responseText, data.responseTitle, opts);
              }
              else{
                toastr.success(data.responseText, data.responseTitle, opts);
              }   
              setTimeout(function(){
                location.reload();
              }, 3000);
            },
            
            error: function( data ){
              
              alert('error');
              
            }
          }); 
            
          }

        }

        $("[name=generateFor]").change(function(event) {
          if($(this).val()==1){
            $("#branchAccountDiv").hide('slow/400/fast');
            $("#dateDiv").show();
            $("#dateTwoDiv").hide();
          }
          else{
            $("#branchAccountDiv").show('slow/400/fast');
            $("#dateDiv").hide();
            $("#dateTwoDiv").show();
          }
        });

        $("#branch").change(function(event) {
          $("#otsAccId option:gt(0)").remove();
          var branchId = $(this).val();
          if (branchId != '') {
            $.ajax({
              url: './getNonMonthlyOtsAccountBaseOnBranch',
              type: 'POST',
              dataType: 'json',
              data: {branchId: branchId},
            })
            .done(function(accounts) {
              $.each(accounts, function(index, account) {
               $("#otsAccId").append("<option value='"+account.id+"'>"+account.accNo+"</option>");
             });
              
            })
            .fail(function() {
              alert("error");
            });
            
          }
        });

        $("#otsAccId").change(function(event) {
          $("#otsAccIdE").hide();
        });


      });/*End Ready*/
    </script>

    <style type="text/css">
      #otsTable tr td.name{
        text-align: left;
        padding-left: 5px;
      }
      #otsTable tr td.amount{
        text-align: right;
        padding-right: 5px;
      }
    </style>


    @include('dataTableScript')

    @endsection
