<?php

namespace App\Http\Controllers\fams;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\fams\FamsDepreciation as FamsDep;
use App\fams\FamsDepDetails;
use App\fams\FamsWriteOff;
use App\fams\FamsSale;
use App\fams\FamsProduct;
use App\fams\FamsAdditionalCharge;
use App\gnr\GnrBranch;
use Validator;
use Response;
use App\Http\Controllers\gnr\Service;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

class FamsDepWriteOffController extends Controller
{
  public function index()
  {
    // $depreciationGroups = FamsDep::groupBy('depGroupId')->orderBy('id','desc')->get();
    $depreciationGroups = FamsDep::orderBy('id','desc')->get();
    $depreciations = FamsDep::all();
    $depDetails = FamsDepDetails::all();
    $branches = GnrBranch::all();
    $lastDepId = FamsDep::max('id');
    $lastDepDate = FamsDep::where('id',$lastDepId)->value('createdDate');
    if ($lastDepDate=="" || $lastDepDate==null) {
      $lastDepDate = "2016-06-30";
    }

    $prefix = DB::table('fams_product_prefix')->where('status',1)->value('name');

    return view('fams/dep_and_writeOff/viewDep',['depreciationGroups'=>$depreciationGroups,'depreciations' => $depreciations,'depDetails'=>$depDetails,'branches'=>$branches,'lastDepDate'=>$lastDepDate,'prefix'=>$prefix]);
  }

  public function viewWriteOff()
  {
    $writeOffs = FamsWriteOff::orderBy('id','desc')->get();
    $prefix = DB::table('fams_product_prefix')->where('status',1)->value('name');
    return view('fams/dep_and_writeOff/viewWriteOff',['writeOffs'=>$writeOffs,'prefix'=>$prefix]);
  }

  public function writeOff()
  {
      //$writeOffProducts = DB::table('fams_write_off')->pluck('id');
      //$products = DB::table('fams_product')->whereNotIn('id', $writeOffProducts)->pluck('id'))->get();
      //$products = DB::table('fams_product')->where('id',1)->get();
    $productTypes = DB::table('fams_product_type')->select('id','name','productTypeCode')->get();
    $productNames = DB::table('fams_product_name')->select('id','name','productNameCode')->get();
    $prefix = DB::table('fams_product_prefix')->where('status',1)->value('name');        

    return view('fams/dep_and_writeOff/writeOff',['productTypes'=>$productTypes,'productNames'=>$productNames,'prefix'=>$prefix]);
  }

  

