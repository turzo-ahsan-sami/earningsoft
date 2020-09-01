<?php 

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosDayEnd extends Model
{
    public $timestamps = false;
    protected $table ='pos_day_end';
    protected $fillable = ['branchIdFk','executedByEmpIdFk','branchDate','startDate','endDate','isLocked','totalSalesQuantity','totalSalesAmount','totalSalesPayAmount','totalCollectionAmount','createdDate'];

}