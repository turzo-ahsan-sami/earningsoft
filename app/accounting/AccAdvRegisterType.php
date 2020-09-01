<?php

	namespace App\accounting;

	use Illuminate\Database\Eloquent\Model;

	class AccAdvRegisterType extends Model {
	
		public $timestamps = false;
	    protected $table ='acc_adv_register_type';
	  
	    public function getCreatedAtAttribute($value) {
	   
        	return date('d-m-Y',strtotime($value));
    	}

	}