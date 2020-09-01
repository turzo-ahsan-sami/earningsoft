<?php

	namespace App\microfin\process;

	use Illuminate\Database\Eloquent\Model;

	class MfnMonthEnd extends Model {

		public $timestamps = false;

		protected $table = 'mfn_month_end';

		protected $fillable = 	[
								'date',
								'branchIdFk',
								'executionDate',
								'isLocked',
								'collectionAmount',
								'collectionDueAmount',
								'advanceAmount',
								'savingsAmount',
								'withdrawAmount',
								];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1);
		}
	}