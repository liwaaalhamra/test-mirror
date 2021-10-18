<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\Api\CmsUserAccess
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $users_id
 * @property string $section
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\CmsUserAccess whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\CmsUserAccess whereUsersId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\CmsUserAccess whereSection($value)
 */
class CmsUserAccess extends Model
{
    protected $table = "cms_users_access";
    protected $fillable = ['users_id','section'];

    public function user(){
        return $this->belongsTo(Users::class,'users_id');
    }

}
