<?php

	namespace App\accounting;

	use Illuminate\Database\Eloquent\Model;

	class AccLoanRegisterAccount extends Model
	{
		public $timestamps = false;
	    protected $table ='acc_loan_register_account';
	    

	    public function getLoanDateAttribute($value)
	    {
        	return date('d-m-Y',strtotime($value));
    	}

    	

		
	}
