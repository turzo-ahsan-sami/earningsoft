<?php

	namespace App\accounting;

	use Illuminate\Database\Eloquent\Model;

	class AccFDRRegisterReceivable extends Model
	{
		public $timestamps = false;
	    protected $table ='acc_fdr_receivable';

	    public function getDateFromAttribute($value)
	    {
        	return date('d-m-Y',strtotime($value));
    	}

	    public function getReceivableDateAttribute($value)
	    {
        	return date('d-m-Y',strtotime($value));
    	}
		
	}
