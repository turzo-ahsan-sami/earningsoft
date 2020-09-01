<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsPcolor extends Model
{
    public $timestamps = false;
    protected $table ='fams_product_color';
    protected $fillable = ['name'];
}
