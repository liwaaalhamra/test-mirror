<?php

namespace App\Models\Api;

use App;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Cassandra\Custom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Api\ItemCustomField
 *
 * @mixin \Eloquent
 * @property integer $id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\ItemCustomField whereId($value)
 */

class ItemCustomField extends Model
{
    protected $table = "item_custom_fields";
    protected $guarded = [];
    protected $hidden = ['updated_at','item_id','custom_field_id'];

    public function getCreatedAtAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function item(){
        return $this->belongsTo(Item::class,'item_id');
    }
    public function custom_field(){
        return $this->belongsTo(CustomField::class,'custom_field_id');
    }

}
