<?php

namespace App\Http\Controllers\microfin\process;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Auth;
use App\Http\Controllers\microfin\MicroFin;
use App\Http\Controllers\microfin\MicroFinance;
use App\Http\Controllers\gnr\Service;
use App\microfin\savings\MfnSavingsDeposit;
use App\microfin\savings\MfnSavingsWithdraw;
use App\microfin\member\MfnMemberPrimaryProductTransfer;
use App\microfin\settings\MfnLoanProductInterestRate;
use App\microfin\loan\MfnLoan;
use App\microfin\loan\MfnLoanSchedule;

class MiscellaneousController extends Controller
{

	public function index(Request $req)
	{
		// $date1 = Carbon::parse('2019-12-01');
		// $date2 = Carbon::parse('2020-01-31');

		// dd($date1->diffInMonths($date2));

		$schedules = Microfin::generateLoanSchedule(218471);
		dd('done');
		$schedules = Microfin::makeLoanSchedule(218470);
		echo "<pre>";
		print_r($schedules);
		echo "</pre>";
		dd('stoped');
	}

	public function calculateDueCollection($branchId = 4, $month = '2019-03-01')
	{
		$monthStartDate = Carbon::parse($month)->startOfMonth()->format('Y-m-d');
		$monthEndDate = Carbon::parse($month)->endOfMonth()->format('Y-m-d');

		$loanPayables = DB::table('mfn_loan_schedule AS ls')
			->join('mfn_loan AS loan', 'loan.id', 'ls.loanIdFk')
			->where('ls.softDel', 0)
			->where('loan.softDel', 0)
			->where('loan.branchIdFk', $branchId)
			->where('ls.scheduleDate', '<', $monthStartDate)
			->select(DB::raw('ls.loanIdFk,SUM(ls.installmentAmount) AS installmentAmount, SUM(ls.principalAmount) AS principalAmount'))
			->groupBy('ls.loanIdFk')
			->get();

		$previousCollections = DB::table('mfn_loan_collection')
			->where('softDel', 0)
			->where('branchIdFk', $branchId)
			->where('collectionDate', '<', $monthStartDate)
			->groupBy('loanIdFk')
			->select(DB::raw("loanIdFk, SUM(amount) AS amount, SUM(principalAmount) AS principalAmount"))
			->get();

		$openingBalances = DB::table('mfn_opening_balance_loan AS opb')
			->join('mfn_loan AS loan', 'loan.id', 'opb.loanIdFk')
			->where('loan.softDel', 0)
			->where('loan.branchIdFk', $branchId)
			->select(DB::raw("opb.loanIdFk,opb.paidLoanAmountOB, opb.principalAmountOB"))
			->get();

		foreach ($openingBalances as $openingBalance) {
			$previousCollections->push([
				'loanIdFk'          => $openingBalance->loanIdFk,
				'amount'            => $openingBalance->paidLoanAmountOB,
				'principalAmount'   => $openingBalance->principalAmountOB
			]);
		}

		$previousLoanWaivers = DB::table('mfn_loan_waivers')
			->where('softDel', 0)
			->where('branchIdFk', $branchId)
			->where('date', '<', $monthStartDate)
			->select('loanIdFk', 'amount', 'principalAmount')
			->get();

		foreach ($previousLoanWaivers as $previousLoanWaiver) {
			$previousCollections->push([
				'loanIdFk'          => $previousLoanWaiver->loanIdFk,
				'amount'            => $previousLoanWaiver->amount,
				'principalAmount'   => $previousLoanWaiver->principalAmount
			]);
		}

		$previousLoanRebates = DB::table('mfn_loan_rebates')
			->where('softDel', 0)
			->where('branchIdFk', $branchId)
			->where('date', '<', $monthStartDate)
			->select('loanIdFk', 'amount')
			->get();

		foreach ($previousLoanRebates as $previousLoanRebate) {
			$previousCollections->push([
				'loanIdFk'          => $previousLoanRebate->loanIdFk,
				'amount'            => $previousLoanRebate->amount,
				'principalAmount'   => 0
			]);
		}

		$thisMonthCollections = DB::table('mfn_loan_collection')
			->where('softDel', 0)
			->where('branchIdFk', $branchId)
			->where('collectionDate', '>=', $monthStartDate)
			->where('collectionDate', '<=', $monthEndDate)
			->select(DB::raw("loanIdFk, SUM(amount) AS amount, SUM(principalAmount) AS principalAmount"))
			->groupBy('loanIdFk')
			->get();

		$thisMonthLoanWaivers = DB::table('mfn_loan_waivers')
			->where('softDel', 0)
			->where('branchIdFk', $branchId)
			->where('date', '>=', $monthStartDate)
			->where('date', '<=', $monthEndDate)
			->select('loanIdFk', 'amount', 'principalAmount')
			->get();

		foreach ($thisMonthLoanWaivers as $thisMonthLoanWaiver) {
			$thisMonthCollections->push([
				'loanIdFk'          => $thisMonthLoanWaiver->loanIdFk,
				'amount'            => $thisMonthLoanWaiver->amount,
				'principalAmount'   => $thisMonthLoanWaiver->principalAmount
			]);
		}

		$thisMonthLoanRebates = DB::table('mfn_loan_rebates')
			->where('softDel', 0)
			->where('branchIdFk', $branchId)
			->where('date', '>=', $monthStartDate)
			->where('date', '<=', $monthEndDate)
			->select('loanIdFk', 'amount')
			->get();

		foreach ($thisMonthLoanRebates as $thisMonthLoanRebate) {
			$thisMonthCollections->push([
				'loanIdFk'          => $thisMonthLoanRebate->loanIdFk,
				'amount'            => $thisMonthLoanRebate->amount,
				'principalAmount'   => 0
			]);
		}

		$loans = DB::table('mfn_loan')
			->where('softDel', 0)
			// ->where('productIdFk',38)
			->where('branchIdFk', $branchId)
			->where('disbursementDate', '<=', $monthEndDate)
			->select('id', 'loanCode')
			->get();

		$totalDueCollectionAmount = 0;
		$totalDueCollectionPrincipalAmount = 0;

		$totalPreviousDueAmount = 0;
		$totalPreviousDuePrincipalAmount = 0;

		$dueLoanIds = [];
		$dueLoanIdsForPrincipal = [];

		foreach ($loans as $loan) {

			// WITH INTEREST
			$payableAmount = $loanPayables->where('loanIdFk', $loan->id)->sum('installmentAmount');
			// $previousCollectionAmount = $openingBalances->where('loanIdFk',$loan->id)->sum('paidLoanAmountOB');
			$previousCollectionAmount = $previousCollections->where('loanIdFk', $loan->id)->sum('amount');

			if ($payableAmount - $previousCollectionAmount > 1) {
				$dueAmount = $payableAmount - $previousCollectionAmount;
				$totalPreviousDueAmount += $dueAmount;
				$thisMonthCollectionAmount = $thisMonthCollections->where('loanIdFk', $loan->id)->sum('amount');
				$dueCollection = $thisMonthCollectionAmount > $dueAmount ? $dueAmount : $thisMonthCollectionAmount;
				$totalDueCollectionAmount += $dueCollection;

				array_push($dueLoanIds, $loan->id);
			}

			// WITHOUT INTEREST/ PRINCIPAL
			$payableAmountP = $loanPayables->where('loanIdFk', $loan->id)->sum('principalAmount');
			// $previousCollectionAmountP = $openingBalances->where('loanIdFk',$loan->id)->sum('principalAmountOB');
			$previousCollectionAmountP = $previousCollections->where('loanIdFk', $loan->id)->sum('principalAmount');

			if ($payableAmountP - $previousCollectionAmountP > 1) {
				$dueAmountP = $payableAmountP - $previousCollectionAmountP;
				$totalPreviousDuePrincipalAmount += $dueAmountP;
				$thisMonthCollectionAmountP = $thisMonthCollections->where('loanIdFk', $loan->id)->sum('principalAmount');
				$dueCollectionP = $thisMonthCollectionAmountP > $dueAmountP ? $dueAmountP : $thisMonthCollectionAmountP;
				$totalDueCollectionPrincipalAmount += $dueCollectionP;

				array_push($dueLoanIdsForPrincipal, $loan->id);
			}
		}

		echo 'Total Due Collection Amount: ' . $totalDueCollectionAmount . '<br>';
		echo 'Total Due Collection Principal Amount: ' . $totalDueCollectionPrincipalAmount . '<br><br>';
		echo '$totalPreviousDueAmount: ' . $totalPreviousDueAmount . '<br>';
		echo '$totalPreviousDuePrincipalAmount: ' . $totalPreviousDuePrincipalAmount . '<br>';

		/*$targetLoanIds = array_diff($dueLoanIdsForPrincipal, $dueLoanIds);
        $targetLoanIds = array_merge($targetLoanIds,array_diff($dueLoanIds,$dueLoanIdsForPrincipal));
        $targetLoanIds = array_unique($targetLoanIds);
        echo implode(',', $targetLoanIds);*/

		echo count($dueLoanIds) . "<br>";
		echo count($dueLoanIdsForPrincipal) . "<br>";
	}

	public function findInapropriateLoanCoompleteDate($branchId = 5, $month = '2019-04-01')
	{
		$monthStartDate = Carbon::parse($month)->startOfMonth()->format('Y-m-d');
		$monthEndDate = Carbon::parse($month)->endOfMonth()->format('Y-m-d');

		$loans = DB::table('mfn_loan')
			->where('softDel', 0)
			->where('branchIdFk', $branchId)
			->where('loanCompletedDate', '>=', $monthStartDate)
			->where('loanCompletedDate', '<=', $monthEndDate)
			->select('id', 'loanCode', 'loanAmount', 'totalRepayAmount')
			->get();

		$collections = DB::table('mfn_loan_collection')
			->where('softDel', 0)
			->whereIn('loanIdFk', $loans->pluck('id'))
			->groupBy('loanIdFk')
			->select(DB::raw("loanIdFk, SUM(amount) AS amount, SUM(principalAmount) AS principalAmount"))
			->get();

		$openingBalances = DB::table('mfn_opening_balance_loan')
			->whereIn('loanIdFk', $loans->pluck('id'))
			->select(DB::raw("loanIdFk,paidLoanAmountOB, principalAmountOB"))
			->get();

		foreach ($loans as $loan) {
			$loanBalance = $loan->totalRepayAmount - $collections->where('loanIdFk', $loan->id)->sum('amount') - $openingBalances->where('loanIdFk', $loan->id)->sum('paidLoanAmountOB');

			if ($loanBalance > 0) {
				echo 'For total<br>';
				echo $loan->loanCode . '<br>';
				echo '$loanBalance: ' . $loanBalance . '<br><br>';
			}

			$loanBalance = $loan->loanAmount - $collections->where('loanIdFk', $loan->id)->sum('principalAmount') - $openingBalances->where('loanIdFk', $loan->id)->sum('principalAmountOB');

			if ($loanBalance > 1) {
				echo 'For principal<br>';
				echo $loan->loanCode . '<br>';
				echo '$loanBalance: ' . $loanBalance . '<br><br>';
			}

			if ($loanBalance < -100) {
				echo 'For principal<br>';
				echo $loan->loanCode . '<br>';
				echo '$loanBalance: ' . $loanBalance . '<br><br>';
			}
		}
	}

	public function generateLoanSchedule($loanIdArr = null)
	{

		DB::beginTransaction();

		try {

			//  UPDATE SCHEDULE ON FIRST REPAY DATE BASIS.
			$repaymentFrequencyWiseRepayDate = [
				'1'  =>  7,
				'2'  =>  30
			];

			$microFinance = new MicroFinance;

			$globalGovtHoliday = $microFinance->getGlobalGovtHoliday();
			$organizationHoliday = $microFinance->getOrganizationHoliday(1);

			if ($loanIdArr == null) {
				$loanIdArr = [];
			}

			// dd($loanIdArr);


			$openingBalances = DB::table('mfn_opening_balance_loan')
				->whereIn('loanIdFk', $loanIdArr)
				->select('loanIdFk', 'paidLoanAmountOB')
				->get();

			$collections = DB::table('mfn_loan_collection')
				->where('softDel', 0)
				->whereIn('loanIdFk', $loanIdArr)
				->select('loanIdFk', 'amount');

			foreach ($loanIdArr as $loanId) :
				$loanOB = DB::table('mfn_loan')->where('id', $loanId)->select('branchIdFk', 'loanCode', 'loanTypeId', 'samityIdFk', 'repaymentFrequencyIdFk', 'repaymentNo', 'firstRepayDate', 'disbursementDate', 'memberIdFk', 'primaryProductIdFk', 'installmentAmount', 'interestRateIndex', 'actualInstallmentAmount', 'extraInstallmentAmount', 'totalRepayAmount', 'loanAmount')->first();

				$samityDayChanges = DB::table('mfn_samity_day_change')
					->where('samityId', $loanOB->samityIdFk)
					->where('effectiveDate', '>=', $loanOB->firstRepayDate)
					->select('effectiveDate', 'samityDayOrFixedDateId', 'samityDayId', 'newSamityDayId', 'fixedDate', 'newFixedDate')
					->get();

				DB::table('mfn_loan_schedule')->where('loanIdFk', $loanId)->delete();

				$branchHoliday = $microFinance->getBranchHolidayNew($loanOB->branchIdFk);
				$samityHoliday = $microFinance->getSamityHolidayWithSamityParam($loanOB->samityIdFk);
				$holiday = array_unique(array_merge($globalGovtHoliday, $organizationHoliday, $branchHoliday, $samityHoliday));

				$holidayFound = 0;
				$scheduleDateArr = [];
				$repaymentFrequencyId = $loanOB->repaymentFrequencyIdFk;
				$repaymentNo = $loanOB->repaymentNo;
				$firstRepayDate = $loanOB->firstRepayDate;
				$disbursementDate = $loanOB->disbursementDate;
				$memberId = $loanOB->memberIdFk;

				/*echo '<pre>';
                        print_r($loanOB);
                        echo '</pre>';*/

				for ($i = 0; $i < 1000; $i++) :

					$dayDiff = ($repaymentFrequencyWiseRepayDate[$repaymentFrequencyId] * $i) . 'days';
					$date = date_create($firstRepayDate);
					date_add($date, date_interval_create_from_date_string($dayDiff));

					// IF SAMITY DAY US CHANGED THEN CONSIDER THE FIRST REPAY DATE AS A NEW SAMITY DAY
					$newSamityDay = null;
					$newSamityDay = $samityDayChanges->where('effectiveDate', '<=', date_format($date, "Y-m-d"))->sortByDesc('effectiveDate')->first();
					if ($newSamityDay != null) {

						$firstRepayDate = Carbon::parse($loanOB->firstRepayDate);
						$firstReapayDateWeekDayNumber = $firstRepayDate->dayOfWeek == 6 ? 1 : $firstRepayDate->dayOfWeek + 2;
						$dayNumber = $firstRepayDate->day;

						// dd($firstRepayDate,$newSamityDay);


						if ($newSamityDay->samityDayOrFixedDateId == 1 || $newSamityDay->samityDayOrFixedDateId == 0) {
							if ($newSamityDay->newSamityDayId > $firstReapayDateWeekDayNumber) {
								$firstRepayDate->addDays($newSamityDay->newSamityDayId - $firstReapayDateWeekDayNumber);
							} else {
								$firstRepayDate->addDays(7 + $newSamityDay->newSamityDayId - $firstReapayDateWeekDayNumber);
							}
						} else {
							if ($newSamityDay->newFixedDate > $dayNumber) {
								$firstRepayDate->addDays($newSamityDay->newFixedDate - $dayNumber);
							} else {
								$firstRepayDate->subDays($dayNumber - $newSamityDay->newFixedDate);
							}
						}

						$firstRepayDate = $firstRepayDate->format('Y-m-d');
						// dd($firstRepayDate,$loanOB->firstRepayDate);
					}

					$dayDiff = ($repaymentFrequencyWiseRepayDate[$repaymentFrequencyId] * $i) . 'days';
					$date = date_create($firstRepayDate);
					date_add($date, date_interval_create_from_date_string($dayDiff));


					// dd($date,$dayDiff);
					//  PROPAGATE SCHEDULE DATE FOR WEEKLY FREQUENCY.
					if ($repaymentFrequencyId == 1) :
						//  CHECK IF A DATE IS MATCHES TO HOLIDAY.
						foreach ($holiday as $key => $val) :
							if (date_create($val) >= $date) :
								if (date_create($val) == $date) :
									$holidayFound = 1;
									break;
								endif;
							endif;
						endforeach;

						if ($holidayFound == 0)
							$scheduleDateArr[] = date_format($date, "Y-m-d");

						$holidayFound = 0;
					endif;

					//  PROPAGATE SCHEDULE DATE FOR MONTHLY FREQUENCY.
					if ($repaymentFrequencyId == 2) :
						$dayDiff = ($repaymentFrequencyWiseRepayDate[$repaymentFrequencyId] * $i) . 'days';
						$date = date_create($firstRepayDate);
						date_add($date, date_interval_create_from_date_string($dayDiff));

						$disbursementDated = date_create($disbursementDate);
						date_add($disbursementDated, date_interval_create_from_date_string($dayDiff));

						$tos = Carbon::parse($firstRepayDate);
						$sot = $tos->addMonthsNoOverflow($i)->toDateString();

						if ($i == 0)
							$targetDate = date_format($date, "Y-m-d");
						else
							$targetDate = $microFinance->getMonthlyLoanScheduleDateFilter($sot, $memberId);

						$originalMD = Carbon::parse($targetDate);
						$MD = Carbon::parse($targetDate);
						$targetDate = $MD->toDateString();

						//  CHECK IF A DATE IS MATCHES TO HOLIDAY, THEN SAMITY DATE SET TO NEXT WEEK.
						for ($j = 0; $j < 100; $j++) :
							if (in_array($targetDate, $holiday)) :
								$targetDate = $MD->addDays(7)->toDateString();

								if ($targetDate > $originalMD->endOfMonth()) :
									$targetDate = $MD->subDays(14)->toDateString();
								else :
									if (in_array($targetDate, $holiday)) :
										$targetDate = $MD->addDays(7)->toDateString();

										if ($targetDate > $originalMD->endOfMonth()) :
											$targetDate = $MD->subDays(21)->toDateString();
										endif;
									else :
										break;
									endif;
								endif;
							else :
								break;
							endif;
						endfor;

						$scheduleDateArr[] = $targetDate;
					endif;

					if (count($scheduleDateArr) == $repaymentNo)
						break;
				endfor;

				$paidAmount = $openingBalances->where('loanIdFk', $loanId)->sum('paidLoanAmountOB');
				$paidAmount += $collections->where('loanIdFk', $loanId)->sum('amount');

				$curPaidAmount = $paidAmount;

				$totalPrincipal = 0;

				for ($i = 0; $i < $repaymentNo; $i++) :

					$installmentAmount = $loanOB->installmentAmount;
					$actualInstallmentAmount = $loanOB->actualInstallmentAmount;
					$extraInstallmentAmount = $loanOB->extraInstallmentAmount;

					$principalAmount = round($loanOB->installmentAmount / $loanOB->interestRateIndex, 5);
					$interestAmount = $loanOB->installmentAmount - $principalAmount;

					if ($i == $repaymentNo - 1) :
						//  CALCULATING PRINCIPAL AMOUNT AND INTEREST AMOUNT FOR LAST INSTALLMENT.

						$installmentAmount = $loanOB->totalRepayAmount - ($loanOB->installmentAmount * ($repaymentNo - 1));

						$principalAmount = $loanOB->loanAmount - $totalPrincipal;
						$interestAmount = $installmentAmount - $principalAmount;

						$actualInstallmentAmount = 0;
						$extraInstallmentAmount = 0;

					endif;

					if ($paidAmount >= $installmentAmount) {
						$isCompleted = 1;
						$isPartiallyPaid = 0;
						$partiallyPaidAmount = 0;
					} elseif ($paidAmount > 0) {
						$isCompleted = 0;
						$isPartiallyPaid = 1;
						$partiallyPaidAmount = $paidAmount;
					} else {
						$isCompleted = 0;
						$isPartiallyPaid = 0;
						$partiallyPaidAmount = 0;
					}

					DB::table('mfn_loan_schedule')->insert([
						'loanIdFk'                  => $loanId,
						'loanCode'                  => $loanOB->loanCode,
						'loanTypeId'                => $loanOB->loanTypeId,
						'installmentSl'             => $i + 1,
						'installmentAmount'         => $installmentAmount,
						'actualInstallmentAmount'   => $actualInstallmentAmount,
						'extraInstallmentAmount'    => $extraInstallmentAmount,
						'principalAmount'           => $principalAmount,
						'interestAmount'            => $interestAmount,
						'scheduleDate'              => $scheduleDateArr[$i],
						'isCompleted'               => $isCompleted,
						'isPartiallyPaid'           => $isPartiallyPaid,
						'partiallyPaidAmount'       => $partiallyPaidAmount,
						'createdDate'               => Carbon::now(),
						'status'                    => 1,
						'softDel'                   => 0,
						'ds'                        => 0
					]);

					$totalPrincipal += $principalAmount;

					$paidAmount = $paidAmount - $installmentAmount;
				endfor;

				DB::table('mfn_loan')->where('id', $loanId)->update(['lastInstallmentDate' => end($scheduleDateArr)]);
			endforeach;
			DB::commit();
		} catch (\Exception $e) {
			DB::rollback();
			dd('error');
		}


		dd('done');
	}

