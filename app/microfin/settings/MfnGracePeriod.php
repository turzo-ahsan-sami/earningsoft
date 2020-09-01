<?php

	namespace App\microfin\settings;

	use Illuminate\Database\Eloquent\Model;

	class MfnGracePeriod extends Model {

		public $timestamps = false;

		protected $table = 'mfn_grace_period';

		protected $fillable = ['name',
							   'inDays', 
							   'createdDate'
							  ];

		public function scopeActive($query) {
		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}					  
	}

