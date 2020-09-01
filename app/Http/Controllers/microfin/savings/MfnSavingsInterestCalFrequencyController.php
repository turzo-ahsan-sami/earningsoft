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
    use App\microfin\savings\MfnSavingsInterestCalFrequency;

    class MfnSavingsInterestCalFrequencyController extends Controller {
        use CreateForm;

        private $TCN;

        public function __construct() {

            $this->TCN = array(
                array('SL No.', 70),
                array('Name', 0),
                array('Action', 100)
            );
        }

        public function index() {

            $interestCalFrequencies = MfnSavingsInterestCalFrequency::where('softDel',0)->get();

            $TCN = $this->TCN;
            $damageData = array(
                'interestCalFrequencies'  => $interestCalFrequencies,
                'TCN'                   =>  $TCN
            );

            return view('microfin.savings.interestCalFrequency.viewInterestCalFrequency',['damageData'=>$damageData]);
        }

        public function addInterestCalFrequency() {

            return view('microfin.savings.interestCalFrequency.addInterestCalFrequency');
        }

        /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: ADD MemberTYpe CONTROLLER.
        |--------------------------------------------------------------------------
        */
        public function storeInterestCalFrequency(Request $req) {

            $rules = array(
                'name'   =>  'required|unique:mfn_savings_interest_cal_frequency,name'
            );


            $attributesNames = array(
                'name'   =>  'Name'
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else {
                // Store Data

                $interestCalFrequency = new MfnSavingsInterestCalFrequency;
                $interestCalFrequency->name         = $req->name;
                $interestCalFrequency->status       = $req->status;
                $interestCalFrequency->createdDate  = Carbon::now();
                $interestCalFrequency->save();

                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  'Interest Calculation Frequency inserted successfully.'
                );

                return response::json($data);                
            }            
        }


        public function updateInterestCalFrequency(Request $req) {

            $rules = array(
                'name'   =>  'required|unique:mfn_savings_interest_cal_frequency,name,'.$req->id
            );

            $attributesNames = array(
                'name'   =>  'Name'
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else {
                // Store Data

                $interestCalFrequency = MfnSavingsInterestCalFrequency::find($req->id);
                $interestCalFrequency->name         = $req->name;
                $interestCalFrequency->status       = $req->status;
                $interestCalFrequency->save();

                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  'Interest Calculation Frequency updated successfully.'
                );

                return response::json($data);                
            }            
        }


        public function deleteInterestCalFrequency(Request $req) {
            $interestCalFrequency = MfnSavingsInterestCalFrequency::find($req->id);
            $interestCalFrequency->softDel = 1;
            $interestCalFrequency->save();

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Your selected product deleted successfully.'
            );

            return response()->json($data);
        }    
        
    }

        