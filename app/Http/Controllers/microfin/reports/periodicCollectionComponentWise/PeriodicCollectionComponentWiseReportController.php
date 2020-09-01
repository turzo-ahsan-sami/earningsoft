<?php

namespace App\Http\Controllers\microfin\reports\periodicCollectionComponentWise;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use App\Traits\GetSoftwareDate;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PeriodicCollectionComponentWiseReportController extends Controller {
    use GetSoftwareDate;

    public function index (Request $req) {

        // GET ALL BRANCH INFORMATION
        $branchInfo = DB::table('gnr_branch')->get();

        // GET ALL FUNDING ORGANIZATION
        $searchFundingOrgs = DB::table('mfn_funding_organization')->select('id','name')->get();

        // GET PRODUCT CATEGORY
        $searchProductCtg = DB::table('mfn_loans_product_category')->get();
        

        return view('microfin.reports.periodicCollectionComponentWiseReport.periodicCollectionComponentWiseReport', 
            compact(
                'branchInfo',
                'searchFundingOrgs',
                'searchProductCtg'
            )
        );
    }

    public function getProduct(Request $req) {
        
        if ($req->id == 'All') {
            $productInfos = DB::table('mfn_loans_product')
                ->get();
        }
        else {
            $productInfos = DB::table('mfn_loans_product')
                ->where('productCategoryId', $req->id)
                ->get();
        }

        return response()->json($productInfos);
    }

    public function getProductCtg (Request $req) {
        if ($req->id == 'All') {
            $productCtgInfos = DB::table('mfn_loans_product_category')
                ->get();
        }
        else {
            $productInfos = DB::table('mfn_loans_product')
                ->where('fundingOrganizationId', $req->id)
                ->groupBy('productCategoryId')
                ->pluck('productCategoryId')
                ->toArray();

            $productCtgInfos = DB::table('mfn_loans_product_category')
                ->whereIn('id', $productInfos)
                ->get();
        }

        return response()->json($productCtgInfos);
    }

