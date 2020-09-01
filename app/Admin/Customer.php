<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['business_holder_name', 'business_name', 'business_address', 'business_holder_name'];
}
