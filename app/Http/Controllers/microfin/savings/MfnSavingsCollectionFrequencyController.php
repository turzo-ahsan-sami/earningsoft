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
    use App\microfin\savings\MfnSavingsCollectionFrequency;

    class MfnSavingsCollectionFrequencyController extends Controller {
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

            $collFrequencies = MfnSavingsCollectionFrequency::where('softDel',0)->get();

            $TCN = $this->TCN;
            $damageData = array(
                'collFrequencies'   => $collFrequencies,
                'TCN'               =>  $TCN
            );

            return view('microfin.savings.collectionFrequency.viewCollectionFrequency',['damageData'=>$damageData]);
        }

        public function addCollectionFrequency() {

            return view('microfin.savings.collectionFrequency.addCollectionFrequency');
        }

        /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: ADD MemberTYpe CONTROLLER.
        |--------------------------------------------------------------------------
        */
        public function storeCollectionFrequency(Request $req) {

            $rules = array(
                'name'   =>  'required|unique:mfn_savings_collection_frequency,name'
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

                $collectionFrequency = new MfnSavingsCollectionFrequency;
                $collectionFrequency->name         = $req->name;
                $collectionFrequency->status       = $req->status;
                $collectionFrequency->createdDate  = Carbon::now();
                $collectionFrequency->save();

                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  'Collection Frequency inserted successfully.'
                );

                return response::json($data);                
            }            
        }

        public function updateCollectionFrequency(Request $req) {

            $rules = array(
                'name'   =>  'required|unique:mfn_savings_collection_frequency,name,'.$req->id
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

                $collectionFrequency = MfnSavingsCollectionFrequency::find($req->id);
                $collectionFrequency->name         = $req->name;
                $collectionFrequency->status       = $req->status;
                $collectionFrequency->save();

                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  'Collection Frequency updated successfully.'
                );

                return response::json($data);                
            }            
        }


         public function deleteCollectionFrequency(Request $req) {
            $collectionFrequency = MfnSavingsCollectionFrequency::find($req->id);
            $collectionFrequency->softDel = 1;
            $collectionFrequency->save();

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Deposit Type deleted successfully.'
            );
            
            return response::json($data);   
        }
        
    }

        