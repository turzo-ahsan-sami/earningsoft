<?php

	namespace App\gnr;

	use Illuminate\Database\Eloquent\Model;

	class GnrBank extends Model {

		public $timestamps = false;
		protected $table = 'gnr_bank';

		public function branches() {
			
			return $this->hasMany('App\gnr\GnrBankBranch', 'bankId_fk', 'id');
		}
		
		
	}