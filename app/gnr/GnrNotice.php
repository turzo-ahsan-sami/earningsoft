<?php
namespace App\gnr;
use Illuminate\Database\Eloquent\Model;
use DB;

class GnrNotice extends Model 
{
    public $timestamps = false;
    
    protected $table = 'gnr_notice';
    
    protected $casts = [
        'branchId' => 'array'
    ];
    
    protected $fillable = ['id', 'name','branchId','startDate','endDate','status', 'created_at','updated_at','created_by','updated_by'];
    public static function findByBranchId( $branchId ) 
    {
        return  static::whereRaw('branchId Like :bid', [':bid'=>'%"'.$branchId.'"%'])->first();
    }
}
