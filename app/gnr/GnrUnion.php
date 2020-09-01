<?php

namespace App\gnr;

use Illuminate\Database\Eloquent\Model;

class GnrUnion extends Model
{
    public $timestamps = false;
    protected $table ='gnr_union';
    protected $fillable = ['name', 'divisionId','districtId','upzillaId', 'branchId', 'createdDate'];

    public static function attributes(){
        return array(
              'divisionId'=>'Division',
              'districtId' => 'District',
              'upzillaId'=>'Upazila',
              'branchId'=>'Branch',
              'name'=>'Name',
              'createdDate'=>'Created At'
            );
    }

    public static function placeholder(){
        return array(
              'divisionId'=>'Division',
              'districtId' => 'District',
              'upzillaId'=>'Upazila',
              'branchId'=>'Branch',
              'name'=>'Name',
              'createdDate'=>'Created At'
            );
    }

    public static function createRules(){
        return [
			'divisionId'=>'required',
			'districtId' => 'required',
			'upzillaId'=>'required',
			'branchId'=>'required',
			'name'=>'required',
			'createdDate'=>'required'
        ];
    }

    public static function updateRules(){
        return [
			'divisionId'=>'required',
			'districtId' => 'required',
			'upzillaId'=>'required',
			'branchId'=>'required',
			'name'=>'required',
			'createdDate'=>'required'
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
}
