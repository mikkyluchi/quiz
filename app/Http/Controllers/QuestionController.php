<?php

namespace App\Http\Controllers;
use App\Question;
use App\Option;
use App\Http\Controllers\Controller;  
use Illuminate\Http\Request;

class QuestionController extends Controller
{
     /**
     * Get questions
     *
     * @return \Illuminate\Http\Response
     */
    public function getQuestions()
    {
        $Questions  = Question::all();
        if(!empty($Questions)){
        	foreach($Questions as $k=>$Question){
        		$Questions[$k]->options = $Question->options;
        	}
        }
        return response()->json($Questions);
    }
}
