<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{config('admin.title')}} | {{ trans('admin.login') }}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    @if(!is_null($favicon = Admin::favicon()))
        <link rel="shortcut icon" href="{{$favicon}}">
    @endif

    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/bootstrap/css/bootstrap.min.css") }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/font-awesome/css/font-awesome.min.css") }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/dist/css/AdminLTE.min.css") }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/plugins/iCheck/square/blue.css") }}">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="hold-transition login-page"
      @if(config('admin.login_background_image'))style="background: url({{config('admin.login_background_image')}}) no-repeat;background-size: cover;"@endif>
<div class="login-box">
    <div class="login-logo">
        <a href="{{ admin_url('/') }}"><b>{{config('admin.name')}}</b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">{{ trans('admin.login') }}</p>

        <form action="{{ admin_url('auth/login') }}" method="post">
            <div class="form-group has-feedback {!! !$errors->has('username') ?: 'has-error' !!}">

                @if($errors->has('username'))
                    @foreach($errors->get('username') as $message)
                        <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}
                        </label><br>
                    @endforeach
                @endif

                <input type="text" class="form-control" placeholder="{{ trans('admin.username') }}" name="username" value="{{ old('username') }}">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback {!! !$errors->has('password') ?: 'has-error' !!}">

                @if($errors->has('password'))
                    @foreach($errors->get('password') as $message)
                        <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}
                        </label><br>
                    @endforeach
                @endif

                <input type="password" class="form-control" placeholder="{{ trans('admin.password') }}" name="password">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            @php
                use Encore\SecuritySignin\SecuritySignin;
                $captcha_enable = SecuritySignin::config('captcha.enable');
                $captcha_first_login_failure_display = SecuritySignin::config('captcha.first_login_failure_display');
                $captcha_cache_name = SecuritySignin::config('captcha.cache_name');
                $throttles_decay_minutes = SecuritySignin::config('throttles.decay_minutes');

                if ($errors->count()) {
                    cache()->put($captcha_cache_name, time(), now()->addMinutes($throttles_decay_minutes));
                }

                $captcha_show = $captcha_enable && (!$captcha_first_login_failure_display || cache()->has($captcha_cache_name));
            @endphp
            @if($captcha_show)
                <div class="form-group has-feedback {!! !$errors->has('captcha') ?: 'has-error' !!}">

                    @if($errors->has('captcha'))
                        @foreach($errors->get('captcha') as $message)
                            <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}
                            </label><br>
                        @endforeach
                    @endif

                    <div class="row">
                        <div class="col-xs-7" style="padding-right: 0px">
                            <input type="text" class="form-control" placeholder="验证码" name="captcha" maxlength="{{config('captcha.laravel-admin.length')}}">
                            <span class="glyphicon glyphicon-circle-arrow-right form-control-feedback"></span>
                        </div>
                        <div class="col-xs-5">
                            <img style="width: 100%" src="{{ captcha_src('laravel-admin') }}" onclick="captcha_refresh(this)">
                        </div>
                    </div>
                </div>
                <script>
                    function captcha_refresh(dom) {
                        var img = $(dom)
                        var img_url = img.attr('src').split('?')[0] + '?' + Math.random()
                        img.attr('src', img_url)
                    }
                </script>
            @endif

            <div class="row">
                <div class="col-xs-8">
                    @if(config('admin.auth.remember'))
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="remember" value="1" {{ (!old('username') || old('remember')) ? 'checked' : '' }}>
                                {{ trans('admin.remember_me') }}
                            </label>
                        </div>
                    @endif
                </div>
                <!-- /.col -->
                <div class="col-xs-4">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">{{ trans('admin.login') }}</button>
                </div>
                <!-- /.col -->
            </div>
        </form>
    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.1.4 -->
<script src="{{ admin_asset("vendor/laravel-admin/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js")}}"></script>
<!-- Bootstrap 3.3.5 -->
<script src="{{ admin_asset("vendor/laravel-admin/AdminLTE/bootstrap/js/bootstrap.min.js")}}"></script>
<!-- iCheck -->
<script src="{{ admin_asset("vendor/laravel-admin/AdminLTE/plugins/iCheck/icheck.min.js")}}"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
</script>

@if(SecuritySignin::config('form_encryption.enable'))
    <script src="{{ admin_asset("vendor/laravel-admin-ext/security-signin/jsencrypt.min.js")}}"></script>
    <script>
        $('form').submit(function (event) {
            var encrypt = new JSEncrypt();
            encrypt.setPublicKey(atob('{!! base64_encode(SecuritySignin::config('form_encryption.public_key')) !!}'));

            var username = $('input[name="username"]')
            var password = $('input[name="password"]')

            username.val(encrypt.encrypt(username.val()))
            password.val(encrypt.encrypt(password.val()))
        })
    </script>
@endif
</body>
</html>
