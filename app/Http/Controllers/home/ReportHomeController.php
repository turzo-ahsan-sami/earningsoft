<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;

class ReportHomeController extends Controller
{
    public function index(){     
      return view('homePages.reportHomePages.viewReport');
    }
    
    
    public function loadReportTab1(){
    	return view('homePages.reportHomePages.loadReportTab1');
    }

    public function loadReportTab2(){
    	return view('homePages.reportHomePages.loadReportTab2');
    }

    public function loadReportTab3(){
    	return view('homePages.reportHomePages.loadReportTab3');
    }

    public function loadReportTab4(){
    	return view('homePages.reportHomePages.loadReportTab4');
    }
}

