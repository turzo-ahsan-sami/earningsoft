<?php

namespace App\Http\Controllers\microfin\process;

use App\Http\Controllers\Controller;
use DB;    

class DataMigrationController extends Controller {

    public function memberAttendence(){
        

        $branchIds = DB::table('mfn_member_attendence')->where('branchIdFk','>',0)->groupBy('branchIdFk')->orderBy('branchIdFk')->pluck('branchIdFk')->toArray();

        foreach ($branchIds as $branchId) {
            $memberAttendences = DB::table('mfn_member_attendence')->where('branchIdFk',$branchId)->get();
            $dates = $memberAttendences->unique('attendance_Date')->pluck('attendance_Date')->toArray();

            foreach ($dates as $date) {
                $samityIds = $memberAttendences->where('attendance_Date',$date)->unique('samityIdFk')->pluck('samityIdFk')->toArray();

                foreach ($samityIds as $samityId) {
                    $onDateInfo = $memberAttendences->where('attendance_Date',$date)->where('samityIdFk',$samityId);
                    $attendenceData = json_encode(array_map(null,$onDateInfo->pluck('memberIdFk')->toArray(),$onDateInfo->pluck('isPresent')->toArray()));

                    DB::table('mfn_auto_process_info_copy')->insert([
                        'date'                      => $date,
                        'samityIdFk'                => $samityId,
                        'branchIdFk'                => $branchId,
                        'totalCollectionAmount'     => 0,
                        'totalDepositAmount'        => 0,
                        'memberAttendence'          => $attendenceData,
                        'status'                    => 1
                    ]);
                }
            }
        }            
    }


    ////////////// Primary Product


    public function updateSavingsDepositPrimaryProduct(){

        $missingMemberIds = DB::select("SELECT memberIdFk, COUNT(DISTINCT `primaryProductIdFk`) as numOfData FROM `mfn_savings_deposit` WHERE ds=1 AND `memberIdFk` IN (SELECT DISTINCT memberIdFk  FROM `mfn_loan_primary_product_transfer` WHERE ds=1 AND `newPrimaryProductFk`!=`oldPrimaryProductFk`) GROUP BY `memberIdFk` HAVING numOfData=1");
        $missingMemberIds = collect($missingMemberIds);
        $missingMemberIds = $missingMemberIds->pluck('memberIdFk')->toArray();

        $transfers = DB::table('mfn_loan_primary_product_transfer')
                                ->where('ds',1)
                                ->whereIn('memberIdFk',$missingMemberIds)
                                ->where('oldPrimaryProductFk','!=','newPrimaryProductFk')
                                ->get();

        $memberIds = $transfers->unique('memberIdFk')->pluck('memberIdFk')->toArray();

        echo "Total member: ".count($memberIds).'<br>';

        foreach ($memberIds as $key => $memberId) {
            $memProductTransfers = $transfers->where('memberIdFk',$memberId)->sortByDesc('transferDate');

            foreach ($memProductTransfers as $memProductTransfer) {
                DB::table('mfn_savings_deposit')
                        ->where('ds',1)
                        ->where('memberIdFk',$memberId)
                        ->where('depositDate','<',$memProductTransfer->transferDate)
                        ->update([
                            'primaryProductIdFk' => $memProductTransfer->oldPrimaryProductFk
                        ]);
            }
            echo "NO: ".$key." Member ".$memberId.' executed <br>';
        }

        echo 'Done';
    }

    public function updateSavingsWithdrawPrimaryProduct(){

        $missingMemberIds = DB::select("SELECT memberIdFk, COUNT(DISTINCT `primaryProductIdFk`) as numOfData FROM `mfn_savings_withdraw` WHERE ds=1 AND `memberIdFk` IN (SELECT DISTINCT memberIdFk  FROM `mfn_loan_primary_product_transfer` WHERE ds=1 AND `newPrimaryProductFk`!=`oldPrimaryProductFk`) GROUP BY `memberIdFk` HAVING numOfData=1");
        $missingMemberIds = collect($missingMemberIds);
        $missingMemberIds = $missingMemberIds->pluck('memberIdFk')->toArray();

        $transfers = DB::table('mfn_loan_primary_product_transfer')
                                ->where('ds',1)
                                ->whereIn('memberIdFk',$missingMemberIds)
                                ->where('oldPrimaryProductFk','!=','newPrimaryProductFk')
                                ->get();

        $memberIds = $transfers->unique('memberIdFk')->pluck('memberIdFk')->toArray();

        echo "Total member: ".count($memberIds).'<br>';

        foreach ($memberIds as $key => $memberId) {
            $memProductTransfers = $transfers->where('memberIdFk',$memberId)->sortByDesc('transferDate');

            foreach ($memProductTransfers as $memProductTransfer) {
                DB::table('mfn_savings_withdraw')
                        ->where('ds',1)
                        ->where('memberIdFk',$memberId)
                        ->where('withdrawDate','<',$memProductTransfer->transferDate)
                        ->update([
                            'primaryProductIdFk' => $memProductTransfer->oldPrimaryProductFk
                        ]);

                echo "NO: ".$key." Member ".$memberId.' executed <br>';
            }
        }

        echo 'Done';
    }

    public function updateLoanCollectionPrimaryProduct(){

        $transfers = DB::table('mfn_loan_primary_product_transfer')
                                ->where('ds',1)
                                ->where('oldPrimaryProductFk','!=','newPrimaryProductFk')
                                ->get();

        $memberIds = $transfers->unique('memberIdFk')->pluck('memberIdFk')->toArray();

        echo "Total member: ".count($memberIds).'<br>';

        foreach ($memberIds as $key => $memberId) {
            $memProductTransfers = $transfers->where('memberIdFk',$memberId)->sortByDesc('transferDate');

            foreach ($memProductTransfers as $memProductTransfer) {
                DB::table('mfn_loan_collection')
                        ->where('ds',1)
                        ->where('memberIdFk',$memberId)
                        ->where('collectionDate','<',$memProductTransfer->transferDate)
                        ->update([
                            'primaryProductIdFk' => $memProductTransfer->oldPrimaryProductFk
                        ]);

                echo "NO: ".$key." Member ".$memberId.' executed <br>';
            }
        }

        echo 'Done';
    
    }

    ////////////// End Primary Product

}