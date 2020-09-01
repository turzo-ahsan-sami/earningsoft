<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['id','name', 'code', 'price', 'description', 'planId', 'numberOfUser', 'renewal_charge_type', 'renewal_charge', 'features', 'image'];

    protected $casts = [
        'features' => 'array'
    ];

    public function plans()
    {
        return $this->hasOne('App\Admin\Plan', 'id', 'planId');
    }

    public function modules()
    {
        return $this->belongsToMany('App\Admin\Module', 'module_product')->withTimestamps();
    }

    public function discounts()
    {
        return $this->hasMany('App\Admin\Discount', 'productId', 'id');
    }

}
