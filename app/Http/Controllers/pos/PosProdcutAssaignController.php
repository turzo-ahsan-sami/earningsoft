<?php

namespace App\Http\Controllers\pos;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\pos\PosProductAssaign;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class PosProdcutAssaignController extends Controller
{
    public function index(){
      $PosProductAssaigns = PosProductAssaign::all();
      return view('pos/productAssaign/viewProductAssing',['PosProductAssaigns' => $PosProductAssaigns]);
      
    }

    public function addProductAssaign(){
      return view('pos/productAssaign/addProductAssaign');
    }
    
//insert function
  public function addItem(Request $req) {
         $rules = array(
                'clientCompanyId'           => 'required',
                'productId'                 => 'required',
                'salesPriceHo'              => 'required',
                'salesPriceBo'              => 'required',
                'serviceChargeHo'           => 'required',
                'serviceChargeBo'           => 'required',
                'paymentNumbere'            => 'required',
                'totalAmount'               => 'required',
              );
 			   $attributeNames = array(
                'clientCompanyId'           => 'Company Name',
                'productId'                 => 'Contact Person',
                'salesPriceHo'              => 'Sales Price Head Office',
                'salesPriceBo'              => 'Sales Price Branch',
                'serviceChargeHo'           => 'Service Charge Head office',
                'serviceChargeBo'           => 'Service Charge Branch',
                'paymentNumbere'            => 'Payment Number',
                'totalAmount'               =>  'Total Amount'
        );

      $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
      		return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  	else{
      
            $PosProductAssaign = new PosProductAssaign;
            $PosProductAssaign->clientcompanyId        = $req->clientCompanyId;
            $PosProductAssaign->productId              = $req->productId;
            $PosProductAssaign->salesPerson            = json_encode($req->salesPerson);
            $PosProductAssaign->salesPriceHo           = $req->salesPriceHo;
            $PosProductAssaign->salesPriceBo           = $req->salesPriceBo;
            $PosProductAssaign->servicePerson          = json_encode($req->servicePerson);
            $PosProductAssaign->serviceChargeHo        = $req->serviceChargeHo;
            $PosProductAssaign->serviceChargeBo        = $req->serviceChargeBo;
            $PosProductAssaign->paymentNumber          = $req->paymentNumbere;
            //$PosProductAssaign->paymentNumber          = $req->paymentNumbere;
            $PosProductAssaign->createdDate            = Carbon::now();
            $PosProductAssaign->save();  

    		return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
        }
  }

