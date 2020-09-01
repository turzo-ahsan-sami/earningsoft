<?php

namespace App\accounting;

use Illuminate\Database\Eloquent\Model;

class AddAccountType extends Model
{
	public $timestamps = false;
    protected $table ='acc_account_type';
    protected $fillable = [
                            'name',
                            'parentId',
                            'description',
                            'createdDate',
                            'isParent'
                        ];
    
}
