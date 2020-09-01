<?php

namespace App\gnr;

use Illuminate\Database\Eloquent\Model;

class FiscalYear extends Model
{
    public $timestamps = false;
    protected $table = 'gnr_fiscal_year';
    protected $fillable = ['name', 'companyId', 'fyStartDate', 'fyEndDate', 'createdDate'];

    public static function options($selectAny = null, $companyId = null)
    {
        if ($companyId == null)
            $result = self::orderby('id', 'desc')->get();
        else
            $result = self::where('companyId', intval($companyId))->orderby('id', 'desc')->get();
        $option = [];
        if ($selectAny != null) {
            $option[''] = $selectAny;
        }
        foreach ($result as $row) {
            $option[$row->id] = $row->name;
        }

        return $option;
    }

    public static function getCurrent()
    {
        $todayDate = date('Y-m-d');
        return FiscalYear::where('fyStartDate', '<=', $todayDate)->where('fyEndDate', '>=', $todayDate)->first();
    }

    public function getPrevious()
    {
        $date = date('Y-m-d', strtotime($this->fyStartDate . '-1 Month'));
        return FiscalYear::where('fyStartDate', '<=', $date)->where('fyEndDate', '>=', $date)->first();
    }

    public static function findByDateRange($startDate, $endDate)
    {
        $searchedFiscalYear = null;

        if ($startDate && $endDate) {

            // Find start range wise
            if($searchedFiscalYear == null){
                $searchedFiscalYear = static::where('fyStartDate', '>', $startDate)->where('fyEndDate', '<=', $endDate)->first();
            }

            // Find end range wise
            if($searchedFiscalYear == null){
                $searchedFiscalYear = static::where('fyStartDate', '>=', $startDate)->where('fyEndDate', '<', $endDate)->first();

            }

            // Find inside range wise
            if($searchedFiscalYear == null){
                $searchedFiscalYear = static::where('fyStartDate', '<=', $startDate)->where('fyEndDate', '>=', $endDate)->first();
            }

            // Find outside range wise
            if($searchedFiscalYear == null) {
                $searchedFiscalYear = static::where('fyStartDate', '>', $startDate)->where('fyEndDate', '<', $endDate)->first();
            }

        }

        return $searchedFiscalYear;
    }
}
