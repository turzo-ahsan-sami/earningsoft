<?php

namespace App\Http\Controllers\fams;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\fams\FamsProduct;
use App\fams\FamsPurchase;
use App\fams\FamsDepDetails;
use App\gnr\GnrProject;
use App\gnr\GnrProjectType;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;



class FamsProductController extends Controller
{
    public function index(Request $request)
    {

        $user_branch_id = Auth::user()->branchId;
        $user_project_id = (int) DB::table('gnr_branch')->where('id', $user_branch_id)->value('projectId');
        $user_project_type_id = (int) DB::table('gnr_branch')->where('id', $user_branch_id)->value('projectTypeId');

        $projectId = array();
        $projectTypeId = array();
        $branchId = array();
        $categoryId = array();
        $productTypeId = array();

        //Project
        if ($request->searchProject == null) {

            if ($user_branch_id == 1) {
                $projectSelected = null;
                $projectId = DB::table('gnr_project')->pluck('id');
            } else {
                $projectSelected = $user_project_id;
                array_push($projectId, $projectSelected);
            }
        } else {
            $projectSelected = (int) json_decode($request->searchProject);
            array_push($projectId, $projectSelected);
        }

        //Project Type
        if ($request->searchProjectType == null) {

            if ($user_branch_id == 1) {
                $projectTypeSelected = null;
                $projectTypeId = DB::table('gnr_project_type')->pluck('id');
            } else {
                $projectTypeSelected = $user_project_type_id;
                array_push($projectTypeId, $projectTypeSelected);
            }
        } else {
            $projectTypeSelected = (int) json_decode($request->searchProjectType);
            array_push($projectTypeId, $projectTypeSelected);
        }

        //Branch
        if ($request->searchBranch == null) {

            if ($user_branch_id == 1) {
                $branchSelected = null;
                $branchId = DB::table('gnr_branch')->pluck('id');
            } else {
                $branchSelected = $user_branch_id;
                array_push($branchId, $branchSelected);
            }
        } else {
            $branchSelected = (int) json_decode($request->searchBranch);

            if ($request->searchBranch == 0) {
                $branchId = DB::table('gnr_branch')->where('id', '!=', 1)->pluck('id');
            } else {
                array_push($branchId, $branchSelected);
            }
        }

        //Category
        if ($request->searchCategory == null) {
            $categorySelected = null;
            $categoryId = DB::table('fams_product_category')->pluck('id');
        } else {
            $categorySelected = (int) json_decode($request->searchCategory);
            array_push($categoryId, $categorySelected);
        }

        //Product Type
        if ($request->searchProductType == null) {
            $productTypeSelected = null;
            $productTypeId = DB::table('fams_product_type')->pluck('id');
        } else {
            $productTypeSelected = (int) json_decode($request->searchProductType);
            array_push($productTypeId, $productTypeSelected);
        }




        $products = DB::table('fams_product')->select('id', 'name', 'productCode', 'depreciationOpeningBalance', 'depreciationPercentage', 'branchId', 'projectId', 'projectTypeId', 'totalCost', 'purchaseDate')->whereIn('projectId', $projectId)->whereIn('projectTypeId', $projectTypeId)->whereIn('branchId', $branchId)->whereIn('categoryId', $categoryId)->whereIn('productTypeId', $productTypeId)->orderBy('id', 'desc')->paginate(20);
        if ($request->page == null || $request->page == 0) {
            $currentPage = 1;
        } else {
            $currentPage = $request->page;
        }

        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $groups = DB::table('fams_product_group')->get();
        $suppliers = DB::table('gnr_supplier')->get();
        $categories = DB::table('fams_product_category')->get();
        $subCategories = DB::table('fams_product_sub_category')->get();
        $productTypes = DB::table('fams_product_type')->whereIn('productCategoryId', $categoryId)->get();
        $branches = DB::table('gnr_branch')->whereIn('projectId', $projectId)->orWhere('id', 1)->get();
        $brands = DB::table('fams_product_brand')->get();
        $models = DB::table('fams_product_model')->get();
        $depDetails = FamsDepDetails::all();

        $projects = DB::table('gnr_project')->get();
        $projectTypes = DB::table('gnr_project_type')->whereIn('projectId', $projectId)->get();


        $prefix = DB::table('fams_product_prefix')->where('status', 1)->value('name');


        return view('fams/product/productSetting/product/viewFamsProduct', ['products' => $products, 'groups' => $groups, 'categories' => $categories, 'subCategories' => $subCategories, 'branches' => $branches, 'brands' => $brands, 'models' => $models, 'depDetails' => $depDetails, 'suppliers' => $suppliers, 'productTypes' => $productTypes, 'projectSelected' => $projectSelected, 'branchSelected' => $branchSelected, 'prefix' => $prefix, 'projects' => $projects, 'projectTypes' => $projectTypes, 'projectTypeSelected' => $projectTypeSelected, 'categorySelected' => $categorySelected, 'productTypeSelected' => $productTypeSelected, 'currentPage' => $currentPage]);
    }

    public function addFamsProduct()
    {
        $userBranchId = Auth::user()->branchId;
        $branches = DB::table('gnr_branch')->select('id', 'name', 'branchCode')->get();
        $prefix = DB::table('fams_product_prefix')->where('status', 1)->value('name');
        return view('fams/product/productSetting/product/addFamsProduct', ['userBranchId' => $userBranchId, 'branches' => $branches, 'prefix' => $prefix]);
    }

