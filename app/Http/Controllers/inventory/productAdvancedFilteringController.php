<?php

namespace App\Http\Controllers\inventory;

use Illuminate\Http\Request;

use App\Http\Requests;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class productAdvancedFilteringController extends Controller
{


//group id change
    public function invPurchaseOnCngGrp(Request $req){
    	
    	if($req->supplierId && $req->productGroupId){
			$productName 		=  DB::table('inv_product')->where('supplierId',$req->supplierId)
															->where('groupId',$req->productGroupId)	
															->pluck('name', 'id');
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')
															->where('supplierId',$req->supplierId)
															->where('groupId',$req->productGroupId)
															->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')
															->where('supplierId',$req->supplierId)
															->where('groupId',$req->productGroupId)
															->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															->where('supplierId',$req->supplierId)
															->where('groupId',$req->productGroupId)
															->get();
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'catagoryName'    		 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->productGroupId && $req->supplierId==''){
			$productName 		=  DB::table('inv_product')->where('groupId',$req->productGroupId)->pluck('name', 'id');
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')->where('groupId',$req->productGroupId)->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')->where('groupId',$req->productGroupId)->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->where('groupId',$req->productGroupId)->get();
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'catagoryName'    		 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}
		else if($req->supplierId && $req->productGroupId==''){

			$productName 		=  DB::table('inv_product')->where('supplierId',$req->supplierId)->pluck('name', 'id');
			$groupIds 			=  DB::table('inv_product')->select('groupId')->where('supplierId',$req->supplierId)->get();
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')->where('supplierId',$req->supplierId)->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')->where('supplierId',$req->supplierId)->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->where('supplierId',$req->supplierId)->get();
			foreach($groupIds as $groupId){
				$groupName []	=  DB::table('inv_product_group')->select('name','id')->where('id',$groupId->groupId)->get();
			}
			$groupName = array_map("unserialize", array_unique(array_map("serialize", $groupName)));
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));
			
			$data = array(
			'productName' 		 	 => $productName,
			'groupName'    			 => $groupName,
			'catagoryName'    		 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
    	}

    	else if($req->productGroupId==''  && $req->supplierId==''){
			$productName 		=  DB::table('inv_product')->pluck('name', 'id');
			$groupIds 			=  DB::table('inv_product')->select('groupId')->get();
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->get();
			foreach($groupIds as $groupId){
				$groupName []	=  DB::table('inv_product_group')->select('name','id')->where('id',$groupId->groupId)->get();
			}
			$groupName = array_map("unserialize", array_unique(array_map("serialize", $groupName)));
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'groupName'    			 => $groupName,
			'catagoryName'    		 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

}

//catagory chanage function
public function invPurchaseOnCngCtg(Request $req){
    	
    	if($req->supplierId && $req->productGroupId && $req->productCategoryId){
			$productName 		=  DB::table('inv_product')->where('supplierId',$req->supplierId)
															->where('groupId',$req->productGroupId)	
															->where('categoryId',$req->productCategoryId)	
															->pluck('name', 'id');
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')
															->where('supplierId',$req->supplierId)
															->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)	
															->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															->where('supplierId',$req->supplierId)
															->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)	
															->get();
			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->productCategoryId && $req->supplierId=='' && $req->productGroupId==''){
			$productName 		=  DB::table('inv_product')->where('categoryId',$req->productCategoryId)->pluck('name', 'id');
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')->where('categoryId',$req->productCategoryId)->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->where('categoryId',$req->productCategoryId)->get();
			
			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->supplierId && $req->productGroupId  && $req->productCategoryId==''){
			$productName 		=  DB::table('inv_product')->where('supplierId',$req->supplierId)
															->where('groupId',$req->productGroupId)
															->pluck('name', 'id');
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')
															->where('supplierId',$req->supplierId)
															->where('groupId',$req->productGroupId)
															->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')
															->where('supplierId',$req->supplierId)
															->where('groupId',$req->productGroupId)
															->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															->where('supplierId',$req->supplierId)
															->where('groupId',$req->productGroupId)
															->get();
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'catagoryName' 		 	 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->supplierId && $req->productCategoryId  && $req->productGroupId==''){
			$productName 		=  DB::table('inv_product')->where('supplierId',$req->supplierId)
															->where('categoryId',$req->productCategoryId)
															->pluck('name', 'id');
			$groupIds	 		=  DB::table('inv_product')->select('groupId')
															->where('supplierId',$req->supplierId)
															->where('categoryId',$req->productCategoryId)
															->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')
															->where('supplierId',$req->supplierId)
															->where('categoryId',$req->productCategoryId)
															->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															->where('supplierId',$req->supplierId)
															->where('categoryId',$req->productCategoryId)
															->get();
			foreach($groupIds as $groupId){
				$groupName []	=  DB::table('inv_product_group')->select('name','id')->where('id',$groupId->groupId)->get();
			}
			$groupName = array_map("unserialize", array_unique(array_map("serialize", $groupName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'groupName' 		 	 => $groupName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}


		else if($req->productGroupId && $req->productCategoryId  && $req->supplierId==''){
			$productName 		=  DB::table('inv_product')->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)
															->pluck('name', 'id');
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')
															->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)
															->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)
															->get();
			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->supplierId && $req->productCategoryId==''  && $req->productGroupId==''){
			$productName 		=  DB::table('inv_product')->where('supplierId',$req->supplierId)->pluck('name', 'id');
			$groupIds 			=  DB::table('inv_product')->select('groupId')->where('supplierId',$req->supplierId)->get();
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')->where('supplierId',$req->supplierId)->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')->where('supplierId',$req->supplierId)->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->where('supplierId',$req->supplierId)->get();
			foreach($groupIds as $groupId){
				$groupName []	=  DB::table('inv_product_group')->select('name','id')->where('id',$groupId->groupId)->get();
			}
			$groupName = array_map("unserialize", array_unique(array_map("serialize", $groupName)));
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'groupName'    			 => $groupName,
			'catagoryName'    		 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->productGroupId && $req->productCategoryId==''  && $req->supplierId==''){
			$productName 		=  DB::table('inv_product')->where('groupId',$req->productGroupId)->pluck('name', 'id');
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')->where('groupId',$req->productGroupId)->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')->where('groupId',$req->productGroupId)->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->where('groupId',$req->productGroupId)->get();
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'catagoryName'    		 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->productGroupId=='' && $req->productCategoryId==''  && $req->supplierId==''){
			$productName 		=  DB::table('inv_product')->pluck('name', 'id');
			$groupIds 			=  DB::table('inv_product')->select('groupId')->get();
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->get();
			foreach($groupIds as $groupId){
				$groupName []	=  DB::table('inv_product_group')->select('name','id')->where('id',$groupId->groupId)->get();
			}
			$groupName = array_map("unserialize", array_unique(array_map("serialize", $groupName)));
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'groupName'    			 => $groupName,
			'catagoryName'    		 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}
}

