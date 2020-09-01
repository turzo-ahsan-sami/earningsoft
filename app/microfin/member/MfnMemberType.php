<?php

	namespace App\microfin\member;

	use Illuminate\Database\Eloquent\Model;

	class MfnMemberType extends Model {
		
		public $timestamps = false;

	    protected $table ='mfn_member_type';

	    public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1);
		}

	}