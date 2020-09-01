<?php

namespace App\Http\Controllers\gnr;

use DB;
use Carbon\Carbon;
use Auth;

class Service {
    /**
     * [createLog description]
     * @param  [array] $dataArray    
     * @return [boolean]               
     */
    public static function createLog($dataArray){

        $isRequiredDataExists = isset($dataArray['moduleId'],$dataArray['controllerName'],$dataArray['tableName'],$dataArray['operation']);

        if($isRequiredDataExists==false){
            return false;
        }

        $data = array(
            'moduleId'          => isset($dataArray['moduleId']) ? $dataArray['moduleId'] : 0,
            'controllerName'    => isset($dataArray['controllerName']) ? $dataArray['controllerName'] : '',
            'tableName'         => isset($dataArray['tableName']) ? $dataArray['tableName'] : '',
            'operation'         => isset($dataArray['operation']) ? $dataArray['operation'] : '',
            'primaryIds'        => isset($dataArray['primaryIds']) ? $dataArray['primaryIds'] : '',
            'previousData'      => isset($dataArray['previousData']) ? $dataArray['previousData'] : '',
            'currentData'       => isset($dataArray['currentData']) ? $dataArray['currentData'] : '',
        );

        $tableName = $dataArray['tableName'];

        $queryString = "SHOW KEYS FROM $tableName WHERE Key_name = 'PRIMARY'";

        $primaryKey = DB::select($queryString);
        $primaryKey = collect($primaryKey);
        $primaryKey = $primaryKey[0]->Column_name;

        if ($dataArray['operation']!='insert' && ($data['previousData']==null || $data['previousData']=='')) {
            return false;
        }

        $previousData = json_encode($data['previousData']);
        $currentData = json_encode( DB::table($tableName)->whereIn(DB::raw($primaryKey),$data['primaryIds'])->get() );
        //dd($tableName,$currentData,$primaryKey,$data['primaryIds']);
        $isInserted = DB::table('gnr_log')->insert([
            'userId'            => Auth::user()->id,
            'branchId'          => Auth::user()->branchId,
            'moduleId'          => $data['moduleId'],
            'controllerName'    => $data['controllerName'],
            'time'              => Carbon::now(),
            'ip_address'        => $_SERVER['REMOTE_ADDR'],
            'tableName'         => $data['tableName'],
            'operation'         => $data['operation'],
            'primaryIds'        => implode(',',$data['primaryIds']),
            'previousData'      => $previousData,
            'currentData'       => $currentData
        ]);

        if ($isInserted) {
            return true;
        }
        else{
            return false;
        }
	}

	public static function findClosestValueInArray($array, $value)
	{
		if (count($array) == 0 || !is_numeric($value)) {
			return null;
		}
		$array = array_values($array);

		for ($i = 0; $i < count($array); $i++) {
			if (!is_numeric($array[$i])) {
				return null;
			}
		}

		$closestDiff = abs($array[0] - $value);
		$closestValue = $array[0];

		// dd($array, $closestDiff, $closestValue, $value);

		for ($i = 1; $i < count($array); $i++) {
			if (abs($array[$i] - $value) < $closestDiff) {
				$closestValue = $array[$i];
				$closestDiff = abs($array[$i] - $value);
			}
		}

		return (float) $closestValue;
	}
}