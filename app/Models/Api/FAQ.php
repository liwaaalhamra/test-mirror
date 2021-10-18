<?php

namespace App\Models\Api;

use App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\FAQ
 *
 * @property integer $id
 * @property string $question
 * @property string $answer
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\FAQ whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\FAQ whereQuestion($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\FAQ whereAnswer($value)
 * @mixin \Eloquent
 */
class FAQ extends Model
{
    protected $table = 'faq';
    protected $guarded = [];
    protected $hidden = ['updated_at','created_at','question_en','question_ar','answer_en','answer_ar','item_order'];
    protected $appends = ['question','answer'];

    public function getQuestionAttribute(){
        $locale = App::getLocale();
        return $this->attributes['question_'.$locale];
    }

    public function getAnswerAttribute(){
        $locale = App::getLocale();
        return $this->attributes['answer_'.$locale];
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

}
