<?php

    namespace App\Http\Controllers\microfin\process;

    use Illuminate\Http\Request;
    use App\Http\Requests;
    use Validator;
    use Response;
    use DB;
    use Carbon\Carbon;
    use App\Http\Controllers\Controller;


    class ChangeFirstRepayDateController extends Controller {

        public function index(){
            return view('microfin/process/changeLoanFirstRepayDate/addData');
        }
    }