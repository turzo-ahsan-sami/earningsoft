<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;
use App\Http\Controllers\home\AccHomeController;

use Route;

class AccChartController extends Controller {

    protected $data;
    protected $orgInfoObj;

    public function __construct() {
        $this->orgInfoObj = new AccHomeController;
        $this->data = $this->orgInfoObj->loadingDivInfos();
    }

    public function loadChart1() {
        $path = Route::current()->uri();
        if($path == 'acc/loadFullChart/Tab1') {
            return view('homePages.accHomePages.accCharts.loadFullChart1', $this->data);
        } elseif($path == 'acc/loadChart/Tab1') {
            return view('homePages.accHomePages.accCharts.loadChart1', $this->data);
        }
    }

    public function loadChart2() {

        $path = Route::current()->uri();
        if($path == 'acc/loadFullChart/Tab2') {
            return view('homePages.accHomePages.accCharts.loadFullChart2', $this->data);
        } elseif($path == 'acc/loadChart/Tab2') {
            return view('homePages.accHomePages.accCharts.loadChart2', $this->data);
        }

    }

    public function loadChart3() {

        $path = Route::current()->uri();
        if($path == 'acc/loadFullChart/Tab3') {
            return view('homePages.accHomePages.accCharts.loadFullChart3', $this->data);
        } elseif($path == 'acc/loadChart/Tab3') {
            return view('homePages.accHomePages.accCharts.loadChart3', $this->data);
        }

    }

    public function loadChart4() {

        $path = Route::current()->uri();
        if($path == 'acc/loadFullChart/Tab4') {
            return view('homePages.accHomePages.accCharts.loadFullChart4', $this->data);
        } elseif($path == 'mfn/loadChart/Tab4') {
            return view('homePages.accHomePages.accCharts.loadChart4', $this->data);
        }

    }

}
