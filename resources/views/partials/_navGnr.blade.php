<ul id="main-menu" class="navbar-nav">
    <li id="general-tools">
        <a class="animated fadeInLeft" href="">
            <i class="linecons-cog"></i>
            <span class="title">Tools</span>
        </a>               
                    
        <ul>
            <li>
                <a href="{{url('viewCompany/')}}">
                    <span class="title">Company</span>
                </a>
            </li>
            <li>
                <a href="{{url('viewProject/')}}">
                    <span class="title">Project</span>
                </a>
            </li>
           {{--  <li>
                <a href="{{url('viewProjectType/')}}">
                    <span class="title">Project Type</span>
                </a>
            </li> --}}
            <li>
                <a href="{{url('viewBranch/')}}">
                    <span class="title">Branch</span>
                </a>
            </li>
            {{-- <li>
                <a href="javascript:;">
                    <span class="title">Holiday</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ url('gnr/viewHolidayCalender/') }}">
                            <span class="title">Holiday Calender</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('gnr/viewGovHoliday/') }}">
                            <span class="title">Gov. Holiday</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('gnr/viewOrgBranchSamityHoliday/') }}">
                            <span class="title">Org./Branch/Samity Holiday</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('gnr/viewHolidayList/') }}">
                            <span class="title">Holiday List</span>
                        </a>
                    </li>

                 

                </ul>
            </li> --}}
            
        </ul>
    </li>

    <li id="emp-tools">
        <a class="animated fadeInLeft" href="">
            <i class="fa-user"></i>
            <span class="title">Employee</span>
        </a>               
                    
        <ul>
            <li>
                <a href="{{ url('gnr/posHrEmployeeList') }}">
                    <span class="title">Employee List</span>
                </a>   
            </li>
            
            <li>
                <a href="{{ url('gnr/viewDepartment/') }}">
                    <span class="title">Department</span>
                </a>
            </li>
             <li>
                <a href="{{ url('gnr/viewPosition/') }}">
                    <span class="title">Position</span>
                </a>
            </li>
            
        </ul>
    </li>
</ul>
<style type="text/css">
#main-menu > li > ul >li > ul{
    left: 268px !important;
    top: -26px !important;

}

#main-menu >li > ul > li{
    height: 26px !important;

}
/*#main-menu >li#general-tools > ul > li{

    left: 270px !important;
    top: -26px !important;
    }*/
</style>

<style type="text/css">
#main-menu li {     
    padding: 0px  20px 0px 0px!important;
}
#main-menu li a {     
   /* padding: 0px  20px 0px 0px!important;*/
}
</style>