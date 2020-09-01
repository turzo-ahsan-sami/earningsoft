<?php
namespace App\Http\Controllers\gnr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\accounting\AccAdvRegisterType;
use Validator;
use Illuminate\Support\Facades\Input;
use Response;
use Carbon\Carbon;
use DB;





class AccAdvRegisterController extends Controller
{


    public function viewRegisterType(){
        
        $accAdvRegisterType = AccAdvRegisterType::all();

        return view('accounting.register.advRegister.viewAdvRegister',['accAdvRegisterType'=>$accAdvRegisterType]);

    }

 
      public function createAdvRegisterType(){

        return view('accounting.register.advRegister.addAdvRegister');

      }

      public function addAdvRegisterType(Request $request){


         $rules = array(
                'name' => 'required|unique:acc_adv_register_type',
        );

          $attributeNames = array(
            'name'    => 'Register Type Name',
            
        );


        $validator = Validator::make(Input::all(), $rules);
         $validator->setAttributeNames($attributeNames);
        if ($validator->fails()) {
            return Response::json(array(

                    'errors' => $validator->getMessageBag()->toArray(),
            ));
        } else {
          $accAdvRegisterType = new  AccAdvRegisterType;
           $accAdvRegisterType->name = $request->input('name');
            $accAdvRegisterType->save();

           return response()->json('success');
        }
        

        }

        /* Edit Account Advance Register Data*/

        public function editAdvRegisterType(Request $request){

       
        $rules = array(
            'name' => 'required|unique:acc_adv_register_type',
                        
            
          );
        $attributeNames = array(
            'name' => 'Advance Registrar Type',
                  
        );

      $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
          if ($validator->fails()){
            return response::json(array('errors'=>$validator->getMessageBag()->toArray()));
          }


          ///Update Information
       else{

        

       
       $accAdvRegisterType = AccAdvRegisterType::find($request->id);
         
         $accAdvRegisterType->name=$request->name;
          $accAdvRegisterType->save();

       }

       return response::json('success');
    }

        

        public function deleteAdvRegisterType(Request $request){

           $accAdvRegisterType=AccAdvRegisterType::find($request->id);
            $accAdvRegisterType->delete();
           return response()->json ($request->id);

        }

     
}
    

