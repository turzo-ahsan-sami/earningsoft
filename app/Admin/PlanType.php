<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;

class PlanType extends Model
{
    protected $fillable = ['id', 'name', 'slug', 'description'];

}
