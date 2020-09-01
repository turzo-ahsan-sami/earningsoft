<?php
	
	namespace App\Http\Controllers\microfin\member;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\member\MfnMemberPrimaryProductTransfer;
	use App\microfin\member\MfnMemberInformation;
	use App\microfin\savings\MfnSavingsDeposit; 
	use App\microfin\savings\MfnSavingsWithdraw;
	use Session;
	use Validator;
	use Response;
	use DB;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\Auth; 
	use App\Http\Controllers\Controller;
	use App\Traits\GetSoftwareDate;
	use App\Http\Controllers\microfin\MicroFinance;

	class MfnMemberPrimaryProductTransferController extends Controller {

		protected $MicroFinance;

		use GetSoftwareDate;
		
		private $TCN;

		public function __construct() {

			$this->MicroFinance = new MicroFinance;

			$this->TCN = array(
				array('SL No.', 70), 
				array('Member Name', 0),
				array('Old Product Name', 0),
				array('New Product Name', 0),
				array('Transfer Date', 120),				
				array('Entry By', 180),
				array('Action', 80)
			);	
		}
	
		public function index() {

			$TCN = $this->TCN;
		
			$damageData = array(
				'TCN' 	   					    =>	$TCN,
				'memberPrimaryProductTransfer'  =>  $this->MicroFinance->getMemberPrimaryProductTransfer(),
				'gender'   						=>  $this->MicroFinance->getGender(),
				'dataNotAvailable'				=>	$this->MicroFinance->dataNotAvailable(),
				'MicroFinance'      			=>  $this->MicroFinance
			);

			return view('microfin.member.memberPrimaryProductTransfer.viewMemberPrimaryProductTransfer', ['damageData' => $damageData]);
		}

		public function addMemberPrimaryProductTransfer() {

			$damageData = array(
				'member'  		=>  $this->MicroFinance->getMemberOptionsForLoan(Auth::user()->branchId),
				'transferDate'  =>  GetSoftwareDate::getSoftwareDate()
			);

			return view('microfin.member.memberPrimaryProductTransfer.addMemberPrimaryProductTransfer', ['damageData' => $damageData]);
		}

		public function loadPrimaryPrduct(Request $req) {

			$primaryProduct = $this->MicroFinance->getActiveLoanPrimaryProductOptions();
			
			//	GET CURRENT PRIMARY PRDUCT OF THE SELECTED MEMBER.
			$getCurrentPrimaryProduct = $this->MicroFinance->getMultipleValueForId($table='mfn_member_information', $req->memberId, ['samityId', 'primaryProductId']);

			//	CUT THE CURRENT PRIMARY PRODUCT FROM THE PRODUCT LIST.
			foreach($primaryProduct as $key => $val):
				if($key==$getCurrentPrimaryProduct->primaryProductId):
					unset($primaryProduct[$key]);
				endif;
			endforeach;
			
			//	GET ALL THE SAVINGS ACCOUNT OF A MEMBER.
			$savingsAccount = $this->MicroFinance->getSavingsAccountNumberPerMember($req->memberId);

			$i = 0;
			$savingsSummary = [];
			
			//	GET ALL THE DETAILS OF ALL THE SAVINGS ACCOUNT OF A MEMBER.
			foreach($savingsAccount as $savingsAcc):
				$savingsSummary[$i]['savingsCode'] = $savingsAcc['savingsCode'];
				$savingsSummary[$i]['deposit'] = $this->MicroFinance->getSavingsDepositPerAccount($savingsAcc['id']);
				$savingsSummary[$i]['withdraw'] = $this->MicroFinance->getSavingsWithdrawPerAccount($savingsAcc['id']);
				$savingsSummary[$i]['balance'] = $savingsSummary[$i]['deposit'] - $savingsSummary[$i]['withdraw'];
				$i++;
			endforeach;

			$data = array(
				'primaryProduct'      =>  $primaryProduct,
				'memberName'          =>  $this->MicroFinance->getMemberNameWithCode($req->memberId),
				'samityName'          =>  $this->MicroFinance->getSamityNameWithCode($getCurrentPrimaryProduct->samityId),
				'currentProductName'  =>  $this->MicroFinance->getNameValueForId($table='mfn_loans_product', $getCurrentPrimaryProduct->primaryProductId),
				'savingsSummary'      =>  $savingsSummary,
				'savingsAccount'	  =>  $savingsAccount,
			);
			
			return response::json($data); 
		}

		public function addItem(Request $req) {

			$rules = array(
				'memberIdFk'		   =>  'required',
				'newPrimaryProductFk'  =>  'required',
			);

			$attributesNames = array(
				'memberIdFk'  =>  'member name'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				/*
				|--------------------------------------------------------------------------
				| ADD     : MEMBER PRIMARY PRODUCT TRANSFER
				|--------------------------------------------------------------------------
				| ADD     : SAVINGS WITHDRAW
				|--------------------------------------------------------------------------
				| ADD     : SAVINGS DEPOSIT 
				|--------------------------------------------------------------------------
				| UPDATE  : MEMBER INFORMATION
				|--------------------------------------------------------------------------
				|           primaryProductId
				|--------------------------------------------------------------------------
				*/

				$transaction = [];
				
				//	CHECK IF THERE ARE ANY TRANSACTION WHICH DATES ARE GREATER THAN SOFTWARE DATE IN
				//	SAVINGS DEPOSIT, SAVINGS WITHDRAW AND LOAN COLLECTION.
				$transaction[] = $this->MicroFinance->getSavingsDepositExistAfterSoftwareDate($req->memberIdFk);
				$transaction[] = $this->MicroFinance->getSavingsWithdrawExistAfterSoftwareDate($req->memberIdFk);
				$transaction[] = $this->MicroFinance->getLoanCollectionExistAfterSoftwareDate($req->memberIdFk);

				$transactionClear = array_sum($transaction)==0?1:0;

				//	CHECK IF THERE IS ANOTHER PRODUCT TRANSFER IN A SNGLE DAY.
				$productTransferExists = $this->MicroFinance->getCheckAnotherProductTransferExists($req->memberIdFk);
				$productTransferClear = $productTransferExists==0?1:0;

				if($transactionClear==1 && $productTransferClear==1): 
					$transferStatus = 1;
				else: 
					$transferStatus = 0;
				endif;

				if($transferStatus==1):
					$now = Carbon::now();
					$req->request->add(['createdDate' => $now]);
					$req->request->add(['branchIdFk' => Auth::user()->branchId]);
					$req->request->add(['entryBy' => Auth::user()->emp_id_fk]);
					$req->request->add(['transferDate' => $this->MicroFinance->getDBDateFormat($req->transferDate)]);

					// GET SAMITY ID OF THE MEMBER.
					$memberOB = $this->MicroFinance->getMultipleValueForId($table='mfn_member_information', $req->memberIdFk, ['samityId', 'primaryProductId']);
					$req->request->add(['oldPrimaryProductFk' => $memberOB->primaryProductId]);
					$req->request->add(['samityIdFk' => $memberOB->samityId]);

					//	GET ALL THE SAVINGS ACCOUNT OF A MEMBER.
					$savingsAccount = $this->MicroFinance->getSavingsAccountNumberPerMember($req->memberIdFk);

					$i = 0;
					$savingsSummary = [];
					$totalTransferAmount = 0;
					
					//	GET ALL THE DETAILS OF ALL THE SAVINGS ACCOUNT OF A MEMBER.
					foreach($savingsAccount as $savingsAcc):
						$savingsSummary[$i]['id'] = $savingsAcc['id'];
						$savingsSummary[$i]['savingsProductId'] = $savingsAcc['savingsProductIdFk']; 
						$savingsSummary[$i]['deposit'] = $this->MicroFinance->getSavingsDepositPerAccount($savingsAcc['id']);
						$savingsSummary[$i]['withdraw'] = $this->MicroFinance->getSavingsWithdrawPerAccount($savingsAcc['id']);
						$savingsSummary[$i]['balance'] = $savingsSummary[$i]['deposit'] - $savingsSummary[$i]['withdraw'];
						$totalTransferAmount +=  $savingsSummary[$i]['balance'];
						$i++;
					endforeach;

					$req->request->add(['totalTransferAmount' => $totalTransferAmount]);
					$req->request->add(['savingsRecord' => $savingsSummary]);
					$create = MfnMemberPrimaryProductTransfer::create($req->all());

					$accountsBalanceArr = [];
					$i = 0;

					//	INSERT DATA INTO SAVINGS WITHDRAW TABLE.
					foreach($savingsAccount as $savingsAcc):
						$balance = $this->MicroFinance->getSavingsDepositPerAccount($savingsAcc['id']) - $this->MicroFinance->getSavingsWithdrawPerAccount($savingsAcc['id']);

						$withdraw = New MfnSavingsWithdraw;
						$withdraw->memberIdFk 		    =  $req->memberIdFk;
						$withdraw->branchIdFk 		    =  $savingsAcc['branchIdFk'];
						$withdraw->samityIdFk   		=  $savingsAcc['samityIdFk'];
						$withdraw->accountIdFk 		    =  $savingsAcc['id'];
						$withdraw->productIdFk 		    =  $savingsAcc['savingsProductIdFk'];
						$withdraw->primaryProductIdFk   =  $memberOB->primaryProductId;
						$withdraw->amount 			    =  $balance;
						$withdraw->withdrawDate 		=  GetSoftwareDate::getSoftwareDate();
						$withdraw->entryByEmployeeIdFk  =  Auth::user()->emp_id_fk;
						$withdraw->isAuthorized         =  1;
						$withdraw->isTransferred        =  1;
						$withdraw->createdAt 		    =  Carbon::now();
						$withdraw->save();

						$accountsBalanceArr[$savingsAcc['id']] = $balance;
						$i++;
					endforeach;
					
					//	INSERT DATA INTO SAVINGS DEPOSIT TABLE.
					foreach($savingsAccount as $savingsAcc):
						$deposit = New MfnSavingsDeposit;
						$deposit->accountIdFk 		   =  $savingsAcc['id'];
						$deposit->productIdFk 		   =  $savingsAcc['savingsProductIdFk'];
						$deposit->primaryProductIdFk   =  $req->newPrimaryProductFk;
						$deposit->memberIdFk 		   =  $req->memberIdFk;
						$deposit->branchIdFk 		   =  $savingsAcc['branchIdFk'];
						$deposit->samityIdFk 		   =  $savingsAcc['samityIdFk'];
						$deposit->amount 			   =  $accountsBalanceArr[$savingsAcc['id']];
						$deposit->depositDate 		   =  GetSoftwareDate::getSoftwareDate();
						$deposit->entryByEmployeeIdFk  =  Auth::user()->emp_id_fk;
						$deposit->isAuthorized         =  1;
						$deposit->isTransferred        =  1;
						$deposit->createdAt 		   =  Carbon::now();
						$deposit->save();
					endforeach;

					//	UPDATE PRIMARY PRODUCT ID IN mfn_samity TABLE.
					$member = MfnMemberInformation::find($req->memberIdFk);
					$member->primaryProductId = $req->newPrimaryProductFk;
					$member->save();
				endif;

				$data = array(
					'responseTitle'  =>  $transferStatus==1?MicroFinance::getMessage('msgSuccess'):MicroFinance::getMessage('msgWarning'),
					'responseText'   =>  $transferStatus==1?MicroFinance::getMessage('primaryProductTransferSuccess'):MicroFinance::getMessage('primaryProductTransferWarning'),
				);
								
				return response::json($data);
			}
		}

		public function detailsMemberPrimaryProductTransfer($memberPrimaryProductTransferId) {

			$primaryProductDetailsTCN = array(
				'oldProductInfo'	  =>  'Old Product Info',
				'newProductInfo'	  =>  'New Product Info',
				'memberName'	      =>  'Member Name',
				'memberCode'	      =>  'Member Code',
				'branchName'	      =>  'Branch Name',
				'samityName'	      =>  'Samity Name',
				'primaryProductName'  =>  'Primary Product Name',
				'transferDate'  	  =>  'Transfer Date'
			);

			$savingsDetailsTCN = array(
				'saving'  	  =>  'Saving',
				'savingCode'  =>  'Saving Code',
				'deposite'	  =>  'Deposite',
				'withdraw'	  =>  'Withdraw',
				'balance'	  =>  'Balance'
			);

			$memberPrimaryProductTransferDetails = $this->MicroFinance->getMemberPrimaryProductTransferDetails($memberPrimaryProductTransferId);
			$getMemberDetails = $this->MicroFinance->getMultipleValueForId($table='mfn_member_information', $memberPrimaryProductTransferDetails->memberIdFk, ['name', 'code', 'branchId', 'samityId', 'primaryProductId']);
			$oldProductName = $this->MicroFinance->getMultipleValueForId($table='mfn_loans_product', $memberPrimaryProductTransferDetails->oldPrimaryProductFk, ['name']);
			$newProductName = $this->MicroFinance->getMultipleValueForId($table='mfn_loans_product', $memberPrimaryProductTransferDetails->newPrimaryProductFk, ['name']);

			$damageData = array(
				'primaryProductDetailsTCN'  		   =>  $primaryProductDetailsTCN,
				'savingsDetailsTCN'  				   =>  $savingsDetailsTCN,
				'memberDetails'						   =>  $getMemberDetails,
				'branchName'						   =>  $this->MicroFinance->getNameValueForId($table='gnr_branch', $getMemberDetails->branchId),
				'samityName'						   =>  $this->MicroFinance->getNameValueForId($table='mfn_samity', $getMemberDetails->samityId),
				'oldProductName'					   =>  $oldProductName->name,
				'newProductName'					   =>  $newProductName->name,
				'memberPrimaryProductTransferDetails'  =>  $memberPrimaryProductTransferDetails,
				'savingsDetails'  					   =>  json_decode($memberPrimaryProductTransferDetails->savingsRecord),
				'MicroFinance'        				   =>  $this->MicroFinance,
			);
			
			return view('microfin.member.memberPrimaryProductTransfer.detailsMemberPrimaryProductTransfer', ['damageData' => $damageData]);
		}
	}