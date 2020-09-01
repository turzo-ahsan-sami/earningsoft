<?php 

namespace App\pos;

use Illuminate\Database\Eloquent\Model;
use App\gnr\GnrCompany;
use App\pos\PosPayments;
use App\pos\PosSales;

class PosCollection extends Model
{
    // public $timestamps = false;
    protected $table ='pos_collection';
    protected $fillable = ['salesBillNo','salesId','customerId','companyId','collectionAmount','collectionDate','createdDate','status'];

    public function customer()
    {
        return $this->belongsTo(PosCustomer::class,'customerId','id');
    }

     public function company()
    {
    	return $this->belongsTo(GnrCompany::class,'companyId','id');
    }

     public function payment()
    {
        return $this->belongsTo(PosPayments::class,'paymentType','id');
    }

     public function sales()
    {
        return $this->belongsTo(PosSales::class,'salesId','id');
    }
}
