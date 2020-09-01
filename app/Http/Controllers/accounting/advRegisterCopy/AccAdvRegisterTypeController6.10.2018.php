<?php
namespace App\Http\Controllers\accounting\advRegister;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\accounting\AccAdvRegisterType;
use Validator;
use Illuminate\Support\Facades\Input;
use Response;
use Carbon\Carbon;
use DB;



  class AccAdvRegisterTypeController extends Controller {

        public function viewRegisterType() {
           $accAdvRegisterType = AccAdvRegisterType::all();

           return view('accounting.register.advRegister.viewAdvRegisterType',['accAdvRegisterType'=>$accAdvRegisterType]);

        }

        public function createAdvRegisterType() {

           return view('accounting.register.advRegister.addAdvRegisterType');

       }

       public function addAdvRegisterType(Request $request) {

                $rules = array(

                      'code'                    => 'required',
                      'name'                    => 'required|unique:acc_adv_register_type',
                );

                $attributeNames = array(
                     'code'                     => 'Register Type Code',
                     'name'                     => 'Register Type Name',
                  
                );


              $validator = Validator::make(Input::all(), $rules);
              $validator->setAttributeNames($attributeNames);

              if ($validator->fails()) {
                    return Response::json(array(
                    'errors' => $validator->getMessageBag()->toArray(),
                  ));

                 } 

              else {

                $accAdvRegisterType = new  AccAdvRegisterType;
                $accAdvRegisterType->code = $request->input('code');
                $accAdvRegisterType->name = $request->input('name');
                $accAdvRegisterType->createdAt = Carbon::now();
                $accAdvRegisterType->save();

                 return response()->json('success');
              }
        }

        /* Edit Account Advance Register Data*/

        public function updateAdvRegTypeInfo(Request $request) {

                $rules = array(
                    'code'          => 'required',
                    'name'          => 'required|unique:acc_adv_register_type,name,'.$request->id
                );

                $attributeNames = array(
                   'code'           => 'Advance Registrar Code',
                   'name'           => 'Advance Registrar Type Name',

                );

                $validator = Validator::make ( Input::all (), $rules);
                $validator->setAttributeNames($attributeNames);
                if ($validator->fails()) {
                    return response::json(array('errors'=>$validator->getMessageBag()->toArray()));
                  }
                  /*Update*/

                else {

                       $accAdvRegisterType = AccAdvRegisterType::find($request->id);
                       $accAdvRegisterType->code = $request->code;
                       $accAdvRegisterType->name = $request->name;
                       $accAdvRegisterType->save(); 

                    }

                 return response::json('success');
         }

       /*--------Delete Advance Register Type Data -----------*/

        public function getAdvRegType(Request $request) {
               $accAdvRegisterType=  AccAdvRegisterType::find($request->id);

               $data = array(

                    'accAdvRegisterType'      => $accAdvRegisterType
               );

            return response::json($data);

        }

        public function deleteAdvRegisterType(Request $request) {

             $accAdvRegisterType=AccAdvRegisterType::find($request->id);
             $accAdvRegisterType->delete();

             return response()->json ($request->id);

        }
    }
    

