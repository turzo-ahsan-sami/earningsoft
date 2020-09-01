<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = ['id','title','planId','desc', 'discount_type','value','effective_date','end_date'];


    public function plan()
    {
        return $this->belongsTo('Rinvex\Subscriptions\Models\Plan', 'planId', 'id');
    }
}
