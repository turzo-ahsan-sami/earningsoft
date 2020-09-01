<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;

class HrmHomeController extends Controller {

    public function index() {
    	return view('homePages.hrmHomePages.viewHrmHome');
    }
    
    public function loadHrmTab1(){
    	return view('homePages.hrmHomePages.loadHrmTab1');
    }

    public function loadHrmTab2(){
    	return view('homePages.hrmHomePages.loadHrmTab2');
    }

    public function loadHrmTab3(){
    	return view('homePages.hrmHomePages.loadHrmTab3');
    }

    public function loadHrmTab4(){
    	return view('homePages.hrmHomePages.loadHrmTab4');
    }
}
