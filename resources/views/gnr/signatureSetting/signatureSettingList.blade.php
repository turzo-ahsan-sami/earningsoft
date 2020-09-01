@extends('layouts/gnr_layout')
@section('title', '|Signature Setting')
@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">
                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading" style="padding-bottom:0px;">
                        <div class="panel-options">
                            <a href="{{url('gnr/addSignatureSetting/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Signature Setting</a>
                        </div>

                        <h1 align="center" style="font-family: Antiqua;letter-spacing:2px;"><font color="white"> SIGNATURE SETTING</font></h1>
                    </div>
                    
                    <div class="panel-body panelBodyView">       
                        <div>
                            <script type="text/javascript">

                                jQuery(document).ready(function($) {
                                   $("#AdvReg").dataTable({              
                                    
                                     "oLanguage": {

                                        "sEmptyTable": "No Records Available",
                                        "sLengthMenu": "Show _MENU_ "

                                    }

                                });
                               });
                                
                           </script>

                       </div>


                       <table class="table table-striped table-bordered" id="AdvReg" style="color:black;">
                        <thead>
                            <tr>
                                <th width="30">SL#</th>
                                <th>Module Name</th>
                                <th>Group Name</th>
                                <th>Company Name</th>
                                <th>Project name</th>
                                <th>Project Type</th>
                                <th>For</th>
                                <th>Action</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@include('dataTableScript')
@endsection

