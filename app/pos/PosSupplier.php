<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosSupplier extends Model
{
    public $timestamps = false;
    protected $table ='pos_supplier';
    protected $fillable = [
		'companyId',
    	'name',
    	'supComName',
    	'code',
    	'mobile',
    	'email',
    	'address',
    	'website',
    	'description',
    	'refNo',
    	'nid', 
    	'createdDate'
    ];
}
