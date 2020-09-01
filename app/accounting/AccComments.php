<?php

namespace App\accounting;

use Illuminate\Database\Eloquent\Model;

class AccComments extends Model
{
    protected $fillable = [
        
        'voucherId',
        'reviewedBy',
        'verifiedBy',
        'approvedBy',
        'commentsDetails',
        'status'

    ];
}
