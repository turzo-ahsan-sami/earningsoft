<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosProductUom extends Model
{
    public $timestamps = false;
    protected $table ='pos_product_uom';
    protected $fillable = ['name', 'createdDate'];
}
