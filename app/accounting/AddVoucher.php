<?php

namespace App\accounting;

use Illuminate\Database\Eloquent\Model;

class AddVoucher extends Model
{
    public $timestamps = false;
    protected $table = 'acc_voucher';
    protected $fillable = [
        'voucherTypeId',
        // 'voucherDetailsId',
        'projectId',
        'projectTypeId',
        'voucherDate',
        'voucherCode',
        'globalNarration',
        'branchId',
        'companyId',
        'prepBy',
        // 'referenceId',
        // 'vGenerateType',
        // 'voucherType',
        // 'authBy',
        // 'apprBy',
        'createdDate'

    ];
}
