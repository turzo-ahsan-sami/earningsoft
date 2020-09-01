<?php

	namespace App\gnr;

	use Illuminate\Database\Eloquent\Model;

	class GnrRole extends Model {

		public $timestamps = false;
	    protected $table ='gnr_role';
	    /*protected $casts = [
	    					'functionalityId' => 'array'
	    				    ];*/

	    protected $fillable = [
	    					   'name', 
	    					   'functionalityId',
	    					   'description'
	    					   ];

		
	}
