<?php
namespace App\Http\Controllers\accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\accounting\AccVatBillTypeRegister;
use App\accounting\AccVatPayment;
use App\accounting\AccVatPaymentDetails;
use App\accounting\AccVatBillTypeRegisterHistory;
use App\accounting\AccVatGenerateRegister;
use Validator;

use Illuminate\Support\Facades\Input;
use Response;
use App\Http\Controllers\gnr\Service;
use Carbon\Carbon;
use DB;
use Auth;


class AccVatRegisterController extends Controller
{
	public function index(){


	}


	public function vatRegister()
	{
		$supplierLists = DB::table('gnr_supplier')
		->select('id','name')
		->get();
		$billTypes      = DB::table('acc_vat_bill_type')
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
		return view('accounting.register.vatRegister.addVatRegister', $filteringArr);
	}
	public function vatCalculationFromBillType(Request $request)
	{
		$billType=$request->billType;
		$vatAmount= DB::table('acc_vat_bill_type')
		->select('vatRate')
		->where('id',$billType)
		->get();

		return response()->json($vatAmount);

	}




	public function vatBillType()
	{

		return view('accounting.register.vatRegister.vatBillType');
	}




	public function addVatBillType(Request $request)
	{
		$bill=	new AccVatBillTypeRegister;
		$billHistory=	new AccVatBillTypeRegisterHistory;
		$bill->serviceName=$request->billTypeName;
		$bill->serviceCode=$request->vatCode;

		$bill->vatRate=$request->vatRate;
		$bill->Type=0;

		$bill->activeFrom=Carbon::parse($request->activationDate);
		$bill->save();


		$billHistory->billTypeIdFk=$bill->id;
		$billHistory->vatRate=$request->vatRate;

		$billHistory->activatedFrom=Carbon::parse($request->activationDate);
		$billHistory->status=1;
		$billHistory->createdAt=Carbon::now();
		$billHistory->save();
		$logArray = array(
			'moduleId'  => 4,
			'controllerName'  => 'AccVatRegisterController',
			'tableName'  => 'acc_vat_bill_type',
			'operation'  => 'insert',
			'primaryIds'  => [DB::table('acc_vat_bill_type')->max('id')]
		);
		Service::createLog($logArray);
		return response::json('success');
	}


	public function accVatProjectTypeFiltering(Request $request)
	{
		$projectId         = (int)$request->projectId;
		$projectType= DB::table('gnr_project_type')
		->join('gnr_project','gnr_project_type.projectId','=','gnr_project.id')
		->select('gnr_project_type.id','gnr_project_type.name','gnr_project.projectCode')
		->where('projectId',$projectId )
		->get();

		return response()->json($projectType);
	}

	public function accAddVatregister(Request $request)
	{
		$addVatRegister = new AccVatGenerateRegister;
		$addVatRegister->supplierIdFk=$request->supplier;

		$addVatRegister->billNo=$request->billNo;

		$addVatRegister->billDate=Carbon::parse($request->billDate);
		$addVatRegister->projectId_Fk=$request->projects;
		$addVatRegister->project_TypeId_Fk=$request->projectType;

		$addVatRegister->billTypeIdFk=$request->billType;
		$addVatRegister->vatInterestRate=$request->vat;
		$addVatRegister->billAmount=$request->billAmount;
		$addVatRegister->vatAmount=$request->total;

		$addVatRegister->voucherNo=$request->voucherNo;

		$addVatRegister->voucherDate=Carbon::parse($request->voucherDate);
		$addVatRegister->entryByEmpIdFk=Auth::user()->emp_id_fk;
				//$addVatRegister->authorizedByEmpIdFk=$request->
		$addVatRegister->createdAt=Carbon::now();
		$addVatRegister->save();
		return response::json('success');


	}

