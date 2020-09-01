<?php

namespace App\accounting;

use Illuminate\Database\Eloquent\Model;

class AccApprovals extends Model
{
    protected $fillable = [
        'userId',
        'verifiedById',
        'reviewedById',
        'approvedById',
        'verifiedBy',
        'commentsDetails',
        'status'

    ];
}
