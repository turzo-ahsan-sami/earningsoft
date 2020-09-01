<?php

namespace App\Http\Controllers;

use Auth;
use Nexmo;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class NexmoController extends Controller
{
    public function show()
    {
        return view('mobile_verify');
    }

    public function verify(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'code' => 'size:6'
        ]);

        $request_id = session('nexmo_request_id');
        $verification = new \Nexmo\Verify\Verification($request_id);

        Nexmo::verify()->check($verification, $request->code);

        $date = date_create();
        DB::table('users')->where('id', Auth::id())->update(['mobile_verified_at' => date_format($date, 'Y-m-d H:i:s')]);

        return redirect('/customer/business-setup')->with('success', 'Subscription successful!');
    }
}
