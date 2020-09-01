<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsTraIssueReturn extends Model
{
	public $timestamps = false;
	protected $table ='fams_tra_issue_return';
	protected $fillable = [
		'id',
		'issueReturnBillNo',
		'branchId',
		'issueReturnDate',
		'totalIssueReturnQuantity',
		'totalIssueReturnAmount',
		'crea',
		'up',
		'st'
		//'createdDate'
	];

}
