<?php

	namespace App\Http\Controllers\microfin\employee;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\loan\MfnProduct;
	use Session;
	use Validator;
	use Response;
	use DB;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\Auth;
	use App\Http\Controllers\Controller;
	use App\Http\Controllers\microfin\MicroFinance;

	class MfnEmployeeController extends Controller {

		protected $MicroFinance;

		public function __construct() {

			$this->MicroFinance = New MicroFinance;

			$this->TCN = array(
				array('SL No.', 70), 
				array('Name', 0),
				array('Employee ID', 0),
				array('Branch', 0),
				array('Designation', 0),
				array('Joining Date', 100),
				array('Blood Group', 100),
				array('Mobile No', 100),
				array('Status', 80)
			);
		}

		public function index() {

			$damageData = array(
				'TCN'	        =>  $this->TCN,
				'employeeList'  =>  $this->MicroFinance->getEmployeeList(),
				'MicroFinance'  =>  $this->MicroFinance
			);

			return view('microfin.employee.viewEmployee', ['damageData' => $damageData]);
		}

		
		
	}