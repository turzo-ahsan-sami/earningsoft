<?php

namespace App\gnr;

use Illuminate\Database\Eloquent\Model;

class GnrDepartment extends Model
{
    public $timestamps = false;
    
    protected $table ='gnr_department';
    
    protected $fillable = [
        'name', 
        'createdDate'
    ];
}
