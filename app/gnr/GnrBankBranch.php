<?php

	namespace App\gnr;

	use Illuminate\Database\Eloquent\Model;

	class GnrBankBranch extends Model {

		public $timestamps = false;
		protected $table = 'gnr_bank_branch';

		public function bank()
		{

			return $this->belongsTo('App\gnr\GnrBank', 'bankId_fk', 'id');
		}
		
	}