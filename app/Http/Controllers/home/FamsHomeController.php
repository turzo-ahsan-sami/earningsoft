<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;

class FamsHomeController extends Controller
{
    public function index(){     
      return view('homePages.famsHomePages.viewFamsHome');
    }
    
    public function loadFamsTab1(){
    	return view('homePages.famsHomePages.loadFamsTab1');
    }

    public function loadFamsTab2(){
    	return view('homePages.famsHomePages.loadFamsTab2');
    }

    public function loadFamsTab3(){
    	return view('homePages.famsHomePages.loadFamsTab3');
    }

    public function loadFamsTab4(){
    	return view('homePages.famsHomePages.loadFamsTab4');
    }
    
}
