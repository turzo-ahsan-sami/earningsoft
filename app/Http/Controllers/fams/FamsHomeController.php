<?php

namespace App\Http\Controllers\fams;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\fams\FamsPcolor;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class FamsHomeController extends Controller
{
    public function index(){     
      return view('fams/view_fams_home');
    }
    
}
