<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsTraIssue extends Model
{
    public $timestamps = false;
    protected $table ='fams_tra_issue';
    

    function getIssueDateAttribute()
	{
    	return date('m/d/Y', strtotime($this->attributes['issueDate']));
	}

	public function issueDetails()
	{
	    return $this->hasOne('App\FamsTraIssueDetails','id', 'id');
	}
}