    //insert function
    public function storeProduct(Request $req)
    {

        $userId = Auth::id();
        $prefix = DB::table('fams_product_prefix')->where('status', 1)->value('name');

        $lastRow = DB::table('fams_product')->max('id') + 1;
        $assetNo = DB::table('fams_product')->max('assetNo') + 1;

        //Project Asset No
        $trasferProductProjectAssetNo = (int) DB::table('fams_tra_transfer')->where('projectIdFrom', $req->projectId)->max('oldProjectAssetNo');
        $productTableProjectAssetNo = (int) DB::table('fams_product')->where('projectId', $req->projectId)->max('projectAssetNo');
        if ($trasferProductProjectAssetNo > $productTableProjectAssetNo) {
            $projectAssetNo = $trasferProductProjectAssetNo + 1;
        } else {
            $projectAssetNo = $productTableProjectAssetNo + 1;
        }

        //Project Type Asset No
        $trasferProductProjectTypeAssetNo = (int) DB::table('fams_tra_transfer')->where('projectIdFrom', $req->projectId)->where('projectTypeIdFrom', $req->projectType)->max('oldProjectTypeAssetNo');
        $productTableProjectTypeAssetNo = (int) DB::table('fams_product')->where('projectId', $req->projectId)->where('projectTypeId', $req->projectType)->max('projectTypeAssetNo');
        if ($trasferProductProjectTypeAssetNo > $productTableProjectTypeAssetNo) {
            $projectTypeAssetNo = $trasferProductProjectTypeAssetNo + 1;
        } else {
            $projectTypeAssetNo = $productTableProjectTypeAssetNo + 1;
        }

        //Branch Asset No
        $trasferProductBranchAssetNo = (int) DB::table('fams_tra_transfer')->where('branchIdFrom', $req->branchId)->max('oldBranchAssetNo');
        $productTableBranchAssetNo = (int) DB::table('fams_product')->where('branchId', $req->branchId)->max('branchAssetNo');
        if ($trasferProductBranchAssetNo > $productTableBranchAssetNo) {
            $branchAssetNo = $trasferProductBranchAssetNo + 1;
        } else {
            $branchAssetNo = $productTableBranchAssetNo + 1;
        }

        $trasferProductProjectTypeAssetNo = (int) DB::table('fams_tra_transfer')->where('projectTypeIdFrom', $req->productTypeId)->max('oldProjectTypeAssetNo');
        $productTableProjectTypeAssetNo = (int) DB::table('fams_product')->where('productTypeId', $req->productTypeId)->max('productTypeAssetNo');
        if ($trasferProductProjectTypeAssetNo > $productTableProjectTypeAssetNo) {
            $productTypeAssetNo = $trasferProductProjectTypeAssetNo + 1;
        } else {
            $productTypeAssetNo = $productTableProjectTypeAssetNo + 1;
        }
        //$productTypeAssetNo = DB::table('fams_product')->where('productTypeId', $req->productTypeId)->max('productTypeAssetNo') + 1;

        $prefix = DB::table('fams_product_prefix')->where('status', 1)->value('name');

        $pieces = explode("-", $req->productCode);

        $FassetNo = str_pad($assetNo, 5, "0", STR_PAD_LEFT);
        $FprojectAssetNo = str_pad($projectAssetNo, 4, "0", STR_PAD_LEFT);
        $FbranchAssetNo = str_pad($branchAssetNo, 4, "0", STR_PAD_LEFT);
        $FproductTypeAssetNo = str_pad($productTypeAssetNo, 4, "0", STR_PAD_LEFT);

        if ($prefix === null) {
            $productCode = $FassetNo . "-" . $pieces[1] . "-" . $FprojectAssetNo . "-" . $pieces[3] . "-" . $FbranchAssetNo . "-" . $pieces[5] . "-" . $FproductTypeAssetNo;
        } else {
            $productCode = $FassetNo . "-" . $pieces[2] . "-" . $FprojectAssetNo . "-" . $pieces[4] . "-" . $FbranchAssetNo . "-" . $pieces[6] . "-" . $FproductTypeAssetNo;
        }

        $product = new FamsProduct;

        $productImageFilename = $warrantyImageFilename = $billImageFilename = $additionalVoucherImageFilename = "";

        if ($req->file('productImage')) {
            $productImageFile = $req->file('productImage');
            //$filename = $file->getClientOriginalName();
            $productImageFilename = $userId . 'pro' . str_random(10) . $lastRow . '.' . $productImageFile->getClientOriginalExtension();
            $destinationPath = base_path() . '/public/images/fams/product/';
            $productImageFile->move($destinationPath, $productImageFilename);
        }
        if ($req->file('warrantyImage')) {
            $warrantyImageFile = $req->file('warrantyImage');
            //$filename = $file->getClientOriginalName();
            $warrantyImageFilename = $userId . 'war' . str_random(10) . $lastRow . '.' . $warrantyImageFile->getClientOriginalExtension();
            $destinationPath = base_path() . '/public/images/fams/product/';
            $warrantyImageFile->move($destinationPath, $warrantyImageFilename);
        }
        if ($req->file('billImage')) {
            $billImageFile = $req->file('billImage');
            //$filename = $file->getClientOriginalName();
            $billImageFilename = $userId . 'bil' . str_random(10) . $lastRow . '.' . $billImageFile->getClientOriginalExtension();
            $destinationPath = base_path() . '/public/images/fams/product/';
            $billImageFile->move($destinationPath, $billImageFilename);
        }
        if ($req->file('additionalVoucherImage')) {
            $additionalVoucherImageFile = $req->file('additionalVoucherImage');
            //$filename = $file->getClientOriginalName();
            $additionalVoucherImageFilename = $userId . 'adv' . str_random(10) . $lastRow . '.' . $additionalVoucherImageFile->getClientOriginalExtension();
            $destinationPath = base_path() . '/public/images/fams/product/';
            $additionalVoucherImageFile->move($destinationPath, $additionalVoucherImageFilename);
        }

        $purchaseDate = Carbon::createFromFormat('d-m-Y', $req->purchaseDate)->hour(0)->minute(0)->second(0);
        $warrantyExpireDate = Carbon::createFromFormat('d-m-Y', $req->warrantyExpireDate2)->hour(0)->minute(0)->second(0);
        $serviceWarrantyExpireDate = Carbon::createFromFormat('d-m-Y', $req->serviceWarrantyExpireDate2)->hour(0)->minute(0)->second(0);

        $product->assetNo = $assetNo;
        $product->name = DB::table('fams_product_name')->where('id', (int) json_decode($req->productName))->value('name');
        $product->productNameId = $req->productName;
        $product->productCode = $productCode;
        $product->description = $req->description;
        $product->supplierId = $req->supplierId;
        $product->groupId = $req->productGroupId;
        $product->categoryId = $req->productCategoryId;
        $product->subCategoryId = $req->productSubCategoryId;
        $product->brandId = $req->brandId;
        $product->modelId = $req->productModelId;
        $product->sizeId = $req->sizeId;
        $product->colorId = $req->colorId;
        $product->uomId = $req->uomId;
        $product->productImage = $productImageFilename;
        $product->warrantyCardImage = $warrantyImageFilename;
        $product->billImage = $billImageFilename;
        $product->additionalVoucherImage = $additionalVoucherImageFilename;
        if ($req->warrantyYear != "") {
            $product->warrantyYear = $req->warrantyYear;
        }
        if ($req->warrantyMonth != "") {
            $product->warrantyMonth = $req->warrantyMonth;
        }
        if ($req->serviceWarrantyYear != "") {
            $product->serviceWarrantyYear = $req->serviceWarrantyYear;
        }
        if ($req->serviceWarrantyMonth != "") {
            $product->serviceWarrantyMonth = $req->serviceWarrantyMonth;
        }
        $product->usefulLifeYear = $req->usefulLifeYear;
        $product->usefulLifeMonth = $req->usefulLifeMonth;
        $product->costPrice = $req->costPrice;
        $product->additionalCost1 = $req->additionalCost1;
        $product->additionalCost2 = $req->additionalCost2;
        $product->totalCost = $req->total;
        if ($req->depOpeningBalance != "") {
            $product->depreciationOpeningBalance = $req->depOpeningBalance;
        }




        ///////////////////////
        $useFulLifeEndDate = $purchaseDate->copy()->addYears($req->usefulLifeYear);
        $useFulLifeEndDate->addMonthsNoOverflow($req->usefulLifeMonth);

        $numberOfDays = $purchaseDate->diffInDays($useFulLifeEndDate);

        //////////////////////




        $totalCost = (float) json_decode($req->total);
        $years = (float) $req->usefulLifeYear;
        $months = (float) $req->usefulLifeMonth + (12 * $years);
        $days = $numberOfDays; //($years*365) + ((float) $req->usefulLifeMonth * 30);

        if ($years == 0) {
            $depPerYear = 0;
        } else {
            $depPerYear = $totalCost / $years;
        }

        if ($months == 0) {
            $depPerMonth = 0;
        } else {
            $depPerMonth = $totalCost / $months;
        }

        if ($days == 0) {
            $depPerDay = 0;
        } else {
            // $depPerDay = $totalCost / $days;

            $depPerDay = $totalCost / ($years*365);
        }



        $product->depreciationAmountPerYear = $depPerYear;
        $product->depreciationAmountPerMonth = $depPerMonth;
        $product->depreciationAmountPerDay = $depPerDay;
        $product->depreciationPercentage = $req->depPercentage;
        $product->purchaseDate = $purchaseDate;
        $product->warrantyExpireDate = $warrantyExpireDate;
        $product->serviceWarrantyExpireDate = $serviceWarrantyExpireDate;
        $product->resellValue = $req->resellValue;
        $product->entryBy = $userId;
        $product->branchId = $req->branchId;
        $product->branchAssetNo = $branchAssetNo; //DB::table('fams_product')->where('branchId', $req->branchId)->max('branchAssetNo') + 1;
        $product->projectId = $req->projectId;
        $product->projectAssetNo = $projectAssetNo; //DB::table('fams_product')->where('projectId', $req->projectId)->max('projectAssetNo') + 1;
        $product->subCategoryAssetNo = DB::table('fams_product')->where('subCategoryId', $req->productSubCategoryId)->max('subCategoryAssetNo') + 1;
        $product->projectTypeId = $req->projectType;
        $product->projectTypeAssetNo = $projectTypeAssetNo;

        $product->productTypeId = $req->productTypeId;
        $product->productTypeAssetNo = $productTypeAssetNo; //DB::table('fams_product')->where('productTypeId', $req->productTypeId)->max('productTypeAssetNo') + 1;
        $product->createdDate = Carbon::today();

        $product->save();


        $purchase = new FamsPurchase;

        $purchase->supplierId = $req->supplierId;
        $purchase->productId = $product->where('productCode', $productCode)->value('id');
        $purchase->productCode = $productCode;
        $purchase->costPrice = $req->costPrice;
        $purchase->projectId = $req->projectId;
        $purchase->branchId = $req->branchId;
        $purchase->purchaseDate = $purchaseDate;

        $purchase->save();

        $lastProduct = FamsProduct::where('productCode', $productCode)->first();
        $pieces = explode("-", $productCode);
        $ambalaAssetNo = (int) $pieces[0];
        $projectAssetNo = (int) $pieces[2];
        $branchAssetNo = (int) $pieces[4];
        $productTypeAssetNo = (int) $pieces[6];

        $lastProductAssetNo = (int) $lastProduct->assetNo;


        $numProducts = (int) $req->productQuantity;
        for ($i = 1; $i < $numProducts; $i++) {

            $ambalaAssetNo = $ambalaAssetNo + 1;
            $formatedAmbalaAssetNo = str_pad($ambalaAssetNo, 5, "0", STR_PAD_LEFT);

            $projectAssetNo = $projectAssetNo + 1;
            $formatedProjectAssetNo = str_pad($projectAssetNo, 4, "0", STR_PAD_LEFT);

            $branchAssetNo = $branchAssetNo + 1;
            $formatedBranchAssetNo = str_pad($branchAssetNo, 4, "0", STR_PAD_LEFT);

            $productTypeAssetNo = $productTypeAssetNo + 1;
            $formatedProductTypeAssetNo = str_pad($productTypeAssetNo, 4, "0", STR_PAD_LEFT);

            $newProductCode = $formatedAmbalaAssetNo . "-" . $pieces[1] . "-" . $formatedProjectAssetNo . "-" . $pieces[3] . "-" . $formatedBranchAssetNo . "-" . $pieces[5] . "-" . $formatedProductTypeAssetNo;

            $newProduct = $lastProduct->replicate();
            $newProduct->assetNo = $lastProductAssetNo + $i;
            $newProduct->projectAssetNo = $projectAssetNo;
            $newProduct->branchAssetNo = $branchAssetNo;
            $newProduct->productTypeAssetNo = $productTypeAssetNo;
            $newProduct->productCode = $newProductCode;
            $newProduct->save();

            $purchase = new FamsPurchase;

            $purchase->supplierId = $req->supplierId;
            $purchase->productId = $product->where('productCode', $newProductCode)->value('id');
            $purchase->productCode = $newProductCode;
            $purchase->costPrice = $req->costPrice;
            $purchase->projectId = $req->projectId;
            $purchase->branchId = $req->branchId;
            $purchase->purchaseDate = $purchaseDate;

            $purchase->save();
        }

        $logArray = array(
            'moduleId'  => 2,
            'controllerName'  => 'FamsProductController',
            'tableName'  => 'fams_product',
            'operation'  => 'insert',
            'primaryIds'  => [DB::table('fams_product')->max('id')]
        );
        Service::createLog($logArray);


        //$req->session()->flash('alert-success', 'Data successful added!');

        return redirect('viewFamsProduct');
    }

