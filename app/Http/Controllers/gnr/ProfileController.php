<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
// use App\microfin\employee\HrGnrEmployeeInfo;
// use App\microfin\employee\HrGnrOrganaizationInfo;
use App\User;
use App\gnr\GnrEmployee;
use App\gnr\GnrDepartment;
use App\gnr\GnrPosition;
use Validator;
use Response;
use DB;
use Route;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App;
use App\gnr\GnrBranch;


class ProfileController extends Controller
{
        //========START FILTARING FOR ADDRESS====

    public function profile($employeeId)
    {

        $hrAllEmployeeDetails = GnrEmployee::where('id',$employeeId)->first();

        return view('gnr/profile/profile', ['hrAllEmployeeDetails' => $hrAllEmployeeDetails]);

    }

   

}