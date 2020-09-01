<?php

namespace App\accounting;

use Illuminate\Database\Eloquent\Model;

class AddVoucherDetails extends Model
{
    public $timestamps = false;
    protected $table = 'acc_voucher_details';
    protected $fillable = [
        'voucherId',
        'debitAcc',
        'creditAcc',
        'amount',
        'localNarration',
        'createdDate'
    ];
}
