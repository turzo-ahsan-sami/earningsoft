<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<style>
body {font-family: Arial, Helvetica, sans-serif;}

/* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    padding-top: 100px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
    position: relative;
    background-color: #fefefe;
    margin: auto;
    padding: 0;
    border: 1px solid #888;
    width: 40%;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
    -webkit-animation-name: animatetop;
    -webkit-animation-duration: 0.4s;
    animation-name: animatetop;
    animation-duration: 0.4s
}

/* Add Animation */
@-webkit-keyframes animatetop {
    from {top:-300px; opacity:0}
    to {top:0; opacity:1}
}

@keyframes animatetop {
    from {top:-300px; opacity:0}
    to {top:0; opacity:1}
}

/* The Close Button */
.close {
    color: white;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

.modal-header {
    padding: 2px 16px;
    background-color: #708090;
    color: white;
}

.modal-body {padding: 2px 16px;}

.modal-footer {
    padding: 2px 16px;
    background-color: #708090;
    color: white;
  }
</style>

<div id="myModal" class="modal">
  <div class="modal-content">
    <div class="modal-header" style="text-align: center !important;">
      <span class="close">&times;</span>
      <h4>Attention</h4>
    </div>
    <div class="modal-body" style="text-align: center !important; padding-top: 10px !important; padding-bottom: 10px !important;">
      <p>This employee has assigned in a samity!</p>
      <p>So please remove this person from samity at first then transfer that person!</p>
    </div>
  </div>

</div>

<script type="text/javascript">
function ALERT_MESSAGE(a, b, btn){
  if (a == 1) {
    var modal = document.getElementById('myModal');
    if (b == 1) {

        console.log(b);

        var span = document.getElementsByClassName("close")[0];

        btn.onclick = function() {
            modal.style.display = "block";
            $('[id="users_id_fk"]').prop('onclick',null).off('click');
            b = 0;
        }
    }
    else {
        btn.onclick = function() {
            modal.style.display = "none";
        }
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    a = -1;
  }

  return 0;
}

</script>

<div class="row">
    <div class="col-md-12">
    <?php
        //print_r($errors->error);
    ?>

    {!! Form::open(array('role' => 'form', 'files'=>'false', 'class'=>'form-horizontal form-groups')) !!}

    <div class="form-group">
        {!! Form::label('pre_company_id_fk', $data['attributes']['pre_company_id_fk'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::select('pre_company_id_fk', $data['companyData'], $data['model']->pre_company_id_fk, ['class' => 'form-control getProject getSalaryIncrementYear', 'id' => 'pre_company_id_fk']) !!}
            <p id='pre_company_id_fk' style="max-height:3px;">{{ $errors->error->first('pre_company_id_fk') }}</p>
        </div>

        {!! Form::label('pre_project_id_fk', $data['attributes']['pre_project_id_fk'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::select('pre_project_id_fk', array(''=>'Select any'), $data['model']->pre_project_id_fk, ['class' => 'form-control getBranch getSalaryIncrementYear', 'id' => 'pre_project_id_fk']) !!}
            <p id='pre_project_id_fk' style="max-height:3px;">{{ $errors->error->first('pre_project_id_fk') }}</p>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('pre_branch_id_fk', $data['attributes']['pre_branch_id_fk'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::select('pre_branch_id_fk', array(''=>'Select any'), $data['model']->pre_branch_id_fk, ['class' => 'form-control getUser', 'id' => 'pre_branch_id_fk']) !!}
            <p id='pre_branch_id_fk' style="max-height:3px;">{{ $errors->error->first('pre_branch_id_fk') }}</p>
        </div>

        {!! Form::label('users_id_fk', $data['attributes']['users_id_fk'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::select('users_id_fk', [''=>'Select any'], $data['model']->users_id_fk, ['class' => 'form-control', 'id' => 'users_id_fk']) !!}
            <p id='users_id_fk' style="max-height:3px;">{{ $errors->error->first('users_id_fk') }}</p>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('pre_emp_designation', $data['attributes']['pre_emp_designation'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('pre_emp_designation', $data['model']->pre_emp_designation, ['class' => 'form-control', 'id' => 'pre_emp_designation', 'readonly'=>'readonly']) !!}
            <p id='pre_emp_designation' style="max-height:3px;">{{ $errors->error->first('pre_emp_designation') }}</p>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('pre_emp_id', $data['attributes']['pre_emp_id'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('pre_emp_id', $data['model']->pre_emp_id, ['class' => 'form-control', 'id' => 'pre_emp_id', 'readonly'=>'readonly']) !!}
            <p id='pre_emp_id' style="max-height:3px;">{{ $errors->error->first('pre_emp_id') }}</p>
        </div>

        {!! Form::label('cur_emp_id', $data['attributes']['cur_emp_id'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('cur_emp_id', $data['model']->cur_emp_id, ['class' => 'form-control', 'id' => 'cur_emp_id']) !!}
            <p id='cur_emp_id' style="max-height:3px;">{{ $errors->error->first('cur_emp_id') }}</p>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('cur_project_id_fk', $data['attributes']['cur_project_id_fk'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::select('cur_project_id_fk', array(''=>'Select any'), $data['model']->cur_project_id_fk, ['class' => 'form-control getBranchCur getSalaryIncrementYear', 'id' => 'cur_project_id_fk']) !!}
            <p id='cur_project_id_fk' style="max-height:3px;">{{ $errors->error->first('cur_project_id_fk') }}</p>
        </div>

        {!! Form::label('cur_branch_id_fk', $data['attributes']['cur_branch_id_fk'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::select('cur_branch_id_fk', array(''=>'Select any'), $data['model']->cur_branch_id_fk, ['class' => 'form-control', 'id' => 'cur_branch_id_fk']) !!}
            <p id='cur_branch_id_fk' style="max-height:3px;">{{ $errors->error->first('cur_branch_id_fk') }}</p>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('effect_month', $data['attributes']['effect_month'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('effect_month', $data['model']->effect_month, ['class' => 'form-control monthpicker', 'id' => 'effect_month']) !!}
            <p id='effect_month' style="max-height:3px;">{{ $errors->error->first('effect_month') }}</p>
        </div>

        {!! Form::label('effect_date', $data['attributes']['effect_date'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('effect_date', $data['model']->effect_date, ['class' => 'form-control datepicker', 'id' => 'effect_date']) !!}
            <p id='effect_date' style="max-height:3px;">{{ $errors->error->first('effect_date') }}</p>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('release_date', $data['attributes']['release_date'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('release_date', $data['model']->release_date, ['class' => 'form-control datepicker', 'id' => 'release_date']) !!}
            <p id='release_date' style="max-height:3px;">{{ $errors->error->first('release_date') }}</p>
        </div>

        {!! Form::label('joining_date', $data['attributes']['joining_date'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('joining_date', $data['model']->joining_date, ['class' => 'form-control datepicker', 'id' => 'joining_date']) !!}
            <p id='joining_date' style="max-height:3px;">{{ $errors->error->first('joining_date') }}</p>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-12 text-center">
            <div class="col-sm-12">
                {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                <a href="{{url('hr/transfer')}}" class="btn btn-danger closeBtn">Close</a>
            </div>
        </div>
    </div>

    {!! Form::close() !!}

    </div>
</div>

@section('footerAssets')
    <script type="text/javascript">
        function getEmployeeCurrentData(users_id_fk){
            // console.log(users_id_fk);
            $('#pre_emp_id').val('');
            $('#cur_emp_id').val('');
            $('#pre_emp_designation').val('');
            $.ajax({
                url: "<?= url('hr/transfer/getEmployeeCurrentData')?>",
                dataType: 'json',
                type: 'POST',
                data: {"id":users_id_fk,"_token":$('meta[name="_token"]').attr('content')},
                success: function( data, textStatus, jQxhr ){
                    if(data.res==1){
                        console.log(data.value);
                        $('#pre_emp_id').val(data.value.emp_id.id);
                        $('#cur_emp_id').val(data.value.emp_id.id);
                        $('#pre_emp_designation').val(data.value.emp_id.position);
                    }
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });
        }

        function getDependentDropdown(sourceAttr,targetAttr,link,selected=null){
            var sourceId=0;
            if(sourceAttr.jquery)
                sourceId = sourceAttr.val();
            else
                sourceId = sourceAttr;

            // console.log(sourceId);

            $.ajax({
                url: link,
                dataType: 'json',
                type: 'POST',
                data: {"sourceId":sourceId,"_token":$('meta[name="_token"]').attr('content')},
                success: function( data, textStatus, jQxhr ){
                    console.log(data);
                    var options='<option value="">Select any</option>';
                    $.each(data,function(key,val){
                        var selectedData='';
                        if(selected!=null && val.id==selected){
                            selectedData = 'selected="selected"';
                        }
                        // console.log(val.samityStatus);
                        options += '<option '+selectedData+' value="'+val.id+' - '+val.samityStatus+'">'+val.name+'</option>';
                    });
                    targetAttr.html(options);
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });
        }

        $(document).on('change','#users_id_fk',function(){
            var users_id_fk = $(this).val();
            var result = $(this).val().split('-');
            getEmployeeCurrentData(result[0]);
            // if(parseInt(users_id_fk)>0){
            //     // console.log(result);
            //     if (result[1] == 0) {
            //       getEmployeeCurrentData(result[0]);
            //     }
            //     else {
            //       var a = 1;
            //       var b = 1;
            //       var btn = document.getElementById("users_id_fk");
            //       console.log(btn);
            //       ALERT_MESSAGE(a, b, btn);
            
            //       // functionAlert();
            //       // alert('This employee has assigned in a samity! So can not Transfer right now!');
            //       getEmployeeCurrentData(-1);
            //     }
            // }
        });

        $(document).on('change','.getProject',function(){
            getDependentDropdown($('.getProject'),$('select[name="pre_project_id_fk"]'),'<?= url('hr/structure/getProject')?>');

            getDependentDropdown($('.getProject'),$('select[name="cur_project_id_fk"]'),'<?= url('hr/structure/getProject')?>');

            var options='<option value="">Select any</option>';
            $('select[name="pre_branch_id_fk"]').html(options);
            $('select[name="cur_branch_id_fk"]').html(options);
        });

        $(document).on('change','.getBranch',function(){
            getDependentDropdown($('.getBranch'),$('select[name="pre_branch_id_fk"]'),'<?= url('hr/structure/getBranchFromProject')?>');
        });

        $(document).on('change','.getBranchCur',function(){
            getDependentDropdown($('.getBranchCur'),$('select[name="cur_branch_id_fk"]'),'<?= url('hr/structure/getBranchFromProject')?>');
        });

        $(document).on('change','.getUser',function(){
            getDependentDropdown($('.getUser'),$('select[name="users_id_fk"]'),'<?= url('hr/structure/getUserFromBranch')?>');
        });

        $(document).ready(function(){

            getDependentDropdown("<?= (old('pre_company_id_fk'))?old('pre_company_id_fk'):$data['model']->pre_company_id_fk?>",$('select[name="pre_project_id_fk"]'),'<?= url('hr/structure/getProject')?>',"<?= (old('pre_project_id_fk'))?old('pre_project_id_fk'):$data['model']->pre_project_id_fk?>");

            getDependentDropdown("<?= (old('pre_project_id_fk'))?old('pre_project_id_fk'):$data['model']->pre_project_id_fk?>",$('select[name="pre_branch_id_fk"]'),'<?= url('hr/structure/getBranchFromProject')?>',"<?= (old('pre_branch_id_fk'))?old('pre_branch_id_fk'):$data['model']->pre_branch_id_fk?>");

            getDependentDropdown("<?= (old('pre_branch_id_fk'))?old('pre_branch_id_fk'):$data['model']->pre_branch_id_fk?>",$('select[name="users_id_fk"]'),'<?= url('hr/structure/getUserFromBranch')?>',"<?= (old('users_id_fk'))?old('users_id_fk'):$data['model']->users_id_fk?>");

            getDependentDropdown("<?= (old('pre_company_id_fk'))?old('pre_company_id_fk'):$data['model']->pre_company_id_fk?>",$('select[name="cur_project_id_fk"]'),'<?= url('hr/structure/getProject')?>',"<?= (old('cur_project_id_fk'))?old('cur_project_id_fk'):$data['model']->cur_project_id_fk?>");

            getDependentDropdown("<?= (old('cur_project_id_fk'))?old('cur_project_id_fk'):$data['model']->cur_project_id_fk?>",$('select[name="cur_branch_id_fk"]'),'<?= url('hr/structure/getBranchFromProject')?>',"<?= (old('cur_branch_id_fk'))?old('cur_branch_id_fk'):$data['model']->cur_branch_id_fk?>");

        });
    </script>
@endsection
