<?php

namespace App\accounting;

use Illuminate\Database\Eloquent\Model;

class AccAutoVoucherConfig extends Model
{
	public $timestamps = false;
    protected $table ='acc_mfn_auto_voucher_config';
    protected $fillable = [
                            'loanProductId',
                            'savingProductId',
                            'ledgerCodeForPrincipal',
                            'ledgerCodeForInterest',
                            'ledgerCodeForInsurence',
                            'ledgerCodeForSktAmount',
                            'ledgerCodeForInterestProvision',
                            'ledgerCodeForUnsettledClaim',
                            'type',
                            'createdDate'
                        ];
}
