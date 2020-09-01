<?php

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvProduct extends Model
{
    public $timestamps = false;
    protected $table ='inv_product';
    protected $fillable = ['name', 'description','supplierId','groupId','categoryId','subCategoryId','brandId', 'modelId','sizeId','colorId','uomId','image','vat','barcode', 'systemBarcode','warranty','serviceWarranty','compresserWarranty','costPrice','salesPrice','openingStock', 'minimumStock', 'openingStockAmount', 'createdDate', 'branchId'];
}


