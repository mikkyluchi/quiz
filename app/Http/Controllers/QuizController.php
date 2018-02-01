<?php

namespace App\Http\Controllers;
use App\Question;
use App\Option;
use App\Answer;
use App\Http\Controllers\Controller;  
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth; 
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Hashing\BcryptHasher;
use Carbon\Carbon;

class QuizController extends Controller
{
     /**
     * Submit Quiz
     *
     * @return \Illuminate\Http\Response
     */
    public function submit(Request $request)
    {
        //mark the test the user has submit
        //validate the request
        //get the user from token
        $user = JWTAuth::parseToken()->authenticate();

        if($user){

        	//validate the request
        	try {
	            $this->validate($request, array(
	                'time_started' => 'required',
	                'time_ended' => 'required'
	            ));
	        }
	        catch (ValidationException $e) {

	            return new JsonResponse(array(
	                "status"=>"failed",
	                "message"=>$e->getResponse()->original
	            ), Response::HTTP_UNAUTHORIZED);
	            
	        }
	        //save the attempt
	        $duration = $this->getTestDuration($request['time_started'],$request['time_ended']);

	        $attempt = Auth::user()->attempt()->Create([
	        	'time_started'=>$request['time_started'],
	        	'time_ended'=>$request['time_ended'],
		        'duration'=>$duration, 
		        'total_questions'=>sizeof($request['questions'])
	        ]);
	        
	        $score = 0;
	        $total_correct = 0;
	        $total_wrong = 0;
	        //validation successfull
	        if(!empty($request['questions'])){
	        	foreach($request['questions'] as $question){
	        		$answer = Answer::create([
		        		'user_id'=>$user->id,
				        'attempt_id'=>$attempt->id,
				        'question_id'=>$question['id'],
				        'option_id'=>$question['option_id'],
				        'is_correct'=>Option::find($question['option_id'])->is_correct_answer
		        	]);
		        	if($answer->is_correct){
		        		$score++;
		        		$total_correct++;

		        	}else{
		        		$total_wrong++;
		        	}
	        	}
	        	
	        }

	        $attempt->score = $score;
	        $attempt->total_correct = $total_correct;
	        $attempt->total_wrong = $total_wrong;
	 
	        $attempt->save();

	        $attempt->answer;

	        return new JsonResponse(array(
	            'status' =>'success',
	            'message' => 'Quiz finished',
	            'data'=>$attempt
	        ), Response::HTTP_OK);

        }else{
        	return new JsonResponse(array(
	            'status' =>'failed',
	            'message' => 'Unable to validate user'
	        ), Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getTestDuration($time_started,$time_ended){

    	$ts = Carbon::createFromFormat('Y-m-d H:i',$time_started);
		$te = Carbon::createFromFormat('Y-m-d H:i',$time_ended);
		return $ts->diffInMinutes($te); 
    }
}
