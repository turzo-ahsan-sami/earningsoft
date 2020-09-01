<?php

	namespace App\microfin\process;

	use Illuminate\Database\Eloquent\Model;

	class MfnDayEnd extends Model {

		public $timestamps = false;

		protected $table = 'mfn_day_end';

		protected $fillable = 	[
								'date',
								'branchIdFk',
								'dueAmount',
								'advancedAmount',
								'totalDueAmount',
								'totalAdvanceAmount',
								'isLocked',
								];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1);
		}
	}