<?php

	namespace App\gnr;

	use Illuminate\Database\Eloquent\Model;

	class GnrUpazila extends Model {

		public $timestamps = false;
		protected $table = 'gnr_upzilla';
		protected $fillable = [
							   'name', 
							   'divisionId',
							   'districtId',
							   'createdDate'
							  ];
							  
	}