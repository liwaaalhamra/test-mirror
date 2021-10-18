<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\MenuSection
 *
 * @property integer $id
 * @property integer $is_visible
 * @property integer $menu_order
 * @property string $name
 * @property string $label
 * @property string $fa_class
 * @property string $available_operations
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\MenuSection whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\MenuSection whereMenuOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\MenuSection whereIsVisible($value)
 * @mixin \Eloquent
 */
class MenuSection extends Model
{
    protected $table = 'menu_sections';
    protected $guarded = [];
    protected $hidden = ['updated_at'];
    protected $appends = ['available_operations_list'];

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function getAvailableOperationsListAttribute(){
        return explode(',', $this->attributes['available_operations']);
    }
}
