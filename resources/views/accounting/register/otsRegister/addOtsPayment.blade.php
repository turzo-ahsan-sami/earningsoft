@extends('layouts/acc_layout')
@section('title', '| OTS Register')
@section('content')

<div class="row add-data-form">
<div class="col-md-2"></div>
<div class="col-md-8 fullbody">
<div class="viewTitle" style="border-bottom: 1px solid white;">
    <a href="{{url('viewOtsPayment/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
    </i>OTS Payment List</a>
</div>

<div class="panel panel-default panel-border">
	<div class="panel-heading">
	    <div class="panel-title">OTS Interest Payment</div>
	</div>


<div class="panel-body">
            <div class="row">                
                <div class="col-md-12">
                {{-- {!! Form::open(array('url' => 'storeOtsPayment','id'=>'entryForm', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!} --}}
                <div class="form-horizontal form-groups">
                    
                
                
                    <div class="col-md-12">

                    <div class="form-group">
                            {!! Form::label('paymentId', 'Payment ID:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">                            
                                
                           {!! Form::text('paymentId', $paymentId, ['class' => 'form-control', 'id' => 'paymentId','style'=>'text-align:left;', 'type' => 'text','autocomplete'=>'off','readonly']) !!}
                                
                            </div>
                    </div>

                    <div class="form-group">
                            {!! Form::label('branchLocation', 'Branch:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                            @php
                                $branches = DB::table('gnr_branch')->select('id','name','branchCode')->get();
                            @endphp
                            <select id="branchLocation" name="branchLocation" class="form-control">
                                <option value="">Select Branch</option>
                                <option value="0">All</option>
                                @foreach($branches as $branch)
                                <option value="{{$branch->id}}">{{str_pad($branch->branchCode,3,'0',STR_PAD_LEFT).'-'.$branch->name}}</option>
                                @endforeach
                            </select>                              
                            </div>
                    </div>

                   

                    <div class="form-group">
                            {!! Form::label('accNature', 'Account Nature:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                            @php
                                $accNature = array(''=>'Select Account Nature') + DB::table('acc_ots_period')->pluck('name','id')->toArray();
                            @endphp
                                
                           {!! Form::select('accNature', $accNature,null ,['class'=>'form-control', 'id' => 'accNature']) !!}
                                
                            </div>
                    </div>
                    
                    <div class="form-group">
                            {!! Form::label('paymentDate', 'Payment Date:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">                            
                                
                           {!! Form::text('paymentDate', null, ['class' => 'form-control', 'id' => 'paymentDate','style'=>'text-align:left;cursor:pointer;', 'type' => 'text','autocomplete'=>'off','readonly']) !!}
                                
                            </div>
                    </div>
                   
                   {{--  <div class="form-group">
                            {!! Form::label('accNo', 'Account No:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                            @php
                                $accNo = array(''=>'Select Account') + DB::table('acc_ots_account')->pluck('accNo','id')->toArray();
                            @endphp
                                
                           {!! Form::select('accNo', $accNo,null ,['class'=>'form-control', 'id' => 'accNo']) !!}
                                
                            </div>
                    </div> --}}
                    
                
                </div>



                
                 <div class="form-group">
                        <div class="col-sm-12 text-right" style="padding-right:29px;">
                            {!! Form::button('Submit', ['class' => 'btn btn-info submitButton','type'=>'button']) !!}
                            <a href="{{url('viewOtsPayment/')}}" class="btn btn-danger closeBtn">Close</a>
                        </div>
                </div>

            {{-- {!! Form::close() !!} --}}
            </div>
                
            </div>

        </div>

<br>

    <p id="amountError" style="color:red;display: none;">*Payable Amount should be equal or less than Due Amount.</p>

        <table id="accountTable" class="table table-bordered responsive" style="color: black;">
            
            <thead>
                <tr>
                    
                    <th rowspan="2" width="60">SL#</th>
                    <th rowspan="2">Account No</th>
                    <th rowspan="2">Member Name</th>
                    <th rowspan="2">Account Nature</th>
                    <th rowspan="2">Principal Amount</th>
                    <th rowspan="2">Due Amount</th>
                    <th colspan="3" width="60">
                    {!! Form::label('checkUncheckAll', 'Check Box', ['class' => 'control-label']) !!} 
                    </th>
                    <th rowspan="2">Payable <br> Amount</th>
                    
                </tr>
                <tr>
                    <th width="20">
                    {!! Form::label('all', 'All', ['class' => 'control-label']) !!} 
                    @php
                        echo "<br>";
                    @endphp
                    {!! Form::checkbox('checkAll', 'value', true,['id'=>'checkAll']) !!}
                    </th>
                    <th width="20">
                    {!! Form::label('zero', 'Zero', ['class' => 'control-label']) !!} 
                    @php
                        echo "<br>";
                    @endphp
                    {!! Form::checkbox('checkAllzero', 'value', false,['id'=>'checkAllzero']) !!}
                    </th>
                    <th width="20">
                    {!! Form::label('partial', 'Partial', ['class' => 'control-label']) !!} 
                    @php
                        echo "<br>";
                    @endphp
                    {!! Form::checkbox('checkAllpartial', 'value', false,['id'=>'checkAllpartial']) !!}
                    </th>
                </tr>
            </thead>
            <tbody id="accountTableTbody">
                
            </tbody>
            <tfoot>
                <tr id="totalRow">
                   
                    <td colspan="4" style="text-align: center;">Total</td>
                    <td id="gTprincipal" class="amount"></td>
                    <td id="gTdue" class="amount"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    
                    <td id="gTpayable" class="amount"></td>
                    
                </tr>
            </tfoot>
        </table>
        <br>
         <div class="form-group">
                        <div class="col-sm-12 text-right" style="padding-right: 0px;">
                            {!! Form::button('Submit', ['class' => 'btn btn-info submitButton','type'=>'button']) !!}
                            <a href="{{url('viewOtsPayment/')}}" class="btn btn-danger closeBtn">Close</a>
                        </div>
                </div>

    </div>

</div>
<div class="footerTitle" style="border-top:1px solid white"></div>
</div>
<div class="col-md-2"></div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
       

        /*Submit the form*/
        $("form").submit(function(event) {

          
           
        });
        /*End Submit the form*/



        /*Validate Number Filed*/
        $("#payableAmount").on('input', function() {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
            var dueAmount = parseFloat($("#dueAmount").val());
            var payableAmount = parseFloat(this.value);
            if (payableAmount>dueAmount) {
                this.value = this.value.slice(0, -1);                    
                alert('Payable Amount should be less than Due Amount');
            }
            if ($("#dueAmount").val()=="") {
                this.value = '';
            }
            
        });
        /*End Validate Number Filed*/




        /*Filter Employee and Account Base on Branch*/
        $("#branchLocation").change(function() {
            var branchId = $("#branchLocation option:selected").val();
            var csrf = "{{csrf_token()}}";
            $.ajax({
                url: './getEmployeeBaseOnBranch',
                type: 'POST',
                dataType: 'json',
                data: {branchId: branchId, _token:csrf},
                success: function(employee) {
                    //alert(JSON.stringify(employee));
                    $("#employeeReference").empty();
                    $('#employeeReference').append("<option value=''>Select Employee</option>");
                    $.each(employee, function(index, emp) {
                         $('#employeeReference').append("<option value='"+ emp.id+"'>"+emp.emp_id+"-"+emp.emp_name_english+"</option>");
                    });
                    
                },
                error: function(argument) {
                    alert('response error')
                }
            });

             $.ajax({
                url: './getOtsAccountBaseOnBranch',
                type: 'POST',
                dataType: 'json',
                data: {branchId: branchId, _token:csrf},
                success: function(account) {
                    //alert(JSON.stringify(account));

                    $("#accNo").empty();
                    $('#accNo').append("<option value=''>Select Account</option>");
                    $.each(account, function(index, account) {
                         $('#accNo').append("<option value='"+ account.id+"'>"+account.accNo+"</option>");
                    });
                    
                },
                error: function(argument) {
                    alert('response error');
                }
            });
           
            
        });
        /*End Filter Employee and Account Base on Branch*/




        /*Filter Account Base on Employee*/
        $("#employeeReference").change(function() {
            var branchId = $("#branchLocation option:selected").val();
            var employeeId = $("#employeeReference option:selected").val();
            var csrf = "{{csrf_token()}}";
            $.ajax({
                url: './getOtsAccountBaseOnEmployee',
                type: 'POST',
                dataType: 'json',
                data: {branchId: branchId,employeeId: employeeId, _token:csrf},
                success: function(account) {
                    //alert(JSON.stringify(employee));
                    $("#accNo").empty();
                    $('#accNo').append("<option value=''>Select Account</option>");
                    $.each(account, function(index, account) {
                         $('#accNo').append("<option value='"+ account.id+"'>"+account.accNo+"</option>");
                    });
                    
                },
                error: function(argument) {
                    alert('response error');
                }
            });
        });

        /*End Filter Account Base on Employee*/

        function num(argument) {
            return argument.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }



        /*These variables will be used to check all check box is check or not*/
        var checkboxAll = 0;
        var checkboxZero = 0;
        var checkBoxPartial = 0;
        var flag = 0;




        /*Get Accounts if Branch Selected*/
         
         $("#branchLocation").on('change',function() {

            var branchId = $("#branchLocation option:selected").val();
            var periodId = $("#accNature option:selected").val();
            var csrf = "{{csrf_token()}}";
            $.ajax({
                url: './getOtsAccountPaymentData',
                type: 'POST',
                dataType: 'json',
                data: {branchId: branchId,periodId: periodId, _token:csrf},
                success: function(data) {
                    //alert(JSON.stringify(employee));
                   
                    
                    $("#accountTableTbody").empty();
                    
                    var grandtotalPricipal = 0;
                    var grandtotalDue = 0;
                    var grandtotalPayable = 0;
                    
                    count =1;                 
                    $.each(data['accounts'], function(index, account) {

                        grandtotalPricipal = grandtotalPricipal + account.amount;


                        var totalDue = 0;
                        var totalPayment = 0;

                        $.each(data['totalDue'], function(index2, totalDueOb) {
                             if (totalDueOb.accId_fk==account.id) {
                                totalDue = totalDue + totalDueOb.amount;
                             }
                        });

                        $.each(data['payments'], function(index3, paymentOb) {
                             if (paymentOb.accId_fk==account.id) {
                                totalPayment = totalPayment + paymentOb.amount;
                             }
                        });

                        totalDue = totalDue + account.openingBalance - totalPayment;

                         var markup = "<tr accId='"+account.id+"'><td>"+count+"</td><td>"+account.accNo+"</td><td class='name'>"+account.memberName+"</td><td>"+account.accNature+"</td><td class='amount'>"+num(account.amount)+"</td><td class='amount'><input style='display:none;' class='amountTextBackup' value='"+totalDue+"'>"+num(totalDue)+"</td><td><input class='accCheckboxAll' type='checkbox' name='account' value='"+account.id+"' checked></td><td><input class='accCheckboxZero' type='checkbox' name='account' value='"+account.id+"'></td><td><input class='accCheckboxPartial' type='checkbox' name='account' value='"+account.id+"'></td><td><input class='amountText' type='text' value='"+totalDue+"' readonly></td></tr>";
                        $("#accountTableTbody").append(markup);
                        count++;


                        grandtotalDue = grandtotalDue + totalDue;
                        grandtotalPayable = grandtotalPayable + totalDue;
                    });


                    $("#gTprincipal").html(num(grandtotalPricipal));
                    $("#gTdue").html(num(grandtotalDue));
                    $("#gTpayable").html(num(grandtotalPayable));


                    /*Some Initial Works*/
                    checkboxAll = $(".accCheckboxAll:checkbox:checked").length;
                    checkboxZero = checkboxAll;
                    
                    flag = $(".accCheckboxAll:checkbox:checked").length;
                    

                    $(".accCheckboxZero").attr("disabled", true);
                    $(".accCheckboxPartial").attr("disabled", true);

                    $("#checkAll").prop("checked", true);
                    $("#checkAllzero").prop("checked", false);
                    $("#checkAllpartial").prop("checked", false);



                },
                error: function(argument) {
                    alert('response error');
                }
            });
        });
        /*End Get Accounts if Branch Selected*/


        $("#accNature").on('change', function() {
            var branchId = $("#branchLocation option:selected").val();
            if (branchId!="") {
                $("#branchLocation").trigger('change');
            }
        });


        function calculateTotal() {

            var total = 0;
            
             $("#accountTable > tbody > tr").each(function(index, row) {
                    var amount = 0;
                    if ($(this).closest('tr').find('.amountText').val()!="") {
                       amount = parseFloat($(this).closest('tr').find('.amountText').val()); 
                    }
                    
                    total = total + amount;
                });
              $("#gTpayable").html(num(total));
        }


        $(document).on('input', '.amountText', function(event) {
            $(this).css('color', 'black');
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
            var dueAmount = parseFloat($(this).closest('tr').find('.amountTextBackup').val());
            var thisAmount = 0;
            if (this.value!="") {
                thisAmount = parseFloat(this.value);
            }
            

            if (thisAmount>dueAmount) {
                this.value = 0;
                $("#amountError").show();
                $(this).css('color', 'red');
                $(this).attr('hasError', '1');
            }


            calculateTotal();
        });

       




        function checkPartial() {
            if ($("#checkAllpartial").is(':checked')) {
                    $(".accCheckboxPartial").attr("disabled", false);
                    
                }
                else{
                    $(".accCheckboxPartial").prop("checked", false);
                    $(".accCheckboxPartial").attr("disabled", true);
                    $('.amountText').attr("readonly", true);
                    
                }
        }


    /*Check Boxes*/

    $("#checkAll").click(function () {
        if ($("#checkAll").is(':checked')) {
            $(".accCheckboxAll").attr("disabled", false);
            $(".accCheckboxAll").prop("checked", true);

            $("#checkAllzero").attr("checked", false);
            $(".accCheckboxZero").prop("checked", false);
            $(".accCheckboxZero").attr("disabled", true);

            $("#accountTable > tbody > tr").each(function(index, row) {
                var amount = $(this).closest('tr').find('.amountTextBackup').val();
                $(this).closest('tr').find('.amountText').val(amount);
            });

            $("#checkAllpartial").prop("checked", false);
            checkPartial();
        }
        else{
            $(".accCheckboxAll").prop("checked", false);
        }
    });

    $("#checkAllzero").click(function () {
        if ($("#checkAllzero").is(':checked')) {
            $(".accCheckboxZero").attr("disabled", false);
            $(".accCheckboxZero").prop("checked", true);

            $("#checkAll").prop("checked", false);
            $(".accCheckboxAll").prop("checked", false);
            $(".accCheckboxAll").attr("disabled", true); 

            $(".amountText").val(0);
            $("#gTpayable").html(num(0));

            $("#checkAllpartial").prop("checked", false);
            checkPartial();         
        }
        else{
            $(".accCheckboxZero").prop("checked", false);
        }
    });




    $("#checkAllpartial").on('click', function() {
         if ($("#checkAllpartial").is(':checked')) {
            $(".accCheckboxPartial").attr("disabled", false);
                       
        }
        else{
            $(".accCheckboxPartial").prop("checked", false);
            $(".accCheckboxPartial").attr("disabled", true);

            if ($("#checkAll").is(':checked')) {
                $("#accountTable > tbody > tr").each(function(index, row) {
                    var amount = $(this).closest('tr').find('.amountTextBackup').val();
                    $(this).closest('tr').find('.amountText').val(amount);
                });
            }
            else{
                $(".amountText").val('0');
            }

            calculateTotal();
        }
        
    });



/*
    $("#checkAllpartial").click(function () {
        if ($("#checkAllpartial").is(':checked')) {
            $(".accCheckboxPartial").attr("disabled", false);
                       
        }
        else{
            $(".accCheckboxPartial").prop("checked", false);
            $(".accCheckboxPartial").attr("disabled", true);
        }
    });*/



    
/////Class checkBox

    $(document).on('click', '.accCheckboxAll', function(event) {
        
        if ($(this).is(':checked')) {
            checkboxAll++;
            $(this).closest('tr').find('.amountText').val($(this).closest('tr').find('.amountTextBackup').val());
            calculateTotal();
        }
        else{
           checkboxAll--;
           $(this).closest('tr').find('.amountText').val('0');
           calculateTotal();
        }

        if (checkboxAll<flag) {
            $("#checkAll").prop("checked", false);
        }
        else{
            $("#checkAll").prop("checked", true);
        }  
    });

    $(document).on('click', '.accCheckboxZero', function(event) {
        
        if ($(this).is(':checked')) {
            checkboxZero++;
        }
        else{
           checkboxZero--; 
        }

        if (checkboxZero<flag) {
            $("#checkAllzero").prop("checked", false);
        }
        else{
            $("#checkAllzero").prop("checked", true);
        }  
    });

    $(document).on('click', '.accCheckboxPartial', function(event) {
        
        if ($(this).is(':checked')) {
            
            $(this).closest('tr').find('.amountText').attr("readonly", false);
        }
        else{
           $(this).closest('tr').find('.amountText').attr("readonly", true);

           if ($("#checkAll").is(':checked')) {
               $(this).closest('tr').find('.amountText').val($(this).closest('tr').find('.amountTextBackup').val());
               calculateTotal();
           }
           else{
                $(this).closest('tr').find('.amountText').val('0');
                calculateTotal();
           }
        }

       
    });



    /*End Check Boxes*/



    /*Save the Data*/
    $(".submitButton").on('click', function(event) {
        event.preventDefault();
        var accId = new Array();
        var dueAmount = new Array();
        var payableAmount = new Array();
        var paymentDate = $("#paymentDate").val();
        var csrf = "{{csrf_token()}}";
        
        $("#accountTable > tbody > tr").each(function(index, el) {
            accId.push(JSON.stringify($(this).attr('accId')));
            dueAmount.push(JSON.stringify($(this).find('.amountTextBackup').val()));
            payableAmount.push(JSON.stringify($(this).find('.amountText').val()));
        });

        if (accId.length>0) {
             $.ajax({
            type: 'post',
            url: './storeOtsPayment',
            data: {accId: accId, dueAmount: dueAmount, payableAmount: payableAmount, paymentDate: paymentDate, _token: csrf},
            
            dataType: 'json',
            success: function( _response ) {
                window.location.href = "viewOtsPayment";
                
                return false;
            },
            error: function( _response ){
            // Handle error
            //alert('_response.errors');
            }
        }); /*End Ajax*/
        }
        
    });
    /*End Save the Data*/



     $("#paymentDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "1995:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy'
            
        });




    });/*End Ready*/


</script>


<style type="text/css">
    #accountTable tbody tr td.amount,tfoot tr td.amount { text-align: right;padding-right: 5px; }
    #accountTable tbody tr td.name { text-align: left;padding-left: 5px; }
    #accountTable thead tr th { margin: 0px !important; padding: 0px !important;}
    input { text-align: center; width: 80px; }
</style>


@endsection
