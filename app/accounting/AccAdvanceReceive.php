<?php
namespace App\accounting;

	use Illuminate\Database\Eloquent\Model;

	class AccAdvanceReceive extends Model {

		public $timestamps = false;
	    protected $table ='acc_adv_receive';
	   
	     public function getCreatedAtAttribute($value) {
	   
        	  return date('d-m-Y',strtotime($value));
    	 }
    }