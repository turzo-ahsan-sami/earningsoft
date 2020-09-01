<?php

namespace App\Http\Controllers\pos;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\pos\PosClient;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class PosClientController extends Controller
{

    public function index(){
       $posClients = PosClient::all();
       return view('pos/client/viewClient',['posClients' => $posClients]);
    }

    public function addClient(){
       return view('pos/client/addClient');
    }
//insert function
  public function addItem(Request $req) {
    
         $rules = array(
                'clientCompanyName'   => 'required|unique:pos_client,clientCompanyName',
                'clientContactPerson' => 'required',
                'phone'               => 'required',
                'mobile'              => 'required',
                'email'               => 'required',
                'nationalId'          => 'required',
                'address'             => 'required',
         );
 			   $attributeNames = array(
                'clientCompanyName'   => 'Client Name ',
                'clientContactPerson' => 'Contact Person',
                'phone'               => 'Phone',
                'mobile'              => 'Mobile',
                'email'               => 'Email',
                'nationalId'          => 'National Id',
                'address'             => 'address',
         );

      $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
      		return response::json(array('errors' => $validator->getMessageBag()->toArray()));
      	else{
              $posClient = new PosClient;
              $posClient->clientCompanyName        = $req->clientCompanyName;
              $posClient->companyShortName        = $req->companyShortName;
              $posClient->clientContactPerson      = $req->clientContactPerson;
              $posClient->contactPersonDesigntion  = $req->clientContactPersonDesigntion;
              $posClient->phone                    = $req->phone;
              $posClient->mobile                   = $req->mobile;
              $posClient->email                    = $req->email;
              $posClient->nationalId               = $req->nationalId;
              $posClient->address                  = $req->address;
              $posClient->web                      = $req->web;
              $posClient->createdDate              = Carbon::now();
              $posClient->save();  

    		return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
        }
  }
    /*Data Details Function*/
      public function getClientData(Request $req){
         $posClient = PosClient::find($req->id);

         $data =array(
              'posClient'=> $posClient
         );

         return response()->json($data);
   }      

//edit function
      public function editItem(Request $req) {
          $rules = array(
              'companyName'         => 'required|unique:pos_client,clientCompanyName,'.$req->id,
              'clientPerson'        => 'required',
              'phone'               => 'required',
              'mobile'              => 'required',
              'email'               => 'required',
              'nationalId'          => 'required',
              'address'             => 'required',
          );
          $attributeNames = array(
              'companyName'         => 'Client Name ',
              'clientPerson'        => 'Contact Person',
              'phone'               => 'Phone',
              'mobile'              => 'Mobile',
              'email'               => 'Email',
              'nationalId'          => 'National Id',
              'address'             => 'address',
          );
      $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
          return response::json(array('errors' => $validator->getMessageBag()->toArray()));
      else{
            $posClient = PosClient::find ($req->id);

            $posClient->clientCompanyName        = $req->companyName;
            $posClient->companyShortName        = $req->companyShortName;
            $posClient->clientContactPerson      = $req->clientPerson;
            $posClient->contactPersonDesigntion  = $req->clientContactPersonDesigntion;
            $posClient->phone                    = $req->phone;
            $posClient->mobile                   = $req->mobile;
            $posClient->email                    = $req->email;
            $posClient->nationalId               = $req->nationalId;
            $posClient->address                  = $req->address;
            $posClient->web                      = $req->web;
            $posClient->createdDate              = Carbon::now();
            $posClient->save();  

      return response()->json('success');
    }
    }
 //delete Item
    public function deleteItem(Request $req) {
      PosClient::find($req->id)->delete();
      return response()->json();
    }   
}
