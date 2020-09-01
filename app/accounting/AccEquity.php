<?php

namespace App\accounting;

use Illuminate\Database\Eloquent\Model;

class AccEquity extends Model
{
	public $timestamps = false;
    protected $table ='acc_equity';
    protected $fillable = [
                            'obId',
                            'projectId',
                            'branchId',
                            'projectTypeId',
                            'openingDate',
                            'fiscalYearId',
                            'reserveFundAmount',
                            'surplusAmount',
                            'createdDate'
                        ];
}
