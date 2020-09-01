<?php

	namespace App\microfin\loan;

	use Illuminate\Database\Eloquent\Model;

	class MfnLoanWriteOffCollection extends Model {

		public $timestamps = false;
		protected $table = 'mfn_loan_write_off_collection';
	}