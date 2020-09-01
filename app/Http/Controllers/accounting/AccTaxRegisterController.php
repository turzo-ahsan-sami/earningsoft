<?php
namespace App\Http\Controllers\accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\accounting\AccTaxGenerateRegister;
use App\accounting\AccTaxPayment;
use App\accounting\AccTaxPaymentDetails;
use App\accounting\AccTaxBillTypeRegisterHistory;
use App\accounting\AccTaxBillTypeRegister;
use Validator;

use Illuminate\Support\Facades\Input;
use Response;
use App\Http\Controllers\gnr\Service;
use Carbon\Carbon;
use DB;
use Auth;


class AccTaxRegisterController extends Controller
{
	public function index(){


	}
	public function taxBillType()
	{

		return view('accounting.register.taxRegister.taxBillType');
	}
	public function accViewTaxBillType()
	{

		$viewVatBillTypes= DB::table('acc_tax_bill_type')

		->select('*')
		->where('Type','=',1)
		->paginate(10);
		$viewVatBillTypesArr=array(
			'viewVatBillTypes' => $viewVatBillTypes
		);

		return view('accounting.register.taxRegister.viewTaxBillType',$viewVatBillTypesArr);
	}

	public function addTaxBillType(Request $request)
	{
		$bill=	new AccTaxBillTypeRegister;
		$billHistory=	new AccTaxBillTypeRegisterHistory;
		$bill->serviceName=$request->billTypeName;
		$bill->serviceCode=$request->taxCode;

		$bill->taxRate=$request->taxRate;

		$bill->activeFrom=Carbon::parse($request->activationDate);
		$bill->Type=1;
		$bill->save();


		$billHistory->billTypeIdFk=$bill->id;
		$billHistory->taxRate=$request->taxRate;

		$billHistory->activatedFrom=Carbon::parse($request->activationDate);
		$billHistory->status=1;
		$billHistory->createdAt=Carbon::now();
		$billHistory->save();
		$logArray = array(
			'moduleId'  => 4,
			'controllerName'  => 'AccTaxRegisterController',
			'tableName'  => 'acc_tax_bill_type',
			'operation'  => 'insert',
			'primaryIds'  => [DB::table('acc_tax_bill_type')->max('id')]
		);
		Service::createLog($logArray);
		return response::json('success');
	}
	public function accViewTaxBillTypeModal(Request $request)
	{
		$taxBillTypeId=$request->taxBillTypeId;
						 $viewVatBillTypes= DB::table('acc_tax_bill_type')// this will get tax but the var name kept as vat since it will inluence the view file
						 ->select('*')
						 ->where('id','=',$taxBillTypeId)
						 ->first();
				//   $viewVatBillTypes->activeFromGeneral=Carbon::parse($viewVatBillTypes->activeFrom)->format('d-m-Y');
						 $viewVatBillTypes->activeFrom=Carbon::parse($viewVatBillTypes->activeFrom)->format('d-m-Y');
						 $viewVatBillTypes->activeFromMin=Carbon::parse($viewVatBillTypes->activeFrom)->addDay()->format('Y-m-d');
						 $viewVatBillTypesArr=array(
						 	'viewVatBillTypes' => $viewVatBillTypes


						 );
						 return response()->json($viewVatBillTypesArr);


						}
						public function accViewTaxBillTypeModalUpdate(Request $request)
						{
							$request->activeFrom=Carbon::parse($request->activeFrom);
							$billTypeUpdate=DB::table('acc_tax_bill_type')
							->where('id',$request->supplierId)
							->update(['serviceName' => $request->billName,'serviceCode' => $request->billCode,'taxRate' => $request->taxRate,'activeFrom' => $request->activeFrom]);

							$billTypeHistoryUpdate=DB::table('acc_tax_bill_type_history')
							->where('billTypeIdFk',$request->supplierId)
							->update(['status' => 0,'activatedTo' => $request->activeFrom]);
							$previousdata = AccTaxBillTypeRegister::find ($request->supplierId);
							$billHistory=	new AccTaxBillTypeRegisterHistory;
							$billHistory->billTypeIdFk=$request->supplierId;
							$billHistory->taxRate=$request->taxRate;
							$billHistory->activatedFrom=Carbon::parse($request->activeFrom);
							$billHistory->status=1;
							$billHistory->createdAt=Carbon::now();
							$billHistory->save();
							$logArray = array(
								'moduleId'  => 4,
								'controllerName'  => 'AccTaxRegisterController',
								'tableName'  => 'acc_tax_bill_type',
								'operation'  => 'update',
								'previousData'  => $previousdata,
								'primaryIds'  => [$previousdata->id]
							);
							Service::createLog($logArray);



							return response::json('success');
						}

