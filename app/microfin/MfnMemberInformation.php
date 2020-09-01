<?php

	namespace App\microfin;

	use Illuminate\Database\Eloquent\Model;

	class MfnMemberInformation extends Model {

		public $timestamps = false;
		protected $table = 'mfn_member_information';
		
		protected $fillable = ['name', 
							   'surName',
							   'code', 
							   'gender',
							   'age',
							   'dob',
							   'religion',
							   'profession',
							   'maritalStatus',
							   'motherName',
							   'spouseFatherSonName',
							   'admissionDate',
							   'createdDate'
							  ];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel',0);
		}
							  
	}