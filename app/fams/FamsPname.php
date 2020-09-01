<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsPname extends Model
{
    public $timestamps = false;
    protected $table ='fams_product_name';
    protected $fillable = ['name','productTypeCode','groupId','categoryId','subCategoryId','productTypeId'];

    public function getProductNameCodeAttribute($value)
    {
        return str_pad( $value, 3, "0", STR_PAD_LEFT );   
    }
}
