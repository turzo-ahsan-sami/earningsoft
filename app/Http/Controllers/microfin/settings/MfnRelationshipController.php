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
    use App\microfin\settings\MfnRelationship;


    class MfnRelationshipController extends Controller {

        private $TCN;

        public function __construct() {

            $this->TCN = array(
                array('SL No.', 70), 
                array('Name', 0),
                array('Action', 100)
            );
        }

        public function index() {

            $mfnRelationship = MfnRelationship::all();
            
            $TCN = $this->TCN;

            return view('microfin.settings.relationship.viewRelationship',['mfnRelationship'=>$mfnRelationship, 'TCN' => $TCN]);
        }

        public function addRelationshipForm() {

             return view('microfin.settings.relationship.addRelationship');
        }

        /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: ADD RELATIONSHIP CONTROLLER.
        |--------------------------------------------------------------------------
        */
        public function addItem(Request $req) {

            $rules = array(
                'name'      =>  'required|unique:mfn_relationship,name'
            );

            $attributesNames = array(
                'name'      =>  'Relationship'
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else {
                $mfnRelationship = new  MfnRelationship;
                $mfnRelationship->name =$req->name;
                $mfnRelationship->createdDate = Carbon::now();
                $mfnRelationship->save();
            
                $data = array(
                    'responseTitle' =>   'Success!',
                    'responseText'  =>   'New Relationship has been saved successfully.'
                );
                
                return response::json($data);
            }
        }
         /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: GET DATA FOR UPDATE RELATIONSHIP CONTROLLER.
        |--------------------------------------------------------------------------
        */

        public function getRelationshipInfo(Request $req){

          $mfnRelationship = MfnRelationship::find($req->id);

          $data =array(
              'mfnRelationship'=>$mfnRelationship

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
                'name'       => 'required|unique:mfn_relationship,name,'.$req->id,
            );

            $attributesNames = array(
                'name'       => 'Relationship',
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else {

                $mfnRelationship = MfnRelationship::find($req->id);
                $mfnRelationship->name = $req->name;
                $mfnRelationship->save();
                $data = array(
                    'responseTitle' =>   'Success!',
                    'responseText'  =>   'Relationship has been updated successfully.'
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
            MfnRelationship::find($req->id)->delete();
            
            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Your selected Relationship deleted successfully.'
            );

            return response()->json($data);
        }
    }