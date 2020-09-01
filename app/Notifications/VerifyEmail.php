<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\VerifyEmail as Notification;

class VerifyEmail extends Notification
{
    //override just the method that sends the url in the email notification when a
    //user has just registered - all the other methods are fine and remain unchanged in
    //the Auth\Notifications\VerifyEmail file; 
    //$notifiable comes from the User model - it is a trait there
    protected function verificationUrl($notifiable)
    {
        //the app.client_url can be found in config/app.php which also references the .env file,
        //this basically references the url where the nuxt js app will be - the app.url is a fallback
        //just in case you didn't provide a client url in the config
        $appUrl = config('app.client_url', config('app.url'));
        //signed url generation - first argument is the verify route we created in the api.php routes file, second is a time for how long this verification url will be valid (temporary signed url) so we use the Carbon library that comes with laravel for that time (we'll set it to expire 60 minutes from now), third we pass the user id that will be notified
        $url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['user' => $notifiable->id]
        );
        //the end result here would look something like.... 
        // http://designhouse.test/api/assasasasa - and we want to replace this with the client url 
        //so here we search $url for /api and replace that part with the $appUrl 
        return str_replace(url('/api'), $appUrl, $url);
    }
}
