<?php
namespace App\Http\Controllers\gnr;

use App\Http\Controllers\Controller;

class GnrHomeController extends Controller
{
	public function index(){      
      return view('gnr/view_gnr_home');      
    }
}



?>