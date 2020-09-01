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
    use App\microfin\savings\MfnSavingsDepositType;

    class MfnSavingsDepositTypeController extends Controller {
        use CreateForm;

        private $TCN;

        public function __construct() {

            $this->TCN = array(
                array('SL No.', 70),
                array('Name', 0),
                array('Code', 0),
                array('Action', 100)
            );
        }

        public function index() {

            $depositTypes = MfnSavingsDepositType::where('softDel',0)->get();

            $TCN = $this->TCN;
            $damageData = array(
                'TCN'               =>  $TCN,
                'depositTypes'      =>  $depositTypes
            );

            return view('microfin.savings.depositType.viewDepositType',['damageData'=>$damageData]);
        }

        public function addDepositType() {

            return view('microfin.savings.depositType.addDepositType');
        }

        /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: ADD MemberTYpe CONTROLLER.
        |--------------------------------------------------------------------------
        */
        public function storeDepositType(Request $req) {

            $rules = array(
                'name'   =>  'required|unique:mfn_savings_deposit_type,name',
                'code'   =>  'required|unique:mfn_savings_deposit_type,code'
            );

            $attributesNames = array(
                'name'   =>  'Name',
                'code'   =>  'Code'
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else {
                // Store Data

                $depositType = new MfnSavingsDepositType;
                $depositType->name         = $req->name;
                $depositType->code         = str_pad($req->code, 3, '0', STR_PAD_LEFT) ;
                $depositType->status       = $req->status;
                $depositType->createdDate  = Carbon::now();
                $depositType->save();

                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  'Deposit Type inserted successfully.'
                );

                return response::json($data);                
            }            
        }

        public function updateDepositType(Request $req) {

            $rules = array(
                'name'   =>  'required|unique:mfn_savings_deposit_type,name,'.$req->depositTypeId,
                'code'   =>  'required|unique:mfn_savings_deposit_type,code,'.$req->depositTypeId
            );

            $attributesNames = array(
                'name'   =>  'Name',
                'code'   =>  'Code'
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else {
                // Store Data

                $depositType = MfnSavingsDepositType::find($req->depositTypeId);
                $depositType->name         = $req->name;
                $depositType->code         = str_pad($req->code, 3, '0', STR_PAD_LEFT) ;
                $depositType->status       = $req->status;
                $depositType->save();

                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  'Deposit Type updated successfully.'
                );

                return response::json($data);                
            }            
        }

        public function deleteDepositType(Request $req) {
            $depositType = MfnSavingsDepositType::find($req->id);
            $depositType->softDel = 1;
            $depositType->save();

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Deposit Type deleted successfully.'
            );
            
            return response::json($data);   
        }
        
    }

        