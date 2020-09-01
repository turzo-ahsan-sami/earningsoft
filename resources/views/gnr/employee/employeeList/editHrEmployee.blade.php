@extends($route['layout'])
@section('title', '| Employee')
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
           <div class="col-md-1"></div>
            <div class="col-md-10 fullbody">
                <div class="viewTitle" style="border-bottom: 1px solid white;">
                    <a href="{{url($route['path'].'/posHrEmployeeList/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                    </i>Employee List</a>
                </div>
                <div class="panel panel-default panel-border">
                    <div class="panel-heading">
                        <div class="panel-title">Edit Employee</div>
                    </div>
                    <div class="panel-body">
                                    {!! Form::open(array('url' => $route['path'].'/posAddHrEmployee', 'enctype' =>'multipart/form-data', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                <div class="row">
                                    <div><h3><u>Personal Info</u></h3></div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('emp_id', 'Employee ID :', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('emp_id', $employee->employeeId, ['class' => 'form-control', 'id' => 'emp_id', 'type' => 'text', 'placeholder' => 'Employee ID','autocomplete'=>'off']) !!}
                                                <p id='emp_ide' style="max-height:4px; color:red;"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('father_name', 'Father &#39;s Name :', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('father_name', $employee->fatherName, ['class' => 'form-control', 'id' => 'father_name', 'type' => 'text', 'placeholder' => 'Father Name','autocomplete'=>'off']) !!}
                                                <p id='father_namee' style="max-height:4px; color:red;"></p>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            {!! Form::label('paymentType', 'Sex:', ['class' => 'col-md-4 control-label']) !!}
                                            <div class="col-md-8">
                                                 <label><input type="radio" class="radio_button" id="sex_male" name="sex" value="Male" {{ ($employee->gender=="Male")? "checked" : "" }}> Male </label>
                                                <label><input type="radio" class="radio_button" id="sex_female" name="sex" value="Female" {{ ($employee->gender=="Female")? "checked" : "" }}> Female </label>
                                                <p id='sexe' style="max-height:4px; color:red;"></p>
                                            </div> 
                                            
                                        </div>
                                        
                                        <div class="form-group">
                                            {!! Form::label('date_of_birth', 'Date of Birth :', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('date_of_birth', $employee->dateOfBirth, ['class' => 'form-control', 'id' => 'date_of_birth', 'placeholder' => 'Date of Birth','autocomplete'=>'off']) !!}
                                                <p id='date_of_birthe' style="max-height:4px; color:red;"></p>
                                            </div>
                                        </div>
                                        
                                       
                                         <h4><u>Present Address</u></h4>
                                        <div class="form-group">
                                            {!! Form::label('present_address', 'Address :', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::textarea('present_address', $employee->presentAddress, ['class' => 'form-control', 'id' => 'present_address', 'placeholder' => 'Present Address','autocomplete'=>'off','cols'=>'50','rows'=>'3']) !!}
                                                <p id='present_addresse' style="max-height:4px; color:red;"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('image', 'Upload Image:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::file('image', $value = null, ['class' => 'form-control', 'id' => 'image', 'type' => 'file']) !!}
                                               </br>
                                                <p id='imagee' style="max-height:3px;"></p>
                                                 <img src="{{ asset("images/employee/$employee->image") }}" width="60" id="blah">
                                            </div>
                                        </div>
                                   </div>
                                       
                                   <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('employeeName', 'Employee Name:', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                 {!! Form::text('employeeName', $employee->name, ['class' => 'form-control', 'id' => 'employeeName', 'type' => 'text','placeholder' =>'Employee Name','autocomplete'=>'off']) !!}
                                                <p id='employeeNamee' style="max-height:4px; color:red;"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('nid_no', 'NID No. :', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('nid_no', $employee->nationalId, ['class' => 'form-control', 'id' => 'nid_no', 'type' => 'text', 'placeholder' => 'NID No.','autocomplite'=>'off','autocomplete'=>'off']) !!}
                                                <p id='nid_noe' style="max-height:4px; color:red;"></p>
                                            </div>
                                            <input type="hidden" name="id" value="{{$employee->id}}">
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('mobile_no', 'Mobile :', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('mobile_no',  $employee->phone, ['class' => 'form-control', 'id' => 'mobile_no', 'type' => 'text', 'placeholder' => 'Mobile No','autocomplete'=>'off']) !!}
                                                <p id='mobile_noe' style="max-height:4px; color:red;"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('email', 'Email :', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('email',  $employee->email, ['class' => 'form-control', 'id' => 'email', 'type' => 'text', 'placeholder' => 'Email','autocomplete'=>'off']) !!}
                                                <p id='emaile' style="max-height:4px; color:red;"></p>
                                            </div>
                                        </div>

                                         <h4>
                                            <u>Permanent Address</u> <span style="font-size: 12px;">
                                            <input type="checkbox" name="sameasaddress"> Same as Present Address</span>
                                        </h4>
                                        <div class="form-group">
                                            {!! Form::label('permanent_address', 'Address :', ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::textarea('permanent_address',  $employee->presentAddress, ['class' => 'form-control', 'id' => 'permanent_address', 'placeholder' => 'Permanent Address','autocomplete'=>'off','cols'=>'50','rows'=>'3']) !!}
                                                <p id='permanent_addresse' style="max-height:4px; color:red;"></p>
                                            </div>
                                        </div>
                                </div>
                            </div>
                            <div class="row">
                                <div><h3><u>Organization Info</u></h3></div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('company_id_fk', 'Company:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            <?php
                                                if(Auth::user()->user_type == 'master'){
                                                    $gnrCompanys = DB::table('gnr_company')->where('customer_id',Auth::user()->customer_id)->get();
                                                }else{
                                                    $gnrCompanys = DB::table('gnr_company')->where('id',Auth::user()->company_id_fk)->get();
                                                }   
                                            ?>    
                                         
                                           <select class="form-control" id="company_id_fk" name="company_id_fk">
                                               <option value="">Select company</option>
                                               @foreach($gnrCompanys as $gnrCompany)
                                                     <option value="{{$gnrCompany->id}}" {{$employee->company_id_fk == $gnrCompany->id ? 'selected' : ''}}>{{$gnrCompany->name}}</option>
                                               @endforeach
                                           </select>
                                            <p id='company_id_fke' style="max-height:3px; color:red;"></p>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        {!! Form::label('branch_id_fk', 'Branch:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                             <select class="form-control" id="branch_id_fk" name="branch_id_fk">
                                                <?php 
                                                $gnrbranches= DB::table('gnr_branch')->where('companyId',Auth::user()->company_id_fk)->get();
                                                ?>        
                                                @foreach($gnrbranches as $gnrbranch)
                                                     <option value="{{$gnrbranch->id}}" {{$employee->branchId == $gnrbranch->id ? 'selected' : ''}}>{{$gnrbranch->name}}</option>
                                               @endforeach
                                            </select>    
                                            <p id='branch_id_fke' style="max-height:3px; color:red;"></p>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="status" class="col-sm-4 control-label">Status</label>
                                        <div class="col-sm-8">
                                            <select class="form-control" id="status" name="status">

                                                <option value="1" {{ ($employee->status=="1")? "selected" : "" }}>Active</option>
                                                <option value="0" {{ ($employee->status=="0")? "selected" : "" }}>Inactive</option>
                                            </select>
                                            <p id='status' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                     
                                     <div class="form-group">
                                        {!! Form::label('department_id_fk', 'Department:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">    
                                           <select class="form-control" id="department_id_fk" name="department_id_fk">
                                               <option value="">Select any</option>
                                               @foreach($departments as $gnrdepartment)
                                                     <option value="{{$gnrdepartment->id}}" {{$employee->department_id_fk == $gnrdepartment->id ? 'selected' : ''}}>{{$gnrdepartment->name}}</option>
                                               @endforeach
                                           </select>
                                            <p id='department_id_fke' style="max-height:3px; color:red;"></p>
                                        </div>
                                    </div>
                                     <div class="form-group">
                                        {!! Form::label('position_id_fk', 'Position:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                          <select class="form-control" name="position_id_fk" id="position_id_fk">
                                             <?php 
                                                $gnrpositions= DB::table('gnr_position')->where('companyId',Auth::user()->company_id_fk)->get();
                                                ?>   
                                                @foreach($gnrpositions as $gnrposition)
                                                     <option value="{{$gnrposition->id}}" {{$employee->position_id_fk == $gnrposition->id ? 'selected' : ''}}>{{$gnrposition->name}}</option>
                                                @endforeach
                                          </select>
                                            <p id='position_id_fke' style="max-height:3px; color:red;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                <div class="form-group">
                                    {!! Form::label('submit', ' ', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {!! Form::submit('Update', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                        <a href="{{url($route['path'].'/posHrEmployeeList/')}}" class="btn btn-danger closeBtn">Close</a>
                                        <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                                    </div>
                                </div>
                            {!! Form::close() !!}
                    </div>
                </div>
            <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>
{{-- company wise branch --}}
<script type="text/javascript">
    $(function(){
        $(document).on('change','#company_id_fk',function(){
            if($(this).val() != ''){
                var company_id_fk = $(this).val();
                $.ajax({
                    type:"GET",
                    url:'../getBranchInfo',
                    data:{company_id_fk:company_id_fk},
                    success:function(data){
                        var branch = '<option value="">Select any</option>';
                        var branches = data.branches;
                    
                        $.each(branches,function(key,v){
                            branch += '<option value="'+v.id+'">'+v.name+'</option>';
                        });

                        $('#branch_id_fk').html(branch); 
                        
                        var department = '<option value="">Select any</option>';
                        var departments = data.departments;

                        $.each(departments,function(key,v){
                            department += '<option value="'+v.id+'">'+v.name+'</option>';
                        });

                        $('#department_id_fk').html(department); 

                    
                    }
                });
            }
        });
    });
</script>
<script type="text/javascript">
     function isPasswordMatch() {
    var password = $("#user_password").val();
    var confirmPassword = $("#txtConfirmPassword").val();

    if (password != confirmPassword) $("#cPassword").html("Passwords do not match!");
    else $("#cPassword").html("Passwords match.");
    }

    $(document).ready(function () {
        $("#txtConfirmPassword").keyup(isPasswordMatch);
    });
    function sameasaddress(){
            //sameasaddress
            var pre_div_id = $('select[name="pre_div_id"]');
            var pre_dis_id = $('select[name="pre_dis_id"]');
            var pre_upa_id = $('select[name="pre_upa_id"]');
            var pre_uni_id = $('select[name="pre_uni_id"]');
            var present_address = $('textarea[name="present_address"]');

            var per_div_id = $('select[name="per_div_id"]');
            per_div_id.val(pre_div_id.val());

            var per_dis_id = $('select[name="per_dis_id"]');
            per_dis_id.html(pre_dis_id.html());
            per_dis_id.val(pre_dis_id.val());

            var per_upa_id = $('select[name="per_upa_id"]');
            per_upa_id.html(pre_upa_id.html());
            per_upa_id.val(pre_upa_id.val());

            var per_uni_id = $('select[name="per_uni_id"]');
            per_uni_id.html(pre_uni_id.html());
            per_uni_id.val(pre_uni_id.val());

            var permanent_address = $('textarea[name="permanent_address"]');
            permanent_address.val(present_address.val());
        }

        $(document).on('click','input[name="sameasaddress"]',function(){
            if($(this).is(':checked')){
                sameasaddress();
            }
        });
 $(function(){
    $( "#date_of_birth,#joining_date" ).datepicker({
      dateFormat: "yy-mm-dd",  
      changeMonth: true,
      changeYear: true,
      showOtherMonths: true,
      selectOtherMonths: true,
      maxDate: "0",
      yearRange: "-100:+0"
    });
//== START PRESENT ADDRESS FILTERING==============
    
//  $('#company_id_fk').on('change',function(){
//         if($(this).val() != ''){
//             var company_id_fk = $(this).val();
//             $.ajax({
//                 url:'../getBranchInfo',
//                 type: 'GET',
//                 data: {company_id_fk:company_id_fk},
//                 dataType: 'json',
//                 success: function(data) {
//                     $("#branch_id_fk").empty(); 
//                     $("#branch_id_fk option[value!='']").remove();
//                      $("#branch_id_fk").append("<option value=''>Select branch</option>"); 
//                      $.each(data,function(key,value){
//                         var name = value.name;
//                         var id = value.id;
//                         $("#branch_id_fk").append("<option value='" + id + "'>" + name + "</option>");
//                 });
//                 }
//             });
//         }else{
//             $("#branch_id_fk").append("<option value=''>Select branch</option>"); 
//         }
//     });

     $('#department_id_fk').on('change',function(){
        if($(this).val() != ''){
            var department_id_fk = $(this).val();
            $.ajax({
                url:'../getPositionInfo',
                type: 'GET',
                data: {department_id_fk:department_id_fk},
                dataType: 'json',
                success: function(data) {
                     $("#position_id_fk").empty(); 
                     $("#position_id_fk").append("<option value=''>Select position</option>"); 
                    
                      $("#position_id_fk option[value!='']").remove();
                     $.each(data,function(key,value){
                        var name = value.name;
                        var id = value.id;
                        $("#position_id_fk").append("<option value='" + id + "'>" + name + "</option>");
                });
                }
            });
        }
    });

//image Show

function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    
    reader.onload = function(e) {
      $('#blah').attr('src', e.target.result);
    }
    
    reader.readAsDataURL(input.files[0]); // convert to base64 string
  }
}

$("#image").change(function() {
  readURL(this);
})


//=====END ADDRESS FILTERING==============
});

    $('form').submit(function(event) {
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            
            $.ajax({
                type: 'post',
                 url: '../updateHrEployeeInfo',
                 data: formData,
                 async: false,
                 cache: false,
                 contentType: false,
                 processData: false,
                 dataType: 'json',  

            })
            .done(function(data) {
                if(data.errors)  {
                    if (data.errors['emp_id']) {
                        $("#emp_ide").empty();
                        $("#emp_ide").append('*'+data.errors['emp_id']);
                    } 
                    if (data.errors['sex']) {
                        $("#sexe").empty();
                        $("#sexe").append('*'+data.errors['sex']);
                    } 
                   
                    if (data.errors['company_id_fk']) {
                        $("#company_id_fke").empty();
                        $("#company_id_fke").append('*'+data.errors['company_id_fk']);
                    }  
                    
                    if (data.errors['position_id_fk']) {
                        $("#position_id_fke").empty();
                        $("#position_id_fke").append('*'+data.errors['position_id_fk']);
                    }  
                   
                    if (data.errors['employeeName']) {
                        $("#employeeNamee").empty();
                        $("#employeeNamee").append('*'+data.errors['employeeName']);
                    }
                    if (data.errors['project_id_fk']) {
                        $("#project_id_fke").empty();
                        $("#project_id_fke").append('*'+data.errors['project_id_fk']);
                    }
                    if (data.errors['branch_id_fk']) {
                        $("#branch_id_fke").empty();
                        $("#branch_id_fke").append('*'+data.errors['branch_id_fk']);
                    } 
                    if (data.errors['department_id_fk']) {
                        $("#department_id_fke").empty();
                        $("#department_id_fke").append('*'+data.errors['department_id_fk']);
                    } 

                } else  {
                    $("#add").prop("disabled", true);
                    window.location.href = '{{url($route['path'].'/posHrEmployeeList/') }}';
                }
            });

           $(document).on('input','input',function() {
               $(this).closest('div').find('p').remove();
            });
            $(document).on('input','textarea',function() {
               $(this).closest('div').find('p').remove();
            });


           $(document).on('change','select',function() {
               $(this).closest('div').find('p').remove();
           });

           $(document).on('change','input:radio',function() {
               $(this).closest('div').find('p').remove();
            });


          });
 
 </script>
@endsection