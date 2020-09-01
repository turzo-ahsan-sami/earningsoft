<?php

namespace App\Http\Middleware;

use Closure;
use App\Service\TransactionCheckHelper;

class CheckTransaction
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
        $checkValue = new TransactionCheckHelper();
        $checkTransaction = $checkValue->monthEndYearEndCheck();

        if($checkTransaction['status'] == true)
        {
            return $next($request);
        }
        else return redirect()->back()->with(['msgActive' => true]);
    }
}
