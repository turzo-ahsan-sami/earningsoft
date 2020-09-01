<?php 

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosMonthEnd extends Model
{
    public $timestamps = false;
    protected $table ='pos_month_end';
    protected $fillable = ['branchIdFk','executedByEmpIdFk','date','isLocked','createdDate'];
}