	public function updateLoanScheduleDates($loanId)
	{
		$dateFrom = '2019-09-10';
		$info = Microfin::updateLoanScheduleDates($loanId, $dateFrom);

		return $info;
	}

	public function getWrongIndexRateLoans($branchId = null)
	{

		$loans = DB::table('mfn_loan')
			->where('softDel', 0)
			->where('loanTypeId', 1)
			->where('branchIdFk', $branchId)
			->select('id', 'loanCode', 'productIdFk', 'repaymentNo', 'loanRepayPeriodIdFk', 'interestRateIndex')
			->get();

		$productInterestRates = DB::table('mfn_loan_product_interest_rate')->get();
		$loanRepayPeriods = DB::table('mfn_loan_repay_period')->get();

		foreach ($loans as $loan) {

			//  GET INTEREST RATE INDEX.
			$interestRateIndexOB = $productInterestRates
				->where('loanProductId', $loan->productIdFk)
				->where('installmentNum', $loan->repaymentNo)
				->where('status', 1)
				// ->select('interestCalculationMethodId', 'interestRateIndex')
				->first();

			// GET THE YEARS TO SET THE ACTUAL INTERETS RATE INDEX
			$months = $loanRepayPeriods->where('id', $loan->loanRepayPeriodIdFk)->first()->inMonths;
			$years = $months / 12;
			$inetrestRate = $interestRateIndexOB->interestRateIndex - 1;
			$inetrestRate = $inetrestRate * $years;
			$inetrestRateIndex = 1 + $inetrestRate;

			if (abs($loan->interestRateIndex - $inetrestRateIndex) >= 0.0001) {
				echo 'Loan Id: ' . $loan->id . '<br>';
				echo 'Loan Code: ' . $loan->loanCode . '<br>';
				echo 'Loan interestRateIndex: ' . $loan->interestRateIndex . '<br>';
				echo 'Actual interestRateIndex: ' . $inetrestRateIndex . '<br><br>';
			}
		}
	}

	public function getNegetiveBalaneLoans()
	{

		$branchId = 81;

		$loans = DB::table('mfn_loan')
			->where('softDel', 0)
			->where('branchIdFk', $branchId)
			->select('id', 'loanCode', 'branchIdFk', 'loanAmount', 'totalRepayAmount', 'loanCompletedDate')
			->get();

		foreach ($loans as $loan) {

			$openingBalance = DB::table('mfn_opening_balance_loan')
				->where('loanIdFk', $loan->id)
				->sum('paidLoanAmountOB');

			$collectionAmount = $openingBalance;

			$realCollectionAmount = DB::table('mfn_loan_collection')
				->where('softDel', 0)
				->where('amount', '>', 0)
				->where('loanIdFk', $loan->id)
				->sum('amount');

			$maxCollectionDate = DB::table('mfn_loan_collection')
				->where('softDel', 0)
				->where('amount', '>', 0)
				->where('loanIdFk', $loan->id)
				->max('collectionDate');

			$collectionAmount += $realCollectionAmount;

			$waiverAmount =  DB::table('mfn_loan_waivers')
				->where('softDel', 0)
				->where('loanIdFk', $loan->id)
				->sum('amount');

			$collectionAmount += $waiverAmount;

			$rebateAmount =  DB::table('mfn_loan_rebates')
				->where('softDel', 0)
				->where('loanIdFk', $loan->id)
				->sum('amount');

			$rebateDate = DB::table('mfn_loan_rebates')
				->where('loanIdFk', $loan->id)
				->max('date');

			$collectionAmount += $rebateAmount;

			$writeOffAmount =  DB::table('mfn_loan_write_off')
				->where('loanIdFk', $loan->id)
				->sum('amount');

			$collectionAmount += $writeOffAmount;

			$maxDate = max($maxCollectionDate, $rebateDate);

			if ($collectionAmount > $loan->totalRepayAmount && $loan->loanCompletedDate == '0000-00-00') {
				echo '$loan->id: ' . $loan->id . '<br>';
				echo '$loan->branchIdFk: ' . $loan->branchIdFk . '<br>';
				echo '$collectionAmount: ' . $collectionAmount . '<br>';
				echo '$loan->totalRepayAmount: ' . $loan->totalRepayAmount . '<br>';
				echo 'extraAmount: ' . ($collectionAmount - $loan->totalRepayAmount) . '<br>';
				echo '$openingBalance: ' . $openingBalance . '<br>';
				echo '$realCollectionAmount: ' . $realCollectionAmount . '<br>';
				echo '$waiverAmount: ' . $waiverAmount . '<br>';
				echo '$rebateAmount: ' . $rebateAmount . '<br>';
				echo '$writeOffAmount: ' . $writeOffAmount . '<br>';
				echo '$maxCollectionDate: ' . $maxCollectionDate . '<br>';
				echo '$rebateDate: ' . $rebateDate . '<br>';
				echo '$maxDate: ' . $maxDate . '<br><br>';
			}
		}
	}

	public function getNegetiveBalaneSavings()
	{
		$branchId = 8;

		$savingAccounts = DB::table('mfn_savings_account')
			->where('softDel', 0)
			->where('branchIdFk', $branchId)
			->select('id', 'savingsCode', 'branchIdFk')
			->get();

		$deposits = DB::table('mfn_savings_deposit')
			->where('softDel', 0)
			->where('branchIdFk', $branchId)
			->select(DB::raw('accountIdFk, SUM(amount) AS amount'))
			->groupBy('accountIdFk')
			->get();

		$withdraws = DB::table('mfn_savings_withdraw')
			->where('softDel', 0)
			->where('branchIdFk', $branchId)
			->select(DB::raw('accountIdFk, SUM(amount) AS amount'))
			->groupBy('accountIdFk')
			->get();

		foreach ($savingAccounts as $savingAccount) {
			$openingBalance = DB::table('mfn_opening_savings_account_info')->where('savingsAccIdFk', $savingAccount->id)->sum('openingBalance');
			$deposit = $deposits->where('accountIdFk', $savingAccount->id)->sum('amount');
			$withdraw = $withdraws->where('accountIdFk', $savingAccount->id)->sum('amount');

			if ($openingBalance + $deposit - $withdraw < 0) {
				echo '$savingAccount->branchIdFk: ' . $savingAccount->branchIdFk . '<br>';
				echo '$savingAccount->id: ' . $savingAccount->id . '<br>';
				echo '$savingAccount->savingsCode: ' . $savingAccount->savingsCode . '<br>';
				echo '$openingBalance: ' . $openingBalance . '<br>';
				echo '$deposit: ' . $deposit . '<br>';
				echo '$withdraw: ' . $withdraw . '<br>';
				echo 'difference: ' . abs($openingBalance + $deposit - $withdraw) . '<br><br>';
			}
		}
	}

	public function getNegetiveBalaneSavingsProductWise()
	{
		$branchId = 105;
		$date = '2019-03-31';

		$savingAccounts = DB::table('mfn_savings_account')
			->where('softDel', 0)
			->where('branchIdFk', $branchId)
			->select('id', 'savingsCode', 'branchIdFk')
			->get();

		$deposits = DB::table('mfn_savings_deposit')
			->where('softDel', 0)
			->where('branchIdFk', $branchId)
			->where('depositDate', '<=', $date)
			->select(DB::raw('accountIdFk, primaryProductIdFk, SUM(amount) AS amount'))
			->groupBy('accountIdFk', 'primaryProductIdFk')
			->get();

		$withdraws = DB::table('mfn_savings_withdraw')
			->where('softDel', 0)
			->where('branchIdFk', $branchId)
			->where('withdrawDate', '<=', $date)
			->select(DB::raw('accountIdFk, primaryProductIdFk, SUM(amount) AS amount'))
			->groupBy('accountIdFk', 'primaryProductIdFk')
			->get();

		foreach ($savingAccounts as $savingAccount) {
			$productIds = array_unique(array_merge($deposits->where('accountIdFk', $savingAccount->id)->pluck('primaryProductIdFk')->toArray(), $withdraws->where('accountIdFk', $savingAccount->id)->pluck('primaryProductIdFk')->toArray()));

			foreach ($productIds as $productId) {
				$openingBalance = DB::table('mfn_opening_savings_account_info')->where('primaryProductIdFk', $productId)->where('savingsAccIdFk', $savingAccount->id)->sum('openingBalance');
				$deposit = $deposits->where('accountIdFk', $savingAccount->id)->where('primaryProductIdFk', $productId)->sum('amount');
				$withdraw = $withdraws->where('accountIdFk', $savingAccount->id)->where('primaryProductIdFk', $productId)->sum('amount');

				if ($openingBalance + $deposit - $withdraw < 0) {
					echo '$savingAccount->branchIdFk: ' . $savingAccount->branchIdFk . '<br>';
					echo '$savingAccount->id: ' . $savingAccount->id . '<br>';
					echo '$savingAccount->savingsCode: ' . $savingAccount->savingsCode . '<br>';
					echo '$productId: ' . $productId . '<br>';
					echo '$openingBalance: ' . $openingBalance . '<br>';
					echo '$deposit: ' . $deposit . '<br>';
					echo '$withdraw: ' . $withdraw . '<br>';
					echo 'difference: ' . abs($openingBalance + $deposit - $withdraw) . '<br><br>';
				}
			}
		}
	}

	public function correctLoanNCollection()
	{

		DB::beginTransaction();

		try {
			$loans = DB::select("SELECT t1.id,t1.`loanCode`,t1.`productIdFk`,t1.`interestRateIndex`, ROUND(((((SELECT `interestRateIndex` FROM `mfn_loan_product_interest_rate` WHERE `softDel`=0 AND `status`=1 AND `loanProductId`=t1.`productIdFk` AND installmentNum=t1.`repaymentNo`) - 1) * ((SELECT `inMonths` FROM `mfn_loan_repay_period` WHERE `id`=t1.`loanRepayPeriodIdFk`)/12)) + 1),3) AS realIndex, ROUND(ABS(`interestRateIndex` - ((((SELECT `interestRateIndex` FROM `mfn_loan_product_interest_rate` WHERE `softDel`=0 AND `status`=1 AND `loanProductId`=t1.`productIdFk` AND installmentNum=t1.`repaymentNo`) - 1) * ((SELECT `inMonths` FROM `mfn_loan_repay_period` WHERE `id`=t1.`loanRepayPeriodIdFk`)/12)) + 1)),3) AS diff FROM `mfn_loan` AS t1 WHERE ABS(`interestRateIndex` - ((((SELECT `interestRateIndex` FROM `mfn_loan_product_interest_rate` WHERE `softDel`=0 AND `status`=1 AND `loanProductId`=t1.`productIdFk` AND installmentNum=t1.`repaymentNo`) - 1) * ((SELECT `inMonths` FROM `mfn_loan_repay_period` WHERE `id`=t1.`loanRepayPeriodIdFk`)/12)) + 1)) > 0.001 AND softDel=0 AND loanTypeId=1 AND t1.id NOT IN (SELECT `loanIdFk` FROM `mfn_loan_rebates`)  ORDER BY branchIdFk LIMIT 1000");

			$loans = collect($loans);

			// dd($loans);

			// UPDATE LOAN'S INDEX
			foreach ($loans as $key => $loan) {
				DB::table('mfn_loan')->where('id', $loan->id)->update(['interestRateIndex' => $loan->realIndex]);
			}

			// UPDATE COLLECTIONS

			$loanCollections = DB::table('mfn_loan_collection AS collection')
				->join('mfn_loan', 'mfn_loan.id', 'collection.loanIdFk')
				->where('collection.amount', '>', 0)
				->whereIn('mfn_loan.id', $loans->pluck('id')->toArray())
				->orderBy('mfn_loan.id')
				->select('mfn_loan.id AS loanId', 'mfn_loan.branchIdFk', 'mfn_loan.loanCode', 'mfn_loan.interestRateIndex', 'collection.id', 'collection.amount', 'collection.principalAmount', 'collection.interestAmount', 'collection.collectionDate')
				->get();

			$branchIds = $loanCollections->unique('branchIdFk')->sortBy('branchIdFk')->pluck('branchIdFk')->toArray();

			foreach ($branchIds as $key => $branchId) {
				echo '$branchId: ' . $branchId . '<br>';
				echo 'minDate: ' . $loanCollections->where('branchIdFk', $branchId)->min('collectionDate') . '<br>';
			}

			echo '<br><br><br><br><br>';

			foreach ($loanCollections as $loanCollection) {
				echo '$loanCollection->loanId: ' . $loanCollection->loanId . '<br>';
				echo '$loanCollection->loanCode: ' . $loanCollection->loanCode . '<br>';
				echo '$loanCollection->interestRateIndex: ' . $loanCollection->interestRateIndex . '<br>';

				echo '$loanCollection->collectionDate: ' . $loanCollection->collectionDate . '<br>';
				echo '$loanCollection->amount: ' . $loanCollection->amount . '<br>';
				echo '$loanCollection->principalAmount: ' . $loanCollection->principalAmount . '<br>';
				echo '$loanCollection->interestAmount: ' . $loanCollection->interestAmount . '<br>';
				$realIndex = $loans->where('id', $loanCollection->loanId)->first()->realIndex;

				$principalAmount = round($loanCollection->amount / $realIndex, 5);
				$interestAmount = round($loanCollection->amount - $principalAmount, 5);

				echo '$realIndex: ' . $realIndex . '<br>';
				echo '$principalAmount: ' . $principalAmount . '<br>';
				echo '$interestAmount: ' . $interestAmount . '<br><br>';


				DB::table('mfn_loan_collection')->where('id', $loanCollection->id)->update(['principalAmount' => $principalAmount, 'interestAmount' => $interestAmount]);

				app('App\Http\Controllers\microfin\process\RedoAutoVouchersController')->redoAutoVouchers($loanCollection->branchIdFk, $loanCollection->collectionDate);
			}

			DB::commit();
		} catch (\Exception $e) {
			DB::rollback();
			echo 'Something went wrong.';
		}

		dd('done');
	}

	public function correctNonClosingSavingsAcoount()
	{
		$branchId = 2;

		$savAccounts = DB::table('mfn_savings_account')
			->where('softDel', 0)
			->where('branchIdFk', $branchId)
			->where('closingDate', '>', '0000-00-00')
			->select('id', 'savingsCode', 'memberIdFk')
			->get();

		$deposits = DB::table('mfn_savings_deposit')
			->where('softDel', 0)
			->whereIn('accountIdFk', $savAccounts->pluck('id')->toArray())
			->select(DB::raw('accountIdFk, SUM(amount) AS amount'))
			->groupBy('accountIdFk')
			->get();

		$withdraws = DB::table('mfn_savings_withdraw')
			->where('softDel', 0)
			->whereIn('accountIdFk', $savAccounts->pluck('id')->toArray())
			->select(DB::raw('accountIdFk, SUM(amount) AS amount'))
			->groupBy('accountIdFk')
			->get();

		foreach ($savAccounts as $savAccount) {
			$openingBalance = DB::table('mfn_opening_savings_account_info')
				->where('savingsAccIdFk', $savAccount->id)
				->sum('openingBalance');

			$depositAmount = $deposits->where('accountIdFk', $savAccount->id)->sum('amount');
			$withdrawAmount = $withdraws->where('accountIdFk', $savAccount->id)->sum('amount');

			if ($openingBalance + $depositAmount - $withdrawAmount > 0) {
				echo 'Savings Id: ' . $savAccount->id . '<br>';
				echo 'Savings Code: ' . $savAccount->savingsCode . '<br>';

				$memberClosingDate = DB::table('mfn_member_information')->where('id', $savAccount->memberIdFk)->value('closingDate');

				if ($memberClosingDate == '0000-00-00') {
					echo 'Member Open<br><br>';
				} else {
					echo 'Member Closed: ' . $memberClosingDate . '<br><br>';
				}
			}
		}
	}

	public function solveMemberClosingOverDate($branchId = null)
	{
		$closedMembers = DB::table('mfn_member_information')
			->where('softDel', 0)
			->where('branchId', $branchId)
			->where('closingDate', '!=', '0000-00-00')
			->select('id', 'code', 'branchId', 'closingDate')
			->get();

		$deposits = DB::table('mfn_savings_deposit')
			->where('softDel', 0)
			->where('amount', '>', 0)
			->whereIn('memberIdFk', $closedMembers->pluck('id')->toArray())
			->groupBy('memberIdFk')
			->select(DB::raw('memberIdFk, MAX(depositDate) AS depositDate'))
			->get();

		$withdraws = DB::table('mfn_savings_withdraw')
			->where('softDel', 0)
			->where('amount', '>', 0)
			->whereIn('memberIdFk', $closedMembers->pluck('id')->toArray())
			->groupBy('memberIdFk')
			->select(DB::raw('memberIdFk, MAX(withdrawDate) AS withdrawDate'))
			->get();

		$loanCollections = DB::table('mfn_loan_collection')
			->where('softDel', 0)
			->where('amount', '>', 0)
			->whereIn('memberIdFk', $closedMembers->pluck('id')->toArray())
			->groupBy('memberIdFk')
			->select(DB::raw('memberIdFk, MAX(collectionDate) AS collectionDate'))
			->get();

		foreach ($closedMembers as $member) {
			$maxDepositDate = @$deposits->where('memberIdFk', $member->id)->first()->depositDate;
			$maxWithdrawDate = @$withdraws->where('memberIdFk', $member->id)->first()->withdrawDate;
			$maxCollectionDate = @$loanCollections->where('memberIdFk', $member->id)->first()->collectionDate;

			if ($maxDepositDate > $member->closingDate || $maxWithdrawDate > $member->closingDate || $maxCollectionDate > $member->closingDate) {
				echo '$member->id: ' . $member->id . '<br>';
				echo 'member closing date: ' . $member->closingDate . '<br>';
				echo 'depositDate: ' . $maxDepositDate . '<br>';
				echo 'withdrawDate: ' . $maxWithdrawDate . '<br>';
				echo 'collectionDate: ' . $maxCollectionDate . '<br><br>';

				// UPDATE THE MEMBER CLOSING DATE
				$maxTransactionDate = max($maxDepositDate, $maxWithdrawDate, $maxCollectionDate);
				// echo '$maxTransactionDate: '.$maxTransactionDate.'<br>';
				DB::table('mfn_member_information')->where('id', $member->id)->update(['closingDate' => $maxTransactionDate]);
				DB::table('mfn_member_closing')->where('memberIdFk', $member->id)->update(['closingDate' => $maxTransactionDate]);
			}
		}
	}

