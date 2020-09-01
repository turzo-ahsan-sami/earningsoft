@extends($route['layout'])
@section('title', '| Employee')
@php
    //dd(Auth::user()->user_type);
@endphp
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
                    <div class="panel-title">New Employee</div>
                </div>
                <div class="panel-body">
                    {!! Form::open(array('url' => $route['path'].'/posAddHrEmployee', 'enctype' =>'multipart/form-data', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                    <div class="row">
                        <div><h3><u>Personal Info</u></h3></div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('emp_id', 'Employee ID :', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('emp_id', $value = null, ['class' => 'form-control', 'id' => 'emp_id', 'type' => 'text', 'placeholder' => 'Employee ID','autocomplete'=>'off']) !!}
                                    <p id='emp_ide' style="max-height:4px; color:red;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('father_name', 'Father &#39;s Name :', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('father_name', $value = null, ['class' => 'form-control', 'id' => 'father_name', 'type' => 'text', 'placeholder' => 'Father Name','autocomplete'=>'off']) !!}
                                    <p id='father_namee' style="max-height:4px; color:red;"></p>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                {!! Form::label('paymentType', 'Sex:', ['class' => 'col-md-4 control-label']) !!}
                                <div class="col-md-8">
                                    <label><input type="radio" class="radio_button" id="sex_male" name="sex" value="Male"> Male </label>
                                    <label><input type="radio" class="radio_button" id="sex_female" name="sex" value="Female"> Female </label>
                                    <p id='sexe' style="max-height:4px; color:red;"></p>
                                </div>
                                
                            </div>
                            
                            <div class="form-group">
                                {!! Form::label('date_of_birth', 'Date of Birth :', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('date_of_birth', $value = null, ['class' => 'form-control', 'id' => 'date_of_birth', 'placeholder' => 'Date of Birth','autocomplete'=>'off']) !!}
                                    <p id='date_of_birthe' style="max-height:4px; color:red;"></p>
                                </div>
                            </div>
                            
                            
                            <h4><u>Present Address</u></h4>
                            <div class="form-group">
                                {!! Form::label('present_address', 'Address :', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::textarea('present_address', $value = null, ['class' => 'form-control', 'id' => 'present_address', 'placeholder' => 'Present Address','autocomplete'=>'off','cols'=>'50','rows'=>'3']) !!}
                                    <p id='present_addresse' style="max-height:4px; color:red;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('image', 'Upload Image:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::file('image', $value = null, ['class' => 'form-control', 'id' => 'image', 'type' => 'file']) !!}
                                    <p id='imagee' style="max-height:4px;"></p>
                                    <p id='imagee' style="max-height:3px;"></p>
                                    <img src="" width="60" id="blah">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('employeeName', 'Employee Name:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('employeeName', $value = null, ['class' => 'form-control', 'id' => 'employeeName', 'type' => 'text','placeholder' =>'Employee Name','autocomplete'=>'off']) !!}
                                    <p id='employeeNamee' style="max-height:4px; color:red;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('nid_no', 'NID No. :', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('nid_no', $value = null, ['class' => 'form-control', 'id' => 'nid_no', 'type' => 'text', 'placeholder' => 'NID No.','autocomplite'=>'off','autocomplete'=>'off']) !!}
                                    <p id='nid_noe' style="max-height:4px; color:red;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('mobile_no', 'Mobile :', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('mobile_no', $value = null, ['class' => 'form-control', 'id' => 'mobile_no', 'type' => 'text', 'placeholder' => 'Mobile No','autocomplete'=>'off']) !!}
                                    <p id='mobile_noe' style="max-height:4px; color:red;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('email', 'Email :', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('email', $value = null, ['class' => 'form-control', 'id' => 'email', 'type' => 'text', 'placeholder' => 'Email','autocomplete'=>'off']) !!}
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
                                        {!! Form::textarea('permanent_address', $value = null, ['class' => 'form-control', 'id' => 'permanent_address', 'placeholder' => 'Permanent Address','autocomplete'=>'off','cols'=>'50','rows'=>'3']) !!}
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
                                            <option value="{{$gnrCompany->id}}" {{($gnrCompany->id == Auth::user()->company_id_fk) ? 'selected' : ''}}>{{$gnrCompany->name}}</option>
                                            @endforeach
                                        </select>
                                        <p id='company_id_fke' style="max-height:3px; color:red;"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('branch_id_fk', 'Branch:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        <select class="form-control" id="branch_id_fk" name="branch_id_fk">
                                            <option value="">Select Branch</option>
                                        </select>
                                        <p id='branch_id_fke' style="max-height:3px; color:red;"></p>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="status" class="col-sm-4 control-label">Status</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" id="status" name="status">
                                            <option value="1" selected="selected">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                        <p id='status' style="max-height:3px;"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">                              
                               @if(Auth::user()->user_type == 'master')
                               <div class="form-group">
                                    {!! Form::label('department_id_fk', 'Department:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        <select class="form-control" name="department_id_fk" id="department_id_fk">
                                            <option value="">Select any</option>
                                        </select>
                                        
                                        <p id='department_id_fke' style="max-height:3px; color:red;"></p>
                                    </div>
                                </div>

                                @else
                                <div class="form-group">
                                    {!! Form::label('department_id_fk', 'Department:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        <select class="form-control" name="department_id_fk" id="department_id_fk">
                                            <option value="">Select any</option>
                                            @foreach($departments as $department)
                                            <option value="{{$department->id}}">{{$department->name}}</option>
                                            @endforeach
                                        </select>
                                        
                                        <p id='department_id_fke' style="max-height:3px; color:red;"></p>
                                    </div>
                                </div>
                               @endif
                                <div class="form-group">
                                    {!! Form::label('position_id_fk', 'Position:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        <select class="form-control" name="position_id_fk" id="position_id_fk">
                                            <option value="">Select any</option>
                                        </select>
                                        <p id='position_id_fke' style="max-height:3px; color:red;"></p>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="row">
                            <div><h3><u>Login Info</u></h3></div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('user_password', 'Password :', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {!! Form::text('user_password', $value = null, ['class' => 'form-control', 'id' => 'user_password', 'type' => 'text', 'placeholder' => 'user Password','autocomplete'=>'off']) !!}
                                        <p id='user_passworde' style="max-height:4px; color:red;"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('txtConfirmPassword', 'Confirm Password :', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        <!-- {!! Form::text('cPassword', $value = null, ['class' => 'form-control', 'id' => 'cPassword', 'type' => 'text', 'placeholder' => 'Confirm password','autocomplete'=>'off']) !!} -->
                                        <input type="password" name="RNPassword" placeholder="confirm  Password" id="txtConfirmPassword" onChange="isPasswordMatch();" class="form-control" />
                                        <p id='cPassworde' style="max-height:4px; color:red;"></p>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="form-group">
                            {!! Form::label('submit', ' ', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::submit('Save', ['id' => 'add', 'class' => 'btn btn-info']); !!}
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
                var company_id_fk = $(this).val();
                //var company_id_fk = $(this).val();
                $.ajax({
                    type:"GET",
                    url:'./getBranchInfo',
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
            });
        });
    </script>

    {{-- department wise position --}}
    <script type="text/javascript">
        $(function(){
            $(document).on('change','#department_id_fk',function(){
                var department_id_fk = $(this).val();
                $.ajax({
                    type:"GET",
                    url:'./getPositionInfo',
                    data:{department_id_fk:department_id_fk},
                    success:function(data){
                        var html = '<option value="">Select any</option>';
    
                        $.each(data,function(key,v){
                             html += '<option value="'+v.id+'">'+v.name+'</option>';
                        });
    
                        $('#position_id_fk').html(html);   
                    }
                });
            });
        });
    </script>
    <script type="text/javascript">
    function isPasswordMatch() {
        var password = $("#user_password").val();
        var confirmPassword = $("#txtConfirmPassword").val();
       
        if (password != confirmPassword) $("#cPassworde").html("Passwords do not match!");
        else $("#cPassworde").html("Passwords match.");
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
    //== START image show==============
 
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
   
    });
    
    // Submit employee form
   
    $('form').submit(function(event) {
        event.preventDefault();
        var formData = new FormData($(this)[0]);
            $.ajax({
                type: 'post',
                url: './storeEmployeeItem',
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
                if (data.errors['father_name']) {
                $("#father_namee").empty();
                $("#father_namee").append('*'+data.errors['father_name']);
                }
                if (data.errors['sex']) {
                $("#sexe").empty();
                $("#sexe").append('*'+data.errors['sex']);
                }
                if (data.errors['date_of_birth']) {
                $("#date_of_birthe").empty();
                $("#date_of_birthe").append('*'+data.errors['date_of_birth']);
                }
                if (data.errors['present_address']) {
                $("#present_addresse").empty();
                $("#present_addresse").append('*'+data.errors['present_address']);
                }
                if (data.errors['permanent_address']) {
                $("#permanent_addresse").empty();
                $("#permanent_addresse").append('*'+data.errors['permanent_address']);
                }
                if (data.errors['nid_no']) {
                $("#nid_noe").empty();
                $("#nid_noe").append('*'+data.errors['nid_no']);
                }
                if (data.errors['mobile_no']) {
                $("#mobile_noe").empty();
                $("#mobile_noe").append('*'+data.errors['mobile_no']);
                }
                if (data.errors['email']) {
                $("#emaile").empty();
                $("#emaile").append('*'+data.errors['email']);
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
            
                if (data.errors['branch_id_fk']) {
                $("#branch_id_fke").empty();
                $("#branch_id_fke").append('*'+data.errors['branch_id_fk']);
                }
            
                if (data.errors['user_password']) {
                $("#user_passworde").empty();
                $("#user_passworde").append('*'+data.errors['user_password']);
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