<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsPgroup extends Model
{
    public $timestamps = false;
    protected $table ='fams_product_group';
    protected $fillable = ['name'];

    public function getGroupCodeAttribute($value)
    {
        return str_pad( $value, 2, "0", STR_PAD_LEFT );   
    }
}