						public function accTaxRegisterForm()
						{
							$supplierLists = DB::table('gnr_supplier')
							->select('id','name')
							->get();
							$billTypes      = DB::table('acc_tax_bill_type')
							->select('id','serviceName')
							->get();
							$projects       = DB::table('gnr_project')
							->select('id','name')
							->get();

							$filteringArr= array(
								'supplierLists'    => $supplierLists,
								'billTypes'       => $billTypes,
								'projects'        => $projects
							);
							return view('accounting.register.taxRegister.accTaxRegisterForm',$filteringArr);
						}

						public function taxCalculationFromBillType(Request $request)
						{
							$billType=$request->billType;
							$taxAmount= DB::table('acc_tax_bill_type')
							->select('taxRate')
							->where('id',$billType)
							->get();

							return response()->json($taxAmount);

						}


						public function accTaxProjectTypeFiltering(Request $request)
						{
							$projectId         = (int)$request->projectId;
							$projectType= DB::table('gnr_project_type')
							->join('gnr_project','gnr_project_type.projectId','=','gnr_project.id')
							->select('gnr_project_type.id','gnr_project_type.name','gnr_project.projectCode')
							->where('projectId',$projectId )
							->get();

							return response()->json($projectType);
						}

						public function accTaxRegisterBillNoGenerate(Request $request)
						{
							$billNos= DB::table('acc_tax_generate')
							->select('supplierIdFk')
							->where('supplierIdFk',$request->supplier)
							->get();
							$count=0;
							foreach ($billNos as $billno) {
								$count++;
							}
							if($count>=10)
							{
								$count++;
								$suffix="000".$count;
							}
							elseif($count>=100)
								{$count++;
									$suffix="00".$count;
								}
								elseif($count>=1000)
									{$count++;
										$suffix="0".$count;
									}
									elseif($count>=10000)
										{$count++;
											$suffix=$count;
										}
										elseif($count>0){
											$count++;
											$suffix="0000".$count;
										}
										else{
											$suffix="00001";
										}

										return response()->json($suffix);
									}

									public function accTaxRegisterBillNoGenerateProjectType(Request $request)
									{

										$billNos= DB::table('gnr_project_type')
									// ->join('gnr_project','gnr_project_type.projectId','=','gnr_project.id')
										->select('gnr_project_type.projectTypeCode')
										->where('gnr_project_type.id',$request->projectType)
										->first();

										$midfix=$billNos->projectTypeCode;
										return response()->json($midfix);

									}



									public function accAddTaxregister(Request $request)
									{
										$addTaxRegister = new AccTaxGenerateRegister;
										$addTaxRegister->supplierIdFk=$request->supplier;

										$addTaxRegister->billNo=$request->billNo;

										$addTaxRegister->billDate=Carbon::parse($request->billDate);
										$addTaxRegister->projectId_Fk=$request->projects;
										$addTaxRegister->project_TypeId_Fk=$request->projectType;

										$addTaxRegister->billTypeIdFk=$request->billType;
										$addTaxRegister->taxInterestRate=$request->tax;
										$addTaxRegister->billAmount=$request->billAmount;
										$addTaxRegister->taxAmount=$request->total;

										$addTaxRegister->voucherNo=$request->voucherNo;

										$addTaxRegister->voucherDate=Carbon::parse($request->voucherDate);
										$addTaxRegister->entryByEmpIdFk=Auth::user()->emp_id_fk;

										$addTaxRegister->createdAt=Carbon::now();
										$addTaxRegister->save();
										return response::json('success');


									}

									public function accViewTaxRegister()
									{
										$viewTaxRegisters= DB::table('acc_tax_generate as t1')
										->join('gnr_supplier as t2','t1.supplierIdFk','=','t2.id')
										->join('acc_tax_bill_type as t3','t1.billTypeIdFk','=','t3.id')
										->select('t1.*','t2.name','t3.serviceName')
										->where('t1.softDel',0)
										->orderBy('t1.voucherDate')
										->paginate(10);
										$accountNo =array("--Select Acccount--")+DB::table('acc_account_ledger')->where('accountTypeId',5)->where('id','!=',350)->pluck('name','id')->toArray();
										$viewTaxRegisterArr=array(
											'viewTaxRegisters' => $viewTaxRegisters,
											'accountNo'        =>$accountNo

										);



										return view('accounting.register.taxRegister.viewTaxRegister',$viewTaxRegisterArr);
									}



									public function accViewTaxRegisterPayTaxBillNoGenerate(Request $request)
									{
										$billNos= DB::table('acc_tax_payment')
										->select('id')
										->get();
										$count=0;
										foreach ($billNos as $billno) {
											$count++;
										}
										if($count>=10)
										{
											$count++;
											$suffix="000".$count;
										}
										elseif($count>=100)
											{$count++;
												$suffix="00".$count;
											}
											elseif($count>=1000)
												{$count++;
													$suffix="0".$count;
												}
												elseif($count>=10000)
													{$count++;
														$suffix=$count;
													}
													elseif($count>0){
														$count++;
														$suffix="0000".$count;
													}
													else{
														$suffix="00001";
													}

													return response()->json($suffix);

												}



