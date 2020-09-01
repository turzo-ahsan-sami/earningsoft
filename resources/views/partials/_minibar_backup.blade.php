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


<div class="minibar" style="background-color:#000;color:#FFF;font-size:11px;height:25px;width:100%;">
  <div style="width:1000px;margin:0 auto;">
    <span style="padding:5px 0;">
      <span><img src="{{ asset('software/images/lazyLoader.svg') }}" width="25"
       height="25"/></span><span>Branch: {{ DB::table('gnr_branch')->where('id', Auth::user()->branchId)->value('name') }}</span>
       <span style="padding:0 20px" id="clock">&nbsp;</span>
       {{-- <span>{{ date('l, F d, Y') }}</span> --}}
       <?php
       if (session()->has('currentModule')) {
        if (session('currentModule') == 'accounting') {
          $softwareDate = GetSoftwareDate::getAccountingSoftwareDate();
        } else if (session('currentModule') == 'microfinance') {
          $softwareDate = GetSoftwareDate::getSoftwareDate();
        } else {
          $softwareDate = date("Y-m-d");
        }

      } else {
        $softwareDate = date("Y-m-d");
      }
      ?>
      <span id="minibarDate">{{ date_format(date_create($softwareDate), 'l, F d, Y') }}</span>

      {{--<span style="padding-left:25px;color:peachpuff;">{{ Inspiring::quote() }}</span>--}}
    </span>
    @php
    $authUser = Auth::user();
    if ($authUser->id == \App\ConstValue::USER_ID_SUPER_ADMIN || $authUser->getRole()->roleId == \App\ConstValue::ROLE_ID_GUEST) {
      $userName = $authUser->name;
    } else {
      $emp = DB::table('hr_emp_general_info')->where('id', $authUser->emp_id_fk)->first();
      $userName = $emp->emp_name_english;
    }
    $branchName = DB::table('gnr_branch')->where('id', $authUser->branchId)->value('name');
            // dd($branchName);
    @endphp
    <span style="padding:5px 0;float:right;">
      <span>
        <i style="padding-right:5px;" class="fa fa-user" aria-hidden="true"></i>
        Welcome {{ $userName.', '.$branchName }}
      </span>
    </span>
        {{-- <span style="float:right;padding:5px 0;">
            <span>
                <a style="color:#FFF;" href="">Change Password</a>
            </span> |
            <span>
                <a style="color:#FFF;" href="">Logout</a>
            </span>
          </span> --}}
        </div>
      </div>




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
