@extends('layouts/acc_layout')
@section('title', '| New Ledger')
@section('content')


    <?php
        $totalProject=DB::table('gnr_project')->select('id')->count();
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
                        <div class="panel-title">Add Ledger Account</div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">

                                {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                <div class="form-group">
                                    {!! Form::label('name', 'Name:', ['class' => 'col-sm-2 control-label']) !!}
                                    <div class="col-sm-10">
                                        {!! Form::text('name', $value = null, ['class' => 'form-control', 'id' => 'name', 'type' => 'text', 'placeholder' => 'Enter Ledger Name','autocomplete' => 'off']) !!}
                                        <emp><p id='namee' style="max-height:3px;"></p></emp>
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('code', 'Code:', ['class' => 'col-sm-2 control-label']) !!}
                                    <div class="col-sm-10">
                                        {!! Form::text('code', $value = null, ['class' => 'form-control', 'id' => 'code', 'type' => 'text', 'placeholder' => 'Enter Code','autocomplete' => 'off']) !!}
                                        <p id='codee' style="max-height:3px;"></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('parentId', 'Parent:', ['class' => 'col-sm-2 control-label']) !!}
                                    <div class="col-sm-10">
                                        <select class ="form-control" id = "parentId" name="parentId">
                                            <?php
                                            $decryptedParentId=decrypt($encryptedParentId);
                                            if($decryptedParentId>0){
                                                $parent=DB::table('acc_account_ledger')->where('id', $decryptedParentId)->where('companyIdFk', Auth::user()->company_id_fk)->select('id','name','accountTypeId')->first();
                                                echo "<option value='".$parent->id."'>".$parent->name."</option>";
                                            } else {
                                                echo "<option value='0'>Grand Parent</option>";
                                            }
                                            ?>
                                        </select>
                                        <p id='parentIde' style="max-height:3px;"></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('accountTypeId', 'Account Type:', ['class' => 'col-sm-2 control-label']) !!}
                                    <div class="col-sm-10">
                                        <select class ="form-control" id = "accountTypeId" name="accountTypeId">
                                            <option value="">Select Account Type</option>
                                            <?php if($decryptedParentId==0){
                                                $accountTypeGrandParents=DB::table('acc_account_type')->where('parentId', 0)->select('id','name')->get();
                                                foreach($accountTypeGrandParents as $accountTypeGrandParent){
                                                    echo "<option value='".$accountTypeGrandParent->id."'>".$accountTypeGrandParent->name."</option>";
                                                }
                                            }elseif ($parent->accountTypeId>=1 && $parent->accountTypeId<=5) {
                                                $accountTypeParent=DB::table('acc_account_type')->where('id', 1)->select('id','name')->first();
                                                $accountTypes=DB::table('acc_account_type')->where('parentId', 1)->select('id','name')->get();
                                                    echo "<option value='".$accountTypeParent->id."'>".$accountTypeParent->name."</option>";
                                                foreach($accountTypes as $accountType){
                                                    echo "<option value='".$accountType->id."'>".str_repeat('&nbsp;', 3).$accountType->name."</option>";
                                                }
                                            }elseif ($parent->accountTypeId>=6 && $parent->accountTypeId<=8) {
                                                $accountTypeParent=DB::table('acc_account_type')->where('id', 6)->select('id','name')->first();
                                                $accountTypes=DB::table('acc_account_type')->where('parentId', 6)->select('id','name')->get();
                                                    echo "<option value='".$accountTypeParent->id."'>".$accountTypeParent->name."</option>";
                                                foreach($accountTypes as $accountType){
                                                    echo "<option value='".$accountType->id."'>".str_repeat('&nbsp;', 3).$accountType->name."</option>";
                                                }
                                            }elseif ($parent->accountTypeId>=9 && $parent->accountTypeId<=11) {
                                                $accountTypeParent=DB::table('acc_account_type')->where('id', 9)->select('id','name')->first();
                                                $accountTypes=DB::table('acc_account_type')->where('parentId', 9)->select('id','name')->get();
                                                    echo "<option value='".$accountTypeParent->id."'>".$accountTypeParent->name."</option>";
                                                foreach($accountTypes as $accountType){
                                                    echo "<option value='".$accountType->id."'>".str_repeat('&nbsp;', 3).$accountType->name."</option>";
                                                }
                                            }elseif ($parent->accountTypeId==12) {
                                                $accountTypes=DB::table('acc_account_type')->where('id', 12)->select('id','name')->get();
                                                foreach($accountTypes as $accountType){
                                                    echo "<option value='".$accountType->id."'>".$accountType->name."</option>";
                                                }
                                            }elseif ($parent->accountTypeId==13) {
                                                $accountTypes=DB::table('acc_account_type')->where('id', 13)->select('id','name')->get();
                                                foreach($accountTypes as $accountType){
                                                    echo "<option value='".$accountType->id."'>".$accountType->name."</option>";
                                                }
                                            }

                                            ?>

                                        </select>
                                        <p id='accountTypeIde' style="max-height:3px;"></p>
                                    </div>
                                </div>

