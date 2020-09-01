<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsDepDetails extends Model
{
    public $timestamps = false;
    protected $table ='fams_depreciation_details';
    protected $fillable = ['productId','productCode','depFrom','depTo','days','amount'];

    public function getAmountAttribute($value)
    {
        return number_format($value, 2, '.', ',');   
    }
}
