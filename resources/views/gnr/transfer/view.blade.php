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
                                                <th><?= $data['attributes']['users_id_fk']?>:</th>
                                                <td><?= @$data['model']->user->employee->emp_name_english?></td>

                                                <th><?= $data['attributes']['status']?>:</th>
                                                <td><?= @$data['model']->status?></td>
                                            </tr>
                                        </table>

                                        <div class="clearfix">&nbsp;</div>

                                        <h4>Employee Organization Data</h4>
                                        <table class="table table-bordered table-condensed">
                                            <tr>
                                                <th><?= $data['attributes']['pre_company_id_fk']?>:</th>
                                                <td><?= @$data['model']->preCompany->name?></td>

                                                <th><?= $data['attributes']['pre_project_id_fk']?>:</th>
                                                <td><?= @$data['model']->preProject->name?></td>

                                                <th><?= $data['attributes']['pre_branch_id_fk']?>:</th>
                                                <td><?= @$data['model']->preBranch->name?></td>

                                                @if($data['model']->pre_emp_designation!='')
                                                <th><?= $data['attributes']['pre_emp_designation']?>:</th>
                                                <td><?= @$data['model']->pre_emp_designation?></td>
                                                @endif
                                            </tr>
                                        </table>

                                        <div class="clearfix">&nbsp;</div>

                                        <h4>Employee Transfered Organization Data</h4>
                                        <table class="table table-bordered table-condensed">
                                            <tr>
                                                <th><?= $data['attributes']['cur_project_id_fk']?>:</th>
                                                <td><?= @$data['model']->project->name?></td>

                                                <th><?= $data['attributes']['cur_branch_id_fk']?>:</th>
                                                <td><?= @$data['model']->branch->name?></td>
                                            </tr>

                                            <tr>
                                                <th><?= $data['attributes']['effect_month']?>:</th>
                                                <td><?= date("F, Y",strtotime($data['model']->effect_month))?></td>

                                                <th><?= $data['attributes']['effect_date']?>:</th>
                                                <td><?= date("d-m-Y",strtotime($data['model']->effect_date))?></td>
                                            </tr>

                                            <tr>
                                                @if($data['model']->release_date!=Null)
                                                <th><?= $data['attributes']['release_date']?>:</th>
                                                <td><?= date("d-m-Y",strtotime($data['model']->release_date))?></td>
                                                @endif

                                                @if($data['model']->joining_date!=Null)
                                                <th><?= $data['attributes']['joining_date']?>:</th>
                                                <td><?= date("d-m-Y",strtotime($data['model']->joining_date))?></td>
                                                @endif
                                            </tr>
                                        </table>

                                        <div class="clearfix">&nbsp;</div>

                                        <div class="form-group">
                                            <div class="col-sm-12 text-center">
                                                <?php if($data['model']->status=='Pending'):?>
                                                    <a href="{!! url('hr/transfer/approved') !!}" data-id="<?= $data['model']->id?>" class="btn btn-xs btn-primary change-status-action" title="Approved"><i class="fa fa-check"></i> Mark as Approved</a>
                                                <?php endif;?>

                                                <?php if($data['model']->status=='Approved'):?>
                                                    <a href="{!! url('hr/transfer/confirmed') !!}" data-id="<?= $data['model']->id?>" class="btn btn-xs btn-primary change-status-action" title="Approved"><i class="fa fa-check"></i> Mark as Confirmed</a>
                                                <?php endif;?>
                                            </div>
                                        </div>

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

@section('footerAssets')
<script type="text/javascript">

    function getEmployeeCurrentData(data){
        var employeePreviousData = $('#employeePreviousData');
        employeePreviousData.html('<tr><td colspan="6">Choose employee first</td></tr>');
        data = JSON.parse(data);
        if(data.res==1){
            var td = '';
            td = td + '<td>'+ data.value.fiscal_year.name +'</td>';
            td = td + '<td>'+ data.value.grade.name +'</td>';
            td = td + '<td>'+ data.value.level.name +'</td>';
            td = td + '<td>'+ data.value.position.name +'</td>';
            td = td + '<td>'+ data.value.recruitment_type.name +'</td>';
            td = td + '<td>'+ data.value.salary_increment_year.name +'</td>';

            employeePreviousData.html('<tr>' + td + '</tr>');
        }
            
    }

    getEmployeeCurrentData('<?= $data['model']->previous_data?>');

</script>
@endsection