{{--                                 <div class="form-group">
                                    {!! Form::label('accountTypeId', 'Account Type:', ['class' => 'col-sm-2 control-label']) !!}
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
 --}}
                                <div class="form-group">
                                    {!! Form::label('ordering', 'Ordering:', ['class' => 'col-sm-2 control-label']) !!}
                                    <div class="col-sm-10">
                                        <?php
                                            $sisters=DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('parentId', $decryptedParentId)->orderBy('ordering', 'asc')->select('id','name','ordering')->get();

                                        ?>
                                        <select class ="form-control" id="ordering" name="ordering">
                                            <option value="">Select Order</option>
                                            <?php if(!$sisters){ ?>
                                            <option value="0" >At First</option>
                                            <?php }else{ ?>
                                            <option value="0" >At First</option>
                                            @foreach ($sisters as $sister)
                                                <option value="{{$sister->ordering}}"><?php echo "After ";?>{{$sister->name}}</option>
                                            @endforeach
                                            <?php } ?>
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
                                        <?php  $projectId=DB::table('gnr_project')->select('name','id')->where('companyId',Auth::user()->company_id_fk)->get(); ?>
                                        @foreach($projectId as $project)


                                            <div class="col-md-3" id="{{$project->name}}">
                                                {!! Form::checkbox('projectId', $project->id, false, array('class' => 'singleProjectClass', 'id'=>'singleProjectId-'.$project->id,'autocomplete' => 'off', 'data-project'=>'')) !!}

                                                <span style="font-size: 11px">  {!! Form::label(($project->name)) !!}</span>
                                                {{--<span style="font-size: 11px"> {{$project->name}}</span>--}}
                                            </div>
                                            <div class="col-md-1">
                                                    <span id="singleBranchModalIcon-{{$project->id}}"><a  href="javascript:;" class="branch-modal" data-id="{{$project->id}}" data-projectname="{{$project->name}}">
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

