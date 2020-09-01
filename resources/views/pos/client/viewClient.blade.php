@extends('layouts/pos_layout')
@section('title', '| Client')
@section('content')
@include('successMsg')

<?php 
/*function doSomething( &$arg )
{
    $return = $arg;
    $arg += 1;
    return $return;
}

$a = 3;
$b = doSomething( $a );
echo $a;
echo $b;*/

   /*$first = 0;
   $second = 1;
   echo $first.'&nbsp;,';
   echo $second.'&nbsp;,';
  

  for($limit=0;$limit<10;$limit++)
   {
     $third = $first+$second;
     echo $third.'&nbsp;,';;
     $first = $second;
     $second = $third;
   
   }*/

/*$array = array(
1 => "a",
"1" => "b",
1.5 => "c",
true => "d",
);
what is the $array;
   print_r($array);*/
?>
   <!-- <table width="300" cellspacing="0" cellpadding="0" border="1">   -->
<?php
/*for ($rows=1; $rows < 8 ; $rows++) { 
    echo '<tr>';
    for ($colum=1; $colum < 8; $colum++) { 
        $total = $rows + $colum;
        if($total % 2 ==0)
        {
            echo "<td width='40' height='40' bgcolor='#FFFFF'></td>";
        }
        else
        {
            echo "<td width='40' height='40' bgcolor='#00000'></td>";
        }
    }
    echo '</tr>';
 } */
?>
<!-- </table>  -->

