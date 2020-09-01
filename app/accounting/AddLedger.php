<?php

	namespace App\accounting;

	use Illuminate\Database\Eloquent\Model;

	class AddLedger extends Model
	{
		public $timestamps = false;
	    protected $table ='acc_account_ledger';
		// protected $casts = [
		// 					'projectId' => 'array'
		// 					];
	    protected $fillable = [
								'name',
								'code',
								'accountTypeId',
								'ordering',
								'parentId',
								'createdDate',
								'projectBranchId'
								];
	}
