<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosProductModel extends Model
{
    public $timestamps = false;
    protected $table ='pos_product_model';
    protected $fillable = ['name', 'productGroupId', 'productCategoryId', 'productSubCategoryId', 'productBrandId', 'createdDate'];
}
