<?php 

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosClient extends Model
{
    public $timestamps = false;
    protected $table ='pos_client';
    protected $fillable = ['clientCompanyName','companyShortName','clientContactPerson','contactPersonDesigntion','phone','mobile','email','nationalId','address','web','createdDate'];
}




