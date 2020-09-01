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

{{-- <div id="myModal" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header" style="text-align: center !important;">
      <span class="close">&times;</span>
      <h4>Attention</h4>
    </div>
    <div class="modal-body" style="text-align: center !important; padding-top: 10px !important; padding-bottom: 10px !important;">
      <p>This employee has assigned in a samity!</p>
      <p>So please remove this person from samity at first then terminate that person!</p>
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

</script> --}}

<div class="row">
    <div class="col-md-12">
    <?php
        //print_r($errors->error);
    ?>

    {!! Form::open(array('role' => 'form', 'files'=>'false', 'class'=>'form-horizontal form-groups')) !!}

    <div class="form-group">
        {!! Form::label('company_id_fk', $data['attributes']['company_id_fk'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::select('company_id_fk', $data['companyData'], $data['model']->company_id_fk, ['class' => 'form-control getProject', 'id' => 'company_id_fk']) !!}
            <p id='company_id_fk' style="max-height:3px;">{{ $errors->error->first('company_id_fk') }}</p>
        </div>

        {!! Form::label('project_id_fk', $data['attributes']['project_id_fk'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::select('project_id_fk', array(''=>'Select any'), $data['model']->project_id_fk, ['class' => 'form-control getBranch', 'id' => 'project_id_fk']) !!}
            <p id='project_id_fk' style="max-height:3px;">{{ $errors->error->first('project_id_fk') }}</p>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('branch_id_fk', $data['attributes']['branch_id_fk'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::select('branch_id_fk', array(''=>'Select any'), $data['model']->branch_id_fk, ['class' => 'form-control getUser', 'id' => 'branch_id_fk']) !!}
            <p id='branch_id_fk' style="max-height:3px;">{{ $errors->error->first('branch_id_fk') }}</p>
        </div>

        {!! Form::label('users_id_fk', $data['attributes']['users_id_fk'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::select('users_id_fk', [''=>'Select any'], $data['model']->users_id_fk, ['class' => 'form-control', 'id' => 'users_id_fk']) !!}
            <p id='users_id_fk' style="max-height:3px;">{{ $errors->error->first('users_id_fk') }}</p>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('position', $data['attributes']['position'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('position', $data['model']->position, ['class' => 'form-control', 'id' => 'position', 'readonly'=>'readonly']) !!}
            <p id='position' style="max-height:3px;">{{ $errors->error->first('position') }}</p>
        </div>

        {!! Form::label('recruitment_type', $data['attributes']['recruitment_type'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('recruitment_type', $data['model']->recruitment_type, ['class' => 'form-control', 'id' => 'recruitment_type', 'readonly'=>'readonly']) !!}
            <p id='recruitment_type' style="max-height:3px;">{{ $errors->error->first('recruitment_type') }}</p>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('terminate_date', $data['attributes']['terminate_date'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('terminate_date', $data['model']->terminate_date, ['class' => 'form-control datepicker', 'id' => 'terminate_date']) !!}
            <p id='terminate_date' style="max-height:3px;">{{ $errors->error->first('terminate_date') }}</p>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('note', $data['attributes']['note'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::textarea('note', $data['model']->note, ['class' => 'form-control', 'id' => 'note', 'rows'=>3]) !!}
            <p id='note' style="max-height:3px;">{{ $errors->error->first('note') }}</p>
        </div>

        {!! Form::label('reason', $data['attributes']['reason'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::select('reason', $data['reasonData'], $data['model']->reason, ['class' => 'form-control', 'id' => 'reason']) !!}
            <p id='reason' style="max-height:3px;">{{ $errors->error->first('reason') }}</p>
        </div>
    </div>

    @if(@$data['action']=='Approve')
    <div class="form-group">
        {!! Form::label('effect_date', $data['attributes']['effect_date'], ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('effect_date', $data['model']->effect_date, ['class' => 'form-control datepicker', 'id' => 'effect_date']) !!}
            <p id='effect_date' style="max-height:3px;">{{ $errors->error->first('effect_date') }}</p>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-md-4"></div>
        <div class="form-group col-md-4 text-center">
            <div class="col-sm-12">
                {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                <a href="{{url('hr/securityMoneyCollection')}}" class="btn btn-danger closeBtn">Close</a>
            </div>
        </div>
        <div class="col-md-4">
            <span id="success" style="color:green; font-size:20px;"></span>
        </div>
    </div>

    {!! Form::close() !!}

    </div>
</div>

@section('footerAssets')
    <script type="text/javascript">
        function getEmployeeCurrentData(users_id_fk){
            $('#position').val('');
            $('#recruitment_type').val('');
            $.ajax({
                url: "<?= url('hr/promotion/getEmployeeCurrentData')?>",
                dataType: 'json',
                type: 'POST',
                data: {"id":users_id_fk,"_token":$('meta[name="_token"]').attr('content')},
                success: function( data, textStatus, jQxhr ){
                    if(data.res==1){
                        $('#position').val(data.value.position.name);
                        $('#recruitment_type').val(data.value.recruitment_type.name);
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
            $.ajax({
                url: link,
                dataType: 'json',
                type: 'POST',
                data: {"sourceId":sourceId,"_token":$('meta[name="_token"]').attr('content')},
                success: function( data, textStatus, jQxhr ){
                    var options='<option value="">Select any</option>';
                    $.each(data,function(key,val){
                        var selectedData='';
                        if(selected!=null && val.id==selected){
                            selectedData = 'selected="selected"';
                        }

                        options += '<option '+selectedData+' value="'+val.id+' - '+val.samityStatus+'">'+val.name+'</option>';
                    });
                    targetAttr.html(options);
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });
        }

        $(document).on('change','.getProject',function(){
            getDependentDropdown($('.getProject'),$('select[name="project_id_fk"]'),'<?= url('hr/structure/getProject')?>');
            var options='<option value="">Select any</option>';
            $('select[name="branch_id_fk"]').html(options);
        });

        $(document).on('change','.getBranch',function(){
            getDependentDropdown($('.getBranch'),$('select[name="branch_id_fk"]'),'<?= url('hr/structure/getBranchFromProject')?>');
        });

        $(document).on('change','.getUser',function(){
            getDependentDropdown($('.getUser'),$('select[name="users_id_fk"]'),'<?= url('hr/structure/getUserFromBranch')?>');
        });

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
            //
            //       // functionAlert();
            //       // alert('This employee has assigned in a samity! So can not Transfer right now!');
            //       getEmployeeCurrentData(-1);
            //     }
            // }
        });

        $(document).ready(function(){

            getDependentDropdown("<?= (old('company_id_fk'))?old('company_id_fk'):@$data['model']->user->company_id_fk?>",$('select[name="project_id_fk"]'),'<?= url('hr/structure/getProject')?>',"<?= (old('project_id_fk'))?old('project_id_fk'):@$data['model']->user->project_id_fk?>");

            getDependentDropdown("<?= (old('project_id_fk'))?old('project_id_fk'):@$data['model']->user->project_id_fk?>",$('select[name="branch_id_fk"]'),'<?= url('hr/structure/getBranchFromProject')?>',"<?= (old('branch_id_fk'))?old('branch_id_fk'):@$data['model']->user->branchId?>");

            getDependentDropdown("<?= (old('branch_id_fk'))?old('branch_id_fk'):@$data['model']->user->branchId?>",$('select[name="users_id_fk"]'),'<?= url('hr/structure/getUserFromBranch')?>',"<?= (old('users_id_fk'))?old('users_id_fk'):@$data['model']->users_id_fk?>");
        })
    </script>
@endsection
