<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;

class GnrHomeController extends Controller
{
    public function index(){     
          return view('homePages.gnrHomePages.view_gnr_home');
    }
    public function loadgnrTab1(){     
          return view('homePages.gnrHomePages.loadgnrTab1');
    }
    public function loadgnrTab3(){     
          return view('homePages.gnrHomePages.loadgnrTab3');
    }
    
}
