<?php

	namespace App\microfin\configuration;

	use Illuminate\Database\Eloquent\Model;

	class MfnSamityConfiguration extends Model {

		public $timestamps = false;

		protected $table = 'mfn_cfg';

		protected $fillable = ['name',
							   'config', 
							   'createdDate'
							  ];

		/*public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}*/
							  
	}