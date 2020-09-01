<?php

	namespace App\microfin\loan;

	use Illuminate\Database\Eloquent\Model;

	class MfnLoanWriteOff extends Model {

		public $timestamps = false;
		protected $table = 'mfn_loan_write_off';
	}