	public function accVatRegisterBillNoGenerate(Request $request)
	{
		$billNos= DB::table('acc_vat_generate')
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





				public function accVatRegisterBillNoGenerateProjectType(Request $request)
				{

					$billNos= DB::table('gnr_project_type')
				            // ->join('gnr_project','gnr_project_type.projectId','=','gnr_project.id')
					->select('gnr_project_type.projectTypeCode')
					->where('gnr_project_type.id',$request->projectType)
					->first();

					$midfix=$billNos->projectTypeCode;
					return response()->json($midfix);

				}


				public function accViewVatRegister()
				{
					$viewVatRegisters= DB::table('acc_vat_generate as t1')
					->join('gnr_supplier as t2','t1.supplierIdFk','=','t2.id')
					->join('acc_vat_bill_type as t3','t1.billTypeIdFk','=','t3.id')
					->select('t1.*','t2.name','t3.serviceName')
					->orderBy('t1.voucherDate')
					->paginate(10);
					$accountNo =array("--Select Acccount--")+DB::table('acc_account_ledger')->where('accountTypeId',5)->where('id','!=',350)->pluck('name','id')->toArray();
					$viewVatRegisterArr=array(
						'viewVatRegisters' => $viewVatRegisters,
						'accountNo'        =>$accountNo

					);



					return view('accounting.register.vatRegister.viewVatRegister',$viewVatRegisterArr);
				}
				public function accViewVatBillType()
				{
					$viewVatBillTypes= DB::table('acc_vat_bill_type')

					->select('*')
					->where('Type','=','0')
					->paginate(10);
					$viewVatBillTypesArr=array(
						'viewVatBillTypes' => $viewVatBillTypes
					);

					return view('accounting.register.vatRegister.viewVatBillType',$viewVatBillTypesArr);
				}


				public function accViewVatBillTypeModal(Request $request)
				{
					$vatBillTypeId=$request->vatBillTypeId;
					$viewVatBillTypes= DB::table('acc_vat_bill_type')
					->select('*')
					->where('id','=',$vatBillTypeId)
					->first();
        //   $viewVatBillTypes->activeFromGeneral=Carbon::parse($viewVatBillTypes->activeFrom)->format('d-m-Y');
					$viewVatBillTypes->activeFrom=Carbon::parse($viewVatBillTypes->activeFrom)->format('d-m-Y');
					$viewVatBillTypes->activeFromMin=Carbon::parse($viewVatBillTypes->activeFrom)->addDay()->format('Y-m-d');
					$viewVatBillTypesArr=array(
						'viewVatBillTypes' => $viewVatBillTypes


					);
					return response()->json($viewVatBillTypesArr);

				}


				public function accViewVatBillTypeModalUpdate(Request $request)
				{   
					$previousdata = AccVatBillTypeRegister::find ($request->supplierId);
					$request->activeFrom=Carbon::parse($request->activeFrom);
					$billTypeUpdate=DB::table('acc_vat_bill_type')
					->where('id',$request->supplierId)
					->update(['serviceName' => $request->billName,'serviceCode' => $request->billCode,'vatRate' => $request->vatRate,'activeFrom' => $request->activeFrom]);

					$billTypeHistoryUpdate=DB::table('acc_vat_bill_type_history')
					->where('billTypeIdFk',$request->supplierId)
					->update(['status' => 0,'activatedTo' => $request->activeFrom]);

					$billHistory=	new AccVatBillTypeRegisterHistory;
					$billHistory->billTypeIdFk=$request->supplierId;
					$billHistory->vatRate=$request->vatRate;
					$billHistory->activatedFrom=Carbon::parse($request->activeFrom);
					$billHistory->status=1;
					$billHistory->createdAt=Carbon::now();
					$billHistory->save();

					$logArray = array(
						'moduleId'  => 4,
						'controllerName'  => 'AccVatRegisterController',
						'tableName'  => 'acc_vat_bill_type',
						'operation'  => 'update',
						'previousData'  => $previousdata,
						'primaryIds'  => [$previousdata->id]
					);
					Service::createLog($logArray);




					return response::json('success');
				}

				public function accViewVatBillTypeModalDelete(Request $request)
				{
					return response::json('success');
				}

				public function accAddVatregisterPayVat(Request $request)
				{
					$vatPayment=	new AccVatPayment;
					$vatPaymentDetails=	new AccVatPaymentDetails;
					$vatPayment->paymentId=$request->paymentId;
					$vatPayment->paymentDate=Carbon::parse($request->paymentDate);
					$vatPayment->amount=$request->vatAmount;
					$vatPayment->bankName=$request->depositBank;
					$vatPayment->chalanNo=$request->chalanNo;
					$vatPayment->paymentType=$request->paymentType;
				//$vatPayment->ledgerIdFk=$request->vatRate;
					$vatPayment->chequeNumber=$request->chequeNo;
					$vatPayment->chequeNumber=$request->chequeNo;
					$vatPayment->createdAt=Carbon::now();
					$vatPayment->save();

					$statusId =$request->vatId;
					$status= DB::table('acc_vat_generate')
					->where('id',$statusId)
					->update(['status' => 1]);



					$vatPaymentDetails->paymentIdFk=$vatPayment->id;
					$vatPaymentDetails->vatIdFk=$request->vatId;

					$query=	DB::table('acc_vat_generate')
					->select('billNo','vatAmount')
					->where('id',$statusId)
					->first();
					$vatPaymentDetails->billNo=$query->billNo;
					$vatPaymentDetails->vatAmount=$query->vatAmount;

					$vatPaymentDetails->save();



					return response::json('success');
				}

				public function accAddVatregisterPayVatBillNoGenerate(Request $request)
				{
					$billNos= DB::table('acc_vat_payment')
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
							public function accViewVatBillTypeUpdateAjax(Request $request)
			{ //$hello=$request->test;
				$ajaxBiilType= DB::table('acc_vat_bill_type')
				->select('serviceName','id')
				->get();

				return response()->json($ajaxBiilType);


			}
			public function accViewVatBillTypeGetAjax(Request $request)
			{
				$ajaxId= $request->vatBillTypeIdeditModalHidden;
				$ajaxResult= DB::table('acc_vat_generate as t1')
				->join('gnr_supplier as t2','t1.supplierIdFk','=','t2.id')
				->join('gnr_project as t3','t1.projectId_Fk','=','t3.id')
				->join('gnr_project_type as t4','t1.project_TypeId_Fk','=','t4.id')
				->select('t1.*','t2.name','t3.name as projectName','t4.name as projectTypeName')
				->where('t1.id',$ajaxId)
				->first();
				return response()->json($ajaxResult);

			}
			public function accViewVatBillRegisterFullUpdate(Request $request)
			{
				$voucherDate= Carbon::parse($request->voucherDate);
				$billDate   = Carbon::parse($request->billDate);
				 // echo $request->billDate;
				 // echo $request->billType;
				 // echo $request->vatRate;
				 // echo $request->billAmount;
				$status= DB::table('acc_vat_generate')
				->where('id',$request->billTypeId)
				->update(['voucherDate' => $voucherDate,'billDate' => $billDate,'vatInterestRate' => $request->vatRate, 'billTypeIdFk' => $request->billType,'billAmount' => $request->billAmount, 'vatAmount'=> $request->vatAmount ]);

				return response::json('success');
			}


			public function accViewVatBillRegisterViewModal(Request $request)
			{
				$viewModal= $request->viewModalId;
				$viewModalResult= DB::table('acc_vat_generate as t1')
				->join('gnr_supplier as t2','t1.supplierIdFk','=','t2.id')
				->join('gnr_project as t3','t1.projectId_Fk','=','t3.id')
				->join( 'acc_vat_bill_type as t4','t1.billTypeIdFk','=','t4.id')
				->join('hr_emp_general_info as t5','t1.entryByEmpIdFk','=','t5.id')

				->select('t1.*','t2.name','t3.name as projectName','t4.serviceName','t5.emp_name_english')
				->where('t1.id',$viewModal)
				->first();
				$viewModalResult->billDate=Carbon::parse($viewModalResult->billDate)->format('d-m-Y');
				return response()->json($viewModalResult);

			}

			public function accVatRegisterPaymentList()
			{
				$paymentLists =DB::table('acc_vat_payment as t1')
				->join('acc_vat_payment_details as t2','t1.id','=','t2.paymentIdFk')
				->select('t1.*','t2.billNo','t2.vatAmount')
				->get();

				$viewPaymentList=array(
					'paymentLists' => $paymentLists
				);

				return view('accounting.register.vatRegister.viewPaymentList',$viewPaymentList);

			}


			public function accViewVatBillRegisterDeleteModal(Request $request)
			{
				$softDelete=DB::table('acc_vat_generate')
				->where('id',$request->DeleteModalId)
				->update(['softDel' => 1]);
				return response::json('success');

			}



		}
		?>
