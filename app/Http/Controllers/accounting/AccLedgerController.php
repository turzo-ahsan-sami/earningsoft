<?php

namespace App\Http\Controllers\accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\accounting\AddLedger;
use App\accounting\AddAccountType;
use App\gnr\GnrCompany;
use App\gnr\GnrProject;
use App\gnr\GnrBranch;
use Carbon\Carbon;
use Validator;
use Response;
//use App\Http\Controllers\gnr\Service;
use DB;
use Auth;
use App\Service\Service;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;


class AccLedgerController extends Controller
{

    public function index()
    {
        // $ledgers = AddLedger::where('parentId',0)->orderBy('ordering', 'asc')->get();
        $ledgers = DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('parentId', 0)->orderBy('ordering', 'asc')->get();
        // dd($ledgers);
      
        // $branches = GnrBranch::all();
        // $accountTypes = AddAccountType::all()->where('parentId',0);
        // $projects=GnrProject::all();

        return view('accounting.ledger.viewLedger', ['ledgers' => $ledgers]);
        // return view('accounting.ledger.viewLedger',['ledgers' => $ledgers, 'branches'=> $branches,'accountTypes'=> $accountTypes, 'projects'=> $projects, 'projects'=> $projects]);
    }

    public function indexTr()
    {
        $ledgers = DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('parentId', 0)->orderBy('ordering', 'asc')->get();
        // dd($ledgers);

        return view('accounting.ledger.viewTransactionLedger', ['ledgers' => $ledgers]);
    }


    public function addLedger($encryptedParentId)
    {

        $encryptedParentId = $encryptedParentId;
        $ledgers = AddLedger::where('companyIdFk', Auth::user()->company_id_fk)->get();
        //        $branches = GnrBranch::all();
        $accountTypes = AddAccountType::all();
        // $maxId = AddAccountType::max('id')+1;
        $projects = GnrProject::where('companyId', Auth::user()->company_id_fk)->get();
        $caLevel = GnrCompany::find(Auth::user()->company_id_fk)->ca_level;
        // dd($caLevel);
        return view('accounting.ledger.addLedger', ['ledgers' => $ledgers,
            'caLevel'=> $caLevel,
            'accountTypes' => $accountTypes,
            //'maxId'=> $maxId,
            'projects' => $projects,
            'encryptedParentId' => $encryptedParentId
        ]);
    }

    public function addTransactionLedger()
    {
        $caLevel = GnrCompany::find(Auth::user()->company_id_fk)->ca_level;
        // $lastParents = AddLedger::where('isGroupHead', 0)->orderBy('code')->distinct('parentId')->pluck('parentId')->toArray();
        $parents = AddLedger::where('companyIdFk', Auth::user()->company_id_fk)->where('level', ($caLevel - 1))->orderBy('code')->select('id', 'name', 'code', 'accountTypeId','parentId')->get();

        foreach ($parents as $key => $item) {
            $order = [];
            $parent = AddLedger::find($item->id);
            do {
                $order[$parent->level] = $parent->name . '[' . $parent->code . ']';
                $parent = AddLedger::find($parent->parentId);
            } 
            while ($parent->parentId != 0);

            $order[$parent->level] = $parent->name . '[' . $parent->code . ']';
            $chain = '';

            for ($i=1; $i <= count($order); $i++) {  
                $chain .= $order[$i];
                if($i != count($order)) {
                    $chain .= ' > ';
                }
            }
            $orderedParent[$item->id] = $chain;
            
        }
        // dd($orderedParent);

        $accountTypes = AddAccountType::all();
        $projects = GnrProject::where('companyId', Auth::user()->company_id_fk)->get();
        $caLevel = GnrCompany::find(Auth::user()->company_id_fk)->ca_level;
        // dd($caLevel);
        return view('accounting.ledger.addTransactionLedger', [
            'parents'=> $orderedParent,
            'caLevel'=> $caLevel,
            'accountTypes' => $accountTypes,
            'projects' => $projects,
        ]);
    }

