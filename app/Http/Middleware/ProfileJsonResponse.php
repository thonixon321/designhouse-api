<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class ProfileJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //we want to be able to pass a string parameter to our route requests (like /users?_debug=true) and have that return debug information from our laravel debugbar package we installed

        //first capture the response that we will send back to the user
        $response = $next($request);

        //check if debug bar is enabled
        if (! app()->bound('debugbar') || ! app('debugbar')->isEnabled()) {
            return $response;
        }

        //profile the json response - must be a json response and contain the debug parameter
        if ($response instanceof JsonResponse && $request->has('_debug')) {
            //grab the debugbar info and append it to whatever the response should be - setData() comes with laravel, getData(true) transforms the data to an array - this will give us info about the queries most importantly, like how many queries are running, which ones were executed, etc.
            //get all debug info
            // $response->setData(array_merge($response->getData(true), [
            //     '_debugbar' => app('debugbar')->getData(true)
            // ]));
            //get only debug query info - Arr::only comes with laravel and can filter arrays
            $response->setData(array_merge([
                '_debugbar' => Arr::only(app('debugbar')->getData(true), 'queries')
            ], $response->getData(true)));

        }

        //if it isn't debugging, just return response as normal
        return $response;
    }
}
