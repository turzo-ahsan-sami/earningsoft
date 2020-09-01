<?php

	namespace App\microfin\savings;

	use Illuminate\Database\Eloquent\Model;

	class MfnOpeningSavingsAccountInfo extends Model {

		public $timestamps = false;

		protected $table = 'mfn_opening_savings_account_info';

		protected $fillable = ['savingsAccIdFk', 
							   'openingPrincipal',
							   'openingInterest', 
							   'openingWithdraw',
							   'openingBalance'
							  ];

	}