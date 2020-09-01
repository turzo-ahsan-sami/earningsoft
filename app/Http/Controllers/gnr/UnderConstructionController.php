<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\gnr\GnrBranch;
use Validator;
use Response;
use App\Http\Controllers\gnr\Service;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class UnderConstructionController extends Controller
{
	public function underPreparationReport(){
		$userBranchId = Auth::user()->branchId;
		return view('gnr.underPrepartionReport.underPrepartionReport');
	}

}
