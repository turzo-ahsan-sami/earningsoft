<?php

	namespace App\microfin\process;

	use Illuminate\Database\Eloquent\Model;

	use DB;

	class MfnLoanWaiver extends Model {

		public $timestamps = false;
		protected $table = 'mfn_loan_waivers';
	}