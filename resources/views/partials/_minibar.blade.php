<?php
// ('currentModule')=='general'
// ('currentModule')=='inventory'
// ('currentModule')=='fams'
// ('currentModule')=='accounting'
// ('currentModule')=='microfinance'
// ('currentModule')=='pos'

use App\Traits\GetSoftwareDate;
use Illuminate\Foundation\Inspiring;
?>

<style type="text/css">
.example1 {
 height: 50px;
 overflow: hidden;
 position: relative;
}
.example1 h5 {
 font-size: 16px;
 color: white;
 position: absolute;
 width: 1700px;
 height: 100%;
 margin: 0;
 line-height: 25px;
 display: inline-block;
 /*  text-align: center;*/
 /* Starting position */
 -moz-transform:translateX(100%);
 -webkit-transform:translateX(100%);
 transform:translateX(100%);
 /* Apply animation to this element */
/* -moz-animation: example1 30s linear infinite;
 -webkit-animation: example1 30s linear infinite;
 animation: example1 30s linear infinite;*/
 -moz-animation: scroll-left 1s linear infinite;
 -webkit-animation: scroll-left 1s linear infinite;
 animation: scroll-left 30s linear infinite;

 /* -moz-animation:example1 scroll-left 5s linear infinite;
 -webkit-animation:example1 scroll-left 5s linear infinite;
 animation:example1 scroll-left 5s linear infinite;
 */
}

.example1 h5:hover {
 -moz-animation-play-state: paused;
 -webkit-animation-play-state: paused;
 animation-play-state: paused;
}
/* Move it (define the animation) */
@-moz-keyframes example1 {
 0%   { -moz-transform: translateX(100%); }
 100% { -moz-transform: translateX(-100%); }
}
@-webkit-keyframes example1 {
 0%   { -webkit-transform: translateX(100%); }
 100% { -webkit-transform: translateX(-100%); }
}
@keyframes example1 {
 0%   {
   -moz-transform: translateX(100%); /* Firefox bug fix */
   -webkit-transform: translateX(100%); /* Firefox bug fix */
   transform: translateX(100%);
 }
 100% {
   -moz-transform: translateX(-100%); /* Firefox bug fix */
   -webkit-transform: translateX(-100%); /* Firefox bug fix */
   transform: translateX(-100%);
 }
}

/* Move it (define the animation) */
@-moz-keyframes scroll-left {
 0%   { -moz-transform: translateX(100%); }
 100% { -moz-transform: translateX(-100%); }
}
@-webkit-keyframes scroll-left {
 0%   { -webkit-transform: translateX(100%); }
 100% { -webkit-transform: translateX(-100%); }
}
@keyframes scroll-left {
 0%   {
   -moz-transform: translateX(100%); /* Browser bug fix */
   -webkit-transform: translateX(100%); /* Browser bug fix */
   transform: translateX(100%);
 }
 100% {
   -moz-transform: translateX(-100%); /* Browser bug fix */
   -webkit-transform: translateX(-100%); /* Browser bug fix */
   transform: translateX(-100%);
 }
}
</style>

<div class="visible-md-block visible-lg-block">
<div class="minibar" style="background-color:#000;color:#FFF;font-size:11px;height:25px;width:100%;">
  <div style="width:1000px;margin:0 auto;">
    <span style="padding:5px 0;">
      <span><img src="{{ asset('software/images/lazyLoader.svg') }}" width="25" height="25"/></span>
      <span>
          {{-- {{ Auth::user()->id }} --}}
          Branch: {{ str_pad(DB::table('gnr_branch')->where('id', Auth::user()->branchId)->value('branchCode'), 3, '0', STR_PAD_LEFT) ?? '' }}  - 
          {{ DB::table('gnr_branch')->where('id', Auth::user()->branchId)->value('name') ?? 'Head Office' }}
      </span>
      <span style="padding:0 20px" id="clock">&nbsp;</span>
        
       
       <?php
        // if (session()->has('currentModule')) {
        //   if (session('currentModule') == 'accounting') {
        //     $softwareDate = GetSoftwareDate::getAccountingSoftwareDate();
        //   } else if (session('currentModule') == 'microfinance') {
        //     $softwareDate = GetSoftwareDate::getSoftwareDate();
        //   } else {
        //     $softwareDate = date("Y-m-d");
        //   }
        // } else {
        //   $softwareDate = date("Y-m-d");
        // }
        $softwareDate = \Carbon\Carbon::now()->format('l, F d, Y');
        
      ?>
      <span id="minibarDate" style="padding-left: 1em">{{ $softwareDate }}</span>
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
        @endphp

      <span id="planEndDate" style="padding-left: 2em">Plan: {{  strtoupper($plan_info->slug) }}, Limit: {{ date_format(date_create($subscription_info->ends_at), 'l, F d, Y') }}, &nbsp; {{ $subs_left }} days left</span>

      <span id="planEndDate" style="padding-left: 1em">
        <a style="color:#fff" href={{ url('/pricing') }}>Upgrade</a>
      </span>
      

      {{--<span style="padding-left:25px;color:peachpuff;">{{ Inspiring::quote() }}</span>--}}
    </span>
    @php
    $userName = Auth::user()->name;
    //if ($authUser->id == \App\ConstValue::USER_ID_SUPER_ADMIN || $authUser->getRole()->roleId == \App\ConstValue::ROLE_ID_GUEST) {
    //  $userName = $authUser->name;
    //} else {
    //  $emp = DB::table('hr_emp_general_info')->where('id', $authUser->emp_id_fk)->first();
    //  $userName = $emp->emp_name_english;
    //}
    // $branchName = DB::table('gnr_branch')->where('id', $authUser->branchId)->value('name');
