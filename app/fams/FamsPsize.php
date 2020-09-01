<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsPsize extends Model
{
    public $timestamps = false;
    protected $table ='fams_product_size';
    protected $fillable = ['name'];
}
