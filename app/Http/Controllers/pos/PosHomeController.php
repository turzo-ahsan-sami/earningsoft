<?php
namespace App\Http\Controllers\pos\;

use App\Http\Controllers\Controller;

class PosHomeController extends Controller
{
	public function index(){      
      return view('pos/view_pos_home');      
    }
}

?>