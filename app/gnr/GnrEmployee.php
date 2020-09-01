<?php

namespace App\gnr;

use Illuminate\Database\Eloquent\Model;

class GnrEmployee extends Model
{
    public $timestamps = false;
    protected $table ='gnr_employee';
    protected $fillable = ['branchId', 'employeeId','name','fatherName', 'designation','gender','phone','email','department_id_fk','position_id_fk','company_id_fk','dateOfBirth', 'nationalId', 'presentAddress', 'parmanentAddress','image'];

}
