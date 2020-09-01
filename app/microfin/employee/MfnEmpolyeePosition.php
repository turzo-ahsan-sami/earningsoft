<?php

	namespace App\microfin\employee;

	use Illuminate\Database\Eloquent\Model;

	class MfnEmpolyeePosition extends Model {
		
		public $timestamps = false;
		
	    protected $table ='hr_settings_position';

	   	protected $fillable =[
								'name',
								'grade_id_fk',
								'level_id_fk',
								'department_id_fk',
								'status',
								'created_at',
								'created_by',
								'updated_by',
								'updated_at'
							];
		public function scopeActive($query) {
			
		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}						
	}