<?php

	namespace App\gnr;

	use Illuminate\Database\Eloquent\Model;

	class GnrDivision extends Model {

		public $timestamps = false;
		protected $table = 'gnr_division';
		protected $fillable = [
							   'name', 
							   'createdDate'
							  ];
							  
	}