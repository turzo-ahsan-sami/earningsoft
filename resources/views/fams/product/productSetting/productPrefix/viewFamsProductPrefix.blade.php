@extends('layouts/fams_layout')
@section('title', '|Prefix')
@section('content')


<div class="row">
    <div class="col-md-2"></div>
<div class="col-md-8">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addFamsProductPrefix/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Prefix</a>
          </div>
            <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">PREFIX LIST</font></h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            /*$("#famsProGroupView").dataTable().yadcf([
    
            ]);*/
            $("#famsProductPrefixTable").dataTable({
                   "oLanguage": {
                  "sEmptyTable": "No Records Available",
                  "sLengthMenu": "Show _MENU_ "
                  }
                });
          });
          </script>
        </div>
          <table id="famsProductPrefixTable" class="table table-striped table-bordered" style="color: black;">
            <thead>
                <tr>
                    <th width="30">SL#</th>
                    <th style="text-align:left;padding:5px;">Name</th>
                     <th>Code</th>
                    <th width="60"th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
               
                      <?php $no=0; ?>
                      @foreach($productPrefixs as $key=>$productPrefix)
                  
                <tr class="item">
                      <td class="text-center">{{$key+1}}</td>
                      <td class="text-center">{{substr($productPrefix->name, 0,-1)}}</td>
                        <td class="text-center">{{$productPrefix->code}}</td>
                    <td>
                        
                       @if($productPrefix->status==0)
                          <span><i class="fa fa-times" aria-hidden="true" style="color:red;font-size: 1.3em;"></i></span>
                        @else                            
                           <span><i class="fa fa-check" aria-hidden="true" style="color:green;font-size: 1.3em;"></i></span>
                        @endif
                        
                       </td>
                    
                    <td class="text-center" width="100">
                      <a id="editIcone" href="javascript:;" class="edit-modal" data-id="" data-name="" data-groupcode="" data-slno="" productPrefix="{{$productPrefix->id}}">
                        <span class="glyphicon glyphicon-edit"></span>
                      </a>&nbsp
                      <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="" productPrefix="{{$productPrefix->id}}">
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
        <div class="col-md-2"></div>
</div>


