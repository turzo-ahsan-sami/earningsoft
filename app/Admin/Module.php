<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['id','name', 'slug', 'code', 'description'];

    public function products()
    {
        return $this->belongsToMany('Rinvex\Subscriptions\Models\Plan');
    }

}
