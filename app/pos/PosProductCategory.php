<?php 

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosProductCategory extends Model
{
    public $timestamps = false;
    protected $table ='pos_product_category';
    protected $fillable = ['name', 'productGroupId', 'createdDate'];
}
