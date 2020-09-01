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
    use App\microfin\settings\MfnProfession;


    class MfnProfessionController extends Controller {

        private $TCN;

        public function __construct() {

            $this->TCN = array(
                array('SL No.', 70), 
                array('Name', 0),
                array('Action', 100)
            );
        }

        public function index() {

            $mfnProfession = MfnProfession::all();
            
            $TCN = $this->TCN;

            return view('microfin.settings.profession.viewProfession',['mfnProfession'=>$mfnProfession, 'TCN' => $TCN]);
        }

        public function addProfessionForm() {

             return view('microfin.settings.profession.addProfession');
        }

        /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: ADD RELATIONSHIP CONTROLLER.
        |--------------------------------------------------------------------------
        */
        public function addItem(Request $req) {

            $rules = array(
                'name'      =>  'required|unique:mfn_profession,name'
            );

            $attributesNames = array(
                'name'      =>  'Profession'
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else {
                $MfnProfession = new  MfnProfession;
                $MfnProfession->name =$req->name;
                $MfnProfession->createdDate = Carbon::now();
                $MfnProfession->save();
            
                $data = array(
                    'responseTitle' =>   'Success!',
                    'responseText'  =>   'New Profession has been saved successfully.'
                );
                
                return response::json($data);
            }
        }
         /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: GET DATA FOR UPDATE RELATIONSHIP CONTROLLER.
        |--------------------------------------------------------------------------
        */

        public function getProfessionInfo(Request $req){

          $mfnProfession = MfnProfession::find($req->id);

          $data =array(
              'mfnProfession'=>$mfnProfession

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
                'name'       => 'required|unique:mfn_profession,name,'.$req->id,
            );

            $attributesNames = array(
                'name'       => 'Profession',
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else {

                $mfnProfession = MfnProfession::find($req->id);
                $mfnProfession->name = $req->name;
                $mfnProfession->save();
                $data = array(
                    'responseTitle' =>   'Success!',
                    'responseText'  =>   'Profession has been updated successfully.'
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
            MfnProfession::find($req->id)->delete();
            
            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Your selected Profession deleted successfully.'
            );

            return response()->json($data);
        }
    }