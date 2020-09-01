<?php

	namespace App\accounting;

	use Illuminate\Database\Eloquent\Model;

	class AccOTSRegisterAccount extends Model
	{
		public $timestamps = false;
	    protected $table ='acc_ots_account';

	    public function getOpeningDateAttribute($value)
	    {
        	return date('d-m-Y',strtotime($value));
    	}

    	public function getMatureDateAttribute($value)
	    {
        	return date('d-m-Y',strtotime($value));
    	}

    	public function getEffectiveDateAttribute($value)
	    {
        	return date('d-m-Y',strtotime($value));
    	}
		
	}
