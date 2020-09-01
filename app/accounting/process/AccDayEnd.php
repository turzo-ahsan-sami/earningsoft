<?php


namespace App\accounting\process;

use Illuminate\Database\Eloquent\Model;

class AccDayEnd extends Model {

	public $timestamps = false;

	protected $table = 'acc_day_end';

	protected $fillable = 	[
							'date',
							'branchIdFk',
							'isDayEnd',
							];

	public function scopeActive($query) {
	    
	    return $query->where('status', '=', 1);
	}
}