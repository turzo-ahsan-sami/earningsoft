<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\User;
use Rinvex\Subscriptions\Models\Plan;
use Rinvex\Subscriptions\Models\PlanSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Service\UserAccessHelper;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd('ok');
        if (! Gate::allows('admin_home')) {
            return abort(401);
        }

        return view('home');
    }

    

    public function dashboard()
    {
        $user = Auth::user();
        // dd($user->id);

        if(!UserAccessHelper::hasValidSubscription($user)) return redirect('/pricing');
        else{
                    
            // $userCustomerId = Auth::user()->customer_id;
            // $mainUserOfCustomer = User::where('customer_id', $userCustomerId)->where('user_type', 'master')->orderBy('id', 'asc')->first();
            // $slug = PlanSubscription::where('user_id', $mainUserOfCustomer->id)->value('slug');           
            // $modules = $mainUserOfCustomer->subscription($slug)->plan->modules;
                       
            $modules = UserAccessHelper::getAuthorizedModules();
    
            return view('homePages.welcomeHomePages.viewWelcomeHome', compact('modules'));
        }
    }



  
}
