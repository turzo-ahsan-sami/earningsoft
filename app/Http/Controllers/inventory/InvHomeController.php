<?php
namespace App\Http\Controllers\inventory;

use App\Http\Controllers\Controller;

class InvHomeController extends Controller
{
	public function index(){      
      return view('inventory/view_inventory_home');      
    }
}

?>