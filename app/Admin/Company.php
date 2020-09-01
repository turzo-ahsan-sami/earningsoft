<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['id','name', 'email', 'mobile','address','website','logo'];
}
