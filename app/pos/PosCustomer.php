<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosCustomer extends Model
{
    public $timestamps = false;
    protected $table ='pos_customer';
    protected $fillable = ['companyId', 'name', 'code','mobile','email','nid', 'createdDate'];
}
