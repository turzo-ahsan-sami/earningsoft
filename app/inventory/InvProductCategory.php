<?php 

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvProductCategory extends Model
{
	public $timestamps = false;
	protected $table ='inv_product_category';
	protected $fillable = ['id','name', 'productGroupId', 'createdDate'];
}
