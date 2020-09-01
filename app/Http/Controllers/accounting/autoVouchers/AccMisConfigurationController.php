<?php

namespace App\Http\Controllers\accounting\autoVouchers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\accounting\AccMisType;
use App\accounting\AccMisConfiguration;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use DB;
use Carbon\Carbon;

use App\Http\Controllers\accounting\Accounting;

class AccMisConfigurationController extends Controller
{   
    protected $Accounting;

    public function __construct() {
        $this->Accounting = new Accounting;
    }
    public function index(){
        
        $moduleOption=array('' => '--Select Module--') + $this->Accounting->getModuleOption();

        $misConfigurationInfos = AccMisConfiguration::all();
        $misTypes=array('' => '--Select MIS Type--')  + $this->Accounting->getMisTypeOption();
        return view('accounting.autoVouchers.misConfiguration.viewMisConfiguration',['misConfigurationInfos' => $misConfigurationInfos, 'misTypes' => $misTypes, 'moduleOption' => $moduleOption]);
    }

    public function addMisConfiguration(){

        $moduleOption=array('' => '--Select Module--') + $this->Accounting->getModuleOption();
        $misTypeOption=array('' => '--Select MIS Type--')  + $this->Accounting->getMisTypeOption();

        return view('accounting.autoVouchers.misConfiguration.addMisConfiguration',['moduleOption'=> $moduleOption, 'misTypeOption'=> $misTypeOption]);
    }

    public function getMisTypeOption(Request $request){

        $misTypeOption=DB::table('acc_mis_type')->where('moduleId', $request->moduleId)->pluck('name', 'id')->toArray();

        return response()->json($misTypeOption); 
    }

    public function addMisConfigurationItem(Request $req) {
        $rules = array(
            'moduleId'          => 'required',
            'misTypeId_Fk'      => 'required',
            'misName'           => 'required',
            'tableFieldName'    => 'required'
        );
        $attributeNames = array(
            'moduleId'          => 'Module',
            'misTypeId_Fk'      => 'MIS Type',
            'misName'           => 'MIS Name',
            'tableFieldName'    => 'Table Field Name'
        );

 		$validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails())
        	return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  		else{
                $now = Carbon::now();
                // $now = date('Y-m-d H:i:s');

                $req->request->add(['createdDate' => $now]);
                $create = AccMisConfiguration::create($req->all());
                return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
        	}
    }

    public function updateMisConfigurationItem(Request $req) {
        $rules = array(
            'moduleId'          => 'required',
            'misTypeId_Fk'      => 'required',
            'misName'           => 'required',
            'tableFieldName'    => 'required'
        );
        $attributeNames = array(
            'moduleId'          => 'Module',
            'misTypeId_Fk'      => 'MIS Type',
            'misName'           => 'MIS Name',
            'tableFieldName'    => 'Table Field Name'
        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails())
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        else{
            $updateMisConfiguration = AccMisConfiguration::find ($req->id);
            $updateMisConfiguration->moduleId = $req->moduleId;
            $updateMisConfiguration->misTypeId_Fk = $req->misTypeId_Fk;
            $updateMisConfiguration->misName = $req->misName;
            $updateMisConfiguration->tableFieldName = $req->tableFieldName;
            $updateMisConfiguration->save();


            if ($updateMisConfiguration) {
                $data = array(
                            'responseTitle' =>  'Success!',
                            'responseText'  =>  'Mis Type Update Successful.'
                        );
            }else{
                $data = array(
                            'responseTitle' =>  'Warning!',
                            'responseText'  =>  'Mis Type Update Unsuccessful.'
                        );          
            }

            // $data = array(
            //     'updateMisConfiguration'     => $updateMisConfiguration,
            //     'slno'      => $req->slno
            // );
            return response()->json($data);
        }
    }

    public function deleteMisConfigurationItem(Request $req){
        $deleteMisType=AccMisConfiguration::find($req->id)->delete();
        // AccMisConfiguration::find($req->id)->delete();
//        $accountTypes = AddAccountType::all();

        if ($deleteMisType) {
            $data = array(
                        'responseTitle' =>  'Success!',
                        'responseText'  =>  ' Mis Type Delete Successful.'
                    );
        }else{
            $data = array(
                        'responseTitle' =>  'Warning!',
                        'responseText'  =>  ' Mis Type Delete Unsuccessful.'
                    );          
        }
        return response()->json($data);     //json($accountTypes);
        // return response()->json();     //json($accountTypes);
    }

}
