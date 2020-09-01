<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsPcategory extends Model
{
    public $timestamps = false;
    protected $table ='fams_product_category';
    protected $fillable = ['name', 'categoryCode','productGroupId'];

    public function getCategoryCodeAttribute($value)
    {
        return str_pad( $value, 2, "0", STR_PAD_LEFT );   
    }
}