    //edit function
    public function editProduct(Request $req)
    {


        $user = Auth::user();
        $userId = $user->id;
        $lastRow = DB::table('fams_product')->max('id') + 1;
        //$lastRow = DB::table('fams_product')->max('id') + 1;


        $productImageFilename = $warrantyImageFilename = $billImageFilename = $additionalVoucherImageFilename = "";

        if ($req->file('editModalProductImage')) {

            $productImageFile = $req->file('editModalProductImage');
            //$filename = $file->getClientOriginalName();
            $productImageFilename = $userId . 'pro' . str_random(10) . $lastRow . '.' . $productImageFile->getClientOriginalExtension();
            $destinationPath = base_path() . '/public/images/fams/product/';
            $productImageFile->move($destinationPath, $productImageFilename);
        }
        if ($req->file('editModalWarrantyImage')) {
            $warrantyImageFile = $req->file('editModalWarrantyImage');
            //$filename = $file->getClientOriginalName();
            $warrantyImageFilename = $userId . 'war' . str_random(10) . $lastRow . '.' . $warrantyImageFile->getClientOriginalExtension();
            $destinationPath = base_path() . '/public/images/fams/product/';
            $warrantyImageFile->move($destinationPath, $warrantyImageFilename);
        }
        if ($req->file('editModalBillImage')) {
            $billImageFile = $req->file('editModalBillImage');
            //$filename = $file->getClientOriginalName();
            $billImageFilename = $userId . 'bil' . str_random(10) . $lastRow . '.' . $billImageFile->getClientOriginalExtension();
            $destinationPath = base_path() . '/public/images/fams/product/';
            $billImageFile->move($destinationPath, $billImageFilename);
        }
        if ($req->file('editModalAdditionalVoucherImage')) {
            $additionalVoucherImageFile = $req->file('editModalAdditionalVoucherImage');
            //$filename = $file->getClientOriginalName();
            $additionalVoucherImageFilename = $userId . 'adv' . str_random(10) . $lastRow . '.' . $additionalVoucherImageFile->getClientOriginalExtension();
            $destinationPath = base_path() . '/public/images/fams/product/';
            $additionalVoucherImageFile->move($destinationPath, $additionalVoucherImageFilename);
        }

        $purchaseDate = Carbon::createFromFormat('d-m-Y', $req->editModalPurchaseDate)->hour(0)->minute(0)->second(0);
        $warrantyExpireDate = Carbon::createFromFormat('d-m-Y', $req->editModalWarrantyExpireDate)->hour(0)->minute(0)->second(0);
        $serviceWarrantyExpireDate = Carbon::createFromFormat('d-m-Y', $req->editModalSeviceWarrantyExpireDate)->hour(0)->minute(0)->second(0);

        // $previousdata = FamsProduct::find($req->id);
        $product = FamsProduct::find($req->productId);
        $product->name = DB::table('fams_product_name')->where('id', (int) json_decode($req->editModalProductName))->value('name');
        $product->productNameId = $req->editModalProductName;

        $pieces = explode("-", $req->editModalProductCode);

        $prefix = DB::table('fams_product_prefix')->where('status', 1)->value('name');

        if ($prefix == null) {
            $product->productCode = $pieces[0] . '-' . $pieces[1] . '-' . $pieces[2] . '-' . $pieces[3] . '-' . $pieces[4] . '-' . $pieces[5] . '-' . $pieces[6];
        } else {
            $product->productCode = $pieces[1] . '-' . $pieces[2] . '-' . $pieces[3] . '-' . $pieces[4] . '-' . $pieces[5] . '-' . $pieces[6] . '-' . $pieces[7];
        }


        $product->description = $req->editModalDescription;
        $product->supplierId = $req->editModalSupplierId;
        $product->groupId = $req->editModalGroup;
        $product->categoryId = $req->editModalCategory;
        $product->subCategoryId = $req->editModalSubCategory;
        $product->brandId = $req->editModalBrand;
        $product->modelId = $req->editModalModel;
        $product->sizeId = $req->editModalSize;
        $product->colorId = $req->editModalColor;
        $product->uomId = $req->editModalUOM;
        $product->purchaseDate = $purchaseDate;

        if ($productImageFilename != "") {
            \File::delete('images/fams/product/' . $product->productImage);
            $product->productImage = $productImageFilename;
        }
        if ($warrantyImageFilename != "") {
            \File::delete('images/fams/product/' . $product->warrantyCardImage);
            $product->warrantyCardImage = $warrantyImageFilename;
        }
        if ($billImageFilename != "") {
            \File::delete('images/fams/product/' . $product->billImage);
            $product->billImage = $billImageFilename;
        }
        if ($additionalVoucherImageFilename != "") {
            \File::delete('images/fams/product/' . $product->additionalVoucherImage);
            $product->additionalVoucherImage = $additionalVoucherImageFilename;
        }

        $product->warrantyYear = $req->editModalWarrantyYear;
        $product->warrantyMonth = $req->editModalWarrantyMonth;
        $product->serviceWarrantyYear = $req->editModalServiceWarrantyYear;
        $product->serviceWarrantyMonth = $req->editModalServiceWarrantyMonth;
        $product->warrantyExpireDate = $warrantyExpireDate;
        $product->serviceWarrantyExpireDate = $serviceWarrantyExpireDate;
        $product->branchId = $req->editModalBranch;
        $product->projectId = $req->editModalProject;
        $product->projectTypeId = $req->editModalProjectType;
        $product->costPrice = $req->editModalCostPrice;
        $product->additionalCost1 = $req->editModalAdditionalCost1;
        $product->additionalCost2 = $req->editModalAdditionalCost2;
        $product->totalCost = $req->editModalTotalCost;
        $product->resellValue = $req->editModalResellValue;
        $product->usefulLifeYear = $req->editModalUsefulLifeYear;
        $product->usefulLifeMonth = $req->editModalUsefulLifeMonth;


        ///////////////////////
        $useFulLifeEndDate = $purchaseDate->copy()->addYears($req->editModalUsefulLifeYear);
        $useFulLifeEndDate->addMonthsNoOverflow($req->editModalUsefulLifeMonth);

        $numberOfDays = $purchaseDate->diffInDays($useFulLifeEndDate);



        $totalCost = (float) json_decode($req->editModalTotalCost);
        $years = (float) $req->editModalUsefulLifeYear;
        $months = (float) $req->editModalUsefulLifeMonth + (12 * $years);
        $days = $numberOfDays; //($years*365) + ((float) $req->usefulLifeMonth * 30);

        if ($years == 0) {
            $depPerYear = 0;
        } else {
            $depPerYear = $totalCost / $years;
        }

        if ($months == 0) {
            $depPerMonth = 0;
        } else {
            $depPerMonth = $totalCost / $months;
        }

        if ($days == 0) {
            $depPerDay = 0;
        } else {
            // $depPerDay = $totalCost / $days;
            $depPerDay = $totalCost / ($years*365);
        }

        //////////////////////

        $product->depreciationOpeningBalance = $req->editModalDepOpeningBalance;
        $product->depreciationAmountPerYear = $depPerYear; //$req->editModalDepAmountPerYear;
        $product->depreciationAmountPerMonth = $depPerMonth; //$req->editModalDepAmountPerMonth;
        $product->depreciationAmountPerDay = $depPerDay; //$req->editModalDepAmountPerDay;
        $product->depreciationPercentage = $req->editModalDepPercentage;
        $product->productTypeId = $req->editModalProductType;
        $product->updatedBy = $userId;

        if ($prefix == null) {
            $projectAssetNo = (int) $pieces[2];
            $branchAssetNo = (int) $pieces[4];
            $productTypeAssetNo = (int) $pieces[6];
        } else {
            $projectAssetNo = (int) $pieces[3];
            $branchAssetNo = (int) $pieces[5];
            $productTypeAssetNo = (int) $pieces[7];
        }


        $product->projectAssetNo = $projectAssetNo;
        $product->branchAssetNo = $branchAssetNo;
        $product->productTypeAssetNo = $productTypeAssetNo;

        $product->save();



        $purchase = FamsPurchase::where('productId', $req->productId)->first();

        $purchase->supplierId = $req->editModalSupplierId;
        $purchase->productCode = $req->editModalProductCode;
        $purchase->costPrice = $req->editModalCostPrice;
        $purchase->projectId = $req->editModalProject;
        $purchase->branchId = $req->editModalBranch;
        $purchase->purchaseDate = $purchaseDate;

        $purchase->save();

        $projectSelected = $req->projectSelected;
        $projectTypeSelected = $req->projectTypeSelected;
        $branchSelected = $req->branchSelected;
        $categorySelected = $req->categorySelected;
        $productTypeSelected = $req->productTypeSelected;

        if ($req->page == null || $req->page == 0) {
            $currentPage = 1;
        } else {
            $currentPage = $req->page;
        }

        //   $logArray = array(
        //     'moduleId'  => 2,
        //     'controllerName'  => 'FamsProductController',
        //     'tableName'  => 'fams_product',
        //     'operation'  => 'update',
        //     'previousData'  => $previousdata,
        //     'primaryIds'  => [$previousdata->id]
        // );
        //   Service::createLog($logArray);


        return redirect()->route('viewFamsProduct', ['searchProject' => $projectSelected, 'searchProjectType' => $projectTypeSelected, 'searchBranch' => $branchSelected, 'searchCategory' => $categorySelected, 'searchProductType' => $productTypeSelected, 'page' => $currentPage]);

        //return redirect('viewFamsProduct')->with(['products'=>$products,'branchSelected'=>$branchSelected,'projectSelected'=>$projectSelected,'projectTypeSelected'=>$projectTypeSelected,'categorySelected'=>$categorySelected,'productTypeSelected'=>$productTypeSelected]);

    }


