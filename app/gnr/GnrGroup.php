<?php

namespace App\gnr;

use Illuminate\Database\Eloquent\Model;

class GnrGroup extends Model
{
	public $timestamps = false;
    protected $table ='gnr_group';
    protected $fillable = ['name', 'email','phone','address','website', 'createdDate'];

    public function company()
    {
        return $this->hasMany('App\gnr\GnrCompany','groupId','id');
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
