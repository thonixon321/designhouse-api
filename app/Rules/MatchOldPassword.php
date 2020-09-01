<?php

namespace App\Rules;

use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Validation\Rule;

class MatchOldPassword implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //passes a true or false (see if value passed in matches the password on file for the authenticated user doing the request)
        //Hash is a base class in laravel that decodes hashed passwords in the DB
        return Hash::check($value, auth()->user()->password);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You have provided a wrong current password';
    }
}
