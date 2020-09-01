<?php

	namespace App\microfin\settings;

	use Illuminate\Database\Eloquent\Model;

	class MfnProfession extends Model {
		
		public $timestamps = false;

	    protected $table ='mfn_profession';

	    public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1);
		}

	}