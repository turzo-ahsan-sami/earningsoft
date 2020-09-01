<?php

namespace App\Http\Controllers\accounting\mailSetting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\accounting\AccMailSetting;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use DB;
use Carbon\Carbon;
use Mail;

class AccMailSettingController extends Controller {

    public function index(Request $req) {
        $emailRecipients = DB::table('acc_account_mail_setting')->get();
        $employees = DB::table('hr_emp_general_info')->select('id','emp_name_english','emp_id')->get();

        $data = array(
            'employees'      =>$employees,
            'emailRecipients'    =>$emailRecipients
         );

     return view('accounting.mailSetting.viewMailSetting',$data);
    }

    public function addMailSetting(Request $req){
        /*$rules = array(
            'employeeId' => 'required',
            'email' => 'required|unique:acc_account_mail_setting,email'
        );
        $attributeNames = array(
           'employeeId'    => 'employeeName Name',
        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails()){
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        else{*/
                
                 AccMailSetting::truncate();
                foreach($req->employeeId as $key=>$value) { 
                   $mailSetting =new AccMailSetting;
                   $mailSetting->employeeIdFk = $value;
                    $mailSetting->email =$req->email[$key];
                   $mailSetting->createdDate = Carbon::now();
                   $mailSetting->save();
                }

                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  'Data saved successfully.'
                );

                return response::json($data);
           //}
        }


        public function sendMail(){
            //id : ambalait123@gmail.com
            // pass: ambala789
            

           /* $mailRecipients = DB::table('acc_account_mail_setting')->pluck('employeeId')->toArray();
            $mailRecipientMails = DB::table('')
            */
           
            $date = Carbon::toDay();            
            $startDate = $date->format('Y-m-d');
            $endDate = $date->addDays(2)->format('Y-m-d');

            $accounts = DB::table('acc_loan_register_account as t1')
                        ->join('acc_loan_register_payment_schedule as t2','t1.id','t2.accId_fk')
                        ->where('t1.status',1)
                        ->whereBetween('t2.paymentDate',[$startDate,$endDate])
                        ->select('t1.*')
                        ->orderBy('t2.paymentDate')
                        ->get();

            /*if (count($accounts)<1) {
                return "No Installment to pay.";
            }*/


            
            $data = array(
                'accounts'  => $accounts,
                'startDate' => $startDate,
                'endDate'   => $endDate
            );
            

            Mail::send('accounting.register.loanRegister.mailBody', $data, function ($message) {
                $message->from('ambalait123@gmail.com', 'Ambala IT');
                $message->sender('ambalait123@gmail.com', 'Ambala IT');
            
                $message->to('rajufm88@gmail.com', '');
                /*$message->to('md.shariful789@gmail.com', '');*/
            
              /*  $message->cc('john@johndoe.com', 'John Doe');
                $message->bcc('john@johndoe.com', 'John Doe');
            
                $message->replyTo('john@johndoe.com', 'John Doe');*/
            
                $message->subject('Loan Register');
            
                $message->priority(3);
            
                
            });

           
        }

        /*public function generateLoanInstallmentReportToMail(){

            $today = Carbon::toDay()->format('Y-m-d');
            $endDate = Carbon::toDay()->addDays(2)->format('Y-m-d');

            $loanAccountIds = DB::table('acc_loan_register_payment_schedule')->whereBetween('paymentDate',[$today,$endDate])->pluck('accId_fk')->toArray();

            $loanAccounts = DB::table('acc_loan_register_account')->where('status',1)->whereIn('id',$loanAccountIds)->get();
        }*/
    }
