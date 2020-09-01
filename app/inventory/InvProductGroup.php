<?php

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvProductGroup extends Model
{
    public $timestamps = false;
    protected $table ='inv_product_group';
    protected $fillable = ['name', 'createdDate'];
}
