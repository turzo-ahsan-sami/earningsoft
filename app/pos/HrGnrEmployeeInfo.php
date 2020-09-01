<?php 

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class HrGnrEmployeeInfo extends Model
{
    public $timestamps = false;
    protected $table ='hr_emp_general_info';
    protected $fillable = ['emp_id','emp_name_english','father_name_english','mother_name_english','sex','religion','date_of_birth','blood_group','mobile_number','nid_no','email','birth_certificate_no','present_address','permanent_address','pre_div_id','pre_dis_id','pre_upa_id','pre_uni_id','per_div_id','per_dis_id','per_upa_id','per_uni_id'];
}
