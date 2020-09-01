<?php

namespace App\Http\Controllers\FrontEnd;

use Auth;
use DB;
use Carbon\Carbon;
use Nexmo;

use App\User;
use App\Admin\Customer;
use App\Admin\Training;
use App\Admin\Discount;
use App\gnr\GnrCompany;
use App\gnr\GnrProject;
use App\gnr\GnrProjectType;
use App\gnr\GnrBranch;
use App\gnr\GnrEmployee;
use App\gnr\FiscalYear;


use App\Service\Service;
use App\Service\LedgerCreationHelper;
use App\Service\UserAccessHelper;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Database\Eloquent\Relations\MorphMany;

use Rinvex\Subscriptions\Models\Plan;
use Rinvex\Subscriptions\Models\PlanSubscription;
use Rinvex\Subscriptions\Models\PlanSubscriptionUsage;

use Rinvex\Subscriptions\Services\Period;
use Rinvex\Subscriptions\Traits\HasSubscriptions;


class CustomerController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function activateTrial($planId)
    {
        if (Auth::user() == null) {
            return redirect('customer/signup/' . $planId . '/trial');
        } elseif (Auth::user()->roles->contains('name', 'customer')) {
            return redirect('/dashboard');
        }
        elseif(Auth::user() != null) {
            # code...
        }
    }

    public function activatePlan($planId)
    {
        if (Auth::user() == null) {
            return redirect('customer/signupBuy/' . $planId . '/buy');
        }
        elseif (Auth::user()->roles->contains('name', 'customer')) {
            return redirect('customer/checkout/' . $planId);
        }
        elseif (Auth::user() != null) {
            return redirect('customer/checkout/' . $planId);
        }
    }

    public function checkout($planId)
    {
        if(Auth::user() == null) return redirect('customer/signupBuy/' . $planId . '/buy');
        if(!UserAccessHelper::isMasterUser(Auth::user())) return redirect('/dashboard')->with('warning', 'You do not have sufficient privileges to subscribe a plan.');

        $checkoutInfo = Plan::find($planId);
        $trainingList = Training::all();

        $discountList = Discount::where('planId',$planId)->where('effective_date', '<=', Carbon::now())->where('end_date', '>=', Carbon::now())->get();

        return view('frontend.checkout', compact('checkoutInfo', 'planId', 'trainingList', 'discountList'));
    }

    public function customerPayment(Request $request)
    {
        //dd($request->invoiceTotal);
        $direct_api_url = "https://sandbox.sslcommerz.com/gwprocess/v3/api.php";

        $post_data = array();

        $post_data['store_id']     = $request->store_id;
        $post_data['store_passwd'] = $request->store_passwd;
        // $post_data['total_amount'] = $request->total_amount;
        $post_data['total_amount'] = $request->invoiceTotal;
        $post_data['currency']     = $request->currency;
        $post_data['tran_id']      = $request->tran_id;

        $post_data['value_a']      = $request->planId;
        $post_data['value_b']      = $request->value_b;
        $post_data['value_c']      = $request->value_c;
        $post_data['value_d']      = $request->value_d;

        $post_data['success_url']  = url('/customer/successPayment');
        $post_data['fail_url']     = url('/customer/customerPaymentFail');
        $post_data['cancel_url']   = url('/customer/customerPaymentFail');
        // dd($post_data);

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $direct_api_url);
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($handle, CURLOPT_POST, 1);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC

        $content = curl_exec($handle);
        // dd($content);

        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);


        if ($code == 200 && !(curl_errno($handle))) {
            curl_close($handle);
            $sslcommerzResponse = $content;
        } else {
            curl_close($handle);
            echo "FAILED TO CONNECT WITH SSLCOMMERZ API";
            exit;
        }

        // dd($sslcommerzResponse);

        # PARSE THE JSON RESPONSE
        $sslcz = json_decode($sslcommerzResponse, true);
        // dd($sslcz);


        if (isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL'] != "") {
            # THERE ARE MANY WAYS TO REDIRECT - Javascript, Meta Tag or Php Header Redirect or Other
            # echo "<script>window.location.href = '". $sslcz['GatewayPageURL'] ."';</script>";
            echo "<meta http-equiv='refresh' content='0;url=" . $sslcz['GatewayPageURL'] . "'>";
            # header("Location: ". $sslcz['GatewayPageURL']);
            exit;
        } else {
            echo "JSON Data parsing error!";
        }
    }

    public function getDiscount(Request $request)
    {
        $discountInfo = DB::table('discounts')
        ->where('discounts.id', $request->discountId)
        ->select('discounts.*')
        ->first();
        return response()->json($discountInfo);
    }

    public function getTraining(Request $request)
    {
        $traininInfo = DB::table('trainings')
        ->where('trainings.id', $request->trainingId)
        ->select('trainings.*')
        ->first();
        return response()->json($traininInfo);
    }

    public function customerPaymentFail()
    {
        return redirect('/pricing')->with('fail', 'fail');
    }

    public function customerSignup($planId)
    {
        $purchaseType = request()->route()->getName();
        return view('frontend.customerSignup', compact('purchaseType', 'planId'));
    }

    public function customerSignupBuy($planId)
    {
        $purchaseType = request()->route()->getName();
        return view('frontend.customerSignupBuy', compact('purchaseType', 'planId'));
    }

    public function customerLogin()
    {
        if(Auth::check()) {
            if (UserAccessHelper::hasValidSubscription(Auth::user())) return redirect('/dashboard');
            else return redirect('/pricing');
        }
        else {
            return view('frontend.customerSignin');
        }
        
    }

    public function customerLoginToDashboard(Request $request)
    {
        // dd($request->all());
        $loginAttempt = Auth::attempt(['email' => $request->email, 'password' => $request->password]);

        if($loginAttempt) {
            if (UserAccessHelper::hasValidSubscription(Auth::user())) return redirect('/dashboard');
            else return redirect('/pricing');
        }
        else {
            return redirect('customer/signin')->with(['response' => 'failed', 'message' => 'These credentials do not match our records.']);
        }

        
    }

    public function customerSubscription(Request $request)
    {
        DB::beginTransaction();
        try{
            // dd($request->all());
            $create = $request->all();
            $create['user_type'] = 'master';

            $plan = Plan::find($create['planId']);
            $purchaseType = $create['planId'];

            $verification = Nexmo::verify()->start([
                'number' => $create['mobile'],
                'brand' => 'Mobile Verify',
                'code_length'  => '6'
            ]);
            // dd($verification);

            session(['nexmo_request_id' => $verification->getRequestId()]);
            // dd('ok');

            $customer = User::create($create);
            $customer->assignRole('customer');

            Auth::guard()->login($customer);
            // UserAccessHelper::updateUserRole('master');

            // $customer->newSubscription('main', $plan);
            $customer->newProductSubscription('main', $plan, 'trial');

            DB::commit();
            return redirect('/nexmo')->with('cid', $customer->id);
            // return redirect('/customer/business-setup')->with('success', 'Trial subscription successful');
        }
        catch(\Exception $exception){
            DB::rollback();
            $data = array(
                'responseTitle'  => 'Warning!',
                'responseText'   => $exception->getMessage()
            );
            return response()->json($data);
        }
    }


    public function customerSubscriptionBuy(Request $request)
    {
        $create = $request->all();
        $create['user_type'] = 'master';

        $plan = Plan::find($create['planId']);
        $purchaseType = $create['planId'];

        $verification = Nexmo::verify()->start([
            'number' => $create['mobile'],
            'brand' => 'Mobile Verify',
            'code_length'  => '6'
        ]);
        // dd($verification);

        session(['nexmo_request_id' => $verification->getRequestId()]);

        $customer = User::create($create);
        // $customer->assignRole('customer');

        Auth::guard()->login($customer);
        // UserAccessHelper::updateUserRole('master');

        // $customer->newSubscription('main', $plan);
        
        return redirect('customer/checkout/' . $purchaseType);
    }




    public function successPayment(Request $request)
    {
        # NEW ARRAY DECLARED TO TAKE VALUE OF ALL POST
        // dd($request);

        $store_passwd = "ambal5c7bc49ad58ae@ssl";
        $pre_define_key = explode(',', $request['verify_key']);

        $new_data = array();
        if(!empty($pre_define_key )) {
            foreach($pre_define_key as $value) {
                if(isset($request[$value])) {
                    $new_data[$value] = ($request[$value]);
                }
            }
        }
        # ADD MD5 OF STORE PASSWORD
        $new_data['store_passwd'] = md5($store_passwd);

        # SORT THE KEY AS BEFORE
        ksort($new_data);

        $hash_string="";
        foreach($new_data as $key=>$value) { $hash_string .= $key.'='.($value).'&'; }
        $hash_string = rtrim($hash_string,'&');


        if(md5($hash_string) == $request['verify_sign']) {

            $val_id = urlencode($request['val_id']);
            $store_id = urlencode("ambal5c7bc49ad58ae");
            $store_passwd = urlencode("ambal5c7bc49ad58ae@ssl");
            $requested_url = ("https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php?val_id=".$val_id."&store_id=".$store_id."&store_passwd=".$store_passwd."&v=1&format=json");

            $handle = curl_init();

            curl_setopt($handle, CURLOPT_URL, $requested_url);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false); # IF YOU RUN FROM LOCAL PC
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false); # IF YOU RUN FROM LOCAL PC

            $result = curl_exec($handle);
            $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

            if($code == 200 && !( curl_errno($handle)))
            {


                # TO CONVERT AS ARRAY
                # $result = json_decode($result, true);
                # $status = $result['status'];

                # TO CONVERT AS OBJECT
                $result = json_decode($result);

                # TRANSACTION INFO
                $status = $result->status;
                $tran_date = $result->tran_date;
                $tran_id = $result->tran_id;

                $val_id = $result->val_id;
                $amount = $result->amount;
                //$dueAmount = $result->dueAmount;
                $store_amount = $result->store_amount;
                $bank_tran_id = $result->bank_tran_id;
                $card_type = $result->card_type;



                # ISSUER INFO
                $card_no = $result->card_no;
                $card_issuer = $result->card_issuer;
                $card_brand = $result->card_brand;
                $card_issuer_country = $result->card_issuer_country;
                $card_issuer_country_code = $result->card_issuer_country_code;

                # API AUTHENTICATION
                $APIConnect = $result->APIConnect;
                $validated_on = $result->validated_on;
                $gw_version = $result->gw_version;
                //dd($tran_id);
                $planId = $result->value_a;
                //dd($planId);


                DB::beginTransaction();

                try{
                    $plan = Plan::find($planId);

                    $plan->userid = Auth::user()->id;

                    $invoicePeriod = '';
                    if(isset($_COOKIE['invoice_period'])) {
                        $invoicePeriod = $_COOKIE['invoice_period'];
                    }

                    $plan->invoice_period = $invoicePeriod;

                    $customer = User::find(Auth::user()->id);
                    // $customer->assignRole('customer');


                    // $trial = new Period($plan->trial_interval, $plan->trial_period, now());
                    // $period = new Period($plan->invoice_interval, $plan->invoice_period, $trial->getEndDate());

                    // return $this->subscriptions()->create([
                    //   'name' => $subscription,
                    //   'plan_id' => $plan->getKey(),
                    //   'trial_ends_at' => $trial->getEndDate(),
                    //   'starts_at' => $period->getStartDate(),
                    //   'ends_at' => $period->getEndDate(),
                    // ]);


                    $customer->newProductSubscription('main', $plan, 'buy');

                    DB::commit();

                    if(Auth::user()->roles->contains('name', 'customer')) return redirect('/dashboard')->with('success', 'Successful');

                    $customer->assignRole('customer');

                    return redirect('/nexmo')->with('cid', $customer->id);

                    // return redirect('/customer/business-setup')->with('success', 'Plan subscription successful');
                }
                catch(\Exception $exception){
                    DB::rollback();
                    $data = array(
                        'responseTitle'  =>  'Warning!',
                        'responseText'   => $exception->getMessage()
                    );
                    return response()->json($data);
                }


            }
            else {
                echo "Failed to connect with SSLCOMMERZ";
            }
        }
        else {
            echo "failed";
        }

    }


    public function businessSetup()
    {
        // LedgerCreationHelper::regenerateLedgerTree(Auth::user()->company_id_fk);
        $businessTypes = DB::table('business_type')->get();
        return view('frontend.afterPurchaseLastStep', compact('businessTypes'));
    }

    public function finishSetup(Request $request)
    {
        $businessData = $request->all();
        // dd($businessData);
        //customer
        $customerData['business_name'] = $businessData['business_name'];
        $customerData['business_holder_name'] = $businessData['business_holder_name'];
        $customerData['business_address'] = $businessData['business_address'];
        $customer = Customer::create($customerData);

        //company
        $companyData['name'] = $businessData['business_name'];
        $companyData['customer_id'] = $customer->id;
        $companyData['address'] = $businessData['business_address'];
        $companyData['business_type'] = $businessData['business_type'];
        $companyData['fy_type'] = $businessData['fy_type'];
        $companyData['stock_type'] = $businessData['stock_type'];
        $companyData['ca_level'] = $businessData['ca_level'];
        $companyData['createdDate'] = Carbon::now();
        $company = GnrCompany::create($companyData);

        // fiscal year generate
        Service::generateFiscalYear($company->id);
        Service::generateNextFiscalYear($company->id);

        // project create
        $projectData['name'] = 'General';
        $projectData['projectCode'] = 1;
        $projectData['companyId'] = $company->id;
        $projectData['customerId'] = $customer->id;
        $projectData['createdDate'] = Carbon::now();
        $project = GnrProject::create($projectData);

        // project type create
        $projectTypeData['name'] = 'General';
        $projectTypeData['projectTypeCode'] = 1;
        $projectTypeData['companyId'] = $company->id;
        $projectTypeData['customerId'] = $customer->id;
        $projectTypeData['projectId'] = $project->id;
        $projectTypeData['createdDate'] = Carbon::now();
        $projectType = GnrProjectType::create($projectTypeData);

        // create branch
        $branchData['name'] = 'Head Office';
        $branchData['branchCode'] = 0;
        $branchData['companyId'] = $company->id;
        $branchData['projectId'] = $project->id;
        $branchData['projectTypeId'] = $projectType->id;
        $branchData['branchOpeningDate'] = Carbon::now()->format('Y-m-d');
        $branchData['softwareStartDate'] = Carbon::now()->format('Y-m-d');
        $branchData['aisStartDate'] = Carbon::now()->format('Y-m-d');
        $branchData['createdDate'] = Carbon::now();
        $branch = GnrBranch::create($branchData);

        // create employee

        $employeeData['name'] = $businessData['business_holder_name'];
        $employeeData['branchId'] = $branch->id;
        $employeeData['company_id_fk'] = $company->id;
        $employeeData['presentAddress'] = $businessData['business_address'];
        $employee = GnrEmployee::create($employeeData);

        // user update
        $user = User::find(Auth::user()->id);
        $user->update(['customer_id' => $customer->id, 'company_id_fk' => $company->id, 'branchId' => $branch->id, 'emp_id_fk' => $employee->id]);

        // ledger generate
        LedgerCreationHelper::generateLedgerTree($company->id);

        return redirect('/dashboard');
    }

}
