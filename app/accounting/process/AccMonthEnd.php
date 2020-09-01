<?php

namespace App\accounting\process;

use Illuminate\Database\Eloquent\Model;

class AccMonthEnd extends Model {

	public $timestamps = false;

	protected $table = 'acc_month_end';

	protected $fillable = 	[
							'date',
							'branchIdFk',
							'executionDate',
							'isMonthEnd',
							
							];

	public function scopeActive($query) {
	    
	    return $query->where('status', '=', 1);
	}
}