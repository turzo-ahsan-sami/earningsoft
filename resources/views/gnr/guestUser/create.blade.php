@extends('layouts/gnr_layout')
@section('title', '| Home')
@section('content')
    <div class="row add-data-form">
        <div class="col-md-12">
            <div class="col-md-2"></div>
            <div class="col-md-8 fullbody">
                <div class="viewTitle" style="border-bottom: 1px solid white;">
                    <a href="{{ route('guestUser.index') }}" class="btn btn-info pull-right addViewBtn">
                        <i class="glyphicon glyphicon-th-list viewIcon">
                        </i>User List</a>
                </div>
                <div class="panel panel-default panel-border">
                    <div class="panel-heading">
                        <div class="panel-title">Create Guest User</div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-8">
                                    {!! Form::open(array('url' => route('guestUser.store'), 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                    <div class="form-group">
                                        {!! Form::label('name', 'Name : ', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('name', null, ['class' => 'form-control','id'=>'name', 'autocomplete'=>'off','placeholder' => 'Enter Full Name']) !!}
                                            @if ($errors->has('name'))
                                                <span style="color: red;">{{ $errors->first('name') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('user_name', 'User Name : ', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('user_name', null, ['class' => 'form-control','id'=>'user_name', 'autocomplete'=>'off', 'placeholder' => 'Enter User Name']) !!}
                                            @if ($errors->has('user_name'))
                                                <span style="color: red;">{{ $errors->first('user_name') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('email', 'Email : ', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('email', null, ['class' => 'form-control', 'id' => 'email','autocomplete'=>'off','placeholder' => 'Enter Email']) !!}
                                            @if ($errors->has('email'))
                                                <span style="color: red;">{{ $errors->first('email') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('role', 'Role : ', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::select('role',$roles, null, ['class' => 'form-control', 'id' => 'role', 'placeholder' => 'Select Role']) !!}
                                            @if ($errors->has('role'))
                                                <span style="color: red;">{{ $errors->first('role') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('project', 'Company : ', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::select('company',$companies, null, ['class' => 'form-control', 'id' => 'company', 'placeholder' => 'Select Company']) !!}
                                            @if ($errors->has('company'))
                                                <span style="color: red;">{{ $errors->first('company') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('project', 'Project : ', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::select('project',$projects, null, ['class' => 'form-control', 'id' => 'project', 'placeholder' => 'Select Project']) !!}
                                            @if ($errors->has('project'))
                                                <span style="color: red;">{{ $errors->first('project') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('branch', 'Branch : ', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::select('branch',$branches, null, ['class' => 'form-control', 'id' => 'branch', 'placeholder' => 'Select Branch']) !!}
                                            @if ($errors->has('branch'))
                                                <span style="color: red;">{{ $errors->first('branch') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('status', 'Status : ', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::select('status',[0=>'Inactive',1=>'Active'], null, ['class' => 'form-control', 'autocomplete'=>'off','placeholder' => 'Select Branch']) !!}
                                            @if ($errors->has('status'))
                                                <span style="color: red;">{{ $errors->first('status') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('password', 'Password : ', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Enter Password','autocomplete'=>'off']) !!}
                                            @if ($errors->has('password'))
                                                <span style="color: red;">{{ $errors->first('password') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('password_confirmation', 'Confirm Password : ', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Re-enter Password','autocomplete'=>'off']) !!}
                                            @if ($errors->has('password'))
                                                <span style="color: red;">{{ $errors->first('password') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group pull-right">
                                        <div class="col-sm-12">
                                            {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                            <a href="{{url('user')}}"
                                               class="btn btn-danger closeBtn">Close</a>
                                            <span id="success" style="color:green; font-size:20px;"
                                                  class="pull-right"></span>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                                <div class="col-md-4 emptySpace vert-offset-top-0">
                                    <img src="{{ asset('images/catalog/image15.png') }}"
                                         width="80%" height=""
                                         style="float:right">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footerTitle" style="border-top:1px solid white"></div>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>
@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
<script type="text/javascript">
    function ajaxRequest(sourceAttr,targetAttr,link,selected=null){

        var sourceId      = sourceAttr.val() || '';
        var projectId     = $('#project').val() || 0;
        var projectTypeId = $('#project_type').val() || 0;
        var branchId      = $('#branch').val() || 0;
        var token         = $('meta[name="_token"]').attr('content');

        var sendingData = {
            'sourceId': sourceId,
            'projectId': projectId,
            'projectTypeId': projectTypeId,
            'branchId':branchId,
            '_token': token
        }

        console.log(sendingData);

        $response = $.ajax({
            url: link,
            dataType: 'json',
            type: 'POST',
            data: sendingData,
            success: function( data ){
                console.log(data);
                var options="<option value=''>Select One</option>";
                $.each(data,function(key,val){
                    var selectedData='';
                    if(selected!=null && val.id==selected){
                        selectedData = 'selected="selected"';
                    }

                    options += '<option '+selectedData+' value="'+val.id+'">'+val.name+'</option>';
                });
                targetAttr.html(options);
            },
            error: function(response){
                // console.log(response);
            }
        });

        return $response;
    }

    // Append project
    $(document).on('change','#company', function() {
        ajaxRequest($('#company'), $('#project'),
            "{{ url('user/getProjectsByCompanyId') }}" +"/"+ $('#company').val() );
    });

    // Append branch
    $(document).on('change','#project', function() {
        ajaxRequest($('#project'), $('#branch'),
            "{{ url('hr/structure/getBranchFromProject') }}" );
    });


    ajaxRequest($('#project'),
        $('#project_type'),
        "{{ url('hr/filter/getProjectType') }}",
        "{{ $data['filter']['project_type'] ?? null }}" )
        .then(function(){
            ajaxRequest($('#project_type'), $('#branch'),
                "{{ url('hr/filter/getBranch') }}",
                "{{ $data['filter']['branch'] ?? null }}" )
                .then(function(){
                    ajaxRequest($('#branch'), $('#user'),
                        "{{ url('hr/filter/getUsers') }}",
                        "{{ $data['filter']['user'] ?? null }}" );
                });
        });

</script> 

