<?php

namespace App\Http\Controllers\accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\accounting\AccLedgerRelation;
use Validator;
use Response;
use App\Http\Controllers\gnr\Service;
use App\Service\Service as ServiceProvider;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Auth;
use DB;
use Carbon\Carbon;

class AccLedgerRelationController extends Controller {

    public function index(){
        $projects = DB::table('gnr_project')
                    ->pluck(DB::raw("CONCAT(projectCode, ' - ', name) AS nameWithCode"), 'id')
                    ->toArray();
        $ledgerRelations = AccLedgerRelation::all()->sortByDesc('id');
        $ledgers = DB::table('acc_account_ledger')->where('isGroupHead', 0)->orderBy('code')->select(DB::raw("CONCAT(code, ' - ', name) AS nameWithCode"), 'id')->get();
        return view('accounting.ledgerRelation.viewLedgerRelation',['ledgerRelations' => $ledgerRelations, 'ledgers' => $ledgers, 'projects' => $projects]);
    }

    public function AddLedgerRelation(){
        $projects = DB::table('gnr_project')
                    ->pluck(DB::raw("CONCAT(projectCode, ' - ', name) AS nameWithCode"), 'id')
                    ->toArray();
        $ledgerRelations = AccLedgerRelation::all()->where('parentId', 0);
        $ledgers = DB::table('acc_account_ledger')->where('isGroupHead', 0)->orderBy('code')->select('id', 'code',  'name')->get();

        return view('accounting.ledgerRelation.addLedgerRelation',['ledgerRelations'=> $ledgerRelations, 'ledgers' => $ledgers, 'projects' => $projects]);
    }

    public static function getLedgersByProject(Request $request) {

        $service = new ServiceProvider;
        $ledgerIdArrByProject = $service->getLedgerHeaderInfo($request->projectId, Auth::user()->branchId, Auth::user()->company_id_fk)->where('isGroupHead', 0)->pluck('id')->toArray();
        $ledgers = DB::table('acc_account_ledger')->whereIn('id', $ledgerIdArrByProject)->orderBy('code')->select('id', 'code',  'name')->get();

        return response()->json($ledgers);
    }

    public function addItem(Request $req) {
        $rules = array(
            'projectId' => 'required',
            'ledger1' => 'required',
            'ledger2' => 'required',
            'relation' => 'required'
        );
        $attributeNames = array(
            'projectId'    => 'Project',
            'ledger1'    => 'First Ledger',
            'ledger2'   => 'Second Ledger',
            'relation'   => 'Relation'
        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails())
        return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        else{
            $now = Carbon::now();
            // $now = date('Y-m-d H:i:s');

            $req->request->add(['createdDate' => $now]);
            $create = AccLedgerRelation::create($req->all());
            $logArray = array(
                'moduleId'  => 4,
                'controllerName'  => 'AccLedgerRelationController',
                'tableName'  => 'acc_ledger_relations',
                'operation'  => 'insert',
                'primaryIds'  => [DB::table('acc_ledger_relations')->max('id')]
            );
            Service::createLog($logArray);
            return response()->json(['responseText' => 'Data successfully inserted!'], 200);
        }
    }

    public function editItem(Request $req) {
        $rules = array(
            'ledger1' => 'required',
            'ledger2' => 'required',
            'relation' => 'required'
        );
        $attributeNames = array(
            'ledger1'    => 'First Ledger',
            'ledger2'   => 'Second Ledger',
            'relation'   => 'Relation'
        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails())
        return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        else{
            $previousdata = AccLedgerRelation::find ($req->id);
            $ledgerRelation = AccLedgerRelation::find ($req->id);
            $ledgerRelation->ledger1 = $req->ledger1;
            $ledgerRelation->ledger2 = $req->ledger2;
            $ledgerRelation->relation = $req->relation;
            $ledgerRelation->save();

            $data = array(
                'ledgerRelation'     => $ledgerRelation,
                'slno'      => $req->slno,
                'responseText' => 'Data updated succesfully!'
            );
            $logArray = array(
                'moduleId'  => 4,
                'controllerName'  => 'AccLedgerRelationController',
                'tableName'  => 'acc_ledger_relations',
                'operation'  => 'update',
                'previousData'  => $previousdata,
                'primaryIds'  => [$previousdata->id]
            );
            Service::createLog($logArray);
            return response()->json($data);
        }
    }


    public function deleteItem(Request $req){
        $previousdata=AccLedgerRelation::find($req->id);
        AccLedgerRelation::find($req->id)->delete();
        $logArray = array(
            'moduleId'  => 4,
            'controllerName'  => 'AccLedgerRelationController',
            'tableName'  => 'acc_ledger_relations',
            'operation'  => 'delete',
            'previousData'  => $previousdata,
            'primaryIds'  => [$previousdata->id]
        );
        Service::createLog($logArray);
        //        $LedgerRelations = AccLedgerRelation::all();
        return response()->json(['responseText' => 'Data deleted successfully!'], 200);     //json($LedgerRelations);
    }

}
