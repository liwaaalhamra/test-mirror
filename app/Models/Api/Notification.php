<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class Notification
 */
class Notification extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'notifications';

    /**
     * The allowed fields.
     *
     * @var array
     */
    protected $fillable = ['message', 'type','title','user_id'];
}
