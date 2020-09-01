<?php

	namespace App\microfin\process;

	use Illuminate\Database\Eloquent\Model;

	class MfnMonthEndProcessMembers extends Model {

		public $timestamps = false;
		protected $table = 'mfn_month_end_process_members';
	}