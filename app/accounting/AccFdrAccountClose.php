<?php

	namespace App\accounting;

	use Illuminate\Database\Eloquent\Model;

	class AccFdrAccountClose extends Model
	{
		public $timestamps = false;
	    protected $table ='acc_fdr_close';

	    public function getClosingDateAttribute($value)
	    {
        	return date('d-m-Y',strtotime($value));
    	}
		
	}
