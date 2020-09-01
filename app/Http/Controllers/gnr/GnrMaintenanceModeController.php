<?php
	
	namespace App\Http\Controllers\gnr;

	use Artisan;
	use App\Http\Controllers\Controller;

	class GnrMaintenanceModeController extends Controller {
		

		public function siteUp() {			
			Artisan::call('up');
		}

		public function siteDown() {			
			Artisan::call('down');
		}

	}
