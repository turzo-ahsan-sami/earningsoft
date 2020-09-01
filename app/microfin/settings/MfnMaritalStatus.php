<?php

	  namespace App\microfin\settings;

	  use Illuminate\Database\Eloquent\Model;

	  class MfnMaritalStatus extends Model {
		
		public $timestamps = false;

	    protected $table ='mfn_marital_status';

	    public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1);
		}

	}