    //delete
    public function deleteItem(Request $req)
    {
        $previousdata = FamsProduct::find($req->productId);

        FamsProduct::find($req->productId)->delete();
        FamsPurchase::where('productId', $req->productId)->delete();
        $logArray = array(
            'moduleId'  => 2,
            'controllerName'  => 'FamsProductController',
            'tableName'  => 'fams_product',
            'operation'  => 'delete',
            'previousData'  => $previousdata,
            'primaryIds'  => [$previousdata->productId]
        );
        Service::createLog($logArray);

        return redirect('viewFamsProduct');
    }

    public function ProimageDelete(Request $req)
    {
        $image = "{$req->replacedImage1}";
        \File::delete($image);
        return response()->json($image);
    }

    public function onChangeGroup(Request $request)
    {
        if ($request->productGroupId == "") {
            $productCategoryList =  DB::table('fams_product_category')->pluck('id', 'name');
            $productSubCategoryList =  DB::table('fams_product_sub_category')->pluck('id', 'name');
        } else {

            $productCategoryList =  DB::table('fams_product_category')->where('productGroupId', (int) json_decode($request->productGroupId))->pluck('id', 'name');

            $productSubCategoryList =  DB::table('fams_product_sub_category')->where('productGroupId', (int) json_decode($request->productGroupId))->pluck('id', 'name');
        }


        $data = array(
            'productCategoryList' => $productCategoryList,
            'productSubCategoryList' => $productSubCategoryList
        );

        return response()->json($data);
    }

    public function onChangeCategory(Request $request)
    {
        if ($request->productGroupId == "" && $request->productCategoryId == "") {
            $productSubCategoryList =  DB::table('fams_product_sub_category')->pluck('id', 'name');
        } else {

            $productSubCategoryList =  DB::table('fams_product_sub_category')->where([['productCategoryId', (int) json_decode($request->productCategoryId)]])->pluck('id', 'name');
        }

        $data = array(
            'productSubCategoryList' => $productSubCategoryList
        );
        return response()->json($data);
    }

    public function onChangeSubCategory(Request $request)
    {
        $subCategoryId = (int) json_decode($request->productSubCategoryId);
        if ($request->productSubCategoryId == "") {
            $productTypeList =  DB::table('fams_product_type')->pluck('id', 'name');
        } else {
            $productTypeList =  DB::table('fams_product_type')->where('productSubCategoryId', $subCategoryId)->pluck('id', 'name');
        }

        $data = array(
            'productTypeList' => $productTypeList
        );
        return response()->json($data);
    }



    public function onChangeProductType(Request $request)
    {

        $productTypeId = array();

        if ($request->productTypeId == "") {
            $productTypeId = DB::table('fams_product_type')->pluck('id');
        } else {
            array_push($productTypeId, (int) json_decode($request->productTypeId));
        }

        $writeOffProducts = DB::table('fams_write_off')->pluck('productId')->toArray();
        $soldProducts = DB::table('fams_sale')->pluck('productId')->toArray();
        $result = array_merge($writeOffProducts, $soldProducts);
        $productList =  DB::table('fams_product')->whereNotIn('id', $result)->whereIn('productTypeId', $productTypeId)->pluck('id', 'productCode');

        $productNameList =  DB::table('fams_product_name')->select('id', 'name', 'productNameCode')->whereIn('productTypeId', $productTypeId)->get();


        $data = array(
            'productNameList' => $productNameList,
            'productList' => $productList
        );

        return response()->json($data);
    }

