<?php

namespace App\Http\Controllers\microfin\reports;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\microfin\MicroFinance;

class AjaxForReportController extends Controller {

	protected $MicroFinance;

	public function __construct() {

		$this->MicroFinance = New MicroFinance;
	}

    public function getSamityByDay(Request $request) {

        $samityNamesOption = $this->MicroFinance->getDayWiseSamityOptions($request->branchValue, $request->samityDayValue);
        // $samityNamesOption=gettype($samityNamesOption);

        return response()->json($samityNamesOption);        
    }

    public function getSamityByBranch(Request $request) {

        $samityNamesOption = $this->MicroFinance->getBranchWiseSamityOptions($request->branchValue);
        // $samityNamesOption=gettype($samityNamesOption);

        return response()->json($samityNamesOption);        
        // return response()->json('Hello');    	
    }

    public function getLoanProductsByProductCategory(Request $request) {

        $loanProductsOption = $this->MicroFinance->getProductCategoryWiseLoanProduct($request->productCategoryValue);

        return response()->json($loanProductsOption);    	
    }

}
