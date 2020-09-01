<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\gnr\GnrCompany;

class CheckManufacture
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        $companyId = Auth::user()->company_id_fk;
        $checkCompany = GnrCompany::where('id', $companyId)->select('business_type', 'stock_type')->first();

        if($checkCompany->business_type == 'manufacture' && $checkCompany->stock_type == 1)
        {
            return $next($request);
        }
        else return redirect()->back();
    }
}
