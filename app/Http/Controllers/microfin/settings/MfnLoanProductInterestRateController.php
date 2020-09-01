<?php

    namespace App\Http\Controllers\microfin\settings;

    use Illuminate\Http\Request;
    use App\Http\Requests;
    use App\microfin\settings\MfnLoanProductInterestRate;
    use Validator;
    use Response;
    use DB;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\Hash;
    use App\Http\Controllers\Controller;
    use App\Http\Controllers\microfin\MicroFinance;


    class MfnLoanProductInterestRateController extends Controller {

        protected $MicroFinance;


        public function __construct() {

            $this->MicroFinance = New MicroFinance;
        }

        public function addInterestRate($loanProductId) {

            $damageData = array(
                'loanProductId'                  =>  $loanProductId,
                'loanInterestCalculationMethod'  =>  $this->MicroFinance->getLoanInterestCalculationMethodOptions(),
                'interestDeclinePeriod'          =>  $this->MicroFinance->getInterestDeclinePeriodOptions(),
                'interestMode'                   =>  $this->MicroFinance->getInterestModeOptions(),
                'installmentOptions'             =>  $this->MicroFinance->getInstallmentNumByProductWise(decrypt($loanProductId)),
                'interestRepaymentFrequency'     =>  $this->MicroFinance->getInterestRepaymentFrequencyOptions(),
                'boolean'                        =>  $this->MicroFinance->getBooleanOptions(),
            );
            // dd($damageData);

            return view('microfin.loan.product.addProductLoanInterestRate', ['damageData' => $damageData]);
        }

        public function addInterestRateItem(Request $req) {

            $rules = array(
                'interestCalculationMethodId'         =>  'required',
                'declinePeriodId'                     =>  'required',
                'effectiveDate'                       =>  'required',
                'interestModeId'                      =>  'required',
                'interestRate'                        =>  'required',
                'installmentNum'                      =>  'required',
                'isEnforceNumberInstallmentRequired'  =>  'required',
            );

            $attributesNames = array(
                'interestCalculationMethodId'         =>  'interest calculation method name',
                'declinePeriodId'                     =>  'decline period',
                'effectiveDate'                       =>  'effective date',
                'interestModeId'                      =>  'interest mode',
                'interestRate'                        =>  'interest rate',
                'installmentNum'                      =>  'installment number',
                'isEnforceNumberInstallmentRequired'  =>  'enforce number installment',
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails())
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else {
                $now = Carbon::now();
                $req->request->add(['createdDate' => $now]);

                // GET INTEREST CALCULATION METHOD SHORT NAME.
                $req->request->add(['interestCalculationMethodShortName' => $this->MicroFinance->getLoanInterestCalculationMethodShortName($req->interestCalculationMethodId)]);

                //  CHANGE THE EFFECTIVE DATE FORMAT.
                $effectiveDate = date_create($req->effectiveDate);
                $req->request->add(['effectiveDate' => date_format($effectiveDate, "Y-m-d")]);
                $create = MfnLoanProductInterestRate::create($req->all());

                $data = array(
                    'responseTitle'  =>   'Success!',
                    'responseText'   =>   'New loan product interest rate has been saved successfully.',
                );

                return response::json($data);
            }
        }

        public function updateInterestRate($interestId) {

            $loanIntertestRateDetails = DB::table('mfn_loan_product_interest_rate')
        								->where('id', $interestId)
        								->first();
                                        // dd($loanIntertestRateDetails);
            $loanProductId = $loanIntertestRateDetails->loanProductId;

            $damageData = array(
                'loanIntertestRateDetails'       =>  $loanIntertestRateDetails,
                'loanProductId'                  =>  $loanProductId,
                'loanInterestCalculationMethod'  =>  $this->MicroFinance->getLoanInterestCalculationMethodOptions(),
                'interestDeclinePeriod'          =>  $this->MicroFinance->getInterestDeclinePeriodOptions(),
                'interestMode'                   =>  $this->MicroFinance->getInterestModeOptions(),
                'installmentOptions'             =>  $this->MicroFinance->getInstallmentNumByProductWise($loanProductId),
                'interestRepaymentFrequency'     =>  $this->MicroFinance->getInterestRepaymentFrequencyOptions(),
                'boolean'                        =>  $this->MicroFinance->getBooleanOptions(),
            );
            // dd($damageData);

            return view('microfin.loan.product.updateLoanInterestRate', ['damageData' => $damageData]);
        }

        public function updateInterestRateItem(Request $req) {

            // dd($req->all());
			//	UPDATE Interest Rate.
			$interestRateDetails = DB::table('mfn_loan_product_interest_rate')->where('id', $req->interestId)->first();
            if ($req->interestCalculationMethodId == 3) {
                $req->interestRateIndex = $req->interestRateIndex;
            }
            else {
                $req->interestRateIndex = 0;
            }
            // dd($interestRateDetails);
            $data = array(
                'interestCalculationMethodId' => $req->interestCalculationMethodId,
                'interestCalculationMethodShortName' => $this->MicroFinance->getLoanInterestCalculationMethodShortName($req->interestCalculationMethodId),
    			'declinePeriodId' => $req->declinePeriodId,
    			'dayCountFixed' => $req->dayCountFixed,
    			'effectiveDate' => $req->effectiveDate,
    			'interestModeId' => $req->interestModeId,
    			'interestRate' => $req->interestRate,
    			'interestRateIndex' => $req->interestRateIndex,
    			'installmentNum' => $req->installmentNum,
    			'repaymentFrequencyId' => $req->repaymentFrequencyId,
    			'isEnforceNumberInstallmentRequired' => $req->isEnforceNumberInstallmentRequired,
    			'enforcedInstallmentNumber' => $req->enforcedInstallmentNumber,
            );
            // dd($data);

			//	update
			DB::table('mfn_loan_product_interest_rate')->where('id', $req->interestId)->update($data);

			$data = array(
				'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
				'responseText'   =>  MicroFinance::getMessage('loanInterestRateUpdateSuccess'),
			);

			return response::json($data);
		}

        public function updateInterestRateItemStatus(Request $req) {

            // dd($req->all());
			//	UPDATE Interest Rate.
			$interestRateDetails = DB::table('mfn_loan_product_interest_rate')->where('id', $req->interestId)->first();
            $installmentOptions = $this->MicroFinance->getInstallmentNumByProductWise($interestRateDetails->loanProductId);
            // dd($installmentOptions);
            if ($interestRateDetails->status == 1) {
                $data = array(
                    'status' => 0,
                );
            }
            else {
                if (in_array($interestRateDetails->installmentNum, $installmentOptions)) {
                    $data = array(
                        'status' => 1,
                    );
                }
                else {
                    $data = array(
        				'responseTitle'  =>  MicroFinance::getMessage('msgWarning'),
        				'responseText'   =>  MicroFinance::getMessage('loanInterestRateStatusUpdateWarning'),
        			);

        			return response::json($data);
                }

            }
            // dd($data);

            // update
			DB::table('mfn_loan_product_interest_rate')->where('id', $req->interestId)->update($data);

			$data = array(
				'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
				'responseText'   =>  MicroFinance::getMessage('loanInterestRateStatusUpdateSuccess'),
			);

			return response::json($data);
		}

        public function deleteInterestRate(Request $req) {
            // dd($req->all());
            // $data = array(
            //     'status' => 0
            // );

            DB::table('mfn_loan_product_interest_rate')->where('id', $req->id)->delete();
            // DB::table('mfn_loan_product_interest_rate')->where('id', $req->id)->update($data);

			$data = array(
				'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
				'responseText'   =>  MicroFinance::getMessage('loanInterestRateDeleteSuccess'),
			);

			return response::json($data);

		}

    }
