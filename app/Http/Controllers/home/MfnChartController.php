<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;
use App\Http\Controllers\home\MfnHomeController;

use Route;

class MfnChartController extends Controller {

    protected $data;
    protected $orgInfoObj;

    public function __construct() {
        $this->orgInfoObj = new MfnHomeController;
        $this->data = $this->orgInfoObj->loadingDivInfos();
    }

    public function loadChart1() {
        $path = Route::current()->uri();
        if($path == 'mfn/loadFullChart/Tab1') {
            return view('homePages.mfnHomePages.mfnCharts.loadFullChart1', $this->data);
        } elseif($path == 'mfn/loadChart/Tab1') {
            return view('homePages.mfnHomePages.mfnCharts.loadChart1', $this->data);
        }
    }

    public function loadChart2() {

        $path = Route::current()->uri();
        if($path == 'mfn/loadFullChart/Tab2') {
            return view('homePages.mfnHomePages.mfnCharts.loadFullChart2', $this->data);
        } elseif($path == 'mfn/loadChart/Tab2') {
            return view('homePages.mfnHomePages.mfnCharts.loadChart2', $this->data);
        }

    }

    public function loadChart3() {

        $path = Route::current()->uri();
        if($path == 'mfn/loadFullChart/Tab3') {
            return view('homePages.mfnHomePages.mfnCharts.loadFullChart3', $this->data);
        } elseif($path == 'mfn/loadChart/Tab3') {
            return view('homePages.mfnHomePages.mfnCharts.loadChart3', $this->data);
        }

    }

    public function loadChart4() {

        $path = Route::current()->uri();
        if($path == 'mfn/loadFullChart/Tab4') {
            return view('homePages.mfnHomePages.mfnCharts.loadFullChart4', $this->data);
        } elseif($path == 'mfn/loadChart/Tab4') {
            return view('homePages.mfnHomePages.mfnCharts.loadChart4', $this->data);
        }

    }

    public function loadChart5() {

        $path = Route::current()->uri();
        if($path == 'mfn/loadFullChart/Tab5') {
            return view('homePages.mfnHomePages.mfnCharts.loadFullChart5', $this->data);
        } elseif($path == 'mfn/loadChart/Tab5') {
            return view('homePages.mfnHomePages.mfnCharts.loadChart5', $this->data);
        }

    }

    public function loadChart6() {

        $path = Route::current()->uri();
        if($path == 'mfn/loadFullChart/Tab6') {
            return view('homePages.mfnHomePages.mfnCharts.loadFullChart6', $this->data);
        } elseif($path == 'mfn/loadChart/Tab6') {
            return view('homePages.mfnHomePages.mfnCharts.loadChart6', $this->data);
        }

    }

    public function loadChart7() {

        $path = Route::current()->uri();
        if($path == 'mfn/loadFullChart/Tab7') {
            return view('homePages.mfnHomePages.mfnCharts.loadFullChart7', $this->data);
        } elseif($path == 'mfn/loadChart/Tab7') {
            return view('homePages.mfnHomePages.mfnCharts.loadChart7', $this->data);
        }

    }

    public function loadChart8() {

        $path = Route::current()->uri();
        if($path == 'mfn/loadFullChart/Tab8') {
            return view('homePages.mfnHomePages.mfnCharts.loadFullChart8', $this->data);
        } elseif($path == 'mfn/loadChart/Tab8') {
            return view('homePages.mfnHomePages.mfnCharts.loadChart8', $this->data);
        }

    }

    public function loadChart9() {

        $path = Route::current()->uri();
        if($path == 'mfn/loadFullChart/Tab9') {
            return view('homePages.mfnHomePages.mfnCharts.loadFullChart9', $this->data);
        } elseif($path == 'mfn/loadChart/Tab9') {
            return view('homePages.mfnHomePages.mfnCharts.loadChart9', $this->data);
        }

    }

    public function loadChart10() {

        $path = Route::current()->uri();
        if($path == 'mfn/loadFullChart/Tab10') {
            return view('homePages.mfnHomePages.mfnCharts.loadFullChart10', $this->data);
        } elseif($path == 'mfn/loadChart/Tab10') {
            return view('homePages.mfnHomePages.mfnCharts.loadChart10', $this->data);
        }

    }

    public function loadChart11() {

        $path = Route::current()->uri();
        if($path == 'mfn/loadFullChart/Tab11') {
            return view('homePages.mfnHomePages.mfnCharts.loadFullChart11', $this->data);
        } elseif($path == 'mfn/loadChart/Tab11') {
            return view('homePages.mfnHomePages.mfnCharts.loadChart11', $this->data);
        }

    }

}
