<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsPuom extends Model
{
    public $timestamps = false;
    protected $table ='fams_product_uom';
    protected $fillable = ['name'];
}
