<?php
use App\Traits\GetSoftwareDate;
?>
<ul id="main-menu" class="navbar-nav">
    <li id="config">
        <a class="animated fadeInLeft" href="{{url('attendence/home')}}">
            <i class="linecons-cog"></i>
            <span class="title">DashBoard</span>
        </a>
        <ul>
            {{-- @if (Auth::user()->branchId==1) --}}
            <li>
                <a href="javascript:;">
                    <span class="title">Demo DashBoard Child</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('#') }}">
                            <span class="title">Demo DashBoard Grand Child</span>
                        </a>
                    </li>
                </ul>
            </li>
            {{-- @endif --}}
        </ul>
    </li>
    <li>
        <a class="animated fadeInLeft" href="javascript:;">
            <i class="linecons-cog"></i>
            <span class="title">Masters</span>
        </a>
        <ul>

            <li>
                <a href="{{url('./deviceList')}}">
                    <span class="title">Device List</span>
                </a>
            </li>



            <li>
                <a href="{{url('./companys')}}">
                    <span class="title">Company</span>
                </a>
            </li>
            <li>
                <a href="{{url('./attendenceHrEmployeeList')}}">
                    <span class="title">Employee List</span>
                </a>
            </li>

            <li>
                <a href="{{url('./branch')}}">
                    <span class="title">Branch</span>
                </a>
            </li>

            <li>
                <a href="{{url('./department')}}">
                    <span class="title">Department</span>
                </a>
            </li>

            <li>
                <a href="{{url('./designation')}}">
                    <span class="title">Designation</span>
                </a>
            </li>

            <li>
                <a href="{{url('gradeAttendence')}}">
                    <span class="title">Grade</span>
                </a>
            </li>

            <li>
                <a href="{{url('viewGovHolidayAttendence')}}">
                    <span class="title">Holyday</span>
                </a>
            </li>





            <li>
                <a href="{{url('lateTime')}}">
                    <span class="title">Late Time</span>
                </a>
            </li>
        </ul>
    </li>
    <li>
        <a class="animated fadeInLeft" href="javascript:;">
            <i class="linecons-cog"></i>
            <span class="title">Assignments</span>
        </a>
        <ul>

            <li>
                <a href="{{url('./deviceAttendence')}}">
                    <span class="title">Assign Device</span>
                </a>
            </li>

            <li>
                <a href="{{url('shiftAttendence')}}">
                    <span class="title">Shifting</span>
                </a>
            </li>
            <li>
                <a href="{{url('assignShiftBranchList')}}">
                    <span class="title">Branch Shift Assign</span>
                </a>
            </li>

            <li>
                <a href="{{url('assignShiftEmpList')}}">
                    <span class="title">Employee Shift Assign</span>
                </a>
            </li>
            <li>
                <a href="{{url('#')}}">
                    <span class="title">Demo Assignments Child</span>
                </a>
            </li>
        </ul>
    </li>
    <li>
        <a class="animated fadeInLeft" href="javascript:;">
            <i class="linecons-cog"></i>
            <span class="title">Management</span>
        </a>
        <ul>
            <li>
                <a href="{{url('#')}}">
                    <span class="title">Demo Management Child</span>
                </a>
            </li>
        </ul>
    </li>
    <li>
        <a class="animated fadeInLeft" href="javascript:;">
            <i class="linecons-cog"></i>
            <span class="title">Utility</span>
        </a>
        <ul>
            <li>
                <a href="{{url('#')}}">
                    <span class="title">Demo Utility Child</span>
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a class="animated fadeInLeft" href="javascript:;">
            <i class="linecons-cog"></i>
            <span class="title">Reports</span>
        </a>
        <ul>
            <li>
                <a href="{{url('report/attendenceReport')}}">
                    <span class="title">Attendence Report</span>
                </a>
            </li>
            <li>
                <a href="{{url('report/dailyAttendenceReport')}}">
                    <span class="title">Daily Attendence Report</span>
                </a>
            </li>
        </ul>
    </li>
</ul>
<style type="text/css">
#main-menu > li > ul >li > ul{
    left: 270px !important;
    top: -15px !important;
    z-index: 0;
    position: relative;
}

#main-menu > li#report-menu > ul >li >ul{
    left: -379px !important;
    top: -25px !important;
}

#main-menu > li#config > ul >li >ul{
    left: 286px !important;
    top: -27px !important;
}

/*register sub menu*/
#main-menu > li > ul >li >ul > li > ul#register{
    left: 10px !important;
    background-color: #ddd;
}

#main-menu > li > ul >li >ul > li > ul#register li{
    /*padding: 15px*/
    margin-left:5px;
}


#main-menu > li > ul >li >ul > li > ul#member-configuration{
    left: 10px !important;
    background-color: #ddd;

}
#main-menu > li > ul >li >ul > li > ul#member-configuration li{
    /*padding: 10px 5px !important;*/
    margin-left: 15px
}
#main-menu > li > ul >li >ul > li > ul#loanconfiguration{

    left: 10px !important;
    background-color: #ddd;

}
#main-menu > li > ul >li >ul > li > ul#loanconfiguration li{
    /*padding: 10px 5px !important;*/
    margin-left: 15px
}
#main-menu > li > ul >li >ul > li > ul#savingsproduct{

    left: 10px !important;
    background-color: #ddd;

}
#main-menu > li > ul >li >ul > li > ul#savingsproduct li{
    /*padding: 10px 5px !important;*/
    margin-left: 15px
}
#main-menu > li > ul >li >ul > li > ul#loanproduct{

    left: 10px !important;
    background-color: #ddd;

}
#main-menu > li > ul >li >ul > li > ul#loanproduct li{
    /*padding: 10px 5px !important;*/
    margin-left: 15px
}
#main-menu > li > ul >li >ul > li > ul#members{

    left: 10px !important;
    background-color: #ddd;

}
#main-menu > li > ul >li >ul > li > ul#members li{
    /*padding: 10px 5px !important;*/
    margin-left: 15px
}
#main-menu > li > ul >li >ul > li > ul#loan{

    left: 10px !important;
    background-color: #ddd;

}
#main-menu > li > ul >li >ul > li > ul#loan li{
    /*padding: 10px 5px !important;*/
    margin-left: 15px
}
#main-menu > li > ul >li >ul > li > ul#savings{

    left: 10px !important;
    background-color: #ddd;

}
#main-menu > li > ul >li >ul > li > ul#savings li{
    /*padding: 10px 5px !important;*/
    margin-left: 15px
}

#main-menu >li > ul > li{
    height: 30px !important;
}
</style>
<style type="text/css">
#main-menu li {
    padding: 0px  20px 0px 0px!important;
}
</style>

<script type="text/javascript">
    $(".holidaySubLink").click(function(){

        var url = $(".holidaySubLink").attr('href');

        var arr = url.split('public/');
        var arrSplit = arr[1].split('/');
        var prefix = arrSplit[0];
        // alert(prefix);

        // $.ajax({
        //     type: 'post',
        //     url: '/getPrefix',
        //     dataType: 'json',
        //     data: {data: prefix},
        //     success: function(data){
        //         console.log(data);
        //         },
        //     error: function(response){
        //             alert('Error'+response);
        //         }
        // });
    });



</script>
