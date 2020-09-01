<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
     protected $fillable = ['id','banner_text1', 'banner_text2', 'banner_text3','button_text1','button_text2','banner_image','mini_image'];
}
