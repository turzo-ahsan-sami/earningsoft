@extends('hr_main')
@section('title', '| '. $data['pageTitle'] )
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
        <div class="col-md-1"></div>
        <div class="col-md-10 fullbody">
            <div class="viewTitle" style="border-bottom: 1px solid white;">
                <a href="{!! $data['allRecruitmentTypeUrl'] !!}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                    </i>{!! $data['allRecruitmentTypeLabel'] !!}</a>
            </div>
            <div class="panel panel-default panel-border">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="panel-heading">
                                <div class="panel-title">{!! $data['pageTitle'] !!}</div>
                            </div>

                            <div class="panel-body">
                                @include('hr.terminateInfo.form')
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>
@endsection