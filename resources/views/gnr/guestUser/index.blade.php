@extends('layouts/gnr_layout')
@section('title', '| Sub Functionality')
@section('content')
    @include('successMsg')
    <div class="row">
        <div class="col-md-12">
            <div class="" style="">
                <div class="">
                    <div class="panel panel-default" style="background-color:#708090;">
                        <div class="panel-heading" style="padding-bottom:0px">
                            <div class="panel-options">
                                <a href="{{ route('guestUser.create') }}" class="btn btn-info pull-right addViewBtn"><i
                                            class="glyphicon glyphicon-plus-sign addIcon"></i>Add User</a>
                            </div>
                            <h1 align="center" style="color: white; font-family: Antiqua;letter-spacing: 2px">GUEST USER
                                LIST</h1>
                        </div>
                        <div class="panel-body panelBodyView">

                            <!-- Filtering Start-->
                            {!! Form::open(array('url' => route('guestUser.index'), 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filterFormId', 'method'=>'get')) !!}
                            <div class="row">
                                <div class="col-md-12">

                                    @if(Auth::user()->branchId == 1)

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    {!! Form::label('', 'Branch:', ['class' => 'control-label pull-left']) !!}
                                                </div>
                                                <div class="col-md-12">
                                                    {!! Form::select('filBranch', !empty($branchList) ? [''=>'--All--'] + $branchList: [], $branchSelected ?? null ,['id'=>'filBranch','class'=>'form-control input-sm','autocomplete'=>'off']) !!}
                                                </div>
                                            </div>
                                        </div>

                                    @endif

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                {!! Form::label('', 'Role:', ['class' => 'control-label pull-left']) !!}
                                            </div>
                                            <div class="col-md-12">
                                                {!! Form::select('filRole', !empty($roleList) ? [''=>'--All--'] + $roleList: [] , $roleSelected ?? null ,['id'=>'filRole','class'=>'form-control input-sm','autocomplete'=>'off']) !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                {!! Form::label('', 'Emp. ID:', ['class' => 'control-label pull-left']) !!}
                                            </div>
                                            <div class="col-md-12">
                                                {!! Form::text('filEmpId', $empIdSelected ?? null ,['id'=>'filEmpId','class'=>'form-control input-sm','autocomplete'=>'off']) !!}
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                {!! Form::submit('Search', ['id' => 'searchButton', 'class' => 'btn btn-primary btn-xs','style'=>'margin-top: 29px;']); !!}
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {!! Form::close()  !!}
                        <!-- filtering end-->

                            <table class="table table-striped table-bordered table-condensed" id="userTbl"
                                   style="color: #000003;">
                                <thead>
                                <tr>
                                    <th width="3%">SL</th>
                                    <th>Name</th>
                                    <th>User Name</th>
                                    <th>Role</th>
                                    <th>Company</th>
                                    <th>Project</th>
                                    <th>Branch</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td style="text-align: left;">{{ $user->name ?? '' }}</td>
                                        <td style="text-align: left;">{{ $user->username ?? '' }}</td>
                                        <td style="text-align: left;">{{ $gnrRole->find($user->getRole()->roleId)->name ?? '' }}</td>
                                        <td style="text-align: left;">{{ $user->company->name ?? '' }}</td>
                                        <td style="text-align: left;">{{ $user->project->name ?? '' }}</td>
                                        <td style="text-align: left;">{{ $user->branch->name ?? '' }}</td>
                                        <td>
                                            @if(\App\ConstValue::USER_ID_SUPER_ADMIN != $user->id)
                                            <a id="editIcone" href="{{ route('guestUser.edit', $user->id) }}" class="edit">
                                                <span class="glyphicon glyphicon-edit"></span>
                                            </a>

                                            &nbsp;

                                            <a id="deleteIcone" href="javascript:;" class="delete"
                                               data-id="{{ $user->id }}">
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot></tfoot>
                            </table>
                            {{ $users->links() }}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--Footer Links--}}
    <script>
        $(document).on('click', '.delete', function (e) {
            var row = $(this).parent().parent();
            if(confirm('Are you sure?')) {
                $.ajax({
                    url: "guestUser/delete/"+$(this).data('id'),
                }).done(function (response) {
                    row.fadeOut(500, function(){
                        this.remove();
                    });
                }).then(function (response) {
                    if(response.success){
                        toastr.success(response.message, 'Success Message', {timeOut: 5000})
                    }
                }).fail(function (response) {
                    toastr.error('Something went wrong', 'Error Message', {timeOut: 5000})
                });
            }
        })
    </script>
@endsection