// dd($branchName);
    @endphp
    <!--<span style="padding:5px 0;float:right;">
      <span>
        <i style="padding-right:5px;" class="fa fa-user" aria-hidden="true"></i>
        {{--    Welcome {{ $userName.', '.$branchName }} --}}
        Welcome {{ $userName}}
      </span>
    </span>-->

  </div>
</div>

</div>
  <div class="visible-xs-block visible-sm-block">

   <div class="minibar" style="background-color:#000;color:#FFF;font-size:11px;height:25px;width:100%;">
  <div style="width:1000px;margin:0 auto;">
    <span style="padding:5px 0;">
      <span><img src="{{ asset('software/images/lazyLoader.svg') }}" width="25" height="25"/></span>
      <span>
       
         {{ str_pad(DB::table('gnr_branch')->where('id', Auth::user()->branchId)->value('branchCode'), 3, '0', STR_PAD_LEFT) ?? '' }}  - 
          {{ DB::table('gnr_branch')->where('id', Auth::user()->branchId)->value('name') ?? 'Head Office' }}
      </span>
      <span style="padding:0 20px" id="clockMobile">&nbsp;</span>
        
       
       <?php

        $softwareDate = \Carbon\Carbon::now()->format('l, F d, Y');
        
      ?>
      <span id="minibarDate" style="padding-left: 1em">{{ $softwareDate }}</span>

      <?php 
        $subscription_id = DB::table('plan_subscriptions')->where('user_id', Auth::user()->id)->value('id');

        $plan_id = DB::table('plan_subscriptions')->where('id', $subscription_id)->value('plan_id');
        $plan_name = strtoupper(DB::table('plans')->where('id', $plan_id)->value('slug'));

        $plan_end_date = DB::table('plan_subscriptions')->where('id', $subscription_id)->value('trial_ends_at') ?? DB::table('plan_subscriptions')->where('user_id', Auth::user()->id)->value('ends_at');
        $todate = strtotime($plan_end_date);
        $fromdate = strtotime(date("Y-m-d"));
        $calculate_seconds = $todate- $fromdate; 
        $plan_usage_left = floor($calculate_seconds / (24 * 60 * 60 )); 
      ?>


  
    </span>


  </div>
</div>
  </div>
{{-- <div class="noticeminibar" style="background-color:green;color:#FFF;font-size:18px;height:25px;width:100%;">
  <div class="example1" style="width:1700px;margin:0 auto;">
   @php
   $notice = DB::table('gnr_notice')->where('gnr_notice.status',1)->first();

   @endphp

   <h5 style="color:white;">{{$notice->name or '' }}</h5>


 </div>
</div> --}}

@php
$notices = DB::table('gnr_notice')->where('gnr_notice.status',1)
->where('gnr_notice.startDate', '<=', Carbon\Carbon::now()->format('Y-m-d'))
->where('gnr_notice.endDate', '>=',Carbon\Carbon::now()->format('Y-m-d'))
->get();

$noticeString = '';
//$icon="<i class='fa fa-arrow-circle-o-down'></i>";

foreach ($notices as $key => $notice) {

 $applicableBrancIds =json_decode($notice->branchId);

 if (in_array($authUser->branchId, $applicableBrancIds)) {
   $noticeString = $noticeString.'&nbsp;<i class="fa fa-align-justify"></i> &nbsp;'.$notice->name.' &nbsp;';
 }
}
@endphp

@if ($noticeString!='')
<div class="noticeminibar" style="background-color:green;color:#FFF;font-size:18px;height:25px;width:100%;">
  <div class="example1" style="width:1700px;margin:0 auto;">
   @php
   @endphp

   <h5>
    {!!$noticeString!!}

  </h5>


</div>
</div>
@endif

<script>
  function clock() {
    var curTime = new Date();

    var curHours = curTime.getHours();
    var curMinutes = curTime.getMinutes();
    var curSeconds = curTime.getSeconds();

    curMinutes = (curMinutes < 10 ? "0" : "") + curMinutes;
    curSeconds = (curSeconds < 10 ? "0" : "") + curSeconds;

    var timeOfDay = (curHours < 12) ? "AM" : "PM";

    curHours = (curHours > 12) ? curHours - 12 : curHours;
    curHours = (curHours == 0) ? 12 : curHours;

    var curTimeStr = curHours + ":" + curMinutes + ":" + curSeconds + " " + timeOfDay;

    document.getElementById("clock").firstChild.nodeValue = curTimeStr;
  }

  setInterval(clock, 1000);
</script>





<script>
  function clockMobile() {
    var curTime = new Date();

    var curHours = curTime.getHours();
    var curMinutes = curTime.getMinutes();
    var curSeconds = curTime.getSeconds();

    curMinutes = (curMinutes < 10 ? "0" : "") + curMinutes;
    curSeconds = (curSeconds < 10 ? "0" : "") + curSeconds;

    var timeOfDay = (curHours < 12) ? "AM" : "PM";

    curHours = (curHours > 12) ? curHours - 12 : curHours;
    curHours = (curHours == 0) ? 12 : curHours;

    var curTimeStr = curHours + ":" + curMinutes + ":" + curSeconds + " " + timeOfDay;

    document.getElementById("clockMobile").firstChild.nodeValue = curTimeStr;
  }

  setInterval(clockMobile, 1000);
</script>
