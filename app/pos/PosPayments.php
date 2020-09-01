<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosPayments extends Model
{
    public $timestamps = false;
    protected $table ='pos_payment';
    protected $fillable = [
        'companyId',
        'ledgerHeadId',
        'name',
        'code',
        'description',
        'createdDate'
    ];


    // public function getAmountAttribute($value)
    // {
    //     return number_format($value, 2, '.', ',');
    // }

    // public function getProductTotalCostAttribute($value)
    // {
    //     return number_format($value, 2, '.', ',');
    // }

    public function ledgerHead()
    {
        return $this->hasOne('App\accounting\AddLedger', 'id', 'ledgerHeadId');
    }
}
