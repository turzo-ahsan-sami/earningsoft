<?php

namespace App\accounting;

use Illuminate\Database\Eloquent\Model;

class AccLedgerRelation extends Model
{
	public $timestamps = false;
    protected $table ='acc_ledger_relations';
    protected $fillable = [
							'projectId',
                            'ledger1',
                            'ledger2',
                            'relation',
                            'createdDate'
                        ];

}
