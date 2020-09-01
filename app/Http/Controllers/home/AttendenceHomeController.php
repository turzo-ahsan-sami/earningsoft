<?php

namespace App\Http\Controllers\home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AttendenceHomeController extends Controller
{
	public function index(){
		// echo 'This is attendence home controller';

		return view ('attendence.dashboard.dashboard');
	}

}
