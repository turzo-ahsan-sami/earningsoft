<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;
use App\accounting\AddLedger;

class PosOtherCost extends Model
{
    public $timestamps = false;
    protected $table ='pos_other_cost';
    protected $fillable = ['name', 'ledger_id'];

    function ledger()
    {
        return $this->hasOne(AddLedger::class, 'id', 'ledger_id');
    } 

}
