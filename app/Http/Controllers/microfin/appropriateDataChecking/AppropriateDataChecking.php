<?php 

namespace App\Http\Controllers\microfin\appropriateDataChecking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Carbon\Carbon;
use Response;
use DateTime;
use DB;

use App\microfin\savings\MfnSavingsClosing;
use App\microfin\savings\MfnSavingsAccount;
use App\microfin\savings\MfnSavingsDeposit;
use App\microfin\savings\MfnSavingsWithdraw;
use App\microfin\savings\MfnOpeningSavingsAccountInfo;

use Excel;

class AppropriateDataChecking extends Controller
{
	public function index () {

		$maxBranchId = DB::table('gnr_branch')->max('id');

		$branchId = DB::table('mfn_BranchInfoFor_AppropriateDataChecking')->first()->branchId;
		
		$this->dataChecking($branchId, $maxBranchId);
		
	}
	
	public function dataChecking ($branchId, $maxBranchId) {
		
		$toDayDate = date("Y-m-d");

		// Excel::create('dataCheckingFor'.$toDayDate, function($excel) { 

		//     $excel->setTitle('Data Checking For'.$toDayDate);

		//     $data = [12,"Hey",123,4234,5632435,"Nope",345,345,345,345];

		//     $excel->sheet('Sheet 1', function ($sheet) use ($data) {
		//         $sheet->setOrientation('landscape');
		//         $sheet->fromArray($data, NULL, 'A3');
		//     });

		// })->store('xls', storage_path('excel-folder'));

		$allSavingsAccount            = MfnSavingsAccount::where('branchIdFk', $branchId)->where('softDel', '=', 0)->select('id', 'memberIdFk')->get();

		$allSavingsIds                = $allSavingsAccount->pluck('id')->toArray();

		$allSavingsAccountCollection  = new MfnSavingsDeposit;

		$allSavingsAccountWithdraw    = new MfnSavingsWithdraw;

		$allSavingsAccountOpeningInfo = new MfnOpeningSavingsAccountInfo;

		$memberAndSavingsAccontInfos  = array();

		$memberAndSavingsAccontCounter= 0;

		foreach ($allSavingsIds as $allSavingsIdsValue) {
			
			$testSavingsOpeningCololection =  $allSavingsAccountOpeningInfo->where('savingsAccIdFk', $allSavingsIdsValue)->where('softDel', '=', 0)->sum('openingBalance');

			$testSavingsCololection        = $allSavingsAccountCollection->where('accountIdFk', $allSavingsIdsValue)->where('softDel', '=', 0)->sum('amount');

			$testSavingsWithdraw           = $allSavingsAccountWithdraw->where('accountIdFk', $allSavingsIdsValue)->where('softDel', '=', 0)->sum('amount');

			if ((($testSavingsOpeningCololection + $testSavingsCololection) - $testSavingsWithdraw) < 0) {

				$memberAndSavingsAccontInfos[$memberAndSavingsAccontCounter]['memberIdFk']  = $allSavingsIdsValue->memberIdFk;
				$memberAndSavingsAccontInfos[$memberAndSavingsAccontCounter]['accountIdFk'] = $allSavingsAccountWithdraw->where('accountIdFk', $allSavingsIdsValue)->where('softDel', '=', 0)->first()->accountIdFk;
				$memberAndSavingsAccontInfos[$memberAndSavingsAccontCounter]['deposite']    = $testSavingsCololection;
				$memberAndSavingsAccontInfos[$memberAndSavingsAccontCounter]['withdraw']    = $testSavingsWithdraw;
				$memberAndSavingsAccontInfos[$memberAndSavingsAccontCounter]['opening']     = $testSavingsOpeningCololection;
				$memberAndSavingsAccontInfos[$memberAndSavingsAccontCounter]['result']      = ($testSavingsOpeningCololection + $testSavingsCololection) - $testSavingsWithdraw;

			}

		}

		if ($maxBranchId != $branchId) {
			DB::table('mfn_BranchInfoFor_AppropriateDataChecking')
			->where('id', 1)
			->update(
				[
					'branchId' => $branchId+1
				]
			);
		}
		else {
			DB::table('mfn_BranchInfoFor_AppropriateDataChecking')
			->where('id', 1)
			->update(
				[
					'branchId' => 2
				]
			);

		}

		dd('Branch Id : ', $branchId, 'Total Size : ', sizeof($allSavingsAccount), 'Infos : ', $memberAndSavingsAccontInfos, 'Max Branch ID : ', $maxBranchId);

		dd("Data Checking!!", $allSavingsIds, $allSavingsAccount);

	}
}