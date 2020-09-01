<?php

namespace App\gnr;

use Illuminate\Database\Eloquent\Model;

class GnrSupplier extends Model
{
    public $timestamps = false;
    protected $table ='gnr_supplier';
    protected $fillable = [
	    						'name',
	    						'supplierCompanyName',
	    						'email','mailForNotify',
	    						'phone', 'address',
	    						'website',
	    						'description',
	    						'refNo',
	    						'attentionFirst',
	    						'attentionFirst',
	    						'attentionSecond',
	    						'attentionThird',
	    						'createdDate'
    						];
}
