<?php


namespace App\Http\Controllers\gnr;


use App\ConstValue;
use App\gnr\GnrArea;
use App\gnr\GnrRegion;
use App\gnr\GnrResponsibility;
use App\gnr\GnrZone;
use App\hr\EmployeeGeneralInfo;
use App\hr\EmployeeOrganizationInfo;
use App\hr\Position;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GnrResponsibilityController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->query('positionId')){
            return $this->getEmployeeByPositionId($request->query('positionId'))->map(function($item){
                return [
                    'id'=>$item->employee->id,
                    'name'=>$item->employee->emp_id.' - '.$item->employee->emp_name_english,
                ];
            });
        }

        if($request->query('typeCode')){
            return $this->getBoundary($request->query('typeCode'));
        }

        $responsibilities = GnrResponsibility::paginate(100);
        return view('gnr.gnrResponsibilities.index', compact('responsibilities'));
    }

    private function getBoundary($typeCode)
    {
        $boundaries = [];
        if($typeCode == ConstValue::RESPONSIBILITY_TYPE_CODE_REGION){
            $boundaries = GnrRegion::get()->map(function($item){
                return ['id'=>$item->id, 'name'=> $item->name];
            });
        }

        if($typeCode == ConstValue::RESPONSIBILITY_TYPE_CODE_AREA){
            $boundaries = GnrArea::get()->map(function($item){
                return ['id'=>$item->id, 'name'=> $item->name];
            });
        }

        if($typeCode == ConstValue::RESPONSIBILITY_TYPE_CODE_ZONE){
            $boundaries = GnrZone::get()->map(function($item){
                return ['id'=>$item->id, 'name'=> $item->name];
            });
        }

        return $boundaries;
    }

    private function getEmployeeByPositionId($positionId)
    {
        return EmployeeOrganizationInfo::with(['user.actingPosition','employee'])
            ->where('status', 'Active')
            ->where('job_status', 'Present')
            ->get()->filter(function ($item) use ($positionId) {
                $actingActivePosition = $item->user ? $item->user->actingAllActivePosition->first() : null;
                if ($actingActivePosition) {
                    if ($positionId == $actingActivePosition->acting_position_fk) {
                        return true;
                    }
                } else {
                    if ($positionId == $item->position_id_fk) {
                        return true;
                    }
                }
            });

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $mResponsibility = new GnrResponsibility();
        $positions = [''=>'Select One'] + Position::all()->pluck('name', 'id')->toArray();
        $responsibleForList =  [''=>'Select One'] + $mResponsibility->getListTypeCode();

        return view('gnr.gnrResponsibilities.create', compact('positions', 'responsibleForList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'position_id_fk'=>'required',
            'type_code'=>'required',
            'id_list'=>'required',
        ]);

        $responsibleEmployee = new GnrResponsibility();
        $responsibleEmployee->fill($request->all());
        $responsibleEmployee->emp_id_fk = $request->employee_specify == 'Yes' ? $request->emp_id_fk: null;
        $responsibleEmployee->id_list = json_encode($request->id_list);
        if($responsibleEmployee->save()){
            return redirect()->route('gnrResponsibility.index')->with('success', 'Successfully created...');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $responsibility = GnrResponsibility::find($request->query('id'));

        $positions = [''=>'Select One'] + Position::all()->pluck('name', 'id')->toArray();
        $responsibleBoundaries =  [''=>'Select One'] + $responsibility->getListTypeCode();
        $employeeList = $this->getEmployeeByPositionId($responsibility->position_id_fk)->each(function($item){
            $item->name = $item->employee->emp_name_english;
        })->pluck('name', 'emp_id_fk')->toArray();
        $boundaryList = $this->getBoundary($responsibility->type_code)->pluck('name','id')->toArray();

        return view('gnr.gnrResponsibilities.edit',
            compact('positions', 'responsibleBoundaries','responsibility','employeeList','boundaryList'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request,[
            'position_id_fk'=>'required',
            'type_code'=>'required',
            'id_list'=>'required',
        ]);

        $responsibleEmployee = GnrResponsibility::find($request->query('id'));
        $responsibleEmployee->fill($request->all());
        $responsibleEmployee->emp_id_fk = $request->employee_specify == 'Yes' ? $request->emp_id_fk: null;
        $responsibleEmployee->id_list = json_encode($request->id_list);
        if($responsibleEmployee->save()){
            return redirect()->route('gnrResponsibility.index')->with('success', 'Successfully updated...');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $responsibleEmployee = GnrResponsibility::find($request->query('id'));

        if($responsibleEmployee->delete()){
            return redirect()->route('gnrResponsibility.index')->with('success', 'Successfully deleted...');
        }
    }
}