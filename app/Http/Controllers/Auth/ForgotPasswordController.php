<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;
    //trans() knows how to translate the response from laravel into something readable (e.g. email successfully sent)
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return response()->json(['status' => trans($response)], 200);
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return response()->json(['email' => trans($response)], 422);
    }
}
