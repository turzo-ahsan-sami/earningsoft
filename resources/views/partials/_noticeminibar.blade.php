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
<div class="minibar" style="background-color:green;color:#FFF;font-size:20px;height:30px;width:100%;">
    <div style="width:1200px;margin:0 auto;">
        <marquee>sdsds</marquee>

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
