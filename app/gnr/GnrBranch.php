<?php

namespace App\gnr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class GnrBranch extends Model
{
    public $timestamps = false;

    protected $table ='gnr_branch';

    protected $casts = [
                        'loanProductId'     =>  'array',
                        'savingsProductId'  =>  'array'
                       ];
    
    protected $fillable = ['name', 'groupId','companyId','projectId','projectTypeId', 'loanProductId', 'savingsProductId', 'contactPerson','email','phone','address','branchCode', 'branchOpeningDate', 'softwareStartDate', 'aisStartDate', 'status', 'createdDate'];

    public function projectType()
    {
        return $this->belongsTo('App\gnr\GnrProjectType','id','projectTypeId');
    }

    public function getBranchCodeAttribute($value)
    {
        return str_pad( $value, 3, "0", STR_PAD_LEFT );   
    }
    
    public static function options($selectAny=null,$companyId=null){
        if($companyId==null)
           $result = self::get();
        else
            $result = self::where('companyId',intval($companyId))->get();
        $option=[];
        if($selectAny!=null){
          $option['']=$selectAny;
        }
        foreach($result as $row){
            $option[$row->id]=$row->name;
        }
        
        return $option;
    }

    public function scopeActive($query) {
            
        return $query->where('status', '=', 1);
    }

    public function scopeBranchWise($query) {
            
        return $query->where('id', '=', Auth::user()->branchId);
    }

    
    public function getArea($bid)
    {
        $model = GnrArea::whereRaw('branchId Like :bid', [':bid'=>'%"'.$bid.'"%'])->first();
        return $model;
    }

    public function getZone($bid)
    {
        $area = GnrArea::whereRaw('branchId Like :bid', [':bid'=>'%"'.$bid.'"%'])->first();
        if( !empty($area) ) {
            $zone = GnrZone::whereRaw('areaId Like :aid', [':aid'=>'%"'.$area->id.'"%'])->first();
            return $zone;
        }
        return null;
    }

    public static function getDescendantBranchByUserBranchAndPosition( $branch, $position )
    {
        $branchesId = [];

        $isZonalManager         = $position->name == 'Zonal Manager';
        $isAreaManager          = $position->name == 'Area Manager';
        $isItNotSpecialPosition = !$isZonalManager && !$isAreaManager;

        if($isZonalManager) {
            $area = GnrArea::whereRaw('branchId Like :bid', [':bid'=>'%"'.$branch->id.'"%'])->first();
            $zone =  GnrZone::whereRaw('areaId Like :aid', [':aid'=>'%"'.$area->id.'"%'])->first();
            
            $areas = GnrArea::find($zone->areaId);
            
            foreach($areas as $area) {
                foreach($area->branchId as $branchId) {
                    $branchesId[] = $branchId;
                }
            }    
        }

        if($isAreaManager) {
            $area = GnrArea::whereRaw('branchId Like :bid', [':bid'=>'%"'.$branch->id.'"%'])->first();
            foreach($area->branchId as $branchId){
                $branchesId[] = $branchId;
            }
        }

        if($isItNotSpecialPosition) {
            $branchesId[] = $branch->id;
        }

        return $branchesId;
    }

}