  public function generateDep(Request $request)
  {


   $depEndDate = date('Y-m-d', strtotime($request->depEndDate));

   $groupDepNo = FamsDep::max('groupDepNo')+1;
   $FgroupDepNo = str_pad( $groupDepNo, 6, "0", STR_PAD_LEFT );
   $depGroupId = "DP". $FgroupDepNo;

   $branchHavingProduct = DB::table('fams_product')->where('projectId',$request->depProject)->distinct()->pluck('branchId');
        //echo $branchHavingProduct;
   if ($request->depBranch!='') {
    $branches = DB::table('gnr_branch')->where('id',$request->depBranch)->whereIn('id',$branchHavingProduct)->get();
  }
  else{
    $branches = DB::table('gnr_branch')->whereIn('id',$branchHavingProduct)->get();
  }


        //$products = FamsProduct::all();

  foreach ($branches as $branch) {

    $last_dep_date_for_all_products = null;
    $data = FamsDep::where('projectId',$request->depProject)->where('branchId',$branch->id)->first();
    if ($data != null) {

      $createdDate = FamsDep::where('projectId',$request->depProject)->where('branchId',$branch->id)->orderBy('id','desc')->value('createdDate');

      if($createdDate==null){

        $last_dep_date_for_all_products = Carbon::create(2016, 6, 30, 0);
      }
      else{

        $last_dep_date_for_all_products = Carbon::parse($createdDate);
      }

    }
    else{
      $last_dep_date_for_all_products = Carbon::create(2016, 6, 30, 0);
    }



    $today =  Carbon::createFromFormat('d-m-Y', $request->depEndDate)->hour(0)->minute(0)->second(0);
    $daysDifference = 0;

    if ($last_dep_date_for_all_products) {
     $daysDifference = $today->diffInDays($last_dep_date_for_all_products);    
   }

   if($daysDifference<= 0){
     return redirect('famsDep')->with('deptodayAlreadyCreated','Depreciation is already generated Today.');
   }


   $is_any_product = FamsProduct::first();
   if($is_any_product==null){
    return redirect('famsDep')->with('noProduct','There is no Product for generating Depreciation.');
  }


  if($daysDifference>0 || $last_dep_date_for_all_products==null){


    $lastDepforThisBranch = (int) FamsDep::where('branchId',$branch->id)->max('branchDepNo')+1;
    $formatedlastDepforThisBranch = str_pad( $lastDepforThisBranch, 5, "0", STR_PAD_LEFT );
    $formatedBranchCode = str_pad( $branch->branchCode, 3, "0", STR_PAD_LEFT );
    $depId = "DP".$formatedBranchCode.$formatedlastDepforThisBranch;
    $amount = 0;

    $dep = new FamsDep;
    $dep->depId = $depId;
    $dep->depGroupId = $depGroupId;
    $dep->groupDepNo = $groupDepNo;
    $dep->branchId = $branch->id;
    $dep->projectId = $request->depProject;
    $dep->branchDepNo = $lastDepforThisBranch;
          $dep->createdDate = $today;//Carbon::now();
          $dep->amount = $amount;          
          $dep->save();

          $lastDepId = FamsDep::max('id');

          $products = DB::table('fams_product')->where('projectId',$request->depProject)->where('branchId',$branch->id)->get();

          foreach ($products as $product) {


            if ($product->usefulLifeYear==0 && $product->usefulLifeMonth==0) {
              continue;
            }

            $product_purchaseDate = date('Y-m-d', strtotime($product->purchaseDate));


            if($product_purchaseDate >= $depEndDate){
              continue;
            }

            if($product->branchId==$branch->id){

                  //Check is the product is write offed or not
              $isWriteOffed = FamsWriteOff::where('productId',$product->id)->get();
                  //Check is the product is sold out or not
              $isSoldOut = FamsSale::where('productId',$product->id)->get();


                //If the product is write offed or sold out then skip this product
              if(sizeof($isWriteOffed)>0 || sizeof($isSoldOut)>0){
                continue;                  
              }   


              $pastDep =  (float) FamsDepDetails::where('productId',$product->id)->sum('amount');
              $totalCostWithAdditionalCharge = FamsAdditionalCharge::where('productId',$product->id)->sum('amount') + $product->totalCost;
              $depOpeningBalance = (float) DB::table('fams_product')->where('id',$product->id)->value('depreciationOpeningBalance');
              $resaleValue = (float) DB::table('fams_product')->where('id',$product->id)->value('resellValue');

              // echo "Past Dep: ".$pastDep."<br>";
              // echo "Total Cost: ".$totalCostWithAdditionalCharge."<br>";
              // echo "Dep. Opening Balance: ".$depOpeningBalance."<br><br>";
              //$initialDepAmount = 0;
              //$initialDays = 0;

              $depDetailData = FamsDepDetails::where('productId',$product->id)->first();
              if ($depDetailData!=null) {
                $last_dep_date = Carbon::parse(FamsDepDetails::where('productId',$product->id)->orderBy('id', 'desc')->value('depTo'));
                $product_dep_from = Carbon::parse($last_dep_date)->addDay();
              }
              else{
                  //$initialDepAmount = $depOpeningBalance;
                  //$initialDays = Carbon::parse($product->depOpeningDate)->diffInDays(Carbon::parse($product->purchaseDate)) - 1;


                if (date('Y-m-d', strtotime($product->purchaseDate)) > date('Y-m-d', strtotime($product->depOpeningDate))) {
                 $last_dep_date = Carbon::parse($product->purchaseDate)->subDay();
                 $product_dep_from = Carbon::parse($product->purchaseDate);
               }
               else{
                $last_dep_date = Carbon::parse($product->depOpeningDate)->subDay();
                $product_dep_from = Carbon::parse($product->depOpeningDate);
              }

            }


            $daysdiff = $today->diffInDays($last_dep_date);               


            $product_amount=0;
            $totalDepPerDayForAddCharge = (float) DB::table('fams_additional_charge')->where('productId',$product->id)->value('depDayforProduct');

            $product_depIdNo = $lastDepId;
               //$product_dep_from = Carbon::parse($last_dep_date)->addDay();
            $product_dep_to = $today;
            $product_days = $daysdiff;

            $usefulLifeExpireDate = Carbon::parse($product->purchaseDate)->addYears($product->usefulLifeYear)->addMonths($product->usefulLifeMonth);

            $daysCount = 0;
            if(($totalCostWithAdditionalCharge - $depOpeningBalance - $pastDep - $resaleValue) <= 0){
              $product_amount = 0;
            }
            else{

              for($i=1; $i<=$product_days; $i++){

                if(($totalCostWithAdditionalCharge - $depOpeningBalance - $pastDep -$resaleValue )>(float)($i * ((float)$product->depreciationAmountPerDay + $totalDepPerDayForAddCharge))){
                  $product_amount = $product_amount + (float)((float)$product->depreciationAmountPerDay + $totalDepPerDayForAddCharge);
                  $daysCount++;
                }
                else{
                  $product_amount = $totalCostWithAdditionalCharge - $depOpeningBalance - $pastDep - $resaleValue;
                  $daysCount++;
                  break;
                }

              }



                /*if($usefulLifeExpireDate->diffInDays($today)==1 || $last_dep_date->diffInDays($today)>=$last_dep_date->diffInDays($usefulLifeExpireDate)){
                
                  $product_amount = $totalCostWithAdditionalCharge - $depOpeningBalance - $pastDep - $resaleValue;
                }*/
                
               /* else{
                  $product_amount = 0;
                  

                  for($i=1; $i<=$product_days; $i++){

                    if(($totalCostWithAdditionalCharge - $depOpeningBalance - $pastDep -$resaleValue )>(float)($i * ((float)$product->depreciationAmountPerDay + $totalDepPerDayForAddCharge))){
                      $product_amount = $product_amount + (float)(((float)$product->depreciationAmountPerDay + $totalDepPerDayForAddCharge));
                      $daysCount++;
                    }
                    else{
                      $product_amount = $totalCostWithAdditionalCharge - $depOpeningBalance - $pastDep - $resaleValue;
                      $daysCount++;
                      break;
                    }

                  }
                  
                                    
                }*/

              }


              $amount = $amount + $product_amount;

              if ($daysCount<$product_days) {
               $product_days = $daysCount;
             }
               //echo "productId: ".$product->id."  DaysCount: ".$product_days."<br>";
               //echo "productId: ".$product->id."  InitialDay: ".$initialDays."<br>";
               //echo "productId: ".$product->id."  InitialAmount: ".$initialDepAmount."<br>";



               /*if ($last_dep_date->gt($usefulLifeExpireDate)) {
                 $product_days = 0;
               }

               elseif ($product_amount==0) {
                 $product_days = 0;
               }

               elseif ($today->gte($usefulLifeExpireDate) && $last_dep_date->lt($usefulLifeExpireDate)) {
                 $product_days = $last_dep_date->diffInDays($usefulLifeExpireDate) - 1;
               }*/
               /*if ($product_amount = 0) {
                 $product_days = 0;
               }*/


               $depDetails = new FamsDepDetails;
               $depDetails->depIdNo = $product_depIdNo;
               $depDetails->depGroupIdNo = $depGroupId;
               $depDetails->productId = $product->id;
               $depDetails->productCode = $product->productCode;
               $depDetails->branchId = $branch->id;
               $depDetails->projectId = $request->depProject;
               $depDetails->depFrom = $product_dep_from;
               $depDetails->depTo = $product_dep_to;
               $depDetails->days = $product_days;
               $depDetails->amount = $product_amount;  
               
               $depDetails->save(); 

             }

           }           


           $hasAnyproductInThisDep = FamsDepDetails::where('depIdNo',$lastDepId)->first();

           if($hasAnyproductInThisDep==null){

            FamsDep::find($lastDepId)->delete();              

          }
          else{
           $currentDep =  FamsDep::find($lastDepId); 
           $currentDep->amount = $amount;
           $currentDep->save();
         }

         $logArray = array(
          'moduleId'  => 2,
          'controllerName'  => 'FamsDepWriteOffController',
          'tableName'  => 'fams_depreciation',
          'operation'  => 'insert',
          'primaryIds'  => [DB::table('fams_depreciation')->max('id')]
        );
         Service::createLog($logArray); 

       }
     }



     return redirect('famsDep');

   }


