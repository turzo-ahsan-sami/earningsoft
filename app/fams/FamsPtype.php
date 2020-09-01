<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsPtype extends Model
{
    public $timestamps = false;
    protected $table ='fams_product_type';
    protected $fillable = ['name','productTypeCode','groupId','categoryId','subCategoryId'];

    public function getProductTypeCodeAttribute($value)
    {
        return str_pad( $value, 3, "0", STR_PAD_LEFT );   
    }
}
