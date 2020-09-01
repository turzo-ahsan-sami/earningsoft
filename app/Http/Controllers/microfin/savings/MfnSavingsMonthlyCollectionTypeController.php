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
    use App\microfin\savings\MfnSavingsMonthlyCollectionType;

    class MfnSavingsMonthlyCollectionTypeController extends Controller {
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

            $monthlyCollectionTypes = MfnSavingsMonthlyCollectionType::where('softDel',0)->get();

            $TCN = $this->TCN;
            $damageData = array(
                'monthlyCollectionTypes'    =>  $monthlyCollectionTypes,
                'TCN'                       =>  $TCN
            );

            return view('microfin.savings.monthlyCollectionType.viewMonthlyCollectionType',['damageData'=>$damageData]);
        }

        public function addMonthlyCollectionType() {

            return view('microfin.savings.monthlyCollectionType.addMonthlyCollectionType');
        }

        /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: ADD MemberTYpe CONTROLLER.
        |--------------------------------------------------------------------------
        */
        public function storeMonthlyCollectionType(Request $req) {

            $rules = array(
                'name'   =>  'required|unique:mfn_saving_monthly_collection_type,name'
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

                $MonthlyCollectionType = new MfnSavingsMonthlyCollectionType;
                $MonthlyCollectionType->name         = $req->name;
                $MonthlyCollectionType->value        = implode(',',$req->values);
                $MonthlyCollectionType->status       = $req->status;
                $MonthlyCollectionType->createdDate  = Carbon::now();
                $MonthlyCollectionType->save();

                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  'Monthly Collection Type inserted successfully.'
                );

                return response::json($data);                
            }            
        }    


        public function updateMonthlyCollectionType(Request $req) {

            $rules = array(
                'name'   =>  'required|unique:mfn_saving_monthly_collection_type,name,'.$req->id
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

                $MonthlyCollectionType = MfnSavingsMonthlyCollectionType::find($req->id);
                $MonthlyCollectionType->name         = $req->name;
                $MonthlyCollectionType->value        = implode(',',$req->values);
                $MonthlyCollectionType->status       = $req->status;
                $MonthlyCollectionType->save();

                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  'Monthly Collection Type updated successfully.'
                );

                return response::json($data);                
            }            
        }

        public function deleteMonthlyCollectionType(Request $req) {
            $monthlyCollectionType = MfnSavingsMonthlyCollectionType::find($req->id);
            $monthlyCollectionType->softDel = 1;
            $monthlyCollectionType->save();

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Your selected product deleted successfully.'
            );

            return response()->json($data);
        }        
        
    }

        