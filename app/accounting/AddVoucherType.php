<?php

namespace App\accounting;

use Illuminate\Database\Eloquent\Model;

class AddVoucherType extends Model
{
	public $timestamps = false;
	protected $table ='acc_voucher_type';
	protected $fillable = [ 'id',
	'name',
	'titleName',
	'shortName',
	'createdDate'
];

}
