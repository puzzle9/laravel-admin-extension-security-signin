<?php

use Illuminate\Support\Facades\Route;

use Encore\SecuritySignin\Http\Controllers\SecuritySigninController;

Route::post('auth/login', SecuritySigninController::class.'@postLogin');
