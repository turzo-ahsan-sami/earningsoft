<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;

class InvHomeController extends Controller
{
    public function index(){     
      return view('homePages.invHomePages.viewInvHome');
    }
    
    public function loadInvTab1(){
    	return view('homePages.invHomePages.loadInvTab1');
    }

    public function loadInvTab2(){
    	return view('homePages.invHomePages.loadInvTab2');
    }

    public function loadInvTab3(){
    	return view('homePages.invHomePages.loadInvTab3');
    }

    public function loadInvTab4(){
    	return view('homePages.invHomePages.loadInvTab4');
    }
}