												public function accViewTaxRegisterPayTax(Request $request)
												{
													$taxPayment=	new AccTaxPayment;
													$taxPaymentDetails=	new AccTaxPaymentDetails;
													$taxPayment->paymentId=$request->paymentId;
													$taxPayment->paymentDate=Carbon::parse($request->paymentDate);
													$taxPayment->amount=$request->taxAmount;
													$taxPayment->bankName=$request->depositBank;
													$taxPayment->chalanNo=$request->chalanNo;
													$taxPayment->paymentType=$request->paymentType;
			//$taxPayment->ledgerIdFk=$request->vatRate;
													$taxPayment->chequeNumber=$request->chequeNo;

													$taxPayment->createdAt=Carbon::now();
													$taxPayment->save();

													$statusId =$request->taxId;
													$status= DB::table('acc_tax_generate')
													->where('id',$statusId)
													->update(['status' => 1]);



													$taxPaymentDetails->paymentIdFk=$taxPayment->id;
													$taxPaymentDetails->taxIdFk=$request->taxId;

													$query=	DB::table('acc_tax_generate')
													->select('billNo','taxAmount')
													->where('id',$statusId)
													->first();
													$taxPaymentDetails->billNo=$query->billNo;
													$taxPaymentDetails->taxAmount=$query->taxAmount;
													$taxPaymentDetails->save();



													return response::json('success');
												}
												public function accViewTaxRegisterViewModal(Request $request)
												{
													$viewModal= $request->viewModalId;
													$viewModalResult= DB::table('acc_tax_generate as t1')
													->join('gnr_supplier as t2','t1.supplierIdFk','=','t2.id')
													->join('gnr_project as t3','t1.projectId_Fk','=','t3.id')
													->join( 'acc_tax_bill_type as t4','t1.billTypeIdFk','=','t4.id')
													->join('hr_emp_general_info as t5','t1.entryByEmpIdFk','=','t5.id')
														//->join('gnr_project_type as t4','t1.project_TypeId_Fk','=','t4.id')
														//->select('t1.*','t2.name','t3.name as projectName','t4.name as serviceName ')
													->select('t1.*','t2.name','t3.name as projectName','t4.serviceName','t5.emp_name_english')
													->where('t1.id',$viewModal)
													->first();
													$viewModalResult->billDate=Carbon::parse($viewModalResult->billDate)->format('d-m-Y');
													return response()->json($viewModalResult);

												}

												public function accViewTaxRegisterUpdateAjax(Request $request)
		{ //$hello=$request->test;
			$ajaxBiilType= DB::table('acc_tax_bill_type')
			->select('serviceName','id')
			->get();

			return response()->json($ajaxBiilType);


		}

		public function accViewTaxRegisterUpdateGetAjax(Request $request)
		{
			$ajaxId= $request->taxBillTypeIdeditModalHidden;
			$ajaxResult= DB::table('acc_tax_generate as t1')
			->join('gnr_supplier as t2','t1.supplierIdFk','=','t2.id')
			->join('gnr_project as t3','t1.projectId_Fk','=','t3.id')
			->join('gnr_project_type as t4','t1.project_TypeId_Fk','=','t4.id')
			->select('t1.*','t2.name','t3.name as projectName','t4.name as projectTypeName')
			->where('t1.id',$ajaxId)
			->first();
			return response()->json($ajaxResult);

		}

		public function accViewTaxRegisterFullUpdate(Request $request)
		{
			$voucherDate= Carbon::parse($request->voucherDate);
			$billDate   = Carbon::parse($request->billDate);
			 // echo $request->billDate;
			 // echo $request->billType;
			 // echo $request->vatRate;
			 // echo $request->billAmount;
			$status= DB::table('acc_tax_generate')
			->where('id',$request->billTypeId)
			->update(['voucherDate' => $voucherDate,'billDate' => $billDate,'taxInterestRate' => $request->taxRate, 'billTypeIdFk' => $request->billType,'billAmount' => $request->billAmount, 'taxAmount'=> $request->taxAmount ]);

			return response::json('success');
		}

		public function accViewTaxRegisterDeleteModal(Request $request)
		{
			$softDelete=DB::table('acc_tax_generate')
			->where('id',$request->DeleteModalId)
			->update(['softDel' => 1]);
			return response::json('success');

		}


		public function accTaxRegisterPaymentList()
		{
			$paymentLists =DB::table('acc_tax_payment as t1')
			->join('acc_tax_payment_details as t2','t1.id','=','t2.paymentIdFk')
			->select('t1.*','t2.billNo','t2.taxAmount')
			->get();

			$viewPaymentList=array(
				'paymentLists' => $paymentLists
			);

			return view('accounting.register.taxRegister.viewTaxPaymentList',$viewPaymentList);

		}





	}
	?>
