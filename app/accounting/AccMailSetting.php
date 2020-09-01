<?php

	namespace App\accounting;

	use Illuminate\Database\Eloquent\Model;

	class AccMailSetting extends Model {

		public $timestamps = false;

		protected $table = 'acc_account_mail_setting';

		protected $fillable = ['employeeId',
		                       'email', 
							   'createdDate'
							    ];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}
							  
	}