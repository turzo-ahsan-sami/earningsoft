<?php
	
	namespace App\Http\Controllers\microfin;

	use App\Http\Controllers\Controller;
	use App\Http\Controllers\microfin\MicroFin;
	use Illuminate\Http\Request;
	use Response;

	class MfnSearchingController extends Controller {
		
		public function getBranchWiseSamityList(Request $req){            
            return MicroFin::getBranchWiseSamityList($req->branchId);
        }

        public function getBranchFieldOfficerList(Request $req){            
            return MicroFin::getBranchWiseFieldOfficerList($req->branchId);
        }

        public function getFunOrgWiseCategoryList(Request $req){            
            return MicroFin::getFunOrgWiseCategoryList($req->funOrgId);
        }
        
        public function getFunOrgWisePrimaryProductList(Request $req){            
        	return MicroFin::getFunOrgWisePrimaryProductList($req->funOrgId);
        }

        public function getCategoryWiseProductList(Request $req){
            return MicroFin::getCategoryWiseProductList($req->categoryId);
        }
        
        public function getWorkingWeeksBaseOnYearNMonth(Request $req){
            return MicroFin::getWorkingWeeksBaseOnYearNMonth($req->year,$req->month);
        }
        public function getLoanAccountsOfParticularMember(Request $req){
            return MicroFin::getLoanAccountsOfParticularMember($req->memberId);
        }
        public function getActiveLoanAccountsOfParticularMember(Request $req){
            return MicroFin::getActiveLoanAccountsOfParticularMember($req->memberId);
        }
        public function getGroupuWiseCompanyList(Request $req){
            return MicroFin::getGroupuWiseCompanyList($req->groupId);
        }
        public function getGroupCompanyWiseProjectList(Request $req){
        	return MicroFin::getGroupCompanyWiseProjectList($req->groupId,$req->companyId);
        }
	}

