<?php

	namespace App\gnr;

	use Illuminate\Database\Eloquent\Model;

	class GnrSubFunction extends Model {

		public $timestamps = false;
		protected $table = 'gnr_sub_function';
		protected $fillable = ['id',
							   'subFunctionName',
							   'description'
						       ];
	}
