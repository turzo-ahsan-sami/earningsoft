<?php

	namespace App\gnr;

	use Illuminate\Database\Eloquent\Model;

	class GnrUserRole extends Model {

		public $timestamps = false;
	    protected $table ='gnr_user_role';
	    protected $casts = [
	    					'functionalityId' => 'array'
	    				    ];

	    protected $fillable = [
	    					   'userId', 
	    					   'roleId',
	    					   'functionalityId'
	    					   ];

		
	}
