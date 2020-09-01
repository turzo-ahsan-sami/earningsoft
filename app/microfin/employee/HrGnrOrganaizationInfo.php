<?php 

namespace App\microfin\employee;

use Illuminate\Database\Eloquent\Model;

class HrGnrOrganaizationInfo extends Model
{
	public $timestamps = false;
	protected $table ='hr_emp_org_info';
	protected $fillable = ['emp_id_fk','joining_date','company_id_fk','project_id_fk','project_type_id_fk','branch_id_fk','position_id_fk','status','	user_enroll'];
}
