<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;

class UserReview extends Model
{
	public $timestamps = false;
    protected $table ='user_review';
    protected $fillable = ['id','user_id','comment'];
}
