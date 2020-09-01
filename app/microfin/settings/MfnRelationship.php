<?php

	namespace App\microfin\settings;

	use Illuminate\Database\Eloquent\Model;

	class MfnRelationship extends Model {
		
		public $timestamps = false;

	    protected $table ='mfn_relationship';

	    public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1);
		}

	}