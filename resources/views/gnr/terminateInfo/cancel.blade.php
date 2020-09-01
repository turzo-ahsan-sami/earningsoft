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

                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-bordered table-condensed">
                                            <tr>
                                                <th class="text-left"><?= $data['attributes']['company_id_fk']?>:</th>
                                                <td><?= $data['model']->user->employee->organization->company->name?></td>

                                                <th class="text-left"><?= $data['attributes']['project_id_fk']?>:</th>
                                                <td><?= $data['model']->user->employee->organization->project->name?></td>
                                            </tr>

                                            <tr>
                                                <th class="text-left"><?= $data['attributes']['branch_id_fk']?>:</th>
                                                <td><?= $data['model']->user->employee->organization->branch->name?></td>

                                                <th class="text-left">Employee ID:</th>
                                                <td><?= $data['model']->user->employee->emp_id?></td>
                                            </tr>

                                            <tr>
                                                <th class="text-left"><?= $data['attributes']['users_id_fk']?>:</th>
                                                <td class="text-left"><?= $data['model']->user->employee->emp_name_english?></td>

                                                 <th class="text-left"><?= $data['attributes']['position']?>:</th>
                                                <td class="text-left"><?= $data['model']->position?></td>
                                            </tr>

                                            <tr>
                                                <th class="text-left"><?= $data['attributes']['terminate_date']?>:</th>
                                                <td><?= ($data['model']->terminate_date!=Null)?date('d-m-Y',strtotime($data['model']->terminate_date)):''?></td>
                                            </tr>

                                            <tr>
                                                <th class="text-left"><?= $data['attributes']['note']?>:</th>
                                                <td class="text-left"><?= $data['model']->note?></td>

                                                <th class="text-left"><?= $data['attributes']['reason']?>:</th>
                                                <td class="text-left"><?= @$data['model']->reasonList->title?></td>
                                            </tr>

                                            <tr>
                                                <th class="text-left"><?= $data['attributes']['recruitment_type']?>:</th>
                                                <td class="text-left"><?= $data['model']->recruitment_type?></td>

                                                <th class="text-left"><?= $data['attributes']['status']?>:</th>
                                                <td class="text-left"><?= $data['model']->status?></td>
                                            </tr>

                                            <?php if($data['model']->approved_id_fk!=Null):?>
                                            <tr>
                                                <th class="text-left"><?= $data['attributes']['approved_id_fk']?>:</th>
                                                <td><?php @$data['model']->approvedBy->employee->emp_name_english ?></td>

                                                <th class="text-left"><?= $data['attributes']['effect_date']?>:</th>
                                                <td><?= date("d-m-Y",strtotime($data['model']->effect_date))?></td>
                                            </tr>
                                            <?php endif;?>
                                        </table>
                                    </div>

                                    
                                    <div class="clearfix">&nbsp;</div>

                                    <div class="col-md-12">
                                        {!! Form::open(array('role' => 'form', 'files'=>'false', 'class'=>'form-horizontal form-groups')) !!}

                                        <div class="col-md-6">
                                            {!! Form::label('cancel_date', $data['attributes']['cancel_date'], ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::text('cancel_date', $data['model']->cancel_date, ['class' => 'form-control datepicker', 'id' => 'cancel_date']) !!}
                                                <p id='cancel_date' style="max-height:3px;">{{ $errors->error->first('cancel_date') }}</p>
                                            </div>

                                            <div class="clearfix">&nbsp;</div> 

                                            {!! Form::label('cancel_reason', $data['attributes']['cancel_reason'], ['class' => 'col-sm-4 control-label']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::textarea('cancel_reason', $data['model']->cancel_reason, ['class' => 'form-control', 'id' => 'cancel_reason', 'rows'=>'3']) !!}
                                                <p id='cancel_reason' style="max-height:3px;">{{ $errors->error->first('cancel_reason') }}</p>
                                            </div>

                                            <div class="clearfix">&nbsp;</div> 
                                            
                                            <div class="col-md-offset-4 col-md-8">
                                                {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                            </div>
                                        </div>

                                        


                                        {!! Form::close() !!}
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>
@endsection