    public function onChangeProductName(Request $request)
    {
        $productNameId = array();

        if ($request->productNameId == "") {
            $productNameId = DB::table('fams_product_name')->pluck('id');
        } else {
            array_push($productNameId, (int) json_decode($request->productNameId));
        }


        $writeOffProducts = DB::table('fams_write_off')->pluck('productId')->toArray();
        $soldProducts = DB::table('fams_sale')->pluck('productId')->toArray();
        $result = array_merge($writeOffProducts, $soldProducts);

        $productList =  DB::table('fams_product')->whereNotIn('id', $result)->whereIn('productNameId', $productNameId)->pluck('id', 'productCode');


        $data = array(
            'productList' => $productList
        );
        return response()->json($data);
    }

    /*public function onChangeProject(Request $request){
        $projectId = (int)json_decode($request->projectId);
        if($request->projectId==""){
             $projectTypeList =  DB::table('gnr_project_type')->pluck('id','name');
             $branchList =  DB::table('gnr_branch')->pluck('id','name');
        }
        else{
            $projectTypeList =  DB::table('gnr_project_type')->where('projectId',$projectId)->pluck('id','name');
            $branchList =  DB::table('gnr_branch')->where('projectId',$projectId)->pluck('id','name');
            
        }

        $data = array(            
            'projectTypeList' => $projectTypeList,
            'branchList' => $branchList
        );
        return response()->json($data);
    }*/

    public function onChangeProject(Request $request)
    {
        $projectId = (int) json_decode($request->projectId);
        if ($request->projectId == "") {
            $projectTypeList =  DB::table('gnr_project_type')->select('id', 'name', 'projectTypeCode')->get();
            $branchList =  DB::table('gnr_branch')->orderBy('branchCode')->select('id', 'name', 'branchCode')->get();
        } else {
            $projectTypeList =  DB::table('gnr_project_type')->where('projectId', $projectId)->select('id', 'name', 'projectTypeCode')->get();
            $branchList =  DB::table('gnr_branch')->where('projectId', $projectId)->orWhere('id', 1)->orderBy('branchCode')->select('id', 'name', 'branchCode')->get();
        }

        $data = array(
            'projectTypeList' => $projectTypeList,
            'branchList' => $branchList
        );
        return response()->json($data);
    }



    public function onChangeProjectType(Request $request)
    {
        $projectId = array();
        $projectTypeId = array();

        if ($request->projectId == "") {
            $projectId = DB::table('gnr_project')->pluck('id')->toArray();
        } else {
            array_push($projectId, (int) json_decode($request->projectId));
        }

        if ($request->projectTypeId == "") {
            $projectTypeId = DB::table('gnr_project_type')->pluck('id')->toArray();
        } else {
            array_push($projectTypeId, (int) json_decode($request->projectTypeId));
        }

        $branchList = DB::table('gnr_branch')->whereIn('projectId', $projectId)->whereIn('projectTypeId', $projectTypeId)->orWhere('id', 1)->orderBy('branchCode')->select('id', 'name', 'branchCode')->get();

        $data = array(
            'branchList' => $branchList
        );
        return response()->json($data);
    }




    /* public function onChangeSubCategory(Request $request){


        if ($request->productGroupId=="" && $request->productCategoryId == "" && $request->productSubproductCategoryId == "") {
           
            $productModelList =  DB::table('fams_product_model')->pluck('id','name');
        }


        elseif ($request->productGroupId=="" && $request->productCategoryId == "" && $request->productSubCategoryId != "") {

            

            $productModelList =  DB::table('fams_product_model')
                ->where('productSubCategoryId',(int)json_decode($request->productSubCategoryId))
                ->pluck('id','name');
        }

        elseif ($request->productGroupId=="" && $request->productCategoryId != "" && $request->productSubCategoryId == "") {

           

            $productModelList =  DB::table('fams_product_model')
                ->where('productCategoryId',(int)json_decode($request->productCategoryId))
                ->pluck('id','name');
        }

        elseif ($request->productGroupId=="" && $request->productCategoryId != "" && $request->productSubCategoryId != "") {

            

            $productModelList =  DB::table('fams_product_model')
                ->where([['productCategoryId',(int)json_decode($request->productCategoryId)],['productSubCategoryId',(int)json_decode($request->productSubCategoryId)]])
                ->pluck('id','name');
        }

        elseif ($request->productGroupId!="" && $request->productCategoryId == "" && $request->productSubCategoryId == "") {

           

            $productModelList =  DB::table('fams_product_model')
                ->where('productGroupId',(int)json_decode($request->productGroupId))
                ->pluck('id','name');
        }

        elseif ($request->productGroupId!="" && $request->productCategoryId == "" && $request->productSubCategoryId != "") {

           

            $productModelList =  DB::table('fams_product_model')
                ->where([['productGroupId',(int)json_decode($request->productGroupId)],['productSubCategoryId',(int)json_decode($request->productSubCategoryId)]])
                ->pluck('id','name');
        }

        elseif ($request->productGroupId!="" && $request->productCategoryId != "" && $request->productSubCategoryId == "") {

            

            $productModelList =  DB::table('fams_product_model')
                ->where([['productGroupId',(int)json_decode($request->productGroupId)],['productCategoryId',(int)json_decode($request->productCategoryId)]])
                ->pluck('id','name');
        }

        elseif ($request->productGroupId!="" && $request->productCategoryId != "" && $request->productSubCategoryId != "") {

            

            $productModelList =  DB::table('fams_product_model')
                ->where([['productGroupId',(int)json_decode($request->productGroupId)],['productCategoryId',(int)json_decode($request->productCategoryId)],['productSubCategoryId',(int)json_decode($request->productSubCategoryId)]])
                ->pluck('id','name');
        }


        $data = array(
            
            'productModelList'         => $productModelList
        );
        return response()->json($data);
    }*/