   public function generateWriteOff(Request $request)
   {

    $user_id = Auth::id();

    $productId = (int) json_decode($request->productId);


    $isWriteOffed = DB::table('fams_write_off')                              
    ->where('.productId',$productId)                              
    ->get();

    if(sizeof($isWriteOffed)>0){
        //echo "Write offed";
      return response()->json("It is already Write Offed."); 
    }

    else{ 

     $branchOfThisProduct = (int) DB::table('fams_product')->where('id',$request->productId)->value('branchId');
     $branchCodeOfThisProduct = DB::table('gnr_branch')->where('id',$branchOfThisProduct)->value('branchCode');
     $formatedBranchCode = str_pad( $branchCodeOfThisProduct, 3, "0", STR_PAD_LEFT );

     $lastWriteOffId = FamsWriteOff::max('id')+1;
     $lastWriteOffForThisBranch = (int) FamsWriteOff::where('branchId',$branchOfThisProduct)->max('branchWriteOffNo')+1;
     $formatedlastWriteOffforThisBranch = str_pad( $lastWriteOffForThisBranch, 5, "0", STR_PAD_LEFT );

     $product_total_cost = (float) json_decode($request->totalCost);

     $dep_generated = (float) json_decode($request->accuDep);
     $dep_remaining = (float) json_decode($request->remainingdep);
     $resale_value = (float) json_decode($request->resaleValue);



     $writeOff = new FamsWriteOff;
     $writeOff->writeOffId = "WF".$formatedBranchCode.$formatedlastWriteOffforThisBranch;
     $writeOff->productId = (int) json_decode($request->productId);
     $writeOff->branchId = $branchOfThisProduct;
     $writeOff->branchWriteOffNo = $lastWriteOffForThisBranch;
     $writeOff->amount = $product_total_cost - $dep_generated;
     $writeOff->lossAmount = $dep_remaining;
     $writeOff->productTotalCost = $product_total_cost;
     $writeOff->productAdditionalCharge = DB::table('fams_additional_charge')->where('productId',$request->productId)->sum('amount');
     $writeOff->depGenerated = $dep_generated;
     $writeOff->remainingDep = $product_total_cost - $dep_generated;
     $writeOff->createdDate = Carbon::createFromFormat('d-m-Y', $request->writeOffDate)->hour(0)->minute(0)->second(0);
     $writeOff->writeOffByUserId = $user_id;
     $writeOff->save();         

   }
   $logArray = array(
    'moduleId'  => 2,
    'controllerName'  => 'FamsDepWriteOffController',
    'tableName'  => 'fams_write_off',
    'operation'  => 'insert',
    'primaryIds'  => [DB::table('fams_write_off')->max('id')]
  );
   Service::createLog($logArray);       

   return response()->json("success");
 }

