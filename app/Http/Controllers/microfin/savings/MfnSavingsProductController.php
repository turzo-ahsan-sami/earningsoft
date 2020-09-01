<?php

    namespace App\Http\Controllers\microfin\savings;

    use Illuminate\Http\Request;
    use App\Http\Requests;
    use Validator;
    use Response;
    use DB;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\Hash;
    use App\Http\Controllers\Controller;
    use App\microfin\MfnMemberType;
    use App\Traits\CreateForm;
    use App\microfin\savings\MfnSavingsProduct;
    use App\microfin\savings\MfnSavingsFdrProductRepayAmount;

    class MfnSavingsProductController extends Controller {
        use CreateForm;

        private $TCN;

        public function __construct() {

            $this->TCN = array(
                array('SL No.', 70),
                array('Name', 0),
                array('Short Name', 0),
                array('Start Date', 0),
                array('Deposit Type', 0),
                array('Mendatory Amount <br> for Deposit', 0),
                array('Interest Rate (%)', 100),
                array('Action', 100)
            );

            $this->toogleList = array(
                ''      => 'Select',
                '1'     => 'Yes',
                '0'     => 'No'
            );

        }


        public function viewProduct(Request $req) {

            $products = MfnSavingsProduct::active()->get();
            $TCN = $this->TCN;

            $damageData = array(
                'TCN'        =>  $TCN,
                'products'   =>  $products,
                'toogleList'      =>   $this->toogleList
            );

            return view('microfin.savings.product.viewProduct',['damageData'=>$damageData]);
        }

        public function addProduct() {
            
            $damageData = array(
                'toogleList'      =>   $this->toogleList
                
            );           

            return view('microfin.savings.product.addProduct',['damageData'=>$damageData]);
        }

        /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: STORE PRODUCT.
        |--------------------------------------------------------------------------
        */
        public function storeProduct(Request $req) {

            $rules = array(
                'name'                          =>  'required|unique:mfn_saving_product,name',
                'shortName'                     =>  'required|unique:mfn_saving_product,shortName',
                'savingProductCode'             =>  'required|unique:mfn_saving_product,code',
                'startDate'                     =>  'required',
                'minSavingsBalance'             =>  'required',
                'typeOfDeposit'                 =>  'required',
                'isMultipleSavingsAllowed'      =>  'required',
                'isNomineeRequired'             =>  'required',
                'isClosingChargeApplicable'     =>  'required',
                'interestCalculationFrequency'  =>  'required',
                'savingCollectionFrequency'     =>  'required',
                'interestCalculationMethod'     =>  'required',
                'isDueMemberGettingInterest'    =>  'required',
                /*'status'                        =>  'required',*/
                'onClosingInterestEditable'     =>  'required',
            );


            /* Deposit Type */
            if ($req->typeOfDeposit==3) {                
                $rules = $rules + array(
                    'lateInstallmentCalMethod'     => 'required',
                    'lateInstallmentPenaltyAmount' => 'required',
                    'monthlyCollectionType'        => 'required',
                    'monthlyCollectionWeekOrDay'   => 'required',
                );
            }
            else{
                if ($req->typeOfDeposit==1) {
                    $rules = $rules + array(
                        'weeklyDepositAmount'  => 'required'
                    );
                }
                elseif($req->typeOfDeposit==2){
                    $rules = $rules + array(
                        'monthlyDepositAmount' => 'required'
                    );                    
                }

                $rules = $rules + array(
                   /* 'weeklyDepositAmount'  => 'required',
                    'monthlyDepositAmount' => 'required',*/
                    'isPartialWithdrawAllowed' => 'required'
                );
            }
            /* End Deposit Type */

            /* Closing Charge */
            if ($req->isClosingChargeApplicable==1) {                
                $rules = $rules + array(
                    'closingCharge'     => 'required',
                );
            }            
            /* End Closing Charge */

            /* Interest Calculation Method */

            $customError = array();

            // Flat Regular Interest Rate
            if ($req->interestCalculationMethod==1) {                
                $rules = $rules + array(
                    'interestRate'     => 'required',
                );
            }

            // Flat Multi Stage Interest Rate
            elseif($req->interestCalculationMethod==2) {
                $hasError = 0;

                foreach ($req->installmentTo as $key => $installmentFrom) {
                    if ($installmentFrom=='' || $req->interestRegular[$key]=='' || $req->interestIrregular[$key]=='') {
                        $hasError = 1;
                    }
                }

                if ($hasError == 1) {
                    $customError =  $customError + array(
                        'savingInterestInformationTable'     => 'Please Fill all Fileds.',
                    );
                }
                
            }      
            /* End Interest Calculation Method */




            $attributesNames = array(
                'name'                          => 'Product Name',
                'shortName'                     => 'Short Name',
                'savingProductCode'             => 'Product Code',
                'startDate'                     => 'Start Date',
                'minSavingsBalance'             => 'Minimum Savings Balance',
                'typeOfDeposit'                 => 'Type Of Deposit',
                'isMultipleSavingsAllowed'      => '',
                'isNomineeRequired'             => '',
                'isClosingChargeApplicable'     => '',
                'interestCalculationFrequency'  => 'Interest Calculation Frequency',
                'savingCollectionFrequency'     => 'Saving Collection Frequency',
                'interestCalculationMethod'     => 'Interest Calculation Method',
                'interestRate'                  => 'Interest Rate',
                'weeklyDepositAmount'           => 'Weekly Deposit Amount',
                'monthlyDepositAmount'          => 'Monthly Deposit Amount',
                'closingCharge'                 => 'Closing Charge',
                'isPartialWithdrawAllowed'      => '',
                'isDueMemberGettingInterest'    => '',
                'status'                        => 'Status',
                'onClosingInterestEditable'     => '',
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray() + $customError));
            else {
                // Store Data

                $product = new MfnSavingsProduct;
                $product->name                                      = $req->name;
                $product->shortName                                 = $req->shortName;
                $product->code                                      = str_pad($req->savingProductCode,2,'0',STR_PAD_LEFT);
                $product->startDate                                 = Carbon::parse($req->startDate);
                $product->minSavingBalance                          = $req->minSavingsBalance;
                $product->depositTypeIdFk                           = $req->typeOfDeposit;
                $product->weeklyDepositAmount                       = $req->weeklyDepositAmount;
                $product->monthlyDepositAmount                      = $req->monthlyDepositAmount;
                $product->isMultipleSavingAllowed                   = $req->isMultipleSavingsAllowed;
                $product->isNomineeRequired                         = $req->isNomineeRequired;
                $product->hasClosingCharge                          = $req->isClosingChargeApplicable;
                $product->closingChargeAmount                       = $req->closingCharge;
                $product->interestCalFrequencyIdFk                  = $req->interestCalculationFrequency;
                $product->savingCollectionFrequencyIdFk             = $req->savingCollectionFrequency;
                $product->interestCalMethodIdFk                     = $req->interestCalculationMethod;
                $product->interestRate                              = $req->interestRate;
                $product->partialWithdrawAllowId                    = $req->isPartialWithdrawAllowed;
                $product->isDueMemberGetInterstId                   = $req->isDueMemberGettingInterest;
                $product->onClosingInterestEditableId               = $req->onClosingInterestEditable;
                $product->maxAllowMissingInstallmentNum             = $req->maximumAllowedMissingInstallments;
                $product->monthlyCollectionTypeIdFk                 = $req->monthlyCollectionType;
                $product->monthlyCollectionTypeIdValueIndex         = $req->monthlyCollectionWeekOrDay;
                $product->lateInstalmentCalMethodId                 = $req->lateInstallmentCalMethod;
                $product->lateInstallmentPenaltyAmount              = $req->lateInstallmentPenaltyAmount;
                $product->createdDate                               = Carbon::now();
                $product->save();

                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  'Product inserted successfully.'
                );

                return response::json($data);                
            }
            
        }


        public function updateProduct(Request $req) {

            $rules = array(
                'name'                          =>  'required|unique:mfn_saving_product,name,'.$req->productId,
                'shortName'                     =>  'required|unique:mfn_saving_product,shortName,'.$req->productId,
                'savingProductCode'             =>  'required|unique:mfn_saving_product,code,'.$req->productId,
                'startDate'                     =>  'required',
                'minSavingsBalance'             =>  'required',
                'typeOfDeposit'                 =>  'required',
                'isMultipleSavingsAllowed'      =>  'required',
                'isNomineeRequired'             =>  'required',
                'isClosingChargeApplicable'     =>  'required',
                'interestCalculationFrequency'  =>  'required',
                'savingCollectionFrequency'     =>  'required',
                'interestCalculationMethod'     =>  'required',
                'isDueMemberGettingInterest'    =>  'required',
                /*'status'                        =>  'required',*/
                'onClosingInterestEditable'     =>  'required',
            );


            /* Deposit Type */
            if ($req->typeOfDeposit==3) {                
                $rules = $rules + array(
                    'lateInstallmentCalMethod'     => 'required',
                    'lateInstallmentPenaltyAmount' => 'required',
                    'monthlyCollectionType'        => 'required',
                    'monthlyCollectionWeekOrDay'   => 'required',
                );
            }
            else{
                if ($req->typeOfDeposit==1) {
                    $rules = $rules + array(
                        'weeklyDepositAmount'  => 'required'
                    );
                }
                elseif($req->typeOfDeposit==2){
                    $rules = $rules + array(
                        'monthlyDepositAmount' => 'required'
                    );                    
                }

                $rules = $rules + array(
                   /* 'weeklyDepositAmount'  => 'required',
                    'monthlyDepositAmount' => 'required',*/
                    'isPartialWithdrawAllowed' => 'required'
                );
            }
            /* End Deposit Type */

            /* Closing Charge */
            if ($req->isClosingChargeApplicable==1) {                
                $rules = $rules + array(
                    'closingCharge'     => 'required',
                );
            }            
            /* End Closing Charge */

            /* Interest Calculation Method */

            $customError = array();

            // Flat Regular Interest Rate
            if ($req->interestCalculationMethod==1) {                
                $rules = $rules + array(
                    'interestRate'     => 'required',
                );
            }

            // Flat Multi Stage Interest Rate
            elseif($req->interestCalculationMethod==2) {
                $hasError = 0;

                foreach ($req->installmentTo as $key => $installmentFrom) {
                    if ($installmentFrom=='' || $req->interestRegular[$key]=='' || $req->interestIrregular[$key]=='') {
                        $hasError = 1;
                    }
                }

                if ($hasError == 1) {
                    $customError =  $customError + array(
                        'savingInterestInformationTable'     => 'Please Fill all Fileds.',
                    );
                }
                
            }      
            /* End Interest Calculation Method */




            $attributesNames = array(
                'name'                          => 'Product Name',
                'shortName'                     => 'Short Name',
                'savingProductCode'             => 'Product Code',
                'startDate'                     => 'Start Date',
                'minSavingsBalance'             => 'Minimum Savings Balance',
                'typeOfDeposit'                 => 'Type Of Deposit',
                'isMultipleSavingsAllowed'      => '',
                'isNomineeRequired'             => '',
                'isClosingChargeApplicable'     => '',
                'interestCalculationFrequency'  => 'Interest Calculation Frequency',
                'savingCollectionFrequency'     => 'Saving Collection Frequency',
                'interestCalculationMethod'     => 'Interest Calculation Method',
                'interestRate'                  => 'Interest Rate',
                'weeklyDepositAmount'           => 'Weekly Deposit Amount',
                'monthlyDepositAmount'          => 'Monthly Deposit Amount',
                'closingCharge'                 => 'Closing Charge',
                'isPartialWithdrawAllowed'      => '',
                'isDueMemberGettingInterest'    => '',
                'status'                        => 'Status',
                'onClosingInterestEditable'     => '',
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray() + $customError));
            else {
                // Store Data

                $product = MfnSavingsProduct::find($req->productId);
                $product->name                                      = $req->name;
                $product->shortName                                 = $req->shortName;
                $product->code                                      = str_pad($req->savingProductCode,2,'0',STR_PAD_LEFT);
                $product->startDate                                 = Carbon::parse($req->startDate);
                $product->minSavingBalance                          = $req->minSavingsBalance;
                $product->depositTypeIdFk                           = $req->typeOfDeposit;
                $product->weeklyDepositAmount                       = $req->weeklyDepositAmount;
                $product->monthlyDepositAmount                      = $req->monthlyDepositAmount;
                $product->isMultipleSavingAllowed                   = $req->isMultipleSavingsAllowed;
                $product->isNomineeRequired                         = $req->isNomineeRequired;
                $product->hasClosingCharge                          = $req->isClosingChargeApplicable;
                $product->closingChargeAmount                       = $req->closingCharge;
                $product->interestCalFrequencyIdFk                  = $req->interestCalculationFrequency;
                $product->savingCollectionFrequencyIdFk             = $req->savingCollectionFrequency;
                $product->interestCalMethodIdFk                     = $req->interestCalculationMethod;
                $product->interestRate                              = $req->interestRate;
                $product->partialWithdrawAllowId                    = $req->isPartialWithdrawAllowed;
                $product->isDueMemberGetInterstId                   = $req->isDueMemberGettingInterest;
                $product->onClosingInterestEditableId               = $req->onClosingInterestEditable;
                $product->maxAllowMissingInstallmentNum             = $req->maximumAllowedMissingInstallments;
                $product->monthlyCollectionTypeIdFk                 = $req->monthlyCollectionType;
                $product->monthlyCollectionTypeIdValueIndex         = $req->monthlyCollectionWeekOrDay;
                $product->lateInstalmentCalMethodId                 = $req->lateInstallmentCalMethod;
                $product->lateInstallmentPenaltyAmount              = $req->lateInstallmentPenaltyAmount;
                $product->save();

                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  'Product Updated Successfully.'
                );

                return response::json($data);                
            }
            
        }

        
      

        /**
         * [deleteProduct description]
         * @param  Request $req [productId]
         * @return [type]       [success delete message]
         */
        public function deleteProduct(Request $req) {
            $product = MfnSavingsProduct::find($req->productId);
            /*$product->softDel = 1;
            $product->save();*/
            $product->delete();

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Your selected product deleted successfully.'
            );

            return response()->json($data);
        }



        public function editSingleProduct(Request $req) {

            $product = MfnSavingsProduct::where('id',$req->EFproductId)->first();            
            $damageData = array(
                'toogleList'      =>   $this->toogleList
                
            );

            return view('microfin.savings.product.editProduct',['damageData'=>$damageData,'product'=>$product]);
        }


        public function updateSingleProduct(Request $req) {
            DB::beginTransaction();
            try{ $rules = array(
                'name'                          =>  'required|unique:mfn_saving_product,name,'.$req->productId,
                'shortName'                     =>  'required|unique:mfn_saving_product,shortName,'.$req->productId,
                'savingProductCode'             =>  'required|unique:mfn_saving_product,code,'.$req->productId,
                'startDate'                     =>  'required',
                'minSavingsBalance'             =>  'required',
                'typeOfDeposit'                 =>  'required',
                'isMultipleSavingsAllowed'      =>  'required',
                'isNomineeRequired'             =>  'required',
                'isClosingChargeApplicable'     =>  'required',
                'interestCalculationFrequency'  =>  'required',
                'savingCollectionFrequency'     =>  'required',
                'interestCalculationMethod'     =>  'required',
                'isDueMemberGettingInterest'    =>  'required',
                /*'status'                        =>  'required',*/
                'onClosingInterestEditable'     =>  'required',
            );


            /* Deposit Type */
            if ($req->typeOfDeposit==3) {                
                $rules = $rules + array(
                    'lateInstallmentCalMethod'     => 'required',
                    'lateInstallmentPenaltyAmount' => 'required',
                    'monthlyCollectionType'        => 'required',
                    'monthlyCollectionWeekOrDay'   => 'required',
                );
            }
            else{
                $rules = $rules + array(
                    'weeklyDepositAmount'  => 'required',
                    'monthlyDepositAmount' => 'required',
                    'isPartialWithdrawAllowed' => 'required',
                );
            }
            /* End Deposit Type */

            /* Closing Charge */
            if ($req->isClosingChargeApplicable==1) {                
                $rules = $rules + array(
                    'closingCharge'     => 'required',
                );
            }            
            /* End Closing Charge */

            /* Interest Calculation Method */

            $customError = array();

            // Flat Regular Interest Rate
            if ($req->interestCalculationMethod==1) {                
                $rules = $rules + array(
                    'interestRate'     => 'required',
                );
            }

            // Flat Multi Stage Interest Rate
            elseif($req->interestCalculationMethod==2) {
                $hasError = 0;

                foreach ($req->installmentTo as $key => $installmentFrom) {
                    if ($installmentFrom=='' || $req->interestRegular[$key]=='' || $req->interestIrregular[$key]=='') {
                        $hasError = 1;
                    }
                }

                if ($hasError == 1) {
                    $customError =  $customError + array(
                        'savingInterestInformationTable'     => 'Please Fill all Fileds.',
                    );
                }
                
            }      
            /* End Interest Calculation Method */




            $attributesNames = array(
                'name'                          => 'Product Name',
                'shortName'                     => 'Short Name',
                'savingProductCode'             => 'Product Code',
                'startDate'                     => 'Start Date',
                'minSavingsBalance'             => 'Minimum Savings Balance',
                'typeOfDeposit'                 => 'Type Of Deposit',
                'isMultipleSavingsAllowed'      => '',
                'isNomineeRequired'             => '',
                'isClosingChargeApplicable'     => '',
                'interestCalculationFrequency'  => 'Interest Calculation Frequency',
                'savingCollectionFrequency'     => 'Saving Collection Frequency',
                'interestCalculationMethod'     => 'Interest Calculation Method',
                'interestRate'                  => 'Interest Rate',
                'weeklyDepositAmount'           => 'Weekly Deposit Amount',
                'monthlyDepositAmount'          => 'Monthly Deposit Amount',
                'closingCharge'                 => 'Closing Charge',
                'isPartialWithdrawAllowed'      => '',
                'isDueMemberGettingInterest'    => '',
                'status'                        => 'Status',
                'onClosingInterestEditable'     => '',
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray() + $customError));
            else {
                if (!empty($req->validateFirstStep)) {
                    return response::json(array('noError' => ['noError'=>'noError'] ));
                }
                // Store Data

                $product = MfnSavingsProduct::find($req->productId);
                $product->name                                      = $req->name;
                $product->shortName                                 = $req->shortName;
                $product->code                                      = str_pad($req->savingProductCode,2,'0',STR_PAD_LEFT);
                $product->startDate                                 = Carbon::parse($req->startDate);
                $product->minSavingBalance                          = $req->minSavingsBalance;
                $product->depositTypeIdFk                           = $req->typeOfDeposit;
                $product->weeklyDepositAmount                       = $req->weeklyDepositAmount;
                $product->monthlyDepositAmount                      = $req->monthlyDepositAmount;
                $product->isMultipleSavingAllowed                   = $req->isMultipleSavingsAllowed;
                $product->isNomineeRequired                         = $req->isNomineeRequired;
                $product->hasClosingCharge                          = $req->isClosingChargeApplicable;
                $product->closingChargeAmount                       = $req->closingCharge;
                $product->interestCalFrequencyIdFk                  = $req->interestCalculationFrequency;
                $product->savingCollectionFrequencyIdFk             = $req->savingCollectionFrequency;
                $product->interestCalMethodIdFk                     = $req->interestCalculationMethod;
                $product->interestRate                              = $req->interestRate;
                $product->partialWithdrawAllowId                    = $req->isPartialWithdrawAllowed;
                $product->isDueMemberGetInterstId                   = $req->isDueMemberGettingInterest;
                $product->onClosingInterestEditableId               = $req->onClosingInterestEditable;
                $product->maxAllowMissingInstallmentNum             = $req->maximumAllowedMissingInstallments;
                $product->monthlyCollectionTypeIdFk                 = $req->monthlyCollectionType;
                $product->monthlyCollectionTypeIdValueIndex         = $req->monthlyCollectionWeekOrDay;
                $product->lateInstalmentCalMethodId                 = $req->lateInstallmentCalMethod;
                $product->lateInstallmentPenaltyAmount              = $req->lateInstallmentPenaltyAmount;
                $product->save();


                /*store Frd congig table if it is a fdr type product*/
                if($req->typeOfDeposit==4){
                    // Delete the existing data of fdr config
                    DB::table('mfn_savings_fdr_product_repay_amount')->where('productIdFk',$product->id)->delete();

                    foreach ($req->monthlyAmountArray as $index => $monthlyAmount) {
                        foreach ($req->repayAmountArray[$index] as $key => $repayAmount) {
                            $repayAmount = new MfnSavingsFdrProductRepayAmount;
                            $repayAmount->productIdFk       = $product->id;
                            $repayAmount->year              = $req->years[$key];
                            $repayAmount->month             = $req->months[$key];
                            $repayAmount->monthlyAmount     = $req->monthlyAmountArray[$index];
                            $repayAmount->repayAmount       = $req->repayAmountArray[$index][$key];
                            $repayAmount->bonusAmount       = $req->bonusAmountArray[$index][$key];
                            $repayAmount->totalRepayAmount  = $req->totalRepayAmountArray[$index][$key];
                            $repayAmount->createdAt         = Carbon::now();
                            if ($req->totalRepayAmountArray[$index][$key]>0) {
                                $repayAmount->save();
                            }
                            
                        }
                    }
                }
                /*end store Frd congig table if it is a fdr type product*/
               DB::commit();
                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  'Product Updated Successfully.'
                );
    
                return response::json($data);                
            }}
            catch(\Exception $e){
					DB::rollback();
					$data = array(
						'responseTitle'  =>  'Warning!',
						'responseText'   =>  'Something went wrong. Please try again.'
	 				);
	 				return response::json($data);
				}         
        }


         /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: GET DATA FOR UPDATE MEMBER TYPE CONTROLLER.
        |--------------------------------------------------------------------------
        */

        public function getMemberTypeInfo(Request $req){

            $mfnMemberType = MfnMemberType::find($req->id);

            $data =array(
                'mfnMemberType'=>$mfnMemberType
            );

            return response()->json($data);
       }


       public function addProductRepayAmount(Request $req) {
            DB::beginTransaction();
            try{$hasDuplicateData = 0;
            if ($req->operationType=='add') {
                $hasDuplicateData = (int) MfnSavingsFdrProductRepayAmount::active()->where('productIdFk',$req->productId)->where('monthlyAmount',$req->amount)->value('id');
            }
            else{
                DB::table('mfn_savings_fdr_product_repay_amount')->where('softDel',0)->where('productIdFk',$req->productId)->where('monthlyAmount',$req->amount)->delete();
            }

            if ($req->amount==null || $req->amount=='') {
                return response::json(array('errors' => ["amount"=>"The Amount is required."]));
            }
            if ($hasDuplicateData>0) {
                return response::json(array('errors' => ["amount"=>"The Amount has been already taken."]));
            }

            if (count($req->year)<1) {
                return response::json(array('errors' => ["emptyTable"=>"Add atleast one duration."]));
            }


                foreach ($req->year as $key => $year) {

                    if (!empty($req->bonusAmount[$key])) {
                        $bonusAmount = $req->bonusAmount[$key];
                    }
                    else{
                        $bonusAmount = 0;
                    }                    

                    if ($req->fdrAmount[$key]>0) {
                        $amount = new MfnSavingsFdrProductRepayAmount;
                        $amount->productIdFk = $req->productId;
                        $amount->year = $year;
                        $amount->month = $req->month[$key];
                        $amount->monthlyAmount = $req->amount;
                        $amount->repayAmount = $req->fdrAmount[$key];
                        $amount->bonusAmount = $bonusAmount;
                        $amount->totalRepayAmount = $req->fdrAmount[$key] + $bonusAmount;
                        $amount->createdAt = Carbon::now();
                        $amount->save();
                    }
                    
                }
                DB::commit();
                return response::json('success');}
                catch(\Exception $e){
					DB::rollback();
					$data = array(
						'responseTitle'  =>  'Warning!',
						'responseText'   =>  'Something went wrong. Please try again.'
	 				);
	 				return response::json($data);
				}

            /*}*/
       }


        
    }