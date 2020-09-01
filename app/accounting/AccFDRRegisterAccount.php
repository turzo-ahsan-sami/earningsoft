<?php

	namespace App\accounting;

	use Illuminate\Database\Eloquent\Model;

	class AccFDRRegisterAccount extends Model
	{
		public $timestamps = false;
	    protected $table ='acc_fdr_account';
	    protected $fillable = ['fdrId', 'fdrTypeId_fk', 'accName', 'accNo', 'projectId_fk','projectTypeId_fk','branchId_fk','principalAmount', 'interestRate', 'duration', 'openingDate','matureDate','bankId_fk','bankBranchId_fk','accNumber','createdAt'];

	    public function getOpeningDateAttribute($value)
	    {
        	return date('d-m-Y',strtotime($value));
    	}

    	public function getMatureDateAttribute($value)
	    {
        	return date('d-m-Y',strtotime($value));
    	}

		
	}
