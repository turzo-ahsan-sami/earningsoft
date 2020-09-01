<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;

class PosHomeController extends Controller
{
    public function index(){     
      return view('homePages.posHomePages.viewPosHome');
    }
    
    public function loadPosTab1(){
    	return view('homePages.posHomePages.loadPosTab1');
    }

    public function loadPosTab2(){
    	return view('homePages.posHomePages.loadPosTab2');
    }

    public function loadPosTab3(){
    	return view('homePages.posHomePages.loadPosTab3');
    }

    public function loadPosTab4(){
    	return view('homePages.posHomePages.loadPosTab4');
    }
}
