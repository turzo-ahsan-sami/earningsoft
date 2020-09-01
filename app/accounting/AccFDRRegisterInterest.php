<?php

	namespace App\accounting;

	use Illuminate\Database\Eloquent\Model;

	class AccFDRRegisterInterest extends Model
	{
		public $timestamps = false;
	    protected $table ='acc_fdr_interest';

	    public function getReceiveDateAttribute($value)
	    {
        	return date('d-m-Y',strtotime($value));
    	}
		
	}
