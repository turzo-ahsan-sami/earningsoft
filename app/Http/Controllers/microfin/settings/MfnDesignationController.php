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
    use App\microfin\settings\MfnDesignation;


    class MfnDesignationController extends Controller {

        private $TCN;

        public function __construct() {

            $this->TCN = array(
                array('SL No.', 70), 
                array('Name', 0),
                array('Action', 100)
            );
        }

        public function index() {

            $mfnDesignation = MfnDesignation::all();
            
            $TCN = $this->TCN;

            return view('microfin.settings.designation.viewDesignation',['mfnDesignation'=>$mfnDesignation, 'TCN' => $TCN]);
        }

        public function addProfessionForm() {

             return view('microfin.settings.designation.addDesignation');
        }

        /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: ADD RELATIONSHIP CONTROLLER.
        |--------------------------------------------------------------------------
        */
        public function addItem(Request $req) {

            $rules = array(
                'name'      =>  'required|unique:mfn_designation,name'
            );

            $attributesNames = array(
                'name'      =>  'Designation'
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else {
                $mfnDesignation = new  MfnDesignation;
                $mfnDesignation->name =$req->name;
                $mfnDesignation->createdDate = Carbon::now();
                $mfnDesignation->save();
            
                $data = array(
                    'responseTitle' =>   'Success!',
                    'responseText'  =>   'New Designation has been saved successfully.'
                );
                
                return response::json($data);
            }
        }
         /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: GET DATA FOR UPDATE RELATIONSHIP CONTROLLER.
        |--------------------------------------------------------------------------
        */

        public function getDesignationInfo(Request $req){

          $mfnDesignation = MfnDesignation::find($req->id);

          $data =array(
              'mfnDesignation'=>$mfnDesignation

            );

         return response()->json($data);
       }

        /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: UPDATE RELATIONSHIP CONTROLLER.
        |--------------------------------------------------------------------------
        */
        public function updateItem(Request $req) {

            $rules = array(
                'name'       => 'required|unique:mfn_designation,name,'.$req->id,
            );

            $attributesNames = array(
                'name'       => 'Designation',
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else {

                $mfnDesignation = MfnDesignation::find($req->id);
                $mfnDesignation->name = $req->name;
                $mfnDesignation->save();
                $data = array(
                    'responseTitle' =>   'Success!',
                    'responseText'  =>   'Designation has been updated successfully.'
                );
                
                return response()->json($data);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: DELETE RELATIONSHIP CONTROLLER.
        |--------------------------------------------------------------------------
        */
        public function deleteItem(Request $req) {
            MfnDesignation::find($req->id)->delete();
            
            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Your selected Designation deleted successfully.'
            );

            return response()->json($data);
        }
    }