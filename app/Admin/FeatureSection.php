<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;

class FeatureSection extends Model
{
	public $timestamps = false;
    protected $table ='feature_section';
    protected $fillable = ['id','name', 'image', 'description'];
}
