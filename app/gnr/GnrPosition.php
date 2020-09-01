<?php

namespace App\gnr;

use Illuminate\Database\Eloquent\Model;

class GnrPosition extends Model
{
    public $timestamps = false;
    protected $table ='gnr_position';
    protected $fillable = [
						'name',
						'dep_id_fk',
						'status',
						'createdDate'
					];
}
