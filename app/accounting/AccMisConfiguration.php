<?php

namespace App\accounting;

use Illuminate\Database\Eloquent\Model;

class AccMisConfiguration extends Model
{
	public $timestamps = false;
    protected $table ='acc_mis_configuration';
    protected $fillable = [
                            'misName',
                            'tableFieldName',
                            'misTypeId_Fk',
                            'moduleId',
                            'createdDate'
                        ];
}
