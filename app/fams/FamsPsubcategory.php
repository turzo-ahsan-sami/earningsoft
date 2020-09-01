<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsPsubcategory extends Model
{
     public $timestamps = false;
    protected $table ='fams_product_sub_category';
    protected $fillable = ['name', 'productGroupId', 'productCategoryId', 'subCategoryCode'];

    public function getSubCategoryCodeAttribute($value)
    {
        return str_pad( $value, 3, "0", STR_PAD_LEFT );   
    }

   
}
