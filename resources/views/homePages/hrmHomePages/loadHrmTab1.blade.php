
<div class="col-xs-12">
	@if ($role != 2)
		<div class="row">
			<div class="col-xs-12">
				<h4 class="title_name">Operations</h4>
				<div class="div_content animated fadeInLeft">
					<a href="{{ url('./hr/attendence')}}">
						<img src="{{ asset('images/dashboards/hrmDashboard/attendance.png') }}" ><br/>
						<p>Attendance</p>
					</a>
				</div>
				<div class="div_content animated fadeInLeft">
					<a href="{{ url('./hr/employee/addGeneralInformation')}}">
						<img src="{{ asset('images/dashboards/hrmDashboard/employees.png') }}" ><br/>
						<p>Employees</p>
					</a>
				</div>
				<div class="div_content  animated fadeInDown">
					<a href="{{ url('./hr/transfer/create')}}">
						<img src="{{ asset('images/dashboards/hrmDashboard/transfer.png') }}" ><br/>
						<p>Transfer</p>
					</a>
				</div>
				<div class="div_content  animated fadeInRight">
					<a href="{{ url('./hr/resignInfo/create')}}">
						<img src="{{ asset('images/dashboards/hrmDashboard/resignation.png') }}" ><br/>
						<p>Resignation</p>
					</a>
				</div>
				<div class="div_content  animated fadeInRight">
					<a href="{{ url('./hr/terminateInfo/create')}}">
						<img src="{{ asset('images/dashboards/hrmDashboard/termination.png') }}" ><br/>
						<p>Termination</p>
					</a>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<h4 class="title_name">Process</h4>
				<h4 class="title_name">Generate</h4>

				<div class="div_content  animated fadeInLeft">
					<a href="{{ url('./hr/salaryGenerate/create')}}">
						<img src="{{ asset('images/dashboards/hrmDashboard/salaryGenerate.png') }}" ><br/>
						<p>Salary Generate</p>
					</a>
				</div>
				<div class="div_content  animated fadeInUp">
					<a href="{{ url('./hr/securityDepositInterest/create')}}">
						<img src="{{ asset('images/dashboards/hrmDashboard/securityInterest.png') }}" ><br/>
						<p>Security Interest Generate</p>
					</a>
				</div>
				<div class="div_content  animated fadeInDown">
					<a href="{{ url('./hr/edpsInterest/create')}}">
						<img src="{{ asset('images/dashboards/hrmDashboard/edpsInterest.png') }}" ><br/>
						<p>EDPS Interest</p>
					</a>
				</div>
				<div class="div_content  animated fadeInRight">
					<a href="{{ url('./hr/pfInterest/create')}}">
						<img src="{{ asset('images/dashboards/hrmDashboard/pfInterest.png') }}" ><br/>
						<p>PF Interest Generate</p>
					</a>
				</div>

			</div>
		</div>
	@endif
	<div class="row">
		<div class="col-xs-12">
			<h4 class="title_name">Leave</h4>

			<div class="div_content  animated fadeInUp">
				<a href="{{ url('./hr/hrLeaveApplication/create')}}">
					<img src="{{ asset('images/dashboards/hrmDashboard/leaveApplication.png') }}" ><br/>
					<p>Leave Application</p>
				</a>
			</div>
			<div class="div_content  animated fadeInUp">
				<a href="#">
					<img src="{{ asset('images/dashboards/hrmDashboard/reminderLeave.png') }}" ><br/>
					<p>Renaining Leave</p>
				</a>
			</div>

		</div>
	</div>
</div>
