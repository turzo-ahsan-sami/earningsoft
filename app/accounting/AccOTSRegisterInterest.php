<?php

	namespace App\accounting;

	use Illuminate\Database\Eloquent\Model;

	class AccOTSRegisterInterest extends Model
	{
		public $timestamps = false;
	    protected $table ='acc_ots_interest';

	    public function getCreatedAtAttribute($value)
	    {
        	return date('d-m-Y',strtotime($value));
    	}
		public function getDateFromAttribute($value)
	    {
        	return date('d-m-Y',strtotime($value));
    	}
    	public function getDateToAttribute($value)
	    {
        	return date('d-m-Y',strtotime($value));
    	}
    	public function getAmountAttribute($value)
	    {
        	return number_format($value,2,'.',',');
    	}
	}
