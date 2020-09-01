<?php

	namespace App\accounting;

	use Illuminate\Database\Eloquent\Model;

	class AccAdvRegister extends Model {

		public $timestamps = false;
	    protected $table ='acc_adv_register';
	   

	    public function getCreatedAtAttribute($value) {
	  
        	return date('d-m-Y',strtotime($value));
    	}

		
	}