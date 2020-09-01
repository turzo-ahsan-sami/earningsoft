@extends('hr_main')
@section('title', '| '.$data['pageTitle'])
@section('content')
    @php
        $moduleIdHr = \App\ConstValue::MODULE_ID_HR;
        $functionCodeTerminate = \App\ConstValue::FUNCTION_CODE_TERMINATE_LIST;
        $functionCodeTerminateCancel = \App\ConstValue::FUNCTION_CODE_TERMINATE_CANCEL;
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
                </div>
                <div class="panel-body">

                    <div class="panel-options">
                        @if(Auth::user()->canSee($moduleIdHr,$functionCodeTerminate,$subFunctionIdCreate))
                            <a href="{!! $data['addBtnUrl'] !!}" class="btn btn-white pull-right">
                                <i class="fa fa-plus-circle"></i>
                                {!! $data['addBtnLabel'] !!}</a>
                        @endif
                    </div>

                    <table class="table table-striped table-bordered table-condensed text-primary dataTables"
                           id="employee-data">
                        <thead>
                        <tr>
                            <th width="30">SL#</th>
                            <th class="text-center"><?= $model->attributes()['terminate_date']?></th>
                            <th class="text-center"><?= $model->attributes()['effect_date']?></th>
                            <th class="text-center">Job Duration</th>
                            <th class="text-left">Employee Name</th>
                            <th class="text-center">Employee ID</th>
                            <th class="text-left">Recruitment Type</th>
                            <th class="text-center"><?= $model->attributes()['position']?></th>
                            <th class="text-left">Branch</th>
                            <th class="text-left"><?= $model->attributes()['reason']?></th>
                            <th class="text-center"><?= $model->attributes()['status']?></th>
                            <th style="width: 80px" class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        @if(count($data['model'])>0)
                            @foreach ($data['model'] as $row)
                                <tr>
                                    <td class="text-center"><?= $loop->iteration ?></td>
                                    <td class="text-center">
                                        <?= ($row->terminate_date != null) ? date("d-m-Y", strtotime($row->terminate_date)) : ''?>
                                    </td>
                                    <td class="text-center">
                                        <?= ($row->effect_date != null) ? date("d-m-Y", strtotime($row->effect_date)) : ''?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $ddate = date("Y-m-d");
                                        if ($row->effect_date != null) {
                                            $ddate = date("Y-m-d", strtotime($row->effect_date));
                                        }
                                        echo $data['easycode']->get_date_diff($row->user->employee->organization->joining_date ?? "" , $ddate);
                                        ?>
                                    </td>
                                    <td class="text-left"><?= $row->user->employee->emp_name_english ?? ''?></td>
                                    <td class="text-center"><?= @$row->user->employee->emp_id ?? ''?></td>
                                    <td class="text-left"><?= @$row->user->employee->organization->recruitmentType->name?></td>
                                    <td class="text-center"><?= $row->position?></td>
                                    <td class="text-left"><?= @$row->user->employee->organization->branch->name?></td>
                                    <td class="text-left"><?= @$row->reasonList->title?></td>
                                    <td class="text-center"><?= $row->status ?? ''?></td>
                                    <td class="text-center">
                                        @if(Auth::user()->canSee($moduleIdHr,$functionCodeTerminate,$subFunctionIdView))
                                            <a href="{!! url('hr/terminateInfo/view/'.$row->id) !!}"
                                               class="btn btn-xs btn-white" title="View"><i class="fa fa-eye"></i></a>
                                        @endif

                                        @if(Auth::user()->canSee($moduleIdHr,$functionCodeTerminate,$subFunctionIdEdit))
                                            <a href="{!! url('hr/terminateInfo/update/'.$row->id) !!}"
                                               class="btn btn-xs btn-white" title="Update"><i class="fa fa-pencil"></i></a>
                                        @endif

                                        @if($row->status=='Pending')
                                            @if(Auth::user()->canSee($moduleIdHr,$functionCodeTerminate,$subFunctionIdDelete))

                                                <a href="{!! url('hr/terminateInfo/delete') !!}"
                                                   data-id="<?= $row->id?>"
                                                   class="btn btn-xs btn-danger delete-action" title="Delete"><i
                                                            class="fa fa-times"></i></a>
                                            @endif
                                        @endif

                                        @if($row->status=='Approved')
                                            @if(Auth::user()->canSee($moduleIdHr,$functionCodeTerminateCancel,$subFunctionIdEdit))
                                                <a href="{!! url('hr/terminateInfo/cancel/'.$row->id) !!}"
                                                   class="btn btn-xs btn-danger" title="Cancel"><i
                                                            class="fa fa-ban"></i></a>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>

                    <div class="pull-right">
                        <?= $data['model']->links()?>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

