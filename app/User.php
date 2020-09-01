<?php
namespace App;

use DateTime;
use Rinvex\Subscriptions\Traits\HasSubscriptions;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Hash;

/**
 * Class User
 *
 * @package App
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
*/
class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    use HasSubscriptions;

    protected $fillable = ['name', 'email', 'mobile', 'password', 'customer_id', 'user_type', 'branchId', 'company_id_fk', 'emp_id_fk', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'dateTime',
        'mobile_verified_at' => 'datetime'
    ];


    /**
     * Hash password
     * @param $input
     */
    public function setPasswordAttribute($input)
    {
        if ($input)
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
    }

    public function employee()
    {
        return $this->hasOne('App\gnr\GnrEmployee', 'id', 'emp_id_fk');
    }

    public function company()
    {
        return $this->hasOne('App\gnr\GnrCompany', 'id', 'company_id_fk');
    }

    public function branch()
    {
        return $this->hasOne('App\gnr\GnrBranch', 'id', 'branchId');
    }
    
}
