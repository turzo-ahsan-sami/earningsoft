<?php

namespace App\Service;

use App\User;

use DB;
use Auth;
use DateTime;
use DateInterval;
use Carbon\Carbon;

use Rinvex\Subscriptions\Models\PlanSubscription;
use Illuminate\Database\Eloquent\Collection;


/**
 *
 * Helper Class For User Access 
 *
 * @author turzo
 *
 */

class UserAccessHelper
{   

    static public function isMasterUser($user){
       return DB::table('users')->where('id', $user->id)->where('user_type', 'master')->exists();
    }

    static public function getMasterUserIdofCustomer($user){
        if(UserAccessHelper::isMasterUser($user)) return $user->id; 
        $masterUser = DB::table('users')->where('customer_id', $user->customer_id)->where('user_type', 'master')->first();
        if(isset($masterUser)) return $masterUser->id;
        return null;
    }

    static public function isValidUser($user)
    { 
        $userId = UserAccessHelper::getMasterUserIdofCustomer($user);
        if($userId){
            return DB::table('plan_subscriptions')->where('user_id', '=', $userId)->exists();
        }
        return false;
    }

    static public function hasValidSubscription($user)
    {   
        if(UserAccessHelper::isValidUser($user)){
            $userId = UserAccessHelper::getMasterUserIdofCustomer($user);
            $planSubscription = DB::table('plan_subscriptions')->where('user_id', '=', $userId)->first();
            if($planSubscription->trial_ends_at){
                return $planSubscription->trial_ends_at >= Carbon::now()->toDateTimeString();
            } 
            else if($planSubscription->ends_at) {
                return $planSubscription->ends_at >= Carbon::now()->toDateTimeString();     
            } 
        }
        return false;
    }

    static public function updateUserRole($role){
        $user = User::find(Auth::user()->id);
        $user->user_type = $role;
        $user->save();
    }

    static public function getAuthorizedModules(){
        $userCustomerId = Auth::user()->customer_id;
        $mainUserOfCustomer = User::where('customer_id', $userCustomerId)->where('user_type', 'master')->orderBy('id', 'asc')->first();
        $slug = DB::table('plan_subscriptions')->where('user_id', $mainUserOfCustomer->id)->value('slug');
        $modules = $mainUserOfCustomer->subscription($slug)->plan->modules->sortBy('code');

        return $modules;
    }


    static public function hasAccessToModule($request){
        $modules = UserAccessHelper::getAuthorizedModules();
        $path = $request->getPathInfo();
        $moduleName = explode ("/", $path)[1];
        
        if($moduleName == 'dashboard') return true;
        if($moduleName == 'home') return true;
        if($moduleName == 'gnr') return true;

        if($moduleName != 'acc' || $moduleName != 'fams' || $moduleName != 'pos' || $moduleName != 'inventory') {
            $has_module = 0;
            foreach($modules as $module){
                if($module->name == ucfirst($moduleName)) $has_module = 1;
            }
            
            if($has_module) return true;
            return false;
        }
        
        return false;
    }

    // static public function hasValidSubscription($user)
    // { 
    //     if (DB::table('plan_subscriptions')->where('user_id', '=', $user->id)->exists()){
    //         $planSubscription = DB::table('plan_subscriptions')->where('user_id', '=', $user->id)->first();
    //         if($planSubscription->trial_ends_at){
    //             return $planSubscription->trial_ends_at >= Carbon::now()->toDateTimeString();
    //         } 
    //         else if($planSubscription->ends_at) {
    //             return $planSubscription->ends_at >= Carbon::now()->toDateTimeString();     
    //         } 
    //     }
    //     return false;
    // }


}
?>