    function onChangeBrand(Request $request)
    {

        if ($request->productGroupId == "" && $request->productCategoryId == "" && $request->productSubCategoryId == "" && $request->productBrandId == "") {

            $productModelList =  DB::table('fams_product_model')->pluck('id', 'name');
        } elseif ($request->productGroupId == "" && $request->productCategoryId == "" && $request->productSubCategoryId == "" && $request->productBrandId != "") {

            $productModelList =  DB::table('fams_product_model')
                ->where('productBrandId', (int) json_decode($request->productBrandId))
                ->pluck('id', 'name');
        } elseif ($request->productGroupId == "" && $request->productCategoryId == "" && $request->productSubCategoryId != "" && $request->productBrandId == "") {

            $productModelList =  DB::table('fams_product_model')
                ->where('productSubCategoryId', (int) json_decode($request->productSubCategoryId))
                ->pluck('id', 'name');
        } elseif ($request->productGroupId == "" && $request->productCategoryId == "" && $request->productSubCategoryId != "" && $request->productBrandId != "") {

            $productModelList =  DB::table('fams_product_model')
                ->where([['productSubCategoryId', (int) json_decode($request->productSubCategoryId)], ['productBrandId', (int) json_decode($request->productBrandId)]])
                ->pluck('id', 'name');
        } elseif ($request->productGroupId == "" && $request->productCategoryId != "" && $request->productSubCategoryId == "" && $request->productBrandId == "") {

            $productModelList =  DB::table('fams_product_model')
                ->where('productCategoryId', (int) json_decode($request->productCategoryId))
                ->pluck('id', 'name');
        } elseif ($request->productGroupId == "" && $request->productCategoryId != "" && $request->productSubCategoryId == "" && $request->productBrandId != "") {

            $productModelList =  DB::table('fams_product_model')
                ->where([['productCategoryId', (int) json_decode($request->productCategoryId)], ['productBrandId', (int) json_decode($request->productBrandId)]])
                ->pluck('id', 'name');
        } elseif ($request->productGroupId == "" && $request->productCategoryId != "" && $request->productSubCategoryId != "" && $request->productBrandId == "") {

            $productModelList =  DB::table('fams_product_model')
                ->where([['productCategoryId', (int) json_decode($request->productCategoryId)], ['productSubCategoryId', (int) json_decode($request->productSubCategoryId)]])
                ->pluck('id', 'name');
        } elseif ($request->productGroupId == "" && $request->productCategoryId != "" && $request->productSubCategoryId != "" && $request->productBrandId != "") {

            $productModelList =  DB::table('fams_product_model')
                ->where([['productCategoryId', (int) json_decode($request->productCategoryId)], ['productSubCategoryId', (int) json_decode($request->productSubCategoryId)], ['productBrandId', (int) json_decode($request->productBrandId)]])
                ->pluck('id', 'name');
        } elseif ($request->productGroupId != "" && $request->productCategoryId == "" && $request->productSubCategoryId == "" && $request->productBrandId == "") {

            $productModelList =  DB::table('fams_product_model')
                ->where('productGroupId', (int) json_decode($request->productGroupId))
                ->pluck('id', 'name');
        } elseif ($request->productGroupId != "" && $request->productCategoryId == "" && $request->productSubCategoryId == "" && $request->productBrandId != "") {

            $productModelList =  DB::table('fams_product_model')
                ->where([['productGroupId', (int) json_decode($request->productGroupId)], ['productBrandId', (int) json_decode($request->productBrandId)]])
                ->pluck('id', 'name');
        } elseif ($request->productGroupId != "" && $request->productCategoryId == "" && $request->productSubCategoryId != "" && $request->productBrandId == "") {

            $productModelList =  DB::table('fams_product_model')
                ->where([['productGroupId', (int) json_decode($request->productGroupId)], ['productSubCategoryId', (int) json_decode($request->productSubCategoryId)]])
                ->pluck('id', 'name');
        } elseif ($request->productGroupId != "" && $request->productCategoryId == "" && $request->productSubCategoryId != "" && $request->productBrandId != "") {

            $productModelList =  DB::table('fams_product_model')
                ->where([['productGroupId', (int) json_decode($request->productGroupId)], ['productSubCategoryId', (int) json_decode($request->productSubCategoryId)], ['productBrandId', (int) json_decode($request->productBrandId)]])
                ->pluck('id', 'name');
        } elseif ($request->productGroupId != "" && $request->productCategoryId != "" && $request->productSubCategoryId == "" && $request->productBrandId == "") {

            $productModelList =  DB::table('fams_product_model')
                ->where([['productGroupId', (int) json_decode($request->productGroupId)], ['productCategoryId', (int) json_decode($request->productCategoryId)]])
                ->pluck('id', 'name');
        } elseif ($request->productGroupId != "" && $request->productCategoryId != "" && $request->productSubCategoryId == "" && $request->productBrandId != "") {

            $productModelList =  DB::table('fams_product_model')
                ->where([['productGroupId', (int) json_decode($request->productGroupId)], ['productCategoryId', (int) json_decode($request->productCategoryId)], ['productBrandId', (int) json_decode($request->productBrandId)]])
                ->pluck('id', 'name');
        } elseif ($request->productGroupId != "" && $request->productCategoryId != "" && $request->productSubCategoryId != "" && $request->productBrandId == "") {

            $productModelList =  DB::table('fams_product_model')
                ->where([['productGroupId', (int) json_decode($request->productGroupId)], ['productCategoryId', (int) json_decode($request->productCategoryId)], ['productSubCategoryId', (int) json_decode($request->productSubCategoryId)]])
                ->pluck('id', 'name');
        } elseif ($request->productGroupId != "" && $request->productCategoryId != "" && $request->productSubCategoryId != "" && $request->productBrandId != "") {

            $productModelList =  DB::table('fams_product_model')
                ->where([['productGroupId', (int) json_decode($request->productGroupId)], ['productCategoryId', (int) json_decode($request->productCategoryId)], ['productSubCategoryId', (int) json_decode($request->productSubCategoryId)], ['productBrandId', (int) json_decode($request->productBrandId)]])
                ->pluck('id', 'name');
        }

        $data = array(
            'productModelList'         => $productModelList
        );
        return response()->json($data);
    }


