<?php

namespace App\gnr;

use Illuminate\Database\Eloquent\Model;

class GnrRoom extends Model
{
    public $timestamps = false;
    protected $table ='gnr_room';
    	protected $casts = [
	    					'departmentId' => 'array'
	    				    ];
    protected $fillable = [
    						'name', 
    						'departmentId', 
    						'branchId', 
    						'createdDate'
    					 ];
}
