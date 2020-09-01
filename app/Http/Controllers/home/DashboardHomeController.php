<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;

class DashboardHomeController extends Controller
{
   
    
    public function loadwelcomeTab1(){
    	return view('homePages.welcomeHomePages.loadWelcomeTab1');
    }

    public function loadwelcomeTab2(){
    	return view('homePages.welcomeHomePages.loadWelcomeTab2');
    }

    public function loadwelcomeTab3(){
    	return view('homePages.welcomeHomePages.loadWelcomeTab3');
    }

    public function loadwelcomeTab4(){
    	return view('homePages.welcomeHomePages.loadWelcomeTab4');
    }
}
