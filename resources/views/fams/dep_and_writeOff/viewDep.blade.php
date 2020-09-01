@extends('layouts/fams_layout')
@section('title', '| Depreciation')
@section('content')
@include('successMsg')
<div class="row">
  <div class="col-md-12">

    @if (session('deptodayAlreadyCreated'))
    <div class="alert alert-info alert-dismissable">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <strong>Info!</strong> {{ session('deptodayAlreadyCreated') }}
    </div>
    @endif
    @if (session('depDelete'))
    <div class="alert alert-info alert-dismissable">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <strong>Info!</strong> {{ session('depDelete') }}
    </div>
    @endif
    <div class="">
      <div class="">
        <div class="panel panel-default" style="background-color:#708090;">
          <div class="panel-heading" style="padding-bottom:0px">
            <div class="panel-options">          
              <a id="depGenerateButton" href="javascript:;" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Generate Depreciation</a>
              
            </div>


            <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px; color: white;">DEPRECIATION LIST</h1>
          </div>
          <div class="panel-body panelBodyView"> 
            <div>
              <script type="text/javascript">
                jQuery(document).ready(function($)
                {

                  $("#famsDepTable").dataTable({
                   "oLanguage": {
                    "sEmptyTable": "No Records Available",
                    "sLengthMenu": "Show _MENU_",
                    "sInfo": ""
                  }

                });

             //var versionNo = $.fn.dataTable.version;
             //alert(versionNo);
           });
         </script>
       </div>
       <table  id="famsDepTable" class="table table-striped table-bordered" style="color: black;">
        <thead>
          <tr>
            <th width="30" rowspan="2">SL#</th>
            <th rowspan="2">Dep. Date</th>                          
            <th rowspan="2">Dep. ID</th>
            <th rowspan="2">Project</th>                        
            <th rowspan="2">Branch</th>                        
            <th colspan="2" style="border-bottom: 0px;">Dep. Period</th>
            <th rowspan="2">Use Days</th>                        
            <th rowspan="2">Amount</th>
            <th rowspan="2">Action</th>
          </tr> 
          <tr>
            <th>From</th>
            <th style="border-right: 1px solid white;">To</th>
          </tr>                     
        </thead>
        <tbody>
          <?php $no=0; ?>
          @foreach($depreciationGroups as $depreciationGroup)
          @php
                          //$depreciations = DB::table('fams_depreciation')->where('depGroupId',$depreciationGroup->depGroupId)->get();
          $amount = round(DB::table('fams_depreciation')->where('depGroupId',$depreciationGroup->depGroupId)->sum('amount'),2);
          $dateFrom = date('d-m-Y',strtotime(DB::table('fams_depreciation_details')->where('depGroupIdNo',$depreciationGroup->depGroupId)->value('depFrom')));
          $dateTo = date('d-m-Y',strtotime(DB::table('fams_depreciation_details')->where('depGroupIdNo',$depreciationGroup->depGroupId)->value('depTo')));
          $days = date_diff(date_create($dateFrom),date_create($dateTo));

          $projectsNameArray = DB::table('fams_depreciation')->where('depGroupId',$depreciationGroup->depGroupId)->groupBy('projectId')->pluck('projectId')->toArray();
          if (sizeof($projectsNameArray)>1) {
            $projectName = 'All';
          }
          else{
            $projectName = DB::table('gnr_project')->where('id',$depreciationGroup->projectId)->value('name');
          }

          $branchNameArray = DB::table('fams_depreciation')->where('depGroupId',$depreciationGroup->depGroupId)->groupBy('branchId')->pluck('branchId')->toArray();
          if (sizeof($branchNameArray)>1) {
            $branchName = 'All';
          }
          else{
            $branchName = DB::table('gnr_branch')->where('id',$depreciationGroup->branchId)->value('name');
          }

          @endphp

          <tr class="item{{$depreciationGroup->id}}">
            <td class="text-center slNo">{{++$no}}</td>
            <td style="color: black;">{{(date('d-m-Y', strtotime($depreciationGroup->createdDate)))}}</td>
            <td style="color: black;">{{$depreciationGroup->depGroupId}}</td>
            <td class="name">{{$projectName}}</td>
            <td class="name">{{$branchName}}</td>
            <td>{{$dateFrom}}</td>
            <td>{{$dateTo}}</td>
            <td>{{(int)$days->format("%a")+1}}</td>
            <td style="color: black;text-align: right;padding-right: 15px;">{{number_format($amount,2)}}</td>                          

            <td  class="text-center" width="80">
             <a href="javascript:;" class="view-modal" depGroupId="{{$depreciationGroup->depGroupId}}">
               <i class="fa fa-eye" aria-hidden="true" ></i>
             </a>&nbsp
             <a href="javascript:;" class="delete-modal" depGroupId="{{$depreciationGroup->depGroupId}}">
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
</div>


