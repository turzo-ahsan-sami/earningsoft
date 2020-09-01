@extends('layouts/acc_layout')
@section('title', '| Mail Setting')
@section('content')


  @php
    use \App\Http\Controllers\microfin\savings\MfnSavingsDepositTypeController as createForm;

  @endphp


  <div class="row add-data-form">
    <div class="col-md-12">
            <div class="col-md-8 col-md-offset-1 fullbody">
              <div class="viewTitle" style="border-bottom:1px solid white;">
                    <!-- <a href="{{ url('/') }}" class="btn btn-info pull-right addViewBtn">
                      <i class="glyphicon glyphicon-th-list viewIcon"></i>
                      Mail List
                    </a> -->
                </div>
                <div class="panel panel-default panel-border">
                  <div class="panel-heading">
                        <div class="panel-title">Mail Receive List</div>
                  </div>
                  <div class="panel-body">
                   <div class="row">
                      <div class="col-md-12">
                        {!! Form::open(array('url' => '', 'role' => 'form', 'class' => 'form-horizontal form-groups')) !!}
                           <table id="mailSettingTable" class="table">
                              <thead>
                                <tr>
                                  <th width="50">SL#</th>
                                  <th width="600">Name</th>
                                  <th width="600">Email Address</th>
                                  <th width="70">Action</th>
                                </tr>
                              </thead>
                               <tbody>
                                   @if(count($emailRecipients)>0)
                                      @foreach($emailRecipients as $key=> $emailRecipient)
                                        <tr>
                                          <td>{{$key+1}}</td>
                                            <td>                                              
                                              <select class="form-control employeeName"  name="employeeId[]">
                                                   <option value="">Select Employee</option>
                                                @foreach($employees as $employee)
                                                   <option value="{{$employee->id}}"@if($employee->id==$emailRecipient->employeeIdFk){{"selected=selected"}}@endif>{{$employee->emp_id.'-'.$employee->emp_name_english}}</option>
                                                @endforeach
                                              </select> 


                                            </td>
                                            <td>
                                              
                                            {!! Form::text('email[]',$emailRecipient->email, ['class'=>'form-control emailAddress','autocomplete'=>'off']) !!}


                                            </td>
                                            <td>
                                              <a href="javascript:;" class="remove">
                                              <i class="fa fa-minus-circle" aria-hidden="true"></i>
                                                    </a>
                                            </td>
                                            </tr>
                                          

                                        @endforeach

                                        @else
                                            <tr>
                                            <td>1</td>
                                            <td>
                                              <select class="form-control employeeName"  name="employeeId[]">
                                                  <option value="">Select Employee</option>
                                                @foreach($employees as $employee)
                                                  <option value="{{$employee->id}}">{{$employee->emp_id.'-'.$employee->emp_name_english}}</option>

                                                @endforeach
                                              </select>        
                                            </td>

                                            <td>
                                              
                                              {!! Form::text('email',null, ['class'=>'form-control emailAddress']) !!}
                                         


                                            </td>
                                            <td>
                                             <a href="javascript:;" class="remove">
                                             <i class="fa fa-minus-circle" aria-hidden="true"></i> </a>
                                                   
                                            </td>
                                            </tr>

                                        @endif   
                                      </tbody>
                                    </table>
                                        <a href="javascript:;" id="addRow" class="btn btn-info add">
                                          <i class="fa fa-plus-circle" aria-hidden="true"></i>Add </a>
                                          <ul class="pager wizard pull-right">                             
                                            {!! Form::submit('Submit', ['id' => 'submit', 'class' => 'btn btn-info']) !!}
                                            <a href="{{ url('/') }}" class="btn btn-danger closeBtn">Close</a>
                                          </ul>                                    
                              
                            {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    </div>
  </div>

<style type="text/css">
    #mailSettingTable{
        color: black;
    }
    #mailSettingTable thead tr th{
        text-align: center;
        padding: 4px;
    }
    #mailSettingTable tbody tr td{
        text-align: center;
    }
    #mailSettingTable tbody tr td:nth-child(3) input,#mailSettingTable tbody tr td:nth-child(4) input{
        text-align: center;
    }
    #mailSettingTable tbody tr td:nth-child(2){
        width: 250px;
    }
    #mailSettingTable tbody tr td input{
        width: 100%;
    }
    .remove{
        color: #e84e4e;
        font-size: 13px;
    }
    .remove:hover{
        color: #c62727;
        font-size: 16px;
    }
    .add{
        background-color: #4c6b42 !important;
        border-radius: 5px;        
    }
    .add:hover{
        background-color: #34492d !important;
    }
    .ui-datepicker-year{
        display:none;
    }
    input{
        border: 1px solid black;
    }
