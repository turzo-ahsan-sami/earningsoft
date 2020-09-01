<?php

namespace App\Service;


use DB;
use Auth;
use DateTime;
use DateInterval;
use Carbon\Carbon;

use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

use App\User;


/**
 *
 * Helper Class For DB Partition 
 *
 * @author turzo
 *
 */

class DatabasePartitionHelper
{   

    static public function getPartitionInfo($db, $table)
    {        
        return DB::select(DB::raw(
            "SELECT `PARTITION_NAME` FROM `information_schema`.`PARTITIONS`"
            . " WHERE `TABLE_SCHEMA` = '" . $db
            . "' AND `TABLE_NAME` = '" . $table . "'"
        ));        
    }   
    

    static public function getDBTableName($table_name, $partitionName)
    {
        return DB::raw("$table_name PARTITION ($partitionName)");
    }

    static public function getCompanyWisePartitionName($companyIdFk)
    {   
        if((int) $companyIdFk < 10) return 'p0';       
        if((int) $companyIdFk < 20) return 'p1';       
        if((int) $companyIdFk < 30) return 'p2';       
        if((int) $companyIdFk < 40) return 'p3';       
        if((int) $companyIdFk < 50) return 'p4';       
        if((int) $companyIdFk < 60) return 'p5';       
        if((int) $companyIdFk < 70) return 'p6';       
        if((int) $companyIdFk < 80) return 'p7';       
        if((int) $companyIdFk < 90) return 'p8';       
        else return 'p9';       
    }

    static public function getUserWisePartitionName()
    {   
        $companyIdFk = Auth::user()->company_id_fk;

        return DatabasePartitionHelper::getCompanyWisePartitionName($companyIdFk);   
    }

    static public function getUserPartitionWiseDBTableName($table_name)
    {
        $companyIdFk = Auth::user()->company_id_fk;

        $partitionName = DatabasePartitionHelper::getCompanyWisePartitionName($companyIdFk);
         
        return DB::raw("$table_name PARTITION ($partitionName)");
    }
    
    
    static public function getPartitionWiseDBTableNameForJoin($table_name, $as)
    {
        $companyIdFk = Auth::user()->company_id_fk;

        $partitionName = DatabasePartitionHelper::getCompanyWisePartitionName($companyIdFk);
         
        return DB::raw("$table_name PARTITION ($partitionName) AS $as");
    }


}
?>