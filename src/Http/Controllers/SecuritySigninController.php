<?php

namespace Encore\SecuritySignin\Http\Controllers;

use Encore\SecuritySignin\SecuritySignin;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;

use Encore\Admin\Controllers\AuthController;
use Illuminate\Support\Facades\Validator;

class SecuritySigninController extends AuthController
{
    use ThrottlesLogins;

    public function maxAttempts()
    {
        return SecuritySignin::config('throttles.max_attempts');
    }

    public function decayMinutes()
    {
        return SecuritySignin::config('throttles.decay_minutes');
    }

    /**
     * Get a validator for an incoming login request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function loginValidator(array $data)
    {
        $rules = [
            $this->username() => 'required',
            'password'        => 'required',
        ];

        if (SecuritySignin::config('captcha.enable') && cache()->has(SecuritySignin::config('captcha.cache_name'))) {
            $rules['captcha'] = 'required|captcha';
        }

        return Validator::make($data, $rules, [
            'captcha' => '验证码输入错误',
        ], [
            'captcha' => '验证码',
        ]);
    }

    /**
     * Handle a login request.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function postLogin(Request $request)
    {
        if (SecuritySignin::config('form_encryption.enable')) {
            $private_key = SecuritySignin::config('form_encryption.private_key');

            if (!openssl_private_decrypt(base64_decode($request->input('username')), $username, $private_key) || !openssl_private_decrypt(base64_decode($request->input('password')), $password, $private_key)) {
                return back()->withInput()->withErrors([
                    $this->username() => '帐号解密错误',
                ]);
            }

            $request->merge([
                'username' => $username,
                'password' => $password,
            ]);
        }

        if (SecuritySignin::config('throttles.enable') && $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return back()->withInput()->withErrors([
                $this->username() => '错误登录次数超限，请等待 ' . $this->decayMinutes() . ' 分钟后进行重试。',
            ]);
        }

        $this->loginValidator($request->all())->validate();

        $credentials = $request->only([$this->username(), 'password']);
        $remember = $request->get('remember', false);

        if ($this->guard()->attempt($credentials, $remember)) {
            $this->clearLoginAttempts($request);
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);
        return back()->withInput()->withErrors([
            $this->username() => $this->getFailedLoginMessage(),
        ]);
    }
}
