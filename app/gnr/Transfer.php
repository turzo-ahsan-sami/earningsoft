<?php
namespace App\hr;

use Illuminate\Database\Eloquent\Model;

use App\hr\FunctionList;
use App\hr\ReportingBossHistory;
use App\hr\ReportingBossEmployee;

class Transfer extends Model
{

    protected $table = 'hr_transfer';

    public $promotionStatus = ['Pending'=>'Pending', 'Approved'=>'Approved', 'Confirmed'=>'Confirmed'];

    protected $fillable = [
        'users_id_fk',
        'pre_company_id_fk',
        'pre_project_id_fk',
        'pre_branch_id_fk',
        'cur_project_id_fk',
        'cur_branch_id_fk',
        'cur_emp_id',
        'pre_emp_id',
        'pre_emp_designation',
        'effect_month',
        'effect_date',
        'status',
        'created_at', 
        'updated_at', 
        'created_by', 
        'updated_by',
        'approved_by_fk',
        'approved_time',
        'confirmed_by_fk',
        'confirmed_time',
        'release_date',
        'joining_date'
    ];

    public static function attributes(){
        return array(
              'users_id_fk'=>'Employee',
              'pre_company_id_fk'=>'Company',
              'pre_project_id_fk' => 'Project',
              'pre_branch_id_fk' => 'Branch',
              'cur_project_id_fk' => 'Transfered Project',
              'cur_branch_id_fk' => 'Transfered Branch',
              'cur_emp_id'=>'Transfered Employee ID',
              'pre_emp_id'=>'Employee ID',
              'pre_emp_designation'=>'Employee Designation',
              'effect_month'=>'Effect Month',
              'effect_date'=>'Transfer Date',
              'status'=>'Status',
              'created_at'=>'Created At',
              'updated_at'=>'Updated At',
              'created_by'=>'Created By',
              'updated_by'=>'Updated By',
              'approved_by_fk'=>'Approved By',
              'approved_time'=>'Approved Time',
              'confirmed_by_fk'=>'Confirmed By',
              'confirmed_time'=>'Confirmed Time',
              'release_date'=>'Release Date',
              'joining_date'=>'Joining Date'
            );
    }

    public static function placeholder(){
        return array(
              'users_id_fk'=>'Employee',
              'pre_company_id_fk'=>'Company',
              'pre_project_id_fk' => 'Project',
              'pre_branch_id_fk' => 'Branch',
              'cur_project_id_fk' => 'Transfered Project',
              'cur_branch_id_fk' => 'Transfered Branch',
              'cur_emp_id'=>'Transfered Employee ID',
              'pre_emp_id'=>'Employee ID',
              'pre_emp_designation'=>'Employee Designation',
              'effect_month'=>'Effect Month',
              'effect_date'=>'Effect Date',
              'status'=>'Status',
              'created_at'=>'Created At',
              'updated_at'=>'Updated At',
              'created_by'=>'Created By',
              'updated_by'=>'Updated By',
              'approved_by_fk'=>'Approved By',
              'approved_time'=>'Approved Time',
              'confirmed_by_fk'=>'Confirmed By',
              'confirmed_time'=>'Confirmed Time',
              'release_date'=>'Release Date',
              'joining_date'=>'Joining Date'
            );
    }

    public static function createRules(){
        return [
            'users_id_fk'=>'required',
            'pre_company_id_fk'=>'required',
            'pre_project_id_fk'=>'required',
            'pre_branch_id_fk'=>'required',
            'cur_project_id_fk'=>'required',
            'cur_branch_id_fk'=>'required',
            'effect_month'=>'required',
            'effect_date'=>'required',
            'pre_emp_id'=>'required',
            'pre_emp_designation'=>'required',
            'cur_emp_id'=>'required'
        ];
    }

    public static function updateRules(){
        return [
            'users_id_fk'=>'required',
            'pre_company_id_fk'=>'required',
            'pre_project_id_fk'=>'required',
            'pre_branch_id_fk'=>'required',
            'cur_project_id_fk'=>'required',
            'cur_branch_id_fk'=>'required',
            'effect_month'=>'required',
            'effect_date'=>'required',
            'pre_emp_id'=>'required',
            'pre_emp_designation'=>'required',
            'cur_emp_id'=>'required'
        ];
    }

    //relation with user
    public function user(){
        return $this->belongsTo('App\User','users_id_fk','id');
    }

    //relation with pre_company
    public function preCompany(){
        return $this->belongsTo('App\gnr\GnrCompany','pre_company_id_fk','id');
    }

    //relation with pre_project
    public function preProject(){
        return $this->belongsTo('App\gnr\GnrProject','pre_project_id_fk','id');
    }

    //relation with pre_branch
    public function preBranch(){
        return $this->belongsTo('App\gnr\GnrBranch','pre_branch_id_fk','id');
    }

    //relation with project
    public function project(){
        return $this->belongsTo('App\gnr\GnrProject','cur_project_id_fk','id');
    }

    //relation with branch
    public function branch(){
        return $this->belongsTo('App\gnr\GnrBranch','cur_branch_id_fk','id');
    }

    public function getPreviousBranchJobDuration($data){

      $findCurTransfer = self::where('users_id_fk',$data->users_id_fk)->where('pre_branch_id_fk', $data->pre_branch_id_fk)->orderby('id','desc')->first();

      $findPreTransfer = self::where('users_id_fk',$data->users_id_fk)->where('cur_branch_id_fk', $data->pre_branch_id_fk)->orderby('id','desc')->first();

      if($findPreTransfer){
        $fromDate = @$findPreTransfer->effect_date;
      }else{
        $fromDate = @$findCurTransfer->user->employee->organization->joining_date;
      }

      $toDate = @$findCurTransfer->effect_date;

      return ['from'=>$fromDate,'to'=>$toDate];

    }
}