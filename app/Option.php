<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $table = 'options';
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'is_correct_answer',
        "is_correct_answer",
                "question_id",
                "created_at",
                "updated_at"
    ];
	public function question(){
		return $this->belongsTo(Question::class);
	}
}