	public function solveMemberClosing($branchId = null)
	{
		$closedMembers = DB::table('mfn_member_information')
			->where('softDel', 0)
			->where('branchId', $branchId)
			->where('closingDate', '!=', '0000-00-00')
			->select('id', 'code', 'branchId', 'closingDate')
			->get();

		$deposits = DB::table('mfn_savings_deposit')
			->where('softDel', 0)
			->whereIn('memberIdFk', $closedMembers->pluck('id')->toArray())
			->groupBy('memberIdFk')
			// ->groupBy('accountIdFk')
			->select(DB::raw('memberIdFk, accountIdFk, SUM(amount) AS amount'))
			->get();

		$withdraws = DB::table('mfn_savings_withdraw')
			->where('softDel', 0)
			->whereIn('memberIdFk', $closedMembers->pluck('id')->toArray())
			->groupBy('memberIdFk')
			// ->groupBy('accountIdFk')
			->select(DB::raw('memberIdFk, accountIdFk, SUM(amount) AS amount'))
			->get();

		$info = collect([]);

		foreach ($closedMembers as $member) {

			// CALCULATE SAVINGS BALANCE
			$openingBalance = DB::table('mfn_opening_savings_account_info')
				->where('memberIdFk', $member->id)
				->sum('openingBalance');

			$depositAmount = $deposits->where('memberIdFk', $member->id)->sum('amount');
			$withdrawAmount = $withdraws->where('memberIdFk', $member->id)->sum('amount');

			$balace = $openingBalance + $depositAmount - $withdrawAmount;

			if ($balace > 0) {
				echo $member->code . '<br>';
				echo 'savings balace: ' . $balace . '<br><br>';

				// IF SAVINGS BALACE EXISTS THEN DELETE THE MEMBER CLOSING.
				DB::table('mfn_member_information')->where('id', $member->id)->update(['status' => 1, 'closingDate' => '0000-00-00']);
				DB::table('mfn_member_closing')->where('memberIdFk', $member->id)->update(['softDel' => 1]);
				$info->push([
					'branchId' => $member->branchId,
					'closingDate' => $member->closingDate
				]);
				continue;
			}

			// IF LOAN IS RUNNIG THEN REMOVE THE CLOSING INFORMATION
			$loanIds = DB::table('mfn_loan')
				->where('softDel', 0)
				->where('memberIdFk', $member->id)
				->pluck('id')
				->toArray();

			if (count($loanIds) > 0) {
				$loanIfos = $this->getLoanOutstanding($loanIds);

				if ($loanIfos->sum('loanBalance') > 5) {
					echo $member->code . '<br>';
					echo 'loan balace: ' . $loanIfos->sum('loanBalance') . '<br><br>';
					// IF LOAN BALACE EXISTS THEN DELETE THE MEMBER CLOSING.
					DB::table('mfn_member_information')->where('id', $member->id)->update(['status' => 1, 'closingDate' => '0000-00-00']);
					DB::table('mfn_member_closing')->where('memberIdFk', $member->id)->update(['softDel' => 1]);
					$info->push([
						'branchId' => $member->branchId,
						'closingDate' => $member->closingDate
					]);
					continue;
				}
			}
		}

		$branchIds = $info->unique('branchId')->pluck('branchId')->toArray();

		foreach ($branchIds as $branchId) {
			echo 'branchId: ' . $branchId . '<br>';
			echo 'minClosingDate: ' . $info->where('branchId', $branchId)->min('closingDate') . '<br>';
		}
		dd();
	}