    public function checkUniqueLedgerCode(Request $request) {

        // $parentLvl = $request->parentId == 0
        // ? 0
        // : DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('id', $request->parentId)->first()->level;
        //             // dd($parentLvl);
        // $lvl = $parentLvl + 1;
        $availableCodes = DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)/*->where('level', $lvl)*/->pluck('code')->toArray();
        $data = in_array($request->code, $availableCodes) ? true : false;
        return response()->json($data);
    }

    public function addItem(Request $request)
    {
        // dd($request->all());

        $rules = array(
            'name' => 'required',
            'code' => 'required|max:10',
            //'clientCode' => 'required',
            //'parentId' => 'required',
            'accountTypeId' => 'required',
            'ordering' => 'required'
        );

        $attributeNames = array(
            'name' => 'Ledger Name',
            'code' => 'Code',
            //             'clientCode'   => 'Client Code',
            //             'parentId'   => 'Parent',
            'accountTypeId' => 'Account Type',
            'ordering' => 'Order',
            'isParent' => 'Parent'
        );

        $validator = Validator::make(Input::all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $now = Carbon::now();
        //     $req->request->add(['createdDate' => $now]);
        //     $create = AddLedger::create($req->all());

        $data = [];

        if ($validator->fails())
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        else {

            $parentId = $request->parentId;
            $ordering = $request->ordering;
            // dd($parentId);
            if ($parentId == 0) {
                $level = 1;
            }
            else {
                $parentLevel = DB::table('acc_account_ledger')->where('id', $parentId)->where('companyIdFk', Auth::user()->company_id_fk)->first()->level;
                $level = $parentLevel + 1;
            }
            // dd($level);
            $parentLevel = DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('id', $parentId);
            $sisters = DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('parentId', $parentId)->orderBy('ordering', 'asc')->select('id', 'name', 'ordering')->get();
            foreach ($sisters as $sister) {
                if (($ordering + 1) == $sister->ordering) {
                    // $sists="match";
                    $sists = DB::table('acc_account_ledger')->where('parentId', $parentId)->where('ordering', '>', $ordering)->where('companyIdFk', Auth::user()->companyIdFk)->orderBy('ordering', 'asc')->select('id', 'name', 'ordering')->get();
                    foreach ($sists as $key => $sist) {
                        $x = ($sist->ordering) + 1;

                        DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('id', $sist->id)->update(['ordering' => $x]);
                        // $val=$ordering+($key+2);
                    }
                }
                // echo $value++."->ID:".$sister->id."->Name:".$sister->name."; ";

            }

            $data[] = array(
                'name' => $request->name,
                'code' => $request->code,
                'companyIdFk' => Auth::user()->company_id_fk,
                'description' => $request->description,
                'accountTypeId' => $request->accountTypeId,
                'ordering' => ($request->ordering) + 1,
                'parentId' => $request->parentId,
                'level' => $level,
                'isGroupHead' => $request->isGroupHead,
                'createdDate' => $now,
                'projectBranchId' => $request->stringifyDataArray
                //                'projectBranchId' => (string) json_decode($request->stringifyDataArray)
                //                'projectBranchId' => $request->dataArray
            );
            // dd($data);
            DB::table('acc_account_ledger')->insert($data);
            $logArray = array(
                'moduleId'  => 4,
                'controllerName'  => 'AccLedgerController',
                'tableName'  => 'acc_account_ledger',
                'operation'  => 'insert',
                'primaryIds'  => [DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->max('id')]
            );
            \App\Http\Controllers\gnr\Service::createLog($logArray);

            // $addLedger = new AddLedger;
            // $addLedger->name = $request->name;
            // $addLedger->code = $request->code;
            // $addLedger->description = $request->description;
            // $addLedger->accountTypeId = $request->accountTypeId;
            // $addLedger->ordering = $request->ordering;
            // $addLedger->parentId = $request->parentId;
            // $addLedger->isGroupHead = $request->isGroupHead;
            // $addLedger->projectBranchId = $request->dataArray;
            // $addLedger->createdDate = $now;
            // $addLedger->save();

            //            $create = AddLedger::create($req->all());
            return response()->json(['responseText' => 'Data successfully inserted!'], 200);
        }
    }

    public function addTransactionLedgerItem(Request $request)
    {
        // dd($request->all());

        $rules = array(
            'name' => 'required',
            'code' => 'required|max:10',
            //'clientCode' => 'required',
            'parentId' => 'required',
            // 'accountTypeId' => 'required',
            // 'ordering' => 'required'
        );

        $attributeNames = array(
            'name' => 'Ledger Name',
            'code' => 'Code',
            //             'clientCode'   => 'Client Code',
                        'parentId'   => 'Parent',
            // 'accountTypeId' => 'Account Type',
            // 'ordering' => 'Order',
            // 'isParent' => 'Parent'
        );

        $validator = Validator::make(Input::all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $now = Carbon::now();
        //     $req->request->add(['createdDate' => $now]);
        //     $create = AddLedger::create($req->all());

        $data = [];

        if ($validator->fails())
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        else {

            $parentId = $request->parentId;
            $ordering = $request->ordering;
            $parent = DB::table('acc_account_ledger')->where('id', $parentId)->first();
            $level = $parent->level + 1;
            $sistersMaxOrder = DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('parentId', $parentId)->max('ordering');
            if($sistersMaxOrder) {
                $ordering = $sistersMaxOrder + 1;
            }
            else {
                $ordering = 1;
            }

            $data[] = array(
                'name' => $request->name,
                'code' => $request->code,
                'companyIdFk' => Auth::user()->company_id_fk,
                'description' => $request->description,
                'accountTypeId' => $parent->accountTypeId,
                'ordering' => $ordering,
                'parentId' => $parent->id,
                'level' => $level,
                'isGroupHead' => 0,
                'createdDate' => $now,
                'projectBranchId' => $request->stringifyDataArray
                //                'projectBranchId' => (string) json_decode($request->stringifyDataArray)
                //                'projectBranchId' => $request->dataArray
            );
            // dd($data);
            DB::table('acc_account_ledger')->insert($data);
            $logArray = array(
                'moduleId'  => 4,
                'controllerName'  => 'AccLedgerController',
                'tableName'  => 'acc_account_ledger',
                'operation'  => 'insert',
                'primaryIds'  => [DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->max('id')]
            );
            \App\Http\Controllers\gnr\Service::createLog($logArray);

            // $addLedger = new AddLedger;
            // $addLedger->name = $request->name;
            // $addLedger->code = $request->code;
            // $addLedger->description = $request->description;
            // $addLedger->accountTypeId = $request->accountTypeId;
            // $addLedger->ordering = $request->ordering;
            // $addLedger->parentId = $request->parentId;
            // $addLedger->isGroupHead = $request->isGroupHead;
            // $addLedger->projectBranchId = $request->dataArray;
            // $addLedger->createdDate = $now;
            // $addLedger->save();

            //            $create = AddLedger::create($req->all());
            return response()->json(['responseText' => 'Data successfully inserted!'], 200);
        }
    }

    public function deleteItem(Request $request)
    {

        $deleteId = $request->id;
        $previousdata=AddLedger::find($deleteId);
        $parentCount = DB::table('acc_account_ledger')->where('parentId', $deleteId)->count('id');
        $openingBalanceCount = DB::table('acc_opening_balance')->where('ledgerId', $deleteId)->count('id');
        $voucherCount = DB::table('acc_voucher_details')->where('debitAcc', $deleteId)->orWhere('creditAcc', $deleteId)->count('id');

        if ($parentCount>0 || $openingBalanceCount>0 || $voucherCount>0) {
            return response()->json("Ledger Had Transactions.Could Not Delete..");
        }else{

            $deletedRow = DB::table('acc_account_ledger')->where('id', $deleteId)->select('parentId', 'ordering')->first();
            $ordering = $deletedRow->ordering;

            $sisters = DB::table('acc_account_ledger')->where('parentId', $deletedRow->parentId)->orderBy('ordering', 'asc')->select('id', 'ordering')->get();
            foreach ($sisters as $sister) {
                if ($ordering == $sister->ordering) {
                    // $sists="match";
                    $sists = DB::table('acc_account_ledger')->where('parentId', $deletedRow->parentId)->where('ordering', '>=', $ordering)->orderBy('ordering', 'asc')->select('id', 'ordering')->get();
                    foreach ($sists as $key => $sist) {
                        $temp = ($sist->ordering) - 1;

                        DB::table('acc_account_ledger')->where('id', $sist->id)->update(['ordering' => $temp]);
                    }
                }
            }
            AddLedger::find($request->id)->delete();
            $logArray = array(
              'moduleId'  => 4,
              'controllerName'  => 'AccLedgerController',
              'tableName'  => 'acc_account_ledger',
              'operation'  => 'delete',
              'previousData'  => $previousdata,
              'primaryIds'  => [$previousdata->id]
          );
            \App\Http\Controllers\gnr\Service::createLog($logArray);
            // return response()->json($voucherCount);
            // return response()->json($voucherCount);
            return response()->json("Delete Sucessfully");
        }

        // return response()->json();     //json($ledgers);
    }

    public function editLedger($encryptedId)
    {
        $previousUrl = url()->previous();
        $arr = explode('/', $previousUrl);
        $preRouteName = $arr[count($arr) - 1];

        $decryptedId = decrypt($encryptedId);


        // $ledgers=AddLedger::all();
        $ledger = AddLedger::where('id', $decryptedId)->first();
        // $ledger=DB::table('acc_account_ledger')->where('id', $decryptedId)->first();

        //        $branches = GnrBranch::all();
        $accountTypes = AddAccountType::all();
        $maxId = AddAccountType::max('id') + 1;
        $projects = GnrProject::where('companyId', Auth::user()->company_id_fk);

        return view('accounting.ledger.editLedger', [
            'ledger' => $ledger,
            'preRouteName'=> $preRouteName,
            //            'branches'=> $branches,
            'accountTypes' => $accountTypes,
            // 'maxId'=> $maxId,
            'projects' => $projects,
            'encryptedId' => $encryptedId,
            'decryptedId' => $decryptedId
        ]);
    }

    public function updateItem(Request $request)
    {


        $rules = array(
            'name' => 'required',
            'code' => 'required|max:10',
            'accountTypeId' => 'required',
            'ordering' => 'required',
        );

        $attributeNames = array(
            'name' => 'Ledger Name',
            'code' => 'Code',
            'accountTypeId' => 'Account Type',
            'ordering' => 'Order',
            'isParent' => 'Parent'
        );

        $validator = Validator::make(Input::all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $ledger =  DB::table('acc_account_ledger')->where('id','=',$request->id)->first();
        $isDuplicateExits = (int) DB::table('acc_account_ledger')->where('companyIdFk', Auth::user()->company_id_fk)->where('id','!=',$ledger->id)->where('level',$ledger->level)->where('code',$request->code)->value('id');
        // dd($isDuplicateExits);
        if ($isDuplicateExits>0) {
            return response::json(array(
                "errors" => [
                    "code" => [
                        0 => "Code already exists."
                    ]
                ]
            ));
        }


        if ($validator->fails())
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        else {

            // $parentId=$request->parentId;
            // $newOrdering=($request->ordering)+1;

            // $previousOrdering=DB::table('acc_account_ledger')->where('id', $request->id)->value('ordering');

            // if($previousOrdering==$newOrdering){
            //   // $a="match";
            // }elseif ($previousOrdering>$newOrdering) {
            //     $sisters=DB::table('acc_account_ledger')->where('parentId', $parentId)->whereBetween('ordering', [$newOrdering, $previousOrdering])->orderBy('ordering', 'asc')->select('id','ordering')->get();
            //     foreach($sisters as $sister){
            //       $temp=($sister->ordering)+1;
            //       DB::table('acc_account_ledger')->where('id', $sister->id)->update(['ordering' => $temp]);
            //     }

            // }elseif ($previousOrdering<$newOrdering) {
            //     $sisters=DB::table('acc_account_ledger')->where('parentId', $parentId)->whereBetween('ordering', [$previousOrdering, $newOrdering])->orderBy('ordering', 'asc')->select('id','ordering')->get();
            //     foreach($sisters as $sister){
            //       $temp=($sister->ordering)-1;
            //       DB::table('acc_account_ledger')->where('id', $sister->id)->update(['ordering' => $temp]);
            //     }
            // }
           $previousdata = AddLedger::find ($request->id);

           $oldId = DB::table('acc_account_ledger')->where('id', $request->id)->select('parentId', 'ordering')->first();
           $newParentId = $request->parentId;
            // $newOrdering=$request->ordering;
           $newOrdering = ($request->ordering) + 1;

           if ($oldId->parentId == $newParentId) {
            $a = "match";
            $previousOrdering = $oldId->ordering;
                // $previousOrdering=DB::table('acc_account_ledger')->where('id', $request->id)->value('ordering');
            if ($previousOrdering == $newOrdering) {
                    // $a="match";
            } elseif ($previousOrdering > $newOrdering) {
                $sisters = DB::table('acc_account_ledger')->where('parentId', $newParentId)->whereBetween('ordering', [$newOrdering, $previousOrdering])->orderBy('ordering', 'asc')->select('id', 'ordering')->get();
                foreach ($sisters as $sister) {
                    $temp = ($sister->ordering) + 1;
                    DB::table('acc_account_ledger')->where('id', $sister->id)->update(['ordering' => $temp]);
                }

            } elseif ($previousOrdering < $newOrdering) {
                $sisters = DB::table('acc_account_ledger')->where('parentId', $newParentId)->whereBetween('ordering', [$previousOrdering, $newOrdering])->orderBy('ordering', 'asc')->select('id', 'ordering')->get();
                foreach ($sisters as $sister) {
                    $temp = ($sister->ordering) - 1;
                    DB::table('acc_account_ledger')->where('id', $sister->id)->update(['ordering' => $temp]);
                }
            }

        } else {
                // $a="Not match";

            $childrenOfOldParent = DB::table('acc_account_ledger')->where('parentId', $oldId->parentId)->where('ordering', '>', $oldId->ordering)->orderBy('ordering', 'asc')->select('id', 'ordering')->get();
            foreach ($childrenOfOldParent as $childOfOldParent) {
                DB::table('acc_account_ledger')->where('id', $childOfOldParent->id)->update(['ordering' => ($childOfOldParent->ordering) - 1]);
            }

            $childrenOfNewParent = DB::table('acc_account_ledger')->where('parentId', $newParentId)->where('ordering', '>=', $newOrdering)->orderBy('ordering', 'asc')->select('id', 'ordering')->get();
            foreach ($childrenOfNewParent as $childOfNewParent) {
                DB::table('acc_account_ledger')->where('id', $childOfNewParent->id)->update(['ordering' => ($childOfNewParent->ordering) + 1]);
            }

        }


        DB::table('acc_account_ledger')
        ->where('id', $request->id)
        ->update(['name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'accountTypeId' => $request->accountTypeId,
                    // 'ordering' => $request->ordering,
            'ordering' => ($request->ordering) + 1,
            'parentId' => $request->parentId,
            'isGroupHead' => $request->isGroupHead,
            'projectBranchId' => $request->stringifyDataArray
        ]);

        $logArray = array(
            'moduleId'  => 4,
            'controllerName'  => 'AccLedgerController',
            'tableName'  => 'acc_account_ledger',
            'operation'  => 'update',
            'previousData'  => $previousdata,
            'primaryIds'  => [$previousdata->id]
        );
        \App\Http\Controllers\gnr\Service::createLog($logArray);

            // return response()->json($a);
            // return response()->json(['oldId'=>$oldId, 'newParentId'=>$newParentId, 'newOrdering'=>$newOrdering,
            // 'a'=>$a
            // 'childrenOfOldParent'=>$childrenOfOldParent, 'childrenOfNewParent'=>$childrenOfNewParent
            // ]);
        return response()->json(['responseText' => 'Data successfully inserted!'], 200);

    }
}


    // public function updateItem(Request $request){

    //   $rules = array(
    //     'name' => 'required',
    //     'code' => 'required',
    //     'accountTypeId' => 'required',
    //     'ordering' => 'required',
    //   );

    //   $attributeNames = array(
    //     'name'    => 'Ledger Name',
    //     'code'   => 'Code',
    //     'accountTypeId'   => 'Account Type',
    //     'ordering'   => 'Order',
    //     'isParent'   => 'Parent'
    //   );

    //   $validator = Validator::make ( Input::all (), $rules);
    //   $validator->setAttributeNames($attributeNames);


    //   if ($validator->fails())
    //     return response::json(array('errors' => $validator->getMessageBag()->toArray()));
    //   else{

    //     $parentId=$request->parentId;
    //     $newOrdering=($request->ordering)+1;

    //     $previousOrdering=DB::table('acc_account_ledger')->where('id', $request->id)->value('ordering');

    //     if($previousOrdering==$newOrdering){
    //       // $a="match";
    //     }elseif ($previousOrdering>$newOrdering) {
    //         $sisters=DB::table('acc_account_ledger')->where('parentId', $parentId)->whereBetween('ordering', [$newOrdering, $previousOrdering])->orderBy('ordering', 'asc')->select('id','ordering')->get();
    //         foreach($sisters as $sister){
    //           $temp=($sister->ordering)+1;
    //           DB::table('acc_account_ledger')->where('id', $sister->id)->update(['ordering' => $temp]);
    //         }

    //     }elseif ($previousOrdering<$newOrdering) {
    //         $sisters=DB::table('acc_account_ledger')->where('parentId', $parentId)->whereBetween('ordering', [$previousOrdering, $newOrdering])->orderBy('ordering', 'asc')->select('id','ordering')->get();
    //         foreach($sisters as $sister){
    //           $temp=($sister->ordering)-1;
    //           DB::table('acc_account_ledger')->where('id', $sister->id)->update(['ordering' => $temp]);
    //         }
    //     }


    //     DB::table('acc_account_ledger')
    //       ->where('id', $request->id)
    //       ->update(['name' => $request->name,
    //         'code' => $request->code,
    //         'description' => $request->description,
    //         'accountTypeId' => $request->accountTypeId,
    //         'ordering' => ($request->ordering)+1,
    //         'parentId' => $request->parentId,
    //         'isGroupHead' => $request->isGroupHead,
    //         'projectBranchId' => $request->stringifyDataArray
    //       ]);
    //     // return response()->json($a);
    //     // return response()->json($sisters);
    //     return response()->json(['responseText' => 'Data successfully inserted!'], 200);
    //   }
    // }


public function viewLedgerByProjectName(Request $request)
{

    $searchedProjectId = $request->projectName;
        // $ledgers = AddLedger::all()->where('parentId',0);
    $ledgers = AddLedger::where('parentId', 0)->where('companyIdFk',Auth::user()->company_id_fk)->orderBy('ordering', 'asc')->get();
        // $ledgers = DB::table('acc_account_ledger')->where('parentId',0)->orderBy('ordering', 'asc')->get();

    return view('accounting.ledger.viewLedgerByProjectSearch', ['ledgers' => $ledgers, 'searchedProjectId' => $searchedProjectId]);
}

public function viewLedgerByBranchName(Request $request)
{

    $searchedBranchId = $request->branchName;
    $ledgers = AddLedger::where('parentId', 0)->where('companyIdFk',Auth::user()->company_id_fk)->orderBy('ordering', 'asc')->get();
    //dd($ledgers);
        // $ledgers = AddLedger::all()->where('parentId',0);

    return view('accounting.ledger.viewLedgerByBranchSearch', ['ledgers' => $ledgers, 'searchedBranchId' => $searchedBranchId]);
}


public function viewLedgerByLedger(Request $request)
{

    $ledgerId = $request->ledgerId;
        // $ledgers = AddLedger::all()->where('id',$ledgerId);
    $ledgers = AddLedger::where('id', $ledgerId)->where('companyIdFk',Auth::user()->company_id_fk)->orderBy('ordering', 'asc')->get();
    //dd($ledgers);

    $branches = GnrBranch::where('companyId',Auth::user()->company_id_fk)->get();
    //dd($branches);
    $accountTypes = AddAccountType::all()->where('parentId', 0);
    $projects = GnrProject::where('companyId',Auth::user()->company_id_fk)->get();

    return view('accounting.ledger.viewLedgerByLedgerSearch', ['ledgers' => $ledgers, 'ledgerId' => $ledgerId, 'branches' => $branches, 'accountTypes' => $accountTypes, 'projects' => $projects]);
}

public function getBranchByProject(Request $request)
{

    if ($request->projectId == "") {
        $branchList = DB::table('gnr_branch')->pluck('id', 'name');
    } else {
            // $branchList =  DB::table('gnr_branch')->where('projectId',(int)json_decode($request->projectId))->whereNotIn('id', [1])->pluck('id','name');
        $branchList = DB::table('gnr_branch')->where('projectId', (int)json_decode($request->projectId))->pluck('id', 'name');
    }

    return response()->json($branchList);
        // return response()->json($request->projectId);
}

public function getLedgerLevelsByProject(Request $request)
{

    $projectId = $request->projectId;
    $branchIdArr = DB::table('gnr_branch')->where('projectId', (int)json_decode($request->projectId))->pluck('id')->toArray();
    $user_company_id = Auth::user()->company_id_fk;
    $service = new Service;
    $ledgers = $service->getLedgerHeaderInfos($projectId, $branchIdArr, $user_company_id);
    $maxLevel = $ledgers->max('level');
    $minLevel = $ledgers->min('level');
    $levels = ['All'];

    for ($i=$minLevel; $i < $maxLevel ; $i++) {
        $levels[] = 'Level-'. $i;
    }

    return response()->json($levels);
}


public function branchChange(Request $request)
{
    $projectId = DB::table('gnr_branch')->where('projectId', $request->projectId)->whereNotIn('id', [1])->pluck('name', 'id');
    return response()->json($projectId);
}

public function testLedger1()
{

    return view('accounting.ledger.test1');

}

public function filteringOrderByParent(Request $request)
{

        // if($request->parentId==""){
        //   $orderingList =  DB::table('acc_account_ledger')->where('parentId', $request->parentId)->pluck('id','name');
        // }
        // else{
        // $branchList =  DB::table('gnr_branch')->where('projectId',(int)json_decode($request->projectId))->whereNotIn('id', [1])->pluck('id','name');
    $orderingList = DB::table('acc_account_ledger')->where('parentId', (int)json_decode($request->parentId))->orderBy('ordering', 'asc')->pluck('ordering', 'name');
        // }

    return response()->json($orderingList);
        // return response()->json($request->parentId);
}

public function getBranchByProjectTest(Request $request)
{
    $branches = DB::table('gnr_branch')->where('projectId', $request->projectId)->whereNotIn('id', [1])->pluck('name', 'id');
    return response()->json($branches);
}

public function testLedger(Request $request)
{

    $searchedLedgerId = $request->ledgerId;
    $searchedProjectId = $request->projectId;
    $searchedBranchId = $request->branchId;

    $userBranchId=Auth::user()->branchId;

    $ledgers = AddLedger::where('parentId', 0)->orderBy('ordering', 'asc')->get();

    return view('accounting.ledger.viewLedgerTest', ['ledgers' => $ledgers, 'searchedLedgerId' => $searchedLedgerId, 'searchedProjectId' => $searchedProjectId, 'searchedBranchId' => $searchedBranchId, 'userBranchId' => $userBranchId]);

}


}
