<?php

	namespace App\microfin\process;

	use Illuminate\Database\Eloquent\Model;

	class MfnMonthEndProcessSavings extends Model {

		public $timestamps = false;
		protected $table = 'mfn_month_end_process_savings';
	}