    public function getTable (Request $req) {
        // DECLARED VARIABLE
        $searchProCategory = $req->searchProductCtg;
        $searchProduct     = $req->searchProduct;
        $searchLoanProduct = $req->searchLoanProduct;
        $startDate         = date_format(date_create($req->startDate), "Y-m-d");
        $endDate           = date_format(date_create($req->endDate), "Y-m-d");

        // DECLARED ARRAY
        $savingsProducts    = array();
        $fieldOfficersIds   = array();
        $fieldOfficersInfos = array();
        $rowByFieldOfficer  = array();

        // GET ALL SAVINGS PRODUCTS
        $savingsProducts = DB::table('mfn_saving_product')->get();

        // GET ALL FIELD OFFICERS INFORMATION (BRANCH WISE)
        $branchId = $req->searchBranch;
        $fieldOfficersIds = DB::table('mfn_samity')
            // ->where([['branchId', $req->searchBranch], ['closingDate', '>=', ]])
            ->where(function ($query) use ($endDate, $branchId) {
                $query->where([['branchId', $branchId], ['closingDate', '>=', $endDate]])
                ->orWhere([['branchId', $branchId], ['closingDate', '=', '0000-00-00']]);
            })
            ->groupBy('fieldOfficerId')
            ->pluck('fieldOfficerId')
            ->toArray();

        $fieldOfficersInfos = DB::table('hr_emp_general_info')
            ->whereIn('id', $fieldOfficersIds)
            ->select('id','emp_id', 'emp_name_english')
            ->get();

        // IF SEARCH OPTION IS SELECTED AS 'Loan Product'
        if ($searchLoanProduct == 'LoanProduct') {
            // IF CATEGORY AND PRODUCT SELECTED AS 'ALL'
            if ($req->searchProductCtg == 'All' and $req->searchProduct == 'All') {
                //  START...
                // TO GET THE ACTUAL ROWSPAN OF THE FIELD OFFICER
                foreach ($fieldOfficersIds as $key => $ids) {
                    $checkShamity[$ids] = DB::table('mfn_samity')
                        ->where([['fieldOfficerId', $ids], ['branchId', $branchId]])
                        ->pluck('id')
                        ->toArray();
                }

                foreach ($checkShamity as $key1 => $check) {
                    foreach ($check as $key2 => $value) {
                        $checkProduct[$key1][$value] = DB::table('mfn_member_information')
                            ->where([['samityId', $value], ['softDel', '=', '0']])
                            //->whereDate([['samityId', $value], ['softDel', '=', '0']])
                            ->groupBy('primaryProductId')
                            ->pluck('primaryProductId')
                            ->toArray();
                    }
                }

                $count = 0;

                foreach ($checkProduct as $key1 => $checkProductValue) {
                    $count = 0;
                    foreach ($checkProductValue as $key2 => $checkValue) {
                        $count = $count + sizeof($checkValue);
                    }
                    $rowByFieldOfficer[$key1] = $count;
                }
                // END...
            }
            // IF CATEGORY NOT SELECTED AS 'ALL' BUT PRODUCT SELECTED AS 'ALL'
            elseif ($req->searchProductCtg != 'All' and $req->searchProduct == 'All') {
                //  START...
                // TO GET THE ACTUAL ROWSPAN OF THE FIELD OFFICER
                foreach ($fieldOfficersIds as $key => $ids) {
                    $checkShamity[$ids] = DB::table('mfn_samity')
                        ->where([['fieldOfficerId', $ids], ['branchId', $branchId]])
                        ->pluck('id')
                        ->toArray();

                }

                foreach ($checkShamity as $key1 => $check) {
                    foreach ($check as $key2 => $value) {
                        $checkProduct[$key1][$value] = DB::table('mfn_member_information')
                            ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
                            ->where([['mfn_member_information.samityId', $value], 
                                    ['mfn_member_information.softDel', '=', '0'], ['mfn_loans_product.productCategoryId', $searchProCategory]])
                            ->groupBy('mfn_member_information.primaryProductId')
                            ->pluck('mfn_member_information.primaryProductId')
                            ->toArray();
                    }
                }

                $count = 0;

                foreach ($checkProduct as $key1 => $checkProductValue) {
                    $count = 0;
                    foreach ($checkProductValue as $key2 => $checkValue) {
                        $count = $count + sizeof($checkValue);
                    }
                    $rowByFieldOfficer[$key1] = $count;
                }
                // END...
            }
            // IF CATEGORY SELECTED AS 'ALL' BUT PRODUCT NOT SELECTED AS 'ALL'
            elseif ($req->searchProductCtg == 'All' and $req->searchProduct != 'All') {
                //  START...
                // TO GET THE ACTUAL ROWSPAN OF THE FIELD OFFICER
                foreach ($fieldOfficersIds as $key => $ids) {
                    $checkShamity[$ids] = DB::table('mfn_samity')
                        ->where([['fieldOfficerId', $ids], ['branchId', $branchId]])
                        ->pluck('id')
                        ->toArray();

                }

                foreach ($checkShamity as $key1 => $check) {
                    foreach ($check as $key2 => $value) {
                        $checkProduct[$key1][$value] = DB::table('mfn_member_information')
                            ->where([['samityId', $value], ['softDel', '=', '0'], ['primaryProductId', $searchProduct]])
                            ->groupBy('primaryProductId')
                            ->pluck('primaryProductId')
                            ->toArray();
                    }
                }

                $count = 0;

                foreach ($checkProduct as $key1 => $checkProductValue) {
                    $count = 0;
                    foreach ($checkProductValue as $key2 => $checkValue) {
                        $count = $count + sizeof($checkValue);
                    }
                    $rowByFieldOfficer[$key1] = $count;
                }
                // END...
            }
            // IF CATEGORY AND PRODUCT BOTH NOT SELECTED AS 'ALL'
            elseif ($req->searchProductCtg != 'All' and $req->searchProduct != 'All') {
                //  START...
                // TO GET THE ACTUAL ROWSPAN OF THE FIELD OFFICER
                foreach ($fieldOfficersIds as $key => $ids) {
                    $checkShamity[$ids] = DB::table('mfn_samity')
                        ->where([['fieldOfficerId', $ids], ['branchId', $branchId]])
                        ->pluck('id')
                        ->toArray();

                }

                foreach ($checkShamity as $key1 => $check) {
                    foreach ($check as $key2 => $value) {
                        $checkProduct[$key1][$value] = DB::table('mfn_member_information')
                            ->where([['samityId', $value],  ['softDel', '=', '0'], ['primaryProductId', $searchProduct]])
                            ->groupBy('primaryProductId')
                            ->pluck('primaryProductId')
                            ->toArray();
                    }
                }

                $count = 0;

                foreach ($checkProduct as $key1 => $checkProductValue) {
                    $count = 0;
                    foreach ($checkProductValue as $key2 => $checkValue) {
                        $count = $count + sizeof($checkValue);
                    }
                    $rowByFieldOfficer[$key1] = $count;
                }
                // END...
            }
        }
        // IF SEARCH OPTION IS SELECTED AS 'Loan Product Category'
        else {
            // IF CATEGORY AND PRODUCT SELECTED AS 'ALL'
            if ($req->searchProductCtg == 'All' and $req->searchProduct == 'All') {
                // START...
                // TO GET THE ACTUAL ROWSPAN OF THE FIELD OFFICER
                foreach ($fieldOfficersIds as $key => $ids) {
                    $checkShamity[$ids] = DB::table('mfn_samity')
                        ->where([['fieldOfficerId', $ids], ['branchId', $branchId]])
                        ->pluck('id')
                        ->toArray();

                }

                foreach ($checkShamity as $key1 => $check) {
                    foreach ($check as $key2 => $value) {
                        $checkProduct[$key1][$value] = DB::table('mfn_member_information')
                            ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
                            ->where([['mfn_member_information.samityId', $value],  ['mfn_member_information.softDel', '=', '0']])
                            ->groupBy('mfn_loans_product.productCategoryId')
                            ->pluck('mfn_loans_product.productCategoryId')
                            ->toArray();
                    }
                }

                $count = 0;

                foreach ($checkProduct as $key1 => $checkProductValue) {
                    $count = 0;
                    foreach ($checkProductValue as $key2 => $checkValue) {
                        $count = $count + sizeof($checkValue);
                    }
                    $rowByFieldOfficer[$key1] = $count;
                }
                // END...
            }
            // IF CATEGORY NOT SELECTED AS 'ALL' BUT PRODUCT SELECTED AS 'ALL'
            elseif ($req->searchProductCtg != 'All' and $req->searchProduct == 'All') {
                //  START...
                // TO GET THE ACTUAL ROWSPAN OF THE FIELD OFFICER
                foreach ($fieldOfficersIds as $key => $ids) {
                    $checkShamity[$ids] = DB::table('mfn_samity')
                        ->where([['fieldOfficerId', $ids], ['branchId', $branchId]])
                        ->pluck('id')
                        ->toArray();

                }

                foreach ($checkShamity as $key1 => $check) {
                    foreach ($check as $key2 => $value) {
                        $checkProduct[$key1][$value] = DB::table('mfn_member_information')
                            ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
                            ->where([['mfn_member_information.samityId', $value], 
                                    ['mfn_member_information.softDel', '=', '0'], ['mfn_loans_product.productCategoryId', $searchProCategory]])
                            ->groupBy('mfn_loans_product.productCategoryId')
                            ->pluck('mfn_loans_product.productCategoryId')
                            ->toArray();
                    }
                }

                $count = 0;

                foreach ($checkProduct as $key1 => $checkProductValue) {
                    $count = 0;
                    foreach ($checkProductValue as $key2 => $checkValue) {
                        $count = $count + sizeof($checkValue);
                    }
                    $rowByFieldOfficer[$key1] = $count;
                }
                // END...
            }
            // IF CATEGORY SELECTED AS 'ALL' BUT PRODUCT NOT SELECTED AS 'ALL'
            elseif ($req->searchProductCtg == 'All' and $req->searchProduct != 'All') {
                //  START...
                // TO GET THE ACTUAL ROWSPAN OF THE FIELD OFFICER
                foreach ($fieldOfficersIds as $key => $ids) {
                    $checkShamity[$ids] = DB::table('mfn_samity')
                        ->where([['fieldOfficerId', $ids], ['branchId', $branchId]])
                        ->pluck('id')
                        ->toArray();

                }

                foreach ($checkShamity as $key1 => $check) {
                    foreach ($check as $key2 => $value) {
                        $checkProduct[$key1][$value] = DB::table('mfn_member_information')
                            ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
                            ->where([['mfn_member_information.samityId', $value], 
                                    ['mfn_member_information.softDel', '=', '0'], ['mfn_member_information.primaryProductId', $searchProduct]])
                            ->groupBy('mfn_loans_product.productCategoryId')
                            ->pluck('mfn_loans_product.productCategoryId')
                            ->toArray();
                    }
                }

                $count = 0;

                foreach ($checkProduct as $key1 => $checkProductValue) {
                    $count = 0;
                    foreach ($checkProductValue as $key2 => $checkValue) {
                        $count = $count + sizeof($checkValue);
                    }
                    $rowByFieldOfficer[$key1] = $count;
                }
                // END...
            }
            // IF CATEGORY AND PRODUCT BOTH NOT SELECTED AS 'ALL'
            elseif ($req->searchProductCtg != 'All' and $req->searchProduct != 'All') {
                //  START...
                // TO GET THE ACTUAL ROWSPAN OF THE FIELD OFFICER
                foreach ($fieldOfficersIds as $key => $ids) {
                    $checkShamity[$ids] = DB::table('mfn_samity')
                        ->where([['fieldOfficerId', $ids], ['branchId', $branchId]])
                        ->pluck('id')
                        ->toArray();

                }

                foreach ($checkShamity as $key1 => $check) {
                    foreach ($check as $key2 => $value) {
                        $checkProduct[$key1][$value] = DB::table('mfn_member_information')
                            ->join('mfn_loans_product', 'mfn_member_information.primaryProductId', '=', 'mfn_loans_product.id')
                            ->where([['mfn_member_information.samityId', $value], 
                                    ['mfn_member_information.softDel', '=', '0'], ['mfn_member_information.primaryProductId', $searchProduct]])
                            ->groupBy('mfn_loans_product.productCategoryId')
                            ->pluck('mfn_loans_product.productCategoryId')
                            ->toArray();
                    }
                }

                $count = 0;

                foreach ($checkProduct as $key1 => $checkProductValue) {
                    $count = 0;
                    foreach ($checkProductValue as $key2 => $checkValue) {
                        $count = $count + sizeof($checkValue);
                    }
                    $rowByFieldOfficer[$key1] = $count;
                }
                // END...
            }
        }

        

        return view('microfin.reports.periodicCollectionComponentWiseReport.periodicCollectionComponentWiseReportTable',
            compact(
                'branchId',
                'savingsProducts',
                'fieldOfficersIds',
                'fieldOfficersInfos',
                'rowByFieldOfficer',
                'searchProCategory',
                'searchProduct',
                'searchLoanProduct',
                'startDate',
                'endDate'
            )
        );
    }

