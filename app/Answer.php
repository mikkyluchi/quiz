<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = [
        'user_id',
        'attempt_id',
        'question_id',
        'option_id',
        'is_correct'
    ];

    public function user(){
		return $this->belongsTo(User::class);
	}
	
	public function attempt(){
		return $this->belongsTo(Attempt::class);
	}
}
