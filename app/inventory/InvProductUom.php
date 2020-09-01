<?php

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvProductUom extends Model
{
    public $timestamps = false;
    protected $table ='inv_product_uom';
    protected $fillable = ['name', 'createdDate'];
}
