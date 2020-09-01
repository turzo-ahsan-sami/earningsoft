<?php
	
	namespace App\gnr;

	use Illuminate\Database\Eloquent\Model;

	class GnrRegion extends Model {

		public $timestamps = false;
		protected $table = 'gnr_region';
		protected $casts = [
							'zoneId' =>	'array'
							];
		protected $fillable = ['name', 'code', 'zoneId', 'createdDate'];
	}
