<?php

	namespace App\microfin\process;

	use Illuminate\Database\Eloquent\Model;

	class AuthorizationInfo extends Model {

		public $timestamps = false;

		protected $table = 'mfn_auto_process_tra_authorization_info';

		/*protected $fillable = 	[
								'date',
								'branchIdFk',
								'dueAmount',
								'advancedAmount',
								'totalDueAmount',
								'totalAdvanceAmount',
								'isLocked',
								];*/

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1);
		}
	}