<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\Role
 *
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Role whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Role whereSlug($value)
 * @mixin \Eloquent
 */
class Role extends Model
{
    protected $table = 'roles';
    protected $guarded = [];
    protected $hidden = ['updated_at'];
    protected $appends = ['sections_list','menu_sections_list'];
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function sections(){
        return $this->hasMany(RoleAccess::class,'role_id','id');
    }
    public function getSectionsListAttribute(){
        return $this->hasMany(RoleAccess::class,'role_id','id')->join('menu_sections','menu_sections.id','=','role_sections.section_id')->where('role_sections.operations', 'LIKE', '%view%')->pluck('menu_sections.name')->toarray();
    }

    public function getMenuSectionsListAttribute(){
        return $this->hasMany(RoleAccess::class,'role_id','id')->join('menu_sections','menu_sections.id','=','role_sections.section_id')->where('menu_sections.is_visible', '=', '1')->where('role_sections.operations', 'LIKE', '%view%')->orderby('menu_order','asc')->get();
    }

    public function scopeHasAccess($query,$section_name)
    {
        return $this->hasMany(RoleAccess::class,'role_id','id')->join('menu_sections','menu_sections.id','=','role_sections.section_id')->where('menu_sections.name','=',$section_name)->first()->operations_list;

    }
}