</style>

<!-- <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
<script src="{{ asset('js/jquery-ui/jquery-ui.min.js') }}"></script>
 -->
<script type="text/javascript">
    $(document).ready(function() {
       /*add row*/
       $("#addRow").click(function(event) {
          var rowNum = $("#mailSettingTable tbody tr").length + 1;
          var appendRow = $('#mailSettingTable tbody tr:last').clone();
       $("#mailSettingTable tbody").append(appendRow);
          var rowCount = $('#mailSettingTable tbody tr').length;
          /*Row Count */
          $('#mailSettingTable tbody tr:last td:first').html(rowCount);
          $('#mailSettingTable tbody tr:last .employeeName').val('');
          $('#mailSettingTable tbody tr:last .emailAddress').val('');

    });
        /*end add row*/

        /*remove row*/
       $(document).on('click', '.remove', function() {
           if($('tbody tr').length > 1){
              $(this).closest('tr').remove();
              $("#mailSettingTable tbody tr").each(function(index, el) {
                $(el).find('td').eq(0).html($(el).index()+1);
            });
             //$(this).val($('.employeeName').index($(this).closest('.employeeName')) - 1);
           }else {
              alert("You Can't Delete This Row");
        }
      }); 

       /*function validateEmail($email) {
             var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
               return emailReg.test( $email );
        }*/

        function validateEmail(email) {
          var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
          return regex.test(email);
        }
  

        $('form').submit(function(event) {
            event.preventDefault();

            /*validate empty field*/
            var emptyFlag = 0;
            $('.employeeName').each(function(index, el) {
                if ($(el).val()=='') {
                    emptyFlag = 1;                   
                }
            });
            $('.emailAddress').each(function(index, el) {
                if ($(el).val()=='') {
                    emptyFlag = 1;                    
                }
            });

            if (emptyFlag==1) {
                alert('Please fill all the fields.');
                return false;
            }
            /*end validate empty field*/

            ////   Validate Email
            var emailValidationFlag = 1;
            $('.emailAddress').each(function(event) {
               if( !validateEmail($(this).val())) { 
                    emailValidationFlag = 0;
                }                
            });

            if (emailValidationFlag==0) {
                alert('Invalid emails');
                return false;
            }

            //// end validation email
            
            /// check duplidate email
            var values = [];
            var eamilUniqueFlag = 1;
            $('.emailAddress').each(function(event) {
               if ($.inArray(this.value, values) >= 0) {
                    eamilUniqueFlag = 0;                     
                }
                values.push(this.value);               
            });

            if (eamilUniqueFlag==0) {
                alert('Emails must be unique.');
                return false;
            }
            /// end checking duplidate email
            
            
            
            $.ajax({
                url:'./addMailSetting',
                type:'POST',
                dataType:'json',
                data:$(this).serialize(),
            })
            .done(function(data) {             
                toastr.success(data.responseText, data.responseTitle, opts);    
              setTimeout(function(){
                  window.location.href = 'viewMailSetting';
              }, 3000);

           })

            .fail(function() {
                alert("error");
            });
        });
      }); /*End Ready*/

   

    
</script>


<script type="text/javascript">

   $(document).ready(function() {

      //$('.employeeName option:selected').val();
     $(document).on('click','.employeeName',function() {
      $(".employeeName option").prop('disabled',false);
        var element = $(this);
       $(".employeeName").not(element).each(function(index,el){
            $(element).find('option[value="'+$(el).find('option:selected').val()+'"]').prop('disabled',true);
            $(element).find('option:first[value="'+$(el).find('option:selected').val()+'"]').prop('disabled',false);
       });
     });
 
});


</script>

@endsection