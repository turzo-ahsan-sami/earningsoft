<?php

    namespace App\Http\Controllers\microfin\member;

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
    use App\microfin\member\MfnMemberType;
    use App\Http\Controllers\microfin\MicroFinance;


    class MfnMemberTypeController extends Controller {

        private $TCN;

        public function __construct() {

            $this->TCN = array(
                array('SL No.', 70), 
                array('Name', 0),
                array('Action', 100)
            );
        }

        public function index() {

            $mfnMemberType = MfnMemberType::all();
            
            $TCN = $this->TCN;

            return view('microfin.settings.memberType.viewMemberTypeList',['mfnMemberType'=>$mfnMemberType, 'TCN' => $TCN]);
        }

        public function addMemberTypeForm() {

             return view('microfin.settings.memberType.addMemberType');
        }

        /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: ADD MemberTYpe CONTROLLER.
        |--------------------------------------------------------------------------
        */
        public function addItem(Request $req) {

            $rules = array(
                'name'  =>  'required|unique:mfn_member_type,name'
            );

            $attributesNames = array(
                'name'  =>  'Member Type'
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else {
                $mfnMemberType = new  MfnMemberType;
                $mfnMemberType->name =$req->name;
                $mfnMemberType->createdDate = Carbon::now();
                $mfnMemberType->save();
                $logArray = array(
                        'moduleId'  => 6,
                        'controllerName'  => 'MfnMemberTypeController',
                        'tableName'  => 'mfn_member_type',
                        'operation'  => 'insert',
                        'primaryIds'  => [DB::table('mfn_member_type')->max('id')]
                        );
                    Service::createLog($logArray);
            
                $data = array(
                    'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
                    'responseText'   =>  MicroFinance::getMessage('memberTypeCreateSuccess'),
                );
                
                return response::json($data);
            }
        }

         /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: GET DATA FOR UPDATE MEMBER TYPE CONTROLLER.
        |--------------------------------------------------------------------------
        */

        public function getMemberTypeInfo(Request $req){

            $mfnMemberType = MfnMemberType::find($req->id);

            $data = array(
                'mfnMemberType'  =>  $mfnMemberType
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
                'name'       => 'required|unique:mfn_member_type,name,'.$req->id,
            );

            $attributesNames = array(
                'name'       => 'Member Type name',
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else {

                $mfnMemberType = MfnMemberType::find($req->id);
                $mfnMemberType->name = $req->name;
                $mfnMemberType->save();

               $data = array(
                    'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
                    'responseText'   =>  MicroFinance::getMessage('memberTypeUpdateSuccess'),
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
            MfnMemberType::find($req->id)->delete();
            
            $data = array(
                'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
                'responseText'   =>  MicroFinance::getMessage('memberTypeDelSuccess'),
            );

            return response()->json($data);
        }
    }