<?php
	
	namespace App\Http\Controllers\microfin;

	use App\Http\Controllers\Controller;

	class MfnHomeController extends Controller {
		
		public function index() {      
	      
	      return view('microfin/view_microfin_home');      
	    }
	}

