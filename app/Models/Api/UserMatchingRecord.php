<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class UserMatchingRecord extends Model
{
    protected $table = 'user_matching_records';
    protected $guarded = [];
    protected $fillable = ['user_id','matching_records'];
    protected $hidden = ['created_at','updated_at'];
}