	public function solveMemberClosingOld($branchId = null)
	{

		$multipleClosingMemberIds = DB::select("SELECT mfn_member_information.id FROM `mfn_member_information`
            JOIN mfn_member_closing ON mfn_member_closing.memberIdFk = mfn_member_information.id
            WHERE mfn_member_information.softDel=0 AND mfn_member_closing.softDel=0 AND mfn_member_information.closingDate='0000-00-00'
            GROUP BY mfn_member_information.id HAVING COUNT(mfn_member_information.id) > 1");

		$multipleClosingMemberIds = collect($multipleClosingMemberIds);
		$multipleClosingMemberIds = $multipleClosingMemberIds->pluck('id')->toArray();

		$closedMembers = DB::table('mfn_member_information AS mi')
			->join('mfn_member_closing AS mcl', 'mcl.memberIdFk', 'mi.id')
			->where([['mi.softDel', 0], ['mcl.softDel', 0], ['mi.closingDate', '0000-00-00']])
			// ->where('mi.branchId',$branchId)
			->where('mi.branchId', '<=', $branchId)
			->whereNotIn('mi.id', $multipleClosingMemberIds)
			->orderBy('mi.branchId')
			// ->limit('50')
			->select('mi.id', 'mi.code', 'mi.branchId', 'mcl.closingDate')
			->get();

		$deposits = DB::table('mfn_savings_deposit')
			->where('softDel', 0)
			->whereIn('memberIdFk', $closedMembers->pluck('id')->toArray())
			->groupBy('memberIdFk')
			// ->groupBy('accountIdFk')
			->select(DB::raw('memberIdFk, accountIdFk, SUM(amount) AS amount'))
			->get();

		$withdraws = DB::table('mfn_savings_withdraw')
			->where('softDel', 0)
			->whereIn('memberIdFk', $closedMembers->pluck('id')->toArray())
			->groupBy('memberIdFk')
			// ->groupBy('accountIdFk')
			->select(DB::raw('memberIdFk, accountIdFk, SUM(amount) AS amount'))
			->get();



		$info = collect([]);

		foreach ($closedMembers as $key => $member) {

			// echo $member->code.'<br>';

			// CALCULATE SAVINGS BALANCE
			$openingBalance = DB::table('mfn_opening_savings_account_info')
				->where('memberIdFk', $member->id)
				->sum('openingBalance');

			$depositAmount = $deposits->where('memberIdFk', $member->id)->sum('amount');
			$withdrawAmount = $withdraws->where('memberIdFk', $member->id)->sum('amount');

			$balace = $openingBalance + $depositAmount - $withdrawAmount;

			if ($balace == 0) {
				$balanceExists = 0;
				// echo 'Savings balace does not Exists<br><br>';
			} else {
				$balanceExists = 1;
				// echo $member->code.'<br>';
				// echo '$balanceExists<br>';
				// echo '$balace: '.$balace.'<br>';

				// member closing information should be removed.
				// DB::table('mfn_member_closing')->where('memberIdFk',$member->id)->update(['softDel'=>1]);
				// DB::table('mfn_savings_withdraw')->where('memberIdFk',$member->id)->where('isFromClosing',1)->where('withdrawDate',$member->closingDate)->update(['isFromClosing'=>0]);
			}

			$loanIds = DB::table('mfn_loan')
				->where('softDel', 0)
				->where('memberIdFk', $member->id)
				->pluck('id')
				->toArray();

			/*if (count($loanIds)==0) {
                echo $member->code.'<br>';
                echo 'No Loan Exists<br><br>';
                continue;
            }*/

			if (count($loanIds) == 0 && $balanceExists == 0) {
				echo $member->code . '<br>';
				// echo 'No Loan Exists and no savings balance.<br><br>';
				$maxDepositDate = DB::table('mfn_savings_deposit')->where('softDel', 0)->where('amount', '>', 0)->where('memberIdFk', $member->id)->max('depositDate');
				$maxWithdrawDate = DB::table('mfn_savings_withdraw')->where('softDel', 0)->where('memberIdFk', $member->id)->max('withdrawDate');
				/*if ($maxDepositDate>$member->closingDate) {
                    echo 'Deposit Exits<br>';
                }
                if ($maxWithdrawDate>$member->closingDate) {
                    echo 'Withdraw Exits<br>';
                }*/

				if ($maxDepositDate <= $member->closingDate && $maxWithdrawDate <= $member->closingDate) {
					echo 'No Loan Exists and no savings balance.<br><br>';
					DB::table('mfn_member_information')->where('id', $member->id)->update(['status' => 0, 'closingDate' => $member->closingDate]);
					$info->push([
						'branchId' => $member->branchId,
						'closingDate' => $member->closingDate,
					]);
				}


				continue;
			}

			continue;
			$this->getLoanOutstanding($loanIds);


			$memberClosingDate = $member->closingDate;

			$loanExists = DB::table('mfn_loan')
				->where('softDel', 0)
				->where('memberIdFk', $member->id)
				->where(function ($query) use ($memberClosingDate) {
					$query->where('loanCompletedDate', '0000-00-00')
						->orWhere('loanCompletedDate', '>', $memberClosingDate);
				})
				->count();

			if ($loanExists > 0) {
				echo '$loanExists<br>';
			}

			$loanCollectionExists = DB::table('mfn_loan_collection')
				->where('softDel', 0)
				->where('memberIdFk', $member->id)
				->where('amount', '>', 0)
				->where('collectionDate', '>', $memberClosingDate)
				->count();

			if ($loanCollectionExists > 0) {
				echo '$loanCollectionExists<br>';
			}

			$savAccExists = DB::table('mfn_savings_account')
				->where('softDel', 0)
				->where('memberIdFk', $member->id)
				->where(function ($query) use ($memberClosingDate) {
					$query->where('closingDate', '0000-00-00')
						->orWhere('closingDate', '>', $memberClosingDate);
				})
				->count();

			if ($savAccExists > 0) {
				echo '$savAccExists<br>';
			}

			$savDepositExists = DB::table('mfn_savings_deposit')
				->where('softDel', 0)
				->where('memberIdFk', $member->id)
				->where('amount', '>', 0)
				->where('depositDate', '>', $memberClosingDate)
				->count();

			if ($savDepositExists > 0) {
				echo '$savDepositExists<br>';
			}

			$savWithdrawExists = DB::table('mfn_savings_withdraw')
				->where('softDel', 0)
				->where('memberIdFk', $member->id)
				->where('withdrawDate', '>', $memberClosingDate)
				->count();

			if ($savWithdrawExists > 0) {
				echo '$savWithdrawExists<br>';
			}

			// CALCULATE SAVINGS BALANCE
			$openingBalance = DB::table('mfn_opening_savings_account_info')
				->where('memberIdFk', $member->id)
				->sum('openingBalance');

			$depositAmount = $deposits->where('memberIdFk', $member->id)->sum('amount');
			$withdrawAmount = $withdraws->where('memberIdFk', $member->id)->sum('amount');

			$balace = $openingBalance + $depositAmount - $withdrawAmount;

			if ($balace == 0) {
				$balanceExists = 0;
			} else {
				$balanceExists = 1;
				echo '$balanceExists<br>';

				// member closing information should be removed.
			}

			if ($loanExists + $loanCollectionExists + $savAccExists + $savDepositExists + $savWithdrawExists + $balanceExists == 0) {
				echo 'No Problem, You can Close the member on ' . $member->closingDate . '<br>';
				$info->push([
					'memberId'      => $member->id,
					'branchId'      => $member->branchId,
					'closingDate'   => $member->closingDate
				]);
				DB::table('mfn_member_information')->where('id', $member->id)->update(['status' => 0, 'closingDate' => $member->closingDate]);
			}

			echo '<br><br>';
		}

		$branchIds = $info->unique('branchId')->pluck('branchId')->toArray();

		foreach ($branchIds as $branchId) {
			echo 'branchId: ' . $branchId . '<br>';
			echo 'minClosingDate: ' . $info->where('branchId', $branchId)->min('closingDate') . '<br>';
		}

		dd();
	}

	public function getLoanOutstanding($loanIds)
	{

		$loanCollections = DB::table('mfn_loan_collection')
			->where('softDel', 0)
			->whereIn('loanIdFk', $loanIds)
			->groupBy('loanIdFk')
			->select(DB::raw('loanIdFk, SUM(amount) AS amount, SUM(principalAmount) AS principalAmount'))
			->get();

		$loans = DB::table('mfn_loan')->whereIn('id', $loanIds)->select('id', 'loanCode', 'loanAmount', 'totalRepayAmount', 'loanCompletedDate')->get();

		$info = collect([]);

		foreach ($loanIds as $loanId) {
			$collectionAmount = 0;
			$collectionAmountPrincipal = 0;

			$openingBalance = DB::table('mfn_opening_balance_loan')->where('softDel', 0)->where('loanIdFk', $loanId)->first();
			if ($openingBalance != null) {
				$collectionAmount += $openingBalance->paidLoanAmountOB;
				$collectionAmountPrincipal += $openingBalance->principalAmountOB;
			}

			$collectionAmount += $loanCollections->where('loanIdFk', $loanId)->sum('amount');
			$collectionAmountPrincipal += $loanCollections->where('loanIdFk', $loanId)->sum('principalAmount');

			$waiver = DB::table('mfn_loan_waivers')->where('softDel', 0)->where('loanIdFk', $loanId)->first();
			if ($waiver != null) {
				$collectionAmount += $waiver->principalAmount;
				$collectionAmountPrincipal += $waiver->principalAmount;
			}

			$rebate = DB::table('mfn_loan_rebates')->where('softDel', 0)->where('loanIdFk', $loanId)->first();
			if ($rebate != null) {
				$collectionAmount += $rebate->amount;
			}

			$writeOff = DB::table('mfn_loan_write_off')->where('softDel', 0)->where('loanIdFk', $loanId)->first();
			if ($writeOff != null) {
				$collectionAmount += $writeOff->amount;
				$collectionAmountPrincipal += $writeOff->principalAmount;
			}

			$loanBalance = $loans->where('id', $loanId)->sum('totalRepayAmount') - $collectionAmount;
			$loanBalancePricipal = $loans->where('id', $loanId)->sum('loanAmount') - $collectionAmountPrincipal;

			$info->push([
				'loanId'    => $loanId,
				'loanCode'    => $loans->where('id', $loanId)->first()->loanCode,
				'loanBalance'    => $loanBalance,
				'loanBalancePricipal'    => $loanBalancePricipal
			]);

			// echo 'loanId: '.$loanId.'<br>';
			// echo 'loanCode: '.$loans->where('id',$loanId)->first()->loanCode.'<br>';
			// echo 'loanCompletedDate: '.$loans->where('id',$loanId)->first()->loanCompletedDate.'<br>';
			// echo 'loanBalance: '.$loanBalance.'<br>';
			// echo 'loanBalancePricipal: '.$loanBalancePricipal.'<br><br>';

			return $info;
		}
	}


	/**
	 * this function is done by Atiq
	 * @return [void]
	 */
	public function abc()
	{
		// branch id minimum is 2 and maximum is 83
		$branchStart = 2;
		$branchEnd   = 10;
		$getAllBranchIds = DB::table('gnr_branch')
			->where('id', '>=', $branchStart)
			->where('id', '<=', $branchEnd)
			->pluck('id')
			->toArray();

		$allProCatIds    = DB::table('mfn_loans_product_category')
			->pluck('id')
			->toArray();

		$allBranchCumulativeOpeningLoanAmount = array();
		$branchWiseSamitIds                   = array();
		$allkindOfInfos                       = array();
		$dataMigrationDate                    = '2018-10-31';

		foreach ($getAllBranchIds as $key => $getAllBranchId) {
			$allBranchCumulativeOpeningLoanAmount[$getAllBranchId] = DB::table('mfn_opening_info_loan')
				->where('branchIdFk', $getAllBranchId)
				->sum('disbursedAmount');
		}

		foreach ($allBranchCumulativeOpeningLoanAmount as $branchId => $singleBranchCumulativeOpeningLoanAmount) {
			$distinctSamityId = DB::table('mfn_samity')
				->where([['openingDate', '<=', $dataMigrationDate]])
				->where(function ($query) use ($dataMigrationDate) {
					$query->where([['softDel', '=', 0], ['closingDate', '>=', $dataMigrationDate]])
						->orWhere([['softDel', '=', 0], ['closingDate', '=', '0000-00-00']])
						->orWhere([['softDel', '=', 0], ['closingDate', '=', null]]);
				})
				->where('branchId', $branchId)
				->groupBy('id')
				->pluck('id')
				->toArray();

			$toalMultiplicationNumber      = sizeof($distinctSamityId) * sizeof($allProCatIds);
			$dividedValuesForCumLoanAmount = $singleBranchCumulativeOpeningLoanAmount / $toalMultiplicationNumber;

			foreach ($distinctSamityId as $key => $samityId) {
				foreach ($allProCatIds as $key => $proCateId) {
					$allkindOfInfos[$branchId . '-' . $samityId . '-' . $proCateId] = $dividedValuesForCumLoanAmount;
				}
			}
		}

		foreach ($allkindOfInfos as $branchSamityProCatIds => $allkindOfInfo) {
			list($uniqueBranchId, $uniqueSamityId, $uniqueProCatId) = explode('-', $branchSamityProCatIds);

			DB::table('mfn_opening_loan_info_samity_wise')
				->insert(
					[
						'branchIdFk'    => $uniqueBranchId,
						'samityIdFk'    => $uniqueSamityId,
						'proCatIdFk'    => $uniqueProCatId,
						'cumLoanAmount' => $allkindOfInfo
					]
				);
		}

		dd('Success!!', 'Total data :', sizeof($allkindOfInfos), 'Branch Started From :', $branchStart, 'Branch Ended At :', $branchEnd);
	}

	public function updateCollection()
	{

		DB::beginTransaction();

		try {

			$loanIds = [];

			$loanCollections = DB::table('mfn_loan_collection AS collection')
				->join('mfn_loan', 'mfn_loan.id', 'collection.loanIdFk')
				->where('collection.amount', '>', 0)
				->whereIn('mfn_loan.id', $loanIds)
				->orderBy('mfn_loan.id')
				->select('mfn_loan.id AS loanId', 'mfn_loan.branchIdFk', 'mfn_loan.loanCode', 'mfn_loan.interestRateIndex', 'collection.id', 'collection.amount', 'collection.principalAmount', 'collection.interestAmount', 'collection.collectionDate')
				->get();

			$branchIds = $loanCollections->unique('branchIdFk')->sortBy('branchIdFk')->pluck('branchIdFk')->toArray();

			foreach ($branchIds as $key => $branchId) {
				echo '$branchId: ' . $branchId . '<br>';
				echo 'minDate: ' . $loanCollections->where('branchIdFk', $branchId)->min('collectionDate') . '<br>';
			}

			echo '<br><br><br><br><br>';

			foreach ($loanCollections as $loanCollection) {
				echo '$loanCollection->loanId: ' . $loanCollection->loanId . '<br>';
				echo '$loanCollection->loanCode: ' . $loanCollection->loanCode . '<br>';
				echo '$loanCollection->interestRateIndex: ' . $loanCollection->interestRateIndex . '<br>';

				echo '$loanCollection->collectionDate: ' . $loanCollection->collectionDate . '<br>';
				echo '$loanCollection->amount: ' . $loanCollection->amount . '<br>';
				echo '$loanCollection->principalAmount: ' . $loanCollection->principalAmount . '<br>';
				echo '$loanCollection->interestAmount: ' . $loanCollection->interestAmount . '<br>';

				$principalAmount = round($loanCollection->amount / $loanCollection->interestRateIndex, 5);
				$interestAmount = round($loanCollection->amount - $principalAmount, 5);

				echo 'updated principalAmount: ' . $principalAmount . '<br>';
				echo 'updated interestAmount: ' . $interestAmount . '<br><br>';


				DB::table('mfn_loan_collection')->where('id', $loanCollection->id)->update(['principalAmount' => $principalAmount, 'interestAmount' => $interestAmount]);

				app('App\Http\Controllers\microfin\process\RedoAutoVouchersController')->redoAutoVouchers($loanCollection->branchIdFk, $loanCollection->collectionDate);
			}

			DB::commit();
		} catch (\Exception $e) {
			DB::rollback();
			echo 'Something went wrong.';
		}

		dd('done');
	}

	public function getLoanWhichHaveAScheduleGap($branchId = null)
	{

		$loanSchedules = DB::table('mfn_loan_schedule AS ls')
			->join('mfn_loan AS loan', 'loan.id', 'ls.loanIdFk')
			->where([['loan.softDel', 0], ['ls.softDel', 0], ['loan.branchIdFk', $branchId]])
			->select('ls.loanIdFk', 'ls.installmentSl', 'scheduleDate')
			->get();

		$loanIds = $loanSchedules->unique('loanIdFk')->pluck('loanIdFk')->toArray();

		foreach ($loanIds as $loanId) {
			$thisLoanSchedules = $loanSchedules->where('loanIdFk', $loanId);

			foreach ($thisLoanSchedules as $schedule) {
				$nextScheduleDate = $thisLoanSchedules->where('installmentSl', $schedule + 1)->max('scheduleDate');

				if ($nextScheduleDate != null) {
					$currentScheduleDateOb = Carbon::parse($schedule->scheduleDate);
					$nextScheduleDateOb = Carbon::parse($nextScheduleDate);

					if ($nextScheduleDateOb->diffInMonths($currentScheduleDateOb) > 1) {
						echo '$loanId: ' . $loanId . '<br>';
						echo '$currentScheduleDate: ' . $currentScheduleDateOb->format('Y-m-d') . '<br>';
						echo '$nextScheduleDate: ' . $nextScheduleDateOb->format('Y-m-d') . '<br><br>';
					}
				}
			}
		}
	}

	public function fixAutoProcessCollection($branchId = null)
	{
		$branchDate = MicroFin::getSoftwareDateBranchWise($branchId);

		$schedules = DB::table('mfn_loan_schedule AS ls')
			->join('mfn_loan AS loan', 'loan.id', 'ls.loanIdFk')
			->where([['loan.softDel', 0], ['ls.softDel', 0], ['loan.branchIdFk', $branchId]])
			->where('ls.scheduleDate', '>=', '2019-04-01')
			->where('ls.scheduleDate', '<=', $branchDate)
			->orderBy('loan.id')
			->select('ls.loanIdFk', 'ls.scheduleDate', 'loan.productIdFk', 'loan.primaryProductIdFk', 'loan.loanTypeId', 'loan.memberIdFk', 'loan.branchIdFk', 'loan.samityIdFk', 'loan.entryBy')
			->get();

		foreach ($schedules as $key => $schedule) {
			$collection = DB::table('mfn_loan_collection')
				->where('softDel', 0)
				->where('loanIdFk', $schedule->loanIdFk)
				->where('collectionDate', $schedule->scheduleDate)
				->select('id', 'isFromAutoProcess')
				->first();

			if ($collection == null) {
				echo 'Loan ID: ' . $schedule->loanIdFk . '<br>';
				echo 'date: ' . $schedule->scheduleDate . '<br>';
				echo 'No collection exists.<br><br>';
				DB::table('mfn_loan_collection')->insert([
					'loanIdFk' => $schedule->loanIdFk,
					'productIdFk' => $schedule->productIdFk,
					'primaryProductIdFk' => $schedule->primaryProductIdFk,
					'loanTypeId' => $schedule->loanTypeId,
					'memberIdFk' => $schedule->memberIdFk,
					'branchIdFk' => $schedule->branchIdFk,
					'samityIdFk' => $schedule->samityIdFk,
					'collectionDate' => $schedule->scheduleDate,
					'amount' => 0,
					'principalAmount' => 0,
					'interestAmount' => 0,
					'paymentType' => 'Cash',
					'ledgerIdFk' => 412,

					'isFromAutoProcess' => 1,
					'isAuthorized' => 1,
					'entryByEmployeeIdFk' => $schedule->entryBy,
					'createdAt' => Carbon::now()
				]);
			} elseif ($collection->isFromAutoProcess == 0) {
				echo 'Loan ID: ' . $schedule->loanIdFk . '<br>';
				echo 'date: ' . $schedule->scheduleDate . '<br>';
				echo 'Regular Collection exists. Collection ID: ' . $collection->id . '<br><br>';
				DB::table('mfn_loan_collection')->where('id', $collection->id)->update(['isFromAutoProcess' => 1]);
			}
		}

		dd('finished');
	}

	public function fixPrimaryProductTransfer($branchId = null)
	{

		// GET THE CURRENT PRIMARY PRODUCT LOANS
		$loans = DB::table('mfn_loan AS loan')
			->join('mfn_member_information AS member', 'member.id', 'loan.memberIdFk')
			->where('loan.softDel', 0)
			->where('loan.loanCompletedDate', '0000-00-00')
			->where('loan.branchIdFk', $branchId)
			->where('member.primaryProductId', '!=', DB::raw('loan.productIdFk'))
			->where('loan.productIdFk', DB::raw('loan.primaryProductIdFk'))
			->select('loan.id', 'loan.loanCode', 'loan.productIdFk', 'member.primaryProductId', 'loan.memberIdFk', 'loan.disbursementDate')
			->get();

		$allMemberIds = $loans->pluck('memberIdFk')->toArray();
		$uniqueMemberIds = $loans->unique('memberIdFk')->pluck('memberIdFk')->toArray();

		if (count($allMemberIds) != count($uniqueMemberIds)) {
			dd('Duplicate Member Exists.');
		}

		foreach ($loans as $loan) {
			$lastTransfer = DB::table('mfn_loan_primary_product_transfer')
				->where('softDel', 0)
				->where('memberIdFk', $loan->memberIdFk)
				->orderBy('transferDate', 'desc')
				->limit(1)
				->first();

			echo '$loan->id: ' . $loan->id . '<br>';
			echo '$loan->loanCode: ' . $loan->loanCode . '<br>';
			echo '$loan->memberIdFk: ' . $loan->memberIdFk . '<br>';
			echo '$loan->productIdFk: ' . $loan->productIdFk . '<br>';
			echo '$loan->primaryProductId: ' . $loan->primaryProductId . '<br>';

			if ($lastTransfer == null) {
				echo 'Transfer does not exists.<br><br>';
			} elseif ($lastTransfer->transferDate <= $loan->disbursementDate && $lastTransfer->newPrimaryProductFk == $loan->productIdFk) {
				echo 'Transfer date:' . $lastTransfer->transferDate . '<br>';
				echo 'Transfer is valid.<br><br>';

				// UPDATE MEMBER'S PRIMARY PRODUCT ID
				// DB::table('mfn_member_information')
				// ->where('id',$loan->memberIdFk)
				// ->update(['primaryProductId'=>$lastTransfer->newPrimaryProductFk]);

				// UPDATE RUNNING OPTIONAL LOAN'S PRIMARY PRODUCT
				// DB::table('mfn_loan')
				// ->where('softDel',0)
				// ->where('memberIdFk',$loan->memberIdFk)
				// ->where('loanCompletedDate','0000-00-00')
				// ->where('productIdFk','!=',DB::raw('primaryProductIdFk'))
				// ->update(['primaryProductIdFk'=>$lastTransfer->newPrimaryProductFk]);

				// update the savings deposit's and withdraw's primary product transfer

				// number of savings deposit and withdraw
				$deposit = DB::table('mfn_savings_deposit')
					->where('softDel', 0)
					->where('memberIdFk', $loan->memberIdFk)
					->where('amount', '>', 0)
					->where('primaryProductIdFk', '!=', $lastTransfer->newPrimaryProductFk)
					->select(DB::raw('SUM(amount) AS amount, COUNT(id) AS numOfDeposit'))
					->get();

				$withdraw = DB::table('mfn_savings_deposit')
					->where('softDel', 0)
					->where('memberIdFk', $loan->memberIdFk)
					->where('amount', '>', 0)
					->select(DB::raw('SUM(amount) AS amount, COUNT(id) AS numOfDeposit'))
					->get();
			} else {
				echo 'Transfer is invalid.<br><br>';
			}
		}
	}

	public function fixPrimaryProductTransferTwo($branchId = null)
	{

		if ($branchId == null) {
			dd('Enter branch id');
		}

		// $primaryProductIds = DB::table('mfn_loans_product')->where('isPrimaryProduct', 1)->pluck('id')->toArray();


		DB::beginTransaction();
		try {
			$transfers = DB::table('mfn_loan_primary_product_transfer')
				->where('softDel', 0)
				->where('branchIdFk', $branchId)
				->orderBy('transferDate')
				->get();

			$memberIds = $transfers->unique('memberIdFk')->pluck('memberIdFk')->toArray();

			foreach ($memberIds as $memberId) {
				$thisMemberTranfers = $transfers->where('memberIdFk', $memberId)->sortBy('transferDate')->values();

				foreach ($thisMemberTranfers as $key => $transfer) {
					$previousTransfer = $thisMemberTranfers->where('memberIdFk', $memberId)->where('transferDate', '<', $transfer->transferDate)->first();

					//////// SAVINGS ////////

					$deposits = DB::table('mfn_savings_deposit')
						->where('softDel', 0)
						->where('amount', '>', 0)
						->where('isTransferred', 0)
						->where('memberIdFk', $transfer->memberIdFk)
						->where('depositDate', '<=', $transfer->transferDate)
						->where('primaryProductIdFk', '!=', $transfer->oldPrimaryProductFk);

					if ($previousTransfer != null) {
						$deposits = $deposits->where('depositDate', '>', $previousTransfer->transferDate);
					}

					$deposits = $deposits->select('id', 'accountIdFk', 'memberIdFk', 'depositDate', 'amount')->get();
					// $deposits = $deposits->update(['primaryProductIdFk' => $transfer->oldPrimaryProductFk]);



					foreach ($deposits as $deposit) {

						DB::table('mfn_savings_deposit')->where('id', $deposit->id)->update(['primaryProductIdFk' => $transfer->oldPrimaryProductFk]);
					}

					$withdraws = DB::table('mfn_savings_withdraw')
						->where('softDel', 0)
						->where('amount', '>', 0)
						->where('isTransferred', 0)
						->where('memberIdFk', $transfer->memberIdFk)
						->where('withdrawDate', '<=', $transfer->transferDate)
						->where('primaryProductIdFk', '!=', $transfer->oldPrimaryProductFk);

					if ($previousTransfer != null) {
						$withdraws = $withdraws->where('withdrawDate', '>', $previousTransfer->transferDate);
					}

					$withdraws = $withdraws->select('id', 'accountIdFk', 'memberIdFk', 'withdrawDate', 'amount')->get();
					// $withdraws = $withdraws->update(['primaryProductIdFk' => $transfer->oldPrimaryProductFk]);

					foreach ($withdraws as $withdraw) {

						DB::table('mfn_savings_withdraw')->where('id', $withdraw->id)->update(['primaryProductIdFk' => $transfer->oldPrimaryProductFk]);
					}
					//////// END SAVINGS ////////


					if (count($deposits) + count($withdraws) > 0) {

						$this->updateTransfer($branchId, $transfer->transferDate);

						$dates = array_merge($deposits->unique('depositDate')->pluck('depositDate')->toArray(), $withdraws->unique('depositDate')->pluck('withdrawDate')->toArray(), [$transfer->transferDate]);

						foreach ($dates as $date) {
							app('App\Http\Controllers\microfin\process\RedoAutoVouchersController')->redoAutoVouchers($branchId, $transfer->transferDate);
							app('App\Http\Controllers\microfin\process\UpdateDayEndInfoController')->index($branchId, $transfer->transferDate);
							echo 'Date: ' . $date . '<br>';
						}
					}

					DB::commit();
				}
			}
		} catch (\Exception $e) {
			DB::rollback();
			echo $e->getMessage();
		}
	}

	public function updateTransfer($targetBranchId, $softwareDate)
	{
		DB::table('mfn_savings_deposit')->where('branchIdFk', $targetBranchId)->where('depositDate', $softwareDate)->where('isTransferred', 1)->delete();
		DB::table('mfn_savings_withdraw')->where('branchIdFk', $targetBranchId)->where('withdrawDate', $softwareDate)->where('isTransferred', 1)->delete();

		$transfers = DB::table('mfn_loan_primary_product_transfer')
			->where('softDel', 0)
			->where('branchIdFk', $targetBranchId)
			->where('transferDate', $softwareDate)
			->orderBy('transferDate')
			->get();

		$savOpeningBalances = DB::table('mfn_opening_savings_account_info')
			->where('softDel', 0)
			->whereIn('memberIdFk', $transfers->pluck('memberIdFk'))
			->get();

		// transfer Date is now assumed a day before because it is assumed date the deposits and withdraws on the tranfer date belongs to the new product
		//$transferDate = Carbon::parse($softwareDate)->format("Y-m-d");

		foreach ($transfers as  $transfer) {
			$memberId = $transfer->memberIdFk;

			$savingsAccount = DB::table('mfn_savings_account')->where('softDel', 0)->where('memberIdFk', $memberId)->where('accountOpeningDate', '<=', $softwareDate)->select('id', 'savingsProductIdFk', 'branchIdFk', 'samityIdFk')->get();

			$i = 0;
			$savingsSummary = [];
			$totalTransferAmount = 0;

			//    GET ALL THE DETAILS OF ALL THE SAVINGS ACCOUNT OF A MEMBER.
			foreach ($savingsAccount as $savingsAcc) :

				$savingsSummary[$i]['id'] = $savingsAcc->id;
				$savingsSummary[$i]['savingsProductId'] = $savingsAcc->savingsProductIdFk;

				/*$savingsSummary[$i]['deposit'] = DB::table('mfn_savings_deposit')->where('softDel',0)->where('accountIdFk', $savingsAcc->id)->where('ledgerIdFk','!=',0)->where('primaryProductIdFk',$transfer->oldPrimaryProductFk)->where('depositDate','<=',$softwareDate)->sum('amount');*/

				$savingsSummary[$i]['deposit'] = DB::table('mfn_savings_deposit')->where('softDel', 0)->where('accountIdFk', $savingsAcc->id)/* ->where('ledgerIdFk', '!=', 0) */->where('primaryProductIdFk', $transfer->oldPrimaryProductFk)->where('depositDate', '<=', $transfer->transferDate)->sum('amount');
				$savingsSummary[$i]['deposit'] += $savOpeningBalances->where('savingsAccIdFk', $savingsAcc->id)->where('primaryProductIdFk', $transfer->oldPrimaryProductFk)->sum('openingBalance');

				/*$savingsSummary[$i]['withdraw'] = DB::table('mfn_savings_withdraw')->where('softDel',0)->where('accountIdFk', $savingsAcc->id)->where('ledgerIdFk','!=',0)->where('primaryProductIdFk',$transfer->oldPrimaryProductFk)->where('withdrawDate','<=',$softwareDate)->sum('amount');*/

				$savingsSummary[$i]['withdraw'] = DB::table('mfn_savings_withdraw')->where('softDel', 0)->where('accountIdFk', $savingsAcc->id)/* ->where('ledgerIdFk', '!=', 0) */->where('primaryProductIdFk', $transfer->oldPrimaryProductFk)->where('withdrawDate', '<=', $transfer->transferDate)->sum('amount');

				$balance = $savingsSummary[$i]['deposit'] - $savingsSummary[$i]['withdraw'];

				$savingsSummary[$i]['balance'] = $savingsSummary[$i]['deposit'] - $savingsSummary[$i]['withdraw'];
				$totalTransferAmount +=  $savingsSummary[$i]['balance'];
				$i++;

				$deposit = new MfnSavingsDeposit;
				$deposit->accountIdFk           =  $savingsAcc->id;
				$deposit->productIdFk           =  $savingsAcc->savingsProductIdFk;
				$deposit->primaryProductIdFk    =  $transfer->newPrimaryProductFk;
				$deposit->memberIdFk            =  $transfer->memberIdFk;
				$deposit->branchIdFk            =  $savingsAcc->branchIdFk;
				$deposit->samityIdFk            =  $savingsAcc->samityIdFk;
				$deposit->amount                =  $balance;
				$deposit->depositDate           =  Carbon::parse($softwareDate);
				$deposit->entryByEmployeeIdFk   =  Auth::user()->emp_id_fk;
				$deposit->isAuthorized          =  1;
				$deposit->isTransferred         =  1;
				$deposit->createdAt             =  Carbon::now();
				$deposit->save();

				$withdraw = new MfnSavingsWithdraw;
				$withdraw->accountIdFk          =  $savingsAcc->id;
				$withdraw->productIdFk          =  $savingsAcc->savingsProductIdFk;
				$withdraw->primaryProductIdFk   =  $transfer->oldPrimaryProductFk;
				$withdraw->memberIdFk           =  $transfer->memberIdFk;
				$withdraw->branchIdFk           =  $savingsAcc->branchIdFk;
				$withdraw->samityIdFk           =  $savingsAcc->samityIdFk;
				$withdraw->amount               =  $balance;
				$withdraw->withdrawDate         =  Carbon::parse($softwareDate);
				$withdraw->entryByEmployeeIdFk  =  Auth::user()->emp_id_fk;
				$withdraw->isAuthorized         =  1;
				$withdraw->isTransferred        =  1;
				$withdraw->createdAt            =  Carbon::now();
				$withdraw->save();
			endforeach;

			$obj = MfnMemberPrimaryProductTransfer::find($transfer->id);
			$obj->totalTransferAmount = $totalTransferAmount;
			$obj->savingsRecord = $savingsSummary;
			$obj->save();
		}
	}


	public function getLog()
	{
		$log = DB::table('log')->where('id', 8)->first();
		echo '<pre>';
		print_r(json_decode($log->previous_data));
		echo '</pre>';
		echo '<pre>';
		print_r(json_decode($log->current_data));
		echo '</pre>';
	}

	public function fixOtsInterestDates()
	{

		DB::beginTransaction();

		try {

			$interestDetails = DB::select("SELECT acc_ots_interest_details.* FROM `acc_ots_interest_details`
                JOIN acc_ots_account ON acc_ots_account.id = acc_ots_interest_details.accId_fk
                WHERE acc_ots_account.periodId_fk NOT IN (1,5) AND DAYOFMONTH(`dateFrom`)=DAYOFMONTH(`dateTo`) ORDER BY accId_fk,dateFrom");
			$interestDetails = collect($interestDetails);

			$accountIds = $interestDetails->unique('accId_fk')->pluck('accId_fk')->toArray();

			foreach ($interestDetails as $interestDetail) {

				DB::table('acc_ots_interest_details')->where('id', $interestDetail->id)->update(['dateTo' => Carbon::parse($interestDetail->dateTo)->subDay()->format('Y-m-d')]);
				$previousDetails = DB::table('acc_ots_interest_details')->where('accId_fk', $interestDetail->accId_fk)->where('dateFrom', '<', $interestDetail->dateFrom)->where('periodNumber', $interestDetail->periodNumber - 1)->first();
				if ($previousDetails != null && Carbon::parse($previousDetails->dateTo)->diffInDays(Carbon::parse($interestDetail->dateFrom)) > 1) {
					DB::table('acc_ots_interest_details')->where('id', $interestDetail->id)->update(['dateFrom' => Carbon::parse($interestDetail->dateFrom)->subDay()->format('Y-m-d')]);
				}
			}

			DB::commit();
		} catch (\Exception $e) {
			DB::rollback();
			echo 'In catch';
		}

		dd($interestDetails->where('accId_fk', 225));
	}


	/**
	 * to change the first repay date and then generate the loan schedue.
	 * @param  [array] $loans [its an associative array, where key the loan id and value is the first repay date]
	 * @return [boolean]        [true or false]
	 */
	public function fixEspecialLoans($loans = null)
	{

		if ($loans == null) {
			$loans = ['179018' => '2019-05-05'];
		}

		// FIRST CHECK IS THERE ANY MONTH THE AFTER THE FIRST REPAY DATE.
		$loanObs = DB::table('mfn_loan')->whereIn('id', array_keys($loans))->select('id', 'branchIdFk', 'firstRepayDate')->get();

		$branchIds = $loanObs->unique('branchIdFk')->pluck('branchIdFk')->toArray();

		$monthendDates = DB::table('mfn_month_end')
			->whereIn('branchIdFk', $branchIds)
			->select(DB::raw('branchIdFk, MAX(date) AS date'))
			->groupBy('branchIdFk')
			->get();

		$errorFlag = 0;
		foreach ($loanObs as $loanOb) {
			if ($loanOb->firstRepayDate <= $monthendDates->where('branchIdFk', $loanOb->branchIdFk)->max('date') || $monthendDates->where('branchIdFk', $loanOb->branchIdFk)->max('date') == null) {
				echo "Month end is executaed for branch id: $loanOb->branchIdFk where loan id: $loanOb->id<br>";
				$errorFlag = 1;
			}
		}

		if ($errorFlag == 1) {
			return false;
		}

		DB::beginTransaction();

		try {
			foreach ($loans as $loanId => $firstRepayDate) {
				$firstRepayDate = Carbon::parse($firstRepayDate)->format('Y-m-d');
				DB::table('mfn_loan')->where('id', $loanId)->update(['firstRepayDate' => $firstRepayDate, 'isFirstRepayDateChanged' => 1]);
			}
			app('App\Http\Controllers\microfin\MicroFinance')->updateLoanSchedule($loans);

			DB::commit();
		} catch (\Exception $e) {
			DB::rollback();
			echo 'Something went wrong.';
		}
	}

	public function updatefirstRepayDateAndSchedule($loanIds = null)
	{
		$loans = DB::table('mfn_loan')->whereIn('id', $loanIds)->select('id', 'samityIdFk', 'disbursementDate')->get();
		foreach ($loans as $loan) {
			$firstRepayDate = Microfin::getFirstRepayDateForMonthlyLoan($loan->samityIdFk, $loan->disbursementDate);
			DB::table('mfn_loan')->where('id', $loan->id)->update(['firstRepayDate' => $firstRepayDate]);
			MicroFin::generateLoanSchedule($loan->id);
		}
	}

	public function updateAvgAttendenceAndSavingsDepositor()
	{

		$branchIds = [2];

		$startMonth = Carbon::parse('2018-11-01');
		$endMonth = Carbon::parse('2019-05-01');

		$months = array();

		while ($startMonth->lte($endMonth)) {
			array_push($months, $startMonth->copy()->endOfMonth()->format('Y-m-d'));
			$startMonth->addMonthsNoOverflow(1);
		}

		foreach ($branchIds as $branchId) {
			foreach ($months as $month) {

				$members = DB::table('mfn_member_information')
					->where('softDel', 0)
					->where('branchId', $branchId)
					->where('admissionDate', '<=', $month)
					->where(function ($query) use ($month) {
						$query->where('closingDate', '0000-00-00')
							->orWhere('closingDate', '>', $month);
					})
					->select('id', 'gender', 'primaryProductId')
					->get();

				$loanProducts = DB::table('mfn_loans_product')
					->whereIn("id", $members->unique('primaryProductId')->pluck('primaryProductId')->toArray())
					->select('id', 'productCategoryId', 'fundingOrganizationId')
					->get();

				foreach ($members as $key => $member) {
					$members[$key]->dayCount = 0;
					$members[$key]->present = 0;
					$members[$key]->loanCategoryId = $loanProducts->where('id', $member->primaryProductId)->max('productCategoryId');
					$members[$key]->funOrgId = $loanProducts->where('id', $member->primaryProductId)->max('fundingOrganizationId');
				}

				$primaryProductTransfers = DB::table('mfn_loan_primary_product_transfer')
					->where('softDel', 0)
					->where('branchIdFk', $branchId)
					->where('transferDate', '>', $month)
					->select('memberIdFk', 'transferDate', 'oldPrimaryProductFk')
					->get();

				$primaryProductTransfers = $primaryProductTransfers->sortBy('transferDate')->unique('memberIdFk');

				foreach ($primaryProductTransfers as $primaryProductTransfer) {
					if ($members->where('id', $primaryProductTransfer->memberIdFk)->first() != null) {
						$members->where('id', $primaryProductTransfer->memberIdFk)->first()->primaryProductId = $primaryProductTransfer->oldPrimaryProductFk;
					}
				}

				$memberAttendences = DB::table('mfn_auto_process_info')
					->where('branchIdFk', $branchId)
					->where('date', '>=', Carbon::parse($month)->startOfMonth()->format('Y-m-d'))
					->where('date', '<=', $month)
					->pluck('memberAttendence')
					->toArray();

				foreach ($memberAttendences as $memberAttendence) {

					$infos = json_decode($memberAttendence);

					foreach ($infos as $info) {
						$memberId = $info[0];
						$isPresent = $info[1];
						$member = $members->where('id', $memberId)->first();
						if ($member != null) {
							$member->dayCount++;
							$member->present += $isPresent;
						}
					}
				}

				$monthEndInfos = DB::table('mfn_month_end_process_members')
					->where('branchIdFk', $branchId)
					->where('date', $month)
					->select('id', 'fundingOrgIdFk', 'loanProductIdFk', 'loanProductCategoryIdFk', 'genderTypeId')
					->get();

				foreach ($monthEndInfos as $monthEndInfo) {
					$avgAttendence = 0;

					if ($monthEndInfo->loanProductIdFk > 0) {

						$present = $members
							->where('primaryProductId', $monthEndInfo->loanProductIdFk)
							->where('gender', $monthEndInfo->genderTypeId)
							->sum('present');

						$dayCount = $members
							->where('primaryProductId', $monthEndInfo->loanProductIdFk)
							->where('gender', $monthEndInfo->genderTypeId)
							->sum('dayCount');
					} elseif ($monthEndInfo->loanProductCategoryIdFk > 0) {

						$present = $members
							->where('loanCategoryId', $monthEndInfo->loanProductCategoryIdFk)
							->where('funOrgId', $monthEndInfo->fundingOrgIdFk)
							->where('gender', $monthEndInfo->genderTypeId)
							->sum('present');

						$dayCount = $members
							->where('loanCategoryId', $monthEndInfo->loanProductCategoryIdFk)
							->where('funOrgId', $monthEndInfo->fundingOrgIdFk)
							->where('gender', $monthEndInfo->genderTypeId)
							->sum('dayCount');
					}

					if ($dayCount > 0) {
						$avgAttendence = $present / $dayCount * 100;
					} else {
						$avgAttendence = 100;
					}
					DB::table('mfn_month_end_process_members')->where('id', $monthEndInfo->id)->update(['avgAttendance' => $avgAttendence]);
				}
			}
		}
	}

	public function fixSavingsInterest()
	{
		$dupliCateInterests = DB::select("SELECT `id`,`accIdFk` FROM `mfn_savings_interest` GROUP BY `accIdFk` HAVING COUNT(id) > 1");
		$dupliCateInterests = collect($dupliCateInterests);
		dd($dupliCateInterests);
	}

	public function checkInappropriateVoucher($branchId = null)
	{
		if ($branchId == null) {
			$branchId = 5;
		}

		$dateFrom = '2018-11-01';
		// $dateTo = Carbon::parse($dateFrom)->endOfMonth();
		$dateTo = '2019-05-31';

		// CHECK ONLY JOURNAL VOUCHER
		$transfers = DB::table('mfn_loan_primary_product_transfer')
			->where('branchIdFk', $branchId)
			->where('softDel', 0)
			->where('transferDate', '>=', $dateFrom)
			->where('transferDate', '<=', $dateTo)
			->select(DB::raw('transferDate, SUM(totalTransferAmount) AS totalTransferAmount'))
			->groupBy('transferDate')
			->get();

		foreach ($transfers as $transfer) {
			$voucherId = DB::table('acc_voucher')
				->where('branchId', $branchId)
				->where('voucherDate', $transfer->transferDate)
				->where('voucherTypeId', 3)
				->where('vGenerateType', 1)
				->where('moduleIdFk', 6)
				->value('id');

			$voucherAmount = DB::table('acc_voucher_details')
				->where('voucherId', $voucherId)
				->sum('amount');

			if ($voucherAmount != $transfer->totalTransferAmount) {
				echo '$transfer->transferDate: ' . $transfer->transferDate . '<br>';
				echo '$transfer->totalTransferAmount: ' . $transfer->totalTransferAmount . '<br>';
				echo '$voucherId: ' . $voucherId . '<br>';
				echo '$voucherAmount: ' . $voucherAmount . '<br><br>';
				app('App\Http\Controllers\microfin\process\RedoAutoVouchersController')->redoAutoVouchers($branchId, $transfer->transferDate);
			}
		}
	}

	public function getSavingsBalanceProductCategoryWise()
	{
		$branchId = 12;
		$date = '2019-03-31';

		$openingBalances = DB::table('mfn_opening_savings_account_info AS opb')
			->join('mfn_savings_account AS sa', 'sa.id', 'opb.savingsAccIdFk')
			->where('sa.branchIdFk', $branchId)
			->groupBy('opb.primaryProductIdFk')
			->groupBy('sa.savingsProductIdFk')
			->select(DB::raw('opb.primaryProductIdFk, sa.savingsProductIdFk, SUM(openingBalance) AS openingBalance'))
			->get();

		$deposits = DB::table('mfn_savings_deposit')
			->where('softDel', 0)
			->where('branchIdFk', $branchId)
			->where('depositDate', '<=', $date)
			->groupBy('primaryProductIdFk')
			->groupBy('productIdFk')
			->select(DB::raw('primaryProductIdFk, productIdFk, SUM(amount) AS amount'))
			->get();

		$withdraws = DB::table('mfn_savings_withdraw')
			->where('softDel', 0)
			->where('branchIdFk', $branchId)
			->where('withdrawDate', '<=', $date)
			->groupBy('primaryProductIdFk')
			->groupBy('productIdFk')
			->select(DB::raw('primaryProductIdFk, productIdFk, SUM(amount) AS amount'))
			->get();

		$primaryProductIds = array_unique(array_merge($openingBalances->pluck('primaryProductIdFk')->toArray(), $deposits->pluck('primaryProductIdFk')->toArray(), $withdraws->pluck('primaryProductIdFk')->toArray()));

		$primaryProducts = DB::table('mfn_loans_product')
			->whereIn('id', $primaryProductIds)
			->select('id', 'name', 'productCategoryId')
			->get();

		$categories = DB::table('mfn_loans_product_category')
			->whereIn('id', $primaryProducts->pluck('productCategoryId')->toArray())
			->where('softDel', 0)
			->get();

		$savingsProductIds = array_unique(array_merge($openingBalances->pluck('savingsProductIdFk')->toArray(), $deposits->pluck('savingsProductIdFk')->toArray(), $withdraws->pluck('savingsProductIdFk')->toArray()));

		$savingsProducts = DB::table('mfn_saving_product')
			->whereIn('id', $savingsProductIds)
			->select('id', 'name')
			->get();

		$infos = collect();

		foreach ($primaryProducts as $primaryProduct) {
			foreach ($savingsProducts as $savingsProduct) {

				$openingBalance = $openingBalances->where('primaryProductIdFk', $primaryProduct->id)->where('savingsProductIdFk', $savingsProduct->id)->sum('openingBalance');

				$deposit = $deposits->where('primaryProductIdFk', $primaryProduct->id)->where('productIdFk', $savingsProduct->id)->sum('amount');

				$withdraw = $withdraws->where('primaryProductIdFk', $primaryProduct->id)->where('productIdFk', $savingsProduct->id)->sum('amount');

				$balance = $openingBalance + $deposit - $withdraw;

				$infos->push([
					'categoryId'    => $primaryProduct->productCategoryId,
					'categoryName'    => $categories->where('id', $primaryProduct->productCategoryId)->max('name'),
					'savingsProductId'    => $savingsProduct->id,
					'savingsProductName'    => $savingsProduct->name,
					'balance'    => $balance
				]);
			}
		}

		$categoryIds = $infos->unique('categoryId')->pluck('categoryId')->toArray();

		echo 'Total Balance: ' . number_format($infos->sum('balance'), 2) . '<br><br>';

		foreach ($categoryIds as $categoryId) {
			$savProdIds = $infos->where('categoryId', $categoryId)->unique('savingsProductId')->pluck('savingsProductId')->toArray();

			foreach ($savProdIds as $savProdId) {
				echo 'Category: ' .  $categories->where('id', $categoryId)->max('name') . '<br>';
				echo 'Sav Product: ' .  $savingsProducts->where('id', $savProdId)->max('name') . '<br>';
				echo 'Balance: ' .  $infos->where('categoryId', $categoryId)->where('savingsProductId', $savProdId)->sum('balance') . '<br><br>';
			}
		}
	}

	public function updateSavingsOpeningBalancePrimaryProduct($branchId = null)
	{
		// $branchId = 20;

		$openingBalances = DB::table('mfn_opening_savings_account_info AS ob')
			->join('mfn_member_information AS mi', 'mi.id', 'ob.memberIdFk')
			->where('mi.branchId', $branchId)
			->select('ob.id', 'ob.memberIdFk', 'ob.primaryProductIdFk')
			->get();

		$members = DB::table('mfn_member_information')
			->where('softDel', 0)
			->where('branchId', $branchId)
			->select('id', 'primaryProductId')
			->get();

		// According to the transfer history, consider the member's primary product
		$primaryProductTransfers = DB::table('mfn_loan_primary_product_transfer')
			->where('softDel', 0)
			->where('branchIdFk', $branchId)
			->where('transferDate', '>', '2018-10-30')
			->get();

		$primaryProductTransfers = $primaryProductTransfers->sortBy('transferDate')->unique('memberIdFk');

		foreach ($primaryProductTransfers as $key => $primaryProductTransfer) {
			if ($members->where('id', $primaryProductTransfer->memberIdFk)->first() != null) {
				$members->where('id', $primaryProductTransfer->memberIdFk)->first()->primaryProductId = $primaryProductTransfer->oldPrimaryProductFk;
			}
		}

		foreach ($openingBalances as $openingBalance) {
			if ($members->where('id', $openingBalance->memberIdFk)->first() == null) {
				// echo 'Invalid MemberId: ' . $openingBalance->memberIdFk . '<br>';
				DB::table('mfn_opening_savings_account_info')->where('id', $openingBalance->id)->update(['softDel' => 1]);
			} else {
				$memberPrimaryProductId = $members->where('id', $openingBalance->memberIdFk)->first()->primaryProductId;
				if ($memberPrimaryProductId != $openingBalance->primaryProductIdFk) {
					echo 'Member ID: ' . $openingBalance->memberIdFk . '<br>';
					DB::table('mfn_opening_savings_account_info')->where('id', $openingBalance->id)->update(['primaryProductIdFk' => $memberPrimaryProductId]);
				}
			}
		}
	}

	public function updatePrimaryProductTransfers($branchId = null)
	{
		if ($branchId == null) {
			dd('Enter Branch Id');
		}

		// first delete the transfer related information from deposit and withdarw where transfer is softdeleted.
		$transferDates = DB::table('mfn_loan_primary_product_transfer')
			->where('softDel', 0)
			->where('branchIdFk', $branchId)
			// ->where('transferDate', '>=','2019-07-01')
			->groupBy('transferDate')
			->orderBy('transferDate')
			->pluck('transferDate')
			->toArray();

		$deletedTransferDates = DB::table('mfn_loan_primary_product_transfer')
			->where('softDel', 1)
			->where('branchIdFk', $branchId)
			->whereNotIn('transferDate', $transferDates)
			// ->where('transferDate', '>=', '2019-07-01')
			->groupBy('transferDate')
			->orderBy('transferDate')
			->pluck('transferDate')
			->toArray();

		foreach ($deletedTransferDates as $deletedTransferDate) {
			$dataExits = (int) DB::table('mfn_savings_deposit')->where('branchIdFk', $branchId)->where('depositDate', $deletedTransferDate)->where('isTransferred', 1)->value('id');
			$dataExits += (int) DB::table('mfn_savings_withdraw')->where('branchIdFk', $branchId)->where('withdrawDate', $deletedTransferDate)->where('isTransferred', 1)->value('id');
			if ($dataExits > 0) {
				DB::table('mfn_savings_deposit')->where('branchIdFk', $branchId)->where('depositDate', $deletedTransferDate)->where('isTransferred', 1)->delete();
				DB::table('mfn_savings_withdraw')->where('branchIdFk', $branchId)->where('withdrawDate', $deletedTransferDate)->where('isTransferred', 1)->delete();
				app('App\Http\Controllers\microfin\process\RedoAutoVouchersController')->redoAutoVouchers($branchId, $deletedTransferDate);
				app('App\Http\Controllers\microfin\process\UpdateDayEndInfoController')->index($branchId, $deletedTransferDate);
			}
		}

		DB::beginTransaction();
		try {

			foreach ($transferDates as $transferDate) {
				$this->updateTransfer($branchId, $transferDate);
				app('App\Http\Controllers\microfin\process\RedoAutoVouchersController')->redoAutoVouchers($branchId, $transferDate);
				app('App\Http\Controllers\microfin\process\UpdateDayEndInfoController')->index($branchId, $transferDate);
			}

			$minDate = null;
			$maxDate = null;

			if (count($deletedTransferDates) > 0) {
				$minDate = min($deletedTransferDates);
				$maxDate = max($deletedTransferDates);
			}
			if (count($transferDates) > 0) {
				if ($minDate == null) {
					$minDate = min($transferDates);
					$maxDate = max($transferDates);
				} else {
					$minDate = min($minDate, min($transferDates));
					$maxDate = max($maxDate, max($transferDates));
				}
			}

			if ($minDate != null && $maxDate != null && ($maxDate >= $minDate)) {
				app('App\Http\Controllers\microfin\process\UpdateMonthEndInfoController')->index($branchId, $minDate, $maxDate);
			}


			DB::commit();
		} catch (\Exception $e) {
			DB::rollback();
			$e->getMessage();
		}
	}

	public function updateTransfersTransactionPrimaryProduct($branchId = null)
	{


		if ($branchId == null) {
			dd('Enter branch id');
		}

		// $primaryProductIds = DB::table('mfn_loans_product')->where('isPrimaryProduct', 1)->pluck('id')->toArray();


		DB::beginTransaction();
		try {
			$transfers = DB::table('mfn_loan_primary_product_transfer')
				->where('softDel', 0)
				->where('branchIdFk', $branchId)
				->where('memberIdFk', 154)
				->orderBy('transferDate')
				->get();

			$memberIds = $transfers->unique('memberIdFk')->pluck('memberIdFk')->toArray();

			$minDate = $maxDate = null;

			foreach ($memberIds as $memberId) {
				$thisMemberTranfers = $transfers->where('memberIdFk', $memberId)->sortBy('transferDate')->values();
				$numberOfTransfersOfThisMember = count($thisMemberTranfers);

				foreach ($thisMemberTranfers as $key => $transfer) {
					if ($key == 0) {
						DB::table('mfn_savings_deposit')
							->where('softDel', 0)
							->where('isTransferred', 0)
							->where('memberIdFk', $memberId)
							->where('depositDate', '<=', $transfer->transferDate)
							->where('primaryProductIdFk', '!=', $transfer->oldPrimaryProductFk)
							->update(['primaryProductIdFk' => $transfer->oldPrimaryProductFk]);
					} else {
						DB::table('mfn_savings_deposit')
							->where('softDel', 0)
							->where('isTransferred', 0)
							->where('memberIdFk', $memberId)
							->where('depositDate', '>', $thisMemberTranfers[$key - 1]->transferDate)
							->where('depositDate', '<=', $transfer->transferDate)
							->where('primaryProductIdFk', '!=', $transfer->oldPrimaryProductFk)
							->update(['primaryProductIdFk' => $transfer->oldPrimaryProductFk]);
					}

					if ($key == $numberOfTransfersOfThisMember - 1) {
						DB::table('mfn_savings_deposit')
							->where('softDel', 0)
							->where('isTransferred', 0)
							->where('memberIdFk', $memberId)
							->where('depositDate', '>', $transfer->transferDate)
							->where('primaryProductIdFk', '!=', $transfer->newPrimaryProductFk)
							->update(['primaryProductIdFk' => $transfer->newPrimaryProductFk]);
					}
				}
			}
		} catch (\Exception $e) {
			DB::rollback();
			echo $e->getMessage();
		}
	}

	public function updateAvgAttendence($branchId = null)
	{
		$month = Carbon::parse('2019-06-01')->endOfMonth()->format('Y-m-d');
		$monthFirstDate = Carbon::parse($month)->startOfMonth()->format('Y-m-d');

		$savingsPartialWithdrawMember = DB::table('mfn_savings_withdraw AS sw')
			->join('mfn_member_information AS mi', 'mi.id', 'sw.memberIdFk')
			->where('sw.softDel', 0)
			->where('sw.isTransferred', 0)
			->where('sw.branchIdFk', $branchId)
			->where('sw.withdrawDate', '>=', $monthFirstDate)
			->where('sw.withdrawDate', '<=', $month)
			->where(function ($query) use ($month) {
				$query->where('mi.closingDate', '0000-00-00')
					->orWhere('mi.closingDate', '>', $month);
			})
			->groupBy('sw.primaryProductIdFk')
			->groupBy('mi.gender')
			->select(DB::raw('sw.primaryProductIdFk, mi.gender, COUNT(DISTINCT sw.memberIdFk) AS memberNo'))
			->get();

		$monthEndProcessMembers = DB::table('mfn_month_end_process_members')
			->where('branchIdFk', $branchId)
			->where('date', $month)
			->where('loanProductIdFk', '>', 0)
			->select('id', 'loanProductIdFk', 'genderTypeId', 'closingMember', 'memberCancellationNo')
			->get();

		foreach ($monthEndProcessMembers as $monthEndProcessMember) {
			$avgAttendence = floor($monthEndProcessMember->closingMember * (rand(65, 70) / 100));
			$avgSavDepositor = floor($avgAttendence * (rand(70, 80) / 100));

			$savingsPartialWithdrawMemberNum = $savingsPartialWithdrawMember
				->where('primaryProductIdFk', $monthEndProcessMember->loanProductIdFk)
				->where('gender', $monthEndProcessMember->genderTypeId)
				->max('memberNo');

			DB::table('mfn_month_end_process_members')
				->where('id', $monthEndProcessMember->id)
				->update(['avgAttendance' => $avgAttendence, 'avgSavingsDepositor' => $avgSavDepositor, 'savingsFullWithdrawMemberNum' => $monthEndProcessMember->memberCancellationNo, 'savingsPartialWithdrawMemberNum' => $savingsPartialWithdrawMemberNum]);
		}

		$monthEndProcessMembers = DB::table('mfn_month_end_process_members')
			->where('branchIdFk', $branchId)
			->where('date', $month)
			->where('loanProductCategoryIdFk', '>', 0)
			->select('id', 'loanProductCategoryIdFk', 'genderTypeId', 'closingMember', 'memberCancellationNo')
			->get();

		foreach ($monthEndProcessMembers as $monthEndProcessMember) {

			$productIds = DB::table('mfn_loans_product')->where('productCategoryId', $monthEndProcessMember->loanProductCategoryIdFk)->pluck('id')->toArray();

			$monthEndInfo = DB::table('mfn_month_end_process_members')->where('branchIdFk', $branchId)->where('date', $month)->where('loanProductIdFk', '>', 0)->whereIn('loanProductIdFk', $productIds)->where('genderTypeId', $monthEndProcessMember->genderTypeId)->get();

			$avgAttendence = $monthEndInfo->sum('avgAttendance');
			$avgSavDepositor = $monthEndInfo->sum('avgSavingsDepositor');
			$savingsFullWithdrawMemberNum = $monthEndInfo->sum('savingsFullWithdrawMemberNum');
			$savingsPartialWithdrawMemberNum = $monthEndInfo->sum('savingsPartialWithdrawMemberNum');

			DB::table('mfn_month_end_process_members')
				->where('id', $monthEndProcessMember->id)
				->update(['avgAttendance' => $avgAttendence, 'avgSavingsDepositor' => $avgSavDepositor, 'savingsFullWithdrawMemberNum' => $savingsFullWithdrawMemberNum, 'savingsPartialWithdrawMemberNum' => $savingsPartialWithdrawMemberNum]);
		}
	}

	/**
	 * this function returns the holidays date
	 *
	 * @param [date] $dateFrom it is optional, if it is given then this function will return holidays from this date.
	 * @return array
	 */
	public function getHolidaysForOrg($dateFrom = null)
	{
		$holidays = array();

		$holidays = DB::table('mfn_setting_holiday')
			->where('softDel', 0);

		if ($dateFrom != null) {
			$dateFrom = Carbon::parse($dateFrom)->format('Y-m-d');
			$holidays = $holidays->where('date', '>=', $dateFrom);
		}

		$holidays = $holidays->pluck('date')->toArray();

		$orgHolidays = DB::table('mfn_setting_orgBranchSamity_holiday')
			->where('softDel', 0)
			->where('isOrgHoliday', 1);

		if ($dateFrom != null) {
			$orgHolidays = $orgHolidays->where('dateTo', '>=', $dateFrom);
		}

		$orgHolidays = $orgHolidays->select('dateFrom', 'dateTo')->get();

		foreach ($orgHolidays as $orgHoliday) {
			$dateFrom = Carbon::parse($orgHoliday->dateFrom);
			$dateTo = Carbon::parse($orgHoliday->dateTo);

			while ($dateFrom->lte($dateTo)) {
				array_push($holidays, $dateFrom->format('Y-m-d'));
				$dateFrom->addDay();
			}
		}

		$holidays = array_unique($holidays);

		dd($holidays);
	}

	/**
	 * this function returns the holidays date
	 *
	 * @param [int] $branchId 
	 * @param [date] $dateFrom it is optional, if it is given then this function will return holidays from this date.
	 * @return array
	 */
	public function getHolidaysForBranch($branchId, $dateFrom = null)
	{
		$holidays = array();

		$holidays = DB::table('mfn_setting_holiday')
			->where('softDel', 0);

		if ($dateFrom != null) {
			$dateFrom = Carbon::parse($dateFrom)->format('Y-m-d');
			$holidays = $holidays->where('date', '>=', $dateFrom);
		}

		$holidays = $holidays->pluck('date')->toArray();

		$branchHolidays = DB::table('mfn_setting_orgBranchSamity_holiday')
			->where('softDel', 0)
			->where(function ($query) use ($branchId) {
				$query->where('branchIdFk', $branchId)
					->orWhere('isOrgHoliday', 1);
			});

		if ($dateFrom != null) {
			$branchHolidays = $branchHolidays->where('dateTo', '>=', $dateFrom);
		}

		$branchHolidays = $branchHolidays->select('dateFrom', 'dateTo')->get();

		foreach ($branchHolidays as $branchHoliday) {
			$dateFrom = Carbon::parse($branchHoliday->dateFrom);
			$dateTo = Carbon::parse($branchHoliday->dateTo);

			while ($dateFrom->lte($dateTo)) {
				array_push($holidays, $dateFrom->format('Y-m-d'));
				$dateFrom->addDay();
			}
		}

		$holidays = array_unique($holidays);

		dd($holidays);
	}

	/**
	 * this function returns the holidays date
	 *
	 * @param [int] $samityId 
	 * @param [date] $dateFrom it is optional, if it is given then this function will return holidays from this date.
	 * @return array
	 */
	public function getHolidaysForSamity($samityId, $dateFrom = null)
	{
		$holidays = array();

		$holidays = DB::table('mfn_setting_holiday')
			->where('softDel', 0);

		if ($dateFrom != null) {
			$dateFrom = Carbon::parse($dateFrom)->format('Y-m-d');
			$holidays = $holidays->where('date', '>=', $dateFrom);
		}

		$holidays = $holidays->pluck('date')->toArray();

		$branchId = DB::table('mfn_samity')->where('id', $samityId)->first()->branchId;

		$samityHolidays = DB::table('mfn_setting_orgBranchSamity_holiday')
			->where('softDel', 0)
			->where(function ($query) use ($branchId, $samityId) {
				$query->where('branchIdFk', $branchId)
					->orWhere('samityIdFk', $samityId)
					->orWhere('isOrgHoliday', 1);
			});

		if ($dateFrom != null) {
			$samityHolidays = $samityHolidays->where('dateTo', '>=', $dateFrom);
		}

		$samityHolidays = $samityHolidays->select('dateFrom', 'dateTo')->get();

		foreach ($samityHolidays as $samityHoliday) {
			$dateFrom = Carbon::parse($samityHoliday->dateFrom);
			$dateTo = Carbon::parse($samityHoliday->dateTo);

			while ($dateFrom->lte($dateTo)) {
				array_push($holidays, $dateFrom->format('Y-m-d'));
				$dateFrom->addDay();
			}
		}

		$holidays = array_unique($holidays);

		dd($holidays);
	}

	public function justInserDayEnd()
	{
		dd('stoped');
		$branchIds = DB::table('gnr_branch')
			->where('id', '>=', 2)
			->where('id', '<=', 110)
			->pluck('id')
			->toArray();

		$targetDate = Carbon::parse('2019-08-14');

		foreach ($branchIds as $branchId) {

			$currentSoftwareDate = Carbon::parse(MicroFin::getSoftwareDateBranchWise($branchId));

			if ($currentSoftwareDate->gte($targetDate)) {
				continue;
			}

			while ($currentSoftwareDate->lte($targetDate)) {
				$nextDay = $currentSoftwareDate->addDay();
				$nextDateString = $nextDay->copy()->format('Y-m-d');
				$isNextDayHoliday = app('App\Http\Controllers\microfin\process\DayEndProcessController')->isHoliday($nextDateString, $branchId);

				if ($isNextDayHoliday == true) {
					continue;
				} else {
					// INSERT NEXT DAY
					$isDayExits = (int) MfnDayEnd::where('branchIdFk', $branchId)->where('date', $nextDateString)->value('id');
					if ($isDayExits > 0) {
						$dayEnd = MfnDayEnd::where('branchIdFk', $branchId)->where('date', $nextDateString)->first();
					} else {
						$dayEnd = new MfnDayEnd;
					}

					$dayEnd->date               = $nextDateString;
					$dayEnd->branchIdFk         = $branchId;
					$dayEnd->isLocked           = 0;
					$dayEnd->executedByEmpIdFk  = Auth::user()->emp_id_fk;
					$dayEnd->createdAt          = Carbon::now();
					$dayEnd->save();

					// LOCK THE PREVIOS DAY
					DB::table('mfn_day_end')->where('branchIdFk', $branchId)->where('date', '<', $nextDateString)->update('isLocked', 1);
				}
			}
		}
	}

	/**
	 * IT RETURNS THE LOAN IDS WHICH HAVE SCHEDULE INTO HOLIDAYS.
	 *
	 * @param int $branchId
	 * @return void
	 */
	public function getLoanIdsScheduleInHoliday($branchId)
	{
		$holidays = $this->getHolidaysForBranch($branchId, '2019-07-01');

		$loansIds = DB::table('mfn_loan_schedule AS ls')
			->join('mfn_loan AS loan', 'loan.id', 'ls.loanIdFk')
			->where('loan.softDel', 0)
			->where('ls.softDel', 0)
			->where('loan.branchIdFk', $branchId)
			->where('ls.scheduleDate', '>=', '2019-07-01')
			->whereIn('ls.scheduleDate', $holidays)
			//->select('loan.id', 'ls.scheduleDate')
			//->get();
			->groupBy('loan.id')
			->pluck('loan.id')
			->toArray();

		dd($loansIds);
	}

	public function updateLoanScheduleDate($loanIdArray, $dateFrom = null)
	{
		$microFinance = new MicroFinance;

		if ($dateFrom != null) {
			$dateFrom = Carbon::parse($dateFrom)->format('Y-m-d');
		}
		//GET ALL LOAN DATA
		$loanOBArray = DB::table('mfn_loan')
			->whereIn('id', $loanIdArray)
			->where('softDel', '=', 0)
			->get();

		if ($loanOBArray) {
			//	GENERATE NEW SCHEDULED DATE.
			$repaymentFrequencyWiseRepayDate = [
				'1'	 =>  7,
				'2'  =>  28
			];

			foreach ($loanOBArray as $key => $loan) {
				$totalPrincipal = 0;
				$totalInstalmentAmount = 0;
				$installmentAmount = $loan->installmentAmount;

				$targetInstallSlNo = (int) DB::table('mfn_loan_schedule')
					->where('loanIdFk', $loan->id)
					->where('scheduleDate', '=', $dateFrom)
					->value('installmentSl');

				//  GET INTEREST RATE INDEX.
				$interestRateIndexOB = MfnLoanProductInterestRate::where('loanProductId', $loan->productIdFk)
					->where('installmentNum', $loan->repaymentNo)
					->where('status', 1)
					->select('interestRateIndex')
					->first();

				//	CALCULATING PRINCIPAL AMOUNT AND INTEREST AMOUNT.
				$principalAmount = $loan->installmentAmount / $interestRateIndexOB->interestRateIndex;
				$interestAmount = $loan->installmentAmount - $principalAmount;

				//	GET HOLIDAY.
				$globalGovtHoliday 		= $microFinance->getGlobalGovtHolidayByDate($loan->disbursementDate);
				$organizationHoliday 	= $microFinance->getOrganizationHolidayByDate(1, $loan->disbursementDate);
				$branchHoliday 			= $microFinance->getBranchHolidayByDate($loan->disbursementDate);
				$samityHoliday 			= $microFinance->getSamityHolidayByDate($loan->memberIdFk, $loan->disbursementDate);
				$holiday = array_unique(array_merge($globalGovtHoliday, $organizationHoliday, $branchHoliday, $samityHoliday));

				$holidayFound = 0;
				$scheduleDateArr = [];

				for ($i = 0; $i < 1000; $i++) :
					$dayDiff = ($repaymentFrequencyWiseRepayDate[$loan->repaymentFrequencyIdFk] * $i) . 'days';
					$date = date_create($loan->firstRepayDate);
					date_add($date, date_interval_create_from_date_string($dayDiff));

					//	PROPAGATE SCHEDULE DATE FOR WEEKLY FREQUENCY.
					if ($loan->repaymentFrequencyIdFk == 1) :
						//	CHECK IF A DATE IS MATCHES TO HOLIDAY.
						foreach ($holiday as $key => $val) :
							if (date_create($val) >= $date) :
								if (date_create($val) == $date) :
									$holidayFound = 1;
									break;
								endif;
							endif;
						endforeach;

						if ($holidayFound == 0)
							$scheduleDateArr[] = date_format($date, "Y-m-d");

						$holidayFound = 0;
					endif;

					//	PROPAGATE SCHEDULE DATE FOR MONTHLY FREQUENCY.
					if ($loan->repaymentFrequencyIdFk == 2) :
						$dayDiff = ($repaymentFrequencyWiseRepayDate[$loan->repaymentFrequencyIdFk] * $i) . 'days';
						$date = date_create($loan->firstRepayDate);
						date_add($date, date_interval_create_from_date_string($dayDiff));
						$disbursementDate = date_create($loan->disbursementDate);
						date_add($disbursementDate, date_interval_create_from_date_string($dayDiff));

						$tos = Carbon::parse($loan->firstRepayDate);
						// $sot = $tos->addMonths($i)->toDateString();
						$sot = $tos->addMonthsNoOverflow($i)->toDateString();

						if ($i == 0)
							$targetDate = date_format($date, "Y-m-d");
						else
							$targetDate = $microFinance->getMonthlyLoanScheduleDateFilter($sot, $loan->memberIdFk);

						$originalMD = Carbon::parse($targetDate);
						$MD = Carbon::parse($targetDate);
						$targetDate = $MD->toDateString();

						//	CHECK IF A DATE IS MATCHES TO HOLIDAY, THEN SAMITY DATE SET TO NEXT WEEK.
						for ($j = 0; $j < 100; $j++) :
							if (in_array($targetDate, $holiday)) :
								$targetDate = $MD->addDays(7)->toDateString();

								if ($targetDate > $originalMD->endOfMonth()) :
									$targetDate = $MD->subDays(14)->toDateString();
								else :
									if (in_array($targetDate, $holiday)) :
										$targetDate = $MD->addDays(7)->toDateString();

										if ($targetDate > $originalMD->endOfMonth()) :
											$targetDate = $MD->subDays(21)->toDateString();
										endif;
									else :
										break;
									endif;
								endif;
							else :
								break;
							endif;
						endfor;

						$scheduleDateArr[] = $targetDate;
					endif;

					if (count($scheduleDateArr) == $loan->repaymentNo)
						break;
				endfor;

				//	NEWLY GENERATE LOAN SCHEDULE.
				for ($i = 0; $i < $loan->repaymentNo; $i++) :

					//App\Flight::create(['name' => 'Flight 10']);

					$insertArray = array();
					$insertArray['loanIdFk'] = $loan->id;
					$insertArray['installmentSl'] = $i + 1;

					//	FOR REGULAR LOAN loanTypeId = 1
					$insertArray['loanTypeId'] = 1;
					$actualInstallmentAmount = $loan->actualInstallmentAmount;
					$extraInstallmentAmount = $loan->extraInstallmentAmount;

					if ($i == $loan->repaymentNo - 1) :
						//	CALCULATING PRINCIPAL AMOUNT AND INTEREST AMOUNT FOR LAST INSTALLMENT.
						$installmentAmount = $loan->totalRepayAmount - ($loan->installmentAmount * ($loan->repaymentNo - 1));
						$principalAmount = $installmentAmount / $interestRateIndexOB->interestRateIndex;
						$interestAmount = $installmentAmount - $principalAmount;
						$insertArray['installmentAmount'] = sprintf("%.5f", $installmentAmount);
						// $insertArray['actualInstallmentAmount'] = sprintf("%.5f", 0);
						// $insertArray['extraInstallmentAmount'] = sprintf("%.5f", 0);

						$actualInstallmentAmount = 0;
						$extraInstallmentAmount = 0;

					endif;
					$insertArray['installmentAmount'] = sprintf("%.5f", $installmentAmount);
					$insertArray['actualInstallmentAmount'] = sprintf("%.5f", $actualInstallmentAmount);
					$insertArray['extraInstallmentAmount'] = sprintf("%.5f", $extraInstallmentAmount);
					$insertArray['principalAmount'] = sprintf("%.5f", $principalAmount);
					$insertArray['interestAmount'] = sprintf("%.5f", $interestAmount);
					$insertArray['scheduleDate'] = $scheduleDateArr[$i];
					$totalPrincipal += $principalAmount;
					$totalInstalmentAmount += $installmentAmount;
					// $create = MfnLoanSchedule::create($insertArray);
					if ($dateFrom != null) {
						if ($i + 1 == $targetInstallSlNo && $scheduleDateArr[$i] < $dateFrom) {
							echo "Target Date is less than the starting date for loan ID: " . $loan->id . "<br>";
							echo "Target Date is : " . $scheduleDateArr[$i] . "<br><br>";
						} else {
							DB::table('mfn_loan_schedule')
								->where('loanIdFk', $loan->id)
								->where('installmentSl', $i + 1)
								->where('scheduleDate', '>=', $dateFrom)
								->update(['scheduleDate' => $scheduleDateArr[$i]]);
						}
					} else {
						DB::table('mfn_loan_schedule')
							->where('loanIdFk', $loan->id)
							->where('installmentSl', $i + 1)
							->update(['scheduleDate' => $scheduleDateArr[$i]]);
					}
				endfor;

				// UPDATE LAST INSTALLMENT DATE IN LOAN TABLE.
				MfnLoan::where('id', $loan->id)->update(['lastInstallmentDate' => end($scheduleDateArr)]);
			}
		}

		echo "Done";
	}

	public function getScheduleOnNotSamityDay($branchId, $dateFrom = null)
	{
		$samities = DB::table('mfn_samity')
			->where('softDel', 0)
			->where('branchId', $branchId)
			->select('id', 'samityDayOrFixedDateId', 'samityDayId', 'fixedDate')
			->get();

		$dateFrom = '2019-05-01';

		foreach ($samities as $samity) {

			$dayChanges = DB::table('mfn_samity_day_change')
				->where('samityId', $samity->id)
				->where('effectiveDate', '>=', $dateFrom)
				->groupBy('samityId', 'effectiveDate')
				->orderBy('effectiveDate')
				->get();

			foreach ($dayChanges as $key => $dayChange) {

				if ($dayChange->samityDayId == 1) {
					$samityDay = 5;
				} elseif ($dayChange->samityDayId == 2) {
					$samityDay = 6;
				} else {
					$samityDay = $dayChange->samityDayId - 3;
				}

				$wrongSchedules = DB::table('mfn_loan_schedule AS ls')
					->join('mfn_loan AS loan', 'loan.id', 'ls.loanIdFk')
					->where([['loan.softDel', 0], ['ls.softDel', 0]])
					->where('loan.samityIdFk', $samity->id)
					->where('ls.scheduleDate', '>=', $dateFrom)
					->where('ls.scheduleDate', '<', $dayChange->effectiveDate)
					->where(DB::raw("WEEKDAY(scheduleDate)"), '!=', $samityDay)
					->groupBy('loan.id')
					->select(DB::raw("WEEKDAY(scheduleDate) AS scWeekDay, loan.id AS loanId, loan.loanCode,ls.id AS scheduleId ,ls.scheduleDate"))
					->get();

				foreach ($wrongSchedules as $wrongSchedule) {
					// echo "samity->id: $samity->id <br>";
					// echo "samity->samityDayId: $samity->samityDayId <br>";
					// echo "samityDay: $samityDay <br>";
					// echo "scWeekDay: $wrongSchedule->scWeekDay <br>";
					// echo "Loan ID: $wrongSchedule->loanId <br>";
					echo "$wrongSchedule->loanCode <br>";
					// echo "Schedule Id: $wrongSchedule->scheduleId <br>";
					// echo "Loan scheduleDate: $wrongSchedule->scheduleDate <br><br>";
				}

				$dateFrom = $dayChange->effectiveDate;
			}

			if ($samity->samityDayId == 1) {
				$samityDay = 5;
			} elseif ($samity->samityDayId == 2) {
				$samityDay = 6;
			} else {
				$samityDay = $samity->samityDayId - 3;
			}

			$wrongSchedules = DB::table('mfn_loan_schedule AS ls')
				->join('mfn_loan AS loan', 'loan.id', 'ls.loanIdFk')
				->where([['loan.softDel', 0], ['ls.softDel', 0]])
				->where('loan.samityIdFk', $samity->id)
				->where('ls.scheduleDate', '>=', $dateFrom)
				->where(DB::raw("WEEKDAY(scheduleDate)"), '!=', $samityDay)
				->groupBy('loan.id')
				->select(DB::raw("WEEKDAY(scheduleDate) AS scWeekDay, loan.id AS loanId, loan.loanCode,ls.id AS scheduleId ,ls.scheduleDate"))
				->get();

			foreach ($wrongSchedules as $wrongSchedule) {
				// echo "samity->id: $samity->id <br>";
				// echo "samity->samityDayId: $samity->samityDayId <br>";
				// echo "samityDay: $samityDay <br>";
				// echo "scWeekDay: $wrongSchedule->scWeekDay <br>";
				// echo "Loan ID: $wrongSchedule->loanId <br>";
				echo "$wrongSchedule->loanCode <br>";
				// echo "Schedule Id: $wrongSchedule->scheduleId <br>";
				// echo "Loan scheduleDate: $wrongSchedule->scheduleDate <br><br>";
			}
		}
	}

	public function findMissMatchOnPOMISOneNAccounting($branchId = 14, $date = '2019-07-31')
	{
		$monthEndInfos = DB::table('mfn_month_end_process_savings')
			->where('date', $date)
			->where('branchIdFk', $branchId)
			->groupBy('productIdFk', 'savingProductIdFk')
			->select(DB::raw("productIdFk, savingProductIdFk, SUM(closingBalance) AS closingBalance"))
			->get();

		$loanProducts = DB::table('mfn_loans_product')
			->whereIn('id', $monthEndInfos->pluck('productIdFk')->toArray())
			->select('id', 'productCategoryId')
			->get();

		$savProducts = DB::table('mfn_saving_product')
			->whereIn('id', $monthEndInfos->pluck('savingProductIdFk')->toArray())
			->select('id', 'name')
			->get();

		$loanCategories = DB::table('mfn_loans_product_category')
			->whereIn('id', $loanProducts->pluck('productCategoryId')->toArray())
			->select('id', 'name')
			->get();

		$savConfig = DB::table('acc_mfn_av_config_saving')->get();

		$previosFiscalYear = DB::table('gnr_fiscal_year')->where('fyEndDate', '<', $date)->orderBy('fyEndDate', 'desc')->first();
		// dd($monthEndInfos, $loanProducts, $previosFiscalYear, $branchId);

		foreach ($loanCategories as $loanCategory) {
			$loanProductIds = $loanProducts->where('productCategoryId', $loanCategory->id)->pluck('id')->toArray();

			foreach ($savProducts as $savProduct) {
				// MIS
				$misSavBalance = $monthEndInfos->whereIn('productIdFk', $loanProductIds)->where('savingProductIdFk', $savProduct->id)->sum('closingBalance');

				// AIS
				$ledgerId = $savConfig->where('loanProductId', $loanProductIds[0])->where('savingProductId', $savProduct->id)->max('ledgerIdForPrincipal');

				$openingBalance = DB::table('acc_opening_balance')
					->where('branchId', $branchId)
					->where('fiscalYearId', $previosFiscalYear->id)
					->where('ledgerId', $ledgerId)
					->sum('balanceAmount');

				$debitAmount = DB::table('acc_voucher_details AS vd')
					->join('acc_voucher AS v', 'v.id', 'vd.voucherId')
					->where('v.branchId', $branchId)
					->where('v.voucherDate', '>', $previosFiscalYear->fyEndDate)
					->where('v.voucherDate', '<=', $date)
					->where('vd.debitAcc', $ledgerId)
					->sum('amount');

				$creditAmount = DB::table('acc_voucher_details AS vd')
					->join('acc_voucher AS v', 'v.id', 'vd.voucherId')
					->where('v.branchId', $branchId)
					->where('v.voucherDate', '>', $previosFiscalYear->fyEndDate)
					->where('v.voucherDate', '<=', $date)
					->where('vd.creditAcc', $ledgerId)
					->sum('amount');

				$aisSavBalance = abs($openingBalance + $debitAmount - $creditAmount);

				if ($misSavBalance != $aisSavBalance) {
					echo "loan Category: $loanCategory->name<br>";
					echo "Savings Product: $savProduct->name<br>";
					echo "misSavBalance: $misSavBalance<br>";
					echo "aisSavBalance: $aisSavBalance<br><br>";
				}
			}
		}
	}

	/**
	 * IT GENERATES LOAN SCHEDULE FOR A LOAN
	 *
	 * @param [int] $loanId
	 * @param [array] $holidays
	 * @return array
	 */
	public function makeLoanSchedule($loanId, &$holidays = null)
	{
		// echo "Loan ID: $loanId <br>";

		$loan = DB::table('mfn_loan')->where('id', $loanId)->select('id', 'loanTypeId', 'productIdFk', 'branchIdFk', 'samityIdFk', 'repaymentFrequencyIdFk', 'repaymentNo', 'actualNumberOfInstallment', 'disbursementDate', 'firstRepayDate', 'loanAmount', 'interestRateIndex', 'totalRepayAmount', 'extraInstallmentAmount', 'actualInstallmentAmount', 'loanRepayPeriodIdFk')->first();

		if ($holidays == null) {
			// GET THE HOLIDAYS
			$holidays = MicroFin::getHolidaysForSamity($loan->samityIdFk, $loan->disbursementDate);

			// FIXED GOV. HOLIDAYS MAY NOT ASSIGN TO THE CALLENDER. BUT WE WILL CONSIDER THE FIXED GOV. HOLIDAYS FOR THE UPCOMMING YEARS. WE WILL CALCULATE THE EXPECTED LOAN COMPLETE YEAR. FOR THE SAFETY PURPOSE WE WILL GET THE HOLIDAYS FOR THE NET ONE YEAR OF THE EXPEDTED LOAN COMPLETE DATE.
			$loanPeriodInMonths = DB::table('mfn_loan_repay_period')->where('id', $loan->loanRepayPeriodIdFk)->first()->inMonths;
			$expectedLoanCompleteYear = Carbon::parse($loan->firstRepayDate)->addMonthsNoOverflow($loanPeriodInMonths)->format('Y');
			$maxGovHolidayYearFromCallender = (int) DB::table('mfn_setting_holiday')->where('isGovHoliday', 1)->max('year');

			if ($maxGovHolidayYearFromCallender < $expectedLoanCompleteYear + 1) {
				$fixedGovHolidays = MicroFin::getFixedGovHolidaysByYears(Carbon::parse($loan->disbursementDate)->format('Y'), $expectedLoanCompleteYear + 1);
				$weeklyHolidays = MicroFin::getWeeklyHolidaysByYears(Carbon::parse($loan->disbursementDate)->format('Y'), $expectedLoanCompleteYear + 1);
				$holidays = array_unique(array_merge($holidays, $fixedGovHolidays, $weeklyHolidays));
			}

			sort($holidays);
		}

		// dd($holidays);

		if ($loan->repaymentNo != $loan->actualNumberOfInstallment) {
			dd(['success' => false, 'msg' => 'repaymentNo and actualNumberOfInstallment are not equal.']);
			return ['success' => false, 'msg' => 'repaymentNo and actualNumberOfInstallment are not equal.'];
		}

		// NOTE: loanTypeId 1 MEANS IT IS REGULAR LOAN AND 2 MEANS ONE TIME LOAN.
		if ($loan->loanTypeId == 1) {
			// MAKE SCHEDULE FOR REGULAR LOAN

			// GET THE INSTALLMENT INFORMATION
			$actualInstallmentAmount = round($loan->totalRepayAmount / $loan->actualNumberOfInstallment, 2);
			$extraInstallmentAmount = $loan->extraInstallmentAmount;
			$installmentAmount = $actualInstallmentAmount + $extraInstallmentAmount;
			$principalAmount = round($installmentAmount / $loan->interestRateIndex, 5);
			$interestAmount = $installmentAmount - $principalAmount;

			// IN REGULAR LOAN THERE ARE TWO TYPES OF LOAN, ONE IS WEEKLY LOAN AND ANOTHER IS MONTHLY LOAN. repaymentFrequencyIdFk 1 means it is weekly loan and 2 means it is monthly loan.
			if ($loan->repaymentFrequencyIdFk == 1) {
				$scheduleDates = $this->makeLoanScheduleForWeeklyLoan($loan, $holidays);
			} else {
				$scheduleDates = $this->makeLoanScheduleForMonthlyLoan($loan, $holidays);
			}
		} else {
			// MAKE SCHEDULE FOR ONLE TIME LOAN.
			dd(['success' => false, 'msg' => 'It is One Time Loan.']);
			return ['success' => false, 'msg' => 'It is One Time Loan.'];
		}

		// MAKE THE SCHEDULE ARRAY
		$schedules = collect();
		for ($i = 0; $i < $loan->actualNumberOfInstallment; $i++) {

			// IF IT IS THE LAST INSTALLMENT THEN THE AMOUNT FIGURE WILL BE CHANGE.
			if ($i == $loan->actualNumberOfInstallment - 1) {
				$installmentAmount = $loan->totalRepayAmount - ($loan->actualNumberOfInstallment - 1) * $installmentAmount;
				$principalAmount = round($installmentAmount / $loan->interestRateIndex, 5);
				$interestAmount = $installmentAmount - $principalAmount;
				$actualInstallmentAmount = 0;
				$extraInstallmentAmount = 0;
			}

			$data = array(
				'installmentSl'	=> $i + 1,
				'installmentAmount'	=> $installmentAmount,
				'actualInstallmentAmount'	=> $actualInstallmentAmount,
				'extraInstallmentAmount'	=> $extraInstallmentAmount,
				'principalAmount'	=> $principalAmount,
				'interestAmount'	=> $interestAmount,
				'scheduleDate'	=> $scheduleDates[$i],
				// 'scheduleDay'	=> Carbon::parse($scheduleDates[$i])->format('l'),
			);

			$schedules->push($data);
		}

		// CHECK SOME DATA TO VALIDATE THAT THIS LOAN SCHEDULE IS CORRECT.
		if ($schedules->count() != $loan->actualNumberOfInstallment) {
			dd(['success' => false, 'msg' => 'Number of Schedules Dates does not match with the actualNumberOfInstallment.']);
			return ['success' => false, 'msg' => 'Number of Schedules Dates does not match with the actualNumberOfInstallment.'];
		}
		if ($schedules->last()['installmentAmount'] > $schedules->first()['installmentAmount']) {
			dd(['success' => false, 'msg' => 'Last Installment Amount is greater than the Installment Amount.']);
			return ['success' => false, 'msg' => 'Last Installment Amount is greater than the Installment Amount.'];
		}
		if ($schedules->sum('installmentAmount') != $loan->totalRepayAmount) {
			dd(['success' => false, 'msg' => 'Total Repay Amount is not matched with the total Installment amount.']);
			return ['success' => false, 'msg' => 'Total Repay Amount is not matched with the total Installment amount.'];
		}
		if (round($schedules->sum('principalAmount')) != $loan->loanAmount) {
			dd(['success' => false, 'msg' => 'Loan Amount: ' . $loan->loanAmount . ' is not matched with the total Installment principal amount: ' . $schedules->sum('principalAmount')]);
			return ['success' => false, 'msg' => 'Loan Amount: ' . $loan->loanAmount . ' is not matched with the total Installment principal amount: .' . $schedules->sum('principalAmount')];
		}

		return $schedules;
	}

	public function makeLoanScheduleForWeeklyLoan($loan, &$holidays)
	{
		$weekMap = [
			1 => Carbon::SATURDAY, // SA
			2 => Carbon::SUNDAY, // SU
			3 => Carbon::MONDAY, // MO
			4 => Carbon::TUESDAY, // TU
			5 => Carbon::WEDNESDAY, // WE
			6 => Carbon::THURSDAY, // TH
			7 => Carbon::FRIDAY  // FR
		];

		$scheduleDates = array();
		$scheduleDate = Carbon::parse($loan->firstRepayDate);
		$scheduleDate->setWeekStartsAt(Carbon::SATURDAY);
		$scheduleDate->setWeekEndsAt(Carbon::FRIDAY);

		$samityDayChanges = DB::table('mfn_samity_day_change')
			->where('samityId', $loan->samityIdFk)
			->where('effectiveDate', '>', $loan->firstRepayDate)
			->groupBy('samityId', 'effectiveDate')
			->orderBy('effectiveDate')
			->get();

		// IF ANY SAMITY DAY CHANGES AFTER THE FIRST REPAY DATE THEN WE NEED TO CONSIDER THE SAMITY DAY CHANGES
		if (count($samityDayChanges) > 0) {
			$currentSamityDayChangeId = 0;
			for ($i = 0; $i < $loan->actualNumberOfInstallment; $i++) {

				// echo $scheduleDate->format('d-m-Y') . ' - ' . $scheduleDate->format('l') . '<br>';				

				$currentSamityDayChangeId = $this->reassignScheduleDateBaseOnSamityDayChangeForWeeklyLoan($samityDayChanges, $scheduleDate, $currentSamityDayChangeId, $weekMap, $holidays);

				while (in_array($scheduleDate->format('Y-m-d'), $holidays)) {
					$scheduleDate->addDays(7);
					$currentSamityDayChangeId = $this->reassignScheduleDateBaseOnSamityDayChangeForWeeklyLoan($samityDayChanges, $scheduleDate, $currentSamityDayChangeId, $weekMap, $holidays);
				}

				array_push($scheduleDates, $scheduleDate->format('Y-m-d'));
				$scheduleDate->addDays(7);
			}
		} else {
			for ($i = 0; $i < $loan->actualNumberOfInstallment; $i++) {

				while (in_array($scheduleDate->format('Y-m-d'), $holidays)) {
					$scheduleDate->addDays(7);
				}
				array_push($scheduleDates, $scheduleDate->format('Y-m-d'));
				$scheduleDate->addDays(7);
			}
		}

		return $scheduleDates;
	}

	public function reassignScheduleDateBaseOnSamityDayChangeForWeeklyLoan($samityDayChanges, $scheduleDate, $currentSamityDayChangeId, $weekMap, $holidays)
	{
		$samityDayChange = $samityDayChanges->where('effectiveDate', '<=', $scheduleDate->format('Y-m-d'))->sortByDesc('effectiveDate')->first();

		if ($samityDayChange != null && $currentSamityDayChangeId != $samityDayChange->id) {
			$ischnaged = 1;
			$currentSamityDayChangeId = $samityDayChange->id;
		} else {
			$ischnaged = 0;
		}

		if ($ischnaged == 1) {
			$scheduleDate->startOfWeek();
			if ($scheduleDate->dayOfWeek != $scheduleDate->copy()->next($weekMap[$samityDayChange->newSamityDayId])->dayOfWeek) {
				$scheduleDate->next($weekMap[$samityDayChange->newSamityDayId]);
			}
		}
		return $currentSamityDayChangeId;
	}

	public function makeLoanScheduleForMonthlyLoan($loan, &$holidays, $customSamityDayChange = null)
	{
		$weekMap = [
			1 => Carbon::SATURDAY, // SA
			2 => Carbon::SUNDAY, // SU
			3 => Carbon::MONDAY, // MO
			4 => Carbon::TUESDAY, // TU
			5 => Carbon::WEDNESDAY, // WE
			6 => Carbon::THURSDAY, // TH
			7 => Carbon::FRIDAY  // FR
		];

		$samityDayIdOrigin = DB::table('mfn_samity')->where('id', $loan->samityIdFk)->first()->samityDayId;

		if ($customSamityDayChange != null) {
			$samityDayIdOrigin = $customSamityDayChange->newSamityDayId;
		}

		$scheduleDates = array();
		$scheduleDate = Carbon::parse($loan->firstRepayDate);
		$scheduleDate->setWeekStartsAt(Carbon::SATURDAY);
		$scheduleDate->setWeekEndsAt(Carbon::FRIDAY);
		$firstRepayDate = Carbon::parse($loan->firstRepayDate);
		$disbursementDate = Carbon::parse($loan->disbursementDate);

		$samityDayChanges = DB::table('mfn_samity_day_change')
			->where('samityId', $loan->samityIdFk)
			->where('effectiveDate', '>', $loan->disbursementDate)
			->groupBy('samityId', 'effectiveDate')
			->orderBy('effectiveDate')
			->select('effectiveDate', 'samityDayId', 'newSamityDayId')
			->get();

		if ($customSamityDayChange != null) {
			$samityDayChanges->push($customSamityDayChange);
		}


		for ($i = 0; $i < $loan->actualNumberOfInstallment; $i++) {

			$scheduleDate = $firstRepayDate->copy()->addMonthNoOverflow($i);
			$monthFirstDate = $scheduleDate->copy()->startOfMonth();
			$monthLastDate = $scheduleDate->copy()->endOfMonth();

			$samityDayChange = $samityDayChanges->where('effectiveDate', '>=', $scheduleDate->format('Y-m-d'))->sortBy('effectiveDate')->first();

			$samityDayId = $samityDayChange != null ? $samityDayChange->samityDayId : $samityDayIdOrigin;

			$scheduleDate->startOfWeek();

			if ($scheduleDate->dayOfWeek != $scheduleDate->copy()->next($weekMap[$samityDayId])->dayOfWeek || $scheduleDate->lt($monthFirstDate)) {
				$scheduleDate->next($weekMap[$samityDayId]);
			}
			if ($scheduleDate->gt($monthLastDate)) {
				$scheduleDate->subDays(7);
			}

			// FIRST SCHEDULE DATE SHOULD BE AFTER 30 DAYS IF NOT HOLIDAY OCCURS
			/* if ($i == 0 && $disbursementDate->diffInDays($scheduleDate) < 30) {
				$scheduleDate->addDays(7);
			} */
			if ($i == 0) {
				while ($disbursementDate->diffInDays($scheduleDate) < 30 && $scheduleDate->copy()->addDays(7)->lte($monthLastDate)) {
					$scheduleDate->addDays(7);
				}
			}

			// IF THE SCHEDULE DATE IN HOLIDAY THEN FIND THE ALTERNATIVE SAMITY DAYS IN THIS MONTH
			if (in_array($scheduleDate->format('Y-m-d'), $holidays)) {

				$alternativeDays = [];
				$monthFirstDateCopy = $monthFirstDate->copy();
				while ($monthFirstDateCopy->lte($monthLastDate)) {
					if ($monthFirstDateCopy->dayOfWeek == $scheduleDate->dayOfWeek) {
						array_push($alternativeDays, $monthFirstDateCopy->format('Y-m-d'));
						$monthFirstDateCopy->addDays(7);
					} else {
						$monthFirstDateCopy->addDay();
					}
				}
				// REMOVE THE HOLIDAYS
				$alternativeDays = array_diff($alternativeDays, $holidays);

				// IF ANY ALTERNATIVE DAY FOUND, THEN FIRST TRY TO ASSIGN THE NEXT CLOSET DATE, IF NEXT DATES ARE NOT AVAILABE THEN ASSIGN THE PREVIOUS NEARREST DAY.
				if (count($alternativeDays) > 0) {

					$nextAlternativeDays = array_filter($alternativeDays, function ($value) use ($scheduleDate) {
						return $value > $scheduleDate->format('Y-m-d');
					});

					if (count($nextAlternativeDays) > 0) {
						$scheduleDate = Carbon::parse(min($nextAlternativeDays));
					} else {
						$previousAlternativeDays = array_filter($alternativeDays, function ($value) use ($scheduleDate) {
							return $value < $scheduleDate->format('Y-m-d');
						});
						$scheduleDate = Carbon::parse(max($previousAlternativeDays));
					}
				}
				// IF NO ALTERNATIVE DAY FOUND THEN ASSIGN TO ANY DAY AFTER OR BEFORE SCHEDULE DATE.
				else {
					$isScheduleAssigned = 0;
					$scheduleDateCopy = $scheduleDate->copy()->addDay();
					while ($scheduleDateCopy->lte($monthLastDate)) {
						if (!in_array($scheduleDateCopy->format('Y-m-d'), $holidays)) {
							$scheduleDate = $scheduleDateCopy;
							$isScheduleAssigned = 1;
							break;
						}
						$scheduleDateCopy->addDay();
					}
					if ($isScheduleAssigned == 0) {
						$scheduleDateCopy = $scheduleDate->copy()->subDay();
						while ($scheduleDateCopy->gte($monthFirstDate)) {
							if (!in_array($scheduleDateCopy->format('Y-m-d'), $holidays)) {
								$scheduleDate = $scheduleDateCopy;
								break;
							}
							$scheduleDateCopy->subDay();
						}
					}
				}
			}

			array_push($scheduleDates, $scheduleDate->format('Y-m-d'));
		}

		return $scheduleDates;
	}

	public function updateLoanScheduleStstus($loanId, $collectionAmount = null)
	{
		$loan = DB::table('mfn_loan')
			->where('id', $loanId)
			->select('id', 'totalRepayAmount', 'loanAmount')
			->first();

		if ($collectionAmount == null) {
			$collectionAmount = DB::table('mfn_loan_collection')->where('softDel', 0)->where('loanIdFk', $loan->id)->sum('amount');
			$collectionAmount += DB::table('mfn_opening_balance_loan')->where('softDel', 0)->where('loanIdFk', $loan->id)->sum('paidLoanAmountOB');
			$collectionAmount += DB::table('mfn_loan_waivers')->where('softDel', 0)->where('loanIdFk', $loan->id)->sum('amount');
			$collectionAmount += DB::table('mfn_loan_write_off')->where('softDel', 0)->where('loanIdFk', $loan->id)->sum('amount');
			$collectionAmount += DB::table('mfn_loan_rebates')->where('softDel', 0)->where('loanIdFk', $loan->id)->sum('amount');
		}

		$schedules = DB::table('mfn_loan_schedule')
			->where('softDel', 0)
			->where('loanIdFk', $loan->id)
			->orderBy('installmentSl')
			->select('id', 'installmentAmount')
			->get();

		foreach ($schedules as $schedule) {
			if ($collectionAmount >= $schedule->installmentAmount) {
				DB::table('mfn_loan_schedule')->where('id', $schedule->id)->update(['isCompleted' => 1, 'isPartiallyPaid' => 0, 'partiallyPaidAmount' => 0]);
			} elseif ($collectionAmount > 0) {
				DB::table('mfn_loan_schedule')->where('id', $schedule->id)->update(['isCompleted' => 0, 'isPartiallyPaid' => 1, 'partiallyPaidAmount' => $collectionAmount]);
			} else {
				DB::table('mfn_loan_schedule')->where('id', $schedule->id)->update(['isCompleted' => 0, 'isPartiallyPaid' => 0, 'partiallyPaidAmount' => 0]);
			}

			$collectionAmount -= $schedule->installmentAmount;
		}
	}

	public function getFirstRepayDateForMonthlyLoan($samityId, $disbursementDate, $productId = null)
	{
		$weekMap = [
			1 => Carbon::SATURDAY, // SA
			2 => Carbon::SUNDAY, // SU
			3 => Carbon::MONDAY, // MO
			4 => Carbon::TUESDAY, // TU
			5 => Carbon::WEDNESDAY, // WE
			6 => Carbon::THURSDAY, // TH
			7 => Carbon::FRIDAY  // FR
		];

		$disbursementDate = Carbon::parse($disbursementDate);

		// FIRST REPAY DATE SHOULD AFTER THE GRACE PERIOD
		
		// GRACE PERIOD WOULD BE TAKEN FROM THE PRODCT CONFIGURATION, NOW IT IS TAKEN AS 30 DAYS
		$gracePeriodInDays = 30;
		$firstRepayDate = $disbursementDate->copy()->addDays($gracePeriodInDays);

		if ($disbursementDate->copy()->endOfMonth()->format('Y-m-d') == $firstRepayDate->copy()->endOfMonth()->format('Y-m-d')) {
			$firstRepayDate = $disbursementDate->copy()->addMonthNoOverflow();
		}

		// IF FRACE PERIOD IS 30 DAYS, FIRST REPAY DATE SHOULD BE INTO NEXT MONTH
		if ($gracePeriodInDays == 30 && $disbursementDate->copy()->addMonthNoOverflow()->endOfMonth()->format('Y-m-d') < $firstRepayDate->copy()->endOfMonth()->format('Y-m-d')) {
			$firstRepayDate = $disbursementDate->copy()->addMonthNoOverflow();
		}

		$monthFirstDate = $firstRepayDate->copy()->startOfMonth();
		$monthEndDate = $firstRepayDate->copy()->endOfMonth();

		$samity = DB::table('mfn_samity')->where('id', $samityId)->select('id', 'samityDayId')->first();
		$samityDayChange = DB::table('mfn_samity_day_change')->where('samityId', $samityId)->where('effectiveDate', '>', $firstRepayDate->format('Y-m-d'))->orderBy('effectiveDate')->first();

		$samityDayId = $samityDayChange != null ? $samityDayChange->samityDayId : $samity->samityDayId;

		// FIRST REPAY DATE SHOULD BE BETWEEN $monthFirstDate AND $monthEndDate DATE AND IT SHOULD BE INTO SAMITY DAY.
		if ($firstRepayDate->dayOfWeek != $firstRepayDate->copy()->next($weekMap[$samityDayId])->dayOfWeek && $firstRepayDate->copy()->next($weekMap[$samityDayId])->lte($monthEndDate)) {

			$samityDayChange = DB::table('mfn_samity_day_change')->where('samityId', $samityId)->where('effectiveDate', '>', $firstRepayDate->copy()->next($weekMap[$samityDayId])->format('Y-m-d'))->orderBy('effectiveDate')->first();
			$samityDayId = $samityDayChange != null ? $samityDayChange->samityDayId : $samity->samityDayId;

			$firstRepayDate->next($weekMap[$samityDayId]);
		} elseif ($firstRepayDate->dayOfWeek != $firstRepayDate->copy()->next($weekMap[$samityDayId])->dayOfWeek) {
			$firstRepayDate->subDays(7);

			$samityDayChange = DB::table('mfn_samity_day_change')->where('samityId', $samityId)->where('effectiveDate', '>', $firstRepayDate->copy()->next($weekMap[$samityDayId])->format('Y-m-d'))->orderBy('effectiveDate')->first();
			$samityDayId = $samityDayChange != null ? $samityDayChange->samityDayId : $samity->samityDayId;

			$firstRepayDate->next($weekMap[$samityDayId]);
		}
		return $firstRepayDate->format('Y-m-d');
	}

	public function shiftMothLyloanSchedule()
	{
		$weekMap = [
			1 => Carbon::SATURDAY, // SA
			2 => Carbon::SUNDAY, // SU
			3 => Carbon::MONDAY, // MO
			4 => Carbon::TUESDAY, // TU
			5 => Carbon::WEDNESDAY, // WE
			6 => Carbon::THURSDAY, // TH
			7 => Carbon::FRIDAY  // FR
		];

		$targetDate = '2019-09-10';
		$loanIds = [170150, 170153];

		$loans = DB::table('mfn_loan')->whereIn('id', $loanIds)->select('id', 'samityIdFk', 'firstRepayDate')->orderBy('samityIdFk')->get();

		$currentSamityId = 0;

		foreach ($loans as $loan) {

			if ($currentSamityId != $loan->samityIdFk) {
				$holidays = MicroFin::getHolidaysForSamity($loan->samityIdFk, $targetDate);
				$samityDayId = DB::table('mfn_samity')->where('id', $loan->samityIdFk)->first()->samityDayId;
			}

			$schedules = DB::table('mfn_loan_schedule')
				->where('loanIdFk', $loan->id)
				->where('scheduleDate', '>=', $targetDate)
				->orderBy('scheduleDate')
				->select('id', 'installmentSl', 'scheduleDate')
				->get();

			$lastScheduleSlNo = $schedules->max('installmentSl');

			foreach ($schedules as $schedule) {
				if ($schedule->installmentSl == $lastScheduleSlNo) {
					// SHIFT THE LAST SCHUDLE TO THE NEXT MONTH
					$firstReapayDateayNo = Carbon::parse($loan->firstRepayDate)->day;
					$firstReapayDateayNo = $firstReapayDateayNo == 31 ? 30 : $firstReapayDateayNo;

					$nextMonthScheduleDate = Carbon::parse($schedule->scheduleDate)->addMonthNoOverflow();
					$nextMonthScheduleDate = Carbon::parse($nextMonthScheduleDate->format('Y-m') . '-' . $firstReapayDateayNo);
					$nextMonthScheduleDate->setWeekStartsAt(Carbon::SATURDAY);
					$nextMonthScheduleDate->setWeekEndsAt(Carbon::FRIDAY);
					$nextMonthScheduleDate->startOfWeek();

					$monthFirstDate = $nextMonthScheduleDate->copy()->startOfMonth();
					$monthLastDate = $nextMonthScheduleDate->copy()->endOfMonth();

					if ($nextMonthScheduleDate->dayOfWeek != $nextMonthScheduleDate->copy()->next($weekMap[$samityDayId])->dayOfWeek || $nextMonthScheduleDate->lt($monthFirstDate)) {
						$nextMonthScheduleDate->next($weekMap[$samityDayId]);
					}
					if ($nextMonthScheduleDate->gt($monthLastDate)) {
						$nextMonthScheduleDate->subDays(7);
					}

					// IF THE SCHEDULE DATE IN HOLIDAY THEN FIND THE ALTERNATIVE SAMITY DAYS IN THIS MONTH
					if (in_array($nextMonthScheduleDate->format('Y-m-d'), $holidays)) {

						$alternativeDays = [];
						$monthFirstDateCopy = $monthFirstDate->copy();
						while ($monthFirstDateCopy->lte($monthLastDate)) {
							if ($monthFirstDateCopy->dayOfWeek == $nextMonthScheduleDate->dayOfWeek) {
								array_push($alternativeDays, $monthFirstDateCopy->format('Y-m-d'));
								$monthFirstDateCopy->addDays(7);
							} else {
								$monthFirstDateCopy->addDay();
							}
						}
						// REMOVE THE HOLIDAYS
						$alternativeDays = array_diff($alternativeDays, $holidays);

						// IF ANY ALTERNATIVE DAY FOUND, THEN FIRST TRY TO ASSIGN THE NEXT CLOSET DATE, IF NEXT DATES ARE NOT AVAILABE THEN ASSIGN THE PREVIOUS NEARREST DAY.
						if (count($alternativeDays) > 0) {

							$nextAlternativeDays = array_filter($alternativeDays, function ($value) use ($nextMonthScheduleDate) {
								return $value > $nextMonthScheduleDate->format('Y-m-d');
							});

							if (count($nextAlternativeDays) > 0) {
								$nextMonthScheduleDate = Carbon::parse(min($nextAlternativeDays));
							} else {
								$previousAlternativeDays = array_filter($alternativeDays, function ($value) use ($nextMonthScheduleDate) {
									return $value < $nextMonthScheduleDate->format('Y-m-d');
								});
								$nextMonthScheduleDate = Carbon::parse(max($previousAlternativeDays));
							}
						}
						// IF NO ALTERNATIVE DAY FOUND THEN ASSIGN TO ANY DAY AFTER OR BEFORE SCHEDULE DATE.
						else {
							$isScheduleAssigned = 0;
							$nextMonthScheduleDateCopy = $nextMonthScheduleDate->copy()->addDay();
							while ($nextMonthScheduleDateCopy->lte($monthLastDate)) {
								if (!in_array($nextMonthScheduleDateCopy->format('Y-m-d'), $holidays)) {
									$nextMonthScheduleDate = $nextMonthScheduleDateCopy;
									$isScheduleAssigned = 1;
									break;
								}
								$nextMonthScheduleDateCopy->addDay();
							}
							if ($isScheduleAssigned == 0) {
								$nextMonthScheduleDateCopy = $nextMonthScheduleDate->copy()->subDay();
								while ($nextMonthScheduleDateCopy->gte($monthFirstDate)) {
									if (!in_array($nextMonthScheduleDateCopy->format('Y-m-d'), $holidays)) {
										$nextMonthScheduleDate = $nextMonthScheduleDateCopy;
										break;
									}
									$nextMonthScheduleDateCopy->subDay();
								}
							}
						}
					}

					echo $nextMonthScheduleDate->format('Y-m-d').'<br>';
					// DB::table('mfn_loan_schedule')->where('id', $schedule->id)->update(['scheduleDate' => $nextMonthScheduleDate->format('Y-m-d')]);
				} else {
					// ASSIGN THE NEXT SCHEDULE TO THIS SCHEDULE DATE
					$nextScheduleDate = $schedules->where('installmentSl', $schedule->installmentSl + 1)->max('scheduleDate');
					echo $nextScheduleDate . '<br>';
					// DB::table('mfn_loan_schedule')->where('id', $schedule->id)->update(['scheduleDate' => $nextScheduleDate]);
				}
			}
		}
	}
}
