<?php

	namespace App\gnr;

	use Illuminate\Database\Eloquent\Model;

	class GnrDistrict extends Model {

		public $timestamps = false;
		protected $table = 'gnr_district';
		protected $fillable = [
							   'name', 
							   'divisionId',
							   'createdDate'
							  ];
							  
	}