    public function validateStep1(Request $request)
    {
        $rules = array(
            'supplierId' => 'required',
            'productGroupId' => 'required',
            'categoryId' => 'required',
            'subCategoryId' => 'required',
            'brandId' => 'required',
            'modelId' => 'required',
            'sizeId' => 'required',
            'colorId' => 'required',
            'uomId' => 'required',
            'projectId' => 'required',
            'branchId' => 'required',
            'item' => 'required',
            'purchaseDate' => 'required',
            'warranty' => 'required',
            'serviceWarranty' => 'required'
        );
        $attributeNames = array(
            'supplierId' => 'Supplier Name',
            'productGroupId' => 'Group Name',
            'categoryId' => 'Category Name',
            'subCategoryId' => 'Subcategory Name',
            'brandId' => 'Brand Name',
            'modelId' => 'Model Name',
            'sizeId' => 'Product Size',
            'colorId' => 'Product Color',
            'uomId' => 'Uom Name',
            'costPrice' => 'Product Cost Price',
            'projectId' => 'Product Sales Price',
            'item' => 'Product Sales Price',
            'warranty' => 'Product Sales Price',
            'serviceWarranty' => 'Product Sales Price',
        );

        $validator = Validator::make(Input::all(), $rules);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails())
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        else {
            return response::json("Success");
        }
    }

    /*public function validateStep3(Request $request){
        $rules = array(
            'productImage' => 'max:512',
            'warrantyImage' => 'max:512',
            'billImage' => 'max:512',
            'additionalVoucherImage' => 'max:512'
        );
        $attributeNames = array(
            'productImage' => 'Product Image',
            'warrantyImage' => 'Warranty Image',
            'billImage' => 'Bill Image',
            'additionalVoucherImage' => 'Additional Voucher Image'
        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails())
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        else{
            return response::json("Success");
        }

    }*/

    public function famsGetInfo(Request $request)
    {
        if ($request->key == "AmbalaAsset") {
            $productAssetNo = DB::table('fams_product')->max('assetNo') + 1;
            $formatedData = str_pad($productAssetNo, 5, "0", STR_PAD_LEFT);
            return response()->json($formatedData);
        }

        if ($request->key == "project") {
            $projectId = $request->projectId;
            $projectCode = DB::table('gnr_project')->where('id', $projectId)->value('projectCode');
            $formatedProjectCode = str_pad($projectCode, 2, "0", STR_PAD_LEFT);


            $trasferProductProjectAssetNo = (int) DB::table('fams_tra_transfer')->where('projectIdFrom', $projectId)->max('oldProjectAssetNo');
            $productTableProjectAssetNo = (int) DB::table('fams_product')->where('projectId', $projectId)->max('projectAssetNo');
            if ($trasferProductProjectAssetNo > $productTableProjectAssetNo) {
                $projectAssetNo = $trasferProductProjectAssetNo + 1;
            } else {
                $projectAssetNo = $productTableProjectAssetNo + 1;
            }

            $productProjectAssetNo = $projectAssetNo; //DB::table('fams_product')->where('projectId',$projectId)->max('projectAssetNo')+1;
            $formatedProjectAssetNo = str_pad($productProjectAssetNo, 4, "0", STR_PAD_LEFT);

            $data = array(
                'project' => $formatedProjectCode,
                'assetNo' => $formatedProjectAssetNo
            );

            return response()->json($data);
        }

        if ($request->key == "projectType") {
            $projectType = $request->projectType;
            $projectTypeCode = DB::table('gnr_project_type')->where('id', $projectType)->value('projectTypeCode');
            $formatedData = str_pad($projectTypeCode, 2, "0", STR_PAD_LEFT);
            return response()->json($formatedData);
        }

        if ($request->key == "projectAsset") {
            $projectId = $request->projectId;
            $projectType = $request->projectType;
            $productProjectAssetNo = DB::table('fams_product')->where([['projectId', $projectId], ['projectTypeId', $projectType]])->max('projectAssetNo') + 1;
            $formatedData = str_pad($productProjectAssetNo, 4, "0", STR_PAD_LEFT);
            return response()->json($formatedData);
        }

        if ($request->key == "branch") {
            $branchId = $request->branchId;
            $branchCode = DB::table('gnr_branch')->where('id', $branchId)->value('branchCode');
            $formatedBranchData = str_pad($branchCode, 3, "0", STR_PAD_LEFT);



            $trasferProductBranchAssetNo = (int) DB::table('fams_tra_transfer')->where('branchIdFrom', $branchId)->max('oldBranchAssetNo');
            $productTableBranchAssetNo = (int) DB::table('fams_product')->where('branchId', $branchId)->max('branchAssetNo');
            if ($trasferProductBranchAssetNo > $productTableBranchAssetNo) {
                $branchAssetNo = $trasferProductBranchAssetNo + 1;
            } else {
                $branchAssetNo = $productTableBranchAssetNo + 1;
            }



            $productBranchAssetNo = $branchAssetNo; //DB::table('fams_product')->where([['branchId',$branchId]])->max('branchAssetNo')+1;
            $formatedBranchAssetData = str_pad($productBranchAssetNo, 4, "0", STR_PAD_LEFT);
            $data = array(
                'branch' => $formatedBranchData,
                'assetNo' => $formatedBranchAssetData
            );

            return response()->json($data);
        }

        if ($request->key == "group") {
            $productGroupId = $request->productGroupId;
            $productGroupCode = DB::table('fams_product_group')->where('id', $productGroupId)->value('groupCode');
            $formatedGroupCodeData = str_pad($productGroupCode, 2, "0", STR_PAD_LEFT);

            /*$productItemAssetNo = DB::table('fams_product')->where([['itemTypeId',$itemId]])->max('itemTypeAssetNo')+1;
            $formatedproductItemAssetData = str_pad( $productItemAssetNo, 4, "0", STR_PAD_LEFT );*/
            $data = array(
                'groupCode' => $formatedGroupCodeData
            );
            return response()->json($data);
        }

        if ($request->key == "subCategory") {
            $productSubCategoryId = $request->productSubCategoryId;
            $productSubCategoryCode = DB::table('fams_product_sub_category')->where('id', $productSubCategoryId)->value('subCategoryCode');
            $formatedProductSubCategoryCode = str_pad($productSubCategoryCode, 2, "0", STR_PAD_LEFT);

            $subCategoryAssetNo = DB::table('fams_product')->where([['subCategoryId', $productSubCategoryId]])->max('subCategoryAssetNo') + 1;
            $formatedSubCategoryAssetNo = str_pad($subCategoryAssetNo, 4, "0", STR_PAD_LEFT);
            $data = array(
                'subCategoryCode' => $formatedProductSubCategoryCode,
                'assetNo' => $formatedSubCategoryAssetNo
            );
            return response()->json($data);
        }

        if ($request->key == "productType") {
            $productTypeId = $request->productTypeId;
            $productTypeCode = DB::table('fams_product_type')->where('id', $productTypeId)->value('productTypeCode');
            $formatedproductTypeCode = str_pad($productTypeCode, 3, "0", STR_PAD_LEFT);

            $productTypeAssetNo = DB::table('fams_product')->where([['productTypeId', $productTypeId]])->max('productTypeAssetNo') + 1;
            $formatedproductTypeAssetNo = str_pad($productTypeAssetNo, 4, "0", STR_PAD_LEFT);
            $data = array(
                'productTypeCode' => $formatedproductTypeCode,
                'assetNo' => $formatedproductTypeAssetNo
            );
            return response()->json($data);
        }

        /*$productCategoryId = $request->productCategoryId;
        $productBrandId = $request->productBrandId;
        $projectId = $request->projectId;
        $projectType = $request->projectType;
        $branchId = $request->branchId;

        $branchCode = DB::table('gnr_branch')->where('id',$branchId)->value('branchCode');
        $projectCode = DB::table('gnr_project')->where('id',$projectId)->value('projectCode');
        $projectTypeCode = DB::table('gnr_project_type')->where('id',$projectType)->value('projectTypeCode');
        $categoryCode = DB::table('fams_product_category')->where('id',$productCategoryId)->value('categoryCode');

        $productAssetNo = DB::table('fams_product')->max('id')+1;
        $productProjectAssetNo = DB::table('fams_product')->where([['projectId',$projectId],['projectTypeId',$projectType]])->max('id')+1;
        $productBranchAssetNo = DB::table('fams_product')->where([['branchId',$branchId]])->max('id')+1;
        $productCategoryAssetNo = DB::table('fams_product')->where([['categoryId',$productCategoryId]])->max('id')+1;

        $data = array(
            'productAssetNo' => $productAssetNo,
            'projectCode' => $projectCode,
            'projectTypeCode' => $projectTypeCode,
            'productProjectAssetNo' => $productProjectAssetNo,
            'branchCode' => $branchCode,
            'productBranchAssetNo' => $productBranchAssetNo,
            'categoryCode' => $categoryCode,
            'productCategoryAssetNo' => $productCategoryAssetNo
        );

        return response()->json($data);*/
    }

    public function validateStep2(Request $request)
    {

        $rules = array(
            'costPrice' => 'required',
            'usefulLifeYear' => 'required_without_all:usefulLifeMonth',
            'usefulLifeMonth' => 'required_without_all:usefulLifeYear',
            'productQuantity' => 'required',
            'resellValue' => 'required'
        );
        /* $attributeNames = array(
             'costPrice' => 'Cost Price',
             'usefulLife' => 'Useful Life'
         );*/

        $validator = Validator::make(Input::all(), $rules);
        //$validator->setAttributeNames($attributeNames);
        if ($validator->fails())
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        //return response::json("Required");
        else {
            return response::json("Success");
        }
    }


    public function getProductInfo(Request $request)
    {
        $productId = (int) json_decode($request->productId);
        $product = FamsProduct::find($productId);


        $branchName = DB::table('gnr_branch')->where('id', $product->branchId)->value('name');
        $projectName = DB::table('gnr_project')->where('id', $product->projectId)->value('name');
        $projectTypeName = DB::table('gnr_project_type')->where('id', $product->projectTypeId)->value('name');
        $purchaseDate = date('d-m-Y', strtotime($product->purchaseDate));

        $warranty = $product->warrantyYear . " Years " . $product->warrantyMonth . " Months";
        $warrantyExpireDate = date('d-m-Y', strtotime($product->warrantyExpireDate));
        $serviceWarranty = $product->serviceWarrantyYear . " Years " . $product->serviceWarrantyMonth . " Months";
        $serviceWarrantyExpireDate = date('d-m-Y', strtotime($product->serviceWarrantyExpireDate));
        $usefulLife = $product->usefulLifeYear . " Years " . $product->usefulLifeMonth . " Months";

        $additionalCharge = (float) DB::table('fams_additional_charge')->where('productId', $product->id)->sum('amount');
        $grandTotalCost = (float) $product->totalCost + $additionalCharge;


        $supplier = DB::table('gnr_supplier')->where('id', $product->supplierId)->value('supplierCompanyName');
        $group = DB::table('fams_product_group')->where('id', $product->groupId)->value('name');
        $category = DB::table('fams_product_category')->where('id', $product->categoryId)->value('name');
        $subCategory = DB::table('fams_product_sub_category')->where('id', $product->subCategoryId)->value('name');
        $productType = DB::table('fams_product_type')->where('id', $product->productTypeId)->value('name');
        $brand = DB::table('fams_product_brand')->where('id', $product->brandId)->value('name');
        $model = DB::table('fams_product_model')->where('id', $product->modelId)->value('name');
        $size = DB::table('fams_product_size')->where('id', $product->sizeId)->value('name');
        $color = DB::table('fams_product_color')->where('id', $product->colorId)->value('name');
        $uom = DB::table('fams_product_uom')->where('id', $product->uomId)->value('name');

        $data = array(
            'name' => $product->name,
            'productCode' => $product->productCode,
            'branchName' => $branchName,
            'projectName' => $projectName,
            'projectTypeName' => $projectTypeName,
            'purchaseDate' => $purchaseDate,
            'warranty' => $warranty,
            'warrantyExpireDate' => $warrantyExpireDate,
            'serviceWarranty' => $serviceWarranty,
            'serviceWarrantyExpireDate' => $serviceWarrantyExpireDate,
            'usefulLife' => $usefulLife,
            'description' => $product->description,

            'costPrice' => $product->costPrice,
            'additionalCost' => $product->additionalCost1,
            'vatTax' => $product->additionalCost2,
            'addCharge' => $product->additionalCost2,
            'grandTotalCost' => $grandTotalCost,

            'resaleValue' => $product->resellValue,
            'depOpeningBalance' => $product->depreciationOpeningBalance,
            'depPerYear' => $product->depreciationAmountPerYear,
            'depPercentage' => $product->depreciationPercentage,

            'productImage' => $product->productImage,
            'warrantyCardImage' => $product->warrantyCardImage,
            'billImage' => $product->billImage,
            'additionalVoucherImage' => $product->additionalVoucherImage,


            'supplier' => $supplier,
            'group' => $group,
            'category' => $category,
            'subCategory' => $subCategory,
            'productType' => $productType,
            'brand' => $brand,
            'model' => $model,
            'size' => $size,
            'color' => $color,
            'uom' => $uom
        );

        return response::json($data);
    }

    public function getProductInfoForEditModal(Request $request)
    {
        $productId = (int) json_decode($request->productId);
        $product = FamsProduct::find($productId);

        $purchaseDate = date('d-m-Y', strtotime($product->purchaseDate));

        $warranty = $product->warrantyYear . " Years " . $product->warrantyMonth . " Months";
        $warrantyExpireDate = date('d-m-Y', strtotime($product->warrantyExpireDate));
        $serviceWarranty = $product->serviceWarrantyYear . " Years " . $product->serviceWarrantyMonth . " Months";
        $serviceWarrantyExpireDate = date('d-m-Y', strtotime($product->serviceWarrantyExpireDate));
        $usefulLife = $product->usefulLifeYear . " Years " . $product->usefulLifeMonth . " Months";

        $additionalCharge = DB::table('fams_additional_charge')->where('productId', $product->id)->sum('amount');
        $grandTotalCost = $product->totalCost + $additionalCharge;


        $data = array(
            'productCode' => $product->productCode,
            'productNameId' => $product->productNameId,
            'supplierId' => $product->supplierId,
            'groupId' => $product->groupId,
            'categoryId' => $product->categoryId,
            'subCategoryId' => $product->subCategoryId,
            'productTypeId' => $product->productTypeId,
            'brandId' => $product->brandId,
            'modelId' => $product->modelId,
            'sizeId' => $product->sizeId,
            'colorId' => $product->colorId,
            'uomId' => $product->uomId,

            'branchId' => $product->branchId,
            'projectId' => $product->projectId,
            'projectTypeId' => $product->projectTypeId,
            'purchaseDate' => $purchaseDate,
            'warrantyYear' => $product->warrantyYear,
            'warrantyMonth' => $product->warrantyMonth,
            'warrantyExpireDate' => $warrantyExpireDate,
            'serviceWarrantyYear' => $product->serviceWarrantyYear,
            'serviceWarrantyMonth' => $product->serviceWarrantyMonth,
            'serviceWarrantyExpireDate' => $serviceWarrantyExpireDate,

            'description' => $product->description,
            'usefulLifeYear' => $product->usefulLifeYear,
            'usefulLifeMonth' => $product->usefulLifeMonth,
            'costPrice' => $product->costPrice,
            'additionalCost' => $product->additionalCost1,
            'vatTax' => $product->additionalCost2,
            'addCharge' => $additionalCharge,
            'grandTotalCost' => $grandTotalCost,
            'resaleValue' => $product->resellValue,
            'depOpeningBalance' => $product->depreciationOpeningBalance,
            'depPerYear' => $product->depreciationAmountPerYear,
            'depPerMonth' => $product->depreciationAmountPerMonth,
            'depPerDay' => $product->depreciationAmountPerDay,
            'depPercentage' => $product->depreciationPercentage,

            'productImage' => $product->productImage,
            'warrantyCardImage' => $product->warrantyCardImage,
            'billImage' => $product->billImage,
            'additionalVoucherImage' => $product->additionalVoucherImage

        );

        return response::json($data);
    }



    public function getFilteredProducts(Request $request)
    {
        $projectId = array();
        $branchId = array();
        if ($request->filterProjectId == "") {
            $projectId = DB::table('gnr_project')->pluck('id');
        } else {
            $tempProjectId = (int) json_decode($request->filterProjectId);
            array_push($projectId, $tempProjectId);
        }

        if ($request->filterbranchId == "") {
            $branchId = DB::table('gnr_branch')->pluck('id');
        } else {
            $tempBranchId = (int) json_decode($request->filterbranchId);
            array_push($branchId, $tempBranchId);
        }

        //echo var_dump($branchId);

        $products = DB::table('fams_product')->whereIn('projectId', $projectId)->whereIn('branchId', $branchId)->orderBy('id', 'desc')->paginate(20);

        $groups = DB::table('fams_product_group')->get();
        $suppliers = DB::table('gnr_supplier')->get();
        $categories = DB::table('fams_product_category')->get();
        $subCategories = DB::table('fams_product_sub_category')->get();
        $productTypes = DB::table('fams_product_type')->get();
        $branches = DB::table('gnr_branch')->get();
        $brands = DB::table('fams_product_brand')->get();
        $models = DB::table('fams_product_model')->get();
        $depDetails = FamsDepDetails::all();

        $projectSelected = $request->projectSelected;
        $projectTypeSelected = $request->projectTypeSelected;
        $branchSelected = $request->branchSelected;
        $categorySelected = $request->categorySelected;
        $productTypeSelected = $request->productTypeSelected;

        $prefix = DB::table('fams_product_prefix')->where('status', 1)->value('name');

        return view('fams/product/productSetting/product/viewFamsProduct', ['products' => $products, 'groups' => $groups, 'categories' => $categories, 'subCategories' => $subCategories, 'branches' => $branches, 'brands' => $brands, 'models' => $models, 'depDetails' => $depDetails, 'suppliers' => $suppliers, 'productTypes' => $productTypes, 'projectSelected' => $projectSelected, 'branchSelected' => $branchSelected, 'categorySelected' => $categorySelected, 'productTypeSelected' => $productTypeSelected, 'prefix' => $prefix]);
    }

    public function updateProductDep()
    {
        $products = DB::table('fams_product')->get();
        $index = 0;
        foreach ($products as $product) {


            ///////////////////////
            $purchaseDate = Carbon::parse($product->purchaseDate);
            $useFulLifeEndDate = $purchaseDate->copy()->addYears($product->usefulLifeYear);
            $useFulLifeEndDate->addMonthsNoOverflow($product->usefulLifeMonth);

            $numberOfDays = $purchaseDate->diffInDays($useFulLifeEndDate);



            $totalCost = (float) json_decode($product->totalCost);
            $years = (float) $product->usefulLifeYear;
            $months = (float) $product->usefulLifeMonth + (12 * $years);
            $days = $numberOfDays; //($years*365) + ((float) $req->usefulLifeMonth * 30);

            if ($years == 0) {
                $depPerYear = 0;
            } else {
                $depPerYear = $totalCost / $years;
            }

            if ($months == 0) {
                $depPerMonth = 0;
            } else {
                $depPerMonth = $totalCost / $months;
            }

            if ($days == 0) {
                $depPerDay = 0;
            } else {
                $depPerDay = $totalCost / $days;
            }

            //////////////////////

            $costPrice = (float) $product->totalCost;
            $usefulLife = (int) $product->usefulLifeYear;
            if ($usefulLife > 0) {
                $depPerYear = $costPrice / $usefulLife;
                $depPerMonth = $costPrice / ($usefulLife * 12);
                $depPerDay = $costPrice / ($usefulLife * 365);

                $newProduct = FamsProduct::find($product->id);

                $newProduct->depreciationAmountPerYear =  $depPerYear;
                $newProduct->depreciationAmountPerMonth =  $depPerMonth;
                $newProduct->depreciationAmountPerDay =  $depPerDay;

                $newProduct->save();
                $index++;
            }
        }

        echo $index;
    }

    public function correctProjectTypeAssetNo()
    {
        $products = FamsProduct::orderBy('id', 'asc')->get();

        foreach ($products as $key => $product) {
            $product->projectTypeAssetNo = (int) DB::table('fams_product')->where('projectId', $product->projectId)->where('projectTypeId', $product->projectTypeId)->max('projectTypeAssetNo') + 1;
            $product->save();
        }
        echo "Updated";
    }

    public function correctSubCategoryAssetNo()
    {
        $products = FamsProduct::orderBy('id', 'asc')->get();

        foreach ($products as $key => $product) {
            $product->subCategoryAssetNo = (int) DB::table('fams_product')->where('subCategoryId', $product->subCategoryId)->max('subCategoryAssetNo') + 1;
            $product->save();
        }
        echo "Updated";
    }
}
