<?php

	  namespace App\microfin\settings;

	  use Illuminate\Database\Eloquent\Model;

	  class MfnPrimaryProduct extends Model {
		
		public $timestamps = false;
		
	    protected $table ='mfn_primary_product';

	    public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1);
		}

	}