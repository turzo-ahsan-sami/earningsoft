<?php

namespace App\Http\Controllers\pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use App\pos\PosOtherCost;
use Illuminate\Support\Facades\Auth;
use App\gnr\GnrCompany;

class OtherCostController extends Controller
{
    public function otherCostList()
    {   
        $otherCosts = PosOtherCost::with('ledger')->where('companyId', Auth::user()->company_id_fk)->get();
        //dd($otherCosts);
        return view('pos/otherCost/otherCostList', compact('otherCosts'));
    }

    public function addOtherCost()
    {      
        $ledgers = DB::table('acc_account_type')
                        ->join('acc_account_ledger', 'acc_account_ledger.accountTypeId', 'acc_account_type.id')
                        ->select('acc_account_type.name as accountType', 'acc_account_ledger.name as ledger', 'acc_account_ledger.code',
                        'acc_account_ledger.id as ledgerId')
                        ->where('isGroupHead', 0)
                        ->where('companyIdFk', Auth::user()->company_id_fk)
                        ->where('acc_account_type.name', 'Expenses')->get();

        return view('pos/otherCost/addOtherCost', compact('ledgers'));
    }

    public function insertOtherCost(Request $request)
    {      
        $rules = array(
            'other_cost'  => 'required',
            'ledger'      => 'required|not_in:0',
        );

        $attributeNames = array(
            'other_cost'      => 'Other Cost',
            'ledger'          => 'Ledger',
        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails())
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        else
        {   
            $otherCost = new PosOtherCost();
            $otherCost->name = $request->other_cost;
            $otherCost->companyId = Auth::user()->company_id_fk;
            $otherCost->ledger_id = $request->ledger;
            $otherCost->save();

            $data = array(
                'status' => true,
                'msg'    => 'Other Cost Add Successfully'
            );

            return response::json($data);
        }
    }

    public function editOtherCost($id)
    {    
        $otherCost = PosOtherCost::find($id);

        $ledgers = DB::table('acc_account_type')
                        ->join('acc_account_ledger', 'acc_account_ledger.accountTypeId', 'acc_account_type.id')
                        ->select('acc_account_type.name as accountType', 'acc_account_ledger.name as ledger', 'acc_account_ledger.code',
                        'acc_account_ledger.id as ledgerId')
                        ->where('acc_account_ledger.isGroupHead', 0)
                        ->where('acc_account_ledger.companyIdFk', Auth::user()->company_id_fk)
                        ->where('acc_account_type.name', 'Expenses')->get();

        return view('pos/otherCost/editOtherCost', compact('ledgers', 'otherCost'));
    }

    public function updateOtherCost(Request $request)
    {   
        $rules = array(
            'other_cost'  => 'required',
            'ledger'      => 'required|not_in:0'
        );

        $attributeNames = array(
            'other_cost'      => 'Other Cost',
            'ledger'          => 'Ledger'
        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails())
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        else
        {      
            $otherCost = PosOtherCost::find($request->ledgerId);
            $otherCost->name = $request->other_cost;
            $otherCost->companyId = Auth::user()->company_id_fk;
            $otherCost->ledger_id = $request->ledger;
            $otherCost->update();

            $data = array(
                'status' => true,
                'msg'    => 'Other Cost Updated Successfully'
            );

            return response::json($data);
        }
    }

    public function deleteOtherCost(Request $request)
    {   
        $otherCost = PosOtherCost::find($request->id);
        $otherCost->delete();

        $data = array(
            'status' => true,
            'msg'    => 'Other Cost Updated Successfully'
        );

        return response::json($data);
    }


}
