<?php

	namespace App\microfin\settings;

	use Illuminate\Database\Eloquent\Model;

	class MfnDesignation extends Model {
		
		public $timestamps = false;
		
	    protected $table ='mfn_designation';

	    public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1);
		}

	}