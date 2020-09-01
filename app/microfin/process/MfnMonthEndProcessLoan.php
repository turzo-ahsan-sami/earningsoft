<?php

	namespace App\microfin\process;

	use Illuminate\Database\Eloquent\Model;

	class MfnMonthEndProcessLoan extends Model {
		public $timestamps = false;
		protected $table = 'mfn_month_end_process_loans';
	}