//===================================================Function for (Checked Individual Project & assign Value)================================================
                                                    $('#'+singleProjectId).change(function () {
                                                        if($(this).is(":checked")){
                                                            $('#'+singleBranchModalIcon).show();
                                                            var projectId = "<?php echo $project->id;?>";
//                                                                var stringifyData=0;
                                                            var data ;
                                                            data = projectId+":"+0;
                                                            $('#'+singleProjectId).attr('data-project',data);
//                                                                alert(data);
                                                            count++;
                                                        }else{
                                                            $('#'+singleBranchModalIcon).hide();
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


//===================================================Function for (Checked All Project & assign Value)===================================================
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
                                        {!! Form::textarea('description', $value = null, ['class' => 'form-control textarea', 'id' => 'description', 'rows' => 3, 'placeholder' => 'Enter description', 'type' => 'textarea','autocomplete' => 'off']) !!}
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
                                    <div class="col-sm-9 text-right">

                                        {!! Form::submit('submit', ['id' => 'add', 'class' => 'btn btn-info' ]) !!}
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
                        </div>
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


<script type="text/javascript">
    $(document).ready(function(){

        // code validation
        $('#code').on('blur', function() {
            var codeInput = $(this).val();
            var parentId = {{ $decryptedParentId }};

            $.ajax({
                type: 'post',
                url: './checkUniqueLedgerCode',
                data: {code:codeInput, parentId: parentId, _token: $('input[name=_token]').val()},
                dataType: 'json',

                success: function( data ){
                    if(data){

                        $('#code').val('');
                        alert('Warning: Code already exists at this level!');
                    }
                },
                error: function( _response ){
                    // Handle error
                    alert('error');
                }
            });
        });

//==================================Function for Checked All Projects(only Checked, not Value) ======================================
        $('#allProject').change(function () {
            if($(this).is(":checked")){
                $('#projectDiv').find('.singleProjectClass').prop('checked', true);
            }else{
                $('#projectDiv').find('.singleProjectClass').prop('checked', false);
            }
        });

//======================================Function for Modal Icon & (Checked Individual Project & assign Value)==========================================
var doneCounter=0;
        $(document).on('click', '.branch-modal', function() {

            $(document).keydown(function(e) {
                if (e.keyCode == 27) return false;
            });

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
            $('.modal-title').text('Select Branches');
            $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
            $('.modal-dialog').css('width','50%');
            $('.form-horizontal').show();
            $('#projectIdModal').val($(this).data('projectname'));   //alert(($(this).data('id')));
            $('#footer_action_button2').hide();
            $('#footer_action_button').show();
            $('.actionBtn').removeClass('delete');
            $('#myModal').modal('show');

            var projectId = $(this).data('id');
//            alert(projectId);

            var singleProjectId= "singleProjectId-"+projectId;

            var projectBranchId=$('#'+singleProjectId).attr('data-project');
            // alert(projectBranchId);

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
                    var x=0;
                    var y=0;
                    var singleBranchArray=[];
                    // var r=0;

                    singleBranchArray[x]="sb-1";
                    x++;
                    $.each(_response, function( index, value ){

                        $('#branchIdAppend').append('<span><input  type="checkbox" class="singleBranchClass" id="sb-'+index+'" name="branchId" value="'+index+'"  >&nbsp;&nbsp;'+value+'<span><br/>');
                        singleBranchArray[x]="sb-"+index;
                        x++;
                    });

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
                            if(singleBranchArray.length==r){
                                $('#allBranchId').prop('checked', true);
                            }
                        }
                    });

                    $('#allBranchId').change(function () {
                        if($(this).is(":checked")){
                            $('#branchIdAppend').find('.singleBranchClass').prop('checked', true);
                        }else{
                            $('#branchIdAppend').find('.singleBranchClass').prop('checked', false);
                        }
                    });

                    var data = [];
// ====================
                    var projectBranchIdSplit=projectBranchId.split(',');
                    $.each(projectBranchIdSplit, function (key1, dataArray1) {
                        var colonSplit = dataArray1.split(":");

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

// ===============

                    $('.modal-footer').on('click', '.done', function() {

                        if($('#allBranchId').is(":checked")){
                            data[0] = projectId+":"+0;
                            $('#'+singleProjectId).attr('data-project',data);
                            doneCounter=0;
                        }else{
                            $('#branchIdAppend input:checked').each(function(i){
                                //alert($(this).val());
                                data[i] = projectId+":"+$(this).val();
                                $('#'+singleProjectId).attr('data-project',data);
                                // alert('bye');
                                // alert(data);
                            });
                            doneCounter=1;
                        }
                        $('#myModal').modal('hide');
                        singleProjectId=null;
                        // doneCounter++;
                    });

                    $('.modal-footer').on('click', '.dismiss', function() {

                        projectId=null;
                        singleProjectId = null;

                    });

                }
            });
        });
//====End Function for Modal Icon & (Checked Individual Project & assign Value)=====================

//=============================================================Final Submission=======================================================================

        $('form').submit(function( event ) {
            event.preventDefault();

            // var singleProjectCount=0;
            // var dataArray=[];

            // var totalProject="<?php echo $totalProject; ?>";
            // for (var i = 1; i <= totalProject; i++) {
            //     var singleProjectId= "singleProjectId-"+i;
            //     if($('#'+singleProjectId).is(':checked')){
            //         dataArray[singleProjectCount]= $('#'+singleProjectId).attr('data-project');
            //         singleProjectCount++;
            //     }
            // }

            // if(singleProjectCount==0){
            //     $('#projectIde').html("Please Select Any Project!!!");
            //     return false;
            // }

            // alert(doneCounter);

            var singleProjectCount=0;
            var totalProject="<?php echo $totalProject; ?>";

            if($('#allProject').is(":checked") && doneCounter==0){
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
            $.ajax({
                type: 'post',
                url: './addLedgerItem',
//             data: $('form').serialize(),
                data: {name:name, code:code, parentId: parentId, accountTypeId: accountTypeId, ordering: ordering, description: description, isGroupHead: isGroupHead, stringifyDataArray:stringifyDataArray, _token: csrf },
                dataType: 'json',

                success: function( _response ){
                    // alert(JSON.stringify(_response));
                    //alert('hi');
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

                    } else {
                        window.location.href = '{{url('viewLedger/')}}';
                    }
                },
                error: function( _response ){
                    // Handle error
                    alert('error');
                }
            });
        });

        $("input").keyup(function(){
            var name = $("#name").val();
            if(name){$('#namee').hide();}else{$('#namee').show();}
            var code = $("#code").val();
            if(code){$('#codee').hide();}else{$('#codee').show();}
            var clientCode = $("#clientCode").val();
            if(clientCode){$('#clientCodee').hide();}else{$('#clientCodee').show();}
        });

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

        $('#parentId').prop("disabled", true);
        $('#code').on('input', function() {
            // this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });



    });
</script>
