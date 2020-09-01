<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $fillable = ['id','title','numberOfTrainee','price','desc'];
}
