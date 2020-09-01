<?php
namespace App\hr;

use Illuminate\Database\Eloquent\Model;
use App\Service\UserUtility;

use App\User;

use App\hr\FunctionList;
use App\hr\ReportingBossHistory;
use App\hr\ReportingBossEmployee;


class TerminateInfo extends Model
{

    protected $table = 'hr_terminate_info';

    protected $guarded = ['company_id_fk','project_id_fk','branch_id_fk'];

    protected $fillable = [
        'users_id_fk',
        'position',
        'recruitment_type',
        'terminate_date',
        'note',
        'reason',
        'approved_id_fk',
        'effect_date',
        'cancel_date',
        'cancel_reason',
        'status',
        'created_at', 
        'updated_at', 
        'created_by', 
        'updated_by',
    ];

    public static function attributes(){
        return array(
              'users_id_fk'=>'Employee',
              'position'=>'Position',
              'recruitment_type'=>'Recruitment Type',
              'terminate_date'=>'Terminate Date',
              'note'=>'Note',
              'reason'=>'Reason',
              'approved_id_fk'=>'Approved By',
              'effect_date'=>'Effect Date',
              'cancel_date'=>'Cancel Date',
              'cancel_reason'=>'Cancel Reason',
              'status'=>'Status',
              'company_id_fk'=>'Company',
              'project_id_fk'=>'Project',
              'branch_id_fk'=>'Branch',
            );
    }

    public static function placeholder(){
        return array(
              'users_id_fk'=>'Employee',
              'position'=>'Position',
              'recruitment_type'=>'Recruitment Type',
              'terminate_date'=>'Terminate Date',
              'note'=>'Note',
              'reason'=>'Reason',
              'approved_id_fk'=>'Approved By',
              'effect_date'=>'Effect Date',
              'cancel_date'=>'Cancel Date',
              'cancel_reason'=>'Cancel Reason',
              'status'=>'Status',
              'company_id_fk'=>'Company',
              'project_id_fk'=>'Project',
              'branch_id_fk'=>'Branch'
            );
    }

    public static function createRules(){
        return [
            'users_id_fk' => 'required',
            'position'=>'required',
            'recruitment_type'=>'required',
            'terminate_date' => 'required',
            'note' => 'required',
            'reason' => 'required'
        ];
    }

    public static function updateRules(){
        return [
            'users_id_fk' => 'required',
            'position'=>'required',
            'recruitment_type'=>'required',
            'terminate_date' => 'required',
            'expected_effect_date' => 'required',
            'note' => 'required',
            'reason' => 'required'
        ];
    }

    public static function cancelRules(){
        return [
            'cancel_date' => 'required',
            'cancel_reason' => 'required'
        ];
    }

    public function user(){
        return $this->belongsTo('App\User','users_id_fk','id');
    }

    public function approvedBy(){
        return $this->belongsTo('App\User','approved_id_fk','id');
    }

    public function reasonList(){
        return $this->belongsTo('App\hr\ReasonList','reason','id');
    }

}