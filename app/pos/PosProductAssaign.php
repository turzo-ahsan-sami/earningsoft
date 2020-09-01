<?php 

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosProductAssaign extends Model {

    public $timestamps = false;
    protected $table ='pos_product_assaign';
    protected $fillable = ['clientcompanyId', 'productId','salesPerson','salesPriceHo','salesPriceBo','servicePerson','serviceChargeHo','serviceChargeBo','createdDate'];
}
