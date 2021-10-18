<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Api\ForgotPassword
 *
 * @property integer $id
 * @property string $email
 * @property integer $code
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\ForgotPassword whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\ForgotPassword whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\ForgotPassword whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\ForgotPassword whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\ForgotPassword whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ForgotPassword extends Model
{
    protected $table = 'forgot_password_code';
}
