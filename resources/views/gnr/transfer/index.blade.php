@extends('hr_main')
@section('title', '| '.$data['pageTitle'])
@section('content')
    @php
        $moduleIdHr = \App\ConstValue::MODULE_ID_HR;
        $functionCodeTransfer = \App\ConstValue::FUNCTION_CODE_TRANSFER;
        $subFunctionIdCreate = \App\ConstValue::SUB_FUNCTION_ID_CREATE;
        $subFunctionIdEdit = \App\ConstValue::SUB_FUNCTION_ID_EDIT;
        $subFunctionIdDelete = \App\ConstValue::SUB_FUNCTION_ID_DELETE;
        $subFunctionIdView = \App\ConstValue::SUB_FUNCTION_ID_VIEW;
    @endphp
    <div class="row">
        <div class="col-md-12">
            <div class="viewTitle" style="border-bottom: 1px solid white;"></div>
        </div>
        <div class="col-md-12">
            <div class="panel panel-default panel-border">
                
                <div class="panel-heading" style="padding-bottom:0px">
                    <div class="panel-title">
                       {!! $data['pageTitle'] !!}
                    </div>
                    <div class="panel-options">
                        @if(Auth::user()->canSee($moduleIdHr,$functionCodeTransfer,$subFunctionIdCreate))
                            <a href="{!! $data['addBtnUrl'] !!}" class="btn btn-white pull-right"><i
                                        class="fa fa-plus-circle"></i> New
                            </a>
                        @endif
                    </div>
                </div>

                <div class="panel-body panelBodyView">
                    <table class="table table-striped table-bordered table-condensed text-primary"
                           id="employee-data">
                        <thead>
                        <tr>
                            <th width="30">SL#</th>
                            <th><?= $model->attributes()['users_id_fk']?></th>
                            <th>Employee ID</th>
                            <th>Recruitment Type</th>
                            <th><?= $model->attributes()['pre_company_id_fk']?></th>
                            <th><?= $model->attributes()['pre_project_id_fk']?></th>
                            <th><?= $model->attributes()['pre_branch_id_fk']?></th>
                            <th>Previous Branch Duration</th>
                            <th><?= $model->attributes()['cur_project_id_fk']?></th>
                            <th><?= $model->attributes()['cur_branch_id_fk']?></th>
                            <th><?= $model->attributes()['status']?></th>

                            <th style="width: 80px">Action</th>

                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if(count($data['model']) > 0):
                        $i = 0; foreach ($data['model'] as $row){ $i++;?>
                        <tr>
                            <td style="width: 3%; text-align: center;"><?= $i?></td>
                            <td><?= @$row->user->employee->emp_name_english?></td>
                            <td><?= @$row->user->employee->emp_id?></td>
                            <td><?= @$row->user->employee->organization->recruitmentType->name?></td>
                            <td><?= @$row->preCompany->name?></td>
                            <td><?= @$row->preProject->name?></td>
                            <td><?= @$row->preBranch->name?></td>
                            <td>
                                <?php
                                $jobDuration = $model->getPreviousBranchJobDuration($row);
                                echo @$data['easycode']->get_date_diff(@$jobDuration['from'], @$jobDuration['to']);
                                ?>
                            </td>
                            <td><?= @$row->project->name?></td>
                            <td><?= @$row->branch->name?></td>
                            <td><?= $row->status?></td>

                            <td class="text-center">
                                @if(Auth::user()->canSee($moduleIdHr,$functionCodeTransfer,$subFunctionIdView))
                                    <a href="{!! url('hr/transfer/view/'.$row->id) !!}" class="btn btn-xs btn-white"
                                       title="View"><i class="fa fa-eye"></i></a>
                                @endif
                                <?php if($row->status == 'Pending'):?>
                                @if(Auth::user()->canSee($moduleIdHr,$functionCodeTransfer,$subFunctionIdEdit))

                                    <a href="{!! url('hr/transfer/update/'.$row->id) !!}" class="btn btn-xs btn-white"
                                       title="Update"><i class="fa fa-pencil"></i></a>
                                @endif
                                <?php endif;?>
                            </td>

                        </tr>
                        <?php } endif;?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerAssets')
    <script>
        $(document).ready(function() {
            $('.table').dataTable({
                // "bPaginate": false,
                "bLengthChange": false,
                "bFilter": true,
                "bInfo": false,
                "lengthMenu": [ [20, 20, 30, "All"] ],
            });
        } );
    </script>
@endsection