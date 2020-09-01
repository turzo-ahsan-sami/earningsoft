<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosProductGroup extends Model
{
    public $timestamps = false;
    protected $table ='pos_product_group';
    protected $fillable = ['name', 'createdDate'];
}
