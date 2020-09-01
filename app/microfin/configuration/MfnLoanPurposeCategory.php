<?php

namespace App\microfin\configuration;

use Illuminate\Database\Eloquent\Model;

class MfnLoanPurposeCategory extends Model {

	public $timestamps = false;

	protected $table = 'mfn_loans_purpose_category';

	protected $fillable = ['id','name','createdDate','updatedDate','status','softDel'
];

public static function attributes(){
	return array(
		'name' => 'Name',
		'status' => 'Status',
		'createdDate'=>'Created At',
		'updatedDate'=>'Updated At'
	);
}

public static function placeholder(){
	return array(
		'name' => 'Name',
		'status' => 'Status',
		'createdDate'=>'Created At',
		'updatedDate'=>'Updated At'

	);
}

public static function createRules(){
	return [
		'name' => 'required'
	];
}

public static function updateRules(){
	return [
		'name' => 'required'
	];
}

public static function options($selectAny=null){
	$result = self::where('status','1')->orderby('name','asc')->get();
	$option=[];
	if($selectAny!=null){
		$option['']=$selectAny;
	}
	foreach($result as $row){
		$option[$row->id]=$row->name;
	}

	return $option;
}

		/*public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}*/

	}