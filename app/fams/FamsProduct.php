<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsProduct extends Model
{
    public $timestamps = false;
    protected $table ='fams_product';

    public function getdepreciationOpeningBalanceAttribute($value)
    {
        return number_format($value, 2, '.', ',');   
    }

    
    /*protected $fillable = ['name', 'description','supplierId','groupId','categoryId','subCategoryId','brandId', 'modelId','sizeId','colorId','uomId','image','vat','barcode', 'systemBarcode','warranty','serviceWarranty','compresserWarranty','costPrice','salesPrice','openingStock', 'minimumStock', 'openingStockAmount'];*/
    //protected $guarded = ['price'];
}
