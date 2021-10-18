<?php

namespace App\Models\Api;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\Models\Api\User
 *
 * @property integer $id
 * @property integer $is_admin
 * @property integer $merchant_id
 * @property string $first_name
 * @property integer $is_locked
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string $password_clear_text
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\User whereMerchantId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\User whereIsLocked($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\User whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\User whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\User wherePasswordClearText($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\User whereIsAdmin($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    protected $table = 'cms_users';
//    protected $with = ['countries'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $hidden = [
        'remember_token'
    ];


    public function role(){
        return $this->belongsTo(Role::class,'role_id');
    }

    public function merchant(){
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }

//    public function countries(){
//        return $this
//            ->belongsToMany(Country::class, 'cms_user_countries','user_id', 'country_id');
//    }

    public function isAdmin(){
        return $this->attributes['is_admin'];
    }

    public function isLocked(){
        return $this->attributes['is_locked'];
    }


    public function access_sections(){
        return $this->hasMany(CmsUserAccess::class,'users_id','id');
    }

    public function setPasswordAttribute($value) {
        $this->attributes['password'] = bcrypt($value);
    }
    


}