    // Mizan Vai Working Area...................... (THIS AREA IS NOT WORKING FOR THE REPORT!)
    public function indexReport(Request $request) {
        $fundingOrgArray  = array();
        $branchArray      = array();
        $productCtgArray  = array();
        $productArray     = array();

        //Auth::user->branchId;
        $branchId = Auth::user()->branchId;
        // selected Branch
        if($request->searchBranch==null) {
            $branchSelected = null;
            $branchArray = DB::table('gnr_branch')->pluck('id')->toArray();
        }
        else {
            $branchSelected = $request->searchBranch;
            array_push($branchArray,$request->searchBranch);
        }
        // Selected Funding Organization
        if($request->searchFundingOrg==null) {
            $fundingOrgSelected = null;
            $fundingOrgArray= DB::table('mfn_funding_organization')->pluck('id')->toArray();
        }
        else {
            $fundingOrgSelected = $request->searchFundingOrg;
            array_push($fundingOrgArray,$request->searchFundingOrg);
        }
        //Search Product Category

        if ($request->searchProductCtg==null) {
            $productCtgSelected = null;
            $productCtgArray= DB::table('mfn_loans_product_category')->pluck('id')->toArray();
        }
        else{
            $productCtgSelected = $request->searchProductCtg;
            array_push($productCtgArray,$request->searchProductCtg);
        }
        //Search Product

        if ($request->searchProduct==null) {
            $productSelected = null;
            $productArray= DB::table('mfn_loans_product')->pluck('id')->toArray();
        }
        else{
            $productSelected = $request->searchProduct;
            array_push($productArray,$request->searchProduct);
        }

        $productId = $request->searchLoanProduct;
        if($branchId==1){
            $searchBranchs     = DB::table('gnr_branch')->where('id','!=',1)->select('id','name','branchCode')->orderBy('branchCode')->get();
        }else{
           $searchBranchs= DB::table('gnr_branch')->where('id',$branchId)->select('id','name','branchCode')->orderBy('branchCode')->get(); 
        }

        if($request->startDate !=null && $request->endDate !=null) {
            $startDate = Carbon::parse($request->startDate)->format('Y-m-d');
            $endDate = Carbon::parse($request->endDate)->format('Y-m-d');

            $fromCollection = DB::table('mfn_loan_collection')->where('collectionDate','>=',$startDate)->where('collectionDate','<=',$endDate)->where('amount','>',0)->select('productIdFk','branchIdFk','samityIdFk','memberIdFk')->get();

            $fromSavingsDeposite = DB::table('mfn_savings_deposit')->where('isTransferred', '=', 0)->where('depositDate','>=',$startDate)->where('depositDate','<=',$endDate)->where('amount','>',0)->select('primaryProductIdFk','branchIdFk','samityIdFk','memberIdFk')->get();

            $fromLoan = DB::table('mfn_loan')->where('disbursementDate','>=',$startDate)->where('disbursementDate','<=',$endDate)->select('productIdFk','branchIdFk','samityIdFk','memberIdFk')->get();

            $fromSavingsWithdrow = DB::table('mfn_savings_withdraw')->where('isTransferred', '=', 0)->where('withdrawDate','>=',$startDate)->where('withdrawDate','<=',$endDate)->select('primaryProductIdFk','branchIdFk','samityIdFk','memberIdFk')->get();

            $arraySamityIdFromCollection      = $fromCollection->pluck('samityIdFk')->toArray();
            $arraySamityIdFromSavingsDeposite = $fromSavingsDeposite->pluck('samityIdFk')->toArray();
            $arraySamityIdFromLoan            = $fromLoan->pluck('samityIdFk')->toArray();
            $arraySamityIdFromSavingsWithdrow = $fromSavingsWithdrow->pluck('samityIdFk')->toArray();

            $allSamityUniqueArray = array_unique((array_merge($arraySamityIdFromCollection,$arraySamityIdFromSavingsDeposite,$arraySamityIdFromLoan,$arraySamityIdFromSavingsWithdrow)));
        }else{

            $allSamityUniqueArray = '';
            $allProductIdUniqueArray = '';
            $fromCollection = '';
            $fromSavingsDeposite = '';
            $fromSavingsWithdrow ='';
            $fromLoan =''; 
        }

        // dd($fromCollection, $fromSavingsDeposite, $fromLoan, $fromSavingsWithdrow);

        $fieldOfficerIds = DB::table('mfn_samity');
        if($request->startDate !=null && $request->endDate !=null) {
            $fieldOfficerIds =  $fieldOfficerIds->whereIn('id',$allSamityUniqueArray);
        } 
        $fieldOfficerIds=$fieldOfficerIds->groupBy('fieldOfficerId')->pluck('fieldOfficerId')->toArray();

        $searchFundingOrgs = DB::table('mfn_funding_organization')->select('id','name')->get();
        $searchProductCtg  = DB::table('mfn_loans_product_category');
        $searchProducts    = DB::table('mfn_loans_product');
        $serchingProductId = DB::table('gnr_branch')->where('id',$request->searchBranch)->value('loanProductId');
        if($serchingProductId !=null) { 
            if($request->searchBranch!=null) {
                $loanProductIds = DB::table('gnr_branch')->where('id',$request->searchBranch)->value('loanProductId');
                $products='';
                $productCtg ='';
                if($loanProductIds!=null) {
                    $productStr =  str_replace(array('"', '[', ']'),'', $loanProductIds);
                    $productArr = array_map('intval', explode(',', $productStr));
                } 
                $categoryId = $searchProducts->whereIn('id',$productArr)->pluck('productCategoryId')->toArray();   
                $searchProducts =  $searchProducts->whereIn('id',$productArr);
                $searchProductCtg = $searchProductCtg->whereIn('id',$categoryId);
            }
        }
        if($request->searchFundingOrg != null) {
            $categoryId = DB::table('mfn_loans_product')->where('fundingOrganizationId',$request->searchFundingOrg)->groupBy('productCategoryId')->pluck('productCategoryId')->toArray();
            $searchProductCtg = DB::table('mfn_loans_product_category')->whereIn('id',$categoryId);
            $searchProducts = DB::table('mfn_loans_product')->where('fundingOrganizationId',$request->searchFundingOrg);
        }

        if($request->searchProductCtg !=null) {
            $searchProducts =  $searchProducts->where('productCategoryId',$request->searchProductCtg);
        }
        $searchProductCtg  = $searchProductCtg->select('id','shortName')->get();
        $searchProducts    = $searchProducts->select('id','shortName')->get();
        //$loanProductId     = DB::table('gnr_branch')->where('id',$request->searchBranch)->value('loanProductId');
        //$loanProductStr    =  str_replace(array('"','[',']'),'',$loanProductId);
        //$loanProductArr    = array_map('intval', explode(',',$loanProductStr));

        //START OPENING BALANCE ARRAY=====

        $samityOpeningBalance = DB::table('mfn_opening_savings_account_info')
                          // ->whereIn('samityIdFk', $savings->unique('samityIdFk')->pluck('samityIdFk'))
                          ->join('mfn_savings_account', 'mfn_savings_account.id', '=', 
                            'mfn_opening_savings_account_info.savingsAccIdFk')
                          ->select('mfn_opening_savings_account_info.samityIdFk', 
                            'mfn_opening_savings_account_info.memberIdFk', 
                            'mfn_opening_savings_account_info.primaryProductIdFk', 
                            'mfn_opening_savings_account_info.openingPrincipal', 
                            'mfn_opening_savings_account_info.openingInterest', 
                            'mfn_opening_savings_account_info.openingWithdraw', 
                            'mfn_savings_account.savingsProductIdFk')
                          ->get();
        //END OPENING BALANCE ARRAY=====

        //START OPENING LOAN BALANCE ARRAY=====

        $samityLoanOpeningBalance = DB::table('mfn_opening_balance_loan')
                          ->join('mfn_loan', 'mfn_loan.id', '=', 
                            'mfn_opening_balance_loan.loanIdFk')
                          ->where('mfn_opening_balance_loan.softDel', 0)
                          ->select('mfn_opening_balance_loan.paidLoanAmountOB',
                            'mfn_loan.productIdFk',
                            'mfn_loan.samityIdFk')
                          ->get();
        //END OPENING LOAN BALANCE ARRAY=====  

        $loanProducts = DB::table('mfn_loans_product');

        if($request->searchFundingOrg !=null) {
            $loanProducts = $loanProducts->where('fundingOrganizationId',$request->searchFundingOrg);
        }
        if($request->searchProductCtg !=null) {
            $loanProducts = $loanProducts->where('productCategoryId',$request->searchProductCtg);  
        }
        if($request->searchProduct !=null) {
            $loanProducts = $loanProducts->where('id',$request->searchProduct);
        }
        $loanProductsName = $loanProducts->select('id','shortName')->get();

        if($request->startDate ==null && $request->endDate ==null) {
            $startDate =DB::table('gnr_branch')->where('id',$branchId)->value('softwareStartDate');
            $endDate =GetSoftwareDate::getMicrofinSoftwareDate();
            $startDateFromarte = null;
            $endDateFromarte = null;
        }
        else{
            $startDateFromarte = date('d-m-Y', strtotime($request->startDate));
            $endDateFromarte = date('d-m-Y', strtotime($request->endDate));
        }
        $savingsProducts = DB::table('mfn_saving_product')->select('shortName','id')->get();

        $productForFundOrg= DB::table('mfn_loans_product');
        if ($fundingOrgSelected!=null) {
            $productForFundOrg = $productForFundOrg->where('fundingOrganizationId',$fundingOrgSelected);
        }
        $productForFundOrg = $productForFundOrg->pluck('id')->toArray();

        return view('microfin.reports.periodicCollectionComponentWiseReport.periodicCollectionComponentWiseReport171018',['searchBranchs'=>$searchBranchs,'branchSelected'=>$branchSelected,'searchFundingOrgs'=>$searchFundingOrgs,'fundingOrgSelected'=>$fundingOrgSelected,'searchProductCtgs'=>$searchProductCtg,'productCtgSelecteds'=>$productCtgSelected,'productSelected'=>$productSelected,'searchProducts'=>$searchProducts,'fieldOfficerIds' => $fieldOfficerIds,'startDate'=>$startDate,'endDate'=>$endDate,'savingsProducts'=>$savingsProducts,'branchId'=>$branchId,'productId'=>$productId,'serchingProductId'=>$serchingProductId,'startDateFromarte'=>$startDateFromarte,'endDateFromarte'=>$endDateFromarte,'productForFundOrg'=> $productForFundOrg,'allSamityUniqueArray'=>$allSamityUniqueArray,'fromCollection'=>$fromCollection,'fromSavingsDeposite'=>$fromSavingsDeposite,'fromLoan'=>$fromLoan,'fromSavingsWithdrow'=>$fromSavingsWithdrow,'loanProductsName'=>$loanProductsName, 'samityOpeningBalance'=>$samityOpeningBalance, 'samityLoanOpeningBalance'=>$samityLoanOpeningBalance]);
       } 
    public function validationfiield (Request $request){
        $rules = array(
            'startDate'    => 'required',
            'endDate'      => 'required',
            'searchBranch' => 'required'
        );
        $attributeNames = array(
            'startDate'     => 'Start date ',
            'endDate'       => 'End date',
            'searchBranch'  => 'Branch name'
        );

        $validator = Validator::make ( Input::all(), $rules);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails()) {
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        return response::json('success');
    }

