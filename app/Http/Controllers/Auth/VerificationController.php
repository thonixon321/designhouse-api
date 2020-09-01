<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use App\Providers\RouteServiceProvider;

// use Illuminate\Foundation\Auth\VerifiesEmails;

class VerificationController extends Controller
{
    
    //comment this out to override some methods from it. We do not need the web views. We set things up to just return json for an api.
    // use VerifiesEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //don't need the middlewar since we wrapped it in the api routes file
        // $this->middleware('auth');

        //signed urls that have timestamps that can expire at a time you set
        //this next line is already being done in the verify method below
        // $this->middleware('signed')->only('verify');
        //throttle is how many times the email can be resent (limit it)
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    //override verify method that was in the trait we commented out above
    public function verify(Request $request, User $user)
    {
        // check if the url is a valid signed url
        if (! URL::hasValidSignature($request) ) {
            return response()->json(["errors" => [
                "message" => "Invalid verification link"
            ]], 422);
        }

        //check if user has already verified their account
        if ($user->hasVerifiedEmail()) {
            return response()->json(["errors" => [
                "message" => "Email address already verified"
            ]], 422);
        }

        $user->markEmailAsVerified();
        //let system know the user has verified their email
        event(new Verified($user));

        return response()->json(["message" => "email successfully verified"], 200);
    }

    public function resend(Request $request)
    {
        $this->validate($request, [
            'email' => ['email', 'required']
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (! $user) {
            return response()->json(["errors" => [
                "email" => "No user could be found with this email address"
            ]], 422);
        }

        //check if user has already verified their account
        if ($user->hasVerifiedEmail()) {
            return response()->json(["errors" => [
                "message" => "Email address already verified"
            ]], 422);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['status' => 'verification link resent']);
    }
}
