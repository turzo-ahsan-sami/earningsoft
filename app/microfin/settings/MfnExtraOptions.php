<?php

	namespace App\microfin\settings;

	use Illuminate\Database\Eloquent\Model;

	class MfnExtraOptions extends Model {

		public $timestamps = false;

		protected $table = 'mfn_extra_options';

		protected $fillable = ['name', 
							   'createdDate'
							  ];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}
							  
	}