  public function getClientData(Request $req){
        $PosProductAssaign = PosProductAssaign::find($req->id);
         $productIds =  DB::table('pos_product')->where('id',$PosProductAssaign->productId)->value('productPackge');

        if($productIds!=null){
            $productStr =  str_replace(array('"', '[', ']'),'', $productIds);
            $productArr = array_map('intval', explode(',', $productStr));
            $productName='';
            
            foreach ($productArr as $key => $prodcutId) {
                $temp = DB::table('pos_product')->where('id',$prodcutId)->value('name');
                if ($key==0) {
                    $productName=$temp;
                }else{
                    $productName=$productName.', '.$temp;
                }
            }
        } else {
            $productName='';
        }

            $salesPersonStr =  str_replace(array('"', '[', ']'),'', $PosProductAssaign->salesPerson);
            $salesPersonArr = array_map('intval', explode(',', $salesPersonStr));
           
            $servicePersonStr =  str_replace(array('"', '[', ']'),'', $PosProductAssaign->servicePerson);
            $servicePersonArr = array_map('intval', explode(',', $servicePersonStr));

         $data =array(
              'PosProductAssaign'     => $PosProductAssaign,
              'productName'           => $productName,
              'salesPersonArr'        => $salesPersonArr,
              'servicePersonArr'      => $servicePersonArr
        );

         return response()->json($data);
   }      

//edit function
public function editItem(Request $req) {
         $rules = array(
                'clientCompanyId'           => 'required',
                'productId'                 => 'required',
                'salesPriceHo'              => 'required',
                'salesPriceBo'              => 'required',
                'serviceChargeHo'           => 'required',
                'serviceCharge'             => 'required',
                
              );
         $attributeNames = array(
                'clientCompanyId'           => 'Company Name',
                'productId'                 => 'Contact Person',
                'salesPriceHo'              => 'Sales Price Head Office',
                'salesPriceBo'              => 'Sales Price Branch',
                'serviceChargeHo'           => 'Service Charge Head office',
                'serviceCharge'             => 'Service Charge Branch',
        );
      $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
      else{
      $PosProductAssaign = PosProductAssaign::find ($req->id);
            $PosProductAssaign->clientcompanyId        = $req->clientCompanyId;
            $PosProductAssaign->productId              = $req->productId;
            $PosProductAssaign->salesPriceHo           = $req->salesPriceHo;
            $PosProductAssaign->salesPriceBo           = $req->salesPriceBo;
            $PosProductAssaign->serviceChargeHo        = $req->serviceChargeHo;
            $PosProductAssaign->serviceChargeBo        = $req->serviceCharge;
            $PosProductAssaign->salesPerson            = json_encode($req->selesPerson);
            $PosProductAssaign->servicePerson          = json_encode($req->servicePerson);
            $PosProductAssaign->save();  

      return response()->json('success');
    }
    }
    public function productAssignDetails(Request $req){
       $productAssignIds =  PosProductAssaign::where('id',$req->id)->get();
      foreach ($productAssignIds as $productAssignId) {
        $salesPriceHo    = $productAssignId->salesPriceHo;
        $salesPriceBo    = $productAssignId->salesPriceBo;
        $serviceChargeHo = $productAssignId->serviceChargeHo;
        $serviceChargeBo = $productAssignId->serviceChargeBo;
        
        $companyName = DB::table('pos_client')->select('clientCompanyName')->where('id',$productAssignId->clientcompanyId)->first();

        $productNamee= DB::table('pos_product')->select('name')->where('id',$productAssignId->productId)->first();

        $productIds =  DB::table('pos_product')->where('id',$productAssignId->productId)->value('productPackge');
        if($productIds!=null){
            $productStr =  str_replace(array('"', '[', ']'),'', $productIds);
            $productArr = array_map('intval', explode(',', $productStr));
            $productName='';
            
            foreach ($productArr as $key => $prodcutId) {
                $temp = DB::table('pos_product')->where('id',$prodcutId)->value('name');
                if ($key==0) {
                    $productName=$temp;
                }else{
                    $productName=$productName.', '.$temp;
                }
            }
        } else {
            $productName='';
        }

            $salesPersonStr =  str_replace(array('"', '[', ']'),'', $productAssignId->salesPerson);
            $salesPersonArr = array_map('intval', explode(',', $salesPersonStr));
            $salesPerson='';
            
            foreach ($salesPersonArr as $key => $salesPersonId) {
                $tempSalesPerson = DB::table('hr_emp_general_info')->where('id',$salesPersonId)->select(DB::raw("CONCAT(emp_id ,' - ', emp_name_english ) AS name"))->value('name');
                if ($key==0) {
                    $salesPerson=$tempSalesPerson;
                }else{
                    $salesPerson=$salesPerson.', '.$tempSalesPerson;
                }
            }

            $servicePersonStr =  str_replace(array('"', '[', ']'),'', $productAssignId->servicePerson);
            $servicePersonArr = array_map('intval', explode(',', $servicePersonStr));
            $servicePerson='';
            
            foreach ($servicePersonArr as $key => $servicePersonId) {
                $tempServicePerson = DB::table('hr_emp_general_info')->where('id',$servicePersonId)->select(DB::raw("CONCAT(emp_id ,' - ', emp_name_english ) AS name"))->value('name');
                if ($key==0) {
                    $servicePerson=$tempServicePerson;
                }else{
                    $servicePerson=$servicePerson.', '.$tempServicePerson;
                }
            }
      }

       $data = array(
            'salesPriceHo'     => $salesPriceHo,
            'salesPriceBo'     => $salesPriceBo,
            'serviceChargeHo'  => $serviceChargeHo,
            'serviceChargeBo'  => $serviceChargeBo,
            'companyName'      => $companyName,
            'productNamee'     => $productNamee,
            'productName'      => $productName,
            'salesPerson'      => $salesPerson,
            'servicePerson'    => $servicePerson
       );
      return response()->json($data);

    }

 //delete Item
    public function deleteItem(Request $req) {
      PosProductAssaign::find($req->id)->delete();
      return response()->json();
    }

    public function onChangeProductId(Request $request) {
        $productId = (int)json_decode($request->productId);
        $productIds =  DB::table('pos_product')->where('id',$productId)->value('productPackge');
        if($productIds!=null){
            $productStr =  str_replace(array('"', '[', ']'),'', $productIds);
            $productArr = array_map('intval', explode(',', $productStr));
            $productName='';
            
            foreach ($productArr as $key => $prodcutId) {
                $temp = DB::table('pos_product')->where('id',$prodcutId)->value('name');
                if ($key==0) {
                    $productName=$temp;
                }else{
                    $productName=$productName.', '.$temp;
                }
            }
        } else {
            $productName='';
        }
        
        $data = array(            
            'productName' => $productName
            
        );
        return response()->json($data);
    }


}
