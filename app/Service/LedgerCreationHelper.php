<?php

namespace App\Service;

use App\User;
use App\accounting\AddLedger;

use DB;
use Auth;
use Carbon\Carbon;
use App\gnr\GnrCompany;

use Illuminate\Database\Eloquent\Collection;
use phpDocumentor\Reflection\Types\Null_;

/**
 *
 * Helper Class For Ledger Creation
 *
 * @author sabri
 *
 */

class LedgerCreationHelper
{

    public static function generateLedgerTree($companyId)
    {

        $company = GnrCompany::find($companyId);
        $inventory = 0;
        $manufacture = 0;

        if ($company->stock_type == 1) {
            $inventory = 1;
            if ($company->business_type == 'manufacture') {
                $manufacture = 1;
            }
        }

        // first level entry
        $firstLevelLedgers = array(
            [
                'name' => 'Assets',
                'code' => '10000',
                'accountTypeId' => 1,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 0,
                'parentId' => 0,
                'isGroupHead' => 1,
                'level' => 1,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Liabilities',
                'code' => '20000',
                'accountTypeId' => 6,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 0,
                'parentId' => 0,
                'isGroupHead' => 1,
                'level' => 1,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Capital Fund',
                'code' => '30000',
                'accountTypeId' => 9,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 0,
                'parentId' => 0,
                'isGroupHead' => 1,
                'level' => 1,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Income',
                'code' => '40000',
                'accountTypeId' => 12,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 0,
                'parentId' => 0,
                'isGroupHead' => 1,
                'level' => 1,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Expenditure',
                'code' => '50000',
                'accountTypeId' => 13,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 0,
                'parentId' => 0,
                'isGroupHead' => 1,
                'level' => 1,
                'createdDate' => Carbon::now(),
            ]
        );

        AddLedger::insert($firstLevelLedgers);

        // second level entry
        $secondLevelLedgers = array(
            [
                'name' => 'Non-Current Assets',
                'code' => '11000',
                'accountTypeId' => 3,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '10000')->value('id'),
                'isGroupHead' => 1,
                'level' => 2,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Current Assets',
                'code' => '12000',
                'accountTypeId' => 2,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 2,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '10000')->value('id'),
                'isGroupHead' => 1,
                'level' => 2,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Non-Current Liabilities',
                'code' => '21000',
                'accountTypeId' => 8,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '20000')->value('id'),
                'isGroupHead' => 1,
                'level' => 2,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Current Liabilities',
                'code' => '22000',
                'accountTypeId' => 7,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 2,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '20000')->value('id'),
                'isGroupHead' => 1,
                'level' => 2,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Retained Earning',
                'code' => '31000',
                'accountTypeId' => 10,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '30000')->value('id'),
                'isGroupHead' => 1,
                'level' => 2,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Others Income',
                'code' => '41000',
                'accountTypeId' => 12,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '40000')->value('id'),
                'isGroupHead' => 1,
                'level' => 2,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Financial Expenses',
                'code' => '51000',
                'accountTypeId' => 13,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '50000')->value('id'),
                'isGroupHead' => 1,
                'level' => 2,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Cost of Goods Sold',
                'code' => '53000',
                'accountTypeId' => 13,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 2,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '50000')->value('id'),
                'isGroupHead' => 1,
                'level' => 2,
                'createdDate' => Carbon::now(),
            ],
        );

        AddLedger::insert($secondLevelLedgers);

        // third level entry
        $thirdLevelLedgers = array(
            [
                'name' => 'Accounts Receiveable',
                'code' => '13000',
                'accountTypeId' => 3,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '11000')->value('id'),
                'isGroupHead' => 1,
                'level' => 3,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Cash & Bank Balance',
                'code' => '14000',
                'accountTypeId' => 2,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '12000')->value('id'),
                'isGroupHead' => 1,
                'level' => 3,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Other Current Assets',
                'code' => '15000',
                'accountTypeId' => 2,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 2,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '12000')->value('id'),
                'isGroupHead' => 1,
                'level' => 3,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Accounts Payable',
                'code' => '23000',
                'accountTypeId' => 8,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '21000')->value('id'),
                'isGroupHead' => 1,
                'level' => 3,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Retained Earning',
                'code' => '32000',
                'accountTypeId' => 10,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '31000')->value('id'),
                'isGroupHead' => 1,
                'level' => 3,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Others Income',
                'code' => '42000',
                'accountTypeId' => 12,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '41000')->value('id'),
                'isGroupHead' => 1,
                'level' => 3,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Financial Expenses',
                'code' => '52000',
                'accountTypeId' => 13,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '51000')->value('id'),
                'isGroupHead' => 1,
                'level' => 3,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Cost of Goods Sold',
                'code' => '54000',
                'accountTypeId' => 13,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '53000')->value('id'),
                'isGroupHead' => 1,
                'level' => 3,
                'createdDate' => Carbon::now(),
            ],
        );

        AddLedger::insert($thirdLevelLedgers);

        // fourth level entry
        $fourthLevelLedgers = array(
            [
                'name' => 'Customers',
                'code' => '13100',
                'accountTypeId' => 3,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '13000')->value('id'),
                'isGroupHead' => 1,
                'level' => 4,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Cash in Hand',
                'code' => '14100',
                'accountTypeId' => 4,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '14000')->value('id'),
                'isGroupHead' => 1,
                'level' => 4,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Cash at Bank',
                'code' => '14200',
                'accountTypeId' => 5,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 2,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '14000')->value('id'),
                'isGroupHead' => 1,
                'level' => 4,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Inventory',
                'code' => '15100',
                'accountTypeId' => 2,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '15000')->value('id'),
                'isGroupHead' => 1,
                'level' => 4,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Suppliers',
                'code' => '23100',
                'accountTypeId' => 8,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '23000')->value('id'),
                'isGroupHead' => 1,
                'level' => 4,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Retained Earning',
                'code' => '32100',
                'accountTypeId' => 10,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '32000')->value('id'),
                'isGroupHead' => 1,
                'level' => 4,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Others Income',
                'code' => '42100',
                'accountTypeId' => 12,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '42000')->value('id'),
                'isGroupHead' => 1,
                'level' => 4,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Financial Expenses',
                'code' => '52100',
                'accountTypeId' => 13,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '52000')->value('id'),
                'isGroupHead' => 1,
                'level' => 4,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'General Expenses',
                'code' => '52200',
                'accountTypeId' => 13,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 2,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '52000')->value('id'),
                'isGroupHead' => 1,
                'level' => 4,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Cost of Goods Sold',
                'code' => '54100',
                'accountTypeId' => 13,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '54000')->value('id'),
                'isGroupHead' => 1,
                'level' => 4,
                'createdDate' => Carbon::now(),
            ],
        );

        AddLedger::insert($fourthLevelLedgers);

        // final level entry
        $finalLevelLedgers = array(
            [
                'name' => 'Cash in Hand',
                'code' => '14101',
                'accountTypeId' => 4,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '14100')->value('id'),
                'isGroupHead' => 0,
                'level' => 5,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Retained Earning',
                'code' => '32101',
                'accountTypeId' => 10,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '32100')->value('id'),
                'isGroupHead' => 0,
                'level' => 5,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Sales of Fixed Assets',
                'code' => '42101',
                'accountTypeId' => 12,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '42100')->value('id'),
                'isGroupHead' => 0,
                'level' => 5,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Sales Return of Fixed Assets',
                'code' => '42102',
                'accountTypeId' => 12,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 2,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '42100')->value('id'),
                'isGroupHead' => 0,
                'level' => 5,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Purchase',
                'code' => '52101',
                'accountTypeId' => 13,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '52100')->value('id'),
                'isGroupHead' => 0,
                'level' => 5,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Purchase Return',
                'code' => '52102',
                'accountTypeId' => 13,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 2,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '52100')->value('id'),
                'isGroupHead' => 0,
                'level' => 5,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'TAX & VAT Expense for Organization',
                'code' => '52201',
                'accountTypeId' => 13,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '52200')->value('id'),
                'isGroupHead' => 0,
                'level' => 5,
                'createdDate' => Carbon::now(),
            ],
            [
                'name' => 'Cost of Goods Sold',
                'code' => '54101',
                'accountTypeId' => 13,
                'companyIdFk' => $companyId,
                'projectBranchId' => '["0:0"]',
                'ordering' => 1,
                'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '54100')->value('id'),
                'isGroupHead' => 0,
                'level' => 5,
                'createdDate' => Carbon::now(),
            ],
        );

        $inventoryLedger = [
            'name' => 'Inventory',
            'code' => '15101',
            'accountTypeId' => 2,
            'companyIdFk' => $companyId,
            'projectBranchId' => '["0:0"]',
            'ordering' => 1,
            'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '15100')->value('id'),
            'isGroupHead' => 0,
            'level' => 5,
            'createdDate' => Carbon::now(),
        ];
        $finishedGoodsLedger = [
            'name' => 'Finished Goods',
            'code' => '15101',
            'accountTypeId' => 2,
            'companyIdFk' => $companyId,
            'projectBranchId' => '["0:0"]',
            'ordering' => 1,
            'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '15100')->value('id'),
            'isGroupHead' => 0,
            'level' => 5,
            'createdDate' => Carbon::now(),
        ];
        $rawMaterialLedger = [
            'name' => 'Raw Materials',
            'code' => '15102',
            'accountTypeId' => 2,
            'companyIdFk' => $companyId,
            'projectBranchId' => '["0:0"]',
            'ordering' => 2,
            'parentId' => AddLedger::where('companyIdFk', $companyId)->where('code', '15100')->value('id'),
            'isGroupHead' => 0,
            'level' => 5,
            'createdDate' => Carbon::now(),
        ];

        if ($inventory == 1) {
            if ($manufacture == 0) {
                array_push($finalLevelLedgers, $inventoryLedger);
            }
            elseif ($manufacture == 1) {
                array_push($finalLevelLedgers, $finishedGoodsLedger, $rawMaterialLedger);
            }
            
        }

        AddLedger::insert($finalLevelLedgers);
        self::billingVoucherConfig($companyId);
    }

    public static function regenerateLedgerTree($companyId)
    {
        AddLedger::where('companyIdFk', $companyId)->delete();
        self::generateLedgerTree($companyId);
        self::billingVoucherConfig($companyId);
    }

    public static function billingVoucherConfig($companyId)
    {

        $companyLedgers = AddLedger::where('companyIdFk', $companyId)->get();
        $company = GnrCompany::find($companyId);
        $inventory = 0;
        $manufacture = 0;

        $settingsArr = [
            'company_id' => $companyId,
            'customer' => $companyLedgers->where('code', 13100)->first()->id,
            'supplier' => $companyLedgers->where('code', 23100)->first()->id,
            'purchase' => $companyLedgers->where('code', 52101)->first()->id,
            'purchase_return' => $companyLedgers->where('code', 52102)->first()->id,
            'sales' => $companyLedgers->where('code', 42101)->first()->id,
            'sales_return' => $companyLedgers->where('code', 42102)->first()->id,
            'vat' => $companyLedgers->where('code', 52201)->first()->id,
            'cost_of_good_sold' => $companyLedgers->where('code', 54101)->first()->id,
            'createdDate' => Carbon::now(),
        ];

        if ($company->stock_type == 1) {
            if ($company->business_type == 'manufacture') {
                $settingsArr['inventory'] = Null;
                $settingsArr['finished_goods'] = $companyLedgers->where('code', 15101)->first()->id;
                $settingsArr['raw_materials'] =  $companyLedgers->where('code', 15102)->first()->id;
            }
            else {
                $settingsArr['inventory'] = $companyLedgers->where('code', 15101)->first()->id;
                $settingsArr['finished_goods'] = Null;
                $settingsArr['raw_materials'] = Null;
            }
        }
        elseif ($company->stock_type == 0) {
            $settingsArr['inventory'] = Null;
            $settingsArr['finished_goods'] = Null;
            $settingsArr['raw_materials'] = Null;
        }
        // dd($settingsArr);

        $setting = DB::table('pos_voucher_setting')->where('company_id', $companyId)->first();

        if ($setting) {
            DB::table('pos_voucher_setting')->where('company_id', $companyId)->update($settingsArr);
        }
        else {
            DB::table('pos_voucher_setting')->insert($settingsArr);
        }

    }

}
?>