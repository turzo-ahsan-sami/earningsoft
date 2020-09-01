<?php

namespace App\Service;


use DB;
use Auth;
use Carbon\Carbon;

class TransactionCheckHelper 
{
    public function monthEndCheck($id, $type)
    {   
        if($type == 'sales')
        {
            $record = DB::table('pos_sales')->where('id', $id)->first();
            $salesDate = $record->salesDate;
            $date = Carbon::parse($salesDate)->endOfMonth()->toDateString();
        }
            
        if($type == 'sales return')
        {   
            $record = DB::table('pos_sales_return')->where('id', $id)->first();
            $salesReturnDate = $record->returnDate;
            $date = Carbon::parse($salesReturnDate)->endOfMonth()->toDateString();
        }

        if($type == 'purchase')
        {
            $record = DB::table('pos_purchase')->where('id', $id)->first();
            $purchaseDate = $record->purchaseDate;
            $date = Carbon::parse($purchaseDate)->endOfMonth()->toDateString();
        }

        if($type == 'purchase return')
        {
            $record = DB::table('pos_purchase_return')->where('id', $id)->first();
            $purchaseReturnDate = $record->returnDate;
            $date = Carbon::parse($purchaseReturnDate)->endOfMonth()->toDateString();
        }
        if($type == 'order')
        {
            $record = DB::table('pos_order')->where('id', $id)->first();
            $orderDate = $record->orderDate;
            $date = Carbon::parse($orderDate)->endOfMonth()->toDateString();
        }

        $monthEndData = DB::table('acc_month_end')->where('branchIdFk', Auth::user()->branchId)
                            ->whereDate('date', '=', $date)->pluck('date');
        
        if(count($monthEndData) == 0)
            $data['status'] = true;
        else $data['status'] = false;

        return $data;
    }

    public function monthEndYearEndCheck()
    {
        $existMonthEnd = DB::table('acc_month_end')->where('branchIdFk', Auth::user()->branchId)->max('date');

        if($existMonthEnd)
        {
            $existFiscalYear = DB::table('gnr_fiscal_year')->where('companyId', Auth::user()->company_id_fk)
                                                           ->where('fyEndDate', $existMonthEnd)->first();
            if($existFiscalYear)
            {
                $existYearEnd = DB::table('acc_year_end')->where('fiscalYearId', $existFiscalYear->id)->first();
                
                if($existYearEnd)
                {
                    $startFrom = Carbon::parse($existMonthEnd)->add(1, 'day')->startofMonth()->toDateString();
                    $endTo = Carbon::parse($startFrom)->endofMonth()->toDateString();
                    $status = true;
                }
                else
                {
                    $startFrom = null;
                    $endTo = null;
                    $status = false;
                }
            }
            else 
            {   
                
                $startFrom = Carbon::parse($existMonthEnd)->add(1, 'day')->startofMonth()->toDateString();
                $endTo = Carbon::parse($startFrom)->endofMonth()->toDateString();
                $status = true;
            }
        }
        else
        {
            $softwareStartDate = DB::table('gnr_branch')->where('id', Auth::user()->branchId)
                                                        ->select('aisStartDate')->first();
                        
            $startFrom = $softwareStartDate->aisStartDate;
            $endTo = Carbon::parse($startFrom)->endofMonth()->toDateString();
            $status = true;
        }

        $data = array(
            'startDate'  => date('d-m-Y', strtotime($startFrom)),
            'endDate'    => date('d-m-Y', strtotime($endTo)),
            'status'     => $status
        );

        return $data;
    }

    public function requestDateMonthEndCheck($requestDate)
    {   
        $lastDateOfRequest = Carbon::parse($requestDate)->endofMonth()->toDateString();
        
        $existMonthEnd = DB::table('acc_month_end')->where('branchIdFk', Auth::user()->branchId)
                                                  ->where('date', $lastDateOfRequest)  
                                                  ->max('date');

        if($existMonthEnd) $data['status'] = false;
        else $data['status'] = true;

        return $data;
    } 

}


?>