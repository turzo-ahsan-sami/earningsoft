<?php

	namespace App\Http\Controllers\microfin\loan;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\loan\MfnLoan;
	use App\microfin\loan\MfnLoanSchedule;
	use App\microfin\loan\MfnLoanReschedule;
	use Session;
	use Validator;
	use Response;
	use DB;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\Auth;
	use App\Http\Controllers\Controller;
	use App\Http\Controllers\microfin\MicroFinance;


	class MfnLoanRescheduleController extends Controller {

		protected $MicroFinance;

		public function __construct() {

			$this->MicroFinance = New MicroFinance;

			$this->TCN = array(
				array('SL', 50), 
				array('Customized Loan No', 0),
				array('Loan Amount', 120),
				array('Installment No', 130),
				array('Rescheduled Date', 140),
				array('Rescheduled From', 150),
				array('Rescheduled To', 130),
				array('Entry By', 180),
				array('Action', 80)
			);	
		}

		public function index() {

			$damageData = array(
				'TCN'               =>  $this->TCN,
				'loanReschedule'    =>  $this->MicroFinance->getActiveLoanReschedule(), 
				'dataNotAvailable'	=>	$this->MicroFinance->dataNotAvailable(),
				'MicroFinance'      =>  $this->MicroFinance
			);

			return view('microfin.loan.loanReschedule.viewLoanReschedule', ['damageData' => $damageData]);
		}

		public function addReschedule($loanId, $installmentSl) {

			$installmentDetails = $this->MicroFinance->getMultipleValueForIdForLoanSchedule($table='mfn_loan_schedule', $loanId, $installmentSl, ['id', 'loanIdFk', 'loanTypeId', 'installmentSl', 'scheduleDate']);

			$loanOB = $this->MicroFinance->getMultipleValueForId($table='mfn_loan', $loanId, ['loanCode', 'memberIdFk', 'samityIdFk', 'branchIdFk', 'repaymentFrequencyIdFk']);
			
			$memberCode = $this->MicroFinance->getMemberNameWithCode($loanOB->memberIdFk);
			$samityOB = $this->MicroFinance->getMultipleValueForId($table='mfn_samity', $loanOB->samityIdFk, ['samityDayId']);

			//	GET HOLIDAY.
			$globalGovtHoliday = $this->MicroFinance->getGlobalGovtHoliday();
			$organizationHoliday = $this->MicroFinance->getOrganizationHoliday(1);
			$branchHoliday = $this->MicroFinance->getBranchHoliday();
			$samityHoliday = $this->MicroFinance->getSamityHoliday($loanOB->memberIdFk);
			$holiday = array_unique(array_merge($globalGovtHoliday, $organizationHoliday, $branchHoliday, $samityHoliday));
			
			//	MAKING THE HOLIDAY INTO AN ARRAY.
			$finalHoliday = [];
			
			foreach($holiday as $hDay):
				$finalHoliday[] = $this->MicroFinance->getMicroFinanceDateFormatWithoutLeadingZero($hDay);
			endforeach;

			$damageData = [
				'installmentDetails'    =>  $installmentDetails,
				'loanCode'		        =>  $loanOB->loanCode,
				'memberId'		        =>  $loanOB->memberIdFk,	
				'memberCode'		    =>  $memberCode,	
				'branchId'		        =>  $loanOB->branchIdFk,	
				'samityId'		        =>  $loanOB->samityIdFk,	
				'repaymentFrequencyId'  =>  $loanOB->repaymentFrequencyIdFk,	
				'samityDayId'		    =>  $samityOB->samityDayId,
				'samityDay'		  	    =>  $this->MicroFinance->getSamityDayNameValue($samityOB->samityDayId),
				'holiday'				=>  $finalHoliday,
				'MicroFinance'          =>  $this->MicroFinance
			];

			return view('microfin.loan.loanReschedule.addLoanReschedule', ['damageData' => $damageData]);
		}

		public function rescheduleGenerate(Request $req) {
			
			DB::beginTransaction();
			try{
				$scheduleDateOB = $this->MicroFinance->getScheduleDateToChange($req->loanId, $req->installmentNumber);

				$installmentId = [];

				$i = 0;
				foreach($scheduleDateOB as $scheduleDate):
					$installmentId[$i]['id'] = $scheduleDate->id;
					$installmentId[$i]['installmentNumber'] = $scheduleDate->installmentSl;
					$installmentId[$i]['scheduleDate'] = $scheduleDate->scheduleDate;
					$i++;
				endforeach;

				//	GET HOLIDAY.
				$holidayFound = 0;
				$scheduleDateArr = [];
				
				$globalGovtHoliday = $this->MicroFinance->getGlobalGovtHoliday();
				$organizationHoliday = $this->MicroFinance->getOrganizationHoliday(1);
				$branchHoliday = $this->MicroFinance->getBranchHoliday();
				$samityHoliday = $this->MicroFinance->getSamityHoliday($req->memberId);
				$holiday = array_unique(array_merge($globalGovtHoliday, $organizationHoliday, $branchHoliday, $samityHoliday));

				$repaymentFrequencyWiseRepayDate = [
					'1'	 =>  7,
					'2'  =>  28
				];

				//	HOLIDAY FILTERING.
				if($req->loanTypeId==1):
					for($i=0;$i<1000;$i++):
						$dayDiff = ($repaymentFrequencyWiseRepayDate[$req->repaymentFrequencyId] * $i) . 'days'; 
						$date = date_create($req->newScheduleDate);
						date_add($date, date_interval_create_from_date_string($dayDiff));
						
						//	PROPAGATE SCHEDULE DATE FOR WEEKLY FREQUENCY.
						if($req->repaymentFrequencyId==1):
							//	CHECK IF A DATE IS MATCHES TO HOLIDAY.
							foreach($holiday as $key => $val):
								if(date_create($val)>=$date):
									if(date_create($val)==$date):
										$holidayFound = 1;
										break;
									endif;
								endif;
							endforeach;

							if($holidayFound==0)
								$scheduleDateArr[] = date_format($date, "Y-m-d");
							
							$holidayFound = 0;
						endif;

						//	PROPAGATE SCHEDULE DATE FOR MONTHLY FREQUENCY.
						if($req->repaymentFrequencyId==2):
							$dayDiff = ($repaymentFrequencyWiseRepayDate[$req->repaymentFrequencyId] * $i) . 'days'; 
							$date = date_create($req->newScheduleDate);
							date_add($date, date_interval_create_from_date_string($dayDiff));
							
							$tos = Carbon::parse($req->newScheduleDate);
							$sot = $tos->addMonths($i)->toDateString();

							if($i==0)
								$targetDate = date_format($date, "Y-m-d");
							else
								$targetDate = $this->MicroFinance->getMonthlyLoanScheduleDateFilter($sot, $req->memberId);
							
							$originalMD = Carbon::parse($targetDate);
							$MD = Carbon::parse($targetDate);
							$targetDate = $MD->toDateString();

							//	CHECK IF A DATE IS MATCHES TO HOLIDAY, THEN SAMITY DATE SET TO NEXT WEEK.
							for($j=0;$j<100;$j++):
								if(in_array($targetDate, $holiday)):
									$targetDate = $MD->addDays(7)->toDateString();
									
									if($targetDate>$originalMD->endOfMonth()):
										$targetDate = $MD->subDays(14)->toDateString();
									else:
										if(in_array($targetDate, $holiday)):
											$targetDate = $MD->addDays(7)->toDateString();
											
											if($targetDate>$originalMD->endOfMonth()):
												$targetDate = $MD->subDays(21)->toDateString();
											endif;
										else:
											break;
										endif;
									endif;
								else:
									break;
								endif;
							endfor;

							$scheduleDateArr[] = $targetDate;
						endif;

						if(count($scheduleDateArr)==count($installmentId))
							break;
					endfor;
				endif;
				
				//	UPDATE PREVIOUS SCHEDULE DATE FROM A SPECIFIC INSTALLMENT NUMBER FOR RESCHEDULE.
				for($i=0;$i<count($installmentId);$i++):
					if($req->loanTypeId==1)
						$newScheduleDate = $scheduleDateArr[$i];
					if($req->loanTypeId==2)
						$newScheduleDate = $req->newScheduleDate;

					$schedule = MfnLoanSchedule::find($installmentId[$i]['id']);
					$schedule->scheduleDate = $this->MicroFinance->getDBDateFormat($newScheduleDate);
					$schedule->save();
					
					//if($req->repaymentFrequencyId==2)
					//	break;
				endfor;

				//	GENERATE LOAN RESCHEDULE.
				for($i=0;$i<count($installmentId);$i++):
					if($req->loanTypeId==1)
						$newScheduleDate = $scheduleDateArr[$i];
					if($req->loanTypeId==2)
						$newScheduleDate = $req->newScheduleDate;

					$req->request->add(['loanScheduleId' => $installmentId[$i]['id']]);
					$req->request->add(['loanIdFk' => $req->loanId]);
					$req->request->add(['loanTypeId' => $req->loanTypeId]);
					$req->request->add(['installmentNo' => $installmentId[$i]['installmentNumber']]);
					$req->request->add(['rescheduleFrom' => $installmentId[$i]['scheduleDate']]);
					$req->request->add(['rescheduleTo' => $this->MicroFinance->getDBDateFormat($newScheduleDate)]);
					$req->request->add(['rescheduleDate' => $this->MicroFinance->getDBDateFormat($newScheduleDate)]);
					$req->request->add(['rescheduleBy' => Auth::user()->emp_id_fk]);
					$req->request->add(['branchIdFk' => $req->branchId]); 
					$req->request->add(['samityIdFk' => $req->samityId]); 
					$req->request->add(['createdDate' => Carbon::now()]);
					$create = MfnLoanReschedule::create($req->all());

					//if($req->repaymentFrequencyId==2)
					//	break;
				endfor;

				// UPDATE LAST INSTALLMENT DATE IN LOAN TABLE.
				MfnLoan::where('id', $req->loanId)->update(['lastInstallmentDate' => end($scheduleDateArr)]);
				DB::commit();

				$data = array(
					'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
					'responseText'   =>  MicroFinance::getMessage('rescheduleCreateSuccess'),
					//'installmentId'  =>  $installmentId,
					//'holiday'		 =>  $holiday,
					'scheduleDateArr' => $scheduleDateArr
				);
					
				return response::json($data);
			}
			catch(\Exception $e){
					DB::rollback();
					$data = array(
						'responseTitle'  =>  'Warning!',
						'responseText'   =>  'Something went wrong. Please try again.'
	 				);
	 				return response::json($data);
				}
				
		}
	}