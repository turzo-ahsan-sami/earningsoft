<?php

	namespace App\microfin\member;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Facades\Auth;

	class MfnMemberInformation extends Model {

		public $timestamps = false;

		protected $table = 'mfn_member_information';

		protected $casts = [
	    					'nomineeId'   => 'array',
	    					'referenceId' => 'array'
	    				   ];


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
							   'familyMemberNum',
							   'relationship',
							   'birthRegNo',
							   'nID',
							   'passportNo',
							   'mobileNo',
							   'nationality',
							   'maxEducation',
							   'admissionDate',
							   'admissionNo',
							   'admissionFee',
							   'formApplicationNo',
							   'memberTypeId',
							   'branchId',
							   'samityId',
							   'primaryProductId',
							   'groupId',
							   'subGroupId',
							   'curVillageWard',
							   'curPostOfficeArea',
							   'curFamilyHomeMobileNo',
							   'curResidence',
							   'permVillageWard',
							   'permPostOfficeArea',
							   'permFamilyHomeMobileNo',
							   'yearlyIncome',
							   'nomineeId',
							   'referenceId',
							   'landArea',
							   'note',
							   'fixedAssetDesc',
							   'profileImage',
							   'regularSignatureImage',
							   'nIDSignatureImage',
							   'entryBy',
							   'createdDate'
							  ];

		
		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '=', 0);
		}

		public function scopeBranchWise($query) {
		    
		    return $query->where('branchId', '=', Auth::user()->branchId);
		}
							  
	}