    public function changeProductCategory(Request $request){

        $searchFundingOrg = (int)json_decode($request->searchFundingOrg);
        if($request->searchFundingOrg=="") {
            $productCtg =  DB::table('mfn_loans_product_category')->select('id','shortName')->get();
            $product =  DB::table('mfn_loans_product')->select('id','shortName')->get();
          }
        else{
            $productFundOrg = DB::table('mfn_loans_product')->where('fundingOrganizationId',$searchFundingOrg)->pluck('productCategoryId')->toArray();
            $productCtg =  DB::table('mfn_loans_product_category')->whereIn('id',$productFundOrg)->select('id','shortName')->get();
            $product =  DB::table('mfn_loans_product')->where('fundingOrganizationId',$searchFundingOrg)->select('id','shortName')->get();
        }
        $data = array(            
           'productCtg' => $productCtg,
           'product'    => $product,
        );
        
        return response()->json($data);
    }

    public function changeProduct(Request $request){
        $searchProCtg = (int)json_decode($request->searchProCtg);
        if($request->searchProCtg==""){
            $product =  DB::table('mfn_loans_product')->select('id','shortName')->get();
        }
        else{
            $product =  DB::table('mfn_loans_product')->where('productCategoryId',$searchProCtg)->select('id','shortName')->get();
        }
        $data = array( 
           'product'        => $product,
        );
        
        return response()->json($data);
    }

    public function changeBranch(Request $request){

        $searchBranch = (int)json_decode($request->searchBranch);
        if($request->searchBranch=="") {
            $productCtg =  DB::table('mfn_loans_product_category')->select('id','shortName')->get();
            $products =  DB::table('mfn_loans_product')->select('id','shortName')->get();
          }
        else{
            $loanProductIds =  DB::table('gnr_branch')->where('id',$searchBranch)->value('loanProductId');
            $products='';
            $productCtg ='';
            if($loanProductIds!=null) {
                $productStr =  str_replace(array('"', '[', ']'),'', $loanProductIds);
                $productArr = array_map('intval', explode(',', $productStr));
                $products = DB::table('mfn_loans_product')->whereIn('id',$productArr)->select('id','shortName','productCategoryId')->get();
                $productCtg = DB::table('mfn_loans_product_category')->whereIn('id',$products->pluck('productCategoryId'))->select('id','shortName')->get();
            }
        }
        $data = array( 
           'products'   => $products,
           'productCtg' =>$productCtg,
        );
        return response()->json($data);
    }
}
