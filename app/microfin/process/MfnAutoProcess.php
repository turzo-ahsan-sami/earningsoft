<?php

	namespace App\microfin\process;

	use Illuminate\Database\Eloquent\Model;

	class MfnAutoProcess extends Model {

		public $timestamps = false;

		protected $table = 'mfn_auto_process_info';

		protected $fillable = 	[
								'date',
								'samityIdFk',
								'branchIdFk',
								'totalCollectionAmount',
								'totalDepositAmount',
								'memberAttendence'
								];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1);
		}
	}