// subcatory change
public function invPurchaseOnCngSubCtg(Request $req){

	if($req->supplierId && $req->productGroupId && $req->productCategoryId && $req->productSubCategoryId){
			$productName 		=  DB::table('inv_product')->where('supplierId',$req->supplierId)
															->where('groupId',$req->productGroupId)	
															->where('categoryId',$req->productCategoryId)
															->where('subCategoryId',$req->productSubCategoryId)	
															->pluck('name', 'id');
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															->where('supplierId',$req->supplierId)
															->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)
															->where('subCategoryId',$req->productSubCategoryId)	
															->get();
			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->productSubCategoryId && $req->productCategoryId=='' && $req->supplierId=='' && $req->productGroupId==''){
			$productName 		=  DB::table('inv_product')->where('subCategoryId',$req->productSubCategoryId)->pluck('name', 'id');
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->where('subCategoryId',$req->productSubCategoryId)->get();

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->productSubCategoryId && $req->supplierId && $req->productCategoryId==''  && $req->productGroupId==''){
			$productName 		=  DB::table('inv_product')->where('supplierId',$req->supplierId)
															->where('subCategoryId',$req->productSubCategoryId)
															->pluck('name', 'id');
			$groupIds	 		=  DB::table('inv_product')->select('groupId')
															->where('supplierId',$req->supplierId)
															->where('subCategoryId',$req->productSubCategoryId)
															->get();
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')
															->where('supplierId',$req->supplierId)
															->where('subCategoryId',$req->productSubCategoryId)
															->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															->where('supplierId',$req->supplierId)
															->where('subCategoryId',$req->productSubCategoryId)
															->get();
			foreach($groupIds as $groupId){
				$groupName []	=  DB::table('inv_product_group')->select('name','id')->where('id',$groupId->groupId)->get();
			}
			$groupName = array_map("unserialize", array_unique(array_map("serialize", $groupName)));

			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'groupName' 		 	 => $groupName,
			'catagoryName'    	 	 => $catagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->supplierId && $req->productGroupId && $req->productCategoryId && $req->productSubCategoryId==''){
			$productName 		=  DB::table('inv_product')->where('supplierId',$req->supplierId)
															->where('groupId',$req->productGroupId)	
															->where('categoryId',$req->productCategoryId)	
															->pluck('name', 'id');
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')
															->where('supplierId',$req->supplierId)
															->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)	
															->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															->where('supplierId',$req->supplierId)
															->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)	
															->get();
			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->supplierId && $req->productGroupId=='' && $req->productCategoryId=='' && $req->productSubCategoryId==''){
			$productName 		=  DB::table('inv_product')->where('supplierId',$req->supplierId)->pluck('name', 'id');
			$groupIds 			=  DB::table('inv_product')->select('groupId')->where('supplierId',$req->supplierId)->get();
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')->where('supplierId',$req->supplierId)->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')->where('supplierId',$req->supplierId)->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->where('supplierId',$req->supplierId)->get();
			foreach($groupIds as $groupId){
				$groupName []	=  DB::table('inv_product_group')->select('name','id')->where('id',$groupId->groupId)->get();
			}
			$groupName = array_map("unserialize", array_unique(array_map("serialize", $groupName)));
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'groupName'    			 => $groupName,
			'catagoryName'    		 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data);
		}

		else if($req->supplierId=='' && $req->productGroupId && $req->productCategoryId && $req->productSubCategoryId){
			$productName 		=  DB::table('inv_product')->where('groupId',$req->productGroupId)	
															->where('categoryId',$req->productCategoryId)
															->where('subCategoryId',$req->productSubCategoryId)	
															->pluck('name', 'id');
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)
															->where('subCategoryId',$req->productSubCategoryId)	
															->get();

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->supplierId=='' && $req->productGroupId=='' && $req->productCategoryId && $req->productSubCategoryId){
			$productName 		=  DB::table('inv_product')->where('categoryId',$req->productCategoryId)
															->where('subCategoryId',$req->productSubCategoryId)	
															->pluck('name', 'id');
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															->where('categoryId',$req->productCategoryId)
															->where('subCategoryId',$req->productSubCategoryId)	
															->get();

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->productSubCategoryId && $req->productGroupId && $req->supplierId=='' && $req->productCategoryId==''){
			$productName 		=  DB::table('inv_product')->where('groupId',$req->productGroupId)
															->where('subCategoryId',$req->productSubCategoryId)
															->pluck('name', 'id');
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')
															->where('groupId',$req->productGroupId)
															->where('subCategoryId',$req->productSubCategoryId)
															->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															->where('groupId',$req->productGroupId)
															->where('subCategoryId',$req->productSubCategoryId)
															->get();
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'catagoryName'    	 	 => $catagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->productSubCategoryId && $req->supplierId && $req->productCategoryId && $req->productGroupId==''){
			$productName 		=  DB::table('inv_product')->where('supplierId',$req->supplierId)
															->where('categoryId',$req->productCategoryId)
															->where('subCategoryId',$req->productSubCategoryId)
															->pluck('name', 'id');
			$groupIds 			=  DB::table('inv_product')->select('groupId')
															->where('supplierId',$req->supplierId)
															->where('categoryId',$req->productCategoryId)
															->where('subCategoryId',$req->productSubCategoryId)
															->get();										
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															->where('supplierId',$req->supplierId)
															->where('categoryId',$req->productCategoryId)
															->where('subCategoryId',$req->productSubCategoryId)
															->get();
			foreach($groupIds as $groupId){
				$groupName []	=  DB::table('inv_product_group')->select('name','id')->where('id',$groupId->groupId)->get();
			}
			$groupName = array_map("unserialize", array_unique(array_map("serialize", $groupName)));
															
			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'groupName' 		 	 => $groupName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 

		}

		else if($req->productSubCategoryId=='' && $req->supplierId=='' && $req->productCategoryId=='' && $req->productGroupId==''){
			$productName 		=  DB::table('inv_product')->pluck('name', 'id');
			$groupIds 			=  DB::table('inv_product')->select('groupId')->get();
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->get();
			foreach($groupIds as $groupId){
				$groupName []	=  DB::table('inv_product_group')->select('name','id')->where('id',$groupId->groupId)->get();
			}
			$groupName = array_map("unserialize", array_unique(array_map("serialize", $groupName)));
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'groupName'    			 => $groupName,
			'catagoryName'    		 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->productCategoryId && $req->supplierId=='' && $req->productSubCategoryId=='' && $req->productGroupId==''){
			$productName 		=  DB::table('inv_product')->where('categoryId', $req->productCategoryId)->pluck('name', 'id');
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')
															->where('categoryId',$req->productCategoryId)
															->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															->where('categoryId',$req->productCategoryId)
															->get();
			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->productCategoryId=='' && $req->supplierId=='' && $req->productSubCategoryId=='' && $req->productGroupId){
			$productName 		=  DB::table('inv_product')->where('groupId',$req->productGroupId)->pluck('name', 'id');
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')->where('groupId',$req->productGroupId)->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')->where('groupId',$req->productGroupId)->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->where('groupId',$req->productGroupId)->get();
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'catagoryName'    		 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data);
		}

		else if($req->productCategoryId && $req->supplierId=='' && $req->productSubCategoryId=='' && $req->productGroupId){
			$productName 		=  DB::table('inv_product')->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)
															->pluck('name', 'id');
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')
															->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)
															->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)
															->get();

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data);
		}

}

    // end purchase filtering
}
