<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosProductSize extends Model
{
    public $timestamps = false;
    protected $table ='pos_product_size';
    protected $fillable = ['name', 'productGroupId', 'productCategoryId', 'productSubCategoryId', 'productBrandId', 'productModelId', 'createdDate'];
}
