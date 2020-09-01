<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>


<div class="row">
    <div class="col-md-12">
    {!! Form::open(array('role' => 'form', 'files'=>'false', 'class'=>'form-horizontal form-groups')) !!}
        <div class="form-group" <?php if($data['utype']['roleid']==2):?> style="display: none" <?php endif;?>>
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

        <div class="form-group" <?php if($data['utype']['roleid']==2):?> style="display: none" <?php endif;?>>
            {!! Form::label('branch_id_fk', $data['attributes']['branch_id_fk'], ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-4">
                {!! Form::select('branch_id_fk', array(''=>'Select any'), $data['model']->branch_id_fk, ['class' => 'form-control getUser', 'id' => 'branch_id_fk']) !!}
                <p id='branch_id_fk' style="max-height:3px;">{{ $errors->error->first('branch_id_fk') }}</p>
            </div>

            {!! Form::label('users_id_fk', $data['attributes']['users_id_fk'], ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-4">
                {!! Form::select('users_id_fk', ['' => 'Select any'], $data['model']->users_id_fk ?? null, ['class' => 'form-control', 'id' => 'users_id_fk']) !!}
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
            {!! Form::label('resign_date', $data['attributes']['resign_date'], ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-4">
                {!! Form::text('resign_date', $data['model']->resign_date, ['class' => 'form-control datepicker', 'id' => 'resign_date']) !!}
                <p id='resign_date' style="max-height:3px;">{{ $errors->error->first('resign_date') }}</p>
            </div>

            {!! Form::label('expected_effect_date', $data['attributes']['expected_effect_date'], ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-4">
                {!! Form::text('expected_effect_date', $data['model']->expected_effect_date, ['class' => 'form-control datepicker', 'id' => 'expected_effect_date']) !!}
                <p id='expected_effect_date' style="max-height:3px;">{{ $errors->error->first('expected_effect_date') }}</p>
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
                    <a href="{{url('hr/resignInfo')}}" class="btn btn-danger closeBtn">Close</a>
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
                        console.log(data.value);
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
                    console.log(data);
                    var options='<option value="">Select any</option>';
                    $.each(data,function(key,val){
                        var selectedData='';
                        if(selected!=null && val.id==selected){
                            selectedData = 'selected="selected"';
                        }

                        options += '<option '+selectedData+' value="'+val.id+'">'+val.name+'</option>';
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
        });

        $(document).ready(function(){

            getDependentDropdown("<?= (old('company_id_fk'))?old('company_id_fk'):@$data['model']->user->company_id_fk?>",$('select[name="project_id_fk"]'),'<?= url('hr/structure/getProject')?>',"<?= (old('project_id_fk'))?old('project_id_fk'):@$data['model']->user->project_id_fk?>");

            getDependentDropdown("<?= (old('project_id_fk'))?old('project_id_fk'):@$data['model']->user->project_id_fk?>",$('select[name="branch_id_fk"]'),'<?= url('hr/structure/getBranchFromProject')?>',"<?= (old('branch_id_fk'))?old('branch_id_fk'):@$data['model']->user->branchId?>");

            getDependentDropdown("<?= (old('branch_id_fk'))?old('branch_id_fk'):@$data['model']->user->branchId?>",$('select[name="users_id_fk"]'),'<?= url('hr/structure/getUserFromBranch')?>',"<?= (old('users_id_fk'))?old('users_id_fk'):@$data['model']->users_id_fk?>");

            <?php if($data['utype']['roleid']==2 && @$data['action']=='insert'):?>
                getEmployeeCurrentData("<?= Auth::user()->id?>");
            <?php endif;?>
        })
    </script>
@endsection
