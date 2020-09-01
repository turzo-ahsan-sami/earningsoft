<?php

    namespace App\Http\Controllers\microfin\settings;

    use Illuminate\Http\Request;
    use App\Http\Requests;
    use App\gnr\GnrDivision;
    use Validator;
    use Response;
    use DB;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\Hash;
    use App\Http\Controllers\Controller;
    use App\microfin\settings\MfnMaritalStatus;


    class MfnMaritalStatusController extends Controller {

        private $TCN;

        public function __construct() {

            $this->TCN = array(
                array('SL No.', 70), 
                array('Name', 0),
                array('Action', 100)
            );
        }

        public function index() {

            $mfnMaritalStatus = MfnMaritalStatus::all();
            
            $TCN = $this->TCN;

            return view('microfin.settings.maritalStatus.viewMaritalStatus',['mfnMaritalStatus'=>$mfnMaritalStatus, 'TCN' => $TCN]);
        }

        public function addMaritalStatusForm() {

             return view('microfin.settings.maritalStatus.addMaritalStatus');
        }

        /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: ADD MemberTYpe CONTROLLER.
        |--------------------------------------------------------------------------
        */
        public function addItem(Request $req) {

            $rules = array(
                'name'      =>  'required|unique:mfn_marital_status,name'
            );

            $attributesNames = array(
                'name'      =>  'Marital Status'
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else {
                $mfnMaritalStatus = new  MfnMaritalStatus;
                $mfnMaritalStatus->name =$req->name;
                $mfnMaritalStatus->createdDate = Carbon::now();
                $mfnMaritalStatus->save();
            
                $data = array(
                    'responseTitle' =>   'Success!',
                    'responseText'  =>   'New Matital Status has been saved successfully.'
                );
                
                return response::json($data);
            }
        }
         /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: GET DATA FOR UPDATE MEMBER TYPE CONTROLLER.
        |--------------------------------------------------------------------------
        */

        public function getMaritalStatusInfo(Request $req){

          $mfnMaritalStatus = MfnMaritalStatus::find($req->id);

          $data =array(
              'mfnMaritalStatus'=>$mfnMaritalStatus
            );

         return response()->json($data);
       }

        /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: UPDATE MEMBER TYPE CONTROLLER.
        |--------------------------------------------------------------------------
        */
        public function updateItem(Request $req) {

            $rules = array(
                'name'       => 'required|unique:mfn_marital_status,name,'.$req->id,
            );

            $attributesNames = array(
                'name'       => 'Marital Status name',
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else {

                $mfnMaritalStatus = MfnMaritalStatus::find($req->id);
                $mfnMaritalStatus->name = $req->name;
                $mfnMaritalStatus->save();
                $data = array(
                    'responseTitle' =>   'Success!',
                    'responseText'  =>   'Marital Status has been updated successfully.'
                );
                
                return response()->json($data);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: DELETE MEMBER TYPE CONTROLLER.
        |--------------------------------------------------------------------------
        */
        public function deleteItem(Request $req) {
            MfnMaritalStatus::find($req->id)->delete();
            
            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Your selected Marital Status deleted successfully.'
            );

            return response()->json($data);
        }
    }