@extends('layouts/gnr_layout')
@section('title', '|Signature Setting')
@section('content')

<div class="row add-data-form">
    <div class="col-md-2"></div>
    <div class="col-md-8 fullbody">
        <div class="viewTitle" style="border-bottom: 1px solid white;">
            <a href="{{url('gnr/signatureSettingList/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
            </i>Signature Setting List</a>
        </div>
        <div class="panel panel-default panel-border">
            <div class="panel-heading">
                <div class="panel-title">Signature Setting</div>
            </div>
            <div class="panel-body">
                <div class="row"> 
                    <div class="col-md-8">
                        {!! Form::open(array('url' => '','id'=>'dataForm' ,'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}

                        <div class="form-group">
                            {!! Form::label('module', 'Module', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-1 control-label">: </div>
                            <div class="col-sm-8">
                                {!! Form::select('module',$modules,null,['class'=>'form-control','required'=>'required']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('group', 'Group', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-1 control-label">: </div>
                            <div class="col-sm-8">  
                                {!! Form::select('group',$groups,null,['class'=>'form-control','id'=>'group','required'=>'required']) !!}
                            </div>
                        </div>
                        
                        <div class="form-group">
                            {!! Form::label('company', 'Company', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-1 control-label">: </div>
                            <div class="col-sm-8"> 
                                {!! Form::select('company',$companies,null,['class'=>'form-control','id'=>'company','required'=>'required']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('project', 'Project', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-1 control-label">: </div>
                            <div class="col-sm-8"> 
                                {!! Form::select('project',$projects,null,['id'=>'project','class'=>'form-control','required'=>'required']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('isHeadOffice', 'For', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-1 control-label">: </div>
                            <div class="col-sm-8">
                                {!! Form::select('isHeadOffice',[''=>'Select','1'=>'Head Office','0'=>'Branch'],null,['class'=>'form-control','id'=>'isHeadOffice','required'=>'required']) !!}
                            </div>
                        </div>
                        <div class="form-group" style="padding-bottom:15px;">
                            {!! Form::label('numOfSignature', 'No. Of Signature ', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-1 control-label">: </div>
                            <div class="col-sm-8">
                                {!! Form::select('numOfSignature',[''=>'Select','1'=>'1','2'=>'2','3'=>'3','4'=>'4'],null,['class'=>'form-control','id'=>'numOfSignature','required'=>'required']) !!}
                            </div>
                        </div> 
                    </div>
                    <table id="signatureInfoTable" class="table table-striped table-bordered dataTable no-footer" style="display: none;">
                        <thead>
                            <th width="80px;">SL#</th>
                            <th>Role</th>
                            <th>Position</th>
                        </thead> 
                        <tbody>
                            
                        </tbody>
                    </table>

                    <div class="form-group" style="padding-top:15px;">
                       <div class="col-sm-12 text-right" style="padding-right: 20px;">
                           {!! Form::submit('Submit', ['id' => 'save', 'class' => 'btn btn-info','type'=>'button']) !!}
                           <a href="{{url('gnr/signatureSettingList/')}}" class="btn btn-danger closeBtn">Close</a>
                       </div>
                   </div>
                   {!! Form::close() !!}
               </div>
           </div>
       </div>
       <div class="footerTitle" style="border-top:1px solid white"></div>
   </div>
   <div class="col-md-2"></div>
</div>

{{-- update notification Modal --}}
<div id="update-confirmation-modal" class="modal fade" role="dialog" style="padding-top: 10%;">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Notification</h4>
            </div>
            <div class="modal-body">
                <p>This data already exis, do you want to update?</p>
            </div>
            <div class="modal-footer">
                <button id="yesBtn" type="button" class="btn btn-info animated slideInRight" style="margin-bottom:0!important;" >Yes</button>
                <button id="noBtn" type="button" class="btn btn-white animated slideInRight" data-dismiss="modal" style="margin-bottom:0!important;">No</button>
            </div>
        </div>
    </div>
</div>
{{-- end update notification Modal --}}

<script type="text/javascript">
    $(document).ready(function() {
        $("#numOfSignature").change(function() {            
            $("#isHeadOffice").trigger('change');            
        });

        $("#isHeadOffice").change(function() {
            if ($(this).val()==1 && $("#numOfSignature").val()!='') {
                var hasEmployee = $("#signatureInfoTable thead tr").find('.employeeHead').length;
                if (hasEmployee==0) {
                    $("#signatureInfoTable thead tr").append("<th class='employeeHead'>Employee</th>");
                }  
                makeTableBody();              
                $("#signatureInfoTable").show();                
            }
            else if($(this).val()==0 && $("#numOfSignature").val()!=''){
                $("#signatureInfoTable thead tr").find('.employeeHead').remove();
                makeTableBody();
                $("#signatureInfoTable").show();
            }
            else{
                $("#signatureInfoTable").hide();                
            }

        });

        function makeTableBody() {
            $("#signatureInfoTable tbody").empty();
            var numOfRows = $("#numOfSignature").val();
            if ($("#isHeadOffice").val()==1) {
                for (var i = 1; i <= numOfRows; i++) {
                    $("#signatureInfoTable tbody").append("<tr><td>"+i+"</td><td><select name='sigRole[]' class='sigRole form-control'><option value=''>Select</option></select></td><td><select name='empPosition[]' class='empPosition form-control'><option value=''>Select</option></select></td><td><select name='empId[]' class='empId form-control'><option value=''>Select</option></select></td></tr>");
                }
            }
            else if($("#isHeadOffice").val()==0){
                for (var i = 1; i <= numOfRows; i++) {
                    $("#signatureInfoTable tbody").append("<tr><td>"+i+"</td><td><select name='sigRole[]' class='sigRole form-control'><option value=''>Select</option></select></td><td><select name='empPosition[]' class='empPosition form-control'><option value=''>Select</option></select></td></tr>");
                }
            }
            else{
                $("#signatureInfoTable tbody").empty();
                return false;
            }

            // fill the selection options, posiotion will be branch wise, one for head office and another for branches
            var groupId = $("#group").val();
            var companyId = $("#company").val();
            var projectId = $("#project").val();
            var isHeadOffice = $("#isHeadOffice").val();
            $.ajax({
                url: './getRolesForSignature',
                type: 'POST'
            })
            .done(function(signatureRoles) {                
                $.each(signatureRoles, function(index, val) {
                 $('.sigRole').append("<option value="+index+">"+val+"</option>");
             });
            })
            .fail(function() {
                alert("error");
            });

            $.ajax({
                url: './getPositionsForSignature',
                type: 'POST',
                dataType: 'json',
                data: {groupId: groupId, companyId: companyId, projectId: projectId, isHeadOffice: isHeadOffice},
            })
            .done(function(empPositions) {
                $.each(empPositions, function(index, val) {
                 $('.empPosition').append("<option value="+index+">"+val+"</option>");
             });
            })
            .fail(function() {
                alert("response error");
            });
            
        }

        $(document).on('submit', '#dataForm', function(event) {
            event.preventDefault();
            $("#submit").prop('disabled',true);
            
            $("#signatureInfoTable tbody tr td select").each(function(index, el) {
                if ($(el).val()=='') {
                    alert('Please fill all the informations.');
                    return false;
                }
            });

            var abc = $("#signatureInfoTable tbody tr td select").serialize();
            
            var formData = $('#dataForm').serialize() + '&' + $("#signatureInfoTable tbody tr td select").serialize();

            $.ajax({
                url: './storeSignatureSetting',
                type: 'POST',
                dataType: 'json',
                data: formData,
            })
            .done(function(data) {
                if (data=='data alreay exits') {
                    $("#update-confirmation-modal").modal('show');
                }
                else{
                    toastr.success(data.responseText, data.responseTitle, opts);                        
                    setTimeout(function(){
                        location.reload();
                    }, 2000);
                }
            })
            .fail(function() {
                $("#submit").prop('disabled',false);
                alert("response error");
            });
        });

        $("#yesBtn").click(function(event) {
            $("#submit").prop('disabled',true);
            var formData = $('#dataForm').serialize() + '&' + $("#signatureInfoTable tbody tr td select").serialize();
            $.ajax({
                url: './updateSignatureSetting',
                type: 'POST',
                dataType: 'json',
                data: formData,
            })
            .done(function(data) {
                toastr.success(data.responseText, data.responseTitle, opts);                        
                setTimeout(function(){
                    location.reload();
                }, 2000);
            })
            .fail(function() {
                $("#submit").prop('disabled',false);
                alert("response error");
            });
            
        });

        $(document).on('change', '.sigRole,.empPosition,.empId', function(event) {
            protectDuplicateValue($(this).attr('class').split(' ')[0]);
        });

        /*protect duplicate data*/
        function protectDuplicateValue(className) {

            $('.'+className+' option').prop('disabled', false);

            var obj = $('.'+className);
            $(obj).each(function(index, el) {
                if ($(el).val()!='') {
                    $('.'+className+' option[value="'+$(el).val()+'"]').prop('disabled', true);
                    $("option:selected", el).prop('disabled', false);
                }
            });
        }
        /*end protecting duplicate data*/

        /*get employee list*/
        $(document).on('change', '.empPosition', function(event) {

            var obj = $(this);

            // var abc = $(this).val();
            var abc = this.value;

            if ($("#isHeadOffice").val()==1 && $(this).val()!='') {

                $(this).closest('tr').find('.empId option:gt(0)').remove();

                $.ajax({
                    url: './getEmpoyeeForSignature',
                    type: 'POST',
                    dataType: 'json',
                    data: {empPositionId: this.value},
                })
                .done(function(employees) {
                    $.each(employees, function(index, val) {
                        obj.closest('tr').find('.empId').append("<option value="+index+">"+val+"</option>");
                    });
                })
                .fail(function() {
                    alert("response error");
                });
                
            }
        });
        /*end getting employee list*/

        /*get group wise company list*/
        $("#group").change(function(event) {
            $("#company option:gt(0)").remove();
            $("#project option:gt(0)").remove();
            $("#numOfSignature").val('');
            $("#numOfSignature").trigger('change');

            if ($(this).val()=='') {
                return false;
            }

            $.ajax({
                url: './../getGroupuWiseCompanyList',
                type: 'POST',
                dataType: 'json',
                data: {groupId: $("#group").val(), companyId: $(this).val()},
            })
            .done(function(companies) {
                $.each(companies, function(index, val) {
                    $("#company").append("<option value="+index+">"+val+"</option>");
                });
            })
            .fail(function() {
                alert("response error");
            });
            
        });
        /*end getting group wise company list*/

        /*get group & company wise project list*/
        $("#company").change(function(event) {
            $("#project option:gt(0)").remove();
            $("#numOfSignature").val('');
            $("#numOfSignature").trigger('change');

            if ($(this).val()=='') {
                return false;
            }

            $.ajax({
                url: './../getGroupCompanyWiseProjectList',
                type: 'POST',
                dataType: 'json',
                data: {groupId: $(this).val()},
            })
            .done(function(companies) {

                $.each(companies, function(index, val) {
                    $("#project").append("<option value="+index+">"+val+"</option>");
                });
            })
            .fail(function() {
                alert("response error");
            });
            
        });
        /*end getting group & company wise project list*/

        $("#project").change(function(event) {
            makeTableBody();
        });


    }); /*ready*/
</script>

@endsection




