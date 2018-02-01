<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'time_started',
        'time_ended',
        'duration',
        'score',
        'total_questions'
    ];

    public function user(){
		return $this->belongsTo(User::class);
	}
	public function answer()
 
   {
 
       return $this->hasMany(Answer::class);
 
   }
}
