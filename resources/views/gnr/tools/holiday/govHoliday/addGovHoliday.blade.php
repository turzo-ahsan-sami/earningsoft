@extends($route['layout'])
@section('title', '| Add Gov. Holiday')
@section('content')
	<div class="row add-data-form">
		<div class="col-md-12">
            <div class="col-md-10 col-md-offset-1 fullbody">
            	<div class="viewTitle" style="border-bottom:1px solid white;">
                    <a href="{{ url($route['path'].'/viewGovHoliday/') }}" class="btn btn-info pull-right addViewBtn">
                    	<i class="glyphicon glyphicon-th-list viewIcon"></i>
                    	Fixed Gov. Holiday List
                    </a>
                </div>
                <div class="panel panel-default panel-border">
                	<div class="panel-heading">
                        <div class="panel-title">Add Fixed Gov. Holiday</div>
                    </div>
                    <div class="panel-body">
                    	<div class="row">
                            <div class="col-md-12">
                        		{!! Form::open(array('url' => '', 'role' => 'form', 'class' => 'form-horizontal form-groups')) !!}
                        			<table id="holidayTable" class="table">
                                        <thead>
                                            <tr>
                                                <th width="50">SL#</th>
                                                <th>Holiday Title</th>
                                                <th width="100">Date</th>
                                                <th>Description</th>
                                                <th width="60">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>                                           
                                               
                                            <tr>
                                                <td>1</td>
                                                <td>{!! Form::text('title[]',null,['class'=>'title']) !!}</td>
                                                <td>{!! Form::text('date[]',null,['class'=>'date','readonly','style'=>'cursor:pointer;']) !!}</td>
                                               
                                                <td>{!! Form::text('description[]',null,['class'=>'description']) !!}</td>
                                                <td>
                                                    <a href="javascript:;" class="remove">
                                                        <i class="fa fa-minus-circle" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                            </tr>                                               
                                            
                                        </tbody>
                                    </table>
                                    <a href="javascript:;" id="addRow" class="btn btn-info add">
                                        <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                        Add
                                    </a>

                                    <ul class="pager wizard pull-right">                                        
                                        {!! Form::submit('Submit', ['id' => 'submit', 'class' => 'btn btn-info']) !!}
                                        <a href="{{ url($route['path'].'/viewGovHoliday/') }}" class="btn btn-danger closeBtn">Close</a>
                                    </ul>                                    
                        			
                        		{!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>

<style type="text/css">
    #holidayTable{
        color: black;
    }
    #holidayTable thead tr th{
        text-align: center;
        padding: 4px;
    }
    #holidayTable tbody tr td{
        text-align: center;
    }
    #holidayTable tbody tr td:nth-child(3) input{
        text-align: center;
    }
    #holidayTable tbody tr td:nth-child(2) input,#holidayTable tbody tr td:nth-child(4) input{
        padding-left: 5px;
    }
    #holidayTable tbody tr td:nth-child(2){
        width: 250px;
    }
    #holidayTable tbody tr td input{
        width: 100%;
    }
    .remove{
        color: #e84e4e;
        font-size: 13px;
    }
    .remove:hover{
        color: #c62727;
        font-size: 15px;
    }
    .add{
        background-color: #4c6b42 !important;
        border-radius: 5px;        
    }
    .add:hover{
        background-color: #34492d !important;
    }
    .ui-datepicker-year{
        display:none;
    }
    input{
        border: 1px solid black;
    }
</style>

{{-- <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
<script src="{{ asset('js/jquery-ui/jquery-ui.min.js') }}"></script> --}}

<script type="text/javascript">
    $(document).ready(function() {

        $('form').submit(function(event) {
            event.preventDefault();
            $(".error").remove();

            var hasError = 0;
            $("input.title,input.date").each(function(index, el) {
                if ($(el).val()=='') {
                    $(el).css('border-color', 'red');
                    hasError = 1;
                }
            });

            
            if (hasError==1) {
                $("#holidayTable").before("<p class='error' style='color:red'>* Please fill the marked fields.</p>");
                return false;
            }
            

            $.ajax({
                url: './storeGovHoliday',
                type: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
            })
            .done(function(data) {

                toastr.success(data.responseText, data.responseTitle, opts);                        
                setTimeout(function(){
                    location.href = "viewGovHoliday";
                }, 2000);
                              
                
            })
            .fail(function() {
                alert("error");
            });
            
        });

    /*add row*/
    $("#addRow").click(function(event) {
        var rowNum = $("#holidayTable tbody tr").length + 1;
        var markUp = "<tr>"+
                    "<td>"+rowNum+"</td>"+
                    "<td><input type='text' name='title[]' class='title'></td>"+
                    "<td><input type='text' name='date[]' class='date' style='cursor:pointer;'></td>"+
                    "<td><input type='text' name='description[]' class='description'></td>"+
                    "<td>"+
                    "<a href='javascript:;' class='remove'>"+
                    "<i class='fa fa-minus-circle' aria-hidden='true'></i>"+
                    "</a>"+
                    "</td>"+
                    "</tr>";
        $("#holidayTable tbody").append(markUp);

             $(".date").datepicker({
                changeMonth: true,
                changeYear: true,
                yearRange : "c:c",
                dateFormat: 'dd-mm',
                onSelect: function() {
                    $(this).css('border-color', 'black');                                       
                }
            });

           
        });
        /*end add row*/

        /*remove row*/
        $(document).on('click', '.remove', function() {
            if ($("#holidayTable tbody tr").length==1) {
                return false;
            }
            $(this).closest('tr').remove();
            $("#holidayTable tbody tr").each(function(index, el) {
                $(el).find('td').eq(0).html($(el).index()+1);
            });        
        });
        /*end remove row*/

        
        $(".date").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "c:c",
            dateFormat: 'dd-mm',
            onSelect: function() {
                $(this).css('border-color', 'black');               
            }
        });

        

        $(document).on('input', '.title', function(event) {
            $(this).css('border-color', 'black');
        });

    }); /*End Ready*/
    
</script>
	
@endsection