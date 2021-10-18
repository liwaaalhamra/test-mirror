<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\Api\RoleAccess
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $role_id
 * @property integer $section_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\RoleAccess whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\RoleAccess whereRoleId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\RoleAccess whereSectionId($value)
 */
class RoleAccess extends Model
{
    protected $table = "role_sections";
    protected $guarded = [];
    protected $appends = ['operations_list'];

    public function role(){
        return $this->belongsTo(Role::class,'role_id');
    }
    public function section(){
        return $this->belongsTo(MenuSection::class,'section_id');
    }

    public function getOperationsListAttribute(){
        return explode(',', $this->attributes['operations']);
    }
}