 public function deleteWriteOff(Request $request)
 { 
  $previousdata=FamsWriteOff::find($request->writeOffId);
  FamsWriteOff::where('id',$request->writeOffId)->delete();
  $logArray = array(
    'moduleId'  => 2,
    'controllerName'  => 'FamsDepWriteOffController',
    'tableName'  => 'fams_write_off',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);
  return redirect('famsViewWriteOff')->with('writeOffDelete','Record is Deleted Successfully.');;
}



public function getProductInfo(Request $request)
{
  $productId = (int) json_decode($request->productId);

  if ($request->productId!="") {
    $product = FamsProduct::find($productId);
    $depOpeningBalance = DB::table('fams_product')->where('id',$productId)->value('depreciationOpeningBalance');
    $totalCost = (float) DB::table('fams_product')->where('id',$productId)->value('totalCost');
    $resaleValue = (float) DB::table('fams_product')->where('id',$productId)->value('resellValue');
    $lastDepId = (int) DB::table('fams_depreciation_details')->where('productId',$productId)->max('id');
    if ($lastDepId>0) {
      $lastDepDate = Carbon::parse(DB::table('fams_depreciation_details')->where('id',$lastDepId)->value('depTo'))->subDay()->toDateTimeString();
    }
    else{
      $lastDepDate = null;
    }


    $additionalCharge = FamsAdditionalCharge::where('productId',$productId)->sum('amount');
    $dep = (float) FamsDepDetails::where('productId',$product->id)->sum('amount');
    if ( ($product->totalCost + $additionalCharge - $dep - $depOpeningBalance) < 0) {
      $writeOff = 0;
    }
    else{
      $writeOff = $product->getOriginal()['totalCost'] + $additionalCharge - $dep - $depOpeningBalance;
    }


          //Write Off Id
    $branchOfThisProduct = (int) DB::table('fams_product')->where('id',$productId)->value('branchId');
    $branchCodeOfThisProduct = DB::table('gnr_branch')->where('id',$branchOfThisProduct)->value('branchCode');
    $formatedBranchCode = str_pad( $branchCodeOfThisProduct, 3, "0", STR_PAD_LEFT );

    $lastWriteOffId = FamsWriteOff::max('id')+1;
    $lastWriteOffForThisBranch = (int) FamsWriteOff::where('branchId',$branchOfThisProduct)->max('branchWriteOffNo')+1;
    $formatedlastWriteOffforThisBranch = str_pad( $lastWriteOffForThisBranch, 5, "0", STR_PAD_LEFT );

    $writeOffId = "WF".$formatedBranchCode.$formatedlastWriteOffforThisBranch;


    $data = array(
      'purchaseDate' => $product->purchaseDate, 
      'costPrice' => $product->totalCost, 
      'additionalCharge' => $additionalCharge, 
      'totalCost' => $product->totalCost + $additionalCharge, 
      'dep' =>  round($dep + $depOpeningBalance,2),          
      'remainingdep' =>  round(($product->getOriginal()['totalCost'] + $additionalCharge - $dep - $depOpeningBalance),2),          
      'depOpeningBalance' => $depOpeningBalance,
      'writeOff' => round(($product->getOriginal()['totalCost'] + $additionalCharge - $dep - $depOpeningBalance),2),          
      'resaleValue' => $resaleValue,
      'lastDepDate' => $lastDepDate,
      'writeOffId' => $writeOffId
    );  

    return response()->json($data);    
  }

}


public function deleteDep(Request $request)
{

 $previousdata=FamsDep::find($request->id);
 //dd($request->id);
 FamsDep::where('depGroupId',$request->depGroupId)->delete();
 FamsDepDetails::where('depGroupIdNo',$request->depGroupId)->delete();
 $logArray = array(
  'moduleId'  => 2,
  'controllerName'  => 'FamsDepWriteOffController',
  'tableName'  => 'fams_depreciation',
  'operation'  => 'delete',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
 Service::createLog($logArray);
 return redirect('famsDep')->with('depDelete','Record is Deleted Successfully.');
}

public function getDepDetails(Request $request)
{
  $depGroupId = $request->depGroupId;
  $depreciations = DB::table('fams_depreciation')->where('depGroupId',$depGroupId)->get();
  $depIds = DB::table('fams_depreciation')->where('depGroupId',$depGroupId)->pluck('id');
  $depDetails = DB::table('fams_depreciation_details')->whereIn('depIdNo',$depIds)->get();
  $branch = DB::table('gnr_branch')->select('id','name')->get();
  $product = DB::table('fams_product')->select('id','name','productCode','totalCost','depreciationPercentage','purchaseDate')->get();

  $data = array(
    'depreciations' => $depreciations,
    'depDetails' => $depDetails,
    'branch' => $branch,
    'product' => $product
  );

  return response::json($data);
}

}
