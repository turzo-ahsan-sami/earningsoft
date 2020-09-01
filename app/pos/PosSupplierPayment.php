<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;
//use App\pos\PosSupplier;
use App\gnr\GnrCompany;
use App\pos\PosPayments;
use App\pos\PosPurchase;

class PosSupplierPayment extends Model
{
	protected $table ='pos_supplier_payment';
    protected $fillable = ['purchaseBillNo','purchaseId','supplierId','companyId','paymentType','paidAmount','paymentDate','createdDate','status'];
     
	 public function supplier()
    {
    	return $this->belongsTo(PosSupplier::class,'supplierId','id');
    }
	
	 public function company()
    {
    	return $this->belongsTo(GnrCompany::class,'companyId','id');
    }

    public function payment()
    {
        return $this->belongsTo(PosPayments::class,'paymentType','id');
    }

    public function purchase()
    {
        return $this->belongsTo(PosPurchase::class,'purchaseId','id');
    }
}