<!-- Edit Modal -->


 <div id="editModal" class="modal fade" style="margin-top:3%;">
  <div class="modal-dialog">
   <div class="modal-content">
    <div class="modal-header">
     <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px;text-align:center;">Update Advance Register</h4>
    </div>

    <div class="modal-body">
     <div class="panel-body float-left">
      <div class="row">   
       <div class="col-md-12">
       {!! Form::open(array('url' => '','id'=>'entryForm', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
       <div class="col-md-12">

       <div class="form-group">
         {!! Form::label('name', 'Name', ['class' => 'col-sm-3 control-label']) !!}
       <div class="col-sm-1 control-label"> :</div>

       <div class="col-sm-8">                                    
        {!! Form::text('name',null, ['class'=>'form-control', 'id' => 'EMname']) !!}

        <p id='namee' class="error" style=color:red;"></p>
       </div>
       </div>

       <div class="form-group">
         {!! Form::label('code', 'Code', ['class' => 'col-sm-3 control-label']) !!}
       <div class="col-sm-1 control-label"> :</div>

       <div class="col-sm-8">                                    
        {!! Form::text('code',null, ['class'=>'form-control', 'id' => 'EMcode']) !!}
          <p id='codee' class="error" style=color:red;"></p>
       </div>
       </div>

       <div class="form-group">
         {!! Form::label('status', 'Status', ['class' => 'col-sm-3 control-label']) !!}
       <div class="col-sm-1 control-label"> :</div>

       <div class="col-sm-8">                                    
        {!! Form::select('status',['0'=>'Inactive','1'=>'Active'],0, ['class'=>'form-control', 'id' => 'EMstatus']) !!}

        <p id='statuse' class="error" style=color:red;"></p>
       </div>
       </div>

  <div class="modal-footer">
   <input id="EMproductPrifix" type="hidden" name="productPrifix" value="">
    <button type="button" id="updateButton" class="btn btn-success"><span class="glyphicon glyphicon-edit" style="padding-right:4px;"></span>Update</button><button type="button" class="btn btn-danger " data-dismiss="modal"><span class="glyphicon glyphicon-remove" style="padding-right:4px;"></span>Close</button>
  </div>
                             
                 </div>


                      </div><!-- row-->


                      </div><!-- panel-body float-left-->

            </div><!-- modal-body -->
          </div><!-- modal-header-->
      </div><!-- modal-content-->
  </div> <!-- end modal-dialog -->
</div> 

<!--  delete Modal -->

  <div id="deleteModal" class="modal fade" style="margin-top:3%">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="clear:both;background-color:black;color:white; padding:10px">Product Prefix Delete!</h4>
                    </div>
                    <div class="modal-body">
                        <h2>Are You Confirm to Delete This Record?</h2>

                        <div class="modal-footer">
                            {!! Form::open(['url' => '/']) !!}
                            <input type="hidden" id="DMproductPrifix" value=>
                            <button  type="button" class="btn actionBtn glyphicon glyphicon-check btn-success" id="DMproductPrifixe"><span id=""> Confirm</button>
                            <button class="btn btn-danger glyphicon glyphicon-remove" data-dismiss="modal" type="button"> Close</button>
                            {!! Form::close() !!}

                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- end Delete Modal -->

        <!-- Delete data Modal -->

<script>         
    $(document).ready(function(){ 
      $(document).on('click', '.delete-modal', function(){

          $("#DMproductPrifix").val($(this).attr('productPrefix'));
                    $("#deleteModal").find('.modal-dialog').css('width', '55%');
                    $('#deleteModal').modal('show');

                  });
                $("#DMproductPrifixe").on('click',  function(){ 
                          var productPrefix= $("#DMproductPrifix").val();
                          var csrf = "{{csrf_token()}}";

                          $.ajax({
                                    url: './deleteFamsProductPrefix',
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {id:productPrefix, _token:csrf},
                                })

                          .done(function(data){ 
                            location.reload();

                               })

                          .fail(function(){

                            console.log("error");

                            })
                          .always(function(){

                           console.log("complete");

                             });


                 });

              
    });     
                  

            </script>

            <!-- end Delete data  --> 

<script type="text/javascript">
   $(document).ready(function(){ 
         
     $(document).on('click', '.edit-modal', function(){
            var productPrefix = $(this).attr('productPrefix');
            var csrf = "{{csrf_token()}}";
            $("#EMproductPrifix").val(productPrefix);
            //$('#p').modal('hide')
             $('.errors').empty();
              $('#namee').empty();
              $('#codee').empty();


         // alert(JSON.stringify(data));

                $.ajax({
                  url: './getFamsProductFrefixinfo',
                  type: 'POST',
                  dataType: 'json',
                 
                  data: {id:productPrefix, _token: csrf},
                  success: function(data){
 //alert(JSON.stringify(data));

                $("#EMname").val(data['productPrefixs'].name.slice(0,-1));
                $("#EMcode").val(data['productPrefixs'].code);
                $("#EMstatus").val(data['productPrefixs'].status);
                $("#editModal").find('.modal-dialog').css('width', '60%');
                $("#editModal").modal('show');


                },
              error: function(argument){
                    alert('response error');          
                  }

               });
         });

    });

  </script>

<script type="text/javascript">
   $(document).ready(function(){ 
     $("#updateButton").on('click', function() {
      $("#updateButton").prop("disabled", true);


            var productPrefixId = $("#EMproductPrifix").val();
            var name = $("#EMname").val();
            var code= $("#EMcode").val();
            var status= $("#EMstatus").val();
            var csrf = "{{csrf_token()}}";
            //alert(status);

           $.ajax({
                url: './editFamsProductPrefix',
                type: 'POST',
                dataType: 'json',
                data: {id:productPrefixId,name:name,code:code,status:status,_token: csrf},
 
})
           


     .done(function(data) {

                 if (data.errors) {
                    if (data.errors['name']) { 
                        $("#namee").empty();
                        $("#namee").append('*'+data.errors['name']);
                       }
                    
                   if (data.errors['code']) {
                        $("#codee").empty();
                        $("#codee").append('*'+data.errors['code']);

                      }
                   if (data.errors['status']) {
                        $("#statuse").empty();
                        $("#statuse").append('*'+data.errors['status']);

                      }  
                                   
                    }
                    
                else{
                    location.reload();
                    }
                console.log("success");
               })
                .fail(function(){
                 console.log("error");

                 })

                .always(function(){
                 console.log("complete");
                })
         
         });
});

</script> 


@include('dataTableScript')
@endsection
