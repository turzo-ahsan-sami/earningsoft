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
    use App\microfin\savings\MfnSavingsInterestCalMethod;

    class MfnSavingsInterestCalMethodController extends Controller {
        
        private $TCN;

        public function __construct() {

            $this->TCN = array(
                array('SL No.', 70),
                array('Name', 0),
                array('Action', 100)
            );
        }

        /**
         * Returns the view page of Interest Calculation Methods List
         * @return [page]
         */
        public function index() {

            $interestCalMethods = MfnSavingsInterestCalMethod::where('softDel',0)->get();

            $TCN = $this->TCN;
            $damageData = array(
                'interestCalMethods'    => $interestCalMethods,
                'TCN'                   =>  $TCN
            );

            return view('microfin.savings.interestCalMethod.viewInterestCalMethod',['damageData'=>$damageData]);
        }


        /**
         * Returns the view page to Add Interest Calculation Method
         */
        public function addInterestCalMethod() {

            return view('microfin.savings.interestCalMethod.addInterestCalMethod');
        }

        /**
         * [store Interest Calculation Method]
         * @param  Request $req [description]
         * @return [json]       [error/success message]
         */
        public function storeInterestCalMethod(Request $req) {

            $rules = array(
                'name'   =>  'required|unique:mfn_savings_interest_cal_method,name'
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

                $interestCalMethod = new MfnSavingsInterestCalMethod;
                $interestCalMethod->name         = $req->name;
                $interestCalMethod->status       = $req->status;
                $interestCalMethod->createdDate  = Carbon::now();
                $interestCalMethod->save();

                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  'Interest Calculation Method inserted successfully.'
                );

                return response::json($data);                
            }            
        }   

        public function updateInterestCalMethod(Request $req) {

            $rules = array(
                'name'   =>  'required|unique:mfn_savings_interest_cal_method,name,'.$req->id
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

                $interestCalMethod = MfnSavingsInterestCalMethod::find($req->id);
                $interestCalMethod->name         = $req->name;
                $interestCalMethod->status       = $req->status;
                $interestCalMethod->save();

                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  'Interest Calculation Method updated successfully.'
                );

                return response::json($data);                
            }            
        }  

        public function deleteInterestCalMethod(Request $req) {
            $interestCalMethod = MfnSavingsInterestCalMethod::find($req->id);
            $interestCalMethod->softDel = 1;
            $interestCalMethod->save();

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Your selected product deleted successfully.'
            );

            return response()->json($data);
        }   
        
    }

        