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
                                <a href="{{ route('gnrResponsibility.create') }}"
                                   class="btn btn-info pull-right addViewBtn"><i
                                            class="glyphicon glyphicon-plus-sign addIcon"></i>Add</a>
                            </div>
                            <h1 align="center" style="color: white; font-family: Antiqua;letter-spacing: 2px">
                                Responsibilities
                            </h1>
                        </div>
                        <div class="panel-body panelBodyView">
                            <table class="table table-striped table-bordered table-condensed"
                                   style="color: #000003;">
                                <thead>
                                <tr>
                                    <th width="3%">SL</th>
                                    <th>Position</th>
                                    <th>Type</th>
                                    <th style="width: 25%;">Boundary</th>
                                    <th>Employee</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($responsibilities as $r)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td style="text-align: left;">{{ $r->position->name ?? '' }}</td>
                                        <td style="text-align: left;">{{ $r->type_code ?? '' }}</td>
                                        <td style="text-align: left;">
                                            @foreach($r->getBoundaries() as $item)
                                                <span style="margin-bottom: 2px;" class="badge badge-default">{{ $item->name ?? '' }}</span>
                                            @endforeach
                                        </td>
                                        <td style="text-align: left;">{{ $r->employee->emp_id ?? '' }} - {{ $r->employee->emp_name_english ?? '' }}</td>
                                        <td>
                                            <a href="{{ route('gnrResponsibility.edit').'?id='.$r->id }}"
                                               class="edit">
                                                <span class="glyphicon glyphicon-edit"></span>
                                            </a>
                                            <a href="{{ route('gnrResponsibility.delete').'?id='.$r->id }}" class="delete-btn">
                                                <span class="glyphicon glyphicon-trash"></span>
                                                <form class="form-delete" method="POST" action="{{ route('gnrResponsibility.delete').'?id='.$r->id }}">
                                                    {{ csrf_field() }}
                                                </form>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot></tfoot>
                            </table>
                            {{ $responsibilities->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('.delete-btn').click(function(e){
            e.preventDefault()
            if (confirm('Are you sure?')) {
                $(this).find('form').submit();
            }
        });
    </script>
@endsection

