@extends('layouts/gnr_layout')
@section('title', '| Subscription')
@section('content')
@include('successMsg')
<?php 
/*echo "<pre>";
        print_r($hrAllEmployeeDetails);
echo "</pre>";*/
?>
<style type="text/css">
    .table tbody tr th, .table thead tr th {
    text-align: left;
    width: 20%;
}
.table tbody tr td.colon {
    text-align: center !important;
    width: 1%;
}

.table tbody tr td.subDetails {
    text-align: left !important;
    padding-left:5px;
}

.table tbody tr td.upgrade {
   border:0px !important;
   text-align: right !important;
   padding-right: 5px !important;
   font-size: 12px !important;

}
.table tbody tr td.left {
   border:0px !important;
   text-align: left !important;
   padding-left: 5px !important;
   padding-top: 14px !important;
}


/*.table tbody tr td.subDetails  span a.upgrade_style{
    text-align: right !important;
   
}*/
</style>
<div class="row">
    <div class="col-md-12">
        <div class="" style="">
            <div class="">
                <div class="panel panel-default" style="background-color:#708090;">
                    <div class="panel-heading" style="padding-bottom:0px">
                       {{--  <div class="panel-options">
                            <a href="" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>All Employee</a>
                        </div> --}}
                        <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">SUBSCIPTION DETAILS</font></h1>
                    </div>
                    <div class="panel-body panelBodyView">
                        <div class="row">
                              <div class="col-md-6"> 
                                <table class="table table-striped table-bordered" id="" style="color:black;">
                                    <tr>
                                        <td colspan="3" style="text-align:left !important; padding-left: 15px; font-size: 20px;"><h4><strong>Plan Information:</strong></h4></td>
                                    </tr>

                                    @php
                                    $customer_id = Auth::user()->customer_id;
                                    $user_id = DB::table('users')->where('customer_id', $customer_id)->value('id');
                                    $subscription_info = DB::table('plan_subscriptions')->where('user_id', $user_id)->first();
                                    $plan_info = DB::table('plans')->where('id', $subscription_info->plan_id)->first(); 

                                    $plan_end_date = DB::table('plan_subscriptions')->where('id', $subscription_info->id)->value('trial_ends_at') ?? DB::table('plan_subscriptions')->where('user_id', $user_id)->value('ends_at');
                                      //dd($plan_end_date);
                                    $end_date = strtotime($plan_end_date);

                                    $todaydate = strtotime(date("Y-m-d"));

                                    $left_date = $end_date - $todaydate ; 
                                    $subs_left = floor($left_date / (24 * 60 * 60 )); 
                                    $features = json_decode($plan_info->features);
                                    $names = json_decode($subscription_info->name);
                                    //dd($names);

                                    @endphp
                                    
                                    <tr>
                                        <th>Name</th>
                                        <td class="subDetails" colspan="2">{{($subscription_info) ? strtoupper($plan_info->slug) : ''}}</td>
                                    </tr>
                                    <tr>
                                        <th>Price</th>
                                        <td class="subDetails" colspan="2">{{($subscription_info) ? $plan_info->price : ''}}</td>
                                    </tr>
                                    <tr>
                                        <th>Renewal Fee</th>
                                        <td class="subDetails" colspan="2">{{($subscription_info) ? $plan_info->renewal_fee : ''}}</td>
                                    </tr>
                                   
                                    <tr>
                                        <th>Active Usres Limit</th>
                                        <td class="left">{{($subscription_info) ? $plan_info->active_users_limit : ''}}</td>
                                         <td class="upgrade"> <span style="text-align: right!important;"><a href="{{ url('/pricing') }}" class="upgrade_style"><u><strong>Upgrade users limit</span></u></td>
                                    </tr> 
                                     <tr>
                                        <th>Active Company Limit</th>
                                        <td class="left">{{($subscription_info) ? $plan_info->active_company_limit : ''}}</td>
                                         <td class="upgrade"> <span style="text-align: right!important;"><a href="{{ url('/pricing') }}" class="upgrade_style"><u><strong>Upgrade company limit</span></u></td>
                                    </tr> 
                                    <tr>
                                        <th>Active Branch Limit</th>
                                        <td class="left">{{($subscription_info) ? $plan_info->active_branch_limit : ''}}</td>
                                         <td class="upgrade"> <span style="text-align: right!important;"><a href="{{ url('/pricing') }}" class="upgrade_style"><u><strong>Upgrade branch limit</span></u></td>
                                    </tr> 
                                     <tr>
                                        <th>Features</th>
                                        <td class="subDetails" colspan="2">
                                            @foreach($features as $key => $value)
                                              <span style="color:#72A230;"><i class="fa fa-check" aria-hidden="true"></i></span>   {{($subscription_info) ? $value : ''}}<br>
                                            @endforeach
                                        </td>
                                    </tr>   
                                </table>
                            </div>
                             <div class="col-md-6"> 
                                <table class="table table-striped table-bordered" id="" style="color:black;">
                                    <tr>
                                        <td colspan="3" style="text-align:left !important; padding-left: 15px; font-size: 20px;"><h4><strong>Subscription Information:</strong></h4></td>
                                    </tr>
                                    <tr>
                                        <th>Name</th>
                                        <td class="subDetails" colspan="2">{{($subscription_info) ? strtoupper($plan_info->slug) : ''}}</td>
                                    </tr>
                                    <tr>
                                        <th>Start Date</th>
                                        <td class="subDetails" colspan="2">{{($subscription_info) ? date_format(date_create($subscription_info->starts_at), 'l, F d, Y') : ''}}</td>
                                    </tr> 
                                    <tr>
                                        <th>End Date</th>
                                        <td class="subDetails" colspan="2">{{($subscription_info) ? date_format(date_create($subscription_info->ends_at), 'l, F d, Y') : ''}}</td>
                                    </tr>
                                    <tr>
                                        <th>Remaining Days</th>
                                        <td class="left">{{($subscription_info) ?  $subs_left  : ''}} days </td>
                                        <td class="upgrade"> <span style="text-align: right!important;"><a href="{{ url('/pricing') }}" class="upgrade_style">
                                            @if($subs_left == 0)
                                            <u><strong>Upgrade</span></u>
                                            @endif
                                        </td>  
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection