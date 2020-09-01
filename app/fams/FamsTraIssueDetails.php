<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsTraIssueDetails extends Model
{
    public $timestamps = false;
    protected $table ='inv_tra_issue_details';
    
    public function InvTraIssue()
    {
        return $this->belongsTo('App\FamsTraIssue','id','id');
    }
}
