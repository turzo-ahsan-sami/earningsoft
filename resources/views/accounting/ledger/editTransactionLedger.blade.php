@extends('layouts/acc_layout')
@section('title', '| Edit Ledger')
@section('content')
<?php
$totalProject=DB::table('gnr_project')->where('companyId', Auth::user()->company_id_fk)->select('id')->count();
$projectId=DB::table('acc_account_ledger')->where('id', $ledger->id)->select('projectBranchId')->first();
// dd($projectId);
$pro=$projectId->projectBranchId;
$allProjectsArray=array();
$allProjects=DB::table('gnr_project')->where('companyId', Auth::user()->company_id_fk)->select('id')->get();
foreach ($allProjects as $allProjectValue) {
array_push($allProjectsArray, $allProjectValue->id);
}
// print_r($allProjectsArray);
?>
<div class="row add-data-form" >
    <div class="col-md-12">
        <div class="col-md-2"></div>
        <div class="col-md-8 fullbody">
            <div class="viewTitle" style="border-bottom: 1px solid white;">
                <a href="{{url('viewLedger/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                </i>Ledger List</a>
            </div>
            <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <div class="panel-title">Edit Ledger Account</div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                            {!! Form::text('id', $value = $ledger->id, ['class' => 'form-control hidden', 'id' => 'id', 'type' => 'text']) !!}
                            <div class="form-group">
                                {!! Form::label('name', 'Name:', ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-10">
                                    {!! Form::text('name', $value = $ledger->name, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Ledger Name','autocomplete' => 'off']) !!}
                                    <emp><p id='namee' style="max-height:3px;"></p></emp>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('code', 'Code:', ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-10">
                                    {!! Form::text('code', $value = $ledger->code, ['class' => 'form-control', 'id' => 'code', 'type' => 'text', 'placeholder' => 'Enter Code','autocomplete' => 'off']) !!}
                                    <p id='codee' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('parentId', 'Parent:', ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-10">
                                    <select class ="form-control" id = "parentId" name="parentId">
                                        <option value="">Select Parent</option>
                                        <?php
                                        if($ledger->parentId>0){
                                        $parentId=DB::table('acc_account_ledger')->where('id', $ledger->parentId)->where('companyIdFk', Auth::user()->company_id_fk)->select('id','name','parentId')->first();
                                        $grandParentID=$parentId->id;
                                        while($parentId->parentId > 0) {
                                        $parentId=DB::table('acc_account_ledger')->where('id',$parentId->parentId)->where('companyIdFk', Auth::user()->company_id_fk)->select('id','name', 'parentId')->first();
                                        if($parentId->parentId==0){
                                        $grandParentID=$parentId->id;
                                        }
                                        }
                                        $parent=DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('id', $grandParentID)->orderBy('ordering', 'asc')->select('id','name','isGroupHead')->first();
                                        if($parent->isGroupHead){ echo "<option value='".$parent->id."'>".$parent->name."</option>"; }
                                        if($parent->isGroupHead==1){
                                        $children1=DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('parentId', $parent->id)->orderBy('ordering', 'asc')->select('id','name','isGroupHead')->get();
                                        foreach ($children1 as $child1) {
                                        if($child1->isGroupHead){ echo "<option value='".$child1->id."'>".$child1->name."</option>"; }
                                        // if($child1->isGroupHead){ echo "<option value='".$child1->id."'>".str_repeat('&nbsp;', (3*1)).$child1->name."</option>"; }
                                        if($child1->isGroupHead==1){
                                        $children2=DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('parentId', $child1->id)->orderBy('ordering', 'asc')->select('id','name','isGroupHead')->get();
                                        foreach ($children2 as $child2) {
                                        if($child2->isGroupHead){ echo "<option value='".$child2->id."'>".$child2->name."</option>";}
                                        if($child2->isGroupHead==1){
                                        $children3=DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('parentId', $child2->id)->orderBy('ordering', 'asc')->select('id','name','isGroupHead')->get();
                                        foreach ($children3 as $child3) {
                                        if($child3->isGroupHead){ echo "<option value='".$child3->id."'>".$child3->name."</option>"; }
                                        if($child3->isGroupHead==1){
                                        $children4=DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('parentId', $child3->id)->orderBy('ordering', 'asc')->select('id','name','isGroupHead')->get();
                                        foreach ($children4 as $child4) {
                                        if($child4->isGroupHead){ echo "<option value='".$child4->id."'>".$child4->name."</option>";}
                                        if($child4->isGroupHead==1){
                                        $children5=DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('parentId', $child4->id)->orderBy('ordering', 'asc')->select('id','name','isGroupHead')->get();
                                        foreach ($children5 as $child5) {
                                        if($child5->isGroupHead){ echo "<option value='".$child5->id."'>".$child5->name."</option>";}
                                        if($child5->isGroupHead==1){
                                        $children6=DB::table('acc_account_ledger')->where('parentId', $child5->id)->where('companyIdFk', Auth::user()->company_id_fk)->orderBy('ordering', 'asc')->select('id','name','isGroupHead')->get();
                                        foreach ($children6 as $child6) {
                                        if($child6->isGroupHead){ echo "<option value='".$child6->id."'>".$child6->name."</option>";}
                                        }
                                        }
                                        }
                                        }
                                        }
                                        }
                                        }
                                        }
                                        }
                                        }
                                        }
                                        }
                                        // $parent=DB::table('acc_account_ledger')->where('id', $ledger->parentId)->select('id','name')->first();
                                        // echo "<option value='".$parent->id."'>".$parent->name."</option>";
                                        } else {
                                        echo "<option value='0'>Grand Parent</option>";
                                        }
                                        ?>
                                    </select>
                                    <p id='parentIde' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <!-- <div class="form-group">
                                {!! Form::label('parentId', 'Parent:', ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-10">
                                    <select class ="form-control" id = "parentId" name="parentId">
                                        <?php
                                        if($ledger->parentId>0){
                                        $parent=DB::table('acc_account_ledger')->where('id', $ledger->parentId)->select('id','name')->first();
                                        echo "<option value='".$parent->id."'>".$parent->name."</option>";
                                        } else {
                                        echo "<option value='0'>Grand Parent</option>";
                                        }
                                        ?>
                                    </select>
                                    <p id='parentIde' style="max-height:3px;"></p>
                                </div>
                            </div> -->
                            <div class="form-group">
                                {!! Form::label('accountTypeId', 'Account Type:', ['class' => 'col-sm-2 control-label']) !!}
                                <script type="text/javascript">
                                // jQuery(document).ready(function($)
                                // {
                                //     $("#accountTypeId").select2({
                                //         placeholder: 'Select Account Type',
                                //         allowClear: true
                                //     }).on('select2-open', function()
                                //     {
                                //         // Adding Custom Scrollbar
                                //         // $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
                                //     });
                                // });
                                </script>
                                <div class="col-sm-10">
                                    <select class ="form-control" id = "accountTypeId" name="accountTypeId">
                                        <option value="">Select Account Type</option>
                                        @foreach($accountTypes as $accountType)
                                        <option value="{{$accountType->id}}">{{$accountType->name}}</option>
                                        @endforeach
                                    </select>
                                    <p id='accountTypeIde' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('ordering', 'Ordering:', ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-10">
                                    <?php
                                    $sisters=DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('parentId', $ledger->parentId)
                                    // ->whereNotIn('id', [$ledger->id])
                                    ->orderBy('ordering', 'asc')->select('id','name','ordering')->get();
                                    $immediateOrderedSister = $sisters->where('ordering', '<', $ledger->ordering)->max('ordering');
                                    ?>
                                    <select class ="form-control" id="ordering" name="ordering">
                                        <option value="">Select Order</option>
                                        <option value="0" >At First</option>
                                        @foreach ($sisters as $sister)
                                        <option value="{{$sister->ordering}}"><?php echo "After ";?>{{$sister->name}}</option>
                                        @endforeach
                                        <!-- <option value="<?php echo ++$value; ?>">Last</option> -->
                                    </select>
                                    <p id='orderinge' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('projectId', 'Project / Branch:', ['class' => 'col-md-2 control-label']) !!}
                                <div  class="col-md-10" id="projectDiv">
                                    {!! Form::checkbox('projectId', 0, false, array('class' => '', 'id'=>'allProject')) !!}
                                    <span style="font-size: 14px">  {!! Form::label(('All Projects')) !!}</span>
                                    {{--<strong>{!! ('All Projects') !!}</strong>--}}
                                    <br/>
                                    <?php  $projectId=DB::table('gnr_project')->where('companyId',Auth::user()->company_id_fk)->select('name','id')->get(); ?>
                                    @foreach($projectId as $project)
                                    <div class="col-md-3" id="{{$project->name}}">
                                        {!! Form::checkbox('projectId', $project->id, false, array('class' => 'singleProjectClass', 'id'=>'singleProjectId-'.$project->id,'autocomplete' => 'off', 'data-project'=>'')) !!}
                                        <span style="font-size: 11px">  {!! Form::label(($project->name)) !!}</span>
                                        {{--<span style="font-size: 11px"> {{$project->name}}</span>--}}
                                    </div>
                                    <div class="col-md-1">
                                        <span id="singleBranchModalIcon-{{$project->id}}"><a  href="javascript:;" class="branch-modal" data-id="{{$project->id}}" data-projectname="{{$project->name}}" >
                                            <span  style="font-size: 11px" class="fa fa-university" ></span>
                                        </a></span>
                                        <br/>
                                    </div>
                                    <script type="text/javascript">
                                    var count=0;
                                    $( document ).ready(function() {
                                    var totalProject="<?php echo $totalProject; ?>";
                                    var singleProjectId= "singleProjectId-"+"<?php echo $project->id;?>";
                                    var singleBranchModalIcon= "singleBranchModalIcon-"+"<?php echo $project->id;?>";
                                    $('#'+singleBranchModalIcon).hide();
                                    // $('#'+singleProjectId).prop('checked', false);
                                    //count projects which are previously checked(from db, onLoad)
                                    if($('#'+singleProjectId).is(":checked")){
                                    count++
                                    }
                                    //===============================================Function for (Checked Individual Project & assign Value)=========================================
                                    $('#'+singleProjectId).change(function () {
                                    if($(this).is(":checked")){
                                    //$('#'+singleBranchModalIcon).show();
                                    $('#'+singleBranchModalIcon).css("display","inline");
                                    var projectId = "<?php echo $project->id;?>";
                                    //                                                                var stringifyData=0;
                                    var data ;
                                    data = projectId+":"+0;
                                    $('#'+singleProjectId).attr('data-project',data);
                                    //                                                                alert(data);
                                    count++;
                                    }else{
                                    $('#'+singleBranchModalIcon).hide();
                                    $('#'+singleBranchModalIcon).removeClass('showBranchIcon');
                                    // .removeClass( [className ] )
                                    // $('.showBranchIcon').hide();
                                    $('#'+singleProjectId).attr('data-project','');
                                    //                                                                $('#'+singleProjectId).attr('data-project','');
                                    $('#allProject').prop('checked', false);
                                    count--;
                                    }
                                    if(count==totalProject){
                                    //                                                                alert('equal');
                                    $('#allProject').prop('checked', true);
                                    }
                                    //                                                            alert("alert for single project. & Total count: "+count);
                                    });
                                    //===================================================Function for (Checked All Project & assign Value)============================================
                                    $('#allProject').change(function () {
                                    if($(this).is(":checked")){
                                    count="<?php echo $totalProject; ?>";
                                    $('#'+singleBranchModalIcon).show();
                                    var projectId = "<?php echo $project->id;?>";
                                    //                                                                            alert(projectId);
                                    var data ;
                                    data = projectId+":"+0;
                                    $('#'+singleProjectId).attr('data-project',data);
                                    //                                                                alert(data);
                                    }else{
                                    $('#'+singleBranchModalIcon).hide();
                                    $('#'+singleProjectId).attr('data-project','');
                                    count=0;
                                    }
                                    });
                                    });
                                    </script>
                                    @endforeach
                                    {{--<p id='projectIde' style="max-height:3px;"></p>--}}
                                </div>
                                <div class="col-sm-12">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-10"><span id='projectIde' style="max-height:3px; color: red;"></span></div>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('description', 'Description:', ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-10">
                                    {!! Form::textarea('description', $value = $ledger->description, ['class' => 'form-control textarea', 'id' => 'description', 'rows' => 3, 'placeholder' => 'Enter description', 'type' => 'textarea','autocomplete' => 'off']) !!}
                                    <p id='descriptione' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('isGroupHead', 'Is Group Head:', ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-10">
                                    {{Form::checkbox('isGroupHead', 1, false, ['id' => 'isGroupHead', 'class' => ''])}}
                                    <p id='isGroupHeade' style="max-height:3px;"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9 text-right" >
                                    {!! Form::submit('Update', ['id' => 'add', 'class' => 'btn btn-info' ]) !!}
                                    {{--{!! Form::button('submit', ['id' => 'add', 'class' => 'btn btn-info' ]) !!}--}}
                                    {{ Form::reset('Reset', ['class' => 'btn btn-warning']) }}
                                    <a href="{{url('viewLedger/')}}" class="btn btn-danger closeBtn">Close</a>
                                    <span id="success" style="color:green; font-size:16px;" class="pull-right"></span>
                                </div>
                            </div>
                            {!! Form::close()  !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>
<div id="myModal" class="modal fade" style="margin-top:3%" data-backdrop="static" data-keyboard="false">
    {{-- <div id="myModal" class="modal fade" style="margin-top:3%"> --}}
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="clear:both"></h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(array('url' => '', 'id' => 'form1', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                    <div class="form-group hidd hidden">
                        {!! Form::label('id', 'ID:', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! Form::text('id', $value = null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text', 'readonly']) !!}
                            {{-- {!! Form::text('projectBranchId', $value = null, ['class' => 'form-control', 'id' => 'projectBranchId1', 'type' => 'text', 'readonly']) !!} --}}
                        </div>
                        {{-- <span id="projectBranchId"></span> --}}
                    </div>
                    <div class="form-group">
                        {!! Form::label('projectIdModal', 'Project:', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-9">
                            {!! Form::text('projectIdModal', $value = null, ['class' => 'form-control', 'readonly', 'id' => 'projectIdModal', 'type' => 'text']) !!}
                            <p id='projectIdModale' style="max-height:3px;"></p>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('branchId', 'Branches:', ['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-9" id="branchIdAppend">
                        </div>
                    </div>
                    {!! Form::close()  !!}
                    <div class="modal-footer">
                        <p id="MSGE" class="pull-left" style="color:red"></p>
                        <p id="MSGS" class="pull-left" style="color:green"></p>
                        {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn', 'id' => 'footer_action_button'] ) !!}
                        {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn',  'data-dismiss' => 'modal', 'id' => 'footer_action_button2'] ) !!}
                        {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-warning', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_dismis'] ) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
    <script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
    {{-- <script src="{{asset('js/jquery-3.1.1.min.js')}}"></script> --}}
    {{-- <link rel="stylesheet" href="{{ asset('js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('js/select2/select2-bootstrap.css') }}">
    <script src="{{ asset('js/select2/select2.min.js') }}"></script> --}}
    <script type="text/javascript">
    // jQuery(document).ready(function($)
    // {
    //     $("#accountTypeId").select2();
    // });
    </script>
    <script type="text/javascript">
    $(document).ready(function(){
    // code validation
    //=============================================For Display the Previous Data from DB===========================================================
    var accountTypeId = "<?php echo $ledger->accountTypeId ;?>";
    $('#accountTypeId').val(accountTypeId);
    // var ordering = "<?php echo ($ledger->ordering)-1;?>";
    var ordering = "<?php echo $immediateOrderedSister;?>";
    // alert(ordering);
    $('#ordering').val(ordering);
    var isGroupHead  = "<?php echo $ledger->isGroupHead;?>";
    if(isGroupHead==1){
    $('#isGroupHead ').prop('checked', true);
    }
    var parentIdValue = "<?php echo $ledger->parentId;?>";
    $('#parentId').val(parentIdValue);
    $('#parentId').prop("disabled", true);
    // $('#ordering').prop("disabled", true);
    // $('#accountTypeId').prop("disabled", true);
    // $('#ordering [value=0]').html("At First");
    // $("select option:last").attr("selected", "selected");
    // $("#ordering option:last").html("At First");
    var totalProject="<?php echo $totalProject; ?>";
    // alert(totalProject);
    // debugger;
    var ar = <?php echo $pro; ?>;
    // alert(ar);
    var totalProjectCounterFromDB=0;
    // var singleBranchModalIconChecked=[];
    var a=ar.join();
    //alert(a);
    var splits = a.split(',');
    //alert(splits);
    var data=[];
    var temp;
    var k=0;
    // $js_array = json_encode($allProjectsArray);
    // alert(js_array);
    var allProjectsArray =<?php echo json_encode($allProjectsArray); ?>;
    // alert(allProjectsArray);
    $.each(ar, function (key, dataArray) {
    var res = dataArray.split(":");
    var projectIdValue;
    var branchIdValue;
    for(var i=0; i<2; i++){
    if(i==0){
    projectIdValue=res[i];
    // alert(projectIdValue);
    if(projectIdValue==0){
    // alert("all project");
    $('#allProject').prop('checked', true);
    $('#projectDiv').find('.singleProjectClass').prop('checked', true);
    $.each(allProjectsArray,function(index2,value2){
    // alert(value2);
    var singleProjectId= "singleProjectId-"+value2;
    var singleBranchModalIconChecked= "singleBranchModalIcon-"+value2;
    $('#'+singleBranchModalIconChecked).addClass('showBranchIcon');
    $('#'+singleProjectId).attr('data-project', value2+":"+0);
    // totalProjectCounterFromDB++;
    });
    }else{
    var singleProjectId= "singleProjectId-"+projectIdValue;
    var singleBranchModalIconChecked= "singleBranchModalIcon-"+projectIdValue;
    $('#'+singleProjectId).prop('checked', true);
    $('#'+singleBranchModalIconChecked).addClass('showBranchIcon');
    $('#'+singleProjectId).attr('data-project',dataArray);
    }
    }else if(i==1){
    branchIdValue=res[i];
    }
    }
    totalProjectCounterFromDB++;
    });
    // alert(totalProjectCounterFromDB);
    if(totalProjectCounterFromDB==totalProject){
    $('#allProject').prop('checked', true);
    }
    var singleBranchModal=[];
    for(var i=0; i<totalProject; i++){
    singleBranchModal[i]= "singleBranchModalIcon-"+(i+1);
    }
    //alert(singleBranchModal);
    //=============For Display the Previous Data from DB==========
    // =================Change Ordering according on Parent=================
    $("#parentId").change(function () {
    var parentId = this.value;
    // alert(parentId);
    var csrf = "<?php echo csrf_token(); ?>";
    $.ajax({
    type: 'post',
    url: './filteringOrderByParent',
    data: {parentId: parentId , _token: csrf},
    dataType: 'json',
    success: function (orderingList) {
    // alert(JSON.stringify(orderingList));
    $("#ordering").empty();
    $("#ordering").prepend('<option  value="0">At First</option>');
    $("#ordering").prepend('<option selected="selected" value="">Select Order</option>');
    $.each(orderingList, function( value ,index){
    $('#ordering').append("<option value='"+index+"'>After "+value+"</option>");
    });
    // $('#ordering [value=1]').html("At First");
    },
    error: function(_response){
    alert("Error");
    }
    });
    });
    // =================End Change Ordering according on Parent=================
    //==================================Function for Checked All Projects(only Checked, not Value) ======================================
    $('#allProject').change(function () {
    if($(this).is(":checked")){
    $('#projectDiv').find('.singleProjectClass').prop('checked', true);
    }else{
    $('#projectDiv').find('.singleProjectClass').prop('checked', false);
    }
    });
    //========End Function for Checked All Projects(only Checked, not Value) ======
    //======================================Function for Modal Icon & (Checked Individual Project & assign Value)==========================================
    var doneCounter=0;
    $(document).on('click', '.branch-modal', function(e) {
    //=================Functions for prevent esc key & disable for click outside event=============
    $(document).keydown(function(e) {
    // if (e.keyCode == 27) return false;
    if(e.keyCode == 27) {
    e.preventDefault();
    return false;
    }
    });
    $('#myModal').modal({
    backdrop: 'static',
    keyboard: false
    });
    // ====End Functions for prevent esc key & disable for click outside event=====
    var projectId = null;
    // alert(projectId);
    var singleProjectId = null;
    //alert(projectId);
    $('.errormsg').empty();
    $('#MSGE').empty();
    $('#MSGS').empty();
    $('#footer_action_button').text(" Done");
    $('#footer_action_button').addClass('glyphicon glyphicon-check');
    //$('#footer_action_button').removeClass('glyphicon-trash');
    $('#footer_action_button_dismis').text(" Close");
    $('#footer_action_button_dismis').addClass('dismiss');
    $('#footer_action_button_dismis').addClass('glyphicon glyphicon-remove');
    $('.actionBtn').addClass('btn-success');
    $('.actionBtn').removeClass('btn-danger');
    $('.actionBtn').addClass('done');
    // $('.modal-title').text('Update Data');
    $('.modal-title').text('Select Branches');
    $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
    $('.modal-dialog').css('width','50%');
    $('.form-horizontal').show();
    $('#projectIdModal').val($(this).data('projectname'));
    //alert(($(this).data('id')));
    // $('#projectBranchId').html($(this).data('project'));
    $('#footer_action_button2').hide();
    $('#footer_action_button').show();
    $('.actionBtn').removeClass('delete');
    $('#myModal').modal('show');
    projectId = $(this).data('id'); //alert(projectId);
    singleProjectId='singleProjectId-'+projectId;
    // alert(singleProjectId);
    var projectBranchId=$('#'+singleProjectId).attr('data-project');
    $.ajax({
    type: 'post',
    url: './projectIdSend',
    data: {
    '_token': $('input[name=_token]').val(),
    'projectId': projectId
    },
    dataType: 'json',
    success: function( _response ){
    $('#branchIdAppend span').remove();
    //                        alert(JSON.stringify(_response));
    $('#branchIdAppend').append('<span><input  type="checkbox" class="allBranchClass" id="allBranchId" name="branchId" value="'+'0'+'"  >&nbsp;&nbsp;All Branches<span><br/>');
    $('#branchIdAppend').append('<span><input  type="checkbox" class="singleBranchClass" id="sb-1" name="branchId" value="'+'1'+'"  >&nbsp;&nbsp;Head Office<span><br/>');
    // checked="checked"
    var x=0;
    var y=0;
    var singleBranchArray=[];
    // var r=0;
    singleBranchArray[x]="sb-1";
    x++;
    $.each(_response, function( index, value ){
    $('#branchIdAppend').append('<span><input  type="checkbox" class="singleBranchClass" id="sb-'+index+'" name="branchId" value="'+index+'">&nbsp;&nbsp;'+value+'<span><br/>');
    singleBranchArray[x]="sb-"+index;
    x++;
    // alert(x);
    });
    // alert(singleBranchArray.length);
    $('.singleBranchClass').change(function () {
    if(!$(this).is(":checked")){
    $('#allBranchId').prop('checked', false);
    }
    else{
    var r=0;
    for(var i=0; i<singleBranchArray.length; i++){
    if($('#'+singleBranchArray[i]).is(":checked")){
    r++;
    }
    }
    // alert(r);
    if(singleBranchArray.length==r){
    $('#allBranchId').prop('checked', true);
    }
    }
    });
    var data = [];
    var projectBranchIdSplit=projectBranchId.split(',');
    //alert(projectBranchIdSplit);
    $.each(projectBranchIdSplit, function (key1, dataArray1) {
    // alert(key1);
    // alert(dataArray1);
    var colonSplit = dataArray1.split(":");
    // alert(dataArray1);
    for(var i=0; i<2; i++){
    if(i==0){
    var value=colonSplit[i];
    // alert(value);
    }else if(i==1){
    var branchId=colonSplit[i];
    // alert(branchId);
    if(branchId==0){
    $('#allBranchId').prop('checked', true);
    }else{
    var branchIdIndex="sb-"+branchId;
    // alert(branchIdIndex);
    $('#'+branchIdIndex).prop('checked', true);
    }
    }
    }
    });
    if($('#allBranchId').is(":checked")){
    $('#branchIdAppend').find('.singleBranchClass').prop('checked', true);
    }
    $('#allBranchId').change(function () {
    if($(this).is(":checked")){
    $('#branchIdAppend').find('.singleBranchClass').prop('checked', true);
    }else{
    $('#branchIdAppend').find('.singleBranchClass').prop('checked', false);
    }
    });
    $('.modal-footer').on('click', '.done', function() {
    if($('#allBranchId').is(":checked")){
    data[0] = projectId+":"+0;
    $('#'+singleProjectId).attr('data-project',data);
    doneCounter=0;
    }else{
    $('#branchIdAppend input:checked').each(function(i){
    data[i] = projectId+":"+$(this).val();
    // alert(singleProjectId);
    $('#'+singleProjectId).attr('data-project',data);
    // alert('bye');
    // alert(data);
    });
    doneCounter=1;
    }
    $('#myModal').modal('hide');
    //                            debugger;
    singleProjectId=null;
    // alert(singleProjectId);
    });
    $('.modal-footer').on('click', '.dismiss', function() {
    projectId=null;
    // alert(projectId);
    singleProjectId = null;
    // alert(singleProjectId);
    //alert("alert from Close");
    });
    }
    });
    });
    //================== End Function for Modal Icon & (Checked Individual Project & assign Value)==============================
    //=============================================================Final Submission=======================================================================
    $('form').submit(function( event ) {
    event.preventDefault();
    // alert(doneCounter);
    var singleProjectCount=0;
    var singleProjectC=0;
    var totalProject="<?php echo $totalProject; ?>";
    var dataProjectLength=[] ;
    var allProjectsId=<?php echo json_encode($allProjectsArray); ?>;
    // alert(allProjectsId);
    $.each(allProjectsId, function (key5, dataArray5) {
    // alert(key5);
    // alert(dataArray5);
    var singleProjectId= "singleProjectId-"+dataArray5;
    if($('#'+singleProjectId).is(':checked')){
    var dataProjectSplit= $('#'+singleProjectId).attr('data-project');
    // alert(dataProjectLength[singleProjectCount]);
    var tem=dataProjectSplit.split(',');
    dataProjectLength[singleProjectC]=tem.length;
    singleProjectC++;
    }
    // alert(singleProjectC);
    });
    // alert(dataProjectLength);
    // for (var i = 1; i <= totalProject; i++) {
    //     var singleProjectId= "singleProjectId-"+i;
    //     if($('#'+singleProjectId).is(':checked')){
    //         var dataProjectSplit= $('#'+singleProjectId).attr('data-project');
    //         // alert(dataProjectLength[singleProjectCount]);
    //         var tem=dataProjectSplit.split(',');
    //         dataProjectLength[singleProjectC]=tem.length;
    //     }
    //         singleProjectC++;
    // }
    // alert(dataProjectLength);
    var single;
    // alert(single);
    $.each(dataProjectLength, function (key3, dataArray3) {
    if(dataArray3==1){
    single=true;
    }else{
    // alert("else");
    single=false;
    return false;
    }
    });
    // $(".done").trigger("click");
    // alert(doneCounter);
    if($('#allProject').is(":checked") && doneCounter==0 && single==true){
    // if($('#allProject').is(":checked") && doneCounter==0 ){
    var dataArray=[] ;
    dataArray[0] = 0+":"+0;
    // alert("allProject is checked");
    }else{
    // alert("allProject is not checked");
    var singleProjectCount=0;
    var dataArray=[] ;
    var allProjectsId=<?php echo json_encode($allProjectsArray); ?>;
    // alert(allProjectsId);
    $.each(allProjectsId, function (key5, dataArray5) {
    // alert(key5);
    // alert(dataArray5);
    var singleProjectId= "singleProjectId-"+dataArray5;
    if($('#'+singleProjectId).is(':checked')){
    // alert(singleProjectId);
    dataArray[singleProjectCount]= $('#'+singleProjectId).attr('data-project');
    singleProjectCount++;
    }
    });
    // for (var i = 1; i <= totalProject; i++) {
    //     var singleProjectId= "singleProjectId-"+i;
    //     if($('#'+singleProjectId).is(':checked')){
    //         dataArray[singleProjectCount]= $('#'+singleProjectId).attr('data-project');
    //         singleProjectCount++;
    //     }
    // }
    if(singleProjectCount==0){
    $('#projectIde').html("Please Select Any Project!!!");
    return false;
    }
    }
    var stringifyDataArray=(JSON.stringify(dataArray));
    // alert(stringifyDataArray);
    var id = $("#id").val();
    var name = $("#name").val();
    var code = $("#code").val();
    var parentId = $("#parentId option:selected").val();
    var accountTypeId = $("#accountTypeId option:selected").val();
    var ordering = $("#ordering option:selected").val();
    var description = $("#description").val();
    if($("#isGroupHead").is(':checked')){
    var isGroupHead = 1;
    }else {
    var isGroupHead = 0;
    }
    var csrf = "<?php echo csrf_token(); ?>";
    // alert(id);
    // alert(name);
    // alert(code);
    // alert(parentId);
    // alert(accountTypeId);
    // alert(ordering);
    // alert(description);
    // alert(isGroupHead);
    // alert(csrf);
    $.ajax({
    type: 'post',
    url: './updateLedgerItem',
    //             data: $('form').serialize(),
    data: { id:id, name:name, code:code, parentId: parentId, accountTypeId: accountTypeId, ordering: ordering, description: description, isGroupHead: isGroupHead, stringifyDataArray:stringifyDataArray, _token: csrf },
    dataType: 'json',
    success: function( _response ){
    // alert(JSON.stringify(_response));
    if (_response.errors) {
    if (_response.errors['name']) {
    $('#namee').empty();
    $('#namee').append('<span style="color:red;">'+_response.errors.name+'</span>');
    return false;
    }
    if (_response.errors['code']) {
    $('#codee').empty();
    $('#codee').append('<span style="color:red;">'+_response.errors.code+'</span>');
    return false;
    }
    if (_response.errors['parentId']) {
    $('#parentIde').empty();
    $('#parentIde').append('<span style="color:red;">'+_response.errors.parentId+'</span>');
    return false;
    }if (_response.errors['accountTypeId']) {
    $('#accountTypeIde').empty();
    $('#accountTypeIde').append('<span style="color:red;">'+_response.errors.accountTypeId+'</span>');
    return false;
    }
    if (_response.errors['ordering']) {
    $('#orderinge').empty();
    $('#orderinge').append('<span style="color:red;">'+_response.errors.ordering+'</span>');
    return false;
    }
    }else {
    window.location.href = '{{url('viewLedger/')}}';
    }
    },
    error: function( _response ){
    // Handle error
    alert('error');
    }
    });
    });
    //=========End Final Submission===
    // $("input").keyup(function(){
    //     var name = $("#name").val();
    //     if(name){$('#namee').hide();}else{$('#namee').show();}
    //     var code = $("#code").val();
    //     if(code){$('#codee').hide();}else{$('#codee').show();}
    //     var clientCode = $("#clientCode").val();
    //     if(clientCode){$('#clientCodee').hide();}else{$('#clientCodee').show();}
    // });
    $('select').on('change', function (e) {
    var parentId = $("#parentId").val();
    if(parentId){$('#parentIde').hide();}else{$('#parentIde').show();}
    var accountTypeId = $("#accountTypeId").val();
    if(accountTypeId){$('#accountTypeIde').hide();}else{$('#accountTypeIde').show();}
    var ordering = $("#ordering").val();
    if(ordering){$('#orderinge').hide();}else{$('#orderinge').show();}
    var projectId = $("#projectId").val();
    if(projectId){$('#projectIde').hide();}else{$('#projectIde').show();}
    var branchId = $("#branchId").val();
    if(branchId){$('#branchIde').hide();}else{$('#branchIde').show();}
    });
    $("textarea").keyup(function(){
    var description = $("#description").val();
    if(description){$('#descriptione').hide();}else{$('#descriptione').show();}
    });
    // $('#parentId').prop("disabled", true);
    // $('#ordering').prop("disabled", true);
    $('#code').on('input', function() {
    // this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
    });
    });
    </script>