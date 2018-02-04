<?php

namespace App\Http\Controllers\Auth;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Hashing\BcryptHasher;

class AuthController extends Controller
{
     /**
     * Handle a login request to the application through facebook.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function postFacebook(Request $request)
    {
        try {
            $this->validate($request, array(
                'email' => 'required|email',
                'name' => 'required',
                'facebook_id' => 'required'
            ));
        }
        catch (ValidationException $e) {

            return new JsonResponse(array(
                "status"=>"failed",
                "message"=>$e->getResponse()->original
            ), Response::HTTP_OK);
            
        }
        
        //check if the facebook_id
        $user = User::whereFacebookId($request['facebook_id'])->first();
        if($user){

            

        }else{

            $user = User::whereEmail($request['email'])->first();

            if($user){

            }else{
                //user not found
                //register the user
                $hashedpassword = app('hash')->make("password");
                $user = User::create(array(
                    'name' => $request['name'],
                    'email' => $request['email'],
                    'password' => $hashedpassword,
                    'facebook_id' => $request['facebook_id'],
                    'profile_photo' => $request['photo'],
                ));
                if($user){

                    
                    try {
                        $request['password'] = 'password';
                        // Attempt to verify the credentials and create a token for the user
                        if (!$token = JWTAuth::attempt($this->getCredentials($request))) {
                            return $this->onUnauthorized();
                        }
                    }
                    catch (JWTException $e) {
                        // Something went wrong whilst attempting to encode the token
                        return $this->onJwtGenerationError();
                    }
                    //$user = Users::where('email', $request['email'])->first();
                    $user = User::first();
                    // All good so return the token
                    return $this->onAuthorized($token,$user);

                }else{
                    return new JsonResponse(array(
                        "status"=>"failed",
                        "message"=>"Unable to create user"
                    ), Response::HTTP_OK);
                } 
            }
            

        }

    }
    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        try {
            $this->validate($request, array(
                'email' => 'required|email|max:255',
                'password' => 'required'
            ));
        }
        catch (ValidationException $e) {

            return new JsonResponse(array(
                "status"=>"failed",
                "message"=>$e->getResponse()->original
            ), Response::HTTP_OK);
            
        }
        
        try {
            // Attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($this->getCredentials($request))) {
                return $this->onUnauthorized();
            }
        }
        catch (JWTException $e) {
            // Something went wrong whilst attempting to encode the token
            return $this->onJwtGenerationError();
        }
        //$user = Users::where('email', $request['email'])->first();
        $user = User::first();
        // All good so return the token
        return $this->onAuthorized($token,$user);
    }
    public function postSignup(Request $request)
    {
        try {
            $this->validate($request, array(
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required',
                'name' => 'required'
            ));
        }
        catch (ValidationException $e) {

            return new JsonResponse(array(
                "status"=>"failed",
                "message"=>$e->getResponse()->original
            ), Response::HTTP_OK);

        }
        //$hashedpassword = (new BcryptHasher)->make($request['password']);
        $hashedpassword = app('hash')->make($request['password']);
        $user = User::create(array(
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => $hashedpassword
        ));
        if($user){

                    try {
                        
                        // Attempt to verify the credentials and create a token for the user
                        if (!$token = JWTAuth::attempt($this->getCredentials($request))) {
                            return $this->onUnauthorized();
                        }
                    }
                    catch (JWTException $e) {
                        // Something went wrong whilst attempting to encode the token
                        return $this->onJwtGenerationError();
                    }
                    //$user = Users::where('email', $request['email'])->first();
                    $user = User::first();
                    // All good so return the token
                    return $this->onAuthorized($token,$user);

        }else{
            return new JsonResponse(array(
                "status"=>"failed",
                "message"=>"Unable to create user"
            ), Response::HTTP_OK);
        } 
    }
    /**
     * What response should be returned on invalid credentials.
     *
     * @return JsonResponse
     */
    protected function onUnauthorized()
    {
        return new JsonResponse(array(
            'status' =>'failed',
            'message' => 'Unable to validate user'
        ), Response::HTTP_OK);
    }
    
    /**
     * What response should be returned on error while generate JWT.
     *
     * @return JsonResponse
     */
    protected function onJwtGenerationError()
    {
        return new JsonResponse(array(
            'status'=>'failed',
            'message' => 'Unable to authorize user'
        ), Response::HTTP_OK);
    }
    
    /**
     * What response should be returned on authorized.
     *
     * @return JsonResponse
     */
    protected function onAuthorized($token,$user)
    {
        return new JsonResponse(array(
            'status' => 'success',
            'message' => 'User authenticated',
            'token' => $token,
            "type"=>"bearer",
            "expiry"=> 60 * 60 * 2,
            'data' => $user
        ));
    }
    
    /**
     * Get the needed authorization credentials from the request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    protected function getCredentials(Request $request)
    {
        return $request->only('email', 'password');
    }
    
    /**
     * Invalidate a token.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteInvalidate()
    {
        $token = JWTAuth::parseToken();
        
        $token->invalidate();
        
        return new JsonResponse(array(
            'message' => 'token_invalidated'
        ));
    }
    
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\Response
     */
    public function patchRefresh()
    {
        $token = JWTAuth::parseToken();
        
        $newToken = $token->refresh();
        
        return new JsonResponse(array(
            'message' => 'token_refreshed',
            'data' => array(
                'token' => $newToken
            )
        ));
    }
    
    /**
     * Get authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUser()
    {
        return new JsonResponse(array(
            'message' => 'authenticated_user',
            'data' => JWTAuth::parseToken()->authenticate()
        ));
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }
}
