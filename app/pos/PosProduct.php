<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosProduct extends Model
{
    public $timestamps = false;
    protected $table ='pos_product';
    protected $fillable = ['companyId', 'name', 'code', 'costPrice', 'salesPrice', 'createdDate'];
}
