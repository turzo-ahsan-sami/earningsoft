<?php

namespace App\gnr;

use Illuminate\Database\Eloquent\Model;

class GnrCompany extends Model
{
    public $timestamps = false;
    protected $table ='gnr_company';
    protected $fillable = ['name', 'groupId','email','phone','address','website','image', 'createdDate', 'customer_id', 'business_type', 'fy_type', 'stock_type', 'ca_level'];

    // public function group()
    // {
    //     return $this->belongsTo('App\gnr\GnrGropu','id','groupId');
    // }

    public function customer()
    {
        return $this->belongsTo('App\Admin\Customer', 'id', 'customer_id');
    }

    public function project()
    {
        return $this->hasMany('App\gnr\GnrProject','companyId','id');
    }

    public static function options($selectAny=null){
        $result = self::where('status','1')->get();
        $option=[];
        if($selectAny!=null){
          $option['']=$selectAny;
        }
        foreach($result as $row){
            $option[$row->id]=$row->name;
        }
        
        return $option;
    }

}
