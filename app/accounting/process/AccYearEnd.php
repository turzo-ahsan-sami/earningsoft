<?php

namespace App\accounting\process;

use Illuminate\Database\Eloquent\Model;

class AccYearEnd extends Model {

	public $timestamps = false;

	protected $table = 'acc_year_end';

	protected $fillable = 	[
							'date',
							'branchIdFk',
							'companyId',
							'fiscalYearId',							
							];
}

?>
