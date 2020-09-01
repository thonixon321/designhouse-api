<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;
    //return a boolean that shows if login was successful or not and also set token
    public function attemptLogin(Request $request)
    {
        //attempt to issue a token to the user based on the login credentials (guard is the api guard)
        //the request would hold the email and password and if valid, it will return a token
        $token = $this->guard()->attempt($this->credentials($request));

        //check if token did not get set (invalid credentials)
        if (! $token) {
            return false;
        }

        //get the authenticated user - whom the token was issued
        $user = $this->guard()->user();
        //check if user is an instance of the mustVerifyEmail - this is a contract seen in the User model so it should be an instance, and make sure they have verified their email
        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return false;
        }

        //set user token if all previous checks have passed (method comes from api guard with JWT package installed)
        $this->guard()->setToken($token);

        return true;
    }
    //another override of the base class (AuthenticatesUsers) because we don't redirect to a web route
    protected function sendLoginResponse(Request $request)
    {
        //clear out checks that log how many times a user attempted to log in so the count can start fresh
        $this->clearLoginAttempts($request); 

        //get token from the api auth guard (JWT) - cast to a string for Nuxt ui
        $token = (string)$this->guard()->getToken();

        //extract the expiry date from the token so that on the frontend we can set cookies (set the token in a cookie and give it the expiry date, so it can keep sending requests to the server using that token until the expiry date)
        $expiration = $this->guard()->getPayload()->get('exp');

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiration
        ]);
    }

    protected function sendFailedLoginResponse()
    {
        $user = $this->guard()->user();

        if($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return response()->json(["errors" => [
                "verification" => "you need to verify your email account"
            ]]);
        }
        //by default the username() is email, so this returns an email exception
        throw ValidationException::withMessages([
            $this->username() => "Authentication failed - invalid credentials"
        ]);
    }

    //just call the logout method of the auth guard
    public function logout(){
        $this->guard()->logout();

        return response()->json([
            'message' => 'logged out successfully'
        ]);
    }
}
