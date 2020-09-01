<?php

namespace App\accounting;

use Illuminate\Database\Eloquent\Model;

class AccAutoVoucherConfigForAll extends Model
{
	public $timestamps = false;
    protected $table ='acc_auto_voucher_config';
    protected $fillable = [
                            'moduleId',
                            'misConfigId',
                            'tableFieldName',
                            'misTypeId_Fk',
                            'amountType',
                            'ledgerId',
                            'localNarration',
                            'voucherType',
                            'createdDate'
                        ];
}
