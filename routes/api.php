<?php
//Public routes
//route to see if current user is logged in or not
Route::get('me', 'User\MeController@getMe');
//get designs
Route::get('designs', 'Designs\DesignController@index');
Route::get('designs/{id}', 'Designs\DesignController@findDesign');
//get users
Route::get('users', 'User\UserController@index');

// Route group for authenticated users only (already logged in) (auth:api was set up with the JWT in the config folder)
Route::group(['middleware' => ['auth:api']], function() {
    Route::post('logout', 'Auth\LoginController@logout');

    Route::put('settings/profile', 'User\SettingsController@updateProfile');
    Route::put('settings/password', 'User\SettingsController@updatePassword');

    //Upload Designs
    Route::post('designs', 'Designs\UploadController@upload');
    Route::put('designs/{id}', 'Designs\DesignController@update');
    Route::delete('designs/{id}', 'Designs\DesignController@destroy');
});
//Route group for guests only (not logged in yet)
Route::group(['middleware' => ['guest:api']], function() {
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('verification/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');
    Route::post('verification/resend', 'Auth\VerificationController@resend');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

});