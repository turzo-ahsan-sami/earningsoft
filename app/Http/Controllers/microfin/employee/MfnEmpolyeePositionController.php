<?php

    namespace App\Http\Controllers\microfin\employee;

    use Illuminate\Http\Request;
    use App\Http\Requests;
    use Validator;
    use App\microfin\employee\MfnEmpolyeePosition;
    use Response;
    use DB;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\Hash;
    use App\Http\Controllers\Controller;
    use App\microfin\settings\MfnDesignation;


    class MfnEmpolyeePositionController extends Controller {

        private $TCN;

        public function __construct() {

            $this->TCN = array(
                array('SL No.', 70), 
                array('Name', 0),
                array('Action', 100)
            );
        }

        public function index() {

            $mfnEmployeePositions = MfnEmpolyeePosition::all();
            
            $TCN = $this->TCN;

            return view('microfin.employee.employeePosition.viewEmployeePosition',['mfnEmployeePositions'=>$mfnEmployeePositions, 'TCN' => $TCN]);
        }

        public function addEmployeePositionForm() {

             return view('microfin.employee.employeePosition.addEmployeePosition');
        }

        /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: ADD RELATIONSHIP CONTROLLER.
        |--------------------------------------------------------------------------
        */

        public function addItem(Request $req) {

            $rules = array(
                'name'      =>  'required|unique:hr_settings_position,name'
            );

            $attributesNames = array(
                'name'      =>  'Designation'
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else {

                $mfnEmployeePosition = new  MfnEmpolyeePosition;
                $mfnEmployeePosition->name =$req->name;
                $mfnEmployeePosition->grade_id_fk =0;
                $mfnEmployeePosition->level_id_fk =0;
                $mfnEmployeePosition->department_id_fk =0;
                $mfnEmployeePosition->created_by =0;
                $mfnEmployeePosition->updated_by =0;
                $mfnEmployeePosition->created_at = Carbon::now();
                $mfnEmployeePosition->save();

                $data = array(
                    'responseTitle' =>   'Success!',
                    'responseText'  =>   'New Designation has been saved successfully.'
                );
                
                return response::json($data);
            }
        }

         /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: GET DATA FOR UPDATE EMPLOYEE POSITION CONTROLLER.
        |--------------------------------------------------------------------------
        */

        public function getEmployeePositionInfo(Request $req){

            $mfnEmpolyeePosition = MfnEmpolyeePosition::find($req->id);
            $data =array(
                'mfnEmpolyeePosition'=>$mfnEmpolyeePosition
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
                'name'       => 'required|unique:hr_settings_position,name,'.$req->id,
            );

            $attributesNames = array(
                'name'       => 'Designation',
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else {
                $mfnEmpolyeePosition = MfnEmpolyeePosition::find($req->id);

                $mfnEmpolyeePosition->name = $req->name;
                $mfnEmpolyeePosition->save();
                $data = array(
                    'responseTitle' =>   'Success!',
                    'responseText'  =>   'Designation has been updated successfully.'
                );
                
                return response()->json($data);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: DELETE EMPLOYEE POSITION CONTROLLER.
        |--------------------------------------------------------------------------
        */
        public function deleteItem(Request $req) {
            MfnEmpolyeePosition::find($req->id)->delete();
            
            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Your selected Designation deleted successfully.'
            );

            return response()->json($data);
        }
    }