{{-- View Modal --}}
<div id="viewModal" class="modal fade" style="margin-top:3%">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Depreciation Details</h4>
      </div>
      <div class="modal-body" id="modalBody">

        <div id="contectHolder">

        </div>

        <div class="modal-footer">
          <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"><span id=""> Close</span></button>
        </div>


      </div>
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
          {!! Form::open(['url' => 'deleteFamsDep/']) !!}
          <input type="hidden" name="depGroupId" id="DMdepGroupId">
          <input type="hidden" name="id" value={{$depreciationGroup->id}}>

          <button  type="submit" class="btn actionBtn glyphicon glyphicon-check btn-success"><span id=""> Confirm</button>

            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>
            {!! Form::close() !!}

          </div>

        </div>
      </div>
    </div>
  </div>
  {{-- End Delete Modal --}}

  {{-- Dep Modal --}}
  <div id="dep-modal" class="modal fade" style="margin-top:3%">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align: center;">Message!</h4>
        </div>
        <div class="modal-body">
          <h3>You are going to Generate Depreciation!</h3><br>
          {!! Form::open(['url' => 'generateDep/']) !!}
          <div class="row">
            <div class="form-horizontal form-groups" style="padding-left: 50px;padding-right: 50px;">

              <div class="form-group">
                {!! Form::label('depProject', 'Project:', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-9">
                  @php
                  $projects = DB::table('gnr_project')->select('name','projectCode','id')->get();
                  @endphp

                  <select id="depProject" name="depProject" class="form-control">
                    {{-- <option value="">All</option> --}}                               
                    @foreach($projects as $project)
                    <option value="{{$project->id}}">{{str_pad($project->projectCode,3,"0",STR_PAD_LEFT).'-'.$project->name }}</option>
                    @endforeach
                  </select>

                  <p id='depProjecte' style="max-height:3px;color: red;display: none;">*Required</p>
                </div>
              </div>

              <div class="form-group">
                {!! Form::label('depBranch', 'Branch:', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-9">
                  @php
                  $branches = DB::table('gnr_branch')->select('name','branchCode','id')->get();
                  @endphp

                  <select id="depBranch" name="depBranch" class="form-control">
                    <option value="">All</option>
                    @foreach($branches as $branch)
                    <option value="{{$branch->id}}">{{str_pad($branch->branchCode,3,"0",STR_PAD_LEFT).'-'.$branch->name }}</option>
                    @endforeach
                  </select>

                  <p id='depBranche' style="max-height:3px;color: red;display: none;">*Required</p>
                </div>
              </div>

              <div class="form-group">
                {!! Form::label('depEndDate', 'Dep. End Date:', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-9">
                  {!! Form::text('depEndDate', $value = null, ['class' => 'form-control', 'id' => 'depEndDate', 'type' => 'text','placeholder' => 'Enter Dep. End Date','autocomplete'=>'off','readonly','style'=>'cursor:pointer']) !!}
                  <p id='depEndDatee' style="max-height:3px;color: red;display: none;">*Required</p>
                </div>
              </div>                          


              <div class="modal-footer">                           

                <button  type="submit" id="submit" class="btn actionBtn glyphicon glyphicon-check btn-success"> Submit</button>
                <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"> No</button>
                {!! Form::close() !!}

              </div>
            </div>                           
          </div> 

        </div>
      </div>
    </div>
  </div>
  {{-- End Dep Modal --}}



  <script type="text/javascript">
    window.hasAnyError = 0;
    window.onerror = function(){
      hasAnyError = 1;
    }
    if (hasAnyError || !hasAnyError) {

      window.onload = function(){

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
      $(".view-modal").on('click', function() {
        if(hasAccess('getFamsDepDetails')){
          var depGroupId = $(this).attr('depGroupId');
          var csrf = "{{csrf_token()}}";
          var count=0;

          $.ajax({
            type: 'post',
            url: './getFamsDepDetails',
            data: { depGroupId: depGroupId,_token: csrf},
            dataType: 'json',
            success: function (data) {

              $("#contectHolder").empty();

              $.each(data.depreciations,function(index1, dep){
                var branchName = "";
                $.each(data.branch,function(index2, branch) {
                  if (branch.id==dep.branchId) {
                    branchName = branch.name;
                  }
                });


                textMarkup = "<br><div class='row viewModalLabel' style='color: black;'><div class='col-md-12'> <div class='form-horizontal form-groups'><div class='form-group'><label for='VMdepId' class='col-sm-2 control-label'>Branch Dep. ID:</label><div class='col-sm-4'><input type='text' name='VMdepId' value='"+dep.depId+"' class='form-control VMdepId' autocomplete='off' readonly></div><label for='VMbranch' class='col-sm-2 control-label'>Branch:</label> <div class='col-sm-4'><input type='text' name='VMdepId' value='"+branchName+"' class='form-control VMbranch' autocomplete='off' readonly></div></div></div></div></div>";

                if (index1>0) {
                  $(".VMtable:last").after(textMarkup);
                }
                else{
                  $("#contectHolder").append(textMarkup);
                }


                tableMarkUp = "<br><table width='100%' class='table table-striped table-bordered VMtable'><thead><tr><th>SL#</th><th>Product</th><th>Product ID</th><th>Product Cost</th><th>Dep. Rate(%)</th><th colspan='3' style='padding:0px;margin:0px;'><table width='100%' style='margin:0px;padding:0px;height:100%;'><tr style='border-bottom:1pt solidwhite;'><th colspan='3'>Date</th></tr><tr><th width='33%'style='border-right:1pt solidwhite;'>Purchase</th><th width='33%'style='border-right:1pt solidwhite;'>Dep.From</th><th width='33%'>Dep.To</th></tr></table></th><th>UseDays</th><th>Amount</th></tr></thead><tbody class='tbody'></tbody><tr><td colspan='9'><span style='font-weight:bold;font-size:15;'>Total</span></td><td style='text-align:right;padding-right:15px;'><span class='totalAount'style='font-weight:bold;font-size:15;'>"+dep.amount.toFixed(2)+"</span></td></tr></tbody></table>";

                $(".viewModalLabel:last").after(tableMarkUp);

                count=0;

                $.each(data.depDetails,function(index3, depDetails) {

                  $.each(data.product,function(index4, product) {
                    if (product.id==depDetails.productId) {
                      productName = product.name;
                      productCode = product.productCode;
                      productCost = product.totalCost;
                      depRate = product.depreciationPercentage;
                      purchaseDate = product.purchaseDate;

                    }
                  });
                  var prefix = "{{$prefix}}";
                  if (depDetails.depIdNo === dep.id) {
                    count++;
                    markup = "<tr><td style='text-align:center;'>"+count+"</td><td style='text-align:left;padding-left:15px;'>"+productName+"</td><td style='text-align:center;'>"+prefix+productCode+"</td><td style='text-align:right;padding-right:15px;'>"+productCost+"</td><td style='text-align:center;'>"+depRate+"</td><td style='text-align:center;'>"+newDateFormate(purchaseDate)+"</td><td style='text-align:center;'>"+newDateFormate(depDetails.depFrom)+"</td><td style='text-align:center;'>"+newDateFormate(depDetails.depTo)+"</td><td style='text-align:center;'>"+depDetails.days+"</td><td style='text-align:right;padding-right:15px;'>"+depDetails.amount.toFixed(2);+"</td></tr>";
                    $(".tbody:last").before(markup);
                  }

                });
              });

            //alert(JSON.stringify(data.depreciations[1].id));
            $("#viewModal").modal('show');
          },
          error: function(){

          }
        });

        }
      //$("#viewModal").modal('show'); 
    });/*End View Modal*/


/*Delete Modal*/
$(document).on('click', '.delete-modal', function() {
  if(hasAccess('deleteFamsDep')){
    $("#DMdepGroupId").val($(this).attr('depGroupId'));      
    $("#deleteModal").modal('show');
  }
});
    /*$(".delete-modal").on('click', function() {
      $("#DMdepGroupId").val($(this).attr('depGroupId'));      
      $("#deleteModal").modal('show');
    });*/
    /*End Delete Modal*/ 

  } /*End On Load*/
  $("#famsWriteOffTable tr").find(".dataTables_empty").css("color","black");
  $("#famsWriteOffTable_info").hide();
  $("#viewModal").find(".modal-dialog").css("width","80%");

}/*End has Error*/

</script> 





<script type="text/javascript">
  $(document).ready(function() {
    $("#famsDepTable tr").find(".dataTables_empty").css("color","black");

    $(".view-modal").find(".modal-dialog").css("width","80%");
  });
  
</script>

{{-- Write Off Date --}}
<script type="text/javascript">
  $(document).ready(function() {

    function pad (str, max) {
      str = str.toString();
      return str.length < max ? pad("0" + str, max) : str;
    }


    $('#dep-modal').on('shown.bs.modal', function() { 
     $("#depProject").trigger('change');
   }) ;


    var lastDepDate = new Date("{{$lastDepDate}}");        
    /*window.startingDate = lastDepDate.setDate(lastDepDate.getDate() + 1);*/
    $("#depEndDate").datepicker({
      changeMonth: true,
      changeYear: true,
      yearRange : "1998:c",
      /*minDate: new Date(startingDate),*/
      /*maxDate: new Date("2017-06-30"),*/
      maxDate: "dateToday",
      dateFormat: 'dd-mm-yy',
      onSelect: function () {
        $('#depEndDatee').hide();               
      }
    });




    /* Change Project*/
    $("#depProject").change(function(){

      var projectId = $(this).val();

      var csrf = "<?php echo csrf_token(); ?>";

      $.ajax({
        type: 'post',
        url: './famsAddProductOnChangeProject',
        data: {projectId:projectId,_token: csrf},
        dataType: 'json',
        success: function( data ){

                 // alert(JSON.stringify(data['branchList']));

                 $("#depBranch").empty();
                 $("#depBranch").prepend('<option selected="selected" value="">All</option>');




                 $.each(data['branchList'], function (key, branchObj) {

                  $('#depBranch').append("<option value='"+ branchObj.id+"'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>");                       
                });

               },
               error: function(_response){
                alert("error");
              }

            });/*End Ajax*/

    });/*End Change Project*/

    $("#depGenerateButton").click(function(event) {
     if(hasAccess('generateDep')){
      $("#dep-modal").modal('show');
    }
  });



  });
</script> 
{{-- End Write Off Date --}}

{{--  --}}
<script type="text/javascript">
  $(document).ready(function() {
    $("#submit").click(function(event) {
      if($("#depEndDate").val()==""){
        event.preventDefault();
        $('#depEndDatee').show();
      }
    });

    /*Hide Table Footer Option*/
    $("#famsDepTable_info").hide();
  });
</script>


<style>

#famsDepTable thead tr th {
  padding: 1px;
  vertical-align: top;
}

#famsDepTable thead tr:nth-child(2) th:nth-child(1),#famsDepTable thead tr:nth-child(2) th:nth-child(2) {
  border-top-width: 1px;  
}

#famsDepTable tbody tr td.name {
  text-align: left;
  padding-left: 5px;
}

</style>





@include('dataTableScript')

@endsection