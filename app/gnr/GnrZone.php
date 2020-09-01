<?php

	namespace App\gnr;

	use Illuminate\Database\Eloquent\Model;

	class GnrZone extends Model {

		public $timestamps = false;
		
		protected $table = 'gnr_zone';
		
		protected $casts = [
			'areaId' =>	'array'
		];
		
		protected $fillable = [
			'name', 
			'code', 
			'areaId', 
			'createdDate'
		];

		public static function findByBranchId($branchId)
		{
			$area = GnrArea::whereRaw('branchId Like :bid', [':bid'=>'%"'.$branchId.'"%'])->first();
			if($area) {
				$zone = GnrZone::whereRaw('areaId Like :aid', [':aid'=>'%"'.$area->id.'"%'])->first();
				return $zone;
			}
			return null;
		}
	}