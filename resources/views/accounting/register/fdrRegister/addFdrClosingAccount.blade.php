@extends('layouts/acc_layout')
@section('title', '| FDR Register')
@section('content')

<div class="row add-data-form">
<div class="col-md-1"></div>
<div class="col-md-10 fullbody">
<div class="viewTitle" style="border-bottom: 1px solid white;">
    <a href="{{url('viewFdrAccountClose/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
    </i>Encashment List</a>
</div>

<div class="panel panel-default panel-border">
    <div class="panel-heading">
        <div class="panel-title">FDR Encashment</div>
    </div>


<div class="panel-body">
            <div class="row">                
                <div class="col-md-12">
               {{--  {!! Form::open(array('url' => 'storeOtsPayment','id'=>'entryForm', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!} --}}
                
                <div class="form-horizontal form-groups">
                
                    <div class="col-md-6">

                    

                    <div class="form-group">
                            {!! Form::label('project', 'project:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                            @php
                                $projects = DB::table('gnr_project')->select('id','name','projectCode')->get();
                            @endphp
                            <select id="project" name="project" class="form-control">
                                <option value="">Select Project</option>
                                @foreach($projects as $project)
                                <option value="{{$project->id}}">{{str_pad($project->projectCode,3,'0',STR_PAD_LEFT).'-'.$project->name}}</option>
                                @endforeach
                            </select>                              
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('projectType', 'Project Type:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                            @php
                                $projectTypes = DB::table('gnr_project_type')->select('id','name','projectTypeCode')->get();
                            @endphp
                            <select id="projectType" name="projectType" class="form-control">
                                <option value="">Select Project Type</option>
                                @foreach($projectTypes as $projectType)
                                <option value="{{$projectType->id}}">{{str_pad($projectType->projectTypeCode,3,'0',STR_PAD_LEFT).'-'.$projectType->name}}</option>
                                @endforeach
                            </select>                              
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('branch', 'Branch:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                            @php
                                $branches = DB::table('gnr_branch')->select('id','name','branchCode')->get();
                            @endphp
                            <select id="branch" name="branch" class="form-control">
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                <option value="{{$branch->id}}">{{str_pad($branch->branchCode,3,'0',STR_PAD_LEFT).'-'.$branch->name}}</option>
                                @endforeach
                            </select>                              
                            </div>
                    </div>

                    

                  
                
                </div>


                {{-- 2nd col-6 --}}
                <div class="col-md-6">

                    <div class="form-group">
                        {!! Form::label('fdrType', 'FDR Type:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">  
                        @php
                          $fdrTypeList = array(''=>'Select FDR Type') + DB::table('acc_fdr_type')->pluck('name','id')->toArray();
                        @endphp                                  
                            {!! Form::select('fdrType',$fdrTypeList ,null, ['class'=>'form-control', 'id' => 'fdrType']) !!}
                            
                        </div>
                    </div>

                   <div class="form-group">
                        {!! Form::label('bank', 'Bank:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">  
                        @php
                          $bankList = array(''=>'Select Bank') + DB::table('gnr_bank')->pluck('name','id')->toArray();
                        @endphp                                  
                            {!! Form::select('bank',$bankList ,null, ['class'=>'form-control', 'id' => 'bank']) !!}
                            
                        </div>
                    </div>


                    <div class="form-group">
                        {!! Form::label('bankBranch', 'Bank Branch Location:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                        @php
                            $bankBranchList = DB::table('gnr_bank_branch')->select('name','id','bankId_fk')->get();
                        @endphp

                        <select id="bankBranch" name="bankBranch" class="form-control">
                             <option value="">Select Location</option>
                             @foreach($bankBranchList as $bankBranch)
                                @php
                                $bankShortName = DB::table('gnr_bank')->where('id',$bankBranch->bankId_fk)->value('shortName');
                                @endphp
                             <option value="{{$bankBranch->id}}">{{$bankBranch->name.'-'.$bankShortName}}</option>>
                             @endforeach
                         </select>                            
                            
                        </div>
                    </div>
                    
                
                </div> {{-- End 2nd col-6 --}}



            
            {{-- </div> --}}
                
            </div>

        </div> {{--End Col 12 --}}

 </div>
</div>


<p class="dotted" style="border-style: dotted;border-width: 1px;opacity:0.3;"></p>

<div class="panel-body">
    <div class="row">
        <div class="col-md-12">
        <div class="form-horizontal form-groups">
            {!! Form::label('accId','Account No:',['class'=>'control-label col-sm-2','style'=>'padding-top:5px;']) !!}
                <div class="col-sm-4">
                    @php
                        $accounts = array(''=>'Select Account') +  DB::table('acc_fdr_account')->where('status',1)->pluck('accNo','id')->toArray();
                    @endphp
                    
                    {!! Form::select('accId',$accounts,null,['id'=>'accId','class'=>'form-control']) !!}
                    
                </div>

                <div class="col-sm-2">
                    {!! Form::button('Select', ['id'=>'selectButton','class' => 'btn btn-info pull-left','type'=>'button']) !!}
                </div>

                <div class="col-sm-4" style="padding-right: 0px;">
                        {!! Form::label('closingDate', 'Closing Date:', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">                                
                        {!! Form::text('closingDate', null ,['class'=>'form-control', 'id' => 'closingDate','readonly','style'=>'cursor:pointer']) !!}
                        <p id='closingDatee' class="error" style="max-height:3px;color: red;"></p>
                        </div>
                </div>
                </div>
        </div>
    </div>
</div>  




<br>

    

        <table id="accountTable" class="table table-bordered responsive" style="color: black;">
            
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Account No</th>
                    <th>Account Name</th>
                    <th>Bank Name</th>
                    <th>Bank Branch Location</th>
                    <th>Principal Amount (Tk)</th>
                    <th>Net Interest Amount (Tk)</th>
                    <th>Total Amount (Tk)</th>
                    
                </tr>
                
            </thead>
            <tbody>
                
            </tbody>
            
        </table>
        <br>
         <div class="form-group">
                        <div class="col-sm-12 text-right" style="padding-right: 0px;">
                            {!! Form::button('Submit', ['id'=>'submitButton','class' => 'btn btn-info']) !!}
                            <a href="{{url('viewFdrAccountClose/')}}" class="btn btn-danger closeBtn">Close</a>
                        </div>
                </div>

   
    

</div>
<div class="footerTitle" style="border-top:1px solid white"></div>
</div>
<div class="col-md-1"></div>
</div>


<script type="text/javascript">
    $(document).ready(function() {


         function pad (str, max) {
              str = str.toString();
              return str.length < max ? pad("0" + str, max) : str;
            }

             function num(argument) {
              return argument.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
          }


        function getFilteredFdrAccounts() {
            var projectId = $("#project option:selected").val();
            var projectTypeId = $("#projectType option:selected").val();
            var branchId = $("#branch option:selected").val();
            var fdrType = $("#fdrType option:selected").val();
            var bank = $("#bank option:selected").val();
            var bankBranch = $("#bankBranch option:selected").val();
            var csrf = "{{csrf_token()}}";

            $.ajax({
                url: './getAccFdrFilteredAccount',
                type: 'POST',
                dataType: 'json',
                data: {projectId: projectId, projectTypeId: projectTypeId, branchId: branchId, fdrType: fdrType, bank: bank,bankBranch: bankBranch, _token: csrf },
            })
            .done(function(accounts) {
              if (accounts.accessDenied) {
                  showAccessDeniedMessage();
                  return false;
              }
                $("#accId").empty();
                $("#accId").append("<option value=''>Select Account</option>");

                $.each(accounts, function(index, account) {
                    $("#accId").append("<option value='"+account.id+"'>"+account.accNo+"</option>"); 
                });


                console.log("success");
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
            
        }/*End Filtered Function*/


        /* Change Project*/
         $("#project").change(function(){
            
            var projectId = $(this).val();

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProject',
                data: {projectId:projectId,_token: csrf},
                dataType: 'json',
                success: function( data ){
                  if (data.accessDenied) {
                      showAccessDeniedMessage();
                      return false;
                  }

                    $("#projectType").empty();
                    $("#projectType").prepend('<option selected="selected" value="">Select Project Type</option>');

                    $("#branch").empty();
                    $("#branch").prepend('<option selected="selected" value="">Select Branch</option>');
                   

                    $.each(data['projectTypeList'], function (key, projectObj) {
                                
                            $('#projectType').append("<option value='"+ projectObj.id+"'>"+pad(projectObj.projectTypeCode,3)+"-"+projectObj.name+"</option>");                       
                    });

                    $.each(data['branchList'], function (key, branchObj) {
                                
                            $('#branch').append("<option value='"+ branchObj.id+"'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>");                       
                    });


                    getFilteredFdrAccounts();
                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Project*/


          /* Change Project Type*/
         $("#projectType").change(function(){
            var projectId = $("#project option:selected").val();
            var projectTypeId = $(this).val();


            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProjectType',
                data: {projectId:projectId,projectTypeId: projectTypeId,_token: csrf},
                dataType: 'json',
                success: function( data ){ 
                  if (data.accessDenied) {
                      showAccessDeniedMessage();
                      return false;
                  }

                     $("#branch").empty();
                    $("#branch").prepend('<option selected="selected" value="">Select Branch</option>');
                    

                     $.each(data['branchList'], function (key, branchObj) {
                                
                            $('#branch').append("<option value='"+ branchObj.id+"'>"+pad(branchObj.branchCode,3)+"-"+branchObj.name+"</option>");
                    });

                     getFilteredFdrAccounts();

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Project Type*/


         /*Filter Branch Location On selecting Bank*/
         $("#bank").on('change', function() {
             var bank = $("#bank option:selected").val();
             var csrf = "{{csrf_token()}}";

             $.ajax({
                 url: './accFdrGetBranchLocationBaseOnBank',
                 type: 'POST',
                 dataType: 'json',
                 data: {bank: bank, _token: csrf},
             })
             .done(function(branch) {

              if (branch.accessDenied) {
                  showAccessDeniedMessage();
                  return false;
              }
                
                $("#bankBranch").empty();
                $("#bankBranch").append("<option value=''>Select Location</option>");
                $.each(branch, function(index, branch) {
                     $("#bankBranch").append("<option value='"+branch.id+"'>"+branch.name+'-'+branch.bankName+"</option>");
                });
               
                getFilteredFdrAccounts();
                 console.log("success");
             })
             .fail(function() {
                 console.log("error");
             })
             .always(function() {
                 console.log("complete");
             });
             
         });
         /*End Filter Branch Location On selecting Bank*/

       
         $("#fdrType,#bankBranch").on('change', function() {             
             getFilteredFdrAccounts();
         });


         /*On Click SelectButton*/
         $("#selectButton").on('click', function() {
             var accId = $("#accId option:selected").val();
             var csrf = "{{csrf_token()}}";

             if (accId!='') {

                $.ajax({
                 url: './fdrGetAccountInfo',
                 type: 'POST',
                 dataType: 'json',
                 data: {accId: accId, _token: csrf},
             })
             .done(function(data) {

              if (data.accessDenied) {
                  showAccessDeniedMessage();
                  return false;
              }

                var markup = "<tr><td><input id='tAccId' value='"+data['account'].id+"' style='display:none;'>"+data['openingDate']+"</td><td>"+data['account'].accNo+"</td><td>"+data['account'].accName+"</td><td>"+data['bankName']+"</td><td>"+data['bankBranchName']+"</td><td>"+num(data['account'].principalAmount)+"</td><td>"+num(data['netInterestAmount'])+"</td><td>"+num(data['totalAmount'])+"</td></tr>";

                $("#accountTable tbody").empty();
                $("#accountTable tbody").append(markup);

                if (data['lastInterestReceivedDate']!=null) {
                    
                    $("#closingDate").datepicker('option','minDate', new Date(data['lastInterestReceivedDate']));
                }
                else{
                    
                   $("#closingDate").datepicker('option','minDate', new Date(data['account'].matureDate)); 
                }

                

                 console.log("success");
             })
             .fail(function() {
                 console.log("error");
             })
             .always(function() {
                 console.log("complete");
             });

             }

             
             
         });
         /*End On Click SelectButton*/

          /*Receive Date*/
         $("#closingDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "2000:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function() {
                $("#closingDatee").empty();
            }
        });
         /*End Receive Date*/


         $("#submitButton").on('click',function() {
             var accId = $("#tAccId").val();
             var closingDate = $("#closingDate").val();
             var csrf = "{{csrf_token()}}";

             if (accId!=null) {
                $.ajax({
                    url: './storeFdrAccountClose',
                    type: 'POST',
                    dataType: 'json',
                    data: {accId: accId,closingDate: closingDate, _token: csrf},
                })
                .done(function(data) {
                  if (data.accessDenied) {
                      showAccessDeniedMessage();
                      return false;
                  }
                    if (data.errors) {
                        if (data.errors['closingDate']) {
                            $("#closingDatee").empty();
                            $("#closingDatee").append('* '+data.errors['closingDate']);
                        }
                    }
                    else{
                        location.href = "viewFdrAccountClose";
                    }
                    
                    console.log("success");
                })
                .fail(function() {
                    console.log("error");
                })
                .always(function() {
                    console.log("complete");
                });
                
             }
             
         });


        
    });/*End Ready*/
</script>


@endsection