<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">
                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading" style="padding-bottom:0px">
                        <div class="panel-options">
                            <a href="{{url('pos/posAddClient/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Client</a>
                        </div>
                        <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">Client LIST</font></h1>
                    </div>
                    <div class="panel-body panelBodyView"> 
                        <div>
                            <script type="text/javascript">
                            jQuery(document).ready(function($)
                            {
                              $("#ProCategoryView").dataTable().yadcf([
                      
                              ]);
                            });
                            </script>
                        </div>
                        <table class="table table-striped table-bordered" id="ProCategoryView" style="color:black;">
                            <thead>
                                <tr>
                                    <th width="30">SL#</th>
                                    <th>Company Name</th>
                                    <th>Short Name</th>
                                    <th>Contact Person</th>
                                    <th>Designation</th>
                                    <th>Phone</th>
                                    <th>Mobile</th>
                                    <th>Email Address</th>
                                    <th>Address</th>
                                    <th>Action</th>
                                </tr>
                                {{ csrf_field() }}
                            </thead>
                            <tbody>
                                <?php $no=0; ?>
                                @foreach($posClients as $posClient)
                                    <tr class="item{{$posClient->id}}">
                                        <td class="text-center slNo">{{++$no}}</td>
                                        <td style="padding-left: 25px; text-align: left;">
                                            {{$posClient->clientCompanyName}}
                                        </td>
                                        <td style="padding-left: 25px; text-align: left;">
                                            {{$posClient->companyShortName}}
                                        </td>
                                        <td style="padding-left: 25px; text-align: left;">{{$posClient->clientContactPerson}}</td>
                                        <td style="padding-left: 25px; text-align: left;">{{$posClient->contactPersonDesigntion}}</td>
                                        <td style="padding-left: 25px; text-align: left;">{{$posClient->phone}}</td>
                                        <td style="padding-left: 25px; text-align: left;">{{$posClient->mobile}}</td>
                                        <td style="padding-left: 25px; text-align: left;">{{$posClient->email}}</td>
                                        <td style="padding-left: 25px; text-align: left;">{{$posClient->address}}</td>
                                        <td  class="text-center" width="80">
                                            <a id="editIcone" href="javascript:;" class="edit-modal" clientId="{{$posClient->id}}">
                                            <span class="glyphicon glyphicon-edit"></span>
                                            </a> &nbsp;
                                            <a id="deleteIcone" href="javascript:;" class="delete-modal" clientId="{{$posClient->id}}">
                                            <span class="glyphicon glyphicon-trash"></span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="editModal" class="modal fade" style="margin-top:3%;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Update Client Info</h4>
            </div>
            <div class="modal-body">
                {!! Form::open(array('url' => '','id'=>'entryForm', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                <input id="EMclientId" type="hidden"  value=""/>
 
                <div class="row">
                    <div class="col-md-12"> 
                   <!--  <div class="col-md-6"> -->
                        <div class="form-group">
                            {!! Form::label('clientCompanyName', 'Company Name:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-7">
                                {!! Form::text('clientCompanyName', $value = null, ['class' => 'form-control', 'id' => 'EMclientCompanyName', 'type' => 'text', 'placeholder' => 'Enter Client Company Name']) !!}
                                <p id='EMclientCompanyNamee' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('companyShortName', 'Short Name:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('companyShortName', $value = null, ['class' => 'form-control', 'id' => 'EMcompanyShortName', 'type' => 'text','autocomplete'=>'off']) !!}
                                <p id='EMcompanyShortNamee' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('clientContactPerson', 'Client Contact Person:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-7">
                                {!! Form::text('clientContactPerson', $value = null, ['class' => 'form-control', 'id' => 'EMclientContactPerson', 'type' => 'text', 'placeholder' => 'Enter Client Contact Person']) !!}
                                <p id='EMclientContactPersone' style="max-height:3px;"></p>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('clientContactPersonDesigntion', 'Desingnation :', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9">
                                {!! Form::text('clientContactPersonDesigntion', $value = null, ['class' => 'form-control', 'id' => 'EMclientContactPersonDesigntion', 'type' => 'text', 'placeholder' => 'Enter Client Contact Person','autocomplete'=>'off']) !!}
                                <p id='namee' style="max-height:3px;"></p>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            {!! Form::label('phone', 'Phone:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-7">
                                {!! Form::text('phone', $value = null, ['class' => 'form-control', 'id' =>'EMphone', 'type' => 'text', 'placeholder' => 'Enter Phone']) !!}
                                <p id='EMphonee' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('mobile', 'Mobile:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-7">
                                {!! Form::text('mobile', $value = null, ['class' => 'form-control', 'id' =>'EMmobile', 'type' => 'text', 'placeholder' => 'Enter Mobile']) !!}
                                <p id='EMmobilee' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('email', 'Email:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-7">
                                {!! Form::text('email', $value = null, ['class' => 'form-control', 'id' =>'EMemail', 'type' => 'text', 'placeholder' => 'Enter Email Address']) !!}
                                <p id='EMemaile' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('nationalId', 'National Id:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-7">
                                {!! Form::text('nationalId', $value = null, ['class' => 'form-control', 'id' =>'EMnationalId', 'type' => 'text', 'placeholder' => 'Enter National Id']) !!}
                                <p id='EMnationalIde' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('address', 'Address:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-7">
                                {!! Form::text('address', $value = null, ['class' => 'form-control', 'id' =>'EMaddress', 'type' => 'text', 'placeholder' => 'Enter National Id']) !!}
                                <p id='EMaddresse' style="max-height:3px;"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('web', 'Web:', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-7">
                                {!! Form::text('web', $value = null, ['class' => 'form-control', 'id' =>'EMweb', 'type' => 'text', 'placeholder' => 'Enter web']) !!}
                                <p id='EMwebe' style="max-height:3px;"></p>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <input id="EMclientId" type="hidden" name="clientId" value="">
                            <button type="button" id="updateButton" class="btn btn-success"><span class="glyphicon glyphicon-edit" style="padding-right:4px;"></span>Update</button>
                            <button type="button" class="btn btn-danger " data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
                        </div>
                </div>    
            </div>
        </div>
    </div>
</div>

    <div id="deleteModal" class="modal fade" style="margin-top:3%;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Delete Client Info</h4>
            </div>

           <div class="modal-body ">
                <div class="row" style="padding-bottom:20px;"> </div>
                <h2>Are You Confirm to Delete This Record?</h2>

                <div class="modal-footer">
                    <input id="DMclientId" type="hidden"  value=""/>
                    <button type="button" class="btn btn-danger"  id="DMclient"  data-dismiss="modal">confirm</button>
                    <button type="button" class="btn btn-warning"  data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){ 
        $(document).on('click', '.delete-modal', function(){
            $("#DMclientId").val($(this).attr('clientId'));
            $('#deleteModal').modal('show');
        });
        $("#DMclient").on('click',  function() {
            var clientId= $("#DMclientId").val();
            var csrf = "{{csrf_token()}}";
            $.ajax({
                url: './posDeleteClientItem',
                type: 'POST',
                dataType: 'json',
                data: {id:clientId, _token:csrf},
            })
            .done(function(data) {
                location.reload();
                window.location.href = '{{url('pos/posViewClient/')}}';
            })
            .fail(function(){
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
        });     
    });   
    $( document ).ready(function() {
        $('#EMphone').on('input', function(event) {
               this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1'); 
        });
        $('#EMmobile').on('input', function(event) {
               this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1'); 
        });
        $('#EMnationalId').on('input', function(event) {
               this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1'); 
        });
         /*Edit Modal Start*/
        $(document).on('click', '.edit-modal', function() {
            var clientId = $(this).attr('clientId');
            var csrf = "{{csrf_token()}}";
            $("#EMclientId").val(clientId);
                $.ajax({
                    url: './posGetinfo',
                    type: 'POST',
                    dataType: 'json',
                    data: {id:clientId , _token: csrf},
                    success: function(data) {
                        $("#EMclientCompanyName").val(data['posClient'].clientCompanyName); 
                        $("#EMcompanyShortName").val(data['posClient'].companyShortName); 
                        $("#EMclientContactPerson").val(data['posClient'].clientContactPerson); 
                        $("#EMclientContactPersonDesigntion").val(data['posClient'].contactPersonDesigntion); 
                        $("#EMphone").val(data['posClient'].phone);
                        $("#EMmobile").val(data['posClient'].mobile);
                        $("#EMemail").val(data['posClient'].email);
                        $("#EMnationalId").val(data['posClient'].nationalId);
                        $("#EMaddress").val(data['posClient'].address);
                        $("#EMweb").val(data['posClient'].web);

                        $("#editModal").find('.modal-dialog').css('width', '55%');
                        $('#editModal').modal('show');

                    },
                      error: function(argument) {
                        //alert('response error');
                }
            });
        });
         /*edit Modal End*/
         /*update Start*/
    $("#updateButton").on('click', function() {
        $("#updateButton").prop("disabled", true);
        var clientId                  = $("#EMclientId").val();
        var companyName               = $("#EMclientCompanyName").val();
        var companyShortName          = $("#EMcompanyShortName").val();
        var clientPerson              = $("#EMclientContactPerson").val();
        var clientContactPersonDesigntion = $("#EMclientContactPersonDesigntion").val();
        var phone                     = $("#EMphone").val();
        var mobile                    = $("#EMmobile").val();
        var email                     = $("#EMemail").val();
        var nationalId                = $("#EMnationalId").val();
        var address                   = $("#EMaddress").val();
        var web                       = $("#EMweb").val();
        var csrf = "{{csrf_token()}}";
        $.ajax({
            url: './posEditClientItem',
            type: 'POST',
            dataType: 'json',
            data: {id:clientId,companyName:companyName,companyShortName:companyShortName,clientPerson:clientPerson,clientContactPersonDesigntion:clientContactPersonDesigntion,phone:phone,mobile:mobile,email:email,nationalId:nationalId,address:address,web:web,_token: csrf},
        })
        .done(function(data) {
            if (data.errors) {
                if (data.errors['companyName']) {
                    $("#EMclientCompanyNamee").empty();
                    $("#EMclientCompanyNamee").append('<span class="errormsg" style="color:red;">'+data.errors['companyName']);
                }
                if (data.errors['clientPerson']) {
                    $("#EMclientContactPersone").empty();
                    $("#EMclientContactPersone").append('<span class="errormsg" style="color:red;"> '+data.errors['clientPerson']);
                }
                if (data.errors['phone']) {
                    $("#EMphonee").empty();
                    $("#EMphonee").append('<span class="errormsg" style="color:red;"> '+data.errors['phone']);
                }
                if (data.errors['mobile']) {
                    $("#EMmobilee").empty();
                    $("#EMmobilee").append('<span class="errormsg" style="color:red;">'+data.errors['mobile']);
                }
                if (data.errors['email']) {
                    $("#EMemaile").empty();
                    $("#EMemaile").append('<span class="errormsg" style="color:red;">'+data.errors['email']);
                }
                if (data.errors['nationalId']) {
                    $("#EMnationalIde").empty();
                    $("#EMnationalIde").append('<span class="errormsg" style="color:red;">'+data.errors['nationalId']);
                }
                if (data.errors['address']) {
                    $("#EMaddresse").empty();
                    $("#EMaddresse").append('<span class="errormsg" style="color:red;">'+data.errors['address']);
                }
            } else {
                location.reload();
            }
                console.log("success");
               })
               .fail(function() {
                  console.log("error");
               })
               .always(function() {
                  console.log("complete");
               })
    });
    /*Update End*/

/*Error Alert remove Start */
    $("input").keyup(function(){
        var companyName = $("#EMclientCompanyName").val();
        if(companyName){$('#EMclientCompanyNamee').hide();}else{$('#EMclientCompanyNamee').show();}
        var contactParson = $("#EMclientContactPerson").val();
        if(contactParson){$('#EMclientContactPersone').hide();}else{$('#EMclientContactPersone').show();}
        var phone = $("#EMphone").val();
        if(phone){$('#EMphonee').hide();}else{$('#EMphonee').show();}
        var mobile = $("#EMmobile").val();
        if(mobile){$('#EMmobilee').hide();}else{$('#EMmobilee').show();}
        var email = $("#EMemail").val();
        if(email){$('#EMemaile').hide();}else{$('#EMemaile').show();}
        var nationalId = $("#EMnationalId").val();
        if(nationalId){$('#EMnationalIde').hide();}else{$('#EMnationalIde').show();}
        var address = $("#EMaddress").val();
        if(address){$('#EMaddresse').hide();}else{$('#EMaddresse').show();}
    });
 /*Error Alert remove end */
 
});/*Ready function End*/
</script>
@include('dataTableScript')
@endsection