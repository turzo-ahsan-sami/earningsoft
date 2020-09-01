<?php

	namespace App\microfin\process;

	use Illuminate\Database\Eloquent\Model;

	class MfnLoanRebate extends Model {

		public $timestamps = false;
		protected $table = 'mfn_loan_rebates';
	}